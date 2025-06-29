<?php

return [
    'server_key' => 'Mid-server-QkeNWATV89t-93P9KTaUX1mu',
    'client_key' => 'Mid-client-gsS3-WSrG64qr3Vw',
    'is_production' => false,
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.midtrans.com/snap/v1/transactions'), // Default to production
    'is_sanitized' => true,
    'is_3ds' => true,
];