<?php

namespace Sculptor\Agent\Actions\Backups;

use Exception;
use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Actions\Backups\Support\Tag;
use Sculptor\Agent\Repositories\Backups;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Archive
{
    public function __construct(private Backups $backups, private Archives $archives, private Compressions $compression)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function all(string $name): array
    {
        $backup = $this->backups->find($name);

        $compression = $this->compression->make($backup->compression);

        $tag = Tag::make($backup)->extension($compression->extension());

        $archive = $this->archives->make($backup->archiveDriver)->create($tag->archive());

        return collect($archive->list('/'))
            ->filter(fn($file) => $tag->match($file->name()))
            ->toArray();
    }
}
