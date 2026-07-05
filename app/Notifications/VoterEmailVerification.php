<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VoterEmailVerification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Election $election,
        public readonly ?string $otp = null,
        public readonly ?string $link = null,
        public readonly ?string $context = null, // verification | auth | import
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Voter $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject())
            ->greeting('Hello!')
            ->line($this->introLine());

        $hasOtp = !empty($this->otp);
        $hasLink = !empty($this->link);

        if ($hasOtp) {
            $mail->line('Use the code below. It expires in ' . config('otp.expiry') . ' minutes.')
                 ->line('')
                 ->line('## ' . $this->otp)
                 ->line('');
        }

        if ($hasOtp && $hasLink) {
            $mail->line('Or');
        }

        if ($hasLink) {
            $mail->line('click the link below to continue:')
                 ->action('Verify Email', $this->link)
                 ->line('');
        }

        return $mail
            ->line($this->footerLine())
            ->salutation('— ' . config('app.name'));
    }

    private function subject(): string
    {
        return match ($this->context) {
            'auth' => 'Login verification — ' . $this->election->name,
            default => 'Verify your voter account — ' . $this->election->name,
        };
    }

    private function introLine(): string
    {
        return match ($this->context) {
            'auth' => 'A login verification was requested for your voter account.',
            default => 'You are registered as a voter for **' . $this->election->name . '**.',
        };
    }

    private function footerLine(): string
    {
        return match ($this->context) {
            'auth' => 'If this login was not initiated by you, ignore this email.',
            default => 'If you did not register for this election, you can ignore this email.',
        };
    }
}