<?php

return [
    'name' => 'CryptoExchange',
    'item_id' => 'fv8wtkr1jfc',
    'options' => [
        ['label' => __('Settings'), 'url' => url(env('ADMIN_PREFIX') . '/crypto_settings/')]
    ],
    'supported_versions' => ['3.9'],
    'transaction_type' => [
        'Crypto_Buy' => ['Stripe', 'Paypal', 'PayUmoney', 'Payeer','Bank'],
    ],
    'permission_group' => ['Crypto Direction', 'Crypto Exchange Transaction', 'Crypto Exchange Settings', 'Crypto Exchange']
];
