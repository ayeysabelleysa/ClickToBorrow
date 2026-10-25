# ClickToBorrow

A PHP and MySQL equipment-borrowing application intended to run with XAMPP.

## Setup

1. Start Apache and MySQL in the XAMPP Control Panel.
2. In phpMyAdmin, import [`database.sql`](database.sql) to create the `clicktoborrow` database and its tables.
3. Configure the admin password as a Windows environment variable named `CLICKTOBORROW_ADMIN_PASSWORD`. Use a strong, unique value; do not put the password in PHP source code or commit it to GitHub.
   - In Windows, open **Edit the system environment variables** → **Environment Variables**, add the variable, then restart Apache from XAMPP so PHP can read it.
   - Admin login fails when this variable is missing or empty.
4. Open `http://localhost/ClickToBorrow/login.php`.

Student accounts can be created from the registration page. Admin login uses an NU Fairview email address and the configured environment-variable password.

`CREATE DATABASE clicktoborrow;.txt` is an older schema draft. Use `database.sql` for a fresh installation.
