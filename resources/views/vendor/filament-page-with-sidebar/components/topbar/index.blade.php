@props([
    'sidebar',
])

<div class="mt-8 px-4">
    <nav class="space-y-1">
        @foreach ($sidebar->getNavigationItems() as $group)
            @if ($groupLabel = $group->getLabel())
                <div class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    {{ $groupLabel }}
                </div>
            @endif

            @foreach ($group->getItems() as $item)
                @php
                    $isActive = $item->isActive();
                    $icon = $item->getIcon();
                    $label = $item->getLabel();
                    $url = $item->getUrl();
                    $shouldOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                @endphp

                <a href="{{ $url }}"
                   @if($shouldOpenUrlInNewTab) target="_blank" @endif
                   class="menu-link {{ $isActive ? 'category-active' : '' }}">
                    <div class="category-img">
                        <x-dynamic-component :component="$icon" class="w-4 h-4" />
                    </div>
                    {{ $label }}
                </a>
            @endforeach
        @endforeach
    </nav>
</div>
