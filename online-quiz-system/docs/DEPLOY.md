# Deployment Guide for QuizPoint

Step-by-step instructions for deploying this application from local development to a live server with a custom domain.

## Prerequisites

- A PHP 8.0+ hosting account with MySQL support (shared hosting, VPS, or cloud server)
- A registered domain name
- Access to your domain registrar's DNS settings
- FTP/SFTP access or a file manager provided by your host
- phpMyAdmin or MySQL command-line access

## Step 1: Choose Hosting

Pick a PHP/MySQL hosting provider. Recommended options:

- **Shared hosting**: Hostinger, Bluehost, A2 Hosting, SiteGround. Good for small projects. Look for PHP 8+ and MySQL 5.7+ support.
- **VPS**: DigitalOcean, Linode, Vultr. More control, requires server administration.
- **Managed**: Cloudways. Handles server management for you.

Minimum requirements:
- PHP 8.0 or later
- MySQL 5.7 or later (or MariaDB 10.3+)
- Apache with mod_rewrite enabled
- 50 MB disk space (for the application, database is minimal)

## Step 2: Buy and Configure a Domain

1. Register a domain at a registrar (Namecheap, GoDaddy, Google Domains, Cloudflare Registrar, etc.).
2. In your registrar's DNS settings, create these records:

| Type  | Name | Value               | TTL  |
|-------|------|---------------------|------|
| A     | @    | YOUR_SERVER_IP      | 3600 |
| CNAME | www  | yourdomain.com      | 3600 |

Replace `YOUR_SERVER_IP` with the IP address provided by your hosting provider.

3. If your host provides nameservers, you can alternatively point your domain's nameservers to the host's nameservers (this is often simpler for shared hosting).

4. DNS changes can take up to 48 hours to propagate, but usually take 15 to 60 minutes.

## Step 3: Upload Files

1. Connect to your hosting account via FTP/SFTP or use the file manager in your hosting control panel.
2. Navigate to the public web root (often `public_html/` or `htdocs/` or `www/`).
3. Upload all project files. You can either:
   - Upload to the root so the site is at `yourdomain.com/`
   - Upload to a subfolder like `yourdomain.com/quiz/`
4. Make sure `.htaccess` is uploaded (it may be hidden by default in some FTP clients).
5. Delete `setup.php` after initial setup (see Step 5).

## Step 4: Create the Database

1. Log in to your hosting control panel (cPanel, Plesk, or similar).
2. Create a new MySQL database (name it something like `quiz_system` or as required by your host).
3. Create a database user with a strong password.
4. Grant the user ALL PRIVILEGES on the new database.
5. Import the schema:
   - Open phpMyAdmin
   - Select your database
   - Go to the "Import" tab
   - Upload `sql/schema.sql`
   - Click "Go"
6. Import the seed data:
   - Same process, upload `sql/seed.sql`

If using command line:
```
mysql -u your_db_user -p your_database < sql/schema.sql
mysql -u your_db_user -p your_database < sql/seed.sql
```

## Step 5: Configure the Application

1. Open `includes/config.php` in a text editor.
2. Update these values:

```php
define('DB_HOST', 'localhost');          // Usually localhost, check with your host
define('DB_NAME', 'your_database');      // The database name you created
define('DB_USER', 'your_db_user');       // The database user you created
define('DB_PASS', 'your_db_password');   // The database user's password

define('BASE_URL', 'https://yourdomain.com');  // No trailing slash
define('CANONICAL_URL', 'https://yourdomain.com');

define('CONTACT_EMAIL', 'you@yourdomain.com');
```

3. Upload the updated `config.php`.
4. Visit `https://yourdomain.com/setup.php` in your browser to:
   - Generate PNG favicon files
   - Fix the admin account password hash
5. Verify the setup completed successfully.
6. **Delete `setup.php` immediately after setup.** It should not remain on a production server.

## Step 6: Enable SSL (HTTPS)

Most hosting providers offer free SSL via Let's Encrypt:

1. In your hosting control panel, find SSL/TLS settings.
2. Enable "Let's Encrypt" or "AutoSSL" for your domain.
3. Wait for the certificate to be issued (usually a few minutes).
4. Once SSL is active, edit `.htaccess` and uncomment the HTTPS redirect rules:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

5. Also uncomment the www redirect rule and adjust for your preference (www vs non-www).

## Step 7: Update Production Settings

1. Update `robots.txt`: change the Sitemap URL to your actual domain.
2. Update `sitemap.xml`: replace `https://quizpoint.example` with your actual domain in all URLs.
3. Update `site.webmanifest`: change `start_url` to `/` (or your subfolder path).
4. Update `.htaccess`: change the `ErrorDocument 404` path if you are not using a subfolder.

## Step 8: Test the Deployment

Verify the following work correctly on the live site:

- [ ] Home page loads with correct styling
- [ ] Registration creates a new account
- [ ] Login works with the new account
- [ ] Login works with admin credentials (admin@quizsystem.local / Admin@1234)
- [ ] Admin dashboard loads and shows stats
- [ ] Creating, editing, and deleting categories works
- [ ] Creating, editing, and deleting quizzes works
- [ ] Adding and editing questions works
- [ ] Taking a quiz and submitting answers works
- [ ] Score is calculated correctly
- [ ] Result page shows the correct score
- [ ] My Attempts page lists past attempts
- [ ] Admin Results page shows all attempts
- [ ] 404 page shows for non-existent URLs
- [ ] Privacy Policy and Terms pages load
- [ ] HTTPS redirect works
- [ ] Favicon appears in browser tab
- [ ] Normal users cannot access /admin/ pages
- [ ] Mobile layout works at 360px width

## Step 9: Change Default Admin Credentials

**This is critical for security.** After verifying the deployment:

1. Log in as admin (admin@quizsystem.local / Admin@1234).
2. The application does not currently have a password change feature in the UI, so update the password directly in the database:

```sql
UPDATE users SET password_hash = '$2y$10$YOUR_NEW_HASH', email = 'your-real-email@domain.com'
WHERE email = 'admin@quizsystem.local';
```

Generate a new hash with: `php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"`

## Step 10: Legal Review

**Before making the site publicly accessible:**

- Review the Privacy Policy (`privacy.php`) with a legal professional to ensure it complies with applicable data protection laws in your jurisdiction (GDPR, CCPA, etc.).
- Review the Terms and Conditions (`terms.php`) for legal accuracy.
- Update the `CONTACT_EMAIL` in `config.php` to a real, monitored email address.
- Update the "Last updated" dates in both legal documents if you make changes.

You are solely responsible for the legal accuracy of these documents.

## Maintenance Checklist

- Regularly back up your database (most hosts offer automated backups)
- Keep PHP updated to the latest stable version
- Monitor your error logs for issues
- Periodically review and update quiz content for accuracy
- Change the admin password regularly
