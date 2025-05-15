@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'grouped' => false,
    'last' => false,
    'first' => false,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'isWireNavigate' => false,
    'url',
])

<li>
    <a
        href="{{ $url }}"
        @if ($shouldOpenUrlInNewTab)
            target="_blank"
        @elseif ($isWireNavigate)
            wire:navigate
        @endif
        class="menu-link {{ $active ? 'category-active' : '' }}"
    >
        @if ($icon || $activeIcon)
            <div class="category-img">
                <x-filament::icon
                    :icon="($active && $activeIcon) ? $activeIcon : $icon"
                    class="h-5 w-5"
                />
            </div>
        @elseif ($grouped)
            {{-- Optional dot logic if icon missing --}}
            <div class="category-img">
                <div
                    class="h-2 w-2 rounded-full {{ $active ? 'bg-yellow-500' : 'bg-gray-400' }}"
                ></div>
            </div>
        @endif

        <span class="text-sm font-medium">
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::badge :color="$badgeColor" class="ml-auto">
                {{ $badge }}
            </x-filament::badge>
        @endif
    </a>
</li>
