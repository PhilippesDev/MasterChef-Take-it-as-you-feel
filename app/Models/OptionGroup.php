<?php

namespace App\Models;

use App\Enums\OptionGroupType;
use Database\Factories\OptionGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionGroup extends Model
{
    /** @use HasFactory<OptionGroupFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dish_id',
        'name',
        'type',
        'is_required',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OptionGroupType::class,
            'is_required' => 'boolean',
        ];
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Valeurs selectionnables du groupe.
     */
    public function values(): HasMany
    {
        return $this->hasMany(OptionValue::class)->orderBy('id');
    }
}
