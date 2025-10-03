<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property float $old_price
 * @property float $new_price
 * @property \Illuminate\Support\Carbon $changed_at
 *
 * @property-read \App\Models\Product $product
 */
class PriceHistory extends BaseModel
{
    /**
     * Vrací název databázové tabulky přiřazené k modelu.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return 'price_history';
    }

    /**
     * Vrací pole atributů, které je možné hromadně přiřadit.
     *
     * @return string[]
     */
    protected function getFillableAttributes(): array
    {
        return [
            'product_id',
            'old_price',
            'new_price',
            'changed_at',
        ];
    }

    /**
     * Definuje přetypování atributů modelu.
     *
     * @return array<string, string>
     */
    protected function getAttributeCasts(): array
    {
        return [
            'old_price' => 'float',
            'new_price' => 'float',
            'changed_at' => 'datetime',
        ];
    }

    /**
     * Určuje, zda model používá automatické časové značky (created_at, updated_at).
     *
     * @return bool
     */
    protected function hasTimestamps(): bool
    {
        return false; // Přepisujeme výchozí chování
    }

    /**
     * Definuje vztah "patří k" (belongs to), který spojuje záznam historie s produktem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

