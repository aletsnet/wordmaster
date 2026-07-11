<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_title' => Option::getValue('site_title', config('app.name')),
            'site_description' => Option::getValue('site_description', ''),
            'site_logo' => Option::getValue('site_logo', ''),
            'posts_per_page' => Option::getValue('posts_per_page', '10'),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => 'required|max:255',
            'site_description' => 'nullable|max:500',
            'site_logo' => 'nullable|max:255',
            'posts_per_page' => 'nullable|integer|min:1|max:100',
        ]);

        foreach ($data as $key => $value) {
            Option::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }
}
