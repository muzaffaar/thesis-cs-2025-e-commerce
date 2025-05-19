# Single-Tenant E-Commerce API with PayPal Integration

## 1. Application Overview
This project is a single-tenant e-commerce API built with Laravel, designed to support a single merchant. It includes features for product management, customer shopping, and PayPal payment integration. The API serves as the backend for web or mobile applications.

---

## 2. Technology Stack
- **Backend Framework**: Laravel (PHP)
- **Database**: MySQL
- **Web Server**: Nginx
- **Authentication**: Sanctum (API token-based authentication)
- **Payment Gateway**: PayPal (REST API integration)
- **Caching**: Optional, using Redis for performance optimization.

---

## 3. Core Features

### Merchant Features
- **Product Management**:
    - Manage product catalog (add, update, delete).
    - Organize products into categories with hierarchical support.
    - Upload and manage product images.

- **Order Management**:
    - View and filter orders based on their statuses (e.g., pending, paid, shipped).
    - Update the status of orders to track their progress.
    - Access detailed information about each order, including items, quantities, and total price.

- **Reporting**:
    - Generate sales reports for specified periods.
    - Analyze product performance (e.g., best-selling items).

---

### Customer Features
- **Product Browsing**:
    - View product catalog with support for search and filters.
    - Browse products by categories.

- **Shopping Cart**:
    - Add products to a cart.
    - Update or remove items from the cart.
    - Persistent cart functionality for logged-in users.

- **Checkout**:
    - Complete payments securely through PayPal.
    - Receive order confirmation upon successful payment.

- **Order History**:
    - View a list of previous orders, including order details and statuses.

---

### Payment Integration (PayPal)
- **Seamless Payment Flow**:
    - Generate payment links for customers to process transactions.
    - Use PayPal webhooks to automatically update payment and order statuses.

- **Error Handling**:
    - Handle payment failures or cancellations gracefully.
    - Notify customers of the status of their payments.
