<?php
// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Page header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-tools text-<?php echo $isActive ? 'orange' : 'gray'; ?>-500"></i>
                Maintenance Mode
            </h1>
            <p class="text-sm text-gray-500 mt-1">Control public site visibility during updates</p>
        </div>

        <!-- Live status badge -->
        <?php if ($isActive): ?>
            <span class="inline-flex items-center gap-2 bg-orange-100 text-orange-700 text-sm font-semibold px-4 py-2 rounded-full border border-orange-200">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse inline-block"></span>
                MAINTENANCE ON
            </span>
        <?php else: ?>
            <span class="inline-flex items-center gap-2 bg-green-100 text-green-700 text-sm font-semibold px-4 py-2 rounded-full border border-green-200">
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                SITE LIVE
            </span>
        <?php endif; ?>
    </div>

    <!-- Flash messages -->
    <?php if ($flashSuccess): ?>
    <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
        <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
        <span><?php echo $flashSuccess; ?></span>
    </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
        <span><?php echo htmlspecialchars($flashError); ?></span>
    </div>
    <?php endif; ?>

    <!-- Status card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center <?php echo $isActive ? 'bg-orange-100' : 'bg-blue-50'; ?>">
                <i class="fas fa-power-off text-lg <?php echo $isActive ? 'text-orange-500' : 'text-blue-500'; ?>"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Quick Toggle</p>
                <p class="text-xs text-gray-400">Enable or disable maintenance mode instantly</p>
            </div>
        </div>

        <div class="px-6 py-5">
            <?php if ($isActive): ?>
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-5 flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                    <div class="text-sm text-orange-800">
                        <strong>Maintenance mode is ON.</strong> All public pages are showing the maintenance screen.
                        The admin panel remains fully accessible.
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        The site is <strong>live</strong>. Enable maintenance mode to show a branded page to visitors
                        while you perform updates.
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="/manager/maintenance/toggle"
                  onsubmit="return confirm('<?php echo $isActive
                      ? 'Disable maintenance mode and restore the public site?'
                      : 'Enable maintenance mode? Visitors will see the maintenance page.'; ?>')">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all
                               <?php echo $isActive
                                   ? 'bg-green-500 hover:bg-green-600 text-white shadow-green-200 shadow-md'
                                   : 'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-200 shadow-md'; ?>">
                    <i class="fas <?php echo $isActive ? 'fa-check-circle' : 'fa-tools'; ?>"></i>
                    <?php echo $isActive ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Settings card (always visible, applies when active) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <i class="fas fa-sliders-h text-gray-500 text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Page Settings</p>
                <p class="text-xs text-gray-400">Customise the message shown to visitors</p>
            </div>
        </div>

        <form method="POST" action="/manager/maintenance/save" class="px-6 py-5 space-y-5">

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Maintenance Message
                </label>
                <textarea id="message" name="message" rows="3"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    placeholder="We are performing scheduled maintenance. We'll be back shortly."><?php echo htmlspecialchars($message); ?></textarea>
                <p class="text-xs text-gray-400 mt-1">This text appears under the headline on the maintenance page.</p>
            </div>

            <!-- Return time -->
            <div>
                <label for="return_time" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Expected Return <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input id="return_time" type="text" name="return_time"
                    value="<?php echo htmlspecialchars($returnTime); ?>"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g. Saturday, April 12 at 08:00 AM (UTC-5)">
                <p class="text-xs text-gray-400 mt-1">Displayed as a badge on the maintenance page. Leave empty to hide it.</p>
            </div>

            <!-- Preview link -->
            <?php if ($isActive): ?>
            <div class="bg-gray-50 rounded-xl p-3 flex items-center justify-between text-sm">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="fas fa-eye text-gray-400"></i>
                    Preview the maintenance page
                </span>
                <a href="/" target="_blank"
                   class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                    Open <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </div>
            <?php endif; ?>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                           text-white font-semibold text-sm rounded-xl transition shadow-sm">
                <i class="fas fa-save"></i>
                Save Settings
            </button>
        </form>
    </div>

    <!-- How it works -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">How it works</p>
        <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                When enabled, every public URL returns HTTP 503 and shows the branded maintenance page.
            </li>
            <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                All <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">/admin/*</code> routes bypass maintenance so you can still manage the site.
            </li>
            <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                A <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">Retry-After: 3600</code> header is sent so search engines know to come back.
            </li>
        </ul>
    </div>

</div>
