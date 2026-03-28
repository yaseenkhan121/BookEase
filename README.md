# 🚀 BookEase - Appointment Booking System

BookEase is a modern, full-stack **Appointment Booking System** built with **Laravel 12 + MySQL (XAMPP)**.  
It is designed to provide a seamless experience for **Customers, Providers, and Admins** with real-time booking and secure authentication.

---

## 📌 Features

### 👤 Authentication & Security
- Secure Login & Registration
- Manual login (No auto-login after signup)
- Role-based access control (Admin, Provider, Customer)
- Session protection & middleware security

---

### 🧑‍⚕️ Provider System
- Providers can register and create profiles
- Add services, pricing, and availability
- **Admin approval required before access**

---

### 📅 Booking System
- Customers can:
  - Browse providers
  - Select services
  - Choose date & time
  - Book appointments

- Real-time booking flow with database integration  
- Prevents invalid bookings

---

### 🛠 Admin Panel
- Approve / Reject providers
- Manage users
- Monitor system activity
- Full control over platform

---

### 📊 Dashboard (Unified)
- Single `/dashboard` route
- Dynamically adapts based on role:
  - Admin Dashboard
  - Provider Dashboard
  - Customer Dashboard

---

### 🔐 Social Login
- Google Sign-In using Laravel Socialite

---

## ⚙️ Tech Stack

- **Backend:** Laravel 12 (PHP)
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL (XAMPP)
- **Authentication:** Laravel Auth + Socialite
- **Environment:** Localhost (XAMPP)

---

## 🧩 System Roles

| Role      | Access |
|----------|--------|
| Admin     | Full control, provider approval |
| Provider  | Manage services & bookings |
| Customer  | Book appointments |

---

## 🛠 Installation Guide

### 1️⃣ Clone Repository
```bash
git clone https://github.com/yaseenkhan121/bookease.git
cd bookease
