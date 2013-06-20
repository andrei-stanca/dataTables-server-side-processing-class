usage ( Symfony 2.3 ) 



1. be sure to set the correct namespace in the class header
EG: namespace root\mainBundle\Controller;

2. where you want to use it do a use statement 
EG: use root\mainBundle\Controller\dTables;

3. example:
<code>

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


?>
</code>



js part: 
<code>
     oTable = $('.dTable-users').dataTable({
                "bJQueryUI": true,
                "bAutoWidth": false,
		"sPaginationType": "full_numbers",
                "bProcessing": true,   
                "bServerSide": true,    
                "sAjaxSource": base_url + '/users' + '/datatables',
    });
</code>
html part:
<code>
    <div class="widget">
              <table class="records_list display dTable-users">
                <thead>
                    <tr>
                        <th class='left-align'>First Name</th>
                        <th class='left-align'>Last Name</th>
                        <th class='left-align'>Phone</th>
                        <th class='left-align'>Email</th>
                        <th class='left-align'>Subscriber</th>
                        <th class='left-align'>When</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
    </div>

</code>

