<?php
namespace  App\Controllers;

use App\Models\Project;

class ProjectController extends BaseController{

  public function getAddProjectAction(){   
            if (!empty($_POST)) {
                $project=new Project();
                $project->title =$_POST['title'];
                $project->description =$_POST['description'];
                $project->save();
            }
            //include_once('../views/addProject.php');
            echo $this->renderHTML('addProject.twig');

    }
}

?>