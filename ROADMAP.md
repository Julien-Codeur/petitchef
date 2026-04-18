# PetitChef - Feuille de route et améliorations futures

## 🎯 Vision long terme

PetitChef deviendra une **plateforme complète** de livraison de repas maison avec:
- Paiements intégrés
- Notifications temps réel
- Ratings et reviews
- Application mobile
- Tableau de bord admin complet

---

## 📊 Phase 2: MVP+ (2-3 semaines)

### 2.1 Tests automatisés (3 jours)
```php
// tests/Feature/OrderTest.php
- Test création commande (happy path)
- Test commande avec stock insuffisant
- Test commande deux cuisiniers
- Test annulation et restauration stock
- Test concurrence (race condition)

// tests/Unit/DishTest.php
- Test decreaseStock()
- Test isAvailableToday()
- Test scope today()
```

**Commande:**
```bash
php artisan test
php artisan test --coverage  # Viser 80%+
```

### 2.2 API RESTful (4 jours)
```
GET    /api/dishes              # Liste des plats du jour
GET    /api/dishes/{id}         # Détail d'un plat
POST   /api/orders              # Créer commande
GET    /api/orders/{id}         # Détail commande
PATCH  /api/orders/{id}/status  # Changer statut
DELETE /api/orders/{id}         # Annuler commande
```

**Avec Laravel Sanctum:**
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 2.3 Upload et traitement photos (2 jours)
```php
// middleware: validate photo
// compress image: Spatie/Image
// store: storage/dishes/
// thumbnail: storage/dishes/thumb/
```

**Dependencies:**
```bash
composer require spatie/image
npm install sharp
```

### 2.4 Notifications (2 jours)
```php
// events: OrderCreated, OrderStatusChanged
// listeners: SendOrderConfirmation, SendStatusUpdate
// mail: mailable classes
// sms: Twilio (optionnel)
```

**Configuration:**
```bash
# .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
```

**Total Phase 2:** ~11 jours

---

## 📈 Phase 3: Engagement (3-4 semaines)

### 3.1 Search et filtres avancés (3 jours)
```blade
<form method="GET" action="{{ route('dishes.index') }}">
    <input name="search" placeholder="Rechercher...">
    <select name="price_min">
    <select name="price_max">
    <select name="cook_id">
    <button>Filtrer</button>
</form>
```

**Implémentation:**
```php
// DishController@index
if ($request->search) {
    $dishes = $dishes->where('name', 'like', "%{$request->search}%");
}
if ($request->price_min) {
    $dishes = $dishes->where('price', '>=', $request->price_min);
}
```

### 3.2 Ratings et reviews (4 jours)
```
Migrations:
- reviews table (user_id, dish_id, rating, comment)
- Pivot: dishes-ratings

Views:
- Form review (après livraison)
- Display ratings sur plats
- Top rated plats
```

**Structure:**
```php
$dish->reviews()->avg('rating')  // Rating moyen
$dish->reviews()->latest()->take(5)  // Top reviews
```

### 3.3 Pagination (1 jour)
```php
// DishController
$dishes = Dish::today()->active()->paginate(12);

// View
{{ $dishes->links() }}
```

### 3.4 Tableau de bord Admin (5 jours)
```
Stats:
- Total commandes (graph)
- Revenue (graph)
- Clients actifs (nombre)
- Cuisiniers actifs (nombre)

Listes:
- Dernières commandes
- Derniers clients
- Cuisiniers non vérifiés

Actions:
- Vérifier cuisinier
- Voir détails client
- Voir historique commandes
```

**Packages:**
```bash
composer require chartjs
npm install chart.js
```

**Total Phase 3:** ~13 jours

---

## 🚀 Phase 4: Scaling (4-5 semaines)

### 4.1 Paiements (5 jours)
```bash
composer require stripe/stripe-php
npm install @stripe/stripe-js
```

**Flux:**
```
Client → Panier → Checkout → Stripe → Confirmation
         ↑                      ↓
         └──── Webhook ←────────┘
```

**Implémentation:**
```php
// POST /checkout
$intent = Stripe::createPaymentIntent($order->total_price);
// Page form Stripe
// POST /webhook (handle webhook)
// Update order status
```

### 4.2 WebSockets temps réel (4 jours)
```bash
composer require laravel/reverb
npm install @laravel/reverb
```

**Événements:**
```php
// OrderStatusChanged
broadcast(new OrderStatusChanged($order));

// Vue
echo @json($order)
Livewire.on('order-status-changed', (order) => {
    updateUI(order);
});
```

### 4.3 Cache et performances (3 jours)
```php
// Cache plats du jour
Cache::remember('dishes.today', 3600, fn() => 
    Dish::today()->active()->get()
);

// Cache pour counts
Cache::remember('order.count', 300, fn() => 
    Order::count()
);

// Redis
REDIS_HOST=127.0.0.1
```

### 4.4 Application mobile (15 jours)
```bash
# React Native avec Expo
npx create-expo-app petitchef-mobile

# Écrans:
- Login
- Home (plats du jour)
- Cart
- Orders
- Profile
```

**Intégration:**
```javascript
// API_URL = "https://api.petitchef.local"
const API = axios.create({
    baseURL: process.env.REACT_APP_API_URL
});
```

**Total Phase 4:** ~27 jours

---

## 🔐 Phase 5: Sécurité et compliance (2 semaines)

### 5.1 RGPD
- [ ] Politique de confidentialité
- [ ] GDPR consent
- [ ] Droit à l'oubli
- [ ] Export données

### 5.2 2FA
- [ ] Authenticator app
- [ ] SMS
- [ ] Recovery codes

### 5.3 Audit logging
- [ ] All actions logged
- [ ] IP tracking
- [ ] Admin alert sur changes

### 5.4 Rate limiting
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id);
});
```

**Total Phase 5:** ~10 jours

---

## 📱 Phase 6: Optimisations finales (1 semaine)

### 6.1 SEO
- [ ] Meta tags
- [ ] Sitemap
- [ ] Robots.txt
- [ ] Open Graph

### 6.2 Performance
- [ ] CDN pour images
- [ ] Compression JS/CSS
- [ ] Lazy loading
- [ ] Service worker

### 6.3 Analytics
```bash
composer require google/analytics
```

### 6.4 Monitoring
```bash
# Sentry
composer require sentry/sentry-laravel
```

**Total Phase 6:** ~5 jours

---

## 💰 Estimation totale

| Phase | Durée | Cumul | Focus |
|-------|-------|-------|-------|
| Phase 1 | ✅ 5 jours | ✅ 5 | MVP Core |
| Phase 2 | 11 jours | 16 | Tests + API |
| Phase 3 | 13 jours | 29 | Engagement |
| Phase 4 | 27 jours | 56 | Scaling |
| Phase 5 | 10 jours | 66 | Sécurité |
| Phase 6 | 5 jours | 71 | Polish |

**Total:** ~71 jours (10 semaines)

---

## 🎓 Points d'apprentissage

### Laravel avancé
- [ ] Event sourcing
- [ ] CQRS
- [ ] Queueing asynchrone
- [ ] Broadcasting WebSocket

### Infrastructure
- [ ] Docker + Compose
- [ ] CI/CD (GitHub Actions)
- [ ] Monitoring (DataDog)
- [ ] Logging centralisé (ELK)

### Frontend
- [ ] Vue/React pour interactivité
- [ ] Tailwind CSS avancé
- [ ] Accessibility (a11y)
- [ ] PWA

### DevOps
- [ ] Load balancing
- [ ] Auto-scaling
- [ ] Database replication
- [ ] Backup strategy

---

## 🎯 Quick Wins (Priorités)

### Court terme (Cette semaine)
- [x] **Phase 1 complète** - MVP fonctionnel
- [ ] **Tests basiques** - Coverage > 50%
- [ ] **Upload photos** - Stockage images

### Moyen terme (1-2 mois)
- [ ] **API RESTful** - Mobile prête
- [ ] **Notifications email** - Confirmations commandes
- [ ] **Pagination** - Performance listes
- [ ] **Admin dashboard** - Stats clés

### Long terme (3-6 mois)
- [ ] **Paiements** - Monétisation
- [ ] **App mobile** - Distribution iOS/Android
- [ ] **Real-time** - WebSockets
- [ ] **Analytics** - Insights

---

## 📋 Ressources et liens

### Documentation
- [Laravel 12](https://laravel.com/docs/12.x)
- [Sanctum API](https://laravel.com/docs/sanctum)
- [Broadcasting](https://laravel.com/docs/broadcasting)

### Services
- [Stripe](https://stripe.com)
- [Sendgrid](https://sendgrid.com)
- [Sentry](https://sentry.io)
- [AWS](https://aws.amazon.com)

### Outils
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- [Laravel Telescope](https://laravel.com/docs/telescope)
- [Laravel Horizon](https://laravel.com/docs/horizon)

---

## 📞 Communication

### Feedback des utilisateurs
- [ ] Survey: Fonctionnalités à ajouter
- [ ] Support chat
- [ ] Bug bounty program

### Community
- [ ] GitHub Discussions
- [ ] Discord server
- [ ] Blog/Articles

---

## 🚀 Lancements cibles

| Étape | Date | Disponibilité |
|-------|------|---------------|
| MVP Alpha | Semaine 1 | Internes |
| MVP Beta | Semaine 3 | Early adopters |
| Prod v1.0 | Semaine 6 | Public |
| Mobile v1.0 | Semaine 12 | iOS + Android |
| Scaling v2.0 | Semaine 16 | Enterprise |

---

## 📈 Success Metrics

- [ ] 1000+ users (3 mois)
- [ ] 100+ orders/jour (3 mois)
- [ ] 99.9% uptime (v2.0)
- [ ] < 100ms load time (v2.0)
- [ ] 4.5+ rating (v1.0)

