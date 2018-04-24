<?php

namespace Entity;


class Note extends Entity
{
    public $id, $name, $description, $users_id, $images_id, $done;

    public function __construct(string $name = "", string $description = "",
                                int $users_id = 0, int $images_id = null, int $done = 0)
    {
        $this->name = $name;
        $this->description = $description;
        $this->users_id = $users_id;
        $this->images_id = $images_id;
        $this->done = $done;
    }

    public static function fromAssocies(array $array): array
    {
        return self::_fromAssocies($array, self::class);
    }
    public function getImageOfNote():?Image
    {
        if ($this->images_id === null) return NULL;
        $image = new Image();
        $image->fromAssoc(\ModuleDatabaseConnection::instance()->images->getElementById($this->images_id));
        return $image;
    }

}