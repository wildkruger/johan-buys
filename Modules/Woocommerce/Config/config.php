<?php

return [
    'name' => 'Woocommerce',
    'item_id' => 'hxrku28ngdp',
    'options' => [
        [
            'label' => 'Configure', 
            'url' => url(env('ADMIN_PREFIX') . '/addon/woocommerce/configure')
        ]
    ],
    'supported_versions' => [3.9]
];
