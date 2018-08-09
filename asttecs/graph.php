<?php 

    require_once ('jpgraph/src/jpgraph.php');   
    require_once ('jpgraph/src/jpgraph_pie.php');   
    require_once ('jpgraph/src/jpgraph_pie3d.php'); 
  
		$r= shell_exec('df -h');
		$ty=explode("G",$r);
		$use=$ty[2];
		$aval=$ty[3];
		$useper=($use+$aval);
		$uper=($use/$useper)*100;
		

    /*  $use=$_REQUEST['use'];
	$aval=$_REQUEST['aval']; $graph = new PieGraph(10, 10);  */ 

      $data    = array($use,$aval);
    $Legends = array('Use','Free');
    $labels  = array("Used\n(%.1f%%)","Free\n(%.1f%%)");
    $color   = array('red','darkgreen');
    $graph   = new PieGraph(300,300);
    
    
    $p1 = new PiePlot($data);
    $p1->ExplodeSlice(-5);
    $p1->SetCenter(0.50);
    $p1->SetLegends($Legends);
    $graph->legend->Pos(0.1,0.1);
    $graph->legend->SetFont(FF_FONT1,FS_NORMAL);


    $p1->SetTheme("earth");
    $p1->SetSliceColors($color);

    // Setup the labels to be displayed
    $p1->SetLabels($labels);
    $p1->SetLabelPos(0.99);
    $p1->SetLabelType(PIE_VALUE_PER);
    $p1->value->Show();
    $p1->value->SetFont(FF_FONT2,FS_NORMAL);
    $p1->value->SetColor('navy');

    $graph->Add($p1);
    $graph->Stroke();

?>



