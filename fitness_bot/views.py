from django.shortcuts import render



from rest_framework.decorators import api_view
from rest_framework.response import Response
from rest_framework import status
from .chat_logic import FitnessChatBot
import uuid

# Create your views here.
@api_view(['POST'])
def chat_endpoint(request):
    """
    Endpoint principal pour le chatbot
    """
    try:
        user_message = request.data.get('message', '').strip()
        session_id = request.data.get('session_id')
        user_level = request.data.get('user_level', 'debutant')
        
        if not user_message:
            return Response(
                {'error': 'Le message ne peut pas être vide'}, 
                status=status.HTTP_400_BAD_REQUEST
            )
        
        # Générer un session_id si non fourni
        if not session_id:
            session_id = str(uuid.uuid4())
        
        # Traiter le message
        chatbot = FitnessChatBot()
        bot_response = chatbot.process_message(
            message=user_message,
            session_id=session_id,
            user_level=user_level
        )
        
        return Response({
            'response': bot_response,
            'session_id': session_id,
            'success': True
        })
        
    except Exception as e:
        return Response({
            'error': f'Erreur interne du serveur: {str(e)}',
            'success': False
        }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

@api_view(['GET'])
def health_check(request):
    """
    Endpoint de vérification de santé de l'API
    """
    return Response({
        'status': 'healthy',
        'service': 'Fitness ChatBot API',
        'version': '1.0'
    })


# fitness_bot/views.py
from django.shortcuts import render
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json

# ... vos autres vues existantes ...

def chat_interface(request):
    """Interface web pour tester le chatbot"""
    return render(request, 'fitness_bot/chat_interface.html')

@method_decorator(csrf_exempt, name='dispatch')
class ChatAPIView(View):
    """Vue API pour l'interface de chat"""
    
    def post(self, request):
        try:
            data = json.loads(request.body)
            user_message = data.get('message', '').strip()
            session_id = data.get('session_id', '')
            user_level = data.get('user_level', 'debutant')
            
            if not user_message:
                return JsonResponse({
                    'error': 'Le message ne peut pas être vide',
                    'success': False
                })
            
            # Utiliser le chatbot existant
            from .chat_logic import FitnessChatBot
            chatbot = FitnessChatBot()
            bot_response = chatbot.process_message(
                message=user_message,
                session_id=session_id,
                user_level=user_level
            )
            
            return JsonResponse({
                'response': bot_response,
                'success': True
            })
            
        except Exception as e:
            return JsonResponse({
                'error': f'Erreur: {str(e)}',
                'success': False
            })

def get_conversation_history_view(request, session_id):
    """Récupère l'historique des conversations pour l'interface"""
    from .history_manager import ConversationHistory
    history = ConversationHistory.get_user_history(session_id)
    
    if not history:
        return JsonResponse({'error': 'Session non trouvée', 'success': False})
    
    conversations = []
    for conv in history['conversations']:
        conversations.append({
            'user_message': conv.user_message,
            'bot_response': conv.bot_response,
            'timestamp': conv.timestamp.strftime('%H:%M'),
            'message_length': conv.message_length,
            'response_length': conv.response_length
        })
    
    return JsonResponse({
        'session_info': {
            'session_id': history['session'].session_id,
            'user_level': history['session'].user_level,
            'total_messages': history['total_messages']
        },
        'conversations': conversations,
        'success': True
    })

from fitness_bot.chat_logic import FitnessChatBot

bot = FitnessChatBot()

def chat_api(request):
    user_message = request.POST.get("message")

    response = bot.process_message(user_message)

    return JsonResponse({"response": response})
