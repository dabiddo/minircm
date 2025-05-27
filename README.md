
# Mini CRM

  

Mini CRM is a Laravel 12 REST API that simulates a basic module for storing and managing contact information. It provides CRUD operations for companies, contacts, and deals.

  

## Requirements

- Docker

- VSCode

- VSCode Docker and DevContainer extensions

  

## Installation

  

1. Clone the repository:

```bash
git clone <repository-url>
```

2. Once the container is built and running, copy the environment file:

  

```bash

cp .env.example .env

```

3. Make a partial copy of the DB settings and store them in the `.devcontainer/.env`
  

4. Open the project in VSCode. You will be prompted to reopen in the container.


5. Install dependencies and generate application key:

  

```bash

composer install && php artisan key:generate

```

  

6. Run database migrations:

```bash

php artisan migrate

```

  

## Data Import

For generating fake data, you can use the built-in command:

  

```bash

php artisan import --file=data.csv

````

Note: The first  time you run this command, it will fail but will generate a file inside storage/private/files/data.csv. You can then run the command again to import that data.

  

## API Usage

  

All API endpoints are versioned and accessible under the /api/v1/ prefix. The API uses Laravel Sanctum for  authentication.

  

Main Endpoints

Companies: /api/v1/companies

Contacts: /api/v1/contacts

Deals: /api/v1/deals

  

Each resource supports standard CRUD operations:

  

GET (list and detail)

POST (create)

PUT/PATCH (update)

DELETE (delete)

  
  

## API Documentation

  

The API comes with built-in OpenAPI documentation. Once the application  is running, you can access it at:

  

http://localhost/api/documentation

  

## Testing

The project is configured with PestPHP for testing. To run tests:

  

Create a testing environment file:

```shell

cp .env .env.testing

```

Update the database configuration in .env.testing:

```

DB_CONNECTION=mysql

DB_HOST=mysql #docker-compose name

DB_PORT=3306

DB_DATABASE=testing #keep this name

DB_USERNAME=root

DB_PASSWORD=

```

Run the tests:

```shell

php artisan test

```

The `phpunit.xml` file is pre-configured for the testing environment.

  

## Architecture

For detailed information about the project's architecture and design decisions, please refer to the [Architecture Document](ARCHITECTURE.md).

  

## Development Environment

This project comes pre-configured as a VSCode DevContainer, providing a consistent development environment with all necessary dependencies. The container includes:

  

PHP 8.x

Laravel 12

MySQL 8

Composer

Other required extensions and tools

Once the container is running, you can start developing without additional setup.

## Files
You can find a copy of the Postman / Bruno JSON files inside the `files/` directory to manually test the API althoug using the Open API address is recommended.
