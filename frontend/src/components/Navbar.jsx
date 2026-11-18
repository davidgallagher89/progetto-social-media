import React, { useState } from 'react';

export default function Navbar({ onGoHome, onGoMyProfile }) {
  const [activeDropdown, setActiveDropdown] = useState(null);

  function toggleDropdown(name) {
    setActiveDropdown(activeDropdown === name ? null : name);
  }

  return (
    <header className="navbar">
      <div className="navbar-container">
        <div className="logo-section" onClick={onGoHome} style={{ cursor: 'pointer' }} title="Torna al Feed">
          <span className="logo-icon">🌐</span>
          <h1 className="logo-title">SocialPulse</h1>
        </div>

        <div className="navbar-search">
          <span className="search-icon">🔍</span>
          <input type="text" placeholder="Cerca su SocialPulse..." />
        </div>

        <div className="navbar-right-actions">
          <div className="action-wrapper">
            <button
              className={`nav-action-circle ${activeDropdown === 'friends' ? 'active' : ''}`}
              onClick={() => toggleDropdown('friends')}
              title="Richieste di amicizia"
            >
              👥
              <span className="notification-badge">2</span>
            </button>

            {activeDropdown === 'friends' && (
              <div className="nav-dropdown">
                <div className="dropdown-header">
                  <h4>Richieste di amicizia</h4>
                </div>
                <div className="dropdown-list">
                  <div className="dropdown-item">
                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100" alt="Elena" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-name">Elena Neri</span>
                      <span className="dropdown-sub">3 amici in comune</span>
                      <div className="dropdown-buttons">
                        <button className="btn-small-primary">Conferma</button>
                        <button className="btn-small-secondary">Elimina</button>
                      </div>
                    </div>
                  </div>
                  <div className="dropdown-item">
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100" alt="Federico" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-name">Federico Conti</span>
                      <span className="dropdown-sub">1 amico in comune</span>
                      <div className="dropdown-buttons">
                        <button className="btn-small-primary">Conferma</button>
                        <button className="btn-small-secondary">Elimina</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>

          <div className="action-wrapper">
            <button
              className={`nav-action-circle ${activeDropdown === 'messages' ? 'active' : ''}`}
              onClick={() => toggleDropdown('messages')}
              title="Messaggi"
            >
              💬
              <span className="notification-badge">3</span>
            </button>

            {activeDropdown === 'messages' && (
              <div className="nav-dropdown">
                <div className="dropdown-header">
                  <h4>Messaggi</h4>
                </div>
                <div className="dropdown-list">
                  <div className="dropdown-item message-item">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100" alt="Luca" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-name">Luca Moretti</span>
                      <span className="dropdown-preview">Hai dato un&apos;occhiata all&apos;ultimo aggiornamento?</span>
                      <span className="dropdown-time">10 min fa</span>
                    </div>
                  </div>
                  <div className="dropdown-item message-item">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100" alt="Chiara" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-name">Chiara Romano</span>
                      <span className="dropdown-preview">Ci sentiamo più tardi per la review grafica!</span>
                      <span className="dropdown-time">1 ora fa</span>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>

          <div className="action-wrapper">
            <button
              className={`nav-action-circle ${activeDropdown === 'notifications' ? 'active' : ''}`}
              onClick={() => toggleDropdown('notifications')}
              title="Notifiche"
            >
              🔔
              <span className="notification-badge">4</span>
            </button>

            {activeDropdown === 'notifications' && (
              <div className="nav-dropdown">
                <div className="dropdown-header">
                  <h4>Notifiche</h4>
                </div>
                <div className="dropdown-list">
                  <div className="dropdown-item">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100" alt="Sofia" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-text"><strong>Sofia Ferri</strong> ha pubblicato un nuovo post.</span>
                      <span className="dropdown-time">25 min fa</span>
                    </div>
                  </div>
                  <div className="dropdown-item">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" alt="Marco" className="avatar small" />
                    <div className="dropdown-info">
                      <span className="dropdown-text"><strong>Marco Bianchi</strong> ha messo &quot;Mi piace&quot; al tuo post.</span>
                      <span className="dropdown-time">2 ore fa</span>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>

          <div
            className="user-profile-btn"
            onClick={onGoMyProfile}
            title="Visualizza il tuo profilo"
          >
            <img
              src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&auto=format&fit=crop&q=80"
              alt="David Vella"
              className="avatar small"
            />
          </div>
        </div>
      </div>
    </header>
  );
}
