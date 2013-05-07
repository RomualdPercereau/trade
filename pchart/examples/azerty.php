<?php

function chart($data, $name)
{
 /* CAT:Combo */

 /* pChart library inclusions */

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints($data, $name);
 // $MyData->addPoints($data2,"mme");
 $MyData->setSerieTicks("Probe 2",4);
 // $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");

 /* Create the pChart object */
 $myPicture = new pImage(1200,600,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */ 
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);

 /* Overlay with a gradient */ 
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 
 /* Add a border to the picture */
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../pchart/fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(150,35, $name,array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../pchart/fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,1150,520);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>255,"GridG"=>255,"GridB"=>255,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Write the chart legend */
 $myPicture->drawLegend(540,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Draw the area chart */
 $MyData->setSerieDrawable("Probe 1",TRUE);
 $MyData->setSerieDrawable("Probe 2",FALSE);$myPicture->drawAreaChart();

 /* Draw a line and a plot chart on top */
 $MyData->setSerieDrawable("Probe 2",TRUE);
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->drawLineChart();
 $myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"PlotSize"=>1,"BorderSize"=>1,"Surrounding"=>-60,"BorderAlpha"=>80));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("$name.png");
}
?>