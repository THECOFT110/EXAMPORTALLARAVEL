# SALU Exam Portal — Harsh Enterprise Audit

**Audit date:** 2026-08-28  
**Scope:** Full codebase, security, architecture, ops, compliance readiness  
**Verdict:** **Not production-ready for enterprise/university deployment** without remediation  
**Overall grade:** **D+** (functional prototype with serious gaps)

---

## Executive Summary

SaluExamPortal is a Laravel 13 monolith that covers real university exam workflows (enrollment, fees, admit cards, results). The service layer, UUID models, audit logging, and 20 passing PHPUnit tests show intentional design. However, the project suffers from **documentation drift**, **payment integrity holes**, **missing CI/CD**, **no authorization policies**, **600+ lines of inline route logic**, **secrets/data in version control**, and **security controls documented but not implemented**.

For an enterprise deployment handling student PII, financial records, and official exam documents, the current state is a **high-risk MVP**, not an enterprise system.

---

## Severity Matrix

| ID | Finding | Severity | Effort |
|----|---------|----------|--------|
| F-01 | Student self-declares payment as PAID without verification | **Critical** | Medium |
| F-02 | `processPayment()` auto-marks paid (placeholder left in API) | **Critical** | Low |
| F-03 | `database.sqlite` + root `logs/` committed (PII leakage) | **Critical** | Low |
| F-04 | Default seeded passwords (`admin123`, `student123`) in repo | **Critical** | Low |
| F-05 | No rate limiting on auth despite docs claiming it exists | **High** | Low |
| F-06 | COLLEGE_ADMIN redirected to admin dashboard but blocked by middleware | **High** | Low |
| F-07 | No CI/CD pipeline | **High** | Medium |
| F-08 | Dual web/API architecture with duplicated, divergent logic | **High** | High |
| F-09 | Email verification bypassed (`is_verified => true` on register) | **High** | Medium |
| F-10 | No Laravel Policies — RBAC is string enum + middleware only | **High** | Medium |
| F-11 | Document uploads use client extension, not MIME magic bytes | **High** | Medium |
| F-12 | No payment gateway integration (JazzCash/EasyPaisa/Bank) | **High** | High |
| F-13 | Documentation describes packages/features that don't exist | **Medium** | Low |
| F-14 | No Form Requests — validation scattered in controllers/closures | **Medium** | Medium |
| F-15 | No model factories — tests depend on seeded data | **Medium** | Medium |
| F-16 | Session config conflicts (480 min env vs 15 min middleware) | **Medium** | Low |
| F-17 | MD5 challan IDs — weak entropy, unnecessary crypto hash | **Low** | Low |
| F-18 | No static analysis (Larastan/Psalm) or enforced Pint in CI | **Medium** | Low |
| F-19 | Queue/cache/session all on DB — won't scale horizontally | **Medium** | Medium |
| F-20 | Legacy .NET log files in repo root | **Medium** | Low |

---

## Critical Findings (Fix Before Any Production Deploy)

### F-01 — Payment fraud: students mark themselves PAID

**Location:** `routes/web.php` (payment submit closure)

Students POST a self-reported `transaction_id` and the fee is immediately set to `PAID`:

```php
$fee->update([
    'transaction_id' => $validated['transaction_id'],
    'payment_method' => $validated['payment_method'],
    'status' => 'PAID',
    'paid_at' => now(),
]);
```

**Impact:** Any student can claim payment without bank/gateway proof. Admit cards and seat allocation may proceed on fraudulent fees.

**Enterprise fix:**
1. Student submission sets status to `PENDING_VERIFICATION`, not `PAID`.
2. Admin (or automated gateway webhook) moves to `PAID` → `VERIFIED`.
3. Integrate JazzCash/EasyPaisa/1Link with signed webhook callbacks.
4. Add idempotency keys and audit trail for every status transition.

---

### F-02 — `processPayment()` is a live foot-gun

**Location:** `app/Http/Controllers/PaymentController.php:149-163`

```php
public function processPayment(Request $request, string $feeId)
{
    $fee = Fee::findOrFail($feeId);
    $fee->markAsPaid('ONLINE', 'TXN-'.uniqid());
    return response()->json(['success' => true, ...]);
}
```

Protected by `ADMIN,SUPERADMIN` on API, but any admin call marks any fee paid with zero gateway verification.

**Enterprise fix:** Remove or gate behind `APP_ENV=local` only. Replace with webhook handler that verifies HMAC signature from payment provider.

---

### F-03 — Sensitive data in version control

| Artifact | Risk |
|----------|------|
| `database/database.sqlite` | Real/modified DB with users, CNIC, enrollments |
| `logs/log-*.txt` | .NET-era login logs with emails, roles, timestamps |

**Enterprise fix:**
```gitignore
/database/*.sqlite
/logs/
```
Rotate any credentials that appeared in logs. Use `php artisan migrate:fresh --seed` for dev only.

---

### F-04 — Known default credentials

**Location:** `database/seeders/UserSeeder.php`, `README.md`

Passwords `admin123` / `student123` are committed and documented. If seeders run in staging/production, the system is instantly compromised.

**Enterprise fix:**
- Seeders must use `env('SEED_ADMIN_PASSWORD')` or skip user seeding in non-local envs.
- Force password change on first login for admin roles.
- Never document real passwords in README.

---

## Security Audit

### Authentication

| Control | Status | Notes |
|---------|--------|-------|
| Password hashing (bcrypt) | ✅ | Eloquent `'password' => 'hashed'` cast |
| Sanctum API tokens | ✅ | Hashed in `personal_access_tokens` |
| Password reset tokens | ⚠️ | Custom SHA-256 on user row; works but non-standard |
| Rate limiting | ❌ | Documented in `SECURITY_SETUP.md`, **not implemented** |
| Email verification | ❌ | Auto-verified on register; middleware exists but unused |
| MFA / 2FA | ❌ | Not present — required for admin roles at enterprise tier |
| Account lockout | ❌ | No failed-login tracking |
| CNIC as login identifier | ✅ | Normalized 13-digit lookup |

### Authorization

| Control | Status | Notes |
|---------|--------|-------|
| Role enum RBAC | ⚠️ | Works for happy path |
| Laravel Policies | ❌ | No `authorize()` anywhere |
| Resource ownership checks | ⚠️ | Inline in controllers; inconsistent |
| COLLEGE_ADMIN access | ❌ | Redirected to `/admin/dashboard` but middleware excludes role |
| Principle of least privilege | ❌ | ADMIN and SUPERADMIN share most routes |

**Recommended:** Introduce `spatie/laravel-permission` (already mentioned in outdated docs) OR native Laravel Policies per model (`EnrollmentPolicy`, `FeePolicy`, etc.) with `Gate::define` for college-scoped access.

### Session & Transport

| Setting | Current | Enterprise target |
|---------|---------|-------------------|
| `SESSION_ENCRYPT` | `false` | `true` |
| `SESSION_LIFETIME` | 480 min | Align with 15-min inactivity OR document both |
| `APP_DEBUG` | `true` in `.env.example` | `false` in prod example |
| HTTPS enforcement | Delegated to host | Add `TrustProxies` + `URL::forceScheme('https')` |
| CSRF | ✅ | Blade + Axios meta token |
| CORS | Not published | Publish and restrict origins |

### File Upload Security

**Photos:** Re-encoded to JPEG via Intervention Image — **good**.

**Documents:** Stored with original extension via `getClientOriginalExtension()` — **bad**. A `.php` renamed to `.pdf` could be stored if validation only checks extension.

**Enterprise fix:**
- Validate MIME with `$file->getMimeType()` or `finfo`.
- Store outside `public/`; serve via signed temporary URLs or authenticated controller.
- Virus scan hook (ClamAV) for production.

---

## MD5 Usage Analysis (`app/Models/Fee.php`)

**Single occurrence in application code:**

```php
$challan = 'SALU-'.now()->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
```

| Aspect | Assessment |
|--------|------------|
| Security risk | **Low** — not used for passwords/tokens |
| Correctness risk | **Medium** — `uniqid()` without `more_entropy=true` is predictable |
| Collision risk | **Low at current scale** — loop checks DB uniqueness |
| Code smell | **Yes** — MD5 for random IDs is an anti-pattern |

**Enterprise replacement:**

```php
use Illuminate\Support\Str;

$challan = 'SALU-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
// Or for guaranteed uniqueness:
$challan = 'SALU-'.now()->format('YmdHis').'-'.strtoupper(Str::ulid());
```

Use `random_bytes()` / `Str::random()` / ULID — never MD5 for identifier generation (audit scanners flag it).

---

## Architecture Audit

### Current Pattern

```
Request → Middleware → Route (closure OR controller) → Service/Model → View/JSON
```

### Problems

1. **`routes/web.php` ~595 lines** — business logic, queries, and validation in closures. Untestable, unreviewable, duplicates API controllers.
2. **API is cleaner; web is legacy-style** — two sources of truth for registration, payment, dashboards.
3. **`REPOSITORY_PATTERN_GUIDE.md` exists; no repositories implemented** — documentation lies.
4. **No DTOs/Actions** — fat controllers (`AdminController` 533+ lines).
5. **No event-driven domain** — enrollment approval doesn't dispatch domain events; jobs called imperatively.

### Enterprise Target Architecture

```
app/
├── Actions/           # Single-purpose commands (ApproveEnrollment, GenerateChallan)
├── Data/              # Spatie Laravel Data DTOs for API responses
├── Events/            # EnrollmentApproved, FeePaid, ResultPublished
├── Listeners/         # SendNotification, AllocateSeat, GeneratePdf
├── Http/
│   ├── Controllers/   # Thin — delegate only
│   ├── Requests/      # Form Request validation
│   └── Resources/     # API transformers
├── Policies/          # Authorization
├── Repositories/      # Optional — only if query complexity warrants
└── Services/          # Keep existing PdfService, EmailService, etc.
```

**Rule:** No closure in `routes/web.php` longer than 3 lines. Move everything to controllers + actions.

---

## COLLEGE_ADMIN Bug (F-06)

`AuthController::redirectBasedOnRole()` sends `COLLEGE_ADMIN` to `admin.dashboard`.

Admin routes use `check.role:ADMIN,SUPERADMIN` — **COLLEGE_ADMIN gets 403**.

Dashboard closure in `web.php` has college-scoping logic for `COLLEGE_ADMIN`, but the role never reaches it.

**Fix:** Change middleware to `check.role:ADMIN,SUPERADMIN,COLLEGE_ADMIN` on web admin routes, or create separate `college-admin` route group with scoped policies.

---

## Testing & Quality

### Current State

- **20 tests, 73 assertions** — all passing (~62s runtime)
- Feature: auth, enrollment, payment, PDF, mail
- Unit: result grade calculation
- Uses `DatabaseTransactions` — good isolation
- **No factories** — tests query seeded users or create ad-hoc records
- **No security tests** — CSRF, IDOR, rate limit, role escalation
- **No browser tests** — enrollment form is complex Blade + Alpine

### Enterprise Testing Target

| Layer | Tool | Coverage goal |
|-------|------|---------------|
| Unit | PHPUnit/Pest | Models, services, actions |
| Feature | PHPUnit/Pest | All API endpoints, policies |
| Browser | Laravel Dusk or Playwright | Enrollment wizard, payment flow |
| Static | Larastan level 6+ | Type safety |
| Style | Laravel Pint | Enforced in CI |
| Security | OWASP ZAP baseline | CI scheduled scan |

---

## DevOps & Infrastructure (Missing)

| Capability | Status | Recommendation |
|------------|--------|------------------|
| CI/CD | ❌ | GitHub Actions: test, pint, phpstan, npm build |
| Docker | ❌ | Multi-stage: PHP-FPM + Nginx + Node build |
| Staging environment | ❌ | Required for UAT before exam windows |
| Monitoring | ❌ | Sentry/Bugsnag + Laravel Telescope (non-prod) |
| Log aggregation | ❌ | Centralize to CloudWatch/Datadog; remove root `logs/` |
| Backups | Documented only | Automated DB + `storage/app` daily |
| Queue workers | Documented | Supervisor/systemd for `queue:work` |
| Scheduler | ✅ | `ExpireUnpaidFeesJob` daily in `console.php` |
| Health checks | ✅ | `/up` and `/api/health` |
| Secrets management | ❌ | Vault / host env; never `.env` in image |

### Sample GitHub Actions Workflow

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3', extensions: gd, sqlite3 }
      - run: composer install --no-interaction
      - run: cp .env.example .env && php artisan key:generate
      - run: php artisan migrate --force
      - run: php artisan test
      - run: ./vendor/bin/pint --test
```

---

## Documentation Integrity

Multiple docs describe a system that **does not match the codebase**:

| Documented | Actual |
|------------|--------|
| Laravel 11/12 | Laravel **13** |
| spatie/laravel-permission | **Not in composer.json** |
| maatwebsite/excel | **Not installed** |
| predis/Redis | **Not installed**; cache/session/queue use database |
| Dockerfile, railway.json, render.yaml | **Do not exist** (suggestion.md references .NET Dockerfile!) |
| Repository pattern | **Guide only, no code** |
| Rate limiting on auth | **Not implemented** |

**Enterprise rule:** Docs are part of the contract. Run a doc audit in every release PR.

---

## Compliance & Data Governance (University Context)

For Pakistani university systems handling CNIC, photos, exam records:

| Requirement | Status | Action |
|-------------|--------|--------|
| PII encryption at rest | ❌ | Encrypt CNIC column or full-disk encryption |
| Data retention policy | ❌ | Define and implement enrollment/archive lifecycle |
| Right to deletion | ❌ | No GDPR-style erase flow (adapt for local law) |
| Audit trail immutability | ⚠️ | `audit_logs` exist but no append-only enforcement |
| Access logging for admin | ⚠️ | Partial via `AuditService` |
| Backup encryption | ❌ | Not specified |
| Incident response plan | ❌ | Document breach procedure |

---

## Enterprise Roadmap (Phased)

### Phase 0 — Stop the bleeding (1–2 weeks)

- [ ] Fix payment flow: `PENDING_VERIFICATION` → admin/gateway → `PAID` → `VERIFIED`
- [ ] Remove/disable `processPayment()` placeholder in non-local envs
- [ ] Gitignore `database.sqlite`, `logs/`; purge from history if needed
- [ ] Fix COLLEGE_ADMIN middleware
- [ ] Implement rate limiting on `/login`, `/register`, `/api/auth/*`
- [ ] Set production `.env.example` defaults (`APP_DEBUG=false`, `SESSION_ENCRYPT=true`)
- [ ] Replace MD5 challan generation with `Str::random()` or ULID

### Phase 1 — Foundation (2–4 weeks)

- [ ] GitHub Actions CI (test + pint + build)
- [ ] Extract all web route closures to controllers
- [ ] Add Form Requests for every write endpoint
- [ ] Add Laravel Policies for Enrollment, Fee, Result, User
- [ ] Model factories + refactor tests to not depend on seeders
- [ ] Publish `config/cors.php`, `config/sanctum.php`
- [ ] Enforce email verification OR remove dead middleware

### Phase 2 — Production hardening (4–8 weeks)

- [ ] Payment gateway integration (JazzCash/EasyPaisa) with webhooks
- [ ] Redis for cache, session, queue
- [ ] Docker + staging deployment
- [ ] Sentry error tracking
- [ ] MFA for ADMIN/SUPERADMIN
- [ ] Larastan + minimum level 5
- [ ] Signed URL document downloads (move uploads off public disk)
- [ ] Database indexes audit on enrollments, fees, audit_logs

### Phase 3 — Enterprise maturity (8–16 weeks)

- [ ] Event-driven architecture (domain events + listeners)
- [ ] Read replicas / query optimization for reporting dashboards
- [ ] Full browser test suite for enrollment wizard
- [ ] API versioning (`/api/v1/`)
- [ ] OpenAPI/Swagger documentation
- [ ] SOC2-style access reviews for admin accounts
- [ ] Disaster recovery drills
- [ ] Performance/load testing before exam window peaks

---

## What's Actually Good (Credit Where Due)

- Modern stack: Laravel 13, PHP 8.3, Sanctum, Vite, Tailwind
- UUID primary keys — good for distributed IDs and URL opacity
- Service layer exists (`PdfService`, `EmailService`, `AuditService`, `SeatAllocationService`)
- Background jobs for fee expiry, PDF generation, seat allocation, notifications
- Bcrypt passwords, SHA-256 reset tokens with `hash_equals()`
- Photo re-encoding strips malicious payloads from images
- Audit logging on sensitive admin actions
- 20 automated tests covering core flows
- Strict 15-minute session inactivity timeout
- Timezone set to `Asia/Karachi` in config

---

## Final Verdict

| Dimension | Score | Notes |
|-----------|-------|-------|
| Functionality | B | Core workflows implemented |
| Security | D | Payment fraud vector, no rate limits, data in git |
| Architecture | C- | Service layer good; routes are a mess |
| Testing | C | Tests exist but narrow; no CI |
| DevOps | F | No pipeline, no containers, no monitoring |
| Documentation | D | Actively misleading in places |
| Enterprise readiness | **D+** | 2–3 months of focused work minimum |

**Do not deploy to production handling real exam fees until Phase 0 and payment gateway (Phase 2) are complete.**

---

*Generated by enterprise audit — 2026-08-28*
