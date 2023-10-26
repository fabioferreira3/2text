<?php

namespace App\Models;

use App\Enums\LanguageModels;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUsage extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array', 'model' => LanguageModels::class];
    protected $table = 'product_usage';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
