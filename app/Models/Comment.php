<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $appends = [
        'is_edited',
    ];

    protected function isEdited(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at !== null
                && $this->updated_at !== null
                && ! $this->created_at->equalTo($this->updated_at)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
