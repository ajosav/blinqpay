<?php

namespace Ajosav\Blinqpay\Services;

use Ajosav\Blinqpay\Exceptions\InvalidRoutingConfigurationException;

class ConfigValidatorService
{
    protected const ROUTING_RULES = ['transaction_cost', 'reliability'];
    protected const ROUTING_CONSTRAINTS = ['low', 'medium', 'high'];

    public function __construct(public array $config)
    {
    }

    /**
     * @return bool
     * @throws InvalidRoutingConfigurationException
     */
    public function __invoke(): bool
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
                if (!is_string($key) || !is_numeric($value)) {
                    throw new InvalidRoutingConfigurationException("The routing rule \"$rule\" must be an associative array with string keys and integer values.");
                }
            }

            foreach (self::ROUTING_CONSTRAINTS as $constraint) {
                if (!array_key_exists($constraint, $config['routing_rules'][$rule])) {
                    throw new InvalidRoutingConfigurationException("The routing rule \"$rule\" must have a \"{$constraint}\" key and value.");
                }
            }
        }

        return true;
    }

}