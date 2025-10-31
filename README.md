# 🚀 Laravel Setup Automator

[![Packagist Version](https://img.shields.io/packagist/v/fahimhossainmunna/quick-setup.svg?style=flat-square)](https://packagist.org/packages/fahimhossainmunna/quick-setup)
[![Total Downloads](https://img.shields.io/packagist/dt/fahimhossainmunna/quick-setup.svg?style=flat-square)](https://packagist.org/packages/fahimhossainmunna/quick-setup)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

A lightweight and time-saving Composer package that helps you **automatically set up a freshly cloned Laravel project** with a single command.  
No more manual `.env` copying, dependency installation, or migration hassle — just run one command and get started!

---

## 🧠 About The Project

When we clone a Laravel repository from GitHub, we usually need to:

- Copy `.env.example` → `.env`
- Run `composer install` and `npm install`
- Generate the application key
- Set up database credentials
- Run migrations and link storage
- Configure app URL and other environment values

Doing this repeatedly can be frustrating — especially for teams.  
So this package provides a **bash-powered automation tool** that executes all these steps with a single command.  
Perfect for developers who want a **faster, cleaner, and repeatable setup workflow.**

---

## ✨ Features

- 🔁 Auto-create `.env` from `.env.example`
- 🧩 Run `composer install` and `npm install`
- 🔑 Auto-generate app key
- 🗄️ Run database migrations
- 🔗 Link Laravel storage
- ⚙️ Optionally set `APP_URL`, `DB_*` configs
- 🧹 Clean and minimal — no heavy dependencies

---

## ⚙️ Installation

You can install it globally or directly inside your Laravel project.

### **Install in your Laravel project**
```bash
composer require fahimhossainmunna/quick-setup
```

### Once installed, simply run the following Artisan command:
```bash
php artisan run:quick-setup
```

## 🛠️ Troubleshooting

If something doesn’t work, try the following steps:

1. **Ensure required tools are installed**
   - PHP  
   - Composer  
   - Node.js  
   - npm

2. **Check your environment file**
   - Make sure your `.env` file exists and contains valid **database credentials**.

3. **Clear and refresh Laravel cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
4. **Fix file or directory permissions - If you encounter permission errors, run:**
   ```bash
    chmod -R 775 storage bootstrap/cache
   ```


 **Fahim Hossain Munna**  
🔗 [Packagist Profile](https://packagist.org/packages/fahimhossainmunna/quick-setup)  
💻 [GitHub Profile](https://github.com/Fahim-Hossain-Munna)

## 🪪 License
This package is open-sourced software licensed under the [MIT license](LICENSE).
