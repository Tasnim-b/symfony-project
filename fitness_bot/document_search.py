# fitness_bot/document_search.py
import re
from .models import KnowledgeDocument
class DocumentSearch:
    def __init__(self):
        self.documents = KnowledgeDocument.objects.filter(is_active=True)

    def search(self, query, doc_type=None):
        query = query.lower()
        terms = self._extract_terms(query)

        results = []

        for doc in self.documents:
            if doc_type and doc.document_type != doc_type:
                continue

            score = self._score(doc, terms)

            if score > 0:
                results.append({
                    "document": doc,
                    "score": score,
                    "excerpt": self._excerpt(doc.content, terms)
                })

        results.sort(key=lambda r: r["score"], reverse=True)
        return results[:5]

    def _extract_terms(self, text):
        import re
        words = re.findall(r"[a-zéèêçùûàâôî]+", text.lower())
        return [w for w in words if len(w) > 3]

    def _score(self, doc, terms):
        score = 0
        title = doc.title.lower()
        content = doc.content.lower()
        keywords = [k.lower() for k in doc.keywords]

        for t in terms:
            if t in title:
                score += 4
            if t in keywords:
                score += 3
            if t in content:
                score += 1
        
        return score

    def _excerpt(self, content, terms, size=150):
        content_lower = content.lower()
        for t in terms:
            idx = content_lower.find(t)
            if idx != -1:
                return "..." + content[max(0, idx-30):idx+size] + "..."
        
        return content[:size] + "..."


# class DocumentSearch:
#     def __init__(self):
#         self.documents = KnowledgeDocument.objects.filter(is_active=True)
#         self.keyword_weights = {
#             'débutant': 3, 'commencer': 3, 'exercice': 2, 'entraînement': 2,
#             'programme': 2, 'sport': 2, 'fitness': 2,
#             'nutrition': 3, 'alimentation': 3, 'régime': 2, 'repas': 2,
#             'calories': 2, 'protéines': 2, 'glucides': 2,
#             'motivation': 3, 'motiver': 3, 'discipline': 2, 'objectifs': 2,
#             'santé': 3, 'sommeil': 3, 'récupération': 2, 'stress': 2,
#         }
    
#     def search(self, query, doc_type=None, user_level='debutant'):
#         query_terms = self._extract_keywords(query)
#         results = []
        
#         for doc in self.documents:
#             if doc_type and doc.document_type != doc_type:
#                 continue
                
#             score = self._calculate_relevance(doc, query_terms, user_level)
#             if score > 0:
#                 results.append({
#                     'document': doc,
#                     'score': score,
#                     'excerpt': self._get_excerpt(doc.content, query_terms),
#                     'type': doc.document_type
#                 })
        
#         results.sort(key=lambda x: x['score'], reverse=True)
#         return results[:5]
    
#     def _extract_keywords(self, text):
#         """Extrait et nettoie les mots-clés de la requête"""
#         text = text.lower()
#         words = re.findall(r'\b[a-zéèêàâûîïôùüç]+\b', text)
        
#         stop_words = {
#             'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles',
#             'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'à',
#             'pour', 'avec', 'dans', 'sur', 'par', 'est', 'sont', 'ai',
#             'as', 'a', 'avons', 'avez', 'ont', 'veux', 'vouloir', 'faire',
#             'mon', 'ma', 'mes', 'ton', 'ta', 'tes', 'son', 'sa', 'ses'
#         }
        
#         meaningful_words = []
#         for word in words:
#             if len(word) > 2 and word not in stop_words:
#                 weight = self.keyword_weights.get(word, 1)
#                 meaningful_words.extend([word] * weight)
        
#         return list(set(meaningful_words))
    
#     def _calculate_relevance(self, document, query_terms, user_level):
#         """Calcule la pertinence d'un document pour la requête"""
#         score = 0
#         content_lower = document.content.lower()
#         title_lower = document.title.lower()
        
#         # Vérifier les mots-clés définis dans le document
#         for keyword in document.keywords:
#             keyword_lower = keyword.lower()
#             for term in query_terms:
#                 if term in keyword_lower or keyword_lower in term:
#                     score += 3
        
#         # Vérifier le titre
#         for term in query_terms:
#             if term in title_lower:
#                 score += 2
        
#         # Vérifier le contenu
#         for term in query_terms:
#             if term in content_lower:
#                 score += 1
        
#         # Bonus pour le niveau utilisateur
#         if user_level == 'debutant' and any(word in content_lower for word in ['débutant', 'commencer', 'premier']):
#             score += 2
#         elif user_level == 'intermediaire' and any(word in content_lower for word in ['intermédiaire', 'progresser', 'niveau']):
#             score += 2
        
#         return score
    
#     def _get_excerpt(self, content, query_terms, length=150):
#         """Extrait un passage pertinent du contenu"""
#         content_lower = content.lower()
        
#         for term in query_terms:
#             if term in content_lower:
#                 index = content_lower.find(term)
#                 start = max(0, index - 30)
#                 end = min(len(content), start + length)
                
#                 excerpt = content[start:end]
#                 if start > 0:
#                     excerpt = "..." + excerpt
#                 if end < len(content):
#                     excerpt = excerpt + "..."
                
#                 return excerpt
        
#         return content[:length] + "..."