const API_BASE_URL = 'http://localhost:8000/api';

export async function getPosts(autoGenerate = true) {
  const url = autoGenerate
    ? `${API_BASE_URL}/posts?auto_generate=1`
    : `${API_BASE_URL}/posts`;

  const response = await fetch(url);
  if (!response.ok) {
    throw new Error('Errore durante il recupero dei post');
  }
  return await response.json();
}

export async function getUserProfile(userId) {
  const response = await fetch(`${API_BASE_URL}/users/${userId}`);
  if (!response.ok) {
    throw new Error('Impossibile caricare il profilo utente');
  }
  return await response.json();
}

export async function getCommunityUsers() {
  const response = await fetch(`${API_BASE_URL}/community`);
  if (!response.ok) {
    throw new Error('Impossibile recuperare i contatti');
  }
  return await response.json();
}

export async function createPost(content, imageUrl = '') {
  const response = await fetch(`${API_BASE_URL}/posts`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      content: content,
      image_url: imageUrl || null,
    }),
  });

  if (!response.ok) {
    throw new Error('Errore durante il salvataggio del post');
  }
  return await response.json();
}

export async function deletePost(postId) {
  const response = await fetch(`${API_BASE_URL}/posts/${postId}`, {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error("Errore durante l'eliminazione del post");
  }
  return await response.json();
}

export async function toggleLike(postId) {
  const response = await fetch(`${API_BASE_URL}/likes/toggle`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ post_id: postId }),
  });

  if (!response.ok) {
    throw new Error("Errore durante l'aggiornamento del like");
  }
  return await response.json();
}

export async function addComment(postId, content) {
  const response = await fetch(`${API_BASE_URL}/comments`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      post_id: postId,
      content: content,
    }),
  });

  if (!response.ok) {
    throw new Error("Errore durante l'invio del commento");
  }
  return await response.json();
}

export async function generateAiPost(topic) {
  const response = await fetch(`${API_BASE_URL}/ai/generate`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ topic: topic }),
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.error || 'Errore nella generazione del testo');
  }
  return data;
}

export async function summarizeAiComments(postId) {
  const response = await fetch(`${API_BASE_URL}/ai/summarize`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ post_id: postId }),
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.error || 'Errore durante il riassunto');
  }
  return data;
}
