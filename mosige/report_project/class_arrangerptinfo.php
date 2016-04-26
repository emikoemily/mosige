<?php

// Global variable for table object
$class_arrange = NULL;

//
// Table class for class_arrange
//
class crclass_arrange extends crTableBase {
	var $arrange_id;
	var $class_id;
	var $teacher_id;
	var $arrangedate;
	var $starttime;
	var $endtime;
	var $maxposition;
	var $try_maxposition;
	var $registercount;
	var $try_registercount;
	var $classroom;
	var $overlap;
	var $otherid;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'class_arrange';
		$this->TableName = 'class_arrange';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// arrange_id
		$this->arrange_id = new crField('class_arrange', 'class_arrange', 'x_arrange_id', 'arrange_id', '`arrange_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->arrange_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['arrange_id'] = &$this->arrange_id;
		$this->arrange_id->DateFilter = "";
		$this->arrange_id->SqlSelect = "";
		$this->arrange_id->SqlOrderBy = "";

		// class_id
		$this->class_id = new crField('class_arrange', 'class_arrange', 'x_class_id', 'class_id', '`class_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['class_id'] = &$this->class_id;
		$this->class_id->DateFilter = "";
		$this->class_id->SqlSelect = "";
		$this->class_id->SqlOrderBy = "";

		// teacher_id
		$this->teacher_id = new crField('class_arrange', 'class_arrange', 'x_teacher_id', 'teacher_id', '`teacher_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['teacher_id'] = &$this->teacher_id;
		$this->teacher_id->DateFilter = "";
		$this->teacher_id->SqlSelect = "";
		$this->teacher_id->SqlOrderBy = "";

		// arrangedate
		$this->arrangedate = new crField('class_arrange', 'class_arrange', 'x_arrangedate', 'arrangedate', '`arrangedate`', 133, EWR_DATATYPE_DATE, 5);
		$this->arrangedate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['arrangedate'] = &$this->arrangedate;
		$this->arrangedate->DateFilter = "";
		$this->arrangedate->SqlSelect = "";
		$this->arrangedate->SqlOrderBy = "";

		// starttime
		$this->starttime = new crField('class_arrange', 'class_arrange', 'x_starttime', 'starttime', '`starttime`', 134, EWR_DATATYPE_TIME, -1);
		$this->starttime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['starttime'] = &$this->starttime;
		$this->starttime->DateFilter = "";
		$this->starttime->SqlSelect = "";
		$this->starttime->SqlOrderBy = "";

		// endtime
		$this->endtime = new crField('class_arrange', 'class_arrange', 'x_endtime', 'endtime', '`endtime`', 134, EWR_DATATYPE_TIME, -1);
		$this->endtime->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectTime");
		$this->fields['endtime'] = &$this->endtime;
		$this->endtime->DateFilter = "";
		$this->endtime->SqlSelect = "";
		$this->endtime->SqlOrderBy = "";

		// maxposition
		$this->maxposition = new crField('class_arrange', 'class_arrange', 'x_maxposition', 'maxposition', '`maxposition`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->maxposition->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['maxposition'] = &$this->maxposition;
		$this->maxposition->DateFilter = "";
		$this->maxposition->SqlSelect = "";
		$this->maxposition->SqlOrderBy = "";

		// try_maxposition
		$this->try_maxposition = new crField('class_arrange', 'class_arrange', 'x_try_maxposition', 'try_maxposition', '`try_maxposition`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->try_maxposition->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['try_maxposition'] = &$this->try_maxposition;
		$this->try_maxposition->DateFilter = "";
		$this->try_maxposition->SqlSelect = "";
		$this->try_maxposition->SqlOrderBy = "";

		// registercount
		$this->registercount = new crField('class_arrange', 'class_arrange', 'x_registercount', 'registercount', '`registercount`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->registercount->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['registercount'] = &$this->registercount;
		$this->registercount->DateFilter = "";
		$this->registercount->SqlSelect = "";
		$this->registercount->SqlOrderBy = "";

		// try_registercount
		$this->try_registercount = new crField('class_arrange', 'class_arrange', 'x_try_registercount', 'try_registercount', '`try_registercount`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->try_registercount->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['try_registercount'] = &$this->try_registercount;
		$this->try_registercount->DateFilter = "";
		$this->try_registercount->SqlSelect = "";
		$this->try_registercount->SqlOrderBy = "";

		// classroom
		$this->classroom = new crField('class_arrange', 'class_arrange', 'x_classroom', 'classroom', '`classroom`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['classroom'] = &$this->classroom;
		$this->classroom->DateFilter = "";
		$this->classroom->SqlSelect = "";
		$this->classroom->SqlOrderBy = "";

		// overlap
		$this->overlap = new crField('class_arrange', 'class_arrange', 'x_overlap', 'overlap', '`overlap`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['overlap'] = &$this->overlap;
		$this->overlap->DateFilter = "";
		$this->overlap->SqlSelect = "";
		$this->overlap->SqlOrderBy = "";

		// otherid
		$this->otherid = new crField('class_arrange', 'class_arrange', 'x_otherid', 'otherid', '`otherid`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['otherid'] = &$this->otherid;
		$this->otherid->DateFilter = "";
		$this->otherid->SqlSelect = "";
		$this->otherid->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`class_arrange`";
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
