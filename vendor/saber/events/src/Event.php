<?php
namespace Saber\Events;

class Event
{
    private $id;
    private $type;
    private $data;

    public function getId()
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function __construct(string $type, $data = null, $id = null)
    {
        $this->type = $type;
        $this->data = $data;
        $this->id = is_null($id) ? flakeid_generate() : $id;
    }

    public function __toString()
    {
        $event = [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
        ];

        return json_encode($event);
    }

    public static function fromString(string $string)
    {
        $event = json_decode($string, true);

        if (json_last_error() === JSON_ERROR_NONE) {

            if (!is_array($event) || !array_key_exists('type', $event)) {
                throw new \InvalidArgumentException('param is invalid');
            }

            return static::fromArray($event);
        }

        throw new \InvalidArgumentException(json_last_error_msg(), json_last_error());
    }

    public static function fromArray(array $array)
    {
        return new static(
            $array['type'],
            array_key_exists('data', $array) ? $array['data'] : null,
            array_key_exists('id', $array) ? $array['id'] : null
        );
    }
}
