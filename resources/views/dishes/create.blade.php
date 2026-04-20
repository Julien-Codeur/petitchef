<x-cook-sidebar-layout>
<div style="padding: 0;">
    <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 20px; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
        <x-icon icon="menu" size="32" color="#333" />
        Créer un Nouveau Plat
    </h1>

    <form method="POST" action="{{ route('dishes.store') }}" enctype="multipart/form-data" style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 30px; max-width: 700px;">
        @csrf

        @if ($errors->any())
            <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px;">
                <p style="color: #d32f2f; font-weight: 600; margin-bottom: 8px;">Erreurs dans le formulaire :</p>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li style="color: #d32f2f; font-size: 13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Nom -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Nom du plat *</label>
            <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
        </div>

        <!-- Description -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Description *</label>
            <textarea name="description" rows="4" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; font-family: 'Inter', sans-serif;">{{ old('description') }}</textarea>
            <p style="font-size: 12px; color: #999; margin-top: 6px;">Ex: Coq braisé au vin rouge avec champignons et petits oignons</p>
        </div>

        <!-- Prix & Quantité -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Prix (€) *</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0.01" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Quantité disponible *</label>
                <input type="number" name="available_qty" value="{{ old('available_qty') }}" min="1" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
        </div>

        <!-- Date de service -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Date de service *</label>
            <input type="date" name="served_date" value="{{ old('served_date', today()->toDateString()) }}" min="{{ today()->toDateString() }}" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
        </div>

        <!-- Photo -->
        <div style="margin-bottom: 30px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Photo du plat</label>
            <div style="border: 2px dashed #e0e0e0; border-radius: 8px; padding: 20px; text-align: center; background-color: #f9f9f9;">
                <input type="file" name="photo" accept="image/*" id="photo-input" style="display: none;">
                <label for="photo-input" style="cursor: pointer; display: block;">
                    <div style="display: flex; justify-content: center; margin-bottom: 8px;">
                        <x-icon icon="search" size="32" color="#ccc" />
                    </div>
                    <p style="margin: 0; color: #333; font-weight: 600;">Cliquez ou déposez une image</p>
                    <p style="margin: 4px 0 0 0; color: #999; font-size: 12px;">JPG, PNG, GIF - Max 5MB</p>
                </label>
                <div id="preview" style="margin-top: 12px;"></div>
            </div>
        </div>

        <!-- Boutons --> display: flex; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                <x-icon icon="check" size="16" color="white" />
                Créer le plat
            </button>
            <a href="{{ route('dishes.index') }}" style="flex: 1; background-color: #f5f5f5; color: #333; border: 1px solid #e0e0e0; padding: 12px; border-radius: 20px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <x-icon icon="close" size="16" color="#333" />
               ton>
            <a href="{{ route('dishes.index') }}" style="flex: 1; background-color: #f5f5f5; color: #333; border: 1px solid #e0e0e0; padding: 12px; border-radius: 20px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">
                ✕ Annuler
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('preview').innerHTML = '<img src="' + event.target.result + '" style="max-width: 200px; border-radius: 8px;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>
</x-cook-sidebar-layout>
