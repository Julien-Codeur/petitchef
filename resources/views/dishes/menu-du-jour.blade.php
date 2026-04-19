<x-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">Menu du Jour 🍽️</h1>
        <p style="color: #666; margin: 0;">Découvrez les meilleures cuisines artisanales locales</p>
    </div>

    <!-- Filtres & Recherche -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
            <!-- Recherche -->
            <div>
                <input type="text" id="search" placeholder="🔍 Rechercher un plat..." style="width: 100%; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 20px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Filtre Prix Max -->
            <div>
                <input type="number" id="priceFilter" placeholder="💰 Prix max" min="0" step="0.01" style="width: 100%; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 20px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Panier -->
            <a href="{{ route('cart.index') }}" style="display: flex; align-items: center; justify-content: center; background-color: #ff6b35; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; gap: 8px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                🛒 Panier
                @php
                    $cart = session()->get('cart', []);
                @endphp
                @if(count($cart) > 0)
                    <span style="background-color: white; color: #ff6b35; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 12px;">{{ count($cart) }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Stats & Infos -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Cuisiniers</p>
            <p id="statCooks" style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #ff6b35;">{{ $cooks->count() }}</p>
        </div>
        <div style="background-color: #e8f5e9; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Plats disponibles</p>
            <p id="statDishes" style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #4caf50;">{{ $dishes->count() }}</p>
        </div>
        <div style="background-color: #e3f2fd; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Portions en stock</p>
            <p id="statStock" style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #00677e;">{{ $dishes->sum('available_qty') }}</p>
        </div>
    </div>

    <!-- Affichage par Cuisinier -->
    <div data-all-dishes-container>
    @if($cooks->count() > 0)
        @foreach($cooks as $cook)
            <?php
                $cookDishes = $dishes->where('cook_id', $cook->id);
            ?>
            @if($cookDishes->count() > 0)
                <!-- Section Cuisinier -->
                <div style="margin-bottom: 30px;" data-cook-section="{{ $cook->id }}">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #ff6b35;">
                        <div style="width: 40px; height: 40px; background-color: #ff6b35; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                            {{ substr($cook->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">👨‍🍳 {{ $cook->name }}</h2>
                            <p style="margin: 2px 0 0 0; font-size: 12px; color: #999;">{{ $cookDishes->count() }} plat(s) disponible(s)</p>
                        </div>
                    </div>

                    <!-- Grille des plats -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
                        @foreach($cookDishes as $dish)
                            <div style="background-color: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s;" data-dish-id="{{ $dish->id }}">
                                
                                <!-- Photo -->
                                @if($dish->photo_path)
                                    <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" style="width: 100%; height: 160px; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 160px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #999; font-size: 32px;">
                                        🍽️
                                    </div>
                                @endif

                                <!-- Contenu -->
                                <div style="padding: 16px;">
                                    <!-- Badge Stock -->
                                    @if($dish->available_qty <= 0)
                                        <span style="display: inline-block; background-color: #ffebee; color: #d32f2f; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-bottom: 8px;">RUPTURE DE STOCK</span>
                                    @elseif($dish->available_qty <= 3)
                                        <span style="display: inline-block; background-color: #fff3e0; color: #e65100; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-bottom: 8px;">DERNIER STOCK</span>
                                    @endif

                                    <!-- Titre -->
                                    <h3 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 700; color: #333;">{{ $dish->name }}</h3>

                                    <!-- Description -->
                                    <p style="margin: 0 0 12px 0; font-size: 13px; color: #666; line-height: 1.4;">{{ Str::limit($dish->description, 60) }}</p>

                                    <!-- Prix & Stock -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0;">
                                        <div>
                                            <p style="margin: 0; font-size: 11px; color: #999;">Prix</p>
                                            <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #ff6b35;">{{ number_format($dish->price, 2) }}€</p>
                                        </div>
                                        <div style="text-align: right;">
                                            <p style="margin: 0; font-size: 11px; color: #999;">Stock</p>
                                            <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: {{ $dish->available_qty > 5 ? '#4caf50' : ($dish->available_qty > 0 ? '#ff9800' : '#d32f2f') }};">{{ $dish->available_qty }}</p>
                                        </div>
                                    </div>

                                    <!-- Bouton Ajouter -->
                                    @if($dish->available_qty > 0)
                                        <button type="button" onclick="openAddToCartModal({{ $dish->id }}, '{{ addslashes($dish->name) }}', {{ $dish->price }}, {{ $dish->available_qty }})" style="width: 100%; background-color: #ff6b35; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 14px; font-weight: 700; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                                            ➕ Ajouter au panier
                                        </button>
                                    @else
                                        <button disabled style="width: 100%; background-color: #e0e0e0; color: #999; border: none; padding: 12px; border-radius: 6px; font-size: 14px; font-weight: 700; cursor: not-allowed;">
                                            ✗ Rupture de stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 60px; text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">🔍</p>
            <p style="font-size: 18px; color: #333; margin: 0 0 8px 0; font-weight: 700;">Aucun plat disponible</p>
            <p style="font-size: 14px; color: #999; margin: 0;">Revenir plus tard pour découvrir les délices de nos cuisiniers !</p>
        </div>
    @endif
    </div>

    <!-- Modal Ajouter au panier -->
    <div id="addToCartModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; max-width: 400px; width: 90%;">
            <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">Ajouter au panier</h3>
            <p style="margin: 0 0 20px 0; font-size: 14px; color: #666;">
                <strong id="dishName"></strong><br>
                <span id="dishPrice" style="color: #ff6b35; font-weight: 700; font-size: 16px;"></span>€
            </p>

            <form id="addToCartForm" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Quantité:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="1" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; background-color: #ff6b35; color: white; border: none; padding: 12px; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                        ✓ Ajouter
                    </button>
                    <button type="button" onclick="closeAddToCartModal()" style="flex: 1; background-color: #f5f5f5; color: #333; border: 1px solid #e0e0e0; padding: 12px; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        ✕ Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddToCartModal(dishId, dishName, dishPrice, maxQty) {
            document.getElementById('dishName').textContent = dishName;
            document.getElementById('dishPrice').textContent = dishPrice.toFixed(2);
            document.getElementById('quantity').max = maxQty;
            document.getElementById('quantity').value = 1;
            document.getElementById('addToCartForm').action = `/cart/${dishId}`;
            document.getElementById('addToCartModal').style.display = 'flex';
        }

        function closeAddToCartModal() {
            document.getElementById('addToCartModal').style.display = 'none';
        }

        // Fermer modal au clic en dehors
        document.getElementById('addToCartModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddToCartModal();
            }
        });

        // Données des plats pour recherche côté client
        const dishes = {!! collect($dishes)->map(function($dish) {
            return collect(['id' => $dish->id, 'name' => $dish->name, 'description' => $dish->description, 'price' => $dish->price, 'cook_id' => $dish->cook_id, 'cook_name' => $dish->cook->name, 'available_qty' => $dish->available_qty]);
        })->values()->toJson() !!};

        // Fonction de filtrage
        function filterDishes() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const maxPrice = parseFloat(document.getElementById('priceFilter').value) || Infinity;
            
            let visibleDishes = 0;
            let visibleCooks = new Set();
            let totalStock = 0;

            // Filtrer tous les plats
            document.querySelectorAll('[data-dish-id]').forEach(element => {
                const dishId = parseInt(element.getAttribute('data-dish-id'));
                const dish = dishes.find(d => d.id === dishId);
                
                const matchesSearch = 
                    dish.name.toLowerCase().includes(searchTerm) ||
                    dish.description.toLowerCase().includes(searchTerm) ||
                    dish.cook_name.toLowerCase().includes(searchTerm);
                
                const matchesPrice = dish.price <= maxPrice;
                const isVisible = matchesSearch && matchesPrice && dish.available_qty > 0;
                
                element.style.display = isVisible ? 'block' : 'none';
                
                if (isVisible) {
                    visibleDishes++;
                    visibleCooks.add(dish.cook_id);
                    totalStock += dish.available_qty;
                }
            });

            // Afficher/masquer les sections des cuisiniers
            document.querySelectorAll('[data-cook-section]').forEach(section => {
                const cookId = parseInt(section.getAttribute('data-cook-section'));
                const hasVisibleDishes = section.querySelector('[data-dish-id]:not([style*="display: none"])');
                section.style.display = hasVisibleDishes ? 'block' : 'none';
            });

            // Mettre à jour les stats
            document.getElementById('statDishes').textContent = visibleDishes;
            document.getElementById('statCooks').textContent = visibleCooks.size;
            document.getElementById('statStock').textContent = totalStock;

            // Afficher message si aucun résultat
            const noResults = visibleDishes === 0;
            const existingMessage = document.getElementById('noResultsMessage');
            
            if (noResults && !existingMessage) {
                const message = document.createElement('div');
                message.id = 'noResultsMessage';
                message.style.cssText = 'background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 60px; text-align: center; margin-top: 20px;';
                message.innerHTML = '<p style="font-size: 48px; margin: 0 0 16px 0;">🔍</p><p style="font-size: 18px; color: #333; margin: 0 0 8px 0; font-weight: 700;">Aucun plat ne correspond</p><p style="font-size: 14px; color: #999; margin: 0;">Essayez une autre recherche ou prix maximum</p>';
                document.querySelector('[data-all-dishes-container]').appendChild(message);
            } else if (!noResults && existingMessage) {
                existingMessage.remove();
            }
        }

        // Event listeners pour recherche et filtre
        document.getElementById('search').addEventListener('keyup', filterDishes);
        document.getElementById('priceFilter').addEventListener('change', filterDishes);
        document.getElementById('priceFilter').addEventListener('input', filterDishes);
    </script>
</div>
</x-sidebar-layout>
