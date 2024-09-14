<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Actualizar contraseña') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Asegurate de usar una contraseña larga y segura.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Contraseña actual') }}" />
            <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current_password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="Contrasena" value="{{ __('Nueva contraseña') }}" />
            <x-input id="Contrasena" type="password" class="mt-1 block w-full" wire:model="state.Contrasena" autocomplete="Nueva_Contrasena" />
            <x-input-error for="Contrasena" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="Contrasena_confirmation" value="{{ __('Confirmar contraseña') }}" />
            <x-input id="Contrasena_confirmation" type="password" class="mt-1 block w-full" wire:model="state.Contrasena_confirmation" autocomplete="Nueva_Contrasena" />
            <x-input-error for="Contrasena_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
