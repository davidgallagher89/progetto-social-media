import React, { useState, useEffect } from 'react';
import { getUserProfile } from '../api';
import PostCard from './PostCard';

export default function UserProfile({ userId, onBack, onUserClick }) {
  const [profileData, setProfileData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [activeTab, setActiveTab] = useState('posts');

  useEffect(() => {
    caricaProfilo();
  }, [userId]);

  async function caricaProfilo() {
    setLoading(true);
    setError(null);
    try {
      const data = await getUserProfile(userId);
      setProfileData(data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }

  function handlePostDeleted(deletedPostId) {
    if (profileData) {
      setProfileData({
        ...profileData,
        posts: profileData.posts.filter((p) => p.id !== deletedPostId),
        user: {
          ...profileData.user,
          posts_count: Math.max(0, profileData.user.posts_count - 1),
        }
      });
    }
  }

  if (loading) {
    return (
      <div className="status-message loading">
        Caricamento profilo in corso...
      </div>
    );
  }

  if (error || !profileData) {
    return (
      <div className="card status-message error">
        <p>{error || 'Utente non trovato'}</p>
        <button className="btn btn-secondary" onClick={onBack}>
          ← Torna al Feed
        </button>
      </div>
    );
  }

  const { user, posts } = profileData;

  const joinDate = user.created_at
    ? new Date(user.created_at).toLocaleDateString('it-IT', { month: 'long', year: 'numeric' })
    : '2026';

  return (
    <div className="profile-container">
      <div className="profile-nav-bar">
        <button className="btn-back" onClick={onBack}>
          ← Torna al Feed
        </button>
      </div>

      <div className="card profile-header-card">
        <div
          className="profile-cover"
          style={{ backgroundImage: `url(${user.cover_url || 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200'})` }}
        />

        <div className="profile-info-section">
          <div className="profile-avatar-wrapper">
            <img
              src={user.avatar_url || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200'}
              alt={user.name}
              className="profile-avatar-large"
            />
          </div>

          <div className="profile-text-details">
            <h2 className="profile-name">{user.name}</h2>
            {user.job_title && <div className="profile-job">{user.job_title}</div>}
            {user.bio && <p className="profile-bio">{user.bio}</p>}
            <div className="profile-meta">
              <span>Iscritto da {joinDate}</span>
              <span>•</span>
              <span>{user.email}</span>
            </div>
          </div>

          <div className="profile-stats-row">
            <div className="stat-box">
              <span className="stat-number">{user.posts_count}</span>
              <span className="stat-label">Post</span>
            </div>
            <div className="stat-box">
              <span className="stat-number">{user.likes_received}</span>
              <span className="stat-label">Mi piace</span>
            </div>
          </div>
        </div>

        <div className="profile-tabs-bar">
          <button
            className={`profile-tab ${activeTab === 'posts' ? 'active' : ''}`}
            onClick={() => setActiveTab('posts')}
          >
            Post
          </button>
          <button
            className={`profile-tab ${activeTab === 'about' ? 'active' : ''}`}
            onClick={() => setActiveTab('about')}
          >
            Informazioni
          </button>
          <button
            className={`profile-tab ${activeTab === 'friends' ? 'active' : ''}`}
            onClick={() => setActiveTab('friends')}
          >
            Amici (148)
          </button>
          <button
            className={`profile-tab ${activeTab === 'photos' ? 'active' : ''}`}
            onClick={() => setActiveTab('photos')}
          >
            Foto
          </button>
        </div>
      </div>

      {activeTab === 'posts' && (
        <div className="profile-posts-section">
          {posts.length === 0 ? (
            <div className="card status-message empty">
              <p>Nessun post pubblicato finora.</p>
            </div>
          ) : (
            posts.map((post) => (
              <PostCard
                key={post.id}
                post={post}
                onPostDeleted={handlePostDeleted}
                onUserClick={onUserClick}
              />
            ))
          )}
        </div>
      )}

      {activeTab === 'about' && (
        <div className="card profile-tab-content">
          <h4>Panoramica</h4>
          <p>Lavora come <strong>{user.job_title || 'Professionista'}</strong></p>
          <p>Vive a Milano, Italia</p>
          <p>Contatto: {user.email}</p>
          <p>Iscrizione: {joinDate}</p>
        </div>
      )}

      {activeTab === 'friends' && (
        <div className="card profile-tab-content">
          <h4>Amici in comune</h4>
          <p>Visualizzazione elenco amici ({user.name} ha 148 amici).</p>
        </div>
      )}

      {activeTab === 'photos' && (
        <div className="card profile-tab-content">
          <h4>Foto</h4>
          <p>Tutte le foto caricate nei post da {user.name}.</p>
        </div>
      )}
    </div>
  );
}
