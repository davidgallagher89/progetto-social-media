# SocialPulse

Applicazione web full-stack ispirata a un feed social, con backend PHP/Laravel, database MySQL e frontend React.

## Funzionalità principali

- Pubblicazione ed eliminazione dei post (testo e immagine opzionale)
- Like con aggiornamento immediato
- Commenti sotto ogni post
- Profilo utente con elenco post pubblicati e statistiche
- Supporto opzionale alle API DeepSeek per generare bozze di post e riassumere i commenti

## Tecnologie utilizzate

- **Frontend:** React, Vite, CSS
- **Backend:** PHP 8.2 / Laravel, PDO, REST API
- **Database:** MySQL 8.0
- **Ambiente:** Docker e Docker Compose

## Avvio del progetto

### 1. Backend e Database (Docker)

Dalla cartella principale del progetto:

```bash
docker compose up --build
```

Servizi disponibili:
- **API Backend:** `http://localhost:8000`
- **phpMyAdmin:** `http://localhost:8080`

*(Opzionale)* Per usare le funzioni di generazione/riassunto con DeepSeek, copia `backend/.env.example` in `backend/.env` e inserisci la tua `DEEPSEEK_API_KEY`.

### 2. Frontend (React)

In un altro terminale:

```bash
cd frontend
npm install
npm run dev
```

Apri nel browser: `http://localhost:5173`
