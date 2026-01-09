from django.test import TestCase
from rest_framework.test import APITestCase
from rest_framework import status
from django.urls import reverse

class ChatbotAPITests(APITestCase):
    def test_chat_response(self):
        """
        Ensure we can get a response from the chat endpoint
        """
        url = reverse('chat')
        data = {'message': 'Bonjour'}
        response = self.client.post(url, data, format='json')
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertIn('response', response.data)
        self.assertIn('session_id', response.data)

    def test_empty_message(self):
        url = reverse('chat')
        data = {'message': ''}
        response = self.client.post(url, data, format='json')
        self.assertEqual(response.status_code, status.HTTP_400_BAD_REQUEST)

