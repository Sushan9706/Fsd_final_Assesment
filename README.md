# Real Estate Listing Platform

A production-ready Real Estate Listing Platform built with PHP (PDO), MySQL, and modern frontend technologies.

## Features

### Viewer (Public)
- **Home Page**: Modern hero section and property listings.
- **Ajax Live Search**: Filter properties by location, type, and price without page reload.
- **Property Details**: Extensive view with image galleries and agent info.
- **Enquiry System**: Send messages to agents directly from the property page via Ajax.

### Admin (Agent)
- **Role-Based Login**: Secure session-based authentication with password hashing.
- **Dashboard**: Statistical overview of personal and platform-wide properties.
- **Property Management**: Full CRUD operations for listings (Title, Price, Location, Type, Status).
- **Image Uploads**: Support for multiple property image uploads.

### Super Admin
- **Agent Management**: Exclusive access to create, edit, and delete agent accounts.
- **Full Control**: Ability to manage all properties on the platform.

## Technology Stack
- **Backend**: PHP 8 (PDO)
- **Database**: MySQL
- **Frontend**: HTML5, Vanilla CSS3, JavaScript (Fetch API)
- **Security**: Prepared statements (SQLi protection), `htmlspecialchars` (XSS protection), Password Hashing (`password_hash`).

## Setup Instructions

1. **Database Import**:
   - Open phpMyAdmin or your MySQL client.
   - Create a database named `real_estate_db`.
   - Import the `database.sql` file provided in the root directory.

2. **Configuration**:
   - Check `config/db.php` and update the database credentials if necessary (default: `root` with no password).

3. **Running the App**:
   - Copy the project folder to your `xampp/htdocs/` directory.
   - Access the site via `http://localhost/fsd_final/public/index.php`.

## Login Credentials

email: admin@admin.com
password: admin123

*Note: Agents can be created by the Super Admin.*

## Project Structure
```text
project_root/
│── admin/             (Agent Dashboard & Property CRUD)
│── superadmin/        (Agent Management)
│── config/            (DB connection)
│── public/            (Viewer pages)
│── includes/          (Reusable components & Auth logic)
│── ajax/              (Backend handlers for Fetch requests)
│── assets/            (CSS, JS, and Image uploads)
│── database.sql       (Database schema)
```

## Known Limitations
- Password reset functionality is not implemented.
- Email notifications for enquiries (currently stored in DB only).
