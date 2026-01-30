import pytest
import asyncio
import json
from fastapi.testclient import TestClient
from unittest.mock import patch, AsyncMock
from main import app
import httpx

# Create a test client that handles lifespan events
@pytest.fixture(scope="session")
def test_client():
    """Create a test client with proper lifespan management"""
    with TestClient(app) as client:
        yield client

@pytest.fixture
def new_conversation(test_client):
    """Fixture pour créer une nouvelle conversation avant chaque test."""
    response = test_client.post("/conversation/new")
    assert response.status_code == 200
    return response.json()

def test_create_conversation(new_conversation):
    """Test de la création d'une nouvelle session de conversation"""
    assert "session_id" in new_conversation
    assert "created_at" in new_conversation
    assert new_conversation["message"] == "Nouvelle conversation créée"

def test_get_conversation_history(test_client, new_conversation):
    """Test de récupération de l'historique d'une conversation"""
    session_id = new_conversation["session_id"]
    response = test_client.get(f"/conversation/{session_id}/history")
    assert response.status_code == 200
    data = response.json()
    assert "session_id" in data
    assert data["session_id"] == session_id
    assert "messages" in data
    assert "metadata" in data

def test_invalid_conversation_history(test_client):
    """Test pour une session invalide"""
    session_id = "invalid_session"
    response = test_client.get(f"/conversation/{session_id}/history")
    assert response.status_code == 404
    assert response.json()["detail"] == "Session non trouvée"

def test_chat_stream(test_client, new_conversation):
    """Test de l'endpoint /chat-stream avec mock du streaming"""
    session_id = new_conversation["session_id"]
    payload = {
        "message": "salut",
        "session_id": session_id,
    }

    # Mock the HTTP client stream method
    async def mock_stream_response():
        """Mock generator that simulates Ollama streaming response"""
        # Simulate streaming chunks
        chunks = [
            '{"response": "Bonjour", "done": false}',
            '{"response": " comment", "done": false}',
            '{"response": " allez-vous?", "done": true}'
        ]
        
        for chunk in chunks:
            yield chunk + "\n"

    # Create a mock response object
    mock_response = AsyncMock()
    mock_response.status_code = 200
    mock_response.aiter_text = mock_stream_response
    
    # Create a mock context manager
    mock_context = AsyncMock()
    mock_context.__aenter__.return_value = mock_response
    mock_context.__aexit__.return_value = None

    # Mock the HTTP_CLIENT.stream method
    with patch('main.HTTP_CLIENT') as mock_http_client:
        mock_http_client.stream.return_value = mock_context
        
        response = test_client.post("/chat-stream", json=payload)
        
        # For streaming responses, we need to check the status code and headers
        assert response.status_code == 200
        assert response.headers["content-type"] == "text/event-stream; charset=utf-8"
        
        # The response should be a streaming response
        # We can't easily test the streaming content with TestClient
        # but we can verify the endpoint doesn't error out

def test_chat_stream_with_error_handling(test_client, new_conversation):
    """Test de l'endpoint /chat-stream avec gestion d'erreurs"""
    session_id = new_conversation["session_id"]
    payload = {
        "message": "test error",
        "session_id": session_id,
    }

    # Mock HTTP client to raise an exception
    with patch('main.HTTP_CLIENT') as mock_http_client:
        mock_http_client.stream.side_effect = httpx.ConnectError("Connection failed")
        
        response = test_client.post("/chat-stream", json=payload)
        
        # Should still return 200 but with error in stream
        assert response.status_code == 200

def test_chat_stream_invalid_message(test_client, new_conversation):
    """Test avec un message invalide"""
    session_id = new_conversation["session_id"]
    
    # Test avec message vide
    payload = {
        "message": "",
        "session_id": session_id,
    }
    response = test_client.post("/chat-stream", json=payload)
    assert response.status_code == 400
    
    # Test avec message trop long
    payload = {
        "message": "x" * 1000,  # Message très long
        "session_id": session_id,
    }
    response = test_client.post("/chat-stream", json=payload)
    assert response.status_code == 400

def test_ping(test_client):
    """Test de l'endpoint /ping"""
    with patch('main.HTTP_CLIENT') as mock_http_client:
        # Create a proper async mock response
        mock_response = AsyncMock()
        mock_response.status_code = 200
        
        # Mock the get method to return the mock response
        mock_http_client.get = AsyncMock(return_value=mock_response)
        
        response = test_client.get("/ping")
        assert response.status_code == 200
        data = response.json()
        assert "status" in data
        assert data["status"] == "ok"

def test_health(test_client):
    """Test de l'endpoint /health"""
    with patch('main.HTTP_CLIENT') as mock_http_client:
        # Create a proper async mock response
        mock_response = AsyncMock()
        mock_response.status_code = 200
        
        # Mock the get method to return the mock response
        mock_http_client.get = AsyncMock(return_value=mock_response)
        
        response = test_client.get("/health")
        assert response.status_code == 200
        data = response.json()
        assert "status" in data
        
        # Check for either 'healthy' or 'degraded' status
        if data["status"] == "healthy":
            assert "cache_size" in data
            assert "active_conversations" in data
        elif data["status"] == "degraded":
            # When Ollama is offline, we might not have cache_size
            # Just check that we get a reasonable response
            assert "ollama" in data

def test_diagnostic(test_client):
    """Test de l'endpoint /diagnostic"""
    with patch('main.HTTP_CLIENT') as mock_http_client:
        # Mock the get method for base connection test
        mock_get_response = AsyncMock()
        mock_get_response.status_code = 200
        mock_http_client.get = AsyncMock(return_value=mock_get_response)
        
        # Mock the post method for model test
        mock_post_response = AsyncMock()
        mock_post_response.status_code = 200
        mock_post_response.json = AsyncMock(return_value={
            "models": [{"name": "llama3"}, {"name": "mistral"}]
        })
        mock_http_client.post = AsyncMock(return_value=mock_post_response)
        
        response = test_client.get("/diagnostic")
        assert response.status_code == 200
        data = response.json()
        assert "status" in data
        assert "base_url" in data
        
        # Handle both success and error cases
        if data["status"] == "ok":
            assert "model_test" in data
        elif data["status"] == "error":
            assert "error" in data
            
def test_delete_conversation(test_client, new_conversation):
    """Test de suppression d'une conversation"""
    session_id = new_conversation["session_id"]
    response = test_client.delete(f"/conversation/{session_id}")
    assert response.status_code == 200
    assert response.json()["message"] == f"Conversation {session_id} supprimée"

def test_list_conversations(test_client, new_conversation):
    """Test de listage des conversations"""
    response = test_client.get("/conversations")
    assert response.status_code == 200
    data = response.json()
    assert "conversations" in data
    assert "total" in data
    assert data["total"] >= 1  # Au moins la conversation créée

def test_export_conversation(test_client, new_conversation):
    """Test d'export de conversation"""
    session_id = new_conversation["session_id"]
    response = test_client.post(f"/conversation/{session_id}/export")
    assert response.status_code == 200
    data = response.json()
    assert "session_id" in data
    assert "exported_at" in data
    assert "conversation" in data

def test_stats(test_client, new_conversation):
    """Test de l'endpoint /stats"""
    response = test_client.get("/stats")
    assert response.status_code == 200
    data = response.json()
    assert "system" in data
    assert "usage" in data
    assert "limits" in data



def test_intent_training(test_client):
    """Test de l'endpoint /intentions/train"""
    training_data = {
        "training_text": """# test_intent
phrase de test
autre phrase de test

# autre_intent
phrase pour autre intent"""
    }
    
    response = test_client.post("/intentions/train", json=training_data)
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "ok"
    assert "added" in data
    assert "total" in data


# Test d'intégration avec mock plus réaliste
def test_integration_chat_flow(test_client):
    """Test d'intégration complète du flux de chat"""
    # 1. Créer une nouvelle conversation
    response = test_client.post("/conversation/new")
    session_id = response.json()["session_id"]
    
    # 2. Mock du streaming pour simuler une vraie réponse
    with patch('main.HTTP_CLIENT') as mock_http_client:
        async def realistic_stream():
            responses = [
                '{"response": "Bonjour", "done": false}',
                '{"response": "! Comment", "done": false}',
                '{"response": " puis-je", "done": false}',
                '{"response": " vous aider", "done": false}',
                '{"response": "?", "done": true}'
            ]
            for resp in responses:
                yield resp + "\n"
        
        mock_response = AsyncMock()
        mock_response.status_code = 200
        mock_response.aiter_text = realistic_stream
        
        mock_context = AsyncMock()
        mock_context.__aenter__.return_value = mock_response
        mock_http_client.stream.return_value = mock_context
        
        # 3. Envoyer un message
        response = test_client.post("/chat-stream", json={
            "message": "Bonjour",
            "session_id": session_id
        })
        
        assert response.status_code == 200
    
    # 4. Vérifier l'historique
    history_response = test_client.get(f"/conversation/{session_id}/history")
    assert history_response.status_code == 200
    history_data = history_response.json()
    assert len(history_data["messages"]) >= 1  # Au moins le message utilisateur

if __name__ == "__main__":
    pytest.main([__file__, "-v"])