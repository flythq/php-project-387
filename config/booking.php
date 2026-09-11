<?php

return [

    'horizon_days' => (int) env('BOOKING_HORIZON_DAYS', 14),

    'slot_minutes' => (int) env('BOOKING_SLOT_MINUTES', 30),

    'host_email' => env('BOOKING_HOST_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),

    'host_name' => env('BOOKING_HOST_NAME', env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel'))),

];
