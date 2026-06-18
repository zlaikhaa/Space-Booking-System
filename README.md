# Space Booking System

A web-based room and lab booking system built with PHP and MySQL. Lecturers can request a space for a specific date and time, space managers and admins review and approve or reject those requests, and admins have full control over users, rooms, and booking reports. The sample data ships with lecture halls, labs, and meeting rooms, so it's well suited to a university or campus setting, but the structure works for booking any shared spaces.

## Features

**Lecturer**
- Browse available buildings and rooms and submit a booking for a date/time
- Built-in double-booking prevention — a room can't be booked twice for the same date and time while a request is pending or approved
- View personal booking history and cancel pending requests
- Edit profile (username and password)

**Space Manager**
- Approve or reject pending bookings
- Search, filter by building, and sort the bookings list
- View booking reports filtered by building and status

**Admin**
- Everything a Space Manager can do, plus:
- Manage users — add, edit, and delete accounts; assign roles (admin, lecturer, manager)
- Manage spaces — add, edit, and delete bookable rooms grouped by building

**General**
- Session-based login with an optional "Remember Me" auto-login cookie
- Public self-registration (new accounts can sign up as Lecturer or Space Manager)

## Tech Stack

- PHP (procedural, `mysqli`)
- MySQL / MariaDB
- HTML, CSS, and vanilla JavaScript (no frontend framework)
- No external dependencies or package manager — runs on any standard Apache + PHP + MySQL stack (XAMPP, WAMP, MAMP, or LAMP)

## Project Structure

```
Space-Booking-System/
├── index.php              # Public landing page
├── login.php              # Login + "remember me" auto-login
├── logout.php             # Destroys the session
├── register.php           # Public self-registration
├── register_process.php   # Legacy registration handler
├── db.php                 # Database connection settings
├── footer.php              # Shared footer include
├── style.css               # Site-wide styling
│
├── admin_dashboard.php     # Admin landing page
├── manager_dashboard.php   # Space Manager landing page
├── lecturer_dashboard.php  # Lecturer landing page
│
├── manage_users.php        # Admin: list/search/sort users
├── add_user.php            # Admin: create a user
├── edit_user.php           # Admin: edit a user
├── delete_user.php         # Admin: delete a user
│
├── manage_spaces.php       # Admin: list/search/sort spaces
├── add_space.php           # Admin/Manager: create a space
├── edit_space.php          # Admin: edit a space
│
├── book_room.php           # Lecturer: submit a booking
├── view_booking.php        # Lecturer: view/cancel own bookings
├── manage_bookings.php     # Admin/Manager: approve or reject bookings
├── view_report.php         # Admin/Manager: booking reports
│
├── profile.php             # View/edit own profile
├── edit_profile.php        # Profile update handler
│
└── space_booking.sql       # Database schema + sample data
```

## Database Schema

The app uses a single database, `space_booking`, with three tables:

| Table | Key columns | Notes |
|---|---|---|
| `users` | `id_number` (PK), `username`, `password`, `role` | `role` is one of `admin`, `lecturer`, `manager` |
| `spaces` | `id` (PK), `building`, `room_name` | A bookable room within a building |
| `bookings` | `id` (PK), `user_id_number` (FK → `users`), `room`, `date`, `time`, `status` | `status` is one of `Pending`, `Approved`, `Rejected` |

## Getting Started

### Prerequisites
- Apache (or any web server) with PHP 7.4+ and the `mysqli` extension
- MySQL or MariaDB
- The easiest way to get all three is a local stack like [XAMPP](https://www.apachefriends.org/) or WAMP

### Installation

1. Clone the repository into your server's web root (e.g. `htdocs` for XAMPP):
   ```
   git clone https://github.com/zlaikhaa/Space-Booking-System.git
   ```
2. Start Apache and MySQL.
3. Create the database and import the schema:
   - In phpMyAdmin, create a new database named `space_booking`.
   - Import `space_booking.sql` into it (this creates the tables and loads sample users, spaces, and bookings).
4. Open `db.php` and update the credentials if your MySQL setup differs from the defaults (`localhost` / `root` / no password):
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $dbname = "space_booking";
   ```
5. Visit `http://localhost/Space-Booking-System/index.php` in your browser.

### Sample Accounts

The SQL dump includes a few demo accounts for testing each role:

| Role | ID Number | Username | Password |
|---|---|---|---|
| Admin | A3001 | Admin | admin123 |
| Lecturer | L1001 | Jas | 123456 |
| Lecturer | L1002 | Nik | 654321 |
| Manager | M2001 | Abdul | Abdul123 |
| Manager | M2002 | Ady | Ady321 |

You can log in with either the ID number or the username. Replace or remove these accounts before deploying anywhere outside your local machine.

## Notes for Further Improvement

This started as a learning/coursework project, so a few things are worth tightening up before any real deployment: passwords for the seed accounts are stored in plain text and compared directly on login (newer passwords set through the profile page are hashed with `password_hash`, so migrating existing rows to hashes is a natural next step); a handful of queries build SQL from `$_GET`/`$_POST` values without prepared statements; and public registration currently lets anyone sign up directly as a Space Manager, which may be worth gating behind admin approval in production.

## License

No license file is currently included. Add one (MIT, for example) if you intend for others to reuse this code.
