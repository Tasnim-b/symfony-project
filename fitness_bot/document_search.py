import os
from django.conf import settings
from langchain_community.vectorstores import Chroma
from langchain_community.embeddings import SentenceTransformerEmbeddings

class DocumentSearch:
    _instance = None
    _vectordb = None

    def __new__(cls):
        if cls._instance is None:
            cls._instance = super(DocumentSearch, cls).__new__(cls)
            cls._instance._initialize_db()
        return cls._instance

    def _initialize_db(self):
        persist_dir = os.path.join(settings.BASE_DIR, 'chroma_db')
        embedding_function = SentenceTransformerEmbeddings(model_name="all-MiniLM-L6-v2")
        
        # Initialize ChromaDB
        self._vectordb = Chroma(
            persist_directory=persist_dir,
            embedding_function=embedding_function
        )

    def search(self, query, doc_type=None, k=4):
        """
        Search for documents relevant to the query.
        filter by doc_type if provided.
        """
        filter_dict = {}
        if doc_type:
            filter_dict = {"doc_type": doc_type}

        # Perform similarity search
        # We handle the case where DB might be empty or filter returns nothing gracefully?
        # ChromaDB handles empty results by returning empty list
        
        if filter_dict:
             results = self._vectordb.similarity_search_with_score(query, k=k, filter=filter_dict)
        else:
             results = self._vectordb.similarity_search_with_score(query, k=k)

        # Format results
        formatted_results = []
        for doc, score in results:
            formatted_results.append({
                "document": {"title": doc.metadata.get("title", "Unknown")},
                "score": score, # Note: Chroma distance, lower is better usually, depending on metric
                "excerpt": doc.page_content,
                "metadata": doc.metadata
            })
            
        return formatted_results