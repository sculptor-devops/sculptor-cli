<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Request;
use Sculptor\Agent\Enums\LogContextType;
use Sculptor\Agent\Logs\Contracts\Logs as LogsInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Logs implements LogsInterface
{
    /*
     * @var string
     */
    /**
     * @var string
     */
    private string $tag = LogContextType::ACTIONS;

    /**
     * @param array $context
     * @return array
     */
    public function context(array $context = []): array
    {
        return array_merge($context, ['ip' => '127.0.0.1', 'tag' => $this->tag]);
    }

    /**
     * @param string $tag
     * @return LogsInterface
     */
    public function tag(string $tag): LogsInterface
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function actions(array $context = []): LogsContext
    {
        return new LogsContext($this->context($context));
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function security(array $context = []): LogsContext
    {
        return new LogsContext($this->tag(LogContextType::SECURITY)->context($context));
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function backup(array $context = []): LogsContext
    {
        return new LogsContext($this->tag(LogContextType::BACKUP)->context($context));
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function batch(array $context = []): LogsContext
    {
        return new LogsContext($this->tag(LogContextType::BATCH)->context($context));
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function login(array $context = []): LogsContext
    {
        return new LogsContext($this->tag(LogContextType::LOGIN)->context($context));
    }

    /**
     * @param array $context
     * @return LogsContext
     */
    public function cli(array $context = []): LogsContext
    {
        return new LogsContext($this->tag(LogContextType::CLI)->context($context));
    }
}
