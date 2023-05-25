<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $table = 'document_history';

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function scopeOfField($query, $field)
    {
        return $query->where('description', $field);
    }
}
