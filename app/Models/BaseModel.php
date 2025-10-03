<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Abstraktní základní model, který vynucuje definici klíčových vlastností
 * pomocí otypovaných metod. Všechny aplikační modely by měly dědit z něj.
 */
abstract class BaseModel extends Model
{
    /**
     * Abstraktní metoda, která nutí každý model definovat svou tabulku.
     */
    abstract protected function getTableName(): string;

    /**
     * Abstraktní metoda pro definici hromadně přiřaditelných atributů.
     * @return array<int, string>
     */
    abstract protected function getFillableAttributes(): array;

    /**
     * Abstraktní metoda pro definici přetypování atributů.
     * @return array<string, string>
     */
    abstract protected function getAttributeCasts(): array;

    /**
     * Metoda pro definici, zda model používá časové značky.
     * Ve výchozím stavu je zapnuto, ale modely to mohou přepsat.
     */
    protected function hasTimestamps(): bool
    {
        return true;
    }

    /**
     * Konstruktor modelu, který se volá při jeho inicializaci.
     * Zde nastavíme standardní (neotypované) vlastnosti Eloquentu
     * na základě hodnot z nových otypovaných metod.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = $this->getTableName();
        $this->fillable = $this->getFillableAttributes();
        $this->timestamps = $this->hasTimestamps();
        $this->casts = $this->getAttributeCasts();
    }
}

