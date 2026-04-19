@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Back Link -->
    <a href="{{ route('admin.reports.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline mb-4 inline-block">
        ← Retour à la gestion des signalements
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            Signalement #{{ $report->id }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Créé par {{ $report->reporter->name }} le {{ $report->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded-full">
                        @switch($report->type)
                            @case('cook')
                                👨‍🍳 Cuisinier
                                @break
                            @case('client')
                                👤 Client
                                @break
                            @case('dish')
                                🍽️ Plat
                                @break
                            @case('order')
                                📦 Commande
                                @break
                            @case('platform')
                                🖥️ Plateforme
                                @break
                        @endswitch
                    </span>

                    {!! $report->getStatusBadge() !!}

                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $report->getPriorityColor() }}40; color: {{ $report->getPriorityColor() }};">
                        {{ $report->getPriorityLabel() }}
                    </span>
                </div>
            </div>

            <!-- Report Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Détails du signalement</h2>

                <!-- Reporter -->
                <div class="mb-6 pb-6 border-b dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signalé par:</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-300 font-semibold">
                            {{ substr($report->reporter->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $report->reporter->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $report->reporter->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reported Entity -->
                @if($report->reportedUser)
                    <div class="mb-6 pb-6 border-b dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Utilisateur signalé:</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-300 font-semibold">
                                {{ substr($report->reportedUser->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $report->reportedUser->name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $report->reportedUser->email }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($report->reportedDish)
                    <div class="mb-6 pb-6 border-b dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Plat signalé:</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $report->reportedDish->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $report->reportedDish->description }}</p>
                    </div>
                @endif

                @if($report->reportedOrder)
                    <div class="mb-6 pb-6 border-b dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Commande signalée:</p>
                        <p class="font-medium text-gray-900 dark:text-white">Commande #{{ $report->reportedOrder->id }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Statut: {{ $report->reportedOrder->status }}</p>
                    </div>
                @endif

                <!-- Category -->
                <div class="mb-6 pb-6 border-b dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Catégorie:</p>
                    <p class="text-gray-900 dark:text-white mt-1">{{ $report->getCategoryLabel() }}</p>
                </div>

                <!-- Description -->
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description:</p>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $report->description }}
                    </div>
                </div>
            </div>

            <!-- Resolution Form -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Gérer le signalement</h2>

                @if($report->resolved_at)
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                        <p class="text-green-800 dark:text-green-200 font-medium">✓ Ce signalement a déjà été résolu le {{ $report->resolved_at->format('d/m/Y à H:i') }}</p>
                        @if($report->admin_comment)
                            <p class="text-green-700 dark:text-green-300 text-sm mt-2">{{ $report->admin_comment }}</p>
                        @endif
                    </div>
                @else
                    <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Sélectionnez un statut --</option>
                                <option value="pending" @selected($report->status === 'pending')>⏳ En attente</option>
                                <option value="investigating" @selected($report->status === 'investigating')>🔍 En investigation</option>
                                <option value="resolved" @selected($report->status === 'resolved')>✅ Résolu</option>
                                <option value="rejected" @selected($report->status === 'rejected')>❌ Rejeté</option>
                                <option value="action_taken" @selected($report->status === 'action_taken')>🎯 Action prise</option>
                            </select>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="admin_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Commentaire <span class="text-gray-500">(optionnel)</span>
                            </label>
                            <textarea 
                                name="admin_comment" 
                                id="admin_comment" 
                                rows="4"
                                maxlength="1000"
                                placeholder="Votre commentaire ou résolution..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >{{ $report->admin_comment }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span id="char-count">0</span>/1000 caractères
                            </p>
                        </div>

                        <button 
                            type="submit" 
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition-colors"
                        >
                            Mettre à jour le signalement
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6 sticky top-4">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Actions rapides</h3>

                <div class="space-y-2">
                    <!-- Mark as Investigating -->
                    @if($report->status !== 'investigating')
                        <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="investigating">
                            <button type="submit" class="w-full px-4 py-2 bg-orange-100 dark:bg-orange-900 hover:bg-orange-200 dark:hover:bg-orange-800 text-orange-700 dark:text-orange-200 font-medium rounded transition-colors text-sm">
                                🔍 En investigation
                            </button>
                        </form>
                    @endif

                    <!-- Mark as Resolved -->
                    @if($report->status !== 'resolved')
                        <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="resolved">
                            <button type="submit" class="w-full px-4 py-2 bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 text-green-700 dark:text-green-200 font-medium rounded transition-colors text-sm">
                                ✅ Résoudre
                            </button>
                        </form>
                    @endif

                    <!-- Mark as Rejected -->
                    @if($report->status !== 'rejected')
                        <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 text-red-700 dark:text-red-200 font-medium rounded transition-colors text-sm">
                                ❌ Rejeter
                            </button>
                        </form>
                    @endif

                    <!-- Mark Action Taken -->
                    @if($report->status !== 'action_taken')
                        <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="action_taken">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-200 font-medium rounded transition-colors text-sm">
                                🎯 Action prise
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Priority Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Priorité</h3>

                <form action="{{ route('admin.reports.priority', $report) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center">
                        <input type="radio" name="priority" id="priority-1" value="1" @checked($report->priority == 1) class="w-4 h-4">
                        <label for="priority-1" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">🟢 Basse</label>
                    </div>

                    <div class="flex items-center">
                        <input type="radio" name="priority" id="priority-2" value="2" @checked($report->priority == 2) class="w-4 h-4">
                        <label for="priority-2" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">🟡 Moyenne</label>
                    </div>

                    <div class="flex items-center">
                        <input type="radio" name="priority" id="priority-3" value="3" @checked($report->priority == 3) class="w-4 h-4">
                        <label for="priority-3" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">🔴 Haute</label>
                    </div>

                    <div class="flex items-center">
                        <input type="radio" name="priority" id="priority-4" value="4" @checked($report->priority == 4) class="w-4 h-4">
                        <label for="priority-4" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">🔴 Critique</label>
                    </div>

                    <button type="submit" class="w-full mt-4 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded text-sm transition-colors">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Info -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informations</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">ID</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $report->id }}</p>
                    </div>

                    <div class="pb-3 border-b dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">CRÉÉ LE</p>
                        <p class="text-gray-900 dark:text-white">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($report->resolved_at)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">RÉSOLU LE</p>
                            <p class="text-gray-900 dark:text-white">{{ $report->resolved_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Character counter
    const textarea = document.getElementById('admin_comment');
    const charCount = document.getElementById('char-count');
    
    if (textarea) {
        textarea.addEventListener('input', () => {
            charCount.textContent = textarea.value.length;
        });
        
        // Initialize counter
        charCount.textContent = textarea.value.length;
    }
</script>
@endsection
