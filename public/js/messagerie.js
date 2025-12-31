// messagerie.js
class Messagerie {
    constructor() {
        this.currentUser = window.currentUser;
        this.activeConversation = null;
        this.checkInterval = null;
        this.lastMessageDate = null;

        this.initialize();
    }

    initialize() {
        // Initialiser les événements
        this.bindEvents();

        // Vérifier les nouveaux messages toutes les 10 secondes
        this.startMessageChecker();

        // Charger le nombre de messages non lus
        this.updateUnreadCount();
    }

    bindEvents() {
        // Nouvelle conversation
        document.getElementById('newConversationBtn')?.addEventListener('click', () => this.showContacts());
        document.getElementById('closeContactsBtn')?.addEventListener('click', () => this.hideContacts());

        // Recherche
        document.getElementById('searchConversations')?.addEventListener('input', (e) => this.searchConversations(e.target.value));
        document.getElementById('searchContacts')?.addEventListener('input', (e) => this.searchContacts(e.target.value));

        // Formulaire d'envoi
        document.getElementById('sendMessageForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Auto-resize du textarea
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('input', this.autoResizeTextarea);
        }

        // Clic sur une conversation
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', () => {
                const userId = item.dataset.userId;
                this.loadConversation(userId);
            });
        });

        // Clic sur un contact
        document.querySelectorAll('.contact-item').forEach(item => {
            item.addEventListener('click', () => {
                const userId = item.dataset.userId;
                this.startNewConversation(userId);
            });
        });
    }

    showContacts() {
        document.getElementById('contactsSidebar').style.display = 'flex';
        document.getElementById('conversationsList').style.display = 'none';
    }

    hideContacts() {
        document.getElementById('contactsSidebar').style.display = 'none';
        document.getElementById('conversationsList').style.display = 'block';
    }

    searchConversations(query) {
        const items = document.querySelectorAll('.conversation-item');
        const normalizedQuery = query.toLowerCase().trim();

        items.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            const lastMessage = item.querySelector('.last-message').textContent.toLowerCase();

            if (name.includes(normalizedQuery) || lastMessage.includes(normalizedQuery)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchContacts(query) {
        const items = document.querySelectorAll('.contact-item');
        const normalizedQuery = query.toLowerCase().trim();

        items.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            const email = item.querySelector('.contact-email').textContent.toLowerCase();

            if (name.includes(normalizedQuery) || email.includes(normalizedQuery)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    async loadConversation(userId) {
        try {
            // Afficher le loader
            this.showLoader();

            // Marquer la conversation comme active
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.userId === userId) {
                    item.classList.add('active');
                }
            });

            // Charger la conversation
            const response = await fetch(`/messagerie/conversation/${userId}`);
            const html = await response.text();

            // Créer un élément temporaire pour parser le HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Extraire les messages
            const messagesContainer = tempDiv.querySelector('#messagesContainer');
            if (messagesContainer) {
                document.getElementById('messagesContainer').innerHTML = messagesContainer.innerHTML;
            }

            // Extraire les infos de l'utilisateur
            const conversationName = tempDiv.querySelector('#activeConversationName')?.textContent || '';
            const conversationAvatar = tempDiv.querySelector('#activeConversationAvatar')?.src || '';

            // Mettre à jour l'interface
            this.updateActiveConversationUI(userId, conversationName, conversationAvatar);

            // Cacher "aucune conversation sélectionnée"
            document.getElementById('noConversationSelected').style.display = 'none';
            document.getElementById('conversationActive').style.display = 'flex';

            // Scroller vers le bas
            this.scrollToBottom();

            // Mettre à jour la conversation active
            this.activeConversation = userId;

            // Démarrer la vérification des nouveaux messages
            this.startMessageChecker();

        } catch (error) {
            console.error('Erreur lors du chargement de la conversation:', error);
            this.showError('Impossible de charger la conversation');
        } finally {
            this.hideLoader();
        }
    }

    updateActiveConversationUI(userId, name, avatar) {
        document.getElementById('activeConversationName').textContent = name;
        document.getElementById('activeConversationAvatar').src = avatar;
        document.getElementById('activeConversationAvatar').alt = name;
    }

    async startNewConversation(userId) {
        // Cacher la liste des contacts
        this.hideContacts();

        // Charger la conversation (créera une nouvelle conversation vide)
        await this.loadConversation(userId);
    }

    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const content = messageInput.value.trim();

        if (!content || !this.activeConversation) {
            return;
        }

        try {
            // Désactiver le formulaire
            messageInput.disabled = true;

            const formData = new FormData();
            formData.append('content', content);

            const response = await fetch(`/messagerie/send/${this.activeConversation}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Ajouter le message à l'interface
                this.addMessageToUI(data.message, true);

                // Vider le champ
                messageInput.value = '';
                this.autoResizeTextarea({ target: messageInput });

                // Scroller vers le bas
                this.scrollToBottom();

                // Mettre à jour la dernière conversation
                this.updateLastMessageInList(this.activeConversation, content);
            } else {
                this.showError(data.message || 'Erreur lors de l\'envoi du message');
            }

        } catch (error) {
            console.error('Erreur lors de l\'envoi du message:', error);
            this.showError('Impossible d\'envoyer le message');
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    }

    addMessageToUI(messageData, isSent = false) {
        const template = document.getElementById('messageTemplate');
        const clone = template.content.cloneNode(true);
        const messageElement = clone.querySelector('.message');

        messageElement.dataset.messageId = messageData.id;
        messageElement.classList.add(isSent ? 'sent' : 'received');

        const messageText = messageElement.querySelector('.message-text');
        const messageTime = messageElement.querySelector('.message-time');
        const readStatus = messageElement.querySelector('.read-status');

        messageText.textContent = messageData.content;
        messageTime.textContent = messageData.createdAt;

        // Ajouter le séparateur de date si nécessaire
        if (this.lastMessageDate !== messageData.date) {
            this.addDateSeparator(messageData.date);
            this.lastMessageDate = messageData.date;
        }

        // Ajouter au container
        document.getElementById('messagesContainer').appendChild(messageElement);
    }

    addDateSeparator(date) {
        const template = document.getElementById('dateSeparatorTemplate');
        const clone = template.content.cloneNode(true);
        const separator = clone.querySelector('.date-separator span');

        separator.textContent = date;

        document.getElementById('messagesContainer').appendChild(clone);
    }

    updateLastMessageInList(userId, content) {
        const conversationItem = document.querySelector(`.conversation-item[data-user-id="${userId}"]`);
        if (conversationItem) {
            const lastMessageElement = conversationItem.querySelector('.last-message');
            const timeElement = conversationItem.querySelector('.message-time');

            if (lastMessageElement) {
                lastMessageElement.textContent = content.length > 50 ? content.substring(0, 50) + '...' : content;
            }

            if (timeElement) {
                const now = new Date();
                timeElement.textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            }
        }
    }

    startMessageChecker() {
        // Arrêter l'intervalle précédent
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }

        // Démarrer un nouvel intervalle
        this.checkInterval = setInterval(() => {
            if (this.activeConversation) {
                this.checkNewMessages();
            }
            this.updateUnreadCount();
        }, 5000); // Vérifier toutes les 5 secondes
    }

    async checkNewMessages() {
        if (!this.activeConversation) return;

        try {
            const response = await fetch(`/messagerie/check-new/${this.activeConversation}`);
            const data = await response.json();

            if (data.success && data.messages.length > 0) {
                // Ajouter les nouveaux messages
                data.messages.forEach(message => {
                    const isSent = message.senderId === this.currentUser.id;
                    this.addMessageToUI(message, isSent);
                });

                // Scroller vers le bas
                this.scrollToBottom();

                // Mettre à jour le badge
                this.updateUnreadCount();
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouveaux messages:', error);
        }
    }

    async updateUnreadCount() {
        try {
            const response = await fetch('/messagerie/unread-count');
            const data = await response.json();

            const globalBadge = document.getElementById('globalUnreadCount');
            const sidebarBadge = document.getElementById('sidebarUnreadCount');

            if (globalBadge) {
                globalBadge.textContent = data.count > 0 ? data.count : '';
                globalBadge.style.display = data.count > 0 ? 'flex' : 'none';
            }

            if (sidebarBadge) {
                sidebarBadge.textContent = data.count;
                sidebarBadge.style.display = data.count > 0 ? 'flex' : 'none';
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour du compteur:', error);
        }
    }

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    autoResizeTextarea(event) {
        const textarea = event.target;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
    }

    showLoader() {
        // Implémenter un loader si nécessaire
    }

    hideLoader() {
        // Cacher le loader
    }

    showError(message) {
        // Afficher une notification d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-notification';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }
}

// Initialiser la messagerie quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.messagerie = new Messagerie();
});
