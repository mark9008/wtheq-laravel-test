# wtheq-laravel-test

## Introduction
This project is a Laravel-based API that provides user registration, login, and CRUD operations for managing users and products. It also implements custom pricing logic based on user types.

## Features
- User registration and login
- CRUD operations for Users and Products
- Custom pricing based on user type

## Requirements
- PHP 8.2+
- Composer
- Laravel
- Git
- sqlite3

## Installation
1. Clone the repository:
    ```shell
   git clone https://github.com/mark9008/wtheq-laravel-test.git
    ```
2. Navigate to the project directory:
    ```shell
   cd wtheq-laravel-test
   ``` 
3. Install dependencies:
    ```shell
    composer install
    ```
4. Create a copy of the .env.example file and rename it to .env:
    ```shell
    cp .env.example .env
    ```
5. Run migrations:
    ```shell
    php artisan migrate
    ```
6. **Optional**: Seed the database if you want to initiate it with 10 users and 10 products:
    ```shell
    php artisan db:seed
    ```

# Usage

## Authentication

### User Registration

- Endpoint: `/api/auth/register`

- Example Request:
 ```http request
    POST /api/auth/register
    Content-Type: application/json
    accept: application/json
    
    {
        "name":"Mark",
        "email":"mark@example.com",
        "password":"password",
        "password_confirmation":"password",
        "is_active":"false",
        "type":"normal"
    }
```
### User Login

- Endpoint: `/api/auth/login`
- Example Request:
 ```http request
    POST /api/auth/login
    Content-Type: application/json
    accept: application/json
    
    {
        "email": "mark@example.com",
        "password": "password"
    }
```

### User Logout

- Endpoint: `/api/auth/logout`
- Example Request:
 ```http request
    POST /api/auth/logout
    accept: application/json
```

### Refresh Token

- Endpoint: `/api/auth/refresh-token`
- Example Request:
 ```http request
    POST /api/auth/refresh-token
    accept: application/json
```

## Users

### Get All Users

- Endpoint: `/api/profile/list`
- Query Parameters: `active_only` (optional, boolean): Filter the list to include only active user profiles. Set to `0` to include both active and inactive profiles. Set to `1` to include only active profiles. If it is not provided the response will only contain active profiles.
- Example Request:
 ```http request
    GET /api/profile/list?active_only=0
    accept: application/json
```

### Get User Profile

- Endpoint: `/api/profile/user`
- Example Request:
 ```http request
    GET /api/profile/user
    accept: application/json
```

### Get User Profiles by Type

- Endpoint: `/api/profile/{type}/list`
- Example Request:
 ```http request
    GET /api/profile/normal/list
    accept: application/json
```

### Update User Profile

- Endpoint: `/api/profile/update`
- Example Request:
 ```http request
    POST /api/profile/update
    Content-Type: multipart/form-data
    accept: application/json
    
    {
        "name":"new name",
        "type":"", // normal, gold, silver
        "avatar":"image.jpg" // file
        }
 ```

### Delete User Profile

- Endpoint: `/api/profile/{id}/delete`
- Example Request:
 ```http request
    DELETE /api/profile/1/delete
    accept: application/json
```

## Products

### Get All Products

- Endpoint: `/api/products/list`
- Query Parameters: `active_only` (optional, boolean): Filter the list to include only active products. Set to `0` to include both active and inactive products. Set to `1` to include only active products. If it is not provided the response will only contain active products.
- Example Request:
 ```http request
    GET /api/products/list?active_only=0
    accept: application/json
```

### Get Product

- Endpoint: `/api/products/{id}/get`
- Example Request:
 ```http request
    GET /api/products/1/get
    accept: application/json
```

### Get Products by IDs

- Endpoint: `/api/products/search`
- Query Parameters: `ids` (required, array): An array of product IDs.
- Example Request:
 ```http request
    GET /api/products/search?ids=1,2,3
    accept: application/json
```

### Create Product

- Endpoint: `/api/products/create`
- Example Request:
 ```http request
    POST /api/products/create
    Content-Type: multipart/form-data
    accept: application/json
    
    {
        "name":"Product Name",
        "description":"Product Description",
        "image": "image.jpg", // file
        "price":"100",
        "is_active":0
    }
```

### Update Product

- Endpoint: `/api/products/{id}/update`
- Example Request:
 ```http request
    POST /api/products/1/update
    Content-Type: multipart/form-data
    accept: application/json
    
    {
        "name":"Product Name",
        "description":"Product Description",
        "image": "image.jpg", // file
        "price":"100",
        "is_active":0
    }
```

### Delete Product

- Endpoint: `/api/products/{id}/delete`
- Example Request:
 ```http request
    DELETE /api/products/1/delete
    accept: application/json
```


## Pricing Logic

- Users are classified into "normal," "gold," or "silver."
- Different user types receive different discounts on product prices.
- normal users receive no discount.
- silver users receive a 5% discount.
- gold users receive a 10% discount.

## Repository Pattern

I have implemented the repository design pattern in this project to separate data access from business logic.

## Custom Pricing Middleware

I have implemented a custom middleware to apply the pricing logic to the product prices. It is registered in the `Kernel.php` file in the `app/Http` directory. It edits the response returned by the API to apply the pricing logic to the product prices based on the user type.

## Authors

- Mark Samuel Shawki
- Email: mark.samuel.shawki@gmail.com

## References

- [Laravel Documentation](https://laravel.com/docs)
- [Composer Documentation](https://getcomposer.org/doc/)
