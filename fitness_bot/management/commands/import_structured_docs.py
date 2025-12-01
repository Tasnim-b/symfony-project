# fitness_bot/management/commands/import_structured_docs.py
import os
import glob
from django.core.management.base import BaseCommand
from fitness_bot.models import KnowledgeDocument

class Command(BaseCommand):
    help = 'Importe la documentation depuis les dossiers fitness/sante/nutrition/motivation'
    
    def handle(self, *args, **options):
        base_dir = 'documents'
        
        # Mapping direct : nom_dossier -> type_document
        folder_mapping = {
            'fitness': 'fitness',
            'sante': 'sante', 
            'nutrition': 'nutrition',
            'motivation': 'motivation'
        }
        
        total_imported = 0
        
        for folder_name, doc_type in folder_mapping.items():
            folder_path = os.path.join(base_dir, folder_name)
            
            if not os.path.exists(folder_path):
                self.stdout.write(self.style.WARNING(f"⚠️ Dossier manquant: {folder_path}"))
                continue
                
            # Lister tous les fichiers .txt dans le dossier
            txt_files = glob.glob(os.path.join(folder_path, "*.txt"))
            
            if not txt_files:
                self.stdout.write(self.style.WARNING(f"⚠️ Aucun fichier .txt dans: {folder_path}"))
                continue
            
            self.stdout.write(f"\n📁 Import depuis: {folder_name} -> {doc_type}")
            
            for file_path in txt_files:
                try:
                    with open(file_path, 'r', encoding='utf-8') as file:
                        content = file.read().strip()
                        
                        if not content:
                            self.stdout.write(self.style.WARNING(f"  ⚠️ Fichier vide: {os.path.basename(file_path)}"))
                            continue
                        
                        # Extraire le titre du nom du fichier
                        filename = os.path.basename(file_path)
                        title = self._filename_to_title(filename)
                        
                        # Vérifier si le document existe déjà
                        existing_doc = KnowledgeDocument.objects.filter(title=title).first()
                        
                        if existing_doc:
                            self.stdout.write(f"  🔄 Mis à jour: {title}")
                            existing_doc.content = content
                            existing_doc.document_type = doc_type
                            existing_doc.keywords = self._extract_keywords(content, title)
                            existing_doc.save()
                        else:
                            KnowledgeDocument.objects.create(
                                title=title,
                                content=content,
                                document_type=doc_type,
                                keywords=self._extract_keywords(content, title)
                            )
                            self.stdout.write(f"  ✅ Créé: {title}")
                            
                        total_imported += 1
                        
                except Exception as e:
                    self.stdout.write(self.style.ERROR(f"  ❌ Erreur avec {file_path}: {str(e)}"))
        
        self.stdout.write(self.style.SUCCESS(f"\n🎯 Import terminé ! {total_imported} documents traités."))
    
    def _filename_to_title(self, filename):
        """Convertit le nom de fichier en titre lisible"""
        name_without_ext = os.path.splitext(filename)[0]
        title = name_without_ext.replace('_', ' ').title()
        return title
    
    def _extract_keywords(self, content, title):
        """Extrait les mots-clés du contenu et du titre"""
        import re
        from collections import Counter
        
        text = f"{title} {content}"
        words = re.findall(r'\b[a-zéèêàâûîïôùüç]{4,}\b', text.lower())
        
        stop_words = {
            'dans', 'pour', 'avec', 'débutant', 'sport', 'santé', 'conseils',
            'comment', 'quels', 'quelles', 'pendant', 'après', 'avant', 'sous',
            'sur', 'entre', 'vers', 'depuis', 'pendant', 'chaque', 'tous', 'tout',
            'plus', 'fait', 'être', 'avoir', 'faire', 'voir', 'dire', 'grand'
        }
        
        meaningful_words = [word for word in words if word not in stop_words]
        word_counts = Counter(meaningful_words)
        return [word for word, count in word_counts.most_common(8)]