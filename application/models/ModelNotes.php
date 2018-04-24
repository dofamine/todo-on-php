<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 15.04.2018
 * Time: 14:25
 */

use Entity\Note;

class ModelNotes extends Model
{
    public function getAllByUserId(int $id): array
    {
        return Note::fromAssocies($this->db
            ->notes
            ->where("users_id", "=", $id)
            ->andWhere("done", "<>", "1")
            ->all());
    }

    public function getById(int $id): Note
    {
        $note = new Note();
        $note->fromAssoc($this->db->notes->getElementById($id));
        return $note;
    }

    public function addNote(Note $note): int
    {
        return $this->db->notes->insert([
            "name" => $note->name,
            "description" => $note->description,
            "users_id" => $note->users_id,
            "images_id" => $note->images_id,
            "done" => $note->done
        ]);
    }

    public function getImageOfNote(int $id):?\Entity\Image
    {
        $note = ModuleDatabaseConnection::instance()
            ->notes
            ->getElementById($id);
        if ($note["images_id"] !== NULL) {
            $image = new ModelImages();
            return $image->getById((int)$note["images_id"]);
        }
        return NULL;
    }

    public function updateById(Note $note)
    {
        ModuleDatabaseConnection::instance()
            ->notes
            ->updateById($note->id, [
                "name" => $note->name,
                "description" => $note->description,
                "images_id" => $note->images_id
            ]);
    }

    public function makeDoneByNoteId(int $id)
    {
        ModuleDatabaseConnection::instance()->notes->updateById($id, ["done" => true]);
    }

    public function getDoneNotesByUserId(int $id):array
    {
        return Note::fromAssocies($this->db
            ->notes
            ->where("users_id", "=", $id)
            ->andWhere("done", "=", "1")
            ->all());
    }

    public function delateByNoteId(int $id)
    {
        $this->db->notes->deleteById($id);
    }

    public function updateNote($id,$name,$description,$add_image,$url_img,$image_id){
        $model_images = new ModelImages();
        $note = new \Entity\Note($name,$description);
        $note->id = $id;
        if ($add_image) {
            if (empty($url_img)) throw new Exception("Enter all images fields");
            $image = new \Entity\Image($url_img);
            if (!empty($image_id)) {
                $model_images->updateById((int)$image_id, $image);
                $note->images_id = (int)$image_id;
            } else $note->images_id = $model_images->addImage($image);
        }
        if (!empty($image_id) && !isset($add_image)) {
            $model_images->deleteById((int)$image_id);
            $note->images_id = null;
        }
        $this->updateById($note);
    }
}