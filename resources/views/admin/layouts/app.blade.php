<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Option::getValue('site_title', config('app.name')) }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-gray-900 text-white">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-lg font-bold">WordMaster</h1>
                <p class="text-sm text-gray-400">{{ __('admin.panel_title') }}</p>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li><a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">{{ __('admin.dashboard') }}</a></li>
                    <li><a href="{{ route('admin.posts.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.posts.*') ? 'bg-gray-700' : '' }}">{{ __('admin.posts') }}</a></li>
                    <li><a href="{{ route('admin.pages.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700' : '' }}">{{ __('admin.pages') }}</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">{{ __('admin.categories') }}</a></li>
                    <li><a href="{{ route('admin.tags.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.tags.*') ? 'bg-gray-700' : '' }}">{{ __('admin.tags') }}</a></li>
                    <li><a href="{{ route('admin.media.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.media.*') ? 'bg-gray-700' : '' }}">{{ __('admin.media') }}</a></li>
                    <li><a href="{{ route('admin.contact-submissions.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-gray-700' : '' }}">{{ __('admin.contact_submissions') }}@php $unread = \App\Models\ContactSubmission::unread()->count(); @endphp @if($unread > 0)<span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full ml-2">{{ $unread }}</span>@endif</a></li>
                    <li><a href="{{ route('admin.menus.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.menus.*') ? 'bg-gray-700' : '' }}">{{ __('admin.menus') }}</a></li>
                    <li><a href="{{ route('admin.templates.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.templates.*') ? 'bg-gray-700' : '' }}">{{ __('admin.templates') }}</a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">{{ __('admin.users') }}</a></li>
                    <li><a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">{{ __('admin.settings') }}</a></li>
                    <li><a href="{{ route('admin.backups.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.backups.*') ? 'bg-gray-700' : '' }}">{{ __('admin.backups') }}</a></li>
                    <li class="pt-4 border-t border-gray-700 mt-4">
                        <a href="{{ route('home') }}" class="block px-4 py-2 rounded hover:bg-gray-700">{{ __('admin.view_site') }}</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700">{{ __('admin.logout') }}</button>
                        </form>
                    </li>
                    <li class="pt-4 mt-2 border-t border-gray-700">
                        <div class="px-4 py-2">
                            @include('partials.locale-switcher')
                        </div>
                    </li>
                </ul>
            </nav>
        </aside>
        <main class="flex-1 p-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
