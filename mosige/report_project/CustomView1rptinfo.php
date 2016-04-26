<?php

// Global variable for table object
$CustomView1 = NULL;

//
// Table class for CustomView1
//
class crCustomView1 extends crTableBase {
	var $countByWeekday;
	var $countByMember;
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
		$this->TableVar = 'CustomView1';
		$this->TableName = 'CustomView1';
		$this->TableType = 'CUSTOMVIEW';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// member_name
		$this->member_name = new crField('CustomView1', 'CustomView1', 'x_member_name', 'member_name', 'member_user.member_name', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_name'] = &$this->member_name;
		$this->member_name->DateFilter = "";
		$this->member_name->SqlSelect = "SELECT DISTINCT member_user.member_name FROM " . $this->getSqlFrom();
		$this->member_name->SqlOrderBy = "member_user.member_name";

		// is_attended
		$this->is_attended = new crField('CustomView1', 'CustomView1', 'x_is_attended', 'is_attended', 'register_record.is_attended', 2, EWR_DATATYPE_NUMBER, -1);
		$this->is_attended->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['is_attended'] = &$this->is_attended;
		$this->is_attended->DateFilter = "";
		$this->is_attended->SqlSelect = "";
		$this->is_attended->SqlOrderBy = "";

		// arrangedate
		$this->arrangedate = new crField('CustomView1', 'CustomView1', 'x_arrangedate', 'arrangedate', 'class_arrange.arrangedate', 133, EWR_DATATYPE_DATE, 5);
		$this->arrangedate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['arrangedate'] = &$this->arrangedate;
		$this->arrangedate->DateFilter = "";
		$this->arrangedate->SqlSelect = "SELECT DISTINCT class_arrange.arrangedate FROM " . $this->getSqlFrom();
		$this->arrangedate->SqlOrderBy = "class_arrange.arrangedate";

		// starttime
		$this->starttime = new crField('CustomView1', 'CustomView1', 'x_starttime', 'starttime', 'class_arrange.starttime', 134, EWR_DATATYPE_TIME, -1);
		$this->starttime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['starttime'] = &$this->starttime;
		$this->starttime->DateFilter = "";
		$this->starttime->SqlSelect = "SELECT DISTINCT class_arrange.starttime FROM " . $this->getSqlFrom();
		$this->starttime->SqlOrderBy = "class_arrange.starttime";

		// class_name
		$this->class_name = new crField('CustomView1', 'CustomView1', 'x_class_name', 'class_name', 'class_design.class_name', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_name'] = &$this->class_name;
		$this->class_name->DateFilter = "";
		$this->class_name->SqlSelect = "SELECT DISTINCT class_design.class_name FROM " . $this->getSqlFrom();
		$this->class_name->SqlOrderBy = "class_design.class_name";

		// class_description
		$this->class_description = new crField('CustomView1', 'CustomView1', 'x_class_description', 'class_description', 'class_design.class_description', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_description'] = &$this->class_description;
		$this->class_description->DateFilter = "";
		$this->class_description->SqlSelect = "SELECT DISTINCT class_design.class_description FROM " . $this->getSqlFrom();
		$this->class_description->SqlOrderBy = "class_design.class_description";

		// inner_id
		$this->inner_id = new crField('CustomView1', 'CustomView1', 'x_inner_id', 'inner_id', 'class_design.inner_id', 3, EWR_DATATYPE_NUMBER, -1);
		$this->inner_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['inner_id'] = &$this->inner_id;
		$this->inner_id->DateFilter = "";
		$this->inner_id->SqlSelect = "SELECT DISTINCT class_design.inner_id FROM " . $this->getSqlFrom();
		$this->inner_id->SqlOrderBy = "class_design.inner_id";

		// register_time
		$this->register_time = new crField('CustomView1', 'CustomView1', 'x_register_time', 'register_time', 'register_record.register_time', 135, EWR_DATATYPE_DATE, 5);
		$this->register_time->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['register_time'] = &$this->register_time;
		$this->register_time->DateFilter = "";
		$this->register_time->SqlSelect = "SELECT DISTINCT register_record.register_time FROM " . $this->getSqlFrom();
		$this->register_time->SqlOrderBy = "register_record.register_time";

		// weekday
		$this->weekday = new crField('CustomView1', 'CustomView1', 'x_weekday', 'weekday', 'DayOfWeek(class_arrange.arrangedate) - 1', 20, EWR_DATATYPE_NUMBER, -1);
		$this->weekday->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['weekday'] = &$this->weekday;
		$this->weekday->DateFilter = "";
		$this->weekday->SqlSelect = "SELECT DISTINCT DayOfWeek(class_arrange.arrangedate) - 1 FROM " . $this->getSqlFrom();
		$this->weekday->SqlOrderBy = "DayOfWeek(class_arrange.arrangedate) - 1";

		// countByWeekday
		$this->countByWeekday = new crChart($this->DBID, 'CustomView1', 'CustomView1', 'countByWeekday', 'countByWeekday', 'weekday', 'is_attended', '', 3, 'COUNT', 550, 440);
		$this->countByWeekday->SqlSelect = "SELECT `weekday`, '', COUNT(`is_attended`) FROM ";
		$this->countByWeekday->SqlGroupBy = "`weekday`";
		$this->countByWeekday->SqlOrderBy = "";
		$this->countByWeekday->SeriesDateType = "";

		// countByMember
		$this->countByMember = new crChart($this->DBID, 'CustomView1', 'CustomView1', 'countByMember', 'countByMember', 'member_name', 'is_attended', '', 2, 'COUNT', 550, 440);
		$this->countByMember->SqlSelect = "SELECT `member_name`, '', COUNT(`is_attended`) FROM ";
		$this->countByMember->SqlGroupBy = "`member_name`";
		$this->countByMember->SqlOrderBy = "";
		$this->countByMember->SeriesDateType = "";
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
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT member_user.member_name, register_record.is_attended, class_arrange.arrangedate, class_arrange.starttime, class_design.class_name, class_design.class_description, class_design.inner_id, register_record.register_time, DayOfWeek(class_arrange.arrangedate) - 1 As weekday FROM " . $this->getSqlFrom();
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
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "register_record.is_attended > 0 And register_record.member_id != 73 And register_record.member_id != 60 And register_record.member_id != 48 And register_record.member_id != 61 And register_record.member_id != 74 And register_record.member_id != 76 And register_record.member_id != 48 And register_record.member_id != 102 And register_record.member_id != 349 And register_record.member_id != 329";
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
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "member_user.member_name, register_record.is_attended, class_arrange.arrangedate, class_arrange.starttime, class_design.class_name, class_design.class_description, class_design.inner_id, register_record.register_time";
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
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "class_design.inner_id";
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
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT * FROM " . $this->getSqlFrom();
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
