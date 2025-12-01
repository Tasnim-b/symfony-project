from django.urls import path
from . import views

urlpatterns = [
    path('chat/', views.chat_endpoint, name='chat'),
    path('health/', views.health_check, name='health_check'),
    path('chat-interface/', views.chat_interface, name='chat_interface'),
    path('chat-api/', views.ChatAPIView.as_view(), name='chat_api'),
    path('history/<str:session_id>/', views.get_conversation_history_view, name='get_history'),
]