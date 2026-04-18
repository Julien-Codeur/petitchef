# PetitChef - Transactions & Sécurité des données

## Vue d'ensemble

PetitChef utilise les **transactions de base de données** pour garantir l'intégrité des données, particulièrement pour les opérations critiques comme la création de commandes.

## Transactions DB

### 1. Création de commande (Plus important)

**Localisation:** [app/Http/Controllers/OrderController.php](app/Http/Controllers/OrderController.php) - Méthode `store()`

**Logique:**
```php
DB::transaction(function () {
    // 1. Valider tous les items
    foreach ($items as $item) {
        $dish = Dish::findOrFail($item['dish_id']);
        
        // 1a. Vérifier que c'est le même cuisinier
        if ($cookId === null) {
            $cookId = $dish->cook_id;
        } elseif ($cookId !== $dish->cook_id) {
            throw new Exception('Un seul cuisinier par commande');
        }
        
        // 1b. Vérifier le stock
        if ($dish->available_qty < $item['quantity']) {
            throw new Exception("Stock insuffisant: {$dish->name}");
        }
        
        // 1c. Calculer le total
        $totalPrice += $dish->price * $item['quantity'];
    }
    
    // 2. Créer la commande
    $order = Order::create([
        'client_id' => auth()->id(),
        'cook_id' => $cookId,
        'total_price' => $totalPrice,
        'pickup_time' => $data['pickup_time'],
        'status' => 'received',
        'note_client' => $data['note_client'] ?? null,
    ]);
    
    // 3. Créer les items et décrémenter le stock
    foreach ($items as $item) {
        $dish = Dish::findOrFail($item['dish_id']);
        
        // 3a. Enregistrer l'item avec le prix actuel
        OrderDish::create([
            'order_id' => $order->id,
            'dish_id' => $dish->id,
            'quantity' => $item['quantity'],
            'unit_price' => $dish->price, // Sauvegardé pour l'historique
        ]);
        
        // 3b. Décrémenter le stock
        $dish->decreaseStock($item['quantity']);
    }
    
    // Si une exception est lancée n'importe où,
    // LA TRANSACTION ENTIÈRE EST ANNULÉE
    // = Aucun ordre n'est créé, le stock n'est pas modifié
});
```

**Bénéfices:**
- ✅ Si l'ordre échoue à mi-chemin, tout revient à l'état initial
- ✅ Pas de stock incohérent
- ✅ Pas d'ordre orphelin sans items
- ✅ Atomicité garantie

### 2. Annulation de commande

**Localisation:** [app/Http/Controllers/OrderController.php](app/Http/Controllers/OrderController.php) - Méthode `destroy()`

**Logique:**
```php
// 1. Vérifier que la commande peut être annulée
if ($order->status !== 'received') {
    return back()->with('error', 'Seule une commande reçue peut être annulée.');
}

// 2. Restaurer le stock
foreach ($order->items as $item) {
    $item->dish->increment('available_qty', $item->quantity);
}

// 3. Mettre à jour le statut
$order->update(['status' => 'cancelled']);
```

**Améliorations suggérées:**
```php
// Utiliser une transaction pour cette opération aussi
DB::transaction(function () {
    foreach ($order->items as $item) {
        $item->dish->increment('available_qty', $item->quantity);
    }
    $order->update(['status' => 'cancelled']);
});
```

## Cas de concurrence

### Problème: Overbooking (surbooking)

**Scénario:**
- Plat "Coq au vin" a 2 portions
- Client A: Commande 1 portion
- Client B: Commande 2 portions (simultanément)
- Résultat: 3 portions vendues, stock négatif ❌

**Solution: Transaction + Lock**

```php
DB::transaction(function () {
    $dish = Dish::lockForUpdate()->find($dishId);
    
    // Maintenant, personne d'autre ne peut modifier ce plat
    
    if ($dish->available_qty < $quantity) {
        throw new Exception("Stock insuffisant");
    }
    
    // Safe!
    $dish->decreaseStock($quantity);
}, attempts: 3, timeout: 10);
```

**Ou utiliser Pessimistic Locking:**

```php
// Dans le seeder ou une migration:
DB::table('dishes')->lockForUpdate()->each(function ($dish) {
    // Traiter le plat
});
```

## Niveau d'isolation des transactions

**SQLite (développement):** `READ_UNCOMMITTED`
```
Plus rapide, mais moins sûr
```

**PostgreSQL (production):** `READ_COMMITTED` (par défaut)
```
Bon équilibre sécurité/performance
```

**MySQL (production):** `REPEATABLE_READ`
```
Très sûr pour les transactions longues
```

## Commandes de débogage

### 1. Afficher les queries SQL

```php
// Dans .env ou boot de AppServiceProvider
DB::listen(function ($query) {
    \Log::debug($query->sql, $query->bindings);
});

// Ou avec Debugbar
composer require barryvdh/laravel-debugbar --dev
```

### 2. Vérifier les transactions en cours

```bash
# MySQL
SHOW ENGINE INNODB STATUS\G

# PostgreSQL
SELECT * FROM pg_stat_activity WHERE state = 'active';

# SQLite
.tables  -- Voir les locks
```

### 3. Tester les transactions

```php
// Dans Tinker
>>> DB::beginTransaction();
>>> $dish = Dish::find(1);
>>> $dish->available_qty -= 5;
>>> $dish->save();
>>> // Voir le changement
>>> DB::rollBack(); // Annuler
>>> // Vérifier que c'est revenu à la normal
```

## Cas spéciaux

### 1. Modification du prix pendant une commande

**Problème:** Le prix change entre la validation et la création

```php
// ❌ Problématique
$price = $dish->price; // 12.50€
// ... 5 secondes plus tard ...
$dish->update(['price' => 10.00]); // Admin change le prix
// La commande est créée au prix 12.50€ ✓ (C'est correct!)
```

**Solution:** Utiliser le prix au moment de la commande (déjà implémenté)
```php
// ✓ Correct
$orderDish->unit_price = $dish->price; // Sauvegardé
```

### 2. Décrémenter du stock déjà décrémenté

**Problème:** Erreur lors du stockage, le stock est doublé

```php
// ❌ Problématique
$dish->decreaseStock($qty); // -5
// Erreur!
$dish->decreaseStock($qty); // -5 de nouveau ❌
```

**Solution:** La transaction revient à l'état initial
```php
// ✓ Correct
DB::transaction(function () {
    $dish->decreaseStock($qty);
    OrderDish::create(...); // Si erreur ici
    // Le stock est restauré automatiquement
});
```

### 3. Client avec faible connexion

**Problème:** Timeout pendant création de commande

```php
// Le timeout est géré par la transaction
// Avec attempts: 3, la requête est retentée 3 fois
DB::transaction(function () {
    // ...
}, attempts: 3, timeout: 10); // 10 secondes max
```

## Amélioration: Event Sourcing

Pour une application à grande échelle:

```php
// events/OrderCreated.php
event(new OrderCreated($order));

// listeners/CreateOrderDishes.php
public function handle(OrderCreated $event)
{
    DB::transaction(function () {
        foreach ($event->items as $item) {
            OrderDish::create($item);
        }
    });
}

// Avantage: Traçabilité complète de tous les changements
```

## Monitoring et Alertes

### 1. Logger les transactions longues

```php
// config/database.php
'slow' => env('DB_SLOW_QUERIES', 5000), // millisecondes

// Dans AppServiceProvider
DB::listen(function ($query) {
    if ($query->time > 5000) {
        \Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'time' => $query->time,
        ]);
    }
});
```

### 2. Alerte sur transactions deadlock

```php
try {
    DB::transaction(function () {
        // ...
    }, attempts: 3);
} catch (DeadlockException $e) {
    \Log::alert('Deadlock detected', [
        'exception' => $e->getMessage(),
    ]);
    // Notifier l'administrateur
}
```

## Checklist de sécurité

- [ ] Toutes les opérations critiques sont dans une transaction
- [ ] Les exceptions sont correctement catchées et loggées
- [ ] Le stock est validé avant la modification
- [ ] Les prix sont sauvegardés au moment de la commande
- [ ] Les tests couvrent les scénarios de concurrence
- [ ] Les timeouts sont configurés correctement
- [ ] Les logs incluent les queries lentes
- [ ] Les alertes deadlock sont en place

## Ressources

- [Laravel Transactions](https://laravel.com/docs/database#transactions)
- [Pessimistic Locking](https://laravel.com/docs/queries#pessimistic-locking)
- [Database Isolation Levels](https://en.wikipedia.org/wiki/Isolation_(database_systems))

