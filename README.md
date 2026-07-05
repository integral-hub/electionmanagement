# Election Management Platform

A multi-tenant Laravel application for organizations to run digital elections end to end: creating elections, managing positions and candidates, registering or importing voters, collecting ballots, and publishing results with role-based staff access and a full audit trail.

## Overview

The platform serves two distinct types of users:

- **Admin / Staff** — sign in at the main app URL, belong to an **Organization**, and manage one or more elections from a dashboard (create elections, configure settings, manage positions/candidates, manage voters, view results, manage staff/roles, view audit logs).
- **Voters** — access a dedicated portal at a per-election URL (`/e/{election-slug}`), register or log in, optionally verify their email and/or complete two-factor authentication, then cast a ballot.

Each organization's staff, roles, and data are isolated from other organizations (multi-tenancy via a `team_id`-scoped permission system).

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13, PHP 8.4 |
| Auth / Permissions | Laravel's built-in auth (staff) + a custom `voter` guard (voters), Spatie Laravel Permission (team-scoped roles/permissions) |
| Business logic | Actions & Service/Interface |
| File storage | Cloudinary |
| Audit logging | Spatie Activitylog |
| Frontend build | Vite, Tailwind CSS |
| Database (default/local) | MySQL/Postgres |

## Requirements

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL/Postgres database
- A Cloudinary account (for file uploads — candidate photos, voter document uploads)

## Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

npm run build     # or: npm run dev
```

## Configuration (.env)

The default `.env.example` only ships the standard Laravel keys. The following must be added manually for this project's features to work:

```env
# Cloudinary — required for all file uploads (candidate photos, voter documents)
CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>


Other configuration of note (all under `config/`):

- `config/otp.php` — OTP code length (`digits`, default 6) and expiry in minutes (`expiry`, default 5). Used for voter email verification and 2FA.
- `config/permission.php` — Spatie permission config; teams are enabled (`teams` => true) so roles/permissions are scoped per organization.
- `config/settings.php` — default system admin identity, read from the env vars above.
- `config/activitylog.php` — audit log retention/behaviour (Spatie Activitylog).
- `config/excel.php` — Laravel Excel (import/export) configuration.
- Mail (`MAIL_*`) — required for OTP codes, verification links, and staff/voter notification emails to actually deliver (defaults to the `log` driver, which writes emails to the log file instead of sending them).
- Queue (`QUEUE_CONNECTION`) — defaults to `database`. Notifications (import summaries, account-created emails, etc.) are queued, so run a worker in non-local environments:
  ```bash
  php artisan queue:work
  ```
Note: with `MAIL_MAILER=log` (the default), OTP codes and verification links are written to `storage/logs/laravel.log` instead of actually being emailed — check there when testing email verification or 2FA locally.

## Organization Registration Tokens

New organizations cannot self-register freely — signing up at `/register` requires a valid, unused **access token** from the `organization_tokens` table. This is effectively an invite-code / plan-gating mechanism: someone (currently: whoever seeds or manually inserts the token, there is no system admin UI for generating them yet) issues a token, and the token's `name` doubles as the plan the organization signs up under.


### Seeded test tokens

`database/seeders/OrganizationTokenSeeder.php` (run automatically by `php artisan migrate --seed`) inserts 15 ready-to-use, unused tokens for local testing:

- `STARTER-TOKEN-001` through `STARTER-TOKEN-005` (plan `STARTER`, `max_elections` = 1)
- `PRO-TOKEN-001` through `PRO-TOKEN-005` (plan `PRO`, `max_elections` = 2)
- `ENT-TOKEN-001` through `ENT-TOKEN-005` (plan `ENTERPRISE`, `max_elections` = 3)

Use any one of these in the "access token" field at `/register` to create a test organization locally. Each is single-use.

## Scheduled Tasks

`elections:sync-status` runs every minute (defined in `routes/console.php`) and automatically transitions election status based on the voting window in each election's settings:

```
* * * * * cd /electionmanagement && php artisan schedule:run >> /dev/null 2>&1
```

Locally, you can either run it once manually or keep it running in the foreground:

```bash
php artisan elections:sync-status   # run once
php artisan schedule:work           # keep running locally, in place of cron
```

## Demo 

There is hosted public demo; the fastest way to see the whole thing end to end: `https://elect.techtrovelab.com` .

**1. Register a new organization yourself:**
- Use any unused token from the list above (e.g. `PRO-TOKEN-001`) as the access token

**2. Create election:** create an election, add at least one position and one candidate, create registration form, edit election settings then open that election's page to get its voter-portal link (`/e/{election-slug}`) and try the voter side — register or log in as a voter, cast a ballot, and view the confirmation screen. Check the results page back on the admin side afterward.

