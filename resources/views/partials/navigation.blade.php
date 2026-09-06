<nav class="site-nav sticky top-0 z-50 border-b border-white/60 bg-white/85 backdrop-blur-xl" x-data="{ mobileMenuOpen: false }">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span
                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white">OD</span>
            <div>
                <p class="text-lg font-bold text-slate-900">OpsDesk</p>
                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Portfolio Workspace</p>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <ul class="hidden items-center gap-6 lg:flex">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard', 'homepage') ? 'nav-link-active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('products.all') }}"
                    class="nav-link {{ request()->routeIs('products.*', 'product.*') ? 'nav-link-active' : '' }}">
                    Inventory
                </a>
            </li>
            <li>
                <a href="{{ route('todo.index') }}"
                    class="nav-link {{ request()->routeIs('todo.*') ? 'nav-link-active' : '' }}">
                    Tasks
                </a>
            </li>
            <li>
                <a href="{{ route('expenses.index') }}"
                    class="nav-link {{ request()->routeIs('expenses.*') ? 'nav-link-active' : '' }}">
                    Finance
                </a>
            </li>
            <li>
                <a href="{{ route('notifications.index') }}"
                    class="nav-link {{ request()->routeIs('notifications.*') ? 'nav-link-active' : '' }}">
                    Notifications
                </a>
            </li>
            <li>
                <a href="{{ route('contact.index') }}"
                    class="nav-link {{ request()->routeIs('contact.*') ? 'nav-link-active' : '' }}">
                    Contact
                </a>
            </li>
            @auth
                @if (Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.*') ? 'nav-link-active' : '' }}">
                            Admin
                        </a>
                    </li>
                @endif
            @endauth
        </ul>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                class="rounded-lg border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-3">

            @guest
                <a href="{{ route('login') }}"
                    class="hidden sm:inline-block rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="hidden sm:inline-block rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Register
                </a>
            @else
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-slate-300 sm:px-4">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-800">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50">Profile settings</a>

                        <button type="button" data-theme-toggle
                            class="theme-toggle bg-white px-4 py-2 text-sm font-semibold">
                            <span data-theme-label>Dark mode</span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-3 text-left text-sm text-slate-700 hover:bg-slate-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false"
        class="border-t border-slate-200 bg-white lg:hidden">
        <ul class="space-y-2 px-4 py-4">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('dashboard', 'homepage') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('products.all') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('products.*', 'product.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Inventory
                </a>
            </li>
            <li>
                <a href="{{ route('todo.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('todo.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Tasks
                </a>
            </li>
            <li>
                <a href="{{ route('expenses.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('expenses.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Finance
                </a>
            </li>
            <li>
                <a href="{{ route('notifications.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('notifications.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Notifications
                </a>
            </li>
            <li>
                <a href="{{ route('contact.index') }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('contact.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                    Contact
                </a>
            </li>
            @auth
                @if (Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('admin.*') ? 'bg-slate-100 text-slate-900' : '' }}">
                            Admin
                        </a>
                    </li>
                @endif
            @endauth
            @guest
                <li class="border-t border-slate-200 pt-2">
                    <a href="{{ route('login') }}"
                        class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        Login
                    </a>
                </li>
                <li>
                    <a href="{{ route('register') }}"
                        class="block rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Register
                    </a>
                </li>
            @endguest
        </ul>
    </div>
</nav>
