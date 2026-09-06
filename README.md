# OpsDesk

OpsDesk is a Laravel portfolio project that combines inventory management, task tracking, expense monitoring, contact handling, and a role-aware admin area in one dashboard-style application.

## What It Shows

- Authentication and profile management with Laravel Breeze
- Role-based access for admin-only features
- Product CRUD with filterable inventory views
- Todo management with recurring tasks and drag-and-drop status updates
- Expense and income tracking with monthly charts
- Contact form with server-side validation and email delivery
- Activity logging for key create, update, and delete actions
- Scheduled command support for recurring task generation
- Automated feature tests

## Stack

- PHP 8.2
- Laravel 12
- Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- PHPUnit

## Main Modules

### Dashboard
The authenticated landing page presents the project clearly and acts as the central navigation hub.

### Inventory
Users can browse products, apply real-time filters, and view individual product details. Admins can create and delete products.

### Tasks
Users can create tasks, assign priorities, mark recurring schedules, and move tasks between status columns with drag and drop.

### Finance
Users can record income and expense entries, inspect balances, and view monthly chart summaries.

### Admin
Admins can access a dashboard with KPIs, low-stock notifications, and user management tools.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Testing

```bash
php artisan test
```

At the time of the latest cleanup pass, the test suite passes successfully.

## Portfolio Notes

This project is intentionally broader than a simple CRUD demo. It is meant to show:

- working Laravel architecture across controllers, requests, services, models, and migrations
- practical business-oriented features instead of tutorial-only examples
- test coverage and post-build cleanup
- iterative polishing from learning project into presentation-ready portfolio work
