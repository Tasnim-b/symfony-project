document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile JS chargé');

    // Gestion du menu dropdown utilisateur
    const userMenuTrigger = document.getElementById('userMenuTrigger');
    const userMenu = document.getElementById('userMenu');

    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });

        // Fermer le menu quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target) && !userMenuTrigger.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });
    }

    // Gestion de l'édition du profil
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const profileForm = document.getElementById('profileForm');
    const formActions = document.getElementById('formActions');
    const avatarOverlay = document.getElementById('avatarOverlay');
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.getElementById('profileAvatar');
    const displayFullName = document.getElementById('displayFullName');

    if (editProfileBtn && profileForm) {
        editProfileBtn.addEventListener('click', function() {
            // Activer les champs
            const inputs = profileForm.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.backgroundColor = '#fff';
                input.style.borderColor = '#2E7D32';
            });

            // Afficher les boutons d'actions
            if (formActions) formActions.style.display = 'flex';

            // Cacher le bouton Modifier
            this.style.display = 'none';
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            // Recharger la page pour annuler les modifications
            window.location.reload();
        });
    }

    // Gestion du changement d'avatar
    if (avatarOverlay && avatarInput) {
        avatarOverlay.addEventListener('click', function() {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];

                // Validation de la taille (5MB max)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('L\'image ne doit pas dépasser 5MB');
                    this.value = '';
                    return;
                }

                // Validation du type
                if (!file.type.match('image.*')) {
                    alert('Veuillez sélectionner une image valide');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (profileAvatar) {
                        profileAvatar.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Gestion des toggles de mot de passe
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');

            if (!input) return;

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

    // Validation du mot de passe en temps réel (si les éléments existent)
    const newPasswordInput = document.getElementById('passwordForm_newPassword_first');
    const confirmPasswordInput = document.getElementById('passwordForm_newPassword_second');

    if (newPasswordInput && confirmPasswordInput) {
        newPasswordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);

        function validatePassword() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Validation de la force du mot de passe
            const hasMinLength = newPassword.length >= 8;
            const hasUpperCase = /[A-Z]/.test(newPassword);
            const hasLowerCase = /[a-z]/.test(newPassword);
            const hasNumbers = /\d/.test(newPassword);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword);

            // Mettre à jour les indicateurs visuels si les éléments existent
            updateRequirement('reqLength', hasMinLength);
            updateRequirement('reqUpper', hasUpperCase);
            updateRequirement('reqLower', hasLowerCase);
            updateRequirement('reqNumber', hasNumbers);
            updateRequirement('reqSpecial', hasSpecialChar);

            // Vérifier si les mots de passe correspondent
            const matchElement = document.getElementById('passwordMatch');
            if (matchElement) {
                if (confirmPassword && newPassword !== confirmPassword) {
                    matchElement.textContent = 'Les mots de passe ne correspondent pas';
                    matchElement.style.color = '#f44336';
                } else if (confirmPassword) {
                    matchElement.textContent = 'Les mots de passe correspondent';
                    matchElement.style.color = '#4caf50';
                } else {
                    matchElement.textContent = '';
                }
            }
        }

        function updateRequirement(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.color = isValid ? '#4caf50' : '#757575';
                element.style.fontWeight = isValid ? 'bold' : 'normal';
            }
        }
    }

    // Fermer automatiquement les messages flash après 5 secondes
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);

    // Gestion de la sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const dashboardSidebar = document.querySelector('.dashboard-sidebar');
    const dashboardContent = document.querySelector('.dashboard-content');

    if (sidebarToggle && dashboardSidebar) {
        sidebarToggle.addEventListener('click', function() {
            dashboardSidebar.classList.toggle('collapsed');
            if (dashboardContent) {
                dashboardContent.classList.toggle('expanded');
            }

            const icon = this.querySelector('i');
            if (dashboardSidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
            }
        });
    }

    // Gestion des erreurs d'image
    if (profileAvatar) {
        profileAvatar.onerror = function() {
            if (window.defaultAvatarUrl) {
                this.src = window.defaultAvatarUrl;
            }
        };
    }
});
