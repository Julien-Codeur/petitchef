<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">📋 Gestion des Signalements</h1>
        <p style="color: #999; font-size: 14px;">Examinez et résolvez les signalements des utilisateurs</p>
    </div>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">⏳ En attente</div>
            <div style="font-size: 32px; font-weight: 700; color: #ff6b35; margin-bottom: 8px;">{{ $stats['pending'] ?? 0 }}</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">🔍 En investigation</div>
            <div style="font-size: 32px; font-weight: 700; color: #ff6b35; margin-bottom: 8px;">{{ $stats['investigating'] ?? 0 }}</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">🔴 Haute priorité</div>
            <div style="font-size: 32px; font-weight: 700; color: #d32f2f; margin-bottom: 8px;">{{ $stats['high_priority'] ?? 0 }}</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">⚠️ Non résolus</div>
            <div style="font-size: 32px; font-weight: 700; color: #ff9800; margin-bottom: 8px;">{{ $stats['total_unresolved'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Reports Table -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f9f9f9; border-bottom: 2px solid #e0e0e0;">
                <tr>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">ID</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Type</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Catégorie</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Signalé par</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Statut</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Priorité</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #666; font-size: 13px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr style="border-bottom: 1px solid #e0e0e0; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 16px; font-weight: 600; color: #333;">#{{ $report->id }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">
                            @if($report->type === 'cook')
                                <span>👨‍🍳 Cuisinier</span>
                            @elseif($report->type === 'client')
                                <span>👤 Client</span>
                            @elseif($report->type === 'dish')
                                <span>🍽️ Plat</span>
                            @elseif($report->type === 'order')
                                <span>📦 Commande</span>
                            @else
                                <span>🖥️ Plateforme</span>
                            @endif
                        </td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $report->category ?? 'N/A' }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $report->reporter->name ?? 'N/A' }}</td>
                        <td style="padding: 16px;">
                            @if($report->status === 'pending')
                                <span style="background-color: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">⏳ En attente</span>
                            @elseif($report->status === 'investigating')
                                <span style="background-color: #d1ecf1; color: #0c5460; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">🔍 Investigation</span>
                            @elseif($report->status === 'resolved')
                                <span style="background-color: #d4edda; color: #155724; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✓ Résolu</span>
                            @else
                                <span style="background-color: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✗ Rejeté</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if(($report->priority ?? 1) >= 3)
                                <span style="background-color: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">🔴 Haute</span>
                            @else
                                <span style="background-color: #d4edda; color: #155724; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">🟢 Normale</span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <a href="{{ route('admin.reports.show', $report) }}" style="display: inline-block; background-color: #ff6b35; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
                            <p style="font-size: 14px; margin: 0;">Aucun signalement trouvé</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $reports->links() ?? '' }}
    </div>
</div>
</x-admin-sidebar-layout>
