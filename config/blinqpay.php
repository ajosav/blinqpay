<?php

return [
    /**
     * This provider will be used to process payments when auto routing is disabled
     * PS: When auto routing is turned off, default processor will be ignored
     */
    'default_processor' => '',

    /**
     * @bool
     * When this is set to true and default processor is configured,
     * payments will only be processed by the default_processor
     */
    'auto_routing' => true,

    'routing_rules' => [
        'transaction_cost' => [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
        ],
        'reliability' => [
            'high' => 1,
            'medium' => 2,
            'low' => 3,
        ]
    ],
];
