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

    public function getMediaFile()
    {
        if ($this->type === 'media_file_image') {
            return MediaFile::where('id', $this->content)->first();
        }

        return null;
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

    public function scopeNotOfImageType($query)
    {
        return $query->whereNotIn('type', ['image', 'media_file_image']);
    }
}
