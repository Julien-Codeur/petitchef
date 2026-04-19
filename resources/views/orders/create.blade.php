<x-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">Finaliser votre Commande ✓</h1>
        <p style="color: #666; margin: 0;">Vérifiez vos articles et confirmez votre commande</p>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px;">
            <p style="margin: 0; color: #d32f2f; font-weight: 600; margin-bottom: 8px;">Erreurs:</p>
            <ul style="margin: 0; padding-left: 20px; color: #d32f2f;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #d32f2f;">
            <p style="margin: 0;">✗ {{ $message }}</p>
        </div>
    @endif

    @php
        $total = 0;
        $cookId = null;
        $cookForOrder = null;
    @endphp

    @if(count($cart) > 0 && count($dishes) > 0)
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <!-- Articles de la commande -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; background-color: #f5f5f5;">
                    <p style="margin: 0; font-size: 14px; font-weight: 700; color: #333;">Récapitulatif ({{ count($cart) }} article(s))</p>
                </div>

                <div style="padding: 16px;">
                    @foreach($dishes as $dish)
                        @if(isset($cart[$dish->id]))
                            @php
                                $itemTotal = $cart[$dish->id] * $dish->price;
                                $total += $itemTotal;
                                if ($cookId === null) {
                                    $cookId = $dish->cook_id;
                                    $cookForOrder = $dish->cook;
                                }
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e0e0e0;">
                                <div>
                                    <p style="margin: 0; font-weight: 700; color: #333;">{{ $dish->name }}</p>
                                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #999;">{{ $cart[$dish->id] }}x {{ number_format($dish->price, 2) }}€</p>
                                </div>
                                <p style="margin: 0; font-weight: 700; color: #ff6b35;">{{ number_format($itemTotal, 2) }}€</p>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div style="padding: 16px; background-color: #f5f5f5; border-top: 2px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 16px; font-weight: 700; color: #333;">TOTAL:</span>
                    <span style="font-size: 20px; font-weight: 700; color: #ff6b35;">{{ number_format($total, 2) }}€</span>
                </div>
            </div>

            <!-- Formulaire de commande -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Infos Cuisinier -->
                <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; border: 1px solid #ffe0b2;">
                    <p style="margin: 0 0 12px 0; font-size: 12px; font-weight: 600; color: #e65100; text-transform: uppercase;">Cuisinier</p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 48px; height: 48px; background-color: #ff6b35; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                            {{ substr($cookForOrder?->name ?? 'X', 0, 1) }}
                        </div>
                        <div>
                            <p style="margin: 0; font-weight: 700; color: #333;">{{ $cookForOrder?->name ?? 'Chargement...' }}</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">Artisan qualifié</p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire -->
                <form action="{{ route('orders.store') }}" method="POST" style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 16px; display: flex; flex-direction: column; gap: 16px;">
                    @csrf

                    <!-- Heure de retrait -->
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Heure de retrait *</label>
                        <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">Heure de retrait chez le cuisinier</p>
                    </div>

                    <!-- Note -->
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Note spéciale (optionnel)</label>
                        <textarea name="note_client" rows="3" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; font-family: 'Inter', sans-serif;" placeholder="Allergies, préférences, demandes spéciales...">{{ old('note_client') }}</textarea>
                    </div>

                    <!-- Checkbox conditions -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="agree_terms" required style="width: 20px; height: 20px; margin-top: 2px; cursor: pointer;">
                        <span style="font-size: 12px; color: #666;">
                            J'accepte les conditions générales et confirme ma commande. Cette commande sera préparée par <strong>{{ $cookForOrder?->name ?? 'le cuisinier' }}</strong>.
                        </span>
                    </label>

                    <!-- Bouttons -->
                    <div style="display: flex; gap: 12px; margin-top: 12px;">
                        <button type="submit" style="flex: 1; background-color: #4caf50; color: white; border: none; padding: 14px 16px; border-radius: 20px; font-size: 16px; font-weight: 700; cursor: pointer;" onmouseover="this.style.backgroundColor='#388e3c'" onmouseout="this.style.backgroundColor='#4caf50'">
                            ✓ Confirmer la Commande
                        </button>
                    </div>

                    <!-- Info légale -->
                    <p style="margin: 12px 0 0 0; font-size: 11px; color: #999; line-height: 1.5;">
                        Vous recevrez un email de confirmation. Les frais de service inclus. Aucun frais supplémentaire.
                    </p>
                </form>

                <!-- Lien retour -->
                <div style="text-align: center;">
                    <a href="{{ route('cart.index') }}" style="color: #ff6b35; font-size: 13px; font-weight: 600; text-decoration: none;">← Retour au panier</a>
                </div>
            </div>
        </div>
    @else
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 60px; text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">🛒</p>
            <p style="font-size: 18px; color: #333; margin: 0 0 8px 0; font-weight: 700;">Votre panier est vide</p>
            <p style="font-size: 14px; color: #999; margin: 0 0 24px 0;">Vous avez besoin de plats avant de passer une commande</p>
            <a href="{{ route('dishes.menu-du-jour') }}" style="background-color: #ff6b35; color: white; padding: 12px 32px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; display: inline-block; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                🍽️ Voir le menu du jour
            </a>
        </div>
    @endif
</div>
</x-sidebar-layout>
