<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentContentBlock extends Model
{
    protected $guarded = ['id'];

    use HasFactory, HasUuids;

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function getUrl()
    {
        if ($this->type === 'image' && $this->content) {
            $file = MediaFile::where('file_path', $this->content)->first();
            return $file ? $file->file_url : $this->content;
        }
        if (!$this->type === 'image' || !$this->content) {
            return null;
        }

        return $this->content;
    }
}
