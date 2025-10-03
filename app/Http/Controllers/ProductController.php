<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Lišákův Obchod API", version: "1.0")]
class ProductController extends Controller
{
    /**
     * Zobrazí seznam produktů s možností filtrování.
     */
    #[OA\Get(
        path: "/api/products",
        summary: "Získá seznam produktů",
        tags: ["Produkty"],
        parameters: [
            new OA\Parameter(name: "name", in: "query", description: "Filtrování podle názvu produktu"),
            new OA\Parameter(name: "stock_from", in: "query", description: "Filtrovat produkty s počtem kusů VĚTŠÍM NEBO ROVNO zadané hodnotě"),
            new OA\Parameter(name: "stock_to", in: "query", description: "Filtrovat produkty s počtem kusů MENŠÍM NEBO ROVNO zadané hodnotě")
        ],
        responses: [new OA\Response(response: 200, description: "Úspěšná operace")]
    )]
    public function index(Request $request): Collection
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', '%' . $request->input('name') . '%');
        }

        if ($request->filled('stock_from') && is_numeric($request->input('stock_from'))) {
            $query->where('stock_quantity', '>=', (int) $request->input('stock_from'));
        }

        if ($request->filled('stock_to') && is_numeric($request->input('stock_to'))) {
            $query->where('stock_quantity', '<=', (int) $request->input('stock_to'));
        }

        return $query->orderBy('id')->get();
    }

    /**
     * Uloží nově vytvořený produkt do databáze.
     */
    #[OA\Post(
        path: "/api/products",
        summary: "Vytvoří nový produkt",
        tags: ["Produkty"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Jablko"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 10.50),
                    new OA\Property(property: "stock_quantity", type: "integer", example: 100),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: "Produkt úspěšně vytvořen")]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Aktualizuje specifický produkt v databázi.
     */
    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Aktualizuje produkt (pouze podle ID)",
        tags: ["Produkty"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, description: "ID produktu pro aktualizaci")],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Hruška"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 12.00),
                    new OA\Property(property: "stock_quantity", type: "integer", example: 50),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Produkt úspěšně aktualizován"),
            new OA\Response(response: 404, description: "Produkt nenalezen")
        ]
    )]
    public function update(Request $request, Product $product): Product
    {
        // Pro 'name' přidáme pravidlo, aby ignorovalo unikátnost pro právě aktualizovaný produkt
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:products,name,' . $product->id,
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
        ]);

        // Záznam do historie cen
        if (isset($validated['price']) && (float) $validated['price'] !== (float) $product->price) {
            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => $product->price,
                'new_price' => $validated['price'],
                'changed_at' => now(),
            ]);
        }
        
        // Záznam do historie skladu
        if (isset($validated['stock_quantity']) && (int) $validated['stock_quantity'] !== (int) $product->stock_quantity) {
            StockHistory::create([
                'product_id' => $product->id,
                'old_stock' => $product->stock_quantity,
                'new_stock' => $validated['stock_quantity'],
                'changed_at' => now(),
            ]);
        }

        $product->update($validated);
        return $product->refresh();
    }

    /**
     * Odstraní specifický produkt z databáze.
     */
    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Smaže produkt (pouze podle ID)",
        tags: ["Produkty"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, description: "ID produktu ke smazání")],
        responses: [
            new OA\Response(response: 204, description: "Produkt úspěšně smazán"),
            new OA\Response(response: 404, description: "Produkt nenalezen")
        ]
    )]
    public function destroy(Product $product): Response
    {
        $product->delete();
        return response()->noContent();
    }

    /**
     * Zobrazí historii cen pro daný produkt.
     */
    #[OA\Get(
        path: "/api/products/{identifier}/price-history",
        summary: "Získá historii cen produktu podle ID nebo jména",
        tags: ["Produkty"],
        parameters: [new OA\Parameter(name: "identifier", in: "path", required: true, description: "ID (číslo) nebo jméno (text) produktu")],
        responses: [
            new OA\Response(response: 200, description: "Úspěšná operace"),
            new OA\Response(response: 404, description: "Produkt nenalezen")
        ]
    )]
    public function priceHistory(string $identifier): Collection
    {
        $product = $this->findProductByIdentifier($identifier);
        return $product->priceHistory()->orderBy('changed_at', 'desc')->get();
    }

    /**
     * Zobrazí historii skladu pro daný produkt.
     */
    #[OA\Get(
        path: "/api/products/{identifier}/stock-history",
        summary: "Získá historii skladu produktu podle ID nebo jména",
        tags: ["Produkty"],
        parameters: [new OA\Parameter(name: "identifier", in: "path", required: true, description: "ID (číslo) nebo jméno (text) produktu")],
        responses: [
            new OA\Response(response: 200, description: "Úspěšná operace"),
            new OA\Response(response: 404, description: "Produkt nenalezen")
        ]
    )]
    public function stockHistory(string $identifier): Collection
    {
        $product = $this->findProductByIdentifier($identifier);
        return $product->stockHistory()->orderBy('changed_at', 'desc')->get();
    }

    /**
     * Najde produkt podle ID nebo jména. Pokud nenajde, vrátí chybu 404.
     */
    private function findProductByIdentifier(string $identifier): Product
    {
        $query = Product::query();

        if (is_numeric($identifier)) {
            $query->where('id', $identifier);
        } else {
            $query->where('name', 'ILIKE', $identifier);
        }

        return $query->firstOrFail();
    }
}