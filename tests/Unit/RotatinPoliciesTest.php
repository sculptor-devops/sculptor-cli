<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Actions\Backups\Archives\Support\ArchiveFile;
use Sculptor\Agent\Actions\Backups\Rotations\Count;
use Sculptor\Agent\Actions\Backups\Rotations\Days;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\YmlFile;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class RotatinPoliciesTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    private function file(string $name, int $timestamp, int $size): ArchiveFile
    {
        return new ArchiveFile(['basename' => $name, 'path' => '', 'timestamp' => $timestamp, 'size' => $size, 'type' => 'file', 'extension' => 'txt' ]);
    }

    public function test_days_policy(): void
    {
        $policy = new Days();

        $file1 = $this->file('file1.txt', now()->addDays(10)->timestamp, 10);

        $file2 = $this->file('file2.txt', now()->addDays(20)->timestamp, 20);

        $file3 = $this->file('file3.txt',now()->addDays(5)->timestamp, 5);

        $files = [ $file1, $file2, $file3 ];

        $rotated = $policy->rotate($files, 10);

        $this->assertCount(1, $rotated);

        $this->assertEquals($file2, collect($rotated)->first());
    }

    public function test_count_policy(): void
    {
        $policy = new Count();

        $file1 = $this->file('file1.txt', now()->addDays(10)->timestamp, 10);

        $file2 = $this->file('file2.txt', now()->addDays(20)->timestamp, 20);

        $file3 = $this->file('file3.txt',now()->addDays(5)->timestamp, 5);

        $files = [ $file1, $file2, $file3 ];

        $rotated = $policy->rotate($files, 1);

        $this->assertCount(2, $rotated);

        $this->assertEquals($file1, collect($rotated)->first());

        $this->assertEquals($file2, collect($rotated)->last());
    }
}
