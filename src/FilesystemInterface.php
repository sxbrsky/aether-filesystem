<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Filesystem;

interface FilesystemInterface
{
    /**
     * Checks if a file exists at the given path.
     *
     * @param string $path
     * @return bool
     * @throws \Aether\Filesystem\IOException
     */
    public function exists(string $path): bool;

    /**
     * Gets the contents of a file.
     *
     * @param string $path
     * @return string
     */
    public function read(string $path): string;

    /**
     * Writes the contents to a file.
     *
     * @param string $path
     * @param string $contents
     * @param bool $lock
     * @return void
     * @throws \Aether\Filesystem\IOException
     */
    public function write(string $path, string $contents, bool $lock = false): void;

    /**
     * Copies a file to a new location.
     *
     * @param string $source
     * @param string $destination

     * @return void
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function copy(string $source, string $destination): void;

    /**
     * Moves a file to a new location.
     *
     * @param string $source
     * @param string $destination
     *
     * @return void
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function move(string $source, string $destination): void;

    /**
     * Appends contents to a file.
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function append(string $path, string $contents): bool;

    /**
     * Sets access and modification time of file.
     *
     * @param string $filename
     * @param int|null $mtime
     * @param int|null $atime
     */
    public function touch(string $filename, ?int $mtime = null, ?int $atime = null): void;

    /**
     * Deletes a file.
     *
     * @param string|array $filename
     * @return void
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function unlink(string|array $filename): void;

    /**
     * Creates a directory.
     *
     * @param string $directory
     * @param int $mode
     * @param bool $recursive
     *
     * @return void
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function mkdir(string $directory, int $mode = 0777, bool $recursive = true): void;

    /**
     * Deletes a directory.
     *
     * @param string $directory
     * @return void
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function rmdir(string $directory): void;

    /**
     * Gets the name.
     *
     * @param string $path
     * @return string
     */
    public function name(string $path): string;

    /**
     * Gets the basename.
     *
     * @param string $path
     * @return string
     */
    public function basename(string $path): string;

    /**
     * Gets the dirname.
     *
     * @param string $path
     * @return string
     */
    public function dirname(string $path): string;

    /**
     * Gets the file extension.
     *
     * @param string $path
     * @return string
     */
    public function extension(string $path): string;

    /**
     * Gets the file size.
     *
     * @param string $path
     * @return int
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function filesize(string $path): int;

    /**
     * Check if a path is a directory.
     *
     * @param string $path
     * @return bool
     */
    public function isDirectory(string $path): bool;

    /**
     * Check if a path is a file.
     *
     * @param string $path
     * @return bool
     */
    public function isFile(string $path): bool;

    /**
     * Get the mime type of file.
     *
     * @param string $path
     * @return string
     *
     * @throws \Aether\Filesystem\IOException
     */
    public function mimeType(string $path): string;
}
