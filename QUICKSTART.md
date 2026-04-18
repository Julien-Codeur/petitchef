# Quick Start Guide - PetitChef

## ⚡ Démarrage rapide (5 minutes)

### 1. Installation des dépendances
```bash
cd petitchef
composer install
npm install
```

### 2. Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Base de données
```bash
php artisan migrate
php artisan db:seed
```

### 4. Lancer l'application
```bash
# Terminal 1 - PHP server
php artisan serve

# Terminal 2 - Build assets (optionnel, pour Tailwind)
npm run dev
```

Application disponible: **http://localhost:8000**

## 🧪 Tester l'application

### Étape 1: Connexion Admin
- Email: `admin@petitchef.local`
- Mot de passe: `password`
- ✓ Voir les statistiques globales

### Étape 2: Connexion Cuisinier
- Email: `pierre@petitchef.local`
- Mot de passe: `password`
- ✓ Voir vos plats
- ✓ Modifier un plat
- ✓ Voir les commandes reçues

### Étape 3: Connexion Client
- Email: `jean@petitchef.local`
- Mot de passe: `password`
- ✓ Voir les plats du jour disponibles
- ✓ Ajouter un plat au panier
- ✓ Voir le panier (`/cart`)
- ✓ Passer une commande
- ✓ Suivre la commande

### Étape 4: Flux complet

**Client:**
1. `GET /dishes` - Voit les 4 plats du jour
2. Ajoute "Coq au vin" (12.50€) × 2 au panier
3. Ajoute "Ratatouille" (10€) × 1 au panier
4. `GET /cart` - Voit le panier: 35€
5. `POST /orders` - Passe commande
6. `GET /orders/{id}` - Voit sa commande (status: received)

**Cuisinier (pierre@petitchef.local):**
1. `GET /orders` - Voit la nouvelle commande
2. Clique "Commencer préparation" → status change à `preparing`
3. Clique "Prête pour retrait" → status change à `ready`
4. Clique "Livrée" → status change à `delivered`

**Client:**
- Suit le changement de statut en temps réel sur sa commande

## 📁 Fichiers clés à connaître

### Logique métier
- [app/Models/Order.php](app/Models/Order.php) - Transitions de statuts
- [app/Models/Dish.php](app/Models/Dish.php) - Gestion du stock
- [app/Http/Controllers/OrderController.php](app/Http/Controllers/OrderController.php) - Transactions DB

### Vues principales
- [resources/views/dishes/index.blade.php](resources/views/dishes/index.blade.php)
- [resources/views/orders/index.blade.php](resources/views/orders/index.blade.php)
- [resources/views/cart/index.blade.php](resources/views/cart/index.blade.php)

### Routes
- [routes/web.php](routes/web.php) - Toutes les routes configurées

## 🛠️ Commandes utiles

```bash
# Réinitialiser la DB (attention ⚠️)
php artisan migrate:fresh --seed

# Voir les routes
php artisan route:list

# Lancer les tests (à implémenter)
php artisan test

# Tinker - REPL interactif
php artisan tinker

# Dans tinker:
>>> $dish = Dish::first();
>>> $dish->isAvailableToday();
>>> $order = Order::first();
>>> $order->getStatusLabel();
```

## 🔐 Sécurité - Points importants

1. **Passwords:** Ne jamais utiliser `password` en production!
2. **Environment:** Configurer `.env` correctement (DB, mail, etc.)
3. **HTTPS:** Toujours utiliser HTTPS en production
4. **CSRF:** Tous les formulaires protégés avec `@csrf`
5. **SQL Injection:** Utiliser Eloquent (pas de raw queries)

## 🚀 Prochaines étapes

### Court terme (1-2 heures)
- [ ] Ajouter tests unitaires
- [ ] Créer API RESTful dans `routes/api.php`
- [ ] Implémenter upload photos avec compression
- [ ] Ajouter pagination aux listes

### Moyen terme (1-2 jours)
- [ ] Système de notifications (mail/SMS)
- [ ] Tableau de bord admin avec stats
- [ ] Système de ratings/reviews
- [ ] Système de paiement (Stripe/PayPal)

### Long terme (1-2 semaines)
- [ ] Application mobile (React Native / Flutter)
- [ ] System de recherche avancée
- [ ] Recommandations basées sur historique
- [ ] Gestion des allergènes
- [ ] Multi-language support

## ❓ Dépannage

### Erreur: "Driver 'sqlite' not supported"
```bash
# .env contient probablement une mauvaise DB_CONNECTION
# Changer en: DB_CONNECTION=sqlite
# Et créer: touch database/database.sqlite
```

### Erreur: "Undefined variable"
```bash
# Vérifier que toutes les variables sont passées à la vue
// Dans le controller:
return view('orders.index', compact('orders')); // ✓
return view('orders.index', ['orders' => $orders]); // ✓
```

### Erreur 500 - CSRF token
```bash
# Vérifier que @csrf est dans tous les formulaires
<form method="POST" action="...">
    @csrf <!-- Ne pas oublier! -->
```

### Erreur: "Policy not registered"
```bash
# Vérifier AppServiceProvider.php
// app/Providers/AppServiceProvider.php
Gate::policy(Dish::class, DishPolicy::class); ✓
```

## 📚 Ressources

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Blade Templating](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Policies & Authorization](https://laravel.com/docs/authorization)

## 💬 Support

Pour toute question, consultez les fichiers de documentation:
- [README.md](README.md) - Complète
- [ARCHITECTURE.md](ARCHITECTURE.md) - Architecture détaillée
- Code commenté et docstrings complets

