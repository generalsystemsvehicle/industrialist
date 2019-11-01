<?php

namespace Riverbedlab\Industrialist\Models;

use Str;
use Illuminate\Contracts\Support\Arrayable;
use Riverbedlab\Industrialist\Exceptions\AttributeNotFoundException;
use Riverbedlab\Industrialist\Exceptions\MethodNotFoundException;

abstract class AbstractStatelessModel implements Arrayable
{
    protected $attributes = [];

    /**
     * Magic method used to create getter and setter functions. For example, setMyAttribute or getMyAttribute.
     *
     * @param string $name The function name being request, eg: getMyAttribute or setMyAttribute
     * @param array $params Any value to assign when the method called is a setter
     *
     * @return mixed the value of the attribute regardless of getter/setter if the attribute is found
     *
     * @throws MethodNotFoundException
     *
     */
    public function __call(string $name, array $params)
    {
        if (Str::startsWith($name, 'get')) {
            return $this->getAttributeReferenceFromCallName($name);
        }
        if (Str::startsWith($name, 'set')) {
            $ref = &$this->getAttributeReferenceFromCallName($name);
            $ref = $params[0];
            return $ref;
        }
        throw new MethodNotFoundException();
    }

    /**
     * Converts the provided getter or setter name and create a snake_case attribute key, then return a reference to the attribute element.
     *
     * @param string $name The function name to convert to a snake case attribute key
     *
     * @return mixed the reference to the attribute
     *
     * @throws AttributeNotFoundException
     *
     */
    protected function &getAttributeReferenceFromCallName(string $name)
    {
        $key = Str::snake(substr($name, 3));
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        throw new AttributeNotFoundException();
    }

    /**
     * Get the instance as an array. Satisfies Arrayable contract.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
