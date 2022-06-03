<?php

namespace Sculptor\Agent\Actions\Webhook\Support;

use Exception;
use Illuminate\Support\Facades\DB;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Entities\Domain;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Repository
{
    /**
     * @throws Exception
     */
    public function __construct(Configuration $configuration)
    {
        $configuration->webhookDatabase();
    }

    public function exists($token): bool
    {
        return DB::table('deploys')->where('token', $token)->count() > 0;
    }

    public function insert(Domain $domain): void
    {
        DB::insert('insert into deploys (token, status, created_at, updated_at) values(?, ?, ?, ?)', [ $domain->token, 'new', now(), now() ]);
    }

    public function pending(): array
    {
        return DB::table('deploys')->where('status', 'requested')->get()->toArray();
    }

    public function all(): array
    {
        return DB::table('deploys')->get()->toArray();
    }

    public function status($deploy)
    {
        DB::update('update deploys set status = ? where token = ?', ['deployed', $deploy->token]);
    }

    public function message($deploy, string $message)
    {
        DB::update('update deploys set status = ? where token = ?', [$message, $deploy->token]);
    }
}
