<?php
/**
 * Domain Redirect Handler
 * 
 * Redirects users to their organization's subdomain
 * Access: /redirect?domain=ino
 */

// Load domain configuration
$configFile = __DIR__ . '/../domains.json';
if (!file_exists($configFile)) {
    die('Configuration file not found.');
}

$config = json_decode(file_get_contents($configFile), true);
$main_domain = $config['main_domain'] ?? 'moneypoint.com';
$domains = $config['domains'] ?? [];

// Get domain from query parameter
$domainIdentifier = $_GET['domain'] ?? null;

if (!$domainIdentifier) {
    // Redirect to main domain landing page
    header('Location: /');
    exit;
}

// Find domain
$domain = null;
foreach ($domains as $d) {
    if ($d['domain'] === $domainIdentifier) {
        $domain = $d;
        break;
    }
}

if (!$domain) {
    // Domain not found - redirect to main domain with error
    header('Location: /?error=domain_not_found');
    exit;
}

if (!($domain['is_active'] ?? false)) {
    // Domain inactive - redirect to main domain with error
    header('Location: /?error=domain_inactive');
    exit;
}

// Redirect to domain's web URL
$redirectUrl = "https://{$domain['domain']}.{$main_domain}";
header('Location: ' . $redirectUrl);
exit;

