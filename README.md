# Driving School Management System

## Overview

This project entails a comprehensive system for managing a driving school, facilitating the organization of candidate information, course registrations, and scheduling. The system is split into a backend powered by Symfony using API Platform and a frontend developed with HTML, CSS, and JavaScript.

## Technologies

- Symfony 6.2
- PHP 8.1
- Composer
- PostgreSQL
- HTML / CSS / JavaScript
- Make (Makefile)

## Getting Started

1. **Database Setup:**
   - Create a PostgreSQL user.
   - Establish a database named **bd_autoecole** with the user as the owner.
   - Create a **.env.local** file in **./Api/** and add the database connection URL.

2. **Build Database:**
   ```sh
   # Create tables and insert initial data into the database
   make build
   ```

3. **Start Servers:**
   ```sh
   # Start local API servers
   make api-start
   # Start the website
   make all
   ```

   _Note: In case of issues, run `make update`._

---

## Installation Details for the API (General)

1. **Navigate to API folder.**

2. **Symfony Installation:**
   - Download and install Symfony. Follow the instructions on their [website](https://symfony.com/).

3. **Database Configuration:**
   - Configure the PostgreSQL database connection in the .env file.

4. **Generate Migrations:**
   ```sh
   php bin/console make:migration
   ```

5. **Execute Migrations:**
   ```sh
   php bin/console doctrine:migrations:migrate
   ```

6. **Load Fixtures:**
   ```sh
   php bin/console doctrine:fixtures:load
   ```

7. **Start Server:**
   ```sh
   symfony server:start
   ```
   or
   ```sh
   php bin/console server:start
   ```

8. **Access API:**
   - [http://localhost:8000/api](http://localhost:8000/api)

## Installation Details for the Web Page (General)

1. **Start Server:**
   ```sh
   php -S localhost:3000
   ```

2. **Access Website:**
   - [http://localhost:3000](http://localhost:3000)