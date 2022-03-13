<?php

namespace Sculptor\Agent\Validation;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Exception;
use Illuminate\Support\Facades\Validator as Validation;
use Illuminate\Support\Str;
use Sculptor\Agent\Exceptions\ValidationException;

class Validator
{
    /**
     * @var string
     */
    public const NAMESPACE = 'Sculptor\\Agent\\Validation\\Validators\\';

    /**
     * @var string|null
     */
    private ?string $scope;

    /**
     * @var string
     */
    private string $error;

    /**
     * Validator constructor.
     * @param string|null $scope
     */
    public function __construct(string $scope = null)
    {
        $this->scope = $scope;
    }

    /**
     * @param string $scope
     * @return Validator
     */
    public static function make(string $scope): Validator
    {
        return new Validator($scope);
    }

    /**
     * @param string $name
     * @return array
     */
    public function rule(string $name): array
    {
        $normalized = collect(explode('.', $name))->map(fn($item) => Str::ucfirst($item))->join('');

        $composed = Validator::NAMESPACE . "{$this->scope}{$normalized}";

        $rule = resolve($composed);

        return $rule->rule();
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function validate(string $name, string $value): bool
    {
        try {
            $rule = $this->rule($name);

            $name = Str::of($name)->replace('.', '_');

            $validated = Validation::make(["$name" => $value], $rule);

            if (!$validated->fails()) {
                return true;
            }

            $this->error = $validated->errors()->first();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        return false;
    }

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @throws ValidationException
     */
    public function validateKeyValue(string $key, string $value): void
    {
        if (!$this->validate($key, $value)) {
            throw new ValidationException($key, $value, $this->error());
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateKeysValues(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->validateKeyValue($key, $value);
        }
    }

}
