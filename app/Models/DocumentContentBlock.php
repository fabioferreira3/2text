<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentContentBlock extends Model
{
    protected $guarded = ['id'];

    use HasFactory, HasUuids;

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentContentBlockVersion::class);
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

    public function hasPastVersions()
    {
        return $this->versions()->count() ? $this->versions()->where('version', '<', $this->versions()
            ->active()->first()->version)->count() > 0 : false;
    }

    public function hasFutureVersions()
    {
        return $this->versions()->count() ? $this->versions()->where('version', '>', $this->versions()
            ->active()->first()->version)->count() > 0 : false;
    }

    public function rollbackVersion()
    {
        if (!$this->hasPastVersions()) {
            return false;
        }

        $currentVersion = $this->versions()->active()->first();
        $previousVersion = $this->versions()->where('version', '<', $currentVersion->version)
            ->orderBy('version', 'DESC')->first();
        $this->content = $previousVersion->content;
        $this->saveQuietly();
        $previousVersion->update(['active' => true]);
        $currentVersion->update(['active' => false]);
    }

    public function fastForwardVersion()
    {
        if (!$this->hasFutureVersions()) {
            return false;
        }

        $currentVersion = $this->versions()->active()->first();
        $nextVersion = $this->versions()->where('version', '>', $currentVersion->version)
            ->orderBy('version', 'ASC')->first();
        $this->content = $nextVersion->content;
        $this->saveQuietly();
        $nextVersion->update(['active' => true]);
        $currentVersion->update(['active' => false]);
    }

    public function scopeNotOfImageType($query)
    {
        return $query->whereNotIn('type', ['image', 'media_file_image']);
    }

    public function scopeOfTextType($query)
    {
        return $query->where('type', 'text');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('order', 'ASC');
        });
        static::created(function ($contentBlock) {
            $contentBlock->versions()->create([
                'content' => $contentBlock->content,
                'version' => 1
            ]);
        });
        static::updated(function ($contentBlock) {
            if ($contentBlock->wasChanged('content')) {
                $latest = $contentBlock->versions()->active()->first();
                $latest->update(['active' => false]);
                $contentBlock->versions()->create([
                    'content' => $contentBlock->content,
                    'version' => $latest->version + 1,
                    'active' => true
                ]);
            }
        });
    }
}
