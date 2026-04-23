# dr-halim-dental-admin

Laravel 10 + PHP 8.2 + MySQL admin CMS for **مركز د. حليم لطب الأسنان**.

## 1) Install & Run

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create database:

```sql
CREATE DATABASE drhalem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update `.env` DB credentials, then run:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Open:
- Public: `http://127.0.0.1:8000/ar`
- Admin: `http://127.0.0.1:8000/ar/admin/dashboard`

Default admin:
- Email: `admin@drhalim.local`
- Password: `Admin@123456`

## 2) ERD (Text)

- `users` 1..* `model_has_roles` (Spatie)
- `settings` key/value site-wide
- `branches` 1..* `working_hours`
- `branches` *..* `doctors` via `branch_doctor`
- `services` 1..* `appointments`
- `branches` 1..* `appointments`
- `blog_categories` 1..* `blog_posts`
- `pages` standalone dynamic CMS pages
- `home_sections` standalone homepage builder blocks
- `contact_messages` standalone inbox
- `subscribers` standalone newsletter list
- `testimonials` standalone social proof records
- `faqs` standalone FAQ records

## 3) Schema Summary

- `settings`: `key`, `value`
- `branches`: bilingual name/address, maps URL, phone, working hours text, active/sort
- `working_hours`: optional branch, day, open/close, emergency fields
- `services`: bilingual title/description/content, image, featured, slug, SEO
- `doctors`: bilingual name/specialty/bio, photo, experience, featured, main branch + pivot branches
- `blog_posts`: bilingual title/content, image, status, publish date, slug, SEO, category
- `pages`: bilingual title/content, slug, SEO, active
- `home_sections`: key, title ar/en, JSON payload, active, order
- `appointments`: patient, phone, branch, service, date/time, status/source
- `contact_messages`: name/phone/email/message, read/unread
- `subscribers`: unique email
- `testimonials`: name, comments, stars, image, active/sort
- `faqs`: bilingual question/answer, active/sort

## 4) Roles

- `Super Admin`: full access
- `Content Manager`: branches/hours/services/doctors/blog/pages/home/faqs/testimonials
- `Receptionist`: appointments/messages/subscribers

## 5) Security Notes

- Form validation on all store/update endpoints
- Image upload restrictions: mime + max size, stored on `public` disk
- CSRF enabled by default
- Rate limiting:
  - `contact-form` (10/min)
  - `appointment-form` (10/min)
  - `newsletter-form` (15/min)
- Role-based route middleware using Spatie

## 6) Key Paths

- Routes: `routes/web.php`
- Admin controllers: `app/Http/Controllers/Admin`
- Front controllers: `app/Http/Controllers/Front`
- Models: `app/Models`
- Migrations: `database/migrations`
- Seeders: `database/seeders`
- Admin views: `resources/views/admin`
- Front views: `resources/views/front`
