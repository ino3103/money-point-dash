<?php

if (!function_exists('getCurrentPageForMenu')) {
    /**
     * Get the current page identifier for menu highlighting
     *
     * This function maps route names to page identifiers used by Alpine.js
     * for menu highlighting in the sidebar.
     *
     * @param string|null $override Optional override value
     * @return string
     */
    function getCurrentPageForMenu(?string $override = null): string
    {
        // If override is provided, use it
        if ($override !== null) {
            return $override;
        }

        // Route to page mapping for menu highlighting
        $routePageMap = [
            'dashboard' => 'dashboard',
            'roles.*' => 'roles',
            'branches.*' => 'branches',
            'users.*' => 'users',
            // Add more route patterns here as needed
        ];

        // Check route patterns in order
        foreach ($routePageMap as $routePattern => $pageName) {
            if (request()->routeIs($routePattern)) {
                return $pageName;
            }
        }

        // Default fallback
        return 'dashboard';
    }
}
