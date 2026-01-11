<?php

namespace App\Traits;

use App\Models\ProductActivityLog;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityTrait {

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(static::$logAttributes)
            ->setDescriptionForEvent(fn (string $event) => $this->getLogDescription($event));

    }

    protected static function generateDescription($model, $event, $mergedAttributes)
    {
        switch ($event) {
            case 'created':
                return [
                    'status'     => 'Add new',
                    'attributes' => (!empty($mergedAttributes)) ? $mergedAttributes : $model->getOriginal()
                ];

            case 'deleted':
                return [
                    'status'     => 'soft deleted',
                    'attributes' => (!empty($mergedAttributes)) ? $mergedAttributes : $model->getOriginal()
                ];

            case 'updated':
            case 'received':
            case 'accepted':
                return self::generateUpdateDescription($model, $mergedAttributes);

            default:
                return [
                    'status'     => $event,
                    'attributes' => $model->getDirty()
                ];
                break;
        }
    }

    protected static function generateUpdateDescription($model, $mergedAttributes)
    {
        $description = [];
        $dirty = $model->getDirty();
        $original = $model->getOriginal();

        // Exclude updated_at field
        unset($dirty['updated_at']);

        foreach ($dirty as $key => $newValue) {
            $oldValue = $original[$key] ?? null;

            if ($oldValue !== $newValue) {
                $description[$key] = [
                    'old' => $oldValue ?? 'null',
                    'new' => $newValue ?? 'null',
                ];
            }
        }
        return $description;
    }

}
