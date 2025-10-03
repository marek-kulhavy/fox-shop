<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Models\PriceHistory;
use App\Models\StockHistory;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property int $stock_quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PriceHistory> $priceHistory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockHistory> $stockHistory
 */
class Product extends BaseModel
{

    /**
     * Vrací název databázové tabulky přiřazené k modelu.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return 'products';
    }

    /**
     * Vrací pole atributů, které je možné hromadně přiřadit.
     *
     * @return string[]
     */
    protected function getFillableAttributes(): array
    {
        return [
            'name',
            'price',
            'stock_quantity',
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
            'price' => 'float',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * Definuje vztah "jeden k mnoha" k historii cen.
     * Jeden produkt může mít mnoho záznamů o změně ceny.
     */
    public function priceHistory(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }

    /**
     * Definuje vztah "jeden k mnoha" k historii skladu.
     * Jeden produkt může mít mnoho záznamů o změně počtu kusů.
     */
    public function stockHistory(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }
}

