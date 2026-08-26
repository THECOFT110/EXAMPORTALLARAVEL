# Security Setup Guide (Laravel)

## Environment & Secrets

Laravel keeps all secrets in the `.env` file (development) or real environment
variables (production). There is no separate secrets store to initialize.

```bash
# Development: create .env and generate the application key
cp .env.example .env
php artisan key:generate
```

**Never commit `.env` to version control.** In production (Railway, Render,
Docker, Forge), set the variables in the host dashboard — they are injected as
real environment variables, which override `.env` automatically.

### Secrets That Must Be Set in Production

| Variable | Purpose |
|---|---|
| `APP_KEY` | Encryption key for sessions/encrypted columns (`php artisan key:generate --show`) |
| `APP_ENV=production` | Enables production behavior |
| `APP_DEBUG=false` | **Never** leave `true` in production (leaks stack traces) |
| `DB_PASSWORD` | PostgreSQL password |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | SMTP credentials (app password for Gmail) |

## Authentication & Sessions

- Web sessions use Laravel's session driver (`database` recommended in production).
- API routes (`/api/*`) use **Laravel Sanctum** bearer tokens — tokens are
  hashed in the `personal_access_tokens` table and can be revoked per device.
- Passwords are bcrypt-hashed automatically via the `'password' => 'hashed'` cast.
- Password resets use a hashed single-use token with expiry
  (`password_reset_token_hash`, `password_reset_token_expires_at`).

## Rate Limiting

Verify throttle groups on auth endpoints (`routes/web.php`, `routes/api.php`).
For extra protection add/adjust limiters in `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('auth', function ($request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

## CORS

Configure allowed origins in `config/cors.php` (publish if missing):

```bash
php artisan config:publish cors
```

Restrict `allowed_origins` to your real frontend domains.

## Uploads

- Student photos are validated and re-encoded to JPEG via Intervention Image
  (see `app/Services/FileUploadService.php`).
- Documents are stored on the `public` disk — keep allowed extensions and max
  size checks in place before accepting a file.

## Production Checklist

- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] `APP_KEY` set to a fresh value (rotating it invalidates sessions)
- [ ] `APP_URL` matches the real domain (used for signed URLs and emails)
- [ ] HTTPS enforced by the host / reverse proxy
- [ ] `SESSION_DRIVER=database` (or `redis`), `CACHE_STORE` not `file` on multi-instance hosts
- [ ] Database credentials are strong and not committed
- [ ] SMTP credentials set and `MAIL_MAILER=smtp` (use `log` only for testing)
- [ ] Queue worker running (`php artisan queue:work`) with failed-jobs monitoring
- [ ] Daily backups of the database and `storage/app` uploads
- [ ] `php artisan config:cache route:cache view:cache` run on deploy

## Common Issues

### "No application encryption key has been specified."

```bash
php artisan key:generate
```

### Password reset links expire immediately

Check that `APP_URL` matches the actual site domain, and that
`password_reset_token_expires_at` is being set when a reset is requested.
