<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationCreated extends Notification
{
    use Queueable;

    public function __construct(
        public Organization $organization,
        public string $role
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Organization Onboarding Notification')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your organization "'.$this->organization->name.'" has been created successfully.')
            ->line('You are the '.$this->role.' of the organization.')
            ->line('You can now login to get started with onboarding.')
            //->action('Login', config('app.url').'/login')
            ->line('Thank you for using our platform!');
    }
}