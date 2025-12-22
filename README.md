# ♻️ Waste Tracking & Detection System (WDS)

A smart web-based platform that enables users to report waste issues using images and live location details.  
The system helps visualize waste-prone areas through interactive maps and allows authorities to track report progress efficiently.

🌍 Built to encourage cleanliness, awareness, and citizen participation.

---

## 📌 Project Overview

The **Waste Tracking & Detection System** allows users to report waste found in their surroundings by uploading images and selecting locations on a map.  
All reports are displayed visually, making it easier to identify critical areas and monitor progress over time.

This project is developed as an **academic and civic-tech solution** addressing real-world environmental challenges.

---

## ✨ Features

### 👤 User Features
- User registration and login
- Waste reporting with:
  - Waste type selection
  - Image upload
  - Map-based location pin
- Track report status:
  - Pending
  - In Progress
  - Completed
- User dashboard with report summary
- Profile information view

### 🛠️ Admin Features
- Dashboard with report statistics
- Interactive map showing waste locations
- View all reported cases
- Update report status
- Filter reports by:
  - Date
  - Waste type
  - Status

---

## 🖥️ Screens & Interface

- 🏠 Landing page with user & admin login
- 📊 Dashboard cards showing report count and status
- 🗺️ OpenStreetMap integration using Leaflet.js
- 📋 Detailed report listing with images and location
- 📱 Fully responsive design for all screen sizes

---

## 🧰 Tech Stack

| Layer | Technologies |
|------|-------------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap |
| Backend | PHP |
| Database | MySQL |
| Maps | Leaflet.js, OpenStreetMap |
| Server | XAMPP / Apache |
| Version Control | Git & GitHub |

---

## 🗂️ Project Structure

wds-new/
├── admin/
│ ├── dashboard.php
│ ├── reports.php
│ └── users.php
├── user/
│ ├── dashboard.php
│ └── profile.php
├── config/
│ └── db_connect.php
├── database/
│ └── wms3.sql
├── uploads/
├── css/
├── js/
├── index.php
├── login.php
├── register.php
├── logout.php
└── README.md


---

## ⚙️ Installation & Setup

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/ni2-vsv11/Waste-Tracking-and-Detection-System.git

2️⃣ Move Project to XAMPP htdocs
mv Waste-Tracking-and-Detection-System /opt/lampp/htdocs/wds

3️⃣ Database Setup

Open phpMyAdmin

Create a database named wms3

Import the file: database/wms3.sql

4️⃣ Configure Database Connection

Edit:

config/db_connect.php


Update database credentials if required.

5️⃣ Set Upload Permissions
chmod 777 uploads/

6️⃣ Run the Project
http://localhost/wds

🔒 Security Features

Password hashing

Session-based authentication

SQL injection prevention

XSS protection

Image upload validation

🎯 Use Cases

Smart city waste reporting

Environmental awareness systems

Academic mini / major project

Civic-tech based web application

🚀 Future Enhancements

📱 Mobile application support

🤖 AI-based waste classification

🔔 Real-time notifications

📊 Advanced analytics dashboard

🌐 Multi-language support

👨‍💻 Developed By

Nitesh Vasave
🎓 Computer Applications Student
💻 Full Stack Developer
🌱 Interested in Smart City & Environmental Solutions

📜 License

This project is licensed under the MIT License.

⭐ Support

If you found this project helpful:

⭐ Star the repository

🍴 Fork it

🐞 Raise issues or suggestions

Happy Coding 🚀♻️
