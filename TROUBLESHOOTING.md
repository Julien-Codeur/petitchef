# PetitChef - Guide de dépannage

Vous rencontrez un problème? Ce guide vous aidera à le résoudre.

---

## 🔴 Erreurs d'installation

### "Command 'composer' not found"
```bash
# Solution: Installer Composer
brew install composer  # macOS
apt install composer   # Ubuntu
# Ou télécharger depuis https://getcomposer.org
```

### "Command 'php' not found"
```bash
# Solution: Installer PHP 8.2+
brew install php@8.2      # macOS
apt install php8.2-cli    # Ubuntu
# Ou utiliser Docker
```

### "SQLSTATE[HY000]: General error: 3 Error writing to file"
```bash
# Problème: Permission sur storage/
# Solution:
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### "Class 'PDO' not found"
```bash
# Problème: PDO extension manquante
# Solution:
php -r "phpinfo();" | grep PDO  # Vérifier si présent
# Installer:
# macOS: brew install php@8.2-pdo
# Ubuntu: apt install php8.2-pdo
```

### "Target class [DishController] does not exist"
```bash
# Problème: Namespace incorrect
# Solution: Vérifier namespace dans le controller
// app/Http/Controllers/DishController.php
namespace App\Http\Controllers;  // ← Correct!

// Puis regénérer l'autoloader
composer dump-autoload
```

---

## 🟡 Erreurs de configuration

### "LARAVEL_ENV not set"
```bash
# Problème: .env manquant
# Solution:
cp .env.example .env
php artisan key:generate
```

### "Unknown database type 'json' requested"
```bash
# Problème: DB driver incompatible
# Solution: Dans .env, utiliser sqlite, mysql, ou pgsql
DB_CONNECTION=sqlite
```

### "SQLSTATE[42000]: Syntax error or access violation"
```bash
# Problème: Migrations mal formées
# Solution:
php artisan migrate:rollback
php artisan migrate
# Ou recréer:
php artisan migrate:fresh --seed
```

### "No database selected"
```bash
# Problème: DB_DATABASE non configuré
# Solution: Dans .env
DB_DATABASE=petitchef
# Puis créer la base:
mysql -u root -p -e "CREATE DATABASE petitchef;"
```

---

## 🔵 Erreurs d'application

### "CSRF token mismatch"
```blade
<!-- Problème: @csrf manquant dans form -->
<!-- Solution: -->
<form method="POST" action="{{ route('dishes.store') }}">
    @csrf  <!-- ← Ajouter ça! -->
    ...
</form>
```

### "Undefined variable" dans template
```blade
<!-- Problème: Variable pas passée du controller -->
<!-- Solution: Dans le controller -->
public function index() {
    $dishes = Dish::all();
    return view('dishes.index', compact('dishes'));  // ← Passer variable
}
```

### "Route not defined"
```blade
<!-- Problème: Route name incorrect -->
<!-- Solution: -->
{{ route('dishes.index') }}  <!-- ← Correct -->
{{ route('dish.index') }}    <!-- ← Incorrect -->

<!-- Voir routes disponibles: -->
php artisan route:list
```

### "Model not found"
```php
// Problème: ID n'existe pas dans DB
$dish = Dish::findOrFail(999); // ← Lance 404

// Solution: Vérifier l'ID
$dishes = Dish::all();  // Voir ce qui existe
Dish::find(1);          // Utiliser find() sans exception
```

### "Undefined array key 'items'"
```php
// Problème: Key manquante du tableau
$items = $request->input('items');  // NULL si manquant

// Solution: Valider d'abord
$items = $request->validate([
    'items' => 'required|array',
]);
```

---

## 🟠 Erreurs de permissions

### "Permission denied" sur files
```bash
# Problème: Permissions insuffisantes
# Solution:
chmod -R 755 app/
chmod -R 755 routes/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### "Cannot create temporary file"
```bash
# Problème: /tmp plein ou permissions insuffisantes
# Solution:
# Nettoyer tmp
rm -rf /tmp/*
# Ou configurer temp dir dans php.ini
upload_tmp_dir = /home/user/tmp
```

### "File is not writable"
```bash
# Problème: storage/ ou bootstrap/cache/ non writable
ls -la storage/        # Vérifier permissions
chmod -R 777 storage/  # Rendre writable
```

---

## 🔴 Erreurs de base de données

### "Connection refused"
```bash
# Problème: DB server pas accessible
# Solution: Vérifier que DB tourne
# MySQL:
mysql -u root -p
# PostgreSQL:
psql -U postgres

# Ou vérifier l'host dans .env
DB_HOST=127.0.0.1  # pas localhost!
```

### "Access denied for user"
```bash
# Problème: Credentials incorrects
# Solution: Vérifier .env
DB_USERNAME=root
DB_PASSWORD=password

# Ou créer l'utilisateur:
mysql -u root -e "GRANT ALL ON petitchef.* TO 'user'@'localhost';"
```

### "No more connections allowed"
```bash
# Problème: Pool de connexions saturé
# Solution: Augmenter connections dans DB ou redémarrer
# MySQL:
SET GLOBAL max_connections = 1000;

# Laravel: Vérifier N+1 queries
# Utiliser eager loading:
$orders = Order::with('items', 'client')->get();
```

### "Deadlock found when trying to get lock"
```php
// Problème: Race condition sur le stock
// Solution: Utiliser lock
$dish = Dish::lockForUpdate()->find($id);

// Ou retry:
DB::transaction(function () {
    // ...
}, attempts: 3);
```

### "Table 'petitchef.dishes' doesn't exist"
```bash
# Problème: Migrations pas exécutées
# Solution:
php artisan migrate
php artisan migrate:status  # Voir status
```

---

## 💜 Erreurs d'authentification

### "Unauthenticated"
```blade
<!-- Problème: Accès page protégée sans login -->
<!-- Solution: -->
@auth
    <!-- Voir ce contenu si connecté -->
@else
    <!-- Voir ça si pas connecté -->
    <a href="{{ route('login') }}">Login</a>
@endauth
```

### "This action is unauthorized"
```php
// Problème: Policy refuse l'action
// Solution: Vérifier la policy
$this->authorize('delete', $dish);  // ← Lance 403 si non autorisé

// Ou vérifier:
if (!auth()->user()->can('delete', $dish)) {
    abort(403, 'Unauthorized');
}
```

### "No query results for model"
```php
// Problème: User n'existe pas dans session
auth()->check();  // Vérifier si authentifié
auth()->user();   // Null si pas authentifié

// Solution: Ajouter middleware
Route::middleware(['auth'])->group(function () {
    // Routes protégées
});
```

### "Token mismatch" (API)
```php
// Problème: Sanctum token incorrect
// Solution: Vérifier Authorization header
// GET /api/... HTTP/1.1
// Authorization: Bearer [token]
```

---

## 🟣 Erreurs de session

### "Undefined index 'cart'"
```php
// Problème: Session cart manquante
$cart = session()->get('cart');  // NULL si n'existe pas

// Solution:
$cart = session()->get('cart', []);  // Default []
```

### "Session data lost after redirect"
```php
// Problème: Session pas sauvegardée
// Solution: Vérifier SESSION_DRIVER dans .env
SESSION_DRIVER=file  // local dev
// ou
SESSION_DRIVER=database  // production
// Créer table si database:
php artisan session:table
php artisan migrate
```

### "Cannot modify header information"
```php
// Problème: Session utilisée après output
echo "Something";
session()->put('cart', $cart);  // ← Error!

// Solution: Garder sessions avant output
session()->put('cart', $cart);
echo "Something";
```

---

## 🟢 Erreurs de vues

### "view [xxx.blade.php] not found"
```bash
# Problème: Template manquant
# Solution: Vérifier le chemin
# Template: resources/views/dishes/index.blade.php
# Call: return view('dishes.index');  ✓

# Template: resources/views/admin/orders.blade.php
# Call: return view('admin.orders');  ✓
```

### Blade syntax error
```blade
<!-- ❌ Incorrect -->
{{ $dish->name }}
{!! $dish->name !!}
{{ Auth::user()->name }}

<!-- ✓ Correct -->
{{ $dish->name }}           <!-- HTML escaped -->
{!! $dish->photo_path !!}   <!-- Non-escaped (careful!) -->
{{ auth()->user()->name }}  <!-- Helper function -->
```

### "@if, @foreach, @forelse rendering issues"
```blade
<!-- ❌ Incorrect -->
<div>
  @if ($dishes->count() > 0)
    @foreach ($dishes as $dish)
      {{ $dish->name }}
<!-- Pas de @endif ou @endforeach! -->

<!-- ✓ Correct -->
<div>
  @if ($dishes->count() > 0)
    @foreach ($dishes as $dish)
      {{ $dish->name }}
    @endforeach
  @else
    No dishes
  @endif
</div>
```

---

## 🔧 Erreurs de développement

### "Call to undefined method"
```php
// Problème: Méthode n'existe pas
$dish->nonExistentMethod();  // ← Error

// Solution: Vérifier le model
// app/Models/Dish.php
public function cook() {  // ← Ajouter la méthode
    return $this->belongsTo(User::class);
}
```

### "Property [xxx] does not exist on Eloquent builder"
```php
// Problème: Accès propriété sur query, pas model
$dish = Dish::where('id', 1);  // ← Query builder
echo $dish->name;              // Error!

// Solution: Get le model
$dish = Dish::where('id', 1)->first();  // ← Model
echo $dish->name;                        // OK
```

### "Serialization of 'Closure' is not allowed"
```php
// Problème: Stocker closure en session
session()->put('callback', function () {});  // Error!

// Solution: Utiliser JSON
session()->put('data', ['key' => 'value']);
```

---

## 📊 Performance issues

### "Page loading very slowly"
```bash
# Problème: N+1 queries
# Solution: Utiliser eager loading
# ❌ Lent:
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->client->name;  // Query par order!
}

# ✓ Rapide:
$orders = Order::with('client')->get();
foreach ($orders as $order) {
    echo $order->client->name;
}
```

### "High memory usage"
```php
// Problème: Charger trop de données
$dishes = Dish::all();  // Des millions!

// Solution: Paginer
$dishes = Dish::paginate(50);

// Ou utiliser chunk:
Dish::chunk(100, function ($dishes) {
    // Traiter par batch de 100
});
```

### "Long query execution time"
```bash
# Problème: Query sans index
# Solution: Ajouter index dans migration
Schema::table('dishes', function (Blueprint $table) {
    $table->index(['cook_id', 'served_date']);
});

# Puis run:
php artisan migrate
```

---

## ✅ Vérifications rapides

```bash
# 1. Vérifier structure DB
php artisan migrate:status

# 2. Voir toutes les routes
php artisan route:list | grep dishes

# 3. Vérifier les permissions
ls -la storage/
ls -la bootstrap/cache/

# 4. Voir les logs
tail -20 storage/logs/laravel.log

# 5. Tester connection DB
php artisan tinker
>>> DB::connection()->getPdo();

# 6. Vérifier config
php artisan config:show database

# 7. Clear cache
php artisan cache:clear
php artisan config:clear
```

---

## 🆘 Cas désespérés

### "Tout est cassé"
```bash
# Nuclear option:
php artisan migrate:fresh --seed
rm -rf storage/logs/*
rm -rf bootstrap/cache/*
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
php artisan cache:clear
php artisan config:clear
php artisan serve
```

### "Besoin de logs plus détaillés"
```bash
# Dans .env:
LOG_LEVEL=debug

# Puis:
tail -f storage/logs/laravel.log
```

### "Toujours bloqué?"
```bash
# 1. Lire le message d'erreur complètement
# 2. Google l'erreur exacte
# 3. Vérifier les fichiers docs:
#    - ARCHITECTURE.md
#    - Commentaires du code
# 4. Consulter Laravel docs:
#    https://laravel.com/docs/
```

---

## 🎯 Prevention

### Avant chaque commit
```bash
php artisan test
php artisan migrate:status
php artisan route:list
```

### Avant chaque déploiement
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
chmod -R 775 storage/
```

### Monitoring en production
```bash
tail -f storage/logs/laravel.log | grep -i error
ps aux | grep php
du -sh storage/logs/
df -h  # Vérifier disque
```

---

**Besoin d'aide? Consultez ARCHITECTURE.md ou posez sur GitHub! 🆘**

