# Mini CRM - Architecture Document

## Overview
Mini CRM is a backend API built with Laravel 12 that simulates a basic module for storing and managing contact information. The system provides CRUD operations for companies, contacts, and deals, forming the core functionality of a minimal Customer Relationship Management system.

## Technology Stack

### Backend Framework
- **Laravel 12**: Selected for its robust features and the developer's familiarity with the framework
- **PHP 8.x**: The underlying programming language

### Database
- **MySQL 8**: Primary data storage
  - Default database for application data
  - Separate `testing` database for running PestPHP tests
  - Initialization scripts located in `.devcontainer/mysql-init/`

### Authentication & Authorization
- **Laravel Sanctum**: Provides API token authentication
  - Used for securing API endpoints
  - Enables API key-based authorization for all API calls

## System Components

### Core Modules
1. **Companies Module**
   - Manages company information
   - Provides CRUD operations for company entities
   - Serves as a parent entity for contacts

2. **Contacts Module**
   - Manages individual contact information
   - Provides CRUD operations for contact entities
   - Associates contacts with companies

3. **Deals Module**
   - Manages deal/opportunity information
   - Provides CRUD operations for deal entities
   - Can be associated with companies and/or contacts

### Service Layer
- Custom services implemented to:
  - Avoid code duplication
  - Handle filtering and search functionality
  - Keep controllers lean and focused on request/response handling

## Data Management
- All data is persisted in MySQL database
- Each resource (Company, Contact, Deal) has its own:
  - Database table
  - Model
  - Controller with CRUD operations
  - Associated routes

## Development Environment
- **VS Code Devcontainer**: Project comes pre-configured with a development container
  - Allows for consistent development environment
  - Easy setup with Docker Compose
  - Includes all necessary dependencies

## Testing
- **PestPHP**: Used for testing the application
- Configured to use a separate `testing` database
- `phpunit.xml` is pre-configured for the testing environment

## Data Import Functionality
- Custom Artisan command for importing data: `php artisan import --file=data.csv`
- Generates fake data as an alternative to using database seeders

## Security Considerations
- API endpoints protected with Laravel Sanctum
- Authentication required for accessing protected resources
- Basic authorization mechanisms in place

## Current Limitations
- No caching mechanism implemented yet
- Limited scalability in the current implementation
- Basic authentication without advanced user roles or permissions

## Deployment
Currently, the project is designed for local development using the provided VS Code Devcontainer configuration. No specific production deployment strategy has been implemented.