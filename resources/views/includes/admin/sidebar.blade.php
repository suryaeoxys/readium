<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        @if ((Auth::user()->roles->contains('5') && in_array('dashboard', $staffPermissions)) || Auth::user()->roles->contains('1'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->roles->contains('1'))
            <li
                class="nav-item {{ request()->is('admin/users*') || request()->is('admin/permissions*') || request()->is('admin/roles*') ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#user"
                    aria-expanded="{{ request()->is('admin/users*') || request()->is('admin/permissions*') || request()->is('admin/roles*') ? 'true' : 'false' }}"
                    aria-controls="user">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">Users Management</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->is('admin/users*') || request()->is('admin/permissions*') || request()->is('admin/roles*') ? 'show' : '' }}"
                    id="user">
                    <ul class="nav flex-column sub-menu">
                        @if (Auth::user()->roles->contains('1'))
                            <li class="nav-item"> <a
                                    class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}"> Users </a></li>
                        @endif
                        {{-- <li class="nav-item">
						<a class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}"> Permission </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"> Roles </a>
        </li> --}}
                    </ul>
                </div>
            </li>
        @endif
        <li
            class="nav-item {{ request()->is('admin/products*') || request()->is('admin/authors*') || request()->is('admin/publishers*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#products"
                aria-expanded="{{ request()->is('admin/products*') || request()->is('admin/authors*') || request()->is('admin/publisher*') ? 'true' : 'false' }}"
                aria-controls="products">
                <i class="menu-icon mdi mdi-library-books"></i>
                <span class="menu-title">Products</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('admin/products*') || request()->is('admin/publishers*') || request()->is('admin/authors*') ? 'show' : '' }}"
                id="products">
                <ul class="nav flex-column sub-menu">
                    @if (Auth::user()->roles->contains('1'))
                        <li class="nav-item"> <a class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}"
                                href="{{ route('admin.products.index') }}"> Products </a>
                        </li>
                        <li class="nav-item"> <a
                                class="nav-link {{ request()->is('admin/publishers*') ? 'active' : '' }}"
                                href="{{ route('admin.publishers.index') }}"> Publisher </a>
                        </li>
                        <li class="nav-item"> <a class="nav-link {{ request()->is('admin/authors*') ? 'active' : '' }}"
                                href="{{ route('admin.authors.index') }}"> Authors </a>
                        </li>
                    @endif

                </ul>
            </div>
        </li>

        @if (Auth::user()->roles->contains('1'))
            <li class="nav-item {{ request()->is('admin/pages*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.pages.index') }}">
                    <i class="mdi mdi-book-open-page-variant menu-icon"></i>
                    <span class="menu-title">Pages</span>
                </a>
            </li>
        @endif
        <li
        class="nav-item {{ request()->is('admin/categories*') || request()->is('admin/categories*')  ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#category"
            aria-expanded="{{ request()->is('admin/categories*') || request()->is('admin/categories*')  ? 'true' : 'false' }}"
            aria-controls="category">
            <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            <span class="menu-title">Categories</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->is('admin/categories*') || request()->is('admin/categories*')  ? 'show' : '' }}"
            id="category">
            <ul class="nav flex-column sub-menu">
                @if (Auth::user()->roles->contains('1'))
                    <li class="nav-item">
                        <a id="category" class="nav-link {{ request()->get('type') == 'category' ? 'active' : '' }}"
                            href="{{ route('admin.categories.index','type=category') }}"> Categories </a>
                    </li>
                @endif
                @if (Auth::user()->roles->contains('1'))
                    <li class="nav-item">
                        <a id="subcategory" class="nav-link {{ request()->get('type') == 'sub-category' ? 'active' : '' }}"
                            href="{{ route('admin.categories.index','type=sub-category') }}"> Sub categories </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>



        @if ((Auth::user()->roles->contains('5') && in_array('s', $staffPermissions)) || Auth::user()->roles->contains('1'))
            <li class="nav-item {{ request()->is('admin/media-post*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.media-post.index') }}">
                    <i class="mdi mdi-message-bulleted menu-icon"></i>
                    <span class="menu-title">Social Media Post</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->roles->contains('1'))
            <li class="nav-item {{ request()->is('admin/email-templates*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.email-templates.index') }}">
                    <i class="mdi mdi-email-outline menu-icon"></i>
                    <span class="menu-title">Email Templates</span>
                </a>
            </li>
        @endif

        {{-- @can('faq_access') --}}
		<li class="nav-item {{ request()->is('admin/faqs*') ? 'active' : '' }}">
			<a class="nav-link" href="{{ route('admin.faqs.index') }}">
				<i class="mdi mdi-comment-question-outline menu-icon"></i>
				<span class="menu-title">FAQ's</span>
			</a>
		</li>
		{{-- @endcan --}}

        <li class="nav-item {{ request()->is('admin/subscription-plan*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('admin.subscription-plan.index') }}">
                <i class="mdi mdi mdi-bell-ring menu-icon"></i>
                <span class="menu-title">Subscription Plan </span>
            </a>
        </li>

        @if (Auth::user()->roles->contains('1'))
            <li
                class="nav-item {{ request()->is('admin/site-setting*') || request()->is('admin/app-setting*') || request()->is('admin/notifications*') ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#setting"
                    aria-expanded="{{ request()->is('admin/site-setting*') || request()->is('admin/app-setting*') || request()->is('admin/notifications*') ? 'true' : 'false' }}"
                    aria-controls="setting">
                    <i class="mdi mdi-settings menu-icon"></i>
                    <span class="menu-title">Settings</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->is('admin/site-setting*') || request()->is('admin/app-setting*') || request()->is('admin/notifications*') ? 'show' : '' }}"
                    id="setting">
                    <ul class="nav flex-column sub-menu">
                        @if (Auth::user()->roles->contains('1'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/site-setting*') ? 'active' : '' }}"
                                    href="{{ route('admin.site-setting.index') }}"> Site Setting </a>
                            </li>
                        @endif
                        @if (Auth::user()->roles->contains('1'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/app-setting*') ? 'active' : '' }}"
                                    href="{{ route('admin.app-setting.index') }}"> App Setting </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>
        @endif

        <li class="nav-item {{ request()->is('admin/logout*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi mdi-logout menu-icon"></i>
                <span class="menu-title">Logout </span>
                {{-- <span class="menu-title">Logout</span> --}}
            </a>
        </li>
    </ul>
</nav>
