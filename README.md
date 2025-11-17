# Money PointDashboard

This is a standalone Laravel application for managing Money Point operations.

## Features

- **Shift Management**: Open, close, and verify teller shifts
- **Transaction Processing**: Handle deposits and withdrawals
- **Account Management**: Manage cash and float accounts
- **Float Providers**: Configure mobile money providers (M-Pesa, Tigo Pesa, etc.)
- **Reports**: Generate comprehensive reports (Shift Summary, Transactions, Float Balance, Variance, Daily Summary, Teller Performance)
- **Dashboard**: Real-time statistics and alerts

## Installation

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Configuration**
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=money_point
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   php artisan permission:cache-reset
   php artisan permission:create-permission "View Money Point Module"
   php artisan permission:create-permission "View Shifts"
   php artisan permission:create-permission "Open Shifts"
   php artisan permission:create-permission "Submit Shifts"
   php artisan permission:create-permission "Verify Shifts"
   php artisan permission:create-permission "View Accounts"
   php artisan permission:create-permission "Create Accounts"
   php artisan permission:create-permission "View Money Point Transactions"
   php artisan permission:create-permission "Create Withdrawals"
   php artisan permission:create-permission "Create Deposits"
   php artisan permission:create-permission "View Money Point Reports"
   ```

5. **Seed Float Providers**
   ```bash
   php artisan db:seed --class=FloatProvidersTableSeeder
   ```

6. **Create Admin User**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   $user = \App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => bcrypt('password'),
       'status' => 1
   ]);
   $user->assignRole('admin');
   $user->givePermissionTo([
       'View Money Point Module',
       'View Shifts',
       'Open Shifts',
       'Submit Shifts',
       'Verify Shifts',
       'View Accounts',
       'Create Accounts',
       'View Money Point Transactions',
       'Create Withdrawals',
       'Create Deposits',
       'View Money Point Reports'
   ]);
   ```

7. **Link Storage**
   ```bash
   php artisan storage:link
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Project Structure

```
money-point/
├── app/
│   ├── Http/Controllers/
│   │   └── MoneyPointController.php
│   ├── Models/
│   │   ├── Account.php
│   │   ├── Allocation.php
│   │   ├── FloatProvider.php
│   │   ├── MoneyPointTransaction.php
│   │   ├── TellerShift.php
│   │   └── TransactionLine.php
│   ├── Services/
│   │   └── AccountingService.php
│   └── Helpers/
│       └── helpers.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_16_231300_create_accounts_table.php
│   │   ├── 2025_11_16_231301_create_teller_shifts_table.php
│   │   ├── 2025_11_16_231302_create_money_point_transactions_table.php
│   │   ├── 2025_11_16_231303_create_transaction_lines_table.php
│   │   ├── 2025_11_16_231304_create_allocations_table.php
│   │   └── 2025_11_16_233221_create_float_providers_table.php
│   └── seeders/
│       └── FloatProvidersTableSeeder.php
└── resources/views/money-point/
    ├── accounts/
    ├── float-providers/
    ├── reports/
    ├── shifts/
    └── transactions/
```

## Key Models

- **TellerShift**: Represents a teller's working shift
- **MoneyPointTransaction**: All financial transactions
- **Account**: Cash and float accounts for tellers
- **FloatProvider**: Mobile money providers (M-Pesa, Tigo Pesa, etc.)
- **TransactionLine**: Double-entry accounting lines
- **Allocation**: Initial allocations when opening shifts

## Routes

All routes are prefixed with `/money-point`:
- `/money-point` - Dashboard
- `/money-point/shifts` - Shift management
- `/money-point/transactions` - Transaction processing
- `/money-point/accounts` - Account management
- `/money-point/float-providers` - Provider configuration
- `/money-point/reports` - Report generation

## Permissions

The application uses Spatie Laravel Permission package. Key permissions:
- View Money Point Module
- View/Open/Submit/Verify Shifts
- View/Create Accounts
- View Money Point Transactions
- Create Withdrawals/Deposits
- View Money Point Reports

## Notes

- This is a standalone replication of the Money Point module
- All business logic is contained in `AccountingService`
- The application uses double-entry accounting principles
- Float accounts use negative balances (system perspective)
- Cash accounts use positive balances

## Support

For issues or questions, refer to the main josesacos project documentation.
# money-point-dash
