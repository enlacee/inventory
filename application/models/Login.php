<?php

class Application_Model_Login
{
    public $log;
    public $con;
    public $ado;
	public $objDatos_index;
    
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
    
	public function sp_obtener_oficina($p)
    {
        $array = $this->sp_obtenerdatasql("select * from usuario where user = '".$p['user']."'");
        return $array;
    }
	
    public function sp_usuarios_login($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_login');
        $this->ado->SetParameterSP($p["usuario"], 'varchar');
        $this->ado->SetParameterSP($p["clave"], 'varchar');
        //echo $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_eventos_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_eventos_guardar');
        $this->ado->SetParameterSP($p["usr_id"],'int');
        $this->ado->SetParameterSP($p["evento"],'varchar');
        $this->ado->SetParameterSP($p["tabla"],'varchar');
        $this->ado->SetParameterSP($p["registro"],'numeric');
        $this->ado->SetParameterSP(str_replace("'","''",$p["detalle"]),'varchar');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
}
