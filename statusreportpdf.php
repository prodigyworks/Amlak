<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	
	class StatusReport extends PDFReport {
	    public $member = null;
	    
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 265.6, 1);
			
			$size = $this->addText( 7, 13, "Status Report Between " . $_POST['datefrom'] . " And " . $_POST['dateto'], 12, 4, 'B') + 5;
			$this->SetFont('Arial','', 6);
			$this->addText( 7, 20, "Date :  " . $this->member['comparedate'], 9, 4, 'B');
			
			$this->SetY(30);
		}
		
		function __construct($orientation, $metric, $size, $startdate, $enddate) {
			$dynamicY = 0;
			
	        parent::__construct($orientation, $metric, $size);
	        
	        	
			start_db();
	        
	        $this->SetAutoPageBreak(true, 30);
			
			try {
				$prevDate = "";
				$prevTruck = "";
				$y = 0;
				$x = 0;
				
				$sql = "SELECT A.id, A.qtyexpected, A.qtydelivered, A.status,
					    B.name AS customername, 
					    C.description, 
					    D.fullname,
					    E.name AS paymentmethodname,
					    F.name AS truckname,
					    G.name AS drivername,
					    H.name AS branchname,
					    DATE_FORMAT(A.fromdeliverydate, '%H:%I') AS fromtime,
					    DATE_FORMAT(A.todeliverydate, '%H:%I') AS totime,
					    DATE_FORMAT(A.fromdeliverydate, '%d/%m/%Y') AS comparedate,
						DATE_FORMAT(A.fromdeliverydate, '%d/%m/%Y') AS fromdeliverydate,
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
						ORDER BY DATE(A.fromdeliverydate), F.name, A.fromdeliverydate";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($this->member = mysql_fetch_assoc($result))) {
						if ($prevDate != $this->member['comparedate']) {
							$this->AddPage();
			
							$prevDate = $this->member['comparedate'];
							$prevTruck = "";
							$y = $this->GetY();
						}
						
						if ($prevTruck != $this->member['truckname']) {
							if ($prevTruck != "") {
								$y += 35;
							}
							
							$prevTruck = $this->member['truckname'];
							$x = 0;
							
							$this->AddText(10, $y + 12, $this->member['truckname']);
							$this->Rect(7, $y - 1, 19, 30);
						}
						
						if ($this->member['status'] == 1) {
							$status = "On Route";
							
						} else if ($this->member['status'] == 2) {
							$status = "Delivered";
						
						} else if ($this->member['status'] == 3) {
							$status = "Cancelled";
						}
						
						$this->AddText(30 + $x, $y, $this->member['customername'] . " / " . $this->member['branchname'], 7, 3, '', 40);
						$this->AddText(30 + $x, $y + 10, $this->member['fromtime'] . " - " . $this->member['totime']);
						$this->AddText(30 + $x, $y + 15, number_format($this->member['qtyexpected'], 2));
						$this->AddText(30 + $x, $y + 20, number_format($this->member['qtydelivered'], 2));
						$this->AddText(30 + $x, $y + 25, $status);
						
						$this->Rect(27 + $x, $y - 1, 44, 30);
						
						$x += 45;
						
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	$pdf = new StatusReport( 'L', 'mm', 'A4', convertStringToDate($_POST['datefrom']), convertStringToDate($_POST['dateto']));
	$pdf->Output();
?>