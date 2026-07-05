<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\User;
use App\Models\VotersImportLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportCompleted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Election        $election,
        private readonly VotersImportLog $importLog,
        private readonly string          $batchCode,
        private readonly int             $imported,
        private readonly int             $failed,
        private readonly array           $rowErrors = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $total   = $this->imported + $this->failed;
        $subject = $this->failed > 0
            ? "Voter import completed with {$this->failed} error(s) — {$this->election->name}"
            : "Voter import completed successfully — {$this->election->name}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your voter import for **{$this->election->name}** has finished.")
            ->line('')
            ->line("**Batch Code:** {$this->batchCode}")
            ->line("**File:** {$this->importLog->file_name}")
            ->line("**Total rows processed:** {$total}")
            ->line("**Successfully imported:** {$this->imported}")
            ->line("**Failed:** {$this->failed}");

        if ($this->failed > 0 && ! empty($this->rowErrors)) {
            $mail->line('')->line('**Failed rows:**');
            foreach (array_slice($this->rowErrors, 0, 20) as $err) {
                $mail->line("Row {$err['row']}: {$err['message']}");
            }
            if (count($this->rowErrors) > 20) {
                $remaining = count($this->rowErrors) - 20;
                $mail->line("... and {$remaining} more. Please review your file and re-import the failed rows.");
            }
        }

        return $mail->line('')->line('Thank you.');
    }
}
