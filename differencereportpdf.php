<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	
	class DifferenceReport extends PDFReport {
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 265.6, 1);
			
			$size = $this->addText( 10, 13, "Difference In Quantity Report Between " . $_POST['datefrom'] . " And " . $_POST['dateto'], 12, 4, 'B') + 5;
			$this->SetFont('Arial','', 6);
				
			$cols = array( 
					"From Date"  => 23,
					"Truck"  => 21,
					"Driver"  => 23,
					"Customer"  => 47,
					"Branch"  => 22,
					"Product"  => 23,
					"Payment Method"  => 18,
					"Qty Expected"  => 18,
					"Qty Delivered"  => 18,
					"Difference in Qty"  => 18,
					"Delivery Note no."  => 23,
					"User Input" => 23
				);
			
			$this->addCols($size, $cols);

			$cols = array(
					"From Date"  => "L",
					"Truck"  => "L",
					"Driver"  => "L",
					"Customer"  => "L",
					"Branch"  => "L",
					"Product"  => "L",
					"Payment Method"  => "L",
					"Qty Expected"  => "R",
					"Qty Delivered"  => "R",
					"Difference in Qty"  => "R",
					"Delivery Note no."  => "L",
					"User Input" => "L"
				);
			$this->addLineFormat( $cols);
			$this->SetY(30);
		}
		
		function __construct($orientation, $metric, $size, $startdate, $enddate) {
			$dynamicY = 0;
			
	        parent::__construct($orientation, $metric, $size);
	        
	        	
			start_db();
	        
	        $this->SetAutoPageBreak(true, 30);
			$this->AddPage();
			
			$y = $this->GetY();
			
			try {
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
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$line = array(
								"From Date"  => $member['fromdeliverydate'],
								"Truck"  => $member['truckname'],
								"Driver"  => $member['drivername'],
								"Customer"  => $member['customername'],
								"Branch"  => $member['branchname'],
								"Product"  => $member['description'],
								"Payment Method"  => $member['paymentmethodname'],
								"Qty Expected"  => $member['qtyexpected'],
								"Qty Delivered"  => $member['qtydelivered'],
								"Difference in Qty"  => number_format(floatval($member['qtyexpected']) - floatval($member['qtydelivered']), 2),
								"Delivery Note no."  => sprintf("%06d", $member['id']),
								"User Input" => $member['fullname']
							);
							
						$y += $this->addLine( $y, $line, 4);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	$pdf = new DifferenceReport( 'L', 'mm', 'A4', convertStringToDate($_POST['datefrom']), convertStringToDate($_POST['dateto']));
	$pdf->Output();
?>