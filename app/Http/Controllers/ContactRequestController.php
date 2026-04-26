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
            ->queue($mailable);

        $previous = url()->previous();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '';
        $previousHost = parse_url($previous, PHP_URL_HOST) ?? '';
        $previousUrl = ($previousHost !== '' && $previousHost === $appHost)
            ? strtok($previous, '#')
            : route('contact.show');

        return redirect()
            ->to($previousUrl.'#contact')
            ->with('status', __('hermes.home.contact_success'));
    }
}
