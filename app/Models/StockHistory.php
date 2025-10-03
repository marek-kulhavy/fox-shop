<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int $old_quantity
 * @property int $new_quantity
 * @property \Illuminate\Support\Carbon $changed_at
 *
 * @property-read \App\Models\Product $product
 */
class StockHistory extends BaseModel
{
    /**
     * Vrací název databázové tabulky přiřazené k modelu.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return 'stock_history';
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
            'old_quantity',
            'new_quantity',
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
        return false; 
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

