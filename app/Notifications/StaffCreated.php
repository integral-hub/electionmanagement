<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffCreated extends Notification
{
    use Queueable;

    public function __construct(
        protected string $password
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $organization = $notifiable->organization?->name ?? 'our organization';

        return (new MailMessage)
            ->subject('Onboarding Notification')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Welcome onboard to {$organization} as {$notifiable->getRoleNames()->first()}.")
            ->line('An account has been created for you.')
            ->line('Email: '.$notifiable->email)
            ->line('Temporary Password: '.$this->password)
            ->line('Please change your password immediately after logging in.')
           // ->action('Login', config('app.frontend_url') . '/login')
            ->line('Thank you for joining us!');
    }

}