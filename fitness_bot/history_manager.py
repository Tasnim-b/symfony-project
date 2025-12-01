from .models import UserSession, Conversation
from django.utils import timezone
from datetime import timedelta

class ConversationHistory:
    """Gère la récupération et l'analyse de l'historique des conversations"""
    
    @staticmethod
    def get_user_history(session_id, limit=50):
        """
        Récupère l'historique complet d'un utilisateur
        """
        try:
            session = UserSession.objects.get(session_id=session_id)
            conversations = session.conversation_set.all().order_by('timestamp')[:limit]
            
            return {
                'session': session,
                'conversations': list(conversations),
                'total_messages': session.conversation_set.count(),
                'user_level': session.user_level,
                'goals': session.goals
            }
        except UserSession.DoesNotExist:
            return None
    
    @staticmethod
    def get_recent_conversations(session_id, hours=24):
        """
        Récupère les conversations récentes (dernières 24h par défaut)
        """
        try:
            session = UserSession.objects.get(session_id=session_id)
            since = timezone.now() - timedelta(hours=hours)
            
            conversations = session.conversation_set.filter(
                timestamp__gte=since
            ).order_by('timestamp')
            
            return conversations
        except UserSession.DoesNotExist:
            return []
    
    @staticmethod
    def cleanup_old_sessions(days=30):
        """
        Nettoie les sessions inactives de plus de X jours
        """
        cutoff_date = timezone.now() - timedelta(days=days)
        old_sessions = UserSession.objects.filter(last_activity__lt=cutoff_date)
        count = old_sessions.count()
        old_sessions.delete()
        return count