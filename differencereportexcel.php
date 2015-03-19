<?php
	/** Error reporting */
	error_reporting(0);
	
	/** Include path **/
	/** PHPExcel */
	include 'system-db.php';
	include 'PHPExcel.php';
	include 'PHPExcel/Writer/Excel2007.php';
	
	start_db();
	
	header('Content-type: application/excel');
	header('Content-disposition: attachment; filename="bulkquotes.xlsx');
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator(GetUserName());
	$objPHPExcel->getProperties()->setLastModifiedBy(GetUserName());
	$objPHPExcel->getProperties()->setTitle("Difference Report");
	$objPHPExcel->getProperties()->setSubject("Difference Report");
	$objPHPExcel->getProperties()->setDescription("Difference Report");
	
	$objPHPExcel->setActiveSheetIndex(0);
	
	$startdate = convertStringToDate($_POST['datefrom']);
	$enddate = convertStringToDate($_POST['dateto']);
	
	$sql = "SELECT A.id, A.qtyexpected, A.qtydelivered, 
		    B.name AS customername, 
		    C.description, 
		    D.fullname,
		    E.name AS paymentmethodname,
		    F.name AS truckname,
		    G.name AS drivername,
		    H.name AS branchname,
			DATE_FORMAT(A.fromdeliverydate, '%d/%m/%Y %H:%I') AS fromdeliverydate,
			DATE_FORMAT(A.todeliverydate, '%d/%m/%Y %H:%I') AS todeliverydate
			FROM {$_SESSION['DB_PREFIX']}delivery A 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer B 
			ON B.id = A.customerid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}product C 
			ON C.id = A.productid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members D 
			ON D.member_id = A.memberid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}paymentmethod E 
			ON E.id = A.paymentmethodid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}truck F 
			ON F.id = A.truckid
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver G 
			ON G.id = A.driverid
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customerbranch H 
			ON H.id = A.customerbranchid
			WHERE ((DATE(A.fromdeliverydate) >= '$startdate' AND DATE(A.fromdeliverydate) <= '$enddate')
			OR     (DATE(A.todeliverydate) >= '$startdate' AND DATE(A.todeliverydate) <= '$enddate'))
			ORDER BY A.fromdeliverydate";
	$result = mysql_query($sql);
	
	if (! $result) {
		die($sql . " - " . mysql_error());
	}
	
	$row = 1;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
	
	$headerArray = array(	
			'font' => array(		'bold' => true),
			'borders' => array(
		    'allborders' => array(
		      'style' => PHPExcel_Style_Border::BORDER_THIN
		    )
		  )
		);
	
	$styleArray = array(
		  'borders' => array(
		    'allborders' => array(
		      'style' => PHPExcel_Style_Border::BORDER_THIN
		    )
		  )
		);
		
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
	
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'From Date');
	$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Truck');
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Driver');
	$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Customer');
	$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Branch');
	$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Product');
	$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Payment Method');
	$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Qty Expected');
	$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Qty Delivered');
	$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Difference in Qty');
	$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Delivery Note no.');
	$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'User Input');
	
	$objPHPExcel->getActiveSheet()->getStyle('H1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($headerArray);
	
	while (($member = mysql_fetch_assoc($result))) {
		$row++;
			
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, $member['fromdeliverydate']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, $member['truckname']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $member['drivername']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, $member['customername']);
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $row, $member['branchname']);
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $row, $member['description']);
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $row, $member['paymentmethodname']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $row, number_format($member['qtyexpected'], 2));
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $row, number_format($member['qtydelivered'], 2));
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $row, number_format(floatval($member['qtyexpected']) - floatval($member['qtydelivered']), 2));
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $row, sprintf("%06d", $member['id']));
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $row, $member['fullname']);
		
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row)->getNumberFormat()->setFormatCode("0.00");
		$objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatCode("0.00");
		$objPHPExcel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatCode("0.00");
		$objPHPExcel->getActiveSheet()->getStyle('K' . $row)->getNumberFormat()->setFormatCode("000000");
	}
	
			
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('php://output');
?>
