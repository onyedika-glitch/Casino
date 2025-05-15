@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

@php
    $tag = $url ? 'a' : 'button';
@endphp

<li>
    <{{ $tag }}
        @if ($url)
            {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
        @else
        type="button"
    @endif
    class="menu-link {{ $active ? 'category-active' : '' }}"
    >
    @if ($icon || $activeIcon)
        <div class="category-img">
            <x-filament::icon
                :icon="($active && $activeIcon) ? $activeIcon : $icon"
                class="h-4 w-4"
            />
        </div>
    @endif

    <span class="whitespace-nowrap">
            {{ $slot }}
        </span>

    @if (filled($badge))
        <x-filament::badge :color="$badgeColor" size="sm">
            {{ $badge }}
        </x-filament::badge>
    @endif
</{{ $tag }}>
</li>
