# Xebia Authority Hub

> **Quality without Compromise** | **Sharing Knowledge**

A showcase project demonstrating modern software craftsmanship for the 2026 "AI Era" of software engineering. Built to demonstrate expertise in decoupled architecture, AI integration, and enterprise-grade development practices.

![Quality Gate](https://img.shields.io/badge/quality%20gate-passing-brightgreen)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-purple)
![Next.js 14](https://img.shields.io/badge/Next.js-14-black)
![TypeScript](https://img.shields.io/badge/TypeScript-strict-blue)

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [Architectural Decision Records (ADRs)](#architectural-decision-records-adrs)
- [Security Measures](#security-measures)
- [Testing Strategy](#testing-strategy)
- [The Authority Dashboard](#the-authority-dashboard)

---

## Overview

The **Xebia Authority Hub** is a high-performance, decoupled application featuring:

- **Backend**: Roots Bedrock WordPress with a custom plugin (`xebia-authority-engine`)
- **Frontend**: Next.js 14 with TypeScript and Tailwind CSS
- **AI Integration**: OpenAI GPT-4o for intelligent content generation
- **API Layer**: WPGraphQL for type-safe data fetching

This project embodies Xebia's core values through:
- **Quality without Compromise**: Strict typing, PSR-12 standards, comprehensive testing, and automated quality gates
- **Sharing Knowledge**: Transparent architecture, full AI traceability, and an interactive developer dashboard

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND                                  │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                    Next.js 14 (App Router)                 │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐   │  │
│  │  │   ISR       │  │  React      │  │  Authority      │   │  │
│  │  │   Pages     │  │  Query      │  │  Dashboard      │   │  │
│  │  └─────────────┘  └─────────────┘  └─────────────────┘   │  │
│  └───────────────────────────────────────────────────────────┘  │
│                              │                                   │
│                              ▼                                   │
│                    ┌─────────────────┐                          │
│                    │    GraphQL      │                          │
│                    │    Client       │                          │
│                    └─────────────────┘                          │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                        BACKEND                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                      WPGraphQL                             │  │
│  └───────────────────────────────────────────────────────────┘  │
│                              │                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │              xebia-authority-engine Plugin                 │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐   │  │
│  │  │    DI       │  │  Custom     │  │   AI Summary    │   │  │
│  │  │  Container  │  │  Post Type  │  │   Generator     │   │  │
│  │  └─────────────┘  └─────────────┘  └─────────────────┘   │  │
│  │                              │                             │  │
│  │                              ▼                             │  │
│  │                    ┌─────────────────┐                    │  │
│  │                    │  OpenAI Client  │────────────────────┼──┼──▶ GPT-4o
│  │                    │    (Guzzle)     │                    │  │
│  │                    └─────────────────┘                    │  │
│  └───────────────────────────────────────────────────────────┘  │
│                              │                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                    Roots Bedrock                           │  │
│  │              (WordPress Core + Composer)                   │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Tech Stack

### Backend
| Technology | Purpose |
|------------|---------|
| **PHP 8.2+** | Strict typing, modern syntax |
| **Roots Bedrock** | Professional WordPress structure |
| **WPGraphQL** | Type-safe API layer |
| **Guzzle** | HTTP client for OpenAI |
| **OpenAI GPT-4o** | AI content generation |
| **ACF** | Structured content fields |
| **Pest PHP** | Modern testing framework |
| **PHPCS** | PSR-12 code standards |

### Frontend
| Technology | Purpose |
|------------|---------|
| **Next.js 14** | App Router, ISR, Server Components |
| **TypeScript** | Full type safety |
| **Tailwind CSS** | Utility-first styling |
| **React Query** | Server state management |
| **Framer Motion** | Smooth animations |
| **Vitest** | Fast unit testing |
| **Lucide Icons** | Modern icon set |

---

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 20 or higher
- npm or pnpm
- MySQL/MariaDB database

### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your .env with:
# - Database credentials
# - WordPress salts (generate at https://roots.io/salts.html)
# - OpenAI API key

# Start local server (or configure with your web server)
php -S localhost:8080 -t web
```

### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env.local

# Configure NEXT_PUBLIC_WORDPRESS_GRAPHQL_URL

# Start development server
npm run dev
```

---

## Project Structure

```
xebia-authority-hub/
├── backend/                          # Roots Bedrock WordPress
│   ├── config/                       # Environment configurations
│   │   ├── application.php          # Main config
│   │   └── environments/            # Per-environment configs
│   ├── web/
│   │   ├── app/
│   │   │   └── plugins/
│   │   │       └── xebia-authority-engine/
│   │   │           ├── src/
│   │   │           │   ├── Container/    # DI Container
│   │   │           │   ├── Services/     # Service classes
│   │   │           │   ├── PostTypes/    # CPT registrations
│   │   │           │   ├── GraphQL/      # GraphQL providers
│   │   │           │   ├── OpenAI/       # AI integration
│   │   │           │   └── Blocks/       # Gutenberg blocks
│   │   │           └── blocks/
│   │   │               └── ai-summary-generator/
│   │   └── wp/                      # WordPress core (gitignored)
│   └── composer.json
│
├── frontend/                         # Next.js 14 Application
│   ├── src/
│   │   ├── app/                     # App Router pages
│   │   ├── components/
│   │   │   ├── ui/                  # UI components
│   │   │   ├── dashboard/           # Authority Dashboard
│   │   │   └── insights/            # Insight components
│   │   ├── lib/                     # Utilities & GraphQL
│   │   └── types/                   # TypeScript types
│   └── package.json
│
├── .github/
│   └── workflows/
│       └── main.yml                 # CI/CD pipeline
│
└── README.md
```

---

## Architectural Decision Records (ADRs)

### ADR-001: Why Decoupled Architecture?

**Context**: Modern content platforms need to serve multiple channels while maintaining editorial flexibility.

**Decision**: Implement a headless WordPress architecture with WPGraphQL and a Next.js frontend.

**Rationale**:
- **Separation of Concerns**: Content management is decoupled from presentation
- **Performance**: Static generation and ISR enable sub-second page loads
- **Scalability**: Frontend and backend can scale independently
- **AI-Ready**: Clean API layer facilitates AI service integration
- **Developer Experience**: Frontend developers work with modern tooling

**Consequences**:
- Increased complexity in deployment
- Requires GraphQL expertise
- More flexible and future-proof architecture

---

### ADR-002: Why Roots Bedrock?

**Context**: WordPress needs professional dependency management and security hardening for enterprise use.

**Decision**: Use Roots Bedrock as the WordPress foundation.

**Rationale**:
- **Composer Integration**: Manages WordPress core, plugins, and themes as dependencies
- **Environment Isolation**: Sensitive configs separated from webroot
- **12-Factor App**: Environment-based configuration aligns with modern practices
- **Security**: Improved directory structure limits exposure
- **Version Control**: Clean `.gitignore` patterns for collaborative development

**Consequences**:
- Learning curve for WordPress developers new to Bedrock
- Requires Composer knowledge
- Professional, maintainable WordPress codebase

---

### ADR-003: Why a Custom DI Container?

**Context**: The plugin needs to manage service dependencies cleanly.

**Decision**: Implement a lightweight PSR-11-inspired dependency injection container.

**Rationale**:
- **Testability**: Services can be mocked and tested in isolation
- **Loose Coupling**: Components depend on abstractions, not implementations
- **Single Responsibility**: Container handles object creation and lifecycle
- **No External Dependencies**: Zero overhead from third-party DI libraries

**Consequences**:
- Additional code to maintain
- Demonstrates OOP mastery
- Clean, SOLID architecture

---

### ADR-004: Why the Authority Dashboard?

**Context**: Xebia values "Sharing Knowledge" and transparency in engineering.

**Decision**: Include a floating developer console that exposes internal metrics.

**Rationale**:
- **Engineering Transparency**: Shows test results, AI traceability, performance scores
- **Interview Demonstration**: Instantly demonstrates technical capabilities
- **Educational Value**: Helps others understand the system's internals
- **Quality Assurance**: Visual proof of testing and performance standards

**Consequences**:
- Additional frontend code
- Powerful demonstration of "Sharing Knowledge" value
- Unique interview differentiator

---

### ADR-005: Why OpenAI GPT-4o Integration?

**Context**: AI-native applications represent the future of software engineering.

**Decision**: Integrate OpenAI's GPT-4o model for executive summary generation with full traceability.

**Rationale**:
- **Practical AI Application**: Demonstrates real-world AI integration
- **Traceability**: Full request/response logging for debugging and compliance
- **Modern Architecture**: Shows how to properly abstract AI services
- **Value Addition**: AI enhances content without replacing human creativity

**Consequences**:
- Requires OpenAI API key and associated costs
- Demonstrates cutting-edge capabilities
- Positions the project in the "AI Era"

---

## Security Measures

The Xebia Authority Hub implements comprehensive security measures:

| Measure | Implementation |
|---------|----------------|
| **Nonce Verification** | All REST API endpoints verify WordPress nonces |
| **Input Sanitization** | `sanitize_text_field()`, `wp_kses_post()` on all inputs |
| **Output Escaping** | `esc_html()`, `esc_attr()` on all outputs |
| **GraphQL Protection** | Query depth limits, complexity analysis |
| **Environment Isolation** | Bedrock separates config from webroot |
| **HTTPS Enforcement** | `FORCE_SSL_ADMIN` in production |
| **Security Headers** | XSS, CSRF, clickjacking protection via Next.js |
| **Dependency Audit** | `roave/security-advisories` blocks vulnerable packages |

---

## Testing Strategy

### Backend (Pest PHP)

```bash
cd backend
composer test:pest
```

Tests cover:
- Container singleton binding
- Circular dependency detection
- Post type registration
- OpenAI client mocking
- GraphQL mutations
- REST API nonce verification

### Frontend (Vitest)

```bash
cd frontend
npm run test
```

Tests cover:
- Component rendering
- Navigation functionality
- GraphQL client error handling
- Theme consistency

---

## The Authority Dashboard

The floating **Authority Dashboard** (bottom-right of the frontend) provides real-time visibility into:

### Test Suite
- Live output from Pest PHP (backend) and Vitest (frontend) tests
- Pass/fail status for each test
- Execution timing

### AI Traceability
- Full JSON payload sent to OpenAI
- Complete response data received
- Request duration and token usage
- Timestamp for audit trails

### Performance Scorecard
- Simulated Lighthouse scores (100/100)
- Core Web Vitals (LCP, FID, CLS)
- Real-time performance metrics

### Security Manifest
- Active security measures
- Implementation status
- Compliance checklist

---

## License

MIT License - See [LICENSE](LICENSE) for details.

---

## Author

**Kevin Champlin**

Built for the Xebia interview process to demonstrate:
- Modern software architecture
- AI-native development practices
- Quality without Compromise
- Sharing Knowledge through transparency

---

<p align="center">
  <strong>Xebia Authority Hub</strong><br>
  <em>Quality without Compromise • Sharing Knowledge</em>
</p>
