Hotel Reservation - Beginner Project

Setup
1. Using XAMPP, start Apache and MySQL.
2. Import `create_tables.sql` into your MySQL (phpMyAdmin or mysql client).
   - Database created: `hotel_reservation`.
3. Place this project folder in XAMPP's htdocs (already in your workspace).
4. Open http://localhost/Hotel_Reservation_System/ in your browser.

Default admin
- Email: admin@local
- Password: admin123
(Note: admin password stored plaintext for compatibility; logging in will migrate it to a secure hash.)

Features implemented
- Customer registration/login (passwords hashed on register; existing plaintext migrated on login).
- Admin dashboard: add/edit/delete rooms, manage customers.
- Room search and listing.
- Book room (customer): create booking and mark room as booked.
- Booking history, cancel booking, payment recording.

Quick notes
- If you see blank pages, enable PHP error display or check XAMPP error log at `/Applications/XAMPP/xamppfiles/logs/error_log`.
- This is a beginner project; do not use in production without securing inputs and upgrading to prepared statements.

If you want, I can:
- Convert queries to prepared statements.
- Add a reset-password flow.
- Improve UI further with Bootstrap.
