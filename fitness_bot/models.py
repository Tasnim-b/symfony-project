from django.db import models

# Create your models here.
from django.db import models

class KnowledgeDocument(models.Model):
    """
    Stocke tous les documents de connaissance pour le chatbot
    Ces documents servent de base de référence pour le système RAG
    """
    DOCUMENT_TYPES = [
        ('nutrition', 'Nutrition'),
        ('fitness', 'Fitness'),
        ('motivation', 'Motivation'),
        ('sante', 'Santé'),
    ]
    
    LANGUAGES = [
        ('fr', 'Français'),
        ('en', 'English'),
        ('ar', 'Arabe'),
        ('es', 'Español'),
        ('de', 'Deutsch'),
    ]
    
    title = models.CharField(
        max_length=200,
        verbose_name="Titre du document",
        help_text="Titre descriptif du document"
    )
    
    content = models.TextField(
        verbose_name="Contenu du document",
        help_text="Texte complet du document (200-500 mots recommandés)"
    )
    
    document_type = models.CharField(
        max_length=20,
        choices=DOCUMENT_TYPES,
        verbose_name="Type de document",
        help_text="Catégorie du document"
    )
    
    language = models.CharField(
        max_length=10,
        choices=LANGUAGES,
        default='fr',
        verbose_name="Langue du document",
        help_text="Langue dans laquelle est rédigé le document"
    )
    
    keywords = models.JSONField(
        default=list,
        verbose_name="Mots-clés",
        help_text="Liste des mots-clés pour la recherche"
    )
    
    tags = models.JSONField(
        default=list,
        verbose_name="Tags",
        help_text="Tags supplémentaires pour la classification"
    )
    
    created_at = models.DateTimeField(
        auto_now_add=True,
        verbose_name="Date de création"
    )
    
    is_active = models.BooleanField(
        default=True,
        verbose_name="Actif",
        help_text="Désactiver pour masquer le document sans le supprimer"
    )

    class Meta:
        verbose_name = "Document de connaissance"
        verbose_name_plural = "Documents de connaissance"
        ordering = ['title']
        indexes = [
            models.Index(fields=['document_type', 'is_active']),
            models.Index(fields=['language', 'is_active']),
            models.Index(fields=['is_active']),
        ]

    def __str__(self):
        return f"{self.title} ({self.get_document_type_display()} - {self.get_language_display()})"

    def save(self, *args, **kwargs):
        """S'assure que les champs JSON sont bien des listes"""
        if not isinstance(self.keywords, list):
            self.keywords = []
        if not isinstance(self.tags, list):
            self.tags = []
        super().save(*args, **kwargs)


class UserSession(models.Model):
    """
    Représente une session utilisateur anonyme
    Permet de suivre les conversations et le contexte utilisateur
    """
    USER_LEVELS = [
        ('debutant', 'Débutant'),
        ('intermediaire', 'Intermédiaire'),
        ('avance', 'Avancé')
    ]
    
    LANGUAGES = [
        ('fr', 'Français'),
        ('en', 'English'),
        ('ar', 'Arabe'),
        ('es', 'Español'),
        ('de', 'Deutsch'),
    ]
    
    session_id = models.CharField(
        max_length=100,
        unique=True,
        verbose_name="ID de session",
        help_text="Identifiant unique pour la session utilisateur"
    )
    
    user_level = models.CharField(
        max_length=20,
        choices=USER_LEVELS,
        default='debutant',
        verbose_name="Niveau utilisateur",
        help_text="Niveau d'expérience en fitness de l'utilisateur"
    )
    
    language = models.CharField(
        max_length=10,
        choices=LANGUAGES,
        default='fr',
        verbose_name="Langue préférée",
        help_text="Langue préférée de l'utilisateur"
    )
    
    goals = models.JSONField(
        default=list,
        verbose_name="Objectifs",
        help_text="Liste des objectifs de l'utilisateur"
    )
    
    created_at = models.DateTimeField(
        auto_now_add=True,
        verbose_name="Date de création"
    )
    
    last_activity = models.DateTimeField(
        auto_now=True,
        verbose_name="Dernière activité"
    )

    class Meta:
        verbose_name = "Session utilisateur"
        verbose_name_plural = "Sessions utilisateur"
        ordering = ['-last_activity']
        indexes = [
            models.Index(fields=['session_id']),
            models.Index(fields=['last_activity']),
            models.Index(fields=['language']),
        ]

    def __str__(self):
        return f"Session {self.session_id} ({self.user_level} - {self.get_language_display()})"

    @property
    def conversation_count(self):
        """Retourne le nombre de messages dans cette session"""
        return self.conversation_set.count()

    @property
    def is_active(self):
        """Détermine si la session est active (moins de 30 minutes d'inactivité)"""
        from django.utils import timezone
        from datetime import timedelta
        return self.last_activity >= timezone.now() - timedelta(minutes=30)

    def save(self, *args, **kwargs):
        """S'assure que goals est bien une liste"""
        if not isinstance(self.goals, list):
            self.goals = []
        super().save(*args, **kwargs)


class Conversation(models.Model):
    """
    Stocke l'historique des conversations entre l'utilisateur et le chatbot
    Permet d'avoir un contexte et d'améliorer le système
    """
    session = models.ForeignKey(
        UserSession,
        on_delete=models.CASCADE,
        verbose_name="Session",
        help_text="Session utilisateur associée"
    )
    
    user_message = models.TextField(
        verbose_name="Message utilisateur",
        help_text="Message envoyé par l'utilisateur"
    )
    
    bot_response = models.TextField(
        verbose_name="Réponse du chatbot",
        help_text="Réponse générée par le chatbot"
    )
    
    timestamp = models.DateTimeField(
        auto_now_add=True,
        verbose_name="Horodatage"
    )

    class Meta:
        verbose_name = "Conversation"
        verbose_name_plural = "Conversations"
        ordering = ['timestamp']
        indexes = [
            models.Index(fields=['session', 'timestamp']),
            models.Index(fields=['timestamp']),
        ]

    def __str__(self):
        return f"Conversation {self.id} - {self.timestamp.strftime('%Y-%m-%d %H:%M')}"

    @property
    def message_length(self):
        """Retourne la longueur totale du message utilisateur"""
        return len(self.user_message)

    @property
    def response_length(self):
        """Retourne la longueur totale de la réponse du bot"""
        return len(self.bot_response)


# # Modèle optionnel pour les statistiques et améliorations futures
# class ChatbotAnalytics(models.Model):
#     """
#     Collecte des données analytiques pour améliorer le chatbot
#     (Optionnel pour le moment, peut être implémenté plus tard)
#     """
#     EVENT_TYPES = [
#         ('search', 'Recherche'),
#         ('response', 'Réponse'),
#         ('feedback', 'Feedback'),
#         ('error', 'Erreur'),
#     ]
    
#     event_type = models.CharField(
#         max_length=20,
#         choices=EVENT_TYPES,
#         verbose_name="Type d'événement"
#     )
    
#     session = models.ForeignKey(
#         UserSession,
#         on_delete=models.SET_NULL,
#         null=True,
#         blank=True,
#         verbose_name="Session"
#     )
    
#     data = models.JSONField(
#         default=dict,
#         verbose_name="Données de l'événement"
#     )
    
#     timestamp = models.DateTimeField(
#         auto_now_add=True,
#         verbose_name="Horodatage"
#     )

#     class Meta:
#         verbose_name = "Statistique chatbot"
#         verbose_name_plural = "Statistiques chatbot"
#         ordering = ['-timestamp']

#     def __str__(self):
#         return f"{self.get_event_type_display()} - {self.timestamp.strftime('%Y-%m-%d %H:%M')}"