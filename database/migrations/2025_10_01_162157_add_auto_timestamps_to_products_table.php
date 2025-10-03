<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vytvoříme funkci, která bude aktualizovat `updated_at`
        // Tuto funkci pak můžeme použít i pro jiné tabulky
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ language "plpgsql";
        ');

        // Vytvoříme samotný spouštěč (trigger) pro tabulku `products`
        DB::unprepared('
            CREATE TRIGGER update_products_updated_at
            BEFORE UPDATE ON products
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');

        // Nastavíme výchozí hodnotu pro `created_at` a `updated_at` na aktuální čas
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Smažeme spouštěč
        DB::unprepared('DROP TRIGGER IF EXISTS update_products_updated_at ON products;');
        // Smažeme funkci (volitelně, pokud ji nepoužívají jiné tabulky)
        DB::unprepared('DROP FUNCTION IF EXISTS update_updated_at_column();');

        // Vrátíme sloupce do původního stavu (bez výchozí hodnoty)
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('created_at')->default(null)->change();
            $table->timestamp('updated_at')->default(null)->change();
        });
    }
};