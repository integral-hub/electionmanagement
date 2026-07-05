<?php

namespace App\Models\Concerns\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait Auditable
{
    use LogsActivity;

    /**
     * Activity logging configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('audit')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ]);
    }

    /**
     *  Configure additinal settings.
     */
    public function tapActivity(Activity $activity): void
    {
        $activity->organization_id =  global_data('org_id');

        $activity->properties = collect(
            $activity->properties ?? []
        )->merge([
            'ip' => global_data('ip'),
            'user_agent' => global_data('user_agent'),
        ]);
    }
}