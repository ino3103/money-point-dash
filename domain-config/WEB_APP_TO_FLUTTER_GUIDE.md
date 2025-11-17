# Complete Web Application Guide for Flutter Developer

This document explains the entire web application structure, features, and how to implement them in Flutter.

## Table of Contents

1. [Application Overview](#application-overview)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Complete Feature List](#complete-feature-list)
4. [Data Models](#data-models)
5. [API Endpoints Mapping](#api-endpoints-mapping)
6. [Flutter Implementation Guide](#flutter-implementation-guide)

---

## Application Overview

**Money Point** is a financial transaction management system for organizations. Each organization has its own separate deployment with its own database.

### Key Concepts

- **Shifts**: Teller work sessions (Open → Active → Submitted → Verified)
- **Accounts**: Financial accounts (Cash, Bank, Mobile Money, etc.)
- **Transactions**: Money movements (Withdrawals, Deposits)
- **Float Providers**: Sources of money (Cash, Bank, M-Pesa, etc.)
- **Reports**: Various financial reports and summaries

---

## User Roles & Permissions

### 1. Super Admin
**Has ALL permissions** - Can do everything in the system.

**Permissions Include:**
- All user management (create, edit, delete, activate/deactivate users)
- All role management (create, edit, delete roles, assign permissions)
- All settings management
- All Money Point operations
- All reports access

### 2. Treasurer
**Can view and verify operations** - Oversees teller activities.

**Permissions:**
- ✅ View Money Point Module
- ✅ View Shifts
- ✅ Verify Shifts (approve submitted shifts)
- ✅ View Accounts
- ✅ View Money Point Transactions
- ✅ View Ledger
- ✅ View Money Point Reports
- ✅ Edit Own Profile
- ✅ Change Password

**Cannot:**
- ❌ Create/Open Shifts
- ❌ Create Transactions
- ❌ Manage Users/Roles
- ❌ Manage Settings

### 3. Teller
**Operational role** - Performs daily transactions.

**Permissions:**
- ✅ View Money Point Module
- ✅ View Shifts
- ✅ Open Shifts (start new shift)
- ✅ Submit Shifts (end shift)
- ✅ View Accounts
- ✅ View Money Point Transactions
- ✅ Create Withdrawals
- ✅ Create Deposits
- ✅ Edit Own Profile
- ✅ Change Password

**Cannot:**
- ❌ Verify Shifts
- ❌ View Reports (except own shift data)
- ❌ Manage Users/Roles
- ❌ Manage Settings

---

## Complete Feature List

### 1. Authentication & Profile

#### Login
- Email/Username + Password authentication
- Session management
- Login history tracking

#### Profile Management
- View profile information
- Edit personal details (name, email, phone, gender, username)
- Change password
- View login history (IP address, user agent, login time)

**Web Routes:**
- `GET /login` - Login page
- `POST /login` - Login action
- `GET /profile` - Profile page
- `POST /profile/update` - Update profile
- `POST /profile/password-update` - Change password

**Flutter Implementation:**
- Login screen with email/username and password fields
- Profile screen showing user info
- Edit profile form
- Change password form
- Login history list

---

### 2. Dashboard

**Purpose:** Overview of key metrics and recent activities

**Web Route:** `GET /money-point` (redirects from `/dashboard`)

**Shows:**
- Total accounts balance
- Active shifts count
- Recent transactions
- Quick stats

**Flutter Implementation:**
- Dashboard screen with cards showing:
  - Total balance
  - Active shifts
  - Today's transactions count
  - Quick action buttons

---

### 3. Shifts Management

**Purpose:** Manage teller work sessions

#### Shift Lifecycle:
1. **Open** - Teller starts a new shift
2. **Active** - Shift is ongoing, transactions can be made
3. **Submitted** - Teller ends shift, waiting for verification
4. **Verified** - Treasurer verifies and closes shift

#### Features:

**View All Shifts**
- List all shifts with filters (status, date range, teller)
- Show shift details (teller, treasurer, status, dates, amounts)

**Open Shift**
- Teller can open a new shift
- Set opening float (starting cash)
- Assign to treasurer (optional)

**View Shift Details**
- Show shift information
- List all transactions in shift
- Show opening/closing balances
- Show variance (difference between expected and actual)

**Submit Shift**
- Teller submits shift for verification
- Enter closing float (ending cash)
- System calculates variance

**Verify Shift**
- Treasurer verifies submitted shift
- Approve or reject
- Add notes/comments

**Web Routes:**
- `GET /money-point/shifts` - List all shifts
- `GET /money-point/shifts/create` - Open new shift form
- `POST /money-point/shifts` - Create/open shift
- `GET /money-point/shifts/{id}` - View shift details
- `GET /money-point/shifts/{id}/submit` - Submit shift form
- `POST /money-point/shifts/{id}/submit` - Submit shift
- `GET /money-point/shifts/{id}/verify` - Verify shift form
- `POST /money-point/shifts/{id}/verify` - Verify shift

**Flutter Implementation:**
- Shifts list screen with filters
- Open shift screen (form with opening float)
- Shift details screen (show all info and transactions)
- Submit shift screen (form with closing float)
- Verify shift screen (for treasurer - approve/reject)

**Data Model:**
```dart
class TellerShift {
  int id;
  int tellerId;
  String tellerName;
  int? treasurerId;
  String? treasurerName;
  String status; // 'open', 'active', 'submitted', 'verified'
  double openingFloat;
  double? closingFloat;
  double? variance;
  DateTime openedAt;
  DateTime? submittedAt;
  DateTime? verifiedAt;
  String? notes;
}
```

---

### 4. Accounts Management

**Purpose:** Manage financial accounts

#### Features:

**View All Accounts**
- List all accounts (Cash, Bank, M-Pesa, etc.)
- Show account balances
- Show account type

**View Account Ledger**
- Transaction history for specific account
- Filter by date range
- Show running balance
- Export option

**Web Routes:**
- `GET /money-point/accounts` - List all accounts
- `GET /money-point/accounts/{id}/ledger` - View account ledger

**Flutter Implementation:**
- Accounts list screen (show all accounts with balances)
- Account ledger screen (transaction list with filters)

**Data Model:**
```dart
class Account {
  int id;
  String name;
  String type; // 'cash', 'bank', 'mobile_money', etc.
  double balance;
  String? description;
}

class LedgerEntry {
  int id;
  DateTime date;
  String description;
  double debit;
  double credit;
  double balance;
  String transactionType;
}
```

---

### 5. Transactions Management

**Purpose:** Record money movements

#### Transaction Types:

**Withdrawal**
- Money going out
- Select account (from)
- Select float provider (to)
- Enter amount
- Add description/reference

**Deposit**
- Money coming in
- Select float provider (from)
- Select account (to)
- Enter amount
- Add description/reference

#### Features:

**View All Transactions**
- List all transactions
- Filters: Type (withdrawal/deposit), Shift, User, Date Range
- Show transaction details
- Search functionality

**View Transaction Details**
- Show full transaction information
- Show related shift
- Show accounts involved

**Create Withdrawal**
- Form to create withdrawal
- Select account (from)
- Select float provider (to)
- Enter amount
- Add description

**Create Deposit**
- Form to create deposit
- Select float provider (from)
- Select account (to)
- Enter amount
- Add description

**Web Routes:**
- `GET /money-point/transactions` - List all transactions
- `GET /money-point/transactions/{id}` - View transaction details
- `GET /money-point/transactions/withdraw/create` - Create withdrawal form
- `POST /money-point/transactions/withdraw` - Create withdrawal
- `GET /money-point/transactions/deposit/create` - Create deposit form
- `POST /money-point/transactions/deposit` - Create deposit

**Flutter Implementation:**
- Transactions list screen with filters
- Transaction details screen
- Create withdrawal screen (form)
- Create deposit screen (form)

**Data Model:**
```dart
class MoneyPointTransaction {
  int id;
  String type; // 'withdrawal', 'deposit'
  int accountId;
  String accountName;
  int floatProviderId;
  String floatProviderName;
  int shiftId;
  String shiftStatus;
  int userId;
  String userName;
  double amount;
  String? description;
  String? reference;
  DateTime createdAt;
}
```

---

### 6. Float Providers Management

**Purpose:** Manage sources of money (Cash, Bank, M-Pesa, etc.)

#### Features:

**View All Float Providers**
- List all float providers
- Show status (active/inactive)
- Show current balance

**Create Float Provider**
- Add new float provider
- Set name and type

**Update Float Provider**
- Edit float provider details
- Change status (activate/deactivate)

**Toggle Status**
- Activate or deactivate float provider

**Web Routes:**
- `GET /money-point/float-providers` - List float providers
- `POST /money-point/float-providers` - Create float provider
- `PUT /money-point/float-providers` - Update float provider
- `POST /money-point/float-providers/{id}/toggle` - Toggle status

**Flutter Implementation:**
- Float providers list screen
- Create/edit float provider form
- Toggle status action

**Data Model:**
```dart
class FloatProvider {
  int id;
  String name;
  String type; // 'cash', 'bank', 'mobile_money', etc.
  bool isActive;
  double? balance;
}
```

---

### 7. Reports

**Purpose:** Generate various financial reports

#### Available Reports:

**Shift Summary Report**
- Summary of all shifts
- Filter by date range, teller, status
- Show totals and averages

**Transactions Report**
- All transactions in date range
- Filter by type, account, shift
- Export to PDF/Excel

**Float Balance Report**
- Current balances of all float providers
- Historical balance changes

**Variance Report**
- Differences between expected and actual balances
- Identify discrepancies

**Daily Summary Report**
- Daily totals and summaries
- Compare days

**Teller Performance Report**
- Performance metrics per teller
- Transaction counts, amounts
- Shift statistics

**Web Routes:**
- `GET /money-point/reports` - Reports index
- `GET /money-point/reports/shift-summary` - Shift summary report
- `GET /money-point/reports/transactions` - Transactions report
- `GET /money-point/reports/float-balance` - Float balance report
- `GET /money-point/reports/variance` - Variance report
- `GET /money-point/reports/daily-summary` - Daily summary report
- `GET /money-point/reports/teller-performance` - Teller performance report

**Flutter Implementation:**
- Reports menu screen
- Each report screen with filters
- Display report data in tables/charts
- Export functionality (PDF/Excel)

---

### 8. Users Management (Admin Only)

**Purpose:** Manage system users

#### Features:

**View All Users**
- List all users
- Show role, status, last login
- Search and filter

**Create User**
- Add new user
- Set name, email, username, password
- Assign role
- Set status (active/inactive)

**Edit User**
- Update user details
- Change role
- Change status

**Toggle User Status**
- Activate or deactivate user

**Delete User**
- Remove user from system

**Web Routes:**
- `GET /users` - List all users
- `GET /users/create` - Create user form
- `POST /users/store` - Create user
- `GET /users/{user}/edit` - Edit user form
- `PUT /users/{user}` - Update user
- `PUT /users/{user}/toggle-status` - Toggle status
- `DELETE /users/destroy` - Delete user

**Flutter Implementation:**
- Users list screen (admin only)
- Create user form
- Edit user form
- User details screen

**Data Model:**
```dart
class User {
  int id;
  String name;
  String email;
  String username;
  String? phoneNo;
  String? gender;
  String? profilePicture;
  int status; // 1 = active, 0 = inactive
  List<String> roles;
  DateTime? lastLoginAt;
}
```

---

### 9. Roles Management (Admin Only)

**Purpose:** Manage user roles and permissions

#### Features:

**View All Roles**
- List all roles
- Show permission count

**Create Role**
- Add new role
- Assign permissions

**Edit Role**
- Update role name
- Modify permissions

**Delete Role**
- Remove role

**Web Routes:**
- `GET /roles` - List all roles
- `GET /roles/create` - Create role form
- `POST /roles/store` - Create role
- `GET /roles/{id}/edit` - Edit role form
- `PUT /roles` - Update role
- `DELETE /roles/destroy` - Delete role

**Flutter Implementation:**
- Roles list screen (admin only)
- Create/edit role form with permission checkboxes

**Data Model:**
```dart
class Role {
  int id;
  String name;
  List<Permission> permissions;
}

class Permission {
  int id;
  String name;
  String category; // 'Users', 'Roles', 'Profile', 'Settings', 'Money Point'
}
```

---

### 10. Settings Management (Admin Only)

**Purpose:** Configure system settings

#### Features:

**System Settings**
- Site name, logo
- Currency, date format, timezone
- Email settings
- SMS settings

**Email Settings**
- SMTP configuration
- Test email functionality

**SMS Settings**
- SMS gateway configuration
- Test SMS functionality

**Web Routes:**
- `GET /settings` - Settings page
- `PUT /settings/update` - Update settings
- `GET /email-settings` - Email settings page
- `POST /email-settings/update` - Update email settings
- `POST /send-test-email` - Send test email
- `GET /sms-settings` - SMS settings page
- `POST /sms-settings/update` - Update SMS settings
- `POST /send-sms` - Send test SMS

**Flutter Implementation:**
- Settings screen (admin only)
- Email settings form
- SMS settings form
- Test email/SMS buttons

---

## API Endpoints Mapping

### Base URL Structure
```
Web Route: /money-point/shifts
API Route: /api/v1/money-point/shifts
```

### Complete API Endpoints

#### Authentication
```
POST   /api/v1/auth/login          - Login
POST   /api/v1/auth/logout         - Logout
GET    /api/v1/auth/user           - Get current user
```

#### Dashboard
```
GET    /api/v1/money-point/dashboard - Dashboard data
```

#### Shifts
```
GET    /api/v1/money-point/shifts           - List shifts
GET    /api/v1/money-point/shifts/{id}     - Get shift details
POST   /api/v1/money-point/shifts           - Open shift
POST   /api/v1/money-point/shifts/{id}/submit - Submit shift
POST   /api/v1/money-point/shifts/{id}/verify - Verify shift
```

#### Accounts
```
GET    /api/v1/money-point/accounts         - List accounts
GET    /api/v1/money-point/accounts/{id}/ledger - Account ledger
```

#### Transactions
```
GET    /api/v1/money-point/transactions           - List transactions
GET    /api/v1/money-point/transactions/{id}     - Get transaction
POST   /api/v1/money-point/transactions/withdraw - Create withdrawal
POST   /api/v1/money-point/transactions/deposit  - Create deposit
```

#### Float Providers
```
GET    /api/v1/money-point/float-providers        - List providers
POST   /api/v1/money-point/float-providers        - Create provider
PUT    /api/v1/money-point/float-providers/{id}   - Update provider
POST   /api/v1/money-point/float-providers/{id}/toggle - Toggle status
```

#### Reports
```
GET    /api/v1/money-point/reports/shift-summary      - Shift summary
GET    /api/v1/money-point/reports/transactions       - Transactions report
GET    /api/v1/money-point/reports/float-balance      - Float balance
GET    /api/v1/money-point/reports/variance            - Variance report
GET    /api/v1/money-point/reports/daily-summary       - Daily summary
GET    /api/v1/money-point/reports/teller-performance  - Teller performance
```

#### Users (Admin Only)
```
GET    /api/v1/users              - List users
GET    /api/v1/users/{id}         - Get user
POST   /api/v1/users              - Create user
PUT    /api/v1/users/{id}         - Update user
PUT    /api/v1/users/{id}/toggle-status - Toggle status
DELETE /api/v1/users/{id}        - Delete user
```

#### Roles (Admin Only)
```
GET    /api/v1/roles              - List roles
GET    /api/v1/roles/{id}        - Get role
POST   /api/v1/roles             - Create role
PUT    /api/v1/roles/{id}        - Update role
DELETE /api/v1/roles/{id}        - Delete role
```

#### Settings (Admin Only)
```
GET    /api/v1/settings           - Get settings
PUT    /api/v1/settings           - Update settings
GET    /api/v1/settings/email     - Get email settings
PUT    /api/v1/settings/email     - Update email settings
POST   /api/v1/settings/email/test - Test email
GET    /api/v1/settings/sms       - Get SMS settings
PUT    /api/v1/settings/sms       - Update SMS settings
POST   /api/v1/settings/sms/test  - Test SMS
```

#### Profile
```
GET    /api/v1/profile             - Get profile
PUT    /api/v1/profile             - Update profile
PUT    /api/v1/profile/password    - Change password
GET    /api/v1/profile/login-history - Get login history
```

---

## Flutter Implementation Guide

### 1. App Structure

```
lib/
├── main.dart
├── models/
│   ├── user.dart
│   ├── shift.dart
│   ├── account.dart
│   ├── transaction.dart
│   ├── float_provider.dart
│   └── role.dart
├── services/
│   ├── api_service.dart
│   ├── auth_service.dart
│   ├── domain_service.dart
│   ├── shift_service.dart
│   ├── transaction_service.dart
│   └── report_service.dart
├── screens/
│   ├── domain_selection_screen.dart
│   ├── login_screen.dart
│   ├── dashboard_screen.dart
│   ├── shifts/
│   │   ├── shifts_list_screen.dart
│   │   ├── shift_details_screen.dart
│   │   ├── open_shift_screen.dart
│   │   ├── submit_shift_screen.dart
│   │   └── verify_shift_screen.dart
│   ├── transactions/
│   │   ├── transactions_list_screen.dart
│   │   ├── transaction_details_screen.dart
│   │   ├── create_withdrawal_screen.dart
│   │   └── create_deposit_screen.dart
│   ├── accounts/
│   │   ├── accounts_list_screen.dart
│   │   └── account_ledger_screen.dart
│   ├── reports/
│   │   ├── reports_menu_screen.dart
│   │   ├── shift_summary_report_screen.dart
│   │   └── ...
│   └── profile/
│       ├── profile_screen.dart
│       └── edit_profile_screen.dart
├── widgets/
│   ├── permission_wrapper.dart
│   ├── loading_indicator.dart
│   └── ...
└── utils/
    ├── permissions.dart
    └── constants.dart
```

### 2. Permission Checking

Create `lib/utils/permissions.dart`:

```dart
class Permissions {
  static const String VIEW_MONEY_POINT_MODULE = 'View Money Point Module';
  static const String VIEW_SHIFTS = 'View Shifts';
  static const String OPEN_SHIFTS = 'Open Shifts';
  static const String SUBMIT_SHIFTS = 'Submit Shifts';
  static const String VERIFY_SHIFTS = 'Verify Shifts';
  static const String VIEW_ACCOUNTS = 'View Accounts';
  static const String VIEW_TRANSACTIONS = 'View Money Point Transactions';
  static const String CREATE_WITHDRAWALS = 'Create Withdrawals';
  static const String CREATE_DEPOSITS = 'Create Deposits';
  static const String VIEW_REPORTS = 'View Money Point Reports';
  static const String VIEW_USERS = 'View Users';
  static const String CREATE_USERS = 'Create Users';
  static const String EDIT_USERS = 'Edit Users';
  static const String DELETE_USERS = 'Delete Users';
  static const String VIEW_ROLES = 'View Roles';
  static const String VIEW_SETTINGS = 'View Settings Module';
  // ... add all permissions
}

class PermissionChecker {
  static bool hasPermission(List<String> userPermissions, String permission) {
    return userPermissions.contains(permission);
  }
  
  static bool isAdmin(List<String> userRoles) {
    return userRoles.contains('Super Admin');
  }
  
  static bool isTreasurer(List<String> userRoles) {
    return userRoles.contains('Treasurer');
  }
  
  static bool isTeller(List<String> userRoles) {
    return userRoles.contains('Teller');
  }
}
```

### 3. Permission Wrapper Widget

Create `lib/widgets/permission_wrapper.dart`:

```dart
import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../utils/permissions.dart';

class PermissionWrapper extends StatelessWidget {
  final String permission;
  final Widget child;
  final Widget? fallback;

  PermissionWrapper({
    required this.permission,
    required this.child,
    this.fallback,
  });

  @override
  Widget build(BuildContext context) {
    // Get user permissions from auth service
    final user = AuthService.currentUser;
    
    if (user == null) {
      return fallback ?? SizedBox.shrink();
    }
    
    final hasPermission = PermissionChecker.hasPermission(
      user.permissions,
      permission,
    );
    
    if (hasPermission || PermissionChecker.isAdmin(user.roles)) {
      return child;
    }
    
    return fallback ?? SizedBox.shrink();
  }
}
```

### 4. Navigation Structure

**Bottom Navigation (Main App):**
- Dashboard
- Shifts
- Transactions
- Accounts
- Reports (if has permission)
- Profile

**Drawer Menu (Additional):**
- Users (Admin only)
- Roles (Admin only)
- Settings (Admin only)
- Logout

### 5. Key Implementation Points

#### Role-Based UI
- Show/hide features based on user role
- Use PermissionWrapper widget
- Check permissions before API calls

#### Shift Workflow
- Teller: Can open and submit shifts
- Treasurer: Can verify shifts
- Show appropriate buttons based on shift status

#### Transaction Creation
- Only Tellers can create transactions
- Must be within an active shift
- Validate account balances

#### Reports Access
- Treasurers and Admins can view reports
- Tellers see limited data

### 6. Data Flow Example

**Opening a Shift:**
```
1. User taps "Open Shift" button
2. Check permission: "Open Shifts"
3. Show form (opening float input)
4. Call API: POST /api/v1/money-point/shifts
5. On success: Navigate to shift details
6. Update shifts list
```

**Creating Withdrawal:**
```
1. User taps "Create Withdrawal"
2. Check permission: "Create Withdrawals"
3. Check if active shift exists
4. Show form (account, float provider, amount)
5. Validate amount (check account balance)
6. Call API: POST /api/v1/money-point/transactions/withdraw
7. On success: Show success message, refresh transactions list
```

---

## Summary

### Must-Have Features in Flutter App:

1. ✅ Domain selection/login flow
2. ✅ Role-based navigation and permissions
3. ✅ Dashboard with key metrics
4. ✅ Shifts management (open, view, submit, verify)
5. ✅ Transactions (create withdrawal/deposit, view list)
6. ✅ Accounts (view list, view ledger)
7. ✅ Float providers management
8. ✅ Reports (all 6 report types)
9. ✅ Profile management
10. ✅ Users management (admin)
11. ✅ Roles management (admin)
12. ✅ Settings (admin)

### Important Notes:

- **Permissions are critical** - Always check before showing features
- **Shift status matters** - Transactions can only be created in active shifts
- **Role determines access** - Super Admin > Treasurer > Teller
- **Each organization is separate** - Domain selection is first step
- **API uses Bearer token** - Store token securely
- **Handle errors gracefully** - Network errors, validation errors, permission errors

---

## Testing Checklist

- [ ] Domain selection works
- [ ] Login with different roles works
- [ ] Permissions are enforced correctly
- [ ] Teller can open shift
- [ ] Teller can create transactions
- [ ] Teller can submit shift
- [ ] Treasurer can verify shift
- [ ] Reports are accessible to correct roles
- [ ] Admin features are hidden from non-admins
- [ ] Profile update works
- [ ] Password change works
- [ ] All API endpoints work correctly

---

This guide provides everything needed to build the Flutter app matching the web application functionality!

