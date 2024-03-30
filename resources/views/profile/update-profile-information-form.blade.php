<x-form-section submit="updateProfileInformation">


    <x-slot name="title">
        {{ __('プロフィール情報') }}
    </x-slot>

    <x-slot name="description">
        {{ __('アカウントのプロフィール情報を更新します。') }}
    </x-slot>

    <x-slot name="form">
 
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="login_id" value="{{ __('名前') }}" />
            <x-input id="login_id" type="text" class="mt-1 block w-full" wire:model.defer="state.login_id" autocomplete="login_id" />
            <x-input-error for="login_id" class="mt-2" />
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __('保存しました。') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('保存') }}
        </x-button>
    </x-slot>
</x-form-section>