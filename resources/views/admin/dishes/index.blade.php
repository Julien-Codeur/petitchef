<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">🍲 Gestion des Plats</h1>
        <p style="color: #999; font-size: 14px;">Tous les plats proposés par les cuisiniers</p>
    </div>

    <!-- Dishes Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        @forelse($dishes as $dish)
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <!-- Image Placeholder -->
                <div style="width: 100%; height: 160px; background: linear-gradient(135deg, #fff3ed 0%, #ffe8d6 100%); display: flex; align-items: center; justify-content: center; font-size: 48px;">
                    🍽️
                </div>

                <!-- Content -->
                <div style="padding: 16px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $dish->name }}</h3>
                    <p style="margin: 0 0 12px 0; color: #999; font-size: 13px;">{{ Str::limit($dish->description, 80) }}</p>

                    <!-- Cook -->
                    <p style="margin: 0 0 12px 0; color: #666; font-size: 13px;">
                        <strong>👨‍🍳</strong> {{ $dish->cook->name ?? 'N/A' }}
                    </p>

                    <!-- Price & Stock -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 18px; font-weight: 700; color: #ff6b35;">{{ number_format($dish->price, 2) }}€</span>
                        <span style="font-size: 12px; font-weight: 600; color: #666;">Stock: {{ $dish->available_quantity }}</span>
                    </div>

                    <!-- Status -->
                    <div style="margin-bottom: 12px;">
                        @if($dish->is_available)
                            <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✓ Disponible</span>
                        @else
                            <span style="display: inline-block; background-color: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✗ Indisponible</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <div style="font-size: 48px; margin-bottom: 12px;">🍽️</div>
                <p style="font-size: 14px; color: #999; margin: 0;">Aucun plat trouvé</p>
            </div>
        @endforelse
    </div>
</div>
</x-admin-sidebar-layout>
