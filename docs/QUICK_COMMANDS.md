# 🚀 Quick Command Reference

Essential commands for working with the SALU Exam Portal.

## 🏁 Initial Setup

```powershell
# Install dependencies
composer install
npm install

# Setup environment
Copy-Item .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Build assets
npm run build
```

## 🔧 Development

```powershell
# Start Laravel server
php artisan serve

# Run Vite dev server (separate terminal)
npm run dev

# Run queue worker (separate terminal)
php artisan queue:work

# Run scheduler (separate terminal)
php artisan schedule:work
```

## 🗄️ Database

```powershell
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Migrate with seeding
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Check migration status
php artisan migrate:status
```

## 🧹 Clear Caches

```powershell
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# Rebuild autoloader
composer dump-autoload
```

## 📦 Build & Optimize

```powershell
# Development build with hot reload
npm run dev

# Production build (optimized)
npm run build

# Cache configuration (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader (production)
composer install --optimize-autoloader --no-dev
```

## 🔄 Queue Management

```powershell
# Start queue worker
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=emails

# Process one job
php artisan queue:work --once

# Restart queue workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

## 👤 User Management

```powershell
# Create new user (via tinker)
php artisan tinker
>>> User::create([
    'full_name' => 'John Doe',
    'father_name' => 'Father Name',
    'cnic' => '42101-1234567-1',
    'email' => 'john@example.com',
    'phone' => '0300-1234567',
    'password' => Hash::make('password'),
    'role' => 'STUDENT',
    'is_verified' => true
]);
```

## 🧪 Testing

```powershell
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest

# Run with coverage
php artisan test --coverage
```

## 🔍 Debug & Logs

```powershell
# View logs
Get-Content storage/logs/laravel.log -Tail 50

# Tail logs (watch in real-time)
Get-Content storage/logs/laravel.log -Wait -Tail 50

# Clear logs
Remove-Item storage/logs/*.log
```

## 🏗️ Code Generation

```powershell
# Create controller
php artisan make:controller ExampleController

# Create model with migration
php artisan make:model Example -m

# Create migration
php artisan make:migration create_examples_table

# Create seeder
php artisan make:seeder ExampleSeeder

# Create job
php artisan make:job ProcessExample

# Create middleware
php artisan make:middleware CheckExample
```

## 📊 Maintenance

```powershell
# Put application in maintenance mode
php artisan down

# Bring application back up
php artisan up

# Check application status
php artisan optimize:clear
php artisan about
```

## 🔐 Security

```powershell
# Generate new application key
php artisan key:generate

# Clear expired password reset tokens
php artisan auth:clear-resets
```

## 📝 Routes & API

```powershell
# List all routes
php artisan route:list

# List API routes only
php artisan route:list --path=api

# List routes for specific controller
php artisan route:list --name=student
```

## 🎨 Frontend

```powershell
# Watch and rebuild on changes
npm run dev

# Build for production
npm run build

# Update dependencies
npm update

# Check for outdated packages
npm outdated
```

## 🗃️ Backup

```powershell
# Export database
mysqldump -u root -p salu_exam_portal > backup.sql

# Import database
mysql -u root -p salu_exam_portal < backup.sql
```

## 🚀 Deployment Checklist

```powershell
# 1. Update environment
# Set APP_ENV=production and APP_DEBUG=false in .env

# 2. Install production dependencies
composer install --optimize-autoloader --no-dev

# 3. Build assets
npm run build

# 4. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Migrate database
php artisan migrate --force

# 6. Setup queue worker (use supervisor/pm2)

# 7. Setup cron job for scheduler
```

## 💡 Quick Fixes

```powershell
# Fix permissions (Windows)
# Run PowerShell as Administrator
icacls "storage" /grant Users:F /T
icacls "bootstrap/cache" /grant Users:F /T

# Reset everything
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
npm run build
```

## 🔧 Troubleshooting

```powershell
# App not working after changes?
php artisan config:clear
php artisan route:clear
composer dump-autoload

# Assets not loading?
npm run build
php artisan view:clear

# Queue not processing?
php artisan queue:restart
php artisan queue:work

# Database issues?
php artisan migrate:fresh --seed

# Complete reset?
Remove-Item -Recurse -Force vendor, node_modules
composer install
npm install
php artisan migrate:fresh --seed
npm run build
```

## 📱 Useful Aliases (Optional)

Add to your PowerShell profile for quick access:

```powershell
# Edit profile
notepad $PROFILE

# Add these functions
function art { php artisan $args }
function migrate { php artisan migrate $args }
function tinker { php artisan tinker }
function serve { php artisan serve }
function fresh { php artisan migrate:fresh --seed }
```

Then use:
```powershell
art serve
migrate
tinker
fresh
```

---

**Keep this file handy for quick reference!** 📌
