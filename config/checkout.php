<?php

return [
    'delivery_fee' => (float) env('CHECKOUT_DELIVERY_FEE', 150), // PHP
    'delivery_eta_days' => (int) env('CHECKOUT_DELIVERY_ETA_DAYS', 2),
];