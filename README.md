# MVC Auth — Fresh Refactor (dbforlab)

This is an original MVC split for Register/Login using **dbforlab**.

## Run
1. Create DB **dbforlab** and import your class-provided `dbforlab.sql`.
2. Update MySQL creds in `settings/db_cred.php` if needed.
3. From the project root: `php -S localhost:8000`
4. Open:
   - Register: http://localhost:8000/public/register.php
   - Login:    http://localhost:8000/public/login.php

## Notes
- `register.php` has no `action`; `public/js/register.js` submits via fetch (AJAX).
- Passwords hashed (`password_hash`) and verified (`password_verify`).
- Required fields: name, email, password, country, city, contact, image(nullable), role(default 2).
