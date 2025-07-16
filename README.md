# Market Core API

#### v-development

**Market Core API** is a Laravel-based project to manage and sell products. It was developed to improve my backend development skills through a marketplace-style API.

## ✨ Key Features
- **Auth**: Login & registration (CLIENT only)
- **Users**: Manage own profile; ADMIN creates MODERATORs
- **Addresses**: CRUD for user addresses
- **Categories**: Public list, ADMIN-only CRUD
- **Discounts/Coupons**: ADMIN manages; used on products/orders
- **Products**: Public list, MODERATOR CRUD + image upload
- **Cart**: One per user; manage items
- **Orders**: Create from cart, cancel own; MODERATOR updates status
- **Roles**: CLIENT, MODERATOR, ADMIN with increasing permissions
- **Rules**: Role-based access, one coupon/order, cart/order logic enforced

## 🧰 Technologies Used & Tested
- **PHP**: v-8.2
- **Laravel**: v-12.0
- **Linux**: Ubuntu 24.04.2 LTS
- **Docker**: 27.5.1, build 27.5.1-0ubuntu3~24.04.2
- **Docker Compose**: Docker Compose version v2.35.1

## 🚀 Installation

### Clone the repository and navigate into the folder:

```bash
git clone https://github.com/vinifen/marketcore-api.git
```
```
cd marketcore-api
```

### Check ```.env.example```

Check if everthing is like you would like (default config is working)

### Simple Run:

```bash
./run setup
```

### Access:

- API: http://localhost:8010/api
- Docs: http://localhost:8010/api/documentation

### Run `help` to see all available commands

```bash
./run help