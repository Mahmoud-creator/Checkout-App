# Checkout App

A Laravel + Inertia.js + Vue.js checkout application.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite

## Running the Project Locally

1. **Clone the repository**

   ```bash
   git clone https://github.com/Mahmoud-creator/Checkout-App.git
   cd checkout-app
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install
   ```

4. **Copy the environment file**

   ```bash
   cp .env.example .env
   ```

   > No need to configure anything, as SQLite is used by default.

5. **Generate application key**

   ```bash
   php artisan key:generate
   ```

6. **Run migrations and seed the database**

   ```bash
   php artisan migrate --seed
   ```

   This will create the necessary tables and populate them with sample data, including the default user:

   - **Email:** `test@example.com`
   - **Password:** `password`

7. **Start the project**

   ```bash
   composer run dev
   ```

   This command will:
   - Start the Laravel server
   - Start the queue worker
   - Start the log tailing
   - Start Vite for frontend assets

8. **Access the app**

   Open [http://localhost:8000](http://localhost:8000) in your browser.

## Testing the Functionality

- **Log in** using the default user:  
  Email: `test@example.com`  
  Password: `password`
- **Browse products** on the dashboard.
- **Add products to your cart.**
- **Update quantities or remove items** from your cart.
- **Checkout Order** from the cart page.
- **View your orders** on the orders page.
- **Cancel an order** (if not shipped) from the orders page.

## Troubleshooting

- If you need to reset the database, run:

  ```bash
  php artisan migrate:fresh --seed
  ```

- For frontend issues, just re-run `composer run dev` and refresh the browser.

---

> **Note:**  
> The `.env.example` is pre-configured for SQLite. No further configuration is required.
