# Mini CRM
This is a take home test of a rest API

# Requirements
- Docker
- VSCode
- VsCode Docker and DevContainer extentions

## Installation
To install the project, clone the repository and open it in VsCode, it will ask you to reopen in the container.

## Usage
Once started, you can run migrations using the command `php artisan migrate`.

For fake data, you can use the built in command `php artisan import --file=data.csv`
The first time it runs, the command will fail but will generate a file inside `storage/private/files/data.csv`, you can run it again to import that data.

### API Docs
The API comes with built in Open API documentation, you can go to `localhost/api/documentation`

# Architecture
Read about Architecture design in [Architecture](ARCHITECTURE.md)