<?php
/**
 * Description of dTables
 *
 * @author andrei
 */

namespace root\mainBundle\Controller;


class dTables {
    
    private $aColumns = array();
    private $sIndexColumn;
    private $sTable;
    private $sLimit;
    private $sOrder;
    private $sWhere;
    private $rResult;
    private $PDO;
    private $aResultFilterTotal;
    private $iFilteredTotal;
    private $iTotal;
    
    public $output;
    

    /**
     * @param: columns, indexColumn, tableName, pdo (array, string, string, PDO instance)
     * @return: 1
     */
    public function __construct($columns, $indexColumn, $tableName, \Doctrine\DBAL\Connection $pdo)
    {
        $this->aColumns = $columns;
        $this->sIndexColumn = $indexColumn;
        $this->sTable = $tableName;
        $this->PDO = $pdo;
        
        
        /*
         * generating Output
         */
        $this->sLimit();
        $this->iSortCol();
        $this->sWhere();
        $this->sQuery();
        $this->getResults();
    }
    
    /**
     * @param: none
     * @return: JSON in dataTablesformat
     */
    public function getOutput()
    {
        return (!empty($this->output)) ? json_encode($this->output) : false;
    }
    
    
    
    /**
     * @param: none;
     * @return: 1
     */
    private function sLimit()
    {        
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$this->sLimit = "LIMIT ". $_GET['iDisplayStart'] .", ".$_GET['iDisplayLength'] ;
	}  
        else
            $this->sLimit = '';
    }
    
    
    /**
     * @param: none;
     * @return: 1
     */
    private function iSortCol()
    {        
        if ( isset( $_GET['iSortCol_0'] ) )
	{
		$this->sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$this->sOrder .= $this->aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	". $_GET['sSortDir_'.$i]  .", ";
			}
		}
		
		$this->sOrder = substr_replace( $this->sOrder, "", -2 );
		if ( $this->sOrder == "ORDER BY" )
		{
			$this->sOrder = "";
		}
	}
        else
        {
            $this->sOrder = '';
        }
    }
    
    
    /**
     * @param: none;
     * @return: 1
     */
    private function sWhere()
    {        
        /*
         * rows filtering
         */
	if ( $_GET['sSearch'] != "" )
	{
		$this->sWhere = "WHERE (";
		for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
		{
			$this->sWhere .= $this->aColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
		}
		$this->sWhere = substr_replace( $this->sWhere, "", -3 );
		$this->sWhere .= ')';
	} 
        else
        {
            $this->sOrder = '';
        }
        
        
        /*
         * Column filtering
         */
	for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $this->sWhere == "" )
			{
				$this->sWhere = "WHERE ";
			}
			else
			{
				$this->sWhere .= " AND ";
			}
			$this->sWhere .= $this->aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
		}
	}
    }
    
    /**
     * @param: none
     * @return: 1
     */
    private function sQuery()
    {
        $this->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $this->aColumns))."
		FROM   $this->sTable
		$this->sWhere
		$this->sOrder
		$this->sLimit";
    }
    
    
    /**
     * @param: none
     * @return: JSON in dataTablesformat
     */
    private function getResults()
    {
        $this->rResult = $this->PDO->fetchAll($this->sQuery);
        
        
        /*
         * Data length after sFilter
         */
        $this->sQuery = "
		SELECT FOUND_ROWS()
	";
        $this->aResultFilterTotal = $this->PDO->fetchAll($this->sQuery);
	$this->iFilteredTotal = $this->aResultFilterTotal[0]['FOUND_ROWS()'];
        
        /*
         * Total data length
         */
        $this->sQuery = "
		SELECT COUNT(".$this->sIndexColumn.")
		FROM   $this->sTable
	";
        
        $this->aResultTotal = $this->PDO->fetchAll($this->sQuery);
        $this->iTotal = $this->aResultTotal[0]['COUNT(id)'];

        /*
	 * Output Format
	 */
	$this->output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $this->iTotal,
		"iTotalDisplayRecords" => $this->iFilteredTotal,
		"aaData" => array()
	);
        
        
        foreach($this->rResult as $aRow)
        {
            	for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
		{
			if ( $this->aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $this->aColumns[$i] ]=="0") ? '-' : $aRow[ $this->aColumns[$i] ];
			}
			else if ( $this->aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $this->aColumns[$i] ];
			}
		}
		$this->output['aaData'][] = $row;
        }
        
        
        return json_encode($this->output);
    }
    
}

?>
