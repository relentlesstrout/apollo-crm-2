# Apollo CRM

Apollo CRM is a customer relationship management (CRM) application for a window cleaning business. It manages customers, properties, services, prices, repeat schedules and cleaning jobs.

The application uses Laravel 13, Livewire 4, React 19, Tailwind CSS 4 and PostgreSQL. It runs in Docker containers through [Laravel Sail](https://laravel.com/docs/sail).

## Contents

- [Requirements](#requirements)
- [First-time setup](#first-time-setup)
- [Start the application (after the first-time setup)](#start-the-application-after-the-first-time-setup)
- [Stop the application](#stop-the-application)
- [Run the tests](#run-the-tests)
- [Connect a database viewer](#connect-a-database-viewer)
- [Troubleshooting](#troubleshooting)

## Requirements

Install these tools on your computer before you start:

| Tool | Why you need it |
|---|---|
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | Runs PHP, PostgreSQL, Node.js and Mailpit in containers. You do not need to install PHP, Composer or Node.js on your computer. |
| [Git](https://git-scm.com/) | Downloads the source code. |
| PHP 8.3+ and [Composer](https://getcomposer.org/) (optional) | Installs the PHP packages one time, before Sail can run. If you do not have them, step 2 shows a Docker alternative. |

Make sure that Docker Desktop is open and running before you do the steps below.

## First-time setup

Do these steps one time only, on a new computer or a new copy of the repository.

### 1. Get the source code

```bash
git clone https://github.com/relentlesstrout/apollo-crm-2.git
cd apollo-crm-2
```

### 2. Install the PHP dependencies

The `vendor/bin/sail` command does not exist until Composer installs the PHP packages. If you have PHP 8.3 or higher and [Composer](https://getcomposer.org/) on your computer, run:

```bash
composer install
```

If you do not have PHP on your computer, use this command. It runs Composer in a temporary Docker container:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

**Result:** A `vendor/` folder is created, and the file `vendor/bin/sail` exists.

### 3. Create the environment file

The `.env` file holds the local settings for the application. Git does not track it, so you must create it from the example file:

```bash
cp .env.example .env
```

The example file already contains the correct settings for Sail:

| Setting | Value | Why |
|---|---|---|
| `DB_HOST` | `pgsql` | The application runs inside Docker, so it finds the database by the service name `pgsql`, not by `127.0.0.1`. |
| `DB_PASSWORD` | `password` | Postgres gets this password when its container starts for the first time. |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT` | `smtp`, `mailpit`, `1025` | Sail includes Mailpit, a local mail catcher. Mailpit receives every email that the application sends and shows it in a web page. No email goes to real people. |

### 4. Change the ports (only if necessary)

Sail uses ports 80, 5173, 5432, 1025 and 8025 on your computer. If a different project already uses one of these ports, `vendor/bin/sail up -d` fails. To see which ports other containers use, run `docker ps`.

To use different ports, add lines like these to `.env`:

```dotenv
APP_PORT=8080
APP_URL=http://localhost:8080
FORWARD_DB_PORT=5433
FORWARD_MAILPIT_PORT=1026
FORWARD_MAILPIT_DASHBOARD_PORT=8026
```

If no port is in use, skip this step.

### 5. Start the containers

```bash
vendor/bin/sail up -d
```

The first start builds the Docker image. This can take several minutes.

**Result:** `vendor/bin/sail ps` shows three running services: `laravel.test`, `pgsql` and `mailpit`.

### 6. Create the application key

Laravel uses this key to encrypt sessions and cookies:

```bash
vendor/bin/sail artisan key:generate
```

**Result:** The `APP_KEY` line in `.env` now has a value.

### 7. Create the database tables

```bash
vendor/bin/sail artisan migrate
```

**Result:** The command lists each migration with `DONE`.

### 8. Add the test data

This command adds the login accounts and sample services, customers, properties and schedules:

```bash
vendor/bin/sail artisan db:seed
```

The seeder creates these login accounts, and many more with random names. All the accounts in this table use the password `password`:

| Email | Role |
|---|---|
| `admin1@apollo.com` | Admin |
| `cleaner1@apollo.com` | Cleaner |
| `customer1@apollo.com` | Customer |

At the moment, only the Admin role can open pages after login. The Cleaner and Customer roles get a "403 Forbidden" error.

### 9. Install the JavaScript dependencies

```bash
vendor/bin/sail npm install
```

### 10. Start the Vite development server

Vite is the tool that serves the CSS and JavaScript to the browser. Open a new terminal window and run:

```bash
vendor/bin/sail npm run dev
```

Keep this terminal window open. If you close it, the pages show a "Vite manifest not found" error.

**Result:** The terminal shows `VITE ... ready` and the text `LARAVEL ... plugin`.

### 11. Log in

1. Open [http://localhost](http://localhost) in your browser. If you changed `APP_PORT`, use that port, for example `http://localhost:8080`.
2. Log in as `admin1@apollo.com` with the password `password`.

**Result:** The dashboard opens, and the top menu shows Customers, Properties, Cleaning Jobs and Services.

The first-time setup is now complete.

## Start the application (after the first-time setup)

Do these steps each time that you want to work on the application.

1. Open Docker Desktop and make sure that it is running.

2. Start the containers:

   ```bash
   vendor/bin/sail up -d
   ```

3. Start the Vite development server in a new terminal window. Vite rebuilds the CSS and JavaScript each time that you save a file, and refreshes the browser:

   ```bash
   vendor/bin/sail npm run dev
   ```

   Keep this terminal window open while you work.

4. Start the queue worker in a new terminal window. The invitation email is sent through the queue, so it does not arrive without a worker:

   ```bash
   vendor/bin/sail artisan queue:work
   ```

   Keep this terminal window open while you work.

5. Optional: start the task scheduler in a new terminal window. The scheduler runs `cleaning-jobs:generate` each day at 06:00 to create the cleaning jobs that are due:

   ```bash
   vendor/bin/sail artisan schedule:work
   ```

   To create the due cleaning jobs immediately, run the command yourself:

   ```bash
   vendor/bin/sail artisan cleaning-jobs:generate
   ```

6. If you pulled new code from Git, update the dependencies and the database:

   ```bash
   vendor/bin/sail composer install
   ```

   ```bash
   vendor/bin/sail npm install
   ```

   ```bash
   vendor/bin/sail artisan migrate
   ```

7. Open these pages in your browser:

   | Page | Address |
   |---|---|
   | The application | [http://localhost](http://localhost) |
   | Mailpit (emails that the application sent) | [http://localhost:8025](http://localhost:8025) |

## Stop the application

1. Press `Ctrl+C` in each terminal window that runs Vite, the queue worker or the scheduler.
2. Stop the containers:

   ```bash
   vendor/bin/sail stop
   ```

The database data stays in a Docker volume. It is available again when you next start the containers.

## Run the tests

The tests use PHPUnit and a separate database named `testing`. Sail creates the `testing` database automatically the first time that the `pgsql` container starts.

Run all the tests:

```bash
vendor/bin/sail artisan test --compact
```

Run the tests in one file:

```bash
vendor/bin/sail artisan test --compact tests/Feature/CleaningJobControllerTest.php
```

**Result:** Each passing test shows a green dot, and the summary line shows the number of passed tests.

## Connect a database viewer

Use these settings in a database viewer such as TablePlus or DBeaver. The containers must be running.

| Setting | Value |
|---|---|
| Type | PostgreSQL |
| Host | `127.0.0.1` |
| Port | `5432`, or the value of `FORWARD_DB_PORT` in `.env` |
| Database | `apollo_crm_2`, or the value of `DB_DATABASE` in `.env` |
| User | `root`, or the value of `DB_USERNAME` in `.env` |
| Password | `password`, or the value of `DB_PASSWORD` in `.env` |

The database viewer runs on your computer, not in Docker. For this reason, it uses `127.0.0.1` and not `pgsql`.

## Troubleshooting

| Problem | Cause and fix |
|---|---|
| `vendor/bin/sail: No such file or directory` | The PHP dependencies are not installed. Do [step 2](#2-install-the-php-dependencies). |
| `Bind for 0.0.0.0:80 failed: port is already allocated` | A different program uses the port. Set a different port in `.env` (see [step 4](#4-change-the-ports-only-if-necessary)), then run `vendor/bin/sail up -d` again. |
| `SQLSTATE[08006] ... Connection refused` | `DB_HOST` in `.env` is not `pgsql`, or the containers are not running. Correct `.env`, then run `vendor/bin/sail up -d`. |
| `password authentication failed for user "root"` | The database password changed after the first start. Postgres keeps the password from its first start. To reset the database and delete all its data, run `vendor/bin/sail down -v`, then `vendor/bin/sail up -d`, then do steps 7 and 8 again. |
| `Vite manifest not found` or `Unable to locate file in Vite manifest` | The Vite development server is not running. Start `vendor/bin/sail npm run dev` and keep it open. |
| Page changes do not show in the browser | Vite is not running. Start `vendor/bin/sail npm run dev`. |
| Invitation emails do not arrive in Mailpit | The queue worker is not running, or the mail settings in `.env` are not correct. Start `vendor/bin/sail artisan queue:work`. Compare the `MAIL_` lines in `.env` with the lines in `.env.example`. |
| I cannot log in after a new setup | The seeder did not run, or it stopped before the end. Run `vendor/bin/sail artisan migrate:fresh --seed`. This command deletes all the tables, creates them again and adds the test data. |
