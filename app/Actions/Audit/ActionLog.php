<?php
declare(strict_types=1);
namespace App\Actions\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Facades\Activity as ActivityFacade;

class ActionLog
{
    use AsAction;

    public function handle(string $event, ?Model $subject = null, array $properties = []): void
    {
        $log = ActivityFacade::causedBy(Auth::user())
            ->withProperties(array_merge($properties, [
                'ip'         => global_data('ip'),
                'user_agent' => global_data('user_agent'),
            ]))
            ->log($event);

        if ($subject) {
            $log->subject()->associate($subject)->save();
        }
    }
}
