<?php

namespace App\Notifications;

use App\Models\Election;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VoterCreated extends Notification
{
    use Queueable;

    public function __construct(
        public Election $election
    ) {}

    public function via($notifiable): array
    {
        return ['mail']; // can add sms later
    }

    public function toMail($notifiable): MailMessage
    {
        $token = encrypt([
            'voter_id' => $notifiable->id,
            'election_id' => $this->election->id,
        ]);

        $link = url("/voter/verify/{$token}");

        return (new MailMessage)
            ->subject('Email Verification')
            ->line('Please verify your voter account to continue.')
            ->action('Verify Account', $link);
    }
}