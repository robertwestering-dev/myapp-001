<?php

namespace App\Notifications;

use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForumReplyPosted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ForumThread $thread,
        public readonly ForumReply $reply,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('hermes.notifications.forum_reply.subject', ['title' => $this->thread->title]))
            ->greeting(__('hermes.notifications.forum_reply.greeting', ['name' => $notifiable->first_name ?? $notifiable->name]))
            ->line(__('hermes.notifications.forum_reply.line1', [
                'author' => $this->reply->author->name,
                'title' => $this->thread->title,
            ]))
            ->action(__('hermes.notifications.forum_reply.cta'), route('forum.show', $this->thread))
            ->line(__('hermes.notifications.forum_reply.line2'))
            ->salutation(__('hermes.notifications.salutation', ['app' => config('app.name')]));
    }
}
