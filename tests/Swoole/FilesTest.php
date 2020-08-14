<?php

namespace Utopia\Tests;

use Utopia\Swoole\Files;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function testParse()
    {
        Files::load(__DIR__.'/../resources');

        $this->assertEquals(11, Files::getCount());
        
        $this->assertEquals(false, Files::isFileLoaded('/index.php'));
        $this->assertEquals(false, Files::isFileLoaded('/unknown.jpg'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/scripts/app.js'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/styles/default-ltr.css'));
        $this->assertEquals(true, Files::isFileLoaded('/dist/styles/default-rtl.css'));

        $this->assertEquals('application/vnd.ms-fontobject', Files::getFileMimeType('/fonts/poppins-v9-latin-100.eot'));
        $this->assertEquals('image/svg+xml', Files::getFileMimeType('/fonts/poppins-v9-latin-100.svg'));
        $this->assertContains(Files::getFileMimeType('/fonts/poppins-v9-latin-100.ttf'), ['font/sfnt', 'application/font-sfnt', 'application/x-font-ttf']);
        $this->assertEquals('application/octet-stream', Files::getFileMimeType('/fonts/poppins-v9-latin-100.woff'));
        $this->assertEquals('application/octet-stream', Files::getFileMimeType('/fonts/poppins-v9-latin-100.woff2'));
        $this->assertEquals('image/png', Files::getFileMimeType('/images/logo.png'));
        $this->assertEquals('text/javascript', Files::getFileMimeType('/dist/scripts/app.js'));
        $this->assertEquals('text/javascript', Files::getFileMimeType('/dist/scripts/app.js'));
        $this->assertEquals('text/css', Files::getFileMimeType('/dist/styles/default-ltr.css'));
        $this->assertEquals('text/css', Files::getFileMimeType('/dist/styles/default-rtl.css'));

        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.eot'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.svg'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.ttf'));
        $this->assertNotEmpty(Files::getFileContents('/fonts/poppins-v9-latin-100.woff'));
        $this->assertNotEmpty(Files::getFileMimeType('/fonts/poppins-v9-latin-100.woff2'));
        $this->assertNotEmpty(Files::getFileContents('/images/logo.png'));
        $this->assertNotEmpty(Files::getFileContents('/dist/scripts/app.js'));
        $this->assertNotEmpty(Files::getFileContents('/dist/scripts/app.js'));
        $this->assertNotEmpty(Files::getFileContents('/dist/styles/default-ltr.css'));
        $this->assertNotEmpty(Files::getFileContents('/dist/styles/default-rtl.css'));

    }
}