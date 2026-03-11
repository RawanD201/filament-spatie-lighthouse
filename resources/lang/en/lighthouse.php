<?php

return [

    'pages' => [
        'lighthouse_results' => [
            'buttons' => [
                'refresh' => 'Refresh',
                'run_audit' => 'Run Audit',
            ],

            'heading' => 'Lighthouse Audit Results',

            'navigation' => [
                'group' => 'Settings',
                'label' => 'Lighthouse Audits',
            ],

            'notifications' => [
                'audit_running' => 'Running Lighthouse audit...',
                'audit_completed' => 'Lighthouse audit completed successfully',
                'audit_failed' => 'Lighthouse audit failed',
                'audit_queued' => 'Lighthouse audit queued',
                'audit_queued_description' => 'The audit has been queued and will run in the background. Results will appear when complete.',
                'results_refreshed' => 'Results refreshed',
                'deleted' => 'Audit result deleted',
                'bulk_deleted' => ':count audit result(s) deleted',
                'no_results' => 'No results available for download',
                'download_failed' => 'Failed to download HTML report',
                'download_success' => 'HTML report saved successfully',
                'download_saved' => 'Report saved to: :path',
            ],

            'categories' => [
                'performance' => 'Performance',
                'accessibility' => 'Accessibility',
                'best_practices' => 'Best Practices',
                'seo' => 'SEO',
            ],

            'form' => [
                'url' => 'URL',
                'url_helper' => 'The URL to audit',
                'url_placeholder' => 'https://example.com',
                'categories' => 'Categories',
                'categories_helper' => 'Select which audit categories to run',
                'form_factor' => 'Form Factor',
                'form_factor_helper' => 'Desktop or Mobile device emulation',
                'desktop' => 'Desktop',
                'mobile' => 'Mobile',
                'user_agent' => 'User Agent',
                'user_agent_helper' => 'Custom user agent string (optional)',
                'user_agent_placeholder' => 'Mozilla/5.0...',
                'headers' => 'Custom Headers',
                'header_name' => 'Header Name',
                'header_value' => 'Header Value',
                'headers_helper' => 'Add custom HTTP headers to the request',
                'throttle_cpu' => 'Throttle CPU',
                'throttle_cpu_helper' => 'Enable CPU throttling to simulate slower devices',
                'throttle_network' => 'Throttle Network',
                'throttle_network_helper' => 'Enable network throttling to simulate slower connections',
                'timeout' => 'Timeout',
                'timeout_helper' => 'Maximum time in seconds to wait for the audit to complete',
                'timeout_suffix' => 'seconds',
            ],

            'messages' => [
                'no_results' => 'No audit results available. Click "Run Audit" to perform a Lighthouse audit on a URL.',
                'history_description' => 'Recent audit history for this URL:',
            ],

            'sections' => [
                'audit_info' => 'Audit Information',
                'no_results' => 'No Results',
                'history' => 'Audit History',
                'performance_metrics' => 'Performance Metrics',
                'failed_audits' => 'Failed Audits',
            ],

            'metrics' => [
                'first_contentful_paint' => 'First Contentful Paint',
                'largest_contentful_paint' => 'Largest Contentful Paint',
                'speed_index' => 'Speed Index',
                'total_blocking_time' => 'Total Blocking Time',
                'time_to_interactive' => 'Time to Interactive',
                'cumulative_layout_shift' => 'Cumulative Layout Shift',
                'total_page_size' => 'Total Page Size',
            ],

            'metric_descriptions' => [
                'first_contentful_paint' => 'FCP measures when the first text or image is painted. A fast FCP helps reassure users that something is happening.',
                'largest_contentful_paint' => 'LCP measures when the largest content element becomes visible. It\'s a key indicator of perceived load speed.',
                'speed_index' => 'Speed Index shows how quickly the contents of a page are visibly populated. Lower is better.',
                'total_blocking_time' => 'TBT measures the total amount of time that a page is blocked from responding to user input. Lower is better.',
                'time_to_interactive' => 'TTI measures how long it takes a page to become fully interactive. Lower is better.',
                'cumulative_layout_shift' => 'CLS measures visual stability. It quantifies how much visible content shifts during page load. Lower is better.',
                'total_page_size' => 'Total size of all resources loaded for the page. Smaller pages load faster.',
            ],

            'metric_icons' => [
                'first_contentful_paint' => 'heroicon-o-bolt',
                'largest_contentful_paint' => 'heroicon-o-paint-brush',
                'speed_index' => 'heroicon-o-chart-bar',
                'total_blocking_time' => 'heroicon-o-clock',
                'time_to_interactive' => 'heroicon-o-cursor-arrow-rays',
                'cumulative_layout_shift' => 'heroicon-o-arrows-pointing-out',
                'total_page_size' => 'heroicon-o-server',
            ],

            'thresholds' => [
                'good' => 'Good',
                'needs_improvement' => 'Needs Improvement',
                'poor' => 'Poor',
                'unknown' => 'Unknown',
            ],

            'form_factor' => 'Form Factor',
            'url' => 'URL',
            'user_agent' => 'User Agent',
            'headers' => 'Headers',
            'custom_headers' => 'custom headers',
            'lighthouse_version' => 'Lighthouse Version',
            'current_value' => 'Current Value',
            'show_all_audits' => 'Show All :count Audits',
            'show_less' => 'Show Less',
            'view_full_report' => 'View Full HTML Report',
            'view_full_report_description' => 'Open the complete Lighthouse report in a new tab',
            'last_ran_at' => 'Last ran :time',
            'not_available' => 'N/A',
            'click_to_expand' => 'Click for details',
            'collapse' => 'Collapse',
            'expand' => 'Expand',

            'table' => [
                'url' => 'URL',
                'performance' => 'Performance',
                'accessibility' => 'Accessibility',
                'best_practices' => 'Best Practices',
                'seo' => 'SEO',
                'finished_at' => 'Finished At',
                'actions' => [
                    'view' => 'View',
                    'view_html' => 'View HTML Report',
                    'download_html' => 'Download HTML Report',
                    'delete' => 'Delete',
                    'export_csv' => 'Export as CSV',
                    'export_json' => 'Export as JSON',
                ],
            ],

            'filters' => [
                'excellent' => 'Excellent (90-100)',
                'good' => 'Good (50-89)',
                'poor' => 'Poor (0-49)',
                'created_from' => 'Created from',
                'created_until' => 'Created until',
            ],

            'export' => [
                'filename_prefix' => 'lighthouse-audits',
                'columns' => [
                    'url' => 'URL',
                    'performance_score' => 'Performance Score',
                    'accessibility_score' => 'Accessibility Score',
                    'best_practices_score' => 'Best Practices Score',
                    'seo_score' => 'SEO Score',
                    'finished_at' => 'Finished At',
                ],
            ],

            'units' => [
                'ms' => 'ms',
                'bytes' => 'bytes',
                'B' => 'B',
                'KB' => 'KB',
                'MB' => 'MB',
                'GB' => 'GB',
            ],

            'confirm' => [
                'delete_title' => 'Delete Audit Result',
                'delete_description' => 'Are you sure you want to delete this audit result? This action cannot be undone.',
                'bulk_delete_title' => 'Delete Selected Results',
                'bulk_delete_description' => 'Are you sure you want to delete the selected audit results? This action cannot be undone.',
            ],
        ],
    ],

];
