<?php
date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'spanish');
class TablasController extends Zend_Controller_Action
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
        $this->objDatos=new Application_Model_Tablas();
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
	
	//Funciones para configuracion >>>	
	public function configuracionAction()
    {	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 49 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function configuracionCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function configuracionListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_configuracion_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function configuracionGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();	
		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from configuracion where nombre = '".$_POST['nombre']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un variable con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}*/
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_configuracion_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function configuracionActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		/*$rs = $this->objDatos->sp_obtenerdatasql("select * from configuracion where nombre = '".$_POST['nombre']."' and con_id <> ".$_POST['con_id']." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un variable con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}*/
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_configuracion_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function configuracionEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$data = array('success' => false, 'mensaje' => 'No se puede eliminar Configuracion de una Tienda');
		
		/*$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_configuracion_eliminar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);*/
        echo json_encode($data);
    }
	
	public function configuracionListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'con_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Variable',
						'ancho'=>'340',
						'alineacion'=>'LEFT',
						'diccionario'=>'Variable'),
				2=>array('idcampo'=>2,
						'descripcion'=>'valor',
						'idtipocontrol'=>'1',
						'comentario'=>'Valor',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Valor')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2'], 'age_id'=>$this->sessName->se_age_id);
        $rs = $this->objDatos->sp_configuracion_lista($var);
		$titulo = "Listado de configuracion";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	//Funciones para numeracion >>>	
	public function numeracionAction()
    {	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 41 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function numeracionCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function numeracionListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_numeracion_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function numeracionGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from numeracion where tipo = '".$_POST['tipo']."' and doc_id = ".$_POST['doc_id']." and age_id = ".$this->sessName->se_age_id." ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Correlativo para '.$_POST['doc_nom'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_numeracion_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function numeracionActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		//echo "select * from numeracion where tipo = '".$_POST['tipo']."' and doc_id = ".$_POST['doc_id']." and age_id = ".$this->sessName->se_age_id." and num_id <> ".$_POST['num_id'];
		//exit();
		$rs = $this->objDatos->sp_obtenerdatasql("select * from numeracion where tipo = '".$_POST['tipo']."' and doc_id = ".$_POST['doc_id']." and age_id = ".$this->sessName->se_age_id." and num_id <> ".$_POST['num_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Correlativo para '.$_POST['doc_nom'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		$_POST['age_id'] = $this->sessName->se_age_id;
        $rs = $this->objDatos->sp_numeracion_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function numeracionEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$_POST['age_id'] = $this->sessName->se_age_id;
		
		
		
        $rs = $this->objDatos->sp_numeracion_eliminar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function numeracionListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'lin_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'340',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'fam_id',
						'idtipocontrol'=>'1',
						'comentario'=>'Codigo de Familia',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2'], 'age_id'=>$this->sessName->se_age_id);
        $rs = $this->objDatos->sp_numeracion_lista($var);
		$titulo = "Listado de numeracion";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	//Numeracion
	public function numeracionObtenerCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//echo "select * from numeracion where age_id = '".$sessName->se_age_id."' and doc_id = ".$_POST['doc_id']." and tipo = '".$_POST['tipo']."'";
		$rs = $this->objDatos->sp_obtenerdatasql("select * from numeracion where age_id = '".$this->sessName->se_age_id."' and doc_id = ".$_POST['doc_id']." and tipo = '".$_POST['tipo']."'");
		if(count($rs)>0){
			$data = array('success' => true, 'data' => $rs);
			echo json_encode($data);
			exit();
		}else{
			$data = array('success' => false, 'total' => count($rs), 'data' => $rs);
			echo json_encode($data);			
		}
    }
	
	//Situacion
	public function situacionListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_situacion_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	//Funciones para tipoMovimiento >>>	
	public function tipoMovimientoAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 46 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function tipoMovimientoCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
		
    public function tipoMovimientoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_tipo_movimiento_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }    
	
	public function tipoMovimientoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_movimiento where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_movimiento where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_movimiento_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoMovimientoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_movimiento where codigo = '".$_POST['codigo']."' and tmv_id <> ".$_POST['tmv_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_movimiento where nombre = '".$_POST['nombre']."' and tmv_id <> ".$_POST['tmv_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_movimiento_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoMovimientoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from altas where tmv_id = ".$_POST['tmv_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen altas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bajas where tmv_id = ".$_POST['tmv_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen bajas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_movimiento_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoMovimientoListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_tipo_movimiento_lista($var);
		$titulo = "Listado de Tipos de Movimiento";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	//Funciones para conceptoPago >>>	
	public function conceptoPagoAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 55 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function conceptoPagoCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
		
    public function conceptoPagoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_concepto_pago_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }    
	
	public function conceptoPagoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from concepto_pago where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from concepto_pago where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_concepto_pago_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function conceptoPagoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from concepto_pago where codigo = '".$_POST['codigo']."' and coc_id <> ".$_POST['coc_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from concepto_pago where nombre = '".$_POST['nombre']."' and coc_id <> ".$_POST['coc_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_concepto_pago_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function conceptoPagoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from altas where coc_id = ".$_POST['coc_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen altas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bajas where coc_id = ".$_POST['coc_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen bajas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_concepto_pago_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function conceptoPagoListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_concepto_pago_lista($var);
		$titulo = "Listado de Tipos de Movimiento";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	//Funciones para tipoPago >>>	
	public function tipoPagoAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 56 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function tipoPagoCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
		
    public function tipoPagoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_tipo_pago_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }    
	
	public function tipoPagoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_pago where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_pago where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_pago_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoPagoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_pago where codigo = '".$_POST['codigo']."' and tpa_id <> ".$_POST['tpa_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_pago where nombre = '".$_POST['nombre']."' and tpa_id <> ".$_POST['tpa_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_pago_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoPagoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from altas where tpa_id = ".$_POST['tpa_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen altas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bajas where tpa_id = ".$_POST['tpa_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen bajas registradas con este tipo de movimiento, por favor elimine o modifique primero las altas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_pago_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoPagoListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_tipo_pago_lista($var);
		$titulo = "Listado de Tipos de Movimiento";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	//Funciones para tipoNota >>>	
	public function tipoNotaAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 46 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function tipoNotaCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
		
    public function tipoNotaListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_tipo_nota_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }    
	
	public function tipoNotaGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_nota where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_nota where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_nota_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoNotaActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_nota where codigo = '".$_POST['codigo']."' and tnt_id <> ".$_POST['tnt_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_nota where nombre = '".$_POST['nombre']."' and tnt_id <> ".$_POST['tnt_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Movimiento con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_nota_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoNotaEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bajas where tnt_id = ".$_POST['tnt_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito registradas con este tipo de nota, por favor elimine o modifique primero las notas de credito relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bajas where tnt_id = ".$_POST['tnt_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Notas de Credito registradas con este tipo de nota, por favor elimine o modifique primero las notas de credito relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_nota_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoNotaListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_tipo_nota_lista($var);
		$titulo = "Listado de Tipos de Movimiento";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para documentos >>>	
	public function documentosAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 44 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function documentosCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function documentosDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function documentosListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_documentos_lista($_POST);
        $_data=Array();
		
		if($_POST['vacio']<>''){
			$fila["id_doc"]='';
			$fila["descripcion"]=$_POST['vacio'];
			$_data[]=$fila;
		}
        foreach($rs as $fila)
        {
            $compras=explode("-",$fila["n_compras"]);
            $ventas=explode("-",$fila["n_ventas"]);
            $credito=explode("-",$fila["nn_credito"]);
            $debito=explode("-",$fila["nn_debito"]);
            $fila["str_serie_n_compras"]=$compras[0];
            $fila["str_n_compras"]=$compras[1];
            $fila["str_serie_n_ventas"]=$ventas[0];
            $fila["str_n_ventas"]=$ventas[1];
            $fila["str_serie_nn_credito"]=$credito[0];
            $fila["str_nn_credito"]=$credito[1];
            $fila["str_serie_nn_debito"]=$debito[0];
            $fila["str_nn_debito"]=$debito[1];
            $_data[]=$fila;
        }
        
        $data = array('success' => true, 'total' => count($_data), 'data' => $_data);
        echo json_encode($data);
    }
    
    public function documentosGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from documentos where descripcion = '".$_POST['descripcion']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Documento con la descripcion '.$_POST['descripcion'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_documentos_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function documentosActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from documentos where descripcion = '".$_POST['descripcion']."' and doc_id <> ".$_POST['doc_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Documento con la descripcion '.$_POST['descripcion'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_documentos_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function documentosEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where doc_id = ".$_POST['doc_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Compras registradas con este tipo de documento, por favor elimine o modifique primero las compras relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_ventas where doc_id = ".$_POST['doc_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Ventas registradas con este tipo de documento, por favor elimine o modifique primero las ventas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_documentos_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function documentosListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'doc_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'40',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'abre',
						'idtipocontrol'=>'1',
						'comentario'=>'Abreviatura',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'descripcion',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'300',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				3=>array('idcampo'=>2,
						'descripcion'=>'compras',
						'idtipocontrol'=>'1',
						'comentario'=>'Compras',
						'ancho'=>'50',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				4=>array('idcampo'=>2,
						'descripcion'=>'ventas',
						'idtipocontrol'=>'1',
						'comentario'=>'Ventas',
						'ancho'=>'50',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_documentos_lista($var);
		$titulo = "Listado de Documentos";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para tipoCambio >>>	
	public function tipoCambioAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 36 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
    public function tipoCambioDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoCambioListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $this->sessName->se_age_id;
		$offset=isset($_POST['start'])?$_POST['start']:0;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select tipo_cambio.tic_id, DATE_FORMAT(tipo_cambio.fecha, '%d/%m/%Y') as fecha, tipo_cambio.valor_compra, tipo_cambio.valor_venta ";		
		$sql_from = " from tipo_cambio ";
		$sql_where = " where 1 = 1 ";
		if($_POST['campo']<>''){
			if($_POST['campo']=='tic_id'){
				$sql_where = $sql_where." and tipo_cambio.".$_POST['campo']." = ".$_POST['query']." ";
			}else{
				$sql_where = $sql_where." and tipo_cambio.".$_POST['campo']." like '".$_POST['query']."%' ";
			}
		}
		$sql_order = " order by tipo_cambio.".($_POST['sort']?$_POST['sort']:'fecha')." ".($_POST['dir']?$_POST['dir']:'asc');
		$sql_limit = " limit ".$offset.", 100 ";
		
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		
		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/
		
		
		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);
		
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
        //$rsData[0]=array('nombre'=>$_POST[0]);
		$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
        echo json_encode($data);
    }
    
    public function tipoCambioGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Cambio con fecha '.$_POST['fecha'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_cambio_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function tipoCambioActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".$_POST['fecha']."' and tic_id <> ".$_POST['tic_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Cambio con fecha '.$_POST['fecha'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_cambio_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoCambioEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where fec_ven in (select fecha from tipo_cambio where tic_id = ".$_POST['tic_id'].")");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Compras registradas con este tipo de cambio, por favor elimine o modifique primero las compras relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_ventas where fec_ven in (select fecha from tipo_cambio where tic_id = ".$_POST['tic_id'].")");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Ventas registradas con este tipo de cambio, por favor elimine o modifique primero las ventas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_tipo_cambio_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function tipoCambioListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'tic_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'70',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'fecha',
						'idtipocontrol'=>'1',
						'comentario'=>'Fecha',
						'ancho'=>'120',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'valor_compra',
						'idtipocontrol'=>'1',
						'comentario'=>'Valor de Compra',
						'ancho'=>'175',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				3=>array('idcampo'=>2,
						'descripcion'=>'valor_venta',
						'idtipocontrol'=>'1',
						'comentario'=>'Valor de Venta',
						'ancho'=>'175',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_tipo_cambio_lista($var);
		$titulo = "Listado de Tipos de Cambio";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
    //Funciones para monedas >>> 
	public function monedasAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 37 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function monedasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function monedasDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function monedasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from monedas where nombre = '".$_POST['nombre']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Moneda con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_monedas_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function monedasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from monedas where nombre = '".$_POST['nombre']."' and mon_id <> ".$_POST['mon_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Moneda con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_monedas_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function monedasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_monedas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function monedasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_monedas_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function monedasListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'mon_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_monedas_lista($var);
		$titulo = "Listado de Monedas";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<

	
    
	//Funciones para condicionesPago >>>
	public function condicionPagoAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 39 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
		
    public function condicionesPagoDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function condicionesPagoCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
    
    public function condicionesPagoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_condiciones_pago_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function condicionesPagoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from condiciones_pago where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Condicion de Pago con el codigo '.$_POST['codigo'].', por favor modifique los datos', 'campo'=>'-codigo');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from condiciones_pago where descripcion = '".$_POST['descripcion']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Condicion de Pago con la descripcion '.$_POST['descripcion'].', por favor modifique los datos', 'campo'=>'-descripcion');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_condiciones_pago_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function condicionesPagoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from condiciones_pago where codigo = '".$_POST['codigo']."' and cpa_id <> ".$_POST['cpa_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Condicion de Pago con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from condiciones_pago where descripcion = '".$_POST['descripcion']."' and cpa_id <> ".$_POST['cpa_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Condicion de Pago con la descripcion '.$_POST['descripcion'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_condiciones_pago_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function condicionesPagoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_compras where cpa_id  = ".$_POST['cpa_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Compras registradas con esta condicion de pago, por favor elimine o modifique primero las compras relacionadas');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from movimientos_ventas where cpa_id  = ".$_POST['cpa_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Ventas registradas con esta condicion de pago, por favor elimine o modifique primero las ventas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_condiciones_pago_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function condicionesPagoListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'descripcion',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'240',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'dias',
						'idtipocontrol'=>'1',
						'comentario'=>'Días',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				3=>array('idcampo'=>2,
						'descripcion'=>'letras',
						'idtipocontrol'=>'1',
						'comentario'=>'Letras',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_condiciones_pago_lista($var);
		$titulo = "Listado de Condiciones de Pago";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para marcas >>>	
	public function marcasAction()
    {
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 43 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function marcasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function marcasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $sessName->se_age_id;
		$offset=isset($_POST['start'])?$_POST['start']:0;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select * ";		
		$sql_from = " from marcas ";
		$sql_where = " where 1 = 1 ";
		if($_POST['campo']<>''){
			if($_POST['campo']=='mar_id'){
				$sql_where = $sql_where." and marcas.".$_POST['campo']." = ".$_POST['query']." ";
			}else{
				$sql_where = $sql_where." and marcas.".$_POST['campo']." like '".$_POST['query']."%' ";
			}
		}
		$sql_order = " order by marcas.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');
		$sql_limit = " limit ".$offset.", 100 ";
		
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		
		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/
		
		
		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);
		
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
        //$rsData[0]=array('nombre'=>$_POST[0]);
		$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
        echo json_encode($data);
    }
	
	public function marcasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from marcas where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Marca con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_marcas_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function marcasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from marcas where nombre = '".$_POST['nombre']."' and mar_id <> ".$_POST['mar_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Marca con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_marcas_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function marcasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where mar_id  = ".$_POST['mar_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Productos registrados con esta marca, por favor elimine o modifique primero los productos relacionados');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_marcas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function marcasListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'mar_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_marcas_lista($var);
		$titulo = "Listado de Marcas";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para lineas >>>	
	public function lineasAction()
    {	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 38 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function lineasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function lineasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_lineas_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function lineasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from lineas where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Linea con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
        $rs = $this->objDatos->sp_lineas_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function lineasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from lineas where nombre = '".$_POST['nombre']."' and lin_id <> ".$_POST['lin_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Linea con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_lineas_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function lineasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where lin_id  = ".$_POST['lin_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Productos registrados con esta linea, por favor elimine o modifique primero los productos relacionados');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_lineas_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function lineasListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'lin_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'340',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'fam_id',
						'idtipocontrol'=>'1',
						'comentario'=>'Codigo de Familia',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_lineas_lista($var);
		$titulo = "Listado de Lineas";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para unidadMedida >>>	
	public function unidadMedidaAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 42 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function unidadMedidaCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function unidadMedidaListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_unidad_medida_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function unidadMedidaGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from unidad_medida where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Unidad de Medida con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from unidad_medida where descripcion = '".$_POST['descripcion']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Unidad de Medida con la descripcion '.$_POST['descripcion'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_unidad_medida_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function unidadMedidaActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from unidad_medida where codigo = '".$_POST['codigo']."' and ume_id <> ".$_POST['ume_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Unidad de Medida con el codigo '.$_POST['codigo'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from unidad_medida where descripcion = '".$_POST['descripcion']."' and ume_id <> ".$_POST['ume_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Unidad de Medida con la descripcion '.$_POST['descripcion'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_unidad_medida_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function unidadMedidaEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where ume_id  = ".$_POST['ume_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Productos registrados con esta unidad de medida, por favor elimine o modifique primero los productos relacionados');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_unidad_medida_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function unidadMedidaListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'abreviatura',
						'idtipocontrol'=>'1',
						'comentario'=>'Avreviatura',
						'ancho'=>'150',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion'),
				2=>array('idcampo'=>2,
						'descripcion'=>'descripcion',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'290',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_unidad_medida_lista($var);
		$titulo = "Listado de Unidades de Medida";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para igv >>>	
	public function igvAction()
    {
	
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 45 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
    public function igvDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function igvListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_igv_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function igvGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_igv_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function igvActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
        $rs = $this->objDatos->sp_igv_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function igvEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_igv_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function igvListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'igv_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'valor',
						'idtipocontrol'=>'1',
						'comentario'=>'Valor de IGV',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_igv_lista($var);
		$titulo = "Listado de IGV";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
    
	//Funciones para familias >>>	
	public function familiasAction()
    {
		
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 40 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function familiasCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
		
    public function familiasListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_familias_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }    
	
	public function familiasGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from familias where codigo = '".$_POST['codigo']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Familia con el codigo '.$_POST['codigo'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from familias where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Familia con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_familias_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function familiasActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from familias where codigo = '".$_POST['codigo']."' and fam_id <> ".$_POST['fam_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Familia con el codigo '.$_POST['codigo'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from familias where nombre = '".$_POST['nombre']."' and fam_id <> ".$_POST['fam_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Familia con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_familias_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function familiasEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from lineas where fam_id  = ".$_POST['fam_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Lineas registrados con esta familia, por favor elimine o modifique primero las lineas relacionadas');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_familias_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function familiasListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'codigo',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_familias_lista($var);
		$titulo = "Listado de Familias";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
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
	
	public function generarReporte($dataCampos, $rs, $titulo){
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
				
			reset($dataCampos);
			$x=30;
			$y = 760;
			foreach($dataCampos as $solo){				  
				$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))
					  ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))
					  ->drawRectangle($x -3,$y+20 - 3, $x + $solo['ancho'] - 3,$y - 3);
					  
				$page->setFont($font, 14)
					  ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))
					  ->drawText(utf8_decode($solo['comentario']), $x,$y);
					  
				$x = $x +$solo['ancho'];
			}	
			
			$y = 740;
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
					$y = 760;
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
					$y = 740;
					
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
	
	//Funciones para bancos >>>	
	public function bancosAction()
    {
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 43 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function bancosCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function bancosListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $sessName->se_age_id;
		$offset=isset($_POST['start'])?$_POST['start']:0;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select * ";		
		$sql_from = " from bancos ";
		$sql_where = " where 1 = 1 ";
		if($_POST['campo']<>''){
			if($_POST['campo']=='ban_id'){
				$sql_where = $sql_where." and bancos.".$_POST['campo']." = ".$_POST['query']." ";
			}else{
				$sql_where = $sql_where." and bancos.".$_POST['campo']." like '".$_POST['query']."%' ";
			}
		}
		$sql_order = " order by bancos.".($_POST['sort']?$_POST['sort']:'nombre')." ".($_POST['dir']?$_POST['dir']:'asc');
		$sql_limit = " limit ".$offset.", 100 ";
		
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		
		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/
		
		
		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);
		
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
        //$rsData[0]=array('nombre'=>$_POST[0]);
		$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
        echo json_encode($data);
    }
	
	public function bancosGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bancos where nombre = '".$_POST['nombre']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Marca con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_bancos_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bancosActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from bancos where nombre = '".$_POST['nombre']."' and ban_id <> ".$_POST['ban_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Marca con el nombre '.$_POST['nombre'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_bancos_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bancosEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from maestros_mercaderias where ban_id  = ".$_POST['ban_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Existen Productos registrados con esta marca, por favor elimine o modifique primero los productos relacionados');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_bancos_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function bancosListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'ban_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nombre',
						'idtipocontrol'=>'1',
						'comentario'=>'Descripcion',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_bancos_lista($var);
		$titulo = "Listado de bancos";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	
	//Funciones para ctabanco >>>	
	public function ctabancoAction()
    {
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 43 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function ctabancoCodigoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_generar_codigo($_POST);

        $data = $rs[0]; 
        echo json_encode($data);
    }
	
    public function ctabancoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$_POST['age_id'] = $sessName->se_age_id;
		$offset=isset($_POST['start'])?$_POST['start']:0;
		
		$sql_count = "select COUNT(*) ";
		$sql_selec = "select ctabanco.*, bancos.nombre as ban_nom, monedas.nombre as mon_nom ";		
		$sql_from = " from ctabanco inner join bancos on ctabanco.ban_id = bancos.ban_id
							inner join monedas on ctabanco.mon_id = monedas.mon_id ";
		$sql_where = " where 1 = 1 ";
		if($_POST['ban_id']>0){
			$sql_where = $sql_where." and ctabanco.ban_id = ".$_POST['ban_id']." ";
		}
		if($_POST['mon_id']>0){
			$sql_where = $sql_where." and ctabanco.mon_id = ".$_POST['mon_id']." ";
		}
		if($_POST['campo']<>''){
			if($_POST['campo']=='mon_id'){
				$sql_where = $sql_where." and ctabanco.".$_POST['campo']." = ".$_POST['query']." ";
			}else{
				$sql_where = $sql_where." and ctabanco.".$_POST['campo']." like '".$_POST['query']."%' ";
			}
		}
		$sql_order = " order by ctabanco.".($_POST['sort']?$_POST['sort']:'nro_cta')." ".($_POST['dir']?$_POST['dir']:'asc');
		$sql_limit = " limit ".$offset.", 100 ";
		
        $rsCount = $this->objDatos->sp_obtenerdatasql($sql_count.$sql_from.$sql_where.$sql_order);
		
		/*$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);*/
		
		
		//$rsData = $this->objDatos->sp_mercaderias_lista($_POST);
		
		$rsData = $this->objDatos->sp_obtenerdatasql($sql_selec.$sql_from.$sql_where.$sql_order.$sql_limit);
		
        //$rsData[0]=array('nombre'=>$_POST[0]);
		$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));
        echo json_encode($data);
    }
	
	public function ctabancoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from ctabanco where nro_cta = '".$_POST['nro_cta']."'");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Cuenta Bancanria con el nro '.$_POST['nro_cta'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_ctabanco_guardar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function ctabancoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from ctabanco where nro_cta = '".$_POST['nro_cta']."' and cta_id <> ".$_POST['cta_id']."");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe una Cuenta Bancanria con el nro '.$_POST['nro_cta'].',<br>Por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_ctabanco_actualizar($_POST);        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function ctabancoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
				
        $rs = $this->objDatos->sp_ctabanco_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function ctabancoListaImpresionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		//Configuracion de Campos de Cabecera		
		$dataCampos = array(
				0=>array('idcampo'=>1,
						'descripcion'=>'cta_id',
						'idtipocontrol'=>'6',
						'comentario'=>'Código',
						'ancho'=>'100',
						'alineacion'=>'LEFT',
						'diccionario'=>'Codigo'),
				1=>array('idcampo'=>2,
						'descripcion'=>'nro_cta',
						'idtipocontrol'=>'1',
						'comentario'=>'Numero de Cuenta',
						'ancho'=>'440',
						'alineacion'=>'LEFT',
						'diccionario'=>'Descripcion')
			);
		
		//Datos que se van mostrar
		$var = array('campo'=>$_POST['txtpar1'],'query'=>$_POST['txtpar2']);
        $rs = $this->objDatos->sp_ctabanco_lista($var);
		$titulo = "Listado de ctabanco";
		$this->generarReporte($dataCampos , $rs, $titulo);
		
    }
	//<<<
	
	
	//ConceptoContable
	public function conceptoContableListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_concepto_contable_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }  
}