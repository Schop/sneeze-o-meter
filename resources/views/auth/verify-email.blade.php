<x-guest-layout>
    <x-slot name="title">{{ __('auth.verify_email') }}</x-slot>
    
    <div class="mb-3 text-muted">
        {{ __('auth.verify_email_text') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            {{ __('auth.verification_link_sent') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('auth.resend_verification_email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link btn-sm">
                {{ __('auth.log_out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
