# Project Management System

A project management app I built with Laravel and Filament. It helps teams collaborate on projects, manage tasks, track progress, and communicate through comments. I implemented role-based permissions that adjust based on user experience levels.

**Repository**: [GitLab](https://git.brainster.co/Sandra.Ilievska-FS21/project-manager) | [GitHub](https://github.com/sandrailievskaa/project-manager)

## About This Project

I built the **backend** completely from scratch—all the Laravel code, Filament admin panel, policies, observers, and API structure. The business logic, permission rules, and authorization policies were all implemented manually. This was my main focus initially since I wanted to make sure the core functionality was solid.

For the **frontend/UI**, I used Vue 3, Inertia.js, and Tailwind CSS. I worked with AI tools (Copilot, Cursor, ChatGPT) to polish the visuals and interactions, mainly due to time constraints. I'm genuinely interested in frontend development and wanted to improve the user experience and visual clarity, so I intentionally focused on enhancing the interface after getting the backend working well.

## Features

I implemented user management with registration, admin approval, role-based access (Admin/User), and experience levels (Junior/Middle/Senior). Users have profiles showing their experience and project assignments.

For project management, you can create projects with descriptions, requirements, deadlines, and time estimates. I set up team member assignments and project progress tracking.

Task management lets you create and assign tasks, track status (To Do → In Progress → QA → Done). I made updates permission-based depending on user experience and project assignment. The TaskObserver I created automatically generates comments when status changes happen.

Comments can be added to tasks for collaboration. When status changes occur, the system automatically generates comments with a history of what changed.

I built personal dashboards showing project stats, task counts by status, and recent activity. Administrators get access to the Filament admin panel.

## Role-Based Permissions

I implemented permissions using Laravel Policies that depend on both user roles and experience levels:

- **Admin**: Full access through the Filament admin panel
- **Team Lead** (project owner): Can create tasks, assign team members, and update any task in their projects
- **Senior**: Can update task statuses for tasks in projects they're assigned to
- **Middle**: Can update statuses for tasks assigned to them or tasks assigned to Junior developers
- **Junior**: Can only update statuses for tasks assigned to themselves

## Tech Stack

**Backend**: Laravel 12, Filament 3, Laravel Fortify, Inertia.js (Laravel), Laravel Wayfinder  
**Frontend**: Vue 3, Inertia.js (Vue), Tailwind CSS 4, Reka UI, TypeScript  
**Development**: Pest, Laravel Pint, Laravel Sail, Vite

## Installation and Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL, PostgreSQL, or SQLite

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd project-manager-v2
```

### Step 2: Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

### Step 3: Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Edit the `.env` file and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Run Migrations

Create the database tables:

```bash
php artisan migrate
```

### Step 5: Build Frontend Assets

For development, start the Vite dev server:

```bash
npm run dev
```

For production, build the assets:

```bash
npm run build
```

### Step 6: Start the Application

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

### Default Admin Account

After running migrations, a default admin account is created:
- **Email**: admin@gmail.com
- **Password**: admin

**Important**: Change the admin password immediately after first login in a production environment.

## Usage

**Administrators**: Log in with admin credentials, access the Filament admin panel at `/admin`, manage users/projects/tasks, approve registrations, view system statistics.

**Team Members**: Register or log in, wait for admin approval (if required), access your dashboard, view assigned projects, manage tasks, add comments, update task statuses based on permissions.

**Creating Projects**: Team leads create projects through the admin panel or frontend, fill in details (title, description, requirements, deadline, time estimate), assign team members, then create tasks.

**Managing Tasks**: Team leads create tasks within their projects and assign them. Team members update task status as work progresses, add comments, and the system automatically logs status changes.

## Implementation Highlights

**Laravel Policies**: I handled authorization through ProjectPolicy, TaskPolicy, and CommentPolicy. Users can only perform actions they're authorized for based on their role and experience level.

**TaskObserver**: I created an observer that automatically generates comments when task statuses change. This provides an audit trail without requiring manual documentation. When a status changes, the observer creates a comment with the old and new status values, attributed to the user who made the change.

**Filament Admin Panel**: I built a full admin interface with User, Project, and Task resources, dashboard widgets, and relation managers for managing tasks within projects and users within projects. Only users with the Admin role can access it.


