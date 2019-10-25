<?php

namespace NeelBhanushali\LaravelAuditLog\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_trail';

    const UPDATED_AT = null;

    protected $casts = [
        'before_transaction'   => 'array',
        'after_transaction'   => 'array',
        'difference' => 'array'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function related()
    {
        return $this->morphTo();
    }
}
