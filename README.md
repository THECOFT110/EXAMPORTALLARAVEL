# SALU Exam Portal - Laravel Full-Stack Application

A complete, production-ready examination management system built with Laravel 11, featuring modern authentication, premium UI with Tailwind CSS, and comprehensive admin/student portals.

## 🚀 Features

### Student Portal
- ✅ **Registration & Authentication** - Email/CNIC login with password reset
- ✅ **Online Enrollment** - Apply for programs with document uploads
- ✅ **Fee Management** - View and pay fees online, download challans
- ✅ **Admit Cards** - Download admit cards for exams
- ✅ **Results** - View and download result cards
- ✅ **Dashboard** - Track application status and progress

### Admin Portal
- ✅ **Enrollment Management** - Approve/reject applications with bulk operations
- ✅ **Student Management** - View and manage student records
- ✅ **College Management** - CRUD operations for affiliated colleges
- ✅ **Academic Year Management** - Configure academic years and enrollment windows
- ✅ **Seat Allocation** - Automatic seat allocation with capacity management
- ✅ **Fee Verification** - Verify payments and generate reports
- ✅ **Audit Logs** - Complete activity tracking
- ✅ **System Settings** - Configure portal settings

### Technical Features
- 🔐 Multi-guard authentication (Student, Admin, Super Admin)
- 📧 Email notifications (enrollment, payment, results)
- 📄 PDF generation (challans, admit cards, results, reports)
- 📁 File upload with validation and optimization
- 🔄 Background job processing with queues
- 🎨 Premium UI with Tailwind CSS and Alpine.js
- 📱 Fully responsive design
- 🔍 Advanced search and filtering
- 📊 Real-time statistics and analytics
- 🛡️ Role-based access control

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0 or PostgreSQL
- Redis (optional, for queue processing)

## 🛠️ Installation

### 1. Clone the Repository
```bash
cd d:/SaluExamPortal_Laravel
```

### 2. Install PHP Dependencies
```powershell
composer install
```

### 3. Install Node Dependencies
```powershell
npm install
```

### 4. Environment Setup
```powershell
# Copy environment file
Copy-Item .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salu_exam_portal
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Run Migrations & Seeders
```powershell
# Create database tables
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 7. Storage Setup
```powershell
# Create storage link
php artisan storage:link
```

### 8. Build Frontend Assets
```powershell
# Development
npm run dev

# Production
npm run build
```

### 9. Start Development Server
```powershell
# Laravel server
php artisan serve

# Queue worker (separate terminal)
php artisan queue:work

# Vite dev server (separate terminal)
npm run dev
```

## 🔑 Default Seeded Accounts

> [!NOTE]
> Passwords in local development default to environment variables `SEED_ADMIN_PASSWORD` and `SEED_STUDENT_PASSWORD` configured in your `.env` file (or `admin123` / `student123` in local testing environments).

| Role | Identifier / Email | Notes |
|---|---|---|
| **Super Admin** | `admin@saluexamportal.edu.pk` | Full system control |
| **Admin** | `admin2@saluexamportal.edu.pk` | Examination controller |
| **College Admin** | `principal.gssc@saluexamportal.edu.pk` | College-scoped portal |
| **Test Student** | `student@example.com` (or CNIC: `42101-3456789-3`) | Sample enrolled student |

## 📁 Project Structure

```
SaluExamPortal_Laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # All controllers
│   │   └── Middleware/        # Custom middleware
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic services
│   └── Jobs/                  # Background jobs
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   ├── css/                   # Tailwind CSS
│   └── js/                    # JavaScript/Alpine.js
├── routes/
│   ├── web.php                # Web routes
│   ├── api.php                # API routes
│   └── console.php            # Console routes
└── public/                    # Public assets
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login (email/CNIC)
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Get current user
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password

### Student Routes (Authenticated)
- `GET /api/student/dashboard` - Dashboard data
- `GET /api/student/profile` - User profile
- `PUT /api/student/profile` - Update profile
- `GET /api/student/enrollments` - List enrollments
- `GET /api/student/enrollments/{id}` - Enrollment details
- `GET /api/student/enrollments/{id}/results` - View results

### Admin Routes (Admin/SuperAdmin)
- `GET /api/admin/dashboard/stats` - Dashboard statistics
- `GET /api/admin/enrollments` - List all enrollments
- `PUT /api/admin/enrollments/{id}/approve` - Approve enrollment
- `PUT /api/admin/enrollments/{id}/reject` - Reject enrollment
- `PUT /api/admin/enrollments/bulk-approve` - Bulk approve
- `GET /api/admin/students` - List all students
- `GET /api/admin/colleges` - List colleges
- `POST /api/admin/colleges` - Create college
- `GET /api/admin/audit-logs` - View audit logs

## 🎨 Frontend Technologies

- **Laravel Blade** - Template engine
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Flowbite** - UI components
- **Vite** - Modern build tool

## 🔄 Background Jobs

The application uses Laravel Queues for background processing:

```powershell
# Run queue worker
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=emails

# Run scheduler (for cron jobs)
php artisan schedule:work
```

### Scheduled Tasks
- Expire unpaid fees daily
- Send enrollment reminders
- Generate periodic reports

## 📧 Email Configuration

Configure email in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@saluexamportal.edu.pk"
MAIL_FROM_NAME="SALU Exam Portal"
```

## 🔒 Security Features

- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Password hashing (Bcrypt)
- Rate limiting on authentication routes
- Role-based access control
- Secure file uploads with validation

## 🧪 Testing

```powershell
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Generate code coverage
php artisan test --coverage
```

## 📦 Production Deployment

### 1. Optimize Application
```powershell
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### 2. Set Environment
```env
APP_ENV=production
APP_DEBUG=false
```

### 3. Configure Queue Worker
Set up supervisor or similar process manager to keep queue worker running.

### 4. Setup Cron Job
Add to crontab:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🐛 Troubleshooting

### Clear Caches
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Storage Permissions
```powershell
chmod -R 775 storage bootstrap/cache
```

### Rebuild Assets
```powershell
npm run build
php artisan view:clear
```

## 📝 License

This project is licensed under the MIT License.

## 👥 Credits

Converted from .NET/C# to Laravel by Kiro AI

## 📞 Support

For issues and questions:
- Email: info@saluexamportal.edu.pk
- Phone: 022-2771331

## 🎯 Roadmap

- [ ] SMS notifications integration
- [ ] Payment gateway integration (JazzCash, EasyPaisa)
- [ ] Mobile app (React Native)
- [ ] Biometric authentication
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Export to Excel/CSV
- [ ] Automated seat allocation algorithm enhancements

---

**Built with ❤️ using Laravel**
