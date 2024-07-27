<?php

return [
    /**
     * This value sets the root namespace for payment processor classes in your application.
     */
    'processor_namespace' => env('PROCESSOR_NAMESPACE', 'App\\Blinqpay\\Processors'),

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
            'low' => 100,
            'medium' => 500,
            'high' => 1000,
        ],
        'reliability' => [
            'high' => 1,
            'medium' => 3,
            'low' => 5,
        ]
    ],
];
