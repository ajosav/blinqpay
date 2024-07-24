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
];
