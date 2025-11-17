# Flutter Mobile App Integration Guide

This guide explains how to integrate your Flutter mobile app with the Money Point domain configuration system.

## Overview

Each organization has its own separate deployment:
- **Main Domain**: `moneypoint.com` (for domain discovery)
- **Subdomains**: `ino.moneypoint.com`, `test.moneypoint.com`, etc. (actual app instances)
- **API Base**: `https://{domain}.moneypoint.com/api/v1`

## Architecture Flow

```
1. User opens app
2. App asks for organization code (e.g., "ino")
3. App validates domain via main domain API
4. App gets subdomain API URL
5. All subsequent API calls go to subdomain
```

## Step 1: Domain Discovery

### 1.1 Add Dependencies

Add to `pubspec.yaml`:

```yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0
```

### 1.2 Create Domain Service

Create `lib/services/domain_service.dart`:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class DomainService {
  // Main domain base URL for discovery
  static const String mainDomainBaseUrl = 'https://moneypoint.com/domain-config';
  
  /// Validate domain and get API URL
  static Future<DomainInfo?> validateDomain(String domainCode) async {
    try {
      final url = Uri.parse('$mainDomainBaseUrl/api?action=validate&domain=$domainCode');
      final response = await http.get(url);
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          return DomainInfo.fromJson(data['data']);
        }
      }
      return null;
    } catch (e) {
      print('Error validating domain: $e');
      return null;
    }
  }
  
  /// List all available domains
  static Future<List<DomainInfo>> listDomains() async {
    try {
      final url = Uri.parse('$mainDomainBaseUrl/api?action=list');
      final response = await http.get(url);
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          final List<dynamic> domains = data['data'];
          return domains.map((d) => DomainInfo.fromJson(d)).toList();
        }
      }
      return [];
    } catch (e) {
      print('Error listing domains: $e');
      return [];
    }
  }
  
  /// Save domain configuration locally
  static Future<void> saveDomainInfo(DomainInfo domainInfo) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('domain_code', domainInfo.domain);
    await prefs.setString('api_base_url', domainInfo.apiUrl);
    await prefs.setString('web_url', domainInfo.webUrl);
  }
  
  /// Get saved domain info
  static Future<DomainInfo?> getSavedDomainInfo() async {
    final prefs = await SharedPreferences.getInstance();
    final domainCode = prefs.getString('domain_code');
    final apiBaseUrl = prefs.getString('api_base_url');
    final webUrl = prefs.getString('web_url');
    
    if (domainCode != null && apiBaseUrl != null) {
      return DomainInfo(
        domain: domainCode,
        name: domainCode,
        apiUrl: apiBaseUrl,
        webUrl: webUrl ?? '',
      );
    }
    return null;
  }
  
  /// Clear saved domain info
  static Future<void> clearDomainInfo() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('domain_code');
    await prefs.remove('api_base_url');
    await prefs.remove('web_url');
  }
}

class DomainInfo {
  final String domain;
  final String? name;
  final String? description;
  final String apiUrl;
  final String webUrl;
  
  DomainInfo({
    required this.domain,
    this.name,
    this.description,
    required this.apiUrl,
    required this.webUrl,
  });
  
  factory DomainInfo.fromJson(Map<String, dynamic> json) {
    return DomainInfo(
      domain: json['domain'],
      name: json['name'],
      description: json['description'],
      apiUrl: json['api_url'],
      webUrl: json['web_url'],
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'domain': domain,
      'name': name,
      'description': description,
      'api_url': apiUrl,
      'web_url': webUrl,
    };
  }
}
```

## Step 2: API Service

Create `lib/services/api_service.dart`:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'domain_service.dart';

class ApiService {
  static String? _baseUrl;
  static String? _authToken;
  
  /// Initialize API service with domain
  static Future<void> initialize() async {
    final domainInfo = await DomainService.getSavedDomainInfo();
    if (domainInfo != null) {
      _baseUrl = domainInfo.apiUrl;
    }
    
    // Load saved token
    final prefs = await SharedPreferences.getInstance();
    _authToken = prefs.getString('auth_token');
  }
  
  /// Set base URL (from domain info)
  static void setBaseUrl(String baseUrl) {
    _baseUrl = baseUrl;
  }
  
  /// Set authentication token
  static Future<void> setAuthToken(String token) async {
    _authToken = token;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }
  
  /// Clear authentication token
  static Future<void> clearAuthToken() async {
    _authToken = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
  
  /// Make authenticated GET request
  static Future<Map<String, dynamic>?> get(String endpoint) async {
    if (_baseUrl == null) {
      throw Exception('API base URL not set. Please configure domain first.');
    }
    
    final url = Uri.parse('$_baseUrl/$endpoint');
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (_authToken != null) 'Authorization': 'Bearer $_authToken',
    };
    
    try {
      final response = await http.get(url, headers: headers);
      return _handleResponse(response);
    } catch (e) {
      print('GET Error: $e');
      return null;
    }
  }
  
  /// Make authenticated POST request
  static Future<Map<String, dynamic>?> post(
    String endpoint,
    Map<String, dynamic> data,
  ) async {
    if (_baseUrl == null) {
      throw Exception('API base URL not set. Please configure domain first.');
    }
    
    final url = Uri.parse('$_baseUrl/$endpoint');
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (_authToken != null) 'Authorization': 'Bearer $_authToken',
    };
    
    try {
      final response = await http.post(
        url,
        headers: headers,
        body: json.encode(data),
      );
      return _handleResponse(response);
    } catch (e) {
      print('POST Error: $e');
      return null;
    }
  }
  
  /// Handle API response
  static Map<String, dynamic>? _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return json.decode(response.body);
    } else {
      print('API Error: ${response.statusCode} - ${response.body}');
      return null;
    }
  }
}
```

## Step 3: Authentication Service

Create `lib/services/auth_service.dart`:

```dart
import 'api_service.dart';

class AuthService {
  /// Login user
  static Future<AuthResult> login(String email, String password) async {
    final response = await ApiService.post('/auth/login', {
      'email': email,
      'password': password,
    });
    
    if (response != null && response['success'] == true) {
      final token = response['data']['token'];
      final user = response['data']['user'];
      
      await ApiService.setAuthToken(token);
      
      return AuthResult(
        success: true,
        token: token,
        user: user,
      );
    }
    
    return AuthResult(
      success: false,
      message: response?['message'] ?? 'Login failed',
    );
  }
  
  /// Logout user
  static Future<void> logout() async {
    await ApiService.post('/auth/logout', {});
    await ApiService.clearAuthToken();
  }
  
  /// Get current user
  static Future<Map<String, dynamic>?> getCurrentUser() async {
    return await ApiService.get('/auth/user');
  }
}

class AuthResult {
  final bool success;
  final String? token;
  final Map<String, dynamic>? user;
  final String? message;
  
  AuthResult({
    required this.success,
    this.token,
    this.user,
    this.message,
  });
}
```

## Step 4: Domain Selection Screen

Create `lib/screens/domain_selection_screen.dart`:

```dart
import 'package:flutter/material.dart';
import '../services/domain_service.dart';
import '../services/api_service.dart';
import 'login_screen.dart';

class DomainSelectionScreen extends StatefulWidget {
  @override
  _DomainSelectionScreenState createState() => _DomainSelectionScreenState();
}

class _DomainSelectionScreenState extends State<DomainSelectionScreen> {
  final _formKey = GlobalKey<FormState>();
  final _domainController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;
  List<DomainInfo> _availableDomains = [];

  @override
  void initState() {
    super.initState();
    _loadAvailableDomains();
  }

  Future<void> _loadAvailableDomains() async {
    final domains = await DomainService.listDomains();
    setState(() {
      _availableDomains = domains;
    });
  }

  Future<void> _validateAndProceed() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final domainCode = _domainController.text.trim().toLowerCase();
    final domainInfo = await DomainService.validateDomain(domainCode);

    if (domainInfo != null) {
      // Save domain info
      await DomainService.saveDomainInfo(domainInfo);
      
      // Initialize API service with domain
      ApiService.setBaseUrl(domainInfo.apiUrl);
      
      // Navigate to login screen
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => LoginScreen()),
      );
    } else {
      setState(() {
        _errorMessage = 'Domain not found or inactive. Please check your organization code.';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Select Organization'),
      ),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'Enter your organization code',
                style: Theme.of(context).textTheme.headlineSmall,
              ),
              SizedBox(height: 8),
              Text(
                'Each organization has its own secure server.',
                style: Theme.of(context).textTheme.bodyMedium,
              ),
              SizedBox(height: 24),
              TextFormField(
                controller: _domainController,
                decoration: InputDecoration(
                  labelText: 'Organization Code',
                  hintText: 'e.g., ino',
                  prefixText: 'https://',
                  suffixText: '.moneypoint.com',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter organization code';
                  }
                  if (!RegExp(r'^[a-z0-9-]+$').hasMatch(value.toLowerCase())) {
                    return 'Invalid format';
                  }
                  return null;
                },
              ),
              if (_errorMessage != null) ...[
                SizedBox(height: 16),
                Container(
                  padding: EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Text(
                    _errorMessage!,
                    style: TextStyle(color: Colors.red.shade700),
                  ),
                ),
              ],
              SizedBox(height: 24),
              ElevatedButton(
                onPressed: _isLoading ? null : _validateAndProceed,
                child: _isLoading
                    ? CircularProgressIndicator()
                    : Text('Continue'),
              ),
              if (_availableDomains.isNotEmpty) ...[
                SizedBox(height: 32),
                Text(
                  'Available Organizations:',
                  style: Theme.of(context).textTheme.titleMedium,
                ),
                SizedBox(height: 8),
                ..._availableDomains.map((domain) => Card(
                  child: ListTile(
                    title: Text(domain.name ?? domain.domain),
                    subtitle: domain.description != null
                        ? Text(domain.description!)
                        : null,
                    trailing: Icon(Icons.arrow_forward_ios),
                    onTap: () {
                      _domainController.text = domain.domain;
                      _validateAndProceed();
                    },
                  ),
                )),
              ],
            ],
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _domainController.dispose();
    super.dispose();
  }
}
```

## Step 5: Login Screen

Create `lib/screens/login_screen.dart`:

```dart
import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';
import 'home_screen.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;
  bool _obscurePassword = true;

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await AuthService.login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    if (result.success) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => HomeScreen()),
      );
    } else {
      setState(() {
        _errorMessage = result.message ?? 'Login failed';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Login'),
      ),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'Welcome Back',
                style: Theme.of(context).textTheme.headlineMedium,
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 32),
              TextFormField(
                controller: _emailController,
                decoration: InputDecoration(
                  labelText: 'Email',
                  border: OutlineInputBorder(),
                ),
                keyboardType: TextInputType.emailAddress,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter email';
                  }
                  if (!value.contains('@')) {
                    return 'Please enter valid email';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _passwordController,
                decoration: InputDecoration(
                  labelText: 'Password',
                  border: OutlineInputBorder(),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscurePassword ? Icons.visibility : Icons.visibility_off,
                    ),
                    onPressed: () {
                      setState(() {
                        _obscurePassword = !_obscurePassword;
                      });
                    },
                  ),
                ),
                obscureText: _obscurePassword,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter password';
                  }
                  return null;
                },
              ),
              if (_errorMessage != null) ...[
                SizedBox(height: 16),
                Container(
                  padding: EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Text(
                    _errorMessage!,
                    style: TextStyle(color: Colors.red.shade700),
                  ),
                ),
              ],
              SizedBox(height: 24),
              ElevatedButton(
                onPressed: _isLoading ? null : _login,
                child: _isLoading
                    ? CircularProgressIndicator()
                    : Text('Login'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
```

## Step 6: Initialize App

Update `lib/main.dart`:

```dart
import 'package:flutter/material.dart';
import 'services/api_service.dart';
import 'services/domain_service.dart';
import 'screens/domain_selection_screen.dart';
import 'screens/home_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize API service
  await ApiService.initialize();
  
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Money Point',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: AppInitializer(),
    );
  }
}

class AppInitializer extends StatefulWidget {
  @override
  _AppInitializerState createState() => _AppInitializerState();
}

class _AppInitializerState extends State<AppInitializer> {
  bool _isLoading = true;
  Widget? _initialScreen;

  @override
  void initState() {
    super.initState();
    _checkDomainAndAuth();
  }

  Future<void> _checkDomainAndAuth() async {
    // Check if domain is configured
    final domainInfo = await DomainService.getSavedDomainInfo();
    
    if (domainInfo == null) {
      // No domain configured - show domain selection
      setState(() {
        _initialScreen = DomainSelectionScreen();
        _isLoading = false;
      });
      return;
    }
    
    // Domain configured - initialize API
    ApiService.setBaseUrl(domainInfo.apiUrl);
    
    // Check if user is logged in
    final user = await AuthService.getCurrentUser();
    
    if (user != null) {
      // User is logged in - go to home
      setState(() {
        _initialScreen = HomeScreen();
        _isLoading = false;
      });
    } else {
      // User not logged in - go to login
      setState(() {
        _initialScreen = LoginScreen();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        body: Center(
          child: CircularProgressIndicator(),
        ),
      );
    }
    
    return _initialScreen ?? DomainSelectionScreen();
  }
}
```

## Step 7: API Usage Examples

### Get Dashboard Data

```dart
final response = await ApiService.get('/money-point/dashboard');
if (response != null && response['success'] == true) {
  final dashboardData = response['data'];
  // Use dashboard data
}
```

### Create Transaction

```dart
final response = await ApiService.post('/money-point/transactions/withdraw', {
  'account_id': 1,
  'amount': 1000.00,
  'description': 'Withdrawal',
});
```

### Get Shifts

```dart
final response = await ApiService.get('/money-point/shifts');
if (response != null && response['success'] == true) {
  final shifts = response['data'];
  // Display shifts
}
```

## Step 8: Error Handling

Create `lib/utils/error_handler.dart`:

```dart
class ErrorHandler {
  static String getErrorMessage(dynamic error) {
    if (error is String) {
      return error;
    }
    
    // Handle different error types
    if (error.toString().contains('SocketException')) {
      return 'No internet connection';
    }
    
    if (error.toString().contains('TimeoutException')) {
      return 'Request timeout. Please try again.';
    }
    
    return 'An error occurred. Please try again.';
  }
}
```

## Summary

1. **Domain Discovery**: Use `DomainService` to validate and get domain info
2. **API Integration**: Use `ApiService` for all API calls
3. **Authentication**: Use `AuthService` for login/logout
4. **Storage**: Domain and token info saved locally
5. **Flow**: Domain Selection → Login → Home

## Testing

### Test Domain Validation

```dart
final domainInfo = await DomainService.validateDomain('ino');
print('API URL: ${domainInfo?.apiUrl}');
```

### Test API Call

```dart
ApiService.setBaseUrl('https://ino.moneypoint.com/api/v1');
final response = await ApiService.get('/money-point/dashboard');
print('Response: $response');
```

## Notes

- Always validate domain before making API calls
- Store domain info and token securely
- Handle network errors gracefully
- Show loading states during API calls
- Implement token refresh if needed

