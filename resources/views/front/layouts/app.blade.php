@php
    $siteLayoutSlug = \App\Models\Option::getValue('site_layout');
@endphp
@if($siteLayoutSlug)
{!! (new \App\Services\TemplateRenderer())->render($siteLayoutSlug, [
    'content' => $__env->yieldContent('content'),
    'title'   => $__env->yieldContent('title')
               ?: \App\Models\Option::getValue('site_title', config('app.name')),
]) !!}
@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Option::getValue('site_title', config('app.name')) }}@hasSection('title') - @yield('title')@endif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @php $customCss = \App\Models\Option::getValue('custom_css'); @endphp
    @if($customCss)<style>{{ $customCss }}</style>@endif
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">
{{ \App\Models\Option::getValue('site_title', config('app.name')) }}
            </a>
            <nav class="flex items-center gap-4">
                @php
                    $menu = \App\Models\Menu::where('location', 'header')->first();
                    $currentPath = request()->path();
                    $currentPath = $currentPath === '/' ? '/' : '/' . $currentPath;
                @endphp
                @if($menu)
                <ul class="flex space-x-6 items-center">
                    @foreach($menu->items as $item)
                    @php
                        $active = $item->url === $currentPath ? ' border-red-600' : ' border-transparent';
                    @endphp
                    <li><a href="{{ $item->url }}" class="text-gray-700 border-2{{ $active }} hover:border-gray-900 px-3 py-1.5 rounded-lg transition">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
                @endif
                @include('partials.locale-switcher', [
                    'activeClass'   => 'text-gray-900 bg-gray-200 px-2 py-1 rounded font-semibold',
                    'inactiveClass' => 'text-gray-500 hover:text-gray-900 hover:bg-gray-100 px-2 py-1 rounded transition',
                ])
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-500">
            &copy; {{ date('Y') }} {{ \App\Models\Option::getValue('site_title', config('app.name')) }}{{ __('front.powered_by') }}
        </div>
    </footer>
</body>
</html>
@endif
