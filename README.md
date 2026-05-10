# AgriFlow - Smart Farming Web Application (Symfony)

## Overview

This project was developed as part of the PIDEV - 3rd Year Engineering Program at Esprit School of Engineering (Academic Year 2025-2026).

AgriFlow is a Symfony web application designed to digitize and optimize agricultural management in Tunisia. It provides smart, accessible digital tools for farmers, experts, and administrators to manage parcels, crops, marketplaces, diagnostics, irrigation plans, and more.

---

## Live Demo

Project website available at:


---

## Features

### CRUD Operations

| Entity | Operations |
|--------|-----------|
| Annonces (Listings) | Create, Read, Update, Delete |
| Reservations | Book, View, Cancel |
| Cultures (Crops) | Create, Read, Update, Delete |
| Parcelles (Parcels) | Create, Read, Update, Delete |
| Utilisateurs (Users) | Create, Read, Update, Delete |
| Produits (Products) | Create, Read, Update, Delete |
| Reclamations | Submit, View, Manage |

### Advanced Business Features

- Groq AI - description enhancement, price suggestion, content moderation
- Anti-fraud detection - automatic suspicious content detection
- PDF contracts - automatic generation with Dompdf / KnpSnappy
- Automatic signing on generated contracts
- AI diagnostics - crop disease detection and recommendations
- Irrigation planning - smart irrigation schedule management
- Collaboration requests - expert-farmer collaboration system
- Marketplace - agricultural product trading platform
- Stripe integration - online payment processing

### User Roles

- Farmers (Agriculteurs): manage profiles, parcels, crops, diagnostics, marketplace
- Administrators: manage users, validate data, monitor the platform
- Experts: provide diagnostics, irrigation planning, collaboration with farmers

---

## Tech Stack

### Frontend

| Technology | Usage |
|-----------|-------|
| Twig | Server-rendered UI templates |
| Symfony UX / Stimulus | Interactive UI behavior |
| Asset Mapper / Importmap | Frontend assets management |
| HTML, CSS, JavaScript | UI and styling |
| Chart.js | Dashboards and charts |

### Backend

| Technology | Usage |
|-----------|-------|
| PHP 8.1+ | Core application language |
| Symfony 6.4 | Web framework |
| Doctrine ORM | Data persistence |
| MySQL | Relational database |
| Dompdf / KnpSnappy | PDF contract generation |
| Groq API | AI-powered features |
| Stripe API | Payment processing |
| PHPUnit | Unit testing |
| PHPStan | Static analysis |

---

## Architecture

The project follows the MVC (Model-View-Controller) pattern:

```
agriflow/
├── assets/              # Frontend assets
├── bin/                 # Console commands
├── config/              # Symfony configuration
├── migrations/          # Doctrine migrations
├── public/              # Public web root
├── src/
│   ├── Controller/      # Web controllers
│   ├── Entity/          # Data models
│   ├── Repository/      # Data access
│   ├── Form/            # Symfony forms
│   ├── Service/         # Business logic
│   ├── Security/        # Auth and access control
│   └── Twig/            # Twig extensions
├── templates/           # Twig views
├── tests/               # PHPUnit tests
└── ...
```

---

## Contributors

| Name | Role |
|------|------|
| Ayoub Maatoug | Team member - TeamSpark |
| Oussama Fattoumi | Team member - TeamSpark |
| Amenallah Jerbi | Team member - TeamSpark |
| Badis Beji | Team member - TeamSpark |
| Yakine Sahli | Team member - TeamSpark |

---

## Academic Context

Developed at Esprit School of Engineering - Tunisia

PIDEV - 3A | 2025-2026

Project developed within the framework of the Professional Integration Project (PIDEV) for 3rd year engineering students at Esprit School of Engineering.

---

## Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- MySQL Server + phpMyAdmin
- Symfony CLI (optional)

### 1. Database Setup

Option A: Import the SQL file

```bash
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import the file: agriflow9.sql
```

Option B: Use Doctrine

```bash
# Configure DATABASE_URL in .env, then:
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Run the Application

```bash
# Option 1: Symfony CLI
symfony serve

# Option 2: PHP built-in server
php -S localhost:8000 -t public
```

### 4. Default Test Account

Simulated user: Amenallah Jerbi (id=39, role=AGRICULTEUR)

---

## Acknowledgments

- Groq API - AI integration for smart agricultural features
- Dompdf / KnpSnappy - PDF generation
- Symfony - Web application framework
- Doctrine - Data persistence
- Esprit School of Engineering - Academic supervision and support
