<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\TemplateAssetExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function __construct(private TemplateAssetExtractor $extractor) {}

    public function index()
    {
        $templates = Template::latest()->paginate(15);
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|max:255',
            'slug'        => 'nullable|unique:templates,slug',
            'content'     => 'required',
            'description' => 'nullable',
            'assets'      => [
                'nullable',
                'file',
                'max:51200', // 50 MB
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidArchive($value->getClientOriginalName())) {
                        $fail(__('messages.template_assets_invalid_format'));
                    }
                },
            ],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        if ($request->hasFile('assets')) {
            try {
                $data['assets_path'] = $this->extractor->extract(
                    $request->file('assets'),
                    $data['slug']
                );
            } catch (\RuntimeException $e) {
                return back()->withInput()->withErrors(['assets' => $e->getMessage()]);
            }
        }

        unset($data['assets']);
        Template::create($data);

        return redirect()->route('admin.templates.index')
            ->with('success', __('messages.template_created'));
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name'        => 'required|max:255',
            'slug'        => 'nullable|unique:templates,slug,' . $template->id,
            'content'     => 'required',
            'description' => 'nullable',
            'assets'      => [
                'nullable',
                'file',
                'max:51200',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidArchive($value->getClientOriginalName())) {
                        $fail(__('messages.template_assets_invalid_format'));
                    }
                },
            ],
            'remove_assets' => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // El slug cambia → mover carpeta de assets al nuevo nombre
        $oldSlug = $template->slug;
        $newSlug = $data['slug'];

        if ($request->hasFile('assets')) {
            // Nuevo archivo: eliminar assets anteriores (del slug correcto) y extraer
            $this->extractor->delete($template->assets_path);
            try {
                $data['assets_path'] = $this->extractor->extract(
                    $request->file('assets'),
                    $newSlug
                );
            } catch (\RuntimeException $e) {
                return back()->withInput()->withErrors(['assets' => $e->getMessage()]);
            }
        } elseif (!empty($data['remove_assets'])) {
            // El usuario pidió eliminar los assets
            $this->extractor->delete($template->assets_path);
            $data['assets_path'] = null;
        } elseif ($oldSlug !== $newSlug && $template->assets_path) {
            // El slug cambió sin nuevo archivo: renombrar la carpeta
            $data['assets_path'] = $this->renameAssetsDir($template->assets_path, $newSlug);
        }

        unset($data['assets'], $data['remove_assets']);
        $template->update($data);

        return redirect()->route('admin.templates.index')
            ->with('success', __('messages.template_updated'));
    }

    public function destroy(Template $template)
    {
        $this->extractor->delete($template->assets_path);
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', __('messages.template_deleted'));
    }

    // -------------------------------------------------------------------------

    private function isValidArchive(string $filename): bool
    {
        $lower = strtolower($filename);
        return str_ends_with($lower, '.zip')
            || str_ends_with($lower, '.rar')
            || str_ends_with($lower, '.tar')
            || str_ends_with($lower, '.tar.gz')
            || str_ends_with($lower, '.tgz')
            || str_ends_with($lower, '.tar.bz2');
    }

    private function renameAssetsDir(string $oldPath, string $newSlug): string
    {
        $newPath  = 'templates/' . $newSlug;
        $oldAbs   = storage_path('app/public/' . $oldPath);
        $newAbs   = storage_path('app/public/' . $newPath);

        if (is_dir($oldAbs) && !is_dir($newAbs)) {
            rename($oldAbs, $newAbs);
        }

        return $newPath;
    }
}
