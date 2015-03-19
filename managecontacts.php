<?php
	require_once("crud.php");
	
	class ContactCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addcustomercontactdocument.php", node, "customercontactdocs", "contactid");
			}
<?php			
		}
	}
	
	$branchid = $_GET['id'];
	
	$crud = new ContactCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Customer Contacts";
	$crud->table = "{$_SESSION['DB_PREFIX']}customercontact";
	$crud->sql = "SELECT A.*, B.name AS branchname, C.name
				  FROM  {$_SESSION['DB_PREFIX']}customercontact A
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}customerbranch B
				  ON B.id = A.branchid
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}customer C
				  ON C.id = B.customerid
				  WHERE A.branchid = $branchid
				  ORDER BY A.firstname";
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
				'name'       => 'name',
				'length' 	 => 60,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Customer'
			),
			array(
				'name'       => 'branchid',
				'length' 	 => 10,
				'default'	 => $branchid,
				'editable'	 => false,
				'showInView' => false,
				'label' 	 => 'Branch ID'
			),
			array(
				'name'       => 'branchname',
				'length' 	 => 20,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Branch'
			),
			array(
				'name'       => 'firstname',
				'length' 	 => 25,
				'label' 	 => 'First Name'
			),			
			array(
				'name'       => 'lastname',
				'length' 	 => 25,
				'label' 	 => 'Last Name'
			),			
			array(
				'name'       => 'email1',
				'length' 	 => 60,
				'required' 	 => false,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone1',
				'length' 	 => 15,
				'label' 	 => 'Telephone'
			),
			array(
				'name'       => 'mobile',
				'length' 	 => 15,
				'label' 	 => 'Mobile'
			),
			array(
				'name'       => 'fax1',
				'length' 	 => 15,
				'required' 	 => false,
				'label' 	 => 'Fax'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			)
		);
		
	$crud->run();
?>
