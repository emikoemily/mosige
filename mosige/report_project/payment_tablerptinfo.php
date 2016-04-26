<?php

// Global variable for table object
$payment_table = NULL;

//
// Table class for payment_table
//
class crpayment_table extends crTableBase {
	var $id_payment_table;
	var $payment_id;
	var $member_id;
	var $payment_days;
	var $payment_startdate;
	var $payment_enddate;
	var $payment_price;
	var $payment_discount;
	var $is_archieved;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'payment_table';
		$this->TableName = 'payment_table';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// id_payment_table
		$this->id_payment_table = new crField('payment_table', 'payment_table', 'x_id_payment_table', 'id_payment_table', '`id_payment_table`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->id_payment_table->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['id_payment_table'] = &$this->id_payment_table;
		$this->id_payment_table->DateFilter = "";
		$this->id_payment_table->SqlSelect = "";
		$this->id_payment_table->SqlOrderBy = "";

		// payment_id
		$this->payment_id = new crField('payment_table', 'payment_table', 'x_payment_id', 'payment_id', '`payment_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['payment_id'] = &$this->payment_id;
		$this->payment_id->DateFilter = "";
		$this->payment_id->SqlSelect = "";
		$this->payment_id->SqlOrderBy = "";

		// member_id
		$this->member_id = new crField('payment_table', 'payment_table', 'x_member_id', 'member_id', '`member_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['member_id'] = &$this->member_id;
		$this->member_id->DateFilter = "";
		$this->member_id->SqlSelect = "";
		$this->member_id->SqlOrderBy = "";

		// payment_days
		$this->payment_days = new crField('payment_table', 'payment_table', 'x_payment_days', 'payment_days', '`payment_days`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->payment_days->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['payment_days'] = &$this->payment_days;
		$this->payment_days->DateFilter = "";
		$this->payment_days->SqlSelect = "";
		$this->payment_days->SqlOrderBy = "";

		// payment_startdate
		$this->payment_startdate = new crField('payment_table', 'payment_table', 'x_payment_startdate', 'payment_startdate', '`payment_startdate`', 135, EWR_DATATYPE_DATE, 5);
		$this->payment_startdate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['payment_startdate'] = &$this->payment_startdate;
		$this->payment_startdate->DateFilter = "";
		$this->payment_startdate->SqlSelect = "";
		$this->payment_startdate->SqlOrderBy = "";

		// payment_enddate
		$this->payment_enddate = new crField('payment_table', 'payment_table', 'x_payment_enddate', 'payment_enddate', '`payment_enddate`', 135, EWR_DATATYPE_DATE, 5);
		$this->payment_enddate->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['payment_enddate'] = &$this->payment_enddate;
		$this->payment_enddate->DateFilter = "";
		$this->payment_enddate->SqlSelect = "";
		$this->payment_enddate->SqlOrderBy = "";

		// payment_price
		$this->payment_price = new crField('payment_table', 'payment_table', 'x_payment_price', 'payment_price', '`payment_price`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->payment_price->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['payment_price'] = &$this->payment_price;
		$this->payment_price->DateFilter = "";
		$this->payment_price->SqlSelect = "";
		$this->payment_price->SqlOrderBy = "";

		// payment_discount
		$this->payment_discount = new crField('payment_table', 'payment_table', 'x_payment_discount', 'payment_discount', '`payment_discount`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['payment_discount'] = &$this->payment_discount;
		$this->payment_discount->DateFilter = "";
		$this->payment_discount->SqlSelect = "";
		$this->payment_discount->SqlOrderBy = "";

		// is_archieved
		$this->is_archieved = new crField('payment_table', 'payment_table', 'x_is_archieved', 'is_archieved', '`is_archieved`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->is_archieved->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['is_archieved'] = &$this->is_archieved;
		$this->is_archieved->DateFilter = "";
		$this->is_archieved->SqlSelect = "";
		$this->is_archieved->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`payment_table`";
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
