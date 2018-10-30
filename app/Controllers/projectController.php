<?php
namespace  App\Controllers;

use App\Models\Project;
use Respect\Validation\Validator as v;
class ProjectController extends BaseController{

  public function getAddProjectAction($request){  

            if ($request->getMethod() == 'POST') {

                $postData = $request->getParsedBody();  

                $projectValidator = v::key('title', v::stringType()->notEmpty())
                  ->key('description', v::stringType()->notEmpty());

                try{
                    $projectValidator->assert($postData);
                    $postData = $request->getParsedBody();  
                    $project=new Project();
                    $project->title =$postData['title'];
                    $project->description =$postData['description'];
                    $project->save();

                    $responseMessage = 'Saved';
                }catch(\Exception $e){
                    $responseMessage = $e->getMessage();
                }                
                 
            }
            //include_once('../views/addProject.php');
            return $this->renderHTML('addProject.twig', [
                'responseMessage' =>$responseMessage
            ]);
            
            
    }
}
