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
            'contact_email' => Option::getValue('contact_email', config('mail.from.address')),
            'contact_phone1' => Option::getValue('contact_phone1', ''),
            'contact_phone2' => Option::getValue('contact_phone2', ''),
            'contact_whatsapp' => Option::getValue('contact_whatsapp', ''),
            'contact_facebook' => Option::getValue('contact_facebook', ''),
            'contact_instagram' => Option::getValue('contact_instagram', ''),
            'contact_tiktok' => Option::getValue('contact_tiktok', ''),
            'custom_css' => Option::getValue('custom_css', ''),
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
            'contact_email' => 'nullable|email|max:255',
            'contact_phone1' => 'nullable|max:50',
            'contact_phone2' => 'nullable|max:50',
            'contact_whatsapp' => 'nullable|max:50',
            'contact_facebook' => 'nullable|max:255',
            'contact_instagram' => 'nullable|max:255',
            'contact_tiktok' => 'nullable|max:255',
            'custom_css' => 'nullable',
        ]);

        foreach ($data as $key => $value) {
            Option::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('messages.settings_saved'));
    }
}
