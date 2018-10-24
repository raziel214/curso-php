<?php
namespace  App\Controllers;

class JobsController{

    public function getAddJobAction($request){

        if (!empty($_POST)) {
            $job=new Job();
            $job->title =$_POST['title'];
            $job->description =$_POST['description'];
            $job->save();
        }
        include_once('../views/addJob.php');

    }

}