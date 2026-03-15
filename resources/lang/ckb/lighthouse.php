<?php

return [

    'pages' => [
        'lighthouse_results' => [
            'buttons' => [
                'refresh' => 'نوێکردنەوە',
                'run_audit' => 'ئەنجامدانی وردبینی',
            ],

            'heading' => 'ئەنجامەکانی وردبینی لایتهۆس (Lighthouse)',

            'navigation' => [
                'group' => 'ڕێکخستنەکان',
                'label' => 'وردبینییەکانی لایتهۆس',
            ],

            'notifications' => [
                'audit_running' => 'وردبینی لایتهۆس لە کاردایە...',
                'audit_completed' => 'وردبینی لایتهۆس بە سەرکەوتوویی تەواو بوو',
                'audit_failed' => 'وردبینی لایتهۆس سەرکەوتوو نەبوو',
                'audit_queued' => 'وردبینی لایتهۆس خرایە سەرە',
                'audit_queued_description' => 'وردبینییەکە خراوەتە سەرە و لە پاشبنەمادا کار دەکات. ئەنجامەکان کاتێک تەواو بوون دەردەکەون.',
                'results_refreshed' => 'ئەنجامەکان نوێکرانەوە',
                'deleted' => 'ئەنجامی وردبینی سڕایەوە',
                'bulk_deleted' => ':count ئەنجامی وردبینی سڕانەوە',
                'no_results' => 'هیچ ئەنجامێک بۆ داگرتن بەردەست نییە',
                'download_failed' => 'داگرتنی ڕاپۆرتی HTML سەرکەوتوو نەبوو',
                'download_success' => 'ڕاپۆرتی HTML بە سەرکەوتوویی پاشەکەوت کرا',
                'download_saved' => 'ڕاپۆرت پاشەکەوت کرا لە: :path',
            ],

            'categories' => [
                'performance' => 'کارایی',
                'accessibility' => 'دەستپێگەیشتن',
                'best_practices' => 'باشترین شێوازەکان',
                'seo' => 'SEO',
            ],

            'form' => [
                'url' => 'بەستەر (URL)',
                'url_helper' => 'ئەو بەستەرەی وردبینی بۆ دەکرێت',
                'url_placeholder' => 'https://example.com',
                'categories' => 'هاوپۆلەکان',
                'categories_helper' => 'ئەو هاوپۆلانەی وردبینی هەڵبژێرە کە دەتەوێت ئەنجام بدرێن',
                'form_factor' => 'جۆری ئامێر',
                'form_factor_helper' => 'ھاوتاکردنی ئامێری کۆمپیوتەر یان مۆبایل',
                'desktop' => 'کۆمپیوتەر',
                'mobile' => 'مۆبایل',
                'user_agent' => 'نێنەری بەکارهێنەر (User Agent)',
                'user_agent_helper' => 'ڕیزبەندی نێنەری بەکارهێنەری تایبەت (ئارەزوومەندانە)',
                'user_agent_placeholder' => 'Mozilla/5.0...',
                'headers' => 'سەردێڕە تایبەتەکان (Custom Headers)',
                'header_name' => 'ناوی سەردێڕ',
                'header_value' => 'نرخی سەردێڕ',
                'headers_helper' => 'سەردێڕی HTTP تایبەت بۆ داواکارییەکە زیاد بکە',
                'throttle_cpu' => 'سنووردارکردنی CPU',
                'throttle_cpu_helper' => 'چالاککردنی سنووردارکردنی CPU بۆ ھاوتاکردنی ئامێرە خاوەکان',
                'throttle_network' => 'سنووردارکردنی تۆڕ',
                'throttle_network_helper' => 'چالاککردنی سنووردارکردنی تۆڕ بۆ ھاوتاکردنی پەیوەندییە خاوەکان',
                'timeout' => 'کاتی بەسەرچوون',
                'timeout_helper' => 'زۆرترین کات بە چرکە بۆ چاوەڕوانیکردنی تەواوبوونی وردبینییەکە',
                'timeout_suffix' => 'چرکە',
            ],

            'messages' => [
                'no_results' => 'هیچ ئەنجامێکی وردبینی بەردەست نییە. کرتە لەسەر "ئەنجامدانی وردبینی" بکە بۆ ئەنجامدانی وردبینی لایتهۆس لەسەر بەستەرێک.',
                'history_description' => 'مێژووی وردبینییەکانی ئەم دواییەی ئەم بەستەرە:',
            ],

            'sections' => [
                'audit_info' => 'زانیاری وردبینی',
                'no_results' => 'هیچ ئەنجامێک نییە',
                'history' => 'مێژووی وردبینی',
                'performance_metrics' => 'پێوەرەکانی کارایی',
                'failed_audits' => 'وردبینییە سەرنەکەوتووەکان',
            ],

            'metrics' => [
                'first_contentful_paint' => 'یەکەم کێشانی ناوەڕۆک (FCP)',
                'largest_contentful_paint' => 'گەورەترین کێشانی ناوەڕۆک (LCP)',
                'speed_index' => 'پێوەرەی خێرایی',
                'total_blocking_time' => 'کۆی کاتی بلۆککردن',
                'time_to_interactive' => 'کاتی کارلێککردن',
                'cumulative_layout_shift' => 'گۆڕانی شێوازی کەڵەکەبوو (CLS)',
                'total_page_size' => 'کۆی قەبارەی پەڕە',
            ],

            'metric_descriptions' => [
                'first_contentful_paint' => 'FCP کاتێک دەپێوێت کە یەکەم دەق یان وێنە دەکێشرێت. FCP خێرا یارمەتیدەرە بۆ دڵنیاکردنەوەی بەکارهێنەران کە شتێک ڕوودەدات.',
                'largest_contentful_paint' => 'LCP کاتێک دەپێوێت کە گەورەترین توخم لە ناوەڕۆک دەردەکەوێت. نیشاندەرێکی سەرەکییە بۆ خێرایی بارکردنی هەستپێکراو.',
                'speed_index' => 'پێوەرەی خێرایی نیشانی دەدات کە چەندە بە خێرایی ناوەڕۆکی پەڕەیەک بە دیار دەردەکەوێت. کەمتر بێت باشترە.',
                'total_blocking_time' => 'TBT کۆی ئەو کاتە دەپێوێت کە پەڕەیەک بلۆک کراوە لە وەڵامدانەوەی تێچووی بەکارهێنەر. کەمتر بێت باشترە.',
                'time_to_interactive' => 'TTI دەپێوێت کە چەند کاتی دەوێت تا پەڕەیەک بە تەواوی دەبێتە کارلێککار. کەمتر بێت باشترە.',
                'cumulative_layout_shift' => 'CLS سەقامگیری بینراو دەپێوێت. بڕی ئەو ناوەڕۆکە بینراوە دیاری دەکات کە لە کاتی بارکردنی پەڕەدا دەجوڵێت. کەمتر بێت باشترە.',
                'total_page_size' => 'کۆی قەبارەی هەموو ئەو سەرچاوانەی بۆ پەڕەکە بارکراون. پەڕە بچووکەکان خێراتر بار دەبن.',
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
                'good' => 'باش',
                'needs_improvement' => 'پێویستی بە باشترکردنە',
                'poor' => 'خراپ',
                'unknown' => 'نەزانراو',
            ],

            'form_factor' => 'جۆری ئامێر',
            'url' => 'بەستەر (URL)',
            'user_agent' => 'نێنەری بەکارهێنەر (User Agent)',
            'headers' => 'سەردێڕەکان',
            'custom_headers' => 'سەردێڕە تایبەتەکان',
            'lighthouse_version' => 'وەشانی لایتهۆس',
            'current_value' => 'نرخی ئێستا',
            'show_all_audits' => 'نیشاندانی هەموو :count وردبینییەکە',
            'show_less' => 'نیشاندانی کەمتر',
            'view_full_report' => 'بینینی ڕاپۆرتی تەواوی HTML',
            'view_full_report_description' => 'کردنەوەی ڕاپۆرتی تەواوی لایتهۆس لە تابێکی نوێدا',
            'last_ran_at' => 'دواین جار ئەنجامدراوە لە :time',
            'not_available' => 'بەردەست نییە',
            'click_to_expand' => 'کرتە بکە بۆ وردەکارییەکان',
            'collapse' => 'کۆکردنەوە',
            'expand' => 'فراوانکردن',

            'table' => [
                'url' => 'بەستەر (URL)',
                'performance' => 'کارایی',
                'accessibility' => 'دەستپێگەیشتن',
                'best_practices' => 'باشترین شێوازەکان',
                'seo' => 'SEO',
                'finished_at' => 'تەواو بووە لە',
                'actions' => [
                    'view' => 'بینین',
                    'view_html' => 'بینینی ڕاپۆرتی HTML',
                    'download_html' => 'داگرتنی ڕاپۆرتی HTML',
                    'delete' => 'سڕینەوە',
                    'export_csv' => 'هەناردەکردن وەک CSV',
                    'export_json' => 'هەناردەکردن وەک JSON',
                ],
            ],

            'filters' => [
                'excellent' => 'نایاب (90-100)',
                'good' => 'باش (50-89)',
                'poor' => 'خراپ (0-49)',
                'created_from' => 'دروستکراوە لە',
                'created_until' => 'دروستکراوە تا',
            ],

            'export' => [
                'filename_prefix' => 'lighthouse-audits',
                'columns' => [
                    'url' => 'بەستەر (URL)',
                    'performance_score' => 'نمرەی کارایی',
                    'accessibility_score' => 'نمرەی دەستپێگەیشتن',
                    'best_practices_score' => 'نمرەی باشترین شێوازەکان',
                    'seo_score' => 'نمرەی SEO',
                    'finished_at' => 'تەواو بووە لە',
                ],
            ],

            'units' => [
                'ms' => 'ms',
                'bytes' => 'بایت',
                'B' => 'بایت',
                'KB' => 'کیلۆبایت',
                'MB' => 'مێگابایت',
                'GB' => 'گێگابایت',
            ],

            'confirm' => [
                'delete_title' => 'سڕینەوەی ئەنجامی وردبینی',
                'delete_description' => 'ئایا دڵنیایت لە سڕینەوەی ئەم ئەنجامەی وردبینی؟ ئەم کارە ناگەڕێتەوە.',
                'bulk_delete_title' => 'سڕینەوەی ئەنجامە هەڵبژێردراوەکان',
                'bulk_delete_description' => 'ئایا دڵنیایت لە سڕینەوەی ئەنجامە هەڵبژێردراوەکانی وردبینی؟ ئەم کارە ناگەڕێتەوە.',
            ],
        ],
    ],

];
