# Database Documentation

## Database Schema Overview

This document provides a comprehensive overview of the MarketCore API database structure, including all tables, relationships, and constraints.

## Entity Relationship Diagram

### Visual Representation
![Database Schema](https://raw.githubusercontent.com/vinifen/marketcore-api/refs/heads/refactor/43/organize-files/docs/database/marketcore-api-dbdiagram-io.png)

### Interactive Diagram
You can visualize and edit the complete database schema using [dbdiagram.io](https://dbdiagram.io/) with the code provided in `schema.dbml`.

**How to use:**
1. Copy the content from `schema.dbml`
2. Paste it into [dbdiagram.io](https://dbdiagram.io/)
3. View, edit, and export the diagram as needed

### Key Entities

- **Users**: Authentication and user management with role-based access control
- **Addresses**: User shipping and billing addresses
- **Categories**: Product categorization system
- **Products**: Core marketplace items with inventory management
- **Discounts**: Product-specific discount system
- **Carts & Cart Items**: Shopping cart functionality
- **Orders & Order Items**: Order processing and fulfillment
- **Coupons**: Order-level discount codes

## Relationships Summary

- **One-to-Many**: User → Addresses, Category → Products, Product → Discounts, Cart → Cart Items, Order → Order Items
- **One-to-One**: User ↔ Cart
- **Many-to-One**: Order → User, Order → Address, Order → Coupon, Cart Item → Product, Order Item → Product

## Role-Based Access Control

The system implements three user roles:
- **CLIENT**: Default role for customers
- **MODERATOR**: Can manage products and orders
- **ADMIN**: Full system access including user role management

## Soft Deletes

Most entities implement soft deletes for data integrity:
- users, addresses, products, discounts, carts, cart_items, coupons, orders, order_items

## Database Features

- **Foreign Key Constraints**: Ensuring data integrity
- **Unique Constraints**: Email uniqueness, cart per user, coupon codes
- **Enum Types**: User roles, order status
- **Decimal Precision**: Proper handling of monetary values
- **Timestamps**: Created/updated tracking on all entities
