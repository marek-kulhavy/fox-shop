# Lišákův Obchod API 🦊

Jednoduché REST API pro evidenci produktů v obchodě, vytvořené jako řešení testovacího zadání. Aplikace je postavena na frameworku Laravel a je plně kontejnerizovaná pomocí Dockeru a Laravel Sail pro snadné spuštění.

## Klíčové Funkce

- [x] **REST API** popsané OpenAPI (Swagger) specifikací.
- [x] Kompletní **správa produktů** (CRUD - Vytvoření, Čtení, Úprava, Mazání).
- [x] Automatické **sledování historie cen a skladových zásob** při každé změně.
- [x] Endpointy pro **výpis historie** cen i skladu pro konkrétní produkt.
- [x] Možnost **získat produkt a jeho historii** jak podle **ID**, tak podle **jména**.
- [x] **Vyhledávání** produktů podle názvu (nerozlišuje velikost písmen).
- [x] **Filtrování** produktů podle rozmezí skladových zásob.
- [x] Plně **typaný a zdokumentovaný** kód v PHP.

---
## Použité Technologie

* **Backend:** PHP 8.2+, Laravel 11
* **Databáze:** PostgreSQL
* **Prostředí:** Docker, Laravel Sail
* **API Dokumentace:** OpenAPI (Swagger)

---
## Požadavky

Než začnete, ujistěte se, že máte na svém počítači nainstalované:
* **Docker** (Docker Desktop pro Windows/Mac nebo Docker Engine pro Linux)
* **WSL 2** (důrazně doporučeno pro uživatele Windows)
* **Git**
* **Composer**
* **PHP** (pro lokální běh Composeru)

---
## Zprovoznění Aplikace 🚀

1.  **Naklonujte si repozitář:**
    ```bash
    git clone https://github.com/marek-kulhavy/fox-shop.git
    cd fox-shop
    ```

2.  **Vytvořte konfigurační soubor `.env`:**
    ```bash
    cp .env.example .env
    ```
    *V souboru `.env` můžete v případě potřeby upravit nastavení, např. `DB_PASSWORD`. Výchozí heslo pro Sail je `password`.*

3.  **Nainstalujte PHP závislosti:**
    ```bash
    composer install
    ```

4.  **Spusťte Docker kontejnery (Sail):**
    *Příkaz spustí všechny potřebné služby. První spuštění může trvat několik minut, než se stáhnou Docker obrazy.*
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Vygenerujte klíč aplikace:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Spusťte databázové migrace:**
    *Vytvoří v databázi všechny potřebné tabulky.*
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

**Hotovo!** Aplikace a její REST API jsou nyní plně funkční.

---
## Testování API 🧪

Aplikaci můžete testovat dvěma doporučenými způsoby:

### 1. Interaktivní OpenAPI (Swagger) Dokumentace
Nejrychlejší způsob pro prozkoumání a otestování všech endpointů.

* **URL:** `http://localhost/api/documentation`

V tomto rozhraní můžete rozkliknout jednotlivé endpointy, kliknout na **"Try it out"**, vyplnit parametry a kliknutím na **"Execute"** odeslat požadavek a okamžitě vidět odpověď.



### 2. Postman (nebo podobný API klient)
Pro pokročilejší testování, ukládání požadavků nebo automatizaci.

* **Base URL:** `http://localhost/api`
* **Hlavičky (Headers):** Pro správnou funkci doporučuji u požadavků posílat hlavičku `Accept: application/json`.

**Příklad požadavku:** `GET http://localhost/api/products/Jablko/price-history`

---
### Důležitá poznámka pro uživatele WSL 2
Pokud přistupujete k aplikaci z Windows (např. z prohlížeče nebo Postmanu), která běží uvnitř WSL 2, je nutné místo `localhost` použít **IP adresu vašeho WSL prostředí**. Zjistíte ji v WSL terminálu spuštěním příkazu:
```bash
hostname -I
```
Příklad URL pak bude: `http://172.28.11.23/api/documentation`