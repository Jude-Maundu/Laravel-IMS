<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_debug' => 'boolean',
            'log_level' => 'required|string|in:emergency,alert,critical,error,warning,notice,info,debug',
            'cache_driver' => 'required|string|in:file,database,redis',
            'session_driver' => 'required|string|in:file,cookie,database,redis',
            'queue_connection' => 'required|string|in:sync,database,redis',
        ]);

        // Update .env file
        $this->updateEnvironmentFile([
            'APP_NAME' => $request->app_name,
            'APP_DEBUG' => $request->app_debug ? 'true' : 'false',
            'LOG_LEVEL' => $request->log_level,
            'CACHE_STORE' => $request->cache_driver,
            'SESSION_DRIVER' => $request->session_driver,
            'QUEUE_CONNECTION' => $request->queue_connection,
        ]);

        // Clear caches
        Cache::flush();
        Artisan::call('config:clear');

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    /**
     * Clear application caches.
     */
    public function clearCache()
    {
        Cache::flush();
        Artisan::call('config:clear');

        // Clear compiled views
        $viewPath = storage_path('framework/views');
        if (is_dir($viewPath)) {
            $files = glob($viewPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return redirect()->route('settings.index')->with('success', 'Cache cleared successfully.');
    }

    /**
     * Clear all application data tables while preserving users.
     */
    public function clearDatabase(Request $request)
    {
        $request->validate([
            'confirm_wipe' => 'accepted',
        ]);

        $tables = [
            'activity_logs',
            'assignments',
            'checklists',
            'event_borrowed_items',
            'event_item_images',
            'event_items',
            'event_operational_items',
            'event_piece_dispatches',
            'event_staff',
            'missing_items',
            'operational_items',
            'receive_session_pieces',
            'receive_sessions',
            'scan_session_pieces',
            'scan_sessions',
            'event_log',
            'events',
            'item_images',
            'items',
            'repairs',
        ];

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

            return redirect()->route('settings.index')->with('success', 'Application database wiped successfully. User accounts remain intact.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('settings.index')->with('error', 'Failed to clear database: ' . $e->getMessage());
        }
    }

    /**
     * Update environment file.
     */
    private function updateEnvironmentFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            $envContent = preg_replace($pattern, $replacement, $envContent);
        }

        file_put_contents($envFile, $envContent);
    }
}