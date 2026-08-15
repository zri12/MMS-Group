# Local Database and Admin Reset

Laravel runtime uses the configured local MySQL connection. phpMyAdmin is only a GUI for that MySQL database.

Run `php artisan migrate` to apply the additive migrations. Never use `migrate:fresh` or `db:wipe` for this workflow.

Run `php artisan mms:reset-dev-users --force-local` only after verifying a local/development environment. The command rejects other environments, revokes tokens, removes prior login accounts, preserves historical PDL profiles by detaching their user account, remaps historical schedule/recap creator references to the new Admin, and creates one random-password Admin account. It does not store the plaintext password in source or documentation.
