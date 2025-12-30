document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM pour le profil
    const editProfileBtn = document.getElementById('editProfileBtn');
    const profileForm = document.getElementById('profileForm');
    const profileInputs = profileForm.querySelectorAll('input');
    const formActions = document.getElementById('formActions');
    const cancelBtn = document.getElementById('cancelBtn');
    const avatarContainer = document.querySelector('.avatar-container');
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.getElementById('profileAvatar');
    const displayFullName = document.getElementById('displayFullName');

    // Éléments du DOM pour le mot de passe
    const passwordForm = document.getElementById('passwordForm');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('passwordMatch');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');

    // 1. GESTION DU PROFIL
    // Activer l'édition du profil
    editProfileBtn.addEventListener('click', function() {
        profileInputs.forEach(input => {
            input.disabled = false;
            input.style.backgroundColor = '#fff';
            input.style.borderColor = '#4CAF50';
        });
        formActions.style.display = 'flex';
        editProfileBtn.style.display = 'none';
    });

    // Annuler l'édition
    cancelBtn.addEventListener('click', function() {
        profileInputs.forEach(input => {
            input.disabled = true;
            input.style.backgroundColor = '';
            input.style.borderColor = '';
        });
        formActions.style.display = 'none';
        editProfileBtn.style.display = 'block';
    });

    // Soumettre le formulaire de profil
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Récupérer les valeurs
        const fullName = document.getElementById('fullName').value;
        const email = document.getElementById('email').value;

        // Validation basique
        if (!fullName.trim()) {
            showMessage('Veuillez entrer un nom complet', 'error');
            return;
        }

        if (!email.trim() || !isValidEmail(email)) {
            showMessage('Veuillez entrer une adresse email valide', 'error');
            return;
        }

        // Simuler l'envoi au serveur
        setTimeout(() => {
            // Mettre à jour l'affichage du nom
            displayFullName.textContent = fullName;

            // Désactiver les champs
            profileInputs.forEach(input => {
                input.disabled = true;
                input.style.backgroundColor = '';
                input.style.borderColor = '';
            });
            formActions.style.display = 'none';
            editProfileBtn.style.display = 'block';

            showMessage('Profil mis à jour avec succès!', 'success');
        }, 500);
    });

    // 2. GESTION DE LA PHOTO DE PROFIL
    avatarContainer.addEventListener('click', function() {
        avatarInput.click();
    });

    avatarInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];

            // Validation de la taille (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showMessage('La taille de l\'image ne doit pas dépasser 2MB', 'error');
                return;
            }

            // Validation du type
            if (!file.type.match('image.*')) {
                showMessage('Veuillez sélectionner une image valide', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                profileAvatar.src = event.target.result;
                showMessage('Photo de profil mise à jour', 'success');
            };
            reader.readAsDataURL(file);
        }
    });

    // Effet hover sur l'avatar
    avatarContainer.addEventListener('mouseenter', function() {
        this.querySelector('.avatar-overlay').style.opacity = '1';
    });

    avatarContainer.addEventListener('mouseleave', function() {
        this.querySelector('.avatar-overlay').style.opacity = '0';
    });

    // 3. GESTION DU MOT DE PASSE
    // Toggle la visibilité du mot de passe
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Vérifier la force du mot de passe
    newPasswordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });

    // Vérifier la correspondance des mots de passe
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        // Mettre à jour les indicateurs visuels
        Object.keys(requirements).forEach((key) => {
            const element = document.getElementById(`req${key.charAt(0).toUpperCase() + key.slice(1)}`);
            if (requirements[key]) {
                element.classList.add('valid');
                element.style.color = '#4CAF50';
                strength++;
            } else {
                element.classList.remove('valid');
                element.style.color = '#757575';
            }
        });

        // Mettre à jour la barre de force
        const width = (strength / 5) * 100;
        strengthBar.style.width = `${width}%`;

        // Changer la couleur selon la force
        if (strength <= 1) {
            strengthBar.style.backgroundColor = '#f44336';
            strengthText.textContent = 'Faible';
        } else if (strength <= 3) {
            strengthBar.style.backgroundColor = '#FF9800';
            strengthText.textContent = 'Moyen';
        } else {
            strengthBar.style.backgroundColor = '#4CAF50';
            strengthText.textContent = 'Fort';
        }
    }

    function checkPasswordMatch() {
        if (newPasswordInput.value && confirmPasswordInput.value) {
            if (newPasswordInput.value === confirmPasswordInput.value) {
                passwordMatch.innerHTML = '<i class="fas fa-check-circle" style="color:#4CAF50"></i> Les mots de passe correspondent';
                return true;
            } else {
                passwordMatch.innerHTML = '<i class="fas fa-times-circle" style="color:#f44336"></i> Les mots de passe ne correspondent pas';
                return false;
            }
        } else {
            passwordMatch.innerHTML = '';
            return false;
        }
    }

    // Soumettre le formulaire de mot de passe
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Validation
        if (!currentPassword) {
            showMessage('Veuillez entrer votre mot de passe actuel', 'error', passwordForm);
            return;
        }

        if (!checkPasswordMatch()) {
            showMessage('Les mots de passe ne correspondent pas', 'error', passwordForm);
            return;
        }

        if (newPassword.length < 8) {
            showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error', passwordForm);
            return;
        }

        // Simuler l'envoi au serveur
        setTimeout(() => {
            showMessage('Mot de passe changé avec succès!', 'success', passwordForm);
            passwordForm.reset();
            strengthBar.style.width = '0%';
            strengthText.textContent = 'Force du mot de passe';
            passwordMatch.innerHTML = '';

            // Réinitialiser les indicateurs
            ['Length', 'Upper', 'Lower', 'Number', 'Special'].forEach(id => {
                const element = document.getElementById(`req${id}`);
                element.classList.remove('valid');
                element.style.color = '#757575';
            });
        }, 500);
    });

    // 4. FONCTIONS UTILITAIRES
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showMessage(text, type, parent = document.body) {
        // Supprimer les messages existants
        const existingMessages = parent.querySelectorAll('.message');
        existingMessages.forEach(msg => msg.remove());

        // Créer le nouveau message
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = text;

        // Ajouter le message au début du parent
        parent.insertBefore(messageDiv, parent.firstChild);

        // Supprimer le message après 5 secondes
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
});
