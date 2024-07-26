<?php

namespace Ajosav\Blinqpay\Command;

use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Illuminate\Console\Command;
use Throwable;

class PaymentProcessorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blinqpay:processor {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new payment processor';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PaymentProcessorManager $processorGenerator)
    {
        $name = $this->argument('name');
        try {
            $class_name = $processorGenerator->generate($name);
            $this->info($class_name . ' payment processor created successfully');
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
        return;
    }
}