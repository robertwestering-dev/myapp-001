<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactFormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ContactRequestController extends Controller
{
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $consentGiven = $request->boolean('privacy_consent');

        $submission = ContactFormSubmission::query()->create([
            'user_id' => $request->user()?->getKey(),
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'message' => $attributes['message'],
            'privacy_consent' => $consentGiven,
            'ip_address' => $request->ip(),
            'user_agent' => $this->limitedHeader($request->userAgent()),
            'referrer' => $this->limitedHeader($request->headers->get('referer')),
        ]);

        $mailable = (new ContactFormSubmitted(
            name: $attributes['name'],
            email: $attributes['email'],
            messageBody: $attributes['message'],
            consentGiven: $consentGiven,
        ))->replyTo($attributes['email'], $attributes['name']);

        try {
            Mail::to(
                config('contact.recipient_address'),
                config('contact.recipient_name'),
            )
                ->send($mailable);

            $submission->forceFill(['mail_sent_at' => now()])->save();
        } catch (Throwable $exception) {
            $submission->forceFill(['mail_failed_at' => now()])->save();

            throw $exception;
        }

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

    protected function limitedHeader(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::limit($value, 1000, '');
    }
}
