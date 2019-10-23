<?php

namespace NeelBhanushali\LaravelAuditLog\Traits;

use NeelBhanushali\LaravelAuditLog\Models\Audit;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($record) {
            if (!defined(get_class($record) . '::AUDIT')) {
                $audit = [
                    'relation' => 'self',
                    'entity_identifier' => 'key'
                ];
            } else {
                $audit = $record::AUDIT;
            }

            $audit_trail = new Audit;

            $audit_trail->entity_type = get_class($record);
            $audit_trail->entity_id = $audit['entity_identifier'] == 'key' ? $record->getKey() : $record->{$audit['entity_identifier']};
            $audit_trail->relation = $audit['relation'];

            if ($audit_trail->relation != 'self') {
                $audit_trail->related_type =  get_class($record);
                $audit_trail->related_id = $audit['self_identifier'] == 'key' ? $record->getKey() : $record->{$audit['self_identifier']};
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
    }

    protected static function removeTimestamps($record, $data)
    {
        $except = [$record->getCreatedAtColumn(), $record->getUpdatedAtColumn()];
        if (method_exists($record, 'getDeletedAtColumn')) {
            $except[] = $record->getDeletedAtColumn();
        }

        return collect($data)->except($except)->toArray();
    }
}
