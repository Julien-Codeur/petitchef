# Architecture Optimisée PetitChef

**Basée sur:** Insights cuisinier + What If scenarios + SCAMPER
**Objectif:** Simplicité cuisinier + Optimisation livraisons + Zéro chaos mental

---

## 1. MODÈLE DE DONNÉES AMÉLIORÉ

### Tables Principales

```sql
-- Utilisateurs
users
  id, name, email, password, role, phone, created_at
  roles: cook, client, admin, delivery_person

-- Plats du jour
dishes
  id, cook_id, name, description, price, initial_qty, photo_path
  is_active, served_date, created_at
  
  **NOUVEAU**: critical_threshold (ex: 5 pour courgettes, 2 pour salade)
              is_critical_notified (bool - évite notifications spam)

-- Commandes
orders
  id, client_id, cook_id, served_date, created_at
  pickup_time (heure souhaitée du client)
  delivery_address_id, delivery_location_name (nom humanisé: "Entreprise X" ou "Alice - Domicile")
  notes_client
  payment_method (cash/ticket_partenaire)
  created_at, updated_at

  **NOUVEAU**: order_group_id (groupe de commandes pour même livraison)
              status (reçue/en_préparation/prête/livrée/annulée)
              estimated_delivery_time (12h15, calculé par système)

-- Plats dans commandes (pivot)
order_items
  id, order_id, dish_id, quantity, unit_price
  customization_notes (allergies, etc.)

-- Livraisons (NEW - crucial!)
deliveries
  id, cook_id, served_date, planned_time (12h)
  status (en_préparation/prête_à_partir/en_route/complète)
  route_order (JSON: [addr_1, addr_2, addr_3] = ordre optimisé)
  estimated_total_duration (minutes)
  created_at, updated_at

-- Arrêts de livraison (NEW)
delivery_stops
  id, delivery_id, delivery_address_id, location_name
  sequence_order (1, 2, 3)
  orders_ids (JSON: [order_1, order_2, ...])
  status (en_attente/en_préparation/prête/livrée)
  estimated_arrival_time
  actual_arrival_time

-- Adresses de livraison (NEW)
delivery_addresses
  id, cook_id, name (humanisé: "Entreprise X" ou "Alice Dupont")
  address, lat, lng (pour carte)
  is_regular (pour entreprises régulières)
  created_at
```

---

## 2. WORKFLOW CUISINIER - OPTIMISÉ & SIMPLIFIÉ

### 9h00 - Préparation du Jour

**Écran: "Dashboard Jour"**

```
🍳 PetitChef - Samedi 25 avril

📊 STATUT GLOBAL
├─ Confirmées: 42 portions ✅
├─ Potentielles: 8 en attente
├─ Rendez-vous livraison: 12h00

📍 VOS LIVRAISONS OPTIMISÉES
├─ Livraison 1: Rue de la Paix (5 portions)
│  └─ Adresse: 123 Rue Paix, Paris 75001
│  └─ Statut: ⏳ En préparation
│  └─ Prêt estimé: 11h45
│
├─ Livraison 2: Avenue Y (3 portions)
│  └─ Adresse: 456 Avenue Y, Paris 75002
│  └─ Statut: ⏳ En préparation
│  └─ Prêt estimé: 11h50
│
├─ Livraison 3: Domicile Alice (1 portion)
│  └─ Adresse: 789 Place Z, Paris 75003
│  └─ Statut: 🟢 PRÊTE
│  └─ Prêt depuis: 11h20

⚠️ ALERTES CRITIQUES
├─ Courgettes farcies: 3 restantes (critique!)
└─ Salade: 1 restante (critique!)

📝 MENU DU JOUR (pour éditer)
├─ [✏️] Courgettes farcies (25/25 initial)
├─ [✏️] Poulet rôti (30/28 commandées)
├─ [✏️] Salade composée (12/11 commandées)
```

**Actions rapides:**
- `[Éditer Menu]` → Changer prix/dispo
- `[Voir Commandes]` → Liste détaillée
- `[Clôturer Service]` → Désactive tous les plats + génère "fermé"
- `[Optimiser Trajets]` → Recalcule ordre optimal

---

### 10h00 - Pendant la Cuisson

**Écran: "Commandes & Statuts"**

```
🔄 COMMANDES EN ATTENTE (5)
├─ Cmd#1 - Alice (Rue Paix): 2 courgettes
│  Status: [REÇUE] → [Marquer EN_PRÉPARATION]
│
├─ Cmd#2 - Bob (Rue Paix): 3 salades
│  Status: [REÇUE] → [Marquer EN_PRÉPARATION]
│
├─ Cmd#3 - Carol (Avenue Y): 2 courgettes
│  Status: [EN_PRÉPARATION]
│
├─ Cmd#4 - Dupont (Domicile): 1 poulet
│  Status: [PRÊTE] ✅
│
└─ Cmd#5 - Denis (Avenue Y): 1 salade
   Status: [REÇUE] → [Marquer EN_PRÉPARATION]

🟢 PRÊTS À LIVRER
├─ ✅ Cmd#4 - Dupont (Domicile)
└─ ✅ Cmd#1 - Alice (Rue Paix) [attends Bob]

📍 TOURNÉE OPTIMISÉE
[VISUALISER CARTE]
Rue Paix → Avenue Y → Domicile → Retour
Distance: 8.3km | Durée: 45min
```

**Interaction rapide:**
- Click sur commande → Voir détails (allergies, notes)
- Click status → Dropdown (Reçue / En préparation / Prête / Livrée)
- Auto-save = pas de bouton "Confirmer"

---

### 11h45 - Préparation Livraison

**Écran: "Prêt à Partir?"**

```
🚗 DÉPART PRÉVU: 12h00 (dans 15 min)

✅ PRÊTS À PARTIR (2 arrêts complets)
├─ Domicile Alice: 1 poulet ✅
└─ Rue Paix: 2 courgettes ✅
   (Bob: 3 salades = sera prête 12h15)

⏳ INCOMPLETS (à chercher après)
├─ Rue Paix (retour 12h30):
│  └─ Bob: 3 salades (5 min encore)
│
├─ Avenue Y (retour 13h00):
│  └─ Carol: 2 courgettes
│  └─ Denis: 1 salade

🗺️ TOURNÉE DÉPART
[DÉPART OPTIMISÉ]: Domicile Alice → Rue Paix
                    Retour cuisine 12h30 pour compléments

[BOUTON: JE PARS!]
```

**Ici l'insight clé:** Le système accepte les livraisons partielles et vous dit clairement:
- Quoi partir maintenant
- Quoi finir après
- Horaires réalistes pour clients

---

### 12h30 - Complément Livraison

Salades de Bob sont prêtes.

```
🔔 NOTIFICATION: Complément Rue Paix prêt!

📦 À LIVRER IMMÉDIATEMENT
├─ Bob (Rue Paix): 3 salades

💬 Client reçoit auto: 
   "Vos salades arrivent! Estimé 12h45"
```

---

## 3. WORKFLOW CLIENT - SIMPLE & CLAIR

### Client Vue Menu

```
☀️ MENU DU JOUR - Samedi 25 avril

🍲 Courgettes farcies
   💰 12€
   📸 [photo]
   📝 Courgettes bio farcies au riz
   
   ⚠️ BIENTÔT ÉPUISÉ (3 restantes)
   
   Quantité: [1▼] [Ajouter au panier]

🍗 Poulet rôti
   💰 15€
   [photo]
   📝 Poulet fermier rôti herbes
   
   ✅ En stock (28 portions)
   Quantité: [1▼] [Ajouter au panier]

🥗 Salade composée
   ❌ ÉPUISÉ
   [Notifiez-moi quand dispo]
```

---

### Client Passe Commande

```
🛒 MON PANIER

├─ 2x Courgettes farcies: 24€
├─ 1x Poulet rôti: 15€
   Total: 39€

📍 LIEU DE LIVRAISON
[Je fais livrer chez moi]
Adresse: 789 Place Z
OU
[Je récupère à l'entreprise]
Entreprise X - Rue Paix

⏰ HEURE SOUHAITÉE
Livraison entre: [12h00 - 13h00 ▼]

📝 NOTES (allergies, etc)
"Sans oignon dans les courgettes"

[COMMANDER]
```

---

### Client Suit sa Commande

```
📦 MES COMMANDES

Commande #1234
├─ 2x Courgettes farcies: 24€
├─ 1x Poulet: 15€
├─ Total: 39€
├─ Status: 🟡 EN PRÉPARATION
├─ Livraison estimée: 12h35
├─ Lieu: Rue de la Paix, Ent X
└─ [➡️ Suivre en direct]

🗺️ SUIVI EN DIRECT
[CARTE]
Ma commande = point rouge
Livreur en route = 7 min
Arrivée estimée: 12h35
```

---

## 4. WORKFLOW ADMIN - MODÉRATION & STATS

### Admin Vue

```
👥 ADMIN DASHBOARD

📊 STATISTIQUES
├─ Cuisiniers actifs: 3
├─ Clients aujourd'hui: 47
├─ Revenus totaux: 1,240€
├─ Commandes complètes: 94%

🔔 MODÉRATION
├─ Profils en attente validation: 2
│  └─ Jean-Luc Traiteur (nouveau)
│  └─ Marie Cuisine (nouveau)
├─ Signalements: 1
│  └─ Mauvaise qualité (Alice vs Cuisinier X)

⚙️ GESTION
├─ [Valider Cuisiniers]
├─ [Voir Signalements]
├─ [Stats Détaillées]
├─ [Gérer Partenaires Paiement]
```

---

## 5. MODÈLE ÉCONOMIQUE & PAIEMENT

### Paiement
- ✅ **Paiement à récupération** (cash)
- ✅ **Tickets partenaires** (banques, organisations)
- 🔄 **Paiement en ligne** (future, pas MVP)

### Monétisation PetitChef
- **Commission cuisiniers:** 15% par commande (après validation)
- **Publicité:** Plats "en avant" (optionnel pour cuisinier)
- **Données:** Stats anonymes vendues à chaînes/investisseurs

---

## 6. INNOVATIONS CLÉS

### 1. **Auto-Optimisation des Trajets**
- Input: Adresses livraison + horaires
- Algo: Nearest neighbor greedy
- Output: Route optimale + durée estimée
- Bénéfice: -30% temps livraison vs manuel

### 2. **Livraisons Partielles Intelligentes**
- Accepte que Rue A = 2/5 prêtes
- Cuisinier peut partir tôt, revenir pour complément
- Clients reçoivent estimé réaliste
- Bénéfice: Pas d'attente artificiellement longue

### 3. **Notifications Critiques Intelligentes**
- Par plat, seuil manuel
- Pas de spam (une fois "critique" → ne notifie plus jusqu'à restock)
- Cuisinier voit le compteur live

### 4. **Vue Unifiée Livraisons**
- Pas 3 pages (commandes/statuts/livraisons)
- Une vue: Qui, Quoi, Où, Quand, Status
- Click = détail, sinon tout sur 1 écran

### 5. **Flexibilité Entreprises**
- Profil "Entreprise": Lisa (elle commande pour 5 collègues)
- Panier de groupe + livraison unique
- Admin peut voir patterns (lundi = toujours ces 5)
- Future: Abonnement récurrent

---

## 7. QUESTIONS UX DE SOUTENANCE - RÉPONSES FONDÉES

### Q1: Comment indiquez-vous "bientôt épuisé" vs "épuisé"?

**Réponse:**
- **Bientôt épuisé:** `available <= critical_threshold` → Badge ⚠️ "BIENTÔT ÉPUISÉ (X restantes)"
- **Épuisé:** `available = 0` → Badge ❌ "ÉPUISÉ" + bouton "Notifiez-moi"
- **Seuil défini manuellement:** Courgettes critique=5 (demandées), Salade critique=2 (moins demandée)
- **Clients peuvent placer commande même avec ⚠️** (accepte le risque)

### Q2: Comment cuisinier gère 15 commandes simultanées sans chaos?

**Réponse:**
- **Vue par Livraison, pas par Commande:** Au lieu de 15 lignes, 5-6 "groupes livraison"
- **Statuts rapides:** Click commande → Dropdown (Reçue/Prép/Prête/Livrée) → Auto-save
- **Groupage intelligent:** Toutes commandes d'une même adresse = un groupe
- **Notifications critiques:** Que plats, pas chaque commande
- **Dashboard global:** En haut résumé (confirmées/potentielles/heure livraison) pour respirer

### Q3: Que si quantité zéro pendant qu'il commande?

**Réponse:**
- **Vérification transactionnelle:** `BEGIN TRANSACTION`
  1. Vérifier `available_qty >= quantity_demandée`
  2. Si OUI: `UPDATE dishes SET available_qty = available_qty - quantity` + créer order
  3. Si NON: Rollback + afficher erreur "Plus en stock, désolé!"
- **Feedback immédiat:** Client voit rouge "Stock insuffisant" + suggestion alternative
- **Pas d'oversell:** Contrainte DB + transaction = garantie

### Q4: Même layout Blade cuisinier/client?

**Réponse:**
- **NON:** Deux Blade layouts distincts
  - `layouts/cook.blade.php`: Dashboard dense, infos mécaniques (statuts/qtés)
  - `layouts/client.blade.php`: Esthétique, menu joli, suivi clair
- **Justification:** 
  - Cuisinier = ops (efficacité), Client = marketing (expérience)
  - Info prioritaire différente (cuisinier = traçabilité, client = livraison)
  - Mobile-first cuisinier vs desktop-friendly client
  - Rôles différents = UX différentes

### Q5: Comment optimisez-vous les trajets?

**Réponse:**
- **Algorithme:** Nearest neighbor greedy
  - Start: Cuisine (0,0)
  - Boucle: Pour chaque adresse non visitée, aller à la plus proche
  - Fin: Retour cuisine
- **Complexité:** O(n²) suffisant pour 10-15 stops
- **Intégration:** Appel API Google Maps (distance/durée réelle)
- **UI:** Carte visuelle + liste ordre + durée totale estimée
- **Bénéfice:** Cuisinier voit route optimale vs essais/erreurs mental

---

## 8. ROADMAP MVP

### V1.0 (MVP Cuisinier + Client Simple)
- ✅ Auth & profils (cook/client/admin)
- ✅ Menu du jour (CRUD cuisinier)
- ✅ Commandes (client passe, cuisinier voit)
- ✅ Statuts simples (Reçue/En préparation/Prête)
- ✅ Notifications critiques (par plat)
- ✅ Clôture service (batch Eloquent)
- ✅ Validation admin (sign-off cuisiniers)

### V1.1 (Livraisons Basiques)
- ✅ Adresses livraison
- ✅ Groupage par adresse
- ✅ Statuts livraison
- ✅ Carte Google Maps (visualisation)

### V1.2 (Optimisation Trajets)
- ✅ Algo nearest neighbor
- ✅ Route réellement optimisée
- ✅ Estimation durée

### V2.0 (Advanced)
- 📱 Suivi live client (WebSocket)
- 💳 Paiement en ligne
- 📊 Analytics complètes
- 🔄 Récurrence entreprises

---

## 9. STACK TECHNIQUE RECOMMANDÉ

```
Backend:  Laravel 11 (migrations, eloquent, policies)
Frontend: Vue 3 (réactivité) + Tailwind (design simple)
Maps:     Google Maps API (trajets + géolocalisation)
Real-time: Laravel Reverb (WebSocket pour suivi live)
DB:       PostgreSQL (transactions, JSON)
Hosting:  Vercel/Railway (deploy facile)
```

