# Jobsphere Application

## Introduction
Jobsphere is a comprehensive Laravel-based application that simplifies the process of job postings and applications, making it easier for employers and job seekers to connect.

## Features
- Realtime mail notifications for job updates and submissions
- Profile management
- User authentication
- Job update views

## Setup Instructions

### Prerequisites
- PHP >= 7.3
- Composer
- Node.js & npm
- MySQL

### Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/Jobsphere.git
    cd Jobsphere
    ```

2. **Install dependencies:**
    ```bash
    composer install
    npm install
    npm run dev
    ```

3. **Copy the `.env` file and set up your environment variables:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Set up the database:**
    - Update your `.env` file with your database credentials.
    - Run the migrations:
        ```bash
        php artisan migrate
        ```

5. **Set up mail configuration:**
    - Update your `.env` file with your mail server credentials.

6. **Set up the queue:**
    - Update your `.env` file to use the desired queue driver.
    - Start the queue worker:
        ```bash
        php artisan queue:work
        ```

7. **Seed the database with a test account:**
    ```bash
    php artisan db:seed
    ```

## Accessing the Application

- **Default Page URL:**
    ```
    http://localhost:8000
    ```

## License
This project is licensed under the MIT License.

