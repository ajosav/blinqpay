<?php

namespace Ajosav\Blinqpay\Commands;

use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Throwable;

class PaymentProcessorCommand extends Command implements PromptsForMissingInput
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
     * @return void
     */
    public function handle(PaymentProcessorManager $processorGenerator)
    {
        $name = $this->argument('name');
        try {
            $class_name = $processorGenerator->generate($name);
            $this->info($class_name . ' payment processor created successfully');
        } catch (Throwable $e) {
            report($e);
            $this->error($e->getMessage());
        }
        return;
    }
}