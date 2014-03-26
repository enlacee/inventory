<?php
class Application_Model_Movimientos
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
	
	
	//Compras >>>
    public function sp_compras_lista($p,$tipo=1)
    {
        $offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select prv.nombre as nombre_proveedor,doc.abrev as descripcion_documento,mco.mco_id,mco.codigo,mco.prv_id,mco.tipo_ingreso,mco.doc_id,mco.doc_n,mco.n_guia,mco.cpa_id,mco.mon_id,mco.valor_compra,mco.impuesto_igv,mco.total_compra, DATE_FORMAT(mco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mco.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, mco.anulado, prv.codigo as prv_codigo, prv.ruc, mco.eliminado, mco.afecta, mco.formato, mco.observacion, prv.direccion, mco.fec_ven as fec_ori, cpa.descripcion condicion, mco.saldo, cpa.letras, cpa.dias, mco.saldo_inicial, mco.saldo_inicial as saldo_tmp, 'CO' as tipo, prv.codigo as codigo_prv, prv.ruc as ruc_prv, mco.tipo_cambio, substring(monedas.nombre,1,1) as abrev_moneda ";		
		$sql_from = " from movimientos_compras mco 
		inner join maestros_proveedores prv on prv.prv_id=mco.prv_id 
		inner join documentos doc on doc.doc_id=mco.doc_id 
		inner join monedas on mco.mon_id = monedas.mon_id  
		inner join condiciones_pago cpa on mco.cpa_id = cpa.cpa_id ";
		$sql_where = " where 1=1 and mco.eliminado = 0 and mco.age_id = ".$p['age_id']." ";
		if($p['campo']<>''){
			if($p['campo']=='mco_id'){
				$sql_where = $sql_where." and mco.".$p['campo']." = ".$p['query']."";
			}else{
				$sql_where = $sql_where." and mco.".$p['campo']." like '".$p['query']."%' ";
			}
		}
		if($p['fec_ini']<>''){
			$sql_where = $sql_where." and mco.fec_ven between '".$p['fec_ini']."' and '".$p['fec_fin']."' ";	
		}
		
		if($p['doc_id']>0){
			$sql_where = $sql_where." and mco.doc_id = ".$p['doc_id']." ";	
		}
		
		if($p['nro_doc']<>''){
			$sql_where = $sql_where." and (mco.doc_n like '%".str_replace("-","%",$p['nro_doc'])."%' or prv.nombre like '%".$p['nro_doc']."%') ";	
		}	
		
		if($p['modo']=='1'){
			$sql_where = $sql_where." and mco.saldo > 0 and mco.cpa_id >1 and mco.anulado = '0' ";
			
			//if($tipo==2){
			$sql_where = $sql_where."
			UNION ALL 
			select prv.nombre as nombre_proveedor,doc.descripcion as descripcion_documento,sco.sco_id,sco.codigo,sco.prv_id,sco.tipo_ingreso,sco.doc_id,sco.doc_n,sco.n_guia,sco.cpa_id,sco.mon_id,sco.valor_compra,sco.impuesto_igv,sco.total_compra, DATE_FORMAT(sco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(sco.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, sco.anulado, prv.codigo as prv_codigo, prv.ruc, sco.eliminado, sco.afecta, sco.formato, sco.observacion, prv.direccion, sco.fec_ven as fec_ori, cpa.descripcion condicion, sco.saldo, cpa.letras, cpa.dias, sco.saldo_inicial, sco.saldo_inicial as saldo_tmp, 'SC' as tipo ,'','','',''   ";		
			$sql_where = $sql_where." from movimientos_saldocompras sco 
			inner join maestros_proveedores prv on prv.prv_id=sco.prv_id 
			inner join documentos doc on doc.doc_id=sco.doc_id 
			inner join monedas on sco.mon_id = monedas.mon_id  
			inner join condiciones_pago cpa on sco.cpa_id = cpa.cpa_id ";
			$sql_where = $sql_where." where 1=1 and sco.eliminado = 0 and sco.age_id = ".$p['age_id']." ";
			$sql_where = $sql_where." and sco.prv_id = ".$p['prv_id']." ";
			//}
		}
		
		$sql_order = " order by ".($p['modo']=='1'?'':'mco.')."".($p['sort']?$p['sort']:'fec_ven')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			//echo "modod:".$p['modo']."<br>".$sql_count.$sql_from.$sql_where.$sql_order."<br>";
			if($p['modo']=='1'){
                                //die("select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL");
				$rsData = $this->sp_obtenerdatasql("select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL");		
			}else{
                                //die($sql_count.$sql_from.$sql_where.$sql_order."xxx");
				$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
			}
		}else{
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		
		/*$sql_order = " order by mco.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}*/
		return $rsData;
    }
    
    public function sp_compras_guardar($p)
    {
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_compras_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_compras', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_compras_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'compras', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_compras_actualizar($p)
    {
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_compras_actualizar');
		$this->ado->SetParameterSP($p["mco_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_compras_guardar');
			$this->ado->SetParameterSP($p["mco_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'compras', 'registro'=>$p["mco_id"], 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_compras_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_compras_eliminar');
        $this->ado->SetParameterSP($p["mco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'compras', 'registro'=>$p["mco_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_compras_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_compras_anular');
        $this->ado->SetParameterSP($p["mco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'compras', 'registro'=>$p["mco_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	
	//Altas
	public function sp_altas_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select prv.nombre as nombre_proveedor,doc.descripcion as descripcion_documento,mal.mal_id,mal.codigo,mal.prv_id,mal.tipo_ingreso,mal.doc_id,mal.doc_n,mal.tmv_id,mal.n_guia,mal.cpa_id,mal.mon_id,mal.valor_compra,mal.impuesto_igv,mal.total_compra, DATE_FORMAT(mal.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mal.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, mal.anulado, prv.codigo as prv_codigo, prv.ruc, mal.eliminado, mal.afecta, mal.formato, mal.observacion, mal.tipo_cambio, doc.abrev as doc_abrev, substring(monedas.nombre,1,1) as mon_abrev ";		
		$sql_from = " from movimientos_altas mal
		left join maestros_proveedores prv on prv.prv_id=mal.prv_id
		inner join documentos doc on doc.doc_id=mal.doc_id
		inner join monedas on mal.mon_id = monedas.mon_id ";
		$sql_where = " where 1=1 and mal.eliminado = 0 and mal.age_id = ".$p['age_id']." ";
		if($_POST['campo']<>''){
				$sql_where = $sql_where." and mal.".$p['campo']." like '".$p['query']."%' ";
		}
		$sql_order = " order by mal.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_altas_guardar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_altas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();

		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_altas', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_altas_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'altas', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $detalle;
    }
	
	public function sp_altas_actualizar($p)
    {		
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_altas_actualizar');
		$this->ado->SetParameterSP($p["mal_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_altas_guardar');
			$this->ado->SetParameterSP($p["mal_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'altas', 'registro'=>$p["mal_id"], 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_altas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_altas_eliminar');
        $this->ado->SetParameterSP($p["mal_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'altas', 'registro'=>$p["mal_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_altas_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_altas_anular');
        $this->ado->SetParameterSP($p["mal_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'altas', 'registro'=>$p["mal_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//TrasladoIng
	public function sp_trasladoing_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select doc.descripcion as descripcion_documento,tin.tin_id,tin.codigo,tin.prv_id,tin.tipo_ingreso,tin.doc_id,tin.doc_n,tin.n_guia,tin.cpa_id,tin.mon_id,tin.valor_compra,tin.impuesto_igv,tin.total_compra, DATE_FORMAT(tin.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(tin.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, tin.anulado, tin.eliminado, tin.afecta, tin.formato, tin.observacion, tin.age_id, tin.tie_des, ori.nombre as origen, des.nombre as destino, substring(monedas.nombre,1,1) as mon_abrev, doc.abrev as doc_abrev, tin.tipo_cambio ";		
		$sql_from = " from movimientos_trasladoing tin
		inner join documentos doc on doc.doc_id=tin.doc_id
		inner join monedas on tin.mon_id = monedas.mon_id
		inner join tiendas ori on tin.age_id = ori.tie_id
		inner join tiendas des on tin.tie_des = des.tie_id
		 ";
		
		
		$sql_where = " where 1=1 and tin.eliminado = 0 ";
		
		if($_POST['txtpar4']=='TI'){
			$sql_where = $sql_where." and tin.tie_des = ".$p['age_id'];
		}else{
			$sql_where = $sql_where." and tin.age_id = ".$p['age_id'];
		}
		
		if($_POST['campo']<>''){
				$sql_where = $sql_where." and tin.".$p['campo']." like '".$p['query']."%' ";
		}
		
		$sql_order = " order by tin.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_trasladoing_guardar($p)
    {
        $this->ado->ReiniciarSQL();
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_trasladoing_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["tie_des"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_trasladoing', 'varchar');
        //$detalle = $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_trasladoing_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		//echo $detalle;
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'trasladoing', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_trasladoing_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
		
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_trasladoing_actualizar');
		$this->ado->SetParameterSP($p["tin_id"],"int");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["tie_des"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_trasladoing_guardar');
			$this->ado->SetParameterSP($p["tin_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'trasladoing', 'registro'=>$p["tin_id"], 'detalle'=>$detalle));
		//echo $detalle;
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_trasladoing_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_trasladoing_eliminar');
        $this->ado->SetParameterSP($p["tin_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'trasladoing', 'registro'=>$p["tin_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_trasladoing_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_trasladoing_anular');
        $this->ado->SetParameterSP($p["tin_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'trasladoing', 'registro'=>$p["tin_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//NotaVentas
	public function sp_notaventas_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select cli.nombre as nombre_cliente,doc.descripcion as descripcion_documento,nve.nve_id,nve.codigo,nve.cli_id,nve.tipo_ingreso,nve.doc_id,nve.doc_n,nve.tnt_id,nve.n_guia,nve.cpa_id,nve.mon_id,nve.valor_venta,nve.impuesto_igv,nve.total_venta,nve.fec_ven as fec_ori, DATE_FORMAT(nve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(nve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, nve.anulado, cli.codigo as cli_codigo, cli.ruc, nve.eliminado, nve.afecta, nve.formato, nve.observacion, doc.abrev as doc_abrev, substring(monedas.nombre,1,1) as mon_abrev, nve.tipo_cambio ";		
		$sql_from = " from movimientos_notaventas nve
		inner join maestros_clientes cli on cli.cli_id=nve.cli_id
		inner join documentos doc on doc.doc_id=nve.doc_id
		inner join monedas on nve.mon_id = monedas.mon_id ";
		$sql_where = " where 1=1 and nve.eliminado = 0 and nve.age_id = ".$p['age_id']." ";
		
                if($p['campo']<>''){
                    if($p['campo']=='nve_id'){
                        $sql_where = $sql_where." and nve.".$p['campo']." = ".$p['query'];
                    }else{
                        $sql_where = $sql_where." and nve.".$p['campo']." like '".$p['query']."%' ";
                    }
		}
                
		$sql_order = " order by nve.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
                    $rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
                    //echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit; exit;
                    $rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
    }
    
    public function sp_notaventas_guardar($p)
    {
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notaventas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tnt_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		//echo $detalle;
		
		
		
        $array = $this->ado->ExecuteSP();

		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_notaventas', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_notaventas_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();

		}		
		
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'notaventas', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return ($data[0] - 1);
    }
	
	public function sp_notaventas_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notaventas_actualizar');
		$this->ado->SetParameterSP($p["nve_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tnt_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		//echo $detalle;
		//exit();
		
		
		
        $array = $this->ado->ExecuteSP();
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_notaventas_guardar');
			$this->ado->SetParameterSP($p["nve_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'notaventas', 'registro'=>$p["nve_id"], 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_notaventas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notaventas_eliminar');
        $this->ado->SetParameterSP($p["nve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'notaventas', 'registro'=>$p["nve_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_notaventas_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notaventas_anular');
        $this->ado->SetParameterSP($p["nve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'notaventas', 'registro'=>$p["nve_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	
    //Ventas >>>
    public function sp_ventas_lista($p,$tipo=1)
    {

		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
                $sql_count = "select COUNT(*) ";
		$sql_selec = "select case when mve.cli_id = 0 then mve.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,mve.mve_id,mve.codigo,mve.cli_id,mve.tipo_ingreso,mve.doc_id,mve.doc_n,mve.n_guia,mve.cpa_id,mve.mon_id,mve.valor_venta,mve.impuesto_igv,mve.total_venta, DATE_FORMAT(mve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, mve.anulado, case when mve.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when mve.cli_id = 0 then mve.cli_ruc else cli.ruc end as ruc , mve.eliminado, mve.afecta, mve.formato, mve.observacion, cli.direccion, mve.fec_ven as fec_ori, cpa.descripcion condicion, mve.saldo, cpa.letras, cpa.dias, mve.saldo_inicial, mve.saldo_inicial as saldo_tmp, 'VE' as tipo, (select CONCAT(nom_dep,' ',nom_ciu,' ',nom_dis) from ubigeo where dep_id = cli.dep_id and ciu_id = cli.ciu_id and dis_id = cli.dis_id) as ubigeo, substring(monedas.nombre,1,1) as mon_abrev, doc.abrev as doc_abrev, cli.codigo as cod_cli, mve.tipo_cambio ";		
		$sql_from = " from movimientos_ventas mve 
		left join maestros_clientes cli on cli.cli_id=mve.cli_id 
		inner join documentos doc on doc.doc_id=mve.doc_id 
		inner join monedas on mve.mon_id = monedas.mon_id  
		inner join condiciones_pago cpa on mve.cpa_id = cpa.cpa_id ";
		$sql_where = " where 1=1 and mve.eliminado = 0 and mve.age_id = ".$p['age_id']." ";
		

                        
		if($p['campo']<>''){
			if($p['campo']=='mve_id'){
					$sql_where = $sql_where." and mve.".$p['campo']." = ".$p['query'];
			}else{
				$sql_where = $sql_where." and mve.".$p['campo']." like '%".$p['query']."%' ";
			}
		}
				
		if($p['fec_ini']<>''){
			$sql_where = $sql_where." and mve.fec_ven between '".$p['fec_ini']."' and '".$p['fec_fin']."' ";	
		}
		
		if($p['doc_id']>0){
			$sql_where = $sql_where." and mve.doc_id = ".$p['doc_id']." ";	
		}
		
		if($p['nro_doc']<>''){
			$sql_where = $sql_where." and (mve.doc_n like '%".str_replace("-","%",$p['nro_doc'])."%' or cli.nombre like '%".$p['nro_doc']."%') ";	
		}	
		
		if($p['cli_id']>0){
			$sql_where = $sql_where." and mve.cli_id = ".$p['cli_id']." ";
		}
		
		if($p['modo']=='1'){
			$sql_where = $sql_where." and mve.saldo > 0 and mve.cpa_id >1 and anulado = '0' ";
			
			//if($tipo==2){
			$sql_where = $sql_where."
			UNION ALL 
			select case when sve.cli_id = 0 then sve.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,sve.sve_id,sve.codigo,sve.cli_id,sve.tipo_ingreso,sve.doc_id,sve.doc_n,sve.n_guia,sve.cpa_id,sve.mon_id,sve.valor_venta,sve.impuesto_igv,sve.total_venta, DATE_FORMAT(sve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(sve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, sve.anulado, case when sve.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when sve.cli_id = 0 then sve.cli_ruc else cli.ruc end as ruc , sve.eliminado, sve.afecta, sve.formato, sve.observacion, cli.direccion, sve.fec_ven as fec_ori, cpa.descripcion condicion, sve.saldo, cpa.letras, cpa.dias, sve.saldo_inicial, sve.saldo_inicial as saldo_tmp, 'SA' as tipo, '','','','','' ";		
			$sql_where = $sql_where." from movimientos_saldoventas sve 
			left join maestros_clientes cli on cli.cli_id=sve.cli_id 
			inner join documentos doc on doc.doc_id=sve.doc_id 
			inner join monedas on sve.mon_id = monedas.mon_id  
			inner join condiciones_pago cpa on sve.cpa_id = cpa.cpa_id ";
			$sql_where = $sql_where." where 1=1 and sve.eliminado = 0 and sve.age_id = ".$p['age_id']." ";
			$sql_where = $sql_where." and sve.cli_id = ".$p['cli_id']." ";
			//}
		}
		
		$sql_order = " order by ".($p['modo']=='1'?'':'mve.')."".($p['sort']?$p['sort']:'fec_ven')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		//die( var_dump(  $sql_where ));
		if($tipo==1){
			//echo $sql_count.$sql_from.$sql_where.$sql_order;
			if($p['modo']=='1'){
                                //die( "select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL" );                                
				$rsData = $this->sp_obtenerdatasql("select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL");	

			}else{
                                //die( $sql_count.$sql_from.$sql_where.$sql_order );                                                                
				$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);

			}
		}else{
                        //die( $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit );
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
                                                    
		}
		return $rsData;
    }
	
	public function getCondicionPago($p){
		$condicion = $p['cpa'];
		$result = $this->sp_obtenerdatasql("SELECT * FROM condiciones_pago WHERE cpa_id='$condicion'");
		return $result;
	} 
	
	
	public function sp_saldo_pendiente_cliente($p){
		$tienda = $p['tienda'];
		$cliente = $p['cliente'];
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_saldo_pendiente_cliente');
        $this->ado->SetParameterSP($cliente, 'int');
        $this->ado->SetParameterSP($tienda, 'int');
        $rs = $this->ado->ExecuteSP();
		return $rs;
	}
    
    
    
    public function sp_ventas_2_lista($p,$tipo=1)
    {

		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		$tienda = $p['age_id'];
		
                $sql_count = "select COUNT(*) ";
		$sql_selec = "select 'MV' AS 'OP',case when mve.cli_id = 0 then mve.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,mve.mve_id,mve.codigo,mve.cli_id,mve.tipo_ingreso,mve.doc_id,mve.doc_n,mve.n_guia,mve.cpa_id,mve.mon_id,mve.valor_venta,mve.impuesto_igv,mve.total_venta, DATE_FORMAT(mve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, mve.anulado, case when mve.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when mve.cli_id = 0 then mve.cli_ruc else cli.ruc end as ruc , mve.eliminado, mve.afecta, mve.formato, mve.observacion, cli.direccion, mve.fec_ven as fec_ori, cpa.descripcion condicion, mve.saldo, cpa.letras, cpa.dias, mve.saldo_inicial, mve.saldo_inicial as saldo_tmp, 'VE' as tipo, (select CONCAT(nom_dep,' ',nom_ciu,' ',nom_dis) from ubigeo where dep_id = cli.dep_id and ciu_id = cli.ciu_id and dis_id = cli.dis_id) as ubigeo, substring(monedas.nombre,1,1) as mon_abrev, doc.abrev as doc_abrev, cli.codigo as cod_cli, mve.tipo_cambio, '0' as 'monto_canje' ";		
		$sql_from = " from movimientos_ventas mve 
		left join maestros_clientes cli on cli.cli_id=mve.cli_id 
		inner join documentos doc on doc.doc_id=mve.doc_id 
		inner join monedas on mve.mon_id = monedas.mon_id  
		inner join condiciones_pago cpa on mve.cpa_id = cpa.cpa_id ";
		$sql_where = " where 1=1 and mve.eliminado = 0 and mve.age_id = ".$p['age_id']."";
		

                        
		if($p['campo']<>''){
			if($p['campo']=='mve_id'){
					$sql_where = $sql_where." and mve.".$p['campo']." = ".$p['query'];
			}else{
				$sql_where = $sql_where." and mve.".$p['campo']." like '%".$p['query']."%' ";
			}
		}
				
		if($p['fec_ini']<>''){
			$sql_where = $sql_where." and mve.fec_ven between '".$p['fec_ini']."' and '".$p['fec_fin']."' ";	
		}
		
		if($p['doc_id']>0){
			$sql_where = $sql_where." and mve.doc_id = ".$p['doc_id']." ";	
		}
		
		if($p['nro_doc']<>''){
			$sql_where = $sql_where." and (mve.doc_n like '%".str_replace("-","%",$p['nro_doc'])."%' or cli.nombre like '%".$p['nro_doc']."%') ";	
		}	
		
		if($p['cli_id']>0){
			$sql_where = $sql_where." and mve.cli_id = ".$p['cli_id']." ";
		}
		
		if($p['modo']=='1'){
			$sql_where = $sql_where." and mve.saldo > 0 and mve.cpa_id >1 and anulado = '0' ";
			
			//if($tipo==2){
			$sql_where = $sql_where."
			UNION ALL 
			select 'SI' AS 'OP',case when sve.cli_id = 0 then sve.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,sve.sve_id,sve.codigo,sve.cli_id,sve.tipo_ingreso,sve.doc_id,sve.doc_n,sve.n_guia,sve.cpa_id,sve.mon_id,sve.valor_venta,sve.impuesto_igv,sve.total_venta, DATE_FORMAT(sve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(sve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, sve.anulado, case when sve.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when sve.cli_id = 0 then sve.cli_ruc else cli.ruc end as ruc , sve.eliminado, sve.afecta, sve.formato, sve.observacion, cli.direccion, sve.fec_ven as fec_ori, cpa.descripcion condicion, sve.saldo, cpa.letras, cpa.dias, sve.saldo_inicial, sve.saldo_inicial as saldo_tmp, 'SA' as tipo, '','','','','', '0' as 'monto_canje' ";		
			$sql_where = $sql_where." from movimientos_saldoventas sve 
			left join maestros_clientes cli on cli.cli_id=sve.cli_id 
			inner join documentos doc on doc.doc_id=sve.doc_id 
			inner join monedas on sve.mon_id = monedas.mon_id  
			inner join condiciones_pago cpa on sve.cpa_id = cpa.cpa_id ";
			$sql_where = $sql_where." where 1=1 and sve.eliminado = 0 and sve.age_id = ".$p['age_id']." ";
			$sql_where = $sql_where." and sve.cli_id = ".$p['cli_id']." ";
                        
                        //notas de credito
                        $sql_where = $sql_where."
                        UNION ALL     
                        select 
                        'MV' AS 'OP',notve.cli_id,doc.descripcion,'','','','','',doc_n,'','','',
                        '','',notve.total_venta,DATE_FORMAT(notve.fec_ven, '%d/%m/%Y'),DATE_FORMAT(notve.fec_mov, '%d/%m/%Y'),monedas.nombre,'','','','','','',
                        '','','','','','','',notve.saldo,'','','','','','','','' 
                        from movimientos_notaventas notve 
                        inner join documentos doc on doc.doc_id=notve.doc_id  
                        inner join monedas on notve.mon_id = monedas.mon_id ";
                        $sql_where = $sql_where." where 1=1 and notve.eliminado = 0 and notve.age_id = ".$p['age_id']." ";
			$sql_where = $sql_where." and notve.cli_id = ".$p['cli_id']." and anulado = '0' ";                        
                        
			//}
		}
		
		$sql_order = " order by ".($p['modo']=='1'?'':'mve.')."".($p['sort']?$p['sort']:'fec_ven')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		//die( var_dump(  $sql_where ));
		if($tipo==1){
			//echo $sql_count.$sql_from.$sql_where.$sql_order;
			if($p['modo']=='1'){
                                //die( "select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL" );                                
				$rsData = $this->sp_obtenerdatasql("select count(*) from (".$sql_selec.$sql_from.$sql_where.$sql_order.") TOTAL");	

			}else{
                                //die( $sql_count.$sql_from.$sql_where.$sql_order );                                                                
				$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);

			}
		}else{
                        //die( $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit );
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
                                                    
		}
		return $rsData;
    }    
    
    public function sp_ventas_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$this->ado->SetParameterSP($p["se_usr_id"],"varchar");
		$detalle = $this->ado->getSql();
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_ventas', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_ventas_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'ventas', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return ($data[0] - 1);;
    }
	
	public function sp_ventas_actualizar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ventas_actualizar');
		$this->ado->SetParameterSP($p["mve_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$detalle = $this->ado->getSql();
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_ventas_guardar');
			$this->ado->SetParameterSP($p["mve_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'ventas', 'registro'=>$p["mve_id"], 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_ventas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ventas_eliminar');
        $this->ado->SetParameterSP($p["mve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'ventas', 'registro'=>$p["mve_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_ventas_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_ventas_anular');
        $this->ado->SetParameterSP($p["mve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'ventas', 'registro'=>$p["mve_id"], 'detalle'=>$detalle));
		
        return $array;
    }	
	//<<<
	
	//Bajas >>>
    public function sp_bajas_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select case when mba.cli_id = 0 then mba.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,mba.mba_id,mba.codigo,mba.cli_id,mba.tipo_ingreso,mba.doc_id,mba.doc_n, mba.tmv_id,mba.n_guia, mba.cpa_id, mba.mon_id, mba.valor_venta, mba.impuesto_igv, mba.total_venta, DATE_FORMAT(mba.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mba.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, mba.anulado, case when mba.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when mba.cli_id = 0 then mba.cli_ruc else cli.ruc end as ruc , mba.eliminado, mba.afecta, mba.formato, mba.observacion, doc.abrev as doc_abrev, substring(monedas.nombre,1,1) as mon_abrev, mba.tipo_cambio ";		
		$sql_from = " from movimientos_bajas mba
		left join maestros_clientes cli on cli.cli_id=mba.cli_id
		inner join documentos doc on doc.doc_id=mba.doc_id
		inner join monedas on mba.mon_id = monedas.mon_id ";
		$sql_where = " where 1=1 and mba.eliminado = 0 and mba.age_id = ".$p['age_id']." ";
		if($p['campo']<>''){
				$sql_where = $sql_where." and mba.".$p['campo']." like '".$p['query']."%' ";
		}
		$sql_order = " order by mba.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_bajas_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bajas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$detalle = $this->ado->getSql();
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_bajas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_bajas', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_bajas_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'bajas', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_bajas_actualizar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bajas_actualizar');
		$this->ado->SetParameterSP($p["mba_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$detalle = $this->ado->getSql();
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_bajas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_bajas_guardar');
			$this->ado->SetParameterSP($p["mba_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'bajas', 'registro'=>$p["mba_id"], 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $detalle;
    }
	
	public function sp_bajas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bajas_eliminar');
        $this->ado->SetParameterSP($p["mba_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'bajas', 'registro'=>$p["mba_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_bajas_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_bajas_anular');
        $this->ado->SetParameterSP($p["mba_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'bajas', 'registro'=>$p["mba_id"], 'detalle'=>$detalle));
		
        return $array;
    }	
	//<<<
	
	//NotaCompras >>>
    public function sp_notacompras_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select case when nco.prv_id = 0 then nco.proveedor else prv.nombre end as nombre_proveedor,doc.descripcion as descripcion_documento,nco.nco_id,nco.codigo,nco.prv_id,nco.tipo_ingreso,nco.doc_id,nco.doc_n, nco.tnt_id,nco.n_guia, nco.cpa_id, nco.mon_id, nco.valor_compra, nco.impuesto_igv, nco.total_compra, DATE_FORMAT(nco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(nco.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, nco.anulado, case when nco.prv_id = 0 then '' else prv.codigo end as prv_codigo, case when nco.prv_id = 0 then nco.prv_ruc else prv.ruc end as ruc , nco.eliminado, nco.afecta, nco.formato, nco.observacion, doc.abrev as doc_abrev, substring(monedas.nombre,1,1) as mon_abrev, nco.tipo_cambio ";		
		$sql_from = " from movimientos_notacompras nco
		inner join maestros_proveedores prv on prv.prv_id=nco.prv_id
		inner join documentos doc on doc.doc_id=nco.doc_id
		inner join monedas on nco.mon_id = monedas.mon_id ";
		$sql_where = " where 1=1 and nco.eliminado = 0 and nco.age_id = ".$p['age_id']." ";
		if($p['campo']<>''){
				$sql_where = $sql_where." and nco.".$p['campo']." like '".$p['query']."%' ";
		}
		$sql_order = " order by nco.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_notacompras_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notacompras_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tnt_id"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["proveedor"],"varchar");
		$this->ado->SetParameterSP($p["prv_ruc"],"varchar");
		$detalle = $this->ado->getSql();
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_notacompras_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;
		
		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_notacompras', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_notacompras_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'notacompras', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_notacompras_actualizar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notacompras_actualizar');
		$this->ado->SetParameterSP($p["nco_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tnt_id"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["proveedor"],"varchar");
		$this->ado->SetParameterSP($p["prv_ruc"],"varchar");
		$detalle = $this->ado->getSql();
		//echo $detalle;
		//exit();
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_notacompras_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_notacompras_guardar');
			$this->ado->SetParameterSP($p["nco_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}	
		//echo $detalle;
		//exit();	
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'notacompras', 'registro'=>$p["nco_id"], 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_notacompras_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notacompras_eliminar');
        $this->ado->SetParameterSP($p["nco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'notacompras', 'registro'=>$p["nco_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_notacompras_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_notacompras_anular');
        $this->ado->SetParameterSP($p["nco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'notacompras', 'registro'=>$p["nco_id"], 'detalle'=>$detalle));
		
        return $array;
    }	
	//<<<
	
	//canje >>>
    public function sp_canje_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select can.canje_id, can.canje_codigo, can.cpa_id, can.fecha_canje, can.formato, can.mon_id, can.total_canje, can.tie_id, can.cli_id, cpa.descripcion as condicion, mon.nombre as moneda, cli.nombre as persona ";		
		
		$sql_from = " from canje can
		inner join condiciones_pago cpa on can.cpa_id = cpa.cpa_id 
		inner join monedas mon on can.mon_id = mon.mon_id
		inner join maestros_clientes cli on can.cli_id = cli.cli_id ";

		$sql_where = " where 1=1 and can.estado = 'N' ";
		
		

		$sql_order = " order by can.".($p['sort']?$p['sort']:'canje_id')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_canje_guardar($p)
    {
        $rs = $this->sp_obtenerdatasql("select CASE WHEN max(canje_id) IS NULL THEN 1 ELSE max(canje_id) + 1 END as nro from canje");	
        //die( var_dump( $rs ) );
	    $data = $rs[0];
		$id_canje = $data['nro'];
		//exit();
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_canje_guardar');
		$this->ado->SetParameterSP($id_canje,"numeric");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["fecha_canje"],"date");
        $this->ado->SetParameterSP($p["formato"],"char");
        $this->ado->SetParameterSP($p["mon_id"],"int");
        $this->ado->SetParameterSP($p["total_ventas"],"decimal");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
		$this->ado->SetParameterSP($p["cli_id"],"int");
		
		$detalle = $this->ado->getSql();
		//echo $detalle;
		//exit();
		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;
		foreach ($p["v_ventas"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			//print_r($dato);
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_canje_guardar');
			$this->ado->SetParameterSP($id_canje,"int");
			$this->ado->SetParameterSP($dato[0],"varchar");
			$this->ado->SetParameterSP($dato[1],"int");
			$this->ado->SetParameterSP($dato[2],"decimal");
			$this->ado->SetParameterSP($dato[3],"decimal");
			$this->ado->SetParameterSP($dato[4],"decimal");
			//$this->ado->SetParameterSP($dato[5],"varchar");
			
			//$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		
		$cont = 0;
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			//print_r($dato);
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_cuota_guardar');
			$this->ado->SetParameterSP($id_canje,"int");
			$this->ado->SetParameterSP($dato[0],"int");
			$this->ado->SetParameterSP($dato[1],"varchar");
			$this->ado->SetParameterSP($dato[2],"date");
			$this->ado->SetParameterSP($dato[3],"decimal");
			//$this->ado->SetParameterSP($dato[4],"int");
			$this->ado->SetParameterSP($dato[4],"varchar");                        
			//$this->ado->SetParameterSP($dato[5],"int");
			$this->ado->SetParameterSP($dato[5],"varchar");                        
			$this->ado->SetParameterSP($dato[6],"varchar");
			$this->ado->SetParameterSP($dato[7],"varchar");
			//$this->ado->SetParameterSP($dato[8],"char");
			//$this->ado->SetParameterSP('VE',"char");
			$this->ado->SetParameterSP(2,"int");
                        
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			//echo $detalle;
			//exit();
			
			/*if(!$array){
				
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'canje', 'registro'=>($dato[1]*100 + $data[0]), 'detalle'=>"CUOTAS ".$dato[0]." => ".$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	public function sp_canje_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_canje_eliminar');
		$this->ado->SetParameterSP($p["canje_id"],"int");
		$this->ado->SetParameterSP($p["se_age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		//echo $detalle;
		//exit();
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'canje', 'registro'=>($p["tabla_id"]*100 + $p["nro"]), 'detalle'=>"CUOTAS ".$dato[0]." => ".$detalle));
		
        return $array;
    }
	//<<<
	
	//caja
	public function sp_caja_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) + 2 ";
		$sql_selec = "select mca.mca_id,mca.tie_id,mca.nro, DATE_FORMAT(mca.fecha,'%d/%m/%Y') as fecha,mca.tipo_cpa,mca.cpa_id,mca.tipo_per,mca.per_id,mca.mon_id,mca.monto,mca.tipo_pag,mca.tpa_id,mca.comentario,
mca.reg_id,mca.estado, case when mca.per_id is null then mca.per_nom else per.persona end as persona, case mca.tipo_cpa when 'I' then mca.monto else 0 end as ingreso, case mca.tipo_cpa when 'E' then mca.monto else 0 end as egreso, 
case when mca.mon_id = 1 then 'SOLES' else 'DOLARES' end as moneda,cpa.nombre as concepto, mca.tc ";		
		$sql_from = " from movimientos_caja mca
		inner join concepto_pago cpa on mca.cpa_id = cpa.coc_id
		left join (
			select cast('1' as char(1)) as tipo, maestros_clientes.cli_id as per_id, maestros_clientes.nombre as persona, maestros_clientes.ruc from maestros_clientes
			UNION ALL
			select cast('2' as char(1)) as tipo, maestros_proveedores.prv_id as per_id, maestros_proveedores.nombre as persona, maestros_proveedores.ruc from maestros_proveedores
		) per on mca.per_id = per.per_id and mca.tipo_per = per.tipo 
		inner join tipo_pago tpa on mca.tpa_id = tpa.tpa_id ";
		$sql_where = " where 1=1 and mca.estado = 'N' and mca.tie_id = ".$p['se_age_id']." ";
		if($p['fecha']<>''){
				$sql_where = $sql_where." and mca.fecha = '".$p['fecha']."' ";
		}
		if($p['tipo_pag']<>''){
				$sql_where = $sql_where." and mca.tipo_pag = '".$p['tipo_pag']."' ";
		}		
		$sql_union = $sql_union." UNION ALL 
		SELECT 0, ".$p['se_age_id'].", 0, '".date("d/m/Y",strtotime($p['fecha']))."', 'I',0, '2', 0, 1, 0, '".$p['tipo_pag']."', 0, 'Saldo Anterior', 0, 'N', 'CAJA',
		CASE WHEN SUM(CASE WHEN mca.tipo_cpa = 'I' THEN 1 ELSE -1 END * monto) IS NULL THEN 0 ELSE SUM(CASE WHEN mca.tipo_cpa = 'I' THEN 1 ELSE -1 END * monto) END, 0, 'SOLES','SALDO ANTERIOR EN SOLES', (select valor_compra from tipo_cambio where fecha='".$p['fecha']."')
		FROM detalle_caja dca INNER JOIN movimientos_caja mca on dca.mca_id = mca.mca_id
		WHERE estado = 'N' and fecha < '".$p['fecha']."' and mon_id = 1 and tipo_pag = '".$p['tipo_pag']."'";
		
		$sql_union = $sql_union." UNION ALL 
		SELECT 0, ".$p['se_age_id'].", 0, '".date("d/m/Y",strtotime($p['fecha']))."', 'I',0, '2', 0, 2, 0, '".$p['tipo_pag']."', 0, 'Saldo Anterior', 0, 'N', 'CAJA',
		CASE WHEN SUM(CASE WHEN mca.tipo_cpa = 'I' THEN 1 ELSE -1 END * monto) IS NULL THEN 0 ELSE SUM(CASE WHEN mca.tipo_cpa = 'I' THEN 1 ELSE -1 END * monto) END, 0, 'DOLARES','SALDO ANTERIOR EN DOLARES', (select valor_compra from tipo_cambio where fecha='".$p['fecha']."')
		FROM detalle_caja dca INNER JOIN movimientos_caja mca on dca.mca_id = mca.mca_id
		WHERE estado = 'N' and fecha < '".$p['fecha']."' and mon_id = 2 and tipo_pag = '".$p['tipo_pag']."'";
		
		$sql_order = " order by ".($p['sort']?$p['sort']:'mca_id')." ".($p['dir']?$p['dir']:'asc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_union.$sql_order.$sql_limit);
		}
		return $rsData;
    }
    
    public function sp_caja_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_caja_guardar');
		$this->ado->SetParameterSP($p["se_age_id"],"int");
        $this->ado->SetParameterSP(0,"int");
        $this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["fecha"],"date");
        $this->ado->SetParameterSP($p["tipo_cpa"],"char");
		$this->ado->SetParameterSP($p["cpa_id"],"char");
		
		$this->ado->SetParameterSP($p["tipo_per"],"char");
        $this->ado->SetParameterSP($p["per_id"],"int");
		
		$this->ado->SetParameterSP($p["per_cod"],"varchar");
		$this->ado->SetParameterSP($p["per_nom"],"varchar");
		
        $this->ado->SetParameterSP($p["mon_id"],"int");
        $this->ado->SetParameterSP($p["monto"],"decimal");
		$this->ado->SetParameterSP($p["tipo_pag"],"char");
        $this->ado->SetParameterSP($p["tpa_id"],"int");
		$this->ado->SetParameterSP($p["nro_cheque"],"varchar");
		$this->ado->SetParameterSP($p["comentario"],"varchar");
		$this->ado->SetParameterSP(0,"int");
        $this->ado->SetParameterSP('N',"char");
		
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		//exit();
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;
		
		//echo $detalle;
		//exit();
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_caja', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 //echo $data[0];
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_caja_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[1],"int");
			$this->ado->SetParameterSP($dato[2],"int");
			$this->ado->SetParameterSP($dato[3],"int");
			$this->ado->SetParameterSP($dato[4],"varchar");
			$this->ado->SetParameterSP($dato[5],"varchar");
			$this->ado->SetParameterSP($dato[6],"varchar");
			$this->ado->SetParameterSP($dato[7],"varchar");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP(0,"decimal");
			$this->ado->SetParameterSP(0,"int");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			//echo $detalle;
			//exit();
			
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'caja', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $detalle;
    }
	
	public function sp_caja_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
		
		/*$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');		
		$dat = $this->ado->iniciaTransaccion();*/
		
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_caja_actualizar');
		$this->ado->SetParameterSP($p["mca_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_caja_guardar');
			$this->ado->SetParameterSP($p["mca_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'caja', 'registro'=>$p["mca_id"], 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_caja_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_caja_eliminar');
        $this->ado->SetParameterSP($p["mca_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'caja', 'registro'=>$p["mca_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_caja_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_caja_anular');
        $this->ado->SetParameterSP($p["mca_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'caja', 'registro'=>$p["mca_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	public function sp_saldoinicialventas_guardar($p)
    {
	
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_saldoinicialventas_guardar');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"numeric");
			
			$sql = $sql.$this->ado->getSql();
			$array = $this->ado->ExecuteSP();
		}
		
		
		return $sql;
        //return $array;
    }
	
	public function sp_saldoinicialcompras_guardar($p)
    {
	
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_saldoinicialcompras_guardar');
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[1],"numeric");
			
			$sql = $sql.$this->ado->getSql();
			$array = $this->ado->ExecuteSP();
		}
		
		
		return $sql;
        //return $array;
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
	
	//prerecibo
	public function sp_prerecibo_lista($p, $tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select mpr.mpr_id , mpr.tie_id , mpr.codigo , mpr.fecha , mpr.fecha_reg , mpr.tipo_pagador , mpr.per_id , case when mpr.per_id is null then mpr.per_doc else per.ruc end as per_doc , case when mpr.per_id is null then mpr.per_nom else per.persona end as per_nom , mpr.estado, 1 as total, concat(LPAD(mpr.tie_id,3,'0'), '-',LPAD(mpr.codigo,6,'0')) as nro_recibo ";
		$sql_from = " from movimientos_prerecibo mpr 
		left join (
			select cast('1' as char(1)) as tipo, maestros_clientes.cli_id as per_id, maestros_clientes.nombre as persona, maestros_clientes.ruc from maestros_clientes
			UNION ALL
			select cast('2' as char(1)) as tipo, maestros_proveedores.prv_id as per_id, maestros_proveedores.nombre as persona, maestros_proveedores.ruc from maestros_proveedores
		) per on mpr.per_id = per.per_id and mpr.tipo_pagador = per.tipo 
		";
		$sql_where = " where mpr.estado = 'N' and mpr.tie_id = ".$p['se_age_id']." ";
		if($p['fecha']<>''){
				$sql_where = $sql_where." and mpr.fecha = '".$p['fecha']."' ";
		}
		if($p['tipo_pagador']<>''){
				$sql_where = $sql_where." and mpr.tipo_pagador = '".$p['tipo_pagador']."' ";
		}
		if($p['nom_per']<>''){
				$sql_where = $sql_where." and mpr.nom_per = '".$p['nom_per']."' ";
		}		
		
		$sql_order = " order by ".($p['sort']?$p['sort']:'fecha')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		//echo $sql_selec.$sql_from.$sql_where.$sql_order;
		//echo "<br>AQUI1><br>";
		if($tipo==1){
			//echo "<br>AQUI2><br>";
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
			
		}else{
			
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		
		//print_r($rsData);
		return $rsData;
    }
    
    public function sp_prerecibo_guardar($p)
    {
        //$this->ado->ReiniciarSQL();
		
		/*$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');		
		$dat = $this->ado->iniciaTransaccion();*/
		
		
		$anio = date("Y",strtotime($p["fecha"]));
		
		$rs = $this->sp_obtenerdatasql("select CASE WHEN max(codigo) IS NULL THEN 1 ELSE max(codigo) + 1 END as nro from movimientos_prerecibo where tie_id = ".$p['se_age_id']."");
	    $data = $rs[0];
		$nro = $data['nro'];
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_prerecibo_guardar');
		$this->ado->SetParameterSP($p["se_age_id"],"int");
        $this->ado->SetParameterSP($nro,"int");
        $this->ado->SetParameterSP($p["fecha"],"date");
        $this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["tipo_pagador"],"char");
        $this->ado->SetParameterSP($p["per_id"],"int");
        $this->ado->SetParameterSP($p["per_doc"],"char");
        $this->ado->SetParameterSP($p["per_nom"],"char");
		$this->ado->SetParameterSP($p["comentario"],"char");
        $this->ado->SetParameterSP('N',"char");
		
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		//exit();
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;
		
		//echo $detalle;
		//exit();
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_prerecibo', 'varchar');
        //echo $this->ado->getSql();
        $rs = $this->ado->ExecuteSP();
		
		//return $this->ado->getSql();
		
	    $data = $rs[0]; 
		
		 //echo $data[0];
			$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$tipo = $dato[4];
			if($tipo=='L'){
				$cuo_id = $dato[5];
				$doc_id=0;
			}else{
				$doc_id = $dato[5];
				$cuo_id=0;
			}
			eval("\$monto = ".$dato[7]." + ".$dato[8].";");
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_prerecibo_guardar');
			$this->ado->SetParameterSP($data[0] - 1,"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($cuo_id,"int");
			$this->ado->SetParameterSP($doc_id,"int");
			$this->ado->SetParameterSP($dato[2],"varchar");
			$this->ado->SetParameterSP($dato[3],"varchar");
			$this->ado->SetParameterSP($dato[9],"int");
			$this->ado->SetParameterSP($dato[1],"char");
			$this->ado->SetParameterSP($dato[6],"char");
			$this->ado->SetParameterSP($monto,"numeric");
			
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			//echo $detalle;
			//exit();
			
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		//echo $detalle."<br>".$dato[7]." + ".$dato[8]."".$dato[9];
		//exit();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'prerecibo', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $detalle;
    }
	
	public function sp_prerecibo_actualizar($p)
    {
        $this->ado->ReiniciarSQL();
		
		/*$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');		
		$dat = $this->ado->iniciaTransaccion();*/
		
		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_prerecibo_actualizar');
		$this->ado->SetParameterSP($p["mpr_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
		$this->ado->SetParameterSP($p["tmv_id"],"int");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
        $this->ado->SetParameterSP($p["valor_desc"],"decimal");
		$this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["impuesto_igv"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$detalle = $this->ado->getSql();
		
		
		
		
        $array = $this->ado->ExecuteSP();
		
		
		//$this->ado->ConnectionOpen($this->log, 'sp_ventas_guardar');
		//$dat = $this->ado->finalizaTransaccion();
		
		//return $this->ado->getSql();
		
		/*if(!$array){
			$this->ado->abortaTransaccion();			
		}*/
		
		$cont = 0;		
		 
		$entra="NO";
		foreach ($p["v_detalle"] as $value){
			$entra ="SI";
			$cont ++;
			$dato = explode(".@.",$value);
			
			
			$this->ado->ReiniciarSQL();
			$this->ado->ConnectionOpen($this->log, 'sp_detalle_prerecibo_guardar');
			$this->ado->SetParameterSP($p["mpr_id"],"numeric");
			$this->ado->SetParameterSP($cont,"int");
			$this->ado->SetParameterSP($dato[0],"numeric");
			$this->ado->SetParameterSP($dato[7],"int");
			$this->ado->SetParameterSP($dato[1],"numeric");
			$this->ado->SetParameterSP($dato[4],"numeric");
			$this->ado->SetParameterSP($dato[5],"VARCHAR");
			$this->ado->SetParameterSP($dato[5],"numeric");
			$this->ado->SetParameterSP($dato[6],"numeric");
			$this->ado->SetParameterSP($p["age_id"],"int");
			$this->ado->SetParameterSP($dato[8],"char");
			$this->ado->SetParameterSP($dato[9],"decimal");
			$this->ado->SetParameterSP($dato[10],"decimal");
			$array = $this->ado->ExecuteSP();
			
			$detalle = $detalle."\n". $this->ado->getSql();
			
			/*if(!$array){
				$this->ado->abortaTransaccion();			
				break;
			}*/
		}		
		/*return $sql;*/
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'prerecibo', 'registro'=>$p["mpr_id"], 'detalle'=>$detalle));
		
		$dat = $this->ado->finalizaTransaccion();
        return $sql;
    }
	
	
	public function sp_prerecibo_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_prerecibo_eliminar');
        $this->ado->SetParameterSP($p["mpr_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'D', 'tabla'=>'prerecibo', 'registro'=>$p["mpr_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_prerecibo_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_prerecibo_anular');
        $this->ado->SetParameterSP($p["mpr_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'prerecibo', 'registro'=>$p["mpr_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	//<<<
	
	//Deudas
	public function sp_deudas_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select * ";		
		$sql_from = " FROM (select canje.tie_id as age_id, 'L' as tipo, canje.fecha_canje as fecha, 'LE' as tip_doc, letra as nro_doc, saldo, cuo_id as doc_id from cuota
							inner join canje on cuota.canje_id = canje.canje_id and canje.cli_id = ".$p['cli_id']." and cuota.saldo >0 and cuota.tipo = '".($_POST['tipo']=='1'?'VE':'CO')."'
							".($p['tipo']=='1'?"union all
							select age_id, 'V' as tipo, movimientos_ventas.fec_ven as fecha, documentos.abrev as tip_doc, doc_n as nro_doc, saldo, mve_id as doc_id
							from movimientos_ventas inner join documentos on movimientos_ventas.doc_id = documentos.doc_id
							where eliminado = '0' and anulado = '0' and cli_id=".$p['cli_id']." and cpa_id = 1 and saldo>0":"union all
							select age_id, 'C' as tipo, movimientos_compras.fec_ven as fecha, documentos.abrev as tip_doc, doc_n as nro_doc, saldo, mco_id as doc_id
							from movimientos_compras inner join documentos on movimientos_compras.doc_id = documentos.doc_id
							where eliminado = '0' and anulado = '0' and prv_id=".$p['cli_id']." and cpa_id = 1 and saldo>0")."
						) TOTAL ";
		$sql_where = " where TOTAL.age_id = ".$p['age_id']." ";
		
		
		/*if($p['campo']<>''){
			if($p['campo']=='mve_id'){
					$sql_where = $sql_where." and mve.".$p['campo']." = ".$p['query'];
			}else{
				$sql_where = $sql_where." and mve.".$p['campo']." like '".$p['query']."%' ";
			}
		}*/
		
		
		
		$sql_order = " order by TOTAL.".($p['sort']?$p['sort']:'fecha')." ".($p['dir']?$p['dir']:'desc');
		/*if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}*/
		
		//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
		//echo $sql_count.$sql_from.$sql_where.$sql_order;
		if($tipo==1){
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		}
		return $rsData;
    }
	//
	
	//Rpt Compras
	public function sp_compras_rpt($p)
    {

		$sql_selec = "select prv.nombre as nombre_proveedor,
		doc.descripcion as descripcion_documento, mco.mco_id, mco.codigo, mco.prv_id, mco.tipo_ingreso, mco.doc_id, 
		mco.doc_n, mco.n_guia,mco.cpa_id, mco.mon_id, mco.valor_compra,
		mco.impuesto_igv,mco.total_compra, DATE_FORMAT(mco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mco.fec_mov, '%d/%m/%Y') as fec_mov, 
		monedas.nombre as moneda, mco.anulado, 
		prv.codigo as prv_codigo, prv.ruc, 
		mco.eliminado, mco.afecta, mco.formato, mco.observacion, tca.valor_compra as tca_compra, mco.igv
		from movimientos_compras mco
		inner join maestros_proveedores prv on prv.prv_id=mco.prv_id
		inner join documentos doc on doc.doc_id=mco.doc_id
		inner join monedas on mco.mon_id = monedas.mon_id
		left join tipo_cambio tca on mco.fec_ven = tca.fecha
		where 1=1 and mco.eliminado = 0 and mco.age_id = ".$p['txtpar1']." and mco.fec_ven >= '".$p['txtpar2']."' and mco.fec_ven <= '".$p['txtpar3']."'
		UNION ALL
		select case when nco.prv_id = 0 then nco.proveedor else prv.nombre end as nombre_proveedor,
		doc.descripcion as descripcion_documento, nco.nco_id, nco.codigo, nco.prv_id, nco.tipo_ingreso, nco.doc_id, 
		nco.doc_n, nco.n_guia, nco.cpa_id,nco.mon_id,nco.valor_compra,
		nco.impuesto_igv, nco.total_compra, DATE_FORMAT(nco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(nco.fec_mov, '%d/%m/%Y') as fec_mov, 
		monedas.nombre as moneda, nco.anulado, 
		case when nco.prv_id = 0 then '' else prv.codigo end as prv_codigo, 
		case when nco.prv_id = 0 then nco.prv_ruc else prv.ruc end as ruc , 
		nco.eliminado, nco.afecta, nco.formato, nco.observacion, tca.valor_compra, nco.igv
		from movimientos_notacompras nco
		inner join maestros_proveedores prv on prv.prv_id=nco.prv_id
		inner join documentos doc on doc.doc_id=nco.doc_id
		inner join monedas on nco.mon_id = monedas.mon_id
		left join tipo_cambio tca on nco.fec_ven = tca.fecha
		where 1=1 and nco.eliminado = 0 and nco.age_id = ".$p['txtpar1']." and nco.fec_ven >= '".$p['txtpar2']."' and nco.fec_ven <= '".$p['txtpar3']."'
		order by doc_id, fec_ven, nombre_proveedor, doc_n";
		//echo $sql_selec;
		$rsData = $this->sp_obtenerdatasql($sql_selec);
		return $rsData;
    }
	
	//Rpt Compras
	public function sp_ventas_rpt($p)
    {

		$sql_selec = "select case when mve.cli_id = 0 then mve.cliente else cli.nombre end as nombre_cliente,
		doc.descripcion as descripcion_documento, mve.mve_id,mve.codigo,mve.cli_id,mve.tipo_ingreso,mve.doc_id,mve.doc_n,mve.n_guia,mve.cpa_id,mve.mon_id,mve.valor_venta,
		mve.impuesto_igv,mve.total_venta, DATE_FORMAT(mve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(mve.fec_mov, '%d/%m/%Y') as fec_mov, 
		monedas.nombre as moneda, mve.anulado, 
		case when mve.cli_id = 0 then '' else cli.codigo end as cli_codigo, 
		case when mve.cli_id = 0 then mve.cli_ruc else cli.ruc end as ruc , 
		mve.eliminado, mve.afecta, mve.formato, mve.observacion, tca.valor_venta as tca_venta, mve.igv
		from movimientos_ventas mve
		left join maestros_clientes cli on cli.cli_id=mve.cli_id
		inner join documentos doc on doc.doc_id=mve.doc_id
		inner join monedas on mve.mon_id = monedas.mon_id 
		inner join condiciones_pago cpa on mve.cpa_id = cpa.cpa_id
		left join tipo_cambio tca on mve.fec_ven = tca.fecha
		where 1=1 and mve.eliminado = 0 and mve.age_id = ".$p['txtpar1']." and mve.fec_ven >= '".$p['txtpar2']."' and mve.fec_ven <= '".$p['txtpar3']."'
		UNION ALL
		select cli.nombre as nombre_cliente,
		doc.descripcion as descripcion_documento, nve.nve_id,nve.codigo,nve.cli_id,nve.tipo_ingreso,nve.doc_id,nve.doc_n,nve.n_guia,nve.cpa_id,nve.mon_id,nve.valor_venta,
		nve.impuesto_igv,nve.total_venta, DATE_FORMAT(nve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(nve.fec_mov, '%d/%m/%Y') as fec_mov, 
		monedas.nombre as moneda, nve.anulado, 
		cli.codigo as cli_codigo, cli.ruc, 
		nve.eliminado, nve.afecta, nve.formato, nve.observacion, tca.valor_venta, nve.igv	
		from movimientos_notaventas nve
		inner join maestros_clientes cli on cli.cli_id=nve.cli_id
		inner join documentos doc on doc.doc_id=nve.doc_id
		inner join monedas on nve.mon_id = monedas.mon_id
		left join tipo_cambio tca on nve.fec_ven = tca.fecha
		where 1=1 and nve.eliminado = 0 and nve.age_id = ".$p['txtpar1']." and nve.fec_ven >= '".$p['txtpar2']."' and nve.fec_ven <= '".$p['txtpar3']."'
		order by doc_id, doc_n, fec_ven";
		//echo $sql_selec;
		$rsData = $this->sp_obtenerdatasql($sql_selec);
		return $rsData;
    }
	
	public function sp_producto_rpt($p)
    {		
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, lineas.nombre as nom_lin, stock_producto.ubicacion, stock_producto.aplicacion, maestros_mercaderias.observacion, maestros_mercaderias.url_imagen, maestros_mercaderias.tipo_mcd ";		
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$p['txtpar1']." ";
		$sql_where = " where 1 = 1 ";
		if($p['txtpar3']<>'' or $p['txtpar3']>0){
				$sql_where = $sql_where." and maestros_mercaderias.lin_id = ".$p['txtpar3']." ";
		}
		if($p['txtpar2']<>'' or $p['txtpar2']>0){
				$sql_where = $sql_where." and maestros_mercaderias.mar_id = ".$p['txtpar2']." ";
		}
		$sql_order = " order by maestros_mercaderias.nombre asc";		
		$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order);
		return $rsData;
    }
	
	public function sp_productoapertura_rpt($p)
    {		
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, stock_cierre.stock as stock_pro, lineas.nombre as nom_lin, stock_producto.ubicacion, stock_producto.aplicacion, maestros_mercaderias.observacion, maestros_mercaderias.url_imagen, maestros_mercaderias.tipo_mcd ";		
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$p['txtpar1']."
			inner join stock_cierre on stock_cierre.anio = '".$p['txtpar4']."' and maestros_mercaderias.mcd_id = stock_cierre.pro_id and stock_cierre.age_id = ".$p['txtpar1']." ";
		$sql_where = " where 1 = 1 ";
		if($p['txtpar3']<>'' or $p['txtpar3']>0){
				$sql_where = $sql_where." and maestros_mercaderias.lin_id = ".$p['txtpar3']." ";
		}
		if($p['txtpar2']<>'' or $p['txtpar2']>0){
				$sql_where = $sql_where." and maestros_mercaderias.mar_id = ".$p['txtpar2']." ";
		}
		$sql_order = " order by maestros_mercaderias.nombre asc";		
		$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order);
		return $rsData;
    }
	
	
	//Saldo Ventas >>>
    public function sp_saldoventas_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select case when sve.cli_id = 0 then sve.cliente else cli.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,sve.sve_id,sve.codigo,sve.cli_id,sve.tipo_ingreso,sve.doc_id,sve.doc_n,sve.n_guia,sve.cpa_id,sve.mon_id,sve.valor_venta,sve.impuesto_igv,sve.total_venta, DATE_FORMAT(sve.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(sve.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, sve.anulado, case when sve.cli_id = 0 then '' else cli.codigo end as cli_codigo, case when sve.cli_id = 0 then sve.cli_ruc else cli.ruc end as ruc , sve.eliminado, sve.afecta, sve.formato, sve.observacion, cli.direccion, sve.fec_ven as fec_ori, cpa.descripcion condicion, sve.saldo, cpa.letras, cpa.dias, sve.saldo_inicial, sve.saldo_inicial as saldo_tmp ";		
		$sql_from = " from movimientos_saldoventas sve 
		left join maestros_clientes cli on cli.cli_id=sve.cli_id 
		inner join documentos doc on doc.doc_id=sve.doc_id 
		inner join monedas on sve.mon_id = monedas.mon_id  
		inner join condiciones_pago cpa on sve.cpa_id = cpa.cpa_id ";
		$sql_where = " where 1=1 and sve.eliminado = 0 and sve.age_id = ".$p['age_id']." ";
		
		
		if($p['campo']<>''){
			if($p['campo']=='sve_id'){
					$sql_where = $sql_where." and sve.".$p['campo']." = ".$p['query'];
			}else{
				$sql_where = $sql_where." and sve.".$p['campo']." like '".$p['query']."%' ";
			}
		}
		
		if($p['modo']=='1'){
			$sql_where = $sql_where." and sve.saldo > 0 and sve.cpa_id >1 and anulado = '0' ";
		}
		
		if($p['cli_id']>0){
			$sql_where = $sql_where." and sve.cli_id = ".$p['cli_id']." ";
		}
		
		$sql_order = " order by sve.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
		if($limit>=0){
			$sql_limit = " limit ".$offset.", ".$limit." ";
		}
		
		if($tipo==1){
			//echo $sql_count.$sql_from.$sql_where.$sql_order;
                        //die('xx');                    
			$rsData = $this->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);		
		}else{
			//echo $sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit;
                        //die('yy');
			$rsData = $this->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);

		}
		return $rsData;
    }
    
    public function sp_saldoventas_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldoventas_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["saldo"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$detalle = $this->ado->getSql();		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_saldoventas', 'varchar');
        $rs = $this->ado->ExecuteSP();		
	    $data = $rs[0]; 
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'saldoventas', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return ($data[0] - 1);;
    }
	
	public function sp_saldoventas_actualizar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldoventas_actualizar');
		$this->ado->SetParameterSP($p["sve_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["cli_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_venta"],"decimal");
        $this->ado->SetParameterSP($p["saldo"],"decimal");
        $this->ado->SetParameterSP($p["total_venta"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["cli_ruc"],"varchar");
		$detalle = $this->ado->getSql();		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		 
		$entra="NO";
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'saldoventas', 'registro'=>$p["sve_id"], 'detalle'=>$detalle));
		
        return $sql;
    }
	
	public function sp_saldoventas_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldoventas_eliminar');
        $this->ado->SetParameterSP($p["sve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'saldoventas', 'registro'=>$p["sve_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_saldoventas_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldoventas_anular');
        $this->ado->SetParameterSP($p["sve_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'saldoventas', 'registro'=>$p["sve_id"], 'detalle'=>$detalle));
		
        return $array;
    }	
	//<<<
	
	//Saldo Compras >>>
    public function sp_saldocompras_lista($p,$tipo=1)
    {
		$offset=isset($p['start'])?$p['start']:0;
		$limit=isset($p['limit'])?$p['limit']:100;
		
        $sql_count = "select COUNT(*) ";
		$sql_selec = "select case when sco.prv_id = 0 then sco.cliente else prv.nombre end as nombre_cliente,doc.descripcion as descripcion_documento,sco.sco_id,sco.codigo,sco.prv_id,sco.tipo_ingreso,sco.doc_id,sco.doc_n,sco.n_guia,sco.cpa_id,sco.mon_id,sco.valor_compra,sco.impuesto_igv,sco.total_compra, DATE_FORMAT(sco.fec_ven, '%d/%m/%Y') as fec_ven, DATE_FORMAT(sco.fec_mov, '%d/%m/%Y') as fec_mov, monedas.nombre as moneda, sco.anulado, case when sco.prv_id = 0 then '' else prv.codigo end as cli_codigo, case when sco.prv_id = 0 then sco.prv_ruc else prv.ruc end as ruc , sco.eliminado, sco.afecta, sco.formato, sco.observacion, prv.direccion, sco.fec_ven as fec_ori, cpa.descripcion condicion, sco.saldo, cpa.letras, cpa.dias, sco.saldo_inicial, sco.saldo_inicial as saldo_tmp ";		
		$sql_from = " from movimientos_saldocompras sco
		left join maestros_proveedores prv on prv.prv_id=sco.prv_id
		inner join documentos doc on doc.doc_id=sco.doc_id
		inner join monedas on sco.mon_id = monedas.mon_id 
		inner join condiciones_pago cpa on sco.cpa_id = cpa.cpa_id ";
		$sql_where = " where 1=1 and sco.eliminado = 0 and sco.age_id = ".$p['age_id']." ";
		
		
		if($p['campo']<>''){
			if($p['campo']=='sco_id'){
					$sql_where = $sql_where." and sco.".$p['campo']." = ".$p['query'];
			}else{
				$sql_where = $sql_where." and sco.".$p['campo']." like '".$p['query']."%' ";
			}
		}
		
		if($p['modo']=='1'){
			$sql_where = $sql_where." and sco.saldo > 0 and sco.cpa_id >1 and anulado = '0' ";
		}
		
		if($p['prv_id']>0){
			$sql_where = $sql_where." and sco.prv_id = ".$p['prv_id']." ";
		}
		
		$sql_order = " order by sco.".($p['sort']?$p['sort']:'fec_mov')." ".($p['dir']?$p['dir']:'desc');
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
    
    public function sp_saldocompras_guardar($p)
    {		
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldocompras_guardar');
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["saldo"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["prv_ruc"],"varchar");
		$detalle = $this->ado->getSql();		
        $array = $this->ado->ExecuteSP();
		//echo $detalle;
	
		$cont = 0;		
		$this->ado->ReiniciarSQL();
		$this->ado->ConnectionOpen($this->log, 'sp_generar_codigo');
        $this->ado->SetParameterSP('movimientos_saldocompras', 'varchar');
        $rs = $this->ado->ExecuteSP();		
	    $data = $rs[0]; 
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'N', 'tabla'=>'saldocompras', 'registro'=>($data[0] - 1), 'detalle'=>$detalle));
		
		//$dat = $this->ado->finalizaTransaccion();
        return ($data[0] - 1);;
    }
	
	public function sp_saldocompras_actualizar($p)
    {
		$this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldocompras_actualizar');
		$this->ado->SetParameterSP($p["sco_id"],"numeric");
        $this->ado->SetParameterSP($p["codigo"],"char");
        $this->ado->SetParameterSP($p["prv_id"],"int");
        $this->ado->SetParameterSP($p["tipo_ingreso"],"int");
        $this->ado->SetParameterSP($p["doc_id"],"int");
        $this->ado->SetParameterSP($p["doc_n"],"varchar");
        $this->ado->SetParameterSP($p["n_guia"],"varchar");
        $this->ado->SetParameterSP($p["cpa_id"],"int");
        $this->ado->SetParameterSP($p["mon_id"],"int");
		$this->ado->SetParameterSP($p["valor_bruto"],"decimal");
		$this->ado->SetParameterSP($p["descuento"],"varchar");
		$this->ado->SetParameterSP($p["valor_desc"],"decimal");
        $this->ado->SetParameterSP($p["valor_compra"],"decimal");
        $this->ado->SetParameterSP($p["saldo"],"decimal");
        $this->ado->SetParameterSP($p["total_compra"],"decimal");
		$this->ado->SetParameterSP($p["fecha"],"date");
		$this->ado->SetParameterSP($p["age_id"],"int");;
		$this->ado->SetParameterSP($p["afecta_stock"],"char");
		$this->ado->SetParameterSP($p["formato"],"char");
		$this->ado->SetParameterSP($p["igv"],"decimal");
		$this->ado->SetParameterSP($p["observacion"],"varchar");
		$this->ado->SetParameterSP($p["cliente"],"varchar");
		$this->ado->SetParameterSP($p["prv_ruc"],"varchar");
		$detalle = $this->ado->getSql();		
        $array = $this->ado->ExecuteSP();
		
		$cont = 0;		 
		$entra="NO";
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'saldocompras', 'registro'=>$p["sco_id"], 'detalle'=>$detalle));
		
        return $sql;
    }
	
	public function sp_saldocompras_eliminar($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldocompras_eliminar');
        $this->ado->SetParameterSP($p["sco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle =  $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'E', 'tabla'=>'saldocompras', 'registro'=>$p["sco_id"], 'detalle'=>$detalle));
		
        return $array;
    }
	
	public function sp_saldocompras_anular($p)
    {
        $this->ado->ReiniciarSQL();
        $this->ado->ConnectionOpen($this->log, 'sp_saldocompras_anular');
        $this->ado->SetParameterSP($p["sco_id"],"int");
		$this->ado->SetParameterSP($p["age_id"],"int");
        $detalle = $this->ado->getSql();
        $array = $this->ado->ExecuteSP();
		
		$array = $this->sp_eventos_guardar(array('usr_id'=>$p['se_usr_id'], 'evento'=>'C', 'tabla'=>'saldocompras', 'registro'=>$p["sco_id"], 'detalle'=>$detalle));
		
        return $array;
    }	
	//<<<
    public function sp_compras_detallado($p = array()){
        $sql = "SELECT 
                mc.mco_id,
                mc.doc_id, 
                d.descripcion, 
                mc.doc_n, 
                DATE_FORMAT(fec_mov, '%d/%m/%Y') as fec_mov, 
                DATE_FORMAT(fec_ven, '%d/%m/%Y') as fec_ven,
                mc.prv_id,
                mp.nombre,
                mc.mon_id,
                mc.age_id,
                mc.anulado,
                mc.tipo_cambio,
                dc.cantidad,
                dc.pro_id,
		mm.codigo1,
		mm.nombre as producto,
		m.nombre as marca,
                dc.precio_compra,
                dc.valor_descuento,
                dc.total
                FROM movimientos_compras mc
                INNER JOIN documentos d ON mc.doc_id=d.doc_id
                INNER JOIN maestros_proveedores mp ON mc.prv_id=mp.prv_id
                INNER JOIN detalle_compras dc ON mc.mco_id=dc.mco_id
		INNER JOIN maestros_mercaderias mm ON dc.pro_id=mm.mcd_id
		LEFT JOIN marcas m ON mm.mar_id=m.mar_id
                WHERE COALESCE(mc.eliminado, 0) = 0 AND mc.age_id = {$p['tie_id']} AND ('{$p['fec_ini']}' <=  mc.fec_ven AND mc.fec_ven <= '{$p['fec_fin']}')
                ORDER BY mc.doc_id, mc.mco_id, mp.nombre, mc.doc_n;";
                
        $data = $this->sp_obtenerdatasql($sql);
        return $data;
    }
    
    public function sp_ventas_detallado($p = array()){
        $sql = "SELECT 
                    mv.mve_id,
                    mv.doc_id, 
                    d.descripcion, 
                    mv.doc_n, 
                    DATE_FORMAT(fec_mov, '%d/%m/%Y') as fec_mov, 
                    DATE_FORMAT(fec_ven, '%d/%m/%Y') as fec_ven,
                    mv.cli_id,
                    mv.cliente,
                    mv.mon_id,
                    mv.age_id,
                    mv.anulado,
                    mv.tipo_cambio,
                    dv.cantidad,
                    dv.pro_id,
		    mm.codigo1,
		    mm.nombre as producto,
		    m.nombre as marca,
                    dv.precio_venta,
                    dv.valor_descuento,
                    dv.total
                    FROM movimientos_ventas mv
                    INNER JOIN documentos d ON mv.doc_id=d.doc_id
                    INNER JOIN detalle_ventas dv ON mv.mve_id=dv.mve_id
		    INNER JOIN maestros_mercaderias mm ON dv.pro_id=mm.mcd_id
		    LEFT JOIN marcas m ON mm.mar_id=m.mar_id
                WHERE COALESCE(mv.eliminado, 0) = 0 AND mv.age_id = {$p['tie_id']} AND ('{$p['fec_ini']}' <=  mv.fec_ven AND mv.fec_ven <= '{$p['fec_fin']}')
                ORDER BY mv.doc_id, mv.mve_id, mv.cliente, mv.doc_n;";

        $data = $this->sp_obtenerdatasql($sql);
        return $data;
    }
	
}
