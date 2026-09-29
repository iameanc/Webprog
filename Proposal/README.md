# FindIt Campus

A PHP/MySQL lost-and-found portal for campus communities. Students and staff can publish lost or found items, search listings, and send private claims. Admins review claims, manage listings and user roles, and track basic stats.

## Requirements

- PHP 8.1+ with `pdo_mysql`, `fileinfo`, and `mbstring` enabled
- MySQL 8+ or a compatible MariaDB release
- A web server or PHP's built-in development server

## Setup

1. Create the database and tables by importing `schema.sql` in MySQL Workbench or running:

   ```powershell
   mysql -u root -p < schema.sql
   ```

2. Configure database credentials. The defaults in `config.php` are `127.0.0.1`, database `findit_campus`, user `root`, and a blank password. Override with `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` environment variables when needed.

3. From this folder, start the development server:

   ```powershell
   php -S localhost:8000
   ```

4. Open `http://localhost:8000`, create a normal account, then use that account for testing.

The `uploads` directory is created automatically when the first item photo is posted. The PHP process needs permission to write to the project folder. Uploads are limited to 5 MB and validated as JPG, PNG, WEBP, or GIF.

## Create the first admin

Register through the site, then promote that account in MySQL (replace the email):

```sql
USE findit_campus;
UPDATE users SET role = 'admin' WHERE email = 'admin@your-campus.edu';
```

Sign out and back in so the new role is loaded into the session. Admin controls are available at `/admin.php`.

## Main files

- `schema.sql`: users, items, claims, categories, and starter categories
- `config.php`: PDO connection, session, CSRF, flash messages, and shared layout
- `index.php` and `search.php`: listings and live search/filter endpoint
- `post-item.php` and `claim.php`: posting photos and private claim submissions
- `admin.php`: claim review, listing management, user roles, and stats
- `assets/`: responsive UI and browser interactions

For production, use HTTPS, set strong database credentials, configure PHP upload limits, disable detailed PHP errors for visitors, and serve the app from a dedicated virtual host. The included PHP development server is for local testing only.
