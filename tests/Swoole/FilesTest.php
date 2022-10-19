<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Swoole\Files;

class FilesTest extends TestCase
{
    public function setUp(): void
    {
        Files::load(__DIR__.'/../resources');
    }

    public function tearDown(): void
    {
        Files::reset();
    }

    public function testCanLoadDirectory(): void
    {
        $this->assertEquals(11, Files::getCount());
    }

    public function testCanIgnoreDuplicateFilesWhenLoading(): void
    {
        $this->assertEquals(11, Files::getCount());
        Files::load(__DIR__.'/../resources');
        $this->assertEquals(11, Files::getCount());
    }

    public function testCanReset(): void
    {
        Files::reset();

        $this->assertEquals(0, Files::getCount());
    }

    public function testCanCheckIfFileHasLoaded(): void
    {
        $this->assertEquals(false, Files::isFileLoaded('/index.php'));
        $this->assertEquals(false, Files::isFileLoaded('/unknown.jpg'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/scripts/app.js'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/styles/default-ltr.css'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/styles/default-rtl.css'));
    }

    public function testCanGetFileMimeType(): void
    {
        $this->assertEquals('application/vnd.ms-fontobject', Files::getFileMimeType('/fonts/poppins-v9-latin-100.eot'));
        $this->assertEquals('image/svg+xml', Files::getFileMimeType('/fonts/poppins-v9-latin-100.svg'));
        $this->assertEquals('application/octet-stream', Files::getFileMimeType('/fonts/poppins-v9-latin-100.woff'));
        $this->assertEquals('application/octet-stream', Files::getFileMimeType('/fonts/poppins-v9-latin-100.woff2'));
        $this->assertEquals('image/png', Files::getFileMimeType('/images/logo.png'));
        $this->assertEquals('text/javascript', Files::getFileMimeType('/dist/scripts/app.js'));
        $this->assertEquals('text/javascript', Files::getFileMimeType('/dist/scripts/app.js'));
        $this->assertEquals('text/css', Files::getFileMimeType('/dist/styles/default-ltr.css'));
        $this->assertEquals('text/css', Files::getFileMimeType('/dist/styles/default-rtl.css'));
        $this->assertContains(Files::getFileMimeType('/fonts/poppins-v9-latin-100.ttf'), ['font/sfnt', 'application/font-sfnt', 'application/x-font-ttf']);
    }

    public function testCanGetFileContents(): void
    {
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.eot'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.svg'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.ttf'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.woff'));
        $this->assertNotEmpty(Files::getFileContents('/images/logo.png'));
        $this->assertNotEmpty(Files::getFileContents('/dist/scripts/app.js'));
        $this->assertNotEmpty(Files::getFileContents('/dist/scripts/app.js'));
        $this->assertNotEmpty(Files::getFileContents('/dist/styles/default-ltr.css'));
        $this->assertNotEmpty(Files::getFileContents('/dist/styles/default-rtl.css'));
    }
}
