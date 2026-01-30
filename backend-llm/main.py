from fastapi import FastAPI, Request, HTTPException
from fastapi.responses import JSONResponse, StreamingResponse
from fastapi.middleware.cors import CORSMiddleware
from langdetect import detect 
import aiofiles
import httpx
import os
import logging
import traceback
import json
import asyncio
import time
import langid
import uuid
from typing import  List, Dict, Any, AsyncGenerator
from contextlib import asynccontextmanager
from datetime import datetime

logger = logging.getLogger(__name__)

# === CONSTANTS ===
ETUDE_MARCHE_TEXT = "étude de marché"
ETAPE_3_TEXT = "étape 3"
COMMENT_FAIRE_TEXT = "comment faire"
SESSION_NOT_FOUND_TEXT = "Session non trouvée"

# === CONFIGURATION ULTRA-OPTIMISÉE ===
OLLAMA_MODEL = os.getenv("LLM_MODEL", "llama3")
OLLAMA_BASE = os.getenv("OLLAMA_API", "http://127.0.0.1:11434")
OLLAMA_URL = f"{OLLAMA_BASE}/api/generate"

# Configuration des timeouts ultra-rapides
TIMEOUT_CONFIG = httpx.Timeout(
    connect=10.0,    # Plus de temps pour la connexion
    read=45.0,       # Plus de temps pour lire (important pour streaming)
    write=5.0,       
    pool=3.0         
)

# Client HTTP réutilisable optimisé
HTTP_CLIENT = None

# === STOCKAGE DE L'HISTORIQUE ===
CONVERSATION_HISTORY = {}  # {session_id: {messages: [], metadata: {}}}
MAX_CONVERSATIONS = 100
MAX_MESSAGES_PER_CONVERSATION = 50

# === CACHE ULTRA-RAPIDE AVEC PRE-COMPUTATION ===
PROMPT_CACHE = {}
PRECOMPUTED_PROMPTS = {}
CACHE_MAX_SIZE = 30




# === GESTION DE L'HISTORIQUE ===
def create_session() -> str:
    """Crée une nouvelle session de conversation"""
    session_id = str(uuid.uuid4())
    CONVERSATION_HISTORY[session_id] = {
        "messages": [],
        "metadata": {
            "created_at": datetime.now().isoformat(),
            "last_activity": datetime.now().isoformat(),
            "message_count": 0
        }
    }
    
    # Nettoyage automatique si trop de conversations
    if len(CONVERSATION_HISTORY) > MAX_CONVERSATIONS:
        oldest_session = min(CONVERSATION_HISTORY.keys(), 
                           key=lambda k: CONVERSATION_HISTORY[k]["metadata"]["last_activity"])
        del CONVERSATION_HISTORY[oldest_session]
        logger.info(f"🧹 Cleaned old session: {oldest_session}")
    
    return session_id

def add_message_to_history(session_id: str, message: str, sender: str, intent: str = None, lang: str = None):
    """Ajoute un message à l'historique de la session"""
    if session_id not in CONVERSATION_HISTORY:
        return False
    
    conversation = CONVERSATION_HISTORY[session_id]
    
    # Limite le nombre de messages par conversation
    if len(conversation["messages"]) >= MAX_MESSAGES_PER_CONVERSATION:
        conversation["messages"].pop(0)  # Supprime le plus ancien
    
    message_data = {
        "id": str(uuid.uuid4()),
        "content": message,
        "sender": sender,  # "user" ou "assistant"
        "timestamp": datetime.now().isoformat(),
        "intent": intent,
        "language": lang
    }
    
    conversation["messages"].append(message_data)
    conversation["metadata"]["last_activity"] = datetime.now().isoformat()
    conversation["metadata"]["message_count"] = len(conversation["messages"])
    
    return True

def get_conversation_history(session_id: str, limit: int = 10) -> List[Dict]:
    """Récupère l'historique d'une conversation"""
    if session_id not in CONVERSATION_HISTORY:
        return []
    
    messages = CONVERSATION_HISTORY[session_id]["messages"]
    return messages[-limit:] if limit else messages

def get_formatted_history_context(session_id: str, limit: int = 5) -> str:
    """Génère un contexte formaté amélioré"""
    messages = get_conversation_history(session_id, limit)
    if not messages:
        return ""
    
    context_lines = []
    for msg in messages[-limit:]:  # Prendre plus de messages
        sender = "User" if msg["sender"] == "user" else "BrainBot"
        content = msg["content"][:120]  # Plus de contexte par message
        intent_info = f" [{msg.get('intent', 'unknown')}]" if msg.get('intent') else ""
        context_lines.append(f"{sender}{intent_info}: {content}")
    
    return "Contexte conversation:\n" + "\n".join(context_lines) + "\n\n"

# === DETECTION LANGUAGE ULTRA-RAPIDE ===
QUICK_KEYWORDS = {
    "fr": {"salut", "bonjour", "bonsoir", "coucou", "bnjr", "comment", "que", "quoi"},
    "en": {"hello", "hi", "hey", "what", "how", "yo", "sup"},
}

def detect_language_ultra_fast(text: str) -> str:
    """Détection de langue ultra-rapide avec cache"""
    text_lower = text.strip().lower()[:50]
    
    text_hash = hash(text_lower)
    if text_hash in PRECOMPUTED_PROMPTS:
        return PRECOMPUTED_PROMPTS[text_hash].get('lang', 'fr')
    
    for lang, keywords in QUICK_KEYWORDS.items():
        if any(kw in text_lower for kw in keywords):
            return lang
    
    return "fr"

# === DETECTION INTENT ULTRA-RAPIDE AMÉLIORÉE ===
INTENT_KEYWORDS = {
    "liste_etapes_programme": ["étapes", "steps", "programme", "program", "12", "douze", "liste"],
    "marketplace_info": ["marketplace", "investisseur", "investor", "startup"],
    "networking_espace": ["networking", "réseau", "network", "entrepreneur"],
    "prise_de_rdv": ["rendez-vous", "rdv", "appointment", "meeting"],
    "objectif_du_programme": ["objectif", "goal", "but", "pourquoi"],
    "historique": ["historique", "history", "conversation", "précédent"],
    "etude_marche_info": [ETUDE_MARCHE_TEXT, "market study", ETAPE_3_TEXT, "step 3"],
    "demande_exemples": ["exemples", "examples", COMMENT_FAIRE_TEXT, "montre-moi", "montrez-moi"]
}

def detect_intent_ultra_fast(text: str, session_id: str = None) -> str:
    """Détection d'intent améliorée avec contexte de conversation"""
    text_lower = text.lower()
    
    # Vérifier le contexte récent pour mieux comprendre
    if session_id and session_id in CONVERSATION_HISTORY:
        recent_messages = get_conversation_history(session_id, 3)
        context_text = " ".join([msg["content"].lower() for msg in recent_messages[-2:]])
        
        # Si on parle d'étapes et qu'il demande des exemples
        if any(word in context_text for word in ["étape", "step"]) and any(word in text_lower for word in ["exemple", "examples", COMMENT_FAIRE_TEXT]):
            return "demande_exemples_etape"
        
        # Si on parle d'étude de marché spécifiquement
        if any(word in context_text for word in [ETUDE_MARCHE_TEXT, "market study", ETAPE_3_TEXT]):
            return "etude_marche_info"
    
    # Détection normale
    for intent, keywords in INTENT_KEYWORDS.items():
        if any(kw in text_lower for kw in keywords):
            return intent
    
    if any(kw in text_lower for kw in ["exemples", "examples", COMMENT_FAIRE_TEXT, "montre-moi"]):
        return "demande_exemples"
    
    return "general"

# === PRE-COMPUTED PROMPTS ===
def init_precomputed_prompts():
    """Initialise les prompts pré-calculés"""
    global PRECOMPUTED_PROMPTS
    
    PRECOMPUTED_PROMPTS.update({
        hash("bonjour"): {
            'lang': 'fr',
            'intent': 'general',
            'prompt': "Tu es BrainBot, assistant IA de BraindCode Startup Studio. Spécialiste du programme 12 étapes, marketplace startup-investisseurs, et networking entrepreneurs.\n\nUser: bonjour\nBrainBot:"
        },
        hash("hello"): {
            'lang': 'en', 
            'intent': 'general',
            'prompt': "You are BrainBot, AI assistant for BraindCode Startup Studio. Specialist in 12-step program, startup-investor marketplace, and entrepreneur networking.\n\nUser: hello\nBrainBot:"
        },
        hash("salut"): {
            'lang': 'fr',
            'intent': 'general', 
            'prompt': "Tu es BrainBot, assistant IA de BraindCode Startup Studio. Spécialiste du programme 12 étapes, marketplace startup-investisseurs, et networking entrepreneurs.\n\nUser: salut\nBrainBot:"
        }
    })

# === FONCTION POUR CRÉER LES EXEMPLES D'ÉTUDE DE MARCHÉ ===
def create_market_study_examples():
    """Crée les exemples d'étude de marché"""
    return """Exemples concrets pour l'Étape 3 - Étude de marché:

**Exemple 1: Application de livraison de repas**
- Analyser la taille du marché local (combien de restaurants, de clients potentiels)
- Étudier la concurrence (Uber Eats, Deliveroo, etc.)
- Interroger 100+ clients potentiels sur leurs habitudes
- Analyser les prix pratiqués et la demande par quartier

**Exemple 2: SaaS pour PME**
- Identifier le nombre de PME dans votre secteur cible
- Analyser les solutions existantes et leurs lacunes
- Conduire des interviews avec 20+ dirigeants de PME
- Estimer le budget moyen que les PME consacrent à ce type d'outil

**Méthodes pratiques:**
- Enquêtes en ligne (Google Forms, Typeform)
- Interviews téléphoniques ou en personne
- Analyse de la concurrence directe et indirecte
- Études des tendances du secteur (rapports, statistiques)
- Test de concepts avec des prototypes simples"""

# === FONCTION POUR CRÉER LES ÉTAPES DU PROGRAMME ===
def create_program_steps():
    """Crée la liste des étapes du programme"""
    return """Les 12 étapes BraindCode:
1. Validation d'idée - Étude approfondie de l'idée
2. Comité d'évaluation - Évaluation par des experts
3. Étude de marché - Analyse du marché cible
4. Business model - Définition du modèle économique
5. Plan stratégique - Établissement du plan stratégique
6. Pitch deck - Création de la présentation
7. Prototype - Mise au point du prototype
8. Processus opérationnels - Établissement des processus
9. Recrutement équipe - Formation de l'équipe
10. MVP - Produit minimum viable
11. Feedback utilisateurs - Amélioration continue
12. Scale-up - Lancement commercial"""

# === PROMPT BUILDING AVEC HISTORIQUE CORRIGÉ ===
# === PROMPT BUILDING REFACTORISÉ ===

def _get_base_context(lang: str) -> str:
    """Retourne le contexte de base selon la langue"""
    contexts = {
        "fr": "Tu es BrainBot, assistant IA de BraindCode Startup Studio. Tu aides avec le programme d'incubation 12 étapes.",
        "en": "You are BrainBot, AI assistant for BraindCode Startup Studio. You help with the 12-step incubation program."
    }
    return contexts.get(lang, contexts["fr"])

def _get_history_context(session_id: str) -> str:
    """Génère le contexte d'historique pour une session"""
    if not session_id or session_id not in CONVERSATION_HISTORY:
        return ""
    
    messages = get_conversation_history(session_id, 5)
    if not messages:
        return ""
    
    history_lines = []
    for msg in messages[-4:]:  # 4 derniers messages
        sender = "User" if msg["sender"] == "user" else "BrainBot"
        content = msg["content"][:150]  # Plus de contexte
        history_lines.append(f"{sender}: {content}")
    
    return "Conversation récente:\n" + "\n".join(history_lines) + "\n\n"

def _should_redirect_to_market_study(session_id: str) -> bool:
    """Vérifie si on doit rediriger vers les exemples d'étude de marché"""
    if not session_id:
        return False
    
    recent_context = " ".join([
        msg["content"].lower() 
        for msg in get_conversation_history(session_id, 3)
    ])
    
    return (ETUDE_MARCHE_TEXT in recent_context or 
            ETAPE_3_TEXT in recent_context)

def _build_intent_specific_prompt(intent: str, context: str, history_context: str, message: str) -> str:
    """Construit le prompt selon l'intent spécifique"""
    
    if intent == "liste_etapes_programme":
        etapes_fr = create_program_steps()
        return f"{context}\n\n{history_context}{etapes_fr}\n\nUser: {message}\nBrainBot:"
    
    if intent in ["demande_exemples_etape", "etude_marche_info"]:
        exemples_etude_marche = create_market_study_examples()
        return f"{context}\n\n{history_context}{exemples_etude_marche}\n\nUser: {message}\nBrainBot:"
    
    if intent == "demande_exemples":
        return f"{context}\n\n{history_context}L'utilisateur demande des exemples. Basez-vous sur le contexte de la conversation pour donner des exemples pertinents.\n\nUser: {message}\nBrainBot:"
    
    # Prompt général par défaut
    return f"{context}\n\n{history_context}Réponds de manière utile en tenant compte du contexte de la conversation.\n\nUser: {message}\nBrainBot:"

def _manage_cache(cache_key: str, prompt: str) -> str:
    """Gère le cache des prompts"""
    if len(PROMPT_CACHE) >= CACHE_MAX_SIZE:
        oldest_key = next(iter(PROMPT_CACHE))
        del PROMPT_CACHE[oldest_key]
    
    PROMPT_CACHE[cache_key] = prompt
    return prompt

def build_prompt_with_history(message: str, lang: str = "fr", intent: str = "general", session_id: str = None) -> str:
    """Construction de prompt améliorée avec vrai contexte conversationnel"""
    
    # Vérifier le cache
    cache_key = f"{hash(message[:50])}_{lang}_{intent}_{session_id}"
    if cache_key in PROMPT_CACHE:
        return PROMPT_CACHE[cache_key]
    
    # Gestion spéciale pour redirection vers étude de marché
    if intent == "demande_exemples" and _should_redirect_to_market_study(session_id):
        return build_prompt_with_history(message, lang, "etude_marche_info", session_id)
    
    # Construire les contextes
    base_context = _get_base_context(lang)
    history_context = _get_history_context(session_id)
    
    # Construire le prompt selon l'intent
    prompt = _build_intent_specific_prompt(intent, base_context, history_context, message)
    
    # Gérer le cache et retourner
    return _manage_cache(cache_key, prompt)

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Gestion du cycle de vie optimisée"""
    global HTTP_CLIENT
    
    logging.info("🚀 Starting BraindCode backend ULTRA-FAST with History...")
    
    init_precomputed_prompts()
    load_trained_intents_txt()
    
    HTTP_CLIENT = httpx.AsyncClient(
        timeout=TIMEOUT_CONFIG,
        limits=httpx.Limits(
            max_keepalive_connections=3,  
            max_connections=6,           
            keepalive_expiry=120.0       
        )
    )
    
    # Either await the warmup or remove the assignment
    await warmup_minimal()
    
    logging.info("✅ BraindCode backend ULTRA-FAST ready!")
    
    yield
    
    if HTTP_CLIENT:
        await HTTP_CLIENT.aclose()

app = FastAPI(lifespan=lifespan, title="BraindCode AI Backend ULTRA-FAST with History", version="3.2.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

logging.basicConfig(level=logging.WARNING)
logger = logging.getLogger(__name__)

# === HELPERS FOR STREAMING ===
def handle_json_decode_error(line: str, e: Exception) -> None:
    """Handle JSON decode errors in streaming"""
    logger.warning(f"JSON decode error: {str(e)} - Line: {line[:100]}")

def handle_line_processing_error(e: Exception) -> None:
    """Handle line processing errors"""
    logger.error(f"Error processing line: {str(e)}")

def should_yield_chunk(token_count: int, chunk_buffer: str) -> bool:
    """Determine if chunk should be yielded"""
    return token_count % 3 == 0 or chunk_buffer.endswith((' ', '.', '!', '?', '\n', ','))

# === STREAMING OPTIMISÉ AVEC GESTION D'ERREURS ===
class StreamResponseError(Exception):
    """Custom exception for stream response errors"""
    def __init__(self, message: str, error_type: str, retry: bool = True, **kwargs):
        super().__init__(message)
        self.error_type = error_type
        self.retry = retry
        self.extra_data = kwargs

class StreamProcessor:
    """Handles streaming response processing with reduced complexity"""
    
    def __init__(self, max_retries: int = 2):
        self.max_retries = max_retries
        self.chunk_buffer = ""
        self.token_count = 0
        self.has_content = False

    def create_error_response(self, error: StreamResponseError) -> str:
        """Create standardized error response"""
        response = {
            "error": str(error),
            "retry": error.retry,
            "error_type": error.error_type
        }
        response.update(error.extra_data)
        return json.dumps(response) + "\n"

    def create_success_response(self, content: str, done: bool = False) -> str:
        """Create standardized success response"""
        return json.dumps({
            "response": content,
            "done": done,
            "has_content": True
        }) + "\n"

    def process_json_line(self, line: str) -> Dict[str, Any]:
        """Process a single JSON line from stream"""
        if not line.strip():
            return {}
        
        try:
            return json.loads(line)
        except json.JSONDecodeError as e:
            handle_json_decode_error(line, e)
            return {}

    def process_token(self, parsed_data: Dict[str, Any]) -> bool:
        """Process a single token, returns True if should yield"""
        if parsed_data.get("done", False):
            return True
        
        token = parsed_data.get("response", "")
        if not token:
            return False
        
        self.chunk_buffer += token
        self.token_count += 1
        self.has_content = True
        
        return should_yield_chunk(self.token_count, self.chunk_buffer)

    def get_final_response(self) -> str:
        """Get final response or error if no content"""
        if self.chunk_buffer.strip():
            return self.create_success_response(self.chunk_buffer, done=True)
        elif not self.has_content:
            error = StreamResponseError(
                "Aucune réponse reçue",
                "no_content",
                done=True,
                has_content=False
            )
            return self.create_error_response(error)
        return ""

    def reset_buffer(self):
        """Reset chunk buffer after yielding"""
        self.chunk_buffer = ""

async def handle_stream_response(response) -> AsyncGenerator[str, None]:
    """Handle streaming response with reduced complexity"""
    processor = StreamProcessor()
    
    try:
        async for chunk in response.aiter_text():
            async for result in _process_chunk(processor, chunk):
                yield result
        
        # Handle any remaining content
        final_response = processor.get_final_response()
        if final_response:
            yield final_response
            
    except Exception as e:
        logger.error(f"Stream processing error: {str(e)}")
        error = StreamResponseError(
            "Erreur de traitement du stream",
            "stream_processing_error"
        )
        yield processor.create_error_response(error)


async def _process_chunk(processor: StreamProcessor, chunk: str) -> AsyncGenerator[str, None]:
    """Process a single chunk and yield results"""
    if not chunk or not chunk.strip():
        return
    
    for line in chunk.split("\n"):
        async for result in _process_line(processor, line):
            yield result


async def _process_line(processor: StreamProcessor, line: str) -> AsyncGenerator[str, None]:
    """Process a single line and yield results"""
    parsed_data = processor.process_json_line(line)
    if not parsed_data:
        return
    
    if not processor.process_token(parsed_data):
        return
    
    if parsed_data.get("done", False):
        async for result in _handle_completion(processor):
            yield result
        return
    
    # Handle normal token
    yield processor.create_success_response(processor.chunk_buffer)
    processor.reset_buffer()
    
    if processor.token_count < 15:
        await asyncio.sleep(0.01)


async def _handle_completion(processor: StreamProcessor) -> AsyncGenerator[str, None]:
    """Handle stream completion"""
    if processor.chunk_buffer.strip():
        yield processor.create_success_response(processor.chunk_buffer, done=True)
    else:
        error = StreamResponseError(
            "Réponse vide reçue",
            "empty_response",
            done=True,
            has_content=False
        )
        yield processor.create_error_response(error)

async def make_request_with_retry(payload: Dict[str, Any], attempt: int) -> AsyncGenerator[str, None]:
    """Make HTTP request with specific attempt handling"""
    try:
        async with HTTP_CLIENT.stream("POST", OLLAMA_URL, json=payload) as response:
            if response.status_code != 200:
                error_msg = f"Ollama error: {response.status_code}"
                logger.error(f"Attempt {attempt + 1}: {error_msg}")
                
                error = StreamResponseError(
                    "Service indisponible temporairement",
                    "service_unavailable",
                    status_code=response.status_code
                )
                yield StreamProcessor().create_error_response(error)
                return
            
            # Stream successful response
            async for chunk in handle_stream_response(response):
                yield chunk
                
    except Exception as e:
        await handle_request_exception(e, attempt)

async def handle_request_exception(exception: Exception, attempt: int) -> AsyncGenerator[str, None]:
    """Handle specific request exceptions"""
    max_retries = 2
    
    if isinstance(exception, httpx.TimeoutException):
        logger.error(f"Ollama timeout - Attempt {attempt + 1}")
        if attempt == max_retries - 1:
            error = StreamResponseError(
                "Le service met trop de temps à répondre",
                "timeout"
            )
            yield StreamProcessor().create_error_response(error)
        else:
            await asyncio.sleep(2)
            
    elif isinstance(exception, httpx.ConnectError):
        logger.error(f"Ollama connection error - Attempt {attempt + 1}")
        if attempt == max_retries - 1:
            error = StreamResponseError(
                "Impossible de se connecter au service",
                "connection_error"
            )
            yield StreamProcessor().create_error_response(error)
        else:
            await asyncio.sleep(1)
    else:
        error = StreamResponseError(
            "Erreur technique",
            "general_error"
        )
        yield StreamProcessor().create_error_response(error)

async def stream_response_ultra_optimized(payload: Dict[str, Any]) -> AsyncGenerator[str, None]:
    """Refactored version with reduced cognitive complexity"""
    
    if HTTP_CLIENT is None:
        logger.error("❌ HTTP_CLIENT non initialisé")
        error = StreamResponseError(
            "Service indisponible",
            "client_not_initialized"
        )
        yield StreamProcessor().create_error_response(error)
        return

    max_retries = 2
    
    for attempt in range(max_retries):
        try:
            async for chunk in make_request_with_retry(payload, attempt):
                yield chunk
            return  # Success, exit retry loop
            
        except (httpx.TimeoutException, httpx.ConnectError) as e:
            await handle_request_exception(e, attempt)
            if attempt < max_retries - 1:
                continue
            
        except Exception as e:
            logger.error(f"Streaming error: {str(e)}")
            error = StreamResponseError(
                "Erreur technique",
                "general_error",
                details=str(e)
            )
            yield StreamProcessor().create_error_response(error)
            return
# === WARMUP MINIMAL ===
async def warmup_minimal():
    """Warmup minimal et asynchrone"""
    try:
        payload = {
            "model": OLLAMA_MODEL,
            "prompt": "Test",
            "stream": False,
            "options": {"num_predict": 1}
        }
        response = await HTTP_CLIENT.post(OLLAMA_URL, json=payload, timeout=10)
        if response.status_code == 200:
            logger.info("✅ Warmup OK")
        else:
            logger.warning(f"⚠️ Warmup failed with status {response.status_code}")
    except Exception as e:
        logger.warning(f"⚠️ Warmup failed: {str(e)}")

# === REFACTORED COMPLEX FUNCTIONS ===
def process_retry_logic(accumulated_response: str, response_received: bool, session_id: str, 
                       retry_attempt: int, intent: str, lang: str):
    """Process retry logic for response handling"""
    if accumulated_response.strip() and response_received:
        # Supprimer l'ancienne réponse en cas de retry
        if retry_attempt > 0:
            conv_messages = CONVERSATION_HISTORY[session_id]["messages"]
            if conv_messages and conv_messages[-1]["sender"] == "assistant":
                conv_messages[-1]["content"] = accumulated_response.strip()
            else:
                add_message_to_history(session_id, accumulated_response.strip(), "assistant", intent, lang)
        else:
            add_message_to_history(session_id, accumulated_response.strip(), "assistant", intent, lang)

def validate_session_and_message(session_id: str, data: Dict[str, Any]) -> tuple:
    """Validate session and message from request data"""
    message = data.get("message", "").strip()
    if not message or len(message) > 500:
        raise HTTPException(status_code=400, detail="Message invalide ou trop long")
    
    retry_attempt = data.get("retry_attempt", 0)
    
    if not session_id or session_id not in CONVERSATION_HISTORY:
        session_id = create_session()
        logger.info(f"🆕 New session created: {session_id}")
    
    return message, retry_attempt, session_id

def create_payload(prompt: str) -> Dict[str, Any]:
    """Create payload for Ollama API"""
    return {
        "model": OLLAMA_MODEL,
        "prompt": prompt,
        "stream": True,
        "keep_alive": 60,
        "options": {
            "num_ctx": 2048,
            "temperature": 0.4,
            "top_p": 0.9,
            "repeat_penalty": 1.1,
            "num_predict": 512
        }
    }

# === ENDPOINTS ===

# === AMÉLIORATION DE L'ENDPOINT CHAT-STREAM ===
async def process_chat_request(data: dict, session_id: str = None) -> tuple[str, str, str, int, str]:
    """Extract and validate chat request data"""
    if session_id is None:
        session_id = data.get("session_id")
    
    message, retry_attempt, session_id = validate_session_and_message(session_id, data)
    
    lang = detect_language_ultra_fast(message)
    intent = detect_intent_ultra_fast(message, session_id)
    
    # Add user message to history only if not a retry
    if retry_attempt == 0:
        add_message_to_history(session_id, message, "user", intent, lang)
    
    return message, lang, intent, retry_attempt, session_id



async def process_streaming_chunk(chunk: str, accumulated_response: str) -> tuple[str, bool, str]:
    """Process individual streaming chunk"""
    try:
        chunk_data = json.loads(chunk)
        
        # Handle errors
        if "error" in chunk_data:
            return accumulated_response, False, chunk
        
        # Handle normal responses
        if "response" in chunk_data:
            accumulated_response += chunk_data["response"]
            return accumulated_response, True, chunk
            
        return accumulated_response, False, chunk
        
    except json.JSONDecodeError:
        return accumulated_response, False, chunk


def create_session_info_response(session_id: str, intent: str, retry_attempt: int, response_received: bool) -> str:
    """Create session info response"""
    return json.dumps({
        "session_info": {
            "session_id": session_id, 
            "message_count": len(CONVERSATION_HISTORY[session_id]["messages"]),
            "intent_detected": intent,
            "retry_attempt": retry_attempt,
            "response_received": response_received
        }
    }) + "\n"


def create_error_response(error_message: str, error_type: str) -> str:
    """Create standardized error response"""
    return json.dumps({
        "error": error_message, 
        "retry": True,
        "error_type": error_type,
        "details": str(error_message)
    }) + "\n"


async def create_enhanced_stream_generator(payload: dict, session_id: str, retry_attempt: int, intent: str, lang: str):
    """Create the enhanced streaming generator"""
    accumulated_response = ""
    response_received = False
    
    try:
        async for chunk in stream_response_ultra_optimized(payload):
            accumulated_response, chunk_received, processed_chunk = await process_streaming_chunk(chunk, accumulated_response)
            
            if chunk_received:
                response_received = True
                
            yield processed_chunk
            
            # Early return if error chunk
            if "error" in processed_chunk:
                return
        
        # Save complete response only if content was received
        process_retry_logic(accumulated_response, response_received, session_id, 
                          retry_attempt, intent, lang)
        
        # Send session information
        yield create_session_info_response(session_id, intent, retry_attempt, response_received)
        
    except Exception as e:
        logger.error(f"Enhanced streaming error: {str(e)}")
        yield create_error_response("Erreur de streaming", "streaming_error")

@app.get("/health-check")
async def health_check_alias():
    return await health()

@app.get("/intentions")
async def list_intentions():
    return {"intentions": [], "message": "Use POST /intentions/train to add examples."}

@app.get("/chat-stream")
async def chat_stream_wrong_method():
    raise HTTPException(status_code=405, detail="Use POST /chat-stream")

@app.post("/chat-stream")
async def chat_stream_with_history(request: Request):
    """Endpoint principal avec gestion d'historique et retry améliorée"""
    start_time = time.time()
    
    try:
        body = await request.body()
        data = json.loads(body)
        
        session_id = data.get("session_id")
        message, lang, intent, retry_attempt, session_id = await process_chat_request(data, session_id)
        
        prompt = build_prompt_with_history(message, lang, intent, session_id)
        payload = create_payload(prompt)
        
        prep_time = time.time() - start_time
        logger.warning(f"🚀 Prep: {prep_time*1000:.1f}ms | Intent: {intent} | Retry: {retry_attempt}")
        
        stream_generator = create_enhanced_stream_generator(payload, session_id, retry_attempt, intent, lang)
        
        return StreamingResponse(
            stream_generator, 
            media_type="text/event-stream",
            headers={
                "Cache-Control": "no-cache",
                "Connection": "keep-alive",
                "X-Session-ID": session_id,
                "X-Intent": intent,
                "X-Retry-Attempt": str(retry_attempt)
            }
        )

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"🔥 Error: {str(e)}")
        traceback.print_exc()
        return JSONResponse(
            status_code=500,
            content={
                "error": "Erreur serveur", 
                "service": "BraindCode",
                "retry": True,
                "error_type": "server_error"
            }
        )
# === ENDPOINTS POUR GESTION DE L'HISTORIQUE ===

@app.post("/conversation/new")
async def create_new_conversation():
    """Crée une nouvelle session de conversation"""
    session_id = create_session()
    return {
        "session_id": session_id,
        "created_at": CONVERSATION_HISTORY[session_id]["metadata"]["created_at"],
        "message": "Nouvelle conversation créée"
    }

@app.get("/conversation/{session_id}/history")
async def get_conversation_history_endpoint(session_id: str, limit: int = 20):
    """Récupère l'historique d'une conversation"""
    if session_id not in CONVERSATION_HISTORY:
        raise HTTPException(status_code=404, detail=SESSION_NOT_FOUND_TEXT)
    
    messages = get_conversation_history(session_id, limit)
    metadata = CONVERSATION_HISTORY[session_id]["metadata"]
    
    return {
        "session_id": session_id,
        "messages": messages,
        "metadata": metadata,
        "total_messages": len(messages)
    }

def _format_last_message(last_message):
    """Helper function to format last message info"""
    if not last_message:
        return None
    
    content_preview = None
    if last_message.get("content"):
        content_preview = last_message["content"][:50] + "..."
    
    return {
        "content": content_preview,
        "timestamp": last_message.get("timestamp")
    }

@app.get("/conversations")
async def list_conversations():
    """Liste toutes les conversations actives"""
    conversations = []
    for session_id, data in CONVERSATION_HISTORY.items():
        last_message = data["messages"][-1] if data["messages"] else None
        
        conversations.append({
            "session_id": session_id,
            "metadata": data["metadata"],
            "last_message": _format_last_message(last_message)
        })
    
    return {
        "conversations": sorted(conversations, key=lambda x: x["metadata"]["last_activity"], reverse=True),
        "total": len(conversations)
    }

@app.delete("/conversation/{session_id}")
async def delete_conversation(session_id: str):
    """Supprime une conversation"""
    if session_id not in CONVERSATION_HISTORY:
        raise HTTPException(status_code=404, detail=SESSION_NOT_FOUND_TEXT)
    
    del CONVERSATION_HISTORY[session_id]
    return {"message": f"Conversation {session_id} supprimée"}

@app.post("/conversation/{session_id}/export")
async def export_conversation(session_id: str):
    """Exporte une conversation au format JSON"""
    if session_id not in CONVERSATION_HISTORY:
        raise HTTPException(status_code=404, detail=SESSION_NOT_FOUND_TEXT)
    
    conversation_data = {
        "session_id": session_id,
        "exported_at": datetime.now().isoformat(),
        "conversation": CONVERSATION_HISTORY[session_id]
    }
    
    return JSONResponse(
        content=conversation_data,
        headers={
            "Content-Disposition": f"attachment; filename=conversation_{session_id[:8]}.json"
        }
    )
@app.post("/intentions/train")
async def train_bot_from_ui(request: Request):
    """
    Reçoit du texte d'entraînement depuis l'interface admin Laravel
    et enrichit dynamiquement les intentions.
    """
    try:
        data = await request.json()
        training_text = data.get("training_text", "").strip()

        if not training_text:
            raise HTTPException(status_code=400, detail="Texte d'entraînement manquant.")

        # Découpage des blocs
        training_lines = [line.strip() for line in training_text.split("\n") if line.strip()]
        current_intent = None
        new_intentions = {}

        for line in training_lines:
            if line.startswith("#"):
                current_intent = line[1:].strip()
                new_intentions[current_intent] = []
            elif current_intent:
                new_intentions[current_intent].append(line)

        # Simulation d'entraînement : enrichir INTENT_KEYWORDS
        for intent, phrases in new_intentions.items():
            existing_keywords = INTENT_KEYWORDS.get(intent, [])
            for phrase in phrases:
                keywords = phrase.lower().split()[:3]
                existing_keywords.extend(keywords)
            INTENT_KEYWORDS[intent] = list(set(existing_keywords))

        # Sauvegarder dans un fichier .txt de manière asynchrone
        save_path = "trained_intents.txt"
        async with aiofiles.open(save_path, "a", encoding="utf-8") as f:
            await f.write(f"\n# --- New Training ({datetime.now().isoformat()}) ---\n")
            for intent, phrases in new_intentions.items():
                await f.write(f"# {intent}\n")
                for phrase in phrases:
                    await f.write(f"{phrase}\n")

        logger.warning(f"✅ Intentions mises à jour depuis l'admin : {list(new_intentions.keys())}")
        return {
            "status": "ok",
            "message": "Intentions enrichies avec succès.",
            "added": new_intentions,
            "total": len(new_intentions),
        }

    except Exception as e:
        logger.error(f"Erreur d'entraînement depuis UI : {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"error": "Erreur lors de l'entraînement", "details": str(e)}
        )
# === ENDPOINTS UTILITAIRES ===

@app.get("/ping")
async def ping():
    try:
        # Utiliser directement HTTP_CLIENT sans l'appeler comme un constructeur
        r = await HTTP_CLIENT.get(OLLAMA_BASE, timeout=2)
        return {"status": "ok", "ollama": "online" if r.status_code == 200 else "offline", "service": "BraindCode"}
    except (httpx.RequestError, httpx.TimeoutException, ConnectionError):
        return {"status": "ok", "ollama": "offline", "service": "BraindCode"}





@app.get("/health")
async def health():
    try:
        start = time.time()
        response = await HTTP_CLIENT.get(OLLAMA_BASE, timeout=3)
        response_time = time.time() - start
        
        return {
            "status": "healthy",
            "ollama": "online" if response.status_code == 200 else "offline",
            "response_time": f"{response_time:.3f}s",
            "cache_size": len(PROMPT_CACHE),
            "precomputed_size": len(PRECOMPUTED_PROMPTS),
            "active_conversations": len(CONVERSATION_HISTORY),
            "total_messages": sum(len(conv["messages"]) for conv in CONVERSATION_HISTORY.values())
        }
    except (httpx.RequestError, httpx.TimeoutException, ConnectionError):
        return {"status": "degraded", "ollama": "offline"}

@app.get("/stats")
async def get_statistics():
    """Statistiques détaillées du système"""
    total_messages = sum(len(conv["messages"]) for conv in CONVERSATION_HISTORY.values())
    
    # Statistiques par langue
    lang_stats = {"fr": 0, "en": 0, "other": 0}
    intent_stats = {}
    
    for conv in CONVERSATION_HISTORY.values():
        for msg in conv["messages"]:
            if msg["sender"] == "user":
                lang = msg.get("language", "other")
                intent = msg.get("intent", "unknown")
                
                if lang in lang_stats:
                    lang_stats[lang] += 1
                else:
                    lang_stats["other"] += 1
                
                intent_stats[intent] = intent_stats.get(intent, 0) + 1
    
    return {
        "system": {
            "active_conversations": len(CONVERSATION_HISTORY),
            "total_messages": total_messages,
            "cache_size": len(PROMPT_CACHE),
            "precomputed_prompts": len(PRECOMPUTED_PROMPTS)
        },
        "usage": {
            "languages": lang_stats,
            "intents": intent_stats
        },
        "limits": {
            "max_conversations": MAX_CONVERSATIONS,
            "max_messages_per_conversation": MAX_MESSAGES_PER_CONVERSATION,
            "cache_max_size": CACHE_MAX_SIZE
        }
    }
def load_trained_intents_txt():
    """Recharge les intentions à partir du fichier .txt au démarrage"""
    global INTENT_KEYWORDS

    path = "trained_intents.txt"
    if not os.path.exists(path):
        logger.warning("📂 Aucun fichier trained_intents.txt trouvé.")
        return

    with open(path, "r", encoding="utf-8") as f:
        lines = [line.strip() for line in f if line.strip()]

    current_intent = None
    for line in lines:
        if line.startswith("# ---"):  # ignorer les commentaires de date
            continue
        elif line.startswith("#"):
            current_intent = line[1:].strip()
            if current_intent not in INTENT_KEYWORDS:
                INTENT_KEYWORDS[current_intent] = []
        elif current_intent:
            keywords = line.lower().split()[:3]
            INTENT_KEYWORDS[current_intent].extend(keywords)

    # Nettoyage doublons
    for intent in INTENT_KEYWORDS:
        INTENT_KEYWORDS[intent] = list(set(INTENT_KEYWORDS[intent]))

    logger.info(f"Intentions rechargées depuis trained_intents.txt : {list(INTENT_KEYWORDS.keys())}")
# === NOUVEL ENDPOINT POUR RETRY AUTOMATIQUE ===
@app.post("/chat-retry")
async def retry_last_message(request: Request):
    """Endpoint pour réessayer le dernier message"""
    try:
        body = await request.body()
        data = json.loads(body)
        
        session_id = data.get("session_id")
        if not session_id or session_id not in CONVERSATION_HISTORY:
            raise HTTPException(status_code=404, detail=SESSION_NOT_FOUND_TEXT)
        
        # Récupérer le dernier message utilisateur
        messages = CONVERSATION_HISTORY[session_id]["messages"]
        last_user_message = None
        
        for msg in reversed(messages):
            if msg["sender"] == "user":
                last_user_message = msg
                break
        
        if not last_user_message:
            raise HTTPException(status_code=400, detail="Aucun message utilisateur trouvé")
        
        # Supprimer la dernière réponse assistant si elle existe et est vide/erronée
        if messages and messages[-1]["sender"] == "assistant":
            messages.pop()
        
        # Relancer le chat avec le dernier message
        retry_data = {
            "message": last_user_message["content"],
            "session_id": session_id,
            "retry_attempt": data.get("retry_attempt", 0) + 1
        }
        
        # Créer une nouvelle requête pour le retry
        from fastapi import Request
        import json
        
        class MockRequest:
            def __init__(self, data):
                self._data = data
            
            async def body(self):
                return json.dumps(self._data).encode()
        
        mock_request = MockRequest(retry_data)
        return await chat_stream_with_history(mock_request)
        
    except Exception as e:
        logger.error(f"Retry error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"error": "Erreur lors du retry", "details": str(e)}
        )
# === ENDPOINT DE DIAGNOSTIQUE ===
@app.get("/diagnostic")
async def diagnostic_ollama():
    """Diagnostique de la connexion Ollama"""
    try:
        # Test de base
        start_time = time.time()
        response = await HTTP_CLIENT.get(OLLAMA_BASE, timeout=5)
        response_time = time.time() - start_time
        
        # Test du modèle
        model_test = None
        try:
            model_response = await HTTP_CLIENT.post(
                f"{OLLAMA_BASE}/api/tags", 
                timeout=5
            )
            if model_response.status_code == 200:
                models_data = model_response.json()
                available_models = [m.get("name", "") for m in models_data.get("models", [])]
                model_test = {
                    "available_models": available_models,
                    "target_model": OLLAMA_MODEL,
                    "model_available": OLLAMA_MODEL in available_models
                }
        except Exception as e:
            model_test = {"error": str(e)}
        
        return {
            "status": "online" if response.status_code == 200 else "offline",
            "base_url": OLLAMA_BASE,
            "response_time": f"{response_time:.3f}s",
            "status_code": response.status_code,
            "model_test": model_test,
            "timeout_config": {
                "connect": TIMEOUT_CONFIG.connect,
                "read": TIMEOUT_CONFIG.read,
                "write": TIMEOUT_CONFIG.write,
                "pool": TIMEOUT_CONFIG.pool
            }
        }
        
    except Exception as e:
        return {
            "status": "error",
            "error": str(e),
            "base_url": OLLAMA_BASE
        }
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5005, reload=False)