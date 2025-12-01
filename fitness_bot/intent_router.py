class IntentRouter:
    def __init__(self):
        self.intents = {
            "greeting": ["bonjour","salut","hello","coucou","bonsoir"],
            "goodbye": ["au revoir","bye","ciao","à plus"],
            "prise_muscle": ["prise de poids","prendre du muscle","masse","hypertrophie"],
            "perte_poids": ["perdre du poids","maigrir","mincir"],
            "nutrition": ["nutrition","manger","repas","alimentation","calories"],
            "entrainement": ["entraînement","exercice","sport","musculation"],
            "motivation": ["motivation","motiver","discipline","objectifs"]
        }

        self.intent_to_doc_type = {
            "prise_muscle": "fitness",
            "perte_poids": "fitness",
            "nutrition": "nutrition",
            "entrainement": "fitness",
            "motivation": "motivation",
        }

    def detect_intent(self, message):
        message = message.lower()

        for intent, keywords in self.intents.items():
            if any(k in message for k in keywords):
                return intent
        
        return "unknown"

    def route_document_type(self, intent):
        return self.intent_to_doc_type.get(intent, None)
