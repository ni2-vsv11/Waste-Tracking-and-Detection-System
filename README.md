♻️ Waste Tracking & Detection System (WDS)
A smart web-based platform that enables users to report waste issues using images and live location details. The system visualizes waste-prone areas on an interactive map and helps track report progress efficiently. Built to encourage cleanliness, awareness, and citizen participation.

📌 Project Overview
The Waste Tracking & Detection System allows users to report waste found in their surroundings by uploading images and selecting locations on a map. Each report is displayed visually, making it easier to identify critical areas and monitor progress over time. This project is developed as an academic and civic-tech solution focused on real-world environmental challenges.

📸 Project Screenshots
🏠 Home Page
👤 User Dashboard
🗺️ Waste Reporting with Map
📋 Waste Reports List
🛠️ Admin Dashboard
✨ Features
👤 User Features
User registration and login

Waste reporting with waste type, image upload, and map-based location pin

Track report status: Pending, In Progress, Completed

User dashboard with report summary

Profile information display

🛠️ Admin Features
Dashboard with report statistics

Interactive map showing waste locations

View all reported cases

Update report status

Filter reports by date, waste type, and status

🧰 Tech Stack
Frontend: HTML5, CSS3, JavaScript, Bootstrap

Backend: PHP

Database: MySQL

Maps: Leaflet.js, OpenStreetMap

Server: XAMPP / Apache

Version Control: Git & GitHub

🗂️ Project Structure
Plaintext

wds-new/
├── admin/
│   ├── dashboard.php
│   ├── reports.php
│   └── users.php
├── user/
│   ├── dashboard.php
│   └── profile.php
├── config/
│   └── db_connect.php
├── database/
│   └── wms3.sql
├── uploads/
├── css/
├── js/
├── screenshots/
│   ├── home.png
│   ├── user-dashboard.png
│   ├── report-map.png
│   ├── reports.png
│   └── admin-dashboard.png
├── index.php
├── login.php
├── register.php
├── logout.php
└── README.md
⚙️ Installation & Setup
Clone the repository:

Bash

git clone https://github.com/ni2-vsv11/Waste-Tracking-and-Detection-System.git
Move the project to XAMPP htdocs:

Bash

mv Waste-Tracking-and-Detection-System /opt/lampp/htdocs/wds
Database setup:

Open phpMyAdmin

Create a database named wms3

Import database/wms3.sql

Configure database connection in: config/db_connect.php

Set upload permissions:

Bash

chmod 777 uploads/
Run the project: http://localhost/wds

🔒 Security Features
Password hashing

Session-based authentication

SQL injection prevention

XSS protection

Image upload validation

🎯 Use Cases
Smart city waste reporting

Environmental awareness applications

Academic mini or major project

Civic-tech web solution

🚀 Future Enhancements
Mobile application support

AI-based waste classification

Real-time notifications

Advanced analytics dashboard

Multi-language support

👨‍💻 Developed By
Nitesh Vasave Computer Applications Student

Full Stack Developer

📜 License
MIT License

⭐ Support
If you found this project useful, consider starring the repository or sharing feedback.

Happy Coding 🚀♻️
