# InvoiceIQ — GST Billing Suite
**Laravel 11 + Blade | SQLite (or MySQL) | PHP 8.2+**

## Quick Setup (4 commands)

```bash
cp .env.example .env
composer install
php artisan key:generate && php artisan migrate --seed
php artisan serve
```

Open: **http://localhost:8000**

## What's included

| Feature | Details |
|---|---|
| Dashboard | Stats, recent invoices, quick actions |
| Invoice Form | Live GST calc, item auto-suggest, quick-add modals |
| GST Logic | Auto CGST+SGST (intra) vs IGST (inter) based on state |
| PDF Export | DomPDF — professional print-ready layout |
| Customer Master | Full CRUD, GSTIN, state for GST routing |
| Item Master | Full CRUD, HSN/SAC, GST rate, unit |
| Settings | Company profile, bank details, invoice numbering |
| Payments | Record partial/full payments |

## Requirements
- PHP >= 8.2
- Composer
- SQLite (default, zero config) OR MySQL

## Using MySQL
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoiceiq
DB_USERNAME=root
DB_PASSWORD=yourpassword
```
Then: `php artisan migrate --seed`

## PDF Generation
Uses `barryvdh/laravel-dompdf`. Installed via composer automatically.
Access at: `/invoices/{id}/pdf`

## Project Structure
```
app/
  Http/Controllers/    — Dashboard, Invoice, Customer, Item, Settings
  Models/              — Invoice, InvoiceItem, Customer, Item, Payment, Setting
  helpers.php          — fmt_inr(), number_to_words(), setting()
database/
  migrations/          — All 6 tables
  seeders/             — Sample data
resources/views/
  layouts/app.blade.php
  invoices/form.blade.php    — Main invoice form with JS
  invoices/pdf.blade.php     — PDF template
  customers/index.blade.php
  items/index.blade.php
  settings/index.blade.php
  dashboard.blade.php
public/css/app.css           — Full professional CSS (no Tailwind dependency)
```
