<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = Medium::latest()->paginate(20);
        return view('admin.media.index', compact('media'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'alt_text' => 'nullable|max:255',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('media', $filename, 'public');

        Medium::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'alt_text' => $request->alt_text,
        ]);

        return redirect()->route('admin.media.index')
            ->with('success', __('messages.file_uploaded'));
    }

    public function destroy(Medium $medium)
    {
        Storage::disk('public')->delete($medium->path);
        $medium->delete();
        return redirect()->route('admin.media.index')
            ->with('success', __('messages.file_deleted'));
    }

    public function jsonList()
    {
        $media = Medium::latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => asset('storage/' . $item->path),
                'thumbnail' => asset('storage/' . $item->path),
                'original_name' => $item->original_name,
                'alt_text' => $item->alt_text,
                'size' => $item->size,
                'mime_type' => $item->mime_type,
            ];
        });
        return response()->json($media);
    }

    public function uploadAjax(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'alt_text' => 'nullable|max:255',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('media', $filename, 'public');

        $medium = Medium::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'alt_text' => $request->alt_text,
        ]);

        return response()->json([
            'id' => $medium->id,
            'url' => asset('storage/' . $medium->path),
            'thumbnail' => asset('storage/' . $medium->path),
            'original_name' => $medium->original_name,
            'alt_text' => $medium->alt_text,
        ]);
    }
}
