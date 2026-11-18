import React from 'react';

export default function SidebarLeft({ currentView, selectedUserId, onGoHome, onGoMyProfile, onRefreshFeed, isRefreshing }) {
  return (
    <aside className="sidebar sidebar-left">
      <div
        className="user-quick-card"
        onClick={onGoMyProfile}
        title="Visualizza il tuo profilo"
      >
        <img
          src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&auto=format&fit=crop&q=80"
          alt="David Vella"
          className="avatar"
        />
        <div className="quick-user-text">
          <span className="user-name">David Vella</span>
          <span className="user-role">Profilo personale</span>
        </div>
      </div>

      <nav className="nav-menu">
        <button
          className={`nav-item ${currentView === 'feed' ? 'active' : ''}`}
          onClick={onGoHome}
        >
          <span className="nav-icon">📰</span>
          <span>Feed</span>
        </button>

        <button
          className={`nav-item ${currentView === 'profile' && selectedUserId === 1 ? 'active' : ''}`}
          onClick={onGoMyProfile}
        >
          <span className="nav-icon">👤</span>
          <span>Profilo</span>
        </button>

        <button
          className="nav-item btn-refresh-feed"
          onClick={onRefreshFeed}
          disabled={isRefreshing}
          title="Aggiorna la bacheca"
        >
          <span className="nav-icon">{isRefreshing ? '⏳' : '🔄'}</span>
          <span>{isRefreshing ? 'Aggiornamento...' : 'Aggiorna feed'}</span>
        </button>

        <div className="nav-divider"></div>

        <button className="nav-item">
          <span className="nav-icon">👥</span>
          <span>Amici</span>
        </button>

        <button className="nav-item">
          <span className="nav-icon">🔖</span>
          <span>Elementi salvati</span>
        </button>

        <button className="nav-item">
          <span className="nav-icon">👥</span>
          <span>Gruppi</span>
        </button>

        <button className="nav-item">
          <span className="nav-icon">🎥</span>
          <span>Video</span>
        </button>

        <button className="nav-item">
          <span className="nav-icon">⏰</span>
          <span>Ricordi</span>
        </button>

        <button className="nav-item">
          <span className="nav-icon">📅</span>
          <span>Eventi</span>
        </button>
      </nav>

      <div className="sidebar-footer">
        <span>Privacy • Condizioni • Cookie • SocialPulse © 2026</span>
      </div>
    </aside>
  );
}
