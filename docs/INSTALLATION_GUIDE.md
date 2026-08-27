# SALU Exam Portal - Complete Installation Guide

This guide will help you set up the SALU Exam Portal from scratch on Windows.

## 📋 Prerequisites Checklist

Before starting, ensure you have installed:

- [ ] PHP 8.2 or higher
- [ ] Composer (PHP dependency manager)
- [ ] Node.js 18+ and NPM
- [ ] MySQL 8.0 or PostgreSQL
- [ ] Git (optional)

### Verify Installations

Open PowerShell and run:
```powershell
php -v        # Should show PHP 8.2+
composer -V   # Should show Composer version
node -v       # Should show Node 18+
npm -v        # Should show NPM version
mysql --version  # Should show MySQL version
```

## 🚀 Step-by-Step Installation

### Step 1: Prepare Database

1. Open MySQL Command Line or MySQL Workbench
2. Create a new database:

```sql
CREATE DATABASE salu_exam_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Configure Environment

1. Copy the example environment file:
```powershell
Copy-Item .env.example .env
```

2. Open `.env` file and configure:

```env
APP_NAME="SALU Exam Portal"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salu_exam_portal
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

MAIL_MAILER=log
# For production, configure SMTP settings
```

### Step 3: Install Dependencies

```powershell
# Install PHP packages (may take a few minutes)
composer install

# Install Node packages (may take a few minutes)
npm install
```

### Step 4: Generate Application Key

```powershell
php artisan key:generate
```

### Step 5: Run Database Migrations

```powershell
# Create all database tables
php artisan migrate

# If you get an error, try:
php artisan migrate:fresh
```

### Step 6: Seed Sample Data

```powershell
# Insert sample users, colleges, and settings
php artisan db:seed
```

This will create:
- **Super Admin:** admin@saluexamportal.edu.pk (password: admin123)
- **Admin:** admin2@saluexamportal.edu.pk (password: admin123)
- **Test Student:** student@example.com (password: student123)
- 10 additional test students
- 8 sample colleges
- Academic years and enrollment windows
- System settings

### Step 7: Create Storage Link

```powershell
php artisan storage:link
```

### Step 8: Build Frontend Assets

```powershell
# For development (with hot reload)
npm run dev

# OR for production (optimized)
npm run build
```

### Step 9: Start the Application

**Option A: Development Mode (Recommended)**

Open **3 separate PowerShell windows**:

Window 1 - Laravel Server:
```powershell
php artisan serve
```

Window 2 - Queue Worker:
```powershell
php artisan queue:work
```

Window 3 - Vite Dev Server:
```powershell
npm run dev
```

**Option B: Simple Mode (Without queue)**

Just run:
```powershell
php artisan serve
```

Then in another terminal:
```powershell
npm run dev
```

### Step 10: Access the Application

Open your browser and visit:
- **Homepage:** http://localhost:8000
- **Student Login:** http://localhost:8000/login
- **Admin Login:** http://localhost:8000/login

## 🔐 Test Accounts

### Super Administrator
- **Email:** admin@saluexamportal.edu.pk
- **Password:** admin123
- **Access:** Full system access

### Test Student
- **Email:** student@example.com
- **CNIC:** 42101-3456789-3 (can also login with this)
- **Password:** student123
- **Access:** Student portal

### Additional Students
- **Email Pattern:** student1@example.com to student10@example.com
- **Password:** password123

## ✅ Verify Installation

### 1. Check Homepage
Visit http://localhost:8000 - You should see the welcome page with SALU branding

### 2. Test Registration
1. Click "Register" 
2. Fill in the form with test data
3. Submit - Should redirect to login

### 3. Test Login
1. Use: student@example.com / student123
2. Should redirect to student dashboard
3. Dashboard should show stats and enrollment options

### 4. Test Admin
1. Logout
2. Login with: admin@saluexamportal.edu.pk / admin123
3. Should redirect to admin dashboard
4. Should see enrollment management, statistics, etc.

## 🐛 Common Issues & Solutions

### Issue 1: "SQLSTATE[HY000] [1045] Access denied"
**Solution:** Check MySQL credentials in `.env` file

### Issue 2: "Class 'X' not found"
**Solution:** 
```powershell
composer dump-autoload
php artisan clear-compiled
```

### Issue 3: "npm ERR!" or build errors
**Solution:**
```powershell
Remove-Item -Recurse -Force node_modules
Remove-Item package-lock.json
npm install
```

### Issue 4: "The stream or file could not be opened"
**Solution:**
```powershell
# Create necessary directories
New-Item -ItemType Directory -Force -Path storage/logs
New-Item -ItemType Directory -Force -Path storage/framework/cache
New-Item -ItemType Directory -Force -Path storage/framework/sessions
New-Item -ItemType Directory -Force -Path storage/framework/views
```

### Issue 5: 404 on routes
**Solution:**
```powershell
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue 6: CSS not loading
**Solution:**
1. Make sure Vite dev server is running: `npm run dev`
2. Or build assets: `npm run build`
3. Clear browser cache

### Issue 7: Queue jobs not processing
**Solution:**
```powershell
# Make sure queue worker is running
php artisan queue:work

# Or restart it
php artisan queue:restart
```

## 📧 Email Configuration (Optional)

For testing emails locally, use Mailtrap:

1. Sign up at https://mailtrap.io (free)
2. Get SMTP credentials
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

## 🎨 Customization

### Change Site Name
Edit `.env`:
```env
APP_NAME="Your Custom Name"
```

### Change Colors
Edit `tailwind.config.js`:
```javascript
colors: {
  primary: {
    500: '#your-color',
    600: '#your-darker-color',
  }
}
```

Then rebuild:
```powershell
npm run build
```

### Change Logo
Replace the logo in:
- `resources/views/layouts/navbar.blade.php`

## 🔄 Updating the Application

```powershell
# Pull latest changes (if using Git)
git pull

# Update dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Rebuild assets
npm run build

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🚀 Deployment to Production

### 1. Optimize for Production

```powershell
# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Build optimized assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Update Environment

```env
APP_ENV=production
APP_DEBUG=false
```

### 3. Configure Web Server

#### Using Apache
Create `.htaccess` in public folder (already included)

#### Using Nginx
Add server block:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/project/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Set Permissions

```powershell
# On Linux/Unix
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Setup Queue Worker

Use Supervisor or PM2 to keep queue worker running:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
```

### 6. Setup Cron Job

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📚 Next Steps

1. **Customize Content:** Edit Blade templates in `resources/views/`
2. **Add Features:** Create new controllers and routes
3. **Configure Email:** Set up production email service
4. **Add Payment Gateway:** Integrate payment providers
5. **Enable SSL:** Set up HTTPS for production

## 🆘 Getting Help

- **Documentation:** See README.md
- **Laravel Docs:** https://laravel.com/docs
- **Tailwind CSS Docs:** https://tailwindcss.com/docs

## ✅ Installation Complete!

You now have a fully functional exam portal system. Happy coding! 🎉
