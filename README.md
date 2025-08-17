# Market Core API

#### v-1.0.0

**Market Core API** is a comprehensive Laravel-based marketplace API built entirely from scratch over 111+ hours of dedicated development work. This project was developed to enhance my backend development skills through hands-on implementation of a complete e-commerce solution.

## 🔧 Built From Scratch
This project was constructed from the ground up with careful attention to every detail:
- **Complete Infrastructure Setup**: Docker containers, Nginx configuration, PostgreSQL database
- **Comprehensive Testing**: 380+ unit and feature tests ensuring robust functionality
- **Code Quality**: PHPStan Level 8 static analysis, PHPCS code standards enforcement
- **Complete Documentation**: Full Swagger/OpenAPI documentation for all API endpoints
- **Security Best Practices**: Rate limiting, authentication, authorization policies, input validation
- **Custom Solutions**: Tailored API responses, exception handling, and business logic implementation
- **Simplified Commands**: Custom `./run` script with streamlined commands for easy project management
- **Professional Git Workflow**: Complete version control with milestones, projects, pull requests, issues, and branching strategy

## 💡 Learning Journey
This project has been instrumental in my backend development learning process, providing hands-on experience with:
- Advanced Laravel concepts and best practices
- Test-driven development methodology
- API design and documentation
- Security implementation and code quality standards
- DevOps practices with Docker and containerization
- Professional Git workflow and project management

## ✨ Key Features
- **Auth**: Login & registration
- **Users**: Manage own profile; ADMIN creates MODERATORs
- **Addresses**: CRUD for user addresses
- **Categories**: Public list, ADMIN-only CRUD
- **Discounts/Coupons**: ADMIN manages; used on products/orders
- **Products**: Public list, MODERATOR CRUD + image upload
- **Cart**: One per user; manage items
- **Orders**: Create from cart, cancel own; MODERATOR updates status
- **Roles**: CLIENT, MODERATOR, ADMIN with increasing permissions
- **Rules**: Role-based access, one coupon/order, cart/order logic enforced
- **Authorization**: Laravel Policies for granular user permission control
- **Data Integrity**: Soft deletes implementation for safe data management

## 🧰 Technologies Used & Tested
- **PHP**: v-8.2
- **Laravel**: v-12.0
- **Docker**: 27.5.1, build 27.5.1-0ubuntu3~24.04.2
- **Docker Compose**: Docker Compose version v2.35.1
- **Docker & Docker Compose**: Complete containerization with Nginx
- **PostgreSQL**: Primary database with pgweb interface
- **Testing**: 380+ comprehensive unit and feature tests
- **Code Quality**: PHPStan Level 8 static analysis + PHPCS standards
- **Documentation**: Complete Swagger/OpenAPI specification
- **Security**: Rate limiting, authentication middleware, authorization policies
- **Linux**: Ubuntu 24.04.2 LTS development environment

## 🚀 Installation

### Clone the repository and navigate into the folder:

```bash
git clone https://github.com/vinifen/marketcore-api.git
cd marketcore-api
```

### Check `.env.example`

Check if everything is like you would like (default config is working)

### Simple Run with Custom Commands:

The project includes a custom `./run` script with simplified commands for easy management:

```bash
./run setup
```

This single command handles the entire setup: environment configuration, Docker containers, dependencies, database migrations, and seeding.

### Access:

- API: http://localhost:8010/api
- Docs: http://localhost:8010/api/documentation
- Pgweb: http://localhost:8011

### Available Commands:

Run `help` to see all available simplified commands:

```bash
./run help
```

**Common commands:**
- `./run setup` - Complete project setup
- `./run test` - Run all tests
- `./run phpstan` - Static analysis
- `./run phpcs` - Code standards check
- `./run all-tests` - Run all quality checks

## 📖 API Documentation Preview

### Swagger Interface
![API Documentation Interface 1](https://res.cloudinary.com/dqafdlj0c/image/upload/v1754863793/marketcore-api-swagger-1_mqhipp.png)

![API Documentation Interface 2](https://res.cloudinary.com/dqafdlj0c/image/upload/v1754864062/marketcore-api-swagger-2_qpdq8b.png)

## 📊 Project Statistics
- **111+ Development Hours**: Dedicated time investment in building this comprehensive solution
- **380+ Tests**: Comprehensive unit and feature test coverage
- **PHPStan Level 8**: Strict static analysis with zero errors
- **PHPCS Compliant**: Follows PSR-12 coding standards
- **100% API Documentation**: Complete Swagger documentation for all endpoints
- **Security First**: Rate limiting, input validation, and proper authorization
- **Custom Exception Handling**: Tailored error responses and exception management
- **Simplified Management**: Custom `./run` script with 15+ streamlined commands
- **Professional Version Control**: Complete Git workflow with organized project management

## 🎯 Development Excellence
This project demonstrates professional-grade development practices including:
- **Test-Driven Development**: Extensive test suite covering all functionality
- **Code Quality**: Static analysis and coding standards enforcement
- **API Documentation**: Complete OpenAPI/Swagger specification
- **Security Implementation**: Authentication, authorization, and rate limiting
- **Containerization**: Full Docker setup with Nginx and PostgreSQL
- **Developer Experience**: Simplified command interface for easy project management
- **Project Management**: Professional Git workflow with milestones, projects, pull requests, and issues
- **Branching Strategy**: Organized development workflow with main and feature branches
- **Best Practices**: Following Laravel and PHP community standards

## 🛢️ Database Schema
![Database Schema](https://github.com/vinifen/marketcore-api/blob/development/docs/database/marketcore-api-dbdiagram-io.png)
*Complete database documentation available in [`docs/database/`](docs/database/)*

## 🔄 Git Workflow & Project Management
This project follows professional development practices with comprehensive version control:

### **Repository Structure:**
- **Main Branch**: Production-ready stable code
- **Development Branches**: Feature development and bug fixes (e.g., `refactor/43/organize-files`)
- **Organized Commits**: Clear, descriptive commit messages following conventional standards

### **Project Management Tools:**
- **📋 GitHub Projects**: Organized task tracking and workflow management
- **🎯 Milestones**: Clear project phases and release planning
- **🔧 Issues**: Detailed bug reports, feature requests, and task documentation
- **🔀 Pull Requests**: Code review process and merge management
- **📝 Documentation**: Comprehensive README, API docs, and code comments

### **Development Process:**
- **Feature Branches**: Isolated development for each feature or fix
- **Code Reviews**: Pull request process ensuring code quality
- **Issue Tracking**: Systematic approach to bug fixes and enhancements
- **Release Management**: Milestone-based versioning and deployment planning

## 🤖 AI-Assisted Development
This project leveraged artificial intelligence to enhance work quality and accelerate learning:
- **Code Quality Enhancement**: AI assistance in identifying best practices and optimization opportunities
- **Learning Acceleration**: AI-guided exploration of Laravel concepts and implementation patterns
- **Problem Solving**: AI support in debugging complex issues and architectural decisions
- **Documentation Improvement**: AI assistance in creating comprehensive and professional documentation
- **Knowledge Expansion**: AI-powered learning of advanced PHP and Laravel techniques