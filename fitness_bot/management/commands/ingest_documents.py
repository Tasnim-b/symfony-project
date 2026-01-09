from django.core.management.base import BaseCommand
import os
from django.conf import settings
from fitness_bot.models import KnowledgeDocument
from langchain_community.document_loaders import PyPDFLoader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.vectorstores import Chroma
from langchain_community.embeddings import SentenceTransformerEmbeddings

class Command(BaseCommand):
    help = 'Ingest PDFs from documents/ folder into ChromaDB'

    def handle(self, *args, **options):
        self.stdout.write("Starting document ingestion...")

        # 1. Setup paths
        docs_dir = os.path.join(settings.BASE_DIR, 'documents')
        persist_dir = os.path.join(settings.BASE_DIR, 'chroma_db')
        
        # 2. Setup Embedding Function (using local model)
        embedding_function = SentenceTransformerEmbeddings(model_name="all-MiniLM-L6-v2")

        # 3. Process files
        all_splits = []
        for root, dirs, files in os.walk(docs_dir):
            for file in files:
                if file.endswith('.pdf'):
                    file_path = os.path.join(root, file)
                    self.stdout.write(f"Processing {file}...")
                    
                    # Load PDF
                    loader = PyPDFLoader(file_path)
                    docs = loader.load()
                    
                    # Add metadata
                    doc_type = 'general'
                    if 'nutrition' in root: doc_type = 'nutrition'
                    elif 'fitness' in root: doc_type = 'fitness'
                    elif 'motivation' in root: doc_type = 'motivation'
                    elif 'sante' in root: doc_type = 'sante'

                    for doc in docs:
                        doc.metadata['source_file'] = file
                        doc.metadata['doc_type'] = doc_type
                        doc.metadata['title'] = file.replace('.pdf', '').replace('-', ' ')

                    # Split text
                    text_splitter = RecursiveCharacterTextSplitter(
                        chunk_size=1000,
                        chunk_overlap=200
                    )
                    splits = text_splitter.split_documents(docs)
                    all_splits.extend(splits)
                    
                    # Update Django Model (optional tracking)
                    KnowledgeDocument.objects.get_or_create(
                        title=file,
                        defaults={
                            'content': f"Imported via RAG script. See ChromaDB for content. {len(splits)} chunks.",
                            'document_type': doc_type
                        }
                    )

        if not all_splits:
            self.stdout.write(self.style.WARNING("No PDF documents found or processed."))
            return

        # 4. Create/Update Vector Store
        self.stdout.write(f"Creating vector store with {len(all_splits)} chunks...")
        Chroma.from_documents(
            documents=all_splits,
            embedding=embedding_function,
            persist_directory=persist_dir
        )

        self.stdout.write(self.style.SUCCESS(f"Successfully ingested {len(all_splits)} chunks into ChromaDB!"))
