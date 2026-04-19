@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Gestion des signalements</h1>
        <p class="text-gray-600 dark:text-gray-400">Examinez et résolvez les signalements des utilisateurs</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">En attente</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">En investigation</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['investigating'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Haute priorité</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['high_priority'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Non résolus</p>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2">{{ $stats['total_unresolved'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Statut
                </label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous</option>
                    <option value="pending" @selected(request('status') === 'pending')>En attente</option>
                    <option value="investigating" @selected(request('status') === 'investigating')>En investigation</option>
                    <option value="resolved" @selected(request('status') === 'resolved')>Résolu</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejeté</option>
                    <option value="action_taken" @selected(request('status') === 'action_taken')>Action prise</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Priorité
                </label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Toutes</option>
                    <option value="1" @selected(request('priority') === '1')>Basse</option>
                    <option value="2" @selected(request('priority') === '2')>Moyenne</option>
                    <option value="3" @selected(request('priority') === '3')>Haute</option>
                    <option value="4" @selected(request('priority') === '4')>Critique</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Type
                </label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous</option>
                    <option value="cook" @selected(request('type') === 'cook')>Cuisinier</option>
                    <option value="client" @selected(request('type') === 'client')>Client</option>
                    <option value="dish" @selected(request('type') === 'dish')>Plat</option>
                    <option value="order" @selected(request('type') === 'order')>Commande</option>
                    <option value="platform" @selected(request('type') === 'platform')>Plateforme</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Recherche
                </label>
                <input 
                    type="text" 
                    name="search" 
                    id="search" 
                    value="{{ request('search') }}"
                    placeholder="Rechercher..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <!-- Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    @if($reports->count() > 0)
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <!-- Left Content -->
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-center gap-3 mb-3 flex-wrap">
                                <!-- Priority Indicator -->
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $report->getPriorityColor() }};"></div>

                                <!-- ID -->
                                <span class="text-sm font-mono text-gray-600 dark:text-gray-400">#{{ $report->id }}</span>

                                <!-- Type Badge -->
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded">
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

                                <!-- Status Badge -->
                                {!! $report->getStatusBadge() !!}
                            </div>

                            <!-- Category and Reported Entity -->
                            <div class="mb-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>{{ $report->getCategoryLabel() }}</strong>
                                    @if($report->reportedUser)
                                        • Signalé par <strong>{{ $report->reporter->name }}</strong>
                                        contre <strong>{{ $report->reportedUser->name }}</strong>
                                    @endif
                                </p>
                            </div>

                            <!-- Description Preview -->
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-3 line-clamp-1">
                                {{ $report->description }}
                            </p>

                            <!-- Meta Info -->
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>📅 {{ $report->created_at->format('d/m/Y H:i') }}</span>
                                <span class="inline-block px-2 py-1 rounded" style="background-color: {{ $report->getPriorityColor() }}20; color: {{ $report->getPriorityColor() }};">
                                    {{ $report->getPriorityLabel() }}
                                </span>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="ml-4 flex items-center gap-2">
                            <a 
                                href="{{ route('admin.reports.show', $report) }}"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors"
                            >
                                Examiner
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
            <div class="text-gray-400 dark:text-gray-600 text-4xl mb-4">📋</div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun signalement trouvé</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Pas de signalements correspondant à vos filtres
            </p>
        </div>
    @endif
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
