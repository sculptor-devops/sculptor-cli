<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Validation\Validator;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Setup
{
    use Logging;

    public function __construct(private Domains $domains, private Folders $folders)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, string $key, string $value): void
    {
        $domain = $this->domains->find($name);

        $this->info($domain, ['key' => $key, 'value' => $value]);

        Validator::make('Domain')->validateKeyValue($key, $this->normalizeValidation($value));

        if ($key == 'name') {
            throw new Exception("Domains cannot be renamed, create a new one");
        }

        if ($key == 'template') {
            $this->template($value, $domain->configs());
        }

        $domain->save([
            "$key" => $this->normalizeSave($value)
        ]);
    }

    private function normalizeValidation(string $value): string
    {
        if ($value == '1' || $value == 'yes' || $value == 'true') {
            return '1';
        }

        if ($value == '0' || $value == 'no' || $value == 'false') {
            return '0';
        }

        return $value;
    }

    private function normalizeSave(string $value): string
    {
        if ($value == '1' || $value == 'yes' || $value == 'true') {
            return 'true';
        }

        if ($value == '0' || $value == 'no' || $value == 'false') {
            return 'false';
        }

        return $value;
    }

    /**
     * @throws Exception
     */
    private function template(string $value, string $path): void
    {
        Filesystem::fromTemplateDirectory($value, $path, $this->folders->templates());
    }
}
