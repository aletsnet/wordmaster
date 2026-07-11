<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use PharData;

class TemplateAssetExtractor
{
    /**
     * Extensiones de archivo soportadas.
     */
    public const ALLOWED_EXTENSIONS = ['zip', 'rar', 'tar', 'gz', 'bz2'];

    /**
     * MIME types aceptados para validación.
     */
    public const ALLOWED_MIMES = [
        'application/zip',
        'application/x-zip-compressed',
        'application/x-rar-compressed',
        'application/vnd.rar',
        'application/x-tar',
        'application/gzip',
        'application/x-bzip2',
        'application/x-compressed',
        'application/octet-stream',
    ];

    /**
     * Extrae el archivo comprimido al disco público bajo templates/{slug}/.
     * Elimina cualquier versión anterior antes de extraer.
     *
     * @param  UploadedFile  $file
     * @param  string        $slug   Slug del template (usado como nombre de carpeta)
     * @return string                Ruta relativa dentro del disco 'public' (templates/{slug})
     *
     * @throws \RuntimeException si el formato no es soportado o falla la extracción
     */
    public function extract(UploadedFile $file, string $slug): string
    {
        $destination = 'templates/' . $slug;

        // Limpiar versión anterior si existe
        if (Storage::disk('public')->exists($destination)) {
            Storage::disk('public')->deleteDirectory($destination);
        }

        $absoluteDest = storage_path('app/public/' . $destination);
        if (!is_dir($absoluteDest)) {
            mkdir($absoluteDest, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();

        // Detectar tipo por extensión del nombre original (más fiable que MIME)
        if ($this->isZip($originalName)) {
            $this->extractZip($file->getRealPath(), $absoluteDest);
        } elseif ($this->isTar($originalName)) {
            $this->extractTar($file->getRealPath(), $originalName, $absoluteDest);
        } elseif ($this->isRar($originalName)) {
            $this->extractRar($file->getRealPath(), $absoluteDest);
        } else {
            throw new \RuntimeException(
                "Formato no soportado: '{$originalName}'. Use .zip, .tar, .tar.gz, .tgz, .tar.bz2 o .rar"
            );
        }

        return $destination;
    }

    /**
     * Elimina todos los assets de un template del almacenamiento público.
     */
    public function delete(?string $assetsPath): void
    {
        if ($assetsPath && Storage::disk('public')->exists($assetsPath)) {
            Storage::disk('public')->deleteDirectory($assetsPath);
        }
    }

    // -------------------------------------------------------------------------
    // Detección de formato
    // -------------------------------------------------------------------------

    private function isZip(string $name): bool
    {
        return str_ends_with(strtolower($name), '.zip');
    }

    private function isTar(string $name): bool
    {
        $name = strtolower($name);
        return str_ends_with($name, '.tar')
            || str_ends_with($name, '.tar.gz')
            || str_ends_with($name, '.tgz')
            || str_ends_with($name, '.tar.bz2');
    }

    private function isRar(string $name): bool
    {
        return str_ends_with(strtolower($name), '.rar');
    }

    // -------------------------------------------------------------------------
    // Extractores
    // -------------------------------------------------------------------------

    private function extractZip(string $filePath, string $destination): void
    {
        $zip = new ZipArchive();
        $result = $zip->open($filePath);

        if ($result !== true) {
            throw new \RuntimeException("No se pudo abrir el archivo ZIP (código: {$result})");
        }

        // Extraer sin el directorio raíz si el ZIP contiene una sola carpeta
        $extractPath = $this->resolveTopLevelDir($zip);
        $zip->extractTo($destination);
        $zip->close();

        // Si todos los archivos estaban dentro de una carpeta raíz, mover contenido un nivel arriba
        if ($extractPath !== null) {
            $this->flattenSingleRootDir($destination, $extractPath);
        }
    }

    /**
     * Detecta si el ZIP tiene un único directorio raíz (ej: "theme/css", "theme/js").
     * Retorna el nombre de ese directorio raíz, o null si no aplica.
     */
    private function resolveTopLevelDir(ZipArchive $zip): ?string
    {
        $rootDirs = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $parts = explode('/', $name);
            if ($parts[0] !== '') {
                $rootDirs[$parts[0]] = true;
            }
        }
        // Solo hay una carpeta raíz y al menos un archivo dentro
        if (count($rootDirs) === 1) {
            $rootDir = array_key_first($rootDirs);
            // Verificar que es un directorio (no un archivo suelto)
            if ($zip->locateName($rootDir . '/') !== false) {
                return $rootDir;
            }
        }
        return null;
    }

    /**
     * Mueve el contenido de $destination/$subDir/* a $destination/ y borra $subDir.
     */
    private function flattenSingleRootDir(string $destination, string $subDir): void
    {
        $subPath = $destination . DIRECTORY_SEPARATOR . $subDir;
        if (!is_dir($subPath)) {
            return;
        }

        $items = array_diff(scandir($subPath), ['.', '..']);
        foreach ($items as $item) {
            rename($subPath . DIRECTORY_SEPARATOR . $item, $destination . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($subPath);
    }

    private function extractTar(string $filePath, string $originalName, string $destination): void
    {
        try {
            $phar = new PharData($filePath);
            $phar->extractTo($destination, null, true);
        } catch (\Exception $e) {
            throw new \RuntimeException("Error extrayendo TAR: " . $e->getMessage());
        }

        // Igual que ZIP: aplanar si tiene directorio raíz único
        $this->flattenIfSingleRootDirFs($destination);
    }

    private function extractRar(string $filePath, string $destination): void
    {
        // Intentar con la extensión PHP ext-rar si está disponible
        if (class_exists('RarArchive')) {
            $rar = \RarArchive::open($filePath);
            if ($rar === false) {
                throw new \RuntimeException("No se pudo abrir el archivo RAR");
            }
            $entries = $rar->getEntries();
            foreach ($entries as $entry) {
                if (!$entry->isDirectory()) {
                    $entry->extract($destination);
                }
            }
            $rar->close();
            $this->flattenIfSingleRootDirFs($destination);
            return;
        }

        // Fallback: comando del sistema 'unrar'
        $unrar = trim(shell_exec('which unrar 2>/dev/null') ?? '');
        if ($unrar) {
            $filePath = escapeshellarg($filePath);
            $destination = escapeshellarg($destination);
            $output = [];
            $code = 0;
            exec("{$unrar} x -o+ {$filePath} {$destination} 2>&1", $output, $code);
            if ($code !== 0) {
                throw new \RuntimeException("Error al extraer RAR: " . implode("\n", $output));
            }
            $this->flattenIfSingleRootDirFs(trim($destination, "'\""));
            return;
        }

        throw new \RuntimeException(
            "Los archivos RAR requieren la extensión PHP 'ext-rar' o el comando 'unrar' instalado en el servidor. "
            . "Sube el archivo en formato .zip o .tar.gz como alternativa."
        );
    }

    /**
     * Versión filesystem del aplanado: si el destino contiene solo UNA carpeta,
     * mueve su contenido al nivel raíz.
     */
    private function flattenIfSingleRootDirFs(string $destination): void
    {
        $items = array_diff(scandir($destination), ['.', '..']);
        if (count($items) === 1) {
            $only = reset($items);
            $subPath = $destination . DIRECTORY_SEPARATOR . $only;
            if (is_dir($subPath)) {
                $this->flattenSingleRootDir($destination, $only);
            }
        }
    }
}
