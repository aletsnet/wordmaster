@php $current = app()->getLocale(); @endphp
<div class="flex items-center gap-1 text-sm font-medium">
    @foreach (['es', 'en'] as $lang)
        @if ($lang === $current)
            <span class="{{ $activeClass ?? 'text-white bg-gray-600 px-2 py-1 rounded' }}">
                {{ strtoupper($lang) }}
            </span>
        @else
            <form method="POST" action="{{ route('locale.switch', $lang) }}" class="inline">
                @csrf
                <button type="submit" class="{{ $inactiveClass ?? 'text-gray-300 hover:text-white hover:bg-gray-700 px-2 py-1 rounded transition' }}">
                    {{ strtoupper($lang) }}
                </button>
            </form>
        @endif
    @endforeach
</div>
