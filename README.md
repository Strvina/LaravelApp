# OpsDesk

OpsDesk is a Laravel portfolio project that combines inventory management, task tracking, expense monitoring, contact handling, and a role-aware admin area in one dashboard-style application. Alongside the web app, it exposes a token-authenticated REST API with interactive documentation.

## What It Shows

- Authentication and profile management with Laravel Breeze
- Role-based access control, enforced through Laravel Policies (not just UI hiding)
- Product CRUD with filterable inventory views, image uploads, CSV import/export, and soft-delete trash/restore
- Todo management with recurring tasks and a custom drag-and-drop status board (native HTML5 drag on desktop, pointer events on touch)
- Expense and income tracking with monthly charts
- Contact form with server-side validation and email delivery
- Activity logging for key create, update, and delete actions
- Low-stock notifications, created and resolved automatically, plus a scheduled sweep
- A REST API (Laravel Sanctum, token auth, rate-limited login) with auto-generated interactive docs at `/docs/api`
- Scheduled commands for recurring task generation and low-stock alert syncing
- Automated feature tests and a GitHub Actions CI pipeline

## Stack

- PHP 8.2
- Laravel 12
- Laravel Sanctum
- Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- PHPUnit

## Main Modules

### Dashboard
The authenticated landing page presents the project clearly and acts as the central navigation hub.

### Inventory
Users can browse products, apply real-time filters, and view individual product details, including an image where uploaded. Admins can create, edit, delete (soft-delete with a trash/restore flow), and bulk import/export products via CSV.

### Tasks
Users can create tasks, assign priorities, mark recurring schedules, and move tasks between status columns with drag and drop.

### Finance
Users can record income and expense entries, inspect balances, and view monthly chart summaries.

### Admin
Admins can access a dashboard with KPIs, low-stock notifications, activity logs, and user management tools.

### API
A REST API mirrors the core web features (todos, products, expenses) behind Sanctum bearer-token auth. Log in via `POST /api/login` to get a token, then browse and try every endpoint at `/docs/api`.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

## Testing

```bash
php artisan test
```

The suite runs against an isolated in-memory SQLite database (see `phpunit.xml`) and is also run automatically on every push via GitHub Actions (`.github/workflows/tests.yml`), alongside a Laravel Pint code-style check.

## Portfolio Notes

This project is intentionally broader than a simple CRUD demo. It is meant to show:

- working Laravel architecture across controllers, requests, services, models, policies, and migrations
- practical business-oriented features instead of tutorial-only examples
- a REST API layered on top of the same domain logic as the web app, not a separate demo
- test coverage, CI, and post-build cleanup
- iterative polishing from learning project into presentation-ready portfolio work
