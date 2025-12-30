# Expense Management API

A RESTful API built with Laravel 11 for managing expenses, categories, vendors, and users. This project follows clean architecture principles using the Repository and Service patterns with comprehensive authentication and authorization.

## Table of Contents

-   [Features](#features)
-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Database Setup](#database-setup)
    -   [Database Seeding](#database-seeding)
    -   [User Roles and Authorization](#user-roles-and-authorization)
-   [Project Structure](#project-structure)
-   [API Documentation](#api-documentation)
-   [Code Architecture](#code-architecture)
-   [Usage Examples](#usage-examples)
-   [Testing](#testing)
-   [Deployment](#deployment)

## Features

-   ✅ **User Authentication** - Secure login and registration using Laravel Sanctum
-   ✅ **Expense Management** - Create, read, update, and delete expenses
-   ✅ **Category Management** - Organize expenses by categories
-   ✅ **Vendor Management** - Track vendors for expenses
-   ✅ **Soft Deletes** - Recover deleted records when needed
-   ✅ **Advanced Filtering** - Filter expenses by category, vendor, and date range
-   ✅ **Data Insights** - Real-time statistics and analytics for expenses, categories, and vendors
-   ✅ **Authorization** - Role-based access control using Laravel Policies
-   ✅ **Repository Pattern** - Clean separation of data access logic
-   ✅ **Service Layer** - Business logic encapsulation

## Requirements

Before you begin, ensure you have the following installed:

-   **PHP**: >= 8.2
-   **Composer**: Latest version
-   **Node.js**: >= 18.x (for frontend assets)
-   **NPM**: Latest version
-   **Database**: SQLite (default) or MySQL/PostgreSQL
-   **Web Server**: Apache, Nginx, or Laravel's built-in server

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/melogail/master-code-task
cd mastercode
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

Copy the example environment file and generate an application key:

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Database

Open the `.env` file and configure your database settings. By default, the project uses SQLite:

```env
DB_CONNECTION=sqlite
```

For MySQL, update to:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 6: Run Migrations

Create the database structure:

```bash
php artisan migrate
```

### Step 7: (Optional) Seed Database

If you have seeders configured, run:

```bash
php artisan db:seed
```

### Step 8: Start Development Server

```bash
# Start Laravel development server
php artisan serve

# In a new terminal, compile frontend assets
npm run dev
```

Your application will be available at `http://localhost:8000`

## Configuration

### Key Environment Variables

Edit your `.env` file to configure:

```env
# Application
APP_NAME="Expense Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Database Setup

The application includes the following tables:

-   **users** - User accounts with authentication
-   **categories** - Expense categories (soft deletes enabled)
-   **vendors** - Vendor information (soft deletes enabled)
-   **expenses** - Expense records (soft deletes enabled)
-   **personal_access_tokens** - API authentication tokens

### Database Schema

**Expenses Table:**

-   `id` - Primary key
-   `category_id` - Foreign key to categories
-   `vendor_id` - Foreign key to vendors
-   `amount` - Expense amount
-   `date` - Expense date
-   `description` - Expense description
-   `created_at`, `updated_at`, `deleted_at` - Timestamps

### Database Seeding

The project includes database seeders to populate initial data. When you run `php artisan db:seed`, the following test users are created:

#### Seeded Users

**1. Admin User**

```
Email: admin@email.com
Password: password
Role: Administrator (is_admin = true)
```

**2. Staff User**

```
Email: staff@email.com
Password: password
Role: Staff (is_admin = false)
```

> [!WARNING] > **Important for Production**: These are test credentials. Make sure to change these passwords or remove these users before deploying to production.

### User Roles and Authorization

This application implements role-based access control using **Laravel Policies**. There are two user roles with different permission levels:

#### Admin Users (`is_admin = true`)

Admins have **full access** to all resources and operations:

**Categories & Vendors:**

-   ✅ View all categories/vendors
-   ✅ View single category/vendor
-   ✅ Create new categories/vendors
-   ✅ Update existing categories/vendors
-   ✅ Soft delete categories/vendors
-   ✅ View all trashed items
-   ✅ View specific trashed items
-   ✅ Restore trashed items
-   ✅ Permanently delete items (force delete)

**Expenses:**

-   ✅ View all expenses
-   ✅ View single expense
-   ✅ Create new expenses
-   ✅ Update own expenses
-   ✅ Delete own expenses
-   ✅ View all trashed expenses
-   ✅ View specific trashed expenses
-   ✅ Restore trashed expenses
-   ✅ Permanently delete expenses
-   ✅ View expense insights

#### Staff Users (`is_admin = false`)

Staff users have **limited access** based on the `ExpensePolicy`:

**Categories & Vendors:**

-   ✅ View all categories/vendors
-   ✅ View single category/vendor
-   ❌ Create new categories/vendors (Admin only)
-   ❌ Update categories/vendors (Admin only)
-   ❌ Delete categories/vendors (Admin only)
-   ❌ View trashed items (Admin only)
-   ❌ Restore items (Admin only)
-   ❌ Force delete items (Admin only)

**Expenses:**

-   ✅ View all expenses
-   ✅ View single expense
-   ✅ Create new expenses
-   ✅ Delete own expenses
-   ❌ Update expenses (Admin only for all, limited for staff)
-   ❌ View trashed expenses (Admin only)
-   ❌ Restore expenses (Admin only)
-   ❌ Force delete expenses (Admin only)
-   ❌ View expense insights (Admin only)

#### Authorization Implementation

The authorization is implemented in the `Policies` base class and extended by resource-specific policies:

**Base Policy** (`app/Policies/Policies.php`):

```php
// All users can view resources
public function viewAny(User $user): bool
{
    return true;
}

// Only admins can create
public function create(User $user): bool
{
    return $user->is_admin;
}

// Only admins can update
public function update(User $user, Model $model): bool
{
    return $user->is_admin;
}

// Only admins can delete
public function delete(User $user, Model $model): bool
{
    return $user->is_admin;
}

// Only admins can view/restore trashed items
public function viewAllTrashed(User $user): bool
{
    return $user->is_admin;
}

public function restore(User $user, Model $model): bool
{
    return $user->is_admin;
}

public function forceDelete(User $user, Model $model): bool
{
    return $user->is_admin;
}
```

**Expense Policy** (`app/Policies/ExpensePolicy.php`):

```php
// Override: All authenticated users can create expenses
public function create(User $user): bool
{
    return true;
}

// Override: All authenticated users can delete their own expenses
public function delete(User $user, $expense): bool
{
    return true;
}

// Only admins can view insights
public function viewInsights(User $user): bool
{
    return $user->is_admin;
}
```

**Testing Authorization:**

```bash
# Login as Admin
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@email.com",
    "password": "password"
  }'

# Login as Staff
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "staff@email.com",
    "password": "password"
  }'
```

## Project Structure

```
mastercode/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/          # API Controllers
│   │   ├── Resources/        # JSON Resources for API responses
│   │   └── Requests/         # Form Request Validation
│   ├── Models/               # Eloquent Models
│   │   ├── Category.php
│   │   ├── Expense.php
│   │   ├── User.php
│   │   ├── Vendor.php
│   │   └── Scopes/           # Global Query Scopes
│   ├── Policies/             # Authorization Policies
│   ├── Repositories/         # Data Access Layer
│   │   └── ExpenseRepository.php
│   └── Services/             # Business Logic Layer
│       └── ExpenseService.php
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # Console commands
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
└── tests/                   # Unit and Feature tests
```

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

This API uses **Laravel Sanctum** for authentication. After logging in or registering, you'll receive a token that must be included in subsequent requests.

#### Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|abc123..."
}
```

#### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**

```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "2|xyz789..."
}
```

#### Logout

```http
POST /api/logout
Authorization: Bearer {your-token}
```

### Protected Routes

All routes below require authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

### Categories API

#### List All Categories

```http
GET /api/categories
Authorization: Bearer {your-token}
```

#### Get Single Category

```http
GET /api/categories/{id}
Authorization: Bearer {your-token}
```

#### Create Category

```http
POST /api/categories
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "name": "Food & Dining",
  "description": "Restaurant and grocery expenses"
}
```

#### Update Category

```http
PUT /api/categories/{id}
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "name": "Food & Beverages",
  "description": "Updated description"
}
```

#### Delete Category (Soft Delete)

```http
DELETE /api/categories/{id}
Authorization: Bearer {your-token}
```

#### List Trashed Categories

```http
GET /api/categories/trashed
Authorization: Bearer {your-token}
```

#### Restore Category

```http
POST /api/categories/{id}/restore
Authorization: Bearer {your-token}
```

#### Permanently Delete Category

```http
DELETE /api/categories/{id}/force-delete
Authorization: Bearer {your-token}
```

### Vendors API

The Vendors API follows the same pattern as Categories:

-   `GET /api/vendors` - List all vendors
-   `POST /api/vendors` - Create vendor
-   `GET /api/vendors/{id}` - Get single vendor
-   `PUT /api/vendors/{id}` - Update vendor
-   `DELETE /api/vendors/{id}` - Soft delete vendor
-   `GET /api/vendors/trashed` - List trashed vendors
-   `POST /api/vendors/{id}/restore` - Restore vendor
-   `DELETE /api/vendors/{id}/force-delete` - Permanently delete

### Expenses API

#### List All Expenses

```http
GET /api/expenses
Authorization: Bearer {your-token}
```

#### Get Filtered Expenses

You can filter expenses using query parameters:

```http
GET /api/expenses?category=Food&vendor=Restaurant&from=2025-01-01&to=2025-12-31
Authorization: Bearer {your-token}
```

**Query Parameters:**

-   `category` - Filter by category name (partial match)
-   `vendor` - Filter by vendor name (partial match)
-   `from` - Filter expenses from this date (YYYY-MM-DD)
-   `to` - Filter expenses until this date (YYYY-MM-DD)

#### Get Single Expense

```http
GET /api/expenses/{id}
Authorization: Bearer {your-token}
```

#### Create Expense

```http
POST /api/expenses
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "category_id": 1,
  "vendor_id": 2,
  "amount": 125.50,
  "date": "2025-12-29",
  "description": "Team lunch at downtown restaurant"
}
```

#### Update Expense

```http
PUT /api/expenses/{id}
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "amount": 135.75,
  "description": "Updated description"
}
```

#### Delete Expense (Soft Delete)

```http
DELETE /api/expenses/{id}
Authorization: Bearer {your-token}
```

#### List Trashed Expenses

```http
GET /api/expenses/trashed
Authorization: Bearer {your-token}
```

#### Restore Expense

```http
POST /api/expenses/{id}/restore
Authorization: Bearer {your-token}
```

#### Permanently Delete Expense

```http
DELETE /api/expenses/{id}/force-delete
Authorization: Bearer {your-token}
```

### Insights API

#### Get Expense Insights

Retrieves comprehensive insights and statistics about expenses.

```http
GET /api/expenses/insights
Authorization: Bearer {your-token}
```

**Standard Response (General Overview):**
Returns overview, monthly, quarterly, and yearly statistics for the current date contexts.

```json
{
    "insights": {
        "overview": {
            "total_expenses": 1250.00,
            "count_of_categories": 5,
            "count_of_vendors": 8,
            "average_of_expenses": 156.25,
            "max_of_expenses": 500.00,
            "min_of_expenses": 25.00
        },
        "monthlyOverview": { ... },
        "quarterlyOverview": { ... },
        "yearlyOverview": { ... }
    }
}
```

**Filtered Response (By Category & Date):**
Pass `category` query parameter to get insights for a specific category. You can also optionally specify `from` and `to` dates.

```http
GET /api/expenses/insights?category=Food&from=2025-01-01&to=2025-01-31
Authorization: Bearer {your-token}
```

**Response:**

```json
{
    "insights": {
        "total_expenses": 450.0,
        "average_of_expenses": 45.0,
        "max_of_expenses": 120.0,
        "min_of_expenses": 15.0
    }
}
```

### Users API

```http
GET /api/users          # List all users
POST /api/users         # Create user
GET /api/users/{id}     # Get single user
PUT /api/users/{id}     # Update user
DELETE /api/users/{id}  # Delete user
```

## Code Architecture

This project follows clean architecture principles with clear separation of concerns.

### Repository Pattern

Repositories handle all database interactions. Example from `ExpenseRepository.php`:

```php
namespace App\Repositories;

use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class ExpenseRepository
{
    // Create a new expense
    public function create(array $data)
    {
        return Expense::create($data);
    }

    // Update an existing expense
    public function update(Expense $expense, array $data)
    {
        return $expense->update($data);
    }

    // Get all expenses with relationships
    public function getFiltered()
    {
        return ExpenseResource::collection(
            Expense::filter()
                ->with(['category', 'vendor'])
                ->get()
        );
    }

    // Soft delete
    public function delete(Expense $expense)
    {
        return $expense->delete();
    }

    // Permanent delete
    public function forceDelete(Expense $expense)
    {
        return $expense->forceDelete();
    }
}
```

### Service Layer

Services contain business logic and authorization. Example from `ExpenseService.php`:

```php
namespace App\Services;

use App\Repositories\ExpenseRepository;
use App\Models\Expense;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {}

    public function create(array $data)
    {
        // Check authorization
        $this->authorize('create', Expense::class);

        // Delegate to repository
        return $this->expenseRepository->create($data);
    }

    public function update(Expense $expense, array $data)
    {
        $this->authorize('update', $expense);
        return $this->expenseRepository->update($expense, $data);
    }

    public function getFiltered()
    {
        return $this->expenseRepository->getFiltered();
    }
}
```

### Model Scopes

Models include custom query scopes for filtering. Example from `Expense.php`:

```php
namespace App\Models;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'vendor_id',
        'amount',
        'date',
        'description',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // Custom filter scope
    public function scopeFilter($query)
    {
        if (request()->has('category')) {
            $query->whereHas('category', function ($q) {
                $q->where('name', 'like', '%' . request('category') . '%');
            });
        }

        if (request()->has('vendor')) {
            $query->whereHas('vendor', function ($q) {
                $q->where('name', 'like', '%' . request('vendor') . '%');
            });
        }

        if (request()->has('from')) {
            $query->where('date', '>=', request('from'));
        }

        if (request()->has('to')) {
            $query->where('date', '<=', request('to'));
        }

        return $query;
    }
}
```

### Data Insights Helper

Complex statistical calculations are offloaded to `App\Helpers\Insights`. This helper class provides static methods to aggregate data reliably (sums, averages, counts, min/max) and handles logic for different time periods (monthly, quarterly, yearly). This keeps the Controller and Service layers clean and focused on request handling and business logic respectively.

### Global Scopes

The application uses **Global Scopes** to automatically filter queries based on user roles. Global scopes are applied automatically to all queries for specific models without explicitly calling them.

#### Why Use Global Scopes?

Global scopes provide:

-   **Automatic Data Filtering**: Queries are filtered without manual intervention
-   **Role-Based Data Access**: Staff users see only active records, admins see everything
-   **Security**: Prevents unauthorized access to inactive or sensitive data
-   **Cleaner Code**: No need to repeatedly add `where('is_active', true)` in every query

#### CategoryScope

**Location**: `app/Models/Scopes/CategoryScope.php`

**Purpose**: Restricts staff users to view only active categories. Admins can see all categories including inactive ones.

```php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CategoryScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scope for admin users
        if (auth()->user()?->is_admin) {
            return;
        }

        // Staff users only see active categories
        $builder->where('is_active', true);
    }
}
```

**Applied to**: `Category` model using `#[ScopedBy(CategoryScope::class)]` attribute

**Effect**:

-   **Admin users**: See all categories (active and inactive)
-   **Staff users**: See only categories where `is_active = true`

#### VendorScope

**Location**: `app/Models/Scopes/VendorScope.php`

**Purpose**: Restricts staff users to view only active vendors. Admins can see all vendors.

```php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VendorScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scope for admin users
        if (auth()->user()?->is_admin) {
            return;
        }

        // Staff users only see active vendors
        $builder->where('is_active', true);
    }
}
```

**Applied to**: `Vendor` model using `#[ScopedBy(VendorScope::class)]` attribute

**Effect**:

-   **Admin users**: See all vendors (active and inactive)
-   **Staff users**: See only vendors where `is_active = true`

#### ExpenseScope

**Location**: `app/Models/Scopes/ExpenseScope.php`

**Purpose**: Restricts staff users to view only valid expenses (positive amounts with active categories). Admins can see all expenses.

```php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExpenseScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scope for admin users
        if (auth()->user()?->is_admin) {
            return;
        }

        // Staff users only see expenses with:
        // 1. Active categories
        // 2. Positive amounts
        $builder->whereHas('category', function (Builder $builder) {
            $builder->where('is_active', true);
        })->where('amount', '>', 0);
    }
}
```

**Applied to**: `Expense` model using `#[ScopedBy(ExpenseScope::class)]` attribute

**Effect**:

-   **Admin users**: See all expenses (any amount, any category status)
-   **Staff users**: See only expenses where:
    -   The associated category has `is_active = true`
    -   The expense amount is greater than 0

#### How Global Scopes Work in Practice

**Example 1: Querying Categories**

```php
// As Staff User (is_admin = false)
$categories = Category::all();
// SQL: SELECT * FROM categories WHERE is_active = 1

// As Admin User (is_admin = true)
$categories = Category::all();
// SQL: SELECT * FROM categories
```

**Example 2: Querying Expenses**

```php
// As Staff User
$expenses = Expense::all();
// SQL: SELECT * FROM expenses
//      WHERE amount > 0
//      AND EXISTS (
//          SELECT * FROM categories
//          WHERE categories.id = expenses.category_id
//          AND is_active = 1
//      )

// As Admin User
$expenses = Expense::all();
// SQL: SELECT * FROM expenses
```

**Example 3: Bypassing Global Scopes (Admin Only)**

If you need to bypass a global scope explicitly:

```php
// Get all categories including inactive ones
$allCategories = Category::withoutGlobalScopes()->get();

// Get all expenses including those with negative amounts
$allExpenses = Expense::withoutGlobalScope(ExpenseScope::class)->get();
```

#### Model Implementation

Models use the `#[ScopedBy()]` attribute to apply global scopes:

```php
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\CategoryScope;

#[ScopedBy(CategoryScope::class)]
class Category extends Model
{
    protected $fillable = ['name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
```

#### Benefits in This Application

1. **Data Integrity**: Staff users can't accidentally create expenses linked to inactive categories
2. **Simplified Queries**: No need to manually filter by `is_active` in controllers or repositories
3. **Centralized Logic**: Filtering rules are in one place, easy to maintain
4. **Security**: Automatic protection against accessing inactive/invalid records
5. **Flexibility**: Admins can still access all data when needed for reporting or maintenance

### Using the Architecture in Your Code

#### 1. Create a New Repository

```php
namespace App\Repositories;

use App\Models\YourModel;

class YourModelRepository
{
    public function create(array $data)
    {
        return YourModel::create($data);
    }

    public function getAll()
    {
        return YourModel::all();
    }
}
```

#### 2. Create a Service

```php
namespace App\Services;

use App\Repositories\YourModelRepository;

class YourModelService
{
    public function __construct(
        protected YourModelRepository $repository
    ) {}

    public function create(array $data)
    {
        // Add business logic here
        return $this->repository->create($data);
    }
}
```

#### 3. Use in Controller

```php
namespace App\Http\Controllers\Api;

use App\Services\YourModelService;

class YourModelController extends Controller
{
    public function __construct(
        protected YourModelService $service
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|string',
        ]);

        $model = $this->service->create($validated);

        return response()->json($model, 201);
    }
}
```

## Usage Examples

### Example 1: Complete Expense Workflow

```bash
# 1. Register a new user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Save the token from response
TOKEN="your-token-here"

# 2. Create a category
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Office Supplies",
    "description": "Pens, paper, and equipment"
  }'

# 3. Create a vendor
curl -X POST http://localhost:8000/api/vendors \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Staples",
    "email": "orders@staples.com"
  }'

# 4. Create an expense
curl -X POST http://localhost:8000/api/expenses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "vendor_id": 1,
    "amount": 45.99,
    "date": "2025-12-29",
    "description": "Office supplies for Q1"
  }'

# 5. Filter expenses by date range
curl -X GET "http://localhost:8000/api/expenses?from=2025-01-01&to=2025-12-31" \
  -H "Authorization: Bearer $TOKEN"
```

### Example 2: Using Postman

1. **Set Base URL**: `http://localhost:8000/api`
2. **Register/Login**: Use the authentication endpoints to get a token
3. **Set Authorization**:
    - Go to Authorization tab
    - Type: Bearer Token
    - Token: Paste your token
4. **Make Requests**: Use the endpoints documented above

### Example 3: JavaScript/Axios

```javascript
const axios = require("axios");

const API_URL = "http://localhost:8000/api";
let authToken = "";

// Login
async function login() {
    const response = await axios.post(`${API_URL}/login`, {
        email: "john@example.com",
        password: "password123",
    });

    authToken = response.data.token;
    return response.data;
}

// Create expense
async function createExpense(expenseData) {
    const response = await axios.post(`${API_URL}/expenses`, expenseData, {
        headers: {
            Authorization: `Bearer ${authToken}`,
            "Content-Type": "application/json",
        },
    });

    return response.data;
}

// Get filtered expenses
async function getExpenses(filters = {}) {
    const params = new URLSearchParams(filters);
    const response = await axios.get(`${API_URL}/expenses?${params}`, {
        headers: {
            Authorization: `Bearer ${authToken}`,
        },
    });

    return response.data;
}

// Usage
(async () => {
    await login();

    const expense = await createExpense({
        category_id: 1,
        vendor_id: 1,
        amount: 99.99,
        date: "2025-12-29",
        description: "Team building event",
    });

    console.log("Created expense:", expense);

    const filtered = await getExpenses({
        category: "Office",
        from: "2025-01-01",
        to: "2025-12-31",
    });

    console.log("Filtered expenses:", filtered);
})();
```

## Testing

### Run PHPUnit Tests

This project includes comprehensive feature tests for authorization and CRUD operations.

```bash
# Run all tests
php artisan test
```

#### Running Specific Test Suites

You can run tests for specific features using the `--filter` option:

**Category Tests:**

```bash
# Run all category tests
php artisan test --filter=Category

# Run specific suite
php artisan test tests/Feature/Categories/CategoryAuthorizationTest.php
php artisan test tests/Feature/Categories/CategoryCrudTest.php
```

**Vendor Tests:**

```bash
# Run all vendor tests
php artisan test --filter=Vendor

# Run specific suite
php artisan test tests/Feature/Vendors/VendorAuthorizationTest.php
php artisan test tests/Feature/Vendors/VendorCrudTest.php
```

**Expense Tests:**

```bash
# Run all expense tests
php artisan test --filter=Expense

# Run specific suite
php artisan test tests/Feature/Expenses/ExpenseAuthorizationTest.php
php artisan test tests/Feature/Expenses/ExpenseCrudTest.php
```

### Test Coverage

The test suite covers:

-   **Authorization**: Verifies that Staff users cannot perform Admin-only actions (creating/updating/deleting categories & vendors, accessing trashed items).
-   **CRUD Operations**: Verifies that Admins can successfully create, read, update, delete, restore, and force-delete resources.
-   **Global Scopes**: Ensures data visibility rules are correctly applied (Staff see only active items).
-   **Validation**: Checks API response codes (201 Created, 403 Forbidden, 422 Unprocessable Entity).

### Manual Testing with Artisan Tinker

```bash
php artisan tinker
```

```php
// Create a test user
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);

// Create a category
$category = Category::create([
    'name' => 'Testing',
    'description' => 'Test category'
]);

// Create an expense
$expense = Expense::create([
    'category_id' => $category->id,
    'amount' => 50.00,
    'date' => now(),
    'description' => 'Test expense'
]);

// Test filters
$filtered = Expense::filter()->get();
```

## Deployment

### Production Checklist

1. **Environment Configuration**

```bash
# Set environment to production
APP_ENV=production
APP_DEBUG=false

# Use a strong app key
php artisan key:generate

# Configure production database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
```

2. **Optimize for Production**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

3. **Build Assets**

```bash
npm run build
```

4. **Set Permissions**

```bash
chmod -R 755 storage bootstrap/cache
```

5. **Run Migrations**

```bash
php artisan migrate --force
```

### Deployment to Shared Hosting

1. Upload files to `public_html` or `www` directory
2. Move contents of `public/` to root web directory
3. Update `index.php` to point to correct paths
4. Configure `.htaccess` for URL rewriting
5. Set environment variables via hosting control panel

### Deployment to VPS (Ubuntu/Nginx)

```bash
# Install dependencies
sudo apt update
sudo apt install php8.2-fpm nginx mysql-server

# Configure Nginx
sudo nano /etc/nginx/sites-available/your-domain

# Enable site
sudo ln -s /etc/nginx/sites-available/your-domain /etc/nginx/sites-enabled/

# Restart Nginx
sudo systemctl restart nginx

# Set permissions
sudo chown -R www-data:www-data /var/www/your-app
```

## Troubleshooting

### Common Issues

**Issue: "Database file not found"**

```bash
# Create SQLite database
touch database/database.sqlite
php artisan migrate
```

**Issue: "Permission denied on storage"**

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**Issue: "Token mismatch"**

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions:

-   Create an issue in the repository
-   Check existing documentation
-   Review Laravel documentation at [laravel.com](https://laravel.com/docs)

---

**Made with ❤️ using Laravel 11**
