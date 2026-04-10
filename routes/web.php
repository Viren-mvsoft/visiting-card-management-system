<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailConfigurationController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\UserManagementController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contacts
    Route::resource('contacts', ContactController::class);
    Route::delete('contacts/{contact}/images/{image}', [ContactController::class, 'deleteImage'])
        ->name('contacts.delete-image');

    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class);
    Route::post('email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])
        ->name('email-templates.duplicate');

    // Send Email
    Route::get('contacts/{contact}/send-email', [SendEmailController::class, 'create'])
        ->name('contacts.send-email');
    Route::post('contacts/{contact}/send-email', [SendEmailController::class, 'store'])
        ->name('contacts.send-email.store');
    Route::post('email-preview', [SendEmailController::class, 'preview'])
        ->name('email-preview');

    // Email Logs
    Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');

    // Admin-only routes
    Route::middleware(AdminMiddleware::class)->group(function () {
        // Email Configurations (SMTP)
        Route::resource('email-configs', EmailConfigurationController::class)
            ->parameters(['email-configs' => 'emailConfig']);
        Route::post('email-configs/{emailConfig}/test', [EmailConfigurationController::class, 'test'])
            ->name('email-configs.test');

        // User Management
        Route::resource('users', UserManagementController::class)->only(['index', 'create', 'store', 'destroy']);

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'store'])->name('settings.store');
    });
});

require __DIR__.'/auth.php';

use App\Models\EmailConfiguration;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');

    return 'Storage link created successfully!';
});

// ============================================================
// QUEUE & EMAIL DIAGNOSTIC ROUTES (Protected by secret token)
// Access: https://yoursite.com/queue-debug/vcms2026
// ============================================================
Route::get('/queue-debug/{secret}', function (string $secret) {
    if ($secret !== 'vcms2026') {
        abort(404);
    }

    $data = [];

    // 1. Environment Info
    $data['environment'] = [
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG'),
        'APP_URL' => env('APP_URL'),
        'QUEUE_CONNECTION' => env('QUEUE_CONNECTION'),
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'PHP_VERSION' => PHP_VERSION,
        'TIMEZONE' => config('app.timezone'),
        'CURRENT_TIME' => now()->toDateTimeString(),
    ];

    // 2. Check if jobs table exists and count pending jobs
    try {
        $pendingJobs = DB::table('jobs')->count();
        $data['jobs_table'] = [
            'exists' => true,
            'pending_count' => $pendingJobs,
            'jobs' => DB::table('jobs')->limit(10)->get()->map(function ($job) {
                return [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'attempts' => $job->attempts,
                    'created_at' => Carbon::createFromTimestamp($job->created_at)->toDateTimeString(),
                ];
            }),
        ];
    } catch (Exception $e) {
        $data['jobs_table'] = ['exists' => false, 'error' => $e->getMessage()];
    }

    // 3. Check failed_jobs table
    try {
        $failedJobs = DB::table('failed_jobs')->count();
        $data['failed_jobs'] = [
            'exists' => true,
            'count' => $failedJobs,
            'recent' => DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(5)
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'queue' => $job->queue,
                        'failed_at' => $job->failed_at,
                        'exception' => Str::limit($job->exception, 500),
                    ];
                }),
        ];
    } catch (Exception $e) {
        $data['failed_jobs'] = ['exists' => false, 'error' => $e->getMessage()];
    }

    // 4. Check email_logs table
    try {
        $emailLogs = EmailLog::orderByDesc('created_at')->limit(10)->get();
        $data['email_logs'] = $emailLogs->map(function ($log) {
            return [
                'id' => $log->id,
                'status' => $log->status,
                'error' => $log->error,
                'recipients' => $log->recipients,
                'subject' => $log->subject,
                'config_id' => $log->email_configuration_id,
                'sent_at' => $log->sent_at,
                'created_at' => $log->created_at->toDateTimeString(),
            ];
        });
    } catch (Exception $e) {
        $data['email_logs'] = ['error' => $e->getMessage()];
    }

    // 5. Check email configurations
    try {
        $configs = EmailConfiguration::all();
        $data['email_configurations'] = $configs->map(function ($config) {
            return [
                'id' => $config->id,
                'name' => $config->name,
                'host' => $config->host,
                'port' => $config->port,
                'encryption' => $config->encryption,
                'from_email' => $config->from_email,
                'username' => $config->username,
                'status' => $config->status,
            ];
        });
    } catch (Exception $e) {
        $data['email_configurations'] = ['error' => $e->getMessage()];
    }

    // 6. Check required PHP extensions
    $data['php_extensions'] = [
        'openssl' => extension_loaded('openssl'),
        'sockets' => extension_loaded('sockets'),
        'pdo' => extension_loaded('pdo'),
        'mbstring' => extension_loaded('mbstring'),
    ];

    // 7. Check storage link
    $data['storage_link'] = [
        'exists' => file_exists(public_path('storage')),
        'public_path' => public_path(),
        'storage_path' => storage_path(),
        'base_path' => base_path(),
    ];

    // 8. Check last log entries
    try {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $data['recent_log'] = implode("\n", array_slice($lines, -30));
        } else {
            $data['recent_log'] = 'Log file does not exist';
        }
    } catch (Exception $e) {
        $data['recent_log'] = 'Error reading log: '.$e->getMessage();
    }

    return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// Manually process queue (run this URL once to process pending jobs)
Route::get('/queue-run/{secret}', function (string $secret) {
    if ($secret !== 'vcms2026') {
        abort(404);
    }

    try {
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--tries' => 3]);
        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Queue worker executed.',
            'output' => $output,
            'time' => now()->toDateTimeString(),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => Str::limit($e->getTraceAsString(), 1000),
        ]);
    }
});

// Test SMTP connection directly
Route::get('/smtp-test/{secret}', function (string $secret) {
    if ($secret !== 'vcms2026') {
        abort(404);
    }

    $config = EmailConfiguration::active()->first();
    if (! $config) {
        return response()->json(['error' => 'No active email configuration found.']);
    }

    try {
        $tls = match (true) {
            $config->port == 465 => true,
            $config->port == 587 => null,
            $config->encryption === 'ssl' => true,
            $config->encryption === 'none' => false,
            default => null,
        };

        $transport = new EsmtpTransport(
            $config->host,
            $config->port,
            $tls
        );

        if ($config->username) {
            $transport->setUsername($config->username);
            $transport->setPassword($config->password);
        }

        // Try to connect
        $transport->start();
        $transport->stop();

        return response()->json([
            'success' => true,
            'message' => 'SMTP connection successful!',
            'config_used' => [
                'name' => $config->name,
                'host' => $config->host,
                'port' => $config->port,
                'encryption' => $config->encryption,
                'from_email' => $config->from_email,
            ],
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'config_used' => [
                'name' => $config->name,
                'host' => $config->host,
                'port' => $config->port,
                'encryption' => $config->encryption,
            ],
        ]);
    }
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
    $output = Artisan::output();

    return $output;
});
