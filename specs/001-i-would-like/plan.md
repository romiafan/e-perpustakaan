# Implementation Plan: Library Management System

**Branch**: `001-i-would-like` | **Date**: 2025-10-02 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/001-i-would-like/spec.md`

## Execution Flow (/plan command scope)

```
1. Load feature spec from Input path
   → If not found: ERROR "No feature spec at {path}"
2. Fill Technical Context (scan for NEEDS CLARIFICATION)
   → Detect Project Type from file system structure or context (web=frontend+backend, mobile=app+api)
   → Set Structure Decision based on project type
3. Fill the Constitution Check section based on the content of the constitution document.
4. Evaluate Constitution Check section below
   → If violations exist: Document in Complexity Tracking
   → If no justification possible: ERROR "Simplify approach first"
   → Update Progress Tracking: Initial Constitution Check
5. Execute Phase 0 → research.md
   → If NEEDS CLARIFICATION remain: ERROR "Resolve unknowns"
6. Execute Phase 1 → contracts, data-model.md, quickstart.md, agent-specific template file (e.g., `CLAUDE.md` for Claude Code, `.github/copilot-instructions.md` for GitHub Copilot, `GEMINI.md` for Gemini CLI, `QWEN.md` for Qwen Code or `AGENTS.md` for opencode).
7. Re-evaluate Constitution Check section
   → If new violations: Refactor design, return to Phase 1
   → Update Progress Tracking: Post-Design Constitution Check
8. Plan Phase 2 → Describe task generation approach (DO NOT create tasks.md)
9. STOP - Ready for /tasks command
```

**IMPORTANT**: The /plan command STOPS at step 7. Phases 2-4 are executed by other commands:

- Phase 2: /tasks command creates tasks.md
- Phase 3-4: Implementation execution (manual or via tools)

## Summary

Library management system enabling members to search book catalog, make single reservations, and manage profiles. Features role-based access (Members/Librarians/Admins), 7-day reservation expiry, and comprehensive search by title, author, genre, year, and ISBN. Technical approach: Laravel monolith with Vue.js frontend via Inertia.js, MySQL database, ShadCN/Vue components, and minimal dependencies.

## Technical Context

**Language/Version**: PHP 8.2+ (Laravel 11+), TypeScript (Vue 3)  
**Primary Dependencies**: Laravel, Vue 3, Inertia.js, ShadCN/Vue, Tailwind CSS, Vite  
**Storage**: MySQL 8.0+ for primary database  
**Testing**: Pest PHP for backend, Vue Test Utils for frontend  
**Target Platform**: Web application (responsive design)
**Project Type**: web - Laravel backend + Vue frontend  
**Performance Goals**: <200ms page loads, <100ms API responses  
**Constraints**: Minimalist UI, single active reservation per member, 7-day expiry  
**Scale/Scope**: Library-scale users, standard book catalog, 3-tier user roles

## Constitution Check

_GATE: Must pass before Phase 0 research. Re-check after Phase 1 design._

**✅ I. Stack Adherence**: Laravel + Vue 3 + TypeScript + Inertia.js + MySQL + ShadCN/Vue - COMPLIANT
**✅ II. Modern Monolith**: Single Laravel app with Inertia.js bridge - COMPLIANT  
**✅ III. Clean & Modular**: Service classes, atomic components, clear boundaries - COMPLIANT
**✅ IV. Test-First Development**: Pest PHP + Vue Test Utils, TDD approach - COMPLIANT
**✅ V. Responsive UX**: ShadCN/Vue + Tailwind, <200ms targets - COMPLIANT

**Additional Constraints Check**:
**✅ Package Manager**: Using pnpm instead of npm as requested - COMPLIANT
**✅ Minimal Dependencies**: Vite build tool, essential libraries only - COMPLIANT
**✅ Best Practices**: Laravel-Vue patterns, clean modular code - COMPLIANT

## Project Structure

### Documentation (this feature)

```
specs/001-i-would-like/
├── plan.md              # This file (/plan command output)
├── research.md          # Phase 0 output (/plan command)
├── data-model.md        # Phase 1 output (/plan command)
├── quickstart.md        # Phase 1 output (/plan command)
├── contracts/           # Phase 1 output (/plan command)
└── tasks.md             # Phase 2 output (/tasks command - NOT created by /plan)
```

### Source Code (repository root)

```
app/
├── Http/
│   ├── Controllers/     # HTTP request handling only
│   ├── Middleware/      # Authentication, CORS, etc.
│   └── Requests/        # Form validation requests
├── Models/              # Eloquent models (User, Book, Reservation)
├── Services/            # Business logic services
└── Providers/           # Service providers

resources/
├── js/
│   ├── pages/          # Inertia pages (Login, Dashboard, BookCatalog)
│   ├── components/     # Vue components (BookCard, ReservationList)
│   ├── composables/    # Vue composition functions
│   └── types/          # TypeScript interfaces
└── css/                # Tailwind styles

database/
├── migrations/         # Database schema
├── seeders/           # Sample data
└── factories/         # Model factories for testing

tests/
├── Feature/           # Laravel Feature tests (HTTP endpoints)
└── Unit/              # Unit tests for services and models
```

**Structure Decision**: Laravel monolith web application structure selected. Single application with backend (Laravel) and frontend (Vue/Inertia) integrated. Follows constitutional requirements for clean separation: Controllers → Services → Models, with Vue components organized by feature.

## Phase 0: Outline & Research

1. **Extract unknowns from Technical Context** above:
    - For each NEEDS CLARIFICATION → research task
    - For each dependency → best practices task
    - For each integration → patterns task

2. **Generate and dispatch research agents**:

    ```
    For each unknown in Technical Context:
      Task: "Research {unknown} for {feature context}"
    For each technology choice:
      Task: "Find best practices for {tech} in {domain}"
    ```

3. **Consolidate findings** in `research.md` using format:
    - Decision: [what was chosen]
    - Rationale: [why chosen]
    - Alternatives considered: [what else evaluated]

**Output**: research.md with all NEEDS CLARIFICATION resolved

## Phase 1: Design & Contracts

_Prerequisites: research.md complete_

1. **Extract entities from feature spec** → `data-model.md`:
    - Entity name, fields, relationships
    - Validation rules from requirements
    - State transitions if applicable

2. **Generate API contracts** from functional requirements:
    - For each user action → endpoint
    - Use standard REST/GraphQL patterns
    - Output OpenAPI/GraphQL schema to `/contracts/`

3. **Generate contract tests** from contracts:
    - One test file per endpoint
    - Assert request/response schemas
    - Tests must fail (no implementation yet)

4. **Extract test scenarios** from user stories:
    - Each story → integration test scenario
    - Quickstart test = story validation steps

5. **Update agent file incrementally** (O(1) operation):
    - Run `.specify/scripts/bash/update-agent-context.sh copilot`
      **IMPORTANT**: Execute it exactly as specified above. Do not add or remove any arguments.
    - If exists: Add only NEW tech from current plan
    - Preserve manual additions between markers
    - Update recent changes (keep last 3)
    - Keep under 150 lines for token efficiency
    - Output to repository root

**Output**: data-model.md, /contracts/\*, failing tests, quickstart.md, agent-specific file

## Phase 2: Task Planning Approach

_This section describes what the /tasks command will do - DO NOT execute during /plan_

**Task Generation Strategy**:

- Load data-model.md to generate Laravel migration and model tasks
- Load contracts/ to generate API endpoint and Inertia page tasks
- Generate TDD task sequence: Feature tests → Unit tests → Implementation
- Organize by domain: Authentication → Books → Reservations → Profile

**Ordering Strategy**:

- Database foundation: Migrations, models, factories
- Authentication system: Fortify setup, middleware, login/register pages
- Book catalog: Search functionality, catalog pages
- Reservation system: Business logic, reservation pages
- UI polish: ShadCN components, responsive design

**Estimated Output**: 35-40 numbered, ordered tasks in tasks.md with [P] parallel markers

**IMPORTANT**: This phase is executed by the /tasks command, NOT by /plan

## Phase 3+: Future Implementation

_These phases are beyond the scope of the /plan command_

**Phase 3**: Task execution (/tasks command creates tasks.md)  
**Phase 4**: Implementation (execute tasks.md following constitutional principles)  
**Phase 5**: Validation (run tests, execute quickstart.md, performance validation)

## Complexity Tracking

_No constitutional violations detected - all requirements align with e-Perpustakaan constitution v1.0.0_

No complexity deviations to document.

## Progress Tracking

_This checklist is updated during execution flow_

**Phase Status**:

- [x] Phase 0: Research complete (/plan command)
- [x] Phase 1: Design complete (/plan command)
- [x] Phase 2: Task planning complete (/plan command - describe approach only)
- [ ] Phase 3: Tasks generated (/tasks command)
- [ ] Phase 4: Implementation complete
- [ ] Phase 5: Validation passed

**Gate Status**:

- [x] Initial Constitution Check: PASS
- [x] Post-Design Constitution Check: PASS
- [x] All NEEDS CLARIFICATION resolved
- [x] Complexity deviations documented (none required)

---

_Based on Constitution v1.0.0 - See `.specify/memory/constitution.md`_
