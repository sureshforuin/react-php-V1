# React + PHP REST API — Hostinger Deployment

A simple full-stack app with a **React (Vite)** frontend and a **PHP REST API** backend, automatically deployed to Hostinger via **GitHub Actions**.

---

## Project Structure

```
react-php-hostinger/
├── .github/workflows/
│   ├── deploy-frontend.yml   ← Auto-deploys on /frontend changes
│   └── deploy-backend.yml    ← Auto-deploys on /backend changes
│
├── frontend/                 ← React (Vite) app
│   ├── src/
│   │   ├── pages/            ← Home.jsx, Users.jsx
│   │   ├── services/api.js   ← Fetch wrapper
│   │   └── App.jsx
│   ├── public/.htaccess      ← SPA routing fix (copied to dist/)
│   ├── .env.development
│   ├── .env.production       ← Set VITE_API_URL here
│   └── vite.config.js
│
└── backend/
    ├── api/
    │   ├── index.php          ← Router entry point
    │   ├── .htaccess          ← URL rewriting
    │   ├── config/database.php
    │   ├── controllers/UserController.php
    │   └── models/User.php
    └── schema.sql             ← Run once in phpMyAdmin
```

---

## Local Development

### Frontend
```bash
cd frontend
npm install
npm run dev      # → http://localhost:5173
```

### Backend (PHP built-in server)
```bash
cd backend/api
php -S localhost:8000
# API: http://localhost:8000/api/health
```

The Vite dev server proxies `/api` calls to `localhost:8000` automatically.

---

## Hostinger Setup (One-time)

### 1. Enable SSH
Hostinger Panel → **Hosting → SSH Access → Enable SSH**

### 2. Add SSH Public Key
Paste your **public key** into Hostinger → SSH Access → Authorized Keys.

### 3. Create Database
Hostinger Panel → **Databases → MySQL Databases** → create DB, user, assign.

### 4. Run Schema
Open **phpMyAdmin**, select your DB, and run `backend/schema.sql`.

### 5. Set Environment Variables (PHP)
Hostinger Panel → **Hosting → PHP Config → Environment Variables**:

| Variable | Value |
|----------|-------|
| `DB_HOST` | MySQL hostname from Hostinger |
| `DB_NAME` | Your database name |
| `DB_USER` | Your database user |
| `DB_PASS` | Your database password |

---

## GitHub Secrets Setup

**GitHub → Repo → Settings → Secrets and Variables → Actions**

| Secret | Value |
|--------|-------|
| `SSH_HOST` | Hostinger server IP or hostname |
| `SSH_USER` | SSH username (e.g. `u123456789`) |
| `SSH_PRIVATE_KEY` | Contents of your private key (`~/.ssh/id_ed25519`) |

Optionally add `VITE_API_URL` if you want to override the API URL at build time instead of using `.env.production`.

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | Server & DB health check |
| GET | `/api/users` | List all users |
| GET | `/api/users/:id` | Get one user |
| POST | `/api/users` | Create user `{ name, email }` |
| PUT | `/api/users/:id` | Update user |
| DELETE | `/api/users/:id` | Delete user |

---

## How Auto-Deploy Works

```
git push origin main
      │
      ├── frontend/** changed?  →  npm ci + npm run build  →  rsync to ~/public_html/
      └── backend/**  changed?  →  rsync PHP files         →  rsync to ~/public_html/api/
```

Both workflows can run in parallel if both directories changed.
