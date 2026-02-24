<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div
        id="kt_app_sidebar_menu_wrapper"
        class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
        data-kt-scroll="true"
        data-kt-scroll-activate="true"
        data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu"
        data-kt-scroll-offset="5px"
        data-kt-scroll-save-state="true"
    >
        <div class="menu menu-column menu-rounded menu-sub-indention px-3 fw-semibold fs-6"
            data-kt-menu="true" data-kt-menu-expand="false"
        >

            @foreach ($items as $menu)
                @if (data_get($menu, 'separator'))
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">
                                {{ data_get($menu, 'name', '') }}
                            </span>
                        </div>
                    </div>
                @elseif (data_get($menu, 'submenu') && count(data_get($menu, 'submenu', [])) > 0)
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ data_get($menu, 'active') ? 'here show' : '' }}"
                    >
                        <span class="menu-link">
                            <span class="menu-icon">
                                {!! data_get($menu, 'icon', '<i class="bullet bullet-dot"></i>') !!}
                            </span>

                            <span class="menu-title">
                                {{ data_get($menu, 'name', 'name') }}
                            </span>

                            @if ($badge = data_get($menu, 'badge'))
                                <span class="menu-badge">
                                    <span
                                        class="badge {{ is_array($badge) ? $badge[1] ?? 'badge-light-primary' : 'badge-light-primary' }}"
                                    >
                                        {{ is_array($badge) ? $badge[0] ?? '' : $badge }}
                                    </span>
                                </span>
                            @endif

                            <span class="menu-arrow"></span>
                        </span>

                        @include('laravel-menus::submenu', [
                            'submenu' => data_get($menu, 'submenu', []),
                        ])
                    </div>
                @else
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ data_get($menu, 'active') ? 'here show' : '' }}"
                    >
                        <a class="menu-link {{ data_get($menu, 'active') ? 'active' : '' }} {{ data_get($menu, 'link') ? '' : '!cursor-default' }}"
                            @if (data_get($menu, 'link')) href="{{ data_get($menu, 'link') }}" @endif
                        >
                            <span class="menu-icon">
                                {!! data_get($menu, 'icon', '<i class="bullet bullet-dot"></i>') !!}
                            </span>

                            <span class="menu-title">
                                {{ data_get($menu, 'name', 'name') }}
                            </span>

                            @if ($badge = data_get($menu, 'badge'))
                                <span class="menu-badge">
                                    <span
                                        class="badge {{ is_array($badge) ? $badge[1] ?? 'badge-light-primary' : 'badge-light-primary' }}"
                                    >
                                        {{ is_array($badge) ? $badge[0] ?? '' : $badge }}
                                    </span>
                                </span>
                            @endif

                            <span
                                class="{{ data_get($menu, 'active') ? 'bullet bullet-dot' : 'menu-arrow' }}"
                            ></span>
                        </a>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>
<!--end::sidebar menu-->
