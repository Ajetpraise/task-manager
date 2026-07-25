# Task Manager

A Laravel task manager with authentication, project ownership, nested tasks, status filtering, and a responsive Blade interface.

## Demo Video

[Watch the Task Manager demo](docs/task-manager-demo.mov)

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and pnpm
- SQLite

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
pnpm install
pnpm run build
```

The example environment uses SQLite by default. The `.env` file is ignored by Git and should never be committed.

## Run the application

Start the application and asset development servers together:

```bash
composer run dev
```

Alternatively, after building the frontend assets, start only Laravel:

```bash
php artisan serve
```

Open `http://localhost:8000`, register an account, and use the Projects navigation item.

## Database

Rebuild the SQLite database from all migrations:

```bash
php artisan migrate:fresh
```

Project and task foreign keys use cascade deletion, so deleting a user removes their projects and deleting a project removes its tasks.

## Tests and code quality

Run the application test suite:

```bash
php artisan test
```

Build the production frontend bundle:

```bash
pnpm run build
```

Check PHP formatting:

```bash
./vendor/bin/pint --test
```

## Authentication

The existing Laravel starter-kit authentication is preserved and powered by Laravel Fortify. It provides registration, login, logout, password reset, password confirmation, and authenticated-route protection. Successful login and registration redirect to the projects page.
