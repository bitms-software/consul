<?php


namespace Bitms\Component\Consul\Model;


class KVStore
{
    private string $key;

    private string|array $value;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return array|string
     */
    public function getValue(): array|string
    {
        if(empty($this->value)) {
            $this->value= "";
        }

        return $this->value;
    }

    /**
     * @param array|string $value
     */
    public function setValue(array|string $value): void
    {
        $this->value = $value;
    }
}