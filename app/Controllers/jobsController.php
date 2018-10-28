<?php
namespace  App\Controllers;
use App\Models\Job;

class JobsController extends BaseController{

    public function getAddJobAction($request) {


        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $files =$request->getUploadedFiles();
            $logo=$files['logo'];
            if ($logo->getError() == UPLOAD_ERR_OK) {
                $fileName = $logo->getClientFilename();
                $logo->moveTo("uploads/$fileName");                                        
            }
            $jobValidator = v::key('title', v::stringType()->notEmpty())
                  ->key('description', v::stringType()->notEmpty());

            try {
                $jobValidator->assert($postData);
                $postData = $request->getParsedBody();
                $job = new Job();
                $job->title = $postData['title'];
                $job->description = $postData['description'];
                $job->save();

                $responseMessage = 'Saved';
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }

        return $this->renderHTML('addJob.twig', [
            'responseMessage' =>$responseMessage
        ]);
        //     if ($request->getMethod() == 'POST') {
        //         $postData = $request->getParsedBody();
        //         $files =$request->getUploadedFiles();
        //         $logo=$files['logo'];
        //         if ($logo->getError() == UPLOAD_ERR_OK) {
        //             $fileName = $logo->getClientFilename();
        //             $logo->moveTo("uploads/$fileName");// $job->filename = "uploads/$fileName";	                        
        //         }
        //         $job = new Job();
        //         $job->title = $postData['title'];
        //         $job->description = $postData['description'];
        //         $job->save();
        //     }

        //     // include '../views/addJob.php';
        //     return $this->renderHTML('addJob.twig');
        // } 

    }
}