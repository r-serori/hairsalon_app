<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-4 border-b border-gray-100">
        <div class="flex justify-between h-16">
            <div class="flex">

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-3 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('一覧画面') }}
                    </x-nav-link>
                    <!-- Add more Navigation Links here -->
                    <x-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.index')">
                        {{ __('勤怠管理') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.index')">
                        {{ __('顧客名簿') }}
                    </x-nav-link>
                    <x-nav-link :href="route('daily_sales.index')" :active="request()->routeIs('daily_sales.index')">
                        {{ __('売上') }}
                    </x-nav-link>
                    <x-nav-link :href="route('schedules.index')" :active="request()->routeIs('schedules.index')">
                        {{ __('予約表') }}
                    </x-nav-link>
                    <x-nav-link :href="route('stocks.index')" :active="request()->routeIs('stocks.index')">
                        {{ __('在庫') }}
                    </x-nav-link>
                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">
                        {{ __('コース') }}
                    </x-nav-link>
                    <x-nav-link :href="route('options.index')" :active="request()->routeIs('options.index')">
                        {{ __('オプション') }}
                    </x-nav-link>
                    <x-nav-link :href="route('merchandises.index')" :active="request()->routeIs('merchandises.index')">
                        {{ __('物販') }}
                    </x-nav-link>
                    <x-nav-link :href="route('hairstyles.index')" :active="request()->routeIs('hairstyles.index')">
                        {{ __('髪型') }}
                    </x-nav-link>
                </div>

                <!-- Add more Navigation Links here -->

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->login_id }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <!-- Navigation Links -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('一覧画面') }}
            </x-responsive-nav-link>

            <!-- Navigation Links -->
            <x-responsive-nav-link :href=" route('attendances.index') " :active="request()->routeIs('dashboard')">
                {{ __('勤怠管理') }}
            </x-responsive-nav-link>
            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('dashboard')">
                {{ __('顧客名簿') }}
            </x-responsive-nav-link>

            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('daily_sales.index')" :active="request()->routeIs('dashboard')">
                {{ __('売上') }}
            </x-responsive-nav-link>

            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('schedules.index')" :active="request()->routeIs('dashboard')">
                {{ __('予約表') }}
            </x-responsive-nav-link>

            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('stocks.index')" :active="request()->routeIs('dashboard')">
                {{ __('在庫') }}
            </x-responsive-nav-link>


            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('dashboard')">
                {{ __('コース') }}
            </x-responsive-nav-link>

            <!-- Navigation Links -->

            <x-responsive-nav-link :href="route('options.index')" :active="request()->routeIs('dashboard')">
                {{ __('オプション') }}
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('merchandises.index')" :active="request()->routeIs('dashboard')">
                {{ __('物販') }}
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('hairstyles.index')" :active="request()->routeIs('dashboard')">
                {{ __('髪型') }}
            </x-responsive-nav-link>

        </div>

        <!-- Add more Responsive Navigation Links here -->

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->login_id }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>