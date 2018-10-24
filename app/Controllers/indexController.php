<?php
namespace App\Controllers;
use App\Models\{Job, Project};

class IndexController {
    
    public function indexAction(){


        $jobs= Job::all();
        $projects=Project::all();    
        $lastName='Quimbaya Orozco';
        $name='John Fredy '.$lastName;
        $limitMonths=5000;
        include_once('../views/index.php');
            
    }

    
}

?>