<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medium extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'user_id', 'filename', 'original_name',
        'mime_type', 'size', 'path', 'alt_text'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
