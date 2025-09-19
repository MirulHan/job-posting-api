# Job Posting API

## Overview

A simple, clean setup guide for running Laravel locally. This is a comprehensive job posting and application management system with email notifications and full test coverage.

---

## System Requirements

- PHP 8.1 or higher
- Composer
- A database (MySQL or PostgreSQL)

---

## 1) Get the Code

- Clone the repository
- Install PHP dependencies:

```bash
composer install
```

- Copy the example environment file:

```bash
cp .env.example .env
```

---

## 2) Configure Environment

Edit `.env` and set at least:

```env
APP_URL=http://localhost:8000
DB_DATABASE="job_posting_api"
DB_USERNAME="your_username"
DB_PASSWORD="your_password"

# Email configuration (optional - mock mode available)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourcompany.com"
MAIL_FROM_NAME="${APP_NAME}"

# Email mock mode (set to true for development)
MAIL_MOCK_MODE=true
```

---

## 3) Application Setup

### Generate the app key

```bash
php artisan key:generate
```

This writes `APP_KEY` in `.env` for Laravel's encryption.

### Run database migrations

```bash
php artisan migrate
```

Creates the database tables defined in your migrations.

### Seed the database

```bash
php artisan db:seed
```

This populates the database with sample job posts and applications for testing.

### Create the storage symlink

```bash
php artisan storage:link
```

Allows public access to files in `storage/app/public` via `public/storage`.

---

## 4) Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

---

## 5) API Documentation & Usage

### API Endpoints

Once the installation is complete, you can access:

- **API Documentation**: Visit `/doc/api` to see all available API endpoints with detailed documentation
- **Web Interface**: Visit `/` to see the job posting interface where you can view and manipulate job application data

### Key Features

- **Job Post Management**: Create, view, and manage job postings
- **Job Application System**: Submit and track job applications
- **Email Notifications**: Automatic email confirmations for applicants (with mock mode for development)
- **Service Layer Architecture**: Clean separation of business logic
- **Comprehensive Testing**: Full unit and feature test coverage

---

## 6) Testing

### Run All Tests

```bash
php artisan test
```

This runs the complete test suite including:

- Unit tests for all services
- Feature tests for controllers
- Email functionality tests

### Test Email System

```bash
php artisan email:test --mock
```

This command:

- Creates a test job application
- Sends a mock email confirmation
- Shows you the email details that would be sent
- Stores mock emails in `storage/logs/mock_emails.json`

### Clear Mock Emails

```bash
php artisan email:test --clear
```

Clears all stored mock emails for testing.

---

## 7) Project Structure

### Project Architecture

- **JobPostService**: Handles job posting business logic
- **JobApplicationService**: Manages job applications and email integration
- **EmailService**: Handles email sending with mock/production modes

### Technical Features

- **Mock Email System**: Development-friendly email testing without sending real emails
- **Service Pattern**: Clean architecture with dependency injection
- **Comprehensive Validation**: Full validation for all inputs
- **Professional Email Templates**: Well-designed HTML email confirmations
- **Database Persistence**: Tests don't clear database, allowing for continuous development

---

## 8) Email Configuration

### Development Mode (Mock Emails)

Set in your `.env`:

```env
MAIL_MOCK_MODE=true
```

Mock emails are stored in `storage/logs/mock_emails.json` and can be viewed using the test command.

### Production Mode (Real Emails)

Set in your `.env`:

```env
MAIL_MOCK_MODE=false
```

Configure your actual SMTP settings for real email sending.

---

## 9) API Examples

### Create a Job Application

```bash
curl -X POST http://localhost:8000/api/job-applications \
  -H "Content-Type: application/json" \
  -d '{
    "job_post_id": 1,
    "full_name": "John Doe",
    "phone_number": "+1234567890",
    "email": "john@example.com",
    "work_experience": "5 years of web development experience..."
  }'
```

### Get Job Posts

```bash
curl http://localhost:8000/api/job-posts
```

---

## 10) Troubleshooting

### Common Issues

1. **Database Connection**: Ensure your database is running and credentials in `.env` are correct
2. **Permission Issues**: Make sure `storage/` and `bootstrap/cache/` directories are writable
3. **Email Issues**: Use mock mode for development, check SMTP settings for production

### Reset Database

If you need to reset everything:

```bash
php artisan migrate:fresh --seed
```

This drops all tables, recreates them, and seeds with sample data.

---

## Tech Stack

- **Backend**: Laravel 10.x
- **Frontend**: Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Email**: Laravel Mail with mock system
- **Testing**: PHPUnit with comprehensive coverage
- **Architecture**: Service layer pattern with dependency injection
