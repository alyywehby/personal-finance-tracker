# Personal Finance Tracker

A full-featured Personal Finance Tracker web application built with **Laravel 12**, **PostgreSQL 16**, and **Docker**. Supports multilingual UI (English + Arabic/RTL), REST API with Sanctum authentication, CSV export, monthly email summaries, and responsive design.

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Database:** PostgreSQL 16
- **Frontend:** Blade + Alpine.js + Tailwind CSS (CDN)
- **Charts:** Chart.js (CDN)
- **Auth:** Laravel Sanctum (web sessions + API tokens)
- **Containerization:** Docker + Docker Compose
- **Mail:** Mailpit (local dev SMTP)
- **Queue:** Laravel Queue (database driver)
- **i18n:** Laravel Localization (EN + AR)

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) >= 24.0
- [Docker Compose](https://docs.docker.com/compose/) >= 2.0

---

## Quick Start

### 1. Clone and configure

```bash
git clone <repo-url> personal-finance-tracker
cd personal-finance-tracker
cp .env.example .env
```

### 2. Start all services

```bash
docker compose up -d
```

This starts: **app** (PHP-FPM), **nginx** (port 8080), **db** (PostgreSQL), **mailpit** (mail), **queue** (worker), **pgadmin**.

### 3. Generate app key

```bash
docker compose exec app php artisan key:generate
```

### 4. Run migrations and seed demo data

```bash
docker compose exec app php artisan migrate --seed
```

### 5. Access the application

| Service   | URL                        |
|-----------|----------------------------|
| Web App   | http://localhost:8080       |
| Mailpit   | http://localhost:8025       |
| pgAdmin   | http://localhost:5050       |

---

## Demo Credentials

| Field    | Value                |
|----------|----------------------|
| Email    | `demo@finance.app`   |
| Password | `password`           |

---

## Features

### Web UI
- **Dashboard** — Monthly income/expense summary cards, 6-month bar chart, category pie chart, recent transactions
- **Transactions** — Paginated list with filters (category, type, date range), inline edit/delete, CSV export
- **Categories** — Color-coded categories with emoji icons, add/edit/delete with transaction protection
- **Reports** — Monthly daily spending chart, category breakdown table, one-click email summary

### Multilingual
- **English (LTR)** and **Arabic (RTL)** support
- Toggle language via **EN | AR** buttons in the navbar
- Locale stored per user in the database
- Guest pages detect browser language via `Accept-Language` header

### REST API (v1)
Full CRUD API with Sanctum token auth. See [API Examples](#api-examples) below.

### Email Notifications
- Monthly summary email dispatched via queue on the 1st of each month at 08:00
- Manual trigger from the Reports page
- HTML + plain text versions, locale-aware (EN/AR)

---

## Running Tests

```bash
# Run all tests (requires PostgreSQL finance_test database)
docker compose exec app php artisan test

# Run specific test file
docker compose exec app php artisan test tests/Feature/Api/AuthApiTest.php

# With coverage
docker compose exec app php artisan test --coverage
```

**Note:** Tests use a separate `finance_test` database. Create it first:
```bash
docker compose exec db psql -U finance_user -c "CREATE DATABASE finance_test;"
```

---

## Queue Worker

The `queue` service runs automatically via Docker. To check status:

```bash
docker compose logs queue
docker compose exec app php artisan queue:monitor
```

To manually trigger the monthly summary command:

```bash
docker compose exec app php artisan finance:send-monthly-summary
```

---

## Switching Language

1. **Web UI:** Click the **EN** or **AR** button in the sidebar (desktop) or top navbar (mobile)
2. **API:** `PUT /api/v1/user/locale` with `{ "locale": "ar" }`

---

## API Examples

### Register

```bash
curl -X POST http://localhost:8080/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
```

### Login

```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@finance.app","password":"password"}'
```

### Create Transaction (replace TOKEN with the token from login)

```bash
curl -X POST http://localhost:8080/api/v1/transactions \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"type":"expense","amount":50.00,"category_id":1,"transaction_date":"2024-03-15","description":"Lunch"}'
```

### List Transactions with Filters

```bash
curl "http://localhost:8080/api/v1/transactions?type=expense&from=2024-01-01&to=2024-03-31" \
  -H "Authorization: Bearer TOKEN"
```

### Export CSV

```bash
curl "http://localhost:8080/api/v1/transactions/export?type=expense" \
  -H "Authorization: Bearer TOKEN" \
  -o transactions.csv
```

### Get Monthly Report

```bash
curl "http://localhost:8080/api/v1/reports/monthly?year=2024&month=3" \
  -H "Authorization: Bearer TOKEN"
```

### Send Summary Email

```bash
curl -X POST http://localhost:8080/api/v1/reports/send-summary \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"year":2024,"month":3}'
```

### Update Locale

```bash
curl -X PUT http://localhost:8080/api/v1/user/locale \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"locale":"ar"}'
```

---

## API Reference

### Authentication

| Method | Endpoint                  | Description         |
|--------|---------------------------|---------------------|
| POST   | `/api/v1/auth/register`   | Register new user   |
| POST   | `/api/v1/auth/login`      | Login, get token    |
| POST   | `/api/v1/auth/logout`     | Revoke token        |

### Transactions (Protected)

| Method | Endpoint                         | Description             |
|--------|----------------------------------|-------------------------|
| GET    | `/api/v1/transactions`           | List (paginated, filtered) |
| POST   | `/api/v1/transactions`           | Create                  |
| GET    | `/api/v1/transactions/{id}`      | Show                    |
| PUT    | `/api/v1/transactions/{id}`      | Update                  |
| DELETE | `/api/v1/transactions/{id}`      | Delete                  |
| GET    | `/api/v1/transactions/export`    | CSV download            |

### Categories (Protected)

| Method | Endpoint                     | Description      |
|--------|------------------------------|------------------|
| GET    | `/api/v1/categories`         | List             |
| POST   | `/api/v1/categories`         | Create           |
| PUT    | `/api/v1/categories/{id}`    | Update           |
| DELETE | `/api/v1/categories/{id}`    | Delete           |

### Reports (Protected)

| Method | Endpoint                          | Description              |
|--------|-----------------------------------|--------------------------|
| GET    | `/api/v1/reports/monthly`         | Monthly summary          |
| GET    | `/api/v1/reports/summary`         | Last 6 months            |
| POST   | `/api/v1/reports/send-summary`    | Queue email              |

---

## Docker Services

| Service   | Image                | Port(s)          | Description         |
|-----------|----------------------|------------------|---------------------|
| app       | php:8.2-fpm (custom) | internal 9000    | PHP-FPM application |
| nginx     | nginx:alpine         | 8080:80          | Web server          |
| db        | postgres:16-alpine   | 5432:5432        | Database            |
| mailpit   | axllent/mailpit      | 1025, 8025:8025  | SMTP + Web UI       |
| queue     | (same as app)        | —                | Queue worker        |
| pgadmin   | dpage/pgadmin4       | 5050:80          | DB admin UI         |

---

## Project Structure

```
├── app/
│   ├── Console/Commands/       # Artisan commands (SendMonthlySummaryCommand)
│   ├── Http/
│   │   ├── Controllers/        # Web controllers
│   │   │   ├── Api/V1/         # API v1 controllers
│   │   │   └── Auth/           # Auth controllers
│   │   ├── Middleware/         # SetLocale middleware
│   │   ├── Requests/           # Form request validation
│   │   └── Resources/          # API resources
│   ├── Jobs/                   # SendMonthlySummaryJob
│   ├── Mail/                   # MonthlySummaryMail
│   ├── Models/                 # User, Category, Transaction
│   └── Services/               # TransactionService, ReportService
├── database/
│   ├── factories/              # User, Category, Transaction factories
│   ├── migrations/             # All DB migrations
│   └── seeders/                # DatabaseSeeder
├── docker/
│   ├── nginx/default.conf      # Nginx config
│   └── php/php.ini             # PHP config
├── lang/
│   ├── en/                     # English translations
│   └── ar/                     # Arabic translations
├── resources/views/
│   ├── auth/                   # Login, Register
│   ├── categories/             # Categories list
│   ├── dashboard/              # Dashboard
│   ├── emails/                 # Email templates
│   ├── layouts/                # app.blade.php, guest.blade.php
│   ├── reports/                # Reports page
│   └── transactions/           # Transaction views
├── routes/
│   ├── api.php                 # API routes
│   ├── console.php             # Scheduler
│   └── web.php                 # Web routes
└── tests/Feature/Api/          # PHPUnit API tests
```

---

## Environment Variables

Key variables in `.env`:

| Variable           | Description                          | Default              |
|--------------------|--------------------------------------|----------------------|
| `DB_CONNECTION`    | Database driver                      | `pgsql`              |
| `DB_DATABASE`      | Database name                        | `finance_tracker`    |
| `QUEUE_CONNECTION` | Queue driver                         | `database`           |
| `MAIL_HOST`        | SMTP host (mailpit in docker)        | `mailpit`            |
| `MAIL_PORT`        | SMTP port                            | `1025`               |
| `APP_LOCALE`       | Default locale                       | `en`                 |

---

## License

MIT
