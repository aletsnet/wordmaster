<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactSubmission;
use App\Models\Medium;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Option;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $stats = [
            'posts' => Post::count(),
            'pages' => Post::ofType('page')->count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'media' => Medium::count(),
            'templates' => Template::count(),
            'menus' => Menu::count(),
        ];

        return view('admin.backups.index', $stats);
    }

    public function export()
    {
        $data = [
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'data' => [
                'users' => User::all()->toArray(),
                'categories' => Category::all()->toArray(),
                'tags' => Tag::all()->toArray(),
                'posts' => Post::all()->toArray(),
                'post_category' => DB::table('post_category')->get()->toArray(),
                'post_tag' => DB::table('post_tag')->get()->toArray(),
                'media' => Medium::all()->toArray(),
                'menus' => Menu::all()->toArray(),
                'menu_items' => MenuItem::all()->toArray(),
                'templates' => Template::all()->toArray(),
                'options' => Option::all()->toArray(),
                'contact_submissions' => ContactSubmission::all()->toArray(),
            ],
        ];

        $tempDir = storage_path('app/private/backup-temp-' . uniqid());
        mkdir($tempDir, 0755, true);

        file_put_contents(
            $tempDir . '/database.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $mediaDir = $tempDir . '/media';
        mkdir($mediaDir, 0755, true);
        foreach (Medium::all() as $medium) {
            $sourcePath = Storage::disk('public')->path($medium->path);
            if (file_exists($sourcePath)) {
                copy($sourcePath, $mediaDir . '/' . $medium->filename);
            }
        }

        $templatesAssetDir = $tempDir . '/templates';
        mkdir($templatesAssetDir, 0755, true);
        foreach (Template::all() as $template) {
            if ($template->assets_path) {
                $sourcePath = Storage::disk('public')->path($template->assets_path);
                if (is_dir($sourcePath)) {
                    $slug = basename($template->assets_path);
                    $this->copyDirectory($sourcePath, $templatesAssetDir . '/' . $slug);
                }
            }
        }

        $zipPath = storage_path('app/private/backup-' . now()->format('Y-m-d-His') . '.zip');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->deleteDirectory($tempDir);
            return back()->with('error', 'Could not create zip file.');
        }

        $this->addDirToZip($zip, $tempDir, '');
        $zip->close();

        $this->deleteDirectory($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:204800',
        ]);

        $tempDir = storage_path('app/private/backup-import-' . uniqid());
        mkdir($tempDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($request->file('backup_file')->path()) !== true) {
            $this->deleteDirectory($tempDir);
            return back()->with('error', 'Could not open zip file.');
        }
        $zip->extractTo($tempDir);
        $zip->close();

        if (!file_exists($tempDir . '/database.json')) {
            $this->deleteDirectory($tempDir);
            return back()->with('error', 'Invalid backup file: database.json not found.');
        }

        $data = json_decode(file_get_contents($tempDir . '/database.json'), true);
        if (!$data || !isset($data['data'])) {
            $this->deleteDirectory($tempDir);
            return back()->with('error', 'Invalid backup file: bad JSON format.');
        }

        $currentUser = auth()->user();

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Clear all CMS data
            DB::table('contact_submissions')->delete();
            DB::table('options')->delete();
            DB::table('templates')->delete();
            DB::table('menu_items')->delete();
            DB::table('menus')->delete();
            DB::table('media')->delete();
            DB::table('post_tag')->delete();
            DB::table('post_category')->delete();
            DB::table('posts')->delete();
            DB::table('tags')->delete();
            DB::table('categories')->delete();
            User::where('id', '!=', $currentUser->id)->delete();

            // Import users – keep the current user
            if (!empty($data['data']['users'])) {
                foreach ($data['data']['users'] as $row) {
                    if ((int)$row['id'] === (int)$currentUser->id) {
                        // Update current user with backup data (except password)
                        $currentUser->update([
                            'name' => $row['name'],
                            'display_name' => $row['display_name'] ?? null,
                            'role' => $row['role'] ?? 'subscriber',
                        ]);
                        continue;
                    }
                    // Check if user with this email already exists
                    $existing = User::where('email', $row['email'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $row['name'],
                            'display_name' => $row['display_name'] ?? null,
                            'role' => $row['role'] ?? 'subscriber',
                        ]);
                        continue;
                    }
                    // Insert backup user with original ID
                    if (!isset($row['password'])) {
                        $row['password'] = Hash::make(Str::random(40));
                    }
                    DB::table('users')->insert($this->normalizeDates($row));
                }
            }

            // Import remaining tables in order
            $tables = [
                'categories', 'tags', 'posts', 'post_category',
                'post_tag', 'media', 'menus', 'menu_items',
                'templates', 'options', 'contact_submissions',
            ];

            foreach ($tables as $table) {
                if (!empty($data['data'][$table])) {
                    foreach ($data['data'][$table] as $row) {
                        DB::table($table)->insert($this->normalizeDates($row));
                    }
                }
            }

            // Restore media files
            $mediaSource = $tempDir . '/media';
            if (is_dir($mediaSource)) {
                foreach (scandir($mediaSource) as $file) {
                    if ($file === '.' || $file === '..') continue;
                    $sourceFile = $mediaSource . '/' . $file;
                    if (is_file($sourceFile)) {
                        Storage::disk('public')->put('media/' . $file, file_get_contents($sourceFile));
                    }
                }
            }

            // Restore template assets
            $tplSource = $tempDir . '/templates';
            if (is_dir($tplSource)) {
                foreach (scandir($tplSource) as $dir) {
                    if ($dir === '.' || $dir === '..') continue;
                    $sourceDir = $tplSource . '/' . $dir;
                    if (is_dir($sourceDir)) {
                        $destDir = 'templates/' . $dir;
                        Storage::disk('public')->deleteDirectory($destDir);
                        $this->copyToStorage($sourceDir, $destDir);
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            DB::commit();

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            DB::rollBack();
            $this->deleteDirectory($tempDir);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $this->deleteDirectory($tempDir);

        return redirect()->route('admin.backups.index')
            ->with('success', __('messages.backup_imported'));
    }

    private function normalizeDates(array $row): array
    {
        foreach ($row as $key => $value) {
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                $row[$key] = str_replace('T', ' ', substr($value, 0, 19));
            }
        }
        return $row;
    }

    private function copyDirectory(string $source, string $dest): void
    {
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        foreach (scandir($source) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $source . '/' . $item;
            $d = $dest . '/' . $item;
            if (is_dir($s)) {
                $this->copyDirectory($s, $d);
            } else {
                copy($s, $d);
            }
        }
    }

    private function copyToStorage(string $sourceDir, string $destDir): void
    {
        foreach (scandir($sourceDir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $sourceDir . '/' . $item;
            if (is_file($s)) {
                Storage::disk('public')->put($destDir . '/' . $item, file_get_contents($s));
            } elseif (is_dir($s)) {
                Storage::disk('public')->makeDirectory($destDir . '/' . $item);
                $this->copyToStorage($s, $destDir . '/' . $item);
            }
        }
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $relative): void
    {
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            $relPath = $relative ? $relative . '/' . $item : $item;
            if (is_dir($path)) {
                $zip->addEmptyDir($relPath);
                $this->addDirToZip($zip, $path, $relPath);
            } else {
                $zip->addFile($path, $relPath);
            }
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
