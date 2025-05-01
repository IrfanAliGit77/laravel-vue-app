<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\UsesUuid;

class Comment extends Model implements Auditable
{
    use SoftDeletes, UsesUuid;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'task_id', 'comment', 'extra', 'is_approved', 'commented_at'
    ];

    protected $casts = [
        'extra'        => 'array',
        'commented_at' => 'datetime',
        'is_approved'  => 'boolean'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
