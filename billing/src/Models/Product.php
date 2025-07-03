<?php

namespace Boy132\Billing\Models;

use App\Models\Egg;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property int $cpu
 * @property int $memory
 * @property int $disk
 * @property int $swap
 * @property array $ports
 * @property array $tags
 * @property int $allocation_limit
 * @property int $database_limit
 * @property int $backup_limit
 * @property int $egg_id
 * @property Egg $egg
 * @property Collection|ProductPrice[] $prices
 */
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'egg_id',
        'cpu',
        'memory',
        'disk',
        'swap',
        'ports',
        'tags',
        'allocation_limit',
        'database_limit',
        'backup_limit',
    ];

    protected $attributes = [
        'ports' => '[]',
        'tags' => '[]',
    ];

    protected function casts(): array
    {
        return [
            'ports' => 'array',
            'tags' => 'array',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function egg(): BelongsTo
    {
        return $this->BelongsTo(Egg::class, 'egg_id');
    }
}
