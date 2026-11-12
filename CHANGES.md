# Fix My Community - UI/UX drop-in

Copy the contents of this zip over your project (same folder structure). No npm build needed:
styles live in public/css/fmc.css and scripts in public/js/fmc.js.

After copying run:  php artisan view:clear

New:      public/css/fmc.css, public/js/fmc.js, components topo-bg, status-chip, progress-track, report-card, report-row
Changed:  layouts (app, guest, navigation), restyled form components, welcome, dashboard,
          auth/login, auth/register, reports/(index,create,show,edit), admin/(dashboard, reports/index, reports/show)
Logic:    routes/web.php (/dashboard sends admins to admin.dashboard, passes reports to residents)
          AdminController (real chart and panel data), Report model (isOverdue helper)
Untouched: profile pages, forgot/reset/confirm/verify pages (still Tailwind, now inside the new layout)
