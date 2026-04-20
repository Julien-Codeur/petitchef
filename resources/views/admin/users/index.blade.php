<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">👥 Gestion des Utilisateurs</h1>
        <p style="color: #999; font-size: 14px;">Liste des clients inscrits</p>
    </div>

    <!-- Users Table -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f9f9f9; border-bottom: 2px solid #e0e0e0;">
                <tr>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Nom</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Email</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Téléphone</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Rôle</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Statut</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #e0e0e0; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 16px; font-weight: 600; color: #333;">{{ $user->name }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $user->email }}</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $user->phone ?? 'N/A' }}</td>
                        <td style="padding: 16px;">
                            @if($user->role === 'admin')
                                <span style="background-color: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">🛡️ Admin</span>
                            @elseif($user->role === 'cook')
                                <span style="background-color: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">👨‍🍳 Chef</span>
                            @else
                                <span style="background-color: #d1ecf1; color: #0c5460; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">👤 Client</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($user->is_verified)
                                <span style="background-color: #d4edda; color: #155724; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✓ Vérifié</span>
                            @else
                                <span style="background-color: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">⏳ En attente</span>
                            @endif
                        </td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $user->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 12px;">👥</div>
                            <p style="font-size: 14px; margin: 0;">Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $users->links() ?? '' }}
    </div>
</div>
</x-admin-sidebar-layout>
