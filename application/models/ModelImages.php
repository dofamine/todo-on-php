<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 21.03.2018
 * Time: 15:58
 */

use Entity\Image;

class ModelImages extends Model
{
    public function getAll()
    {
        return Image::fromAssocies($this->db->images->getAllWhere());
    }

    public function getById(int $id): Image
    {
        $img = new Image();
        $img->fromAssoc($this->db->images->getElementById($id));
        return $img;
    }

    public function addImage(Image $image): int
    {
        return $this->db->images->insert([
            "url"=>$image->url
        ]);
    }

    public function updateById(int $id, Image $image)
    {
        ModuleDatabaseConnection::instance()
            ->images
            ->updateById($id,["url"=>$image->url]);
    }
    public function deleteById(int $id)
    {
        ModuleDatabaseConnection::instance()
            ->images
            ->deleteById($id);
    }
}