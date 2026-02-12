# FundTastic

FundTastic is a modern web-based crowdfunding platform designed to bridge the gap between project creators and potential backers. The platform enables collaborative funding for diverse projects and causes, serving individuals and organizations seeking financial support for their initiatives.

## Project Overview

Visitors can explore available campaigns immediately, while registration unlocks comprehensive features—including financial contributions, campaign tracking, and community participation. The platform fosters an engaged funding ecosystem where contributors can back causes they believe in, participate in campaign discussions through comments, and monitor projects of interest.

Administrators maintain operational integrity, moderate campaign content, and enforce community guidelines. Project creators gain tools to publish progress updates, manage multimedia presentations, and analyze their campaign performance.

## Key Features

- User Authentication: Secure registration and login for members and administrators.
- Campaign Management: Tools for creators to launch and manage crowdfunding campaigns.
- Financial Contributions: Secure platform for backers to support projects financially.
- Content Moderation: Administrative tools for monitoring and moderating platform content.
- Discovery System: Advanced search and filtering mechanisms for project discovery.
- Responsive Design: Consistent experience across desktop and mobile devices.

## Technology Stack

- Framework: Laravel 12
- Database: PostgreSQL
- Frontend: Blade Templates, Vanilla CSS, Vanilla JavaScript
- Containerization: Docker, Nginx

## Repository Structure

- `app/`: Core application logic (Models, Controllers, Policies, etc.).
- `bootstrap/`: Framework bootstrap files.
- `config/`: Application configuration files.
- `database/`: Database schema, seeders, and migration-style SQL scripts.
    - `sql/`: Raw SQL files for database initialization and population.
- `docker/`: Docker-related configuration files (Nginx, PHP).
- `docs/`: Project documentation and instructions.
- `public/`: Publicly accessible assets (CSS, JS, Images).
- `resources/`: Original assets and Blade templates.
- `routes/`: Application route definitions.
- `scripts/`: Utility scripts for development and deployment.
- `storage/`: Logs, cache, and uploaded files.
- `tests/`: Automated test suites.

## Getting Started

### Prerequisites

- PHP 8.3 or higher
- Composer 2.2 or higher
- Docker Desktop

### Installation

1. Clone the repository.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Set up your environment file:
   ```bash
   cp .env.example .env
   ```
   Note: If `.env.example` is not present, use the project defaults.
4. Start the Docker containers for the database:
   ```bash
   docker compose up -d
   ```
5. Seed the database:
   ```bash
   php artisan db:seed
   ```
6. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage and Credentials

### Development Server
Access the application at `http://localhost:8000`.

### Production Image
To run the production Docker image locally:
```bash
docker run -d --name lbaw2532 -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2526/lbaw2532
```
Access at `http://localhost:8001`.

### Default Accounts

#### Administrative Account
- Email: `admin1@example.com`
- Password: `1234`

#### Standard User Account
- Email: `alice@example.com`
- Password: `1234`

## Automated Testing

Run the test suite using:
```bash
php artisan test
```

## Credits

This project was developed for the **Laboratório de Bases de Dados e Aplicações Web (LBAW)** course at the Faculty of Engineering, University of Porto (FEUP).

### Developed by
- Group lbaw2532

### Academic Support
- LBAW Teaching Team, 2025
- FEUP - Faculty of Engineering, University of Porto