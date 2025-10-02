<!--
Sync Impact Report:
- Version change: Initial → 1.0.0
- Constitution established for e-Perpustakaan library management system
- Added sections: Core Principles (5), Technology Stack Constraints, Development Workflow
- Templates requiring updates: ✅ all existing templates compatible
- Follow-up TODOs: none
-->

# e-Perpustakaan Constitution

## Core Principles

### I. Stack Adherence (NON-NEGOTIABLE)

All development MUST strictly use the approved technology stack: Laravel (backend), Vue 3 with TypeScript (frontend), Inertia.js (SPA bridge), MySQL (database), and ShadCN/Vue (UI components). No additional frameworks or alternative technologies are permitted without constitutional amendment. Dependencies MUST be minimal and justify their inclusion.

### II. Modern Monolith Architecture

The application MUST maintain a single, cohesive Laravel application structure. No microservices, no separate API applications. All functionality resides within one Laravel project using Inertia.js for seamless frontend-backend integration. This ensures simplicity, maintainability, and consistent deployment patterns.

### III. Clean & Modular Design

Every feature MUST be organized into self-contained modules with clear boundaries. Laravel's built-in service providers, repositories, and resource patterns are mandatory. Frontend components MUST follow atomic design principles. Business logic MUST be separated from controllers and placed in dedicated service classes.

### IV. Test-First Development (NON-NEGOTIABLE)

TDD is mandatory: Feature tests → Unit tests → Implementation. All features require both Laravel Feature tests and Vue component tests. Integration testing MUST cover Inertia.js page components and API endpoints. Tests MUST be written first and MUST fail before implementation begins.

### V. Responsive & Consistent UX

All UI components MUST be responsive-first using Tailwind CSS utilities. ShadCN/Vue components provide the design system foundation - no custom UI patterns outside this system. User experience MUST be consistent across all devices and features. Performance target: <200ms page loads, <100ms API responses.

## Technology Stack Constraints

**Backend Requirements**:

- Laravel 11+ with PHP 8.2+
- MySQL 8.0+ for primary database
- Laravel Fortify for authentication
- Pest PHP for testing framework

**Frontend Requirements**:

- Vue 3 with Composition API and TypeScript
- Inertia.js for SPA functionality
- ShadCN/Vue for UI components
- Tailwind CSS for styling
- Vite for build tooling

**Quality Assurance**:

- Laravel Pint for PHP code formatting
- ESLint + Prettier for TypeScript/Vue formatting
- Laravel Pail for real-time log monitoring
- Vue DevTools and Laravel Telescope for debugging

## Development Workflow

**Code Organization**:

- Follow Laravel's convention: Controllers handle HTTP, Services handle business logic, Models handle data
- Vue components organized by feature, not type (pages/, components/, composables/ per feature)
- Inertia pages mirror Laravel route structure
- Database migrations version controlled and never modified after deployment

**Quality Gates**:

- All code MUST pass automated formatting (Pint, Prettier)
- Feature tests MUST pass before merge
- TypeScript compilation MUST succeed without errors
- Vue components MUST have corresponding test coverage

**Library Management Domain**:

- Focus on core library functions: catalog management, member services, circulation, reports
- Admin and member interfaces clearly separated
- Audit trails for all transactional operations
- Multi-tenant considerations for future scaling

## Governance

Constitution supersedes all coding practices and architectural decisions. Amendments require documented justification, stakeholder approval, and migration plan for existing code. All pull requests MUST verify constitutional compliance through automated checks and code review.

Complexity deviations MUST be justified in writing before implementation. Performance requirements are measured and enforced. Use `.github/copilot-instructions.md` for runtime development guidance and context.

**Version**: 1.0.0 | **Ratified**: 2025-10-02 | **Last Amended**: 2025-10-02
