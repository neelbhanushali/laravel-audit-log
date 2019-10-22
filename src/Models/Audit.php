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
}
