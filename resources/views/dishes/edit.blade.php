<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier: ' . $dish->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dishes.update', $dish) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom du plat:</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $dish->name) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                        <textarea name="description" id="description" rows="4" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">{{ old('description', $dish->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix (€):</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $dish->price) }}" step="0.01" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                        </div>

                        <div>
                            <label for="available_qty" class="block text-gray-700 text-sm font-bold mb-2">Quantité disponible:</label>
                            <input type="number" name="available_qty" id="available_qty" value="{{ old('available_qty', $dish->available_qty) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="served_date" class="block text-gray-700 text-sm font-bold mb-2">Date de service:</label>
                            <input type="date" name="served_date" id="served_date" value="{{ old('served_date', $dish->served_date->toDateString()) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                        </div>

                        <div>
                            <label for="is_active" class="flex items-center text-gray-700 text-sm font-bold">
                                <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', $dish->is_active) ? 'checked' : '' }} class="mr-2">
                                Actif
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="photo" class="block text-gray-700 text-sm font-bold mb-2">Photo:</label>
                        @if($dish->photo_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" class="w-32 h-32 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mettre à jour
                        </button>
                        <a href="{{ route('dishes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
