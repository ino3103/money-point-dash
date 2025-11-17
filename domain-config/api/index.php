<?php
/**
 * Domain Discovery API Endpoint
 * 
 * JSON API for domain discovery and validation
 * Access: /api?action=list or /api?action=validate&domain=ino
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
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
function addDomainUrls($domain, $main_domain) {
    $domain['api_url'] = "https://{$domain['domain']}.{$main_domain}/api/v1";
    $domain['web_url'] = "https://{$domain['domain']}.{$main_domain}";
    return $domain;
}

// Helper function to find domain
function findDomain($domains, $domainIdentifier) {
    foreach ($domains as $domain) {
        if ($domain['domain'] === $domainIdentifier) {
            return $domain;
        }
    }
    return null;
}

// Helper function to get active domains
function getActiveDomains($domains) {
    return array_filter($domains, function($domain) {
        return ($domain['is_active'] ?? false) === true;
    });
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// Route handling
switch ($action) {
    case 'validate':
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
        
        $domain = addDomainUrls($domain, $main_domain);
        
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
        
    case 'list':
    default:
        // List all active domains
        $activeDomains = getActiveDomains($domains);
        $domainList = array_values(array_map(function($domain) use ($main_domain) {
            return addDomainUrls($domain, $main_domain);
        }, $activeDomains));
        
        // Format response
        $formattedList = array_map(function($domain) {
            return [
                'domain' => $domain['domain'],
                'name' => $domain['name'] ?? ucfirst($domain['domain']),
                'description' => $domain['description'] ?? null,
                'api_url' => $domain['api_url'],
                'web_url' => $domain['web_url'],
            ];
        }, $domainList);
        
        echo json_encode([
            'success' => true,
            'data' => $formattedList
        ]);
        break;
}

