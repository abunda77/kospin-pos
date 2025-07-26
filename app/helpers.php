<?php

if (!function_exists('is_mobile_mode')) {
    /**
     * Check if the current request is in mobile mode
     *
     * @return bool
     */
    function is_mobile_mode()
    {
        return session('view_preference') === 'mobile' ||
               request()->routeIs('catalog.mobile*') ||
               str_contains(request()->path(), 'm/catalog');
    }
}

if (!function_exists('get_catalog_route')) {
    /**
     * Get the appropriate catalog route based on current mode
     *
     * @param string|null $category
     * @param array $params
     * @return string
     */
    function get_catalog_route($category = null, $params = [])
    {
        $isMobile = is_mobile_mode();
        
        if ($category) {
            return $isMobile 
                ? route('catalog.mobile.show', array_merge([$category], $params))
                : route('catalog.show', array_merge([$category], $params));
        }
        
        return $isMobile 
            ? route('catalog.mobile', $params)
            : route('catalog', $params);
    }
}
