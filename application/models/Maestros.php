<?php

class Application_Model_Maestros
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
	
	//Inventario>>
	public function sp_inventario_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_inventario_guardar($p)
    {
	
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			$rsData = $this->sp_obtenerdatasql("select * from stock_producto where pro_id = ".$dato[0]." and age_id = ".$p["age_id"]."");
			if(count($rsData)>0){
				$this->sp_ejecutarsql("UPDATE stock_producto SET editado = CASE WHEN stock_inicial = ".$dato[2]." THEN editado ELSE 'S' END, stock_inicial = ".$dato[2].", ume_id = ".$dato[1]." WHERE pro_id = ".$dato[0]." and age_id = ".$p["age_id"]."");
			}else{
				$this->sp_ejecutarsql("INSERT INTO stock_producto (age_id, pro_id, ume_id, stock, stock_inicial, editado) VALUES (".$p["age_id"].", ".$dato[0].", ".$dato[2].", 0, ".$dato[2].", 'S')");
			}
			
			$rsData = $this->sp_obtenerdatasql("select * from stock_cierre where anio = ".$p["anio"]." and pro_id = ".$dato[0]." and age_id = ".$p["age_id"]."");
			if(count($rsData)>0){
				$this->sp_ejecutarsql("UPDATE stock_cierre SET stock = ".$dato[2]."  WHERE anio = ".$p["anio"]." and pro_id = ".$dato[0]." and age_id = ".$p["age_id"]."");
			}else{
				$this->sp_ejecutarsql("insert into stock_cierre (anio, age_id, pro_id, stock) values (".$p["anio"].", ".$p["age_id"].", ".$dato[0].", ".$dato[2].")");
			}
			
			/*$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_stock_producto_guardar');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"int");
			$this->ado->SetParameterSP($dato[2],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			
			$sql = $sql.$this->ado->getSql();
			$array = $this->ado->ExecuteSP();*/
		}
		
		
		return $sql;
        //return $array;
    }
	
	public function sp_activacion_guardar($p)
    {
	
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_stock_producto_activar');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"int");
			$this->ado->SetParameterSP($dato[2],"varchar");
			$this->ado->SetParameterSP($p["age_id"],"int");
			
			$sql = $sql.$this->ado->getSql();
			$array = $this->ado->ExecuteSP();
		}
		
		
		return $sql;
        //return $array;
    }
	
	public function sp_stock_guardar($p)
    {
	
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_stock_producto_actualizar');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"int");
			$this->ado->SetParameterSP($dato[2],"numeric");
			$this->ado->SetParameterSP($dato[3],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			
			$sql = $sql.$this->ado->getSql();
			$array = $this->ado->ExecuteSP();
		}
		
		
		return $sql;
        //return $array;
    }
	
	public function sp_stock_actualizar($p)
    {

			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_actualizar_stock');
			$this->ado->SetParameterSP($p['anio'],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($p['pro_id'],"int");			
			
			$sql = $this->ado->getSql();
			$array = $this->ado->ExecuteSP();

		return $sql;
        //return $array;
    }
	
	//Agencias
    public function sp_agencias_transporte_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_agencias_transporte_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_agencias_transporte_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_agencias_transporte_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"varchar");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'maestros_agencias_transporte'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'agencias_transporte', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_agencias_transporte_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_agencias_transporte_actualizar');
        $this->ado->SetParameterSP($p["age_id"],"int");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"varchar");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'agencias_transporte', 'registro'=>$p["age_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_agencias_transporte_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_agencias_transporte_eliminar');
        $this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'agencias_transporte', 'registro'=>$p["age_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	//Tiendas
    public function sp_tiendas_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tiendas_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_tiendas_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tiendas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"varchar");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'tiendas'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'tiendas', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_tiendas_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tiendas_actualizar');
        $this->ado->SetParameterSP($p["tie_id"],"int");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"varchar");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'tiendas', 'registro'=>$p["tie_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_tiendas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tiendas_eliminar');
        $this->ado->SetParameterSP($p["tie_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'tiendas', 'registro'=>$p["tie_id"], 'detalle'=>$detalle));
        return $array;
    }
    
	//Proveedores
    public function sp_proveedores_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;		
       	$sql_count = "select COUNT(*) ";
		$sql_selec = 'select maestros_proveedores.prv_id, maestros_proveedores.codigo, maestros_proveedores.nombre, maestros_proveedores.ruc, maestros_proveedores.direccion, replace(maestros_proveedores.telefono,"/"," / ") as telefono, maestros_proveedores.activo, maestros_proveedores.tip_per, maestros_proveedores.tip_doc, maestros_proveedores.rep_leg, maestros_proveedores.fec_cre, maestros_proveedores.dir_loc, maestros_proveedores.observacion, maestros_proveedores.directorio , case when maestros_proveedores.tip_per = "N" then "Nat" else "Jur" end as tipo_per, case when maestros_proveedores.tip_doc = "D" then "Dni" else "RUC" end as tipo_doc ';		
		$sql_from = " from maestros_proveedores ";
		$sql_where = " where 1 = 1 ";
		if($p['campo']<>''){
			if($p['campo']=='prv_id'){
				$sql_where = $sql_where." and maestros_proveedores.".$p['campo']." = ".$p['query']." ";
			}else{
				$sql_where = $sql_where." and maestros_proveedores.".$p['campo']." like '".$p['query']."%' ";
			}
		}
		if($p['tip_per']<>''){
				$sql_where = $sql_where." and maestros_proveedores.tip_per = '".$p['tip_per']."' ";
		}
		$sql_order = " order by maestros_proveedores.".($p['sort']?$p['sort']:'nombre')." ".($p['dir']?$p['dir']:'asc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
    }
    
    public function sp_proveedores_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_proveedores_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"char");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
		$this->ado->SetParameterSP($p["observacion"],"varchar");		
		$this->ado->SetParameterSP($p["directorio"],"varchar");		
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'maestros_proveedores'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'proveedores', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_proveedores_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_proveedores_actualizar');
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"char");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
		$this->ado->SetParameterSP($p["observacion"],"varchar");		
		$this->ado->SetParameterSP($p["directorio"],"varchar");	
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'proveedores', 'registro'=>$p["prv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_proveedores_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_proveedores_eliminar');
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'proveedores', 'registro'=>$p["prv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
	//Clientes
    public function sp_clientes_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = 'select maestros_clientes.*, case when maestros_clientes.tip_per = "N" then "Nat" else "Jur" end as tipo_per, case when maestros_clientes.tip_doc = "D" then "Dni" else "RUC" end as tipo_doc, ubigeo.nom_dep, ubigeo.nom_ciu, ubigeo.nom_dis ';		
		$sql_from = " from maestros_clientes 
						inner join ubigeo on maestros_clientes.dep_id = ubigeo.dep_id and maestros_clientes.ciu_id = ubigeo.ciu_id and maestros_clientes.dis_id = ubigeo.dis_id ";
		$sql_where = " where maestros_clientes.cli_id >0 ";
		if($p['campo']<>''){
				$sql_where = $sql_where." and maestros_clientes.".$p['campo']." like '".$p['query']."%' ";
		}
		if($p['tip_per']<>''){
				$sql_where = $sql_where." and maestros_clientes.tip_per = '".$p['tip_per']."' ";
		}
		$sql_order = " order by maestros_clientes.".($p['sort']?$p['sort']:'nombre')." ".($p['dir']?$p['dir']:'asc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
		
    }
    
    public function sp_clientes_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_clientes_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"char");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["email"],"varchar");
        $this->ado->SetParameterSP($p["web"],"varchar");
        $this->ado->SetParameterSP($p["dep_id"],"int");
        $this->ado->SetParameterSP($p["ciu_id"],"int");
        $this->ado->SetParameterSP($p["dis_id"],"int");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["observacion"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
		$this->ado->SetParameterSP($p["sit_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'maestros_clientes'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'clientes', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return ($pcod["AUTO_INCREMENT"] - 1);
    }

    public function sp_clientes_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_clientes_actualizar');
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ruc"],"char");
        $this->ado->SetParameterSP($p["telefono"],"varchar");
        $this->ado->SetParameterSP($p["email"],"varchar");
        $this->ado->SetParameterSP($p["web"],"varchar");
        $this->ado->SetParameterSP($p["dep_id"],"int");
        $this->ado->SetParameterSP($p["ciu_id"],"int");
        $this->ado->SetParameterSP($p["dis_id"],"int");
        $this->ado->SetParameterSP($p["direccion"],"varchar");
        $this->ado->SetParameterSP($p["observacion"],"varchar");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["tip_per"],"char");
		$this->ado->SetParameterSP($p["tip_doc"],"char");
		$this->ado->SetParameterSP($p["rep_leg"],"varchar");
		$this->ado->SetParameterSP($p["sit_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'clientes', 'registro'=>$p["cli_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_clientes_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_clientes_eliminar');
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'clientes', 'registro'=>$p["cli_id"], 'detalle'=>$detalle));
        return $array;
    }
    
    public function sp_mercaderias_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, lineas.nombre as nom_lin, stock_producto.ubicacion, stock_producto.aplicacion, maestros_mercaderias.observacion, maestros_mercaderias.url_imagen, maestros_mercaderias.tipo_mcd, lineas.nombre as linea, familias.nombre as familia, (CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END)*precio_costo as total ";		
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join familias on lineas.fam_id = familias.fam_id
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$p['age_id']." ";
		$sql_where = " where 1 = 1 ";
		if($p['campo']<>''){
				$sql_where = $sql_where." and maestros_mercaderias.".$p['campo']." like '".$p['query']."%' ";
		}
		if($p['lin_id']<>'' or $p['lin_id']>0){
				$sql_where = $sql_where." and maestros_mercaderias.lin_id = ".$p['lin_id']." ";
		}
		if($p['mar_id']<>'' or $p['mar_id']>0){
				$sql_where = $sql_where." and maestros_mercaderias.mar_id = ".$p['mar_id']." ";
		}
		
		$sql_order = " order by maestros_mercaderias.".($p['sort']?$p['sort']:'nombre')." ".($p['dir']?$p['dir']:'asc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
        /*$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;*/
    }
    
    public function sp_mercaderias_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_guardar');
        $this->ado->SetParameterSP($p["codigo1"],"char");
        $this->ado->SetParameterSP($p["codigo2"],"char");
        $this->ado->SetParameterSP($p["codigo3"],"char");
        $this->ado->SetParameterSP(utf8_decode($p["nombre"]),"varchar");
        $this->ado->SetParameterSP($p["ume_id"],"int");
        $this->ado->SetParameterSP($p["mar_id"],"int");
        $this->ado->SetParameterSP($p["lin_id"],"int");
        $this->ado->SetParameterSP($p["precio_costo"],"decimal");
        $this->ado->SetParameterSP($p["precio_venta"],"decimal");
        $this->ado->SetParameterSP($p["stock_minimo"],"int");
        $this->ado->SetParameterSP($p["stock_actual"],"int");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"int");
		$this->ado->SetParameterSP($p["desc2"],"int");
		$this->ado->SetParameterSP($p["desc3"],"int");
		$this->ado->SetParameterSP($p["desc4"],"int");
		$this->ado->SetParameterSP($p["precio_compra"],"decimal");
		$this->ado->SetParameterSP($p["ind_calculo"],"char");
		$this->ado->SetParameterSP($p["ubicacion"],"char");
		$this->ado->SetParameterSP($p["aplicacion"],"char");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"char");
		$this->ado->SetParameterSP($p["url_imagen"],"char");
		$this->ado->SetParameterSP($p["tipo_mcd"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'maestros_mercaderias'));
        $pcod = $rs[0];
		
		/*$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_stock_producto_guardar');
		$this->ado->SetParameterSP(($pcod["AUTO_INCREMENT"] - 1),"numeric");
		$this->ado->SetParameterSP($p["ume_id"],"int");
		$this->ado->SetParameterSP($p["stock_actual"],"numeric");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
		
		$detalle = $detalle."\n".$this->ado->getSql();;
		$array = $this->ado->ExecuteSP();*/
			
		
				
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'mercaderias', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_mercaderias_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_actualizar');
        $this->ado->SetParameterSP($p["mcd_id"],"int");
        $this->ado->SetParameterSP($p["codigo1"],"char");
        $this->ado->SetParameterSP($p["codigo2"],"char");
        $this->ado->SetParameterSP($p["codigo3"],"char");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $this->ado->SetParameterSP($p["ume_id"],"varchar");
        $this->ado->SetParameterSP($p["mar_id"],"int");
        $this->ado->SetParameterSP($p["lin_id"],"int");
        $this->ado->SetParameterSP($p["precio_costo"],"decimal");
        $this->ado->SetParameterSP($p["precio_venta"],"decimal");
        $this->ado->SetParameterSP($p["stock_minimo"],"int");
        $this->ado->SetParameterSP($p["stock_actual"],"int");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"int");
		$this->ado->SetParameterSP($p["desc2"],"int");
		$this->ado->SetParameterSP($p["desc3"],"int");
		$this->ado->SetParameterSP($p["desc4"],"int");
		$this->ado->SetParameterSP($p["precio_compra"],"decimal");
		$this->ado->SetParameterSP($p["ind_calculo"],"char");
		$this->ado->SetParameterSP($p["ubicacion"],"char");
		$this->ado->SetParameterSP($p["aplicacion"],"char");
		$this->ado->SetParameterSP($p["se_age_id"],"int");		
		$this->ado->SetParameterSP($p["observacion"],"char");
		$this->ado->SetParameterSP($p["url_imagen"],"char");
		$this->ado->SetParameterSP($p["tipo_mcd"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'mercaderias', 'registro'=>$p["mcd_id"], 'detalle'=>$detalle));

        return $array;
    }
    
    public function sp_mercaderias_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_eliminar');
        $this->ado->SetParameterSP($p["mcd_id"],"int");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'producto', 'registro'=>$p["mcd_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_producto_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, 0 as precio_costo, 0 as precio_venta, 0 as stock_minimo, maestros_mercaderias.activo, 0 as utilidad, 0 as desc1, 0 as desc2, 0 as desc3, 0 as desc4, '' as ind_calculo, 0 as precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, 0 as stock_inicial, 0 as stock_pro, lineas.nombre as nom_lin, '' as ubicacion, '' as aplicacion, maestros_mercaderias.observacion, maestros_mercaderias.url_imagen, maestros_mercaderias.tipo_mcd, familias.nombre as familia, lineas.nombre as linea ";		
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			left join familias on lineas.fam_id = familias.fam_id
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id ";
		$sql_where = " where 1 = 1 ";
		if($p['campo']<>''){
				$sql_where = $sql_where." and maestros_mercaderias.".$p['campo']." like '".$p['query']."%' ";
		}
		if($p['lin_id']<>'' or $p['lin_id']>0){
				$sql_where = $sql_where." and maestros_mercaderias.lin_id = ".$p['lin_id']." ";
		}
		if($p['mar_id']<>'' or $p['mar_id']>0){
				$sql_where = $sql_where." and maestros_mercaderias.mar_id = ".$p['mar_id']." ";
		}
		
		$sql_order = " order by maestros_mercaderias.".($p['sort']?$p['sort']:'nombre')." ".($p['dir']?$p['dir']:'asc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
        /*$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_mercaderias_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;*/
    }
    
    public function sp_producto_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_producto_guardar');
        $this->ado->SetParameterSP($p["codigo1"],"char");
        $this->ado->SetParameterSP($p["codigo2"],"char");
        $this->ado->SetParameterSP($p["codigo3"],"char");
        $this->ado->SetParameterSP(utf8_encode($p["nombre"]),"varchar");
        $this->ado->SetParameterSP($p["ume_id"],"int");
        $this->ado->SetParameterSP($p["mar_id"],"int");
        $this->ado->SetParameterSP($p["lin_id"],"int");
        $this->ado->SetParameterSP($p["precio_costo"],"decimal");
        $this->ado->SetParameterSP($p["precio_venta"],"decimal");
        $this->ado->SetParameterSP($p["stock_minimo"],"int");
        $this->ado->SetParameterSP($p["stock_actual"],"int");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"int");
		$this->ado->SetParameterSP($p["desc2"],"int");
		$this->ado->SetParameterSP($p["desc3"],"int");
		$this->ado->SetParameterSP($p["desc4"],"int");
		$this->ado->SetParameterSP($p["precio_compra"],"decimal");
		$this->ado->SetParameterSP($p["ind_calculo"],"char");
		$this->ado->SetParameterSP($p["ubicacion"],"char");
		$this->ado->SetParameterSP($p["aplicacion"],"char");
		$this->ado->SetParameterSP($p["se_age_id"],"int");				
		$this->ado->SetParameterSP($p["observacion"],"char");
		$this->ado->SetParameterSP($p["url_imagen"],"char");
		$this->ado->SetParameterSP($p["tipo_mcd"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'maestros_mercaderias'));
        $pcod = $rs[0];
		
		/*$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_stock_producto_guardar');
		$this->ado->SetParameterSP(($pcod["AUTO_INCREMENT"] - 1),"numeric");
		$this->ado->SetParameterSP($p["ume_id"],"int");
		$this->ado->SetParameterSP($p["stock_actual"],"numeric");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
		
		$detalle = $detalle."\n".$this->ado->getSql();;
		$array = $this->ado->ExecuteSP();*/
			
		
				
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'mercaderias', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }

    public function sp_producto_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_producto_actualizar');
        $this->ado->SetParameterSP($p["mcd_id"],"int");
        $this->ado->SetParameterSP($p["codigo1"],"char");
        $this->ado->SetParameterSP($p["codigo2"],"char");
        $this->ado->SetParameterSP($p["codigo3"],"char");
        $this->ado->SetParameterSP(utf8_encode(str_replace("\'","''",$p["nombre"])),"varchar");
        $this->ado->SetParameterSP($p["ume_id"],"varchar");
        $this->ado->SetParameterSP($p["mar_id"],"int");
        $this->ado->SetParameterSP($p["lin_id"],"int");
        $this->ado->SetParameterSP($p["precio_costo"],"decimal");
        $this->ado->SetParameterSP($p["precio_venta"],"decimal");
        $this->ado->SetParameterSP($p["stock_minimo"],"int");
        $this->ado->SetParameterSP($p["stock_actual"],"int");
        $this->ado->SetParameterSP($p["estado"],"int");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"int");
		$this->ado->SetParameterSP($p["desc2"],"int");
		$this->ado->SetParameterSP($p["desc3"],"int");
		$this->ado->SetParameterSP($p["desc4"],"int");
		$this->ado->SetParameterSP($p["precio_compra"],"decimal");
		$this->ado->SetParameterSP($p["ind_calculo"],"char");
		$this->ado->SetParameterSP($p["ubicacion"],"char");
		$this->ado->SetParameterSP($p["aplicacion"],"char");
		$this->ado->SetParameterSP($p["se_age_id"],"int");		
		$this->ado->SetParameterSP($p["observacion"],"char");
		$this->ado->SetParameterSP('http://importacionesinga.com.pe/inventory/public/uploads/'.$_FILES['producto-url_imagen']['name'],"char");
		$this->ado->SetParameterSP($p["tipo_mcd"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		

		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'mercaderias', 'registro'=>$p["mcd_id"], 'detalle'=>$detalle));

        return $array;
    }
    
    public function sp_producto_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_producto_eliminar');
        $this->ado->SetParameterSP($p["mcd_id"],"int");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'mercaderias', 'registro'=>$p["mcd_id"], 'detalle'=>$detalle));
		
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
