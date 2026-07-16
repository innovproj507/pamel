<?php
if (!function_exists('opcache_get_status')) {
    die('OPcache extension NOT loaded');
}
$s = opcache_get_status(false);
echo 'OPcache enabled: ' . ($s['opcache_enabled'] ? 'YES' : 'NO') . "\n";
echo 'Memory used: ' . round($s['memory_usage']['used_memory'] / 1024 / 1024, 1) . " MB\n";
echo 'Cached files: ' . $s['opcache_statistics']['num_cached_scripts'] . "\n";
// Delete this file immediately after use
unlink(__FILE__);
