// Communauté JavaScript - Version avec données réelles

// Utiliser les données du serveur (passées par PHP) ou les données de fallback
let membersData = window.serverMembersData || [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Communauté JS chargé - ' + membersData.length + ' membres chargés');

    // Initialiser les statistiques
    updateStatistics();

    // Initialiser les gestionnaires d'événements
    initEventHandlers();
});

function updateStatistics() {
    const totalMembers = membersData.length;
    // Dans votre cas, tous les membres sont hors ligne, donc online = 0
    const onlineMembers = 0; // membersData.filter(member => member.online).length;

    // Mettre à jour les statistiques dans l'en-tête
    document.getElementById('totalMembers').textContent = totalMembers;
    document.getElementById('onlineMembers').textContent = onlineMembers;
}

function initEventHandlers() {
    // Gestion du menu utilisateur
    initUserMenu();

    // Gestion de la sidebar
    initSidebar();

    // Gestion du modal de contact
    initContactModal();
}

function initUserMenu() {
    const userMenuTrigger = document.getElementById('userMenuTrigger');
    const userMenu = document.getElementById('userMenu');

    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = userMenu.style.opacity === '1';
            userMenu.style.opacity = isVisible ? '0' : '1';
            userMenu.style.visibility = isVisible ? 'hidden' : 'visible';
            userMenu.style.transform = isVisible ? 'translateY(-10px)' : 'translateY(0px)';
        });

        document.addEventListener('click', function() {
            userMenu.style.opacity = '0';
            userMenu.style.visibility = 'hidden';
            userMenu.style.transform = 'translateY(-10px)';
        });

        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

function initSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.dashboard-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.style.transform = sidebar.classList.contains('active') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }
}

function initContactModal() {
    const modal = document.getElementById('contactModal');
    const closeModal = document.getElementById('closeModal');
    const cancelContact = document.getElementById('cancelContact');
    const contactForm = document.getElementById('contactForm');
    const contactButtons = document.querySelectorAll('.contact-btn');
    let currentMember = null;

    // Écouter les clics sur les boutons "Contacter"
    contactButtons.forEach(button => {
        button.addEventListener('click', function() {
            const memberId = parseInt(this.dataset.memberId);
            currentMember = membersData.find(m => m.id === memberId);

            if (currentMember) {
                openContactModal(currentMember);
            }
        });
    });

    function openContactModal(member) {
        // Assurez-vous que l'avatar a une URL valide
        const avatarUrl = member.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(member.name) + '&background=2e7d32&color=fff&size=100';
        document.getElementById('modalAvatar').src = avatarUrl;
        document.getElementById('modalName').textContent = member.name;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeContactModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        if (contactForm) contactForm.reset();
        currentMember = null;
    }

    if (closeModal) {
        closeModal.addEventListener('click', closeContactModal);
    }

    if (cancelContact) {
        cancelContact.addEventListener('click', closeContactModal);
    }

    // Fermer en cliquant en dehors du modal
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeContactModal();
            }
        });
    }

    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeContactModal();
        }
    });

    // Gestion de l'envoi du formulaire
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const messageContent = document.getElementById('messageContent').value;

            // Validation basique
            if (!messageContent.trim()) {
                alert('Veuillez écrire un message');
                return;
            }

            // Simuler l'envoi du message
            setTimeout(() => {
                alert(`Message envoyé à ${currentMember.name} !\n\nIl/Elle recevra une notification et vous répondra dans les plus brefs délais.`);
                closeContactModal();

                // Mettre à jour le badge des messages
                const messageBadge = document.querySelector('.message-badge');
                if (messageBadge) {
                    const currentCount = parseInt(messageBadge.textContent) || 0;
                    messageBadge.textContent = currentCount + 1;
                }
            }, 500);
        });
    }
}

// Simulation : basculer l'état en ligne/hors ligne (optionnel - pour démo)
function simulateOnlineStatus() {
    setInterval(() => {
        // Changer aléatoirement l'état en ligne de quelques membres
        membersData.forEach(member => {
            if (Math.random() > 0.85) { // 15% de chance de changer (réduit pour plus de réalisme)
                member.online = !member.online;
            }
        });

        // Mettre à jour l'affichage
        updateMemberCards();
        updateStatistics();
    }, 15000); // Toutes les 15 secondes
}

function updateMemberCards() {
    const memberCards = document.querySelectorAll('.member-card');
    memberCards.forEach(card => {
        const memberId = parseInt(card.dataset.id);
        const member = membersData.find(m => m.id === memberId);

        if (member) {
            const statusElement = card.querySelector('.member-status');
            if (statusElement) {
                const icon = statusElement.querySelector('i');
                const text = statusElement.querySelector('span');

                icon.className = `fas fa-circle status-${member.online ? 'online' : 'offline'}`;
                text.textContent = member.online ? 'En ligne' : 'Hors ligne';
            }
        }
    });
}



/*
window.addEventListener('load', function() {
    // Démarrer la simulation après 2 secondes
    setTimeout(simulateOnlineStatus, 2000);
});
*/
