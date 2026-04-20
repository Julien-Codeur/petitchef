<x-cook-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="menu" size="32" color="#333" />
            Mes Plats
        </h1>
        @if(auth()->user()->isCook())
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('dishes.create') }}" style="background-color: #ff6b35; color: white; padding: 12px 24px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    <x-icon icon="plus" size="16" color="white" />
                    Ajouter un plat
                </a>
                <form action="{{ route('dishes.close-service') }}" method="POST">
                    @csrf
                    <button type="submit" style="background-color: #705a49; color: white; padding: 12px 24px; border-radius: 20px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.backgroundColor='#5a4738'" onmouseout="this.style.backgroundColor='#705a49'" onclick="return confirm('Êtes-vous sûr? Cela désactivera tous vos plats du jour.')">
                        <x-icon icon="close" size="16" color="white" />
                        Clôturer le service
                    </button>
                </form>
            </div>
        @endif
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

    <!-- Filtres & Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Total plats</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #ff6b35;">{{ $dishes->count() }}</p>
        </div>
        <div style="background-color: #e8f5e9; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Actifs aujourd'hui</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #4caf50;">{{ $dishes->where('is_active', true)->where('served_date', '>=', today())->count() }}</p>
        </div>
        <div style="background-color: #e3f2fd; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Stock total</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #00677e;">{{ $dishes->sum('available_qty') }}</p>
        </div>
        <div style="background-color: #f3e5f5; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Chiffre potentiel</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #705a49;">{{ number_format($dishes->sum(function($dish) { return $dish->price * $dish->available_qty; }), 2) }}€</p>
        </div>
    </div>

    <!-- Grille des plats -->
    @if($dishes->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
            @foreach($dishes as $dish)
                <div style="background-color: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    
                    <!-- Photo -->
                    @if($dish->photo_path)
                        <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" style="width: 100%; height: 180px; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 180px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #999;">
                            📷 Pas de photo
                        </div>
                    @endif

                    <!-- Contenu -->
                    <div style="padding: 16px;">
                        <!-- Titre & Badges -->
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #333; flex: 1;">{{ $dish->name }}</h3>
                            <div style="display: flex; gap: 6px;">
                                @if($dish->is_active && $dish->served_date >= today())
                                    <span style="background-color: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">✓ Actif</span>
                                @else
                                    <span style="background-color: #ffebee; color: #d32f2f; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">✗ Inactif</span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p style="margin: 0 0 12px 0; font-size: 13px; color: #666; line-height: 1.5;">{{ Str::limit($dish->description, 80) }}</p>

                        <!-- Prix & Stock -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0;">
                            <div>
                                <p style="margin: 0; font-size: 11px; color: #999;">Prix</p>
                                <p style="margin: 4px 0 0 0; font-size: 18px; font-weight: 700; color: #ff6b35;">{{ number_format($dish->price, 2) }}€</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-size: 11px; color: #999;">Stock</p>
                                <p style="margin: 4px 0 0 0; font-size: 18px; font-weight: 700; color: {{ $dish->available_qty > 5 ? '#4caf50' : ($dish->available_qty > 0 ? '#ff9800' : '#d32f2f') }};">{{ $dish->available_qty }}</p>
                            </div>
                        </div>

                        <!-- Date & Cuisinier -->
                        <div style="font-size: 12px; color: #999; margin-bottom: 12px;">
                            📅 {{ $dish->served_date->format('d/m/Y') }}<br>
                            👨‍🍳 {{ $dish->cook->name }}
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('dishes.edit', $dish) }}" style="flex: 1; background-color: #705a49; color: white; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#5a4738'" onmouseout="this.style.backgroundColor='#705a49'">
                                ✏️ Modifier
                            </a>
                            <form action="{{ route('dishes.destroy', $dish) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="width: 100%; background-color: #d32f2f; color: white; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat?')">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 40px; text-align: center;">
            <p style="font-size: 32px; margin: 0 0 16px 0;">🍽️</p>
            <p style="font-size: 16px; color: #666; margin: 0;">Aucun plat pour le moment</p>
            <p style="font-size: 13px; color: #999; margin: 8px 0 0 0;">Commencez à ajouter vos spécialités !</p>
            @if(auth()->user()->isCook())
                <a href="{{ route('dishes.create') }}" style="display: inline-block; margin-top: 16px; background-color: #ff6b35; color: white; padding: 12px 24px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    ➕ Créer votre premier plat
                </a>
            @endif
        </div>
    @endif
</div>

<script>
/**
 * Real-time polling for chef's dishes
 */
(function() {
    const pollingEndpoint = '{{ route("api.dishes.my-dishes") }}';
    
    function updateChefDishes(data) {
        // Could update stats or highlight changes
        console.log('Dishes updated:', data);
    }
    
    // Start polling
    if (window.OrderPoller && '{{ auth()->user()->isCook() ? 'true' : 'false' }}' === 'true') {
        window.OrderPoller.pollInterval = 4000; // 4 seconds for dishes
        window.OrderPoller.start(pollingEndpoint, updateChefDishes);
    }
    
    // Stop polling when leaving page
    window.addEventListener('beforeunload', () => {
        if (window.OrderPoller) {
            window.OrderPoller.stop();
        }
    });
})();
</script>

</x-cook-sidebar-layout>
