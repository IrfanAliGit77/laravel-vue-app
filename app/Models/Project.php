<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\UsesUuid;

class Project extends Model implements Auditable
{
    use SoftDeletes, UsesUuid;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'description', 'status', 'due_date', 'metadata', 'document'
    ];

    protected $casts = [
        'metadata'  => 'array',
        'due_date'  => 'datetime',
        'status'    => 'boolean'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
