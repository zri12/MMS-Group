<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Project
    |--------------------------------------------------------------------------
    */

    'name' => env(
        'MMS_PROJECT_NAME',
        'MMS Marketing Monitoring System'
    ),

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */

    'api_name' => env(
        'MMS_API_NAME',
        'MMS Marketing Monitoring API'
    ),

    'api_version' => env(
        'MMS_API_VERSION',
        'v1'
    ),

    /*
    |--------------------------------------------------------------------------
    | Regional
    |--------------------------------------------------------------------------
    */

    'timezone' => env(
        'MMS_TIMEZONE',
        'Asia/Jakarta'
    ),

    'locale' => env(
        'MMS_LOCALE',
        'id'
    ),

    /*
    |--------------------------------------------------------------------------
    | Hari Operasional
    |--------------------------------------------------------------------------
    */

    'operational_days' => [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        'max_image_kb' => (int) env(
            'MMS_MAX_IMAGE_KB',
            2048
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default_per_page' => (int) env(
            'MMS_DEFAULT_PER_PAGE',
            20
        ),

        'max_per_page' => (int) env(
            'MMS_MAX_PER_PAGE',
            100
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation' => [
        'labels' => [
            'dashboard' => 'Dashboard',
            'marketing' => 'Pengaturan PDL',
            'daily' => 'Rencana Kerja',
            'tracking' => 'Tracking PDL',
            'prospects' => 'Data Prospek',
            'members' => 'Data Anggota',
            'operational_reports' => 'Laporan Operasional',
            'visit_reports' => 'Laporan Kunjungan',
            'operational_recaps' => 'Rekap Operasional',
            'schedules' => 'Jadwal PDL',
            'journeys' => 'Riwayat Perjalanan',
            'profile' => 'Profil Admin',
        ],
        'bottom_labels' => [
            'dashboard' => 'Dashboard',
            'marketing' => 'PDL',
            'daily' => 'Rencana',
            'tracking' => 'Tracking',
            'profile' => 'Profil',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'admin_login_max_attempts' => max(1, (int) env(
            'MMS_ADMIN_LOGIN_MAX_ATTEMPTS',
            5
        )),

        'admin_login_decay_seconds' => max(1, (int) env(
            'MMS_ADMIN_LOGIN_DECAY_SECONDS',
            60
        )),

        'marketing_api_login_max_attempts' => max(1, (int) env(
            'MMS_MARKETING_API_LOGIN_MAX_ATTEMPTS',
            5
        )),

        'marketing_api_login_decay_seconds' => max(1, (int) env(
            'MMS_MARKETING_API_LOGIN_DECAY_SECONDS',
            60
        )),

        'marketing_api_token_ability' => env(
            'MMS_MARKETING_API_TOKEN_ABILITY',
            'marketing-mobile'
        ),

        'marketing_api_token_prefix' => env(
            'MMS_MARKETING_API_TOKEN_PREFIX',
            'mms-marketing'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    |
    | Nilai ini dapat diatur melalui environment sesuai kebutuhan operasional.
    |
    */

    'tracking' => [
        'polling_seconds' => (int) env(
            'MMS_TRACKING_POLLING_SECONDS',
            15
        ),

        'max_batch_points' => (int) env(
            'MMS_MAX_TRACKING_BATCH_POINTS',
            100
        ),

        'gps_stale_seconds' => max(30, (int) env(
            'MMS_TRACKING_GPS_STALE_SECONDS',
            180
        )),
    ],

];
