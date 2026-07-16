<?php
/**
 * Elearning Plugin
 * Provides LMS functionality integrated into the CMS.
 */

use Core\PluginManager;
use Core\Config;

// Get plugin manager instance
$pm = PluginManager::getInstance();

// Register hooks for the main CMS (when NOT in LMS subdomain)
if (!defined('IS_LMS') || !IS_LMS) {
    // Add "My Courses" link to the account menu
    $pm->addAction('account_menu_links', function() {
        $lmsUrl = str_replace('//', '//elearning.', Config::get('site.url'));
        echo '<li><a href="' . $lmsUrl . '/dashboard" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-graduation-cap mr-3"></i>' . __('lms.my_courses') . '</a></li>';
    });
}

// Global actions
$pm->addAction('header_meta', function() {
    if (defined('IS_LMS') && IS_LMS) {
        echo '<meta name="lms-mode" content="active">';
    }
});
