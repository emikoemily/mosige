<?php

// Global variable for table object
$try_member_user = NULL;

//
// Table class for try_member_user
//
class crtry_member_user extends crTableBase {
	var $id_try;
	var $member_id;
	var $member_name;
	var $member_sex;
	var $member_cell;
	var $member_email;
	var $member_level;
	var $member_points;
	var $member_weixinid;
	var $member_cardid;
	var $member_password;
	var $member_enddate;
	var $member_regtime;
	var $member_startdate;
	var $member_days;
	var $member_classcount;
	var $member_attendmax;
	var $member_intro;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'try_member_user';
		$this->TableName = 'try_member_user';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// id_try
		$this->id_try = new crField('try_member_user', 'try_member_user', 'x_id_try', 'id_try', '`id_try`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->id_try->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['id_try'] = &$this->id_try;
		$this->id_try->DateFilter = "";
		$this->id_try->SqlSelect = "";
		$this->id_try->SqlOrderBy = "";

		// member_id
		$this->member_id = new crField('try_member_user', 'try_member_user', 'x_member_id', 'member_id', '`member_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_id'] = &$this->member_id;
		$this->member_id->DateFilter = "";
		$this->member_id->SqlSelect = "";
		$this->member_id->SqlOrderBy = "";

		// member_name
		$this->member_name = new crField('try_member_user', 'try_member_user', 'x_member_name', 'member_name', '`member_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_name'] = &$this->member_name;
		$this->member_name->DateFilter = "";
		$this->member_name->SqlSelect = "";
		$this->member_name->SqlOrderBy = "";

		// member_sex
		$this->member_sex = new crField('try_member_user', 'try_member_user', 'x_member_sex', 'member_sex', '`member_sex`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_sex'] = &$this->member_sex;
		$this->member_sex->DateFilter = "";
		$this->member_sex->SqlSelect = "";
		$this->member_sex->SqlOrderBy = "";

		// member_cell
		$this->member_cell = new crField('try_member_user', 'try_member_user', 'x_member_cell', 'member_cell', '`member_cell`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_cell'] = &$this->member_cell;
		$this->member_cell->DateFilter = "";
		$this->member_cell->SqlSelect = "";
		$this->member_cell->SqlOrderBy = "";

		// member_email
		$this->member_email = new crField('try_member_user', 'try_member_user', 'x_member_email', 'member_email', '`member_email`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_email'] = &$this->member_email;
		$this->member_email->DateFilter = "";
		$this->member_email->SqlSelect = "";
		$this->member_email->SqlOrderBy = "";

		// member_level
		$this->member_level = new crField('try_member_user', 'try_member_user', 'x_member_level', 'member_level', '`member_level`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_level'] = &$this->member_level;
		$this->member_level->DateFilter = "";
		$this->member_level->SqlSelect = "";
		$this->member_level->SqlOrderBy = "";

		// member_points
		$this->member_points = new crField('try_member_user', 'try_member_user', 'x_member_points', 'member_points', '`member_points`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_points'] = &$this->member_points;
		$this->member_points->DateFilter = "";
		$this->member_points->SqlSelect = "";
		$this->member_points->SqlOrderBy = "";

		// member_weixinid
		$this->member_weixinid = new crField('try_member_user', 'try_member_user', 'x_member_weixinid', 'member_weixinid', '`member_weixinid`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_weixinid'] = &$this->member_weixinid;
		$this->member_weixinid->DateFilter = "";
		$this->member_weixinid->SqlSelect = "";
		$this->member_weixinid->SqlOrderBy = "";

		// member_cardid
		$this->member_cardid = new crField('try_member_user', 'try_member_user', 'x_member_cardid', 'member_cardid', '`member_cardid`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_cardid'] = &$this->member_cardid;
		$this->member_cardid->DateFilter = "";
		$this->member_cardid->SqlSelect = "";
		$this->member_cardid->SqlOrderBy = "";

		// member_password
		$this->member_password = new crField('try_member_user', 'try_member_user', 'x_member_password', 'member_password', '`member_password`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_password'] = &$this->member_password;
		$this->member_password->DateFilter = "";
		$this->member_password->SqlSelect = "";
		$this->member_password->SqlOrderBy = "";

		// member_enddate
		$this->member_enddate = new crField('try_member_user', 'try_member_user', 'x_member_enddate', 'member_enddate', '`member_enddate`', 135, EWR_DATATYPE_DATE, 5);
		$this->member_enddate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['member_enddate'] = &$this->member_enddate;
		$this->member_enddate->DateFilter = "";
		$this->member_enddate->SqlSelect = "";
		$this->member_enddate->SqlOrderBy = "";

		// member_regtime
		$this->member_regtime = new crField('try_member_user', 'try_member_user', 'x_member_regtime', 'member_regtime', '`member_regtime`', 135, EWR_DATATYPE_DATE, 5);
		$this->member_regtime->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['member_regtime'] = &$this->member_regtime;
		$this->member_regtime->DateFilter = "";
		$this->member_regtime->SqlSelect = "";
		$this->member_regtime->SqlOrderBy = "";

		// member_startdate
		$this->member_startdate = new crField('try_member_user', 'try_member_user', 'x_member_startdate', 'member_startdate', '`member_startdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->member_startdate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['member_startdate'] = &$this->member_startdate;
		$this->member_startdate->DateFilter = "";
		$this->member_startdate->SqlSelect = "";
		$this->member_startdate->SqlOrderBy = "";

		// member_days
		$this->member_days = new crField('try_member_user', 'try_member_user', 'x_member_days', 'member_days', '`member_days`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->member_days->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['member_days'] = &$this->member_days;
		$this->member_days->DateFilter = "";
		$this->member_days->SqlSelect = "";
		$this->member_days->SqlOrderBy = "";

		// member_classcount
		$this->member_classcount = new crField('try_member_user', 'try_member_user', 'x_member_classcount', 'member_classcount', '`member_classcount`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->member_classcount->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['member_classcount'] = &$this->member_classcount;
		$this->member_classcount->DateFilter = "";
		$this->member_classcount->SqlSelect = "";
		$this->member_classcount->SqlOrderBy = "";

		// member_attendmax
		$this->member_attendmax = new crField('try_member_user', 'try_member_user', 'x_member_attendmax', 'member_attendmax', '`member_attendmax`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->member_attendmax->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['member_attendmax'] = &$this->member_attendmax;
		$this->member_attendmax->DateFilter = "";
		$this->member_attendmax->SqlSelect = "";
		$this->member_attendmax->SqlOrderBy = "";

		// member_intro
		$this->member_intro = new crField('try_member_user', 'try_member_user', 'x_member_intro', 'member_intro', '`member_intro`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_intro'] = &$this->member_intro;
		$this->member_intro->DateFilter = "";
		$this->member_intro->SqlSelect = "";
		$this->member_intro->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`try_member_user`";
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
