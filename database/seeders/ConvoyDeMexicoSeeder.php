<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medium;
use App\Models\Option;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ConvoyDeMexicoSeeder extends Seeder
{
    /**
     * Archivos de imagen a importar desde el sitio estático.
     * Clave: nombre original en /img/, Valor: alt_text descriptivo.
     */
    private array $images = [
        'fondo01.jpg' => 'Convoy de México – Fondo 1',
        'fondo02.jpg' => 'Convoy de México – Fondo 2',
        'fondo03.jpg' => 'Convoy de México – Fondo 3',
        'fondo04.jpg' => 'Convoy de México – Fondo 4',
        'fondo05.jpg' => 'Convoy de México – Fondo 5',
        'logo.png'    => 'Logo Convoy de México',
    ];

    public function run(): void
    {
        // ── 1. Obtener (o crear) el usuario admin ──────────────────────────────
        $admin = User::where('role', 'admin')->first()
            ?? User::first();

        // ── 2. Copiar imágenes al disco public y registrarlas en media ─────────
        $sourceDir = '/home/alets/Proyectos/portadas/convoydemexico.com/img';
        $mediaDir  = storage_path('app/public/media');

        File::ensureDirectoryExists($mediaDir);

        $mediaRecords = [];

        foreach ($this->images as $file => $altText) {
            $sourcePath = "{$sourceDir}/{$file}";

            if (! File::exists($sourcePath)) {
                $this->command->warn("  [skip] No encontrado: {$sourcePath}");
                continue;
            }

            $filename = 'convoy_' . $file;
            $destPath = "{$mediaDir}/{$filename}";

            // Copiar solo si no existe ya
            if (! File::exists($destPath)) {
                File::copy($sourcePath, $destPath);
                $this->command->info("  [ok] Copiado: {$filename}");
            } else {
                $this->command->line("  [exists] Ya existe: {$filename}");
            }

            $storagePath = "media/{$filename}";
            $mimeType    = str_ends_with($file, '.png') ? 'image/png' : 'image/jpeg';
            $fileSize    = File::size($destPath);

            // Upsert: no duplicar si ya existe el registro
            $medium = Medium::firstOrCreate(
                ['filename' => $filename],
                [
                    'user_id'       => $admin->id,
                    'original_name' => $file,
                    'mime_type'     => $mimeType,
                    'size'          => $fileSize,
                    'path'          => $storagePath,
                    'alt_text'      => $altText,
                ]
            );

            $mediaRecords[$file] = $medium;
        }

        // ── 3. Actualizar opciones del sitio ───────────────────────────────────
        Option::updateOrCreate(
            ['key' => 'site_title'],
            ['value' => 'Convoy de México']
        );

        Option::updateOrCreate(
            ['key' => 'site_description'],
            ['value' => 'Agencia de Viajes – más de 28 años de experiencia']
        );

        Option::updateOrCreate(
            ['key' => 'site_keywords'],
            ['value' => 'agencia de viajes, convoy de México, paquetes turísticos, Hidalgo, Tulancingo']
        );

        // ── 4. Categoría Viajes ────────────────────────────────────────────────
        $catViajes = Category::firstOrCreate(
            ['slug' => 'viajes'],
            [
                'name'        => 'Viajes',
                'description' => 'Paquetes turísticos, promociones y programación de viajes.',
            ]
        );

        // ── 5. Página HOME ─────────────────────────────────────────────────────
        $logoMedia      = $mediaRecords['logo.png']    ?? null;
        $featuredImage  = $logoMedia ? $logoMedia->path : null;

        Post::updateOrCreate(
            ['slug' => 'convoy-home'],
            [
                'author_id'      => $admin->id,
                'title'          => '¡Bienvenido a Convoy de México!',
                'content'        => '<div class="text-center">
<div class="mb-6">
  <img src="/storage/' . ($featuredImage ?? '') . '" alt="Logo Convoy de México" class="mx-auto max-w-xs">
</div>
<p class="tagline uppercase tracking-widest text-sm mb-4">Agencia de Viajes</p>
<h1 class="text-3xl font-bold mb-4">¡Bienvenido a Convoy de México!</h1>
<p class="mb-6 text-gray-600">
  Tu agencia de viajes de confianza con más de 28 años de experiencia.<br>
  Descubre destinos increíbles con los mejores precios y la mayor calidad de servicio.
</p>
</div>',
                'excerpt'        => 'Tu agencia de viajes de confianza con más de 28 años de experiencia.',
                'status'         => 'published',
                'type'           => 'page',
                'template'       => 'default-page',
                'featured_image' => $featuredImage,
            ]
        );

        // ── 6. Página QUIÉNES SOMOS ────────────────────────────────────────────
        Post::updateOrCreate(
            ['slug' => 'quienes-somos'],
            [
                'author_id' => $admin->id,
                'title'     => '¿Quiénes Somos?',
                'content'   => '<h2>¿Quiénes Somos?</h2>

<p>La Agencia de Viajes Convoy de México nace en el estado de Hidalgo como "Viajes América" en el año 1996,
comenzando con renta de autobuses de categoría económica y servicio de excursiones escolares para la región
del Valle de Tulancingo, llevando a cabo la logística de todos y cada uno de los servicios, facilitando con
esto la organización de los mismos a las instituciones y grupos, teniendo como destinos principales Hidalgo,
México DF, Puebla, Guanajuato, Oaxaca, Michoacán, Querétaro, Estado de México entre muchos otros, visitando
museos, parques recreativos, sitios históricos y zonas arqueológicas.</p>

<p>En el año 2000 abrimos las oficinas que actualmente ocupamos en la Ciudad de Tulancingo, Hidalgo, México.
Motivados por la demanda del mercado y la excelente calidad de nuestros servicios, incorporamos renta de
autobuses de 1ª calidad con flota moderna, venta de boletos aéreos y paquetes vacacionales, haciendo
alianzas estratégicas con diversos proveedores y operadoras turísticas.</p>

<p>En el año 2006 se crea formalmente <strong>Convoy de México</strong>, marca registrada en México y USA,
integrando todos los servicios con los que actualmente contamos.</p>

<h3>Misión</h3>
<p>Brindar los mejores servicios turísticos a los precios más bajos de la región, con la mayor calidad
y responsabilidad para nuestros clientes.</p>

<h3>Visión</h3>
<p>Ser la mejor agencia de viajes, lograr la satisfacción del cliente y el crecimiento de la compañía
con estímulo y orgullo para nuestros trabajadores. Que nuestros clientes se conviertan en fans.</p>

<h3>Política de Calidad</h3>
<p>Cumplir con los requerimientos y expectativas de nuestros clientes en todos los servicios que contratan
para su total seguridad y satisfacción, con la contribución y compromiso de todos nuestros departamentos.</p>

<h3>Valores</h3>
<ul>
  <li>Respeto</li>
  <li>Enfoque al cliente</li>
  <li>Responsabilidad</li>
  <li>Compromiso</li>
  <li>Trabajo en equipo</li>
  <li>Innovación</li>
  <li>Institucionalidad</li>
  <li>Rapidez</li>
  <li>Simplicidad</li>
  <li>Agilidad</li>
</ul>',
                'excerpt'        => 'Conoce la historia, misión, visión y valores de Convoy de México, agencia fundada en 1996 en Hidalgo.',
                'status'         => 'published',
                'type'           => 'page',
                'template'       => 'default-page',
                'featured_image' => $featuredImage,
            ]
        );

        // ── 7. Página VIAJES ───────────────────────────────────────────────────
        $viajesPost = Post::updateOrCreate(
            ['slug' => 'viajes'],
            [
                'author_id' => $admin->id,
                'title'     => 'Nuestros Viajes',
                'content'   => '<h2>Nuestros Viajes</h2>

<p>Explora nuestra amplia programación de paquetes turísticos, promociones y revista de viajes.
Todo actualizado automáticamente para que siempre encuentres las mejores ofertas.</p>

<h3>Promociones Destacadas</h3>
<p>Consulta nuestras promociones más recientes en:</p>
<ul>
  <li><a href="https://www.megatravel.com.mx" target="_blank" rel="noopener">MegaTravel – Promociones</a></li>
</ul>

<h3>Programación Completa</h3>
<div style="position:relative;width:100%;padding-top:150%;overflow:hidden;border-radius:0.5rem;background:#fff;">
  <iframe
    src="https://www.megatravel.com.mx/tools/vi.php?Dest=2"
    style="position:absolute;top:0;left:0;width:800px;height:1200px;border:none;transform-origin:top left;"
    allowtransparency="true"
    loading="lazy"
    title="Programación de viajes Convoy de México">
  </iframe>
</div>

<h3>Revista de Viajes</h3>
<iframe
  src="https://www.megatravel.com.mx/tools/megatraveler.php"
  style="width:100%;height:620px;border:none;border-radius:0.5rem;"
  allowtransparency="true"
  loading="lazy"
  title="Revista Mega Traveler">
</iframe>',
                'excerpt'        => 'Explora nuestra amplia programación de paquetes turísticos, promociones y revista de viajes.',
                'status'         => 'published',
                'type'           => 'page',
                'template'       => 'default-page',
                'featured_image' => $featuredImage,
            ]
        );

        $viajesPost->categories()->syncWithoutDetaching([$catViajes->id]);

        $this->command->info('ConvoyDeMexicoSeeder completado.');
        $this->command->table(
            ['Recurso', 'Resultado'],
            [
                ['Imágenes copiadas', count($mediaRecords)],
                ['Registros en media', count($mediaRecords)],
                ['Páginas creadas/actualizadas', 3],
                ['Opciones del sitio', 3],
                ['Categorías', 1],
            ]
        );
    }
}
