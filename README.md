# Laravel Task Manager API

A robust and secure RESTful API for managing tasks, built with Laravel 10 and Sanctum authentication. This project provides endpoints for user authentication and full CRUD operations on tasks, complete with request validation, API resources, database migrations, seeders, and comprehensive tests.

---

## 🛠️ Features

* **User Authentication**: Issue and manage API tokens with Laravel Sanctum.
* **Task Management**: Create, read, update, and delete tasks.
* **Validation**: Requests validated via `StoreTaskRequest` and `UpdateTaskRequest`.
* **API Resources**: Response transformation with `TaskResource` and `UserResource`.
* **Database Migrations & Seeders**: Migrations for users, tokens, and tasks; seeders to populate sample data.
* **Factories**: Quickly generate test data for users and tasks.
* **Automated Tests**: Feature and unit tests covering authentication and task endpoints.

---

## 🚀 Getting Started

### Prerequisites

* PHP ^8.1
* Composer
* MySQL (or any supported database)
* Node.js & npm (for frontend assets)

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/your-repo.git
   cd your-repo
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Environment setup**

   * Copy `.env.example` to `.env`
   * Configure your database credentials and other env variables

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database migrations & seeding**

   ```bash
   php artisan migrate --seed
   ```

5. **Build frontend assets** (optional)

   ```bash
   npm run dev
   ```

6. **Serve the application**

   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`.

---

## 📐 Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── TaskController.php
│   ├── Middleware/
│   └── Requests/
│       ├── StoreTaskRequest.php
│       └── UpdateTaskRequest.php
├── Models/
│   ├── User.php
│   └── Task.php
└── Resources/
    ├── UserResource.php
    └── TaskResource.php

config/       Configuration files (Sanctum, database, cache, etc.)

database/
├── migrations/    Database schema definitions
├── seeders/       Initial data seeders
└── factories/     Model factories for testing

tests/        Automated unit and feature tests
public/       Front controller and assets
resources/    Views & frontend scripts
routes/       Route definitions (api.php, web.php)

```

---

## 🔑 Authentication

* **Issue Token**: `POST /api/login`

  * **Body**: `email`, `password`
  * **Response**: Bearer token

* **Protected Routes**: Add `Authorization: Bearer {token}` header to all `/api/tasks` endpoints.

---

## 📋 API Endpoints

| Method | Endpoint          | Description                    |
| ------ | ----------------- | ------------------------------ |
| POST   | `/api/login`      | Authenticate and get API token |
| GET    | `/api/user`       | Get authenticated user profile |
| GET    | `/api/tasks`      | List all tasks                 |
| POST   | `/api/tasks`      | Create a new task              |
| GET    | `/api/tasks/{id}` | Retrieve a specific task       |
| PUT    | `/api/tasks/{id}` | Update a specific task         |
| DELETE | `/api/tasks/{id}` | Delete a specific task         |

**Request Validation**: Both create and update endpoints enforce rules defined in `StoreTaskRequest` and `UpdateTaskRequest`. Validation errors return status `422` with error details.

**Response Format**: All successful responses wrap data in JSON resources:

```json
{
  "data": {
    // Task or User attributes
  }
}
```

---

## 🧪 Running Tests

This project includes both unit and feature tests using PHPUnit.

```bash
php artisan test
# or
vendor/bin/phpunit
```

Tests cover:

* Authentication flows (issue token, access protected routes).
* CRUD operations on tasks (including validation and 404 cases).

---

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature-name`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature-name`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
