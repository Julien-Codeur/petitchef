<x-profile-layout>
@php
    $user = auth()->user();
@endphp

<div style="padding: 30px;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="profile" size="32" color="#333" />
            Mon Profil
        </h1>
        <p style="color: #666; margin: 0;">Gérez vos informations personnelles et vos paramètres de sécurité</p>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session('status') === 'profile-updated')
        <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #4caf50; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="check" size="20" color="#4caf50" />
            <p style="margin: 0;">Votre profil a été mis à jour avec succès.</p>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #4caf50; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="check" size="20" color="#4caf50" />
            <p style="margin: 0;">Votre mot de passe a été modifié avec succès.</p>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Section Informations Profil -->
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px;">
            <h2 style="font-size: 18px; font-weight: 700; color: #333; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
                <x-icon icon="profile" size="20" color="#ff6b35" />
                Informations Personnelles
            </h2>
            <p style="font-size: 13px; color: #999; margin: 0 0 20px 0;">Mettez à jour vos informations de compte et votre adresse email.</p>

            <form method="post" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('patch')

                <!-- Nom -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    @error('name')
                        <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    @error('email')
                        <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div style="background-color: #fff3e0; border-left: 4px solid #ff9800; padding: 12px; border-radius: 4px; margin-top: 12px;">
                            <p style="margin: 0; font-size: 12px; color: #e65100; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                <x-icon icon="alert" size="14" color="#ff9800" />
                                Votre adresse email n'est pas vérifiée
                            </p>
                            <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: #ff6b35; text-decoration: underline; cursor: pointer; padding: 0; font-size: 12px; font-weight: 600; margin-top: 8px;">
                                    Cliquez ici pour renvoyer le lien de vérification
                                </button>
                            </form>
                            @if (session('status') === 'verification-link-sent')
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #4caf50; font-weight: 600;">Un nouveau lien de vérification a été envoyé à votre adresse email.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Téléphone (optionnel) -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    @error('phone')
                        <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bouton Enregistrer -->
                <button type="submit" style="background-color: #ff6b35; color: white; padding: 12px; border: none; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    <x-icon icon="check" size="16" color="white" />
                    Enregistrer les modifications
                </button>
            </form>
        </div>

        <!-- Colonne droite: Sécurité -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Section Mot de passe -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #333; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
                    <x-icon icon="settings" size="20" color="#ff6b35" />
                    Modifier le Mot de Passe
                </h2>
                <p style="font-size: 13px; color: #999; margin: 0 0 20px 0;">Assurez-vous d'utiliser un mot de passe long et aléatoire pour rester en sécurité.</p>

                <form method="post" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    @method('put')

                    <!-- Mot de passe actuel -->
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Mot de passe actuel</label>
                        <input type="password" name="current_password" autocomplete="current-password" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        @error('current_password', 'updatePassword')
                            <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nouveau mot de passe -->
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Nouveau mot de passe</label>
                        <input type="password" name="password" autocomplete="new-password" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        @error('password', 'updatePassword')
                            <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        @error('password_confirmation', 'updatePassword')
                            <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bouton Mettre à jour -->
                    <button type="submit" style="background-color: #2196f3; color: white; padding: 12px; border: none; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.backgroundColor='#1976d2'" onmouseout="this.style.backgroundColor='#2196f3'">
                        <x-icon icon="check" size="16" color="white" />
                        Mettre à jour le mot de passe
                    </button>
                </form>
            </div>

            <!-- Section Supprimer le compte -->
            <div style="background-color: #ffebee; border-radius: 8px; border: 1px solid #ef9a9a; padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #d32f2f; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
                    <x-icon icon="trash" size="20" color="#d32f2f" />
                    Zone de Danger
                </h2>
                <p style="font-size: 13px; color: #c62828; margin: 0 0 16px 0;">Une fois votre compte supprimé, il ne peut pas être récupéré.</p>

                <button type="button" onclick="openDeleteModal()" style="background-color: #d32f2f; color: white; padding: 12px 20px; border: none; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'">
                    <x-icon icon="trash" size="16" color="white" />
                    Supprimer mon compte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Suppression du compte -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 8px; padding: 30px; max-width: 400px; width: 90%;">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #d32f2f; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 8px;">
            <x-icon icon="alert" size="20" color="#d32f2f" />
            Confirmer la suppression
        </h3>
        <p style="margin: 0 0 20px 0; font-size: 14px; color: #666;">Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>

        <form method="post" action="{{ route('profile.destroy') }}" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('delete')

            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">Confirmez avec votre mot de passe:</label>
                <input type="password" name="password" placeholder="Votre mot de passe" style="width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                @error('password', 'userDeletion')
                    <p style="color: #d32f2f; font-size: 12px; margin: 4px 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeDeleteModal()" style="flex: 1; background-color: #f5f5f5; color: #333; border: 1px solid #e0e0e0; padding: 12px; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <x-icon icon="close" size="16" color="#333" />
                    Annuler
                </button>
                <button type="submit" style="flex: 1; background-color: #d32f2f; color: white; border: none; padding: 12px; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'">
                    <x-icon icon="trash" size="16" color="white" />
                    Supprimer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
</x-profile-layout>
