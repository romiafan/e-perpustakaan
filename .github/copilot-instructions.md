# e-Perpustakaan Development Guidelines

Auto-generated from constitution v1.0.0. Last updated: 2025-10-02

## Active Technologies

**Backend**:

- Laravel 11+ with PHP 8.2+
- MySQL 8.0+ for primary database
- Laravel Fortify for authentication
- Pest PHP for testing framework

**Frontend**:

- Vue 3 with Composition API and TypeScript
- Inertia.js for SPA functionality
- ShadCN/Vue for UI components
- Tailwind CSS for styling
- Vite for build tooling

## Project Structure

```
app/
├── Http/Controllers/        # HTTP request handling only
├── Models/                  # Eloquent models
├── Services/               # Business logic (NEW: create for each feature)
└── Providers/              # Service providers

resources/
├── js/
│   ├── pages/             # Inertia pages (mirror route structure)
│   ├── components/        # Vue components (organized by feature)
│   └── composables/       # Vue composition functions
└── css/                   # Tailwind styles

database/
├── migrations/            # Never modify after deployment
└── seeders/              # Development data

tests/
├── Feature/              # Laravel Feature tests (required)
└── Unit/                 # Unit tests for services
```

## Commands

**Development**:

```bash
composer run dev          # Start all services (Laravel + Vite + Queue + Logs)
composer run test         # Run all tests
php artisan pint          # Format PHP code
npm run lint              # Format TypeScript/Vue code
```

**Database**:

```bash
php artisan migrate       # Run migrations
php artisan db:seed       # Seed development data
```

## Code Style

**PHP (Laravel)**:

- Controllers: Handle HTTP only, delegate to Services
- Services: Contains business logic, injected via DI
- Models: Eloquent relationships and accessors only
- Follow Laravel Pint formatting

**TypeScript/Vue**:

- Use Composition API with `<script setup>` syntax
- Props with TypeScript interfaces
- ShadCN/Vue components for all UI elements
- Follow ESLint + Prettier formatting

**Testing**:

- Feature tests MUST be written first
- Test HTTP endpoints via Feature tests
- Test business logic via Unit tests on Services
- Vue component tests using Vue Test Utils

## Recent Changes

- Initial constitution v1.0.0 established
- Technology stack defined and locked
- Testing requirements formalized

<!-- MANUAL ADDITIONS START -->
<!-- Add project-specific development notes here -->
<!-- MANUAL ADDITIONS END -->
