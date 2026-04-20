<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background-color: #ff6b35; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 24px;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #333; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $user->name }}</h1>
                <p style="color: #999; margin: 4px 0 0 0;">Profil du cuisinier</p>
            </div>
        </div>
    </div>

    <!-- Status & Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Status Card -->
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
            <p style="margin: 0 0 12px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Statut Actuel</p>
            @if($user->is_verified)
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <span style="font-size: 32px;">✅</span>
                    <div>
                        <p style="margin: 0; font-size: 18px; font-weight: 700; color: #2e7d32;">APPROUVÉ</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Le cuisinier peut créer des plats</p>
                    </div>
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <span style="font-size: 32px;">⏳</span>
                    <div>
                        <p style="margin: 0; font-size: 18px; font-weight: 700; color: #e65100;">EN ATTENTE</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Approuvez ou rejetez ce profil</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Card -->
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
            <p style="margin: 0 0 12px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Actions Admin</p>
            @if(!$user->is_verified)
                <form action="{{ route('admin.cooks.approve', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="width: 100%; background-color: #4caf50; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; margin-bottom: 8px;" onmouseover="this.style.backgroundColor='#388e3c'" onmouseout="this.style.backgroundColor='#4caf50'">
                        ✅ APPROUVER
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('rejectForm').style.display='block'" style="width: 100%; background-color: #d32f2f; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;" onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'">
                    ❌ REJETER
                </button>
            @else
                <p style="margin: 0; color: #666; font-size: 14px; margin-bottom: 12px;">Ce profil est déjà approuvé.</p>
                <button type="button" onclick="document.getElementById('suspendForm').style.display='block'" style="width: 100%; background-color: #ff9800; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;" onmouseover="this.style.backgroundColor='#f57c00'" onmouseout="this.style.backgroundColor='#ff9800'">
                    ⚠️ SUSPENDRE
                </button>
            @endif
        </div>
    </div>

    <!-- Profile Information -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Informations Personnelles</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Nom</p>
                <p style="margin: 0; font-size: 14px; color: #333; font-weight: 600;">{{ $user->name }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Email</p>
                <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->email }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Téléphone</p>
                <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->phone ?? 'Non fourni' }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Inscrit depuis</p>
                <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 30px;">
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
            <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600;">Plats Créés</p>
            <p style="margin: 0; font-size: 28px; font-weight: 700; color: #ff6b35;">{{ $dishes->count() }}</p>
        </div>
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
            <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600;">Commandes Reçues</p>
            <p style="margin: 0; font-size: 28px; font-weight: 700; color: #705a49;">{{ $orderCount }}</p>
        </div>
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
            <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600;">Note Moyenne</p>
            <p style="margin: 0; font-size: 28px; font-weight: 700; color: #00677e;">{{ number_format($avgRating, 1) }}/5 ⭐</p>
        </div>
    </div>

    <!-- Recent Dishes -->
    @if($dishes->count() > 0)
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
            <h2 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Plats Créés ({{ $dishes->count() }})</h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($dishes->take(5) as $dish)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background-color: #f9f9f9; border-radius: 6px; border: 1px solid #e0e0e0;">
                        <div>
                            <p style="margin: 0; font-weight: 600; color: #333;">{{ $dish->name }}</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">{{ $dish->served_date->format('d/m/Y') }} • {{ $dish->available_qty }} disponibles</p>
                        </div>
                        <p style="margin: 0; font-weight: 700; color: #ff6b35;">{{ number_format($dish->price, 2) }}€</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    <div id="rejectForm" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; display: flex; align-items: center; justify-content: center;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; max-width: 500px; width: 90%;">
            <h2 style="font-size: 18px; font-weight: 700; color: #333; margin: 0 0 16px 0;">Rejeter le Cuisinier</h2>
            <form action="{{ route('admin.cooks.reject', $user) }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Raison du rejet *</label>
                    <textarea name="reason" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; min-height: 100px; box-sizing: border-box;" placeholder="Expliquez pourquoi ce profil est rejeté..."></textarea>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; background-color: #d32f2f; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Confirmer le rejet</button>
                    <button type="button" onclick="document.getElementById('rejectForm').style.display='none'" style="flex: 1; background-color: #ccc; color: #333; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendForm" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; display: flex; align-items: center; justify-content: center;">
        <div style="background-color: white; border-radius: 8px; padding: 30px; max-width: 500px; width: 90%;">
            <h2 style="font-size: 18px; font-weight: 700; color: #333; margin: 0 0 16px 0;">Suspendre le Cuisinier</h2>
            <form action="{{ route('admin.cooks.suspend', $user) }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Raison de la suspension *</label>
                    <textarea name="reason" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; min-height: 100px; box-sizing: border-box;" placeholder="Expliquez pourquoi ce profil est suspendu..."></textarea>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; background-color: #ff9800; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Suspendre</button>
                    <button type="button" onclick="document.getElementById('suspendForm').style.display='none'" style="flex: 1; background-color: #ccc; color: #333; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Back Button -->
    <div style="margin-top: 30px;">
        <a href="{{ route('admin.cooks.index') }}" style="display: inline-block; background-color: #e0e0e0; color: #333; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">← Retour à la liste</a>
    </div>
</div>
</x-admin-sidebar-layout>
