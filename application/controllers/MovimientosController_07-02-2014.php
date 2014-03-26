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

		//Zend_Session::start();

        $this->sessName = new Zend_Session_Namespace('inventory');		

		

		if($this->sessName->se_usr_id=='' or $this->sessName->se_usr_id == NULL){

			$data = array('success' => false, 'mensaje' => 'Se ha cerrado la sesion, inicie de nuevo la sesion');

			echo json_encode($data);

		}

				

		$_POST['se_usr_id']=$this->sessName->se_usr_id;

		$_POST['se_age_id']=$this->sessName->se_age_id;

		$this->view->usp_id = $this->sessName->se_usp_id;

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
		//$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo']);
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Compras',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'fec_ven' => 'Fecha',
			'descripcion_documento' => 'Doc',
			'doc_n' => 'Numero',
			'ruc' => 'RUC',
			'nombre_proveedor' => 'Proveedor',
			'valor_compra' => 'V.Compra',
			'impuesto_igv' => 'V.IGV',
			'total_compra' => 'T.Compra',
			'abrev_moneda' => 'Mon.',
			'anulado' => 'X',
		);
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'descripcion_documento' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'ruc' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'valor_compra' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_compra' => array('justification'=>'right'),
				'abrev_moneda' => array('justification'=>'left'),
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
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Compras', $options,$_POST['txtfiltro']);
		
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_compra'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_compra'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR COMPRA :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL COMPRA :".$suma_t, 12, array('justification' => 'left'));
		
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

		

		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Ingresos Varios',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

		$col_names = array(
			'fec_ven' => 'Fecha',
			'doc_abrev' => 'Doc',
			'doc_n' => 'Numero',
			'valor_compra' => 'Valor Ingreso',
			'impuesto_igv' => 'Impuesto IGV',
			'total_compra' => 'Total Ingreso',
			'mon_abrev' => 'Mon',
			'anulado' => 'X',
			'tipo_cambio' => 'Tipo Cambio',
		);

		

		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'doc_abrev' => array('justification'=>'center'),
				'doc_n' => array('justification'=>'left'),
				'valor_compra' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_compra' => array('justification'=>'right'),
				'mon_abrev' => array('justification'=>'center'),
				'anulado' => array('justification'=>'left'),
				'tipo_cambio' => array('justification'=>'right'),
			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=100;

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$db_data = $this->objDatos->sp_altas_lista($_POST,2);

		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_compra'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_compra'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR ING :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL ING :".$suma_t, 12, array('justification' => 'left'));
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
		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',($_POST['txtpar4']=='TI'?'Listado de Ingresos por Traslado':'Listado de Salidas por Traslado'),0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'fec_ven' => 'Fecha',
			'doc_abrev' => 'Doc',
			'doc_n' => 'Numero',
			'origen' => 'Origen',
			'destino' => 'Destino',
			'valor_compra' => 'Valor '.$_POST['txtpar4'],
			'impuesto_igv' => 'IGV',
			'total_compra' => 'Total '.$_POST['txtpar4'],
			'mon_abrev' => 'Mon',
			'anulado' => 'X',
			'tipo_cambio' => 'T.Cambio',
		);
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'doc_abrev' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'origen' => array('justification'=>'left'),
				'destino' => array('justification'=>'left'),
				'valor_compra' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_compra' => array('justification'=>'right'),
				'mon_abrev' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
				'tipo_cambio' => array('justification'=>'right'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['tipo'] = "E";
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_trasladoing_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, ($_POST['txtpar4']=='TI'?'Listado de Ingresos por Traslado':'Listado de Salidas por Traslado'), $options);
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_compra'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_compra'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR ".$_POST['txtpar4']." :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL ".$_POST['txtpar4']." :".$suma_t, 12, array('justification' => 'left'));
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

		

		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Notas de Credito de Ventas',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

		$col_names = array(
			'fec_ven' => 'Fecha',
			'doc_abrev' => 'Doc',
			'doc_n' => 'Numero',
			'valor_venta' => 'Valor NCV',
			'impuesto_igv' => 'IGV',
			'total_venta' => 'Total NCV',
			'mon_abrev' => 'Mon',
			'anulado' => 'X',
			'tipo_cambio' => 'T.Cambio',
		);

		

		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'doc_abrev' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'valor_venta' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_venta' => array('justification'=>'right'),
				'mon_abrev' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
				'tipo_cambio' => array('justification'=>'right'),
			)

		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$db_data = $this->objDatos->sp_notaventas_lista($_POST,2);
		$pdf->ezTable($db_data, $col_names, 'Listado de Ingresos Varios', $options);
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_venta'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_venta'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR NV :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL NV :".$suma_t, 12, array('justification' => 'left'));
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

        //print_r($rs); exit;

        $data = array('success' => true, 'mve_id' => $rs);

        $content = json_encode($data);
        $this->getResponse()->appendBody($content);

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
	//die(var_dump($this->sessName->se_age_id));	
        $_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_ventas_lista($_POST,1);

	$rsData = $this->objDatos->sp_ventas_lista($_POST,2);  

	$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

		

        echo json_encode($data);

    }

	
    public function ventas2ListaAction()
    {

        $this->_helper->viewRenderer->setNoRender();
	//die(var_dump($this->sessName->se_age_id));	
        $_POST['age_id'] = $this->sessName->se_age_id;
		
        $rsCount = $this->objDatos->sp_ventas_2_lista($_POST,1);

	$rsData = $this->objDatos->sp_ventas_2_lista($_POST,2);  

	$size=$rsCount[0][0];

        $data = array('success' => true, 'total' => $rsCount[0][0], 'data' => array_splice($rsData, 0, $size));

		

        echo json_encode($data);

    }

	public function ventasListaImpresionAction()

    {

		ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		//$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo'],1);
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Ventas',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

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
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }

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

		$this->view->se_valida_stock = $this->sessName->se_valida_stock;

    }

	

	public function ventasrapidaAction()

    {

		

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 12 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

		$this->view->se_valida_stock = $this->sessName->se_valida_stock;

    }

	

	public function formatoVentasAction()

    {

        $this->_helper->viewRenderer->setNoRender();

$this->objDatos->sp_ejecutarsql("delete from formato_cabecera where usr_id = ".$this->sessName->se_usr_id.";insert into formato_cabecera

select case when mve.cli_id = 0 then mve.cliente else cli.nombre end as nombre_cliente,

doc.descripcion as descripcion_documento,

mve.mve_id,

mve.codigo,

mve.cli_id,

mve.tipo_ingreso,

mve.doc_id,

mve.doc_n,

mve.n_guia,

mve.cpa_id,

mve.mon_id,

mve.valor_venta,

mve.impuesto_igv,

mve.total_venta, 

DATE_FORMAT(mve.fec_ven, '%d/%m/%Y') as fec_ven, 

DATE_FORMAT(mve.fec_mov, '%d/%m/%Y') as fec_mov, 

monedas.nombre as moneda, 

mve.anulado, 

case when mve.cli_id = 0 then '' else cli.codigo end as cli_codigo, 

case when mve.cli_id = 0 then mve.cli_ruc else cli.ruc end as ruc , 

mve.eliminado, 

mve.afecta, 

mve.formato,

mve.observacion,

cli.direccion,

mve.fec_ven as fec_ori,

cpa.descripcion condicion,

mve.saldo,

cpa.letras,

cpa.dias, tie.nombre as agencia, concat(usu.nombres,' ',usu.apellidos) as usuario, usu.usr_id, mve.age_id 

from movimientos_ventas mve

inner join tiendas tie on mve.age_id = tie.tie_id

left join usuarios usu on usu.usr_id = ".$this->sessName->se_usr_id."

		left join maestros_clientes cli on cli.cli_id=mve.cli_id

		inner join documentos doc on doc.doc_id=mve.doc_id

		inner join monedas on mve.mon_id = monedas.mon_id 

		inner join condiciones_pago cpa on mve.cpa_id = cpa.cpa_id

where mve.mve_id=".$_POST['txtpar2']);



$this->objDatos->sp_ejecutarsql("delete from formato_detalle;insert into formato_detalle

select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca 

from detalle_ventas dve 

inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id 

inner join marcas on mcd.mar_id = marcas.mar_id 

where dve.mve_id =".$_POST['txtpar2']);

		

		if($_POST['tipo'] == '1'){

			if($rsData[0]['doc_id']==2){

				$_SESSION['tamano'] = '590:450:';

			}

			if($rsData[0]['doc_id']==3){

				$_SESSION['tamano'] = '570:450:';

			}

		}else{

			$_SESSION['tamano'] = 'A4';

		}

		$pdf = new Zend_Pdf();

		$style = new Zend_Pdf_Style();

		$style->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0)); 

		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA); 

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

		

			if($rsData[0]['doc_id']==2){	//FACTURA

				$pdf->pages[] = ($page = $pdf->newPage('590:450:'));//'624:842:'

				$page->setStyle($style);		

				

				$page->setFont($font, 7);

				$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));

				

				$page->drawText("F/.".$rsData[0]['doc_n'], 390 ,418);					

				$page->drawText(($rsData[0]['nombre_cliente']), 90 ,402,"UTF-8");			

				$page->drawText((trim($rsData[0]['direccion']).' - '.utf8_encode(trim($rsData[0]['ubigeo']))), 90 ,392,"UTF-8");

				

				$page->drawText($rsData[0]['ruc'], 90 ,382);

				$page->drawText($rsData[0]['n_guia'], 320 ,382);

				$page->setFont($font, 7);

				$page->drawText(date('d           m              Y',strtotime($rsData[0]['fec_ori'])), 490 , 382);				

				

					

				$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);   

				$y=362;				

				foreach($rsDetalle as $solo){	

					$y = $y - 8;		

					$page->setFont($font, 7);			

					$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());			  

					$page->drawText(utf8_decode($solo['cantidad']), 85 - $lon_tot - 30 ,$y)  //45

						->drawText(($solo['codigo1']), 80 ,$y,"UTF-8")			

						->drawText(($solo['producto']), 165 ,$y,"UTF-8")												  

						->drawText(($solo['marca']), 420 ,$y,"UTF-8");												  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());

					$page->drawText(utf8_decode($solo['precio_venta']), 540 - $lon_tot - 30,$y); //490						  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					

					$page->drawText(utf8_decode($solo['total']), 615 - $lon_tot - 30,$y); //550					

				}

				$y=225;

				$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());

				$page->setFont($font, 7)

					->drawText($rsData[0]['valor_venta'] , 225 ,235)

					->drawText($rsData[0]['impuesto_igv'] , 440 ,235)

					->drawText($rsData[0]['total_venta'], 615 - $lon_tot - 30,235) //550

					->drawText($this->num2letras($rsData[0]['total_venta'],false,true,$rsData[0]['mon_id']), 40 ,220);

					

				

			}

			if($rsData[0]['doc_id']==3){	 //BOLETA

				$pdf->pages[] = ($page = $pdf->newPage('590:450:'));

				$page->setStyle($style);		

				

				$page->setFont($font, 7);

				$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));

				

				$page->drawText("B/.".$rsData[0]['doc_n'], 390 ,420);					

				$page->drawText(($rsData[0]['nombre_cliente']), 90 ,385,"UTF-8");			

				$page->drawText((trim($rsData[0]['direccion']).' - '.trim($rsData[0]['ubigeo'])), 90 ,375,"UTF-8");



				$page->drawText(date('d           m              Y',strtotime($rsData[0]['fec_ori'])), 510 , 385);				

					

				$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_ventas dve inner join maestros_mercaderias mcd on dve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dve.mve_id = ".$_POST['mve_id']);   

				$y=359;				

				foreach($rsDetalle as $solo){	

					$y = $y - 8;		

					$page->setFont($font, 7);			

					$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());			  

					$page->drawText(utf8_decode($solo['cantidad']), 85 - $lon_tot - 30 ,$y)  //45

						->drawText(($solo['codigo1']), 80 ,$y,"UTF-8")			

						->drawText(($solo['producto']), 142 ,$y,"UTF-8")												  

						->drawText(($solo['marca']), 435 ,$y,"UTF-8");												  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());

					$page->drawText(utf8_decode($solo['precio_venta']), 550 - $lon_tot - 30,$y); //490						  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					

					$page->drawText(utf8_decode($solo['total']), 615 - $lon_tot - 30,$y); //550					

				}

				$y=235;

				$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());

				$page->setFont($font, 7)

					->drawText($rsData[0]['total_venta'], 615 - $lon_tot - 30 ,$y);

			}

			/*if(!($rsData[0]['doc_id']==2 or $rsData[0]['doc_id']==2)){

				$pdf->pages[] = ($page = $pdf->newPage('200:200'));//'700:500'

				$page->setStyle($style);	

				$page->drawText("Sin informacion", 5 ,5);

			}*/

		}else{

			$pdf->pages[] = ($page = $pdf->newPage('A4'));

			$page->setStyle($style);		

			

			$page->setFont($font, 12);

			$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));

			

			

			$page->drawText(($rsDataEmpresa[0]['nombre'].'-'.$rsDataEmpresa[0]['ruc']), 40 ,800,"UTF-8");		

			$page->drawText(($rsDataEmpresa[0]['direccion']), 40 ,780,"UTF-8");		

			$page->drawText($rsDataEmpresa[0]['telefono'], 40 ,760,"UTF-8");	

					

			$page->setFont($font, 12);

			$page->drawText($rsData[0]['doc_n'], 450 ,750);					

			$page->drawText(($rsData[0]['nombre_cliente']), 40 ,700,"UTF-8");			

			$page->drawText(($rsData[0]['direccion']), 40 ,680,"UTF-8");

			

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

				->drawText($this->num2letras($rsData[0]['total_venta'],false,true,$rsData[0]['mon_id']), 50 ,$y - 40);			

		}

		header("content-type: application/pdf");

		echo "<script type='text/javascript'>

				window.print()

</script>";

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
		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Salidas Varias',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'fec_ven' => 'Fecha',
			'doc_abrev' => 'Doc',
			'doc_n' => 'Numero',
			'valor_venta' => 'Valor SAL',
			'impuesto_igv' => 'IGV',
			'total_venta' => 'Total SAL',
			'mon_abrev' => 'Mon',
			'observacion' => 'Observacion',
			'anulado' => 'X',
			'tipo_cambio' => 'T.Cambio',
		);
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'doc_abrev' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'valor_venta' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_venta' => array('justification'=>'right'),
				'mon_abrev' => array('justification'=>'left'),
				'observacion' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
				'tipo_cambio' => array('justification'=>'right'),
			)
		);

		$_POST['age_id'] = $this->sessName->se_age_id;

		$_POST['start']=($_POST['txtpar3']-1)*100;

		$_POST['limit']=100;

		$_POST['campo']=$_POST['txtpar1'];

		$_POST['query']=$_POST['txtpar2'];

		$db_data = $this->objDatos->sp_bajas_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Salida Varias', $options);
                
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_venta'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_venta'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR SAL :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL SAL :".$suma_t, 12, array('justification' => 'left'));
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
		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Notas de Credito de Compras',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		$col_names = array(
			'fec_ven' => 'Fecha',
			'doc_abrev' => 'Doc',
			'doc_n' => 'Numero',
			'nombre_proveedor' => 'Proveedor',
			'valor_compra' => 'Valor NCC',
			'impuesto_igv' => 'IGV',
			'total_compra' => 'Total NCC',
			'mon_abrev' => 'Mon',
			'anulado' => 'X',
			'tipo_cambio' => 'T.Cambio',
		);
		$options = array(
			'width' => 550,
			'cols' => array(
				'fec_ven' => array('justification'=>'center'),
				'doc_abrev' => array('justification'=>'left'),
				'doc_n' => array('justification'=>'left'),
				'nombre_proveedor' => array('justification'=>'left'),
				'valor_compra' => array('justification'=>'right'),
				'impuesto_igv' => array('justification'=>'right'),
				'total_compra' => array('justification'=>'right'),
				'mon_abrev' => array('justification'=>'left'),
				'anulado' => array('justification'=>'left'),
				'tipo_cambio' => array('justification'=>'right'),
			)
		);
		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['start']=($_POST['txtpar3']-1)*100;
		$_POST['limit']=100;
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
                
		$db_data = $this->objDatos->sp_notacompras_lista($_POST,2);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Notas de Credito de Compras', $options);
                
		$suma_v=0;
		$suma_i=0;
		$suma_t=0;
		foreach($db_data as $dato){
			$suma_v=$suma_v + $dato['valor_compra'];
			$suma_i=$suma_i + $dato['impuesto_igv'];
			$suma_t=$suma_t + $dato['total_compra'];
		}
		$pdf->ezText('', 12, array('justification' => 'left'));
		$pdf->ezText(utf8_decode("VALOR NCC :").$suma_v."               "."IGV :".$suma_i."               "."TOTAL NCC :".$suma_t, 12, array('justification' => 'left'));
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

		

		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Movimientos de Caja',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

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

		

		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Pre-Recibos',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

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

						echo '<td>'.utf8_decode($value['nombre_proveedor']).'</td>';

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

	public function rptproductoAction()

    {

	

		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);

		$perfil = $rs[0][0];

		

		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }
	
	public function productoExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Productos.xls");
		$db_data = $this->objDatos->sp_producto_rpt($_POST);
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">REGISTRO DE PRODUCTOS</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">'.$_POST['txtpar5'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>ID</td>
					<td>Codigo 1</td>
					<td>Nombre</td>					
					<td>IdMarca</td>
					<td>Marca</td>
					<td>IdLinea</td>
					<td>Linea</td>
					<td>Precio Costo</td>
					<td>Precio Venta</td>
					<td>Stock Inicial</td>
					<td>Stock Actual</td>
					<td>Precio Costo Total</td>
				</tr>';
		foreach ($db_data as $value){
			echo '<tr>';
			echo '	<td>'.$value['mcd_id'].'</td>
					<td>'.$value['codigo1'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.$value['mar_id'].'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.$value['lin_id'].'</td>
					<td>'.utf8_decode($value['nom_lin']).'</td>
					<td>'.$value['precio_costo'].'</td>
					<td>'.$value['precio_venta'].'</td>
					<td>'.$value['stock_inicial'].'</td>
					<td>'.$value['stock_pro'].'</td>
					<td>'.($value['precio_costo']*$value['stock_pro']).'</td>';					
			echo '</tr>';
			
		}
		echo '</table>';
    }
	
	public function rptproductoaperturaAction()
    {
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);		

        $this->view->acceso = $rs[0];

    }
	
	public function productoaperturaExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Stock_Valorado.xls");
		$db_data = $this->objDatos->sp_productoapertura_rpt($_POST);
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">REGISTRO DE PRODUCTOS</td></tr>';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">'.$_POST['txtpar5'].'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>ID</td>
					<td>Codigo 1</td>
					<td>Nombre</td>					
					<td>IdMarca</td>
					<td>Marca</td>
					<td>IdLinea</td>
					<td>Linea</td>
					<td>Precio Costo</td>
					<td>Precio Venta</td>
					<td>Stock Actual</td>
					<td>Precio Costo Total</td>
				</tr>';
		foreach ($db_data as $value){
			echo '<tr>';
			echo '	<td>'.$value['mcd_id'].'</td>
					<td>'.$value['codigo1'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.$value['mar_id'].'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.$value['lin_id'].'</td>
					<td>'.utf8_decode($value['nom_lin']).'</td>
					<td>'.$value['precio_costo'].'</td>
					<td>'.$value['precio_venta'].'</td>
					<td>'.$value['stock_pro'].'</td>
					<td>'.($value['precio_costo']*$value['stock_pro']).'</td>';					
			echo '</tr>';
			
		}
		echo '</table>';
    }
	
	public function rptresumenstockAction()
    {
		$rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 8 and usp_id = ".$perfil);
        $this->view->acceso = $rs[0];
    }
	
	public function rptresumenstockExcelAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		/*print date('Y',strtotime($_POST['txtpar2']))."<br>";
		echo ((int)date('Y',strtotime($_POST['txtpar2'])) - 1);
		exit();*/
		header("Content-Type: application/vnd.ms-excel");
		header("Expires: 0")	;
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Reporte_Productos_".$_POST['txtfile'].".xls");
		$db_data = $this->objDatos->sp_obtenerdatasql("select maestros_mercaderias.mcd_id, maestros_mercaderias.codigo1, maestros_mercaderias.nombre, 
		unidad_medida.descripcion as ume_nom, marcas.nombre as mar_nom, lineas.nombre as nom_lin, stock_producto.precio_costo
		from maestros_mercaderias 
			inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id 
			inner join unidad_medida on maestros_mercaderias.ume_id = unidad_medida.ume_id 
			inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id
			inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$_POST['txtpar1']."
		where mcd_id in (
			select detalle_compras.pro_id
			from detalle_compras	
				inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id and age_id = ".$_POST['txtpar1']." and movimientos_compras.eliminado = '0' and movimientos_compras.anulado='0' and detalle_compras.afecta_stock = 'S'
				inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id	
				inner join documentos on movimientos_compras.doc_id = documentos.doc_id	
			where movimientos_compras.fec_ven >= '".$_POST['txtpar2']."' and movimientos_compras.fec_ven <= '".$_POST['txtpar3']."'	
			UNION ALL	
			select  detalle_ventas.pro_id
			from detalle_ventas	
				inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id and age_id = ".$_POST['txtpar1']."  and movimientos_ventas.eliminado = '0' and  movimientos_ventas.anulado='0' and detalle_ventas.afecta_stock = 'S'	
				left join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id	
				inner join documentos on movimientos_ventas.doc_id = documentos.doc_id	
			where movimientos_ventas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_ventas.fec_ven <= '".$_POST['txtpar3']."'		
			UNION ALL		
			select detalle_altas.pro_id	
			from detalle_altas	
				inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id and age_id = ".$_POST['txtpar1']." and movimientos_altas.eliminado = '0' and movimientos_altas.anulado='0' and detalle_altas.afecta_stock = 'S'	
				inner join documentos on movimientos_altas.doc_id = documentos.doc_id	
				inner join tipo_movimiento on movimientos_altas.tmv_id = tipo_movimiento.tmv_id	
			where movimientos_altas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_altas.fec_ven <= '".$_POST['txtpar3']."'			
			UNION ALL	
			select  detalle_bajas.pro_id	
			from detalle_bajas	
				inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id and age_id = ".$_POST['txtpar1']."  and movimientos_bajas.eliminado = '0' and  movimientos_bajas.anulado='0' and detalle_bajas.afecta_stock = 'S'	
				inner join documentos on movimientos_bajas.doc_id = documentos.doc_id	
				inner join tipo_movimiento on movimientos_bajas.tmv_id = tipo_movimiento.tmv_id	
			where movimientos_bajas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_bajas.fec_ven <= '".$_POST['txtpar3']."'				
			UNION ALL		
			select detalle_notacompras.pro_id	
			from detalle_notacompras	
				inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id and age_id = ".$_POST['txtpar1']." and movimientos_notacompras.eliminado = '0' and movimientos_notacompras.anulado='0' and detalle_notacompras.afecta_stock = 'S'	
				inner join maestros_proveedores on movimientos_notacompras.prv_id = maestros_proveedores.prv_id	
				inner join documentos on movimientos_notacompras.doc_id = documentos.doc_id	
			where movimientos_notacompras.fec_ven >= '".$_POST['txtpar2']."' and movimientos_notacompras.fec_ven <= '".$_POST['txtpar3']."'					
			UNION ALL	
			select  detalle_notaventas.pro_id
			from detalle_notaventas	
				inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id and age_id = ".$_POST['txtpar1']."  and movimientos_notaventas.eliminado = '0' and  movimientos_notaventas.anulado='0' and detalle_notaventas.afecta_stock = 'S'	
				inner join maestros_clientes on movimientos_notaventas.cli_id = maestros_clientes.cli_id	
				inner join documentos on movimientos_notaventas.doc_id = documentos.doc_id	
			where movimientos_notaventas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_notaventas.fec_ven <= '".$_POST['txtpar3']."'						
			UNION ALL	
			select  detalle_trasladoing.pro_id
			from detalle_trasladoing	
				inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and age_id = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S'	
				inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
			where movimientos_trasladoing.fec_ven >= '".$_POST['txtpar2']."' and movimientos_trasladoing.fec_ven <= '".$_POST['txtpar3']."'	
			UNION ALL	
			select  detalle_trasladoing.pro_id
			from detalle_trasladoing	
				inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and tie_des = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S'	
				inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
			where movimientos_trasladoing.fec_ven >= '".$_POST['txtpar2']."' and movimientos_trasladoing.fec_ven <= '".$_POST['txtpar3']."'	
		)");
		echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
		echo '	<tr style=" font-size:15px; font-weight:bold"><td colspan="11">RESUMEN DE MOVIMIENTOS DE STOCK '.($_POST['txtpar2']==$_POST['txtpar3']?' EL DIA '.date('d/m/Y',strtotime($_POST['txtpar2'])):' DEL '.date('d/m/Y',strtotime($_POST['txtpar2'])).' AL '.date('d/m/Y',strtotime($_POST['txtpar2']))).'</td></tr>';
		echo '	<tr style=" font-size:14px; font-weight:bold">
					<td>Codigo 1</td>
					<td>Nombre</td>					
					<td>Marca</td>
					<td>Linea</td>
					<td>Unidad</td>
					<td>Stock Anterior</td>
					<td>Ingresos</td>
					<td>Egresos</td>
					<td>Stock Final</td>
					<td>Precio Costo</td>
					<td>Precio Costo Total</td>
				</tr>';
		foreach ($db_data as $value){
			
			$rs = $this->objDatos->sp_obtenerdatasql("select stock from stock_cierre where anio = '".((int)date('Y',strtotime($_POST['txtpar2'])) - 1)."' and age_id = ".$_POST['txtpar1']." and pro_id = ".$value['mcd_id']);
			$stock_inicial = $rs[0]['stock'];
			
			$db_antes = $this->objDatos->sp_obtenerdatasql("select SUM(ingreso) as ingreso, SUM(egreso) as egreso FROM (
				select SUM(cantidad) as ingreso, 0 as egreso
				from detalle_compras	
					inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id and age_id = ".$_POST['txtpar1']." and movimientos_compras.eliminado = '0' and movimientos_compras.anulado='0' and detalle_compras.afecta_stock = 'S' and detalle_compras.pro_id = ".$value['mcd_id']."
					inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id	
					inner join documentos on movimientos_compras.doc_id = documentos.doc_id	
				where movimientos_compras.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_compras.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_ventas	
					inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id and age_id = ".$_POST['txtpar1']."  and movimientos_ventas.eliminado = '0' and  movimientos_ventas.anulado='0' and detalle_ventas.afecta_stock = 'S' and detalle_ventas.pro_id = ".$value['mcd_id']."
					left join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id	
					inner join documentos on movimientos_ventas.doc_id = documentos.doc_id	
				where movimientos_ventas.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_ventas.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL		
				select SUM(cantidad) as ingreso, 0 as egreso
				from detalle_altas	
					inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id and age_id = ".$_POST['txtpar1']." and movimientos_altas.eliminado = '0' and movimientos_altas.anulado='0' and detalle_altas.afecta_stock = 'S' and detalle_altas.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_altas.doc_id = documentos.doc_id	
					inner join tipo_movimiento on movimientos_altas.tmv_id = tipo_movimiento.tmv_id	
				where movimientos_altas.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_altas.fec_ven < '".$_POST['txtpar3']."'		
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_bajas	
					inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id and age_id = ".$_POST['txtpar1']."  and movimientos_bajas.eliminado = '0' and  movimientos_bajas.anulado='0' and detalle_bajas.afecta_stock = 'S' and detalle_bajas.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_bajas.doc_id = documentos.doc_id	
					inner join tipo_movimiento on movimientos_bajas.tmv_id = tipo_movimiento.tmv_id	
				where movimientos_bajas.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_bajas.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL		
				select 0 as ingreso, SUM(cantidad) as egreso
				from detalle_notacompras	
					inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id and age_id = ".$_POST['txtpar1']." and movimientos_notacompras.eliminado = '0' and movimientos_notacompras.anulado='0' and detalle_notacompras.afecta_stock = 'S' and detalle_notacompras.pro_id = ".$value['mcd_id']."
					inner join maestros_proveedores on movimientos_notacompras.prv_id = maestros_proveedores.prv_id	
					inner join documentos on movimientos_notacompras.doc_id = documentos.doc_id	
				where movimientos_notacompras.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_notacompras.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL	
				select  SUM(cantidad) as ingreso, 0 as egreso
				from detalle_notaventas	
					inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id and age_id = ".$_POST['txtpar1']."  and movimientos_notaventas.eliminado = '0' and  movimientos_notaventas.anulado='0' and detalle_notaventas.afecta_stock = 'S' and detalle_notaventas.pro_id = ".$value['mcd_id']."
					inner join maestros_clientes on movimientos_notaventas.cli_id = maestros_clientes.cli_id	
					inner join documentos on movimientos_notaventas.doc_id = documentos.doc_id	
				where movimientos_notaventas.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_notaventas.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_trasladoing	
					inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and age_id = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S' and detalle_trasladoing.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
				where movimientos_trasladoing.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_trasladoing.fec_ven < '".$_POST['txtpar3']."'
				UNION ALL	
				select  SUM(cantidad) as ingreso, 0 as egreso
				from detalle_trasladoing	
					inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and tie_des = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S' and detalle_trasladoing.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
				where movimientos_trasladoing.fec_ven >= '".date('Y',strtotime($_POST['txtpar2']))."-01-01' and movimientos_trasladoing.fec_ven < '".$_POST['txtpar3']."'
			) TOTAL");
			
			$stock_inicial = $stock_inicial + $db_antes[0]['ingreso'] - $db_antes[0]['egreso'];
			
			$db_actual = $this->objDatos->sp_obtenerdatasql("select SUM(ingreso) as ingreso, SUM(egreso) as egreso FROM (
				select SUM(cantidad) as ingreso, 0 as egreso
				from detalle_compras	
					inner join movimientos_compras on detalle_compras.mco_id = movimientos_compras.mco_id and age_id = ".$_POST['txtpar1']." and movimientos_compras.eliminado = '0' and movimientos_compras.anulado='0' and detalle_compras.afecta_stock = 'S' and detalle_compras.pro_id = ".$value['mcd_id']."
					inner join maestros_proveedores on movimientos_compras.prv_id = maestros_proveedores.prv_id	
					inner join documentos on movimientos_compras.doc_id = documentos.doc_id	
				where movimientos_compras.fec_ven >= '".$_POST['txtpar2']."' and movimientos_compras.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_ventas	
					inner join movimientos_ventas on detalle_ventas.mve_id = movimientos_ventas.mve_id and age_id = ".$_POST['txtpar1']."  and movimientos_ventas.eliminado = '0' and  movimientos_ventas.anulado='0' and detalle_ventas.afecta_stock = 'S' and detalle_ventas.pro_id = ".$value['mcd_id']."
					left join maestros_clientes on movimientos_ventas.cli_id = maestros_clientes.cli_id	
					inner join documentos on movimientos_ventas.doc_id = documentos.doc_id	
				where movimientos_ventas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_ventas.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL		
				select SUM(cantidad) as ingreso, 0 as egreso
				from detalle_altas	
					inner join movimientos_altas on detalle_altas.mal_id = movimientos_altas.mal_id and age_id = ".$_POST['txtpar1']." and movimientos_altas.eliminado = '0' and movimientos_altas.anulado='0' and detalle_altas.afecta_stock = 'S' and detalle_altas.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_altas.doc_id = documentos.doc_id	
					inner join tipo_movimiento on movimientos_altas.tmv_id = tipo_movimiento.tmv_id	
				where movimientos_altas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_altas.fec_ven <= '".$_POST['txtpar3']."'		
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_bajas	
					inner join movimientos_bajas on detalle_bajas.mba_id = movimientos_bajas.mba_id and age_id = ".$_POST['txtpar1']."  and movimientos_bajas.eliminado = '0' and  movimientos_bajas.anulado='0' and detalle_bajas.afecta_stock = 'S' and detalle_bajas.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_bajas.doc_id = documentos.doc_id	
					inner join tipo_movimiento on movimientos_bajas.tmv_id = tipo_movimiento.tmv_id	
				where movimientos_bajas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_bajas.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL		
				select 0 as ingreso, SUM(cantidad) as egreso
				from detalle_notacompras	
					inner join movimientos_notacompras on detalle_notacompras.nco_id = movimientos_notacompras.nco_id and age_id = ".$_POST['txtpar1']." and movimientos_notacompras.eliminado = '0' and movimientos_notacompras.anulado='0' and detalle_notacompras.afecta_stock = 'S' and detalle_notacompras.pro_id = ".$value['mcd_id']."
					inner join maestros_proveedores on movimientos_notacompras.prv_id = maestros_proveedores.prv_id	
					inner join documentos on movimientos_notacompras.doc_id = documentos.doc_id	
				where movimientos_notacompras.fec_ven >= '".$_POST['txtpar2']."' and movimientos_notacompras.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL	
				select  SUM(cantidad) as ingreso, 0 as egreso
				from detalle_notaventas	
					inner join movimientos_notaventas on detalle_notaventas.nve_id = movimientos_notaventas.nve_id and age_id = ".$_POST['txtpar1']."  and movimientos_notaventas.eliminado = '0' and  movimientos_notaventas.anulado='0' and detalle_notaventas.afecta_stock = 'S' and detalle_notaventas.pro_id = ".$value['mcd_id']."
					inner join maestros_clientes on movimientos_notaventas.cli_id = maestros_clientes.cli_id	
					inner join documentos on movimientos_notaventas.doc_id = documentos.doc_id	
				where movimientos_notaventas.fec_ven >= '".$_POST['txtpar2']."' and movimientos_notaventas.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL	
				select  0 as ingreso, SUM(cantidad) as egreso
				from detalle_trasladoing	
					inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and age_id = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S' and detalle_trasladoing.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
				where movimientos_trasladoing.fec_ven >= '".$_POST['txtpar2']."' and movimientos_trasladoing.fec_ven <= '".$_POST['txtpar3']."'
				UNION ALL	
				select  SUM(cantidad) as ingreso, 0 as egreso
				from detalle_trasladoing	
					inner join movimientos_trasladoing on detalle_trasladoing.tin_id = movimientos_trasladoing.tin_id and tie_des = ".$_POST['txtpar1']."  and movimientos_trasladoing.eliminado = '0' and  movimientos_trasladoing.anulado='0' and detalle_trasladoing.afecta_stock = 'S' and detalle_trasladoing.pro_id = ".$value['mcd_id']."
					inner join documentos on movimientos_trasladoing.doc_id = documentos.doc_id	
				where movimientos_trasladoing.fec_ven >= '".$_POST['txtpar2']."' and movimientos_trasladoing.fec_ven <= '".$_POST['txtpar3']."'
			) TOTAL");
			
			echo '<tr>';
			echo '	<td>'.$value['codigo1'].'</td>
					<td>'.utf8_decode($value['nombre']).'</td>
					<td>'.utf8_decode($value['mar_nom']).'</td>
					<td>'.utf8_decode($value['nom_lin']).'</td>
					<td>'.utf8_decode($value['ume_nom']).'</td>
					<td>'.$stock_inicial.'</td>
					<td>'.$db_actual[0]['ingreso'].'</td>
					<td>'.$db_actual[0]['egreso'].'</td>
					<td>'.($stock_inicial + $db_actual[0]['ingreso'] - $db_actual[0]['egreso']).'</td>
					<td>'.($value['precio_costo']).'</td>
					<td>'.($stock_inicial + $db_actual[0]['ingreso'] - $db_actual[0]['egreso'])*($value['precio_costo']).'</td>';
			echo '</tr>';
			
		}
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

						echo '<td>'.utf8_decode($value['nombre_cliente']).'</td>';

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

    
    public function saldoventasListaImpresionAction()
    {
        

        ini_set('memory_limit', '512M'); //Raise to 512 MB 
        
        ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

        //$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo'],1);
        $pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Saldos por Cobrar',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

        $col_names = array(

                'fec_mov' => 'Fecha',

                'doc_n' => 'Documento',

                'nombre_cliente' => 'Cliente',

                'total_venta' => 'Total Venta',

                'saldo' => 'Total Venta',

                'moneda' => 'Moneda',

                'anulado' => 'X',

        );



        $options = array(

                'width' => 550,

                'cols' => array(

                        'fec_mov' => array('justification'=>'center'),

                        'doc_n' => array('justification'=>'left'),

                        'nombre_cliente' => array('justification'=>'left'),

                        'total_venta' => array('justification'=>'left'),

                        'saldo' => array('justification'=>'right'),

                        'moneda' => array('justification'=>'left'),

                        'anulado' => array('justification'=>'left'),

                )

        );

        $_POST['age_id'] = $this->sessName->se_age_id;

        $_POST['limit'] = -1;

        $_POST['sort']=$_POST['txtsort'];

        $_POST['dir']=$_POST['txtdir'];

        $db_data = $this->objDatos->sp_saldoventas_lista($_POST,2);

        foreach($db_data as $key => $row){

            if(is_array($row)){
                foreach($row as $k => $d){
                    $db_data[$key][$k] = utf8_decode($d);
                }
            }else{
                $db_data[$key] = $row;
            }                    
        }

        $pdf->ezTable($db_data, $col_names, 'Listado de Saldos por Cobrar', $options, $_POST['txtfiltro']);		
        
        /*
        $pdf->ezText("CANTIDAD DE DOCUMENTOS : ".$pdf->con1);

        $pdf->ezText("CANTIDAD DE DOCUMENTOS EN SOLES : ".$pdf->con2);

        $pdf->ezText("CANTIDAD DE DOCUMENTOS EN DOLARES : ".$pdf->con3);

        $pdf->ezText("TOTAL VENTA EN SOLES : ".$pdf->sum2);

        $pdf->ezText("TOTAL VENTA EN DOLARES : ".$pdf->sum3);
        */
        $pdf->ezStream();

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

	
    public function saldocomprasListaImpresionAction()
    {
        

        ini_set('memory_limit', '512M'); //Raise to 512 MB 
        
        ini_set('max_execution_time', '10000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

        //$pdf = new ZendCPdf_Cezpdf('a4', 'portrait',$_POST['txttitulo'],1);
        $pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Saldos por Pagar',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

        $col_names = array(

                'fec_mov' => 'Fecha',

                'doc_n' => 'Documento',

                'nombre_cliente' => 'Cliente',

                'total_compra' => 'Total Compra',

                'saldo' => 'Saldo',

                'moneda' => 'Moneda',

                'anulado' => 'X',

        );



        $options = array(

                'width' => 550,

                'cols' => array(

                        'fec_mov' => array('justification'=>'center'),

                        'doc_n' => array('justification'=>'left'),

                        'nombre_cliente' => array('justification'=>'left'),

                        'total_compra' => array('justification'=>'left'),

                        'saldo' => array('justification'=>'right'),

                        'moneda' => array('justification'=>'left'),

                        'anulado' => array('justification'=>'left'),

                )

        );

        $_POST['age_id'] = $this->sessName->se_age_id;

        $_POST['limit'] = -1;

        $_POST['sort']=$_POST['txtsort'];

        $_POST['dir']=$_POST['txtdir'];

        $db_data = $this->objDatos->sp_saldocompras_lista($_POST,2);

        foreach($db_data as $key => $row){

            if(is_array($row)){
                foreach($row as $k => $d){
                    $db_data[$key][$k] = utf8_decode($d);
                }
            }else{
                $db_data[$key] = $row;
            }                    
        }

        $pdf->ezTable($db_data, $col_names, 'Listado de Saldos por Pagar', $options, $_POST['txtfiltro']);		
        
        /*
        $pdf->ezText("CANTIDAD DE DOCUMENTOS : ".$pdf->con1);

        $pdf->ezText("CANTIDAD DE DOCUMENTOS EN SOLES : ".$pdf->con2);

        $pdf->ezText("CANTIDAD DE DOCUMENTOS EN DOLARES : ".$pdf->con3);

        $pdf->ezText("TOTAL VENTA EN SOLES : ".$pdf->sum2);

        $pdf->ezText("TOTAL VENTA EN DOLARES : ".$pdf->sum3);
        */
        $pdf->ezStream();

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

	

	public function stockminimoImpresionAction()

    {

		ini_set('memory_limit', '512M'); //Raise to 512 MB 

		ini_set('max_execution_time', '100000'); //Raise to 512 MB

        $this->_helper->viewRenderer->setNoRender();

		

		//$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Stock Minimo');
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Stock Mínimo',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);

		

		$col_names = array(

			'codigo1' => 'Codigo',

			'nombre' => 'Producto',

			'marca' => 'Marca',

			'linea' => 'Linea',

			'stock' => 'Stock',

			'stock_minimo' => 'Stock Minimo',

		);

		

		$options = array(

			'width' => 550,

			'cols' => array(

				'codigo1' => array('justification'=>'center'),

				'nombre' => array('justification'=>'left'),

				'marca' => array('justification'=>'left'),

				'linea' => array('justification'=>'left'),

				'stock' => array('justification'=>'right'),

				'stock_minimo' => array('justification'=>'right'),

			)

		);

		$db_data = $this->objDatos->sp_obtenerdatasql("select maestros_mercaderias.codigo1, maestros_mercaderias.nombre, marcas.nombre as marca, lineas.nombre as linea, stock_producto.stock_inicial + stock_producto.stock as stock, stock_producto.stock_minimo

from maestros_mercaderias

	inner join stock_producto on maestros_mercaderias.mcd_id = stock_producto.pro_id and stock_producto.age_id = ".$this->sessName->se_age_id."

	inner join marcas on maestros_mercaderias.mar_id = marcas.mar_id

	inner join lineas on maestros_mercaderias.lin_id = lineas.lin_id

where stock_producto.stock_inicial + stock_producto.stock <= case when stock_producto.stock_minimo is null then 0 else stock_producto.stock_minimo  end

order by 5");

		//print_r($db_data);

		$pdf->ezTable($db_data, $col_names, 'Reportes de Stock Minimos', $options,'Tienda : '.$this->sessName->se_tienda);

		$pdf->ezStream();

    }

    function comprasDetalladoExcelAction(){
        
        ini_set('memory_limit', '512M'); //Raise to 512 MB
        ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
        
        header("Content-Type: application/vnd.ms-excel");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename=Reporte_Compras_Detallado_".$_POST['txtfile'].".xls");
        
        $data = $this->objDatos->sp_compras_detallado($_POST);
        
        echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
        switch (date('m',strtotime($_POST['fec_ini']))) {
            case '01': $monthName="ENERO"; break;
            case '02': $monthName="FEBRERO"; break;
            case '03': $monthName="MARZO"; break;
            case '04': $monthName="ABRIL";  break;
            case '05': $monthName="MAYO"; break;
            case '06': $monthName="JUNIO"; break;
            case '07': $monthName="JULIO"; break;
            case '08': $monthName="AGOSTO"; break;
            case '09': $monthName="SEPTIEMBRE"; break;
            case '10': $monthName="OCTUBRE"; break;
            case '11': $monthName="NOVIEMBRE"; break;
            case '12': $monthName="DICIEMBRE"; break;
        }
        $d1 = date('d',strtotime($_POST['fec_ini']));
        $m1 = date('m',strtotime($_POST['fec_ini']));
        $y1 = date('Y',strtotime($_POST['fec_ini']));
        $fecha1 = "$d1/$m1/$y1";
        
        $d2 = date('d',strtotime($_POST['fec_fin']));
        $m2 = date('m',strtotime($_POST['fec_fin']));
        $y2 = date('Y',strtotime($_POST['fec_fin']));
        $fecha2 = "$d2/$m2/$y2";
        
        echo '	<tr style="font-size:15px; font-weight:bold"><td colspan="15">REGISTRO DE COMPRAS CORRESPONDIENTE DEL '.$fecha1.' AL '.$fecha2.'</td></tr>';
        echo '  <tr style="font-size:14px; font-weight:bold">
                    <td>DOC</td>
                    <td>NUMERO</td>
                    <td>FECHA</td>
                    <td>NOMBRE O RAZON SOCIAL</td>
                    <td>CANTIDAD</td>
                    <td>CODIGO1</td>
		    <td>PRODUCTO</td>
		    <td>MARCA</td>
                    <td colspan="3">COMPRA EN DOLARES</td>
                    <td></td>
                    <td colspan="3">COMPRA EN SOLES</td>
                </tr>
                <tr style="font-size:13px; font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
		    <td></td>
                    <td></td>
                    <td>PRECIO</td>
                    <td>DESCUENTO</td>
                    <td>TOTAL</td>
                    <td>TIPO CAMBIO</td>
                    <td>PRECIO</td>
                    <td>DESCUENTO</td>
                    <td>TOTAL</td>
                </tr>';
        
        $doc_id = "";
        $mco_id = "";
        $tot_so = 0;
        $tot_do = 0;
        
        foreach($data as $k => $d){
            if($doc_id != $d['doc_id']){
            echo '
                <tr style="font-size:13px; font-weight:bold">
                    <td colspan="13">'.$d['descripcion'].'</td>
                </tr>';
                $doc_id = $d['doc_id'];
            }
            if($d['anulado'] == 1){
                echo '<tr style="font-size:13px;"><td colspan="15" style="text-align:center">***ANULADO***</td></tr>';
            }else{
                echo '
                    <tr style="font-size:13px;">
                        <td></td>';
                        if($mco_id != $d['mco_id']){
                        echo '
                            <td style="text-align:left;"><strong>'.$d['doc_n'].'</strong></td>
                            <td style="text-align:left;"><strong>'.$d['fec_ven'].'</strong></td>
                            <td style="text-align:left;"><strong>'.utf8_decode($d['nombre']).'</strong></td>';

                            $mco_id = $d['mco_id'];
                        }else{
                            echo '<td></td><td></td><td></td>';
                        }

                        echo '
                        <td>'.$d['cantidad'].'</td>
                        <td style="text-align:left;">'.$d['codigo1'].'</td>
			<td style="text-align:left;">'.utf8_decode($d['producto']).'</td>
			<td style="text-align:left;">'.utf8_decode($d['marca']).'</td>';

                        if($d['mon_id']=='1'){
                            echo'
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>'.number_format($d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['precio_compra'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'], 2).'</td>
                            <td>'.number_format($d['total'], 2).'</td>';
                            $tot_so+=$d['total'];
                        }else{
                            echo'
                            <td>'.number_format($d['precio_compra'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['total'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['precio_compra'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'], 2).'</td>
                            <td>'.number_format($d['total'], 2).'</td>';
                            $tot_do+=$d['total'];
                        }
                    '</tr>';
            }
        }
        /*echo '
            <tr style="font-size:14px; font-weight:bold">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$tot_so.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$tot_do.'</td>
            </tr>';*/

        echo '</table>';
        
    }
    
    function ventasDetalladoExcelAction(){
        
        ini_set('memory_limit', '512M'); //Raise to 512 MB
        ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
        
        header("Content-Type: application/vnd.ms-excel");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename=Reporte_Ventas_Detallado_".$_POST['txtfile'].".xls");
        
        $data = $this->objDatos->sp_ventas_detallado($_POST);
        
        echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style=" font-size:12px;" id="Exportar_a_Excel">';
        switch (date('m',strtotime($_POST['fec_ini']))) {
            case '01': $monthName="ENERO"; break;
            case '02': $monthName="FEBRERO"; break;
            case '03': $monthName="MARZO"; break;
            case '04': $monthName="ABRIL";  break;
            case '05': $monthName="MAYO"; break;
            case '06': $monthName="JUNIO"; break;
            case '07': $monthName="JULIO"; break;
            case '08': $monthName="AGOSTO"; break;
            case '09': $monthName="SEPTIEMBRE"; break;
            case '10': $monthName="OCTUBRE"; break;
            case '11': $monthName="NOVIEMBRE"; break;
            case '12': $monthName="DICIEMBRE"; break;
        }
        $d1 = date('d',strtotime($_POST['fec_ini']));
        $m1 = date('m',strtotime($_POST['fec_ini']));
        $y1 = date('Y',strtotime($_POST['fec_ini']));
        $fecha1 = "$d1/$m1/$y1";
        
        $d2 = date('d',strtotime($_POST['fec_fin']));
        $m2 = date('m',strtotime($_POST['fec_fin']));
        $y2 = date('Y',strtotime($_POST['fec_fin']));
        $fecha2 = "$d2/$m2/$y2";
        
        echo '	<tr style="font-size:15px; font-weight:bold"><td colspan="15">REGISTRO DE VENTAS CORRESPONDIENTE DEL '.$fecha1.' AL '.$fecha2.'</td></tr>';
        echo '  <tr style="font-size:14px; font-weight:bold">
                    <td>DOC</td>
                    <td>NUMERO</td>
                    <td>FECHA</td>
                    <td>NOMBRE O RAZON SOCIAL</td>
                    <td>CANTIDAD</td>
                    <td>CODIGO1</td>
                    <td>PRODUCTO</td>
                    <td>MARCA</td>
                    <td colspan="3">COMPRA EN DOLARES</td>
                    <td></td>
                    <td colspan="3">COMPRA EN SOLES</td>
                </tr>
                <tr style="font-size:13px; font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>PRECIO</td>
                    <td>DESCUENTO</td>
                    <td>TOTAL</td>
                    <td>TIPO CAMBIO</td>
                    <td>PRECIO</td>
                    <td>DESCUENTO</td>
                    <td>TOTAL</td>
                </tr>';
        
        $doc_id = "";
        $mve_id = "";
        $tot_so = 0;
        $tot_do = 0;
        
        foreach($data as $k => $d){
            if($doc_id != $d['doc_id']){
            echo '
                <tr style="font-size:13px; font-weight:bold">
                    <td colspan="13">'.$d['descripcion'].'</td>
                </tr>';
                $doc_id = $d['doc_id'];
            }
            
            if($d['anulado'] == 1){
                echo '<tr style="font-size:13px;"><td colspan="15" style="text-align:center">***ANULADO***</td></tr>';
            }else{            
                echo '
                    <tr style="font-size:13px;">
                        <td></td>';
                        if($mve_id != $d['mve_id']){
                        echo '
                            <td style="text-align:left;"><strong>'.$d['doc_n'].'</strong></td>
                            <td style="text-align:left;"><strong>'.$d['fec_ven'].'</strong></td>
                            <td style="text-align:left;"><strong>'.utf8_decode($d['cliente']).'</strong></td>';

                            $mve_id = $d['mve_id'];
                        }else{
                            echo '<td></td><td></td><td></td>';
                        }

                        echo '
                        <td>'.$d['cantidad'].'</td>
                        <td style="text-align:left;">'.$d['codigo1'].'</td>
			<td style="text-align:left;">'.utf8_decode($d['producto']).'</td>
			<td style="text-align:left;">'.utf8_decode($d['marca']).'</td>';

                        if($d['mon_id']=='1'){
                            echo'
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>'.number_format($d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['precio_venta'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'], 2).'</td>
                            <td>'.number_format($d['total'], 2).'</td>';
                            $tot_so+=$d['total'];
                        }else{
                            echo'
                            <td>'.number_format($d['precio_venta'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['total'] * $d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['tipo_cambio'], 2).'</td>
                            <td>'.number_format($d['precio_compra'], 2).'</td>
                            <td>'.number_format($d['valor_descuento'], 2).'</td>
                            <td>'.number_format($d['total'], 2).'</td>';
                            $tot_do+=$d['total'];
                        }
                    '</tr>';
            }
        }
        /*echo '
            <tr style="font-size:14px; font-weight:bold">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$tot_so.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$tot_do.'</td>
            </tr>';*/

        echo '</table>';
        
    }
    //FORMATO Nota Credito Ventas
    public function formatoNotaventasAction(){
        $this->_helper->viewRenderer->setNoRender();

        $this->objDatos->sp_ejecutarsql("delete from formato_cabecera_nve where usr_id = ".$this->sessName->se_usr_id.";
            insert into formato_cabecera_nve 
                select (case when (nve.cli_id = 0) then nve.cliente else cli.nombre end) as nombre_cliente,
                        doc.descripcion as descripcion_documento,
                        nve.nve_id,
                        nve.codigo,
                        nve.cli_id,
                        nve.tipo_ingreso,
                        nve.doc_id,
                        nve.doc_n,
                        nve.n_guia,
                        nve.cpa_id,
                        nve.mon_id,
                        nve.valor_venta,
                        nve.impuesto_igv,
                        nve.total_venta, 
                        DATE_FORMAT(nve.fec_ven, '%d/%m/%Y') as fec_ven, 
                        DATE_FORMAT(nve.fec_mov, '%d/%m/%Y') as fec_mov, 
                        monedas.nombre as moneda, 
                        nve.anulado, 
                        case when nve.cli_id = 0 then '' else cli.codigo end as cli_codigo, 
                        case when nve.cli_id = 0 then nve.cli_ruc else cli.ruc end as ruc , 
                        nve.eliminado, 
                        nve.afecta, 
                        nve.formato,
                        nve.observacion,
                        cli.direccion,
                        nve.fec_ven as fec_ori,
                        cpa.descripcion condicion,
                        nve.saldo,
                        cpa.letras,
                        cpa.dias, tie.nombre as agencia, concat(usu.nombres,' ',usu.apellidos) as usuario, usu.usr_id, nve.age_id 
                        from movimientos_notaventas nve
                        inner join tiendas tie on nve.age_id = tie.tie_id
                        left join usuarios usu on usu.usr_id = ".$this->sessName->se_usr_id."
                        left join maestros_clientes cli on cli.cli_id=nve.cli_id
                        inner join documentos doc on doc.doc_id=nve.doc_id
                        inner join monedas on nve.mon_id = monedas.mon_id 
                        inner join condiciones_pago cpa on nve.cpa_id = cpa.cpa_id
                    where nve.nve_id=".$_POST['txtpar2']);

$this->objDatos->sp_ejecutarsql("delete from formato_detalle_nve;
                                    insert into formato_detalle_nve 
                                        select dnve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca 
                                            from detalle_notaventas dnve 
                                            inner join maestros_mercaderias mcd on dnve.pro_id = mcd.mcd_id 
                                            inner join marcas on mcd.mar_id = marcas.mar_id 
                                            where dnve.nve_id =".$_POST['txtpar2']);

		if($_POST['tipo'] == '1'){
			if($rsData[0]['doc_id']==2){
				$_SESSION['tamano'] = '590:450:';
			}
			if($rsData[0]['doc_id']==3){
				$_SESSION['tamano'] = '570:450:';
			}
		}else{
			$_SESSION['tamano'] = 'A4';
		}

		$pdf = new Zend_Pdf();
		$style = new Zend_Pdf_Style();
		$style->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0)); 
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA); 
		$style->setFont($font,12);
		

		$rsDataEmpresa = $this->objDatos->sp_obtenerdatasql("select * from tiendas where tie_id = ".$this->sessName->se_age_id);

                
		//Jalando NotaVenta

		$_POST['age_id'] = $this->sessName->se_age_id;
		$_POST['campo'] = 'nve_id';
		$_POST['query'] = $_POST['txtpar2'];
		$_POST['nve_id'] = $_POST['txtpar2'];
		$_POST['tipo'] = $_POST['txtpar1'];

		$rsData = $this->objDatos->sp_notaventas_lista($_POST,2);
		
		if($_POST['tipo'] == '1'){
			if($rsData[0]['doc_id']==5){	//NOTA CREDITO
				$pdf->pages[] = ($page = $pdf->newPage('590:450:'));//'624:842:'
				$page->setStyle($style);
				$page->setFont($font, 7);
				$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
				$page->drawText("NC/.".$rsData[0]['doc_n'], 390 ,418);
				$page->drawText(($rsData[0]['nombre_cliente']), 90 ,402,"UTF-8");
				$page->drawText((trim($rsData[0]['direccion']).' - '.trim($rsData[0]['ubigeo'])), 90 ,392,"UTF-8");
				$page->drawText($rsData[0]['ruc'], 90 ,382);
				$page->drawText($rsData[0]['n_guia'], 320 ,382);
				$page->setFont($font, 7);
				$page->drawText(date('d           m              Y',strtotime($rsData[0]['fec_ori'])), 490 , 382);				
                                
				$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dnve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_notaventas dnve inner join maestros_mercaderias mcd on dnve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dnve.nve_id = ".$_POST['nve_id']);   

				$y=362;				

				foreach($rsDetalle as $solo){	
					$y = $y - 8;		
					$page->setFont($font, 7);			
					$lon_tot = $this->getTextWidth(utf8_decode($solo['cantidad']), $page->getFont(), $page->getFontSize());
					$page->drawText(utf8_decode($solo['cantidad']), 85 - $lon_tot - 30 ,$y)  //45
						->drawText(($solo['codigo1']), 80 ,$y,"UTF-8")			
						->drawText(($solo['producto']), 165 ,$y,"UTF-8")
						->drawText(($solo['marca']), 420 ,$y,"UTF-8");												  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['precio_venta']), $page->getFont(), $page->getFontSize());

					$page->drawText(utf8_decode($solo['precio_venta']), 540 - $lon_tot - 30,$y); //490						  

					$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					

					$page->drawText(utf8_decode($solo['total']), 615 - $lon_tot - 30,$y); //550
				}

				$y=225;

				$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());

				$page->setFont($font, 7)
					->drawText($rsData[0]['valor_venta'] , 225 ,235)
					->drawText($rsData[0]['impuesto_igv'] , 440 ,235)
					->drawText($rsData[0]['total_venta'], 615 - $lon_tot - 30,235) //550
					->drawText($this->num2letras($rsData[0]['total_venta'],false,true,$rsData[0]['mon_id']), 40 ,220);
			}
		}else{
			$pdf->pages[] = ($page = $pdf->newPage('A4'));

			$page->setStyle($style);
			$page->setFont($font, 12);
			$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
			$page->drawText(($rsDataEmpresa[0]['nombre'].'-'.$rsDataEmpresa[0]['ruc']), 40 ,800,"UTF-8");		
			$page->drawText(($rsDataEmpresa[0]['direccion']), 40 ,780,"UTF-8");		
			$page->drawText($rsDataEmpresa[0]['telefono'], 40 ,760,"UTF-8");	
			$page->setFont($font, 12);
			$page->drawText($rsData[0]['doc_n'], 450 ,750);					
			$page->drawText(($rsData[0]['nombre_cliente']), 40 ,700,"UTF-8");			
			$page->drawText(($rsData[0]['direccion']), 40 ,680,"UTF-8");
			$page->drawText($rsData[0]['ruc'], 40 ,660);
			$page->drawText($rsData[0]['n_guia'], 300 ,660);
			$page->drawText(date('d    m    Y',strtotime($rsData[0]['fec_ori'])), 450 ,660);				

			$rsDetalle = $this->objDatos->sp_obtenerdatasql("select dnve.*, mcd.codigo1, mcd.nombre as producto, marcas.nombre as marca from detalle_notaventas dnve inner join maestros_mercaderias mcd on dnve.pro_id = mcd.mcd_id inner join marcas on mcd.mar_id = marcas.mar_id where dnve.nve_id = ".$_POST['nve_id']);   

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

				$page->drawText(utf8_decode($solo['valor_descuento']), 480 - $lon_tot,$y);						  

				$lon_tot = $this->getTextWidth(utf8_decode($solo['total']), $page->getFont(), $page->getFontSize());					

				$page->drawText(utf8_decode($solo['total']), 550 - $lon_tot,$y);
			}

			$y=80;

			$lon_tot = $this->getTextWidth($rsData[0]['total_venta'], $page->getFont(), $page->getFontSize());

			$page->setFont($font, 12)
				->drawText($rsData[0]['valor_venta'] , 200 ,$y)
				->drawText($rsData[0]['impuesto_igv'] , 400 ,$y)
				->drawText($rsData[0]['total_venta'], 550 - $lon_tot ,$y)
				->drawText($this->num2letras($rsData[0]['total_venta'],false,true,$rsData[0]['mon_id']), 50 ,$y - 40);
		}

		header("content-type: application/pdf");
		echo "<script type='text/javascript'>window.print();</script>";
		print($pdf->render());
    }
    
}