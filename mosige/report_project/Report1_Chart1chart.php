<?php

// Set up chart
//$Chart = &$Table->Chart1;
// Initialize chart data

$Chart->ID = "Report1_Chart1"; // Chart ID
$Chart->SetChartParms(array(array("type", "2", FALSE),
	array("seriestype", "", FALSE)));  // Chart type / Chart series type
$Chart->SetChartParms(array(array("caption", $Chart->ChartCaption()),
	array("xaxisname", $Chart->ChartXAxisName()))); // Chart caption / X axis name
$Chart->SetChartParm("yaxisname", $Chart->ChartYAxisName(), TRUE); // Y axis name
$Chart->SetChartParms(array(array("shownames", ""),
	array("showvalues", ""),
	array("showhovercap", ""))); // Show names / Show values / Show hover
?>
<?php

	// Setup chart series data
	if ($Chart->ChartSeriesSql <> "") {
		ewr_LoadChartSeries($Chart->ChartSeriesSql, $Chart);
		if (EWR_DEBUG_ENABLED)
			echo "<p>(Chart Series SQL): " . $Chart->ChartSeriesSql . "</p>";
	}

	// Setup chart data
	if ($Chart->ChartSql <> "") {
		ewr_LoadChartData($Chart->ChartSql, $Chart);
		if (EWR_DEBUG_ENABLED)
			echo "<p>(Chart SQL): " . $Chart->ChartSql . "</p>";
	}
	ewr_SortChartData($Chart->Data,"",null);

	// Render chart
	$Chart->LoadChartParms();
	$chartxml = $Chart->ChartXml();
?>
<span class="">
<?php

	// Show page break content
	if ($Chart->PageBreak && $Chart->PageBreakType == "before")
		echo $Chart->PageBreakContent;
	if ($Chart->ShowChart) { // Show actual chart
		echo $Chart->ShowChartFC($chartxml, FALSE, $Chart->DrillDownInPanel);
	} elseif ($Chart->ShowTempImage) { // Show temp image
		$TmpChartImage = ewr_TmpChartImage("chart_Report1_Chart1", FALSE);
		$TmpGridImage = ewr_TmpChartImage("chart_Report1_Chart1_grid", FALSE);
		if ($TmpChartImage <> "") {
?>
<?php if ($Page->Export == "word" && defined('EWR_USE_PHPWORD') || $Page->Export == "excel" && defined('EWR_USE_PHPEXCEL')) { ?>
<table class="ewChart">
<tr><td><img src="<?php echo $TmpChartImage ?>" alt=""><br><?php if ($TmpGridImage <> "") { ?>
<img src="<?php echo $TmpGridImage ?>" alt=""><?php } ?></td></tr>
</table>
<?php } else { ?>
<div class="ewChart"><img src="<?php echo $TmpChartImage ?>" alt=""><br><?php if ($TmpGridImage <> "") { ?>
<img src="<?php echo $TmpGridImage ?>" alt=""><?php } ?></div>
<?php } ?>
<?php
		}
	}

	// Show page break content
	if ($Chart->PageBreak && $Chart->PageBreakType == "after")
		echo $Chart->PageBreakContent;
?>
</span>
