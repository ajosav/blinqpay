<?php

return [
    /**
     * This value sets the root namespace for payment processor classes in your application.
     */
    'processor_namespace' => env('PROCESSOR_NAMESPACE', 'App\\Blinqpay\\Processors'),

    /**
     * The routing rule is used to predefine how payment processors should be selected
     * Low: minimum value.
     * Medium: medium value
     * high: High value
     *
     * Example:
     * Low represents that a processor whose transaction cost is between 100 and 499 should be the best choice for our processing
     * Each value scores the processor a min of 1 point and max of 3points, for transaction cost: low -> 3, medium -> 2, high -> 1
     * For reliability: high means very stable. high -> 3, medium -> 2, low -> 1
     */
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
