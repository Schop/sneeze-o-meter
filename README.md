# 🤧 Sneeze Tracker

A Laravel-based web application for tracking sneezes with user authentication and a public leaderboard.

## Features

- **User Authentication**: Register and log in to track your sneezes
- **Record Sneezes**: Log each sneeze with:
  - Timestamp (when the sneeze occurred)
  - Intensity (1-5 scale)
  - Optional notes
- **Personal Dashboard**: 
  - View your sneeze history
  - See statistics (today, this week, all time)
  - Delete sneeze records
- **Public Leaderboard**: See who sneezes the most (accessible without login)
- **Responsive Design**: Works on desktop and mobile devices

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL, PostgreSQL, or SQLite
- Node.js & NPM (for frontend assets)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sneeze-tracker
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Create environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure database**
   
   Edit the `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sneeze_tracker
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```
   
   Or for development with hot reload:
   ```bash
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    
    Open your browser and navigate to: `http://localhost:8000`

## Database Schema

### Users Table
- `id`: Primary key
- `name`: User's name
- `email`: User's email (unique)
- `password`: Hashed password
- `email_verified_at`: Email verification timestamp
- `remember_token`: Remember me token
- `created_at`, `updated_at`: Timestamps

### Sneezes Table
- `id`: Primary key
- `user_id`: Foreign key to users table
- `sneezed_at`: Timestamp when the sneeze occurred
- `intensity`: Integer (1-5, nullable)
- `notes`: Text field for additional notes (nullable)
- `created_at`, `updated_at`: Timestamps

## Routes

### Public Routes
- `/` - Welcome page with link to leaderboard
- `/leaderboard` - Public leaderboard showing all users' sneeze counts
- `/login` - Login page
- `/register` - Registration page

### Authenticated Routes
- `/dashboard` - User dashboard with sneeze recording form and history
- `POST /sneezes` - Record a new sneeze
- `DELETE /sneezes/{sneeze}` - Delete a sneeze record
- `/profile` - User profile management

## Usage

1. **Register an account** or log in if you already have one
2. **Record a sneeze** using the form on your dashboard:
   - Select when the sneeze occurred (defaults to current time)
   - Optionally rate the intensity (1-5)
   - Add notes if desired
3. **View your stats** on the dashboard (today, this week, all time)
4. **Check the leaderboard** to see how you rank against others
5. **Manage your sneezes** by viewing history and deleting records if needed

## Models and Relationships

### User Model
- `hasMany(Sneeze::class)` - A user can have many sneezes

### Sneeze Model
- `belongsTo(User::class)` - Each sneeze belongs to one user
- **Fillable**: `user_id`, `sneezed_at`, `intensity`, `notes`
- **Casts**: `sneezed_at` to datetime, `intensity` to integer

## Controller Methods

### SneezeController

- `leaderboard()` - Display public leaderboard with user rankings
- `index()` - Show authenticated user's dashboard with sneeze history and stats
- `store(Request $request)` - Create a new sneeze record
- `destroy(Sneeze $sneeze)` - Delete a sneeze record (with authorization check)

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
This project follows Laravel coding standards. To format code:
```bash
./vendor/bin/pint
```

## Technologies Used

- **Laravel 11.x** - PHP Framework
- **Breeze** - Authentication scaffolding
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - JavaScript framework for interactivity
- **Blade** - Templating engine
- **MySQL/PostgreSQL/SQLite** - Database

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
