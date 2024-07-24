<?php

namespace Ajosav\Blinqpay\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FilePathUtil
{
    public static function getAppPathFromNamespace(string $namespace): string
    {
        $base_path = Str::replaceFirst(app()->getNamespace(), '', $namespace);
        return app('path') . '/' . str_replace('\\', '/', $base_path);
    }
    public static function pathFromNamespace($namespace, $relativePath): string
    {
        $extended_path = implode('\\', array_map(function ($value) {
            return ucfirst($value);
        }, explode(DIRECTORY_SEPARATOR, $relativePath)));

        $base_path = Str::replaceFirst(app()->getNamespace(), '', $namespace);
        $path = $base_path . DIRECTORY_SEPARATOR . $extended_path . '.php';

        return app('path') . '/' . str_replace('\\', '/', $path);
    }

    public static function classNamespace($namespace, $relativePath): string
    {
        $path = array_map(function ($value) {
            return ucfirst($value);
        }, explode(DIRECTORY_SEPARATOR, $relativePath));

        array_pop($path);

        return rtrim($namespace . '\\' . implode('\\', $path), '\\');
    }

    public static function className($relativePath): string
    {
        $path = explode(DIRECTORY_SEPARATOR, $relativePath);

        return ucfirst(array_pop($path));
    }

    protected function ensureDirectoryExists($namespace, $relativePath)
    {
        $path = $this->pathFromNamespace($namespace, $relativePath);

        if (!File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, $recursive = true, $force = true);
        }
    }
}