<x-guest-layout>
<div style="display: flex; height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Left Side - Image -->
    <div style="flex: 1; background: linear-gradient(135deg, #fff3ed 0%, #ffe8d6 100%); display: flex; align-items: center; justify-content: center; padding: 40px;">
        <img src="https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&h=800&fit=crop" alt="Cuisine" style="border-radius: 12px; object-fit: cover; height: 100%; max-height: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    </div>

    <!-- Right Side - Login Form -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background-color: white; padding: 40px;">
        <div style="width: 100%; max-width: 400px;">
            <!-- Logo -->
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🍽️</div>
                <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; font-weight: 700; color: #333; margin: 0;">PetitChef</h1>
                <p style="color: #999; margin-top: 8px;">Commandez vos faits maisons en toute sécurité !</p>
            </div>

            <!-- Role Selection -->
            <div style="margin-bottom: 30px;">
                <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; text-align: center; color: #666; margin-bottom: 16px; font-size: 14px;">CHOISISSEZ VOTRE RÔLE</p>
                <div style="display: flex; gap: 12px; margin-bottom: 20px;">
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="role" value="client" checked style="display: none;">
                        <div class="role-btn" style="border: 2px solid #ff6b35; border-radius: 12px; padding: 12px; text-align: center; transition: all 0.3s;">
                            <div style="font-size: 24px;">🛒</div>
                            <div style="font-size: 13px; font-weight: 600; color: #ff6b35; margin-top: 8px;">CLIENT</div>
                        </div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="role" value="chef" style="display: none;">
                        <div class="role-btn" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 12px; text-align: center; transition: all 0.3s;">
                            <div style="font-size: 24px;">👨‍🍳</div>
                            <div style="font-size: 13px; font-weight: 600; color: #666; margin-top: 8px;">CUISINIER</div>
                        </div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="role" value="admin" style="display: none;">
                        <div class="role-btn" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 12px; text-align: center; transition: all 0.3s;">
                            <div style="font-size: 24px;">🛡️</div>
                            <div style="font-size: 13px; font-weight: 600; color: #666; margin-top: 8px;">ADMINISTRATEUR</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" style="margin-top: 30px;">
                @csrf

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

                <!-- Remember & Forgot Password -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 13px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <input type="checkbox" name="remember" style="cursor: pointer;">
                        <span>Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">Mot de passe oublié ?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width: 100%; background-color: #ff6b35; color: white; border: none; padding: 12px; border-radius: 20px; font-size: 15px; font-weight: 700; cursor: pointer; margin-bottom: 16px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    SE CONNECTER
                </button>

                <!-- Register Link -->
                <p style="text-align: center; font-size: 13px; color: #666;">
                    Pas encore inscrit ? 
                    <a href="{{ route('register') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    // Role selection interactivity
    document.querySelectorAll('input[name="role"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.style.borderColor = '#e0e0e0';
                btn.style.backgroundColor = 'white';
                const label = btn.querySelector('div:last-child');
                label.style.color = '#666';
            });
            
            const roleBtn = this.parentElement.querySelector('.role-btn');
            roleBtn.style.borderColor = '#ff6b35';
            roleBtn.style.backgroundColor = '#fff3ed';
            const label = roleBtn.querySelector('div:last-child');
            label.style.color = '#ff6b35';
        });
    });
</script>
</x-guest-layout>
