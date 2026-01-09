import os
from google import genai
from django.conf import settings
from .document_search import DocumentSearch
from .models import UserSession, Conversation

class FitnessChatBot:
    def __init__(self):
        self.document_search = DocumentSearch()
        
        # Initialize Google GenAI Client
        api_key = os.getenv('GOOGLE_API_KEY') or getattr(settings, 'GOOGLE_API_KEY', None)
        if not api_key:
             print("WARNING: GOOGLE_API_KEY not found.")
             self.client = None
        else:
             self.client = genai.Client(api_key=api_key)

    def process_message(self, message, session_id=None, user_level='debutant'):
        """Traite le message avec RAG"""
        
        if not self.client:
            return "Erreur de configuration : Clé API manquante."

        message_lower = message.lower().strip()

        # 0. Basic Conservation
        greetings = ['bonjour', 'salut', 'hello', 'coucou', 'hey']
        if any(g in message_lower for g in greetings) and len(message.split()) < 3:
             return "Bonjour ! Je suis votre coach santé virtuel. Comment puis-je vous aider aujourd'hui ? 🥗💪"
        
        farewells = ['au revoir', 'bye', 'adieu', 'ciao']
        if any(f in message_lower for f in farewells) and len(message.split()) < 3:
             return "Au revoir ! Gardez la forme ! 👋"

        # 1. Search for relevant documents
        doc_results = self.document_search.search(message, k=4)
        
        # 2. Prepare Context
        context_text = "\n\n".join([f"--- Source: {r['document']['title']} ---\n{r['excerpt']}" for r in doc_results])
        
        # 3. Build Prompt
        system_instruction = """Tu es un assistant expert en fitness, nutrition et santé pour le site 'Health & Fitness'.
        Utilise les informations de contexte suivantes pour répondre à la question de l'utilisateur.
        Si la réponse n'est pas dans le contexte, utilise tes connaissances générales mais précise que c'est un conseil général.
        Sois encourageant et bienveillant.
        """
        
        full_prompt = f"""{system_instruction}
        
        CONTEXTE DOCUMENTAIRE :
        {context_text}
        
        QUESTION UTILISATEUR :
        {message}
        
        REPONSE :"""

        # 4. Generate Answer
        try:
            # Using the standard flash model (reliable free tier)
            response = self.client.models.generate_content(
                model="gemini-flash-latest", 
                contents=full_prompt
            )
            bot_response = response.text
        except Exception as e:
            print(f"Error generating response: {e}")
            bot_response = "Désolé, je n'arrive pas à joindre le cerveau de l'IA pour le moment. Veuillez réessayer."

        # 5. Save Conversation
        if session_id:
            self._save_conversation(session_id, message, bot_response, user_level)
            
        return bot_response

    def _save_conversation(self, session_id, user_message, bot_response, user_level='debutant'):
        try:
            session, created = UserSession.objects.get_or_create(
                session_id=session_id,
                defaults={'user_level': user_level}
            )
            Conversation.objects.create(
                session=session,
                user_message=user_message,
                bot_response=bot_response
            )
            session.save()
        except Exception as e:
            print(f"❌ Erreur sauvegarde conversation: {e}")
