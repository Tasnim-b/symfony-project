// auth.js - Scripts communs pour login et register

document.addEventListener('DOMContentLoaded', function() {
    console.log('Auth JS chargé');

    // ===========================
    // TOGGLES DE MOT DE PASSE
    // ===========================
    const toggles = [
        { btnId: 'toggleLoginPassword', inputId: 'loginPassword' },
        // { btnId: 'toggleRegisterPassword', inputId: 'registerPassword' },
        // { btnId: 'toggleConfirmPassword', inputId: 'confirmPassword' }
    ];

    toggles.forEach(toggle => {
        const btn = document.getElementById(toggle.btnId);
        const input = document.getElementById(toggle.inputId);

        if (btn && input) {
            btn.addEventListener('click', function() {
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });

    // ===========================
    // UPLOAD D'IMAGE (register seulement)
    // ===========================
    // const imageInput = document.getElementById('profileImage');
    // if (imageInput) {
    //     initImageUpload();
    // }

    // ===========================
    // VALIDATION DES FORMULAIRES
    // ===========================
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }

    // const registerForm = document.getElementById('registerForm');
    // if (registerForm) {
    //     registerForm.addEventListener('submit', function(e) {
    //         if (!validateRegisterForm()) {
    //             e.preventDefault();
    //         }
    //     });
    // }
});

// =========================================
// UPLOAD D'IMAGE
// =========================================
function initImageUpload() {
    const imageInput = document.getElementById('profileImage');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = imagePreview?.querySelector('.preview-image');
    const previewPlaceholder = imagePreview?.querySelector('.preview-placeholder');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const fileInfo = document.getElementById('fileInfo');

    if (!imageInput) return;

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérification de la taille (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showNotification('L\'image ne doit pas dépasser 5MB', 'error');
                this.value = '';
                return;
            }

            // Vérification du type
            if (!file.type.match('image.*')) {
                showNotification('Veuillez sélectionner une image valide', 'error');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImage) {
                    previewImage.src = e.target.result;
                    previewImage.classList.add('loaded');
                }
                if (previewPlaceholder) {
                    previewPlaceholder.style.display = 'none';
                }
                if (removeImageBtn) {
                    removeImageBtn.style.display = 'inline-flex';
                }
                if (fileInfo) {
                    const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                    fileInfo.textContent = `${file.name} (${sizeInMB} MB)`;
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Bouton de suppression
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            if (previewImage) {
                previewImage.src = '';
                previewImage.classList.remove('loaded');
            }
            if (previewPlaceholder) {
                previewPlaceholder.style.display = 'flex';
            }
            this.style.display = 'none';
            if (fileInfo) {
                fileInfo.textContent = '';
            }
        });
    }
}

// =========================================
// VALIDATION FORMULAIRE LOGIN
// =========================================
function validateLoginForm() {
    const email = document.getElementById('loginEmail')?.value;
    const password = document.getElementById('loginPassword')?.value;

    if (!email || !validateEmail(email)) {
        showNotification('Veuillez entrer une adresse email valide', 'error');
        return false;
    }

    if (!password || password.length < 6) {
        showNotification('Le mot de passe doit contenir au moins 6 caractères', 'error');
        return false;
    }

    return true;
}

// =========================================
// VALIDATION FORMULAIRE REGISTER
// =========================================
function validateRegisterForm() {
    // const name = document.getElementById('registerName')?.value;
    // const email = document.getElementById('registerEmail')?.value;
    // const password = document.getElementById('registerPassword')?.value;
    // const confirm = document.getElementById('confirmPassword')?.value;
    // const acceptTerms = document.getElementById('acceptTerms')?.checked;

    // // Validation nom
    // if (!name || name.trim().length < 2) {
    //     showNotification('Veuillez entrer votre nom complet (min. 2 caractères)', 'error');
    //     return false;
    // }

    // // Validation email
    // if (!email || !validateEmail(email)) {
    //     showNotification('Veuillez entrer une adresse email valide', 'error');
    //     return false;
    // }

    // // Validation mot de passe
    // if (!password || password.length < 8) {
    //     showNotification('Le mot de passe doit contenir au moins 8 caractères', 'error');
    //     return false;
    // }

    // // Vérification force mot de passe
    // if (!validatePasswordStrength(password)) {
    //     showNotification('Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre', 'error');
    //     return false;
    // }

    // // Confirmation mot de passe
    // if (password !== confirm) {
    //     showNotification('Les mots de passe ne correspondent pas', 'error');
    //     return false;
    // }

    // // Conditions d'utilisation
    // if (!acceptTerms) {
    //     showNotification('Veuillez accepter les conditions d\'utilisation', 'error');
    //     return false;
    // }

    return true;
}

// =========================================
// VALIDATION EMAIL
// =========================================
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// =========================================
// VALIDATION FORCE MOT DE PASSE
// =========================================
function validatePasswordStrength(password) {
    // Au moins 8 caractères, une majuscule, une minuscule, un chiffre
    const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return re.test(password);
}

// =========================================
// NOTIFICATION
// =========================================
function showNotification(message, type = 'success') {
    // Créer une notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#f44336' : type === 'info' ? '#2196f3' : '#4caf50'};
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        align-items: center;
        gap: 10px;
    `;

    const icon = type === 'error' ? 'fa-exclamation-circle' :
                 type === 'info' ? 'fa-info-circle' : 'fa-check-circle';

    notification.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);

    // Ajouter les animations CSS si elles n'existent pas
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
    }
}






// Animation de chargement pour le modal
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter la classe 'loaded' après un court délai
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 100);

    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const closeBtn = document.querySelector('.modal-close-btn');
            if (closeBtn) {
                closeBtn.click();
            }
        }
    });

    // Fermer en cliquant sur l'overlay (optionnel)
    document.querySelector('.blur-overlay')?.addEventListener('click', function() {
        const closeBtn = document.querySelector('.modal-close-btn');
        if (closeBtn) {
            window.location.href = closeBtn.href;
        }
    });








});
