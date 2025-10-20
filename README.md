# Artax-ID 👤

> The AI Identity & Access Hub — manage, authorize, and contextualize every agent securely and effortlessly.

**Artax-ID** is a **dedicated OAuth 2.0 server and agent identity manager** designed for the Artax-Eye ecosystem. It provides secure authentication, context-aware authorization, and fine-grained access control for every AI agent. Each agent gets personalized credentials, login data, and access rights defined by its manager, making it more than a traditional IAM — it’s **AI identity, elevated.**

---

## ✨ Features

* **OAuth 2.0 Server:** Out-of-the-box, fully standards-compliant OAuth server for AI agents.
* **Agent-Specific Context:** Each agent has its own login info, profile, and context-aware permissions.
* **Fine-Grained Authorization:** Grant access to tools, APIs, and resources based on agent type and manager policies.
* **Secure Identity Management:** Safely store credentials, keys, and tokens for every agent.
* **Manager-Controlled Access:** Managers define and adjust permissions, enforcing policies dynamically.
* **Extensible:** Easily integrate with Artax-Eye or other AI orchestration systems.

---

## 🛠️ Tech Stack

* **Backend:** Laravel 12 (PHP 8.3+)
* **Database:** PostgreSQL
* **Authentication:** Laravel Passport / Sanctum for OAuth 2.0
* **Testing:** PestPHP
* **Package Manager:** Bun
* **Optional Tools:** Redis for session/cache management

---

## 🚀 Installation

### Prerequisites

* PHP >= 8.3
* Composer
* PostgreSQL
* Node.js or Bun
* Git

### Steps

```bash
git clone https://github.com/artaxnetwork-dev/artax-id.git
cd artax-id

cp .env.example .env
# Update .env with database & environment configs

composer install
herd php artisan key:generate
herd php artisan passport:install

bun install

herd php artisan migrate
herd php artisan db:seed # Optional: preload agent identities
```

### Run the Application

Access the OAuth server at: `https://artax-id.test` (Already setup by Laravel Herd automatically no need to serve)

---

## 🧪 Testing

```bash
herd php artisan test
bun run test
```

Secure, elegant, and fully covered by **PestPHP** tests.

---

## 🌌 Roadmap

* Agent login & token lifecycle management
* Manager dashboards for access control
* Context-aware token scopes per agent type
* Integration with Artax-Eye orchestration
* Audit logs & security metrics
* Extensible plugin architecture for external tools

---

## 💎 Contributing

Artax-ID is part of the **Artax Network**. Contributions should be elegant, secure, and fully tested. Feature proposals, bug fixes, and integration examples are all welcome.