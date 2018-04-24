<?php

namespace Entity;


class Image extends Entity
{
    public $id,$url;

    public function __construct(string $url="")
    {
        $this->url = $url;
    }
    public static function fromAssocies(array $array): array
    {
        return self::_fromAssocies($array,self::class);
    }
}