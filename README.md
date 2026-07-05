# Election Management Platform

A multi-tenant Laravel application for organizations to run digital elections end to end: creating elections, managing positions and candidates, registering or importing voters, collecting ballots, and publishing results with role-based staff access and an audit trail.

## Overview

The platform serves two distinct types of users:

- **Admin / Staff** — sign in at the main app URL, belong to an **Organization**, and manage one or more elections from a dashboard (create elections, configure settings, manage positions/candidates, manage voters, view results, manage staff/roles, view audit logs).
- **Voters** — access a dedicated portal at a per-election URL (e.g. `/e/{election-slug}`), register or log in, optionally verify their email and/or complete two-factor authentication, then cast a ballot.

Each organization's staff, roles, and data are isolated from other organizations (multi-tenancy via a `team_id`-scoped permission system).

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13, PHP 8.4 |
| Auth / Permissions | Laravel's built-in auth (staff) + a custom `voter` guard (voters), Spatie Laravel Permission (team-scoped roles/permissions) |
| Business logic | `lorisleiva/laravel-actions` (single-purpose Action classes) |
| File storage | Cloudinary (`cloudinary/cloudinary_php`) |
| Spreadsheet import/export | `maatwebsite/excel` |
| OTP (email verification / 2FA) | `tzsk/otp` |
| Audit logging | `spatie/laravel-activitylog` |
| Frontend build | Vite, Tailwind CSS |
| Database (default/local) | MySQL/Postgres |

## Requirements

- PHP 8.4+
- Composer
- Node.js + npm
- A database (MySQL/Postgres supported)
- A Cloudinary account (for file uploads — candidate photos, voter document uploads)

## Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate


php artisan migrate --seed

npm run build     # or: npm run dev
php artisan serve
```

## Configuration (.env)

The default `.env.example` only ships the standard Laravel keys. The following must be added manually for this project's features to work:

```env
# Cloudinary — required for all file uploads (candidate photos, voter documents)
CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>

# Default system administrator (used by the initial seeder)
SYSTEM_ADMIN_NAME="System Admin"
SYSTEM_ADMIN_EMAIL=admin@system.com
```

Other configuration of note (all under `config/`):

- `config/otp.php` — OTP code length (`digits`, default 6) and expiry in minutes (`expiry`, default 5). Used for voter email verification and 2FA.
- `config/activitylog.php` — audit log retention/behaviour (Spatie Activitylog).
- `config/excel.php` — Laravel Excel (import/export) configuration.
- Mail (`MAIL_*`) — required for OTP codes, verification links, and staff/voter notification emails to actually deliver (defaults to the `log` driver, which writes emails to the log file instead of sending them).
- Queue (`QUEUE_CONNECTION`): defaults to `database`. Notifications (import summaries, account-created emails, etc.) are queued, so run a worker in non-local environments:
  ```bash
  php artisan queue:work

  ```
### Configure Cron (Production)
