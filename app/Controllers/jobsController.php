<?php
namespace  App\Controllers;

class JobsController{

    public function getAddJobAction($request){
        

        if ($reuest->getMethod()=='POS') {
            $postData= $request->getParsedBody();
            $job=new Job();
            $job->title =$postData['title'];
            $job->description =$postData['description'];
            $job->save();
        }
        include_once('../views/addJob.php');

    }

}