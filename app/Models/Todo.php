<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'open',
        'in_progress',
        'completed',
    ];

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];
}
