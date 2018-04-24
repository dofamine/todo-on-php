<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 21.03.2018
 * Time: 13:24
 */

class ControllerMain extends Controller
{
    public function action_index()
    {
        $view = new View("main");
        $view->useTemplate();
        $view->css = "main";
        $view->main = true;
        $this->response($view);
    }

    public function action_delete()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $note_id = (int)$this->getUriParam("id");
        $model_notes = new ModelNotes();
        $model_notes->delateByNoteId($note_id);
        $this->redirect($_SERVER["HTTP_REFERER"]);
    }

    public function action_change()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $id = (int)$this->getUriParam("id");
        $note = ModuleDatabaseConnection::instance()->notes->getElementById($id);
        if (!$note) $this->redirect404();
        $notes_model = new ModelNotes();
        $note = $notes_model->getById($id);
        $image = $notes_model->getImageOfNote($id);
        $view = new View("updateform");
        $view->useTemplate("default2");
        $view->note = $note;
        if ($image) $view->image = $image;
        $view->css = "addnote";
        $view->description = "Change and save inforation into fields below";
        $this->response($view);
    }

    public function action_addNote()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $name = @$_POST["name"];
        $description = @$_POST["description"];
        $url = @$_POST["url"];
        $add_image = @$_POST["add_image"];
        if (empty($name) || empty($description)) throw new Exception("Enter all notes fields");
        $model_notes = new ModelNotes();
        if (isset($add_image)) {
            if (empty($url)) throw new Exception("Enter all images fields");
            $model_image = new ModelImages();
            $img_id = $model_image->addImage(new \Entity\Image($url));
        }
        $model_notes->addNote(new \Entity\Note(
            $name,
            $description,
            (int)ModuleAuth::instance()->getUser()["id"], @$img_id));
        $this->redirect("/todo");
    }

    public function action_notes()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $user = ModuleAuth::instance()->getUser();
        $view = new View("note");
        $view->useTemplate("default2");
        $note_model = new ModelNotes();
        $view->notes = $note_model->getAllByUserId((int)$user["id"]);
        $view->main = true;
        $view->user = $user["login"];
        $view->css = "notes";
        $this->response($view);
    }

    public function action_register()
    {
        $view = new View("register");
        $view->useTemplate();
        $view->css = "reg";
        $this->response($view);
    }

    public function action_new()
    {
        $view = new View("addnote");
        $view->useTemplate("default2");
        $view->css = "addnote";
        $view->description = "Enter information into fields below";
        $this->response($view);
    }

    public function action_updateNote()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $description = @$_POST["description"];
        $url = isset($_POST["url"])?trim($_POST["url"]):"";
        $add_image = isset($_POST["add_image"]);
        $image_id = @$_POST["image_id"];
        if (empty($_POST["name"]) || empty($_POST["description"])) throw new Exception("Enter all notes fields");
        $model_notes = new ModelNotes();
        $model_notes->updateNote(
            (int)$this->getUriParam("id"),
            trim($_POST["name"]),
            $description,
            $add_image,
            $url,
            $image_id);
        $this->redirect("/todo");
    }

    public function action_done()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $note_id = (int)$this->getUriParam("id");
        $note = new ModelNotes();
        $note->makeDoneByNoteId($note_id);
        $this->redirect($_SERVER["HTTP_REFERER"]);
    }

    public function action_showNote()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $note_id = (int)$this->getUriParam("id");
        $note_model = new ModelNotes();
        $note = $note_model->getById($note_id);
        $view = new View("description");
        if (!empty($note)) $view->note = $note;
        $view->css = "desc";
        $view->description = "Overview of note : \"{$view->note->name}\"";
        $view->useTemplate("default2");
        $this->response($view);
    }


    public function action_showHistoryList()
    {
        if (!ModuleAuth::instance()->isAuth()) $this->redirect(URLROOT);
        $user_id = (int)ModuleAuth::instance()->getUser()["id"];
        $model_notes = new ModelNotes();
        $notes = $model_notes->getDoneNotesByUserId($user_id);
        $view = new View("note");
        $view->useTemplate("default2");
        $view->notes = $notes;
        $view->done = true;
        $view->css = "notes";
        $view->description = "Your history";
        $this->response($view);
    }
}