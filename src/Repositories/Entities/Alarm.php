<?php


namespace Sculptor\Agent\Repositories\Entities;


use Exception;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Entity as EntityInterface;
use Sculptor\Agent\Repositories\Support\Entity;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 * @property bool $enabled
 * @property string $error
 * @property string $previous
 * @property string $cron
 * @property string $actionMethod
 * @property string $actionTo
 * @property string $conditionMethod
 * @property string $conditionSubject
 * @property bool $statusAlarmed
 * @property string $statusAt
 * @property string $statusLast
 * @property string $statusRearm
 * @property array $actionParametersArray
 * @property bool $statusPrevious
 * @property array $conditionParametersArray
 * @property array $subjectParametersArray
 * @property array $rearmParametersArray
 * @property string $subjectMethod
 * @property string $rearmMethod
 * @property float $subjectLast
 * @property bool $statusAlarmedBool
 * @property bool $statusPreviousBool
 * @property string $actionMessage
 */
class Alarm extends Entity implements EntityInterface
{
    protected array $fields = [ ];

    /**
     * @throws InvalidConfigurationException
     */
    public function __construct(Configuration $configuration, YmlFile $yml)
    {
        parent::__construct($configuration, $yml);

        $yml->verify(1);
    }

    public function runnable(): bool
    {
        return $this->enabled;
    }

    /**
     * @throws Exception
     */
    public function success(): void
    {
        // $this->save([ 'status' => Enum::OK, 'last' => now(), 'error' => '' ]);
    }

    /**
     * @throws Exception
     */
    public function error(string $error): void
    {
        // $this->save(['status' => BackupStatusType::ERROR, 'last' => now(), 'error' => $error]);
    }

    public function subjectParameters(): Parameters
    {
        return Parameters::make($this->subjectParametersArray);
    }

    public function conditionParameters(): Parameters
    {
        return Parameters::make($this->conditionParametersArray);
    }

    public function actionParameters(): Parameters
    {
        return Parameters::make($this->actionParametersArray);
    }

    public function rearmParameters(): Parameters
    {
        return Parameters::make($this->rearmParametersArray);
    }

    public function raised(): bool
    {
        return $this->statusAlarmed && !$this->statusPrevious;
    }
}
