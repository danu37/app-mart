# TechStore Complete E-Commerce Flow — Implementation Plan

## Current State Analysis

- **Users table**: No `role` column — everyone is the same
- **tbl_pelanggan**: Standalone, NOT linked to `users`
- **Cart**: Frontend-only (Vue ref), lost on refresh
- **Checkout**: Frontend-only invoice, no backend transaction created
- **Dashboard**: Single dashboard for all, shows hardcoded stats
- **Routes**: No role-based protection, all authenticated users see admin pages

## Changes Required

### Phase 1: Database — Add role to users, link pelanggan to users

1. New migration: add `role` column to `users` (default: 'customer')
2. New migration: add `user_id` FK to `tbl_pelanggan` (nullable)
3. New migration: add `status` column to `tbl_transaksi` (pending/paid/shipped/done/cancelled)

### Phase 2: Models — Update relationships

1. User model: add `role`, `pelanggan()` relationship, `isAdmin()` helper
2. Pelanggan model: add `user()` relationship
3. Transaksi model: add `detailTransaksi()` relationship, add `status`
4. New model: DetailTransaksi

### Phase 3: Middleware — Role-based access

1. New middleware: `AdminMiddleware` (check role === 'admin')
2. Register in bootstrap/app.php

### Phase 4: Controllers

1. Update RegisteredUserController: auto-create pelanggan on register
2. Update AuthenticatedSessionController: redirect based on role
3. New: Admin/ProductController (CRUD for products)
4. New: Admin/TransactionController (manage all orders)
5. New: Customer/CartController (server-side cart via session)
6. New: Customer/CheckoutController (create real transaction)
7. New: Customer/OrderController (view own orders)

### Phase 5: Routes

1. Admin route group with admin middleware
2. Customer route group with auth middleware
3. Keep existing public routes

### Phase 6: Frontend Pages

1. Admin Dashboard (with real stats from DB)
2. Admin Products CRUD pages
3. Admin Orders management page
4. Customer Dashboard (order history)
5. Customer Checkout page
6. Customer Order detail/confirmation page
7. Update Welcome.vue checkout to redirect to login if guest

### Phase 7: Seeder

1. Update seeder: create admin user, customer users, link pelanggan
