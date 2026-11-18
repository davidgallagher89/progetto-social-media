import React, { useState } from 'react';
import { toggleLike, deletePost } from '../api';
import CommentSection from './CommentSection';

export default function PostCard({ post, onPostDeleted, onUserClick }) {
  const [likesCount, setLikesCount] = useState(post.likes_count || 0);
  const [isLiked, setIsLiked] = useState(false);
  const [showComments, setShowComments] = useState(false);
  const [isLiking, setIsLiking] = useState(false);

  async function handleLikeToggle() {
    if (isLiking) return;
    setIsLiking(true);

    try {
      const result = await toggleLike(post.id);
      setIsLiked(result.liked);
      setLikesCount(result.likes_count);
    } catch (error) {
      alert('Errore like: ' + error.message);
    } finally {
      setIsLiking(false);
    }
  }

  async function handleDelete() {
    if (confirm('Sei sicuro di voler cancellare questo post?')) {
      try {
        await deletePost(post.id);
        onPostDeleted(post.id);
      } catch (error) {
        alert('Errore cancellazione: ' + error.message);
      }
    }
  }

  const formattedDate = new Date(post.created_at).toLocaleString('it-IT', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  });

  const authorName = post.user ? post.user.name : 'Utente Anonimo';
  const authorAvatar = post.user ? post.user.avatar_url : null;
  const authorJob = post.user ? post.user.job_title : null;
  const authorId = post.user ? post.user.id : null;

  return (
    <article className="card post-card">
      <div className="post-header">
        <div
          className="post-author-info clickable-author"
          onClick={() => onUserClick && authorId && onUserClick(authorId)}
          title={`Visualizza profilo di ${authorName}`}
        >
          {authorAvatar ? (
            <img src={authorAvatar} alt={authorName} className="avatar small" />
          ) : (
            <div className="avatar small">
              {authorName.charAt(0).toUpperCase()}
            </div>
          )}
          <div>
            <div className="author-name">{authorName}</div>
            {authorJob && <div className="author-job">{authorJob}</div>}
            <div className="post-date">{formattedDate}</div>
          </div>
        </div>

        {authorId === 1 && (
          <button
            className="btn-icon"
            onClick={handleDelete}
            title="Elimina questo post"
          >
            🗑️
          </button>
        )}
      </div>

      <p className="post-content">{post.content}</p>

      {post.image_url && (
        <div className="post-image-container">
          <img src={post.image_url} alt="Allegato post" className="post-image" />
        </div>
      )}

      <div className="post-actions">
        <button
          type="button"
          className={`action-btn ${isLiked ? 'liked' : ''}`}
          onClick={handleLikeToggle}
          disabled={isLiking}
        >
          <span className="action-icon">{isLiked ? '❤️' : '🤍'}</span>
          <span>{likesCount} {likesCount === 1 ? 'Like' : 'Likes'}</span>
        </button>

        <button
          type="button"
          className="action-btn"
          onClick={() => setShowComments(!showComments)}
        >
          <span className="action-icon">💬</span>
          <span>
            {post.comments ? post.comments.length : 0} Commenti
          </span>
        </button>
      </div>

      {showComments && (
        <CommentSection
          postId={post.id}
          initialComments={post.comments || []}
        />
      )}
    </article>
  );
}
