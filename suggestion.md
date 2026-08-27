# SALU Exam Portal — Project Overview & Suggested Improvement Topics

## Project Overview

**SALU Exam Portal** is a University Examination Management System built on **Laravel 12 (PHP 8.3)** with **Blade + Tailwind CSS + Alpine.js** on the frontend, **Sanctum** for token-based API auth, and **MySQL** as the database. It serves four user roles: `STUDENT`, `COLLEGE_ADMIN`, `ADMIN`, and `SUPERADMIN`.

### Core Modules

| Module | Description |
|---|---|
| Authentication | Register, login, email verification, custom password reset (`AuthController`) |
| Exam Enrollment | Detailed exam form (personal, board, previous exam, domicile, photo) with `DRAFT → PENDING → APPROVED/REJECTED` flow |
| Enrollment Window | Admin-controlled open/close periods per academic year |
| Fees & Payments | Fee records, challan PDF download, manual/simulated payment, admin verification, auto-expiry job |
| Admit Cards & Seating | Seat allocation service (center/room/seat), admit card PDF generation |
| Results | Per-enrollment results with result card PDF |
| Admin Console | Dashboard stats, approve/reject (single + bulk), students, colleges, academic years, audit logs, system settings |
| Audit Logging | `AuditService` + `audit_logs` table tracking admin actions |
| Public Pages | Colleges list, programs, contact, FAQ |

### Architecture Snapshot

- **Service layer**: `AuditService`, `EmailService`, `FileUploadService`, `PdfService`, `SeatAllocationService`
- **Queue jobs**: expire unpaid fees, generate PDF, seat allocation, enrollment notifications
- **Packages**: spatie/laravel-permission, barryvdh/laravel-dompdf, maatwebsite/excel, intervention/image, predis (Redis)
- **Deployment**: Dockerfile, railway.json, render.yaml

---

## Suggested Main Topics for Improvement

### 1. Fix Deployment Configuration (Critical) 🔴
- The `Dockerfile` builds an **ASP.NET Core app (`SaluExamPortal.dll`)** — completely wrong for this Laravel project.
- `railway.json` runs `dotnet SaluExamPortal.dll`; `render.yaml` uses a .NET-style connection string env var.
- **Action**: Replace all three with proper Laravel deployment configs (PHP-FPM + Nginx or `laravel/sail`, `php artisan serve` / Octane, `DB_*` env vars).

### 2. Unify Role/Permission System 🟠
- spatie/laravel-permission is installed with migrated tables but **never used**; auth relies on a role enum column + custom `CheckRole` middleware.
- **Action**: Either remove spatie and the migration, or migrate roles/permissions to spatie for granular, named permissions.

### 3. Refactor Fat Controllers & Route Closures 🟠
- `AdminController` (533 lines), `AuthController` (375 lines), `StudentController` (350 lines) contain business logic inline; `routes/web.php` has logic-bearing closures.
- No Form Requests — validation is inline in controllers.
- `REPOSITORY_PATTERN_GUIDE.md` exists but there is no `app/Repositories` directory.
- **Action**: Extract Form Requests, Actions/Services, and (optionally) repositories; align the guide with the code or the code with the guide.

### 4. Consistent API vs Web Auth Behavior 🟠
- `CheckRole` middleware assumes web-session redirects; under `auth:sanctum` API routes it behaves inconsistently.
- **Action**: Return JSON 403 for API requests and redirects for web, or use separate middleware per surface.

### 5. Testing 🟡
- PHPUnit is installed but the codebase has little/no test coverage visible.
- **Action**: Add feature tests for enrollment flow (submit → approve → fee → admit card → result), auth, and role-based access; add CI (GitHub Actions).

### 6. Security Hardening 🟡
- Review the custom password-reset token-hash flow against Laravel's built-in `Password` broker.
- Validate photo uploads strictly (mime/size), confirm `ExpireUnpaidFeesJob` idempotency, add rate limiting on auth + payment endpoints.

### 7. Performance & Caching 🟡
- Redis (predis) is available — cache college/program lists, dashboard stats; queue all PDF generation (already partly done); add DB indexes per `DATABASE_INDEXES_GUIDE.md` and verify they're in migrations.

### 8. Code Quality & Housekeeping 🟢
- Consolidate the 11 root-level MD guides into `docs/`.
- Run `laravel/pint` for style; consider Larastan for static analysis.
- Resolve the spatie permissions migration mismatch (created 2026-08-26 but unused) before it confuses future deployments.
