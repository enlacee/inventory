<?php
class Application_Model_Usuarios
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
	
	public function sp_ejecutarsql($p)
    {
		
		//exit();
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, '');
        return $this->ado->ejecutarSQL($p);
    }

    public function sp_tienda_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tienda_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }


    public function sp_usuarios_lista($p)
    {
		
		$sql = "select usuarios.*, usuarios_tipo.nombre as tip_usu, usuarios_perfil.nombre as usu_per, tiendas.nombre as usu_tie  from usuarios inner join usuarios_tipo on usuarios.ust_id = usuarios_tipo.ust_id
			inner join usuarios_perfil on usuarios.usp_id = usuarios_perfil.usp_id
		inner join tiendas on usuarios.tie_id = tiendas.tie_id where 1=1";		
		if($p["campo"]<>'' and $p["query"]<>""){
			$sql = $sql." and usuarios.".$p["campo"]." like '".$p["query"]."%'";	
		}
		if($p['sort']=='tip_usu'){$p['sort']='usuarios_tipo.nombre';}
		if($p['sort']=='usu_per'){$p['sort']='usuarios_perfil.nombre';}
		if($p['sort']=='usu_tie'){$p['sort']='tiendas.nombre';}
		if($p['sort']=="nombres"){
			$sql = $sql." order by usuarios.nombres, usuarios.apellidos";
		}else{
			$sql = $sql." order by ".($p['sort']?$p['sort']:'nombres')." ".($p['dir']?$p['dir']:'asc');
		}
        $array = $this->sp_obtenerdatasql($sql);
        return $array;
    }

    public function sp_usuarios_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_eliminar');
        $this->ado->SetParameterSP($p["usr_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'usuarios', 'registro'=>$p["usr_id"], 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_usuarios_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_actualizar');
        $this->ado->SetParameterSP($p["usr_id"],'int');
        $this->ado->SetParameterSP($p["nombres"],'varchar');
        $this->ado->SetParameterSP($p["apellidos"],'varchar');
        $this->ado->SetParameterSP($p["cargo"],'varchar');
        $this->ado->SetParameterSP($p["area"],'varchar');
        $this->ado->SetParameterSP($p["telefono"],'varchar');
        $this->ado->SetParameterSP($p["email"],'varchar');
        $this->ado->SetParameterSP($p["direccion"],'varchar');
        $this->ado->SetParameterSP($p["usuario"],'varchar');
        $this->ado->SetParameterSP($p["clave"],'varchar');
        $this->ado->SetParameterSP($p["ust_id"],'int');
        $this->ado->SetParameterSP($p["usp_id"],'int');
        $this->ado->SetParameterSP($p["tie_id"],'int');
        $this->ado->SetParameterSP($p["estado"],'int');
		$this->ado->SetParameterSP($p["lunes"],'varchar');
		$this->ado->SetParameterSP($p["martes"],'varchar');
		$this->ado->SetParameterSP($p["miercoles"],'varchar');
		$this->ado->SetParameterSP($p["jueves"],'varchar');
		$this->ado->SetParameterSP($p["viernes"],'varchar');
		$this->ado->SetParameterSP($p["sabado"],'varchar');
		$this->ado->SetParameterSP($p["domingo"],'varchar');
		
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'usuarios', 'registro'=>$p["usr_id"], 'detalle'=>$detalle));
        return $array;
    }

    public function sp_usuarios_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_guardar');
        $this->ado->SetParameterSP($p["nombres"],'varchar');
        $this->ado->SetParameterSP($p["apellidos"],'varchar');
        $this->ado->SetParameterSP($p["cargo"],'varchar');
        $this->ado->SetParameterSP($p["area"],'varchar');
        $this->ado->SetParameterSP($p["telefono"],'varchar');
        $this->ado->SetParameterSP($p["email"],'varchar');
        $this->ado->SetParameterSP($p["direccion"],'varchar');
        $this->ado->SetParameterSP($p["usuario"],'varchar');
        $this->ado->SetParameterSP($p["clave"],'varchar');
        $this->ado->SetParameterSP($p["ust_id"],'int');
        $this->ado->SetParameterSP($p["usp_id"],'int');
        $this->ado->SetParameterSP($p["tie_id"],'int');
        $this->ado->SetParameterSP($p["estado"],'int');
		$this->ado->SetParameterSP($p["lunes"],'varchar');
		$this->ado->SetParameterSP($p["martes"],'varchar');
		$this->ado->SetParameterSP($p["miercoles"],'varchar');
		$this->ado->SetParameterSP($p["jueves"],'varchar');
		$this->ado->SetParameterSP($p["viernes"],'varchar');
		$this->ado->SetParameterSP($p["sabado"],'varchar');
		$this->ado->SetParameterSP($p["domingo"],'varchar');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'usuarios'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'usuarios', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
        return $this->ado->getSql();
    }
    
	public function hora_local_int($zona_horaria = 0)
	{
		if ($zona_horaria > -12.1 and $zona_horaria < 12.1)
		{
			$hora_local = time() + ($zona_horaria * 3600);
			return $hora_local;
		}
		return 'error';
	}
			
    public function sp_usuarios_tipo_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_tipo_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }

    public function sp_usuarios_tipo_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_tipo_eliminar');
        $this->ado->SetParameterSP($p["ust_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'usuarios_tipo', 'registro'=>$p["ust_id"], 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_usuarios_tipo_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_tipo_actualizar');
        $this->ado->SetParameterSP($p["ust_id"],'int');
        $this->ado->SetParameterSP($p["nombre"],'varchar');
        $this->ado->SetParameterSP($p["estado"],'int');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'usuarios_tipo', 'registro'=>$p["ust_id"], 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_usuarios_tipo_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_tipo_guardar');
        $this->ado->SetParameterSP($p["nombre"],'varchar');
        $this->ado->SetParameterSP($p["estado"],'int');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'usuarios_tipo'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'usuarios_tipo', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    
    public function sp_usuarios_perfil_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_perfil_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_usuarios_perfil_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_perfil_eliminar');
        $this->ado->SetParameterSP($p["usp_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'usuarios_perfil', 'registro'=>$p["usp_id"], 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_usuarios_perfil_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_perfil_actualizar');
        $this->ado->SetParameterSP($p["usp_id"],'int');
        $this->ado->SetParameterSP($p["nombre"],'varchar');
        $this->ado->SetParameterSP($p["estado"],'int');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		foreach ($p["vDetalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_asignar_permiso');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"varchar");
			$this->ado->SetParameterSP($dato[2],"varchar");
			$this->ado->SetParameterSP($dato[3],"varchar");
			$this->ado->SetParameterSP($dato[4],"varchar");
			$this->ado->SetParameterSP($p["usp_id"],"int");
			
			$detalle = $detalle."\n".$this->ado->getSql();
			$array = $this->ado->ExecuteSP();
		}
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'usuarios_perfil', 'registro'=>$p["usp_id"], 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_usuarios_perfil_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_perfil_guardar');
        $this->ado->SetParameterSP($p["nombre"],'varchar');
        $this->ado->SetParameterSP($p["estado"],'int');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'usuarios_perfil'));
        $pcod = $rs[0];	
		
		//if(!empty($p["vDetalle"])){
			foreach ($p["vDetalle"] as $value){
				$cont ++;
				$dato = explode(".@.",$value);
				
				
				$this->ado->ReiniciarSQL();
				$this->ado->ConnectionOpen($this->log, 'sp_asignar_permiso');
				$this->ado->SetParameterSP($dato[0],"numeric");
				$this->ado->SetParameterSP($dato[1],"varchar");
				$this->ado->SetParameterSP($dato[2],"varchar");
				$this->ado->SetParameterSP($dato[3],"varchar");
				$this->ado->SetParameterSP($dato[4],"varchar");
				$this->ado->SetParameterSP(($pcod["AUTO_INCREMENT"] - 1),"int");
				
				$detalle = $detalle."\n".$this->ado->getSql();
				$array = $this->ado->ExecuteSP();
			}
		//}
			
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'usuarios_perfil', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_usuarios_perfil_menu_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_usuarios_perfil_menu_lista');
		$this->ado->SetParameterSP($p["usp_id"],'int');
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		//$this->sp_eventos_guardar(array(''=>''));
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