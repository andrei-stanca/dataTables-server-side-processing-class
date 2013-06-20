usage ( Symfony 2.3 ) 



1. be sure to set the correct namespace in the class header
EG: namespace root\mainBundle\Controller;

2. where you want to use it do a use statement 
EG: use root\mainBundle\Controller\dTables;

3. example:



<?php


use root\mainBundle\Controller\dTables;

class UsersController extends \root\apiBundle\Controller\DefaultController
{

...

    /**
     * @Route("users/datatables", name="users_ajax")
     * @Template()
     */
    public function dTableAction()
    {
        
        $dTablesClass = new dTables(array( 'fname', 'lname', 'phone', 'email', 'subscribe', 'when_' ), 'id', 'users', $this->getDoctrine()->getConnection());
        $json_result = $dTablesClass->getOutput();
        
        echo $json_result;
        exit();
    }

...

}