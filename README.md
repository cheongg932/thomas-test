# PageBuilder V2

Local AI-capable CMS: a **Laravel API + public website** and a **React admin** with a GrapesJS drag-and-drop page builder.

This guide walks you through installing everything from scratch and running the project on **macOS** or **Windows**. It is written for beginners — follow the steps in order.

---

## Table of contents

1. [What this project is](#1-what-this-project-is)
2. [What you will install](#2-what-you-will-install)
3. [Install prerequisites — macOS](#3-install-prerequisites--macos)
4. [Install prerequisites — Windows (WSL2)](#4-install-prerequisites--windows-wsl2)
5. [Get the project code](#5-get-the-project-code)
6. [Create the MySQL database](#6-create-the-mysql-database)
7. [First-time project setup](#7-first-time-project-setup)
8. [Run the project every day](#8-run-the-project-every-day)
9. [How to stop](#9-how-to-stop)
10. [Troubleshooting](#10-troubleshooting)
11. [What’s included](#11-whats-included)
12. [More docs](#12-more-docs)
13. [Windows without WSL (fallback)](#13-windows-without-wsl-fallback)

---

## 1. What this project is

You will run two parts on your computer:

| Piece | Folder | What it does | Address in browser |
|---|---|---|---|
| Admin studio | `apps/admin` | Login, page builder, products, menus, settings | http://localhost:5173 |
| API + public site | `apps/api` | Saves data, serves the published website | http://localhost:8000 |

```text
pagebuilderv2/          ← project root (folder that contains package.json)
  apps/
    api/                ← Laravel (backend + public pages)
    admin/              ← React (admin UI)
  package.json          ← npm run dev starts both
```

After setup, one command starts both: `npm run dev`.

---

## 2. What you will install

| Tool | Why you need it | Suggested version |
|---|---|---|
| **Git** | Download / update the project code | latest |
| **PHP** | Runs the Laravel backend | **8.3 or newer** |
| **Composer** | Installs PHP libraries for Laravel | 2.x |
| **Node.js** | Runs the admin UI tooling | **20 or newer** (LTS) |
| **npm** | Installs JavaScript packages (comes with Node) | 10+ |
| **MySQL** | Stores users, pages, products, etc. | 8.x |

You do **not** need Redis, Docker, or cloud accounts for a basic local run.

**Before you start:** decide which section to follow.

- **Mac** → [Section 3](#3-install-prerequisites--macos)
- **Windows** → [Section 4](#4-install-prerequisites--windows-wsl2) (recommended: WSL2 Ubuntu)

---

## 3. Install prerequisites — macOS

### 3.1 Open Terminal

1. Press `Cmd + Space`, type **Terminal**, press Enter.
2. Keep this window open for all commands below.

### 3.2 Install Homebrew (package manager)

Paste this and press Enter:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Follow the on-screen prompts. If Homebrew finishes and tells you to add it to your PATH, copy those two lines it prints and run them.

Check:

```bash
brew -v
```

You should see a version number.

### 3.3 Install Git, PHP, Composer, Node, MySQL

```bash
brew install git php@8.3 composer node@20 mysql
```

Link PHP and Node so your terminal finds them (Homebrew may show similar commands — use what it prints if different):

```bash
brew link php@8.3 --force --overwrite
brew link node@20 --force --overwrite
```

### 3.4 Start MySQL

```bash
brew services start mysql
```

Optional: set a root password (remember it — you will put it in `.env` later):

```bash
mysql_secure_installation
```

If you skip a password, leave `DB_PASSWORD` empty in `.env` later.

### 3.5 Verify everything

Run each command. Each should print a version (not “command not found”):

```bash
git --version
php -v
composer -V
node -v
npm -v
mysql --version
```

- PHP must be **8.3** or higher.
- Node must be **20** or higher.

Then continue at [Section 5](#5-get-the-project-code).

---

## 4. Install prerequisites — Windows (WSL2)

On Windows, this project’s `npm run dev` uses a **bash** script. The easiest path for beginners is **WSL2 with Ubuntu** (a Linux environment inside Windows).

### 4.1 Install WSL2 + Ubuntu

1. Open **PowerShell as Administrator** (right-click Start → Windows Terminal / PowerShell → Run as administrator).
2. Run:

```powershell
wsl --install
```

3. Restart your PC when asked.
4. After restart, Ubuntu may open and ask you to create a **UNIX username** and **password**. Remember them.
5. Later, open apps with the **Ubuntu** app from the Start menu (or Windows Terminal → Ubuntu tab).

All remaining Windows steps are run **inside Ubuntu**, not in PowerShell.

### 4.2 Update Ubuntu packages

```bash
sudo apt update && sudo apt upgrade -y
```

Enter your Ubuntu password when asked (nothing appears while you type — that is normal).

### 4.3 Install Git, PHP extensions, MySQL client tools, curl, unzip

```bash
sudo apt install -y git curl unzip lsof \
  php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-mysql php8.3-sqlite3 php8.3-bcmath \
  mysql-server
```

If `php8.3` packages are not found, enable a PHP repository first, then retry:

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y git curl unzip lsof \
  php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-mysql php8.3-sqlite3 php8.3-bcmath \
  mysql-server
```

### 4.4 Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer -V
```

### 4.5 Install Node.js 20 (LTS)

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

### 4.6 Start MySQL and set a root password

```bash
sudo service mysql start
sudo mysql
```

In the MySQL prompt, run (replace `YourStrongPassword` with a password you will remember):

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourStrongPassword';
FLUSH PRIVILEGES;
EXIT;
```

You will put this same password in `.env` as `DB_PASSWORD` later.

### 4.7 Verify everything

```bash
git --version
php -v
composer -V
node -v
npm -v
mysql --version
```

- PHP must be **8.3** or higher.
- Node must be **20** or higher.

Then continue at [Section 5](#5-get-the-project-code).

> Cannot use WSL? See [Section 13](#13-windows-without-wsl-fallback).

---

## 5. Get the project code

You need the project folder on your machine. Use **one** of these methods.

### Option A — Clone with Git (recommended)

1. Decide where to keep projects, for example a `Projects` folder in your home directory.
2. In Terminal (Mac) or Ubuntu (Windows):

```bash
cd ~
mkdir -p Projects
cd Projects
git clone <REPO_URL> pagebuilderv2
cd pagebuilderv2
```

Replace `<REPO_URL>` with the real Git URL your team gave you (HTTPS or SSH).

### Option B — Download / unzip

1. Download the project ZIP and unzip it.
2. Open Terminal / Ubuntu and go into that folder. Example:

```bash
cd ~/Downloads/pagebuilderv2
```

Use the real path to **your** unzipped folder.

### Confirm you are in the project root

```bash
ls
```

You should see at least:

- `package.json`
- `apps`
- `README.md`

If you do not see `package.json`, you are in the wrong folder — `cd` into the folder that contains it.

---

## 6. Create the MySQL database

The app expects a database named **`pagebuilderv2`**.

### Using the terminal (works on Mac and WSL)

If MySQL root has **no** password:

```bash
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS pagebuilderv2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

If MySQL root **has** a password:

```bash
mysql -uroot -p -e "CREATE DATABASE IF NOT EXISTS pagebuilderv2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Type the password when asked.

### Optional: GUI tools

You can also create an empty database named `pagebuilderv2` with **MySQL Workbench**, **TablePlus**, or **phpMyAdmin**. Character set: `utf8mb4`, collation: `utf8mb4_unicode_ci`.

---

## 7. First-time project setup

Run these only the **first time** (or after a fresh clone). Stay in the **project root** (the folder with `package.json`).

### 7.1 Install root npm packages

```bash
npm install
```

Wait until it finishes without red errors.

### 7.2 Install Laravel (API) dependencies and configure env

```bash
cd apps/api
composer install
```

Copy the example env file and generate an app key:

```bash
cp .env.example .env
php artisan key:generate
```

On native Windows PowerShell (not WSL), use this instead of `cp`:

```powershell
Copy-Item .env.example .env
```

### 7.3 Set your database password in `.env`

Open `apps/api/.env` in any text editor. Find these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pagebuilderv2
DB_USERNAME=root
DB_PASSWORD=
```

- If your MySQL root password is empty, leave `DB_PASSWORD=` empty.
- If you set a password, put it after `=` with **no spaces**, for example: `DB_PASSWORD=YourStrongPassword`

Also confirm (usually already correct):

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

Save the file.

### 7.4 Create tables, demo data, and storage link

Still inside `apps/api`:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

- `migrate` creates database tables.
- `db:seed` adds demo users, a Home page, products, and menus.
- `storage:link` makes uploaded images viewable in the browser.

### 7.5 Install admin (React) packages

```bash
cd ../admin
npm install
cd ../..
```

You should be back at the project root (`ls` shows `package.json` again).

### 7.6 Optional one-shot helper later

After Composer and root `npm install` are done once, you can refresh migrate/seed/admin install with:

```bash
npm run setup
```

That does **not** replace creating the MySQL database or running `composer install` / `key:generate`.

---

## 8. Run the project every day

From the **project root**:

```bash
npm run dev
```

Leave this terminal open. You should see two processes (api + admin) starting.

### Open in your browser

| What | URL |
|---|---|
| Admin login / studio | http://localhost:5173 |
| Public website | http://localhost:8000 |
| API health check | http://localhost:8000/up |

### Demo logins (from seeder)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@pagebuilder.test` | `password` |
| Editor | `editor@pagebuilder.test` | `password` |

### Quick demo path

1. Open http://localhost:5173 and log in as admin.
2. Open **Pages → Edit Home** — drag blocks, try AI (needs an API key in **Settings → AI**).
3. Click **Publish**, then open http://localhost:8000.

### Run API and admin in separate terminals (optional)

```bash
# Terminal 1
npm run dev:api

# Terminal 2
npm run dev:admin
```

---

## 9. How to stop

1. Click the terminal where `npm run dev` is running.
2. Press `Ctrl + C` (Mac and Windows/WSL).
3. Wait until both processes stop.

To start again later: open Terminal / Ubuntu, `cd` into the project root, run `npm run dev` again.

You do **not** need to repeat Section 7 every day — only after a fresh clone or when dependencies change.

---

## 10. Troubleshooting

| Problem | What to try |
|---|---|
| `command not found: php` / `node` / `composer` | Prerequisites are missing or not on PATH. Re-check Section 3 (Mac) or 4 (Windows). Close and reopen the terminal. |
| `Access denied for user 'root'@'localhost'` | Wrong `DB_PASSWORD` in `apps/api/.env`. Fix it and save. |
| `Unknown database 'pagebuilderv2'` | Create the database (Section 6), then run migrate/seed again. |
| `Connection refused` on MySQL | MySQL is not running. Mac: `brew services start mysql`. WSL: `sudo service mysql start`. |
| Login fails | Re-seed: `cd apps/api && php artisan db:seed --force` |
| Uploaded images return 404 | `cd apps/api && php artisan storage:link` |
| Port 8000 or 5173 already in use | Stop the other app using that port, or close an old `npm run dev` with `Ctrl+C`. |
| Admin page loads but API errors / CORS | Confirm `APP_URL`, `FRONTEND_URL`, and `SANCTUM_STATEFUL_DOMAINS` in `.env` match Section 7.3. |
| Fresh clone, no `.env` | Repeat Section 7.2–7.4. |
| `npm run dev` fails on `bash` / `lsof` on Windows | Use WSL (Section 4), or the fallback in [Section 13](#13-windows-without-wsl-fallback). |
| AI always errors | Expected until you add a key under **Settings → AI**. |
| PHP version too old | Install PHP 8.3+ (`php -v` must show 8.3 or higher). |

---

## 11. What’s included

- Drag-and-drop page builder (GrapesJS) with a modern admin UI
- Custom HTML block + page-level head/body scripts
- AI panel (API key in **Settings → AI**; clear hint if missing)
- CRUD: Pages, Products, Menus, Users, Media
- Settings: AI key, storage (`local` default / S3–R2 fields), DB fields
- Seeded demo Home page + products + menus

Defaults are local: **MySQL** + files under `apps/api/storage/app/public`.

---

## 12. More docs

| Doc | Purpose |
|---|---|
| [docs/SETUP_AND_SERVE.md](./docs/SETUP_AND_SERVE.md) | Extra setup notes, presentation walkthrough, layout |
| [docs/PROGRESS.md](./docs/PROGRESS.md) | Progress vs plan |
| [CONTINUITY.md](./CONTINUITY.md) | Resume checklist for the next session |

---

## 13. Windows without WSL (fallback)

Prefer WSL. If you cannot use it:

1. Install **Git for Windows** (includes Git Bash), **PHP 8.3+**, **Composer**, **Node 20+**, and **MySQL** using their Windows installers.
2. Do first-time setup (Section 7) in **Git Bash** or PowerShell from the project root.
3. Instead of `npm run dev`, start two terminals:

**Terminal 1 — API**

```bash
cd apps/api
php artisan serve --host=127.0.0.1 --port=8000
```

**Terminal 2 — Admin**

```bash
cd apps/admin
npm run dev -- --host 127.0.0.1 --port 5173
```

Then open the same URLs as in Section 8.

Note: the custom router in `scripts/dev-api.sh` will not run in this mode; basic local demo still works with `php artisan serve`.
