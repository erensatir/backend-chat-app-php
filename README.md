# Chat Application Backend in PHP

This project is a backend implementation for a chat application using PHP and the Slim framework. Users can create chat groups, join existing groups, and send messages within them. The backend provides a RESTful JSON API for communication over HTTP, with data stored in an SQLite database.

## Features

- **User Management**: Create users identified by unique tokens.
- **Group Management**: Create public chat groups and join any group.
- **Messaging**: Send messages within groups and list all messages in a group.
- **Authentication**: Middleware to authenticate users via tokens.
- **Database**: SQLite for persistent data storage.
- **Routing**: Slim framework for structured API endpoints.
- **Security**: Validations and structured error handling.

## Requirements

- PHP >= 7.4
- Composer
- SQLite

## Installation

### Option 1: Manual Installation

1. **Clone the repository**:
    ```bash
    git clone <repository-url>
    cd <repository-folder>
    ```

2. **Install dependencies**:
    ```bash
    composer install
    ```

3. **Set up the database**:
    - For simplicity and easier testing, I've added the `.env` file to the root directory of the project. The database will be initialized automatically.

4. **Run the application**:
    ```bash
    php -S localhost:8000 -t public
    ```
    > **IMPORTANT**: You first need to create a user from the `POST /users` endpoint after running the application for the first time. From the response of that request, extract the token. This token must be included in the headers of **ALL** subsequent requests as `X-User-Token`.

### Option 2: Using Docker Compose

1. **Clone the repository**:
    ```bash
    git clone <repository-url>
    cd <repository-folder>
    ```

2. **Run the application with Docker**:
    - Ensure Docker and Docker Compose are installed on your machine.
    - Execute the following command:
        ```bash
        docker-compose up
        ```
    This will build and start the application in a containerized environment. The necessary dependencies and the database will be configured automatically.

3. **Access the application**:
    - The application will be available at `http://localhost:8000`.
    - The database will also be pre-configured and ready to use.

    > **IMPORTANT**: As with the manual setup, you must create a user via the `POST /users` endpoint, extract the token, and include it in the headers of all subsequent requests as `X-User-Token`.

## API Endpoints

### Public Endpoints
- **Create User**: `POST /users`

### Protected Endpoints
- **Create Group**: `POST /groups`
- **Join Group**: `POST /groups/{id}/join`
- **List Groups**: `GET /groups`
- **Send Message**: `POST /groups/{id}/messages`
- **List Messages**: `GET /groups/{id}/messages`

### Authentication
Include the `X-User-Token` header in requests to protected endpoints:
```json
{ "X-User-Token": "user-token-string" }
```

## Postman Collection

You can find the Postman collection at the `PostmanCollection` folder located in the root directory of the project. Import this collection into Postman to test the necessary API Endpoints.

> **IMPORTANT**: You first need to create a user from the `POST /users` endpoint. From the response of that request, extract the token. This token must be included in the headers of **ALL** subsequent requests as `X-User-Token`.

## Testing

To run the tests, you have two options:

### Option 1: Using the `run-tests.sh` Script
You can execute the `run-tests.sh` script located in the root directory of the project. This script will:
- Run the tests,
- Generate a detailed test report,
- Automatically open the report in a web browser for easier analysis of the test results.

To use this option, simply run:
```bash
./run-tests.sh
```

### Option 2: Manually run the tests

To run the tests:
1. Install PHPUnit (if it is not installed already):
    ```bash
    composer require --dev phpunit/phpunit
    ```
2. Run tests:
    ```bash
    vendor/bin/phpunit
    ```

## Design Considerations

- **Scalability**: The backend is designed with separate layers for routing, controllers, and models to ensure scalability.
- **Security**: Token-based authentication is used to identify users and validate their actions.
- **Error Handling**: Centralized error handling ensures clear and consistent responses.
