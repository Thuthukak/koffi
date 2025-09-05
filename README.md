# Kofi Barbershop Management System

A modern barbershop management application built with Laravel (backend) and Vue.js (frontend) to streamline appointment scheduling, customer management, and business operations.

## 🚀 Features

- **Customer Management**: Register and manage customer profiles
- **Appointment Booking**: Online appointment scheduling system
- **Service Management**: Manage barbershop services and pricing
- **Staff Management**: Handle barber schedules and availability
- **Dashboard Analytics**: Business insights and reporting
- **Payment Integration**: Process payments and track revenue
- **Notification System**: SMS/Email reminders for appointments
- **Inventory Management**: Track products and supplies

## 🛠 Tech Stack

### Backend
- **Laravel 10.x** - PHP Framework
- **MySQL** - Database
- **Laravel Sanctum** - API Authentication
- **Laravel Queue** - Background job processing
- **Laravel Mail** - Email notifications

### Frontend
- **Vue.js 3** - JavaScript Framework
- **Vue Router** - Client-side routing
- **Pinia** - State management
- **Tailwind CSS** - Utility-first CSS framework
- **Axios** - HTTP client
- **Vite** - Build tool

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.1+**
- **Composer**
- **Node.js 16+**
- **npm or yarn**
- **MySQL 8.0+**
- **Git**

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/kofi-barbershop.git
cd kofi-barbershop
```

### 2. Backend Setup (Laravel)

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kofi_barbershop
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run database migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Create storage link
php artisan storage:link

# Install Laravel Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Frontend Setup (Vue.js)

```bash
# Install JavaScript dependencies
npm install

# Build assets for development
npm run dev
```

### 4. Start Development Servers

```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite development server
npm run dev
```

The application will be available at:
- Frontend: `http://localhost:3000`
- Backend API: `http://localhost:8000`

## 🔧 Configuration

### Environment Variables

Create a `.env` file based on `.env.example` and configure:

```env
APP_NAME="Kofi Barbershop"
APP_ENV=local
APP_KEY=your_generated_key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kofi_barbershop
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password

# SMS Configuration (optional)
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_token
TWILIO_PHONE_NUMBER=your_twilio_number

# Payment Gateway (optional)
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

### Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE kofi_barbershop;
exit

# Run migrations with sample data
php artisan migrate --seed
```

## 📁 Project Structure

```
kofi-barbershop/
├── app/                    # Laravel application files
│   ├── Http/Controllers/   # API controllers
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/               # Vue.js application
│   │   ├── components/   # Vue components
│   │   ├── views/        # Vue pages
│   │   ├── router/       # Vue Router configuration
│   │   └── stores/       # Pinia stores
│   └── views/            # Blade templates
├── routes/
│   ├── api.php           # API routes
│   └── web.php           # Web routes
└── public/               # Public assets
```

## 🎯 Usage

### Default Admin User

After seeding the database, you can login with:
- **Email**: admin@kofibarbershop.com
- **Password**: password123

### API Endpoints

The application provides RESTful API endpoints:

```
# Authentication
POST /api/login
POST /api/register
POST /api/logout

# Appointments
GET /api/appointments
POST /api/appointments
PUT /api/appointments/{id}
DELETE /api/appointments/{id}

# Services
GET /api/services
POST /api/services
PUT /api/services/{id}
DELETE /api/services/{id}

# Customers
GET /api/customers
POST /api/customers
PUT /api/customers/{id}
DELETE /api/customers/{id}
```

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run JavaScript tests
npm run test

# Run tests with coverage
php artisan test --coverage
```

## 🚀 Deployment

### Production Build

```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Environment Setup

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up SSL certificate
5. Configure web server (Apache/Nginx)

### Queue Workers

For production, set up queue workers:

```bash
# Start queue worker
php artisan queue:work --daemon

# Or use Supervisor for process management
sudo apt-get install supervisor
```

## 📱 Mobile Responsiveness

The application is fully responsive and works seamlessly on:
- Desktop computers
- Tablets
- Mobile phones

## 🔒 Security Features

- CSRF protection
- SQL injection prevention
- XSS protection
- Rate limiting
- Input validation and sanitization
- Secure authentication with Laravel Sanctum

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Code Style

- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript
- Write meaningful commit messages
- Add tests for new features

## 🐛 Troubleshooting

### Common Issues

1. **Permission Denied**: Ensure storage and cache directories are writable
   ```bash
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

2. **Database Connection Error**: Check database credentials in `.env`

3. **Asset Not Found**: Run `npm run build` and `php artisan storage:link`

4. **CORS Issues**: Configure CORS settings in `config/cors.php`

## 📞 Support

For support and questions:
- Email: support@kofibarbershop.com
- Phone: +1 (555) 123-4567
- GitHub Issues: [Create an issue](https://github.com/your-username/kofi-barbershop/issues)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🎉 Acknowledgments

- Laravel community for the excellent framework
- Vue.js team for the progressive framework
- Tailwind CSS for the utility-first CSS framework
- All contributors who helped build this application

---

**Kofi Barbershop Management System** - Streamlining barbershop operations with modern technology.