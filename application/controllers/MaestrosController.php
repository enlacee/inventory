<?php
date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'spanish');

class MaestrosController extends Zend_Controller_Action
{
    private $objDatos;
    private $objDatos_index;
    private $auth;
	private $page;
	private $sessName; 

    public function init()
    {

		//Zend_Session::start();
        $this->sessName = new Zend_Session_Namespace('inventory');

		if($this->sessName->se_usr_id=='' or $this->sessName->se_usr_id == NULL){
			$data = array('success' => false, 'mensaje' => 'Se ha cerrado la sesion, inicie de nuevo la sesion');
			echo json_encode($data);
		}
		
		$_POST['se_usr_id']=$this->sessName->se_usr_id;
		$_POST['se_age_id']=$this->sessName->se_age_id;
        $this->objDatos=new Application_Model_Maestros();
        $this->objDatos_index=new Application_Model_Index();
        $this->auth=new Application_Model_Auth();
        $this->auth->validar_session();
        $this->auth->defines();
    }

    public function getPage(){
		return $this->page;	
	}

	public function setPage(Zend_Pdf_Page $par){
		return $this->page = $par;	
	}

	public function stockAction()
    {

    }	

	public function cierreAction()
    {

    }

	public function rptdocsAction()
    {

    }

	public function rptdocsPdfAction()
    {

		ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);

		

		$col_names = array(

			'tipo' => 'Tipo',

			'nro_doc' => 'Nro Doc',

			'fecha' => 'Fecha',

			'persona' => 'Persona',

			'soles' => 'Imp. Soles',

			'dolares' => 'Imp. Dolares'

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'tipo' => array('justification'=>'center'),

				'nro_doc' => array('justification'=>'left'),

				'fecha' => array('justification'=>'center'),

				'persona' => array('justification'=>'left'),

				'soles' => array('justification'=>'right'),

				'dolares' => array('justification'=>'right')

			)

		);

	

		if($_POST['txtpar3']=='ventas'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE VENTAS";

			$sql="select documentos.descripcion as tipo, movimientos_ventas.doc_n as nro_doc, movimientos_ventas.fec_mov as fecha, maestros_clientes.nombre as persona, case when movimientos_ventas.mon_id = 1 then movimientos_ventas.total_venta else 0 end as soles, case when movimientos_ventas.mon_id = 1 then 0 else movimientos_ventas.total_venta end as dolares

					from movimientos_ventas

					inner join documentos on movimientos_ventas.doc_id = documentos.doc_id

					inner join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id

					where  movimientos_ventas.age_id = ".$_POST['se_age_id']." and movimientos_ventas.eliminado <> 1 and movimientos_ventas.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_ventas.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_ventas.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_ventas.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_ventas.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_ventas.fec_mov ";

			}

		}

		if($_POST['txtpar3']=='compras'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE COMPRAS";

			$sql="select documentos.descripcion as tipo, movimientos_compras.doc_n as nro_doc, movimientos_compras.fec_mov as fecha, maestros_proveedores.nombre as persona, case when movimientos_compras.mon_id = 1 then movimientos_compras.total_compra else 0 end as soles, case when movimientos_compras.mon_id = 1 then 0 else movimientos_compras.total_compra end as dolares

					from movimientos_compras

					inner join documentos on movimientos_compras.doc_id = documentos.doc_id

					inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id

					where  movimientos_compras.age_id = ".$_POST['se_age_id']." and movimientos_compras.eliminado <> 1 and movimientos_compras.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_compras.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_compras.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_compras.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_compras.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_compras.fec_mov, movimientos_compras.doc_n ";

			}

		}

		if($_POST['txtpar3']=='altas'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE ALTAS";

			$sql="select documentos.descripcion as tipo, movimientos_altas.doc_n as nro_doc, movimientos_altas.fec_mov as fecha, '' as persona, case when movimientos_altas.mon_id = 1 then movimientos_altas.total_compra else 0 end as soles, case when movimientos_altas.mon_id = 1 then 0 else movimientos_altas.total_compra end as dolares

					from movimientos_altas

					inner join documentos on movimientos_altas.doc_id = documentos.doc_id

					where  movimientos_altas.age_id = ".$_POST['se_age_id']." and movimientos_altas.eliminado <> 1 and movimientos_altas.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_altas.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_altas.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_altas.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_altas.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_altas.fec_mov, movimientos_altas.doc_n ";

			}

		}

		if($_POST['txtpar3']=='bajas'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE BAJAS";

			$sql="select documentos.descripcion as tipo, movimientos_bajas.doc_n as nro_doc, movimientos_bajas.fec_mov as fecha, '' as persona, case when movimientos_bajas.mon_id = 1 then movimientos_bajas.total_venta else 0 end as soles, case when movimientos_bajas.mon_id = 1 then 0 else movimientos_bajas.total_venta end as dolares

					from movimientos_bajas

					inner join documentos on movimientos_bajas.doc_id = documentos.doc_id

					where  movimientos_bajas.age_id = ".$_POST['se_age_id']." and movimientos_bajas.eliminado <> 1 and movimientos_bajas.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_bajas.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_bajas.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_bajas.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_bajas.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_bajas.fec_mov, movimientos_bajas.doc_n ";

			}	

		}

		if($_POST['txtpar3']=='nventas'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE NOTA DE VENTAS";

			$sql="select documentos.descripcion as tipo, movimientos_notaventas.doc_n as nro_doc, movimientos_notaventas.fec_mov as fecha, maestros_clientes.nombre as persona, case when movimientos_notaventas.mon_id = 1 then movimientos_notaventas.total_venta else 0 end as soles, case when movimientos_notaventas.mon_id = 1 then 0 else movimientos_notaventas.total_venta end as dolares

					from movimientos_notaventas

					inner join documentos on movimientos_notaventas.doc_id = documentos.doc_id

					inner join maestros_clientes on movimientos_notaventas.cli_id = maestros_clientes.cli_id

					where  movimientos_notaventas.age_id = ".$_POST['se_age_id']." and movimientos_notaventas.eliminado <> 1 and movimientos_notaventas.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_notaventas.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_notaventas.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_notaventas.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_notaventas.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_notaventas.fec_mov ";

			}

		}

		if($_POST['txtpar3']=='ncompras'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE NOTA DE COMPRAS";

			$sql="select documentos.descripcion as tipo, movimientos_notacompras.doc_n as nro_doc, movimientos_notacompras.fec_mov as fecha, maestros_proveedores.nombre as persona, case when movimientos_notacompras.mon_id = 1 then movimientos_notacompras.total_compra else 0 end as soles, case when movimientos_notacompras.mon_id = 1 then 0 else movimientos_notacompras.total_compra end as dolares

					from movimientos_notacompras

					inner join documentos on movimientos_notacompras.doc_id = documentos.doc_id

					inner join maestros_proveedores on movimientos_notacompras.prv_id = maestros_proveedores.prv_id

					where  movimientos_notacompras.age_id = ".$_POST['se_age_id']." and movimientos_notacompras.eliminado <> 1 and movimientos_notacompras.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_notacompras.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_notacompras.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_notacompras.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_notacompras.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_notacompras.fec_mov, movimientos_notacompras.doc_n ";

			}

		}

		if($_POST['txtpar3']=='tingresos'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE TRASLADOS INGRESOS";

			$sql="select documentos.descripcion as tipo, movimientos_trasladoing.doc_n as nro_doc, movimientos_trasladoing.fec_mov as fecha, '' as persona, case when movimientos_trasladoing.mon_id = 1 then movimientos_trasladoing.total_compra else 0 end as soles, case when movimientos_trasladoing.mon_id = 1 then 0 else movimientos_trasladoing.total_compra end as dolares

					from movimientos_trasladoing

					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id

					where  movimientos_trasladoing.tie_des = ".$_POST['se_age_id']." and movimientos_trasladoing.eliminado <> 1 and movimientos_trasladoing.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_trasladoing.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_trasladoing.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_trasladoing.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_trasladoing.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_trasladoing.fec_mov, movimientos_trasladoing.doc_n ";

			}

		}

		if($_POST['txtpar3']=='tsalidas'){

			$filtro="";

			$titulo = "LISTADO DE DOCUMENTOS DE TRASLADOS SALIDAS";

			$sql="select documentos.descripcion as tipo, movimientos_trasladoing.doc_n as nro_doc, movimientos_trasladoing.fec_mov as fecha, '' as persona, case when movimientos_trasladoing.mon_id = 1 then movimientos_trasladoing.total_compra else 0 end as soles, case when movimientos_trasladoing.mon_id = 1 then 0 else movimientos_trasladoing.total_compra end as dolares

					from movimientos_trasladoing

					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id

					where  movimientos_trasladoing.age_id = ".$_POST['se_age_id']." and movimientos_trasladoing.eliminado <> 1 and movimientos_trasladoing.fec_mov between '".$_POST['txtpar1']."' and '".$_POST['txtpar2']."' ";	

					if($_POST['txtpar1']==$_POST['txtpar2']){

						$filtro=$filtro." Fecha : ".$_POST['txtpar1'];

					}else{

						$filtro=$filtro." Fecha : ".$_POST['txtpar1']. " al ".$_POST['txtpar2'];

					}

			if($_POST['txtpar4']>0){

				$sql=$sql." and movimientos_trasladoing.doc_id = ".$_POST['txtpar4'];

				$filtro=$filtro." Tipo Doc : ".$_POST['txtpar7'];

			}

			if($_POST['txtpar6']==1){

				$sql=$sql." and movimientos_trasladoing.anulado <> 1";

				$filtro=$filtro." Estado : Habiles";

			}

			if($_POST['txtpar6']==2){

				$sql=$sql." and movimientos_trasladoing.anulado = 1 ";

				$filtro=$filtro." Estado : Solo anulados";

			}

			if($_POST['txtpar5']==1){

				$sql=$sql." order by documentos.doc_id, movimientos_trasladoing.doc_n ";

			}

			if($_POST['txtpar5']==2){

				$sql=$sql." order by documentos.doc_id, movimientos_trasladoing.fec_mov, movimientos_trasladoing.doc_n ";

			}	

		}

		//echo $sql;

		//exit();

		$db_data = $this->objDatos->sp_obtenerdatasql($sql);

		foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }

		$pdf->ezTable($db_data, $col_names, $titulo, $options, $filtro);

		$pdf->ezStream();

    }	

	public function inventarioAction()

    {

        

    }

	

	public function activacionAction()

    {

        $this->view->datos = $this->sessName;

    }

	

	public function kardexAction()

    {

        

    }

	

	public function kardexListaAction(){
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $offset=isset($_POST['start'])?$_POST['start']:0;
		$anio = $_POST['anio'];		
		$mon_id = $_POST['mon_id'];
		$tienda = $_POST['age_id'];
		$producto = $_POST['pro_id'];
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select TOTAL.* ";		
		$sql_from = "FROM (
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion FROM (SELECT @vAno:='$anio' i)fecha,
(SELECT @vTienda:='$tienda' ii)tienda,(SELECT @vProducto:='$producto' iii) producto, v_lista_kardex_saldo_anterior
UNION ALL	
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_compra WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_venta WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL	
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_ingreso WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_salida WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL	
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_nota_credito_compra WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_nota_credito_venta WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_traslado_salida WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio'
UNION ALL
SELECT id,fecha,operacion,tip_doc,nro_doc,entrada,salida,saldo,precio,razonsocial,observacion 
FROM v_lista_kardex_traslado_ingreso WHERE pro_id='$producto' AND age_id='$tienda' AND YEAR(fecha)='$anio') TOTAL";
		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){
				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";
		}

		$sql_order = " order by fecha asc, id ";

		$sql_limit = " limit ".$offset.", 100 ";
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/	
		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);
		$rsSaldo = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order);
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
	
		$saldo = 0;
		
		if(isset($_POST['url'])){
			foreach($rsSaldo as $value){
				if($sw==0){
					$_POST['saldo_anterior'] = $value['entrada'] - $value['salida'];
					$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];
					$sw=1;
				}else{
					$_POST['entradas'] = $_POST['entradas'] + $value['entrada'];
					$_POST['salidas'] = $_POST['salidas'] + $value['salida'];
					$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];	
				}
			}
			$datos = array('saldo_anterior'=>$_POST['saldo_anterior'],'saldo_actual'=>$_POST['saldo_actual'],'entradas'=>$_POST['entradas'],'salidas'=>$_POST['salidas']);
			
			echo json_encode($datos);
		}
		
		if(!isset($_POST['url'])){
			foreach($rsData as $value){
				$saldo = $saldo + $value['entrada'] - $value['salida'];
				$value['saldo'] = $saldo;
				$rsDataFinal[] = $value;
			}
		
			$size=$rsCount[0][0];
		
        	$data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsDataFinal, 0, $size));
       		echo json_encode($data);
		}
    }

	public function kardexListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();	
		$pdf = new ZendCPdf_Cezpdf('a4', 'landscape',$_POST['txttitulo']);

		$_POST['age_id'] = $this->sessName->se_age_id;
        $offset=isset($_POST['start'])?$_POST['start']:0;
		$anio = $_POST['txtpar1'];		
		$mon_id = $_POST['txtpar2'];
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select TOTAL.* ";		
		$sql_from = "
		FROM (
		select 0 as id, CONCAT(cast(".$anio."-1 as char(4)),'-12-31') as fecha, 'SALDO ANTERIOR' as operacion, 'Cierre' as tip_doc, '".($anio-1)."' as nro_doc, stock_cierre.stock as entrada, 0 as salida, stock_cierre.stock as saldo, ROUND(case when mon_id = ".$mon_id." then stock_cierre.precio else case when mon_id = 1 then precio/tipo_cambio else precio*tipo_cambio end end,2) as precio, 'INVENTARIO ".($anio-1)."' as razonsocial, stock_cierre.observacion
		from maestros_mercaderias
			left join stock_cierre on maestros_mercaderias.mcd_id = stock_cierre.pro_id and age_id = ".$_POST['age_id']." and stock_cierre.anio = ".($anio-1)."
		where maestros_mercaderias.mcd_id = ".$_POST['txtpar3']."
		UNION ALL
		select movimientos_compras.mco_id as id, movimientos_compras.fec_ven as fecha, 'COMPRA' as operacion , documentos.abrev as tip_doc, movimientos_compras.doc_n as nro_doc, detalle_compras.cantidad as entrada, 0 as salida, 0 as saldo, ROUND(case when movimientos_compras.mon_id = ".$mon_id." then detalle_compras.precio_compra else case when movimientos_compras.mon_id = 1 then detalle_compras.precio_compra/movimientos_compras.tipo_cambio else detalle_compras.precio_compra*movimientos_compras.tipo_cambio end end,2) as precio, maestros_proveedores.nombre as razonsocial, movimientos_compras.observacion
		from detalle_compras
			inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id and detalle_compras.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']." and movimientos_compras.eliminado = '0' and movimientos_compras.anulado='0' and detalle_compras.afecta_stock = 'S'
			inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id
			inner join documentos on movimientos_compras.doc_id = documentos.doc_id
		where YEAR(movimientos_compras.fec_ven) = ".$anio."
		UNION ALL
		select  movimientos_ventas.mve_id as id, movimientos_ventas.fec_ven as fecha, 'VENTA' as operacion , documentos.abrev as tip_doc, movimientos_ventas.doc_n as nro_doc, 0 as entrada, detalle_ventas.cantidad as salida, 0 as saldo, ROUND(case when movimientos_ventas.mon_id = ".$mon_id." then detalle_ventas.precio_venta else case when movimientos_ventas.mon_id = 1 then detalle_ventas.precio_venta/movimientos_ventas.tipo_cambio else detalle_ventas.precio_venta*movimientos_ventas.tipo_cambio end end,2) as precio, case when movimientos_ventas.cli_id > 0 then maestros_clientes.nombre else movimientos_ventas.cliente end as razonsocial, movimientos_ventas.observacion
		from detalle_ventas
			inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id and detalle_ventas.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']."  and movimientos_ventas.eliminado = '0' and  movimientos_ventas.anulado='0' and detalle_ventas.afecta_stock = 'S'
			left join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id
			inner join documentos on movimientos_ventas.doc_id = documentos.doc_id
		where YEAR(movimientos_ventas.fec_ven) = ".$anio."
		UNION ALL	
		select movimientos_altas.mal_id as id, movimientos_altas.fec_ven as fecha, 'INGRESOS' as operacion , documentos.abrev as tip_doc, movimientos_altas.doc_n as nro_doc, detalle_altas.cantidad as entrada, 0 as salida, 0 as saldo, ROUND(case when movimientos_altas.mon_id = ".$mon_id." then detalle_altas.precio_compra else case when movimientos_altas.mon_id = 1 then detalle_altas.precio_compra/movimientos_altas.tipo_cambio else detalle_altas.precio_compra*movimientos_altas.tipo_cambio end end,2) as precio, tipo_movimiento.nombre as razonsocial, movimientos_altas.observacion
		from detalle_altas
			inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id and detalle_altas.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']." and movimientos_altas.eliminado = '0' and movimientos_altas.anulado='0' and detalle_altas.afecta_stock = 'S'
			inner join documentos on movimientos_altas.doc_id = documentos.doc_id
			inner join tipo_movimiento on movimientos_altas.tmv_id = tipo_movimiento.tmv_id
		where YEAR(movimientos_altas.fec_ven) = ".$anio."
		UNION ALL
		select  movimientos_bajas.mba_id as id, movimientos_bajas.fec_ven as fecha, 'SALIDAS' as operacion , documentos.abrev as tip_doc, movimientos_bajas.doc_n as nro_doc, 0 as entrada, detalle_bajas.cantidad as salida, 0 as saldo, ROUND(case when movimientos_bajas.mon_id = ".$mon_id." then detalle_bajas.precio_venta else case when movimientos_bajas.mon_id = 1 then detalle_bajas.precio_venta/movimientos_bajas.tipo_cambio else detalle_bajas.precio_venta*movimientos_bajas.tipo_cambio end end,2) as precio, tipo_movimiento.nombre as razonsocial, movimientos_bajas.observacion
		from detalle_bajas
			inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id and detalle_bajas.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']."  and movimientos_bajas.eliminado = '0' and  movimientos_bajas.anulado='0' and detalle_bajas.afecta_stock = 'S'
			inner join documentos on movimientos_bajas.doc_id = documentos.doc_id
			inner join tipo_movimiento on movimientos_bajas.tmv_id = tipo_movimiento.tmv_id
		where YEAR(movimientos_bajas.fec_ven) = ".$anio."
		UNION ALL	
		select movimientos_notacompras.nco_id as id, movimientos_notacompras.fec_ven as fecha, 'NOTA CREDITO COMPRAS' as operacion , documentos.abrev as tip_doc, movimientos_notacompras.doc_n as nro_doc, 0 as entrada, detalle_notacompras.cantidad as salida, 0 as saldo, ROUND(case when movimientos_notacompras.mon_id = ".$mon_id." then detalle_notacompras.precio_compra else case when movimientos_notacompras.mon_id = 1 then detalle_notacompras.precio_compra/movimientos_notacompras.tipo_cambio else detalle_notacompras.precio_compra*movimientos_notacompras.tipo_cambio end end,2) as precio, maestros_proveedores.nombre as razonsocial, movimientos_notacompras.observacion
		from detalle_notacompras
			inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id and detalle_notacompras.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']." and movimientos_notacompras.eliminado = '0' and movimientos_notacompras.anulado='0' and detalle_notacompras.afecta_stock = 'S'
			inner join maestros_proveedores on movimientos_notacompras.prv_id = maestros_proveedores.prv_id
			inner join documentos on movimientos_notacompras.doc_id = documentos.doc_id
		where YEAR(movimientos_notacompras.fec_ven) = ".$anio."
		UNION ALL
		select  movimientos_notaventas.nve_id as id, movimientos_notaventas.fec_ven as fecha, 'NOTA CREDITO VENTAS' as operacion , documentos.abrev as tip_doc, movimientos_notaventas.doc_n as nro_doc, detalle_notaventas.cantidad as entrada, 0 as salida, 0 as saldo, ROUND(case when movimientos_notaventas.mon_id = ".$mon_id." then detalle_notaventas.precio_venta else case when movimientos_notaventas.mon_id = 1 then detalle_notaventas.precio_venta/movimientos_notaventas.tipo_cambio else detalle_notaventas.precio_venta*movimientos_notaventas.tipo_cambio end end,2) as precio, maestros_clientes.nombre as razonsocial, movimientos_notaventas.observacion
		from detalle_notaventas
			inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id and detalle_notaventas.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']."  and movimientos_notaventas.eliminado = '0' and  movimientos_notaventas.anulado='0' and detalle_notaventas.afecta_stock = 'S'
			inner join maestros_clientes on movimientos_notaventas.cli_id = maestros_clientes.cli_id
			inner join documentos on movimientos_notaventas.doc_id = documentos.doc_id
		where YEAR(movimientos_notaventas.fec_ven) = ".$anio."
		UNION ALL
		select  movimientos_trasladoing.tin_id as id, movimientos_trasladoing.fec_ven as fecha, 'TRASLADO S' as operacion , documentos.abrev as tip_doc, movimientos_trasladoing.doc_n as nro_doc, 0 as entrada, detalle_trasladoing.cantidad as salida, 0 as saldo, ROUND(case when movimientos_trasladoing.mon_id = ".$mon_id." then detalle_trasladoing.precio_compra else case when movimientos_trasladoing.mon_id = 1 then detalle_trasladoing.precio_compra/movimientos_trasladoing.tipo_cambio else detalle_trasladoing.precio_compra*movimientos_trasladoing.tipo_cambio end end,2) as precio, '' as razonsocial, movimientos_trasladoing.observacion
		from detalle_trasladoing
			inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and detalle_trasladoing.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S'
			inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id
		where YEAR(movimientos_trasladoing.fec_ven) = ".$anio."
		UNION ALL
		select  movimientos_trasladoing.tin_id as id, movimientos_trasladoing.fec_ven as fecha, 'TRASLADO I' as operacion , documentos.abrev as tip_doc, movimientos_trasladoing.doc_n as nro_doc, detalle_trasladoing.cantidad as entrada, 0 as salida, 0 as saldo, ROUND(case when movimientos_trasladoing.mon_id = ".$mon_id." then detalle_trasladoing.precio_compra else case when movimientos_trasladoing.mon_id = 1 then detalle_trasladoing.precio_compra/movimientos_trasladoing.tipo_cambio else detalle_trasladoing.precio_compra*movimientos_trasladoing.tipo_cambio end end,2) as precio, '' as razonsocial, movimientos_trasladoing.observacion
		from detalle_trasladoing
			inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and detalle_trasladoing.pro_id = ".$_POST['txtpar3']." and tie_des = ".$_POST['age_id']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S'
			inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id
		where YEAR(movimientos_trasladoing.fec_ven) = ".$anio."
		) TOTAL ";
		$sql_where = " where 1 = 1 ";
		$sql_order = " order by fecha asc, id ";
	
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		$saldo = 0;
		$_POST['entradas']=0;
		$_POST['salidas']=0;
		$_POST['saldo_anterior']=0;
		$_POST['saldo_actual']=0;
		$sw=0;

		foreach($rsData as $value){
			if($sw==0){
				$_POST['saldo_anterior'] = $value['entrada'] - $value['salida'];
				$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];
				$sw=1;
			}else{
				$_POST['entradas'] = $_POST['entradas'] + $value['entrada'];
				$_POST['salidas'] = $_POST['salidas'] + $value['salida'];
				$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];	
			}
			$saldo = $saldo + $value['entrada'] - $value['salida'];
			$value['saldo'] = $saldo;
			$rsDataFinal[]=$value;
		}

		$size=$rsCount[0][0];
		$col_names = array(
			'fecha' => 'Fecha',
			'operacion' => 'Operacion',
			'tip_doc' => 'Doc',
			'nro_doc' => 'Numero',
			'entrada' => 'Ingreso',
			'salida' => 'Egreso',
			'saldo' => 'Saldo',
			'precio' => 'Precio',
			'razonsocial' => 'Razon Social',
		);

		$options = array(
			'width' => 800,
			'cols' => array(
			'fecha' => array('justification'=>'center'),
			'operacion' => array('justification'=>'left'),
			'tip_doc' => array('justification'=>'center'),
			'nro_doc' => array('justification'=>'center'),
			'entrada' => array('justification'=>'right'),
			'salida' => array('justification'=>'right'),
			'saldo' => array('justification'=>'right'),
			'precio' => array('justification'=>'right'),
			'razon_social' => array('justification'=>'left'),
			)
		);

		$pdf->ezText(utf8_decode("AÑO :").$_POST['txtpar1']."          MONEDA : ".$_POST['txtpar7'], 12, array('justification' => 'left'));
		$pdf->ezText("PRODUCTO :".$_POST['txtpar4'], 12, array('justification' => 'left'));
		$pdf->ezText("MARCA :".$_POST['txtpar5'], 12, array('justification' => 'left'));
		$pdf->ezText("LINEA :".$_POST['txtpar6'], 12, array('justification' => 'left'));
		$pdf->ezText("", 12, array('justification' => 'left'));		
		$pdf->ezTable($rsDataFinal, $col_names, '', $options);	
		$pdf->ezText("", 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("SALDO ANTERIOR :").$_POST['saldo_anterior']."               "."INGRESOS :".$_POST['entradas']."               "."SALIDAS :".$_POST['salidas']."               "."SALDO ACTUAL :".$_POST['saldo_actual'], 12, array('justification' => 'left'));
		$pdf->ezStream();


        /*$this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

        $offset=isset($_POST['start'])?$_POST['start']:0;

		

		$anio = $_POST['txtpar1'];		

		$mon_id = $_POST['txtpar2'];

		

		$sql_count = "select COUNT(*) ";

		$sql_selec = "select TOTAL.* ";		

		$sql_from = " 

		FROM (

		select 0 as id, CONCAT(cast(".$anio."-1 as char(4)),'-12-31') as fecha, 'SALDO ANTERIOR' as operacion, 'Cierre' as tip_doc, '".($anio-1)."' as nro_doc, stock_cierre.stock as entrada, 0 as salida, stock_cierre.stock as saldo, ROUND(case when mon_id = ".$mon_id." then stock_cierre.precio else case when mon_id = 1 then precio/tipo_cambio else precio*tipo_cambio end end,2) as precio, 'INVENTARIO ".($anio-1)."' as razonsocial, stock_cierre.observacion

		from maestros_mercaderias

			left join stock_cierre on maestros_mercaderias.mcd_id = stock_cierre.pro_id and age_id = ".$_POST['age_id']." and stock_cierre.anio = ".($anio-1)."

		where maestros_mercaderias.mcd_id = ".$_POST['txtpar3']."

		UNION ALL	

		select movimientos_compras.mco_id as id, movimientos_compras.fec_ven as fecha, 'COMPRA' as operacion , documentos.abrev as tip_doc, movimientos_compras.doc_n as nro_doc, detalle_compras.cantidad as entrada, 0 as salida, 0 as saldo, ROUND(case when movimientos_compras.mon_id = ".$mon_id." then detalle_compras.precio_compra else case when movimientos_compras.mon_id = 1 then detalle_compras.precio_compra/movimientos_compras.tipo_cambio else detalle_compras.precio_compra*movimientos_compras.tipo_cambio end end,2) as precio, maestros_proveedores.nombre as razonsocial, movimientos_compras.observacion

		from detalle_compras

			inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id and detalle_compras.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']." and movimientos_compras.eliminado = '0' and movimientos_compras.anulado='0' and detalle_compras.afecta_stock = 'S'

			inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id

			inner join documentos on movimientos_compras.doc_id = documentos.doc_id

		where YEAR(movimientos_compras.fec_ven) = ".$anio."

		UNION ALL

		select  movimientos_ventas.mve_id as id, movimientos_ventas.fec_ven as fecha, 'VENTA' as operacion , documentos.abrev as tip_doc, movimientos_ventas.doc_n as nro_doc, 0 as entrada, detalle_ventas.cantidad as salida, 0 as saldo, ROUND(case when movimientos_ventas.mon_id = ".$mon_id." then detalle_ventas.precio_venta else case when movimientos_ventas.mon_id = 1 then detalle_ventas.precio_venta/movimientos_ventas.tipo_cambio else detalle_ventas.precio_venta*movimientos_ventas.tipo_cambio end end,2) as precio, maestros_clientes.nombre as razonsocial, movimientos_ventas.observacion

		from detalle_ventas

			inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id and detalle_ventas.pro_id = ".$_POST['txtpar3']." and age_id = ".$_POST['age_id']."  and movimientos_ventas.eliminado = '0' and  movimientos_ventas.anulado='0' and detalle_ventas.afecta_stock = 'S'

			inner join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id

			inner join documentos on movimientos_ventas.doc_id = documentos.doc_id

		where YEAR(movimientos_ventas.fec_ven) = ".$anio."

		) TOTAL ";

		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}

		$sql_order = " order by fecha asc, id ";

	

		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order);

		

		$saldo = 0;

		$_POST['entradas']=0;

		$_POST['salidas']=0;

		$_POST['saldo_anterior']=0;

		$_POST['saldo_actual']=0;

		$sw=0;

		foreach($rsData as $value){

			if($sw==0){

				$_POST['saldo_anterior'] = $value['entrada'] - $value['salida'];

				$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];

				$sw=1;

			}else{

				$_POST['entradas'] = $_POST['entradas'] + $value['entrada'];

				$_POST['salidas'] = $_POST['salidas'] + $value['salida'];

				

				$_POST['saldo_actual'] = $_POST['saldo_actual'] + $value['entrada'] - $value['salida'];	

			}

			$saldo = $saldo + $value['entrada'] - $value['salida'];

			$value['saldo'] = $saldo;

			$rsDataFinal[]=$value;

		}

		

		

        

		//Configuracion de Campos de Cabecera		

		$dataCampos = array(

				0=>array('idcampo'=>1,

						'descripcion'=>'id',

						'comentario'=>'Código',

						'ancho'=>'20',

						'alineacion'=>'LEFT'),

				1=>array('idcampo'=>2,

						'descripcion'=>'fecha',

						'comentario'=>'Fecha',

						'ancho'=>'60',

						'alineacion'=>'LEFT'),

				2=>array('idcampo'=>3,

						'descripcion'=>'operacion',

						'comentario'=>'OPER',

						'ancho'=>'150',

						'alineacion'=>'LEFT'),

				3=>array('idcampo'=>4,

						'descripcion'=>'entrada',

						'comentario'=>'Ingreso',

						'ancho'=>'60',

						'alineacion'=>'RIGHT'),

				4=>array('idcampo'=>5,

						'descripcion'=>'salida',

						'comentario'=>'Salida',

						'ancho'=>'60',

						'alineacion'=>'RIGHT'),

				5=>array('idcampo'=>6,

						'descripcion'=>'saldo',

						'comentario'=>'Saldo',

						'ancho'=>'60',

						'alineacion'=>'RIGHT'),

				6=>array('idcampo'=>7,

						'descripcion'=>'razonsocial',

						'idtipocontrol'=>'1',

						'comentario'=>'Persona',

						'ancho'=>'150',

						'alineacion'=>'LEFT',

						'diccionario'=>'Descripcion')

			);



		$titulo = "Kardex de Productos";

		$this->generarReporte($dataCampos , $rsDataFinal, $titulo, $_POST);*/

    }

	

	public function inventarioDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function stockDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function inventarioListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $offset=isset($_POST['start'])?$_POST['start']:0;
		$limit=isset($_POST['limit'])?$_POST['limit']:100;
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_inicial, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_pro, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo, lineas.nombre as linea, familias.nombre as familia, (CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END)*precio_costo as total ";		
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join familias on lineas.fam_id = familias.fam_id
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']."
			left join stock_cierre on maestros_mercaderias.mcd_id = stock_cierre.pro_id and stock_cierre.age_id = ".$_POST['age_id']." and stock_cierre.anio = ".$_POST['anio']." ";
		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}
		if($_POST['sort']=='nombre'){
			$_POST['sort']='maestros_mercaderias.nombre';
		}
		if($_POST['sort']=='codigo1'){
			$_POST['sort']='maestros_mercaderias.codigo1';
		}
		if($_POST['sort']=='codigo2'){
			$_POST['sort']='maestros_mercaderias.codigo2';
		}
		if($_POST['sort']=='mar_nom'){
			$_POST['sort']='marcas.nombre';
		}
		if($_POST['sort']=='familia'){
			$_POST['sort']='familias.nombre';
		}
		if($_POST['sort']=='linea'){
			$_POST['sort']='lineas.nombre';
		}
		if($_POST['sort']=='stock_inicial'){
			$_POST['sort']='23';
		}
		if($_POST['sort']=='precio_costo'){
			$_POST['sort']='stock_producto.precio_costo';
		}
		if($_POST['sort']=='precio_venta'){
			$_POST['sort']='stock_producto.precio_venta';
		}
		if($_POST['sort']=='total'){
			$_POST['sort']='29';
		}
		$sql_order = " order by ".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');
		$sql_limit = " limit ".$offset.", ".$limit." ";
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		$size=$rsCount[0][0];
		//$size=2*$offset + 200;
        $data = array('success' => true, 'total' => $size, 'data' => array_splice($rsData, 0, $size));
        echo json_encode($data);
    }

	

	public function stockListaAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

        $offset=isset($_POST['start'])?$_POST['start']:0;

		

		$sql_count = "select COUNT(*) ";

		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, 

		

			CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, 

			CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, 

			

			CASE WHEN ISNULL(stock_producto.stock_inservible_inicial) THEN 0 ELSE stock_producto.stock_inservible_inicial END as stock_inservible_inicial, 

			CASE WHEN ISNULL(stock_producto.stock_inservible) THEN 0 ELSE stock_producto.stock_inservible END as stock_inservible, 

			CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as total_stock,

			

			CASE WHEN ISNULL(stock_producto.stock_inservible_inicial) THEN 0 ELSE stock_producto.stock_inservible_inicial END + CASE WHEN ISNULL(stock_producto.stock_inservible) THEN 0 ELSE stock_producto.stock_inservible END as total_inservible, 

						

			CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, 

			CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo ";		

		$sql_from = " from maestros_mercaderias 

			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 

			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 

			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 

			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']." ";

		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}

		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');

		$sql_limit = " limit ".$offset.", 100 ";

		

        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);

		

		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/

		

		

		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);

		

		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);

		

        

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

	

	public function stockListaImpresionAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

        $offset=isset($_POST['start'])?$_POST['start']:0;

		

		$sql_count = "select COUNT(*) ";

		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo ";		

		$sql_from = " from maestros_mercaderias 

			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 

			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 

			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 

			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']." ";

		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}

		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');

		$sql_limit = " limit ".$offset.", 100 ";

		

		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);

		

        

		$pdf = new ZendCPdf_Cezpdf();

		

		$col_names = array(

			'codigo1' => 'Codigo',

			'nombre' => 'Nombre',

			'mar_nom' => 'Marca',

			'stock_pro' => 'Stock Actual',

			'precio_compra' => 'P. Compra',

			'precio_venta' => 'P. Venta',

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'codigo1' => array('justification'=>'center'),

				'nombre' => array('justification'=>'left'),

				'mar_nom' => array('justification'=>'left'),

				'stock_inicial' => array('justification'=>'right'),

				'precio_compra' => array('justification'=>'right'),

				'precio_venta' => array('justification'=>'right'),

			)

		);



		$pdf->ezTable($rsData, $col_names, 'Inventario Inicial', $options);

		$pdf->ezStream();

    }

	

	public function inventarioListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $offset=isset($_POST['start'])?$_POST['start']:0;
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_inicial, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_pro, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo, lineas.nombre as linea, familias.nombre as familia, (CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END)*precio_costo as total ";
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join familias on lineas.fam_id = familias.fam_id
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']."
			left join stock_cierre on maestros_mercaderias.mcd_id = stock_cierre.pro_id and stock_cierre.age_id = ".$_POST['age_id']." and stock_cierre.anio = 2011 ";
		$sql_where = " where 1 = 1 ";
		if($_POST['txtpar1']<>''){
				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['txtpar1']." like '".$_POST['txtpar2']."%' ";
		}
		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');
		//$sql_limit = " limit ".$offset.", 100 ";
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Inventario Inicial de Productos',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'codigo1' => 'Codigo',
			'nombre' => 'Nombre',
			'mar_nom' => 'Marca',
			'linea' => 'Linea',			
			'stock_inicial' => 'Stock Inicial',
			'precio_compra' => 'P. Compra',
			'precio_costo' => 'P. Costo',
			'precio_venta' => 'P. Venta',
			'total' => 'Total',
		);
		$options = array(
			'width' => 550,
			'cols' => array(
				'codigo1' => array('justification'=>'center'),
				'nombre' => array('justification'=>'left'),
				'mar_nom' => array('justification'=>'left'),
				'stock_inicial' => array('justification'=>'right'),
				'precio_compra' => array('justification'=>'right'),
                                'precio_costo' => array('justification'=>'right'),
				'precio_venta' => array('justification'=>'right'),
			)
		);
                
                foreach($rsData as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $rsData[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $rsData[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($rsData, $col_names, 'Inventario Inicial', $options);
                
		$suma_stock=0;
		$suma_total=0;
		foreach($rsData as $dato){
			$suma_stock=$suma_stock + $dato['stock_inicial'];
			$suma_total=$suma_total + $dato['stock_inicial']*$dato['precio_costo'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("STOCK :").$suma_stock."               "."TOTAL :".$suma_total, 12, array('justification' => 'left'));
		$pdf->ezStream();
    }
	
	public function inventarioListaExcelAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $offset=isset($_POST['start'])?$_POST['start']:0;
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_inicial, CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END as stock_pro, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo, lineas.nombre as linea, familias.nombre as familia, (CASE WHEN ISNULL(stock_cierre.stock) THEN 0 ELSE stock_cierre.stock END)*precio_costo as total ";
		$sql_from = " from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join familias on lineas.fam_id = familias.fam_id
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']."
			left join stock_cierre on maestros_mercaderias.mcd_id = stock_cierre.pro_id and stock_cierre.age_id = ".$_POST['age_id']." and stock_cierre.anio = 2011 ";
		$sql_where = " where 1 = 1 ";
		if($_POST['txtpar1']<>''){
				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['txtpar1']." like '".$_POST['txtpar2']."%' ";
		}
		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');
		//$sql_limit = " limit ".$offset.", 100 ";
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
		
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Productos.xls");
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="10">LISTADO DE STOCK DE PRODUCTOS</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="10">'.$_POST['txtfiltro'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>Codigo 1</td>
					<td>Codigo 2</td>					
					<td>Nombre</td>
					<td>Marca</td>
					<td>Linea</td>
					<td>Stock Inicial</td>
					<td>Precio Compra</td>
					<td>Precio Costo</td>
					<td>Precio Venta</td>
					<td>Precio Total</td>
				</tr>';
		$suma_stock=0;
		$suma_total=0;
		
		foreach ($rsData as $value){
			$suma_stock=$suma_stock + $value['stock_inicial'];
			$suma_total=$suma_total + $value['total'];
			echo '<tr>';
			echo '	<td>'.$value['codigo1'].'</td>
					<td>'.$value['codigo2'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.utf8_decode($value['linea']).'</td>
					<td>'.$value['stock_inicial'].'</td>
					<td>'.$value['precio_compra'].'</td>
					<td>'.$value['precio_costo'].'</td>
					<td>'.$value['precio_venta'].'</td>
					<td>'.$value['total'].'</td>';					
			echo '</tr>';
			
		}
		echo '<tr>';
			echo '	<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.$suma_stock.'</td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.$suma_total.'</td>';					
			echo '</tr>';
		echo '</table>';
    }

	

	public function activacionListaAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

        $offset=isset($_POST['start'])?$_POST['start']:0;

		

		$sql_count = "select COUNT(*) ";

		$sql_selec = "select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.codigo2, maestros_mercaderias.codigo3, maestros_mercaderias.nombre, maestros_mercaderias.ume_id, maestros_mercaderias.mar_id, maestros_mercaderias.lin_id, stock_producto.precio_costo, stock_producto.precio_venta, stock_producto.stock_minimo, maestros_mercaderias.activo, stock_producto.utilidad, stock_producto.desc1, stock_producto.desc2, stock_producto.desc3, stock_producto.desc4, stock_producto.ind_calculo, stock_producto.precio_compra, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE CASE WHEN stock_producto.editado='S' THEN 'true' ELSE 'false' END END as editado, CASE WHEN ISNULL(stock_producto.editado) THEN 'false' ELSE 'true' END as activo ";		

		$sql_from = " from maestros_mercaderias 

			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 

			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 

			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 

			left join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']." ";

		$sql_where = " where stock_producto.pro_id IS NULL ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}

		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');

		$sql_limit = " limit ".$offset.", 100 ";

		

        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);

		

		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/

		

		

		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);

		

		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);

		

        

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

	

	public function stockGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		

		//print_r($_POST);

		

		$rs = $this->objDatos->sp_stock_guardar($_POST);		

		

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function stockActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		

		//print_r($_POST);

		

		$rs = $this->objDatos->sp_stock_actualizar($_POST);		

		//echo $rs;

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function inventarioGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		

		//print_r($_POST);

		

		$rs = $this->objDatos->sp_inventario_guardar($_POST);		

		

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function activacionGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		

		//print_r($_POST);

		//exit();

		$rs = $this->objDatos->sp_activacion_guardar($_POST);		

		

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

    public function ubigeoDepAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_ubigeo_dep($_POST);

        

        $array=array();

        

        foreach($rs as $fila)

        {

            $fila["nom_dep"]=utf8_encode($fila["nom_dep"]);

            $array[]=$fila;

        }



        $data = array('success' => true, 'total' => count($rs), 'data' => $array);

        echo json_encode($data);

    }

    

    public function ubigeoCiuAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_ubigeo_ciu($_POST);

        

        $array=array();

        

        foreach($rs as $fila)

        {

            $fila["nom_ciu"]=utf8_encode($fila["nom_ciu"]);

            $array[]=$fila;

        }

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $array);

        echo json_encode($data);

    }

    

    public function ubigeoDisAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_ubigeo_dis($_POST);

        

        $array=array();

        

        foreach($rs as $fila)

        {

            $fila["nom_dis"]=utf8_encode($fila["nom_dis"]);

            $array[]=$fila;

        }

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $array);

        echo json_encode($data);

    }

    

	

	//

    public function agenciasTransporteCodigoAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_generar_codigo($_POST);



        $data = $rs[0]; 

        echo json_encode($data);

    }

    

    public function agenciasTransporteDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function agenciasTransporteListaAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_agencias_transporte_lista($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	

	public function printAction(){

		$this->_helper->viewRenderer->setNoRender();

		//echo "HOLA";

		

		$prueba = new ZendCPdf_Cezpdf();

		

		//$prueba->configuraPagina("portrait","Titulo");

		//echo $prueba;

		$prueba->ezText('CODIGO :', 12, array('justification' => 'left'));			

		$prueba->ezStream();

			

	}

	public function agenciasTransporteListaImpresionAction()

    {

        ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		$pdf = new ZendCPdf_Cezpdf();

		

		$col_names = array(

			'codigo' => 'Codigo',

			'nombre' => 'Nombre',

			'ruc' => 'Documento',

			'direccion' => 'Direccion',

			'telefono' => 'Telefono',

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'codigo' => array('justification'=>'center'),

				'nombre' => array('justification'=>'left'),

				'ruc' => array('justification'=>'left'),

				'direccion' => array('justification'=>'left'),

				'telefono' => array('justification'=>'left'),

			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=100;

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$db_data = $this->objDatos->sp_agencias_transporte_lista($_POST);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Agencias Transportes', $options);

		$pdf->ezStream();

		

    }

    

    public function agenciasTransporteGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Agencia de Transporte con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where codigo = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Agencia de Transporte con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where ruc = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Agencia de Transporte con el RUC '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_agencias_transporte_guardar($_POST);        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function agenciasTransporteActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where codigo = '".$_POST['codigo']."' and age_id <> ".$_POST['age_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where nombre = '".$_POST['nombre']."' and age_id <> ".$_POST['age_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_agencias_transporte where ruc = '".$_POST['ruc']."' and age_id <> ".$_POST['age_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el codigo '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_agencias_transporte_actualizar($_POST);        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function agenciasTransporteEliminarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_agencias_transporte_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function agenciasTransporteAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 7 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }	

	

	//

	public function tiendasCodigoAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_generar_codigo($_POST);



        $data = $rs[0]; 

        echo json_encode($data);

    }

    

    public function tiendasDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function tiendasListaAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_tiendas_lista($_POST);

		

		if(isset($_POST['quitar'])=='SI'){

			foreach($rs as $fila)

			{

				if($fila["tie_id"]>2){

					$fila["tie_id"]=utf8_encode($fila["tie_id"]);

					$fila["nombre"]=utf8_encode($fila["nombre"]);

					if(!(isset($_POST['mismo'])=='NO' and $fila["tie_id"] == $_POST['se_age_id'])){

						$array[]=$fila;

					}

				}

			}

			$rs = $array;

		}

		

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function tiendasListaImpresionAction()

    {

		ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Tiendas',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

		$col_names = array(

			'codigo' => 'Codigo',

			'nombre' => 'Nombre',

			'ruc' => 'Documento',

			'direccion' => 'Direccion',

			'telefono' => 'Telefono',

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'codigo' => array('justification'=>'center'),

				'nombre' => array('justification'=>'left'),

				'ruc' => array('justification'=>'left'),

				'direccion' => array('justification'=>'left'),

				'telefono' => array('justification'=>'left'),

			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=100;

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$db_data = $this->objDatos->sp_tiendas_lista($_POST);
		
		if($_POST["campo"]=="" || $_POST["query"]==""){
			$filtro = "";
		}else{
			$filtro = "FILTRO : ".$_POST["campo"]." = ".$_POST["query"];
		}
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }

		$pdf->ezTable($db_data, $col_names, $filtro, $options);

		$pdf->ezStream();

		

    }

    

    public function tiendasGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Tienda con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where codigo = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Tienda con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where ruc = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Tienda con el RUC '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_tiendas_guardar($_POST);        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function tiendasActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where codigo = '".$_POST['codigo']."' and tie_id <> ".$_POST['tie_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Tienda con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where nombre = '".$_POST['nombre']."' and tie_id <> ".$_POST['tie_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe unaq Tienda con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from tiendas where ruc = '".$_POST['ruc']."' and tie_id <> ".$_POST['tie_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una Tienda con el codigo '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_tiendas_actualizar($_POST);        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function tiendasEliminarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_tiendas_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function tiendasAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 48 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }	

	

    //

    public function proveedoresCodigoAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_generar_codigo($_POST);



        $data = $rs[0]; 

        echo json_encode($data);

    }

    

    public function proveedoresDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	

    

    public function proveedoresListaAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		$offset=isset($_POST['start'])?$_POST['start']:0;

		

		$rsCount = $this->objDatos->sp_proveedores_lista($_POST,1);		

		$rsData = $this->objDatos->sp_proveedores_lista($_POST,2);

		

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

	

	public function proveedoresListaImpresionAction()

    {

        ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);

		

		$col_names = array(
			'codigo' => 'Codigo',
			'nombre' => 'Nombre/Razon_Social',
			'tip_per' => 'N/J',
			'ruc' => 'Documento',
			'direccion' => 'Direccion',
			'telefono' => 'Telefono',
		);

		

		$options = array(
			'width' => 550,
			'cols' => array(
				'codigo' => array('justification'=>'center'),
				'nombre' => array('justification'=>'left'),
				'ruc' => array('justification'=>'left'),
				'direccion' => array('justification'=>'left'),
				'telefono' => array('justification'=>'left'),
			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=-1;

		$_POST['sort']=$_POST['txtsort'];

		$_POST['dir']=$_POST['txtdir'];

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$_POST['tip_per']=$_POST['txtpar4'];

		$db_data = $this->objDatos->sp_proveedores_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Proveedores', $options, $_POST['txtfiltro']);

		$pdf->ezStream();	

		

    }

    

    public function proveedoresGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where codigo = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where ruc = '".$_POST['ruc']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el ruc '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		/*echo "HOLA";

		exit();*/

		

		$rs = $this->objDatos->sp_proveedores_guardar($_POST);        

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);

    }

    

    public function proveedoresActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where codigo = '".$_POST['codigo']."' and prv_id <> ".$_POST['prv_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where nombre = '".$_POST['nombre']."' and prv_id <> ".$_POST['prv_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_proveedores where ruc = '".$_POST['ruc']."' and prv_id <> ".$_POST['prv_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Proveedor con el ruc '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_proveedores_actualizar($_POST);        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function proveedoresEliminarAction()

    {

	$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where eliminado = 0 and anulado = 0 and prv_id = ".$_POST['prv_id']."");

	if(count($rs)>0){

		$data = array('success' => false, 'mensaje' => 'Existes movimientos de compra con este proveedor, por favor verifique');

		echo json_encode($data);

		exit();

	}

	$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_notacompras where eliminado = 0 and anulado = 0 and prv_id = ".$_POST['prv_id']."");

	if(count($rs)>0){

		$data = array('success' => false, 'mensaje' => 'Existes movimientos de compra con este proveedor, por favor verifique');

		echo json_encode($data);

		exit();

	}

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_proveedores_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function proveedoresAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 6 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }

	

	public function buscaproveedoresAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 6 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }

    

	//

	

	//Clientes

	public function buscaclientesAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 5 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }

	

	public function clientesAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 5 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }

	

    public function clientesCodigoAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_generar_codigo($_POST);



        $data = $rs[0]; 

        echo json_encode($data);

    }

    

    public function clientesDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

	

	public function clientesBuscarrucAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

				

        $rs = $this->objDatos->sp_obtenerdatasql("select maestros_clientes.*, ubigeo.nom_dep, ubigeo.nom_ciu, ubigeo.nom_dis from maestros_clientes inner join ubigeo on maestros_clientes.dep_id = ubigeo.dep_id and maestros_clientes.ciu_id = ubigeo.ciu_id and maestros_clientes.dis_id = ubigeo.dis_id where ruc like '".$_POST['cli_ruc']."%'");

		if(count($rs)==1){

			$data = array('success' => true, 'nro' => 1, 'cli_id' => $rs[0]['cli_id'], 'cli' => $rs[0]['nombre'], 'codigo' => $rs[0]['codigo'], 'tip_per' => $rs[0]['tip_per'], 'direccion' => $rs[0]['direccion'].'-'.$rs[0]['nom_dep'].' '.$rs[0]['nom_ciu'].' '.$rs[0]['nom_dis']);

			echo json_encode($data);

			exit();

		}

        $data = array('success' => true, 'nro' => count($rs));

        echo json_encode($data);

    }

	

    public function clientesListaAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;		

        $rsCount = $this->objDatos->sp_clientes_lista($_POST,1);		

		$rsData = $this->objDatos->sp_clientes_lista($_POST,2);

		

        //$rsData[0]=array('nombre'=>$_POST[0]);

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

    

    public function clientesGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where codigo = '".$_POST['codigo']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where ruc = '".$_POST['ruc']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el RUC '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$codigo = $this->objDatos->sp_clientes_guardar($_POST);        

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs, codigo => $codigo);

		echo json_encode($data);

    }

    

    public function clientesActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where codigo = '".$_POST['codigo']."' and cli_id <> ".$_POST['cli_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el codigo '.$_POST['codigo'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}*/

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where nombre = '".$_POST['nombre']."' and cli_id <> ".$_POST['cli_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_clientes where ruc = '".$_POST['ruc']."' and cli_id <> ".$_POST['cli_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe un Cliente con el RUC '.$_POST['ruc'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_clientes_actualizar($_POST);		

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);



    }

    

    public function clientesEliminarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos->sp_clientes_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

	

	public function clientesListaImpresionAction()

    {

        ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);

		

		$col_names = array(

			'codigo' => 'Codigo',

			'nombre' => 'Nombre',

			'tipo_doc' => 'Doc',

			'ruc' => 'Numero'

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'codigo' => array('justification'=>'center'),

				'nombre' => array('justification'=>'left'),

				'tipo_doc' => array('justification'=>'left'),

				'ruc' => array('justification'=>'left'),

			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=-1;

		$_POST['sort']=$_POST['txtsort'];

		$_POST['dir']=$_POST['txtdir'];

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$db_data = $this->objDatos->sp_clientes_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, "LISTADO DE CLIENTES", $options,$_POST['txtfiltro']);

		$pdf->ezStream();			

    }
	
	public function clientesListaExcelAction()
    {
        ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=-1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_clientes_lista($_POST,2);
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Clientes.xls");
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="7">LISTADO DE CLIENTES</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="7">'.$_POST['txtfiltro'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>Codigo</td>
					<td>Nombre</td>					
					<td>Persona Jur/Nat</td>
					<td>Tipo Doc</td>
					<td>Nro Doc</td>
					<td>Ubigeo</td>
					<td>Direccion</td>
				</tr>';
		foreach ($db_data as $value){
			echo '<tr>';
			echo '	<td>'.$value['codigo'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.$value['tipo_per'].'</td>
					<td>'.$value['tipo_doc'].'</td>
					<td>'.$value['ruc'].'</td>
					<td>'.$value['ubigeo'].'</td>
					<td>'.utf8_decode($value['direccion']).'</td>';
			echo '</tr>';
			
		}
		echo '</table>';

    }

    

    

    

	//

    public function mercaderiasDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function mercaderiasListaAction()

    {

		

        $this->_helper->viewRenderer->setNoRender();

		

		$_POST['age_id'] = $this->sessName->se_age_id;

		

        $rsCount = $this->objDatos->sp_mercaderias_lista($_POST,1);

		$rsData = $this->objDatos->sp_mercaderias_lista($_POST,2);

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

	

	public function mercaderiasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);		
		$col_names = array(
			'codigo1' => 'Codigo 1',
			'codigo2' => 'Codigo 2',
			'nombre' => 'Nombre',
			'stock_pro' => 'Stock',
			'mar_nom' => 'Marca',
			'linea' => 'Linea',
			'precio_compra' => 'P. Compra',
			'precio_costo' => 'P. Costo',
			'precio_venta' => 'P. Venta',
			'total' => 'Total',
		);		
		$options = array(
			'width' => 550,
			'cols' => array(
				'codigo1' => array('justification'=>'center'),
				'codigo2' => array('justification'=>'center'),
				'nombre' => array('justification'=>'left'),
				'stock_pro' => array('justification'=>'center'),
				'mar_nom' => array('justification'=>'left'),
				'linea' => array('justification'=>'left'),
				'precio_compra' => array('justification'=>'right'),
				'precio_costo' => array('justification'=>'right'),
				'precio_venta' => array('justification'=>'right'),
				'total' => array('justification'=>'right'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar4']-1)*100;
		$_POST['limit']=-1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$_POST['lin_id']=$_POST['txtpar3'];
		$_POST['mar_id']=$_POST['txtpar5'];
                
		$db_data = $this->objDatos->sp_mercaderias_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Stock de Productos', $options,$_POST['txtfiltro'] );
		$suma_stock=0;
		$suma_total=0;
		foreach($db_data as $dato){
			$suma_stock=$suma_stock + $dato['stock_pro'];
			$suma_total=$suma_total + $dato['stock_pro']*$dato['precio_costo'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("STOCK :").$suma_stock."               "."TOTAL :".$suma_total, 12, array('justification' => 'left'));
		$pdf->ezStream();
    }



    

    public function mercaderiasGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        

		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}*/

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where codigo1 = '".$_POST['codigo1']."' and mar_id = ".$_POST['mar_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el codigo 1 : '.$_POST['codigo1'].' y marca '.$_POST['mar_nom'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		if($_POST['lin_id']==0 || $_POST['lin_id']==''){

			$data = array('success' => false, 'mensaje' => 'Debe seleccionar la linea, por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_mercaderias_guardar($_POST);			

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);

    }

    

    public function mercaderiasActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		//echo "FINAL";

		//exit();

		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where nombre = '".$_POST['nombre']."' and mcd_id <> ".$_POST['mcd_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}*/

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where codigo1 = '".$_POST['codigo1']."' and mar_id = ".$_POST['mar_id']."  and mcd_id <> ".$_POST['mcd_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el codigo 1 : '.$_POST['codigo1'].' y marca '.$_POST['mar_nom'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		if($_POST['lin_id']==0 || $_POST['lin_id']==''){

			$data = array('success' => false, 'mensaje' => 'Debe seleccionar la linea, por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_mercaderias_actualizar($_POST);	

		//echo $rs;	

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);

    }

    

    public function mercaderiasEliminarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_altas inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id where movimientos_altas.age_id = ".$_POST['se_age_id']." and movimientos_altas.eliminado = 0 and  detalle_altas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Altas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_bajas inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id where movimientos_bajas.age_id = ".$_POST['se_age_id']." and movimientos_bajas.eliminado = 0 and  detalle_bajas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Altas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_compras inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id where movimientos_compras.age_id = ".$_POST['se_age_id']." and movimientos_compras.eliminado = 0 and  detalle_compras.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Compras con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_ventas inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id where movimientos_ventas.age_id = ".$_POST['se_age_id']." and movimientos_ventas.eliminado = 0 and  detalle_ventas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Ventas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_notacompras inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id where movimientos_notacompras.age_id = ".$_POST['se_age_id']." and movimientos_notacompras.eliminado = 0 and  detalle_notacompras.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito de Compras con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_notaventas inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id where movimientos_notaventas.age_id = ".$_POST['se_age_id']." and movimientos_notaventas.eliminado = 0 and  detalle_notaventas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito de Ventas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		

		

		

        $rs = $this->objDatos->sp_mercaderias_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

        

    public function mercaderiasAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 4 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

		$this->view->datos = $this->sessName;

		$this->view->usp_id = $this->sessName->se_usp_id;

		

		

    }

	

	//Producto

	public function productoDescribeAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        $rs = $this->objDatos_index->sp_table_describe($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

    

    public function productoListaAction()

    {		

        $this->_helper->viewRenderer->setNoRender();		

		$_POST['age_id'] = $this->sessName->se_age_id;

		/*$offset=isset($_POST['start'])?$_POST['start']:0;

		

		$sql_count = "select COUNT(*) ";

		$sql_selec = "select maestros_mercaderias.*, lineas.fam_id, unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END as stock_inicial, CASE WHEN ISNULL(stock_producto.stock_inicial) THEN 0 ELSE stock_producto.stock_inicial END + CASE WHEN ISNULL(stock_producto.stock) THEN 0 ELSE stock_producto.stock END as stock_pro, lineas.nombre as nom_lin ";		

		$sql_from = " from maestros_mercaderias 

			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 

			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 

			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id 

			left join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['age_id']." ";

		$sql_where = " where 1 = 1 ";

		if($_POST['campo']<>''){

				$sql_where = $sql_where." and maestros_mercaderias.".$_POST['campo']." like '".$_POST['query']."%' ";

		}

		if($_POST['lin_id']<>'' or $_POST['lin_id']>0){

				$sql_where = $sql_where." and maestros_mercaderias.lin_id = ".$_POST['lin_id']." ";

		}

		

		$sql_order = " order by maestros_mercaderias.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');

		$sql_limit = " limit ".$offset.", 100 ";*/

		

        $rsCount = $this->objDatos->sp_producto_lista($_POST,1);

		$rsData = $this->objDatos->sp_producto_lista($_POST,2);

		$size=$rsCount[0][0];



        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

        echo json_encode($data);

    }

	

	public function productoListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo'],0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'codigo1' => 'Codigo',
			'nombre' => 'Nombre',
			'mar_nom' => 'Marca',
			'nom_lin' => 'Linea'
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'codigo1' => array('justification'=>'center'),
				'nombre' => array('justification'=>'left'),
				'mar_nom' => array('justification'=>'left'),
				'nom_lin' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar4']-1)*100;
		$_POST['limit']=-1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$_POST['lin_id']=$_POST['txtpar3'];
		$_POST['mar_id']=$_POST['txtpar5'];
		
                $db_data = $this->objDatos->sp_producto_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Mercaderias', $options,$_POST['txtfiltro'] );
		$pdf->ezStream();
    }
	
	public function productoListaExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar4']-1)*100;
		$_POST['limit']=-1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$_POST['lin_id']=$_POST['txtpar3'];
		$_POST['mar_id']=$_POST['txtpar5'];
		$db_data = $this->objDatos->sp_producto_lista($_POST,2);
		
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Mercaderias.xls");
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="7">LISTADO DE MERCADERIAS</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="7">'.$_POST['txtfiltro'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>Codigo 1</td>
					<td>Codigo 2</td>					
					<td>Nombre</td>
					<td>Marca</td>
					<td>Familia</td>
					<td>Linea</td>
					<td>Estado</td>
				</tr>';
		foreach ($db_data as $value){
			echo '<tr>';
			echo '	<td>'.$value['codigo1'].'</td>
					<td>'.$value['codigo2'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.utf8_decode($value['familia']).'</td>
					<td>'.utf8_decode($value['linea']).'</td>
					<td>'.$value['activo'].'</td>';					
			echo '</tr>';
			
		}
		echo '</table>';
    }
	
	public function mercaderiasListaExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar4']-1)*100;
		$_POST['limit']=-1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$_POST['lin_id']=$_POST['txtpar3'];
		$_POST['mar_id']=$_POST['txtpar5'];
		$db_data = $this->objDatos->sp_mercaderias_lista($_POST,2);
		
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Productos.xls");
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="9">LISTADO DE STOCK DE PRODUCTOS</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="9">'.$_POST['txtfiltro'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>Codigo 1</td>
					<td>Codigo 2</td>					
					<td>Nombre</td>
					<td>Marca</td>
					<td>Linea</td>
					<td>Precio Compra</td>
					<td>Precio Costo</td>
					<td>Precio Venta</td>
					<td>Precio Total</td>
				</tr>';
		$suma_stock=0;
		$suma_total=0;
		foreach ($db_data as $value){
			
			$suma_stock=$suma_stock + $dato['stock_inicial'];
			$suma_total=$suma_total + $value['total'];
			
			echo '<tr>';
			echo '	<td>'.$value['codigo1'].'</td>
					<td>'.$value['codigo2'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.utf8_decode($value['linea']).'</td>
					<td>'.$value['precio_compra'].'</td>
					<td>'.$value['precio_costo'].'</td>
					<td>'.$value['precio_venta'].'</td>
					<td>'.$value['total'].'</td>';
			echo '</tr>';
			
		}
		echo '<tr>';
			echo '	<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.$suma_total.'</td>';					
			echo '</tr>';
		echo '</table>';
    }

    

    public function productoGuardarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

        

		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where nombre = '".$_POST['nombre']."'");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}*/

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where codigo1 = '".$_POST['codigo1']."' and mar_id = ".$_POST['mar_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el codigo 1 : '.$_POST['codigo1'].' y marca '.$_POST['mar_nom'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		

		$rs = $this->objDatos->sp_producto_guardar($_POST);			

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);

    }

    

    public function productoActualizarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		//echo "FINAL";

		//exit();

		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where nombre = '".$_POST['nombre']."' and mcd_id <> ".$_POST['mcd_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el nombre '.$_POST['nombre'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}*/

		

		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where codigo1 = '".$_POST['codigo1']."' and mar_id = ".$_POST['mar_id']."  and mcd_id <> ".$_POST['mcd_id']."");

		if(count($rs)>0){

			$data = array('success' => false, 'mensaje' => 'Ya existe una mercaderia con el codigo 1 : '.$_POST['codigo1'].' y marca '.$_POST['mar_nom'].', por favor modifique los datos');

			echo json_encode($data);

			exit();

		}

		move_uploaded_file($_FILES['producto-url_imagen']['tmp_name'],"http://importacionesinga.com.pe/inventory/public/uploads/".$_FILES['producto-url_imagen']['name']);

		//echo $_FILES['producto-url_imagen']['tmp_name'];

		//exit();

		$rs = $this->objDatos->sp_producto_actualizar($_POST);	

		//echo $rs;	

		$data = array('success' => true, 'total' => count($rs), 'data' => $rs);

		echo json_encode($data);

    }

    

    public function productoEliminarAction()

    {

        $this->_helper->viewRenderer->setNoRender();

		

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_altas inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id where movimientos_altas.eliminado = 0 and  detalle_altas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Altas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_bajas inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id where movimientos_bajas.eliminado = 0 and  detalle_bajas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Altas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_compras inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id where movimientos_compras.eliminado = 0 and  detalle_compras.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Compras con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_ventas inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id where movimientos_ventas.eliminado = 0 and  detalle_ventas.pro_id = ".$_POST['mcd_id']."");



		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Ventas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_notacompras inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id where movimientos_notacompras.eliminado = 0 and  detalle_notacompras.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito de Compras con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		$rs = $this->objDatos->sp_obtenerdatasql("select COUNT(*) as nro from detalle_notaventas inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id where movimientos_notaventas.eliminado = 0 and  detalle_notaventas.pro_id = ".$_POST['mcd_id']."");

		if($rs[0][0]>0){

			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito de Ventas con el producto, no se puede eliminar');

			echo json_encode($data);

			exit();

		}

		

        $rs = $this->objDatos->sp_producto_eliminar($_POST);

        

        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);

        echo json_encode($data);

    }

        

    public function productoAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 50 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

		//echo "ABC";

    }

	//<<<

	

	

	//Reporte

	public function getTextWidth($text, Zend_Pdf_Resource_Font $font, $font_size) 

	{

		$drawing_text = iconv('', 'UTF-16BE', $text);

		$characters    = array();

		for ($i = 0; $i < strlen($drawing_text); $i++) {

			$characters[] = (ord($drawing_text[$i++]) << 8) | ord ($drawing_text[$i]);

		}

		$glyphs        = $font->glyphNumbersForCharacters($characters);

		$widths        = $font->widthsForGlyphs($glyphs);

		$text_width   = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;

		return $text_width;	

	}

	

	public function generarReporte($dataCampos, $rs, $titulo, $p=NULL){

		$pdf = new Zend_Pdf();

		$pdf->pages[] = ($page = $pdf->newPage('A4'));

		

		$style = new Zend_Pdf_Style();

        $style->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0)); 

        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES); 

        $style->setFont($font,12);

        $page->setStyle($style);

		

		$page->setFont($font, 14)

					->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

					->drawText("Sistema Inventory", 20,820);

					  

		

		

		$nro_tot = count($rs);

		

		if($nro_tot % 35 == 0){

			$nro_pag = (int)($nro_tot/35);

		}else{

			$nro_pag = (int)($nro_tot/35) + 1;

		}



		

		if($nro_tot>0){

			

			$text_width = $this->getTextWidth("Pagina 1 de ".$nro_pag, $page->getFont(), $page->getFontSize());

			$left = 595 - $text_width - 10;

		

			$page->setFont($font, 14)

						->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						->drawText("Pagina 1 de ".$nro_pag, $left ,820);

						  

			$page->setLineWidth(0.2)

					->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

					->setLineDashingPattern(array(0, 0, 0, 0), 1.6)

					->drawLine(10, 815, 585, 815);

					

			//Titulo

			$page->setFont($font, 16)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText($titulo, 20 ,790);

				

			if($titulo == "Kardex de Productos"){

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText(utf8_decode("AÑO :").$p['txtpar1']."          MONEDA : ".$p['txtpar7'], 20 ,775);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("PRODUCTO :".$p['txtpar4'], 20 ,760);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("MARCA :".$p['txtpar5'], 20 ,745);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("LINEA :".$p['txtpar6'], 20 ,730);

			}

			

			$espacio_leyenda=60;

				

			reset($dataCampos);

			$x=30;

			$y = 760 - $espacio_leyenda;

			foreach($dataCampos as $solo){				  

				$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))

					  ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))

					  ->drawRectangle($x -3,$y+20 - 3, $x + $solo['ancho'] - 3,$y - 3);

					  

				$page->setFont($font, 14)

					  ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

					  ->drawText(utf8_decode($solo['comentario']), $x,$y);

					  

				$x = $x +$solo['ancho'];

			}	

			

			$y = 740 - $espacio_leyenda;

			$con_pag =1;

	

			foreach($rs as $fila)

			{

				$x=30;

				if($y - 50 < 0){

					

					$con_pag = $con_pag +1;

					

					$page->setFont($font, 14)

								->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

								->drawText("Empresa XXXX", 10,20);

								  

					$text_width = $this->getTextWidth("Lima, ".strftime("%A, %d de %B de %Y"), $page->getFont(), $page->getFontSize());

					$left = 595 - $text_width - 10;

							

					$page->setFont($font, 14)

								  ->drawText("Lima, ".strftime("%A, %d de %B de %Y"), $left ,20);

					$page->setLineWidth(0.2)

							->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

							->setLineDashingPattern(array(0, 0, 0, 0), 1.6)

							->drawLine(10, 40, 585, 40);				

							

					$pdf->pages[] = ($page = $pdf->newPage('A4'));

					

					$page->setStyle($style);

					

					$page->setFont($font, 14)

								->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

								->drawText("Sistema Inventory", 20,820);

					

					$text_width = $this->getTextWidth("Pagina ".$con_pag." de ".$nro_pag, $page->getFont(), $page->getFontSize());

					$left = 595 - $text_width - 10;

							

					$page->setFont($font, 14)

								->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

								->drawText("Pagina ".$con_pag." de ".$nro_pag, $left ,820);

								  

					$page->setLineWidth(0.2)

							->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

							->setLineDashingPattern(array(0, 0, 0, 0), 1.6)

							->drawLine(10, 815, 585, 815);

					

					//Titulo

					$page->setFont($font, 16)

						->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						->drawText($titulo, 20 ,790);

					

					reset($dataCampos);

					$x=30;

					$y = 760 - $espacio_leyenda;

					foreach($dataCampos as $solo){				  

						$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))

							  ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))

							  ->drawRectangle($x -3,$y+20 - 3, $x + $solo['ancho'] - 3,$y - 3);

							  

						$page->setFont($font, 14)

							  ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

							  ->drawText(utf8_decode($solo['comentario']), $x,$y);

							  

						$x = $x +$solo['ancho'];

					}	

					

					$x=30;

					$y = 740 - $espacio_leyenda;

					

				}

				reset($dataCampos);

				foreach($dataCampos as $solo){					  

					$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'))

						  ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))

						  ->setLineDashingPattern(array(0, 0, 0, 0), 1.6)

						  ->drawRectangle($x -3,$y+20 - 3, $x + $solo['ancho'] - 3,$y - 3);

						  

					$page->setFont($font, 12)

						  ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						  ->drawText(utf8_decode($fila[$solo['descripcion']]), $x,$y);

						  

					$x = $x + $solo['ancho'];

				}

				

				$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'))

						  ->setLineColor(Zend_Pdf_Color_Html::color('#ffffff'))

						  ->setLineDashingPattern(array(0, 0, 0, 0), 0)

						  ->drawRectangle($x -3 +1,$y+20 - 3, $x + $solo['ancho'] - 3,$y - 3);

						  

				$page->setFont($font, 12)

						  ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						  ->drawText("                       ", $x,$y);

	

				$y = $y - 20;

				

	

	

			}

			

			if($titulo == "Kardex de Productos"){

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText(utf8_decode("SALDO ANTERIOR :").$p['saldo_anterior'], 30 ,$y);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("INGRESOS :".$p['entradas'], 200, $y);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("SALIDAS :".$p['salidas'], 300, $y);

				$page->setFont($font, 10)

				->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

				->drawText("SALDO ACTUAL :".$p['saldo_actual'],400, $y);

			}

			

			$page->setFont($font, 14)

						->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						->drawText("Empresa XXXX", 10,20);

			

			



			$text_width = $this->getTextWidth("Lima, ".strftime("%A, %d de %B de %Y"), $page->getFont(), $page->getFontSize());

			$left = 595 - $text_width - 10;

					

			$page->setFont($font, 14)

						  ->drawText("Lima, ".strftime("%A, %d de %B de %Y"), $left ,20);

			$page->setLineWidth(0.2)

					->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

					->setLineDashingPattern(array(0, 0, 0, 0), 1.6)

					->drawLine(10, 40, 585, 40);

		}else{

			$page->setFont($font, 14)

						->setFillColor(Zend_Pdf_Color_Html::color('#000000'))

						->drawText("Sin informacion", 220 ,700);

		}

			echo "<pre>";

			header("content-type: application/pdf");

			print($pdf->render());	

	}
        
        function rptstocksAction(){}
        function rptstocksExcelAction(){
            $this->_helper->viewRenderer->setNoRender();
            
            $prm['age_id'] = $this->sessName->se_age_id;
            $prm['tip_doc'] = $_POST['dato1'];
            $prm['stock_sel'] = $_POST['dato2'];
            $prm['ord_doc'] = $_POST['dato3'];
            
            $sql = "SELECT * FROM (
                        SELECT mm.mcd_id, TRIM(mm.codigo1) as codigo1, mm.codigo2, TRIM(mm.nombre) as descripcion, m.nombre as marca, l.nombre as linea, sp.precio_compra, sp.precio_costo, sp.precio_venta, 
                        (COALESCE(sp.stock_minimo, 0)) as stock_minimo,
                        (COALESCE(sp.stock_inicial, 0)) as stock_inicial, 
                        (COALESCE(sp.stock_inicial, 0) + COALESCE(sp.stock, 0)) as stock
                        FROM maestros_mercaderias mm 
                        INNER JOIN stock_producto sp ON mm.mcd_id=sp.pro_id AND sp.age_id='{$prm['age_id']}' 
                        INNER JOIN marcas m ON mm.mar_id=m.mar_id
                        INNER JOIN lineas l ON mm.lin_id=l.lin_id) as stockp";
            
            $whr = "";
            switch(trim($prm['tip_doc'])){
                case 'N': $whr = " WHERE stockp.stock < 0"; break;
                case 'M': $whr = " WHERE stockp.stock <=  stockp.stock_minimo"; break;
                case 'S': $whr = " WHERE stockp.stock <=  '{$prm['stock_sel']}'"; break;
            }
            
            $ord = "";
            switch(trim($prm['ord_doc'])){
                case 'C': $ord = " ORDER BY stockp.codigo1 ASC"; break;
                case 'N': 
                default:    
                    $ord = " ORDER BY stockp.descripcion ASC"; break;
            }
            $sql = $sql.$whr.$ord;
            //echo $sql; exit;
            $rsData = $this->objDatos->sp_obtenerdatasql($sql);

            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0")	;
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=Reporte_Productos_Stock.xls");
            
            echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
            echo '  <tr style=" font-size:15px; font-weight:bold"><td colspan="9">LISTADO DE STOCK DE PRODUCTOS</td></tr>';
            echo '  <tr style=" font-size:15px; font-weight:bold"><td colspan="9">&nbsp;</td></tr>';
            echo '  <tr style=" font-size:14px; font-weight:bold">
                        <td>C&oacute;digo 1</td>
                        <td>C&oacute;digo 2</td>					
                        <td>Descripci&oacute;n</td>
                        <td>Marca</td>
                        <td>L&iacute;nea</td>
                        <td>Stock</td>
                        <td>Precio Compra</td>
                        <td>Precio Costo</td>
                        <td>Precio Venta</td>
                    </tr>';
            
            foreach ($rsData as $value){
                echo '<tr>';
                echo '	<td style="text-align: left;">'.$value['codigo1'].'</td>
                        <td style="text-align: left;">'.$value['codigo2'].'</td>
                        <td style="text-align: left;">'.utf8_decode($value['descripcion']).'</td>
                        <td style="text-align: left;">'.utf8_decode($value['marca']).'</td>
                        <td style="text-align: left;">'.utf8_decode($value['linea']).'</td>
                        <td style="text-align: right;">'.$value['stock'].'</td>
                        <td style="text-align: right;">'.$value['precio_compra'].'</td>
                        <td style="text-align: right;">'.$value['precio_costo'].'</td>
                        <td style="text-align: right;">'.$value['precio_venta'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

}