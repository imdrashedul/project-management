# Task Management System (Dummy Project)

This project is a demonstration of my skills in Laravel development, built as a dummy Task Management System. It showcases my ability to implement user authentication, REST API development with Laravel Passport, CSV import/export functionality, task and project management, and PDF report generation. Although simplified, this system highlights various Laravel concepts that I’ve applied within a short period of time.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Features](#features)
- [REST API](#rest-api)
- [Usage Instructions](#usage-instructions)
- [Thank You](#thank-you)
- [Further Assistance](#further-assistance)

## Prerequisites

Before you begin, ensure your system has the following:

- PHP >= 8.1
- Composer
- Node.js and npm
- MySQL or another compatible database
- Laravel CLI

## Installation

1. **Clone the repository**:

    ```bash
    git clone https://github.com/imdrashedul/project-management.git
    cd task-management-system
    ```

2. **Install backend dependencies**:

    ```bash
    composer install --dev
    ```

3. **Install frontend dependencies and build assets**:

    ```bash
    npm install
    npm run build
    ```

4. **Set up your environment**: 

    - You can find the `.env` file attached to the email I sent. Download it and place it in the root directory of the project.
    - Update the following database configuration inside the `.env` file:

    ```dotenv
    DB_DATABASE=project_management
    DB_USERNAME=root
    DB_PASSWORD=your_password
    ```

5. **Run database migrations and seed the database**:

    ```bash
    php artisan migrate --seed
    ```

6. **Set up Laravel Passport for API authentication**:

    ```bash
    php artisan passport:client --personal
    ```

7. **Start the Laravel development server**:

    ```bash
    php artisan serve
    ```

8. **Start the queue worker** in a separate terminal:

    ```bash
    php artisan queue:work --daemon --timeout=900 --memory=1024
    ```

9. Access the application at `http://localhost:8000`. You can log in using the default credentials:

    - Email: `user@localhost`
    - Password: `password`

    Or, register a new user account.

## Features

- **User Authentication**: Secure user registration and login.
- **Projects and Tasks Management**: Users can create, update, and delete their own projects and tasks.
- **Subtasks**: Each task can have its own subtasks, enhancing project organization.
- **CSV Import**: Import projects, tasks, and subtasks via a CSV file.
- **PDF Report Generation**: Generate detailed PDF reports for projects and tasks.
- **API Integration**: REST API developed with Laravel Passport for interacting with projects and tasks.

## Usage Instructions

### Importing Projects and Tasks via CSV

1. After logging in, go to "Import From CSV" in the header navigation menu.
2. Download the sample CSV by clicking the **Download CSV Sample** button.
3. Upload the CSV file using the file drop zone and click **Import**.
4. Depending on your system, the import may take 1-2 minutes. You will be notified once the process is complete.

### Creating Projects and Tasks

- Navigate to **Projects > Add** to create a new project.
- Navigate to **Tasks > Add** to create a new task.

### Generating PDF Reports

- On the **Projects List** or **Project Details** page, click the **Download Report** button to generate a PDF report of your projects and tasks.

## REST API

This project includes a fully functional REST API for managing projects, tasks, and subtasks. API documentation is available in the application, accessible from the **API Docs** link in the navigation menu.

### Authentication

To interact with the API, obtain an authentication token via the `/auth/login` or `/oauth/token` endpoint. Include this token as a Bearer token in the Authorization header for subsequent API requests.

For detailed API usage, refer to the [API Docs](http://localhost:8000/api/documentation) provided in the application.

## Thank You

Thank you for reviewing this project, which I’ve created to demonstrate my Laravel development skills. I hope it provides a good insight into my technical abilities.

## Further Assistance

If you need further clarification or assistance, feel free to reach out to me at:

**Email**: route.imdrashedul@gmail.com
