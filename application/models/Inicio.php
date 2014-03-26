<?php

class Application_Model_Usuarios
{
    public $log;
    public $con;
    public $ado;
	public $objSess;
	public $objDatos_index;
    
    public function __construct()
    {
        $this->con=new Application_Model_Connection();
        $this->log=$this->con->getInitDsnMySql00();
        $this->ado=new Application_Model_Adodb();
		$this->objDatos_index=new Application_Model_Index();
    }
	
	public function sp_obtenerdatasql($p)
    {
		
		//exit();
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, '');
		//return array('hola'=>$p);
        return $this->ado->obtenerDataSQL($p);
    }
}