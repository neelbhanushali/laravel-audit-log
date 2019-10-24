<?php

namespace NeelBhanushali\LaravelAuditLog\Traits;

use NeelBhanushali\LaravelAuditLog\Models\Audit;

trait Auditable
{
    public function audit()
    {
        return $this->morphMany(Audit::class, 'entity');
    }

    protected static function bootAuditable()
    {
        static::created(function ($record) {
            if (!defined(get_class($record) . '::AUDIT')) {
                $audit = [
                    'relation' => 'self',
                    'entity_id' => 'key',
                    'entity_type' => get_class($record)
                ];
            } else {
                $audit = $record::AUDIT;
            }

            $audit_trail = new Audit;

            $audit_trail->entity_type = self::getValue($record, $audit, 'entity_type');
            $audit_trail->entity_id = self::getValue($record, $audit, 'entity_id');
            $audit_trail->relation = $audit['relation'];

            if ($audit_trail->relation != 'self') {
                $audit_trail->related_type =  self::getValue($record, $audit, 'entity_type');
                $audit_trail->related_id = self::getValue($record, $audit, 'entity_id');
                $audit_trail->entity_type = self::getValue($record, $audit, 'parent_type');
                $audit_trail->entity_id = self::getValue($record, $audit, 'parent_id');
            }

            $audit_trail->before_transaction = [];
            $audit_trail->after_transaction = self::removeTimestamps($record, $record->attributes);
            $audit_trail->difference = self::removeTimestamps($record, $record->attributes);

            $audit_trail->activity = 'create';
            $audit_trail->user_id = request()->user() ? request()->user()->id : null;

            $audit_trail->token = request()->bearerToken();
            $audit_trail->ip = request()->ip();
            $audit_trail->ua = request()->userAgent();
            $audit_trail->url = url()->full();

            $audit_trail->save();
        });

        static::updated(function ($record) {
            if (!defined(get_class($record) . '::AUDIT')) {
                $audit = [
                    'relation' => 'self',
                    'entity_id' => 'key',
                    'entity_type' => get_class($record)
                ];
            } else {
                $audit = $record::AUDIT;
            }

            $audit_trail = new Audit;

            $audit_trail->entity_type = self::getValue($record, $audit, 'entity_type');
            $audit_trail->entity_id = self::getValue($record, $audit, 'entity_id');
            $audit_trail->relation = $audit['relation'];

            if ($audit_trail->relation != 'self') {
                $audit_trail->related_type =  self::getValue($record, $audit, 'entity_type');
                $audit_trail->related_id = self::getValue($record, $audit, 'entity_id');
                $audit_trail->entity_type = self::getValue($record, $audit, 'parent_type');
                $audit_trail->entity_id = self::getValue($record, $audit, 'parent_id');
            }

            $before_transaction = Audit::where([
                'entity_type' => $audit_trail->entity_type,
                'entity_id' => $audit_trail->entity_id,
                'related_type' => $audit_trail->relation == 'self' ? null : $audit_trail->related_type,
                'related_id' => $audit_trail->relation == 'self' ? null : $audit_trail->related_id,
            ])->latest()->first();

            $audit_trail->before_transaction = $before_transaction->after_transaction;
            $audit_trail->after_transaction = self::removeTimestamps($record, $record->attributes);
            $audit_trail->difference = self::removeTimestamps($record, $record->getChanges());

            $audit_trail->activity = 'update';
            $audit_trail->user_id = request()->user() ? request()->user()->id : null;

            $audit_trail->token = request()->bearerToken();
            $audit_trail->ip = request()->ip();
            $audit_trail->ua = request()->userAgent();
            $audit_trail->url = url()->full();

            $audit_trail->save();
        });
    }

    protected static function removeTimestamps($record, $data)
    {
        $except = [$record->getCreatedAtColumn(), $record->getUpdatedAtColumn()];
        if (method_exists($record, 'getDeletedAtColumn')) {
            $except[] = $record->getDeletedAtColumn();
        }

        return collect($data)->except($except)->toArray();
    }

    protected static function getValue($record, $audit, $key)
    {
        $value = $audit[$key];
        return class_exists($value) ? $value : ($value == 'key' ? $record->getKey() : $record->{$value});
    }
}
