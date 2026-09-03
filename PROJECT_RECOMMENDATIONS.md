# SALU Exam Portal Recommendations

**Review date:** 2026-09-03
**Scope:** Application architecture, security, data integrity, operations, testing, and documentation

## 1. Executive Summary

SALU Exam Portal is a Laravel 13 monolith for university examination operations. It combines Blade and Alpine.js pages with Sanctum JSON APIs. The main business flow is:

1. A student registers and authenticates with email or CNIC.
2. The student creates an enrollment application and uploads supporting documents.
3. An administrator reviews the application, verifies fees, and approves or rejects it.
4. The system allocates seats and produces admit cards.
5. Results and downloadable PDFs are made available to the student.
6. Audit logs record important administrative activity.

The project has a useful service and job layer, database indexes, feature tests, Docker deployment files, and security-focused documentation. It should not yet be described as production-ready until payment integrity, API authorization, private document delivery, enrollment-window enforcement, queue operation, and credential hygiene are resolved.

## 2. Current Architecture

### Application layers

- **Web UI:** Blade templates, Tailwind CSS, Alpine.js, Flowbite, Vite.
- **API:** Sanctum token authentication under `routes/api.php`.
- **Web routes:** Session-authenticated student and administration workflows under `routes/web.php`.
- **Domain model:** Users, colleges, academic years, enrollment windows, enrollments, fees, seats, admit cards, results, audit logs, and system settings.
- **Services:** Payment, file upload, PDF generation, email, seat allocation, OCR, and audit logging.
- **Asynchronous work:** Notification, PDF, seat allocation, and fee-expiration jobs.
- **Persistence:** PostgreSQL is the documented runtime database. JSON columns are used for some enrollment records and documents.
- **Deployment:** A PHP-FPM/Nginx Docker image with Node-based asset compilation; Railway configuration is also present.

### Main roles

- **Student:** Maintains profile, submits enrollment, uploads documents, views fees, admit cards, and results.
- **Admin:** Reviews enrollment, manages students and colleges, verifies fees, allocates seats, and views reports.
- **Superadmin:** Manages users, settings, and enrollment windows.
- **College admin:** Supported in parts of the web and policy design, but API support and college scoping need completion.

## 3. Highest-Priority Recommendations

### P0: Block production release until payment state is authoritative

**Why:** Fee records can be marked paid through administrative or gateway paths without a complete provider reconciliation and replay-protection model.

**Recommended changes:**

- Define explicit payment states and permitted transitions: pending, submitted, under review, verified, rejected, expired, and reversed.
- Make provider transaction identifiers unique at the database level.
- Store provider events and their idempotency keys in a durable table.
- Verify amount, currency, fee ownership, and provider response before changing a fee to paid.
- Make repeated callbacks safe and non-mutating after the first accepted event.
- Keep mock payment behavior outside production route registration, not only behind an environment condition.
- Require a reason, actor, and audit event for manual payment overrides.

**Relevant areas:** `app/Http/Controllers/PaymentController.php`, `app/Services/PaymentGatewayService.php`, `app/Models/Fee.php`, payment migrations, and payment feature tests.

**Acceptance checks:** Duplicate callbacks do not create duplicate payments; mismatched amounts are rejected; a paid fee cannot be silently changed by a replay; manual overrides are fully audited.

### P0: Complete API authorization and college scoping

**Why:** Several API administration operations appear to rely mainly on role middleware. Role membership alone does not ensure that a college administrator can access only that college's students, enrollments, fees, or reports.

**Recommended changes:**

- Apply policies to every API read and write operation, not only selected web controller methods.
- Add `COLLEGE_ADMIN` to the correct API middleware groups where intended.
- Enforce college scope in the query itself, before pagination or resource serialization.
- Do not accept a college identifier from the client when it can be derived from the authenticated user.
- Add authorization tests for same-college, cross-college, admin, superadmin, and unauthenticated cases.

**Relevant areas:** `routes/api.php`, `app/Policies`, `app/Http/Controllers/AdminController.php`, `CollegeController.php`, and `EnrollmentController.php`.

**Acceptance checks:** A college administrator cannot read, edit, approve, or export records from another college, even when IDs are guessed directly.

### P0: Protect uploaded documents

**Why:** Student documents and photos are exposed through public storage URLs. Documents contain identity and academic information and should not be guessable or directly web-readable.

**Recommended changes:**

- Store documents on a private disk.
- Serve downloads through authorized controller actions with `Content-Disposition: attachment`.
- Check the authenticated user's relationship to the enrollment before every download.
- Use detected MIME type and file content validation, not only filename extensions.
- Generate random storage names and avoid returning internal paths in API responses.
- Add size limits, image dimension limits, and malware-scanning or quarantine integration for production.
- Create document metadata records when verification status, uploader, type, and retention need to be tracked.

**Relevant areas:** `app/Services/FileUploadService.php`, student download actions, storage configuration, and enrollment views.

**Acceptance checks:** An unauthenticated request or another student cannot download a document by changing its URL or identifier.

### P0: Enforce the enrollment window in the write path

**Why:** A window-checking method exists, but the important create and submit operations must independently enforce the active period. A UI check is not a business rule.

**Recommended changes:**

- Centralize the rule in a service or domain action used by create, update, and submit operations.
- Define behavior for no active window, expired windows, future windows, and multiple overlapping windows.
- Re-check the window inside the transaction used for submission.
- Return a stable validation or domain error for closed enrollment.
- Add tests for every window state and for a window closing during submission.

**Relevant areas:** `app/Http/Controllers/EnrollmentController.php`, `app/Models/EnrollmentWindow.php`, migrations, and enrollment tests.

## 4. Security Recommendations

### Authentication and account lifecycle

- Implement real email verification or remove the misleading verification state. Registration and login should not automatically make an account verified.
- Apply verification middleware to the authenticated routes that require verified identity.
- Use a consistent password-reset implementation with cryptographically random tokens, short expiry, one-time use, rate limiting, and constant-shape responses.
- Revoke active Sanctum tokens after password reset, password change, or an administrative security action.
- Prefer secure, HttpOnly, SameSite cookies for browser authentication where possible. Tokens in `localStorage` are exposed to any successful injected script.
- Review inactivity timeout and secure-cookie settings separately for local, staging, and production environments.
- Configure trusted proxy addresses narrowly instead of trusting every forwarded proxy.

### Input and output handling

- Keep all raw SQL parameterized and encapsulate repeated CNIC and phone normalization in model scopes or value objects.
- Validate authorization before loading or serializing sensitive records.
- Add request validation for every API endpoint, including pagination bounds, sort fields, file content, and bulk-operation sizes.
- Add security headers such as a restrictive Content Security Policy after auditing inline scripts and third-party assets.
- Use generic authentication and password-reset responses to avoid account enumeration.

### Secrets and seeded data

- Treat any committed example credential, database password, or application key as compromised. Remove it from tracked files and rotate it in the hosting provider.
- Ensure `.env`, logs, uploaded files, and runtime caches are ignored and are not packaged into releases.
- Require explicit seed passwords in non-local environments, or create non-loginable demo accounts.
- Make production startup fail when required secrets are missing rather than silently using demo defaults.

## 5. Correctness and Data Integrity

### Seat allocation

- Fix the undefined `$count` reference in `SeatAllocationService::assignRoom()`.
- Allocate seats inside a transaction with row locks or an atomic database strategy.
- Add unique constraints for the business identity of a seat, such as exam period, room, seat number, and gender where applicable.
- Make retries idempotent and record allocation attempts.
- Test two workers processing the same college concurrently.

### Configuration and settings

- Choose one source of truth for fee amounts, enrollment limits, and operational settings. The code currently combines configuration values with `system_settings` records.
- Validate settings on write and cache them with an explicit invalidation path.
- Add a settings audit trail showing old value, new value, actor, and reason.

### Programs and academic data

- Move hardcoded programs from controller logic into a managed table or a versioned data source.
- Associate programs with colleges and academic years where program availability changes.
- Validate that an enrollment's selected program belongs to its selected college.
- Treat uploaded academic records as structured, versioned data if they need reporting or verification.

### State transitions

- Replace scattered status assignments with named domain methods or transition objects.
- Define who may perform each transition and what side effects occur.
- Use transactions and outbox-style events for approval, payment verification, seat assignment, and result publication.
- Add database constraints for impossible states, such as an admit card without an approved enrollment.

## 6. Operations and Deployment

- Run web, queue worker, and scheduler as explicit production processes. The current container startup path primarily runs PHP-FPM and Nginx; queued notifications and scheduled fee expiration need a separately provisioned worker and scheduler.
- Monitor failed jobs and configure retry, backoff, timeout, and dead-letter behavior for every job.
- Pass model IDs to queued jobs and reload fresh models in `handle()` instead of serializing large or stale model graphs.
- Add health checks for application readiness, database connectivity, queue connectivity, storage, and mail configuration.
- Use production-only Docker settings: `APP_DEBUG=false`, secure cookies, restrictive trusted hosts/proxies, non-local environment, and no demo payment behavior.
- Keep generated build artifacts policy consistent: either build during deployment or commit verified assets, but do not allow stale `public/build` output to drift from source.
- Document backup frequency, restore testing, retention, upload cleanup, log rotation, and incident response.
- Use separate staging and production databases and run migrations as a controlled release step.

## 7. Testing Strategy

### Immediate test additions

- API authorization matrix for each role and college scope.
- Payment idempotency, duplicate transaction identifiers, amount mismatch, replay, rejection, reversal, and manual override tests.
- Enrollment-window tests for closed, future, expired, overlapping, and race-condition cases.
- Private document download tests for owner, unauthorized student, admin, college admin, and unauthenticated access.
- Realistic file tests with mismatched extensions, invalid image content, oversized files, and dangerous document names.
- Concurrent seat-allocation tests and database uniqueness tests.
- Queue job tests for retries, missing records, stale records, and failed notifications.
- Browser tests for the multi-step enrollment wizard, especially validation, upload recovery, back navigation, and duplicate submission.

### Test infrastructure

- Make test database configuration explicit in CI and local test commands.
- Use an isolated database or transaction strategy so tests do not depend on a developer's PostgreSQL state.
- Add static analysis and formatting checks to CI.
- Add dependency vulnerability checks and a production build check.
- Publish coverage for authorization, payment, enrollment transitions, and file access rather than only overall line coverage.

## 8. Documentation Cleanup

The README and older documents describe different Laravel versions, database options, setup paths, and feature states. Consolidate them into one current source of truth.

Update the documentation to include:

- Laravel and PHP versions from `composer.json`.
- PostgreSQL-first setup matching the current migrations and startup guide.
- Required PHP extensions and Node version.
- Environment variables without real credentials or application keys.
- Exact roles, permissions, route groups, and seeded-account behavior.
- Queue worker and scheduler requirements for local and production use.
- Payment integration configuration and a clear statement that mock payments are local-test-only.
- Private file-storage and backup expectations.
- Deployment steps for Docker and Railway, including process responsibilities.
- A short architecture diagram or request-flow description.

Mark historical audits as historical. Every security finding should state whether it is open, fixed, or needs re-verification against the current code.

## 9. Suggested Delivery Plan

### Phase 1: Release blockers

- Remove and rotate exposed credentials and demo defaults.
- Close payment and document-access vulnerabilities.
- Complete API policies and college scoping.
- Enforce enrollment windows in transactions.
- Fix seat allocation runtime and concurrency defects.

### Phase 2: Reliability

- Add payment event storage and idempotency.
- Formalize status transitions and audit events.
- Provision worker and scheduler processes.
- Add health checks, failed-job monitoring, and backups.
- Stabilize the CI test database and add the missing security matrix.

### Phase 3: Maintainability

- Move programs and operational values into managed data.
- Extract large inline enrollment JavaScript into tested frontend modules.
- Simplify controller responsibilities around application services or actions.
- Standardize API resources, error responses, pagination, and request validation.
- Consolidate setup, deployment, and security documentation.

## 10. Definition of Production Readiness

The portal should be considered ready for production only when all of the following are demonstrated in staging:

- Payment callbacks are authenticated, idempotent, reconciled, and audited.
- Every sensitive API and download path passes an authorization and scope test.
- Enrollment submission is impossible outside an active, valid window.
- Documents are private and access-controlled.
- Seat allocation remains correct under concurrent retries.
- Queue workers and the scheduler run independently and are monitored.
- Secrets are externalized and production debug/demo settings are disabled.
- CI runs isolated tests, static checks, dependency checks, and a production asset build.
- Database restore and application rollback procedures have been rehearsed.
- Documentation matches the code and deployment environment.
