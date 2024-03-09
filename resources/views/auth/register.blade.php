<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('名前')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

    <!-- Position -->
<div class="mt-4">
    <x-input-label for="position" :value="__('役職')" />
    <select id="position" name="position" class="block w-full mt-1 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required autocomplete="position">
        <option value="owner" {{ old('position') == 'owner' ? 'selected' : '' }}>オーナー</option>
        <option value="employee" {{ old('position') == 'employee' ? 'selected' : '' }}>社員</option>
    </select>
    <x-input-error :messages="$errors->get('position')" class="mt-2" />
</div>

<!-- Phone Number -->
<div class="mt-4">
    <x-input-label for="phone_number" :value="__('電話番号')" />
    <x-text-input id="phone_number" class="block mt-1 w-full"
                    type="text"
                    name="phone_number"
                    :value="old('phone_number')"
                    required autocomplete="phone_number" />
    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
</div>


        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

            <!-- Password Confirmation -->
            <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('パスワード確認')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('既に登録済みですか?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('登録') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
