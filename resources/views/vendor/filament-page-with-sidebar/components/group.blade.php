@props([
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
])

<li x-data="{ open: true }" class="mb-4">
    @if ($label)
        <div
            @if ($collapsible)
                @click="open = !open"
            class="cursor-pointer"
            @endif
            class="flex items-center gap-x-3 px-2 py-2 text-white font-semibold"
        >
            @if ($icon)
                <div class="category-img">
                    <x-filament::icon :icon="$icon" class="h-5 w-5" />
                </div>
            @endif
            <span>{{ $label }}</span>

            @if ($collapsible)
                <svg :class="{ 'rotate-180': open }" class="ml-auto h-4 w-4 transition-transform text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            @endif
        </div>
    @endif

    <ul x-show="open" x-collapse.duration.200ms class="ml-4 mt-2 flex flex-col gap-y-1">
        @foreach ($items as $item)
            <x-filament-page-with-sidebar::item
                :active-icon="$item->getActiveIcon()"
                :active="$item->isActive()"
                :badge-color="$item->getBadgeColor()"
                :badge="$item->getBadge()"
                :icon="$item->getIcon()"
                :url="$item->getUrl()"
                :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
            >
                {{ $item->getLabel() }}
            </x-filament-page-with-sidebar::item>
        @endforeach
    </ul>
</li>
