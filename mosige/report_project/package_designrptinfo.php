<?php

// Global variable for table object
$package_design = NULL;

//
// Table class for package_design
//
class crpackage_design extends crTableBase {
	var $idclass_package_design;
	var $package_id;
	var $package_name;
	var $package_description;
	var $package_course_count;
	var $package_price;
	var $weiddianlink;
	var $ref_common;
	var $star_nandu;
	var $star_liliang;
	var $star_shiyong;
	var $star_suxing;
	var $star_liliao;
	var $star_quwei;
	var $star_ranzhi;
	var $star_shushi;
	var $star_lachen;
	var $star_pingheng;
	var $star_rouren;
	var $star_chuanlian;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'package_design';
		$this->TableName = 'package_design';
		$this->TableType = 'TABLE';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// idclass_package_design
		$this->idclass_package_design = new crField('package_design', 'package_design', 'x_idclass_package_design', 'idclass_package_design', '`idclass_package_design`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->idclass_package_design->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['idclass_package_design'] = &$this->idclass_package_design;
		$this->idclass_package_design->DateFilter = "";
		$this->idclass_package_design->SqlSelect = "";
		$this->idclass_package_design->SqlOrderBy = "";

		// package_id
		$this->package_id = new crField('package_design', 'package_design', 'x_package_id', 'package_id', '`package_id`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['package_id'] = &$this->package_id;
		$this->package_id->DateFilter = "";
		$this->package_id->SqlSelect = "";
		$this->package_id->SqlOrderBy = "";

		// package_name
		$this->package_name = new crField('package_design', 'package_design', 'x_package_name', 'package_name', '`package_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['package_name'] = &$this->package_name;
		$this->package_name->DateFilter = "";
		$this->package_name->SqlSelect = "";
		$this->package_name->SqlOrderBy = "";

		// package_description
		$this->package_description = new crField('package_design', 'package_design', 'x_package_description', 'package_description', '`package_description`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['package_description'] = &$this->package_description;
		$this->package_description->DateFilter = "";
		$this->package_description->SqlSelect = "";
		$this->package_description->SqlOrderBy = "";

		// package_course_count
		$this->package_course_count = new crField('package_design', 'package_design', 'x_package_course_count', 'package_course_count', '`package_course_count`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->package_course_count->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['package_course_count'] = &$this->package_course_count;
		$this->package_course_count->DateFilter = "";
		$this->package_course_count->SqlSelect = "";
		$this->package_course_count->SqlOrderBy = "";

		// package_price
		$this->package_price = new crField('package_design', 'package_design', 'x_package_price', 'package_price', '`package_price`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->package_price->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['package_price'] = &$this->package_price;
		$this->package_price->DateFilter = "";
		$this->package_price->SqlSelect = "";
		$this->package_price->SqlOrderBy = "";

		// weiddianlink
		$this->weiddianlink = new crField('package_design', 'package_design', 'x_weiddianlink', 'weiddianlink', '`weiddianlink`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['weiddianlink'] = &$this->weiddianlink;
		$this->weiddianlink->DateFilter = "";
		$this->weiddianlink->SqlSelect = "";
		$this->weiddianlink->SqlOrderBy = "";

		// ref_common
		$this->ref_common = new crField('package_design', 'package_design', 'x_ref_common', 'ref_common', '`ref_common`', 200, EWR_DATATYPE_STRING, -1);
		$this->fields['ref_common'] = &$this->ref_common;
		$this->ref_common->DateFilter = "";
		$this->ref_common->SqlSelect = "";
		$this->ref_common->SqlOrderBy = "";

		// star_nandu
		$this->star_nandu = new crField('package_design', 'package_design', 'x_star_nandu', 'star_nandu', '`star_nandu`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_nandu->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_nandu'] = &$this->star_nandu;
		$this->star_nandu->DateFilter = "";
		$this->star_nandu->SqlSelect = "";
		$this->star_nandu->SqlOrderBy = "";

		// star_liliang
		$this->star_liliang = new crField('package_design', 'package_design', 'x_star_liliang', 'star_liliang', '`star_liliang`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_liliang->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_liliang'] = &$this->star_liliang;
		$this->star_liliang->DateFilter = "";
		$this->star_liliang->SqlSelect = "";
		$this->star_liliang->SqlOrderBy = "";

		// star_shiyong
		$this->star_shiyong = new crField('package_design', 'package_design', 'x_star_shiyong', 'star_shiyong', '`star_shiyong`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_shiyong->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_shiyong'] = &$this->star_shiyong;
		$this->star_shiyong->DateFilter = "";
		$this->star_shiyong->SqlSelect = "";
		$this->star_shiyong->SqlOrderBy = "";

		// star_suxing
		$this->star_suxing = new crField('package_design', 'package_design', 'x_star_suxing', 'star_suxing', '`star_suxing`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_suxing->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_suxing'] = &$this->star_suxing;
		$this->star_suxing->DateFilter = "";
		$this->star_suxing->SqlSelect = "";
		$this->star_suxing->SqlOrderBy = "";

		// star_liliao
		$this->star_liliao = new crField('package_design', 'package_design', 'x_star_liliao', 'star_liliao', '`star_liliao`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_liliao->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_liliao'] = &$this->star_liliao;
		$this->star_liliao->DateFilter = "";
		$this->star_liliao->SqlSelect = "";
		$this->star_liliao->SqlOrderBy = "";

		// star_quwei
		$this->star_quwei = new crField('package_design', 'package_design', 'x_star_quwei', 'star_quwei', '`star_quwei`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_quwei->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_quwei'] = &$this->star_quwei;
		$this->star_quwei->DateFilter = "";
		$this->star_quwei->SqlSelect = "";
		$this->star_quwei->SqlOrderBy = "";

		// star_ranzhi
		$this->star_ranzhi = new crField('package_design', 'package_design', 'x_star_ranzhi', 'star_ranzhi', '`star_ranzhi`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_ranzhi->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_ranzhi'] = &$this->star_ranzhi;
		$this->star_ranzhi->DateFilter = "";
		$this->star_ranzhi->SqlSelect = "";
		$this->star_ranzhi->SqlOrderBy = "";

		// star_shushi
		$this->star_shushi = new crField('package_design', 'package_design', 'x_star_shushi', 'star_shushi', '`star_shushi`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_shushi->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_shushi'] = &$this->star_shushi;
		$this->star_shushi->DateFilter = "";
		$this->star_shushi->SqlSelect = "";
		$this->star_shushi->SqlOrderBy = "";

		// star_lachen
		$this->star_lachen = new crField('package_design', 'package_design', 'x_star_lachen', 'star_lachen', '`star_lachen`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_lachen->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_lachen'] = &$this->star_lachen;
		$this->star_lachen->DateFilter = "";
		$this->star_lachen->SqlSelect = "";
		$this->star_lachen->SqlOrderBy = "";

		// star_pingheng
		$this->star_pingheng = new crField('package_design', 'package_design', 'x_star_pingheng', 'star_pingheng', '`star_pingheng`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_pingheng->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_pingheng'] = &$this->star_pingheng;
		$this->star_pingheng->DateFilter = "";
		$this->star_pingheng->SqlSelect = "";
		$this->star_pingheng->SqlOrderBy = "";

		// star_rouren
		$this->star_rouren = new crField('package_design', 'package_design', 'x_star_rouren', 'star_rouren', '`star_rouren`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_rouren->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_rouren'] = &$this->star_rouren;
		$this->star_rouren->DateFilter = "";
		$this->star_rouren->SqlSelect = "";
		$this->star_rouren->SqlOrderBy = "";

		// star_chuanlian
		$this->star_chuanlian = new crField('package_design', 'package_design', 'x_star_chuanlian', 'star_chuanlian', '`star_chuanlian`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->star_chuanlian->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['star_chuanlian'] = &$this->star_chuanlian;
		$this->star_chuanlian->DateFilter = "";
		$this->star_chuanlian->SqlSelect = "";
		$this->star_chuanlian->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`package_design`";
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
