<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header with back link -->
    <div style="margin-bottom: 30px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('admin.reports.index') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">← Retour</a>
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">📋 Signalement #{{ $report->id }}</h1>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Main Content -->
        <div>
            <!-- Report Details Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Signalé par</h3>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #ff6b35; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                        {{ substr($report->reporter->name, 0, 1) }}
                    </div>
                    <div>
                        <p style="margin: 0; font-weight: 600; color: #333;">{{ $report->reporter->name }}</p>
                        <p style="margin: 0; font-size: 13px; color: #999;">{{ $report->reporter->email }}</p>
                    </div>
                </div>

                <!-- Reported Entity -->
                @if($report->reportedUser)
                    <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 20px 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Utilisateur signalé</h3>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9f9f9; border-radius: 8px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #d0d0d0; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                            {{ substr($report->reportedUser->name, 0, 1) }}
                        </div>
                        <div>
                            <p style="margin: 0; font-weight: 600; color: #333;">{{ $report->reportedUser->name }}</p>
                            <p style="margin: 0; font-size: 13px; color: #999;">{{ $report->reportedUser->email }}</p>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 20px 0 12px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Description</h3>
                <div style="background-color: #f9f9f9; padding: 16px; border-radius: 8px; color: #666; line-height: 1.6; white-space: pre-wrap;">
                    {{ $report->description }}
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Statut</h3>
                @if($report->status === 'pending')
                    <span style="display: inline-block; background-color: #fff3cd; color: #856404; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">⏳ En attente</span>
                @elseif($report->status === 'investigating')
                    <span style="display: inline-block; background-color: #d1ecf1; color: #0c5460; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">🔍 Investigation</span>
                @elseif($report->status === 'resolved')
                    <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">✓ Résolu</span>
                @else
                    <span style="display: inline-block; background-color: #f8d7da; color: #721c24; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">✗ Rejeté</span>
                @endif
            </div>

            <!-- Type Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Type</h3>
                @if($report->type === 'cook')
                    <p style="margin: 0; color: #666;">👨‍🍳 Cuisinier</p>
                @elseif($report->type === 'client')
                    <p style="margin: 0; color: #666;">👤 Client</p>
                @elseif($report->type === 'dish')
                    <p style="margin: 0; color: #666;">🍽️ Plat</p>
                @elseif($report->type === 'order')
                    <p style="margin: 0; color: #666;">📦 Commande</p>
                @else
                    <p style="margin: 0; color: #666;">🖥️ Plateforme</p>
                @endif
            </div>

            <!-- Priority Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Priorité</h3>
                @if(($report->priority ?? 1) >= 3)
                    <span style="display: inline-block; background-color: #f8d7da; color: #721c24; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">🔴 Haute</span>
                @else
                    <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">🟢 Normale</span>
                @endif
            </div>

            <!-- Date Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">📅 Dates</h3>
                <p style="margin: 0; color: #666; font-size: 13px;">
                    <strong>Créé:</strong> {{ $report->created_at->format('d/m/Y H:i') }}<br>
                    @if($report->updated_at)
                        <strong>Mis à jour:</strong> {{ $report->updated_at->format('d/m/Y H:i') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
</x-admin-sidebar-layout>
