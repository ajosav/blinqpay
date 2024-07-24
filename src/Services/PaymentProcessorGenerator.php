<?php

namespace Ajosav\Blinqpay\Services;

use Ajosav\Blinqpay\Exceptions\FileAlreadyExistException;
use Ajosav\Blinqpay\Utils\FilePathUtil;
use Illuminate\Support\Facades\File;

class PaymentProcessorGenerator
{
    public function generate(string $name): string
    {
        $namespace = config('blinqpay.processor_namespace', 'App\\Blinqpay\\Processors');

        // Checking if processor already exists
        $file_exists = File::exists(FilePathUtil::pathFromNamespace($namespace, $name));
        throw_if($file_exists, new FileAlreadyExistException('File already exists'));

        $content = preg_replace_array(
            ['/\[namespace\]/', '/\[class\]/'],
            [FilePathUtil::classNamespace($namespace, $name), FilePathUtil::className($name)],
            file_get_contents(__DIR__ . '/stubs/PaymentProcessor.stub')
        );

        FilePathUtil::ensureDirectoryExists($namespace, $name);
        File::put(FilePathUtil::pathFromNamespace($namespace, $name), $content);
        return FilePathUtil::className($name);
    }
}