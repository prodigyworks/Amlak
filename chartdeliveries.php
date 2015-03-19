<?php // content="text/plain; charset=utf-8"
    require_once ('jpgraph/jpgraph.php');
    require_once ('jpgraph/jpgraph_bar.php');
    require_once ('system-db.php');

    start_db();

    $data1y = array();
    $data2y = array();
	$dates = array();
    
	$datefrom = DateTime::createFromFormat("Y-m-d", convertStringToDate($_POST['datefrom']));
    $todate = $datefrom->diff(DateTime::createFromFormat('Y-m-d', convertStringToDate($_POST['dateto'])));
    
    // Create the graph. These two calls are always required
    $graph = new Graph(90 * $todate->days,400,'auto');
    $graph->SetScale("textlin");
    
    $theme_class=new UniversalTheme;
    $graph->SetTheme($theme_class); 
    $graph->SetBox(false);
    $graph->ygrid->SetFill(false);

	for ($i = 0; $i <= $todate->days; $i++) {
	    $dates[] = $datefrom->format("d/m/Y");
	    $wheredate = $datefrom->format("Y-m-d");
	    $and = "";
	    
	    if (isset($_POST['truckid'])) {
	    	$and = " AND truckid = " . $_POST['truckid'];
	    }
	    
	    $qry = "SELECT
	    		SUM(qtyexpected) AS qtyexpected, 
	    		SUM(qtydelivered) AS qtydelivered 
	    		FROM {$_SESSION['DB_PREFIX']}delivery 
	    		WHERE DATE(fromdeliverydate) = '$wheredate'
	    		$and";
    	$result = mysql_query($qry);

    	if (! $result) {
    		logError($qry . " = " . mysql_error());
    	}
    	
    	while (($member = mysql_fetch_assoc($result))) {
    	    $data1y[] = $member['qtyexpected'];
    	    $data2y[] = $member['qtydelivered'];
    	}
	    
    	$datefrom->add(new DateInterval('P1D'));
	}
    
    $graph->xaxis->SetTickLabels($dates);
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false,false);
    
    // Create the bar plots
    $b1plot = new BarPlot($data1y);
    $b2plot = new BarPlot($data2y);

    // Create the grouped bar plot
    $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
    // ...and add it to the graPH
    $graph->Add($gbplot);
    
    
    if (isset($_POST['truckid'])) {
    	$truckid = $_POST['truckid'];
    	$qry = "SELECT name FROM {$_SESSION['DB_PREFIX']}truck WHERE id = '$truckid'";
    	$result = mysql_query($qry);

    	if (! $result) {
    		logError($qry . " = " . mysql_error());
    	}
    	
    	while (($member = mysql_fetch_assoc($result))) {
		    $graph->title->Set($member['name']);
    	}    	
    	
    } else {
	    $graph->title->Set('All Trucks');
    }
    
	$graph->title->SetColor('navy');    
    $b1plot->SetColor("white");
    $b1plot->SetFillColor("#cc1111");
    $b1plot->value->SetFormat('%d');
    $b1plot->value->Show();
    $b1plot->value->SetColor('#55bbdd');
    $b1plot->SetShadow("gray", 3, 3, true);
    $b1plot->SetLegend("Expected");

    $b2plot->SetColor("white");
    $b2plot->SetFillColor("#11cccc");
    $b2plot->value->SetFormat('%d');
    $b2plot->value->Show();
    $b2plot->value->SetColor('#55bbdd');
    $b2plot->SetShadow("gray", 3, 3, true);
    $b2plot->SetLegend("Delivered");
    
    // Display the graph
    $graph->Stroke();
?>	