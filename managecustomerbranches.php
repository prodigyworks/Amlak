<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			/* Derived  address callback. */
			function fullAddress(node) {
				var address = "";
				
				if (node.address1 != null && (node.address1) != "") {
					address = address + node.address1;
				} 
				
				if (node.address2 != null && (node.address2) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.address2;
				} 
				
				if (node.address3 != null && (node.address3) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.address3;
				} 
				
				if (node.city != null && (node.city) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.city;
				} 
				
				if (node.postcode != null && (node.postcode) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.postcode;
				} 
				
				if (node.country != null && (node.country) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.country;
				} 
				
				return address;
			}
	<?php			
		}
	}
	
	$customerid = $_GET['id'];
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Customer Branch";
	$crud->table = "{$_SESSION['DB_PREFIX']}customerbranch";
	$crud->sql = "SELECT A.*, B.name AS customername
				  FROM  {$_SESSION['DB_PREFIX']}customerbranch A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer B
				  ON B.id = A.customerid
				  WHERE A.customerid = $customerid
				  ORDER BY A.name";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'customerid',
				'length' 	 => 10,
				'default'	 => $customerid,
				'editable'	 => false,
				'showInView' => false,
				'label' 	 => 'Customer ID'
			),
			array(
				'name'       => 'customername',
				'length' 	 => 70,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Customer'
			),
			array(
				'name'       => 'name',
				'length' 	 => 20,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'address1',
				'length' 	 => 60,
				'showInView' => false,
				'label' 	 => 'Address 1'
			),
			array(
				'name'       => 'address2',
				'length' 	 => 60,
				'showInView' => false,
				'label' 	 => 'Address 2'
			),
			array(
				'name'       => 'address3',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Address 3'
			),
			array(
				'name'       => 'city',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'City'
			),
			array(
				'name'       => 'postcode',
				'length' 	 => 10,
				'showInView' => false,
				'label' 	 => 'Post Code'
			),
			array(
				'name'       => 'country',
				'length' 	 => 30,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Country'
			),
			array(
				'name'       => 'address',
				'length' 	 => 90,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullAddress',
				'label' 	 => 'Address'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Contacts',
				'imageurl'	  => 'images/contact.png',
				'application' => 'managecontacts.php'
			)
		);
		
	$crud->run();
?>
