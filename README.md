## Tech Stack

### Backend

- **Framework:** Symfony 7.3.5
- **PHP Version:** 8.2+
- **Database:** MySQL 8.2 (SQLite supported)
- **ORM:** Doctrine ORM 3.5.3
- **Email:** Symfony Mailer with Mailtrap SMTP

### Frontend

- **JavaScript Framework:** Turbo & Stimulus 3.0
- **Build Tool:** Webpack 5.74.0 with Symfony Webpack Encore 5.1.0
- **CSS Framework:** Tailwind 4.1.16
- **UI Components:** DaisyUI 5.4.3
- **Language:** TypeScript

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL 8.2+ or SQLite

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database credentials
   ```

5. **Build assets**
   ```bash
   npm run build
   # or for development with watch mode
   npm run watch
   ```

6. **Start the server**
   ```bash
   symfony server:start
   ```

## Demo Accounts

Use these credentials to login and explore different user roles:

### Administrator

- **Email:** admin@vla-library.it.com
- **Password:** admin

### Regular User

- **Email:** user@vla-library.it.com
- **Password:** applicationuser

## Features

- User authentication with email verification
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- Email notifications via Mailtrap
