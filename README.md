<<<<<<< HEAD
# Waste Detection System (WDS)

A full-stack web application for reporting and managing waste detection in communities. The system allows users to report waste locations with images and location data, while administrators can manage and track these reports.

## Features

- User Registration and Authentication
- Interactive Map Integration using OpenStreetMap
- Waste Report Submission with Images
- Real-time Location Detection
- Admin Dashboard with Statistics
- Report Status Management
- Responsive Design

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript
- Bootstrap 4.5
- Leaflet.js for Maps
- OpenStreetMap

## Prerequisites

- XAMPP (or similar local server environment)
- Web Browser
- Internet Connection (for maps)

## Installation

1. Clone the repository to your XAMPP's htdocs folder:
   ```bash
   cd /path/to/xampp/htdocs
   git clone [repository-url] wds
   ```

2. Create a MySQL database named 'wms3':
   - Open phpMyAdmin
   - Create a new database named 'wms3'
   - Import the database schema from `database/wms3.sql`

3. Configure the database connection:
   - Open `config/db_connect.php`
   - Update the database credentials if needed

4. Set up the file permissions:
   ```bash
   chmod 777 uploads/
   ```

5. Access the application:
   ```
   http://localhost/wds
   ```

## Default Admin Credentials

- Email: admin@gmail.com
- Password: pass123

## Directory Structure

```
wds/
├── admin/
│   └── dashboard.php
├── config/
│   └── db_connect.php
├── css/
│   └── style.css
├── database/
│   └── wms3.sql
├── uploads/
├── user/
│   └── dashboard.php
├── index.php
├── login.php
├── register.php
├── logout.php
└── README.md
```

## Usage

1. User Registration:
   - Navigate to the registration page
   - Fill in the required details
   - Submit the form

2. Reporting Waste:
   - Login to your user account
   - Click on the map to select location
   - Fill in waste details
   - Upload an image
   - Submit the report

3. Admin Management:
   - Login with admin credentials
   - View all reports on the dashboard
   - Update report statuses
   - View statistics

## Security Features

- Password Hashing
- Session Management
- SQL Injection Prevention
- XSS Protection
- File Upload Validation

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email [your-email@example.com] 
=======
# Waste-Tracking-and-Detection-System
>>>>>>> d84f0e55d78e4dd42dde45bdb09c62eced4bc7a5
