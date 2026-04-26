<?php

namespace App\Models;

use Database\Factories\ForumReplyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'forum_thread_id',
    'body',
])]
class ForumReply extends Model
{
    /** @use HasFactory<ForumReplyFactory> */
    use HasFactory;

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function renderedBody(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
