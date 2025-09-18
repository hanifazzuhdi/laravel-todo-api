# Todo List API

A simple RESTful API for managing todo items built with Laravel.

## 📋 Requirements

-   PHP 8.1 or higher
-   Composer
-   Laravel 12.x
-   SQLite (included) or MySQL/PostgreSQL

## 🛠️ Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd todo-list
    ```

2. **Install dependencies**

    ```bash
    composer install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database setup**

    ```bash
    # Create SQLite database (default)
    touch database/database.sqlite

    # Run migrations
    php artisan migrate

    # Seed sample data (optional)
    php artisan db:seed
    ```

5. **Start the development server**
    ```bash
    php artisan serve
    ```

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api/v1
```

### Endpoints

All endpoints can be accessed in this collection: [API Documentation](https://documenter.getpostman.com/view/13031385/2sB3HrmHGW)

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/TodoServiceTest.php

# Run tests with coverage
php artisan test --coverage
```

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

**Happy Coding!**
