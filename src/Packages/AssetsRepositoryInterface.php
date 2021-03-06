<?php

namespace Symbiotic\Packages;

use Psr\Http\Message\StreamInterface;

/**
 * Interface AssetsRepositoryInterface
 * @package Symbiotic\Packages
 */
interface AssetsRepositoryInterface
{
    /**
     * @param string $package_id
     * @param string $path
     * @return StreamInterface
     * @throws \Throwable Если файл не найден
     */
    public function getAssetFileStream(string $package_id, string $path): StreamInterface;
}