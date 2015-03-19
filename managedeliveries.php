<?php
	require_once("crud.php");
	
	class DiscountBandCrud extends Crud {
		
		public function postEditScriptEvent() {
?>
			$("#customerid").trigger("change");
			$("#customerbranchid").val(data[0].customerbranchid);
<?php
		}
		
		public function postScriptEvent() {
?>
			function deliveryReference(node) {
				return padZero(node.id, 6);
			}
			
			function preAddScriptEvent() {
				$.ajax({
						url: "createbranchcombo.php",
						dataType: 'html',
						async: false,
						data: { 
							customerid: -1
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							pwAlert("ERROR :" + errorThrown);
						},
						success: function(data) {
							$("#customerbranchid").html(data);
						}
					});
			}
			
			function customerid_onchange() {			
				$.ajax({
						url: "createbranchcombo.php",
						dataType: 'html',
						async: false,
						data: { 
							customerid: $("#customerid").val()
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							pwAlert("ERROR :" + errorThrown);
						},
						success: function(data) {
							$("#customerbranchid").html(data);
						}
					});
					
			}
<?php
		}
	}
	
	$crud = new DiscountBandCrud();
	$crud->title = "Deliveries";
	$crud->dialogwidth = 600;
	$crud->preAddScriptEvent = "preAddScriptEvent";
	$crud->table = "{$_SESSION['DB_PREFIX']}delivery";
	$crud->sql = "SELECT 
				  A.*, AA.name AS customername, AB.name AS branchname, B.name AS truckname, 
				  C.description, D.name AS drivername, E.fullname, 
				  F.name AS paymentmethodname 
				  FROM {$_SESSION['DB_PREFIX']}delivery A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer AA
				  ON AA.id = A.customerid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customerbranch AB
				  ON AB.id = A.customerbranchid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}truck B
				  ON B.id = A.truckid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}product C
				  ON C.id = A.productid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver D
				  ON D.id = A.driverid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members E
				  ON E.member_id = A.memberid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}paymentmethod F
				  ON F.id = A.paymentmethodid
				  ORDER BY A.id DESC";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'filter'	 => false,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'deliveryref',
				'function'   => 'deliveryReference',
				'sortcolumn' => 'A.id',
				'type'		 => 'DERIVED',
				'length' 	 => 16,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'Delivery'
			),
			array(
				'name'       => 'customerid',
				'type'       => 'LAZYDATACOMBO',
				'length' 	 => 60,
				'label' 	 => 'Customer',
				'table'		 => 'customer',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'customername',
				'onchange'	 => 'customerid_onchange',
				'table_name' => 'name'
			),
			array(
				'name'       => 'customerbranchid',
				'type'       => 'DATACOMBO',
				'length' 	 => 20,
				'label' 	 => 'Branch',
				'filter'	 => false,
				'table'		 => 'customerbranch',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'branchname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'productid',
				'type'       => 'DATACOMBO',
				'length' 	 => 20,
				'label' 	 => 'Product',
				'table'		 => 'product',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'description',
				'table_name' => 'description'
			),
			array(
				'name'       => 'fromdeliverydate',
				'length' 	 => 20,
				'datatype'	 => 'datetime',
				'label' 	 => 'From Date'
			),
			array(
				'name'       => 'todeliverydate',
				'length' 	 => 20,
				'datatype'	 => 'datetime',
				'label' 	 => 'To Date'
			),
			array(
				'name'       => 'status',
				'length' 	 => 15,
				'label' 	 => 'Status',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 1,
							'text'		=> 'On Route'
						),
						array(
							'value'		=> 2,
							'text'		=> 'Delivered'
						),
						array(
							'value'		=> 3,
							'text'		=> 'Cancelled'
						)
					)
			),
			array(
				'name'       => 'truckid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Truck',
				'table'		 => 'truck',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'truckname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'driverid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Driver',
				'table'		 => 'driver',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'drivername',
				'table_name' => 'name'
			),
			array(
				'name'       => 'paymentmethodid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Payment Method',
				'table'		 => 'paymentmethod',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'paymentmethodname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'memberid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'User',
				'table'		 => 'members',
				'required'	 => true,
				'table_id'	 => 'member_id',
				'alias'		 => 'fullname',
				'table_name' => 'fullname'
			),
			array(
				'name'       => 'qtyexpected',
				'length' 	 => 18,
				'label' 	 => 'Qty Expected',
				'align'		 => 'right'
			),
			array(
				'name'       => 'qtydelivered',
				'length' 	 => 18,
				'required' 	 => false,
				'label' 	 => 'Qty Delivered',
				'align'		 => 'right'
			)
		);
		
	$crud->run();
	
?>
