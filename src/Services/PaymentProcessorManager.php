<?php

namespace Ajosav\Blinqpay\Services;

use Ajosav\Blinqpay\Exceptions\FileAlreadyExistException;
use Ajosav\Blinqpay\Utils\FilePathUtil;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 *
 */
class PaymentProcessorManager
{
    protected string $namespace;

    public function __construct()
    {
        $this->namespace = config('blinqpay.processor_namespace', 'App\\Blinqpay\\Processors');
    }

    /**
     * @param $name
     * @return string
     */
    public function getProcessorName($name): string
    {
        return File::exists(FilePathUtil::pathFromNamespace($this->namespace, $name)) ?
            FilePathUtil::pathFromNamespace($this->namespace, $name) : '';
    }

    /**
     * @param $name
     * @return string
     */
    public function getClassPath($name): string
    {
        return File::exists(FilePathUtil::pathFromNamespace($this->namespace, $name)) ?
            FilePathUtil::classNamespace($this->namespace, $name) . '\\' . FilePathUtil::className($name) : '';
    }

    /**
     * @param string $name
     * @return string
     */
    public function generate(string $name): string
    {
        // Checking if processor already exists
        $file_exists = File::exists(FilePathUtil::pathFromNamespace($this->namespace, $name));
        throw_if($file_exists, new FileAlreadyExistException('File already exists'));

        $content = $this->getStubContent($name);
        FilePathUtil::ensureDirectoryExists($this->namespace, $name);
        File::put(FilePathUtil::pathFromNamespace($this->namespace, $name), $content);
        return FilePathUtil::className($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getStubContent(string $name): string
    {
        return preg_replace_array(
            ['/\[namespace\]/', '/\[class\]/'],
            [FilePathUtil::classNamespace($this->namespace, $name), FilePathUtil::className($name)],
            file_get_contents(__DIR__ . '/stubs/PaymentProcessor.stub')
        );
    }

    /**
     * @param string $name
     * @return bool|null
     */
    public function delete(string $name): ?bool
    {
        // Checking if processor already exists
        $file_exists = File::exists(FilePathUtil::pathFromNamespace($this->namespace, $name));
        if ($file_exists) {
            return File::delete(FilePathUtil::pathFromNamespace($this->namespace, $name));
        }
        return false;
    }

    /**
     * @param string $slug
     * @return string
     */
    public function getFileNameFromSlug(string $slug): string
    {
        return Str::studly(Str::title(str_replace('-', '_', $slug)));
    }
}