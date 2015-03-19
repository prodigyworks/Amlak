<?php
	require_once("crud.php");
	
	class ProductCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addproductdocument.php", node, "productdocs", "productid");
			}
	
<?php			
		}
	}
	
	$crud = new ProductCrud();
	$crud->dialogwidth = 740;
	$crud->title = "Products";
	$crud->table = "{$_SESSION['DB_PREFIX']}product";
	$crud->sql = "SELECT A.*, B.name
				  FROM  {$_SESSION['DB_PREFIX']}product A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}uom B
				  ON B.id = A.uomid
				  ORDER BY A.productcode";
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
				'name'       => 'productcode',
				'length' 	 => 20,
				'label' 	 => 'Product Code'
			),
			array(
				'name'       => 'description',
				'length' 	 => 90,
				'label' 	 => 'Description'
			),			
			array(
				'name'       => 'uomid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'UOM',
				'table'		 => 'uom',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'name',
				'table_name' => 'name'
			),
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'required'   => false,
				'length' 	 => 35,
				'showInView' => false,
				'label' 	 => 'Image'
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
