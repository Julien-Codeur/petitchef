# 🚀 PetitChef - Guide de Démarrage Développement

Bienvenue! Voici comment démarrer votre projet Laravel PetitChef.

## 📋 Prérequis

- PHP 8.3+
- Composer
- Node.js + npm
- SQLite (déjà configuré par défaut)

---

## 🔧 Installation Rapide

### Étape 1: Setup Composer & NPM

```bash
composer install
npm install --ignore-scripts
```

### Étape 2: Configuration ENV

```bash
cp .env.example .env
php artisan key:generate
```

### Étape 3: Migrations & Seeders

```bash
# Créer les tables
php artisan migrate

# Remplir avec des données de test
php artisan db:seed
```

**Résultat attendu:**
```
✅ Database seeded successfully!
👤 Admin: admin@petitchef.local
🍳 Cook: cook@petitchef.local
👥 Clients: alice@example.com, bob@example.com
🍲 Dishes: Courgettes farcies, Poulet rôti fermier, Salade composée
📦 Orders created: 4
🚚 Delivery routes created: 1
```

### Étape 4: Lancer le Serveur

**Option A - Concurrently (Node + Laravel):**
```bash
npm run dev
```

**Option B - Séparé (terminal 1 & 2):**
```bash
# Terminal 1: Serveur Laravel
php artisan serve

# Terminal 2: Vite (assets)
npm run dev
```

Serveur dispo à: **http://localhost:8000**

---

## 🗄️ Base de Données

### Structure des Tables

```
✅ users              (admin, cook, client, delivery_person)
✅ dishes             (menu du jour avec stock)
✅ delivery_addresses (lieux de livraison)
✅ orders             (commandes par client)
✅ order_items        (détail plats par commande)
✅ deliveries         (tournées du jour)
✅ delivery_stops     (arrêts de la tournée)
```

### Voir les Données

```bash
# Ouvrir Tinker (REPL Laravel)
php artisan tinker

# Exemples:
>>> User::all()
>>> Dish::ofTheDay()->get()
>>> Order::where('status', 'prête')->get()
>>> Delivery::today()->first()
```

---

## 👥 Utilisateurs de Test

### Identifiants

| Rôle | Email | Password | Usage |
|------|-------|----------|-------|
| Admin | admin@petitchef.local | password | Modération, Stats |
| Cook | cook@petitchef.local | password | Menu, Commandes, Livraisons |
| Client 1 | alice@example.com | password | Commandes |
| Client 2 | bob@example.com | password | Commandes |

*Note: Password par défaut = "password"*

---

## 📦 Architecture BD

### Flux Données Principal

```
Client (Alice) 
  ↓
Order (Commande) → OrderItems → Dishes
  ↓
DeliveryAddress
  ↓
Delivery (Tournée du jour)
  ↓
DeliveryStops (Arrêts)
```

### Clés Techniquement Importants

**Dish.available_qty** (Gestion du stock)
- ✅ Décrémenté transactionnellement lors de commande
- ✅ Transactions DB pour éviter la survente
- ✅ Seuil critique défini manuellement

**Order.status** (Workflow)
- reçue → en_préparation → prête → livrée (ou annulée)
- Statut markable par cuisinier au fur et à mesure

**Delivery** (Optimisation routes)
- `route_order` = JSON array d'IDs d'adresses (ordre optimisé)
- `estimated_total_duration` = durée totale tournée
- Calculé par algo nearest neighbor

**OrderItem.unit_price** (Snapshot prix)
- Stocke le prix au moment de la commande
- Protège contre les changements de prix après commande

---

## 🔑 Modèles Eloquent Clés

### Scopes Utiles

```php
// Dishes
Dish::ofTheDay()->get()           // Menu d'aujourd'hui
Dish::forCook($cookId)->get()     // Pour un cuisinier
Dish::critical()->get()           // Stock critique
Dish::exhausted()->get()          // Stock épuisé

// Orders
Order::forCook($cookId)->get()
Order::ofTheDay()->get()
Order::byStatus('prête')->get()

// Delivery
Delivery::forCook($cookId)->get()
Delivery::ofTheDay()->first()     // Tournée du jour
```

### Helpers Utiles

```php
// Dish
$dish->decrementStock(3)      // Décrémenter stock transactionnellement
$dish->isCritical()           // Vrai si stock <= seuil critique
$dish->isExhausted()          // Vrai si 0 stock

// Order
$order->markInPreparation()   // Passer en préparation
$order->markReady()           // Marquer prête
$order->markDelivered()       // Marquer livrée
$order->isReady()             // Vrai si status = prête

// Delivery
$delivery->markReadyToLeave() // Prêt à partir
$delivery->getTotalItems()    // Nombre total portions
```

---

## 🧪 Tests

### Lancer Tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter OrderTest
php artisan test tests/Feature/Orders/CreateOrderTest.php
```

### Exemple Test Simple

```php
// tests/Feature/DishStockTest.php
public function test_dish_stock_decrements_on_order()
{
    $dish = Dish::factory()->create(['available_qty' => 10]);
    $dish->decrementStock(3);
    
    $this->assertEquals(7, $dish->fresh()->available_qty);
}
```

---

## 🛑 Problèmes Courants

### Erreur: "SQLSTATE[HY000]: General error"
→ Supprimer `database.sqlite` et réexécuter `php artisan migrate --seed`

### Erreur: "Class 'App\Models\Dish' not found"
→ Vérifier que fichier est en `app/Models/Dish.php` (avec namespace correct)

### Erreur: "No query results for model"
→ Vérifier les IDs en DB avec Tinker: `Dish::first()`

---

## 📚 Ressources

- **Migrations:** `database/migrations/`
- **Modèles:** `app/Models/`
- **Factories:** `database/factories/`
- **Seeder:** `database/seeders/DatabaseSeeder.php`
- **Architecture Doc:** `_bmad-output/brainstorming/ARCHITECTURE-OPTIMISÉE-PETITCHEF.md`

---

## 🎯 Prochaines Étapes

1. **✅ BD Créée** — Modèles, migrations, relations OK
2. **⏳ Controllers** — Créer les endpoints API/Web
3. **⏳ Views/Frontend** — Dashboards pour chaque rôle
4. **⏳ Routes** — Routes web et API
5. **⏳ Tests** — Couverture de test complète

---

**Questions? Consultez l'architecture complète:**  
→ `_bmad-output/brainstorming/ARCHITECTURE-OPTIMISÉE-PETITCHEF.md`
