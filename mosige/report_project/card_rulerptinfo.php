<?php

// Global variable for table object
$card_rule = NULL;

//
// Table class for card_rule
//
class crcard_rule extends crTableBase {
	var $rule_id;
	var $rule_name;
	var $level_actual;
	var $rule_alias;
	var $rule_displayname;
	var $rule_days;
	var $rule_startdate;
	var $rule_enddate;
	var $rule_description;
	var $rule_maxcount;
	var $has_kongzhong;
	var $has_ertong;
	var $time_rule;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'card_rule';
		$this->TableName = 'card_rule';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// rule_id
		$this->rule_id = new crField('card_rule', 'card_rule', 'x_rule_id', 'rule_id', '`rule_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->rule_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['rule_id'] = &$this->rule_id;
		$this->rule_id->DateFilter = "";
		$this->rule_id->SqlSelect = "";
		$this->rule_id->SqlOrderBy = "";

		// rule_name
		$this->rule_name = new crField('card_rule', 'card_rule', 'x_rule_name', 'rule_name', '`rule_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['rule_name'] = &$this->rule_name;
		$this->rule_name->DateFilter = "";
		$this->rule_name->SqlSelect = "";
		$this->rule_name->SqlOrderBy = "";

		// level_actual
		$this->level_actual = new crField('card_rule', 'card_rule', 'x_level_actual', 'level_actual', '`level_actual`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['level_actual'] = &$this->level_actual;
		$this->level_actual->DateFilter = "";
		$this->level_actual->SqlSelect = "";
		$this->level_actual->SqlOrderBy = "";

		// rule_alias
		$this->rule_alias = new crField('card_rule', 'card_rule', 'x_rule_alias', 'rule_alias', '`rule_alias`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['rule_alias'] = &$this->rule_alias;
		$this->rule_alias->DateFilter = "";
		$this->rule_alias->SqlSelect = "";
		$this->rule_alias->SqlOrderBy = "";

		// rule_displayname
		$this->rule_displayname = new crField('card_rule', 'card_rule', 'x_rule_displayname', 'rule_displayname', '`rule_displayname`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['rule_displayname'] = &$this->rule_displayname;
		$this->rule_displayname->DateFilter = "";
		$this->rule_displayname->SqlSelect = "";
		$this->rule_displayname->SqlOrderBy = "";

		// rule_days
		$this->rule_days = new crField('card_rule', 'card_rule', 'x_rule_days', 'rule_days', '`rule_days`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->rule_days->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['rule_days'] = &$this->rule_days;
		$this->rule_days->DateFilter = "";
		$this->rule_days->SqlSelect = "";
		$this->rule_days->SqlOrderBy = "";

		// rule_startdate
		$this->rule_startdate = new crField('card_rule', 'card_rule', 'x_rule_startdate', 'rule_startdate', '`rule_startdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->rule_startdate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['rule_startdate'] = &$this->rule_startdate;
		$this->rule_startdate->DateFilter = "";
		$this->rule_startdate->SqlSelect = "";
		$this->rule_startdate->SqlOrderBy = "";

		// rule_enddate
		$this->rule_enddate = new crField('card_rule', 'card_rule', 'x_rule_enddate', 'rule_enddate', '`rule_enddate`', 135, EWR_DATATYPE_DATE, 5);
		$this->rule_enddate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['rule_enddate'] = &$this->rule_enddate;
		$this->rule_enddate->DateFilter = "";
		$this->rule_enddate->SqlSelect = "";
		$this->rule_enddate->SqlOrderBy = "";

		// rule_description
		$this->rule_description = new crField('card_rule', 'card_rule', 'x_rule_description', 'rule_description', '`rule_description`', 201, EWR_DATATYPE_MEMO, -1);
		$this->fields['rule_description'] = &$this->rule_description;
		$this->rule_description->DateFilter = "";
		$this->rule_description->SqlSelect = "";
		$this->rule_description->SqlOrderBy = "";

		// rule_maxcount
		$this->rule_maxcount = new crField('card_rule', 'card_rule', 'x_rule_maxcount', 'rule_maxcount', '`rule_maxcount`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->rule_maxcount->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['rule_maxcount'] = &$this->rule_maxcount;
		$this->rule_maxcount->DateFilter = "";
		$this->rule_maxcount->SqlSelect = "";
		$this->rule_maxcount->SqlOrderBy = "";

		// has_kongzhong
		$this->has_kongzhong = new crField('card_rule', 'card_rule', 'x_has_kongzhong', 'has_kongzhong', '`has_kongzhong`', 16, EWR_DATATYPE_NUMBER, -1);
		$this->has_kongzhong->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['has_kongzhong'] = &$this->has_kongzhong;
		$this->has_kongzhong->DateFilter = "";
		$this->has_kongzhong->SqlSelect = "";
		$this->has_kongzhong->SqlOrderBy = "";

		// has_ertong
		$this->has_ertong = new crField('card_rule', 'card_rule', 'x_has_ertong', 'has_ertong', '`has_ertong`', 16, EWR_DATATYPE_NUMBER, -1);
		$this->has_ertong->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['has_ertong'] = &$this->has_ertong;
		$this->has_ertong->DateFilter = "";
		$this->has_ertong->SqlSelect = "";
		$this->has_ertong->SqlOrderBy = "";

		// time_rule
		$this->time_rule = new crField('card_rule', 'card_rule', 'x_time_rule', 'time_rule', '`time_rule`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['time_rule'] = &$this->time_rule;
		$this->time_rule->DateFilter = "";
		$this->time_rule->SqlSelect = "";
		$this->time_rule->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`card_rule`";
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
