# PetitChef - Cas d'usage et flux utilisateur

## Cas d'usage 1: Client passe commande

### Flux complet

1. **Accueil non-authentifié**
   - Client arrive sur `/`
   - Voit le landing page
   - Clique "Inscription" ou "Connexion"

2. **Inscription**
   - Email: jean@example.com
   - Mot de passe: password123
   - Rôle: Client
   - Téléphone: +33612345678

3. **Voir les plats du jour**
   ```
   GET /dashboard (redirect to /dishes)
   GET /dishes
   ```
   - Voit les plats d'aujourd'hui actifs
   - Filtrés: served_date = today() AND is_active = true
   - Affiche: Coq au vin, Blanquette de veau, Ratatouille, Bouillabaisse

4. **Ajouter au panier**
   - Client clique "Ajouter au panier" sur "Coq au vin"
   - Modal popup: "Quantité?"
   - Saisit: 2
   - Session cart: { 1: 2 }
   - Message: "Plat ajouté au panier"

5. **Ajouter autre plat**
   - Voir plat "Ratatouille"
   - Ajouter 1 portion
   - Session cart: { 1: 2, 3: 1 }

6. **Consulter panier**
   ```
   GET /cart
   ```
   - Voit:
     - Coq au vin: 12.50€ × 2 = 25€
     - Ratatouille: 10€ × 1 = 10€
     - Total: 35€
   - Peut modifier quantités
   - Peut supprimer articles

7. **Passer commande**
   ```
   GET /orders/create
   ```
   - Récap des plats
   - Saisit: Heure de retrait = 12:30
   - Saisit: Note = "Sans sel svp"
   - Clique "Passer la commande"

8. **Création commande**
   ```
   POST /orders
   ```
   - Transaction DB:
     - Vérifier stock Coq au vin (5 → 3 ok)
     - Vérifier stock Ratatouille (8 → 7 ok)
     - Créer Order: id=1, client_id=1, cook_id=1, total=35€, status=received
     - Créer OrderDish: (1, 1, 2, 12.50) et (1, 3, 1, 10.00)
     - Décrémenter stock
     - Effacer panier
   - Redirect: `/orders/1`

9. **Voir la commande**
   ```
   GET /orders/1
   ```
   - Affiche:
     - Numéro: #1
     - Date: 18/04/2026 14:30
     - Statut: Reçue (bleu)
     - Heure retrait: 12:30
     - Total: 35€
     - Détail: 2× Coq au vin (12.50€), 1× Ratatouille (10€)
     - Client: Jean Dupont
     - Cuisinier: Chef Pierre

10. **Suivi en temps réel** (Amélioration future)
    - Statut change à 14:45: "En préparation" (jaune)
    - Statut change à 15:15: "Prête" (vert)
    - Client reçoit notification

11. **Retrait**
    - Client arrive à 12:30
    - Se présente au cuisinier
    - Cuisinier change statut: "Livrée"
    - Commande terminée

### Cas alternatifs

**Cas: Stock insuffisant**
- Client veut 3× Coq au vin
- Stock: 2
- À la création: Exception
- Message: "Stock insuffisant pour Coq au vin"
- Stock revient à 5

**Cas: Deux cuisiniers**
- Client ajoute "Coq au vin" (Chef Pierre)
- Client ajoute "Ratatouille" (Chef Marie)
- À la création: Exception
- Message: "Vous ne pouvez commander que chez un seul cuisinier"
- Panier conservé

**Cas: Annulation**
- Commande status = "received"
- Client clique "Annuler"
- DELETE /orders/1
- Stock restauré:
  - Coq au vin: 3 → 5
  - Ratatouille: 7 → 8
- Commande status = "cancelled"

---

## Cas d'usage 2: Cuisinier gère ses plats

### Flux complet

1. **Connexion**
   - Email: pierre@petitchef.local
   - Mot de passe: password
   - Rôle: Cook

2. **Voir ses plats**
   ```
   GET /dishes
   ```
   - Voit ses 2 plats:
     - Coq au vin (5 disponibles)
     - Blanquette de veau (3 disponibles)
   - Peut éditer ou supprimer

3. **Créer un plat**
   ```
   GET /dishes/create
   POST /dishes
   ```
   - Form:
     - Nom: "Cassoulet"
     - Description: "Cassoulet de Toulouse authentique"
     - Prix: 16.50€
     - Quantité: 4
     - Date: 19/04/2026 (demain)
     - Photo: upload image
   - POST crée le plat
   - Redirect: `/dishes` avec message success

4. **Modifier un plat**
   ```
   GET /dishes/1/edit
   PATCH /dishes/1
   ```
   - Form pré-rempli:
     - Quantity: 5 → 3 (des clients ont commandé)
     - Photo: affiche l'ancienne
   - Peut uploader nouvelle photo
   - Redirect: `/dishes/1` (show)

5. **Voir les commandes reçues**
   ```
   GET /orders
   ```
   - Voit:
     - Commande #1: Jean Dupont, 2× Coq au vin, Statut: Reçue
     - Commande #2: Sophie Martin, 1× Coq au vin, Statut: Reçue
   - Peut cliquer pour voir détails

6. **Détails commande**
   ```
   GET /orders/1
   ```
   - Affiche:
     - Numéro: #1
     - Client: Jean (jean@example.com)
     - Plats: 2× Coq au vin
     - Total: 25€
     - Statut: Reçue
     - Heure retrait: 12:30
     - Note: "Sans sel svp"

7. **Gérer statuts**
   - Button "Commencer préparation"
   ```
   PATCH /orders/1/status
   status = "preparing"
   ```
   - Statut devient: "En préparation" (jaune)
   - Client reçoit notification

   - Après 15 minutes, button "Prête"
   ```
   PATCH /orders/1/status
   status = "ready"
   ```
   - Statut devient: "Prête" (vert)
   - Client notifié

   - Au retrait, button "Livrée"
   ```
   PATCH /orders/1/status
   status = "delivered"
   ```
   - Statut devient: "Livrée" (purple)
   - Commande terminée

8. **Clôturer le service**
   ```
   POST /dishes/1/close-service
   ```
   - Désactive tous les plats du jour
   - Messages: "4 plat(s) désactivé(s)"
   - Clients ne peuvent plus voir ces plats
   - Clients ne peuvent plus commander

---

## Cas d'usage 3: Admin supervise

### Flux complet

1. **Connexion**
   - Email: admin@petitchef.local
   - Mot de passe: password
   - Rôle: Admin

2. **Voir toutes les commandes**
   ```
   GET /orders
   ```
   - Voit toutes les commandes
   - Filtre par statut, date, cuisinier, client

3. **Valider un cuisinier**
   - Voir users table
   - Cook: is_verified = false
   - Passer is_verified = true
   - Cook peut maintenant créer des plats

4. **Voir statistiques** (Amélioration future)
   - Nombre total commandes
   - Revenu total
   - Nombre clients
   - Nombre cuisiniers
   - Ratings moyens

5. **Signalements** (Amélioration future)
   - Voir les plaintes des clients
   - Modérer les reviews
   - Bannir les utilisateurs

---

## Scénario d'erreur: Race condition sur le stock

### Chronologie

**T0:** Coq au vin: 2 disponibles

**T0 + 0ms:**
```
Client A: Ajout panier (1 quantité)
Client B: Ajout panier (2 quantités)
```

**T0 + 10ms:**
```
Client A: POST /orders [1× Coq au vin]
└─ Transaction DB:
   ├─ Check stock: 2 >= 1 ✓
   ├─ Create Order
   ├─ Create OrderDish
   └─ Decrement: 2 → 1
```

**T0 + 15ms:**
```
Client B: POST /orders [2× Coq au vin]
└─ Transaction DB:
   ├─ Check stock: 1 >= 2 ✗ FAIL
   └─ ROLLBACK - Rien n'est créé
```

**Résultat:**
- ✅ Stock: 1 (Correct!)
- ✅ Commande A créée
- ❌ Commande B échouée
- Client B reçoit: "Stock insuffisant"

---

## Scénario à tester: Panier avec plats différents

### Initialisation
```
Chef Pierre a:
- Coq au vin (5)
- Blanquette de veau (3)

Chef Marie a:
- Ratatouille (8)
- Bouillabaisse (4)
```

### Client ajoute au panier
1. Ajoute "Coq au vin" ✓
2. Ajoute "Ratatouille" ✓ (panier: {1: 1, 3: 1})
3. Clique "Passer commande"

### Tentative de commande
- POST /orders avec items: [{dish_id: 1}, {dish_id: 3}]
- Vérification:
  - Dish 1 cook_id = 1 (Chef Pierre)
  - Dish 3 cook_id = 2 (Chef Marie)
  - cookId != previousCookId → Exception
- Message: "Vous ne pouvez commander que chez un seul cuisinier"
- Panier conservé

### Client retire Ratatouille
- DELETE /cart/3
- Panier: {1: 1}
- POST /orders maintenant OK

---

## Scénario: Modification de prix

### Situation
- 14:00: Client voit "Coq au vin" à 12.50€
- 14:00:30: Chef change le prix à 15€
- 14:00:45: Client passe commande

### Vérification avant création
- Affiche le prix actuel: 15€
- Client confirme

### À la création
- OrderDish.unit_price = 15.00 (prix actuel)
- ✓ Correct! Client paie le prix correct

### Après livraison
- Admin peut voir l'historique des prix
- Si client conteste: "Vous aviez accepté 15€"

---

## Checklist de test manuelle

- [ ] S'inscrire comme Client
- [ ] Voir les plats du jour
- [ ] Ajouter au panier (modal)
- [ ] Modifier quantité
- [ ] Retirer du panier
- [ ] Passer commande
- [ ] Voir la commande
- [ ] S'inscrire comme Cook
- [ ] Créer un plat
- [ ] Éditer un plat
- [ ] Voir les commandes reçues
- [ ] Changer statuts
- [ ] Clôturer le service
- [ ] Voir que les plats sont inactifs
- [ ] Vérifier que stock diminue
- [ ] Annuler une commande
- [ ] Voir que stock augmente
- [ ] Tenter commande stock insuffisant
- [ ] Tenter commande deux cuisiniers

