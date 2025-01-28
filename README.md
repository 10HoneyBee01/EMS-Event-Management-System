# EMS - Event Management System

A comprehensive **Event Management System** (EMS) built with PHP, MySQL, and Bootstrap. This project provides a seamless interface for managing events and attendee registrations. Users can create, update, and manage events, while the system enforces capacity limits and provides real-time feedback.

---

## 📋 Project Overview

This Event Management System enables users to:
- Register and log in securely.
- Create, update, and delete events.
- Register attendees while enforcing event capacity limits.
- Download event attendee reports as CSV files (Admin only).

Hosted on **InfinityFree**: [Visit the Website](http://eventos.infinityfreeapp.com/)

---

## ✨ Features

### Core Functionalities
1. **User Authentication**:
   - Secure user registration and login using hashed passwords.
   - Admin and user roles for different access levels.

2. **Event Management**:
   - Authenticated users can create, update, view, and delete events with details such as:
     - Event name
     - Description
     - Maximum capacity

3. **Attendee Registration**:
   - Users can register for events.
   - Prevents registrations beyond the maximum capacity of events.

4. **Event Dashboard**:
   - Displays events in a paginated, sortable, and filterable format for better management.

5. **Event Reports**:
   - Admins can download attendee lists for specific events in CSV format.

---

## 🚀 Installation Instructions

### Prerequisites
Ensure you have the following installed:
- [XAMPP](https://www.apachefriends.org/) for hosting the project locally.
- PHP 7.4 or higher.
- MySQL database.
- Git for version control.

### Steps to Set Up the Project
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/10HoneyBee01/EMS-Event-Management-System.git
   cd EMS-Event-Management-System
