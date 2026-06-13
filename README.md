# AutoLocation — Système de Gestion de Flotte

> A complete vehicle rental management system built with PHP 8.2 and MySQL — featuring a multilingual admin dashboard, a public client booking portal, fleet tracking, maintenance logs, incident reporting, and audit trails.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?logo=tailwindcss&logoColor=white)
![Railway](https://img.shields.io/badge/Deploy-Railway-0B0D0E?logo=railway&logoColor=white)
![i18n](https://img.shields.io/badge/i18n-FR%20%7C%20EN%20%7C%20AR-10b981)

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Database Schema](#database-schema)
- [Getting Started](#getting-started)
  - [Local Setup (XAMPP)](#local-setup-xampp)
  - [Deploy to Railway](#deploy-to-railway)
- [Configuration](#configuration)
- [User Roles](#user-roles)
- [Multilingual Support](#multilingual-support)
- [Project Structure](#project-structure)

---

## Features

### Admin Dashboard
- **KPI Cards** — real-time overview of fleet size, active reservations, monthly revenue, and client count
- **Charts** — monthly reservation trends and fleet utilization (Chart.js)
- **Revenue tracking** — month-over-month comparison with trend indicators

### Fleet Management
- Add, edit, and delete vehicles with full spec sheets (brand, model, year, fuel, transmission, seats, mileage, price/day, deposit)
- **Multi-image gallery** — upload multiple photos per vehicle with Cloudinary integration and local fallback
- Hover slideshow on vehicle cards
- Status tracking: `disponible` / `loué` / `maintenance` / `indisponible`
- Oil change interval alerts

### Reservations
- Full reservation lifecycle: pending → confirmed → in-progress → completed / cancelled
- Reference number auto-generation
- Linked to client, vehicle, departure/return locations, and km readings
- Payment history and balance tracking per reservation

### Client Management
- Individual and corporate client profiles
- CIN, driver's license (number, category, expiry)
- Blacklist status
- Full reservation history per client

### Payments
- Record deposits, rentals, and extra fees
- Multiple payment methods (cash, card, transfer, cheque)
- Monthly totals and full payment ledger

### Maintenance
- Schedule and track maintenance tasks per vehicle
- Planned vs. actual completion dates and costs
- Status: `planifiée` / `en cours` / `terminée` / `annulée`

### Incidents
- Log accidents, theft, breakdowns, and damage per vehicle and reservation
- Repair cost tracking
- Open / in-progress / closed status

### Client Portal (Public)
- Public-facing vehicle browsing page with search, date range, category, and price filters
- Calculated total based on rental duration
- Online booking form — no account required
- Booking confirmation page (printable)

### Audit Log
- Every create / update / delete action is recorded with timestamp, user, module, and detail
- Filterable by user, module, and date range

### User Management
- Two roles: `admin` and `operateur`
- Admin-only user creation and deletion

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2 |
| Database | MySQL 8 (utf8mb4) |
| Routing | Custom MVC Router (no framework) |
| Frontend | Tailwind CSS (CDN), vanilla JS |
| Charts | Chart.js 4 |
| Image hosting | Cloudinary API (with local upload fallback) |
| Icons | Material Symbols (Google Fonts) |
| Fonts | Inter (LTR) / Noto Sans Arabic (RTL) |
| Deployment | Railway (Nixpacks, PHP built-in server) |

---

## Architecture

The project follows a lightweight **MVC pattern** with no external PHP framework.

```
Request → index.php → Router → Controller → Model → View
```

- **`app/Core/Router.php`** — maps `?page=` query params to controller/action pairs for both GET and POST
- **`app/Core/Controller.php`** — base class: view rendering, flash messages, auth guard, pagination helper
- **`app/Core/Model.php`** — base class: PDO connection wrapper with query helpers
- **`app/Controllers/`** — one controller per module
- **`app/Models/`** — one model per database table
- **`views/layouts/`** — `admin.php` and `client.php` master layouts (with RTL support baked in)
- **`lang/`** — flat PHP arrays for FR / EN / AR translations

---

## Database Schema

8 tables, all InnoDB with utf8mb4:

| Table | Description |
|---|---|
| `users` | Admin and operator accounts |
| `vehicles` | Fleet — specs, status, pricing, images |
| `clients` | Client profiles and driver's license info |
| `reservations` | Rental agreements linking client + vehicle |
| `paiements` | Payment records per reservation |
| `maintenance` | Scheduled and completed maintenance tasks |
| `sinistres` | Incidents and accidents |
| `audit_log` | Immutable action log |

Initialize the database:

```bash
mysql -u root -p < setup.sql
```

> For Railway, use `setup_railway.sql` instead (same schema, compatible with the Railway MySQL plugin).

---

## Getting Started

### Local Setup (XAMPP)

**Requirements:** PHP 8.2+, MySQL 8, Apache/XAMPP

1. **Clone the repository**

   ```bash
   git clone https://github.com/Saman-hero/location_enhanced.git
   cd location_enhanced
   ```

2. **Place the project in your web root**

   ```bash
   # XAMPP on macOS
   cp -r . /Applications/XAMPP/xamppfiles/htdocs/location_enhanced
   ```

3. **Create the database**

   ```bash
   mysql -u root -p < setup.sql
   ```

4. **Configure the database connection**

   Open `config/database.php` and update the PDO credentials:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'location_enhanced');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

5. **Start Apache and MySQL in XAMPP, then visit:**

   ```
   http://localhost/location_enhanced
   ```

6. **Default admin credentials** (created by `setup.sql`)

   ```
   Username: admin
   Password: admin123
   ```

   > Change the password immediately after first login.

---

### Deploy to Railway

The project ships with a `railway.json` and `nixpacks.toml` for zero-config deployment.

1. **Fork or push this repo to GitHub**

2. **Create a new Railway project** → "Deploy from GitHub repo"

3. **Add a MySQL plugin** to your Railway project

4. **Set the following environment variables** in Railway:

   | Variable | Description |
   |---|---|
   | `DB_HOST` | MySQL host (from Railway plugin) |
   | `DB_PORT` | MySQL port |
   | `DB_NAME` | Database name |
   | `DB_USER` | MySQL user |
   | `DB_PASS` | MySQL password |
   | `CLOUDINARY_URL` | *(optional)* `cloudinary://api_key:api_secret@cloud_name` |

5. **Initialize the database** via the Railway MySQL shell:

   ```bash
   mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < setup_railway.sql
   ```

6. Railway will build with Nixpacks (PHP 8.2 + extensions) and start the server automatically.

---

## Configuration

### Cloudinary (optional image hosting)

Set the `CLOUDINARY_URL` environment variable. If not set, images are stored locally in `uploads/vehicles/`.

```
CLOUDINARY_URL=cloudinary://123456789:abc-secret@your-cloud-name
```

### Base URL

The base URL is auto-detected from `$_SERVER['HTTP_HOST']` and `SCRIPT_NAME` — no manual configuration needed for local or Railway deployments.

---

## User Roles

| Role | Access |
|---|---|
| `admin` | Full access — all modules + user management |
| `operateur` | All modules except user management and sensitive audit data |

---

## Multilingual Support

The interface is fully translated in **3 languages**:

| Code | Language | Direction |
|---|---|---|
| `fr` | French | LTR |
| `en` | English | LTR |
| `ar` | Arabic | RTL |

The active language is stored in the session (`$_SESSION['lang']`) and switched via a language picker in the nav bar. The HTML `dir` attribute, font (Inter vs. Noto Sans Arabic), and all layout offsets adapt automatically.

Numbers, dates, amounts, and references are explicitly forced to LTR direction via `dir="ltr"` attributes and a `.num` CSS utility class, preventing bidi reversal in Arabic mode.

---

## Project Structure

```
location_enhanced/
├── app/
│   ├── Controllers/        # One controller per module
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── VehicleController.php
│   │   ├── ReservationController.php
│   │   ├── ClientController.php
│   │   ├── PaymentController.php
│   │   ├── MaintenanceController.php
│   │   ├── IncidentController.php
│   │   ├── UserController.php
│   │   ├── AuditController.php
│   │   └── ClientPortalController.php
│   ├── Core/
│   │   ├── Controller.php  # Base controller (render, flash, auth, pagination)
│   │   ├── Model.php       # Base model (PDO wrapper)
│   │   └── Router.php      # GET/POST router
│   └── Models/             # One model per table
│       ├── Vehicle.php
│       ├── Reservation.php
│       ├── Client.php
│       ├── Payment.php
│       ├── Maintenance.php
│       ├── Incident.php
│       ├── User.php
│       └── AuditLog.php
├── config/
│   ├── database.php        # DB connection, session, i18n bootstrap
│   └── routes.php          # All route definitions
├── lang/
│   ├── fr.php              # French translations
│   ├── en.php              # English translations
│   └── ar.php              # Arabic translations
├── views/
│   ├── layouts/
│   │   ├── admin.php       # Admin shell (sidebar, topbar, RTL support)
│   │   └── client.php      # Public portal shell (navbar, footer)
│   ├── dashboard/
│   ├── vehicles/
│   ├── reservations/
│   ├── clients/
│   ├── payments/
│   ├── maintenance/
│   ├── incidents/
│   ├── users/
│   ├── audit/
│   ├── client-portal/      # Public booking pages
│   ├── auth/
│   └── errors/
├── uploads/
│   └── vehicles/           # Local vehicle image storage (gitignored)
├── index.php               # Application entry point
├── setup.sql               # Local database initialization
├── setup_railway.sql       # Railway database initialization
├── railway.json            # Railway deployment config
└── nixpacks.toml           # Nixpacks build config (PHP 8.2 + extensions)
```

---

## License

MIT — free to use, modify, and distribute.
