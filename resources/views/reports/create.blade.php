@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Créer un signalement</h1>
            <p class="text-gray-600 dark:text-gray-400">Aidez-nous à maintenir une communauté sûre et respectueuse</p>
        </div>

        <!-- Form -->
        <form action="{{ route('reports.store') }}" method="POST" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            @csrf

            <!-- Report Type (Hidden) -->
            <input type="hidden" name="type" value="{{ $type }}">
            @if($userId)
                <input type="hidden" name="reported_user_id" value="{{ $userId }}">
            @endif
            @if($dishId)
                <input type="hidden" name="reported_dish_id" value="{{ $dishId }}">
            @endif
            @if($orderId)
                <input type="hidden" name="reported_order_id" value="{{ $orderId }}">
            @endif

            <!-- Type Display -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Type de signalement:</p>
                <p class="text-lg font-semibold text-blue-600 dark:text-blue-300 mt-1">
                    @switch($type)
                        @case('cook')
                            Signaler un cuisinier
                            @break
                        @case('client')
                            Signaler un client
                            @break
                        @case('dish')
                            Signaler un plat
                            @break
                        @case('order')
                            Signaler une commande
                            @break
                        @case('platform')
                            Signaler un problème avec la plateforme
                            @break
                    @endswitch
                </p>
            </div>

            <!-- Reported Entity (if applicable) -->
            @if($userId)
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Utilisateur signalé:</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        {{ \App\Models\User::find($userId)?->name ?? 'Inconnu' }}
                    </p>
                </div>
            @endif

            <!-- Category -->
            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Catégorie <span class="text-red-500">*</span>
                </label>
                <select name="category" id="category" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror">
                    <option value="">-- Sélectionnez une catégorie --</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description détaillée <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="8"
                    required
                    minlength="10"
                    maxlength="1000"
                    placeholder="Décrivez en détail le problème que vous avez rencontré..."
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span id="char-count">0</span>/1000 caractères
                </p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tips -->
            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg border border-yellow-200 dark:border-yellow-700">
                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-2">💡 Conseils pour un signalement efficace:</h3>
                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li>• Soyez aussi précis et détaillé que possible</li>
                    <li>• Incluez les dates et heures si pertinent</li>
                    <li>• Décrivez clairement le comportement ou le problème</li>
                    <li>• Les signalements seront examinés par notre équipe</li>
                </ul>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    ← Retour
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Annuler
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow-sm transition-colors"
                    >
                        Soumettre le signalement
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Character counter
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', () => {
        charCount.textContent = textarea.value.length;
    });
    
    // Initialize counter
    charCount.textContent = textarea.value.length;
</script>
@endsection
