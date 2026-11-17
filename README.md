# Laravel10-Blog

A blog application built on Laravel 10 with a Filament admin panel, user accounts, likes, view counting and Livewire comments.

## Context

A learning project built by Oleksandr Karabudka in May and June 2023 to practise Laravel 10 and its ecosystem (Filament, Livewire, Breeze, spatie/laravel-permission) on a complete, realistic feature set. The git history shows the project growing step by step: Docker setup, models and admin panel, public blog pages, authentication and roles, engagement features, and finally comments.

## Features

- Public blog: home page, post pages with slugs, posts by category, an "About us" page and pagination.
- Home page sections: latest post, three most popular posts by upvotes, and recommended posts (for logged-in users based on categories of posts they upvoted, for guests based on view count).
- Previous/next post navigation on the post page.
- Post view tracking (IP address, user agent, user) in a `post_views` table.
- Upvotes and downvotes on posts (Livewire component).
- Reading and writing comments (Livewire components).
- Estimated reading time for posts.
- SEO meta fields on posts.
- Editable text widgets (e.g. for sidebar and about page content).
- Registration, login, password reset, email verification and profile management (Laravel Breeze).
- Filament admin panel with resources for posts, categories, text widgets and users, plus a widget showing views, upvotes and downvotes per post.
- Admin access restricted to users with the `admin` role (spatie/laravel-permission).

## Tech stack

- PHP 8.1+, Laravel 10
- Filament 2 (admin panel)
- Livewire (comments, voting)
- Laravel Breeze (authentication scaffolding)
- spatie/laravel-permission (roles)
- Tailwind CSS, Alpine.js, Vite
- MariaDB 10.5, Redis, MailHog
- Docker Compose (nginx, PHP-FPM 8.2)

## Architecture

- Standard Laravel MVC: `PostController` and `SiteController` render Blade views; Filament resources in `app/Filament` cover the back office.
- Interactive parts of the post page (voting, comment list, comment form, single comment) are Livewire components in `app/Http/Livewire`.
- Models: `Post`, `Category` (many-to-many via `category_post`), `Comment`, `UpvoteDownvote`, `PostView`, `TextWidget`, `User`.
- Home page ranking is done with SQL aggregation (joins and `COUNT`) in the controller rather than stored counters.
- `User::canAccessFilament()` checks the `admin` role, so the admin panel is closed to regular users.

## Getting started

Requires Docker and Docker Compose.

```bash
sh docker/conf/ssl/generate.sh        # create the local self-signed TLS certificate
cp .env.example .env                  # then adjust APP_NAME and the DB_* values
docker compose up -d web db           # web starts php and mailhog as dependencies
docker compose exec php composer install
docker compose exec php php artisan key:generate --ansi
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed   # creates an admin user with the admin role (see DatabaseSeeder)
npm install && npm run build
```

When using the `db` container, set `DB_HOST=db` in `.env`. Open http://localhost; the admin panel is served by Filament. MailHog is available at http://localhost:8025.

## Testing

PHPUnit tests come from Laravel Breeze (authentication, registration, password reset, email verification, profile) plus the default example tests. They run against in-memory SQLite:

```bash
docker compose exec php php artisan test
```

Laravel Pint is installed as a dev dependency (`vendor/bin/pint`).

## Status

Finished learning project (2023), not maintained.

## License

MIT, see [LICENSE](LICENSE).
