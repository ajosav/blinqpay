<?php

namespace Ajosav\Blinqpay\Services;

use Ajosav\Blinqpay\Exceptions\InvalidRoutingConfigurationException;

class ConfigValidatorService
{
    protected const ROUTING_RULES = ['transaction_cost', 'reliability'];

    public function __construct(public array $config)
    {
    }

    public function __invoke()
    {
        $config = $this->config;
        if (empty($config['routing_rules']) || !is_array($config['routing_rules'])) {
            throw new InvalidRoutingConfigurationException('The "routing_rules" configuration is required and must be an array.');
        }

        foreach (self::ROUTING_RULES as $rule) {
            if (empty($config['routing_rules'][$rule]) || !is_array($config['routing_rules'][$rule])) {
                throw new InvalidRoutingConfigurationException("The routing rule \"$rule\" is required and must be a non-empty array.");
            }

            foreach ($config['routing_rules'][$rule] as $key => $value) {
                if (!is_string($key) || !is_int($value)) {
                    throw new InvalidRoutingConfigurationException("The routing rule \"$rule\" must be an associative array with string keys and integer values.");
                }
            }
        }
    }

}