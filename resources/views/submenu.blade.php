<div class="menu-sub menu-sub-accordion">
    @foreach ($submenu as $sub)
        <div class="menu-item">
            <a class="menu-link {{ data_get($sub, 'active') ? 'active' : '' }} {{ data_get($sub, 'link') ? '' : '!cursor-default' }}"
                @if (data_get($sub, 'link')) href="{{ data_get($sub, 'link') }}" @endif
            >
                <span class="menu-icon">
                    {!! data_get($sub, 'icon', '<i class="bullet bullet-dot"></i>') !!}
                </span>

                <span class="menu-title">
                    {{ data_get($sub, 'name', '') }}
                </span>

                @if ($badge = data_get($sub, 'badge'))
                    <span class="menu-badge">
                        <span
                            class="badge {{ is_array($badge) ? $badge[1] ?? 'badge-light-primary' : 'badge-light-primary' }}"
                        >
                            {{ is_array($badge) ? $badge[0] ?? '' : $badge }}
                        </span>
                    </span>
                @endif
            </a>

            @if (data_get($sub, 'submenu'))
                <span class="menu-arrow"></span>
            @endif
        </div>

        @if (data_get($sub, 'submenu'))
            @include('laravel-menus::submenu', [
                'submenu' => data_get($sub, 'submenu', []),
            ])
        @endif
    @endforeach
</div>
