<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
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

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
      <!-- Remember Me -->
      <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('入力情報を保存しておく') }}</span>
            </label>

            <x-primary-button>
                {{ __('ログイン') }}
            </x-primary-button>
        </div>


        </div>
    </form>
</x-guest-layout>
