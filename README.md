# Event Management System

A Laravel REST API for creating events, managing participants, and authenticating users via [Laravel Passport](https://laravel.com/docs/passport) OAuth2 personal access tokens.

## Features

- User authentication (login, logout, profile)
- Create events with optional participants
- Add participants to existing events
- OpenAPI 3.0 documentation with Swagger UI
- Database seeders with demo data

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 13 |
| PHP | 8.3+ |
| Authentication | Laravel Passport 13 |
| API Docs | L5-Swagger (OpenAPI 3.0) |
| Database | MySQL (default) |

## Requirements

- PHP >= 8.3 with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer
- MySQL 8+ (or compatible database)
- Node.js 18+ and npm (for frontend assets, optional for API-only usage)

## Quick Start

### 1. Clone and install dependencies

```bash
git clone <repository-url> event-management
cd event-management

composer install
```

### 2. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

> **DockR users:** The `.env.example` is pre-configured for [DockR](https://dockr.in) with `DB_HOST=dockr_mysql` and `APP_URL=http://event-management-system.localhost`.

### 3. Database setup

Run migrations, install Passport encryption keys, and seed demo data:

```bash
php artisan migrate
php artisan passport:install
php artisan db:seed
```

### 4. Generate API documentation

```bash
php artisan l5-swagger:generate
```

### 5. Start the development server

```bash
php artisan serve
```

Or use the combined dev script (server, queue, logs, and Vite):

```bash
composer dev
```

One-command setup (install, migrate, build assets):

```bash
composer setup
```

> After `composer setup`, still run `php artisan passport:install` and `php artisan db:seed` before using the API.

## API Documentation (Swagger / OpenAPI)

| Resource | URL |
|----------|-----|
| Swagger UI | `http://localhost:8000/api/documentation` |
| OpenAPI JSON | `http://localhost:8000/docs` |

### Using Swagger UI

1. Open `/api/documentation` in your browser.
2. Call **POST /login** with seeded credentials (see below).
3. Copy the `access_token` from the response.
4. Click **Authorize**, enter `Bearer <your-token>`, and confirm.
5. Try the protected endpoints under **Events** and **Authentication**.

Regenerate docs after changing controller annotations:

```bash
php artisan l5-swagger:generate
```

## Seeded Demo Data

After running `php artisan db:seed`, the following accounts are available:

| Email | Password | Role |
|-------|----------|------|
| `organizer@example.com` | `password` | Primary demo user with 5 events |
| `demo@example.com` | `password` | Secondary demo user |

The organizer account includes sample events (conference, webinar, workshop) with participants, plus 2 additional factory-generated events.

## API Endpoints

All API routes are prefixed with `/api`.

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/login` | No | Obtain a Bearer access token |
| `POST` | `/api/logout` | Bearer | Revoke the current token |
| `GET` | `/api/me` | Bearer | Get authenticated user profile |

### Events

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/events` | Bearer | Create an event (with optional participants) |
| `POST` | `/api/events/{id}/participants` | Bearer | Add participants to an event |

## Example Requests

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"organizer@example.com","password":"password"}'
```

### Create an event

```bash
curl -X POST http://localhost:8000/api/events \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "title": "Team Meetup",
    "description": "Monthly sync",
    "location": "Office",
    "event_date": "2026-08-01 10:00:00",
    "participants": [
      {"name": "Jane Doe", "email": "jane@example.com", "phone": "+919876543210"}
    ]
  }'
```

### Add participants

```bash
curl -X POST http://localhost:8000/api/events/1/participants \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "participants": [
      {"name": "John Doe", "email": "john@example.com"}
    ]
  }'
```

## Database Seeders

Seeders live in `database/seeders/`:

| Seeder | Purpose |
|--------|---------|
| `DatabaseSeeder` | Orchestrates all seeders |
| `UserSeeder` | Creates demo users |
| `EventSeeder` | Creates sample events and participants |

Run individual seeders:

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=EventSeeder
```

Refresh database and re-seed:

```bash
php artisan migrate:fresh --seed
```

## Running Tests

```bash
composer test
# or
php artisan test
```

## Project Structure

```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php      # Login, logout, profile
│   └── EventController.php     # Events and participants
├── Models/
│   ├── Event.php
│   ├── Participant.php
│   └── User.php
└── OpenApi/
    └── OpenApiSpec.php         # Shared OpenAPI schemas

database/
├── factories/                  # Model factories for testing/seeding
├── migrations/                 # Database schema
└── seeders/                    # Database seeders

routes/
└── api.php                     # API route definitions
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
