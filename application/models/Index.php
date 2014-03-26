<?php

class Application_Model_Index
{
    public $log;
    public $con;
    public $ado;
    
    public function __construct()
    {
        $this->con=new Application_Model_Connection();
        $this->log=$this->con->getInitDsnMySql00();
        $this->ado=new Application_Model_Adodb();

    }
	
	public function sp_obtenerdatasql($p)
    {
		
		//exit();
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, '');
		//return array('hola'=>$p);
        return $this->ado->obtenerDataSQL($p);
    }

    public function sp_generar_codigo($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP($p["table"], 'varchar');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_table_describe($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_table_describe');
        $this->ado->SetParameterSP($p["table"], 'varchar');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_usuarios_menu($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_menu');
        $this->ado->SetParameterSP(VAR_USR_ID, 'int');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }

    public function sp_ubigeo_dep($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ubigeo_dep');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_ubigeo_ciu($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ubigeo_ciu');
        $this->ado->SetParameterSP($p["dep_id"], 'int');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_ubigeo_dis($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ubigeo_dis');
        $this->ado->SetParameterSP($p["dep_id"], 'int');
        $this->ado->SetParameterSP($p["ciu_id"], 'int');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
}
