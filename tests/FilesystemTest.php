<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\Filesystem;

use Aether\Contracts\Filesystem\Filesystem as FilesystemContract;
use Aether\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Filesystem::class)]
class FilesystemTest extends TestCase
{
    private ?string $tempDir;
    private FilesystemContract $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tmp';
        $this->filesystem->mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->exists($this->tempDir)) {
            $this->filesystem->rmdir($this->tempDir);
        }

        $this->tempDir = null;
    }

    public function testFileExists(): void
    {
        $basePath = $this->tempDir . DIRECTORY_SEPARATOR;
        $this->filesystem->touch($basePath . 'file.txt');

        self::assertFalse($this->filesystem->exists($basePath . 'file122.txt'));
        self::assertTrue($this->filesystem->exists($basePath . 'file.txt'));
    }

    public function testReadFileContent(): void
    {
        $basePath = $this->tempDir . DIRECTORY_SEPARATOR . 'file1.txt';
        \file_put_contents($basePath, 'test content');
        self::assertSame('test content', $this->filesystem->read($basePath));
    }

    public function testWriteFile(): void
    {
        $basePath = $this->tempDir . DIRECTORY_SEPARATOR . 'file.txt';

        $this->filesystem->write($basePath, 'test content');
        self::assertSame('test content', $this->filesystem->read($basePath));
    }

    public function testAppendContentToFile(): void
    {
        $basePath = $this->tempDir . DIRECTORY_SEPARATOR . 'file.txt';
        $this->filesystem->write($basePath, 'test content');
        $this->filesystem->append($basePath, 'test content');

        self::assertSame('test contenttest content', $this->filesystem->read($basePath));
    }

    public function testRemoveFiles(): void
    {
        $basePath = $this->tempDir . DIRECTORY_SEPARATOR . 'file1.txt';
        $this->filesystem->touch($basePath);
        $this->filesystem->unlink($basePath);

        self::assertFalse($this->filesystem->exists($basePath));
    }

    public function testRemoveDirectory(): void
    {
        $this->filesystem->mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'dir');
        $this->filesystem->rmdir($this->tempDir);

        self::assertFalse($this->filesystem->exists($this->tempDir));
    }

    public function testRemoveDirectoryRecursive(): void
    {
        $this->filesystem->mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'dir' . DIRECTORY_SEPARATOR . 'dir1', 0777, true);
        $this->filesystem->rmdir($this->tempDir);

        self::assertFalse($this->filesystem->exists($this->tempDir));
    }

    public function testCopyFile()
    {
        $source = $this->tempDir . '/file1.txt';
        $destination = $this->tempDir . '/file2.txt';

        $this->filesystem->write($source, 'test content');
        $this->filesystem->copy($source, $destination);

        self::assertTrue($this->filesystem->exists($destination));
        self::assertEquals('test content', $this->filesystem->read($destination));
    }

    public function testMoveFile()
    {
        $source = $this->tempDir . '/file1.txt';
        $destination = $this->tempDir . '/file2.txt';

        $this->filesystem->write($source, 'test content');
        $this->filesystem->move($source, $destination);

        self::assertFalse($this->filesystem->exists($source));
        self::assertTrue($this->filesystem->exists($destination));
        self::assertEquals('test content', $this->filesystem->read($destination));
    }

    public function testName(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR .'file1.txt';
        $this->filesystem->touch($file);

        self::assertSame('file1', $this->filesystem->name($file));
    }

    public function testDirname(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR .'file1.txt';
        $this->filesystem->touch($file);

        self::assertSame('/tmp/tmp', $this->filesystem->dirname($file));
    }

    public function testBasename(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR .'file1.txt';
        $this->filesystem->touch($file);

        self::assertSame('file1.txt', $this->filesystem->basename($file));
    }

    public function testExtension(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR .'file1.txt';
        $this->filesystem->touch($file);

        self::assertSame('txt', $this->filesystem->extension($file));
    }

    public function testFileSize(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR .'file1.txt';
        $this->filesystem->touch($file);

        self::assertSame(0, $this->filesystem->filesize($file));
    }

    public function testMimeType()
    {
        $path = $this->tempDir . '/file.txt';
        $this->filesystem->write($path, 'test');

        self::assertEquals('text/plain', $this->filesystem->mimeType($path));
    }
}
