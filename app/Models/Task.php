<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'status'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const STATUSES = ['todo', 'in_progress', 'done'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'done' => 'Done',
            default => $this->status,
        };
    }
}
