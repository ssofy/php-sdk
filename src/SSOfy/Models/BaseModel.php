<?php

namespace SSOfy\Models;

use SSOfy\Exceptions\InvalidValueException;
use SSOfy\Exceptions\RequiredAttributeException;

class BaseModel implements \JsonSerializable
{
    /** @var string[] */
    protected $properties = [];

    /** @var string[] */
    protected $required = [];

    /** @var array */
    protected $defaults = [];

    protected $values = [];

    public function __construct($attributes = [])
    {
        $this->initialize($attributes);
    }

    /**
     * @return BaseModel
     */
    public static function make($attributes)
    {
        $class = get_called_class();
        return new $class($attributes);
    }

    protected function validate($attr, $value)
    {
        return true;
    }

    /**
     * @throws RequiredAttributeException
     * @throws InvalidValueException
     */
    protected function export()
    {
        foreach ($this->required as $requiredAttr) {
            if (!isset($this->values[$requiredAttr])) {
                throw new RequiredAttributeException($requiredAttr);
            }
        }

        foreach ($this->values as $attr => $value) {
            if (true !== $message = $this->validate($attr, $value)) {
                throw new InvalidValueException($attr, $message);
            }
        }

        return $this->values;
    }

    public function toArray()
    {
        $result = $this->export();
        $clean  = array();

        foreach ($result as $key => $val) {
            if (empty($val)) {
                continue;
            }

            $clean[$key] = $val;
        }

        array_walk_recursive($clean, function (&$value) {
            if ($value instanceof \DateTime) {
                $value = $value->format(\DateTime::ISO8601);
            }
        });

        return json_decode(json_encode($clean), true);
    }

    /**
     * @throws InvalidValueException
     */
    public function __set($name, $value)
    {
        if (true !== $message = $this->validate($name, $value)) {
            throw new InvalidValueException($name, $message);
        }

        $this->getOrSet($name, $value);
    }

    public function __get($name)
    {
        return $this->getOrSet($name, null);
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    private function getOrSet($attr, $value)
    {
        if (!key_exists($attr, $this->values)) {
            return null;
        }

        if (is_null($value)) {
            return $this->values[$attr];
        }

        $this->values[$attr] = $value;

        return $this;
    }

    private function initialize($attributes)
    {
        foreach ($this->properties as $attr) {
            $value = isset($attributes[$attr]) ? $attributes[$attr] : (isset($this->defaults[$attr]) ? $this->defaults[$attr] : null);

            $this->values[$attr] = $value;

            if (!is_null($value) && true !== $message = $this->validate($attr, $value)) {
                throw new InvalidValueException($attr, $message);
            }

            unset($attributes[$attr]);
        }
    }
}
