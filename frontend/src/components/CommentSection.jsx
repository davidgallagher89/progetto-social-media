import React, { useState } from 'react';
import { addComment, summarizeAiComments } from '../api';

export default function CommentSection({ postId, initialComments = [] }) {
  const [comments, setComments] = useState(initialComments);
  const [newComment, setNewComment] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [aiSummary, setAiSummary] = useState(null);
  const [isSummarizing, setIsSummarizing] = useState(false);

  async function handleAddComment(e) {
    e.preventDefault();
    if (!newComment.trim()) return;

    setIsSubmitting(true);
    try {
      const data = await addComment(postId, newComment);
      setComments([...comments, data.comment]);
      setNewComment('');
    } catch (error) {
      alert('Errore: ' + error.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  async function handleSummarize() {
    setIsSummarizing(true);
    try {
      const data = await summarizeAiComments(postId);
      setAiSummary(data.summary);
    } catch (error) {
      alert('Errore riassunto: ' + error.message);
    } finally {
      setIsSummarizing(false);
    }
  }

  return (
    <div className="comment-section">
      <div className="comment-section-header">
        <h4>Commenti ({comments.length})</h4>

        {comments.length > 1 && (
          <button
            type="button"
            className="btn-ai-small"
            onClick={handleSummarize}
            disabled={isSummarizing}
          >
            {isSummarizing ? 'Analisi in corso...' : '✨ Punti chiave'}
          </button>
        )}
      </div>

      {aiSummary && (
        <div className="ai-summary-card">
          <div className="ai-summary-title">Sintesi della discussione:</div>
          <p className="ai-summary-text">{aiSummary}</p>
          <button
            type="button"
            className="btn-link"
            onClick={() => setAiSummary(null)}
          >
            Chiudi sintesi
          </button>
        </div>
      )}

      <div className="comment-list">
        {comments.length === 0 ? (
          <p className="no-comments">Nessun commento. Sii il primo a commentare!</p>
        ) : (
          comments.map((comment) => (
            <div key={comment.id} className="comment-item">
              <span className="comment-author">
                {comment.user ? comment.user.name : 'Utente'}:
              </span>
              <span className="comment-text">{comment.content}</span>
            </div>
          ))
        )}
      </div>

      <form onSubmit={handleAddComment} className="comment-form">
        <input
          type="text"
          placeholder="Scrivi una risposta..."
          value={newComment}
          onChange={(e) => setNewComment(e.target.value)}
          disabled={isSubmitting}
        />
        <button
          type="submit"
          className="btn btn-secondary"
          disabled={isSubmitting}
        >
          Invia
        </button>
      </form>
    </div>
  );
}
