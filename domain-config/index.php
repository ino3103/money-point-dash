<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Point - Enter Your Organization</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error {
            background: #fff2f2;
            border-left: 4px solid #f53003;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #d32f2f;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 5px;
            transition: all 0.3s;
        }

        .input-wrapper:focus-within {
            border-color: #667eea;
        }

        .domain-prefix {
            padding: 0 15px;
            color: #666;
            font-weight: 500;
        }

        input[type="text"] {
            flex: 1;
            border: none;
            outline: none;
            padding: 12px;
            font-size: 16px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .info-box strong {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .domain-list {
            list-style: none;
            margin-top: 10px;
            padding-left: 0;
        }

        .domain-list li {
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php
    // Load domain configuration
    $configFile = __DIR__ . '/domains.json';
    if (!file_exists($configFile)) {
        die('Configuration file not found.');
    }

    $config = json_decode(file_get_contents($configFile), true);
    $main_domain = $config['main_domain'] ?? 'moneypoint.com';
    $domains = $config['domains'] ?? [];

    // Get active domains
    $activeDomains = array_filter($domains, function ($d) {
        return ($d['is_active'] ?? false) === true;
    });

    // Add URLs to domains
    foreach ($activeDomains as &$domain) {
        $domain['api_url'] = "https://{$domain['domain']}.{$main_domain}/api/v1";
        $domain['web_url'] = "https://{$domain['domain']}.{$main_domain}";
    }
    unset($domain);

    // Check for errors
    $error = $_GET['error'] ?? null;
    ?>

    <div class="container">
        <h1>Money Point</h1>
        <p>Enter your organization code to access your Money Point system. Each organization has its own separate deployment.</p>

        <?php if ($error === 'domain_not_found'): ?>
            <div class="error">
                Domain not found. Please check your organization code.
            </div>
        <?php elseif ($error === 'domain_inactive'): ?>
            <div class="error">
                This domain is currently inactive. Please contact your administrator.
            </div>
        <?php endif; ?>

        <form action="/redirect" method="GET">
            <div class="form-group">
                <label for="domain">Organization Code</label>
                <div class="input-wrapper">
                    <input type="text"
                        id="domain"
                        name="domain"
                        placeholder="ino"
                        required
                        pattern="[a-z0-9-]+"
                        title="Only lowercase letters, numbers, and hyphens allowed">
                    <span class="domain-prefix">.<?php echo htmlspecialchars($main_domain); ?></span>
                </div>
            </div>

            <button type="submit" class="submit-btn">Continue</button>
        </form>

        <?php if (count($activeDomains) > 0): ?>
            <div class="info-box">
                <strong>Available Organizations:</strong>
                <ul class="domain-list">
                    <?php foreach ($activeDomains as $domain): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($domain['name'] ?? ucfirst($domain['domain'])); ?></strong>
                            <?php if (!empty($domain['description'])): ?>
                                <br><span style="color: #666; font-size: 12px;"><?php echo htmlspecialchars($domain['description']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <strong>📱 Mobile App Users:</strong>
            Enter your organization code in the app settings. The app will connect directly to your organization's server.
            <br><br>
            <strong>Example:</strong> If your code is "ino", the app will connect to:<br>
            <code>https://ino.<?php echo htmlspecialchars($main_domain); ?>/api/v1</code>
        </div>
    </div>
</body>

</html>