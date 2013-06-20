Usage



your php file


    public function dTableAction()
    {
        
        $dTablesClass = new dTables(
					        			[array(string(column name), string(column name2))], 
					        			[string(id column), 
					        			[string(table name)], 
					        			[PDO instance] 
        							);
        $json_result = $dTablesClass->getOutput();
        
        echo $json_result;
        exit();
    }



js table: 

     oTable = $('.dTable-users').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": http://url-to-your-thing,
    });
    