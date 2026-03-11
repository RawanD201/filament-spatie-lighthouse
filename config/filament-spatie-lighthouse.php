<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Result Store
    |--------------------------------------------------------------------------
    |
    | The result store to use for storing audit results.
    | Options: 'database', 'cache'
    |
    */
    'result_store' => env('LIGHTHOUSE_RESULT_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Raw Results Storage Driver
    |--------------------------------------------------------------------------
    |
    | Where to store the full raw Lighthouse JSON results.
    |
    | Options:
    |   'database'   — stored in the raw_results JSON column (default, fine for small-scale)
    |   'filesystem' — stored as JSON files on disk (recommended for production at scale,
    |                  avoids storing 500KB–2MB blobs in your database per audit)
    |
    | When using 'filesystem':
    |   - raw_results_disk: the Laravel filesystem disk to use (default: 'local')
    |   - raw_results_path: directory within the disk (default: 'lighthouse-results')
    |
    */
    'raw_results_driver' => env('LIGHTHOUSE_RAW_RESULTS_DRIVER', 'database'),
    'raw_results_disk'   => env('LIGHTHOUSE_RAW_RESULTS_DISK', 'local'),
    'raw_results_path'   => env('LIGHTHOUSE_RAW_RESULTS_PATH', 'lighthouse-results'),

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Database connection to use for storing results (if using database store).
    |
    */
    'database' => [
        'connection' => env('LIGHTHOUSE_DB_CONNECTION', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | Cache TTL in seconds (if using cache store).
    |
    */
    'cache_ttl' => env('LIGHTHOUSE_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | Default Timeout
    |--------------------------------------------------------------------------
    |
    | Default timeout for Lighthouse audits in seconds.
    |
    | IMPORTANT: When running audits synchronously (not queued), PHP's
    | max_execution_time must be greater than this value. The code will
    | automatically increase PHP's execution time limit, but if your
    | server has strict limits, you may need to adjust php.ini or use
    | queue-based audits instead.
    |
    */
    'default_timeout' => env('LIGHTHOUSE_TIMEOUT', 180),

    /*
    |--------------------------------------------------------------------------
    | History Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep audit history (for database store).
    |
    */
    'keep_history_for_days' => env('LIGHTHOUSE_KEEP_HISTORY_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Categories
    |--------------------------------------------------------------------------
    |
    | Default categories to audit when running a new audit.
    |
    */
    'default_categories' => [
        'performance',
        'accessibility',
        'best-practices',
        'seo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether audits run in the background via queues.
    |
    */
    'use_queue' => env('LIGHTHOUSE_USE_QUEUE', false),
    'queue_connection' => env('LIGHTHOUSE_QUEUE_CONNECTION', null),
    'queue_name' => env('LIGHTHOUSE_QUEUE_NAME', 'default'),
    'queue_tries' => env('LIGHTHOUSE_QUEUE_TRIES', 1),
    /*
     * Queue timeout in seconds. This should be greater than default_timeout
     * to allow the audit to complete. The job will use the timeout value
     * passed to it, but this serves as a maximum safety limit.
     */
    'queue_timeout' => env('LIGHTHOUSE_QUEUE_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Score Thresholds
    |--------------------------------------------------------------------------
    |
    | Customize the score thresholds for color indicators.
    | Scores >= 'good' are green, >= 'needs_improvement' are orange, else red.
    |
    */
    'score_thresholds' => [
        'good' => 90,
        'needs_improvement' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Metric Thresholds
    |--------------------------------------------------------------------------
    |
    | Customize the threshold values for each performance metric.
    | Each metric has a 'good' and 'needs_improvement' threshold.
    | Values above 'needs_improvement' are considered 'poor'.
    |
    */
    'metric_thresholds' => [
        'first_contentful_paint' => ['good' => 1800, 'needs_improvement' => 3000, 'unit' => 'ms'],
        'largest_contentful_paint' => ['good' => 2500, 'needs_improvement' => 4000, 'unit' => 'ms'],
        'speed_index' => ['good' => 3400, 'needs_improvement' => 5800, 'unit' => 'ms'],
        'total_blocking_time' => ['good' => 200, 'needs_improvement' => 600, 'unit' => 'ms'],
        'time_to_interactive' => ['good' => 3800, 'needs_improvement' => 7300, 'unit' => 'ms'],
        'cumulative_layout_shift' => ['good' => 0.1, 'needs_improvement' => 0.25, 'unit' => 'score'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which sections and features are visible on the results page.
    |
    */
    'display' => [
        /*
         * Show the category score cards at the top of the page.
         */
        'show_category_scores' => true,

        /*
         * Show the audit information section (URL, form factor, etc.).
         */
        'show_audit_info' => true,

        /*
         * Show the HTML report buttons (view/download).
         */
        'show_html_report' => true,

        /*
         * Show the performance metrics section.
         */
        'show_performance_metrics' => true,

        /*
         * Show the failed audits section.
         */
        'show_failed_audits' => true,

        /*
         * Show the audit history section.
         */
        'show_history' => true,

        /*
         * Number of failed audits to show initially before "Show All".
         */
        'failed_audits_initial_count' => 10,

        /*
         * Max height for the scrollable failed audits container (CSS value).
         */
        'failed_audits_max_height' => '800px',

        /*
         * Number of history items to show.
         */
        'history_count' => 5,

        /*
         * Table polling interval (set to null to disable).
         */
        'table_poll_interval' => '30s',

        /*
         * Toggle visibility for each table row action.
         */
        'table_actions' => [
            'view' => true,
            'view_html' => true,
            'download_html' => true,
            'delete' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email' => [
            'enabled' => env('LIGHTHOUSE_NOTIFICATIONS_EMAIL_ENABLED', false),
            'to' => env('LIGHTHOUSE_NOTIFICATIONS_EMAIL_TO', null),
            'on_failure' => env('LIGHTHOUSE_NOTIFICATIONS_EMAIL_ON_FAILURE', true),
            'on_completion' => env('LIGHTHOUSE_NOTIFICATIONS_EMAIL_ON_COMPLETION', false),
        ],
        'slack' => [
            'enabled' => env('LIGHTHOUSE_NOTIFICATIONS_SLACK_ENABLED', false),
            'webhook_url' => env('LIGHTHOUSE_NOTIFICATIONS_SLACK_WEBHOOK_URL', null),
            'channel' => env('LIGHTHOUSE_NOTIFICATIONS_SLACK_CHANNEL', null),
            'on_failure' => env('LIGHTHOUSE_NOTIFICATIONS_SLACK_ON_FAILURE', true),
            'on_completion' => env('LIGHTHOUSE_NOTIFICATIONS_SLACK_ON_COMPLETION', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
    'export' => [
        'enabled' => env('LIGHTHOUSE_EXPORT_ENABLED', true),
        'formats' => ['csv', 'json'],
        'disk' => env('LIGHTHOUSE_EXPORT_DISK', 'local'),
        'path' => env('LIGHTHOUSE_EXPORT_PATH', 'lighthouse-exports'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduling
    |--------------------------------------------------------------------------
    */
    'scheduling' => [
        'enabled' => env('LIGHTHOUSE_SCHEDULING_ENABLED', true),
        'default_frequency' => env('LIGHTHOUSE_SCHEDULE_FREQUENCY', 'daily'),
        'urls' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP API Endpoints
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'enabled' => env('LIGHTHOUSE_ENDPOINTS_ENABLED', false),
        'secret_token' => env('LIGHTHOUSE_ENDPOINTS_SECRET_TOKEN', null),
        'prefix' => env('LIGHTHOUSE_ENDPOINTS_PREFIX', 'lighthouse-api'),
    ],

];
