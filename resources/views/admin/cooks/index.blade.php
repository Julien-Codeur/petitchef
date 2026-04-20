<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">Gestion des Cuisiniers</h1>
                <p style="color: #999; font-size: 14px;">Validez, supervisez et gérez les profils des cuisiniers</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 12px; margin-bottom: 30px;">
        <a href="{{ route('admin.cooks.index', ['filter' => 'all']) }}" style="padding: 10px 20px; border-radius: 20px; background-color: {{ $filter === 'all' ? '#ff6b35' : '#e0e0e0' }}; color: {{ $filter === 'all' ? 'white' : '#333' }}; text-decoration: none; font-weight: 600; font-size: 14px;">
            ✓ Tous ({{ $cooks->total() }})
        </a>
        <a href="{{ route('admin.cooks.index', ['filter' => 'verified']) }}" style="padding: 10px 20px; border-radius: 20px; background-color: {{ $filter === 'verified' ? '#4caf50' : '#e0e0e0' }}; color: {{ $filter === 'verified' ? 'white' : '#333' }}; text-decoration: none; font-weight: 600; font-size: 14px;">
            ✅ Approuvés ({{ \App\Models\User::where('role', 'cook')->where('is_verified', true)->count() }})
        </a>
        <a href="{{ route('admin.cooks.index', ['filter' => 'pending']) }}" style="padding: 10px 20px; border-radius: 20px; background-color: {{ $filter === 'pending' ? '#ff9800' : '#e0e0e0' }}; color: {{ $filter === 'pending' ? 'white' : '#333' }}; text-decoration: none; font-weight: 600; font-size: 14px;">
            ⏳ En attente ({{ \App\Models\User::where('role', 'cook')->where('is_verified', false)->count() }})
        </a>
    </div>

    <!-- Cooks Table -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                <tr>
                    <th style="padding: 16px; text-align: left; font-weight: 700; color: #333; font-size: 14px;">Nom</th>
                    <th style="padding: 16px; text-align: left; font-weight: 700; color: #333; font-size: 14px;">Email</th>
                    <th style="padding: 16px; text-align: left; font-weight: 700; color: #333; font-size: 14px;">Téléphone</th>
                    <th style="padding: 16px; text-align: left; font-weight: 700; color: #333; font-size: 14px;">Plats</th>
                    <th style="padding: 16px; text-align: left; font-weight: 700; color: #333; font-size: 14px;">Commandes</th>
                    <th style="padding: 16px; text-align: center; font-weight: 700; color: #333; font-size: 14px;">Status</th>
                    <th style="padding: 16px; text-align: center; font-weight: 700; color: #333; font-size: 14px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cooks as $cook)
                    <tr style="border-bottom: 1px solid #e0e0e0; hover:background-color: #fafafa;">
                        <td style="padding: 16px; color: #333; font-weight: 600;">{{ $cook->name }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $cook->email }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $cook->phone ?? 'N/A' }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $cook->dishes()->count() }} 🍽️</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $cook->ordersAsCook()->count() }} 📦</td>
                        <td style="padding: 16px; text-align: center;">
                            @if($cook->is_verified)
                                <span style="display: inline-block; background-color: #c8e6c9; color: #2e7d32; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 700;">✅ APPROUVÉ</span>
                            @else
                                <span style="display: inline-block; background-color: #ffe0b2; color: #e65100; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 700;">⏳ EN ATTENTE</span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <a href="{{ route('admin.cooks.show', $cook) }}" style="display: inline-block; background-color: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; margin-right: 8px;">
                                👁️ Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            Aucun cuisinier trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $cooks->links() }}
    </div>
</div>
</x-admin-sidebar-layout>
