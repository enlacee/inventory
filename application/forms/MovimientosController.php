<?php
class MovimientosController extends Zend_Controller_Action
{
    private $objDatos;
    private $objDatos_index;
	private $objDatos_usuarios;
    private $auth;
	private $sessName;
    
    public function init()
    { 
		Zend_Session::start();
        $this->sessName = new Zend_Session_Namespace('inventory');		
		
		if($this->sessName->se_usr_id=='' or $this->sessName->se_usr_id == NULL){
			$data = array('success' => false, 'mensaje' => 'Se ha cerrado la sesion, inicie de nuevo la sesion');
			echo json_encode($data);
		}
				
		$_POST['se_usr_id']=$this->sessName->se_usr_id;
		$_POST['se_age_id']=$this->sessName->se_age_id;
        $this->objDatos=new Application_Model_Movimientos();
        $this->objDatos_index=new Application_Model_Index();
		//$this->objDatos_usuarios=new Application_Model_Usuarios();
        $this->auth=new Application_Model_Auth();
        $this->auth->validar_session();
        $this->auth->defines();
    }
	
	public function buscaventasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 5 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function buscacomprasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 5 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	//Compras >>>
	public function comprasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//echo "HOL;A";
		//exit();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_compras dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.mco_id = ".$_POST['mco_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function comprasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
         $rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_compras where age_id = '.$this->sessName->se_age_id);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function comprasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function comprasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and prv_id = ".$_POST['prv_id']." and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Compra con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		/*if($_POST['afecta_stock']=='S'){
			foreach ($_POST["v_detalle"] as $value){
				$dato = explode(".@.",$value);
				$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]);
				if(count($rs)>0){
					$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
					echo json_encode($data);
					exit();
				}
			}
		}*/
		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_compras_guardar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function comprasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and prv_id = ".$_POST['prv_id']." and age_id = ".$_POST['age_id']."  and movimientos_compras.mco_id <> ".$_POST['mco_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Compra con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		/*if($_POST['afecta_stock']=='S'){
			foreach ($_POST["v_detalle"] as $value){
				$dato = explode(".@.",$value);
				$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]);
				if(count($rs)>0){
					$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
					echo json_encode($data);
					exit();
				}
			}
		}*/
		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_compras_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function comprasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '100000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		$_POST['fec_ini'] = $_POST['txtpar1'];
		$_POST['fec_fin'] = $_POST['txtpar2'];
		$_POST['doc_id'] = $_POST['txtpar3'];
		$_POST['nro_doc'] = $_POST['txtpar4'];
		$_POST['limit'] = -1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$db_data = $this->objDatos->sp_compras_lista($_POST,2);
		//print_r($db_data);
		$pdf->ezTable($db_data, $col_names, 'Listado de Compras', $options,$_POST['txtfiltro']);
		$pdf->ezStream();
    }
	
	public function comprasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		$rsCount = $this->objDatos->sp_compras_lista($_POST,1);
		$rsData = $this->objDatos->sp_compras_lista($_POST,2);

		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function comprasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_compras_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function comprasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_compras_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function comprasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
    //<<
	
	//Altas >>>
    public function altasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_altas dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.mal_id = ".$_POST['mal_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function altasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_altas where age_id = '.$this->sessName->se_age_id);
        //$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function altasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function altasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_altas where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Alta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_altas_guardar($_POST);		
		//echo ($rs);
		//exit();
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function altasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_altas where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and movimientos_altas.mal_id <> ".$_POST['mal_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Alta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_altas_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function altasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;       
		
        $rsCount = $this->objDatos->sp_altas_lista($_POST,1);
		$rsData = $this->objDatos->sp_altas_lista($_POST,2);
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function altasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_altas_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$pdf->ezStream();
    }
	
	public function altasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_altas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function altasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_altas_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function altasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 13 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	//<<<
	
	//TrasladoIng >>>
    public function trasladoingDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_trasladoing dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.tin_id = ".$_POST['tin_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function trasladoingCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_trasladoing where age_id = '.$this->sessName->se_age_id);
		//$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function trasladoingDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladoingGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_trasladoing where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id =  ".$_POST['age_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Traslado con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_trasladoing_guardar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function trasladoingActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_trasladoing where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id =  ".$_POST['age_id']." and movimientos_trasladoing.tin_id <> ".$_POST['tin_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Alta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_trasladoing_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladoingListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;       
		
        $rsCount = $this->objDatos->sp_trasladoing_lista($_POST,1);
		$rsData = $this->objDatos->sp_trasladoing_lista($_POST,2);
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function trasladoingListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_trasladoing_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$pdf->ezStream();
    }
	
	public function trasladoingEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_trasladoing_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function trasladoingAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_trasladoing_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladoingAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 13 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	public function trasladoentAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 13 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	//<<<
    
    //NotaVentas >>
	public function ventasJalarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
				
		$cabecera = $this->objDatos->sp_obtenerdatasql("select mve.*, cli.ruc, cli.codigo, cli.nombre as nombre_cliente from movimientos_ventas mve inner join maestros_clientes cli on mve.cli_id = cli.cli_id where mve.eliminado = 0 and mve.doc_id = ".$_POST['tipo']." and mve.doc_n like '".str_replace('-','%',$_POST['doc_n'])."'");
		
		if(count($cabecera)==0){
			$data = array('success' => false, 'mensaje' => 'No existe la Venta '.chr(13).'Con Nro : '.$_POST['doc_n'].', por favor indique otro numero');
			echo json_encode($data);
			exit();
		}

		$id = $cabecera[0]['mve_id'];
		$rs = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as mar_nom from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$id);
		$varibale[1]=$rs;

        
        $data = array('success' => true, 'total' => count($varibale), 'cabecera' => $cabecera, 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notaventasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_notaventas dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.nve_id = ".$_POST['nve_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function notaventasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_notaventas where age_id = '.$this->sessName->se_age_id);
        //$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function notaventasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function notaventasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_notaventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Nota de Credito con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_notaventas_guardar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notaventasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_notaventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and movimientos_notaventas.nve_id <> ".$_POST['nve_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Nota de CRedito con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_notaventas_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function notaventasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;       
		
        $rsCount = $this->objDatos->sp_notaventas_lista($_POST,1);
		$rsData = $this->objDatos->sp_notaventas_lista($_POST,2);
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function notaventasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_notaventas_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$pdf->ezStream();
    }
	
	public function notaventasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_notaventas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notaventasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_notaventas_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function notaventasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 13 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	//<<
    
    public function trasladosEntradasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosEntradasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_compras_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosEntradasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_compras_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosEntradasAction()
    {
        
    }
	
	//Ventas
	public function ventasProformaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
				
		$cabecera = $this->objDatos->sp_obtenerdatasql("select mve.*, cli.ruc, cli.codigo from movimientos_ventas mve inner join maestros_clientes cli on mve.cli_id = cli.cli_id where mve.eliminado = 0 and mve.doc_id = 1 and mve.doc_n = '".$_POST['doc_n']."'");
		
		if(count($cabecera)==0){
			$data = array('success' => false, 'mensaje' => 'No existe la Proforma <br>Con Nro : '.$_POST['doc_n'].', por favor indique otro numero');
			echo json_encode($data);
			exit();
		}

		$id = $cabecera[0][0];
		$rs = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id where dve.mve_id = ".$id);
		$varibale[1]=$rs;

        
        $data = array('success' => true, 'total' => count($varibale), 'cabecera' => $cabecera, 'data' => $rs);
        echo json_encode($data);
    }
	
	public function ventasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//echo "HOL;A";
		//exit();
		$rs = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }
    
    public function ventasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_ventas where age_id = '.$this->sessName->se_age_id);
		//print_r($rs);
		//echo $this->sessName->se_age_id;
		//exit();
        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function ventasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function ventasGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		
		/*$data = array('success' => true, 'mensaje' => 'Preuba');
        echo json_encode($data);
		exit();*/
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_ventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]);
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_ventas_guardar($_POST);		
		//print_r($rs);
         $data = array('success' => true, 'mve_id' => $rs);
        echo json_encode($data);
    }
	
	public function ventasStockAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$_POST['pro_id']." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$_POST['cantidad']);
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$_POST['pro_des'].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
			}
		}
         $data = array('success' => true);
        echo json_encode($data);
    }
	
	public function ventasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
		if(count($rs)==0){
			$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_ventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and mve_id <> ".$_POST['mve_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]. " - (select SUM(Cantidad) from detalle_ventas where mve_id = ".$_POST['mve_id']." and pro_id = ".$dato[0].") ");
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}		
		}
		//print_r($_POST);
		$rs = $this->objDatos->sp_ventas_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function ventasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_ventas_lista($_POST,1);
		$rsData = $this->objDatos->sp_ventas_lista($_POST,2);  
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function ventasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo'],1);
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_cliente' => 'Cliente',
			'total_venta' => 'Total Venta',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_cliente' => array('justification'=>'left'),
				'total_venta' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['fec_ini'] = $_POST['txtpar1'];
		$_POST['fec_fin'] = $_POST['txtpar2'];
		$_POST['doc_id'] = $_POST['txtpar3'];
		$_POST['nro_doc'] = $_POST['txtpar4'];
		$_POST['limit'] = -1;
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		$db_data = $this->objDatos->sp_ventas_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ventas', $options, $_POST['txtfiltro']);		
		$pdf->ezText("CANTIDAD DE DOCUMENTOS : ".$pdf->con1);
		$pdf->ezText("CANTIDAD DE DOCUMENTOS EN SOLES : ".$pdf->con2);
		$pdf->ezText("CANTIDAD DE DOCUMENTOS EN DOLARES : ".$pdf->con3);
		$pdf->ezText("TOTAL VENTA EN SOLES : ".$pdf->sum2);
		$pdf->ezText("TOTAL VENTA EN DOLARES : ".$pdf->sum3);
		$pdf->ezStream();
    }
	
	public function ventasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_ventas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function ventasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_ventas_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	
    
    public function ventasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	public function formatoVentasAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new Zend_Pdf();
		$style = new Zend_Pdf_Style();
		$style->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0)); 
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES); 
		$style->setFont($font,12);
		
		$rsDataEmpresa = $this->objDatos->sp_obtenerdatasql("select * from tiendas where tie_id = ".$this->sessName->se_age_id);
		
		//Jalando Venta
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['campo'] = 'mve_id';
		$_POST['query'] = $_POST['txtpar2'];
		$_POST['mve_id'] = $_POST['txtpar2'];
		$_POST['tipo'] = $_POST['txtpar1'];
		$rsData = $this->objDatos->sp_ventas_lista($_POST,2);
		
		if($_POST['tipo'] == '1'){	
		
			if($rsData[0]['doc_id']==2){	
				$pdf->pages[] = ($page = $pdf->newPage('624:842:'));
				$page->setStyle($style);		
				
				$page->setFont($font, 12);
				$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
				
				$page->drawText($rsData[0]['doc_n'], 500 ,690);					
				$page->drawText($rsData[0]['nombre_cliente'], 70 ,356);			
				$page->drawText($rsData[0]['direccion'], 70 ,336);
				
				$page->drawText($rsData[0]['ruc'], 70 ,316);
				$page->drawText($rsData[0]['n_guia'], 300 ,316);
				$page->drawText(date('d        m         Y',strtotime($rsData[0]['fec_ori'])), 490 ,316);				
				
					
				$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);   
				$y=288;				
				foreach($rsDetalle as $solo){	
					$y = $y - 15;		
					$page->setFont($font, 11);			
					$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());			  
					$page->drawText(utf8_decode($solo['cantidad']), 60 - $lon_tot,$y)
						->drawText(utf8_decode($solo['codigo1']), 63,$y)			
						->drawText(utf8_decode($solo['producto']), 170,$y);												  
					$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());
					$page->drawText(utf8_decode($solo['precio_venta']), 470 - $lon_tot,$y);						  
					$lon_tot = $this->getTextWidth(utf8_decode($solo['valor_descuento']), $page->getFont(), $page->getFontSize());
					$page->drawText(utf8_decode($solo['precio_venta']), 530 - $lon_tot,$y);						  
					$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					
					$page->drawText(utf8_decode($solo['total']), 610 - $lon_tot,$y);					
				}
				$y=65;
				$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());
				$page->setFont($font, 12)
					->drawText($rsData[0]['valor_venta'] , 220 ,$y)
					->drawText($rsData[0]['impuesto_igv'] , 430 ,$y)
					->drawText($rsData[0]['total_venta'], 610 - $lon_tot ,$y)
					->drawText($this->num2letras($rsData[0]['total_venta']), 50 ,$y - 40);
					
				
			}
			if($rsData[0]['doc_id']==3){	
				$pdf->pages[] = ($page = $pdf->newPage('590:842:'));
				$page->setStyle($style);		
				
				$page->setFont($font, 12);
				$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
				
				$page->drawText($rsData[0]['doc_n'], 500 ,400);					
				$page->drawText($rsData[0]['nombre_cliente'], 70 ,350);			
				$page->drawText($rsData[0]['fec_ven'], 500 ,350);					
				$page->drawText($rsData[0]['direccion'], 70 ,330);
				$page->drawText($rsData[0]['n_guia'], 500 ,330);
					
				$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);   
				$y=280;				
				foreach($rsDetalle as $solo){	
					$y = $y - 25;					
					$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());			  
					$page->setFont($font, 11)
						->drawText(utf8_decode($solo['cantidad']), 80 - $lon_tot,$y)
						->drawText(utf8_decode($solo['producto']), 90,$y);						  
					$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());
					$page->drawText(utf8_decode($solo['precio_venta']), 550 - $lon_tot,$y);						  
					$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());
					$page->drawText(utf8_decode($solo['total']), 590 - $lon_tot,$y);					
				}
				$y=80;
				$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());
				$page->setFont($font, 12)
					->drawText(date('d',strtotime($rsData[0]['fec_ori'],1)) , 140 ,$y)
					->drawText(date('F',strtotime($rsData[0]['fec_ori'],1)) , 260 ,$y)
					->drawText(date('Y',strtotime($rsData[0]['fec_ori'],1)) , 400 ,$y)
					->drawText($rsData[0]['total_venta'], 590 - $lon_tot ,$y);
			}
			if(!($rsData[0]['doc_id']==2 or $rsData[0]['doc_id']==2)){
				$pdf->pages[] = ($page = $pdf->newPage('700:500'));
				$page->setStyle($style);	
				$page->drawText("Sin informacion", 5 ,5);
			}
		}else{
			$pdf->pages[] = ($page = $pdf->newPage('A4'));
			$page->setStyle($style);		
			
			$page->setFont($font, 12);
			$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
			
			
			$page->drawText($rsDataEmpresa[0]['nombre'].'-'.$rsDataEmpresa[0]['ruc'], 40 ,800);		
			$page->drawText($rsDataEmpresa[0]['direccion'], 40 ,780);		
			$page->drawText($rsDataEmpresa[0]['telefono'], 40 ,760);	
					
			$page->setFont($font, 12);
			$page->drawText($rsData[0]['doc_n'], 450 ,750);					
			$page->drawText($rsData[0]['nombre_cliente'], 40 ,700);			
			$page->drawText($rsData[0]['direccion'], 40 ,680);
			
			$page->drawText($rsData[0]['ruc'], 40 ,660);
			$page->drawText($rsData[0]['n_guia'], 300 ,660);
			$page->drawText(date('d    m    Y',strtotime($rsData[0]['fec_ven'])), 450 ,660);				
			
				
			$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);   
			$y=600;			
			
			$page->setFont($font, 12);
			$lon_tot = $this->getTextWidth("CANT.", $page->getFont(), $page->getFontSize());		
			$page->drawText("CANT.", 80 - $lon_tot,600);	
			$page->drawText("DESCRIPCION", 90,600);	
			$lon_tot = $this->getTextWidth("P. UNIT.", $page->getFont(), $page->getFontSize());		
			$page->drawText("P. UNIT.", 410 - $lon_tot,600);
			$lon_tot = $this->getTextWidth("DSCTO.", $page->getFont(), $page->getFontSize());		
			$page->drawText("DSCTO.", 480 - $lon_tot,600);
			$lon_tot = $this->getTextWidth("IMPORTE", $page->getFont(), $page->getFontSize());		
			$page->drawText("IMPORTE", 550 - $lon_tot,600);
			foreach($rsDetalle as $solo){	
				$y = $y - 25;					
				$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());			  
				$page->setFont($font, 11)
					->drawText(utf8_decode($solo['cantidad']), 80 - $lon_tot,$y)
					->drawText(utf8_decode($solo['producto']), 90,$y);												  
				$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());
				$page->drawText(utf8_decode($solo['precio_venta']), 410 - $lon_tot,$y);						  
				$lon_tot = $this->getTextWidth(utf8_decode($solo['valor_descuento']), $page->getFont(), $page->getFontSize());
				$page->drawText(utf8_decode($solo['precio_venta']), 480 - $lon_tot,$y);						  
				$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					
				$page->drawText(utf8_decode($solo['total']), 550 - $lon_tot,$y);					
			}
			$y=80;
			$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());
			$page->setFont($font, 12)
				->drawText($rsData[0]['valor_venta'] , 200 ,$y)
				->drawText($rsData[0]['impuesto_igv'] , 400 ,$y)
				->drawText($rsData[0]['total_venta'], 550 - $lon_tot ,$y)
				->drawText($this->num2letras($rsData[0]['total_venta']), 50 ,$y - 40);			
		}
		header("content-type: application/pdf");
		print($pdf->render());	
		
    }
	//<<<
    
    //Bajas	
	public function bajasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//echo "HOL;A";
		//exit();
		$rs = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_bajas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mba_id = ".$_POST['mba_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bajasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_bajas where age_id = '.$this->sessName->se_age_id);
        //$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function bajasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function bajasGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_bajas where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]);
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_bajas_guardar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bajasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
		if(count($rs)==0){
			$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_bajas where eliminado=0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and mba_id <> ".$_POST['mba_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]. " - (select SUM(Cantidad) from detalle_bajas where mba_id = ".$_POST['mba_id']." and pro_id = ".$dato[0].") ");
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}		
		}
		//print_r($_POST);
		$rs = $this->objDatos->sp_bajas_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function bajasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;	
		
        $rsCount = $this->objDatos->sp_bajas_lista($_POST,1);
		$rsData = $this->objDatos->sp_bajas_lista($_POST,2);  
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function bajasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_cliente' => 'Cliente',
			'total_venta' => 'Total Venta',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_cliente' => array('justification'=>'left'),
				'total_venta' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_bajas_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Salida Varias', $options);
		$pdf->ezStream();
    }
	
	public function bajasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_bajas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bajasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_bajas_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	
    
    public function bajasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	//<<<
    
    //NotaCompras	
	public function comprasJalarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
				
		$cabecera = $this->objDatos->sp_obtenerdatasql("select mco.*, prv.ruc, prv.codigo, prv.nombre as nombre_proveedor from movimientos_compras mco inner join maestros_proveedores prv on mco.prv_id = prv.prv_id where mco.eliminado = 0 and mco.doc_id = ".$_POST['tipo']." and mco.doc_n like '".str_replace('-','%',$_POST['doc_n'])."'");
		
		if(count($cabecera)==0){
			$data = array('success' => false, 'mensaje' => 'No existe la Proforma <br>Con Nro : '.$_POST['doc_n'].', por favor indique otro numero');
			echo json_encode($data);
			exit();
		}

		$id = $cabecera[0]['mco_id'];
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as mar_nom from detalle_compras dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.mco_id = ".$id);
		$varibale[1]=$rs;

        
        $data = array('success' => true, 'total' => count($varibale), 'cabecera' => $cabecera, 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notacomprasDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		/*echo "HOL;A".$_POST['nco_id']."<";
		print_r($_POST);
		exit();*/
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_notacompras dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.nco_id = ".$_POST['nco_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notacomprasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_notacompras where age_id = '.$this->sessName->se_age_id);
        //$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function notacomprasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function notacomprasGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_notacompras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]);
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].'<br>Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}
		}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_notacompras_guardar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notacomprasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
		if(count($rs)==0){
			$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
			echo json_encode($data);
			exit();
		}
		
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_notacompras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and nco_id <> ".$_POST['nco_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		if($this->sessName->se_valida_stock=='1'){
			if($_POST['afecta_stock']=='S'){
				foreach ($_POST["v_detalle"] as $value){
					$dato = explode(".@.",$value);
					$rs = $this->objDatos->sp_obtenerdatasql("select stock_inicial + stock as stock from stock_producto where pro_id = ".$dato[0]." and age_id = ".$_POST['age_id']." and stock_inicial + stock < ".$dato[1]. " - (select SUM(Cantidad) from detalle_notacompras where nco_id = ".$_POST['nco_id']." and pro_id = ".$dato[0].") ");
					if(count($rs)>0){
						$data = array('success' => false, 'mensaje' => 'Stock insuficiente del siguiente<br>Producto : '.$dato[3].chr(13).'Stock Actual : '.$rs[0][0].', por favor modifique los datos');
						echo json_encode($data);
						exit();
					}
				}
			}		
		}
		
		$rs = $this->objDatos->sp_notacompras_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function notacomprasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;	
		
        $rsCount = $this->objDatos->sp_notacompras_lista($_POST,1);
		$rsData = $this->objDatos->sp_notacompras_lista($_POST,2);  
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function notacomprasListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_cliente' => 'Cliente',
			'total_venta' => 'Total Venta',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_cliente' => array('justification'=>'left'),
				'total_venta' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_notacompras_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Salida Varias', $options);
		$pdf->ezStream();
    }
	
	public function notacomprasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_notacompras_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function notacomprasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_notacompras_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	
    
    public function notacomprasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	//<<<
	
	//canje
    public function canjeGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_canje_guardar($_POST);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
    public function canjeListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;	
		
        $rsCount = $this->objDatos->sp_canje_lista($_POST,1);
		$rsData = $this->objDatos->sp_canje_lista($_POST,2);  
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function canjeEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_canje_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function canjeAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 27 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function compromisoventasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 27 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function compromisocomprasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 27 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	//<<<
	
	//caja >>>
    public function cajaDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_caja dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.mca_id = ".$_POST['mca_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function cajaCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql("select case when max(nro) is null then 1 else max(nro) + 1 end as codigo, (select codigo from concepto_pago where coc_id = ".$_POST['cpa_id'].") as mov from movimientos_caja where tie_id = ".$this->sessName->se_age_id." and cpa_id = ".$_POST['cpa_id']." and year(fec_reg) = ".$_POST['anio']." and month(fec_reg) = ".$_POST['mes']);
		
        $data = $rs[0]; 
		
        echo json_encode($data);
    }
    
    public function cajaDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function cajaGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_caja_guardar($_POST);		
		//echo ($rs);
		//exit();
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function cajaActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//if($_POST['mon_id']==2){
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_caja where doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and movimientos_caja.mca_id <> ".$_POST['mca_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Alta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_caja_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function cajaListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;       
		
        $rsCount = $this->objDatos->sp_caja_lista($_POST,1);
		$rsData = $this->objDatos->sp_caja_lista($_POST,2);
        
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function cajaListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_caja_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$pdf->ezStream();
    }
	
	public function cajaEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_caja_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function cajaAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_caja_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function cajaAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 54 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	//<<<
    
    public function trasladosSalidasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_trasladoing where age_id = '.$this->sessName->se_age_id);
        //$rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function trasladosSalidasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosSalidasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_ventas_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosSalidasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_ventas_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function trasladosSalidasAction()
    {
        
    }
	
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
	
	public function num2letras($num, $fem = false, $dec = true, $mon=1) { 
	   $matuni[2]  = "dos"; 
	   $matuni[3]  = "tres"; 
	   $matuni[4]  = "cuatro"; 
	   $matuni[5]  = "cinco"; 
	   $matuni[6]  = "seis"; 
	   $matuni[7]  = "siete"; 
	   $matuni[8]  = "ocho"; 
	   $matuni[9]  = "nueve"; 
	   $matuni[10] = "diez"; 
	   $matuni[11] = "once"; 
	   $matuni[12] = "doce"; 
	   $matuni[13] = "trece"; 
	   $matuni[14] = "catorce"; 
	   $matuni[15] = "quince"; 
	   $matuni[16] = "dieciseis"; 
	   $matuni[17] = "diecisiete"; 
	   $matuni[18] = "dieciocho"; 
	   $matuni[19] = "diecinueve"; 
	   $matuni[20] = "veinte"; 
	   $matunisub[2] = "dos"; 
	   $matunisub[3] = "tres"; 
	   $matunisub[4] = "cuatro"; 
	   $matunisub[5] = "quin"; 
	   $matunisub[6] = "seis"; 
	   $matunisub[7] = "sete"; 
	   $matunisub[8] = "ocho"; 
	   $matunisub[9] = "nove"; 
	
	   $matdec[2] = "veint"; 
	   $matdec[3] = "treinta"; 
	   $matdec[4] = "cuarenta"; 
	   $matdec[5] = "cincuenta"; 
	   $matdec[6] = "sesenta"; 
	   $matdec[7] = "setenta"; 
	   $matdec[8] = "ochenta"; 
	   $matdec[9] = "noventa"; 
	   $matsub[3]  = 'mill'; 
	   $matsub[5]  = 'bill'; 
	   $matsub[7]  = 'mill'; 
	   $matsub[9]  = 'trill'; 
	   $matsub[11] = 'mill'; 
	   $matsub[13] = 'bill'; 
	   $matsub[15] = 'mill'; 
	   $matmil[4]  = 'millones'; 
	   $matmil[6]  = 'billones'; 
	   $matmil[7]  = 'de billones'; 
	   $matmil[8]  = 'millones de billones'; 
	   $matmil[10] = 'trillones'; 
	   $matmil[11] = 'de trillones'; 
	   $matmil[12] = 'millones de trillones'; 
	   $matmil[13] = 'de trillones'; 
	   $matmil[14] = 'billones de trillones'; 
	   $matmil[15] = 'de billones de trillones'; 
	   $matmil[16] = 'millones de billones de trillones'; 
	   
	   //Zi hack
	   $float=explode('.',$num);
	   $num=$float[0];
	
	   $num = trim((string)@$num); 
	   if ($num[0] == '-') { 
		  $neg = 'menos '; 
		  $num = substr($num, 1); 
	   }else 
		  $neg = ''; 
	   while ($num[0] == '0') $num = substr($num, 1); 
	   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
	   $zeros = true; 
	   $punt = false; 
	   $ent = ''; 
	   $fra = ''; 
	   for ($c = 0; $c < strlen($num); $c++) { 
		  $n = $num[$c]; 
		  if (! (strpos(".,'''", $n) === false)) { 
			 if ($punt) break; 
			 else{ 
				$punt = true; 
				continue; 
			 } 
	
		  }elseif (! (strpos('0123456789', $n) === false)) { 
			 if ($punt) { 
				if ($n != '0') $zeros = false; 
				$fra .= $n; 
			 }else 
	
				$ent .= $n; 
		  }else 
	
			 break; 
	
	   } 
	   $ent = '     ' . $ent; 
	   if ($dec and $fra and ! $zeros) { 
		  $fin = ' coma'; 
		  for ($n = 0; $n < strlen($fra); $n++) { 
			 if (($s = $fra[$n]) == '0') 
				$fin .= ' cero'; 
			 elseif ($s == '1') 
				$fin .= $fem ? ' una' : ' un'; 
			 else 
				$fin .= ' ' . $matuni[$s]; 
		  } 
	   }else 
		  $fin = ''; 
	   if ((int)$ent === 0) return 'Cero ' . $fin; 
	   $tex = ''; 
	   $sub = 0; 
	   $mils = 0; 
	   $neutro = false; 
	   while ( ($num = substr($ent, -3)) != '   ') { 
		  $ent = substr($ent, 0, -3); 
		  if (++$sub < 3 and $fem) { 
			 $matuni[1] = 'una'; 
			 $subcent = 'as'; 
		  }else{ 
			 $matuni[1] = $neutro ? 'un' : 'uno'; 
			 $subcent = 'os'; 
		  } 
		  $t = ''; 
		  $n2 = substr($num, 1); 
		  if ($n2 == '00') { 
		  }elseif ($n2 < 21) 
			 $t = ' ' . $matuni[(int)$n2]; 
		  elseif ($n2 < 30) { 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  }else{ 
			 $n3 = $num[2]; 
			 if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
			 $n2 = $num[1]; 
			 $t = ' ' . $matdec[$n2] . $t; 
		  } 
		  $n = $num[0]; 
		  if ($n == 1) { 
			 $t = ' ciento' . $t; 
		  }elseif ($n == 5){ 
			 $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
		  }elseif ($n != 0){ 
			 $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
		  } 
		  if ($sub == 1) { 
		  }elseif (! isset($matsub[$sub])) { 
			 if ($num == 1) { 
				$t = ' mil'; 
			 }elseif ($num > 1){ 
				$t .= ' mil'; 
			 } 
		  }elseif ($num == 1) { 
			 $t .= ' ' . $matsub[$sub] . '?n'; 
		  }elseif ($num > 1){ 
			 $t .= ' ' . $matsub[$sub] . 'ones'; 
		  }   
		  if ($num == '000') $mils ++; 
		  elseif ($mils != 0) { 
			 if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
			 $mils = 0; 
		  } 
		  $neutro = true; 
		  $tex = $t . $tex; 
	   } 
           
	   $tex = $neg . substr($tex, 1) . $fin; 
           
           if((strtolower($tex) == 'ciento') && ($float[1] == '00'))
               $tex = 'cien';
           
	   //Zi hack --> return ucfirst($tex);
	   $end_num=ucfirst($tex).' con '.$float[1].'/100'.($mon==1?' NUEVOS SOLES':' DOLARES AMERICANOS');
	   return strtoupper($end_num); 
	}
	
	public function saldoinicialAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 59 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function saldoinicialGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//print_r($_POST);
		
		$rs = $this->objDatos->sp_saldoinicialventas_guardar($_POST);		
		
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function saldoinicialcomprasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 60 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function saldoinicialcomprasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//print_r($_POST);
		
		$rs = $this->objDatos->sp_saldoinicialcompras_guardar($_POST);		
		
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	//prerecibo >>>
    public function prereciboDetalleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$rs = $this->objDatos->sp_obtenerdatasql("select dco.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_prerecibo dco inner join maestros_mercaderias mcd on dco.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dco.mpr_id = ".$_POST['mpr_id']);        
        $data = array('success' => true, 'data' => $rs);
        echo json_encode($data);
    }	
    
	public function prereciboCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_prerecibo where tie_id = '.$this->sessName->se_age_id);
		//print_r($rs);
		//echo $this->sessName->se_age_id;
		//exit();
        $data = $rs[0]; 
        echo json_encode($data);
    }
	
	public function prereciboCuentaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql('select cuenta from concepto_contable where cco_id = '.$_POST['cco_id']);
		//print_r($rs);
		//echo $this->sessName->se_age_id;
		//exit();
        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function prereciboDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function prereciboGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_prerecibo_guardar($_POST);		
		//echo ($rs);
		//exit();
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function prereciboActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;	
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_prerecibo_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function prereciboListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;       
		
        $rsCount = $this->objDatos->sp_prerecibo_lista($_POST,1);
		//print_r($rsCount);
		$rsData = $this->objDatos->sp_prerecibo_lista($_POST,2);
        //print_r($data);
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function prereciboListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf();
		
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Documento',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'total_compra' => 'Total Compra',
			'moneda' => 'Moneda',
			'anulado' => 'X',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'total_compra' => array('justification'=>'right'),
				'moneda' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_prerecibo_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$pdf->ezStream();
    }
	
	public function prereciboEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_prerecibo_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function prereciboAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_prerecibo_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function prereciboAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 60 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
		$this->view->sesion = $this->sessName;
    }
	
	//<<<
	
	//Deudas
	public function deudasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_deudas_lista($_POST,1);
		$rsData = $this->objDatos->sp_deudas_lista($_POST,2);  
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function buscadeudasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 5 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	//
	
	//Rpt Compras
	public function rptcomprasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function comprasExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Compras_".$_POST['txtfile'].".xls");
		
		$db_data = $this->objDatos->sp_compras_rpt($_POST);

		/*echo '<table id="Exportar_a_Excel">';
		echo '	<tr>
					<td>FECHA</td>
					<td>DOC</td>
					<td>NUMERO</td>
					<td>PROVEEDOR</td>
					<td>TOTAL SOLES</td>
					<td>TOTAL DOLARES</td>
				</tr>';
		foreach ($db_data as $value){
			echo '<tr>';
			echo '	<td>'.$value['fec_ven'].'</td>
					<td>'.$value['descripcion_documento'].'</td>
					<td>'.$value['doc_n'].'</td>
					<td>'.$value['nombre_proveedor'].'</td>
					<td>'.($value['mon_id']==1?$value['total_compra']:'0').'</td>
					<td>'.($value['mon_id']==1?'0':$value['total_compra']).'</td>';
			echo '</tr>';
			
		}
		echo '</table>';
		*/
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		
		switch (date('m',strtotime($_POST['txtpar2']))) {
			case '01':
				$nombre_mes="ENERO";
				break;
			case '02':
				$nombre_mes="FEBRERO";
				break;
			case '03':
				$nombre_mes="MARZO";
				break;
			case '04':
				$nombre_mes="ABRIL";
				break;
			case '05':
				$nombre_mes="MAYO";
				break;
			case '06':
				$nombre_mes="JUNIO";
				break;
			case '07':
				$nombre_mes="JULIO";
				break;
			case '08':
				$nombre_mes="AGOSTO";
				break;
			case '09':
				$nombre_mes="SEPTIEMBRE";
				break;
			case '10':
				$nombre_mes="OCTUBRE";
				break;
			case '11':
				$nombre_mes="NOVIEMBRE";
				break;
			case '12':
				$nombre_mes="DICIEMBRE";
				break;
		}
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">REGISTRO DE COMPRAS CORRESPONDIENTE AL MES DE '.$nombre_mes.' '.date('Y',strtotime($_POST['txtpar2'])).'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>DOC</td>
					<td>NUMERO</td>
					<td>FECHA</td>
					
					<td>NOMBRE O RAZON SOCIAL</td>
					<td colspan="3">COMPRA EN DOLARES</td>
					<td></td>
					<td colspan="3">COMPRA EN SOLES</td>
				</tr>
				<tr style=" font-size:13px; font-weight:bold">
					<td></td>
					<td></td>
					<td></td>
					
					<td></td>
					<td>V. COMPRA</td>
					<td>IGV</td>
					<td>T. COMPRA</td>
					<td>T. CAMBIO</td>
					<td>V. COMPRA</td>
					<td>IGV</td>
					<td>T. COMPRA</td>
				</tr>';
		$doc_sw = '';
		$s_gs_1 = 0;
		$s_gs_2 = 0;
		$s_gs_3 = 0;
		$s_gd_1 = 0;
		$s_gd_2 = 0;
		$s_gd_3 = 0;
		$s_ts_1 = 0;
		$s_ts_2 = 0;
		$s_ts_3 = 0;
		$s_td_1 = 0;
		$s_td_2 = 0;
		$s_td_3 = 0;
		foreach ($db_data as $value){
			
			if($doc_sw <> $value['descripcion_documento']){
				if($doc_sw<>''){
					echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL '.$doc_sw.'</td>
						<td align="right">'.abs($s_gd_1).'</td>
						<td align="right">'.abs($s_gd_2).'</td>
						<td align="right">'.abs($s_gd_3).'</td>
						<td>-</td>
						<td align="right">'.abs($s_gs_1).'</td>
						<td align="right">'.abs($s_gs_2).'</td>
						<td align="right">'.abs($s_gs_3).'</td>
					</tr>';
				}
				$s_ts_1 = $s_ts_1 + $s_gs_1;
				$s_ts_2 = $s_ts_2 + $s_gs_2;
				$s_ts_3 = $s_ts_3 + $s_gs_3;
				$s_td_1 = $s_td_1 + $s_gd_1;
				$s_td_2 = $s_td_2 + $s_gd_2;
				$s_td_3 = $s_td_3 + $s_gd_3;
				
				$s_gs_1 = 0;
				$s_gs_2 = 0;
				$s_gs_3 = 0;
				$s_gd_1 = 0;
				$s_gd_2 = 0;
				$s_gd_3 = 0;
				echo '<tr style=" font-size:13px; font-weight:bold">
					<td colspan="11">'.$value['descripcion_documento'].'</td>
				</tr>';
				$doc_sw = $value['descripcion_documento'];
			}
			echo '<tr>';
			echo '	<td>'.substr($value['descripcion_documento'],0,1).'</td>
					<td>'.$value['doc_n'].'</td>
					<td>'.$value['fec_ven'].'</td>';
					if($value['anulado']=='1'){
						echo '<td>***********  A  N U L A D A **************</td>';
					}else{
						echo '<td>'.$value['nombre_proveedor'].'</td>';
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_1 = $s_gd_1 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format(($value['valor_compra']/$value['tca_compra']),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['valor_compra']=-1*$value['valor_compra'];
							}
							$s_gd_1 = $s_gd_1 + floatval(number_format(($value['valor_compra']/$value['tca_compra']),2,'.',''));*/
						}else{
							if(empty($value['valor_compra'])){
								$value['valor_compra'] = $value['total_compra'] - $value['impuesto_igv'];
							}
							echo '<td align="right">'.number_format($value['valor_compra'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['valor_compra']=-1*$value['valor_compra'];
							}
							$s_gd_1 = $s_gd_1 + floatval(number_format($value['valor_compra'],2,'.',''));
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_2 = $s_gd_2 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format($value['impuesto_igv']/$value['tca_compra'],2,'.','').'</td>';
							$s_gd_2 = $s_gd_2 + floatval(number_format($value['impuesto_igv']/$value['tca_compra'],2,'.',''));
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}*/
						}else{
							echo '<td align="right">'.number_format($value['impuesto_igv'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}
							$s_gd_2 = $s_gd_2 + floatval(number_format($value['impuesto_igv'],2,'.',''));
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_3 = $s_gd_3 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format(($value['total_compra']/$value['tca_compra']),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_compra']=-1*$value['total_compra'];
							}
							$s_gd_3 = $s_gd_3 + floatval(number_format(($value['total_compra']/$value['tca_compra']),2,'.',''));*/
						}else{
							echo '<td align="right">'.number_format($value['total_compra'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_compra']=-1*$value['total_compra'];
							}
							$s_gd_3 = $s_gd_3 + floatval(number_format($value['total_compra'],2,'.',''));
						}
						
					}
					
					echo '<td align="right">'.number_format($value['tca_compra'],3).'</td>';
					
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_1 = $s_gs_1 + 0;
					}else{
						if(empty($value['valor_compra'])){
							$value['valor_compra'] = $value['total_compra'] - $value['impuesto_igv'];
						}
						if($value['mon_id']==1){
							echo '<td align="right">='.number_format($value['valor_compra'],2,'.','').' + 0</td>';
							if($value['doc_id']==5){
								$value['valor_compra']=-1*$value['valor_compra'];
							}
							$s_gs_1 = $s_gs_1 + floatval(number_format($value['valor_compra'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format(round(($value['total_compra']/((100 + $value['igv'])/100))*$value['tca_compra'],2),2,'.','').'</td>';
							if($value['doc_id']==5){
								$s_gs_1 = $s_gs_1 - floatval(number_format(round(($value['total_compra']/((100 + $value['igv'])/100))*$value['tca_compra'],2),2,'.',''));
							}else{
								$s_gs_1 = $s_gs_1 + floatval(number_format(round(($value['total_compra']/((100 + $value['igv'])/100))*$value['tca_compra'],2),2,'.',''));
							}
							$valor_compra = floatval(number_format(round(($value['total_compra']/((100 + $value['igv'])/100))*$value['tca_compra'],2),2,'.',''));
							
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_2 = $s_gs_2 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">'.number_format($value['impuesto_igv'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}
							$s_gs_2 = $s_gs_2 + floatval(number_format($value['impuesto_igv'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format((round($value['total_compra']*$value['tca_compra'],2) - $valor_compra),2,'.','').'</td>';
							if($value['doc_id']==5){
								$s_gs_2 = $s_gs_2 - floatval(number_format((round($value['total_compra']*$value['tca_compra'],2) - $valor_compra),2,'.',''));
							}else{
								$s_gs_2 = $s_gs_2 + floatval(number_format((round($value['total_compra']*$value['tca_compra'],2) - $valor_compra),2,'.',''));	
							}
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_3 = $s_gs_3 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">='.number_format($value['total_compra'],2,'.','').' + 0</td>';
							if($value['doc_id']==5){
								$value['total_compra']=-1*$value['total_compra'];
							}
							$s_gs_3 = $s_gs_3 + floatval(number_format($value['total_compra'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format(round($value['total_compra'] * $value['tca_compra'],2),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_compra']=-1*$value['total_compra'];
							}
							$s_gs_3 = $s_gs_3 + floatval(number_format(round($value['total_compra'] * $value['tca_compra'],2),2,'.',''));
						}
						
					}
					
			echo '</tr>';
			
		}
		echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL '.$doc_sw.'</td>
						<td align="right">'.abs($s_gd_1).'</td>
						<td align="right">'.abs($s_gd_2).'</td>
						<td align="right">'.abs($s_gd_3).'</td>
						<td>-</td>
						<td align="right">'.abs($s_gs_1).'</td>
						<td align="right">'.abs($s_gs_2).'</td>
						<td align="right">'.abs($s_gs_3).'</td>
					</tr>';
				$s_ts_1 = $s_ts_1 + $s_gs_1;
				$s_ts_2 = $s_ts_2 + $s_gs_2;
				$s_ts_3 = $s_ts_3 + $s_gs_3;
				$s_td_1 = $s_td_1 + $s_gd_1;
				$s_td_2 = $s_td_2 + $s_gd_2;
				$s_td_3 = $s_td_3 + $s_gd_3;
		echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL GENERAL</td>
						<td align="right">'.abs($s_td_1).'</td>
						<td align="right">'.abs($s_td_2).'</td>
						<td align="right">'.abs($s_td_3).'</td>
						<td align="right">-</td>
						<td align="right">'.abs($s_ts_1).'</td>
						<td align="right">'.abs($s_ts_2).'</td>
						<td align="right">'.abs($s_ts_3).'</td>
					</tr>';
		echo '</table>';
		
		
    }
	
	//Rpt Compras
	public function rptventasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function ventasExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Ventas_".$_POST['txtfile'].".xls");
		
		$db_data = $this->objDatos->sp_ventas_rpt($_POST);

		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		
		switch (date('m',strtotime($_POST['txtpar2']))) {
			case '01':
				$nombre_mes="ENERO";
				break;
			case '02':
				$nombre_mes="FEBRERO";
				break;
			case '03':
				$nombre_mes="MARZO";
				break;
			case '04':
				$nombre_mes="ABRIL";
				break;
			case '05':
				$nombre_mes="MAYO";
				break;
			case '06':
				$nombre_mes="JUNIO";
				break;
			case '07':
				$nombre_mes="JULIO";
				break;
			case '08':
				$nombre_mes="AGOSTO";
				break;
			case '09':
				$nombre_mes="SEPTIEMBRE";
				break;
			case '10':
				$nombre_mes="OCTUBRE";
				break;
			case '11':
				$nombre_mes="NOVIEMBRE";
				break;
			case '12':
				$nombre_mes="DICIEMBRE";
				break;
		}
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">REGISTRO DE VENTAS CORRESPONDIENTE AL MES DE '.$nombre_mes.' '.date('Y',strtotime($_POST['txtpar2'])).'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>DOC</td>
					<td>NUMERO</td>
					<td>FECHA</td>
					
					<td>NOMBRE O RAZON SOCIAL</td>
					<td colspan="3">VENTA EN DOLARES</td>
					<td></td>
					<td colspan="3">VENTA EN SOLES</td>
				</tr>
				<tr style=" font-size:13px; font-weight:bold">
					<td></td>
					<td></td>
					<td></td>
					
					<td></td>
					<td>V. VENTA</td>
					<td>IGV</td>
					<td>T. VENTA</td>
					<td>T. CAMBIO</td>
					<td>V. VENTA</td>
					<td>IGV</td>
					<td>T. VENTA</td>
				</tr>';
		$doc_sw = '';
		$s_gs_1 = 0;
		$s_gs_2 = 0;
		$s_gs_3 = 0;
		$s_gd_1 = 0;
		$s_gd_2 = 0;
		$s_gd_3 = 0;
		$s_ts_1 = 0;
		$s_ts_2 = 0;
		$s_ts_3 = 0;
		$s_td_1 = 0;
		$s_td_2 = 0;
		$s_td_3 = 0;
		foreach ($db_data as $value){
			/*if($value['doc_id']==5){
				$value['valor_venta']=-1*$value['valor_venta'];
				$value['impuesto_igv']=-1*$value['impuesto_igv'];
				$value['total_venta']=-1*$value['total_venta'];
			}*/
			if($doc_sw <> $value['descripcion_documento']){
				if($doc_sw<>''){
					echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL '.$doc_sw.'</td>
						<td align="right">'.abs($s_gd_1).'</td>
						<td align="right">'.abs($s_gd_2).'</td>
						<td align="right">'.abs($s_gd_3).'</td>
						<td>-</td>
						<td align="right">'.abs($s_gs_1).'</td>
						<td align="right">'.abs($s_gs_2).'</td>
						<td align="right">'.abs($s_gs_3).'</td>
					</tr>';
				}
				$s_ts_1 = $s_ts_1 + $s_gs_1;
				$s_ts_2 = $s_ts_2 + $s_gs_2;
				$s_ts_3 = $s_ts_3 + $s_gs_3;
				$s_td_1 = $s_td_1 + $s_gd_1;
				$s_td_2 = $s_td_2 + $s_gd_2;
				$s_td_3 = $s_td_3 + $s_gd_3;
				
				$s_gs_1 = 0;
				$s_gs_2 = 0;
				$s_gs_3 = 0;
				$s_gd_1 = 0;
				$s_gd_2 = 0;
				$s_gd_3 = 0;
				echo '<tr style=" font-size:13px; font-weight:bold">
					<td colspan="11">'.$value['descripcion_documento'].'</td>
				</tr>';
				$doc_sw = $value['descripcion_documento'];
			}
			echo '<tr>';
			echo '	<td>'.substr($value['descripcion_documento'],0,1).'</td>
					<td>'.$value['doc_n'].'</td>
					<td>'.$value['fec_ven'].'</td>';
					if($value['anulado']=='1'){
						echo '<td>***********  A  N U L A D A **************</td>';
					}else{
						echo '<td>'.$value['nombre_cliente'].'</td>';
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_1 = $s_gd_1 + 0;
					}else{
						if(empty($value['valor_venta'])){
							$value['valor_venta'] = $value['total_venta'] - $value['impuesto_igv'];
						}
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format(($value['valor_venta']/$value['tca_venta']),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['valor_venta']=-1*$value['valor_venta'];
							}
							$s_gd_1 = $s_gd_1 + floatval(number_format(($value['valor_venta']/$value['tca_venta']),2,'.',''));*/
						}else{
							echo '<td align="right">'.number_format($value['valor_venta'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['valor_venta']=-1*$value['valor_venta'];
							}
							$s_gd_1 = $s_gd_1 + floatval(number_format($value['valor_venta'],2,'.',''));
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_2 = $s_gd_2 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format($value['impuesto_igv']/$value['tca_venta'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}
							$s_gd_2 = $s_gd_2 + floatval(number_format($value['impuesto_igv']/$value['tca_venta'],2,'.',''));*/
						}else{
							echo '<td align="right">'.number_format($value['impuesto_igv'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}
							$s_gd_2 = $s_gd_2 + floatval(number_format($value['impuesto_igv'],2,'.',''));
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gd_3 = $s_gd_3 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">0.00</td>';
							/*echo '<td align="right">'.number_format(($value['total_venta']/$value['tca_venta']),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_venta']=-1*$value['total_venta'];
							}
							$s_gd_3 = $s_gd_3 + floatval(number_format(($value['total_venta']/$value['tca_venta']),2,'.',''));*/
						}else{
							echo '<td align="right">'.number_format($value['total_venta'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_venta']=-1*$value['total_venta'];
							}
							$s_gd_3 = $s_gd_3 + floatval(number_format($value['total_venta'],2,'.',''));
						}
						
					}
					
					echo '<td align="right">'.number_format($value['tca_venta'],3).'</td>';
					
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_1 = $s_gs_1 + 0;
					}else{
						if(empty($value['valor_venta'])){
							$value['valor_venta'] = $value['total_venta'] - $value['impuesto_igv'];
						}
						if($value['mon_id']==1){
							echo '<td align="right">'.number_format($value['valor_venta'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['valor_venta']=-1*$value['valor_venta'];
							}
							$s_gs_1 = $s_gs_1 + floatval(number_format($value['valor_venta'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format(round((($value['total_venta'] * $value['tca_venta'])/((100 + $value['igv'])/100)),2),2,'.','').'</td>';
							if($value['doc_id']==5){
								$s_gs_1 = $s_gs_1 - floatval(number_format(round((($value['total_venta'] * $value['tca_venta'])/((100 + $value['igv'])/100)),2),2,'.',''));
							}else{
								$s_gs_1 = $s_gs_1 + floatval(number_format(round((($value['total_venta'] * $value['tca_venta'])/((100 + $value['igv'])/100)),2),2,'.',''));
							}
							$valor_venta = round((($value['total_venta'] * $value['tca_venta'])/((100 + $value['igv'])/100)),2);
							
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_2 = $s_gs_2 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">'.number_format($value['impuesto_igv'],2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['impuesto_igv']=-1*$value['impuesto_igv'];
							}
							$s_gs_2 = $s_gs_2 + floatval(number_format($value['impuesto_igv'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format(round($value['total_venta']*$value['tca_venta'],2) - $valor_venta,2,'.','').'</td>';
							if($value['doc_id']==5){
								$s_gs_2 = $s_gs_2 - floatval(number_format(round($value['total_venta']*$value['tca_venta'],2) - $valor_venta,2,'.',''));
							}else{
								$s_gs_2 = $s_gs_2 + floatval(number_format(round($value['total_venta']*$value['tca_venta'],2) - $valor_venta,2,'.',''));
							}
						}
						
					}
					if($value['anulado']=='1'){
						echo '<td align="right">0.00</td>';
						$s_gs_3 = $s_gs_3 + 0;
					}else{
						if($value['mon_id']==1){
							echo '<td align="right">='.number_format($value['total_venta'],2,'.','').' + 0</td>';
							if($value['doc_id']==5){
								$value['total_venta']=-1*$value['total_venta'];
							}
							$s_gs_3 = $s_gs_3 + floatval(number_format($value['total_venta'],2,'.',''));
						}else{
							echo '<td align="right">'.number_format(round($value['total_venta'] * $value['tca_venta'],2),2,'.','').'</td>';
							if($value['doc_id']==5){
								$value['total_venta']=-1*$value['total_venta'];
							}
							$s_gs_3 = $s_gs_3 + floatval(number_format(round($value['total_venta'] * $value['tca_venta'],2),2,'.',''));
						}
						
					}
					
			echo '</tr>';
			
		}
		echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL '.$doc_sw.'</td>
						<td align="right">'.$s_gd_1.'</td>
						<td align="right">'.$s_gd_2.'</td>
						<td align="right">'.$s_gd_3.'</td>
						<td>-</td>
						<td align="right">'.$s_gs_1.'</td>
						<td align="right">'.$s_gs_2.'</td>
						<td align="right">'.$s_gs_3.'</td>
					</tr>';
				$s_ts_1 = $s_ts_1 + $s_gs_1;
				$s_ts_2 = $s_ts_2 + $s_gs_2;
				$s_ts_3 = $s_ts_3 + $s_gs_3;
				$s_td_1 = $s_td_1 + $s_gd_1;
				$s_td_2 = $s_td_2 + $s_gd_2;
				$s_td_3 = $s_td_3 + $s_gd_3;
		echo '<tr style=" font-size:13px; font-weight:bold">
						<td></td>
						<td></td>
						<td></td>					
						<td>TOTAL GENERAL</td>
						<td align="right">'.$s_td_1.'</td>
						<td align="right">'.$s_td_2.'</td>
						<td>'.$s_td_3.'</td>
						<td align="right">-</td>
						<td align="right">'.$s_ts_1.'</td>
						<td align="right">'.$s_ts_2.'</td>
						<td align="right">'.$s_ts_3.'</td>
					</tr>';
		echo '</table>';
		
    }
	
	
	
	
	//Saldo Ventas
	public function saldoventasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function saldoventasGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		
		/*$data = array('success' => true, 'mensaje' => 'Preuba');
        echo json_encode($data);
		exit();*/
		//if($_POST['mon_id']==2){
			/*$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}*/
		//}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_saldoventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_saldoventas_guardar($_POST);		
		//print_r($rs);
         $data = array('success' => true, 'sve_id' => $rs);
        echo json_encode($data);
    }
	
	public function saldoventasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_venta>0");
		if(count($rs)==0){
			$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
			echo json_encode($data);
			exit();
		}*/
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_saldoventas where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and age_id = ".$_POST['age_id']." and sve_id <> ".$_POST['sve_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Venta con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}	
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_saldoventas_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function saldoventasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_saldoventas_lista($_POST,1);
		$rsData = $this->objDatos->sp_saldoventas_lista($_POST,2);  
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function saldoventasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_saldoventas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function saldoventasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_saldoventas_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function saldoventasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function saldoventasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_saldoventas where age_id = '.$this->sessName->se_age_id);
		//print_r($rs);
		//echo $this->sessName->se_age_id;
		//exit();
        $data = $rs[0]; 
        echo json_encode($data);
    }
	
	//<<<
	
	//Jalar Prerecibo
	public function prereciboJalarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
				
		$cabecera = $this->objDatos->sp_obtenerdatasql("select mpr.mpr_id , mpr.tie_id , mpr.codigo , mpr.fecha , mpr.fecha_reg , mpr.tipo_pagador , mpr.per_id , case when mpr.per_id is null then mpr.per_doc else per.ruc end as per_doc , case when mpr.per_id is null then mpr.per_nom else per.persona end as per_nom , mpr.estado, 1 as total, concat(LPAD(mpr.tie_id,3,'0'), '-',LPAD(mpr.codigo,6,'0')) as nro_recibo
		from movimientos_prerecibo mpr 
		left join (
			select cast('1' as char(1)) as tipo, maestros_clientes.cli_id as per_id, maestros_clientes.nombre as persona, maestros_clientes.ruc from maestros_clientes
			UNION ALL
			select cast('2' as char(1)) as tipo, maestros_proveedores.prv_id as per_id, maestros_proveedores.nombre as persona, maestros_proveedores.ruc from maestros_proveedores
		) per on mpr.per_id = per.per_id and mpr.tipo_pagador = per.tipo 
		where mpr.tie_id = ".$this->sessName->se_age_id." and mpr.codigo = '".$_POST['nro']."'");
		
		if(count($cabecera)==0){
			$data = array('success' => false, 'mensaje' => 'No existe el recibo <br>Con Nro : '.$_POST['nro'].', por favor indique otro numero');
			echo json_encode($data);
			exit();
		}

		$id = $cabecera[0][0];
		$rs = $this->objDatos->sp_obtenerdatasql("select dpr.*, cco.nombre as concepto from detalle_prerecibo dpr inner join concepto_contable cco on dpr.cco_id = cco.cco_id where dpr.mpr_id = ".$id);
		$varibale[1]=$rs;

        
        $data = array('success' => true, 'total' => count($varibale), 'cabecera' => $cabecera, 'data' => $rs);
        echo json_encode($data);
    }
	
	
	//Saldo Compras
	public function saldocomprasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function saldocomprasGuardarAction()
    {	
		$this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		
			$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_compra>0");
			if(count($rs)==0){
				$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
				echo json_encode($data);
				exit();
			}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_saldocompras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and prv_id = ".$_POST['prv_id']." and age_id = ".$_POST['age_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Compra con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].' para el mismo proveedor, por favor modifique los datos');
			echo json_encode($data);
			exit();
		}		
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_saldocompras_guardar($_POST);		
		//print_r($rs);
         $data = array('success' => true, 'sco_id' => $rs);
        echo json_encode($data);
    }
	
	public function saldocomprasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and valor_compra>0 and valor_compra>0");
		if(count($rs)==0){
			$data = array('success' => false, 'mensaje' => 'No existe Tipo de Cambio<br>Para Fecha '.$_POST['fecha'].',<br>por favor ingrese el Tipo de Cambio');
			echo json_encode($data);
			exit();
		}*/
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_saldocompras where eliminado = 0 and doc_id = ".$_POST['doc_id']." and doc_n = '".$_POST['doc_n']."' and prv_id = ".$_POST['prv_id']." and age_id = ".$_POST['age_id']." and sco_id <> ".$_POST['sco_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Compra con el Documento '.$_POST['tip_doc'].' y el Numero '.$_POST['doc_n'].' para el mismo proveedor, por favor modifique los datos');
			echo json_encode($data);
			exit();
		}	
		
		//print_r($_POST);
		$rs = $this->objDatos->sp_saldocompras_actualizar($_POST);		
		//print_r($rs);
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function saldocomprasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_saldocompras_lista($_POST,1);
		$rsData = $this->objDatos->sp_saldocompras_lista($_POST,2);  
		$size=$rsCount[0][0];
        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
		
        echo json_encode($data);
    }
	
	public function saldocomprasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_saldocompras_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function saldocomprasAnularAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
        $rs = $this->objDatos->sp_saldocompras_anular($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }	
    
    public function saldocomprasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function saldocomprasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_obtenerdatasql('select case when max(codigo) is null then 1 else max(codigo) + 1 end as codigo from movimientos_saldocompras where age_id = '.$this->sessName->se_age_id);
		//print_r($rs);
		//echo $this->sessName->se_age_id;
		//exit();
        $data = $rs[0]; 
        echo json_encode($data);
    }
	
	//<<<
	
	public function printAction(){
		$this->_helper->viewRenderer->setNoRender();
		//echo "HOLA";
		
		$prueba = new ZendCPdf_Cezpdf();
		
		//$prueba->configuraPagina("portrait","Titulo");
		//echo $prueba;
		$prueba->ezText('CODIGO :', 12, array('justification' => 'left'));			
		$prueba->ezStream();
			
	}
	
}

