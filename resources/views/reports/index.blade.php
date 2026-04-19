@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mes signalements</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Suivez l'état de vos signalements</p>
        </div>
        <a 
            href="{{ route('reports.create', ['type' => 'cook']) }}" 
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow-sm transition-colors"
        >
            + Nouveau signalement
        </a>
    </div>

    <!-- Alerts -->
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
            <p class="text-red-800 dark:text-red-200 font-medium">Erreur:</p>
            <ul class="text-red-700 dark:text-red-300 text-sm mt-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
            <p class="text-green-800 dark:text-green-200 font-medium">✓ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Reports List -->
    @if($reports->count() > 0)
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <!-- Left Content -->
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-center gap-3 mb-3">
                                <!-- Type Badge -->
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

                                <!-- Status Badge -->
                                {!! $report->getStatusBadge() !!}

                                <!-- Priority Badge -->
                                <span class="px-2 py-1 text-xs font-semibold rounded" style="background-color: {{ $report->getPriorityColor() }}40; color: {{ $report->getPriorityColor() }};">
                                    {{ $report->getPriorityLabel() }}
                                </span>
                            </div>

                            <!-- Category -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <strong>Catégorie:</strong> {{ $report->getCategoryLabel() }}
                            </p>

                            <!-- Description Preview -->
                            <p class="text-gray-700 dark:text-gray-300 mb-3 line-clamp-2">
                                {{ $report->description }}
                            </p>

                            <!-- Meta Info -->
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>📅 {{ $report->created_at->format('d/m/Y H:i') }}</span>
                                @if($report->resolved_at)
                                    <span>✓ Résolu le {{ $report->resolved_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="ml-4 flex items-center gap-2">
                            <a 
                                href="{{ route('reports.show', $report) }}"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors"
                            >
                                Voir
                            </a>
                            @if($report->status === 'pending')
                                <form 
                                    action="{{ route('reports.destroy', $report) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Êtes-vous sûr?')"
                                    class="inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="px-3 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded transition-colors"
                                    >
                                        Supprimer
                                    </button>
                                </form>
                            @endif
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun signalement</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Vous n'avez pas encore créé de signalement
            </p>
            <a 
                href="{{ route('reports.create', ['type' => 'cook']) }}"
                class="inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow-sm transition-colors"
            >
                Créer votre premier signalement
            </a>
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
