# Demo Dataset

Run `php artisan mms:seed-demo --force-local` only on local/development. It preserves the existing Admin account and creates six non-login PDL profiles, 18 members, six prospects, daily schedules, seven days of operational reports, 12 operational attachments, tracking sessions/points, and one current recap.

PDL demo profiles do not have `users` records; therefore they cannot login or obtain a token. Assets are local under `public/demo/`.
