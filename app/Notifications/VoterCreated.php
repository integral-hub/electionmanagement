<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VoterCreated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Election $election,
        public readonly ?string $password = null, // optional password
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Voter $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Registration confirmed — ' . $this->election->name)
            ->greeting('Hello ' . ($notifiable->voter_data['full_name'] ?? ''))
            ->line('Your registration for **' . $this->election->name . '** has been received.')
            ->line('Check your inbox for a separate email with your verification code.');

        // 👇 Only include password if it exists
        if ($this->password) {
            $mail->line('')
                ->line('Your temporary password is:')
                ->line('**' . $this->password . '**')
                ->line('Please change it after login.');
        }

        return $mail->salutation('— ' . config('app.name'));
    }
}