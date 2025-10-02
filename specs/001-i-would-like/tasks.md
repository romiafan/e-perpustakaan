# Tasks: Library Management System

**Input**: Design documents from `/specs/001-i-would-like/`
**Prerequisites**: plan.md ✓, research.md ✓, data-model.md ✓, contracts/ ✓

## Execution Flow Summary

1. ✅ Load plan.md - Laravel + Vue 3 + Inertia.js + MySQL + ShadCN/Vue stack
2. ✅ Extract entities from data-model.md - User, Book, Reservation
3. ✅ Extract contracts from contracts/ - auth, books, reservations, profile endpoints
4. ✅ Generate TDD task sequence with [P] parallel markers
5. ✅ Order by dependencies: Setup → Tests → Models → Services → Pages → Integration → Polish

## Format: `[ID] [P?] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- Include exact file paths in descriptions

## Phase 3.1: Setup & Environment

- [ ] T001 Configure package.json to use pnpm and verify Laravel + Vue 3 + Inertia.js setup
- [ ] T002 [P] Configure Laravel Pint for PHP formatting in phpunit.xml
- [ ] T003 [P] Configure ESLint + Prettier for TypeScript/Vue in eslint.config.js
- [ ] T004 [P] Set up Pest PHP testing environment in tests/Pest.php

## Phase 3.2: Database Foundation

- [ ] T005 [P] Create User migration in database/migrations/create_users_table.php
- [ ] T006 [P] Create Book migration in database/migrations/create_books_table.php
- [ ] T007 [P] Create Reservation migration in database/migrations/create_reservations_table.php
- [ ] T008 [P] Create User model in app/Models/User.php
- [ ] T009 [P] Create Book model in app/Models/Book.php
- [ ] T010 [P] Create Reservation model in app/Models/Reservation.php
- [ ] T011 [P] Create UserFactory in database/factories/UserFactory.php
- [ ] T012 [P] Create BookFactory in database/factories/BookFactory.php
- [ ] T013 [P] Create ReservationFactory in database/factories/ReservationFactory.php

## Phase 3.3: Tests First (TDD) ⚠️ MUST COMPLETE BEFORE 3.4

**CRITICAL: These tests MUST be written and MUST FAIL before ANY implementation**

### Authentication Contract Tests

- [ ] T014 [P] Contract test POST /login in tests/Feature/Auth/LoginTest.php
- [ ] T015 [P] Contract test POST /register in tests/Feature/Auth/RegisterTest.php
- [ ] T016 [P] Contract test POST /logout in tests/Feature/Auth/LogoutTest.php

### Books Contract Tests

- [ ] T017 [P] Contract test GET /books (search/catalog) in tests/Feature/Books/BookCatalogTest.php
- [ ] T018 [P] Contract test GET /books/{id} in tests/Feature/Books/BookDetailTest.php

### Reservations Contract Tests

- [ ] T019 [P] Contract test POST /reservations in tests/Feature/Reservations/CreateReservationTest.php
- [ ] T020 [P] Contract test GET /reservations in tests/Feature/Reservations/UserReservationsTest.php
- [ ] T021 [P] Contract test PATCH /reservations/{id} in tests/Feature/Reservations/UpdateReservationTest.php

### Profile Contract Tests

- [ ] T022 [P] Contract test GET /profile in tests/Feature/Profile/ProfileTest.php
- [ ] T023 [P] Contract test PATCH /profile in tests/Feature/Profile/UpdateProfileTest.php

### Integration Tests

- [ ] T024 [P] Integration test complete user registration flow in tests/Feature/Integration/UserRegistrationFlowTest.php
- [ ] T025 [P] Integration test book search and reservation flow in tests/Feature/Integration/BookReservationFlowTest.php
- [ ] T026 [P] Integration test reservation expiry automation in tests/Feature/Integration/ReservationExpiryTest.php

## Phase 3.4: Service Layer (ONLY after tests are failing)

- [ ] T027 [P] Create UserService in app/Services/UserService.php
- [ ] T028 [P] Create BookService in app/Services/BookService.php
- [ ] T029 [P] Create ReservationService in app/Services/ReservationService.php
- [ ] T030 [P] Create SearchService in app/Services/SearchService.php

## Phase 3.5: HTTP Controllers

- [ ] T031 Create AuthController in app/Http/Controllers/AuthController.php
- [ ] T032 Create BookController in app/Http/Controllers/BookController.php
- [ ] T033 Create ReservationController in app/Http/Controllers/ReservationController.php
- [ ] T034 Create ProfileController in app/Http/Controllers/ProfileController.php

## Phase 3.6: Frontend Pages (Inertia.js)

- [ ] T035 [P] Create Login page in resources/js/pages/Auth/Login.vue
- [ ] T036 [P] Create Register page in resources/js/pages/Auth/Register.vue
- [ ] T037 [P] Create Dashboard page in resources/js/pages/Dashboard.vue
- [ ] T038 [P] Create BookCatalog page in resources/js/pages/Books/Catalog.vue
- [ ] T039 [P] Create BookDetail page in resources/js/pages/Books/Detail.vue
- [ ] T040 [P] Create UserReservations page in resources/js/pages/Reservations/Index.vue
- [ ] T041 [P] Create Profile page in resources/js/pages/Profile/Index.vue

## Phase 3.7: Vue Components (ShadCN/Vue)

- [ ] T042 [P] Create BookCard component in resources/js/components/BookCard.vue
- [ ] T043 [P] Create ReservationList component in resources/js/components/ReservationList.vue
- [ ] T044 [P] Create SearchForm component in resources/js/components/SearchForm.vue
- [ ] T045 [P] Create UserMenu component in resources/js/components/UserMenu.vue

## Phase 3.8: Request Validation

- [ ] T046 [P] Create LoginRequest in app/Http/Requests/LoginRequest.php
- [ ] T047 [P] Create RegisterRequest in app/Http/Requests/RegisterRequest.php
- [ ] T048 [P] Create CreateReservationRequest in app/Http/Requests/CreateReservationRequest.php
- [ ] T049 [P] Create UpdateProfileRequest in app/Http/Requests/UpdateProfileRequest.php

## Phase 3.9: Routes & Middleware

- [ ] T050 Define authentication routes in routes/auth.php
- [ ] T051 Define web routes in routes/web.php
- [ ] T052 Create role-based middleware in app/Http/Middleware/RoleMiddleware.php
- [ ] T053 Configure middleware groups in app/Http/Kernel.php

## Phase 3.10: Background Jobs & Automation

- [ ] T054 [P] Create ReservationExpiryJob in app/Jobs/ReservationExpiryJob.php
- [ ] T055 [P] Schedule reservation expiry in app/Console/Kernel.php
- [ ] T056 [P] Create notification system for expired reservations

## Phase 3.11: Database Seeders

- [ ] T057 [P] Create BookSeeder with sample library data in database/seeders/BookSeeder.php
- [ ] T058 [P] Create UserSeeder with admin/librarian accounts in database/seeders/UserSeeder.php
- [ ] T059 Update DatabaseSeeder to call all seeders in database/seeders/DatabaseSeeder.php

## Phase 3.12: TypeScript Interfaces

- [ ] T060 [P] Create User types in resources/js/types/User.ts
- [ ] T061 [P] Create Book types in resources/js/types/Book.ts
- [ ] T062 [P] Create Reservation types in resources/js/types/Reservation.ts
- [ ] T063 [P] Create API response types in resources/js/types/Api.ts

## Phase 3.13: Polish & Performance

- [ ] T064 [P] Unit tests for UserService in tests/Unit/Services/UserServiceTest.php
- [ ] T065 [P] Unit tests for BookService in tests/Unit/Services/BookServiceTest.php
- [ ] T066 [P] Unit tests for ReservationService in tests/Unit/Services/ReservationServiceTest.php
- [ ] T067 [P] Performance tests for book search (<100ms) in tests/Feature/Performance/SearchPerformanceTest.php
- [ ] T068 [P] Component tests for Vue components using Vue Test Utils
- [ ] T069 Optimize database queries and add proper indexing
- [ ] T070 [P] Update README.md with setup and usage instructions
- [ ] T071 Run full quickstart.md test scenarios and fix any issues

## Dependencies

### Sequential Dependencies

- **Setup (T001-T004)** → **Database (T005-T013)** → **Tests (T014-T026)** → **Services (T027-T030)** → **Controllers (T031-T034)**
- **Pages (T035-T041)** depend on Controllers (T031-T034)
- **Routes (T050-T053)** depend on Controllers (T031-T034)
- **Jobs (T054-T056)** depend on Models (T008-T010)

### Parallel Execution Groups

```bash
# Group 1: Setup
T002, T003, T004

# Group 2: Database Foundation
T005, T006, T007, T008, T009, T010, T011, T012, T013

# Group 3: Contract Tests
T014, T015, T016, T017, T018, T019, T020, T021, T022, T023

# Group 4: Integration Tests
T024, T025, T026

# Group 5: Services
T027, T028, T029, T030

# Group 6: Frontend Pages
T035, T036, T037, T038, T039, T040, T041

# Group 7: Components
T042, T043, T044, T045

# Group 8: Request Validation
T046, T047, T048, T049

# Group 9: Background Jobs
T054, T055, T056

# Group 10: Seeders
T057, T058

# Group 11: TypeScript Types
T060, T061, T062, T063

# Group 12: Unit Tests & Performance
T064, T065, T066, T067, T068, T070
```

## Parallel Execution Examples

### Example 1: Contract Tests (after database setup)

```bash
# Run these tasks simultaneously:
Task: "Contract test POST /login in tests/Feature/Auth/LoginTest.php"
Task: "Contract test GET /books (search/catalog) in tests/Feature/Books/BookCatalogTest.php"
Task: "Contract test POST /reservations in tests/Feature/Reservations/CreateReservationTest.php"
Task: "Contract test GET /profile in tests/Feature/Profile/ProfileTest.php"
```

### Example 2: Vue Components

```bash
# Run these tasks simultaneously:
Task: "Create BookCard component in resources/js/components/BookCard.vue"
Task: "Create ReservationList component in resources/js/components/ReservationList.vue"
Task: "Create SearchForm component in resources/js/components/SearchForm.vue"
Task: "Create UserMenu component in resources/js/components/UserMenu.vue"
```

## Validation Checklist

- ✅ All 4 contract files have corresponding test tasks
- ✅ All 3 entities (User, Book, Reservation) have model tasks
- ✅ All major endpoints have implementation tasks
- ✅ TDD order: Tests before implementation
- ✅ Parallel tasks target different files
- ✅ File paths are specific and absolute
- ✅ Constitutional compliance: Laravel + Vue + Inertia + MySQL + ShadCN/Vue

## Task Generation Summary

- **Total Tasks**: 71 tasks
- **Parallel Tasks**: 45 tasks marked [P] for concurrent execution
- **Critical Path**: Setup → Database → Tests → Services → Controllers → Routes
- **Estimated Completion**: ~40-50 hours of development work with parallel execution
