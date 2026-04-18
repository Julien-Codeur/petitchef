# PetitChef - Architecture & Bonnes Pratiques

## Vue d'ensemble

PetitChef est une plateforme de commande de repas maison construite avec Laravel 12. Elle suit les bonnes pratiques Laravel et les principes SOLID.

## Structure des dossiers clés

```
app/
├── Models/
│   ├── User.php           # Model d'authentification avec rôles
│   ├── Dish.php           # Plats avec relations et scopes
│   ├── Order.php          # Commandes avec logique de statuts
│   └── OrderDish.php      # Pivot avec prix sauvegardé
├── Http/
│   ├── Controllers/
│   │   ├── DishController.php
│   │   ├── OrderController.php
│   │   └── CartController.php
│   ├── Requests/
│   │   ├── StoreDishRequest.php
│   │   ├── UpdateDishRequest.php
│   │   └── StoreOrderRequest.php
│   └── Middleware/
├── Policies/
│   ├── DishPolicy.php
│   └── OrderPolicy.php
└── Providers/
    └── AppServiceProvider.php

routes/
├── web.php
├── api.php
└── auth.php

resources/views/
├── dishes/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── orders/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── cart/
│   └── index.blade.php
└── layouts/

database/
├── migrations/
├── seeders/
│   ├── AdminSeeder.php
│   └── DatabaseSeeder.php
└── factories/
```

## Flux d'application

### 1. Authentification
- Laravel Breeze fournit l'authentification de base
- Les utilisateurs choisissent leur rôle lors de l'inscription
- Trois rôles: `client`, `cook`, `admin`

### 2. Affichage des plats

**Client:**
```
GET /dishes
└─ Voit les plats d'AUJOURD'HUI (served_date = today)
└─ Voit UNIQUEMENT les plats ACTIFS (is_active = true)
```

**Cuisinier:**
```
GET /dishes
└─ Voit TOUS ses plats (par cook_id)
└─ Peut les éditer, supprimer, activer/désactiver
```

### 3. Gestion du panier

**Stockage:** Session PHP
```php
session()->get('cart', [])
// Format: ['dish_id' => quantity, ...]
```

**Flux:**
1. Client voit les plats du jour
2. Ajoute au panier via `POST /cart/{dish}`
3. Voit le panier à `/cart`
4. Peut modifier quantités ou retirer des articles
5. Procède à la commande

### 4. Création de commande

**Contrôles:**
- ✓ Stock suffisant pour chaque plat
- ✓ Tous les plats du même cuisinier
- ✓ Tous les plats actifs et du jour
- ✓ Transaction DB pour atomicité

**Processus:**
```
POST /orders
├─ Valider les items du panier
├─ Vérifier le stock
├─ Créer l'Order
├─ Créer les OrderDish items (avec unit_price)
├─ Décrémenter le stock
├─ Effacer le panier
└─ Rediriger vers la commande créée
```

### 5. Suivi de commande

**Statuts possibles:**
- `received` (reçue) - Initial, peut être modifié par cuisinier ou annulé par client
- `preparing` (en préparation) - Cuisinier a commencé
- `ready` (prête) - Prête pour retrait
- `delivered` (livrée) - Remise au client
- `cancelled` (annulée) - Commande annulée, stock restauré

**Transitions valides:**
```
received ──→ preparing ──→ ready ──→ delivered
    ↓             ↓          ↓
  cancelled    cancelled   (pas de transition)
```

## Logique métier clé

### 1. Transactions DB

Lors de la création de commande:
```php
DB::transaction(function () {
    // Vérifier stock
    // Créer Order
    // Créer OrderDish items
    // Décrémenter stock
    // Si erreur: tout est annulé
});
```

### 2. Gestion du stock

```php
// Avant création: vérifier
if ($dish->available_qty < $quantity) {
    throw new Exception("Stock insuffisant");
}

// Après création: décrémenter
$dish->decreaseStock($quantity);

// Si annulation: restaurer
$dish->increment('available_qty', $quantity);
```

### 3. Pivot avec données

La table `order_dishes` stocke:
- `order_id`, `dish_id`, `quantity`
- **`unit_price`** - Prix à la date de la commande (important!)
- `timestamps`

Cela permet de voir l'historique des prix même si le plat change.

### 4. Scopes Eloquent

```php
// Plats du jour actifs
Dish::today()->active()->get();

// Commandes d'un statut
Order::byStatus('received')->get();

// Commandes d'un client
Order::forClient($clientId)->get();
```

## Sécurité

### 1. Policies

Les policies vérifient qui peut faire quoi:

```php
// DishPolicy
- viewAny(): Client ou Cook
- view(): Client si actif, Cook si owner
- create(): Cook vérifié
- update/delete(): Cook owner uniquement

// OrderPolicy
- viewAny(): Cook ou Admin
- view(): Client si owner, Cook si owner, Admin toujours
- create(): Client uniquement
- update(): Cook si owner, Client si owner et status=received
- changeStatus(): Cook si owner
- closeService(): Cook uniquement
```

### 2. Form Requests

```php
// StoreDishRequest
- Autorisé: Cook uniquement
- Valide: name, description, price, available_qty, served_date, photo

// StoreOrderRequest
- Autorisé: Client uniquement
- Valide: items array, pickup_time, note_client
```

### 3. Protection CSRF

Tous les formulaires incluent `@csrf`

### 4. Hashmapping des mots de passe

Utilisé automatiquement par Laravel Breeze

## Scalabilité

### Améliorations possibles

1. **Cache**
   ```php
   // Cacher les plats du jour
   Cache::remember('dishes.today', 3600, fn() => 
       Dish::today()->active()->get()
   );
   ```

2. **Pagination**
   ```php
   $orders = Order::paginate(15);
   ```

3. **Événements**
   ```php
   OrderCreated::dispatch($order);
   OrderStatusChanged::dispatch($order, $newStatus);
   ```

4. **Jobs asynchrones**
   ```php
   SendOrderConfirmation::dispatch($order);
   ```

5. **API RESTful**
   ```php
   // routes/api.php
   Route::apiResource('dishes', DishApiController::class);
   ```

## Performance

### Requêtes optimisées

**Éviter N+1:**
```php
// ✗ Mauvais
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->client->name; // N+1 queries
}

// ✓ Bon
$orders = Order::with('client', 'cook', 'items.dish')->get();
foreach ($orders as $order) {
    echo $order->client->name; // 1 query
}
```

**Indexes DB:**
```php
// Dans migrations
$table->index(['cook_id', 'served_date', 'is_active']);
$table->index(['client_id', 'cook_id', 'status', 'created_at']);
$table->unique(['order_id', 'dish_id']);
```

## Tests

### À implémenter

```php
// tests/Feature/OrderTest.php
public function test_client_can_create_order()
public function test_stock_decremented_on_order()
public function test_order_status_transitions()
public function test_user_cannot_order_multiple_cooks()

// tests/Unit/DishTest.php
public function test_dish_decreases_stock()
public function test_scope_today()
```

## Conventions

### Nommage

- **Models:** Singulier, PascalCase (User, Dish, Order)
- **Tables:** Pluriel, snake_case (users, dishes, orders, order_dishes)
- **Colonnes:** snake_case (cook_id, available_qty)
- **Routes:** RESTful
- **Controllers:** Singulier + Controller (DishController, OrderController)
- **Migrations:** Verbe + timestamp (create_dishes_table)

### Code Style

- PSR-12
- 4 espaces d'indentation
- Ligne max 120 caractères
- DocBlocks pour les méthodes publiques

### Git

```
main (production)
└─ develop
   ├─ feature/auth-system
   ├─ feature/cart-system
   ├─ bugfix/stock-issue
   └─ etc.
```

Commit messages:
```
feat: add cart functionality
fix: prevent overselling with transactions
docs: update README with setup instructions
test: add order creation tests
refactor: simplify policy logic
```

## Ressources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Blade Templates](https://laravel.com/docs/blade)

