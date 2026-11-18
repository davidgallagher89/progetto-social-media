import React, { useState, useEffect } from 'react';
import { getCommunityUsers } from '../api';

export default function SidebarRight({ onUserClick }) {
  const [contacts, setContacts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    caricaContatti();
  }, []);

  async function caricaContatti() {
    try {
      const data = await getCommunityUsers();
      setContacts(data);
    } catch (e) {
      console.error('Errore caricamento contatti', e);
    } finally {
      setLoading(false);
    }
  }

  return (
    <aside className="sidebar sidebar-right">
      <div className="card birthdays-card">
        <div className="birthdays-header">
          <span className="birthdays-icon">🎁</span>
          <div className="birthdays-text">
            <strong>Compleanni</strong>
            <p>Oggi è il compleanno di <strong>Sofia Ferri</strong>.</p>
          </div>
        </div>
      </div>

      <div className="card community-card">
        <div className="community-header">
          <h4>Contatti</h4>
          <span className="contacts-count">({contacts.length})</span>
        </div>

        {loading ? (
          <div className="loading-small">Caricamento contatti...</div>
        ) : (
          <div className="community-list">
            {contacts.map((user) => (
              <div
                key={user.id}
                className="community-item"
                onClick={() => onUserClick(user.id)}
                title={`Apri profilo di ${user.name}`}
              >
                <div className="avatar-wrapper">
                  <img
                    src={user.avatar_url || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100'}
                    alt={user.name}
                    className="avatar small"
                  />
                  <span className="online-dot"></span>
                </div>
                <div className="community-info">
                  <div className="community-name">{user.name}</div>
                  <div className="community-role">{user.job_title || 'Online'}</div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </aside>
  );
}
