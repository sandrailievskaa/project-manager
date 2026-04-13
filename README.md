# Project Manager

A web app for managing projects and tasks with role- and experience-based permissions. Admins use Filament; team members work in a Vue/Inertia UI.

**Repos:** [GitLab](https://git.brainster.co/Sandra.Ilievska-FS21/project-manager) · [GitHub](https://github.com/sandrailievskaa/project-manager)

## Tech stack

- **Backend:** Laravel 12, Filament 3, Laravel Fortify, Inertia (Laravel), Wayfinder
- **Frontend:** Vue 3, Inertia, TypeScript, Tailwind CSS 4, Reka UI, Vite
- **Quality / dev:** Pest, Laravel Pint, Laravel Sail (optional)

## Key features

- Projects with descriptions, requirements, deadlines, estimates, and team assignments
- Tasks with status workflow (To Do → In Progress → QA → Done), assignments, and comments
- User registration with admin approval; **Admin** vs **User** roles; experience levels (Junior / Middle / Senior)
- Dashboards with project and task stats; Filament admin at `/admin` for administrators
- Permissions that combine role, experience, and project membership (e.g. team lead vs senior vs junior)

## Highlights

- **Policies:** `ProjectPolicy`, `TaskPolicy`, and `CommentPolicy` enforce who can view or change what
- **TaskObserver:** records automatic comments when task status changes (light audit trail)
- **Filament:** CRUD for users, projects, and tasks, plus dashboards and relation managers

## Setup

Prerequisites: PHP 8.2+, Composer, Node.js, and a database (MySQL, PostgreSQL, or SQLite).

```bash
git clone <repo-url> && cd project-manager-v2
cp .env.example .env && php artisan key:generate
composer install && npm install
# Configure DB_* in .env, then:
php artisan migrate
php artisan db:seed   # optional: creates admin@gmail.com / password: admin — change in production
npm run dev           # Vite (separate terminal)
php artisan serve     # app at http://localhost:8000
```

For a one-shot install (includes asset build), you can use `composer run setup` after configuring `.env`.
