import React, { useState, useEffect } from 'react';
import Navbar from './components/Navbar';
import SidebarLeft from './components/SidebarLeft';
import SidebarRight from './components/SidebarRight';
import CreatePost from './components/CreatePost';
import PostCard from './components/PostCard';
import UserProfile from './components/UserProfile';
import { getPosts } from './api';

export default function App() {
  const [currentView, setCurrentView] = useState('feed');
  const [selectedUserId, setSelectedUserId] = useState(null);

  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [isRefreshing, setIsRefreshing] = useState(false);

  useEffect(() => {
    caricaPostDalServer(true);
  }, []);

  async function caricaPostDalServer(autoGenerate = false) {
    if (posts.length === 0) {
      setLoading(true);
    } else {
      setIsRefreshing(true);
    }
    setError(null);

    try {
      const data = await getPosts(autoGenerate);
      setPosts(data);
    } catch (err) {
      setError('Impossibile connettersi al backend. Verifica che i container Docker siano avviati.');
    } finally {
      setLoading(false);
      setIsRefreshing(false);
    }
  }

  function handleUserClick(userId) {
    setSelectedUserId(userId);
    setCurrentView('profile');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function handleGoHome() {
    setCurrentView('feed');
    setSelectedUserId(null);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function handleGoMyProfile() {
    handleUserClick(1);
  }

  function handlePostCreated(newPost) {
    setPosts([newPost, ...posts]);
  }

  function handlePostDeleted(deletedPostId) {
    setPosts(posts.filter((p) => p.id !== deletedPostId));
  }

  return (
    <div className="facebook-app">
      <Navbar onGoHome={handleGoHome} onGoMyProfile={handleGoMyProfile} />

      <div className="main-layout-container">
        <SidebarLeft
          currentView={currentView}
          selectedUserId={selectedUserId}
          onGoHome={handleGoHome}
          onGoMyProfile={handleGoMyProfile}
          onRefreshFeed={() => caricaPostDalServer(true)}
          isRefreshing={isRefreshing}
        />

        <main className="feed-center-column">
          {currentView === 'feed' ? (
            <>
              <CreatePost onPostCreated={handlePostCreated} />

              {isRefreshing && (
                <div className="refresh-banner">
                  Aggiornamento bacheca in corso...
                </div>
              )}

              {loading && (
                <div className="status-message loading">
                  Caricamento post in corso...
                </div>
              )}

              {error && (
                <div className="status-message error">
                  <p>{error}</p>
                  <button className="btn btn-secondary" onClick={() => caricaPostDalServer(false)}>
                    Riprova
                  </button>
                </div>
              )}

              {!loading && !error && posts.length === 0 && (
                <div className="status-message empty">
                  <p>Nessun post presente.</p>
                </div>
              )}

              {!loading && !error && posts.map((post) => (
                <PostCard
                  key={post.id}
                  post={post}
                  onPostDeleted={handlePostDeleted}
                  onUserClick={handleUserClick}
                />
              ))}
            </>
          ) : (
            <UserProfile
              userId={selectedUserId}
              onBack={handleGoHome}
              onUserClick={handleUserClick}
            />
          )}
        </main>

        <SidebarRight onUserClick={handleUserClick} />
      </div>
    </div>
  );
}
