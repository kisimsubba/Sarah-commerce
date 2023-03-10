<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
use function array_combine;
use function array_key_exists;
use function is_int;
use function var_export;
class NamedParameterMap extends AbstractMap
{
    use TypeTrait;
    use ValueToStringTrait;
    /**
     * @var mixed[]
     */
    protected $namedParameters;
    public function __construct(array $namedParameters, array $data = [])
    {
        $this->namedParameters = $this->filterNamedParameters($namedParameters);
        parent::__construct($data);
    }
    public function getNamedParameters() : array
    {
        return $this->namedParameters;
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            throw new InvalidArgumentException('Map elements are key/value pairs; a key must be provided for ' . 'value ' . var_export($value, \true));
        }
        if (!array_key_exists($offset, $this->namedParameters)) {
            throw new InvalidArgumentException('Attempting to set value for unconfigured parameter \'' . $offset . '\'');
        }
        if ($this->checkType($this->namedParameters[$offset], $value) === \false) {
            throw new InvalidArgumentException('Value for \'' . $offset . '\' must be of type ' . $this->namedParameters[$offset] . '; value is ' . $this->toolValueToString($value));
        }
        $this->data[$offset] = $value;
    }
    /**
     * @param mixed[] $namedParameters
     */
    protected function filterNamedParameters($namedParameters) : array
    {
        $names = [];
        $types = [];
        foreach ($namedParameters as $key => $value) {
            if (is_int($key)) {
                $names[] = $value;
                $types[] = 'mixed';
            } else {
                $names[] = $key;
                $types[] = $value;
            }
        }
        return array_combine($names, $types) ?: [];
    }
}
