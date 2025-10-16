# Tasks: Library Management System

**Input**: Design documents from `/specs/001-i-would-like/`
**Prerequisites**:### Phase 3.6: Frontend Pages [T035-T041] ### Phase 3.7: Vue Components (ShadCN/Vue) [T042-T045] ✅

**T042: Create BookCard component** ✅

- [x] resources/js/components/BookCard.vue (reusable book display)
- [x] Props for book data, loading states, click handlers
- [x] ShadCN/Vue Card, Button components
- [x] Responsive design with proper spacing
      **T043: Create ReservationList component** ✅
- [x] resources/js/components/ReservationList.vue
- [x] Active reservations and history sections
- [x] Status badges, action buttons, empty states
- [x] Date formatting and days remaining logic
      **T044: Create SearchForm component** ✅
- [x] resources/js/components/SearchForm.vue
- [x] Search input, genre/year filters, sorting options
- [x] Advanced filters toggle, active filters display
- [x] Clear functionality and form validation
      **T045: Create UserMenu component** ✅
- [x] resources/js/components/UserMenu.vue
- [x] Dropdown menu with user info, navigation links
- [x] Configurable appearance and menu items
- [x] Avatar display, role badges, logout functionality035: Create Inertia.js pages\*\* ✅
- [x] Login/Register pages (auth/)
- [x] Dashboard.vue
- [x] Books/Catalog.vue (search, filters, pagination)
- [x] Books/Detail.vue (book details, reservation)
- [x] Reservations/Index.vue (user reservations)
- [x] Profile/Index.vue (user profile management)
      **T036: Add TypeScript interfaces** ✅
- [x] User, Book, Reservation types
- [x] API response interfaces
- [x] Props interfaces for components
      **T037: Implement responsive layouts** ✅
- [x] Mobile-first design
- [x] ShadCN/Vue components
- [x] Consistent spacing/typography
      **T038: Add loading states** ✅
- [x] Skeleton loaders
- [x] Progress indicators
- [x] Empty states
      **T039: Form validation integration** ✅
- [x] Client-side validation
- [x] Error display
- [x] Success feedback
      **T040: Data fetching integration** ✅
- [x] Axios/fetch setup
- [x] Error handling
- [x] State management
      **T041: Navigation structure [P]** ✅
- [x] Main navigation
- [x] Breadcrumbs
- [x] User menu✓, research.md ✓, data-model.md ✓, contracts/ ✓

## 🚀 Implementation Status (Updated October 16, 2025)

### ✅ **COMPLETED PHASES**

- **Phase 3.1**: Setup & Environment (T001-T004) - 100% Complete
- **Phase 3.2**: Database Foundation (T005-T013) - 100% Complete
- **Phase 3.3**: Tests First/TDD (T014-T026) - 100% Complete
- **Phase 3.4**: Service Layer (T027-T030) - 100% Complete
- **Phase 3.5**: HTTP Controllers (T031-T034) - 100% Complete
- **Phase 3.6**: Frontend Pages (T035-T041) - 100% Complete
- **Phase 3.7**: Vue Components (T042-T045) - 100% Complete
- **Phase 3.8**: Request Validation (T046-T049) - 100% Complete
- **Phase 3.9**: Routes & Middleware (T050-T053) - 100% Complete
- **Phase 3.10**: Background Jobs (T054-T056) - 100% Complete
- **Phase 3.11**: Database Seeders (T057-T059) - 100% Complete
- **Phase 3.12**: TypeScript Interfaces (T060-T063) - 100% Complete
- **Phase 3.13**: Polish & Performance (T069-T070) - Complete (Tests skipped per request)

### 📊 **Current Status**

- ✅ **Core library functionality ready**
- ✅ **All backend services implemented**
- ✅ **Frontend pages and components ready**
- ✅ **Database with sample data**
- ✅ **Authentication and authorization working**
- ✅ **Reservation system with notifications**
- ⏭️ **Testing phase skipped per user request**

### 🎯 **Key Deliverables**

- ✅ User authentication (login, register, logout)
- ✅ Role-based access control (admin, librarian, member)
- ✅ Book catalog with search and filtering
- ✅ Book reservation system with 7-day expiry
- ✅ Automatic expiry with email notifications
- ✅ User profile management
- ✅ Responsive UI with ShadCN/Vue
- ✅ TypeScript type safety
- ✅ Database seeders with 12 sample books and test users

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

- [x] T001 Configure package.json to use pnpm and verify Laravel + Vue 3 + Inertia.js setup
- [x] T002 [P] Configure Laravel Pint for PHP formatting in phpunit.xml
- [x] T003 [P] Configure ESLint + Prettier for TypeScript/Vue in eslint.config.js
- [x] T004 [P] Set up Pest PHP testing environment in tests/Pest.php

## Phase 3.2: Database Foundation

- [x] T005 [P] Create User migration in database/migrations/create_users_table.php
- [x] T006 [P] Create Book migration in database/migrations/create_books_table.php
- [x] T007 [P] Create Reservation migration in database/migrations/create_reservations_table.php
- [x] T008 [P] Create User model in app/Models/User.php
- [x] T009 [P] Create Book model in app/Models/Book.php
- [x] T010 [P] Create Reservation model in app/Models/Reservation.php
- [x] T011 [P] Create UserFactory in database/factories/UserFactory.php
- [x] T012 [P] Create BookFactory in database/factories/BookFactory.php
- [x] T013 [P] Create ReservationFactory in database/factories/ReservationFactory.php

## Phase 3.3: Tests First (TDD) ⚠️ MUST COMPLETE BEFORE 3.4

**CRITICAL: These tests MUST be written and MUST FAIL before ANY implementation**

### Authentication Contract Tests

- [x] T014 [P] Contract test POST /login in tests/Feature/Auth/LoginTest.php
- [x] T015 [P] Contract test POST /register in tests/Feature/Auth/RegisterTest.php
- [x] T016 [P] Contract test POST /logout in tests/Feature/Auth/LogoutTest.php

### Books Contract Tests

- [x] T017 [P] Contract test GET /books (search/catalog) in tests/Feature/Books/BookCatalogTest.php
- [x] T018 [P] Contract test GET /books/{id} in tests/Feature/Books/BookDetailTest.php

### Reservations Contract Tests

- [x] T019 [P] Contract test POST /reservations in tests/Feature/Reservations/CreateReservationTest.php
- [x] T020 [P] Contract test GET /reservations in tests/Feature/Reservations/UserReservationsTest.php
- [x] T021 [P] Contract test PATCH /reservations/{id} in tests/Feature/Reservations/UpdateReservationTest.php

### Profile Contract Tests

- [x] T022 [P] Contract test GET /profile in tests/Feature/Profile/ProfileTest.php
- [x] T023 [P] Contract test PATCH /profile in tests/Feature/Profile/UpdateProfileTest.php

### Integration Tests

- [x] T024 [P] Integration test complete user registration flow in tests/Feature/Integration/UserRegistrationFlowTest.php
- [x] T025 [P] Integration test book search and reservation flow in tests/Feature/Integration/BookReservationFlowTest.php
- [x] T026 [P] Integration test reservation expiry automation in tests/Feature/Integration/ReservationExpiryTest.php

## Phase 3.4: Service Layer (ONLY after tests are failing)

- [x] T027 [P] Create UserService in app/Services/UserService.php
- [x] T028 [P] Create BookService in app/Services/BookService.php
- [x] T029 [P] Create ReservationService in app/Services/ReservationService.php
- [x] T030 [P] Create SearchService in app/Services/SearchService.php

## Phase 3.5: HTTP Controllers

- [x] T031 Create AuthController in app/Http/Controllers/AuthController.php
- [x] T032 Create BookController in app/Http/Controllers/BookController.php
- [x] T033 Create ReservationController in app/Http/Controllers/ReservationController.php
- [x] T034 Create ProfileController in app/Http/Controllers/ProfileController.php

## Phase 3.6: Frontend Pages (Inertia.js)

- [x] T035 [P] Create Login page in resources/js/pages/Auth/Login.vue
- [x] T036 [P] Create Register page in resources/js/pages/Auth/Register.vue
- [x] T037 [P] Create Dashboard page in resources/js/pages/Dashboard.vue
- [x] T038 [P] Create BookCatalog page in resources/js/pages/Books/Catalog.vue
- [x] T039 [P] Create BookDetail page in resources/js/pages/Books/Detail.vue
- [x] T040 [P] Create UserReservations page in resources/js/pages/Reservations/Index.vue
- [x] T041 [P] Create Profile page in resources/js/pages/Profile/Index.vue

## Phase 3.7: Vue Components (ShadCN/Vue)

- [x] T042 [P] Create BookCard component in resources/js/components/BookCard.vue
- [x] T043 [P] Create ReservationList component in resources/js/components/ReservationList.vue
- [x] T044 [P] Create SearchForm component in resources/js/components/SearchForm.vue
- [x] T045 [P] Create UserMenu component in resources/js/components/UserMenu.vue

## Phase 3.8: Request Validation

- [x] T046 [P] Create LoginRequest in app/Http/Requests/LoginRequest.php
- [x] T047 [P] Create RegisterRequest in app/Http/Requests/RegisterRequest.php
- [x] T048 [P] Create CreateReservationRequest in app/Http/Requests/CreateReservationRequest.php
- [x] T049 [P] Create UpdateProfileRequest in app/Http/Requests/UpdateProfileRequest.php

## Phase 3.9: Routes & Middleware

- [x] T050 Define authentication routes in routes/auth.php
- [x] T051 Define web routes in routes/web.php
- [x] T052 Create role-based middleware in app/Http/Middleware/RoleMiddleware.php
- [x] T053 Configure middleware groups in app/Http/Kernel.php

## Phase 3.10: Background Jobs & Automation

- [x] T054 [P] Create ReservationExpiryJob in app/Jobs/ReservationExpiryJob.php
- [x] T055 [P] Schedule reservation expiry in app/Console/Kernel.php
- [x] T056 [P] Create notification system for expired reservations

## Phase 3.11: Database Seeders

- [x] T057 [P] Create BookSeeder with sample library data in database/seeders/BookSeeder.php
- [x] T058 [P] Create UserSeeder with admin/librarian accounts in database/seeders/UserSeeder.php
- [x] T059 Update DatabaseSeeder to call all seeders in database/seeders/DatabaseSeeder.php

## Phase 3.12: TypeScript Interfaces

- [x] T060 [P] Create User types in resources/js/types/User.ts
- [x] T061 [P] Create Book types in resources/js/types/Book.ts
- [x] T062 [P] Create Reservation types in resources/js/types/Reservation.ts
- [x] T063 [P] Create API response types in resources/js/types/Api.ts

## Phase 3.13: Polish & Performance

- [ ] T064 [P] Unit tests for UserService in tests/Unit/Services/UserServiceTest.php (SKIPPED - Testing phase skipped)
- [ ] T065 [P] Unit tests for BookService in tests/Unit/Services/BookServiceTest.php (SKIPPED - Testing phase skipped)
- [ ] T066 [P] Unit tests for ReservationService in tests/Unit/Services/ReservationServiceTest.php (SKIPPED - Testing phase skipped)
- [ ] T067 [P] Performance tests for book search (<100ms) in tests/Feature/Performance/SearchPerformanceTest.php (SKIPPED - Testing phase skipped)
- [ ] T068 [P] Component tests for Vue components using Vue Test Utils (SKIPPED - Testing phase skipped)
- [x] T069 Optimize database queries and add proper indexing
- [x] T070 [P] Update README.md with setup and usage instructions
- [ ] T071 Run full quickstart.md test scenarios and fix any issues (SKIPPED - Testing phase skipped)

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
