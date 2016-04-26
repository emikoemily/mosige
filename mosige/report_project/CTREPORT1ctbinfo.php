<?php

// Global variable for table object
$CTREPORT1 = NULL;

//
// Table class for CTREPORT1
//
class crCTREPORT1 extends crTableCrosstab {
	var $Chart1;
	var $member_id;
	var $register_time;
	var $member_name;
	var $class_name;
	var $class_description;
	var $arrangedate;
	var $weekday;
	var $starttime;
	var $endtime;
	var $totalnumber;
	var $innerid;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'CTREPORT1';
		$this->TableName = 'CTREPORT1';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// member_id
		$this->member_id = new crField('CTREPORT1', 'CTREPORT1', 'x_member_id', 'member_id', '`member_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->member_id->GroupingFieldId = 1;
		$this->member_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['member_id'] = &$this->member_id;
		$this->member_id->DateFilter = "";
		$this->member_id->SqlSelect = "SELECT DISTINCT `member_id` FROM " . $this->getSqlFrom();
		$this->member_id->SqlOrderBy = "`member_id`";

		// register_time
		$this->register_time = new crField('CTREPORT1', 'CTREPORT1', 'x_register_time', 'register_time', '`register_time`', 135, EWR_DATATYPE_DATE, 5);
		$this->register_time->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['register_time'] = &$this->register_time;
		$this->register_time->DateFilter = "";
		$this->register_time->SqlSelect = "";
		$this->register_time->SqlOrderBy = "";

		// member_name
		$this->member_name = new crField('CTREPORT1', 'CTREPORT1', 'x_member_name', 'member_name', '`member_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->member_name->GroupingFieldId = 5;
		$this->fields['member_name'] = &$this->member_name;
		$this->member_name->DateFilter = "";
		$this->member_name->SqlSelect = "";
		$this->member_name->SqlOrderBy = "";

		// class_name
		$this->class_name = new crField('CTREPORT1', 'CTREPORT1', 'x_class_name', 'class_name', '`class_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_name'] = &$this->class_name;
		$this->class_name->DateFilter = "";
		$this->class_name->SqlSelect = "SELECT DISTINCT `class_name` FROM " . $this->getSqlFrom();
		$this->class_name->SqlOrderBy = "`class_name`";

		// class_description
		$this->class_description = new crField('CTREPORT1', 'CTREPORT1', 'x_class_description', 'class_description', '`class_description`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_description'] = &$this->class_description;
		$this->class_description->DateFilter = "";
		$this->class_description->SqlSelect = "";
		$this->class_description->SqlOrderBy = "";

		// arrangedate
		$this->arrangedate = new crField('CTREPORT1', 'CTREPORT1', 'x_arrangedate', 'arrangedate', '`arrangedate`', 133, EWR_DATATYPE_DATE, 5);
		$this->arrangedate->GroupingFieldId = 2;
		$this->arrangedate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['arrangedate'] = &$this->arrangedate;
		$this->arrangedate->DateFilter = "";
		$this->arrangedate->SqlSelect = "";
		$this->arrangedate->SqlOrderBy = "";

		// weekday
		$this->weekday = new crField('CTREPORT1', 'CTREPORT1', 'x_weekday', 'weekday', '`weekday`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->weekday->GroupingFieldId = 3;
		$this->weekday->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['weekday'] = &$this->weekday;
		$this->weekday->DateFilter = "";
		$this->weekday->SqlSelect = "SELECT DISTINCT `weekday` FROM " . $this->getSqlFrom();
		$this->weekday->SqlOrderBy = "`weekday`";

		// starttime
		$this->starttime = new crField('CTREPORT1', 'CTREPORT1', 'x_starttime', 'starttime', '`starttime`', 134, EWR_DATATYPE_TIME, -1);
		$this->starttime->GroupingFieldId = 4;
		$this->starttime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['starttime'] = &$this->starttime;
		$this->starttime->DateFilter = "";
		$this->starttime->SqlSelect = "SELECT DISTINCT `starttime` FROM " . $this->getSqlFrom();
		$this->starttime->SqlOrderBy = "`starttime`";

		// endtime
		$this->endtime = new crField('CTREPORT1', 'CTREPORT1', 'x_endtime', 'endtime', '`endtime`', 134, EWR_DATATYPE_TIME, -1);
		$this->endtime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['endtime'] = &$this->endtime;
		$this->endtime->DateFilter = "";
		$this->endtime->SqlSelect = "";
		$this->endtime->SqlOrderBy = "";

		// totalnumber
		$this->totalnumber = new crField('CTREPORT1', 'CTREPORT1', 'x_totalnumber', 'totalnumber', '`totalnumber`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->totalnumber->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['totalnumber'] = &$this->totalnumber;
		$this->totalnumber->DateFilter = "";
		$this->totalnumber->SqlSelect = "";
		$this->totalnumber->SqlOrderBy = "";

		// innerid
		$this->innerid = new crField('CTREPORT1', 'CTREPORT1', 'x_innerid', 'innerid', '`innerid`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->innerid->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['innerid'] = &$this->innerid;
		$this->innerid->DateFilter = "";
		$this->innerid->SqlSelect = "";
		$this->innerid->SqlOrderBy = "";

		// Chart1
		$this->Chart1 = new crChart($this->DBID, 'CTREPORT1', 'CTREPORT1', 'Chart1', 'Chart1', 'weekday', 'totalnumber', '', 1, 'COUNT', 550, 440);
		$this->Chart1->SqlSelect = "SELECT `weekday`, '', COUNT(`totalnumber`) FROM ";
		$this->Chart1->SqlGroupBy = "`weekday`";
		$this->Chart1->SqlOrderBy = "";
		$this->Chart1->SeriesDateType = "";
		$this->Chart1->ChartDrillDownUrl = "view2rpt.php?d=1&t=view2&s=CTREPORT1&weekd=%0:weekd:3%";
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
		return ($this->ColumnField <> "") ? $this->ColumnField : "`class_name`";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`view1`";
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
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT `member_id`, `arrangedate`, `weekday`, `starttime`, `member_name`, <DistinctColumnFields> FROM " . $this->getSqlFrom();
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
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
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
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "`member_id`, `arrangedate`, `weekday`, `starttime`, `member_name`";
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
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "`member_id` ASC, `arrangedate` ASC, `weekday` ASC, `starttime` ASC, `member_name` ASC";
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
		return ($this->_SqlDistinctSelect <> "") ? $this->_SqlDistinctSelect : "SELECT DISTINCT `class_name` FROM `view1`";
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
		$sWhere = ($this->_SqlDistinctWhere <> "") ? $this->_SqlDistinctWhere : "";
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
		return ($this->_SqlDistinctOrderBy <> "") ? $this->_SqlDistinctOrderBy : "`class_name` ASC";
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
		$this->Col = &ewr_Init2DArray($this->ColCount+1, 6, NULL);
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

		$nGrps = 5;
		$this->SummaryFields[0] = new crSummaryField('x_totalnumber', 'totalnumber', '`totalnumber`', 'COUNT');
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
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "`member_id`";
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
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "`member_id` ASC";
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
