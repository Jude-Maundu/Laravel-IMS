# Grey Apple Events — Inventory Management System

A production inventory management system built for Grey Apple Events Limited, Nairobi Kenya.
Tracks inventory lifecycle from warehouse dispatch through events to return, cleaning, repair and write-off.

## Tech Stack

- PHP 8.3 / Laravel 12
- MySQL
- Blade + Tailwind CSS + Vite
- Spatie Laravel Permission
- DomPDF (report generation)

## Local Development Setup

### Requirements
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8+

### Installation

1. Clone the repository
   git clone https://github.com/your-username/grey-apple-ims.git
   cd grey-apple-ims

2. Install PHP dependencies
   composer install

3. Install Node dependencies
   npm install

4. Copy environment file and configure
   cp .env.example .env
   php artisan key:generate

5. Create the database in MySQL, then update .env with your credentials

6. Run migrations and seed
   php artisan migrate --seed

7. Link storage
   php artisan storage:link

8. Build assets
   npm run build

9. Start the development server
   php artisan serve

## Project Structure

- app/Models        — Eloquent models
- app/Http/Controllers — Application controllers
- resources/views   — Blade templates
- database/migrations — Database schema
- database/seeders  — Demo data seeders
- public/images     — Brand assets
- public/sounds     — Notification sounds

## Key Modules

- Inventory — Full item lifecycle tracking
- Events    — Event creation, dispatch, checklist, return
- Repairs   — Repair tracking and report generation
- Reports   — PDF reports for inventory, events, damage

## Branching Strategy

- main       — Production only. Never commit directly.
- develop    — Active development branch. All PRs merge here first.
- feature/*  — Individual feature branches off develop.

## Contributing

Create a feature branch off develop:
  git checkout develop
  git checkout -b feature/your-feature-name

When complete, open a pull request to develop.

## Licence

Private and confidential. Grey Apple Events Limited. All rights reserved.


# Laravel-IMS
# Laravel-IMS
