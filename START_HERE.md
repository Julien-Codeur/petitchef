# 🚀 PetitChef - START HERE

**Bienvenue!** Vous venez de recevoir une application Laravel 12 complète et fonctionnelle.

---

## ⚡ En 5 minutes (pour les pressés)

```bash
# 1. Installer dépendances
composer install && npm install

# 2. Configurer
cp .env.example .env
php artisan key:generate

# 3. Base de données
php artisan migrate
php artisan db:seed

# 4. Lancer
php artisan serve  # Terminal 1
npm run dev         # Terminal 2

# 5. Ouvrir
# http://localhost:8000
# Email: jean@petitchef.local
# Pass: password
```

**Voilà! C'est prêt! ✅**

---

## 📖 Plan de lecture (30 min)

| Temps | Fichier | Lire |
|-------|---------|------|
| 2 min | **[FINAL_SUMMARY.md](FINAL_SUMMARY.md)** | Résumé de tout |
| 5 min | **[QUICKSTART.md](QUICKSTART.md)** | Setup détaillé |
| 5 min | **[USECASES.md](USECASES.md)** | Flux utilisateur |
| 5 min | **[README.md](README.md)** | Routes & architecture |
| 2 min | **[DOCS_INDEX.md](DOCS_INDEX.md)** | Index des autres docs |

**Fait!** Vous avez maintenant compris 80% du projet.

---

## 🎮 Tester immédiatement

### 1️⃣ Client - Commander un plat
```
- Login: jean@petitchef.local
- Voir les plats du jour
- Ajouter "Coq au vin" au panier
- Passer commande
- Voir le statut "Reçue"
```

### 2️⃣ Cuisinier - Préparer la commande
```
- Login: pierre@petitchef.local
- Voir les commandes reçues
- Cliquer "Commencer"
- Statut passe à "En préparation"
- Cliquer "Prête"
- Statut passe à "Prête"
```

### 3️⃣ Admin - Superviser
```
- Login: admin@petitchef.local
- Voir TOUTES les commandes
- Voir TOUS les plats
- Gestion complète
```

**🎉 Vous venez de tester le workflow complet!**

---

## 🎯 Comprendre la structure (10 min)

```
petitchef/                          # Racine
├── app/
│   ├── Models/                      # 📊 Données (Dish, Order, User)
│   ├── Http/
│   │   ├── Controllers/             # 🎮 Logique métier
│   │   ├── Requests/                # ✅ Validation
│   │   └── Middleware/              # 🔐 Protection
│   ├── Policies/                    # 👮 Authorization
│   └── Providers/                   # 🔧 Configuration
│
├── routes/
│   └── web.php                      # 🗺️ Toutes les routes
│
├── resources/views/                 # 🎨 Affichage (Blade)
│   ├── dishes/                      # Plats
│   ├── orders/                      # Commandes
│   ├── cart/                        # Panier
│   └── layouts/                     # Layout général
│
├── database/
│   ├── migrations/                  # 📋 Structure DB
│   └── seeders/                     # 🌱 Données de test
│
├── Documentation/                   # 📚 À lire!
│   ├── FINAL_SUMMARY.md
│   ├── QUICKSTART.md
│   ├── README.md
│   ├── ARCHITECTURE.md
│   ├── USECASES.md
│   └── [6 autres fichiers...]
│
└── [config, bootstrap, vendor...]   # Standard Laravel
```

**Clé:** Models → Controllers → Views → Database

---

## 🔑 Les 3 concepts clés

### 1. 👥 Rôles (3 types d'utilisateurs)
```
Client (Acheteur)        → Voir plats, commander
Cuisinier (Vendeur)      → Ajouter plats, préparer
Admin (Superviseur)      → Voir tout
```

### 2. 🛒 Commande (Cycle de vie)
```
Panier → Commande → Reçue → En préparation → Prête → Livrée
                  ↘ Annulée
```

### 3. 💾 Sécurité (Transaction DB)
```
Si un truc échoue pendant la création → TOUT s'annule
= Pas de commande sans stock
= Pas de stock décrémenté sans commande
```

---

## 🚀 Commandes importantes

```bash
# Voir les routes
php artisan route:list

# Tester la DB
php artisan tinker
>>> Dish::where('cook_id', 1)->get();

# Voir les logs
tail -f storage/logs/laravel.log

# Refracter tout
php artisan migrate:fresh --seed

# Optimiser (avant production)
php artisan optimize

# Tester (quand implémentés)
php artisan test
```

---

## ❓ Questions fréquentes

### Q: Où sont les comptes de test?
**A:** Dans `.env` après `db:seed`. Ou voir [QUICKSTART.md](QUICKSTART.md)

### Q: Pourquoi une erreur "CSRF token mismatch"?
**A:** Vous avez oublié `@csrf` dans un formulaire. Voir [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

### Q: Comment ajouter un nouveau plat?
**A:** Login cuisinier → "Ajouter plat" → Remplir form → Submit

### Q: Comment changer le statut d'une commande?
**A:** Login cuisinier → Voir commande → Bouton "Commencer"/"Prête"/etc.

### Q: Est-ce qu'il y a des tests?
**A:** Non encore, mais le code est testable. À ajouter en Phase 2.

### Q: Comment déployer?
**A:** Voir [DEPLOYMENT.md](DEPLOYMENT.md) (Heroku, DigitalOcean, AWS, etc.)

### Q: Où sont les uploads de photos?
**A:** `storage/app/public/dishes/`

### Q: Est-ce qu'il y a une API?
**A:** Template en `routes/api_example.php`. À implémenter en Phase 2.

---

## 🛠️ Troubleshooting rapide

### Erreur "SQLSTATE[HY000]"
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Erreur "Class not found"
```bash
composer dump-autoload
```

### DB vide après migration
```bash
php artisan db:seed
```

### Tout est cassé
```bash
php artisan migrate:fresh --seed
php artisan cache:clear
```

**Toujours pas bon?** Voir [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📚 Documentation complète

| Fichier | Quand lire |
|---------|-----------|
| **[FINAL_SUMMARY.md](FINAL_SUMMARY.md)** | Résumé exécutif |
| **[QUICKSTART.md](QUICKSTART.md)** | Installation rapide |
| **[README.md](README.md)** | Guide complet |
| **[ARCHITECTURE.md](ARCHITECTURE.md)** | Comprendre patterns |
| **[TRANSACTIONS.md](TRANSACTIONS.md)** | Comprendre DB |
| **[USECASES.md](USECASES.md)** | Voir les workflows |
| **[DEPLOYMENT.md](DEPLOYMENT.md)** | Déployer |
| **[CHECKLIST.md](CHECKLIST.md)** | Avant production |
| **[ROADMAP.md](ROADMAP.md)** | Futur du projet |
| **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** | Résoudre erreurs |
| **[DOCS_INDEX.md](DOCS_INDEX.md)** | Index all docs |

---

## 🎓 Apprendre le code

### Où commencer à lire?
1. `routes/web.php` - Voir toutes les routes
2. `app/Http/Controllers/DishController.php` - Controller complet
3. `app/Models/Dish.php` - Model avec relations
4. `resources/views/dishes/index.blade.php` - Vue complète

### Points clés à comprendre
- ✅ Relations Eloquent (BelongsTo, HasMany)
- ✅ Transactions DB pour atomicité
- ✅ Policies pour authorization
- ✅ Form Requests pour validation
- ✅ Blade templates pour vues

---

## 🚀 Prochaines étapes

### Aujourd'hui
- [x] Cloner/recevoir le repo
- [x] Installer dépendances
- [ ] Lancer localement
- [ ] Tester les 3 workflows

### Cette semaine
- [ ] Lire ARCHITECTURE.md
- [ ] Comprendre les Models
- [ ] Modifier quelque chose (test)
- [ ] Ajouter une feature

### Ce mois
- [ ] Ajouter tests
- [ ] Déployer quelque part
- [ ] Intégrer paiements
- [ ] Ajouter notifications

---

## 💡 Pro tips

1. **Utilisez Tinker** pour explorer la DB
   ```bash
   php artisan tinker
   >>> Dish::today()->active()->get()
   ```

2. **Lisez les migrations** pour comprendre la DB
   ```bash
   # database/migrations/
   ```

3. **Consultez les seeders** pour voir des données exemples
   ```bash
   # database/seeders/DatabaseSeeder.php
   ```

4. **Signetez DOCS_INDEX.md** pour navigation facile

5. **Lisez les commentaires** dans les controllers critiques

---

## ✅ Checklist avant de bosser

- [ ] Cloner/recevoir repo
- [ ] `composer install`
- [ ] `npm install`
- [ ] `cp .env.example .env`
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate`
- [ ] `php artisan db:seed`
- [ ] `php artisan serve` + `npm run dev`
- [ ] Lancer http://localhost:8000
- [ ] Login test et tester workflow
- [ ] Lire QUICKSTART.md
- [ ] Lire README.md

**Si tout check: You're ready! 🚀**

---

## 🎯 Les 80/20 (ce que vous DEVEZ savoir)

### 80% du travail vient de:
1. Lire `routes/web.php` - Savoir quoi existe
2. Lire Models - Comprendre les données
3. Lire Controllers - Comprendre la logique
4. Lire Views - Comprendre l'affichage
5. Tester localement - Voir ça marcher

### 20% du travail vient de:
- Détails de migration
- Configuration avancée
- Optimization
- Deployment

**Focus sur les 80%! 📊**

---

## 🎊 Ready?

```bash
# 1
composer install && npm install

# 2
cp .env.example .env && php artisan key:generate

# 3
php artisan migrate && php artisan db:seed

# 4
php artisan serve & npm run dev

# 5
open http://localhost:8000
# Login: jean@petitchef.local
# Password: password
```

**C'est parti! 🚀**

---

## 📞 Besoin d'aide?

1. **Erreur technique?** → [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
2. **Comment ça marche?** → [ARCHITECTURE.md](ARCHITECTURE.md)
3. **Installation bloquée?** → [QUICKSTART.md](QUICKSTART.md)
4. **Comprendre workflows?** → [USECASES.md](USECASES.md)
5. **Index everything?** → [DOCS_INDEX.md](DOCS_INDEX.md)

---

**Bienvenue dans PetitChef! Bon développement! 👨‍💻👩‍💻**

*Une application Laravel 12 complète pour la vente de repas maison*

✅ Prêt pour développement  
✅ Prêt pour test local  
✅ Prêt pour apprentissage  
✅ Prêt pour extension  

**Let's code! 🎉**

