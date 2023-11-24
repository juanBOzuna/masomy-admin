<?php
namespace App\Http\Controllers;


use App\Models\OrdenDetailModel;
use App\Models\OrdenModel;
use App\Models\PaymentLinksModel;
// use App\Http\Controllers\EpaycoController;
use App\Models\PreOrdenDetailModel;
use App\Models\PreOrdenModel;
use Illuminate\Support\Facades\Http;
use Session;
use Request;
use DB;
use CRUDBooster;

class AdminPaymentLinksController extends \crocodicstudio\crudbooster\controllers\CBController
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
		$this->table = "payment_links";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label" => "Reference", "name" => "reference"];
		$this->col[] = ["label" => "Link", "name" => "link"];
		$this->col[] = ["label" => "Status", "name" => "status"];
		$this->col[] = ["label" => "User Id", "name" => "user_id", "join" => "users,name"];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Reference', 'name' => 'reference', 'type' => 'textarea', 'validation' => 'required|string|min:5|max:5000', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Link', 'name' => 'link', 'type' => 'textarea', 'validation' => 'required|string|min:5|max:5000', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Status', 'name' => 'status', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'User Id', 'name' => 'user_id', 'type' => 'select2', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'datatable' => 'users,name'];
		# END FORM DO NOT REMOVE THIS LINE

		# OLD START FORM
		//$this->form = [];
		//$this->form[] = ["label"=>"Reference","name"=>"reference","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
		//$this->form[] = ["label"=>"Link","name"=>"link","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
		//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
		//$this->form[] = ["label"=>"User Id","name"=>"user_id","type"=>"select2","required"=>TRUE,"validation"=>"required|min:1|max:255","datatable"=>"user,id"];
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
		$rechazada = PaymentLinksModel::RECHAZADA;
		$rechazada2 = PaymentLinksModel::NO_PAGADO;
		$rechazada3 = PaymentLinksModel::CANCELADA;
		$rechazada4 = PaymentLinksModel::FALLIDA;
		$success = PaymentLinksModel::APROBADA;
		$success2 = PaymentLinksModel::PAGADO;
		$success3 = PaymentLinksModel::ACEPTADA;

		$info1 = PaymentLinksModel::POR_DEFINIR;
		$info2 = PaymentLinksModel::ABANDONADA;
		$info3 = PaymentLinksModel::PAGO_PENDIENTE;
		// const POR_DEFINIR = "Por definir";
		$other = PaymentLinksModel::OTRO;
		$max_attemps = PaymentLinksModel::MULTIPLES_INTENTOS;

		$this->addaction[] = [
			'label' => 'Revisar Estado',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-status/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] != '" . $success . "' && [status] != '" . $success2 . "' &&  [status] != '" . $success3 . "'",
			'color' => 'success'
			//                'showIf' => "[status] != $entregado && [status] != $cancelada &&  [status] != $incompleta"
		];
		$this->addaction[] = [
			'label' => 'Generar Orden',
			'url' => \crocodicstudio\crudbooster\helpers\CRUDBooster::mainpath('set-orden/[id]'),
			'icon' => 'fa fa-refresh',
			'showIf' => "[status] == '" . $success . "' || [status] == '" . $success2 . "' ||  [status] == '" . $success3 . "'",
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
		$rechazada = PaymentLinksModel::RECHAZADA;
		$rechazada2 = PaymentLinksModel::NO_PAGADO;
		$rechazada3 = PaymentLinksModel::CANCELADA;
		$rechazada4 = PaymentLinksModel::FALLIDA;
		$success = PaymentLinksModel::APROBADA;
		$success2 = PaymentLinksModel::PAGADO;
		$success3 = PaymentLinksModel::ACEPTADA;

		$info1 = PaymentLinksModel::POR_DEFINIR;
		$info2 = PaymentLinksModel::ABANDONADA;
		$info3 = PaymentLinksModel::PAGO_PENDIENTE;
		// const POR_DEFINIR = "Por definir";
		$other = PaymentLinksModel::OTRO;
		$max_attemps = PaymentLinksModel::MULTIPLES_INTENTOS;
		// const ABANDONADA = "Abandonada";
		// const PAGO_PENDIENTE = "Pago pendiente";		

		$this->table_row_color[] = ['condition' => "[status] == '" . $rechazada . "'  || [status] == '" . $rechazada2 . "'" . "  || [status] == '" . $rechazada3 . "'" . "  || [status] == '" . $rechazada4 . "'" . "  || [status] == '" . $max_attemps . "'", "color" => "danger"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $success . "'  || [status] == '" . $success2 . "'" . "  || [status] == '" . $success3 . "'", "color" => "success"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $info1 . "'  || [status] == '" . $info2 . "'" . "  || [status] == '" . $info3 . "'" . "  || [status] == '" . $other . "'", "color" => "info"];
		$this->table_row_color[] = ['condition' => "[status] == '" . $other . "'  || [status] == '" . $max_attemps . "'", "color" => "warning"];


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

	public function getSetStatus($id)
	{
		// return EpaycoController::
		$link = PaymentLinksModel::where('id', $id)->first();

		$token = $this->loginEpayco();

		// dd($link);
		$response = Http::withHeaders([
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $token->token,
		])->post('https://apify.epayco.co/transaction', ['filter' => ['referenceClient' => $link->reference]]);

		$referenses = json_decode($response)->data->data;
		$status = count($referenses) >= 1 ? $referenses[0]->status : PaymentLinksModel::POR_DEFINIR;

		// dd($status);
		if (count($referenses) >= 1) {

			$link->status = $status;
			$link->update();
		}

		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "El link fue verificado exitosamente. ESTADO: " . $status, "info");
	}
	public function getSetOrden($id)
	{
		$link = PaymentLinksModel::where('id', $id)->first();
		$pre_orden = PreOrdenModel::where('payment_link_id', $link->id)->first();

		$orden = OrdenModel::create([
			'total_price' => $pre_orden->total_price,
			'total_disccount' => $pre_orden->total_disccount,
			'payment_link_id' => $pre_orden->payment_link_id,
			'status' => OrdenModel::PENDIENTE,
		]);

		$pre_details = PreOrdenDetailModel::where('pre_orden_id', $pre_orden->id)->get();

		foreach ($pre_details as $pre_detail) {
			# code...

			OrdenDetailModel::create([
				'quantity' => $pre_detail->quantity,
				'subtotal' => $pre_detail->subtotal,
				'disccount' => $pre_detail->disccount,
				'total' => $pre_detail->total,
				'product_id' => $pre_detail->product_id,
				'orden_id' => $orden->id
			]);
		}

		$link->have_order = true;
		$link->update();

		\crocodicstudio\crudbooster\helpers\CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Orden Creada Exitosamente", "info");
	}

	private static function loginEpayco()
	{

		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => 'https://apify.epayco.co/login',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Basic ' . env('AUTHORIZATION_EPAYCO')
				),
			)
		);

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response);

	}

}