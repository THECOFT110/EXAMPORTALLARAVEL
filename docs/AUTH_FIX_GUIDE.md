# Auth Fix Guide (Laravel)

## Overview

This guide documents how authentication works in the Laravel port of the
portal and how to fix the most common sign-in problems (originally written
for the .NET version — cookie policy, redirects, and API auth — now mapped
to Laravel equivalents).

## How Auth Works Here

- **Web (Blade)**: standard Laravel session auth (`auth` middleware).
  The session cookie is managed by Laravel (`config/session.php` defaults:
  HttpOnly, SameSite `Lax`, secure when HTTPS is detected).
- **API (`/api/*`)**: Laravel Sanctum bearer tokens issued at login
  (`POST /api/auth/login`). Tokens are hashed in `personal_access_tokens`.
- **Roles**: `role` enum column on `users` (`STUDENT`, `COLLEGE_ADMIN`,
  `ADMIN`, `SUPERADMIN`) enforced by the `check.role` middleware alias
  (`App\Http\Middleware\CheckRole`).

## Fixes Applied

### 1. Role middleware now API-aware

`CheckRole` previously always redirected unauthenticated users to the login
page — wrong for API clients. It now returns JSON for requests that expect
JSON:

- Unauthenticated API request → `401 {"success": false, "message": "Unauthenticated."}`
- Wrong role API request → `403` with the required role in the message
- Web requests still redirect to `login` / get an HTML 403 page

### 2. Session cookie behavior

Laravel's defaults already match what the old .NET fix tried to achieve:

| Concern | .NET fix | Laravel |
|---|---|---|
| SameSite | `Lax` | `lax` by default (`SESSION_SAME_SITE`) |
| Secure policy | `SameAsRequest` | automatic when behind HTTPS (`SESSION_SECURE_COOKIE`) |
| Sliding expiry | 8h sliding | `SESSION_LIFETIME` (default 120 min, `expire_on_close` available) |
| API responses instead of redirects | event handlers | middleware returns JSON (see above) |

## Testing the Fix

```bash
# Restart the app
php artisan serve

# 1. Clear browser cookies for localhost:8000 (DevTools → Application → Cookies)

# 2. Sign in via the web form, then verify the session cookie:
#    DevTools → Application → Cookies → laravel_session (HttpOnly, SameSite=Lax)

# 3. API role check returns JSON instead of a redirect:
curl -i http://127.0.0.1:8000/api/student/dashboard \
  -H "Accept: application/json"
# → 401 {"success":false,"message":"Unauthenticated."}

# 4. With a valid token:
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"student@example.com","password":"password"}' | jq -r .token)
curl -i http://127.0.0.1:8000/api/student/dashboard \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json"
```

## Troubleshooting

### Still redirecting after sign-in

1. Check the session cookie exists (DevTools → Cookies → `laravel_session`)
2. Verify `SESSION_DRIVER` is set and its store is reachable
3. Try an Incognito window (stale cookies/cache)
4. `php artisan optimize:clear` after config changes

### Cookie not being set

- Mixing `localhost` and `127.0.0.1` — pick one and stick to it
- HTTPS mismatch — set `SESSION_SECURE_COOKIE=true` only when serving HTTPS
- Check `APP_KEY` exists (missing key breaks encrypted cookies)

### Works once, then logs out

- `SESSION_LIFETIME` too short, or `SESSION_EXPIRE_ON_CLOSE=true`
- Multiple workers with `file` sessions on different disks (use `database` driver)

## Production Checklist

- [ ] HTTPS enforced; `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=lax` (or `strict` if no cross-site flows)
- [ ] `SESSION_DRIVER=database`
- [ ] Sanctum tokens have sensible expiry
- [ ] `APP_KEY` strong and unique
- [ ] Rate limiting on login/register/reset endpoints
