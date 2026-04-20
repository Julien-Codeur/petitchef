<x-client-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="cart" size="32" color="#333" />
            Mon Panier
        </h1>
        <p style="color: #666; margin: 0;">{{ count($cart) }} article(s) en attente</p>
    </div>

    <!-- Messages -->
    @if ($message = Session::get('success'))
        <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #2e7d32; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="check" size="20" color="#4caf50" />
            <p style="margin: 0;">{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #d32f2f; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="alert" size="20" color="#d32f2f" />
            <p style="margin: 0;">{{ $message }}</p>
        </div>
    @endif

    <!-- Stock Availability Check -->
    @if(count($cart) > 0)
        @php
            $stockIssues = [];
            foreach($dishes as $dish) {
                if(isset($cart[$dish->id]) && $cart[$dish->id] > $dish->available_qty) {
                    $stockIssues[] = $dish;
                }
            }
        @endphp

        @if(count($stockIssues) > 0)
            <div style="background-color: #fff3cd; border-left: 4px solid #ff9800; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #f57c00;">
                <p style="margin: 0 0 8px 0; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <x-icon icon="alert" size="18" color="#ff9800" />
                    Problèmes de stock détectés:
                </p>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($stockIssues as $issue)
                        <li>
                            <strong>{{ $issue->name }}</strong>: Vous avez demandé {{ $cart[$issue->id] }} mais seulement {{ $issue->available_qty }} disponible(s)
                        </li>
                    @endforeach
                </ul>
                <p style="margin: 8px 0 0 0; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                    <x-icon icon="info" size="14" color="#f57c00" />
                    Veuillez ajuster les quantités avant de continuer.
                </p>
            </div>
        @endif
    @endif

    @if(count($cart) > 0)
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <!-- Panier Items -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; background-color: #f5f5f5;">
                    <p style="margin: 0; font-size: 14px; font-weight: 700; color: #333;">Articles ({{ count($cart) }})</p>
                </div>

                <div style="display: flex; flex-direction: column;">
                    @php
                        $total = 0;
                    @endphp
                    @foreach($dishes as $dish)
                        @if(isset($cart[$dish->id]))
                            @php
                                $itemTotal = $cart[$dish->id] * $dish->price;
                                $total += $itemTotal;
                            @endphp
                            <div style="display: flex; align-items: center; padding: 16px; border-bottom: 1px solid #e0e0e0; gap: 12px;">
                                <!-- Image/Icône -->
                                <div style="width: 60px; height: 60px; background-color: #f5f5f5; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                    @if($dish->photo_path)
                                        <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    @else
                                        <x-icon icon="menu" size="24" color="#999" />
                                    @endif
                                </div>

                                <!-- Info Produit -->
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #333;">{{ $dish->name }}</h4>
                                    <p style="margin: 0; font-size: 12px; color: #999;">Chef: {{ $dish->cook->name }}</p>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; font-weight: 600; color: #ff6b35;">{{ number_format($dish->price, 2) }}€ x {{ $cart[$dish->id] }}</p>
                                    
                                    <!-- Stock Status -->
                                    <div style="margin-top: 6px;">
                                        @if($dish->available_qty <= 3 && $dish->available_qty > 0)
                                            <span style="display: inline-block; background-color: #fff3cd; color: #f57c00; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                <x-icon icon="alert" size="10" color="#f57c00" />
                                                Derniers {{ $dish->available_qty }} disponibles
                                            </span>
                                        @elseif($dish->available_qty <= 0)
                                            <span style="display: inline-block; background-color: #ffebee; color: #d32f2f; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                <x-icon icon="alert" size="10" color="#d32f2f" />
                                                En rupture
                                            </span>
                                        @else
                                            <span style="display: inline-block; background-color: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                <x-icon icon="check" size="10" color="#2e7d32" />
                                                {{ $dish->available_qty }} en stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Contrôles Quantité -->
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <form action="{{ route('cart.update', $dish) }}" method="POST" style="display: flex; gap: 4px;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ max(0, $cart[$dish->id] - 1) }}">
                                        <button type="submit" style="background-color: #f5f5f5; color: #333; border: 1px solid #e0e0e0; width: 32px; height: 32px; border-radius: 4px; cursor: pointer; font-weight: 600;">−</button>
                                    </form>
                                    <span style="width: 40px; text-align: center; font-weight: 600; color: #333;">{{ $cart[$dish->id] }}</span>
                                    <form action="{{ route('cart.update', $dish) }}" method="POST" style="display: flex; gap: 4px;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ min($dish->available_qty, $cart[$dish->id] + 1) }}">
                                        @if($cart[$dish->id] >= $dish->available_qty)
                                            <button type="button" disabled style="background-color: #ccc; color: #999; border: 1px solid #ccc; width: 32px; height: 32px; border-radius: 4px; cursor: not-allowed; font-weight: 600;" title="Stock insuffisant">+</button>
                                        @else
                                            <button type="submit" style="background-color: #ff6b35; color: white; border: none; width: 32px; height: 32px; border-radius: 4px; cursor: pointer; font-weight: 600;">+</button>
                                        @endif
                                    </form>
                                </div>

                                <!-- Prix Total & Supprimer -->
                                <div style="text-align: right;">
                                    <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: 700; color: #ff6b35;">{{ number_format($itemTotal, 2) }}€</p>
                                    <form action="{{ route('cart.remove', $dish) }}" method="POST"> display: flex; align-items: center; gap: 4px;">
                                            <x-icon icon="trash" size="12" color="#d32f2f" />
                                            Supprimer
                                        
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background-color: #ffebee; color: #d32f2f; border: none; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">🗑️ Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Résumé & Checkout -->
            <div style="background-color: #ff6b35; border-radius: 8px; padding: 20px; height: fit-content; color: white;">
                <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Résumé</h3>

                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.3);">
                    <span>Sous-total:</span>
                    <span>{{ number_format($total, 2) }}€</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.3);">
                    <span>Frais de service:</span>
                    <span>0€</span>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid rgba(255,255,255,0.3);">
                    <span style="font-weight: 700;">TOTAL:</span>
                    <span style="font-size: 20px; font-weight: 700;">{{ number_format($total, 2) }}€</span>
                </div>

                @php
                    $canCheckout = count($stockIssues ?? []) === 0;
                @endphp

                @if($canCheckout)
                    <a href="{{ route('orders.create') }}" style="display: block; background-color: white; color: #ff6b35; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; margin-bottom: 12px; cursor: pointer;">
                        ✓ Procéder au paiement
                    </a>
                @else
                    <button disabled style="display: block; width: 100%; background-color: #ccc; color: #999; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 12px; cursor: not-allowed; border: none;" title="Veuillez corriger les problèmes de stock">
                        ✓ Procéder au paiement
                    </button>
                @endif

                <a href="{{ route('dishes.index') }}" style="display: block; background-color: rgba(255,255,255,0.2); color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; border: 1px solid rgba(255,255,255,0.3); margin-bottom: 12px; cursor: pointer;">
                    ← Continuer les achats
                </a>

                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" style="width: 100%; background-color: rgba(255,255,255,0.2); color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; border: 1px solid rgba(255,255,255,0.3); cursor: pointer;" onclick="return confirm('Vider le panier?')">
                        🗑️ Vider le panier
                    </button>
                </form>
            </div>
        </div>
    @else
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 60px; text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">🛒</p>
            <p style="font-size: 18px; color: #333; margin: 0 0 8px 0; font-weight: 700;">Votre panier est vide</p>
            <p style="font-size: 14px; color: #999; margin: 0 0 24px 0;">Commencez à explorer nos délicieux plats !</p>
            <a href="{{ route('dishes.index') }}" style="background-color: #ff6b35; color: white; padding: 12px 32px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; display: inline-block; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                🍽️ Voir nos plats
            </a>
        </div>
    @endif
</div>
</x-client-sidebar-layout>
