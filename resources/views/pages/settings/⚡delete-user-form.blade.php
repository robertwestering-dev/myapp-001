<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-4">
    <style>
        .delete-user-copy {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            line-height: 1.75rem;
            color: #5a6762;
        }

        .delete-user-link-wrap {
            display: inline;
        }

        .delete-user-link-wrap > * {
            display: inline;
        }

        .delete-user-link-wrap :where(button, a),
        .delete-user-link {
            display: inline;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
            appearance: none;
            min-height: 0;
            vertical-align: baseline;
            font: inherit;
            font-weight: 700;
            line-height: inherit;
            color: #a84a19;
            text-decoration: underline;
            text-underline-offset: 4px;
            cursor: pointer;
        }
    </style>

    <div class="delete-user-copy">
        <span>{{ __('hermes.settings.delete_account.prefix') }} </span>
        <span class="delete-user-link-wrap">
            <flux:modal.trigger name="confirm-user-deletion">
                <button type="button" class="delete-user-link" data-test="delete-user-link">{{ __('hermes.settings.delete_account.link') }}</button>
            </flux:modal.trigger>
        </span>
        <span> {{ __('hermes.settings.delete_account.suffix') }}</span>
    </div>

    <livewire:pages::settings.delete-user-modal />
</section>
