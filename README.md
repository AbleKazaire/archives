# TaskTogether — Project README

This repository contains TaskTogether, a small task‑management web application (frontend + PHP backend). This README explains what the project contains, how the app works, and step‑by‑step instructions to run it locally (XAMPP) or via Docker, and how to set up the database.

## Quick overview
- Frontend: Static HTML/CSS/JS in the repository root (pages such as `index.html`, `login.html`, `dashboard.html`, etc.). The frontend uses a small `app.js` localStorage model for offline/demo behavior and also contains UI hooks to call the backend APIs when available.
- Backend: `backend/` — a small PHP app with PSR‑4 autoloading (Composer). Entry point: `backend/public/index.php` which defines the API routes (currently task routes). The backend supports SQLite by default and can connect to other databases via PDO using an environment `DB_DSN` (plus `DB_USER` and `DB_PASS`).

## What the app does
- Tasks are grouped by `groups` and optionally by `projects`.
- Backend provides these API routes (in `backend/public/index.php`):
  - `GET /api/health` — health check (returns `{ok:true, ts}`)
  - `GET /api/groups/{id}/tasks` — list tasks for a group
  - `POST /api/groups/{id}/tasks` — create a task in a group

The backend code also contains an `AuthController` (`backend/src/Controllers/AuthController.php`) that implements secure signup/login logic (password hashing and verification). To enable auth routes, add the appropriate router entries in `backend/public/index.php` (instructions below).

## Project structure (important files)
- `index.html` — landing page
- `login.html` — login page + signup modal (signup modal was integrated here)
- `dashboard.html` — app UI after login
- `app.js` — frontend application logic (localStorage-based model and helpers)
- `style.css` — main styles
- `backend/` — PHP backend
  - `backend/public/index.php` — router / entry
  - `backend/src/Config/Database.php` — database handling (SQLite default, PDO support)
  - `backend/src/Controllers/TaskController.php` — tasks controller
  - `backend/src/Controllers/AuthController.php` — signup/login controller (needs routing)
  - `backend/src/Repositories/TaskRepository.php` — DB queries for tasks
  - `backend/tasktogether_schema.sql` — SQL schema you can import into phpMyAdmin (creates `users`, `groups`, `projects`, `tasks` tables)
  - `backend/Dockerfile`, `backend/render.yaml` — Docker/Render deployment artifacts

## Requirements
- PHP 8.0+ (8.2 recommended)
- Composer (for backend dependencies)
- For local hosting: XAMPP (Apache + MySQL + PHP) or Docker

## Local setup — XAMPP (recommended for quick local network testing)
1. Install XAMPP and start Apache and MySQL from the XAMPP Control Panel.
2. Copy the project folder into your XAMPP `htdocs` directory, e.g. `C:\xampp\htdocs\tasktogether`.
3. Install backend dependencies with Composer (if you plan to run backend PHP code):

```powershell
cd C:\xampp\htdocs\tasktogether\backend
composer install --no-dev --prefer-dist
```

4. Database options:
   - Default (simplest): let the backend use SQLite. The backend will create `backend/storage/database.sqlite` automatically if `DB_DSN` is not set.
   - MySQL (phpMyAdmin): create a MySQL database (e.g., `tasktogether`) using phpMyAdmin and either import `backend/tasktogether_schema.sql` or set `DB_DSN` to a MySQL DSN.

5. If you use MySQL/phpMyAdmin: import the schema
   - Go to `http://localhost/phpmyadmin/` → create a database (e.g., `tasktogether`) → select it → Import → choose `backend/tasktogether_schema.sql` and run.

6. Configure environment variables (optional): create a `.env` file in `backend/` with values like:

```
APP_ENV=local
DB_DSN=mysql:host=127.0.0.1;port=3306;dbname=tasktogether;charset=utf8mb4
DB_USER=your_db_user
DB_PASS=your_db_password
```

If `.env` is absent, the backend uses SQLite by default.

7. Ensure `backend/storage` is writable by the web server (create it if missing). On Windows with XAMPP, right-click → Properties → uncheck Read-only.

8. Open in browser:
- Frontend: `http://localhost/tasktogether/index.html` or `http://localhost/tasktogether/login.html`
- Backend API (health): `http://localhost/tasktogether/backend/public/api/health`

To allow other devices on the same Wi‑Fi to access the app, find your PC's local IP (`ipconfig`) and allow Apache through the firewall. Then use `http://<your-pc-ip>/tasktogether/` on phones.

## Enabling authentication routes (signup/login)
An `AuthController` exists at `backend/src/Controllers/AuthController.php` with `signup()` and `login()` methods. To expose these over HTTP, add routes in `backend/public/index.php` like:

```php
$router->post('/api/signup', [App\Controllers\AuthController::class, 'signup']);
$router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
```

After adding those routes you can call them from the frontend (`login.html` modal calls the signup flow client-side). The backend's signup uses `password_hash()` and login uses `password_verify()`.

Note: currently the frontend signup modal performs a client-side flow (it seeds localStorage) — enabling the backend routes lets you persist accounts in MySQL/SQLite.

## SQL schema
You can import `backend/tasktogether_schema.sql`. The schema creates these tables:
- `users (id, username, email, password_hash, full_name, created_at)`
- `groups (id, name, owner_id, created_at)` — `owner_id` references `users(id)`
- `projects (id, group_id, name)` — `group_id` references `groups(id)`
- `tasks (id, group_id, project_id, name, description, assignee_id, status, due_date)` — `assignee_id` references `users(id)`

If you prefer SQLite, the backend will auto-create tables using the SQL in `Database::ensureSchema()`.

## OAuth (Google / Microsoft / Apple)
The frontend includes buttons for Google, Microsoft and Apple OAuth, but backend wiring is not implemented. To add social login you'll need:
- Create OAuth apps/credentials with each provider (set redirect URIs to your backend endpoints).
- Implement the OAuth flow server-side (redirect to provider, handle callback, exchange code for tokens, create or find local user, issue session or JWT).

## Troubleshooting
- If Continue (login) doesn’t work: open browser DevTools → Console. If you see DOM-related errors check that `login.html` is not malformed (the file should contain a valid `<form id="authForm">` and inputs with the expected IDs `email`, `password`).
- If backend returns DB connection errors, confirm `DB_DSN`, `DB_USER`, and `DB_PASS` are correct or remove them to use SQLite.
- If files are edited but changes don't show: hard refresh (Ctrl+F5) or clear cache.

