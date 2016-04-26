<?php

// Global variable for table object
$cv1 = NULL;

//
// Table class for cv1
//
class crcv1 extends crTableCrosstab {
	var $cv1_report;
	var $PersonTimeChart;
	var $ByWeekday;
	var $member_name;
	var $is_attended;
	var $arrangedate;
	var $starttime;
	var $class_name;
	var $class_description;
	var $inner_id;
	var $register_time;
	var $weekday;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'cv1';
		$this->TableName = 'cv1';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// member_name
		$this->member_name = new crField('cv1', 'cv1', 'x_member_name', 'member_name', 'member_user.member_name', 200, EWR_DATATYPE_STRING, -1);
		$this->member_name->GroupingFieldId = 3;
		$this->fields['member_name'] = &$this->member_name;
		$this->member_name->DateFilter = "";
		$this->member_name->SqlSelect = "SELECT DISTINCT member_user.member_name FROM " . $this->getSqlFrom();
		$this->member_name->SqlOrderBy = "member_user.member_name";

		// is_attended
		$this->is_attended = new crField('cv1', 'cv1', 'x_is_attended', 'is_attended', 'register_record.is_attended', 2, EWR_DATATYPE_NUMBER, -1);
		$this->is_attended->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['is_attended'] = &$this->is_attended;
		$this->is_attended->DateFilter = "";
		$this->is_attended->SqlSelect = "";
		$this->is_attended->SqlOrderBy = "";

		// arrangedate
		$this->arrangedate = new crField('cv1', 'cv1', 'x_arrangedate', 'arrangedate', 'class_arrange.arrangedate', 133, EWR_DATATYPE_DATE, 5);
		$this->arrangedate->GroupingFieldId = 1;
		$this->arrangedate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['arrangedate'] = &$this->arrangedate;
		$this->arrangedate->DateFilter = "";
		$this->arrangedate->SqlSelect = "SELECT DISTINCT class_arrange.arrangedate FROM " . $this->getSqlFrom();
		$this->arrangedate->SqlOrderBy = "class_arrange.arrangedate";

		// starttime
		$this->starttime = new crField('cv1', 'cv1', 'x_starttime', 'starttime', 'class_arrange.starttime', 134, EWR_DATATYPE_TIME, -1);
		$this->starttime->GroupingFieldId = 2;
		$this->starttime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['starttime'] = &$this->starttime;
		$this->starttime->DateFilter = "";
		$this->starttime->SqlSelect = "SELECT DISTINCT class_arrange.starttime FROM " . $this->getSqlFrom();
		$this->starttime->SqlOrderBy = "class_arrange.starttime";
		$this->starttime->DrillDownUrl = "CustomView2rpt.php?d=1&t=CustomView2&s=cv1&member_name=f0&weekday=f1";

		// class_name
		$this->class_name = new crField('cv1', 'cv1', 'x_class_name', 'class_name', 'class_design.class_name', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_name'] = &$this->class_name;
		$this->class_name->DateFilter = "";
		$this->class_name->SqlSelect = "SELECT DISTINCT class_design.class_name FROM " . $this->getSqlFrom();
		$this->class_name->SqlOrderBy = "class_design.class_name";

		// class_description
		$this->class_description = new crField('cv1', 'cv1', 'x_class_description', 'class_description', 'class_design.class_description', 200, EWR_DATATYPE_STRING, -1);
		$this->class_description->GroupingFieldId = 4;
		$this->fields['class_description'] = &$this->class_description;
		$this->class_description->DateFilter = "";
		$this->class_description->SqlSelect = "SELECT DISTINCT class_design.class_description FROM " . $this->getSqlFrom();
		$this->class_description->SqlOrderBy = "class_design.class_description";

		// inner_id
		$this->inner_id = new crField('cv1', 'cv1', 'x_inner_id', 'inner_id', 'class_design.inner_id', 3, EWR_DATATYPE_NUMBER, -1);
		$this->inner_id->GroupingFieldId = 5;
		$this->inner_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['inner_id'] = &$this->inner_id;
		$this->inner_id->DateFilter = "";
		$this->inner_id->SqlSelect = "SELECT DISTINCT class_design.inner_id FROM " . $this->getSqlFrom();
		$this->inner_id->SqlOrderBy = "class_design.inner_id";

		// register_time
		$this->register_time = new crField('cv1', 'cv1', 'x_register_time', 'register_time', 'register_record.register_time', 135, EWR_DATATYPE_DATE, 5);
		$this->register_time->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['register_time'] = &$this->register_time;
		$this->register_time->DateFilter = "";
		$this->register_time->SqlSelect = "";
		$this->register_time->SqlOrderBy = "";

		// weekday
		$this->weekday = new crField('cv1', 'cv1', 'x_weekday', 'weekday', 'DayOfWeek(class_arrange.arrangedate) - 1', 20, EWR_DATATYPE_NUMBER, -1);
		$this->weekday->GroupingFieldId = 6;
		$this->weekday->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['weekday'] = &$this->weekday;
		$this->weekday->DateFilter = "";
		$this->weekday->SqlSelect = "SELECT DISTINCT DayOfWeek(class_arrange.arrangedate) - 1 FROM " . $this->getSqlFrom();
		$this->weekday->SqlOrderBy = "DayOfWeek(class_arrange.arrangedate) - 1";

		// cv1_report
		$this->cv1_report = new crChart($this->DBID, 'cv1', 'cv1', 'cv1_report', 'cv1_report', 'starttime', 'is_attended', '', 1, 'COUNT', 550, 440);
		$this->cv1_report->SqlSelect = "SELECT class_arrange.starttime, '', COUNT(register_record.is_attended) FROM ";
		$this->cv1_report->SqlGroupBy = "class_arrange.starttime";
		$this->cv1_report->SqlOrderBy = "";
		$this->cv1_report->SeriesDateType = "";
		$this->cv1_report->ChartDrillDownUrl = "CustomView1rpt.php?d=1&t=CustomView1&s=cv1&starttime=%0:starttime:134%&member_name=f1&class_name=f2&weekday=f3";

		// PersonTimeChart
		$this->PersonTimeChart = new crChart($this->DBID, 'cv1', 'cv1', 'PersonTimeChart', 'PersonTimeChart', 'class_name', 'is_attended', '', 1, 'COUNT', 550, 440);
		$this->PersonTimeChart->SqlSelect = "SELECT class_design.class_name, '', COUNT(register_record.is_attended) FROM ";
		$this->PersonTimeChart->SqlGroupBy = "class_design.class_name";
		$this->PersonTimeChart->SqlOrderBy = "";
		$this->PersonTimeChart->SeriesDateType = "";
		$this->PersonTimeChart->ChartDrillDownUrl = "CustomView1rpt.php?d=1&t=CustomView1&s=cv1&class_name=%0:class_name:200%&member_name=f1&weekday=f2";

		// ByWeekday
		$this->ByWeekday = new crChart($this->DBID, 'cv1', 'cv1', 'ByWeekday', 'ByWeekday', 'weekday', 'is_attended', '', 1, 'COUNT', 550, 440);
		$this->ByWeekday->SqlSelect = "SELECT DayOfWeek(class_arrange.arrangedate) - 1, '', COUNT(register_record.is_attended) FROM ";
		$this->ByWeekday->SqlGroupBy = "DayOfWeek(class_arrange.arrangedate) - 1";
		$this->ByWeekday->SqlOrderBy = "DayOfWeek(class_arrange.arrangedate) - 1 ASC";
		$this->ByWeekday->SeriesDateType = "";
		$this->ByWeekday->ChartDrillDownUrl = "CustomView1rpt.php?d=1&t=CustomView1&s=cv1&weekday=%0:weekday:20%&member_name=f1&starttime=f2&class_name=f3";
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
		} else {
			if ($ofld->GroupingFieldId == 0) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = "";
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fld->FldExpression, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fld->FldExpression . " " . $fld->getSort();
				} else {
					if ($sDtlSortSql <> "") $sDtlSortSql .= ", ";
					$sDtlSortSql .= $fld->FldExpression . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ",";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// Column field

	var $ColumnField = "";

	function getColumnField() {
		return ($this->ColumnField <> "") ? $this->ColumnField : "class_design.class_name";
	}

	function setColumnField($v) {
		$this->ColumnField = $v;
	}

	// Column date type
	var $ColumnDateType = "";

	function getColumnDateType() {
		return ($this->ColumnDateType <> "") ? $this->ColumnDateType : "";
	}

	function setColumnDateType($v) {
		$this->ColumnDateType = $v;
	}

	// Column captions
	var $ColumnCaptions = "";

	function getColumnCaptions() {
		global $ReportLanguage;
		return ($this->ColumnCaptions <> "") ? $this->ColumnCaptions : "";
	}

	function setColumnCaptions($v) {
		$this->ColumnCaptions = $v;
	}

	// Column names
	var $ColumnNames = "";

	function getColumnNames() {
		return ($this->ColumnNames <> "") ? $this->ColumnNames : "";
	}

	function setColumnNames($v) {
		$this->ColumnNames = $v;
	}

	// Column values
	var $ColumnValues = "";

	function getColumnValues() {
		return ($this->ColumnValues <> "") ? $this->ColumnValues : "";
	}

	function setColumnValues($v) {
		$this->ColumnValues = $v;
	}

	// From
	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "register_record Inner Join class_arrange On register_record.arrange_id = class_arrange.arrange_id Inner Join member_user On register_record.member_id = member_user.member_id Inner Join class_design On class_design.class_id = class_arrange.class_id";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";

	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT class_arrange.arrangedate AS `arrangedate`, class_arrange.starttime AS `starttime`, member_user.member_name AS `member_name`, class_design.class_description AS `class_description`, class_design.inner_id AS `inner_id`, DayOfWeek(class_arrange.arrangedate) - 1 AS `weekday`, <DistinctColumnFields> FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "register_record.is_attended > 0";
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";

	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "class_arrange.arrangedate, class_arrange.starttime, member_user.member_name, class_design.class_description, class_design.inner_id, DayOfWeek(class_arrange.arrangedate) - 1";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";

	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";

	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "class_arrange.arrangedate ASC, class_arrange.starttime ASC, member_user.member_name ASC, class_design.class_description ASC, class_design.inner_id ASC, DayOfWeek(class_arrange.arrangedate) - 1 ASC";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Select Distinct
	var $_SqlDistinctSelect = "";

	function getSqlDistinctSelect() {
		return ($this->_SqlDistinctSelect <> "") ? $this->_SqlDistinctSelect : "SELECT DISTINCT class_design.class_name FROM register_record Inner Join class_arrange On register_record.arrange_id = class_arrange.arrange_id Inner Join member_user On register_record.member_id = member_user.member_id Inner Join class_design On class_design.class_id = class_arrange.class_id";
	}

	function SqlDistinctSelect() { // For backward compatibility
		return $this->getSqlDistinctSelect();
	}

	function setSqlDistinctSelect($v) {
		$this->_SqlDistinctSelect = $v;
	}

	// Distinct Where
	var $_SqlDistinctWhere = "";

	function getSqlDistinctWhere() {
		$sWhere = ($this->_SqlDistinctWhere <> "") ? $this->_SqlDistinctWhere : "register_record.is_attended > 0";
		return $sWhere;
	}

	function SqlDistinctWhere() { // For backward compatibility
		return $this->getSqlDistinctWhere();
	}

	function setSqlDistinctWhere($v) {
		$this->_SqlDistinctWhere = $v;
	}

	// Distinct Order By
	var $_SqlDistinctOrderBy = "";

	function getSqlDistinctOrderBy() {
		return ($this->_SqlDistinctOrderBy <> "") ? $this->_SqlDistinctOrderBy : "class_design.class_name ASC";
	}

	function SqlDistinctOrderBy() { // For backward compatibility
		return $this->getSqlDistinctOrderBy();
	}

	function setSqlDistinctOrderBy($v) {
		$this->_SqlDistinctOrderBy = $v;
	}
	var $ColCount;
	var $Col;
	var $DistinctColumnFields = "";

	// Load column values
	function LoadColumnValues($filter = "") {
		global $ReportLanguage;
		$conn = &$this->Connection();

		// Build SQL
		$sSql = ewr_BuildReportSql($this->getSqlDistinctSelect(), $this->getSqlDistinctWhere(), "", "", $this->getSqlDistinctOrderBy(), $filter, "");

		// Load recordset
		$rscol = $conn->Execute($sSql);

		// Get distinct column count
		$this->ColCount = ($rscol) ? $rscol->RecordCount() : 0;

/* Uncomment to show phrase
		if ($this->ColCount == 0) {
			if ($rscol) $rscol->Close();
			echo "<p>" . $ReportLanguage->Phrase("NoDistinctColVals") . $sSql . "</p>";
			exit();
		}
*/
		$this->Col = &ewr_Init2DArray($this->ColCount+1, 7, NULL);
		$colcnt = 0;
		while (!$rscol->EOF) {
			if (is_null($rscol->fields[0])) {
				$wrkValue = EWR_NULL_VALUE;
				$wrkCaption = $ReportLanguage->Phrase("NullLabel");
			} elseif ($rscol->fields[0] == "") {
				$wrkValue = EWR_EMPTY_VALUE;
				$wrkCaption = $ReportLanguage->Phrase("EmptyLabel");
			} else {
				$wrkValue = $rscol->fields[0];
				$wrkCaption = $rscol->fields[0];
			}
			$colcnt++;
			$this->Col[$colcnt] = new crCrosstabColumn($wrkValue, $wrkCaption, TRUE);
			$rscol->MoveNext();
		}
		$rscol->Close();

		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of distinct values

		$nGrps = 6;
		$this->SummaryFields[0] = new crSummaryField('x_is_attended', 'is_attended', 'register_record.is_attended', 'COUNT');
		$this->SummaryFields[0]->SummaryCaption = $ReportLanguage->Phrase("RptCnt");
		$this->SummaryFields[0]->SummaryVal = &ewr_InitArray($this->ColCount+1, NULL);
		$this->SummaryFields[0]->SummaryValCnt = &ewr_InitArray($this->ColCount+1, NULL);
		$this->SummaryFields[0]->SummaryCnt = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);
		$this->SummaryFields[0]->SummarySmry = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);
		$this->SummaryFields[0]->SummarySmryCnt = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);
		$this->SummaryFields[0]->SummaryInitValue = 0;

		// Update crosstab sql
		$sSqlFlds = "";
		$cnt = count($this->SummaryFields);
		for ($is = 0; $is < $cnt; $is++) {
			$smry = &$this->SummaryFields[$is];
			for ($colcnt = 1; $colcnt <= $this->ColCount; $colcnt++) {
				$sFld = ewr_CrossTabField($smry->SummaryType, $smry->FldExpression, $this->getColumnField(), $this->getColumnDateType(), $this->Col[$colcnt]->Value, "'", "C" . $is . $colcnt, $this->DBID);
				if ($sSqlFlds <> "")
					$sSqlFlds .= ", ";
				$sSqlFlds .= $sFld;
			}
		}
		$this->DistinctColumnFields = $sSqlFlds;
	}

	// Table Level Group SQL
	// First Group Field

	var $_SqlFirstGroupField = "";

	function getSqlFirstGroupField() {
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "class_arrange.arrangedate";
	}

	function SqlFirstGroupField() { // For backward compatibility
		return $this->getSqlFirstGroupField();
	}

	function setSqlFirstGroupField($v) {
		$this->_SqlFirstGroupField = $v;
	}

	// Select Group
	var $_SqlSelectGroup = "";

	function getSqlSelectGroup() {
		return ($this->_SqlSelectGroup <> "") ? $this->_SqlSelectGroup : "SELECT DISTINCT " . $this->getSqlFirstGroupField() . " FROM " . $this->getSqlFrom();
	}

	function SqlSelectGroup() { // For backward compatibility
		return $this->getSqlSelectGroup();
	}

	function setSqlSelectGroup($v) {
		$this->_SqlSelectGroup = $v;
	}

	// Order By Group
	var $_SqlOrderByGroup = "";

	function getSqlOrderByGroup() {
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "class_arrange.arrangedate ASC";
	}

	function SqlOrderByGroup() { // For backward compatibility
		return $this->getSqlOrderByGroup();
	}

	function setSqlOrderByGroup($v) {
		$this->_SqlOrderByGroup = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT <DistinctColumnFields> FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Group By Aggregate
	var $_SqlGroupByAgg = "";

	function getSqlGroupByAgg() {
		return ($this->_SqlGroupByAgg <> "") ? $this->_SqlGroupByAgg : "";
	}

	function SqlGroupByAgg() { // For backward compatibility
		return $this->getSqlGroupByAgg();
	}

	function setSqlGroupByAgg($v) {
		$this->_SqlGroupByAgg = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		return "";
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here	
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		// if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//     $filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
