@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Back Link -->
    <a href="{{ route('reports.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline mb-4 inline-block">
        ← Retour à mes signalements
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
                            Créé le {{ $report->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        {!! $report->getStatusBadge() !!}
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded-full">
                        @switch($report->type)
                            @case('cook')
                                👨‍🍳 Signalement cuisinier
                                @break
                            @case('client')
                                👤 Signalement client
                                @break
                            @case('dish')
                                🍽️ Signalement plat
                                @break
                            @case('order')
                                📦 Signalement commande
                                @break
                            @case('platform')
                                🖥️ Signalement plateforme
                                @break
                        @endswitch
                    </span>

                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-semibold rounded-full">
                        {{ $report->getCategoryLabel() }}
                    </span>

                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $report->getPriorityColor() }}40; color: {{ $report->getPriorityColor() }};">
                        {{ $report->getPriorityLabel() }}
                    </span>
                </div>
            </div>

            <!-- Report Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Détails du signalement</h2>

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

                <!-- Description -->
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description:</p>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $report->description }}
                    </div>
                </div>
            </div>

            <!-- Resolution (if resolved) -->
            @if($report->resolved_at)
                <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-green-900 dark:text-green-100 mb-4">Résolution</h2>

                    <div class="mb-4 pb-4 border-b border-green-200 dark:border-green-700">
                        <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-1">Statut:</p>
                        <p class="font-medium text-green-900 dark:text-green-100">
                            @switch($report->status)
                                @case('resolved')
                                    ✅ Résolu
                                    @break
                                @case('rejected')
                                    ❌ Rejeté
                                    @break
                                @case('action_taken')
                                    🎯 Action prise
                                    @break
                            @endswitch
                        </p>
                    </div>

                    <div class="mb-4 pb-4 border-b border-green-200 dark:border-green-700">
                        <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-1">Date de résolution:</p>
                        <p class="text-green-900 dark:text-green-100">{{ $report->resolved_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($report->admin_comment)
                        <div>
                            <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-2">Commentaire de l'administrateur:</p>
                            <div class="p-4 bg-white dark:bg-gray-800 rounded text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $report->admin_comment }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6 sticky top-4">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informations</h3>

                <div class="space-y-4 text-sm">
                    <!-- ID -->
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">ID DU SIGNALEMENT</p>
                        <p class="text-gray-900 dark:text-white font-mono">{{ $report->id }}</p>
                    </div>

                    <!-- Type -->
                    <div class="pb-4 border-b dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">TYPE</p>
                        <p class="text-gray-900 dark:text-white capitalize">{{ $report->type }}</p>
                    </div>

                    <!-- Category -->
                    <div class="pb-4 border-b dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">CATÉGORIE</p>
                        <p class="text-gray-900 dark:text-white">{{ $report->getCategoryLabel() }}</p>
                    </div>

                    <!-- Status -->
                    <div class="pb-4 border-b dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">STATUT</p>
                        <p class="text-gray-900 dark:text-white capitalize">
                            @switch($report->status)
                                @case('pending')
                                    En attente
                                    @break
                                @case('investigating')
                                    En investigation
                                    @break
                                @case('resolved')
                                    Résolu
                                    @break
                                @case('rejected')
                                    Rejeté
                                    @break
                                @case('action_taken')
                                    Action prise
                                    @break
                            @endswitch
                        </p>
                    </div>

                    <!-- Priority -->
                    <div class="pb-4 border-b dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">PRIORITÉ</p>
                        <p class="text-gray-900 dark:text-white">{{ $report->getPriorityLabel() }}</p>
                    </div>

                    <!-- Dates -->
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">CRÉÉ LE</p>
                        <p class="text-gray-900 dark:text-white">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($report->status === 'pending')
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 text-red-700 dark:text-red-200 font-medium rounded transition-colors">
                            Supprimer ce signalement
                        </button>
                    </form>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                        Vous ne pouvez supprimer que les signalements en attente
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
