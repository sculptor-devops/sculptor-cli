<?php


namespace Unit;


use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Filesystem;
use Tests\Dummy\RandomHome;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class FilesystemTest extends TestCase
{
    use RandomHome;

    private Configuration $configuration;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->app->make(Configuration::class);
    }

    /**
     * @throws Exception
     */
    public function test_make_directory_recursive(): void
    {
        $path = $this->configuration->root() . '/folder1/folder2';

        Filesystem::makeDirectoryRecursive($path);

        $this->assertTrue(file_exists($path));
    }

    /**
     * @throws Exception
     */
    public function test_from_template(): void
    {
        $filename = $this->configuration->root() . '/domain.yml';

        Filesystem::fromTemplateFile('domain.yml', $filename);

        $this->assertTrue(file_exists($filename));
    }

    /**
     * @throws Exception
     */
    public function test_template_file(): void
    {
        $content = Filesystem::templateFile('/templates/laravel/metadata.yml');

        $this->assertStringContainsString('engine:', $content);
    }

    /**
     * @throws Exception
     */
    public function test_from_template_directory(): void
    {
        $path = $this->configuration->root();

        Filesystem::fromTemplateDirectory('laravel', $path);

        $this->assertTrue(file_exists("$path/metadata.yml"), "file not found $path/metadata.yml");
    }

    /**
     * @throws Exception
     */
    public function test_get_files(): void
    {
        $path = $this->configuration->root();

        $files = Filesystem::getFiles($path);

        $this->assertNotCount(0, $files);
    }
}
