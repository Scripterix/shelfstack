<?php
declare(strict_types=1);

const APP_NAME = 'ShelfStack';

function app_base_path(): string
{
    static $basePath;

    if ($basePath !== null) {
        return $basePath;
    }

    $configuredPath = getenv('BOOKSTORE_BASE_PATH');
    if ($configuredPath !== false) {
        $basePath = '/' . trim(str_replace('\\', '/', $configuredPath), '/');
        return $basePath === '/' ? '' : $basePath;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $applicationRoot = realpath(dirname(__DIR__));

    if ($documentRoot !== false && $applicationRoot !== false) {
        $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
        $normalizedApplicationRoot = str_replace('\\', '/', $applicationRoot);

        $applicationRootLower = strtolower($normalizedApplicationRoot);
        $documentRootLower = strtolower($normalizedDocumentRoot);
        $isInsideDocumentRoot = $applicationRootLower === $documentRootLower
            || str_starts_with($applicationRootLower, $documentRootLower . '/');

        if ($isInsideDocumentRoot) {
            $relativePath = substr($normalizedApplicationRoot, strlen($normalizedDocumentRoot));
            $basePath = '/' . trim($relativePath, '/');
            return $basePath === '/' ? '' : $basePath;
        }
    }

    $basePath = '';
    return $basePath;
}

function app_url(string $path = ''): string
{
    $basePath = app_base_path();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $basePath === '' ? '/' : $basePath . '/';
    }

    return $basePath . '/' . $path;
}

function env_value(string $name, string $default): string
{
    $value = getenv($name);
    return $value === false ? $default : $value;
}
