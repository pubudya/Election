# Automated Sri Lankan Presidential Election System 🇱🇰

A secure, full-stack web application for managing Sri Lankan presidential elections, featuring multi-factor authentication, preference-based voting, real-time results, and robust security at every layer.

---

## 🏛️ Project Overview
This system enables digital voter registration, secure multi-factor authentication, electronic voting, automated counting, and transparent result reporting for Sri Lankan presidential elections. Designed for maximum security, auditability, and accessibility.

## 🚀 Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5
- **Backend:** PHP 8.1+, PHPMailer
- **Database:** MySQL 8.0 (WAMP localhost)
- **Security:** SHA-256, CSRF, rate limiting, session hardening
- **Other:** jQuery, DataTables

## 📁 Directory Structure
- `public/` — Main web entry points (index, login, voting, dashboards)
- `assets/` — CSS, JS, images (Sri Lankan flag, branding)
- `src/` — PHP backend (controllers, models, middleware, utils)
- `config/` — DB, mail, and security configs
- `database/` — SQL schema and seed data
- `docs/` — Technical and user documentation
- `tests/` — PHPUnit and E2E tests

## 🔒 Security Features
- Multi-factor authentication (NIC + PIN + OTP)
- CSRF protection, session hardening, brute-force lockout
- Secure password/PIN hashing (SHA-256 + salt)
- Input validation and sanitization (frontend & backend)
- Secure cookies, HTTPS-ready, strict CORS/headers
- Full audit trail and system logging

## 🧑‍💻 Group Members
- **Kaweesha Liyanage** (Leader, DB Engineer)
- **Pubudya Thathsarani Bandaranayake** (Project Manager & Analyst)
- **Denuwan Guruge** (Software Engineer)
- **Chiraj Deepesh** (UI Engineer)
- **Amiru Mallawarachchi** (QA Engineer & Doc Lead)

## 🇱🇰 Sri Lankan Theme
All pages feature the Sri Lankan flag and national colors for authenticity and pride.

## ⚡ Setup Instructions
1. Clone/download this repo to your WAMP `www` directory.
2. Import `database/schema.sql` into MySQL.
3. Copy `config/.env.example` to `.env` and set DB/mail credentials.
4. Run `composer install` for dependencies (PHPMailer, etc.).
5. Start WAMP and access via `http://localhost/Election/public/`

## 📄 License
MIT 