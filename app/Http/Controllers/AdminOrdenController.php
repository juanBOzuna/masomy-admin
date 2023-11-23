<?php
namespace App\Http\Controllers;

use App\Models\OrdenModel;
use Session;
use Request;
use DB;
use CRUDBooster;

class AdminOrdenController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function cbInit()
	{

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "20";
		$this->orderby = "id,desc";
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = true;
		$this->button_action_style = "button_icon";
		$this->button_add = false;
		$this->button_edit = false;
		$this->button_delete = false;
		$this->button_detail = true;
		$this->button_show = true;
		$this->button_filter = true;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "orden";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label" => "Precio Total", "name" => "total_price"];
		$this->col[] = ["label" => "Descuento Total", "name" => "total_disccount"];
		$this->col[] = ["label" => "Link de pago", "name" => "payment_link_id", "join" => "payment_links,link"];
		$this->col[] = ["label" => "Estado", "name" => "status"];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Precio Total', 'name' => 'total_price', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Descuento Total', 'name' => 'total_disccount', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Link de pago', 'name' => 'payment_link_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'payment_links,link'];
		$this->form[] = ['label' => 'Estado de la orden', 'name' => 'status', 'type' => 'textarea', 'validation' => 'required|string|min:5|max:5000', 'width' => 'col-sm-10'];
		# END FORM DO NOT REMOVE THIS LINE

		# OLD START FORM
		//$this->form = [];
		//$this->form[] = ["label"=>"Total Price","name"=>"total_price","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
		//$this->form[] = ["label"=>"Total Disccount","name"=>"total_disccount","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
		//$this->form[] = ["label"=>"Payment Link Id","name"=>"payment_link_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"payment_link,id"];
		//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
		# OLD END FORM

		/* 
																	   | ---------------------------------------------------------------------- 
																	   | Sub Module
																	   | ----------------------------------------------------------------------     
																	   | @label          = Label of action 
																	   | @path           = Path of sub module
																	   | @foreign_key 	  = foreign key of sub table/module
																	   | @button_color   = Bootstrap Class (primary,success,warning,danger)
																	   | @button_icon    = Font Awesome Class  
																	   | @parent_columns = Sparate with comma, e.g : name,created_at
																	   | 
																	   */
		$this->sub_module = array();


		/* 
																 | ---------------------------------------------------------------------- 
																 | Add More Action Button / Menu
																 | ----------------------------------------------------------------------     
																 | @label       = Label of action 
																 | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
																 | @icon        = Font awesome class icon. e.g : fa fa-bars
																 | @color 	   = Default is primary. (primary, warning, succecss, info)     
																 | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
																 | 
																 */
		$this->addaction = array();

		$statusPENDIENTE = OrdenModel::PENDIENTE;
		$statusEN_PROCESO = OrdenModel::EN_PROCESO;
		$statusEN_ENVIO = OrdenModel::EN_ENVIO;

		// const PENDIENTE = "Pendiente";
		// const EN_PROCESO = "En Proceso";
		// const EN_ENVIO = "En Envio";
		// const ENTREGADO = "Entregado";

		$this->addaction[] = [
			'label' => 'Poner en Proceso',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-proceso/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] == '" . $statusPENDIENTE . "'",
			'color' => 'success'
			//                'showIf' => "[status] != $entregado && [status] != $cancelada &&  [status] != $incompleta"
		];
		$this->addaction[] = [
			'label' => 'Cancelar',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-cancelado/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] == '" . $statusPENDIENTE . "'",
			'color' => 'danger'
			//                'showIf' => "[status] != $entregado && [status] != $cancelada &&  [status] != $incompleta"
		];
		$this->addaction[] = [
			'label' => 'Poner en envio',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-envio/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] == '" . $statusEN_PROCESO . "'",
			'color' => 'success'
			//                'showIf' => "[status] != $entregado && [status] != $cancelada &&  [status] != $incompleta"
		];
		$this->addaction[] = [
			'label' => 'Entregado',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-entregado/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] == '" . $statusEN_ENVIO . "'",
			'color' => 'success'
			//                'showIf' => "[status] != $entregado && [status] != $cancelada &&  [status] != $incompleta"
		];


		/* 
																 | ---------------------------------------------------------------------- 
																 | Add More Button Selected
																 | ----------------------------------------------------------------------     
																 | @label       = Label of action 
																 | @icon 	   = Icon from fontawesome
																 | @name 	   = Name of button 
																 | Then about the action, you should code at actionButtonSelected method 
																 | 
																 */
		$this->button_selected = array();


		/* 
																 | ---------------------------------------------------------------------- 
																 | Add alert message to this module at overheader
																 | ----------------------------------------------------------------------     
																 | @message = Text of message 
																 | @type    = warning,success,danger,info        
																 | 
																 */
		$this->alert = array();



		/* 
																 | ---------------------------------------------------------------------- 
																 | Add more button to header button 
																 | ----------------------------------------------------------------------     
																 | @label = Name of button 
																 | @url   = URL Target
																 | @icon  = Icon from Awesome.
																 | 
																 */
		$this->index_button = array();



		/* 
																 | ---------------------------------------------------------------------- 
																 | Customize Table Row Color
																 | ----------------------------------------------------------------------     
																 | @condition = If condition. You may use field alias. E.g : [id] == 1
																 | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
																 | 
																 */
		$this->table_row_color = array();
		$statusCANCELADO = OrdenModel::CANCELADO;
		$statusENTREGADO = OrdenModel::ENTREGADO;
		$this->table_row_color[] = ['condition' => "[status] == '" . $statusCANCELADO . "'  ", "color" => "danger"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $statusEN_PROCESO . "'  ", "color" => "info"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $statusEN_ENVIO . "'  ", "color" => "info"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $statusENTREGADO . "'  ", "color" => "success"];

		/*
																 | ---------------------------------------------------------------------- 
																 | You may use this bellow array to add statistic at dashboard 
																 | ---------------------------------------------------------------------- 
																 | @label, @count, @icon, @color 
																 |
																 */
		$this->index_statistic = array();



		/*
																 | ---------------------------------------------------------------------- 
																 | Add javascript at body 
																 | ---------------------------------------------------------------------- 
																 | javascript code in the variable 
																 | $this->script_js = "function() { ... }";
																 |
																 */
		$this->script_js = NULL;


		/*
															  | ---------------------------------------------------------------------- 
															  | Include HTML Code before index table 
															  | ---------------------------------------------------------------------- 
															  | html code to display it before index table
															  | $this->pre_index_html = "<p>test</p>";
															  |
															  */
		$this->pre_index_html = null;



		/*
																 | ---------------------------------------------------------------------- 
																 | Include HTML Code after index table 
																 | ---------------------------------------------------------------------- 
																 | html code to display it after index table
																 | $this->post_index_html = "<p>test</p>";
																 |
																 */
		$this->post_index_html = null;



		/*
																 | ---------------------------------------------------------------------- 
																 | Include Javascript File 
																 | ---------------------------------------------------------------------- 
																 | URL of your javascript each array 
																 | $this->load_js[] = asset("myfile.js");
																 |
																 */
		$this->load_js = array();



		/*
																 | ---------------------------------------------------------------------- 
																 | Add css style at body 
																 | ---------------------------------------------------------------------- 
																 | css code in the variable 
																 | $this->style_css = ".style{....}";
																 |
																 */
		$this->style_css = NULL;



		/*
																 | ---------------------------------------------------------------------- 
																 | Include css File 
																 | ---------------------------------------------------------------------- 
																 | URL of your css each array 
																 | $this->load_css[] = asset("myfile.css");
																 |
																 */
		$this->load_css = array();


	}


	/*
								  | ---------------------------------------------------------------------- 
								  | Hook for button selected
								  | ---------------------------------------------------------------------- 
								  | @id_selected = the id selected
								  | @button_name = the name of button
								  |
								  */
	public function actionButtonSelected($id_selected, $button_name)
	{
		//Your code here

	}


	/*
								  | ---------------------------------------------------------------------- 
								  | Hook for manipulate query of index result 
								  | ---------------------------------------------------------------------- 
								  | @query = current sql query 
								  |
								  */
	public function hook_query_index(&$query)
	{
		//Your code here

	}

	/*
								  | ---------------------------------------------------------------------- 
								  | Hook for manipulate row of index table html 
								  | ---------------------------------------------------------------------- 
								  |
								  */
	public function hook_row_index($column_index, &$column_value)
	{
		//Your code here
	}

	/*
								  | ---------------------------------------------------------------------- 
								  | Hook for manipulate data input before add data is execute
								  | ---------------------------------------------------------------------- 
								  | @arr
								  |
								  */
	public function hook_before_add(&$postdata)
	{
		//Your code here

	}

	/* 
								  | ---------------------------------------------------------------------- 
								  | Hook for execute command after add public static function called 
								  | ---------------------------------------------------------------------- 
								  | @id = last insert id
								  | 
								  */
	public function hook_after_add($id)
	{
		//Your code here

	}

	/* 
								  | ---------------------------------------------------------------------- 
								  | Hook for manipulate data input before update data is execute
								  | ---------------------------------------------------------------------- 
								  | @postdata = input post data 
								  | @id       = current id 
								  | 
								  */
	public function hook_before_edit(&$postdata, $id)
	{
		//Your code here

	}

	/* 
								  | ---------------------------------------------------------------------- 
								  | Hook for execute command after edit public static function called
								  | ----------------------------------------------------------------------     
								  | @id       = current id 
								  | 
								  */
	public function hook_after_edit($id)
	{
		//Your code here 

	}

	/* 
								  | ---------------------------------------------------------------------- 
								  | Hook for execute command before delete public static function called
								  | ----------------------------------------------------------------------     
								  | @id       = current id 
								  | 
								  */
	public function hook_before_delete($id)
	{
		//Your code here

	}

	/* 
								  | ---------------------------------------------------------------------- 
								  | Hook for execute command after delete public static function called
								  | ----------------------------------------------------------------------     
								  | @id       = current id 
								  | 
								  */
	public function hook_after_delete($id)
	{
		//Your code here

	}



	//By the way, you can still create your own method in here... :) 

	public function getSetProceso($id)
	{
		$orden = OrdenModel::where('id', $id)->first();
		$orden->status = OrdenModel::EN_PROCESO;
		$orden->update();

		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Orden Actualizada Correctamente", "success");
	}
	public function getSetCancelado($id)
	{
		$orden = OrdenModel::where('id', $id)->first();
		$orden->status = OrdenModel::CANCELADO;
		$orden->update();

		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Orden Actualizada Correctamente", "success");
	}
	public function getSetEnvio($id)
	{

		$orden = OrdenModel::where('id', $id)->first();
		$orden->status = OrdenModel::EN_ENVIO;
		$orden->update();
		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Orden Actualizada Correctamente", "success");
	}
	public function getSetEntregado($id)
	{
		$orden = OrdenModel::where('id', $id)->first();
		$orden->status = OrdenModel::ENTREGADO;
		$orden->update();

		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Orden Actualizada Correctamente", "success");
	}

}