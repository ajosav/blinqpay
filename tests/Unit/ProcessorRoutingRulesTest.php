<?php

namespace Ajosav\Blinqpay\Tests\Unit;

use Ajosav\Blinqpay\Exceptions\InvalidRoutingConfigurationException;
use Ajosav\Blinqpay\Services\ConfigValidatorService;
use Ajosav\Blinqpay\Tests\TestCase;

class ProcessorRoutingRulesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_router_throws_routing_exception_when_routing_rules_is_not_set()
    {
        $routing_rules = [];
        $this->expectException(InvalidRoutingConfigurationException::class);
        $this->expectExceptionMessage('The "routing_rules" configuration is required and must be an array.');

        (new ConfigValidatorService($routing_rules))();
    }

    public function test_router_throws_routing_exception_when_routing_rules_is_missing_keys()
    {
        $routing_rules = [
            'routing_rules' => [
                'transaction_cost' => [
                    'high' => 1000,
                    'medium' => 500
                ],
                'reliability' => [
                    'high' => 1,
                    'medium' => 3,
                    'low' => 5,
                ]
            ]
        ];
        $this->expectException(InvalidRoutingConfigurationException::class);
        $this->expectExceptionMessage('The routing rule "transaction_cost" must have a "low" key and value.');

        $validator = new ConfigValidatorService($routing_rules);
        $validator();
    }

    public function test_validator_returns_true_when_configuration_matches_expected_structure()
    {
        $routing_rules = [
            'routing_rules' => [
                'transaction_cost' => [
                    'high' => 1000,
                    'medium' => 500,
                    'low' => 100
                ],
                'reliability' => [
                    'high' => 1,
                    'medium' => 3,
                    'low' => 5,
                ]
            ]
        ];

        $validator = new ConfigValidatorService($routing_rules);
        $this->assertTrue($validator());
    }
}