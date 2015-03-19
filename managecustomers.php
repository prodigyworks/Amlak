<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addcustomerdocument.php", node, "customerdocs", "customerid");
			}
			
	<?php			
		}
	}
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Customers";
	$crud->table = "{$_SESSION['DB_PREFIX']}customer";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}customer A
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
				'name'       => 'accountnumber',
				'length' 	 => 17,
				'label' 	 => 'Account Number'
			),			
			array(
				'name'       => 'name',
				'length' 	 => 70,
				'label' 	 => 'Name'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Branches',
				'imageurl'	  => 'images/branch.png',
				'application' => 'managecustomerbranches.php'
			)
		);
		
	$crud->run();
?>
