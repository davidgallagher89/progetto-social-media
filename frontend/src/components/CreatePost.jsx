import React, { useState } from 'react';
import { createPost, generateAiPost } from '../api';

export default function CreatePost({ onPostCreated }) {
  const [content, setContent] = useState('');
  const [imageUrl, setImageUrl] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isAiLoading, setIsAiLoading] = useState(false);
  const [showAiInput, setShowAiInput] = useState(false);
  const [aiPrompt, setAiPrompt] = useState('');

  async function handleSubmit(e) {
    e.preventDefault();

    if (!content.trim()) {
      alert('Inserisci del testo prima di pubblicare!');
      return;
    }

    setIsSubmitting(true);
    try {
      const data = await createPost(content, imageUrl);
      onPostCreated(data.post);
      setContent('');
      setImageUrl('');
    } catch (error) {
      alert('Errore: ' + error.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  async function handleGenerateAi() {
    if (!aiPrompt.trim()) {
      alert('Scrivi un argomento (es. "annuncio di un nuovo progetto")');
      return;
    }

    setIsAiLoading(true);
    try {
      const result = await generateAiPost(aiPrompt);
      if (result.text) {
        setContent(result.text);
        setShowAiInput(false);
        setAiPrompt('');
      }
    } catch (error) {
      alert('Errore nella generazione: ' + error.message);
    } finally {
      setIsAiLoading(false);
    }
  }

  return (
    <div className="card create-post-card">
      <div className="create-post-header">
        <h3>Cosa stai pensando?</h3>

        <button
          type="button"
          className="btn-ai-toggle"
          onClick={() => setShowAiInput(!showAiInput)}
        >
          ✨ Suggerisci testo
        </button>
      </div>

      {showAiInput && (
        <div className="ai-box">
          <label className="ai-label">
            Inserisci un argomento:
          </label>
          <div className="ai-input-group">
            <input
              type="text"
              placeholder="Es: Giornata di lavoro e nuovi progetti..."
              value={aiPrompt}
              onChange={(e) => setAiPrompt(e.target.value)}
              disabled={isAiLoading}
            />
            <button
              type="button"
              className="btn btn-ai"
              onClick={handleGenerateAi}
              disabled={isAiLoading}
            >
              {isAiLoading ? 'Scrivo...' : 'Genera testo'}
            </button>
          </div>
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <textarea
          className="post-textarea"
          rows="3"
          placeholder="Condividi un pensiero con la community..."
          value={content}
          onChange={(e) => setContent(e.target.value)}
          disabled={isSubmitting}
        />

        <div className="create-post-footer">
          <input
            type="url"
            className="input-image"
            placeholder="Link immagine (opzionale, es. https://...)"
            value={imageUrl}
            onChange={(e) => setImageUrl(e.target.value)}
            disabled={isSubmitting}
          />

          <button
            type="submit"
            className="btn btn-primary"
            disabled={isSubmitting}
          >
            {isSubmitting ? 'Pubblicazione...' : 'Pubblica Post'}
          </button>
        </div>
      </form>
    </div>
  );
}
