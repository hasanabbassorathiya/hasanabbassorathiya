# hasanabbassorathiya.tech — Portfolio

Personal portfolio of **Hasan Abbas Sorathiya** — AI/GenAI Engineer | Full-Stack (MERN) | Flutter.

Built with Vite + React + Tailwind CSS v3, with a PHP/MySQL CRUD API backend for the CMS admin.

## Stack

- **Frontend:** React 18 SPA, Vite 5, Tailwind CSS 3
- **Backend:** PHP 8.3+, MySQL custom API
- **Hosting:** Hostinger (auto-deployed via GIT)
- **Analytics:** GA4 (configured in `dist/assets/`)

## Sections

1. Boot Intro (first-visit animation)
2. Hero — AI/GenAI Engineer positioned
3. Featured Reel — app chapters (Duas & Aamal, Carvan-e-Hind, Lumio IPTV, EVLAB)
4. Currently Building
5. Stats & API
6. Skills (15+)
7. Experience (6 positions)
8. Open Source
9. Blog
10. Testimonials
11. CTA
12. Extras Dock

## Deployment

The `deploy` branch auto-deploys to Hostinger public_html via hPanel GIT integration.

### First-time DB setup

1. Create MySQL DB + user in hPanel → Databases → MySQL
2. Import `api/schema.sql` and `api/seed.sql`
3. Edit `api/config.php` with actual DB credentials + generate AUTH_PEPPER
4. Access `/admin` to manage content via CMS
