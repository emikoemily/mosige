<?php

// Global variable for table object
$view1 = NULL;

//
// Table class for view1
//
class crview1 extends crTableBase {
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
		$this->TableVar = 'view1';
		$this->TableName = 'view1';
		$this->TableType = 'VIEW';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// member_id
		$this->member_id = new crField('view1', 'view1', 'x_member_id', 'member_id', '`member_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->member_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['member_id'] = &$this->member_id;
		$this->member_id->DateFilter = "";
		$this->member_id->SqlSelect = "SELECT DISTINCT `member_id` FROM " . $this->getSqlFrom();
		$this->member_id->SqlOrderBy = "`member_id`";

		// register_time
		$this->register_time = new crField('view1', 'view1', 'x_register_time', 'register_time', '`register_time`', 135, EWR_DATATYPE_DATE, 5);
		$this->register_time->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['register_time'] = &$this->register_time;
		$this->register_time->DateFilter = "";
		$this->register_time->SqlSelect = "SELECT DISTINCT `register_time` FROM " . $this->getSqlFrom();
		$this->register_time->SqlOrderBy = "`register_time`";

		// member_name
		$this->member_name = new crField('view1', 'view1', 'x_member_name', 'member_name', '`member_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_name'] = &$this->member_name;
		$this->member_name->DateFilter = "";
		$this->member_name->SqlSelect = "SELECT DISTINCT `member_name` FROM " . $this->getSqlFrom();
		$this->member_name->SqlOrderBy = "`member_name`";

		// class_name
		$this->class_name = new crField('view1', 'view1', 'x_class_name', 'class_name', '`class_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_name'] = &$this->class_name;
		$this->class_name->DateFilter = "";
		$this->class_name->SqlSelect = "SELECT DISTINCT `class_name` FROM " . $this->getSqlFrom();
		$this->class_name->SqlOrderBy = "`class_name`";

		// class_description
		$this->class_description = new crField('view1', 'view1', 'x_class_description', 'class_description', '`class_description`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_description'] = &$this->class_description;
		$this->class_description->DateFilter = "";
		$this->class_description->SqlSelect = "SELECT DISTINCT `class_description` FROM " . $this->getSqlFrom();
		$this->class_description->SqlOrderBy = "`class_description`";

		// arrangedate
		$this->arrangedate = new crField('view1', 'view1', 'x_arrangedate', 'arrangedate', '`arrangedate`', 133, EWR_DATATYPE_DATE, 5);
		$this->arrangedate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['arrangedate'] = &$this->arrangedate;
		$this->arrangedate->DateFilter = "";
		$this->arrangedate->SqlSelect = "SELECT DISTINCT `arrangedate` FROM " . $this->getSqlFrom();
		$this->arrangedate->SqlOrderBy = "`arrangedate`";

		// weekday
		$this->weekday = new crField('view1', 'view1', 'x_weekday', 'weekday', '`weekday`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->weekday->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['weekday'] = &$this->weekday;
		$this->weekday->DateFilter = "";
		$this->weekday->SqlSelect = "SELECT DISTINCT `weekday` FROM " . $this->getSqlFrom();
		$this->weekday->SqlOrderBy = "`weekday`";

		// starttime
		$this->starttime = new crField('view1', 'view1', 'x_starttime', 'starttime', '`starttime`', 134, EWR_DATATYPE_TIME, -1);
		$this->starttime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['starttime'] = &$this->starttime;
		$this->starttime->DateFilter = "";
		$this->starttime->SqlSelect = "SELECT DISTINCT `starttime` FROM " . $this->getSqlFrom();
		$this->starttime->SqlOrderBy = "`starttime`";

		// endtime
		$this->endtime = new crField('view1', 'view1', 'x_endtime', 'endtime', '`endtime`', 134, EWR_DATATYPE_TIME, -1);
		$this->endtime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['endtime'] = &$this->endtime;
		$this->endtime->DateFilter = "";
		$this->endtime->SqlSelect = "SELECT DISTINCT `endtime` FROM " . $this->getSqlFrom();
		$this->endtime->SqlOrderBy = "`endtime`";

		// totalnumber
		$this->totalnumber = new crField('view1', 'view1', 'x_totalnumber', 'totalnumber', '`totalnumber`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->totalnumber->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['totalnumber'] = &$this->totalnumber;
		$this->totalnumber->DateFilter = "";
		$this->totalnumber->SqlSelect = "SELECT DISTINCT `totalnumber` FROM " . $this->getSqlFrom();
		$this->totalnumber->SqlOrderBy = "`totalnumber`";

		// innerid
		$this->innerid = new crField('view1', 'view1', 'x_innerid', 'innerid', '`innerid`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->innerid->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['innerid'] = &$this->innerid;
		$this->innerid->DateFilter = "";
		$this->innerid->SqlSelect = "SELECT DISTINCT `innerid` FROM " . $this->getSqlFrom();
		$this->innerid->SqlOrderBy = "`innerid`";
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
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
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
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
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
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT COUNT(*) AS `cnt_member_id` FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";

	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "";
	}

	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}

	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";

	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "";
	}

	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}

	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";

	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}

	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}

	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
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
