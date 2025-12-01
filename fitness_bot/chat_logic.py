# # fitness_bot/chat_logic.py
# import random
# from .document_search import DocumentSearch
# from .models import UserSession, Conversation

# class FitnessChatBot:
#     def __init__(self):
#         self.document_search = DocumentSearch()
#         self.setup_responses()
#         self.conversation_context = {} 
    
#     def setup_responses(self):
#         """Configure les réponses par défaut"""
#         self.greetings = {
#             'fr': ['bonjour', 'salut', 'coucou', 'hello', 'hey', 'bonsoir'],
#             'responses': [
#                 "Bonjour ! Je suis votre assistant fitness. Comment puis-je vous aider aujourd'hui ? 💪",
#                 "Salut ! Prêt à booster votre forme ? Dites-moi ce qui vous préoccupe ! 🏃‍♂️",
#                 "Hello ! Je suis là pour vous accompagner dans votre parcours santé. Que souhaitez-vous savoir ? 🌟"
#             ]
#         }
#         self.intent_keywords = {
#             'prise_muscle': ['prise de poids', 'prendre du muscle', 'masse musculaire', 'devenir musclé'],
#             'perte_poids': ['perdre du poids', 'maigrir', 'régime', 'mincir'],
#             'debutant': ['débutant', 'commencer', 'premier fois', 'novice'],
#             'nutrition': ['alimentation', 'nutrition', 'manger', 'repas', 'calories'],
#             'entrainement': ['entraînement', 'exercice', 'sport', 'musculation']
#         }
        
#         self.farewells = {
#             'fr': ['au revoir', 'bye', 'à plus', 'ciao', 'adieu'],
#             'responses': [
#                 "Au revoir ! N'oubliez pas : la régularité est la clé du succès ! 💪",
#                 "À bientôt ! Continuez vos efforts, vous faites du bon travail ! 🌟",
#                 "Bye ! Revenez me voir pour partager vos progrès ! 🏃‍♂️"
#             ]
#         }
        
#         self.motivation_responses = [
#             "Chaque petit effort compte ! Vous êtes sur la bonne voie 🚀",
#             "La progression n'est pas toujours linéaire, mais chaque séance vous rapproche de vos objectifs 💫",
#             "Rappelez-vous pourquoi vous avez commencé. Vous êtes plus fort que vous ne le pensez ! 🔥"
#         ]
    
#     def process_message(self, message, session_id=None, user_level='debutant'):
#         """Traite le message avec gestion du contexte"""
#         message_lower = message.lower().strip()
        
#         # Gérer les salutations
#         if any(greeting in message_lower for greeting in self.greetings['fr']):
#             return random.choice(self.greetings['responses'])
        
#         # Gérer les au revoir
#         if any(farewell in message_lower for farewell in self.farewells['fr']):
#             return random.choice(self.farewells['responses'])
        
#         # Analyser l'intention
#         intent = self._detect_intent(message_lower)
        
#         # Recherche dans la documentation avec contexte
#         doc_results = self.document_search.search(message, user_level=user_level)
        
#         # Si pas de résultats, essayer avec l'intention détectée
#         if not doc_results or doc_results[0]['score'] <= 2:
#             if intent:
#                 doc_results = self.document_search.search(intent, user_level=user_level)
        
#         if doc_results and doc_results[0]['score'] > 1:  # Baisser le seuil
#             # CORRECTION : Supprimer le paramètre 'intent' 
#             response = self._format_document_response(doc_results, message_lower)
#         else:
#             response = self._get_contextual_response(message_lower, intent, user_level)
        
#         # Sauvegarder la conversation
#         if session_id:
#             self._save_conversation(session_id, message, response, user_level)
        
#         return response

#     def _detect_intent(self, message):
#         """Détecte l'intention de l'utilisateur"""
#         message_lower = message.lower()
        
#         for intent, keywords in self.intent_keywords.items():
#             if any(keyword in message_lower for keyword in keywords):
#                 return intent
#         return None

#     def _get_contextual_response(self, message, intent, user_level):
#         """Réponses contextuelles basées sur l'intention"""
#         if intent == 'prise_muscle' and 'nutrition' in message:
#             return self._get_prise_muscle_nutrition_response()
#         elif intent == 'prise_muscle' and 'debutant' in message:
#             return self._get_prise_muscle_debutant_response()
#         elif intent == 'debutant':
#             return "Je vois que vous êtes débutant ! C'est excellent de commencer le sport. Voulez-vous des conseils pour la nutrition, l'entraînement ou les deux ? 🎯"
#         elif intent == 'nutrition':
#             return "Parfait, parlons nutrition ! Souhaitez-vous des conseils pour prendre du muscle, perdre du poids ou simplement mieux manger ? 🥗"
#         else:
#             generic_responses = [
#                 "Je veux vous aider précisément. Parlez-moi de vos objectifs : prise de muscle, perte de poids, bien-être général ? 🎯",
#                 "Pour des conseils personnalisés, dites-moi si vous visez plutôt la prise de muscle, la perte de poids ou l'amélioration de votre condition physique ? 💪",
#             ]
#             return random.choice(generic_responses)

#     def _get_prise_muscle_nutrition_response(self):
#         """Réponse spécifique pour nutrition prise de muscle"""
#         return """**🥗 Nutrition pour la Prise de Masse Musculaire**

# Pour prendre du muscle efficacement, voici les bases nutritionnelles :

# • **Surplus calorique** : Mangez 300-500 calories de plus que vos besoins
# • **Protéines** : 1.6-2.2g/kg (viande, poisson, œufs, légumineuses)
# • **Glucides** : 4-6g/kg (riz, pâtes, patate douce)
# • **Repas fréquents** : 4-6 repas par jour

# Voulez-vous un exemple de programme alimentaire détaillé ? 📋"""

#     def _get_prise_muscle_debutant_response(self):
#         """Réponse spécifique pour débutant en prise de muscle"""
#         return """**🏋️‍♂️ Prise de Muscle pour Débutant**

# Parfait pour commencer ! Voici les bases :

# • **Entraînement** : 3-4 séances/semaine, exercices de base
# • **Nutrition** : Augmentez progressivement les calories
# • **Récupération** : Dormez 7-9h par nuit

# Je peux vous détailler un programme d'entraînement débutant si vous voulez ! 💪"""

#     def _format_document_response(self, doc_results, user_message):
#         """Formate la réponse basée sur les documents trouvés"""
#         best_doc = doc_results[0]
#         response = f"**💡 {best_doc['document'].title}**\n\n"
        
#         # Personnaliser l'introduction selon le contexte
#         if any(word in user_message for word in ['débutant', 'commencer', 'premier']):
#             response = "**🎯 Parfait pour commencer !**\n\n" + response
        
#         response += f"{best_doc['excerpt']}\n\n"
        
#         # Ajouter des documents connexes
#         if len(doc_results) > 1:
#             response += "**📚 Pour aller plus loin :**\n"
#             for doc in doc_results[1:3]:
#                 response += f"• {doc['document'].title}\n"
        
#         # Ajouter le disclaimer médical
#         response += "\n---\n"
#         response += "⚠️ *Rappel : Je suis un assistant virtuel. Consultez un professionnel de santé pour des conseils personnalisés.*"
        
#         return response
    
#     def _get_generic_response(self, message):
#         """Réponses génériques quand aucun document pertinent n'est trouvé"""
#         if any(word in message for word in ['motivation', 'motiver', 'envie']):
#             return random.choice(self.motivation_responses)
        
#         elif any(word in message for word in ['merci']):
#             return "Avec plaisir ! N'hésitez pas si vous avez d'autres questions 🌟"
        
#         else:
#             generic_responses = [
#                 "Je comprends votre question ! Pouvez-vous préciser si cela concerne la nutrition, l'entraînement, la motivation ou la santé ? 🎯",
#                 "Intéressant ! Pour vous donner la meilleure réponse, dites-moi si vous êtes débutant, intermédiaire ou avancé en sport ? 🏋️‍♂️",
#                 "Je veux vous aider au mieux ! Parlez-moi de vos objectifs : perte de poids, prise de muscle, bien-être général ? 🌟"
#             ]
#             return random.choice(generic_responses)
    
#     def _save_conversation(self, session_id, user_message, bot_response, user_level='debutant'):
#         """Sauvegarde la conversation dans la base de données"""
#         try:
#             session, created = UserSession.objects.get_or_create(
#                 session_id=session_id,
#                 defaults={'user_level': user_level}
#             )
            
#             Conversation.objects.create(
#                 session=session,
#                 user_message=user_message,
#                 bot_response=bot_response
#             )
            
#             # Met à jour la dernière activité
#             session.save()
            
#         except Exception as e:
#             print(f"❌ Erreur sauvegarde conversation: {e}")
import random
from .intent_router import IntentRouter
from .document_search import DocumentSearch

class FitnessChatBot:

    def __init__(self):
        self.router = IntentRouter()
        self.search_engine = DocumentSearch()

    def process_message(self, message, session_id=None, user_level="debutant"):
        msg = message.lower()

        # 1 — Router détecte l'intention
        intent = self.router.detect_intent(msg)

        # 2 — Cas simples : salut, au revoir
        if intent == "greeting":
            return random.choice(["Bonjour ! Comment puis-je aider ? 😊"])
        if intent == "goodbye":
            return random.choice(["À bientôt 👋"])

        # 3 — Le type de document dépend de l'intent
        doc_type = self.router.route_document_type(intent)

        # 4 — RAG search
        docs = self.search_engine.search(msg, doc_type)

        if docs:
            return self._format_rag_answer(docs)

        # 5 — Fallback intelligent
        return self._fallback(intent)

    def _format_rag_answer(self, docs):
        best = docs[0]
        return f"""
📘 **{best['document'].title}**

{best['excerpt']}

---
Pour aller plus loin :
{chr(10).join('• ' + d['document'].title for d in docs[1:3])}

⚠️ Ceci est un guide général, pas un avis médical.
"""

    def _fallback(self, intent):
        if intent == "unknown":
            return "Pouvez-vous préciser si c’est pour la nutrition, l’entraînement ou la motivation ? 😊"

        return "Je n’ai pas encore de document exact pour votre question, mais je peux vous aider si vous précisez un peu plus ✨"
