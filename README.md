# PetitChef - Plateforme de Commande de Repas Maison

Plateforme Laravel 12 permettant aux cuisiniers de publier des plats et aux clients de passer commande.

## Installation

### 1. Cloner et installer les dépendances
```bash
composer install
npm install
```

### 2. Configuration de l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Base de données
```bash
php artisan migrate
php artisan db:seed
```

### 4. Démarrer l'application
```bash
php artisan serve
npm run dev
```

L'application sera disponible à `http://localhost:8000`

## Comptes de test

### Admin
- Email: `admin@petitchef.local`
- Mot de passe: `password`

### Cuisinier 1
- Email: `pierre@petitchef.local`
- Mot de passe: `password`
- Plats: Coq au vin, Blanquette de veau

### Cuisinier 2
- Email: `marie@petitchef.local`
- Mot de passe: `password`
- Plats: Ratatouille, Bouillabaisse

### Client 1
- Email: `jean@petitchef.local`
- Mot de passe: `password`

### Client 2
- Email: `sophie@petitchef.local`
- Mot de passe: `password`

## Architecture

### Rôles et Permissions

**Client**
- Voir les plats disponibles du jour
- Ajouter au panier
- Passer commande
- Suivre le statut de ses commandes
- Modifier la note d'une commande (avant préparation)
- Annuler une commande (avant préparation)

**Cuisinier**
- Créer, modifier, supprimer ses plats
- Voir les commandes reçues
- Changer le statut: reçue → en préparation → prête → livrée
- Clôturer le service du jour (désactiver tous les plats)

**Admin** 🔐
- Accès total à toutes les données
- **Valider les profils de cuisiniers** (workflow complet)
- Voir les statistiques
- Gérer les commandes
- Gérer les plats
- Gérer les utilisateurs

👉 **[Voir le guide complet de l'interface Admin](ADMIN_INTERFACE.md)**  
👉 **[Guide d'utilisation pas à pas](ADMIN_USAGE_GUIDE.md)**

### Structure de la base de données

#### Users
- `id`, `name`, `email`, `password`, `role` (client|cook|admin)
- `phone`, `is_verified`, `timestamps`

#### Dishes
- `id`, `cook_id` (FK), `name`, `description`, `price`
- `available_qty`, `photo_path`, `served_date`, `is_active`, `timestamps`

#### Orders
- `id`, `client_id` (FK), `cook_id` (FK), `total_price`
- `pickup_time`, `status` (received|preparing|ready|delivered|cancelled)
- `note_client`, `timestamps`

#### OrderDishes (Pivot)
- `id`, `order_id` (FK), `dish_id` (FK)
- `quantity`, `unit_price`, `timestamps`

### Logique métier

**Stock et transactions**
- Le stock (`available_qty`) est décrémenté lors de la création d'une commande
- Utilisation de transactions DB pour garantir la cohérence
- Remise en stock si annulation de commande
- Validation du stock avant création de commande

**Panier (Session)**
- Stocké en session (`$cart` array)
- Format: `[dish_id => quantity, ...]`
- Persisté sur `/cart`

**Plats du jour**
- Filtrage par `served_date = today()` et `is_active = true`
- Seuls les clients voient les plats du jour
- Les cuisiniers voient tous leurs plats

**Commandes**
- Un client ne peut commander que chez UN seul cuisinier par commande
- Les prix sont sauvegardés dans la pivot `order_dishes` (`unit_price`)
- Transitions de statut validées

## Routes principales

### Authentification
- `/login` - Connexion
- `/register` - Inscription
- `/logout` - Déconnexion

### Plats
- `GET /dishes` - Liste des plats
- `POST /dishes` - Créer plat (cuisinier)
- `GET /dishes/{dish}/edit` - Éditer plat (cuisinier)
- `PATCH /dishes/{dish}` - Mettre à jour plat (cuisinier)
- `DELETE /dishes/{dish}` - Supprimer plat (cuisinier)
- `POST /dishes/{dish}/close-service` - Clôturer service (cuisinier)

### Panier
- `GET /cart` - Voir le panier
- `POST /cart/{dish}` - Ajouter au panier
- `PATCH /cart/{dish}` - Modifier quantité
- `DELETE /cart/{dish}` - Retirer du panier
- `POST /cart/clear` - Vider le panier

### Commandes
- `GET /orders` - Liste des commandes
- `GET /orders/create` - Formulaire commande
- `POST /orders` - Créer commande
- `GET /orders/{order}` - Voir détails
- `GET /orders/{order}/edit` - Éditer note
- `PATCH /orders/{order}` - Mettre à jour note
- `PATCH /orders/{order}/status` - Changer statut (cuisinier)
- `DELETE /orders/{order}` - Annuler commande

## Fichiers clés

### Models
- [app/Models/User.php](app/Models/User.php) - Relations: dishes, ordersAsClient, ordersAsCook
- [app/Models/Dish.php](app/Models/Dish.php) - Relations: cook, orderDishes; Scopes: today(), active()
- [app/Models/Order.php](app/Models/Order.php) - Relations: client, cook, items; Scopes: byStatus(), forCook()
- [app/Models/OrderDish.php](app/Models/OrderDish.php) - Pivot avec `unit_price` stocké

### Controllers
- [app/Http/Controllers/DishController.php](app/Http/Controllers/DishController.php)
- [app/Http/Controllers/OrderController.php](app/Http/Controllers/OrderController.php)
- [app/Http/Controllers/CartController.php](app/Http/Controllers/CartController.php)

### Policies
- [app/Policies/DishPolicy.php](app/Policies/DishPolicy.php)
- [app/Policies/OrderPolicy.php](app/Policies/OrderPolicy.php)

### Migrations
- `0001_01_01_000000_create_users_table.php` - Users avec role, phone
- `2026_04_18_184601_create_dishes_table.php` - Dishes
- `2026_04_18_184601_create_orders_table.php` - Orders
- `2026_04_18_184618_create_order_dishes_table.php` - Pivot

### Seeders
- [database/seeders/AdminSeeder.php](database/seeders/AdminSeeder.php)
- [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php)

## Sécurité et bonnes pratiques

✅ **Implémenté**
- Policies pour autorisation par rôle
- Form Requests avec validation
- Transactions DB pour opérations critiques
- Hachage des mots de passe
- Protection CSRF
- Eloquent avec relations
- Scopes pour requêtes courantes
- Stockage des prix dans pivot
- Gestion d'erreurs avec messages clairs

## À améliorer/Extension

- [ ] Système de notifications (mail/SMS)
- [ ] Système de ratings
- [ ] Tableau de bord admin avec statistiques
- [ ] Téléversement photo avec compression
- [ ] API RESTful
- [ ] Tests unitaires/feature
- [ ] Pagination des listes
- [ ] Recherche et filtrage avancés
- [ ] Système de paiement
- [ ] Gestion des allergènes

## Développement

Respecte les conventions Laravel:
- PSR-12 pour le code
- Migrations avec timestamps
- Models avec relations explicites
- Controllers RESTful
- Blade templates
- Sessions pour le panier

## Support

Pour toute question, consultez la [documentation Laravel](https://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# petitchef
