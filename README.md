# Blinqpay Payment Processor Auto Routing

A Laravel package that can intelligently route payment transactions to the most
suitable payment processor based on *transaction cost*, *reliability*, and
*currency support*.

## Installation

You can install the dev package via composer:

```bash
composer require ajosav/ajosav/blinqpay: dev-master
```
> [!TIP]
> We are using `dev-master` because the package has not been published yet. For this to work with our laravel project, we need to specify repository in our `composer.json`
```bash
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/ajosav/blinqpay"
    }
]
```

To customize routing rules and package processor settings, you need to publish the config, run the vendor publish command:
```bash
php artisan vendor:publish --tag=blinqpay-config
```

Run migrations
``` bash
php artisan migrate
```

## Usage
### Creating a new processor with artisan command

The package provides a command that allows us to manually create a processor via the artisan command. This is mostly useful if you already have a list of processors in the database. The supporting class for the processor can be generated with the following artisan command:
``` bash
php artisan blinqpay:processor TestPaymentProcessor
```
The command above will generate a class that extends BasePaymentProcessor
```php
<?php

namespace App\Blinqpay\Processors;

use Ajosav\Blinqpay\Processors\BasePaymentProcessor;

class TestPaymentProcessor extends BasePaymentProcessor
{
    public function process(float $amount, ?string $currency = 'NGN'): bool
    {
        // Implement process action.
        return true;
    }

}

```
The process method will contain the execution statement for the processor.
> [!IMPORTANT]
> When processors are generated with the artisan command, you need to ensure that a processor with the slug of the generated processor class exists in the `payment_processors` table. For the above payment processor, there should be a record with slug `test-payment-processor`.
> 
> It is advised to use the [Creating processors with blinqpay facades](#creating-processors-with-blinq-facades)

### Creating processors with Blinq Facades

Creating the processors with facades would create all the necessary records on the `payment_processors`, `payment_processor_settings` and `payment_processor_currencies` tables, and also generate the processor class. 
```php
<?php

use Ajosav\Blinqpay\DTO\PaymentProcessorDto;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Ajosav\Blinqpay\Models\BlinqpayCurrency;
    
$data = PaymentProcessorDto::fromArray([
                        'name' => 'Facade Payment Processor'
                        'status' => 'active',
                        'fees_percentage' => 1.5,
                        'fees_cap' => 2000,
                        'reliability' => 3,
                        'currency_ids' => BlinqpayCurrency::whereIn('code', ['USD', 'NGN'])->pluck('id')->toArray()   
                    ]);
                    
$processor = Blinqpay::processor()
                    ->create($data);
```
> [!TIP]
> The create method accepts an instance of `PaymentProcessorDto` which is used to supply the needed data.
> 
> If you are wondering how we are retrieving currencies, the currencies are seeded when the migration file is executed.

When the code above is executed, a new `FacadePaymentProcessor` class with be generate

```php
<?php

namespace App\Blinqpay\Processors;

use Ajosav\Blinqpay\Processors\BasePaymentProcessor;

class FacadePaymentProcessor extends BasePaymentProcessor
{
    public function process(float $amount, ?string $currency = 'NGN'): bool
    {
        // Implement process action.
        return true;
    }

}
````

To configure the namespace where the generated file is saved, you can configure the namespace on the .env 

```bash
PROCESSOR_NAMESPACE = 'App\\Blinqpay\\Processors'
```

### Making payments with blinqpay
This is where the auto routing feature of this package comes to play, the most suitable payment processor is used to process transactions based of the `reliability`, `currency` and `transaction` from your config `routing_rules`

Initiating a payment:
```php
<?php

use Ajosav\Blinqpay\Facades\Blinqpay;
                       
$processor = Blinqpay::initiatePayment()
                    ->setAmount(4500.75)
                    ->setCurrency('NGN')
                    ->pay();
```

If you would like a set of instructions to be executed after a payment is processed, you can simply pass a callback for that purpose

```php
<?php

use Ajosav\Blinqpay\Facades\Blinqpay;

$processor = Blinqpay::initiatePayment()
                    ->setAmount(4500.75)
                    ->setCurrency('USD')
                    ->pay(function($reference, $processor_used) {
                        info("Payment was processed using {$processor_used->name}, and the reference is {$reference}");
                    });
````
You can also do this as a one-liner
```php
<?php

use Ajosav\Blinqpay\Facades\Blinqpay;

$processor = Blinqpay::initiatePayment(
                        4500.75,
                        'USD',
                        function($reference, $processor_used) {
                            info("Payment was processed using {$processor_used->name}, and the reference is {$reference}");
                        }
                    )->pay();
````
### More Features
There are more crud features on the processor that you can explore using
`Blinqpay::processor()`
* `->update(slug, paymentDTO)` -> Can be used to update payment processor
* -`>all()` -> Retrieves all payment processors from the Database
* `->find(slug)` -> Find a payment processor by processor slug
* `->delete(slug)` -> Deletes a payment processor using the processor slug


### Usage in a laravel project

You'll find the usage on [https://github.com/ajosav/blinqpay-laravel-integration](https://github.com/ajosav/blinqpay-laravel-integration).


### Testing

```bash
$ vendor/bin/phpunit
```
