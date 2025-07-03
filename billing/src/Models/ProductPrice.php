<?php

namespace Boy132\Billing\Models;

use Boy132\Billing\Enums\PriceInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $cost
 * @property PriceInterval $interval_type
 * @property int $interval_value
 * @property int $product_id
 * @property Product $product
 */
class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'cost',
        'interval_type',
        'interval_value',
    ];

    protected function casts(): array
    {
        return [
            'interval_type' => PriceInterval::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
