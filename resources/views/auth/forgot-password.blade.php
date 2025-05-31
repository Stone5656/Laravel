<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your id address and we will id you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.id') }}">
        @csrf

        <!-- id Address -->
        <div>
            <x-input-label for="id" :value="__('id')" />
            <x-text-input id="id" class="block mt-1 w-full" type="id" name="id" :value="old('id')" required autofocus />
            <x-input-error :messages="$errors->get('id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('id Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
