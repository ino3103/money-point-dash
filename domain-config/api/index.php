<?php

/**
 * Domain Discovery API Endpoint
 *
 * JSON API for domain discovery and validation
 * Access: /api?action=validate&domain=ino.moneypoint.com
 *
 * Security: Requires X-API-Key and X-MoneyPoint-App headers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key, X-MoneyPoint-App, X-App-Version, X-Platform');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Security constants
define('REQUIRED_API_KEY', 'MP-MOBILE-APP-2024');
define('REQUIRED_APP_IDENTIFIER', 'flutter-mobile');

// Helper function to get header value
function getHeader($name)
{
    $name = 'HTTP_' . str_replace('-', '_', strtoupper($name));
    return $_SERVER[$name] ?? null;
}

// Helper function to validate security headers
function validateSecurityHeaders()
{
    $apiKey = getHeader('X-API-Key');
    $appIdentifier = getHeader('X-MoneyPoint-App');

    // Check if API Key is present and valid
    if (!$apiKey || $apiKey !== REQUIRED_API_KEY) {
        return [
            'valid' => false,
            'message' => 'Unauthorized - Invalid API key or missing security headers'
        ];
    }

    // Check if App Identifier is present and valid
    if (!$appIdentifier || $appIdentifier !== REQUIRED_APP_IDENTIFIER) {
        return [
            'valid' => false,
            'message' => 'Unauthorized - Invalid API key or missing security headers'
        ];
    }

    return ['valid' => true];
}

// Helper function to log request (optional - for security monitoring)
function logRequest($action, $domain, $success, $ip)
{
    $logFile = __DIR__ . '/../logs/domain_validation.log';
    $logDir = dirname($logFile);

    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = sprintf(
        "[%s] IP: %s | Action: %s | Domain: %s | Success: %s | API-Key: %s | App: %s\n",
        $timestamp,
        $ip,
        $action,
        $domain ?? 'N/A',
        $success ? 'YES' : 'NO',
        getHeader('X-API-Key') ? 'PRESENT' : 'MISSING',
        getHeader('X-MoneyPoint-App') ?: 'MISSING'
    );

    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Load domain configuration
$configFile = __DIR__ . '/../domains.json';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Configuration file not found.'
    ]);
    exit;
}

$config = json_decode(file_get_contents($configFile), true);
$main_domain = $config['main_domain'] ?? 'moneypoint.com';
$domains = $config['domains'] ?? [];

// Helper function to add URLs to domain
function addDomainUrls($domain)
{
    $domain['api_url'] = "https://{$domain['domain']}/api/v1";
    $domain['web_url'] = "https://{$domain['domain']}";
    return $domain;
}

// Helper function to find domain
function findDomain($domains, $domainIdentifier)
{
    foreach ($domains as $domain) {
        if ($domain['domain'] === $domainIdentifier) {
            return $domain;
        }
    }
    return null;
}

// Helper function to get active domains
function getActiveDomains($domains)
{
    return array_filter($domains, function ($domain) {
        return ($domain['is_active'] ?? false) === true;
    });
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Disable domain listing endpoint - Security requirement
if ($action === 'list' || $action === null) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint not found'
    ]);
    logRequest('list', null, false, $clientIp);
    exit;
}

// Route handling
switch ($action) {
    case 'validate':
        // Validate security headers first
        $securityCheck = validateSecurityHeaders();
        if (!$securityCheck['valid']) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => $securityCheck['message']
            ]);
            logRequest('validate', $_GET['domain'] ?? null, false, $clientIp);
            exit;
        }
        // Validate a specific domain
        $domainIdentifier = $_GET['domain'] ?? $_POST['domain'] ?? null;

        if (!$domainIdentifier) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Domain parameter is required.'
            ]);
            exit;
        }

        $domain = findDomain($domains, $domainIdentifier);

        if (!$domain) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Domain not found.'
            ]);
            exit;
        }

        if (!($domain['is_active'] ?? false)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Domain is not active.'
            ]);
            exit;
        }

        $domain = addDomainUrls($domain);

        // Log successful validation
        logRequest('validate', $domainIdentifier, true, $clientIp);

        echo json_encode([
            'success' => true,
            'data' => [
                'domain' => $domain['domain'],
                'name' => $domain['name'] ?? null,
                'description' => $domain['description'] ?? null,
                'api_url' => $domain['api_url'],
                'web_url' => $domain['web_url'],
            ]
        ]);
        break;

    default:
        // Unknown action - return 404
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
        logRequest($action ?? 'unknown', null, false, $clientIp);
        break;
}
