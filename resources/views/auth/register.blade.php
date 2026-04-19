<x-guest-layout>
<div style="display: flex; height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Left Side - Image -->
    <div style="flex: 1; background: linear-gradient(135deg, #fff3ed 0%, #ffe8d6 100%); display: flex; align-items: center; justify-content: center; padding: 40px;">
        <img src="https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&h=800&fit=crop" alt="Cuisine" style="border-radius: 12px; object-fit: cover; height: 100%; max-height: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    </div>

    <!-- Right Side - Register Form -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background-color: white; padding: 40px;">
        <div style="width: 100%; max-width: 400px;">
            <!-- Logo -->
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🍽️</div>
                <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; font-weight: 700; color: #333; margin: 0;">PetitChef</h1>
                <p style="color: #999; margin-top: 8px;">Rejoignez notre communauté culinaire !</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" style="margin-top: 30px;">
                @csrf

                <!-- Name -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">NOM COMPLET</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;" placeholder="Votre nom">
                    @error('name')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">EMAIL</label>
                    <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;" placeholder="votre@email.com">
                    @error('email')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">MOT DE PASSE</label>
                    <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;" placeholder="••••••••">
                    @error('password')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">CONFIRMEZ MOT DE PASSE</label>
                    <input type="password" name="password_confirmation" required style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;" placeholder="••••••••">
                    @error('password_confirmation')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width: 100%; background-color: #ff6b35; color: white; border: none; padding: 12px; border-radius: 20px; font-size: 15px; font-weight: 700; cursor: pointer; margin-bottom: 16px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    S'INSCRIRE
                </button>

                <!-- Login Link -->
                <p style="text-align: center; font-size: 13px; color: #666;">
                    Déjà inscrit ? 
                    <a href="{{ route('login') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
</div>
</x-guest-layout>
