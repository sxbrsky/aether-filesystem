<?php

namespace Aether\Filesystem;

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

use Aether\Contracts\Filesystem\Filesystem as FilesystemContract;
use Aether\Contracts\Filesystem\IOException;

class Filesystem implements FilesystemContract
{
    public function exists(string $path): bool
    {
        if (\strlen($path) > \PHP_MAXPATHLEN - 2) {
            throw new IOException("Could not check if file exists because path exceeds  " . (\PHP_MAXPATHLEN - 2) . " characters.");
        }

        return \file_exists($path);
    }

    public function read(string $path): string
    {
        if (! $this->exists($path) && ! $this->isFile($path)) {
            throw new IOException("File $path does not exists.");
        }

        return \file_get_contents($path);
    }

    public function write(string $path, string $contents, bool $lock = false): void
    {
        if (\file_put_contents($path, $contents, $lock ? \LOCK_EX : 0) === false) {
            throw new IOException("Failed to save file $path.");
        }
    }

    public function copy(string $source, string $destination): void
    {
        if (\copy($source, $destination) === false) {
            throw new IOException("Filed to copy '".$source."' to '".$destination."'.");
        }
    }

    public function move(string $source, string $destination): void
    {
        if (\rename($source, $destination) === false) {
            throw new IOException("Failed to rename '".$source."' to '".$destination."'.");
        }
    }

    public function append(string $path, string $contents): bool
    {
        return \file_put_contents($path, $contents, \FILE_APPEND) !== false;
    }

    public function touch(string $filename, ?int $mtime = null, ?int $atime = null): void
    {
        if (\touch($filename, $mtime, $atime) === false) {
            throw new IOException("Failed to touch '$filename'.");
        }
    }

    public function unlink(string|array $filename): void
    {
        $paths = \is_array($filename) ? $filename : [$filename];

        foreach ($paths as $path) {
            if (\unlink($path) === false) {
                throw new IOException("Failed to unlink '". $path ."'.");
            }
        }
    }

    public function mkdir(string $directory, int $mode = 0777, bool $recursive = false): void
    {
        if (\mkdir($directory, $mode, $recursive) === false) {
            throw new IOException("Failed to create a directory '".$directory."'.");
        }
    }

    public function rmdir(string $directory): void
    {
        if (! $this->isDirectory($directory)) {
            throw new IOException("Directory $directory does not exists.");
        }

        $items = new \FilesystemIterator($directory);
        foreach ($items as $item) {
            $item->isDir() && ! $item->isLink()
                ? $this->rmdir($item->getPathname())
                : $this->unlink($item->getPathname());
        }

        unset($items);
        @rmdir($directory);
    }

    public function name(string $path): string
    {
        return \pathinfo($path, \PATHINFO_FILENAME);
    }

    public function basename(string $path): string
    {
        return \pathinfo($path, \PATHINFO_BASENAME);
    }

    public function dirname(string $path): string
    {
        return \pathinfo($path, \PATHINFO_DIRNAME);
    }

    public function extension(string $path): string
    {
        return \pathinfo($path, \PATHINFO_EXTENSION);
    }

    public function filesize(string $path): int
    {
        if (! $this->exists($path)) {
            throw new IOException("File $path does not exists.");
        }

        return \filesize($path);
    }

    public function isDirectory(string $path): bool
    {
        return \is_dir($path);
    }

    public function isFile(string $path): bool
    {
        return \is_file($path);
    }

    public function mimeType(string $path): string
    {
        if (! $this->exists($path)) {
            throw new IOException("File not found $path");
        }

        return \mime_content_type($path);
    }
}
