<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactRequestController extends Controller
{
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $mailable = (new ContactFormSubmitted(
            name: $attributes['name'],
            email: $attributes['email'],
            messageBody: $attributes['message'],
            consentGiven: $request->boolean('privacy_consent'),
        ))->replyTo($attributes['email'], $attributes['name']);

        Mail::to(
            config('contact.recipient_address'),
            config('contact.recipient_name'),
        )
            ->send($mailable);

        return redirect()
            ->to(route('home').'#contact')
            ->with('status', __('hermes.home.contact_success'));
    }
}
