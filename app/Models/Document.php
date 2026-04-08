<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model
{
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    protected $fillable = [
        'original_name',
        'stored_path',
        'mime_type',
        'file_size',
        'status',
        'ocr_text',
        'ai_raw_response',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}
