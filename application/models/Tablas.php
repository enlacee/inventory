<?php

class Application_Model_Tablas
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
	
	//Funciones para configuracion >>>
    public function sp_configuracion_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_configuracion_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_configuracion_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_configuracion_guardar');
		$this->ado->SetParameterSP($p["tie_id"],"int");
		$this->ado->SetParameterSP($p["apertura_stock"],"date");
		$this->ado->SetParameterSP($p["apertura_clientes"],"date");
		$this->ado->SetParameterSP($p["apertura_proveedores"],"date");
		$this->ado->SetParameterSP($p["moneda_almacen"],"varchar");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"numeric");
		$this->ado->SetParameterSP($p["desc2"],"numeric");
		$this->ado->SetParameterSP($p["desc3"],"numeric");
		$this->ado->SetParameterSP($p["desc4"],"numeric");
		$this->ado->SetParameterSP($p["calculo_precios"],"char");
		$this->ado->SetParameterSP($p["igv"],"numeric");
		
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'configuracion'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'configuracion', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_configuracion_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_configuracion_actualizar');
        $this->ado->SetParameterSP($p["con_id"],"int");
		$this->ado->SetParameterSP($p["tie_id"],"int");
		$this->ado->SetParameterSP($p["apertura_stock"],"date");
		$this->ado->SetParameterSP($p["apertura_clientes"],"date");
		$this->ado->SetParameterSP($p["apertura_proveedores"],"date");
		$this->ado->SetParameterSP($p["moneda_almacen"],"varchar");
		$this->ado->SetParameterSP($p["utilidad"],"numeric");
		$this->ado->SetParameterSP($p["desc1"],"numeric");
		$this->ado->SetParameterSP($p["desc2"],"numeric");
		$this->ado->SetParameterSP($p["desc3"],"numeric");
		$this->ado->SetParameterSP($p["desc4"],"numeric");
		$this->ado->SetParameterSP($p["calculo_precios"],"char");
		$this->ado->SetParameterSP($p["igv"],"numeric");
		
		
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'configuracion', 'registro'=>$p["con_id"], 'detalle'=>$detalle));
		
        return $detalle.$array;
    }
	
	public function sp_configuracion_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_configuracion_eliminar');
        $this->ado->SetParameterSP($p["con_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'configuracion', 'registro'=>$p["con_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para numeracion >>>
    public function sp_numeracion_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_numeracion_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_numeracion_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_numeracion_guardar');
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["doc_id"],"int");
		$this->ado->SetParameterSP($p["tipo"],"char");
		$this->ado->SetParameterSP($p["serie"],"char");
		$this->ado->SetParameterSP($p["lon"],"numeric");
		$this->ado->SetParameterSP($p["nro"],"numeric");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'numeracion'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'numeracion', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_numeracion_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_numeracion_actualizar');
        $this->ado->SetParameterSP($p["num_id"],"int");
		$this->ado->SetParameterSP($p["doc_id"],"int");
		$this->ado->SetParameterSP($p["tipo"],"char");
		$this->ado->SetParameterSP($p["serie"],"char");
		$this->ado->SetParameterSP($p["lon"],"numeric");
		$this->ado->SetParameterSP($p["nro"],"numeric");
		
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'numeracion', 'registro'=>$p["num_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_numeracion_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_numeracion_eliminar');
        $this->ado->SetParameterSP($p["num_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'numeracion', 'registro'=>$p["num_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Situacion
	 public function sp_situacion_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_situacion_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
	//Funciones para documentos >>>
    public function sp_documentos_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_documentos_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_documentos_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_documentos_guardar');
        $this->ado->SetParameterSP($p["abrev"],"char");
        $this->ado->SetParameterSP($p["descripcion"],"varchar");
        $this->ado->SetParameterSP($p["compras"],"char");
        $this->ado->SetParameterSP($p["ventas"],"char");
        $this->ado->SetParameterSP($p["n_compras"],"varchar");
        $this->ado->SetParameterSP($p["n_ventas"],"varchar");
        $this->ado->SetParameterSP($p["nn_credito"],"varchar");
        $this->ado->SetParameterSP($p["nn_debito"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["altas"],"varchar");
		$this->ado->SetParameterSP($p["bajas"],"varchar");
		$this->ado->SetParameterSP($p["ncompras"],"varchar");
		$this->ado->SetParameterSP($p["nventas"],"varchar");
		$this->ado->SetParameterSP($p["tingresos"],"varchar");
		$this->ado->SetParameterSP($p["tsalidas"],"varchar");
		$detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'documentos'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'documentos', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $detalle;
    }
    
    public function sp_documentos_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_documentos_actualizar');
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["abrev"],"char");
        $this->ado->SetParameterSP($p["descripcion"],"varchar");
        $this->ado->SetParameterSP($p["compras"],"char");
        $this->ado->SetParameterSP($p["ventas"],"char");
        $this->ado->SetParameterSP($p["n_compras"],"varchar");
        $this->ado->SetParameterSP($p["n_ventas"],"varchar");
        $this->ado->SetParameterSP($p["nn_credito"],"varchar");
        $this->ado->SetParameterSP($p["nn_debito"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["altas"],"varchar");
		$this->ado->SetParameterSP($p["bajas"],"varchar");
		$this->ado->SetParameterSP($p["ncompras"],"varchar");
		$this->ado->SetParameterSP($p["nventas"],"varchar");
		$this->ado->SetParameterSP($p["tingresos"],"varchar");
		$this->ado->SetParameterSP($p["tsalidas"],"varchar");
		$detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'documentos', 'registro'=>$p["doc_id"], 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_documentos_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_documentos_eliminar');
        $this->ado->SetParameterSP($p["doc_id"],"int");
		$detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'documentos', 'registro'=>$p["doc_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<

	//Funciones para tipo_cambio >>>
    public function sp_tipo_cambio_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_cambio_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
    
    public function sp_tipo_cambio_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_cambio_guardar');
        $this->ado->SetParameterSP($p["fecha"],"date");
        $this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'tipo_cambio'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'tipo_cambio', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_tipo_cambio_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_cambio_actualizar');
        $this->ado->SetParameterSP($p["tic_id"],"int");
        $this->ado->SetParameterSP($p["fecha"],"date");
        $this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'tipo_cambio', 'registro'=>$p["tic_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_cambio_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_cambio_eliminar');
        $this->ado->SetParameterSP($p["tic_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'tipo_cambio', 'registro'=>$p["tic_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	
	//Funciones para familias >>>
    public function sp_familias_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_familias_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_familias_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_familias_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'familias'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'familias', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_familias_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_familias_actualizar');
        $this->ado->SetParameterSP($p["fam_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'familias', 'registro'=>$p["fam_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_familias_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_familias_eliminar');
        $this->ado->SetParameterSP($p["fam_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'familias', 'registro'=>$p["fam_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para monedas >>>
    public function sp_monedas_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_monedas_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_monedas_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_monedas_guardar');
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		//$this->ado->SetParameterSP($p["codigo"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'monedas'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'monedas', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
    
    public function sp_monedas_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_monedas_actualizar');
        $this->ado->SetParameterSP($p["mon_id"],"int");
        $this->ado->SetParameterSP($p["nombre"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'monedas', 'registro'=>$p["mon_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_monedas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_monedas_eliminar');
        $this->ado->SetParameterSP($p["mon_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'monedas', 'registro'=>$p["mon_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para condiciones_pago >>>
    public function sp_condiciones_pago_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_condiciones_pago_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_condiciones_pago_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_condiciones_pago_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["descripcion"],"varchar");
		$this->ado->SetParameterSP($p["dias"],"varchar");
		$this->ado->SetParameterSP($p["letras"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'condiciones_pago'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'condiciones_pago', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_condiciones_pago_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_condiciones_pago_actualizar');
        $this->ado->SetParameterSP($p["cpa_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["descripcion"],"varchar");
		$this->ado->SetParameterSP($p["dias"],"varchar");
		$this->ado->SetParameterSP($p["letras"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'condiciones_pago', 'registro'=>$p["cpa_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_condiciones_pago_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_condiciones_pago_eliminar');
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'condiciones_pago', 'registro'=>$p["cpa_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para lineas >>>
    public function sp_lineas_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_lineas_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_lineas_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_lineas_guardar');
        $this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["fam_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'lineas'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'lineas', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_lineas_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_lineas_actualizar');
        $this->ado->SetParameterSP($p["lin_id"],"int");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["fam_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'lineas', 'registro'=>$p["lin_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_lineas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_lineas_eliminar');
        $this->ado->SetParameterSP($p["lin_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'lineas', 'registro'=>$p["lin_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para marcas >>>
    public function sp_marcas_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_marcas_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_marcas_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_marcas_guardar');
        $this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'marcas'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'marcas', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_marcas_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_marcas_actualizar');
        $this->ado->SetParameterSP($p["mar_id"],"int");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'marcas', 'registro'=>$p["mar_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_marcas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_marcas_eliminar');
        $this->ado->SetParameterSP($p["mar_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'marcas', 'registro'=>$p["mar_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para unidad_medida >>>
    public function sp_unidad_medida_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_unidad_medida_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_unidad_medida_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_unidad_medida_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["abreviatura"],"varchar");
		$this->ado->SetParameterSP($p["descripcion"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'unidad_medida'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'unidad_medida', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_unidad_medida_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_unidad_medida_actualizar');
        $this->ado->SetParameterSP($p["ume_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["abreviatura"],"varchar");
		$this->ado->SetParameterSP($p["descripcion"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'unidad_medida', 'registro'=>$p["ume_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_unidad_medida_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_unidad_medida_eliminar');
        $this->ado->SetParameterSP($p["ume_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'unidad_medida', 'registro'=>$p["ume_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
    
	//Funciones para igv >>>
    public function sp_igv_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_igv_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_igv_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_igv_guardar');
        $this->ado->SetParameterSP($p["valor"],"numeric");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'igv'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'igv', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_igv_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_igv_actualizar');
        $this->ado->SetParameterSP($p["igv_id"],"int");
		$this->ado->SetParameterSP($p["valor"],"numeric");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'igv', 'registro'=>$p["igv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_igv_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_igv_eliminar');
        $this->ado->SetParameterSP($p["igv_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'igv', 'registro'=>$p["igv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para tipo_movimiento >>>
    public function sp_tipo_movimiento_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_movimiento_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_tipo_movimiento_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_movimiento_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'tipo_movimiento'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'tipo_movimiento', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_movimiento_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_movimiento_actualizar');
        $this->ado->SetParameterSP($p["tmv_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'tipo_movimiento', 'registro'=>$p["tmv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_movimiento_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_movimiento_eliminar');
        $this->ado->SetParameterSP($p["tmv_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'tipo_movimiento', 'registro'=>$p["tmv_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para concepto_pago >>>
    public function sp_concepto_pago_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_concepto_pago_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_concepto_pago_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_concepto_pago_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'concepto_pago'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'concepto_pago', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_concepto_pago_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_concepto_pago_actualizar');
        $this->ado->SetParameterSP($p["coc_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'concepto_pago', 'registro'=>$p["coc_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_concepto_pago_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_concepto_pago_eliminar');
        $this->ado->SetParameterSP($p["coc_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'concepto_pago', 'registro'=>$p["coc_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para tipo_pago >>>
    public function sp_tipo_pago_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_pago_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_tipo_pago_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_pago_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'tipo_pago'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'tipo_pago', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_pago_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_pago_actualizar');
        $this->ado->SetParameterSP($p["tpa_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'tipo_pago', 'registro'=>$p["tpa_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_pago_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_pago_eliminar');
        $this->ado->SetParameterSP($p["tpa_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'tipo_pago', 'registro'=>$p["tpa_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para tipo_nota >>>
    public function sp_tipo_nota_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_nota_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_tipo_nota_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_nota_guardar');
        $this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'tipo_nota'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'tipo_nota', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_nota_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_nota_actualizar');
        $this->ado->SetParameterSP($p["tnt_id"],"int");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["indicador"],"char");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'tipo_nota', 'registro'=>$p["tnt_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_tipo_nota_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_tipo_nota_eliminar');
        $this->ado->SetParameterSP($p["tnt_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'tipo_nota', 'registro'=>$p["tnt_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
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
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	//Funciones para bancos >>>
    public function sp_bancos_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bancos_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_bancos_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bancos_guardar');
        $this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'bancos'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'bancos', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_bancos_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bancos_actualizar');
        $this->ado->SetParameterSP($p["ban_id"],"int");
		$this->ado->SetParameterSP($p["nombre"],"varchar");
		$this->ado->SetParameterSP($p["codigo"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'bancos', 'registro'=>$p["ban_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_bancos_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bancos_eliminar');
        $this->ado->SetParameterSP($p["ban_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'bancos', 'registro'=>$p["ban_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para ctabanco >>>
    public function sp_ctabanco_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ctabanco_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
	
	public function sp_ctabanco_guardar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ctabanco_guardar');
        $this->ado->SetParameterSP($p["ban_id"],"int");
		$this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["nro_cta"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$rs = $this->objDatos_index->sp_generar_codigo(array('table'=>'ctabanco'));
        $pcod = $rs[0];		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'ctabanco', 'registro'=>($pcod["AUTO_INCREMENT"] - 1), 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_ctabanco_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ctabanco_actualizar');
        $this->ado->SetParameterSP($p["cta_id"],"int");
		$this->ado->SetParameterSP($p["ban_id"],"int");
		$this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["nro_cta"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'ctabanco', 'registro'=>$p["cta_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_ctabanco_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ctabanco_eliminar');
        $this->ado->SetParameterSP($p["cta_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'ctabanco', 'registro'=>$p["cta_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Funciones para concepto_contable >>>
    public function sp_concepto_contable_lista($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_concepto_contable_lista');
        $this->ado->SetParameterSP($p["campo"],"varchar");
        $this->ado->SetParameterSP($p["query"],"varchar");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
        return $array;
    }
}