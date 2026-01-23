<nav class="bg-white shadow-md mb-6 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            <!-- Logo / App name -->
            <div class="text-xl font-bold text-gray-800">
                Mini Apps
            </div>

            <!-- Left: Navigation links -->
            <ul class="flex space-x-6 items-center">
                <li>
                    <a href="{{ route('homepage') }}"
                        class="font-semibold transition-colors
                            {{ request()->routeIs('homepage') ? 'text-blue-600 border-b-2 border-blue-500 pb-1' : 'text-gray-600 hover:text-blue-600' }}">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.all') }}"
                        class="font-semibold transition-colors
                            {{ request()->routeIs('products.all') ? 'text-blue-600 border-b-2 border-blue-500 pb-1' : 'text-gray-600 hover:text-blue-600' }}">
                        Proizvodi
                    </a>
                </li>
                <li>
                    <a href="{{ route('contact.index') }}"
                        class="font-semibold transition-colors
                            {{ request()->routeIs('contact.index') ? 'text-blue-600 border-b-2 border-blue-500 pb-1' : 'text-gray-600 hover:text-blue-600' }}">
                        Kontakt
                    </a>
                </li>
                <li>
                    <a href="{{ route('todo.index') }}"
                        class="font-semibold transition-colors
                            {{ request()->routeIs('todo.index') ? 'text-blue-600 border-b-2 border-blue-500 pb-1' : 'text-gray-600 hover:text-blue-600' }}">
                        To-Do
                    </a>
                </li>
                <li>
                    <a href="{{ route('expenses.index') }}"
                        class="font-semibold transition-colors
                            {{ request()->routeIs('expenses.index') ? 'text-blue-600 border-b-2 border-blue-500 pb-1' : 'text-gray-600 hover:text-blue-600' }}">
                        Expenses
                    </a>
                </li>
            </ul>

            <!-- Right: Auth Links / Dropdown -->
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                       Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition">
                       Register
                    </a>
                @else
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-md z-50">
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

        </div>
    </div>
</nav>
