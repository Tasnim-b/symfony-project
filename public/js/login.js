// assets/js/login.js

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const closeBtn = document.getElementById('closeBtn');
    const forgotPasswordLink = document.getElementById('forgotPassword');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const closeForgotModal = document.getElementById('closeForgotModal');
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');

    // Toggle password visibility
    const togglePassword = (toggleBtnId, inputId) => {
        const toggleBtn = document.getElementById(toggleBtnId);
        const passwordInput = document.getElementById(inputId);

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    };

    // Initialiser les toggles de mot de passe
    togglePassword('toggleLoginPassword', 'loginPassword');
    togglePassword('toggleRegisterPassword', 'registerPassword');
    togglePassword('toggleConfirmPassword', 'confirmPassword');

    // Changer entre les onglets
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');

            // Mettre à jour les onglets actifs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Afficher le formulaire correspondant
            document.querySelectorAll('.login-form, .register-form').forEach(form => {
                form.classList.remove('active');
            });

            if (tab === 'login') {
                document.getElementById('loginForm').classList.add('active');
            } else {
                document.getElementById('registerForm').classList.add('active');
            }
        });
    });

    // Fermer le popup
    closeBtn.addEventListener('click', function() {
        // Rediriger vers la page d'accueil ou fermer la fenêtre
        window.location.href = '/';
    });

    // Ouvrir le modal de mot de passe oublié
    forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        forgotPasswordModal.style.display = 'block';
    });

    // Fermer le modal de mot de passe oublié
    closeForgotModal.addEventListener('click', function() {
        forgotPasswordModal.style.display = 'none';
    });

    // Réinitialiser le mot de passe
    resetPasswordBtn.addEventListener('click', function() {
        const email = document.getElementById('resetEmail').value;

        if (!email || !validateEmail(email)) {
            showNotification('Veuillez entrer une adresse email valide', 'error');
            return;
        }

        // Simuler l'envoi d'email
        showNotification('Lien de réinitialisation envoyé à ' + email);
        forgotPasswordModal.style.display = 'none';
        document.getElementById('resetEmail').value = '';
    });

    // Validation de l'email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Vérification de la force du mot de passe
    const registerPassword = document.getElementById('registerPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');

    if (registerPassword) {
        registerPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = 'Très faible';
            let color = '#ff5252';

            // Vérifications
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;

            // Mettre à jour la barre et le texte
            passwordStrength.style.width = strength + '%';

            if (strength <= 25) {
                text = 'Très faible';
                color = '#ff5252';
            } else if (strength <= 50) {
                text = 'Faible';
                color = '#ff9800';
            } else if (strength <= 75) {
                text = 'Moyen';
                color = '#ffeb3b';
            } else {
                text = 'Fort';
                color = '#4caf50';
            }

            passwordStrength.style.backgroundColor = color;
            strengthText.textContent = 'Force du mot de passe: ' + text;
            strengthText.style.color = color;

            // Vérifier la correspondance des mots de passe
            if (confirmPassword.value && password !== confirmPassword.value) {
                confirmPassword.style.borderColor = '#ff5252';
                confirmPassword.style.backgroundColor = 'rgba(255, 82, 82, 0.05)';
            } else if (confirmPassword.value) {
                confirmPassword.style.borderColor = '#4caf50';
                confirmPassword.style.backgroundColor = 'rgba(76, 175, 80, 0.05)';
            }
        });
    }

    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            const password = registerPassword.value;
            const confirm = this.value;

            if (confirm && password !== confirm) {
                this.style.borderColor = '#ff5252';
                this.style.backgroundColor = 'rgba(255, 82, 82, 0.05)';
            } else if (confirm) {
                this.style.borderColor = '#4caf50';
                this.style.backgroundColor = 'rgba(76, 175, 80, 0.05)';
            } else {
                this.style.borderColor = '#e0e0e0';
                this.style.backgroundColor = '#f8f9fa';
            }
        });
    }

    // Soumission du formulaire de connexion
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        if (!validateEmail(email)) {
            showNotification('Veuillez entrer une adresse email valide', 'error');
            return;
        }

        if (password.length < 6) {
            showNotification('Le mot de passe doit contenir au moins 6 caractères', 'error');
            return;
        }

        // Simuler la connexion
        showNotification('Connexion en cours...', 'info');

        setTimeout(() => {
            showNotification('Connexion réussie! Redirection...');
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
        }, 1000);
    });

    // Soumission du formulaire d'inscription
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        const acceptTerms = document.getElementById('acceptTerms').checked;

        // Validation
        if (!name.trim()) {
            showNotification('Veuillez entrer votre nom complet', 'error');
            return;
        }

        if (!validateEmail(email)) {
            showNotification('Veuillez entrer une adresse email valide', 'error');
            return;
        }

        if (password.length < 8) {
            showNotification('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return;
        }

        if (password !== confirm) {
            showNotification('Les mots de passe ne correspondent pas', 'error');
            return;
        }

        if (!acceptTerms) {
            showNotification('Veuillez accepter les conditions d\'utilisation', 'error');
            return;
        }

        // Simuler l'inscription
        showNotification('Création de votre compte...', 'info');

        setTimeout(() => {
            showNotification('Compte créé avec succès! Vous allez être redirigé.');
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
        }, 1000);
    });

    // Afficher une notification
    function showNotification(message, type = 'success') {
        notificationMessage.textContent = message;

        // Changer la couleur selon le type
        if (type === 'error') {
            notification.style.background = 'linear-gradient(135deg, #ff5252 0%, #c62828 100%)';
            notification.querySelector('i').className = 'fas fa-exclamation-circle';
        } else if (type === 'info') {
            notification.style.background = 'linear-gradient(135deg, #2196f3 0%, #0d47a1 100%)';
            notification.querySelector('i').className = 'fas fa-info-circle';
        } else {
            notification.style.background = 'linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%)';
            notification.querySelector('i').className = 'fas fa-check-circle';
        }

        notification.style.display = 'flex';

        // Masquer après 3 secondes
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    // Fermer en cliquant en dehors du modal
    window.addEventListener('click', function(e) {
        if (e.target === forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
        }
    });

    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            forgotPasswordModal.style.display = 'none';
        }
    });
});
