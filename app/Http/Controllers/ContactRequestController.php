<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            ->queue($mailable);

        $previous = url()->previous();
        $previousUrl = Str::startsWith($previous, config('app.url'))
            ? strtok($previous, '#')
            : route('contact.show');

        return redirect()
            ->to($previousUrl.'#contact')
            ->with('status', __('hermes.home.contact_success'));
    }
}
