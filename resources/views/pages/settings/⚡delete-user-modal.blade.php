<?php

use App\Livewire\Actions\Logout;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public string $password = '';

    public function anonymizeUser(Logout $logout, AuditLogger $audit): void
    {
        $this->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $audit->log('user.anonymized', "Account geanonimiseerd: {$user->name} ({$user->email})", $user);

        $user->anonymizeForStatistics();

        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
    <style>
        .delete-user-modal-actions {
            justify-content: flex-end;
        }
    </style>

    <form method="POST" wire:submit="anonymizeUser" class="space-y-6">
        <div>
            <flux:text class="mt-4 block text-base leading-7 text-zinc-600">
                {!! nl2br(e(__('hermes.settings.delete_account.confirmation'))) !!}
            </flux:text>
        </div>

        <flux:field>
            <flux:label>{{ __('hermes.settings.delete_account.password_label') }}</flux:label>
            <flux:input wire:model="password" type="password" autocomplete="current-password" />
            <flux:error name="password" />
        </flux:field>

        <x-user-action-row align="end" class="delete-user-modal-actions">
            <flux:modal.close>
                <button type="button" class="pill pill--neutral">{{ __('hermes.settings.delete_account.cancel') }}</button>
            </flux:modal.close>

            <button type="submit" class="pill" data-test="confirm-delete-user-button">
                {{ __('hermes.settings.delete_account.confirm') }}
            </button>
        </x-user-action-row>
    </form>
</flux:modal>
