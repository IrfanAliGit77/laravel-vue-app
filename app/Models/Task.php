<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\UsesUuid;

class Task extends Model implements Auditable
{
    use SoftDeletes, UsesUuid;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'project_id', 'title', 'details', 'completed', 'due_date'
    ];

    protected $casts = [
        'details'   => 'array',
        'due_date'  => 'datetime',
        'completed' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
