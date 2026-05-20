<?php

namespace App\Models;

use Database\Factories\ContactFormSubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'email',
    'message',
    'privacy_consent',
    'ip_address',
    'user_agent',
    'referrer',
    'mail_sent_at',
    'mail_failed_at',
])]
class ContactFormSubmission extends Model
{
    /** @use HasFactory<ContactFormSubmissionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'privacy_consent' => 'boolean',
            'mail_sent_at' => 'datetime',
            'mail_failed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
