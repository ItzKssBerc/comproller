<div align="center">

# 💼 Comproller

**The Modern HR & Payroll Management System**  
*Built for speed, aesthetics, and enterprise-grade reliability.*

[![PHP Version](https://img.shields.io/badge/PHP-8.5%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament Version](https://img.shields.io/badge/Filament-v5-FFA116?style=flat-square&logo=filament&logoColor=white)](https://filamentphp.com)
[![Tailwind Version](https://img.shields.io/badge/Tailwind--CSS-v4-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

---

</div>

## 🌐 System Architecture

Comproller maintains a modular, high-security ecosystem. Below is a high-level overview of our specialized panels:

```mermaid
graph TD
    User((User)) --> Gateway{Authentication}
    Gateway -- Indigo Theme --> Admin[Admin Panel]
    Gateway -- Indigo Theme --> HR[HR Panel]
    Gateway -- Indigo Theme --> Finance[Finance Panel]
    Gateway -- Indigo Theme --> Camera[Camera Panel]
    
    subgraph Core
        Admin --- DB[(Database)]
        HR --- DB
        Finance --- DB
        Camera --- DB
    end

    subgraph Security
        Gateway --- MFA[Multi-Factor Auth]
        Gateway --- Roles[Role Based Access]
    end
```

## 💎 Key Features

| Feature | Description | Highlight |
| :--- | :--- | :--- |
| **Aurora UI** | Dynamic Indigo & Teal animated gradients. | ✨ Modern |
| **Typewriter** | Smooth character-by-character text entrance. | ⌨️ Interactive |
| **MFA Security** | Google Authenticator integration for all roles. | 🔒 Secure |
| **Modular Panels** | Separate contexts for HR, Finance, and Admin. | 🧩 Scalable |
| **Smart PDF** | Bulk generation for contracts and ID cards. | 📄 Efficient |

## 🛠️ Tech Stack

### 🏗️ Infrastructure
- **Framework:** Laravel 12+ (Latest)
- **Database:** PostgreSQL / MySQL / SQLite
- **Runtime:** PHP 8.5+

### 🎨 Frontend & UI
- **Admin Engine:** Filament v5
- **Reactivity:** Livewire 3
- **Styling:** Tailwind CSS v4-beta (Ultra-fast)
- **Design:** Glassmorphism & Aurora Gradients

## 📦 Installation & Quick Start

```bash
# 1. Clone & Enter
git clone https://github.com/ItzKssBerc/comproller.git && cd comproller

# 2. Automated Setup
# Installs dependencies, sets up the database, and builds assets.
composer setup
```

## 👨‍💻 Development

To start the local development environment:

```bash
composer dev
```

---

<div align="center">

Made with ❤️ by **Kiss Bercel**  
*Innovating HR management, one line of code at a time.*

</div>

---

<p align="center">
  Made with ❤️ by <strong>Kiss Bercel</strong><br>
  <em>Innovating HR management, one line of code at a time.</em>
</p>