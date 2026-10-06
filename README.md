# RoyalCommerce

A Laravel-based e-commerce platform focused on complete storefront workflows, administration, product management, and rich product presentation.

RoyalCommerce combines a traditional Laravel/PHP commerce application with enhanced product-media experiences including **360° product views, 3D model previews, interactive hotspots, and video media**. The application uses a MySQL-backed Laravel architecture and is deployed on **Hostinger** under the NexiView infrastructure.

> **Project type:** Full-stack e-commerce web application  
> **Backend:** Laravel 10 / PHP  
> **Frontend:** Blade, HTML, CSS, JavaScript  
> **Database:** MySQL  
> **Hosting:** Hostinger

---

## Overview

RoyalCommerce is a production-hosted e-commerce web project built around Laravel.

The platform includes customer-facing commerce and account experiences together with administrative product-management workflows. A major extension of the project is its advanced product-media layer, which allows products to be presented through conventional imagery as well as richer interactive formats.

The project has also been hardened for multiple runtime environments. Product media is resolved through host-agnostic paths so the same application can operate across:

- Laravel `php artisan serve`
- local XAMPP / subdirectory environments
- Hostinger production hosting

This avoids coupling stored media references to a specific hostname or development path.

---

## Key Features

### Customer Storefront

The customer-facing application includes the core screens and flows expected from an e-commerce platform, including:

- product and category browsing
- product-detail pages
- quick product viewing
- customer registration and login
- OTP-related authentication flows
- cart and checkout interfaces
- customer account dashboard
- order listing and order-detail views
- order tracking
- wishlist
- transactions and deposits
- rewards
- messages
- support tickets and disputes
- vendor and brand presentation

### Administration

The Laravel administration area provides product-management and supporting commerce workflows.

Confirmed project areas include:

- administrator authentication
- product management
- category-backed catalog structure
- customer/user data
- orders
- payment-gateway configuration data
- general application settings
- product media management

---

## Advanced Product Media

One of the distinguishing areas of RoyalCommerce is the enhanced product-media system.

### 360° Product Views

Products can use a frame-based 360° presentation.

The media structure supports:

```text
assets/
└── products_media/
    └── {product_id}/
        └── 360/
            ├── frames/
            └── manifest.json
```

The manifest and frame set provide the runtime data needed by the product viewer.

### 3D Product Models

Products can reference `.glb` model assets for interactive 3D viewing.

Typical structure:

```text
assets/
└── products_media/
    └── {product_id}/
        └── 3d/
            └── model.glb
```

### Interactive Hotspots

Product media can include interactive hotspot information so specific product regions or features can be surfaced contextually in the storefront experience.

### Video Media

The advanced-media workflow also supports product video media, including uploaded media and URL-based sources where configured.

### Media Metadata

The product schema includes dedicated conventional media fields together with a `media_extra` field used for richer media metadata such as 360°, hotspot, and 3D references.

---

## Product Media Portability

A key deployment requirement is that media paths remain **environment-independent**.

Stored media references should remain relative to the application's public asset root rather than containing development-specific origins such as:

```text
http://127.0.0.1:8000/...
http://localhost/...
/RoyalCommerce/...
```

Preferred form:

```text
assets/products_media/{product_id}/...
```

The application can then resolve the public origin appropriate to the current environment.

This keeps the same product data usable across local development and production deployment without rewriting media records for each host.

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 10 |
| Backend language | PHP |
| Server-rendered UI | Laravel Blade |
| Frontend | HTML, CSS, JavaScript |
| Database | MySQL |
| Package management | Composer |
| Frontend tooling | npm / Vite |
| Web server | Apache-compatible hosting |
| Production hosting | Hostinger |
| Product media | Images, 360° frame sets, GLB 3D models, hotspots, video |

---

## Repository Structure

RoyalCommerce uses a non-standard deployment layout in which the Laravel application lives under `project/` while a root bootstrap/front-controller layer exposes the application to the hosting environment.

A simplified view:

```text
RoyalCommerce/
├── assets/
├── project/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   │   └── views/
│   ├── routes/
│   │   └── web.php
│   ├── storage/
│   ├── vendor/
│   └── ...
├── public_html/
│   └── assets/
├── storage/
├── .htaccess
├── index.php
└── ...
```

The exact production layout is designed around shared-hosting constraints rather than assuming the web server can always point directly at Laravel's conventional `public/` directory.

---

## Application Architecture

At a high level:

```text
Browser
   │
   ▼
Apache / Hostinger
   │
   ▼
Root index.php + .htaccess
   │
   ▼
Laravel application
   │
   ├── Routes
   ├── Controllers
   ├── Blade views
   ├── Authentication / guards
   ├── Product & commerce workflows
   ├── Advanced product media
   └── Database access
            │
            ▼
          MySQL
```

Product pages additionally resolve rich-media assets:

```text
Product
   │
   ├── Standard images
   ├── Thumbnail
   ├── 360° manifest + frames
   ├── 3D GLB model
   ├── Hotspot metadata
   └── Video
```

---

## Local Development

### Requirements

Use versions compatible with the Laravel application and its dependency lockfiles.

Typical requirements:

- PHP 8.x
- Composer
- MySQL / MariaDB
- Node.js and npm
- Laravel-compatible PHP extensions

### 1. Clone the repository

```bash
git clone <repository-url>
cd RoyalCommerce
```

### 2. Install Laravel dependencies

```bash
cd project
composer install
```

### 3. Configure the environment

Create the local environment configuration from the example file if available:

```bash
cp .env.example .env
```

Configure at minimum:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=<your-mysql-port>
DB_DATABASE=<your-database>
DB_USERNAME=<your-database-user>
DB_PASSWORD=<your-database-password>
```

Do not commit `.env` or production credentials.

### 4. Generate the Laravel application key

```bash
php artisan key:generate
```

### 5. Prepare the database

RoyalCommerce has historically used an existing application schema / installer SQL structure, so database initialization should follow the schema source included with the project rather than assuming migrations alone reconstruct the full production database.

### 6. Install frontend dependencies

If frontend assets need to be rebuilt:

```bash
npm install
npm run dev
```

### 7. Clear stale Laravel configuration

After changing `.env` or deployment settings:

```bash
php artisan optimize:clear
```

### 8. Run locally

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

---

## XAMPP / Subdirectory Development

RoyalCommerce has also been run through an Apache/XAMPP-style environment.

Because the application may be served from a subdirectory in this setup, application code and product-media URLs must not assume that `/RoyalCommerce/` exists in production.

Avoid permanently storing environment-specific URL prefixes in the database.

---

## Hostinger Deployment

RoyalCommerce is deployed on Hostinger.

The production deployment uses an external bootstrap/front-controller arrangement suitable for shared hosting.

Conceptually:

```text
public_html/
├── index.php
├── .htaccess
├── assets/
└── app/ or application files
    └── project/
```

The root `index.php` bridges requests into the Laravel application, while `.htaccess` forwards non-file requests through the application front controller.

### Deployment checks

After a deployment or hosting-path change, verify:

1. application homepage loads
2. static assets resolve correctly
3. Laravel routes do not expose filesystem paths
4. login/logout works
5. CSRF-protected forms submit successfully
6. product images load
7. 360° manifests and frames load
8. GLB model requests return successfully
9. hotspot assets and metadata resolve
10. video media resolves correctly
11. uploaded assets remain accessible
12. redirects stay on the intended production origin

---

## Authentication and Access Areas

The application includes separate customer and administrator entry points.

Examples from the current application structure include:

```text
/user/login
/user/register
/admin/login
```

Customer login also includes OTP-related variants.

The repository has multiple authentication guards, so guard-specific behavior should be preserved when changing authentication, middleware, session, or route configuration.

---

## Important Product Routes

Representative storefront routes include:

```text
/category/{cat?}/{sub?}/{child?}
/categories
/item/{slug}
/item/quick/view/{id}
```

These routes form part of the customer catalog and product-discovery experience.

---

## Reliability Considerations

### Environment-safe URLs

Do not persist absolute development URLs for media or uploaded assets.

Bad:

```text
http://localhost/RoyalCommerce/assets/products_media/...
```

Preferred:

```text
assets/products_media/...
```

### Configuration cache

Laravel may continue using stale environment values after `.env` changes.

Use:

```bash
php artisan optimize:clear
```

after environment or hostname changes.

### Filesystem / media deployment

When moving environments, verify that:

- referenced files actually exist
- path casing matches production
- public asset paths are accessible
- storage links are valid where used
- 360° manifests reference valid frame locations
- GLB files are served with a usable MIME type
- cross-origin requests are not accidentally introduced

---

## Security Notes

Production deployments should keep the standard Laravel security boundaries intact:

- never commit `.env`
- never expose database credentials
- preserve CSRF protection
- preserve authentication middleware and guard boundaries
- validate uploaded product media
- avoid trusting client-provided file paths
- keep writable directories limited to required application/storage paths
- disable debug output in production
- ensure production errors do not expose stack traces, secrets, or server paths

Recommended production setting:

```env
APP_ENV=production
APP_DEBUG=false
```

---

## Current Engineering Notes

### Advanced media authoring

The advanced product-media workflow is currently strongest in the **Edit Product** flow.

Confirmed Edit Product functionality includes:

- 360° frames
- 3D model media
- hotspots
- video media

Create Product does not yet have complete parity with Edit Product for all advanced-media capabilities.

This limitation is intentionally documented rather than presenting unfinished behavior as complete.

### Existing application complexity

RoyalCommerce is an established Laravel application rather than a newly scaffolded demo. It contains substantial pre-existing routing, authentication, commerce, storefront, and administrative behavior.

Changes should therefore be scoped carefully to avoid regressions across unrelated customer and admin flows.

---

## Deployment Philosophy

The project follows three important portability rules:

1. **application data should not depend on one hostname**
2. **media references should not depend on one development folder**
3. **deployment-specific routing belongs at the deployment boundary, not inside product data**

This is especially important because the same application has been operated under Laravel's development server, XAMPP-style Apache hosting, and Hostinger production infrastructure.

---

## Project Status

RoyalCommerce is an actively maintained e-commerce application.

Current confirmed focus areas include:

- Laravel storefront and administration
- production hosting
- product catalog and order/account flows
- environment portability
- advanced product presentation
- 360° media
- interactive 3D models
- hotspots
- video
- deployment and integration stabilization

---

## Future Improvements

Potential next phases should be implemented without disrupting the existing commerce core.

Current known improvement area:

- bring advanced product-media authoring on **Create Product** to full parity with **Edit Product**

Any further roadmap items should be added here only after they become approved project scope or implemented functionality.

---

## License

This repository does not declare a license in this documentation.

Unless a license file is explicitly added, do not assume that the source code is licensed for redistribution or reuse.

---

## Maintainer

**Mithun Kumar**

RoyalCommerce / NexiView web development project.
