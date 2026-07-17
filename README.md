# Sovryx Tech — Team Portal

A lightweight PHP-based employee portal for registration, authentication, document management and basic admin workflows. Designed as a small MVC-style app (controllers + services) that runs on any PHP web server and is managed with Composer.

## Stack
- Language(s): PHP (server-side), CSS, JavaScript
- Runtime: Plain PHP + Composer (no full-stack framework required)
- Dependencies: Composer-managed (see composer.json). The app uses an internal MVC-like layout with controllers in `src/Controller` and services in `src/Service`.

## Quick features
- User registration, login, logout and verification
- Admin area for user management
- Document upload/management and QR code generation
- Contact, careers and privacy pages
- SMTP/email support (Mailtrap sandbox default, can be switched to real SMTP)

## Repository layout
Top-level entries (important items only):
```
README.md           — this file (currently minimal)
index.php           — application front page / router entry
register.php        — registration page
login.php           — login page
logout.php          — logout handler
verify.php          — verification flow
about.php           — about page
careers.php         — careers listing/form
contact.php         — contact page
privacy.php         — privacy page

config/              — PHP configuration files (app, database, mail)
src/                 — application PHP source (Controller, Service)
  Controller/        — controllers: AuthController, RegistrationController, EmployeeController, AdminController, VerificationController
  Service/           — services: DocumentManager, QRCodeGenerator
admin/               — admin-facing pages/assets
dashboard/           — dashboard pages
database/            — DB-related scripts or migrations (if any)
assets/ images/ uploads/ logs/ vendor/ — static assets, uploads, runtime files, and vendor code
composer.json        — Composer manifest
composer.lock        — lockfile
composer.phar        — shipped composer binary (optional)
```

How it fits together:
- Public entry points and simple page scripts (index.php, register.php, login.php) hand off to controllers in `src/Controller`.
- Business logic and utilities live in `src/Service` (document handling, QR code generation).
- Configuration is stored in PHP files under `config/` (no environment variables by default).

## Configuration
Edit the files in `config/` to match your environment:
- config/app.php
  - `site_name`, `base_url`, `secret_key`, session settings, upload constraints
  - Important: change `secret_key` to a strong random value in production.
- config/database.php
  - Update `host`, `port`, `dbname`, `username`, `password` to point to your MySQL (or compatible) database.
- config/mail.php
  - SMTP settings; defaults point to Mailtrap sandbox and `debug_mode` is `true` to log emails to `logs/emails.log`.

Logs, uploads and other runtime files:
- `logs/` — email debug log location (mail debug path referenced in config)
- `uploads/` — user-uploaded files (ensure this directory exists and is writable)

## Quick start (development)
1. Clone the repo:
   ```
   git clone https://github.com/sovryxtech/team-portal.git
   cd team-portal
   ```
2. Install dependencies (recommended to use Composer installed globally; composer.phar is also included):
   ```
   composer install
   ```
3. Update configuration files in `config/`:
   - Set database credentials in `config/database.php`
   - Update `config/app.php` → `base_url` and `secret_key`
   - Configure SMTP or keep `debug_mode` true in `config/mail.php` for development
4. Ensure runtime folders exist and are writable:
   ```
   mkdir -p uploads logs
   chmod 750 uploads logs
   ```
5. Start a local server (for quick testing):
   ```
   php -S localhost:8000 -t .
   ```
   Then open http://localhost:8000/ in your browser. For production, use Apache/Nginx + PHP-FPM and point your virtual host to the project directory.

Notes:
- The project expects a database (default name `employee_portal` in config). Create the DB and run any SQL setup scripts found under `database/` if present, or import your schema.
- If you use the built-in server, some rewrite/pretty URLs may not work exactly like on Apache; prefer a proper webserver for feature parity.

## Security & deployment checklist
- Replace `config/app.php` → `secret_key` with a secure, random secret.
- Disable or remove `composer.phar` from production if unnecessary.
- Set `config/mail.php` → `debug_mode = false` when using real SMTP.
- Ensure `uploads/` and `logs/` are outside the webroot or protected (or configure your webserver to deny direct access).
- Use HTTPS in production and restrict file permissions on configuration files.

## Development notes
- Controllers are located in `src/Controller`. Read those files to see application flows (registration, verification, admin, employee actions).
- Services in `src/Service` hold reusable logic (document handling, QR code generation).
- Use Composer for autoloading and dependency management.

## Contributing
- Fork the repository, create a feature branch, and open a pull request.
- Describe database changes clearly and include migration or SQL scripts in `database/` if applicable.
- Keep secrets out of commits — use config files or server-side configuration for production values.

## Tests
- No automated test suite is present in the repo at the moment. Consider adding PHPUnit or other tests for critical flows (auth, registration, file uploads).

## License
- No license file detected. Add a LICENSE file to make the repo's license explicit.

## Questions you can ask next
- "Where is the database schema or SQL to create the initial tables?"
- "Can you add environment-variable based configuration instead of committing credentials in config/*.php?"
- "Please add a Docker Compose setup for local development (MySQL + PHP + web server)."
