<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "cv1ctbinfo.php" ?>
<?php

//
// Page class
//

$cv1_crosstab = NULL; // Initialize page object first

class crcv1_crosstab extends crcv1 {

	// Page ID
	var $PageID = 'crosstab';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'cv1_crosstab';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (cv1)
		if (!isset($GLOBALS["cv1"])) {
			$GLOBALS["cv1"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["cv1"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'crosstab', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'cv1', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fcv1crosstab";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		// Setup export options

		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $ReportLanguage;
		$exportid = session_id();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = FALSE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_cv1\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_cv1',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fcv1crosstab\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = FALSE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fcv1crosstab\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fcv1crosstab\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				ob_end_clean();
				echo $this->$fn($sContent);
				ewr_CloseConn(); // Close connection
				exit();
			} else {
				$this->$fn($sContent);
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $DisplayGrps = 3; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpFldCount = 0;
	var $ColSpan;
	var $GrpIdx;

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

		// Get sort
		$this->Sort = $this->GetSort();

		// Popup values and selections
		$this->arrangedate->SelectionList = "";
		$this->arrangedate->DefaultSelectionList = "";
		$this->arrangedate->ValueList = "";
		$this->starttime->SelectionList = "";
		$this->starttime->DefaultSelectionList = "";
		$this->starttime->ValueList = "";
		$this->member_name->SelectionList = "";
		$this->member_name->DefaultSelectionList = "";
		$this->member_name->ValueList = "";
		$this->class_description->SelectionList = "";
		$this->class_description->DefaultSelectionList = "";
		$this->class_description->ValueList = "";
		$this->inner_id->SelectionList = "";
		$this->inner_id->DefaultSelectionList = "";
		$this->inner_id->ValueList = "";
		$this->weekday->SelectionList = "";
		$this->weekday->DefaultSelectionList = "";
		$this->weekday->ValueList = "";
		$this->class_name->SelectionList = "";
		$this->class_name->DefaultSelectionList = "";
		$this->class_name->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Restore filter list
		$this->RestoreFilterList();

		// Extended filter
		$sExtendedFilter = "";

		// Load columns to array
		$this->GetColumns();

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get total group count
		$sGrpSort = ewr_UpdateSortFields($this->getSqlOrderByGroup(), $this->Sort, 2); // Get grouping field only
		$sSql = ewr_BuildReportSql($this->getSqlSelectGroup(), $this->getSqlWhere(), $this->getSqlGroupBy(), "", $this->getSqlOrderByGroup(), $this->Filter, $sGrpSort);
		$this->TotalGrps = $this->GetGrpCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = TRUE;

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
			$this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup();

		// Set no record found message
		if ($this->TotalGrps == 0) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Get total groups
		$rsgrp = $this->GetGrpRs($sSql, $this->StartGrp, $this->DisplayGrps);

		// Init detail recordset
		$rs = NULL;

		// Set up column attributes
		$this->class_name->ViewAttrs["style"] = "";
		$this->class_name->CellAttrs["style"] = "vertical-align: top;";
		$this->SetupFieldCount();
	}

	// Get column values
	function GetColumns() {
		global $ReportLanguage;
		$this->LoadColumnValues($this->Filter);

		// Reset summary values
		$this->ResetLevelSummary(0);

		// Set up column search values
		for ($i = 1; $i <= $this->ColCount; $i++) {
			$wrkValue = $this->Col[$i]->Value;
			$wrkCaption = $this->Col[$i]->Caption;
			$this->class_name->ValueList[$wrkValue] = $wrkCaption;
		}

		// Get active columns
		if (!is_array($this->class_name->SelectionList)) {
			$this->ColSpan = $this->ColCount;
		} else {
			$this->ColSpan = 0;
			for ($i = 1; $i <= $this->ColCount; $i++) {
				$bSelected = FALSE;
				$cntsel = count($this->class_name->SelectionList);
				for ($j = 0; $j < $cntsel; $j++) {
					if (ewr_CompareValue($this->class_name->SelectionList[$j], $this->Col[$i]->Value, $this->class_name->FldType)) {
						$this->ColSpan++;
						$bSelected = TRUE;
						break;
					}
				}
				$this->Col[$i]->Visible = $bSelected;
			}
		}
		$this->ColSpan++; // Add summary column
	}

	// Get group count
	function GetGrpCnt($sql) {
		$conn = &$this->Connection();
		$rsgrpcnt = $conn->Execute($sql);
		$grpcnt = ($rsgrpcnt) ? $rsgrpcnt->RecordCount() : 0;
		if ($rsgrpcnt) $rsgrpcnt->Close();
		return $grpcnt;
	}

	// Get group recordset
	function GetGrpRs($wrksql, $start = -1, $grps = -1) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get group row values
	function GetGrpRow($opt) {
		global $rsgrp;
		if (!$rsgrp)
			return;
		if ($opt == 1) { // Get first group

	//		$rsgrp->MoveFirst(); // NOTE: no need to move position
			$this->arrangedate->setDbValue(""); // Init first value
		} else { // Get next group
			$rsgrp->MoveNext();
		}
		if (!$rsgrp->EOF) {
			$this->arrangedate->setDbValue($rsgrp->fields[0]);
		} else {
			$this->arrangedate->setDbValue("");
		}
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row

	//		$rs->MoveFirst(); // NOTE: no need to move position
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			if ($opt <> 1)
				$this->arrangedate->setDbValue($rs->fields('arrangedate'));
			$this->starttime->setDbValue($rs->fields('starttime'));
			$this->member_name->setDbValue($rs->fields('member_name'));
			$this->class_description->setDbValue($rs->fields('class_description'));
			$this->inner_id->setDbValue($rs->fields('inner_id'));
			$this->weekday->setDbValue($rs->fields('weekday'));
			$cntbase = 6;
			$cnt = count($this->SummaryFields);
			for ($is = 0; $is < $cnt; $is++) {
				$smry = &$this->SummaryFields[$is];
				$cntval = count($smry->SummaryVal);
				for ($ix = 1; $ix < $cntval; $ix++) {
					if ($smry->SummaryType == "AVG") {
						$smry->SummaryVal[$ix] = $rs->fields[$ix*2+$cntbase-2];
						$smry->SummaryValCnt[$ix] = $rs->fields[$ix*2+$cntbase-1];
					} else {
						$smry->SummaryVal[$ix] = $rs->fields[$ix+$cntbase-1];
					}
				}
				$cntbase += ($smry->SummaryType == "AVG") ? 2*($cntval-1) : ($cntval-1);
			}
		} else {
			$this->arrangedate->setDbValue("");
			$this->starttime->setDbValue("");
			$this->member_name->setDbValue("");
			$this->class_description->setDbValue("");
			$this->inner_id->setDbValue("");
			$this->weekday->setDbValue("");
		}
	}

	// Check level break
	function ChkLvlBreak($lvl) {
		switch ($lvl) {
			case 1:
				return (is_null($this->arrangedate->CurrentValue) && !is_null($this->arrangedate->OldValue)) ||
					(!is_null($this->arrangedate->CurrentValue) && is_null($this->arrangedate->OldValue)) ||
					($this->arrangedate->GroupValue() <> $this->arrangedate->GroupOldValue());
			case 2:
				return (is_null($this->starttime->CurrentValue) && !is_null($this->starttime->OldValue)) ||
					(!is_null($this->starttime->CurrentValue) && is_null($this->starttime->OldValue)) ||
					($this->starttime->GroupValue() <> $this->starttime->GroupOldValue()) || $this->ChkLvlBreak(1); // Recurse upper level
			case 3:
				return (is_null($this->member_name->CurrentValue) && !is_null($this->member_name->OldValue)) ||
					(!is_null($this->member_name->CurrentValue) && is_null($this->member_name->OldValue)) ||
					($this->member_name->GroupValue() <> $this->member_name->GroupOldValue()) || $this->ChkLvlBreak(2); // Recurse upper level
			case 4:
				return (is_null($this->class_description->CurrentValue) && !is_null($this->class_description->OldValue)) ||
					(!is_null($this->class_description->CurrentValue) && is_null($this->class_description->OldValue)) ||
					($this->class_description->GroupValue() <> $this->class_description->GroupOldValue()) || $this->ChkLvlBreak(3); // Recurse upper level
			case 5:
				return (is_null($this->inner_id->CurrentValue) && !is_null($this->inner_id->OldValue)) ||
					(!is_null($this->inner_id->CurrentValue) && is_null($this->inner_id->OldValue)) ||
					($this->inner_id->GroupValue() <> $this->inner_id->GroupOldValue()) || $this->ChkLvlBreak(4); // Recurse upper level
			case 6:
				return (is_null($this->weekday->CurrentValue) && !is_null($this->weekday->OldValue)) ||
					(!is_null($this->weekday->CurrentValue) && is_null($this->weekday->OldValue)) ||
					($this->weekday->GroupValue() <> $this->weekday->GroupOldValue()) || $this->ChkLvlBreak(5); // Recurse upper level
		}
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cnt = count($this->SummaryFields);
		for ($is = 0; $is < $cnt; $is++) {
			$smry = &$this->SummaryFields[$is];
			$cntx = count($smry->SummarySmry);
			for ($ix = 1; $ix < $cntx; $ix++) {
				$cnty = count($smry->SummarySmry[$ix]);
				for ($iy = 0; $iy < $cnty; $iy++) {
					$valwrk = $smry->SummaryVal[$ix];
					$smry->SummaryCnt[$ix][$iy]++;
					$smry->SummarySmry[$ix][$iy] = ewr_SummaryValue($smry->SummarySmry[$ix][$iy], $valwrk, $smry->SummaryType);
					if ($smry->SummaryType == "AVG") {
						$cntwrk = $smry->SummaryValCnt[$ix];
						$smry->SummarySmryCnt[$ix][$iy] += $cntwrk;
					}
				}
			}
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cnt = count($this->SummaryFields);
		for ($is = 0; $is < $cnt; $is++) {
			$smry = &$this->SummaryFields[$is];
			$cntx = count($smry->SummarySmry);
			for ($ix = 1; $ix < $cntx; $ix++) {
				$cnty = count($smry->SummarySmry[$ix]);
				for ($iy = $lvl; $iy < $cnty; $iy++) {
					$smry->SummaryCnt[$ix][$iy] = 0;
					$smry->SummarySmry[$ix][$iy] = $smry->SummaryInitValue;
					if ($smry->SummaryType == "AVG") {
						$smry->SummarySmryCnt[$ix][$iy] = 0;
					}
				}
			}
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Set up starting group
	function SetUpStartGroup() {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;

		// Check for a 'start' parameter
		if (@$_GET[EWR_TABLE_START_GROUP] != "") {
			$this->StartGrp = $_GET[EWR_TABLE_START_GROUP];
			$this->setStartGroup($this->StartGrp);
		} elseif (@$_GET["pageno"] != "") {
			$nPageNo = $_GET["pageno"];
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for arrangedate

			if ($popupname == 'cv1_arrangedate') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->arrangedate->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->arrangedate->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->arrangedate->setDbValue($rswrk->fields[0]);
					if (is_null($this->arrangedate->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->arrangedate->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->arrangedate->GroupViewValue = ewr_FormatDateTime($this->arrangedate->GroupValue(), 5);
						ewr_SetupDistinctValues($this->arrangedate->ValueList, $this->arrangedate->GroupValue(), $this->arrangedate->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->arrangedate->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->arrangedate->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->arrangedate;
			}

			// Build distinct values for starttime
			if ($popupname == 'cv1_starttime') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->starttime->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->starttime->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->starttime->setDbValue($rswrk->fields[0]);
					if (is_null($this->starttime->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->starttime->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->starttime->GroupViewValue = $this->starttime->GroupValue();
						ewr_SetupDistinctValues($this->starttime->ValueList, $this->starttime->GroupValue(), $this->starttime->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->starttime->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->starttime->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->starttime;
			}

			// Build distinct values for member_name
			if ($popupname == 'cv1_member_name') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->member_name->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->member_name->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->member_name->setDbValue($rswrk->fields[0]);
					if (is_null($this->member_name->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->member_name->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->member_name->GroupViewValue = $this->member_name->GroupValue();
						ewr_SetupDistinctValues($this->member_name->ValueList, $this->member_name->GroupValue(), $this->member_name->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->member_name->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->member_name->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->member_name;
			}

			// Build distinct values for class_description
			if ($popupname == 'cv1_class_description') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->class_description->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->class_description->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->class_description->setDbValue($rswrk->fields[0]);
					if (is_null($this->class_description->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->class_description->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->class_description->GroupViewValue = $this->class_description->GroupValue();
						ewr_SetupDistinctValues($this->class_description->ValueList, $this->class_description->GroupValue(), $this->class_description->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->class_description->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->class_description->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->class_description;
			}

			// Build distinct values for inner_id
			if ($popupname == 'cv1_inner_id') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->inner_id->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->inner_id->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->inner_id->setDbValue($rswrk->fields[0]);
					if (is_null($this->inner_id->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->inner_id->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->inner_id->GroupViewValue = $this->inner_id->GroupValue();
						ewr_SetupDistinctValues($this->inner_id->ValueList, $this->inner_id->GroupValue(), $this->inner_id->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->inner_id->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->inner_id->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->inner_id;
			}

			// Build distinct values for weekday
			if ($popupname == 'cv1_weekday') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->weekday->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->weekday->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->weekday->setDbValue($rswrk->fields[0]);
					if (is_null($this->weekday->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->weekday->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->weekday->GroupViewValue = $this->weekday->GroupValue();
						ewr_SetupDistinctValues($this->weekday->ValueList, $this->weekday->GroupValue(), $this->weekday->GroupViewValue, FALSE);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->weekday->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->weekday->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->weekday;
			}

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ClearSessionSelection('arrangedate');
				$this->ClearSessionSelection('starttime');
				$this->ClearSessionSelection('member_name');
				$this->ClearSessionSelection('class_description');
				$this->ClearSessionSelection('inner_id');
				$this->ClearSessionSelection('weekday');
				$_SESSION["sel_cv1_class_name"] = "";
				$_SESSION["rf_cv1_class_name"] = "";
				$_SESSION["rt_cv1_class_name"] = "";
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get arrangedate selected values

		if (is_array(@$_SESSION["sel_cv1_arrangedate"])) {
			$this->LoadSelectionFromSession('arrangedate');
		} elseif (@$_SESSION["sel_cv1_arrangedate"] == EWR_INIT_VALUE) { // Select all
			$this->arrangedate->SelectionList = "";
		}

		// Get starttime selected values
		if (is_array(@$_SESSION["sel_cv1_starttime"])) {
			$this->LoadSelectionFromSession('starttime');
		} elseif (@$_SESSION["sel_cv1_starttime"] == EWR_INIT_VALUE) { // Select all
			$this->starttime->SelectionList = "";
		}

		// Get member_name selected values
		if (is_array(@$_SESSION["sel_cv1_member_name"])) {
			$this->LoadSelectionFromSession('member_name');
		} elseif (@$_SESSION["sel_cv1_member_name"] == EWR_INIT_VALUE) { // Select all
			$this->member_name->SelectionList = "";
		}

		// Get class_description selected values
		if (is_array(@$_SESSION["sel_cv1_class_description"])) {
			$this->LoadSelectionFromSession('class_description');
		} elseif (@$_SESSION["sel_cv1_class_description"] == EWR_INIT_VALUE) { // Select all
			$this->class_description->SelectionList = "";
		}

		// Get inner_id selected values
		if (is_array(@$_SESSION["sel_cv1_inner_id"])) {
			$this->LoadSelectionFromSession('inner_id');
		} elseif (@$_SESSION["sel_cv1_inner_id"] == EWR_INIT_VALUE) { // Select all
			$this->inner_id->SelectionList = "";
		}

		// Get weekday selected values
		if (is_array(@$_SESSION["sel_cv1_weekday"])) {
			$this->LoadSelectionFromSession('weekday');
		} elseif (@$_SESSION["sel_cv1_weekday"] == EWR_INIT_VALUE) { // Select all
			$this->weekday->SelectionList = "";
		}
		if (is_array(@$_SESSION["sel_cv1_class_name"])) {
			$this->class_name->SelectionList = @$_SESSION["sel_cv1_class_name"];
			$this->class_name->RangeFrom = @$_SESSION["rf_cv1_class_name"];
			$this->class_name->RangeTo = @$_SESSION["rt_cv1_class_name"];
		} elseif (@$_SESSION["sel_cv1_class_name"] == EWR_INIT_VALUE) { // Select all
			$this->class_name->SelectionList = "";
		}
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 3; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 3; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $Security, $ReportLanguage;
		$conn = &$this->Connection();

		// Set up summary values
		$colcnt = $this->ColCount+1;
		$this->SummaryCellAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryViewAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryLinkAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryCurrentValue = &ewr_InitArray($colcnt, NULL);
		$this->SummaryViewValue = &ewr_InitArray($colcnt, NULL);
		$cnt = count($this->SummaryFields);
		for ($is = 0; $is < $cnt; $is++) {
			$smry = &$this->SummaryFields[$is];
			$smry->SummaryViewAttrs = &ewr_InitArray($colcnt, NULL);
			$smry->SummaryLinkAttrs = &ewr_InitArray($colcnt, NULL);
			$smry->SummaryCurrentValue = &ewr_InitArray($colcnt, NULL);
			$smry->SummaryViewValue = &ewr_InitArray($colcnt, NULL);
			$smry->SummaryRowSmry = $smry->SummaryInitValue;
			$smry->SummaryRowCnt = 0;
		}
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total

			// Aggregate SQL
			$sSql = ewr_BuildReportSql(str_replace("<DistinctColumnFields>", $this->DistinctColumnFields, $this->getSqlSelectAgg()), $this->getSqlWhere(), $this->getSqlGroupByAgg(), "", "", $this->Filter, "");
			$rsagg = $conn->Execute($sSql);
			if ($rsagg && !$rsagg->EOF) $rsagg->MoveFirst();
		}
		for ($i = 1; $i <= $this->ColCount; $i++) {
			if ($this->Col[$i]->Visible) {
				$cntbaseagg = 0;
				$cnt = count($this->SummaryFields);
				for ($is = 0; $is < $cnt; $is++) {
					$smry = &$this->SummaryFields[$is];
					if ($this->RowType == EWR_ROWTYPE_DETAIL) { // Detail row
						$thisval = $smry->SummaryVal[$i];
						if ($smry->SummaryType == "AVG")
							$thiscnt = $smry->SummaryValCnt[$i];
					} elseif ($this->RowTotalType == EWR_ROWTOTAL_GROUP) { // Group total
						$thisval = $smry->SummarySmry[$i][$this->RowGroupLevel];
						if ($smry->SummaryType == "AVG")
							$thiscnt = $smry->SummarySmryCnt[$i][$this->RowGroupLevel];
					} elseif ($this->RowTotalType == EWR_ROWTOTAL_PAGE) { // Page total
						$thisval = $smry->SummarySmry[$i][0];
						if ($smry->SummaryType == "AVG")
							$thiscnt = $smry->SummarySmryCnt[$i][0];
					} elseif ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total
						if ($smry->SummaryType == "AVG") {
							$thisval = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i*2+$cntbaseagg-2] : 0;
							$thiscnt = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i*2+$cntbaseagg-1] : 0;
							$cntbaseagg += 2;
						} else {
							$thisval = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i+$cntbaseagg-1] : 0;
							$cntbaseagg += 1;
						}
					}
					if ($smry->SummaryType == "AVG")
						$smry->SummaryCurrentValue[$i-1] = ($thiscnt > 0) ? $thisval / $thiscnt : 0;
					else
						$smry->SummaryCurrentValue[$i-1] = $thisval;
					$smry->SummaryRowSmry = ewr_SummaryValue($smry->SummaryRowSmry, $thisval, $smry->SummaryType);
					if ($smry->SummaryType == "AVG")
						$smry->SummaryRowCnt += $thiscnt;
				}
			}
		}
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total
			if ($rsagg) $rsagg->Close();
		}
		$cnt = count($this->SummaryFields);
		for ($is = 0; $is < $cnt; $is++) {
			$smry = &$this->SummaryFields[$is];
			if ($smry->SummaryType == "AVG")
				$smry->SummaryCurrentValue[$this->ColCount] = ($smry->SummaryRowCnt > 0) ? $smry->SummaryRowSmry / $smry->SummaryRowCnt : 0;
			else
				$smry->SummaryCurrentValue[$this->ColCount] = $smry->SummaryRowSmry;
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		//  Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// arrangedate
			$this->arrangedate->GroupViewValue = $this->arrangedate->GroupOldValue();
			$this->arrangedate->GroupViewValue = ewr_FormatDateTime($this->arrangedate->GroupViewValue, 5);
			$this->arrangedate->CellAttrs["class"] = ($this->RowGroupLevel == 1) ? "ewRptGrpSummary1" : "ewRptGrpField1";

			// starttime
			$this->starttime->GroupViewValue = $this->starttime->GroupOldValue();
			$this->starttime->CellAttrs["class"] = ($this->RowGroupLevel == 2) ? "ewRptGrpSummary2" : "ewRptGrpField2";

			// member_name
			$this->member_name->GroupViewValue = $this->member_name->GroupOldValue();
			$this->member_name->CellAttrs["class"] = ($this->RowGroupLevel == 3) ? "ewRptGrpSummary3" : "ewRptGrpField3";

			// class_description
			$this->class_description->GroupViewValue = $this->class_description->GroupOldValue();
			$this->class_description->CellAttrs["class"] = ($this->RowGroupLevel == 4) ? "ewRptGrpSummary4" : "ewRptGrpField4";

			// inner_id
			$this->inner_id->GroupViewValue = $this->inner_id->GroupOldValue();
			$this->inner_id->CellAttrs["class"] = ($this->RowGroupLevel == 5) ? "ewRptGrpSummary5" : "ewRptGrpField5";

			// weekday
			$this->weekday->GroupViewValue = $this->weekday->GroupOldValue();
			$this->weekday->CellAttrs["class"] = ($this->RowGroupLevel == 6) ? "ewRptGrpSummary6" : "ewRptGrpField6";

			// Set up summary values
			$smry = &$this->SummaryFields[0];
			$scvcnt = count($smry->SummaryCurrentValue);
			for ($i = 0; $i < $scvcnt; $i++) {
				$smry->SummaryViewValue[$i] = $smry->SummaryCurrentValue[$i];
				$smry->SummaryViewAttrs[$i]["style"] = "";
				$smry->SummaryCellAttrs[$i]["style"] = "white-space: nowrap;";
				$this->SummaryCellAttrs[$i]["class"] = ($this->RowTotalType == EWR_ROWTOTAL_GROUP) ? "ewRptGrpSummary" . $this->RowGroupLevel : "";
			}

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";
			if ($this->Export == "") {
				$drillurl = $this->starttime->DrillDownUrl;
				$drillurl = str_replace("f0", ewr_Encrypt($this->GetDrillDownSQL($this->member_name, "member_name", $this->RowTotalType, -1)), $drillurl);
				$drillurl = str_replace("f1", ewr_Encrypt($this->GetDrillDownSQL($this->weekday, "weekday", $this->RowTotalType, -1)), $drillurl);
				$this->starttime->LinkAttrs["title"] = ewr_JsEncode($GLOBALS["ReportLanguage"]->Phrase("ClickToDrillDown"));
				$this->starttime->LinkAttrs["class"] = "ewDrillLink";
				$this->starttime->LinkAttrs["onclick"] = ewr_DrillDownJs($drillurl, 'cv1_starttime', $GLOBALS["ReportLanguage"]->TablePhrase('CustomView2', 'TblCaption'), $this->UseDrillDownPanel);
			}

			// member_name
			$this->member_name->HrefValue = "";

			// class_description
			$this->class_description->HrefValue = "";

			// inner_id
			$this->inner_id->HrefValue = "";

			// weekday
			$this->weekday->HrefValue = "";
		} else {

			// arrangedate
			$this->arrangedate->GroupViewValue = $this->arrangedate->GroupValue();
			$this->arrangedate->GroupViewValue = ewr_FormatDateTime($this->arrangedate->GroupViewValue, 5);
			$this->arrangedate->CellAttrs["class"] = "ewRptGrpField1";
			if ($this->arrangedate->GroupValue() == $this->arrangedate->GroupOldValue() && !$this->ChkLvlBreak(1))
				$this->arrangedate->GroupViewValue = "&nbsp;";

			// starttime
			$this->starttime->GroupViewValue = $this->starttime->GroupValue();
			$this->starttime->CellAttrs["class"] = "ewRptGrpField2";
			if ($this->starttime->GroupValue() == $this->starttime->GroupOldValue() && !$this->ChkLvlBreak(2))
				$this->starttime->GroupViewValue = "&nbsp;";

			// member_name
			$this->member_name->GroupViewValue = $this->member_name->GroupValue();
			$this->member_name->CellAttrs["class"] = "ewRptGrpField3";
			if ($this->member_name->GroupValue() == $this->member_name->GroupOldValue() && !$this->ChkLvlBreak(3))
				$this->member_name->GroupViewValue = "&nbsp;";

			// class_description
			$this->class_description->GroupViewValue = $this->class_description->GroupValue();
			$this->class_description->CellAttrs["class"] = "ewRptGrpField4";
			if ($this->class_description->GroupValue() == $this->class_description->GroupOldValue() && !$this->ChkLvlBreak(4))
				$this->class_description->GroupViewValue = "&nbsp;";

			// inner_id
			$this->inner_id->GroupViewValue = $this->inner_id->GroupValue();
			$this->inner_id->CellAttrs["class"] = "ewRptGrpField5";
			if ($this->inner_id->GroupValue() == $this->inner_id->GroupOldValue() && !$this->ChkLvlBreak(5))
				$this->inner_id->GroupViewValue = "&nbsp;";

			// weekday
			$this->weekday->GroupViewValue = $this->weekday->GroupValue();
			$this->weekday->CellAttrs["class"] = "ewRptGrpField6";
			if ($this->weekday->GroupValue() == $this->weekday->GroupOldValue() && !$this->ChkLvlBreak(6))
				$this->weekday->GroupViewValue = "&nbsp;";

			// Set up summary values
			$smry = &$this->SummaryFields[0];
			$scvcnt = count($smry->SummaryCurrentValue);
			for ($i = 0; $i < $scvcnt; $i++) {
				$smry->SummaryViewValue[$i] = $smry->SummaryCurrentValue[$i];
				$smry->SummaryViewAttrs[$i]["style"] = "";
				$smry->SummaryCellAttrs[$i]["style"] = "white-space: nowrap;";
				$this->SummaryCellAttrs[$i]["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";
			}

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";
			if ($this->Export == "") {
				$drillurl = $this->starttime->DrillDownUrl;
				$drillurl = str_replace("f0", ewr_Encrypt($this->GetDrillDownSQL($this->member_name, "member_name", 0, -1)), $drillurl);
				$drillurl = str_replace("f1", ewr_Encrypt($this->GetDrillDownSQL($this->weekday, "weekday", 0, -1)), $drillurl);
				$this->starttime->LinkAttrs["title"] = ewr_JsEncode($ReportLanguage->Phrase("ClickToDrillDown"));
				$this->starttime->LinkAttrs["class"] = "ewDrillLink";
				$this->starttime->LinkAttrs["onclick"] = ewr_DrillDownJs($drillurl, 'cv1_starttime', $GLOBALS["ReportLanguage"]->TablePhrase('CustomView2', 'TblCaption'), $this->UseDrillDownPanel);
			}

			// member_name
			$this->member_name->HrefValue = "";

			// class_description
			$this->class_description->HrefValue = "";

			// inner_id
			$this->inner_id->HrefValue = "";

			// weekday
			$this->weekday->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// arrangedate
			$this->CurrentIndex = 0; // Current index
			$CurrentValue = $this->arrangedate->GroupOldValue();
			$ViewValue = &$this->arrangedate->GroupViewValue;
			$ViewAttrs = &$this->arrangedate->ViewAttrs;
			$CellAttrs = &$this->arrangedate->CellAttrs;
			$HrefValue = &$this->arrangedate->HrefValue;
			$LinkAttrs = &$this->arrangedate->LinkAttrs;
			$this->Cell_Rendered($this->arrangedate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// starttime
			$this->CurrentIndex = 1; // Current index
			$CurrentValue = $this->starttime->GroupOldValue();
			$ViewValue = &$this->starttime->GroupViewValue;
			$ViewAttrs = &$this->starttime->ViewAttrs;
			$CellAttrs = &$this->starttime->CellAttrs;
			$HrefValue = &$this->starttime->HrefValue;
			$LinkAttrs = &$this->starttime->LinkAttrs;
			$this->Cell_Rendered($this->starttime, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_name
			$this->CurrentIndex = 2; // Current index
			$CurrentValue = $this->member_name->GroupOldValue();
			$ViewValue = &$this->member_name->GroupViewValue;
			$ViewAttrs = &$this->member_name->ViewAttrs;
			$CellAttrs = &$this->member_name->CellAttrs;
			$HrefValue = &$this->member_name->HrefValue;
			$LinkAttrs = &$this->member_name->LinkAttrs;
			$this->Cell_Rendered($this->member_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// class_description
			$this->CurrentIndex = 3; // Current index
			$CurrentValue = $this->class_description->GroupOldValue();
			$ViewValue = &$this->class_description->GroupViewValue;
			$ViewAttrs = &$this->class_description->ViewAttrs;
			$CellAttrs = &$this->class_description->CellAttrs;
			$HrefValue = &$this->class_description->HrefValue;
			$LinkAttrs = &$this->class_description->LinkAttrs;
			$this->Cell_Rendered($this->class_description, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// inner_id
			$this->CurrentIndex = 4; // Current index
			$CurrentValue = $this->inner_id->GroupOldValue();
			$ViewValue = &$this->inner_id->GroupViewValue;
			$ViewAttrs = &$this->inner_id->ViewAttrs;
			$CellAttrs = &$this->inner_id->CellAttrs;
			$HrefValue = &$this->inner_id->HrefValue;
			$LinkAttrs = &$this->inner_id->LinkAttrs;
			$this->Cell_Rendered($this->inner_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// weekday
			$this->CurrentIndex = 5; // Current index
			$CurrentValue = $this->weekday->GroupOldValue();
			$ViewValue = &$this->weekday->GroupViewValue;
			$ViewAttrs = &$this->weekday->ViewAttrs;
			$CellAttrs = &$this->weekday->CellAttrs;
			$HrefValue = &$this->weekday->HrefValue;
			$LinkAttrs = &$this->weekday->LinkAttrs;
			$this->Cell_Rendered($this->weekday, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
			for ($i = 0; $i < $scvcnt; $i++) {
				$this->CurrentIndex = $i;
				$cnt = count($this->SummaryFields);
				for ($is = 0; $is < $cnt; $is++) {
					$smry = &$this->SummaryFields[$is];
					$CurrentValue = $smry->SummaryCurrentValue[$i];
					$ViewValue = &$smry->SummaryViewValue[$i];
					$ViewAttrs = &$smry->SummaryViewAttrs[$i];
					$CellAttrs = &$this->SummaryCellAttrs[$i];
					$HrefValue = "";
					$LinkAttrs = &$smry->SummaryLinkAttrs[$i];
					$this->Cell_Rendered($smry, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
				}
			}
		} else {

			// arrangedate
			$this->CurrentIndex = 0; // Group index
			$CurrentValue = $this->arrangedate->GroupValue();
			$ViewValue = &$this->arrangedate->GroupViewValue;
			$ViewAttrs = &$this->arrangedate->ViewAttrs;
			$CellAttrs = &$this->arrangedate->CellAttrs;
			$HrefValue = &$this->arrangedate->HrefValue;
			$LinkAttrs = &$this->arrangedate->LinkAttrs;
			$this->Cell_Rendered($this->arrangedate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// starttime
			$this->CurrentIndex = 1; // Group index
			$CurrentValue = $this->starttime->GroupValue();
			$ViewValue = &$this->starttime->GroupViewValue;
			$ViewAttrs = &$this->starttime->ViewAttrs;
			$CellAttrs = &$this->starttime->CellAttrs;
			$HrefValue = &$this->starttime->HrefValue;
			$LinkAttrs = &$this->starttime->LinkAttrs;
			$this->Cell_Rendered($this->starttime, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_name
			$this->CurrentIndex = 2; // Group index
			$CurrentValue = $this->member_name->GroupValue();
			$ViewValue = &$this->member_name->GroupViewValue;
			$ViewAttrs = &$this->member_name->ViewAttrs;
			$CellAttrs = &$this->member_name->CellAttrs;
			$HrefValue = &$this->member_name->HrefValue;
			$LinkAttrs = &$this->member_name->LinkAttrs;
			$this->Cell_Rendered($this->member_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// class_description
			$this->CurrentIndex = 3; // Group index
			$CurrentValue = $this->class_description->GroupValue();
			$ViewValue = &$this->class_description->GroupViewValue;
			$ViewAttrs = &$this->class_description->ViewAttrs;
			$CellAttrs = &$this->class_description->CellAttrs;
			$HrefValue = &$this->class_description->HrefValue;
			$LinkAttrs = &$this->class_description->LinkAttrs;
			$this->Cell_Rendered($this->class_description, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// inner_id
			$this->CurrentIndex = 4; // Group index
			$CurrentValue = $this->inner_id->GroupValue();
			$ViewValue = &$this->inner_id->GroupViewValue;
			$ViewAttrs = &$this->inner_id->ViewAttrs;
			$CellAttrs = &$this->inner_id->CellAttrs;
			$HrefValue = &$this->inner_id->HrefValue;
			$LinkAttrs = &$this->inner_id->LinkAttrs;
			$this->Cell_Rendered($this->inner_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// weekday
			$this->CurrentIndex = 5; // Group index
			$CurrentValue = $this->weekday->GroupValue();
			$ViewValue = &$this->weekday->GroupViewValue;
			$ViewAttrs = &$this->weekday->ViewAttrs;
			$CellAttrs = &$this->weekday->CellAttrs;
			$HrefValue = &$this->weekday->HrefValue;
			$LinkAttrs = &$this->weekday->LinkAttrs;
			$this->Cell_Rendered($this->weekday, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
			for ($i = 0; $i < $scvcnt; $i++) {
				$this->CurrentIndex = $i;
				$cnt = count($this->SummaryFields);
				for ($is = 0; $is < $cnt; $is++) {
					$smry = &$this->SummaryFields[$is];
					$CurrentValue = $smry->SummaryCurrentValue[$i];
					$ViewValue = &$smry->SummaryViewValue[$i];
					$ViewAttrs = &$smry->SummaryViewAttrs[$i];
					$CellAttrs = &$this->SummaryCellAttrs[$i];
					$HrefValue = "";
					$LinkAttrs = &$smry->SummaryLinkAttrs[$i];
					$this->Cell_Rendered($smry, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
				}
			}
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpFldCount = 0;
		if ($this->arrangedate->Visible) $this->GrpFldCount += 1;
		if ($this->starttime->Visible) $this->GrpFldCount += 1;
		if ($this->member_name->Visible) $this->GrpFldCount += 1;
		if ($this->class_description->Visible) $this->GrpFldCount += 1;
		if ($this->inner_id->Visible) $this->GrpFldCount += 1;
		if ($this->weekday->Visible) $this->GrpFldCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("crosstab", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage;
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = FALSE;
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_cv1_$parm"] = "";
		$_SESSION["rf_cv1_$parm"] = "";
		$_SESSION["rt_cv1_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_cv1_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_cv1_$parm"];
		$fld->RangeTo = @$_SESSION["rt_cv1_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {

		/**
		* Set up default values for non Text filters
		*/

		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		/**
		* Set up default values for popup filters
		*/

		// Field member_name
		// $this->member_name->DefaultSelectionList = array("val1", "val2");
		// Field arrangedate
		// $this->arrangedate->DefaultSelectionList = array("val1", "val2");
		// Field starttime
		// $this->starttime->DefaultSelectionList = array("val1", "val2");
		// Field class_name
		// $this->class_name->DefaultSelectionList = array("val1", "val2");
		// Field class_description
		// $this->class_description->DefaultSelectionList = array("val1", "val2");
		// Field inner_id
		// $this->inner_id->DefaultSelectionList = array("val1", "val2");
		// Field weekday
		// $this->weekday->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check member_name popup filter
		if (!ewr_MatchedArray($this->member_name->DefaultSelectionList, $this->member_name->SelectionList))
			return TRUE;

		// Check arrangedate popup filter
		if (!ewr_MatchedArray($this->arrangedate->DefaultSelectionList, $this->arrangedate->SelectionList))
			return TRUE;

		// Check starttime popup filter
		if (!ewr_MatchedArray($this->starttime->DefaultSelectionList, $this->starttime->SelectionList))
			return TRUE;

		// Check class_name popup filter
		if (!ewr_MatchedArray($this->class_name->DefaultSelectionList, $this->class_name->SelectionList))
			return TRUE;

		// Check class_description popup filter
		if (!ewr_MatchedArray($this->class_description->DefaultSelectionList, $this->class_description->SelectionList))
			return TRUE;

		// Check inner_id popup filter
		if (!ewr_MatchedArray($this->inner_id->DefaultSelectionList, $this->inner_id->SelectionList))
			return TRUE;

		// Check weekday popup filter
		if (!ewr_MatchedArray($this->weekday->DefaultSelectionList, $this->weekday->SelectionList))
			return TRUE;

		// Check class_name popup filter
		if (!ewr_MatchedArray($this->class_name->DefaultSelectionList, $this->class_name->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field member_name
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->member_name->SelectionList))
			$sWrk = ewr_JoinArray($this->member_name->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->member_name->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field arrangedate
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->arrangedate->SelectionList))
			$sWrk = ewr_JoinArray($this->arrangedate->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->arrangedate->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field starttime
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->starttime->SelectionList))
			$sWrk = ewr_JoinArray($this->starttime->SelectionList, ", ", EWR_DATATYPE_TIME, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->starttime->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field class_name
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->class_name->SelectionList))
			$sWrk = ewr_JoinArray($this->class_name->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->class_name->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field class_description
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->class_description->SelectionList))
			$sWrk = ewr_JoinArray($this->class_description->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->class_description->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field inner_id
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->inner_id->SelectionList))
			$sWrk = ewr_JoinArray($this->inner_id->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->inner_id->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field weekday
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->weekday->SelectionList))
			$sWrk = ewr_JoinArray($this->weekday->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->weekday->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "") {
			$sMessage = "<div class=\"ewDisplayTable\"" . $divstyle . "><div id=\"ewrFilterList\" class=\"alert alert-info\"" . $divdataclass . "><div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList . "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field member_name
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->member_name->SelectionList <> EWR_INIT_VALUE) ? $this->member_name->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_member_name\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field arrangedate
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->arrangedate->SelectionList <> EWR_INIT_VALUE) ? $this->arrangedate->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_arrangedate\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field starttime
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->starttime->SelectionList <> EWR_INIT_VALUE) ? $this->starttime->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_starttime\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field class_name
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->class_name->SelectionList <> EWR_INIT_VALUE) ? $this->class_name->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_class_name\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field class_description
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->class_description->SelectionList <> EWR_INIT_VALUE) ? $this->class_description->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_class_description\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field inner_id
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->inner_id->SelectionList <> EWR_INIT_VALUE) ? $this->inner_id->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_inner_id\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field weekday
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->weekday->SelectionList <> EWR_INIT_VALUE) ? $this->weekday->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_weekday\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);

		// Field member_name
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_member_name", $filter)) {
			$sWrk = $filter["sel_member_name"];
			$sWrk = explode("||", $sWrk);
			$this->member_name->SelectionList = $sWrk;
			$_SESSION["sel_cv1_member_name"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field arrangedate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_arrangedate", $filter)) {
			$sWrk = $filter["sel_arrangedate"];
			$sWrk = explode("||", $sWrk);
			$this->arrangedate->SelectionList = $sWrk;
			$_SESSION["sel_cv1_arrangedate"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field starttime
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_starttime", $filter)) {
			$sWrk = $filter["sel_starttime"];
			$sWrk = explode("||", $sWrk);
			$this->starttime->SelectionList = $sWrk;
			$_SESSION["sel_cv1_starttime"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field class_name
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_class_name", $filter)) {
			$sWrk = $filter["sel_class_name"];
			$sWrk = explode("||", $sWrk);
			$this->class_name->SelectionList = $sWrk;
			$_SESSION["sel_cv1_class_name"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field class_description
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_class_description", $filter)) {
			$sWrk = $filter["sel_class_description"];
			$sWrk = explode("||", $sWrk);
			$this->class_description->SelectionList = $sWrk;
			$_SESSION["sel_cv1_class_description"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field inner_id
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_inner_id", $filter)) {
			$sWrk = $filter["sel_inner_id"];
			$sWrk = explode("||", $sWrk);
			$this->inner_id->SelectionList = $sWrk;
			$_SESSION["sel_cv1_inner_id"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field weekday
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_weekday", $filter)) {
			$sWrk = $filter["sel_weekday"];
			$sWrk = explode("||", $sWrk);
			$this->weekday->SelectionList = $sWrk;
			$_SESSION["sel_cv1_weekday"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
			if (is_array($this->member_name->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->member_name, "member_user.member_name", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->member_name, $sFilter, "popup");
				$this->member_name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->arrangedate->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->arrangedate, "class_arrange.arrangedate", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->arrangedate, $sFilter, "popup");
				$this->arrangedate->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->starttime->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->starttime, "class_arrange.starttime", EWR_DATATYPE_TIME, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->starttime, $sFilter, "popup");
				$this->starttime->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->class_name->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->class_name, "class_design.class_name", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->class_name, $sFilter, "popup");
				$this->class_name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->class_description->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->class_description, "class_design.class_description", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->class_description, $sFilter, "popup");
				$this->class_description->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->inner_id->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->inner_id, "class_design.inner_id", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->inner_id, $sFilter, "popup");
				$this->inner_id->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->weekday->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->weekday, "DayOfWeek(class_arrange.arrangedate) - 1", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->weekday, $sFilter, "popup");
				$this->weekday->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		return $sWrk;
	}

	// Return drill down SQL
	// - fld = source field object
	// - target = target field name
	// - rowtype = row type
	//  * 0 = detail
	//  * 1 = group
	//  * 2 = page
	//  * 3 = grand
	// - parm = filter/column index
	//  * -1  = use field filter value / current/old value
	//  * 0   = use grouping/column field value
	//  * > 0 = use column index
	function GetDrillDownSQL($fld, $target, $rowtype, $parm = 0) {
		$sql = "";

		// Handle grand/page total
		if ($fld->FldVar == "x_arrangedate") { // First grouping field
			if ($rowtype == EWR_ROWTOTAL_GRAND) { // Grand total
				$sql = $fld->CurrentFilter;
				if ($sql == "")
					$sql = "1=1"; // Show all records
			} elseif ($rowtype == EWR_ROWTOTAL_PAGE && $this->PageFirstGroupFilter <> "") { // Page total
				$sql = str_replace($fld->FldExpression, "@" . $target, "(" . $this->PageFirstGroupFilter . ")");
			}
		}

		// Handle group/row/column field
		if ($parm >= 0 && $sql == "") {
			switch (substr($fld->FldVar,2)) {
			case "member_name":
				if ($fld->FldGroupSql <> "") {
					$sql = str_replace("%s", "@" . $target, $fld->FldGroupSql) . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, EWR_DATATYPE_STRING, $this->DBID);
					ewr_AddFilter($sql, str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter));
				} else {
					$sql = "@" . $target . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, $fld->FldDataType, $this->DBID);
				}
				break;
			case "starttime":
				if ($fld->FldGroupSql <> "") {
					$sql = str_replace("%s", "@" . $target, $fld->FldGroupSql) . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, EWR_DATATYPE_STRING, $this->DBID);
					ewr_AddFilter($sql, str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter));
				} else {
					$sql = "@" . $target . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, $fld->FldDataType, $this->DBID);
				}
				break;
			case "class_name":
				if ($parm >= 1 && $parm <= $this->ColCount) {
					$sql = "@" . $target . " = " . ewr_QuotedValue($this->Col[$parm]->Value, EWR_DATATYPE_STRING, $this->DBID);
				}
				break;
			case "weekday":
				if ($fld->FldGroupSql <> "") {
					$sql = str_replace("%s", "@" . $target, $fld->FldGroupSql) . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, EWR_DATATYPE_STRING, $this->DBID);
					ewr_AddFilter($sql, str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter));
				} else {
					$sql = "@" . $target . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, $fld->FldDataType, $this->DBID);
				}
				break;
			}
		}

		// Detail field
		if ($sql == "" && $rowtype == 0)
			if ($fld->CurrentFilter <> "") // Use current filter
				$sql = str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter);
			elseif ($fld->CurrentValue <> "") // Use current value for detail row
				$sql = "@" . $target . "=" . ewr_QuotedValue($fld->CurrentValue, $fld->FldDataType, $this->DBID);
		return $sql;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort() {
		if ($this->DrillDown)
			return "";

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->arrangedate->setSort("");
				$this->starttime->setSort("");
				$this->member_name->setSort("");
				$this->class_description->setSort("");
				$this->inner_id->setSort("");
				$this->weekday->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $ReportLanguage;
		$sContentType = @$_POST["contenttype"];
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];

		// Subject
		$sSubject = ewr_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ewr_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "")
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterSenderEmail") . "</p>";
		if (!ewr_CheckEmail($sSender))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperSenderEmail") . "</p>";

		// Check recipient
		if (!ewr_CheckEmailList($sRecipient, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperRecipientEmail") . "</p>";

		// Check cc
		if (!ewr_CheckEmailList($sCc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperCcEmail") . "</p>";

		// Check bcc
		if (!ewr_CheckEmailList($sBcc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperBccEmail") . "</p>";

		// Check email sent count
		$emailcount = ewr_LoadEmailCount();
		if (intval($emailcount) >= EWR_MAX_EMAIL_SENT_COUNT)
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("ExceedMaxEmailExport") . "</p>";
		if ($sEmailMessage <> "") {
			if (EWR_REMOVE_XSS) $sEmailMessage = ewr_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		$sAttachmentContent = ewr_CleanEmailContent($EmailContent);
		$sAppPath = ewr_FullUrl();
		$sAppPath = substr($sAppPath, 0, strrpos($sAppPath, "/")+1);
		if (strpos($sAttachmentContent, "<head>") !== FALSE)
			$sAttachmentContent = str_replace("<head>", "<head><base href=\"" . $sAppPath . "\">", $sAttachmentContent); // Add <base href> statement inside the header
		else
			$sAttachmentContent = "<base href=\"" . $sAppPath . "\">" . $sAttachmentContent; // Add <base href> statement as the first statement

		//$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . ".html";
		$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . "_" . ewr_Random() . ".html";
		if ($sContentType == "url") {
			ewr_SaveFile(EWR_UPLOAD_DEST_PATH, $sAttachmentFile, $sAttachmentContent);
			$sAttachmentFile = EWR_UPLOAD_DEST_PATH . $sAttachmentFile;
			$sUrl = $sAppPath . $sAttachmentFile;
			$sEmailMessage .= $sUrl; // Send URL only
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		} else {
			$sEmailMessage .= $sAttachmentContent;
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		}

		// Send email
		$Email = new crEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Content = $sEmailMessage; // Content
		if ($sAttachmentFile <> "")
			$Email->AddAttachment($sAttachmentFile, $sAttachmentContent);
		if ($sContentType <> "url") {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
		}
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EWR_EMAIL_CHARSET;
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();
		ewr_DeleteTmpImages($EmailContent);

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count and write log
			ewr_AddEmailLog($sSender, $sRecipient, $sEmailSubject, $sEmailMessage);

			// Sent email success
			return "<p class=\"text-success\">" . $ReportLanguage->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-error\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export to HTML
	function ExportHtml($html) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');
		//echo $html;

	} 

	// Export to WORD
	function ExportWord($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
		echo $html;
	}

	// Export to EXCEL
	function ExportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
		echo $html;
	}

	// Export PDF
	function ExportPDF($html) {
		global $gsExportFile;
		include_once "dompdf061/dompdf_config.inc.php";
		@ini_set("memory_limit", EWR_PDF_MEMORY_LIMIT);
		set_time_limit(EWR_PDF_TIME_LIMIT);
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		ob_end_clean();
		$dompdf->set_paper("a4", "portrait");
		$dompdf->render();
		ewr_DeleteTmpImages($html);
		$dompdf->stream($gsExportFile . ".pdf", array("Attachment" => 1)); // 0 to open in browser, 1 to download

//		exit();
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($cv1_crosstab)) $cv1_crosstab = new crcv1_crosstab();
if (isset($Page)) $OldPage = $Page;
$Page = &$cv1_crosstab;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var cv1_crosstab = new ewr_Page("cv1_crosstab");

// Page properties
cv1_crosstab.PageID = "crosstab"; // Page ID
var EWR_PAGE_ID = cv1_crosstab.PageID;

// Extend page with Chart_Rendering function
cv1_crosstab.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
cv1_crosstab.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fcv1crosstab = new ewr_Form("fcv1crosstab");
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">
<?php $jsdb = ewr_GetJsDb($Page->class_name, $Page->class_name->FldType); ?>
ewr_CreatePopup("cv1_class_name", <?php echo $jsdb ?>); // Popup filters
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- Top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container (report) (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- crosstab report starts -->
<div id="report_crosstab">
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fcv1crosstab" id="fcv1crosstab" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
</form>
<script type="text/javascript">
fcv1crosstab.Init();
fcv1crosstab.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps)) {
	$Page->StopGrp = $Page->TotalGrps;
}

// Navigate
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetGrpRow(1);
	$Page->GrpCount = 1;
}
while ($rsgrp && !$rsgrp->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show header
	if ($Page->ShowHeader) {
?>
<?php if ($Page->GrpCount > 1) { ?>
</tbody>
</table>
</div>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "cv1ctbpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php echo $Page->PageBreakContent ?>
<?php } ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->GrpFldCount > 0) { ?>
		<td class="ewRptColSummary" colspan="<?php echo $Page->GrpFldCount ?>"><div><?php echo $Page->RenderSummaryCaptions() ?></div></td>
<?php } ?>
		<td class="ewRptColHeader" colspan="<?php echo @$Page->ColSpan ?>">
			<div class="ewTableHeaderBtn">
				<span class="ewTableHeaderCaption"><?php echo $Page->class_name->FldCaption() ?></span>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
				<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_class_name', false, '<?php echo $Page->class_name->RangeFrom ?>', '<?php echo $Page->class_name->RangeTo ?>');" name="x_class_name" id="x_class_name"><span class="icon-filter"></span></a>
<?php } ?>
			</div>
		</td>
	</tr>
	<tr class="ewTableHeader">
<?php if ($Page->arrangedate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="arrangedate">
		<div class="cv1_arrangedate"><span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="arrangedate">
<?php if ($Page->SortUrl($Page->arrangedate) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_arrangedate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->arrangedate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_arrangedate', false, '<?php echo $Page->arrangedate->RangeFrom; ?>', '<?php echo $Page->arrangedate->RangeTo; ?>');" id="x_arrangedate"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_arrangedate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->arrangedate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->arrangedate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->arrangedate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_arrangedate', false, '<?php echo $Page->arrangedate->RangeFrom; ?>', '<?php echo $Page->arrangedate->RangeTo; ?>');" id="x_arrangedate"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="starttime">
		<div class="cv1_starttime"><span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="starttime">
<?php if ($Page->SortUrl($Page->starttime) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_starttime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->starttime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_starttime', false, '<?php echo $Page->starttime->RangeFrom; ?>', '<?php echo $Page->starttime->RangeTo; ?>');" id="x_starttime"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_starttime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->starttime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->starttime->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->starttime->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_starttime', false, '<?php echo $Page->starttime->RangeFrom; ?>', '<?php echo $Page->starttime->RangeTo; ?>');" id="x_starttime"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_name">
		<div class="cv1_member_name"><span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="member_name">
<?php if ($Page->SortUrl($Page->member_name) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_member_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_member_name', false, '<?php echo $Page->member_name->RangeFrom; ?>', '<?php echo $Page->member_name->RangeTo; ?>');" id="x_member_name"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_member_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_member_name', false, '<?php echo $Page->member_name->RangeFrom; ?>', '<?php echo $Page->member_name->RangeTo; ?>');" id="x_member_name"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="class_description">
		<div class="cv1_class_description"><span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="class_description">
<?php if ($Page->SortUrl($Page->class_description) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_class_description" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->class_description) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_class_description', false, '<?php echo $Page->class_description->RangeFrom; ?>', '<?php echo $Page->class_description->RangeTo; ?>');" id="x_class_description"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_class_description" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->class_description) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->class_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->class_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_class_description', false, '<?php echo $Page->class_description->RangeFrom; ?>', '<?php echo $Page->class_description->RangeTo; ?>');" id="x_class_description"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="inner_id">
		<div class="cv1_inner_id"><span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="inner_id">
<?php if ($Page->SortUrl($Page->inner_id) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_inner_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->inner_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_inner_id', false, '<?php echo $Page->inner_id->RangeFrom; ?>', '<?php echo $Page->inner_id->RangeTo; ?>');" id="x_inner_id"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_inner_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->inner_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->inner_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->inner_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_inner_id', false, '<?php echo $Page->inner_id->RangeFrom; ?>', '<?php echo $Page->inner_id->RangeTo; ?>');" id="x_inner_id"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->weekday->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="weekday">
		<div class="cv1_weekday"><span class="ewTableHeaderCaption"><?php echo $Page->weekday->FldCaption() ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="weekday">
<?php if ($Page->SortUrl($Page->weekday) == "") { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_weekday" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->weekday) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->weekday->FldCaption() ?></span>			
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_weekday', false, '<?php echo $Page->weekday->RangeFrom; ?>', '<?php echo $Page->weekday->RangeTo; ?>');" id="x_weekday"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer cv1_weekday" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->weekday) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->weekday->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->weekday->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->weekday->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'cv1_weekday', false, '<?php echo $Page->weekday->RangeFrom; ?>', '<?php echo $Page->weekday->RangeTo; ?>');" id="x_weekday"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<!-- Dynamic columns begin -->
<?php
	$cntval = count($Page->Col);
	for ($iy = 1; $iy < $cntval; $iy++) {
		if ($Page->Col[$iy]->Visible) {
			$Page->SummaryCurrentValue[$iy-1] = $Page->Col[$iy]->Caption;
			$Page->SummaryViewValue[$iy-1] = $Page->SummaryCurrentValue[$iy-1];
?>
		<td class="ewTableHeader"<?php echo $Page->class_name->CellAttributes() ?>><div<?php echo $Page->class_name->ViewAttributes() ?>><?php echo $Page->SummaryViewValue[$iy-1]; ?></div></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
		<td class="ewTableHeader"<?php echo $Page->class_name->CellAttributes() ?>><div<?php echo $Page->class_name->ViewAttributes() ?>><?php echo $Page->RenderSummaryCaptions() ?></div></td>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}

	// Build detail SQL
	$sWhere = ewr_DetailFilterSQL($Page->arrangedate, $Page->getSqlFirstGroupField(), $Page->arrangedate->GroupValue(), $Page->DBID);
	if ($Page->PageFirstGroupFilter <> "") $Page->PageFirstGroupFilter .= " OR ";
	$Page->PageFirstGroupFilter .= $sWhere;
	if ($Page->Filter != "")
		$sWhere = "($Page->Filter) AND ($sWhere)";
	$sSql = ewr_BuildReportSql(str_replace("<DistinctColumnFields>", $Page->DistinctColumnFields, $Page->getSqlSelect()), $Page->getSqlWhere(), $Page->getSqlGroupBy(), "", $Page->getSqlOrderBy(), $sWhere, $Page->Sort);
	$rs = $conn->Execute($sSql);
	$rsdtlcnt = ($rs) ? $rs->RecordCount() : 0;
	if ($rsdtlcnt > 0)
		$Page->GetRow(1);
	while ($rs && !$rs->EOF) {
		$Page->RecCount++;
		$Page->RecIndex++;

		// Render row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->arrangedate->Visible) { ?>
		<!-- arrangedate -->
		<td data-field="arrangedate"<?php echo $Page->arrangedate->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_arrangedate"<?php echo $Page->arrangedate->ViewAttributes() ?>><?php echo $Page->arrangedate->GroupViewValue ?></span></td>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
		<!-- starttime -->
		<td data-field="starttime"<?php echo $Page->starttime->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_starttime"<?php echo $Page->starttime->ViewAttributes() ?>>
<?php if ($Page->starttime->HrefValue <> "" || @$Page->starttime->LinkAttrs["onclick"] <> "") { ?>
<?php if ($Page->starttime->GroupViewValue <> "" && $Page->starttime->GroupViewValue <> "&nbsp;") { ?>
<a<?php echo $Page->starttime->LinkAttributes() ?>><?php echo $Page->starttime->GroupViewValue ?></a>
<?php } else { echo "&nbsp;"; } ?>
<?php } else { ?>
<?php if ($Page->starttime->GroupViewValue <> "" && $Page->starttime->GroupViewValue <> "&nbsp;") { ?>
<?php echo $Page->starttime->GroupViewValue ?>
<?php } else { echo "&nbsp;"; } ?>
<?php } ?>
</span></td>
<?php } ?>
<?php if ($Page->member_name->Visible) { ?>
		<!-- member_name -->
		<td data-field="member_name"<?php echo $Page->member_name->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_member_name"<?php echo $Page->member_name->ViewAttributes() ?>><?php echo $Page->member_name->GroupViewValue ?></span></td>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
		<!-- class_description -->
		<td data-field="class_description"<?php echo $Page->class_description->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_class_description"<?php echo $Page->class_description->ViewAttributes() ?>><?php echo $Page->class_description->GroupViewValue ?></span></td>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
		<!-- inner_id -->
		<td data-field="inner_id"<?php echo $Page->inner_id->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_inner_id"<?php echo $Page->inner_id->ViewAttributes() ?>><?php echo $Page->inner_id->GroupViewValue ?></span></td>
<?php } ?>
<?php if ($Page->weekday->Visible) { ?>
		<!-- weekday -->
		<td data-field="weekday"<?php echo $Page->weekday->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_cv1_weekday"<?php echo $Page->weekday->ViewAttributes() ?>><?php echo $Page->weekday->GroupViewValue ?></span></td>
<?php } ?>
<!-- Dynamic columns begin -->
<?php
		$cntcol = count($Page->SummaryViewValue);
		for ($iy = 1; $iy <= $cntcol; $iy++) {
			$bColShow = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Visible : TRUE;
			$sColDesc = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
			if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $Page->SummaryCellAttributes($iy-1) ?>><?php echo $Page->RenderSummaryFields($iy-1) ?></td>
<?php
			}
		}
?>
<!-- Dynamic columns end -->
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
?>
<?php
	} // End detail records loop
?>
<?php
	$Page->GetGrpRow(2);

	// Show header if page break
	if ($Page->Export <> "")
		$Page->ShowHeader = ($Page->ExportPageBreakCount == 0) ? FALSE : ($Page->GrpCount % $Page->ExportPageBreakCount == 0);

	// Page_Breaking server event
	if ($Page->ShowHeader)
		$Page->Page_Breaking($Page->ShowHeader, $Page->PageBreakContent);
	$Page->GrpCount++;

	// Handle EOF
	if (!$rsgrp || $rsgrp->EOF)
		$Page->ShowHeader = FALSE;
}
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<?php if (($Page->StopGrp - $Page->StartGrp + 1) <> $Page->TotalGrps) { ?>
<?php
			$Page->ResetAttrs();
			$Page->RowType = EWR_ROWTYPE_TOTAL;
			$Page->RowTotalType = EWR_ROWTOTAL_PAGE;
			$Page->RowAttrs["class"] = "ewRptPageSummary";
			$Page->RenderRow();
?>
	<!-- Page Summary -->
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpFldCount > 0) { ?>
	<td colspan="<?php echo $Page->GrpFldCount ?>"><?php echo $Page->RenderSummaryCaptions("page") ?></td>
<?php } ?>
<!-- Dynamic columns begin -->
<?php
	$cntcol = count($Page->SummaryViewValue);
	for ($iy = 1; $iy <= $cntcol; $iy++) {
		$bColShow = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Visible : TRUE;
		$sColDesc = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
		if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $Page->SummaryCellAttributes($iy-1) ?>><?php echo $Page->RenderSummaryFields($iy-1) ?></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
	</tr>
<?php } ?>
<?php
			$Page->ResetAttrs();
			$Page->RowType = EWR_ROWTYPE_TOTAL;
			$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
			$Page->RowAttrs["class"] = "ewRptGrandSummary";
			$Page->RenderRow();
?>
	<!-- Grand Total -->
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpFldCount > 0) { ?>
	<td colspan="<?php echo $Page->GrpFldCount ?>"><?php echo $Page->RenderSummaryCaptions("grand") ?></td>
<?php } ?>
<!-- Dynamic columns begin -->
<?php
	$cntcol = count($Page->SummaryViewValue);
	for ($iy = 1; $iy <= $cntcol; $iy++) {
		$bColShow = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Visible : TRUE;
		$sColDesc = ($iy <= $Page->ColCount) ? $Page->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
		if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $Page->SummaryCellAttributes($iy-1) ?>><?php echo $Page->RenderSummaryFields($iy-1) ?></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
	</tr>
</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || TRUE) { // Show footer ?>
</table>
</div>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "cv1ctbpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
</div>
<!-- Crosstab report ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container (report) (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<a id="cht_cv1_cv1_report"></a>
<div class="">
<div id="div_ctl_cv1_cv1_report" class="ewChart">
<div id="div_cv1_cv1_report" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_cv1_cv1_report_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->cv1_report;

// Set up chart SQL
$SqlSelect = str_replace("<DistinctColumnFields>", $Table->DistinctColumnFields, $Table->getSqlSelect());
$SqlChartSelect = $Chart->SqlSelect;
$sSqlChartBase = $Table->getSqlFrom();

// Load chart data from sql directly
$sSql = $SqlChartSelect . $sSqlChartBase;
$sChartFilter = $Chart->SqlWhere;
ewr_AddFilter($sChartFilter, $Table->getSqlWhere());
$sSql = ewr_BuildReportSql($sSql, $sChartFilter, $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $Page->Filter, "");
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $Page->DrillDownInPanel;

// Update chart drill down url from filter
$Chart->ChartDrillDownUrl = str_replace("f1", ewr_Encrypt($Page->GetDrillDownSQL($Page->member_name, "member_name", 0, -1)), $Chart->ChartDrillDownUrl);
$Chart->ChartDrillDownUrl = str_replace("f2", ewr_Encrypt($Page->GetDrillDownSQL($Page->class_name, "class_name", 3, 0)), $Chart->ChartDrillDownUrl);
$Chart->ChartDrillDownUrl = str_replace("f3", ewr_Encrypt($Page->GetDrillDownSQL($Page->weekday, "weekday", 0, -1)), $Chart->ChartDrillDownUrl);

// Set up page break
if (($Page->Export == "print" || $Page->Export == "pdf" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD")) && $Page->ExportChartPageBreak) {

	// Page_Breaking server event
	$Page->Page_Breaking($Page->ExportChartPageBreak, $Page->PageBreakContent);
	$Chart->PageBreakType = "before";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = ($Page->Export == "" || ($Page->Export == "print" && $Page->CustomExport == "") || ($Page->Export == "email" && @$_POST["contenttype"] == "url"));
$Chart->ShowTempImage = ($Page->Export == "pdf" || $Page->CustomExport <> "" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD"));
?>
<?php include_once "cv1_cv1_reportchart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<a id="cht_cv1_PersonTimeChart"></a>
<div class="">
<div id="div_ctl_cv1_PersonTimeChart" class="ewChart">
<div id="div_cv1_PersonTimeChart" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_cv1_PersonTimeChart_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->PersonTimeChart;

// Set up chart SQL
$SqlSelect = str_replace("<DistinctColumnFields>", $Table->DistinctColumnFields, $Table->getSqlSelect());
$SqlChartSelect = $Chart->SqlSelect;
$sSqlChartBase = $Table->getSqlFrom();

// Load chart data from sql directly
$sSql = $SqlChartSelect . $sSqlChartBase;
$sChartFilter = $Chart->SqlWhere;
ewr_AddFilter($sChartFilter, $Table->getSqlWhere());
$sSql = ewr_BuildReportSql($sSql, $sChartFilter, $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $Page->Filter, "");
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $Page->DrillDownInPanel;

// Update chart drill down url from filter
$Chart->ChartDrillDownUrl = str_replace("f1", ewr_Encrypt($Page->GetDrillDownSQL($Page->member_name, "member_name", 0, -1)), $Chart->ChartDrillDownUrl);
$Chart->ChartDrillDownUrl = str_replace("f2", ewr_Encrypt($Page->GetDrillDownSQL($Page->weekday, "weekday", 0, -1)), $Chart->ChartDrillDownUrl);

// Set up page break
if (($Page->Export == "print" || $Page->Export == "pdf" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD")) && $Page->ExportChartPageBreak) {

	// Page_Breaking server event
	$Page->Page_Breaking($Page->ExportChartPageBreak, $Page->PageBreakContent);
	$Chart->PageBreakType = "before";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = ($Page->Export == "" || ($Page->Export == "print" && $Page->CustomExport == "") || ($Page->Export == "email" && @$_POST["contenttype"] == "url"));
$Chart->ShowTempImage = ($Page->Export == "pdf" || $Page->CustomExport <> "" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD"));
?>
<?php include_once "cv1_PersonTimeChartchart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<a id="cht_cv1_ByWeekday"></a>
<div class="">
<div id="div_ctl_cv1_ByWeekday" class="ewChart">
<div id="div_cv1_ByWeekday" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_cv1_ByWeekday_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->ByWeekday;

// Set up chart SQL
$SqlSelect = str_replace("<DistinctColumnFields>", $Table->DistinctColumnFields, $Table->getSqlSelect());
$SqlChartSelect = $Chart->SqlSelect;
$sSqlChartBase = $Table->getSqlFrom();

// Load chart data from sql directly
$sSql = $SqlChartSelect . $sSqlChartBase;
$sChartFilter = $Chart->SqlWhere;
ewr_AddFilter($sChartFilter, $Table->getSqlWhere());
$sSql = ewr_BuildReportSql($sSql, $sChartFilter, $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $Page->Filter, "");
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $Page->DrillDownInPanel;

// Update chart drill down url from filter
$Chart->ChartDrillDownUrl = str_replace("f1", ewr_Encrypt($Page->GetDrillDownSQL($Page->member_name, "member_name", 0, -1)), $Chart->ChartDrillDownUrl);
$Chart->ChartDrillDownUrl = str_replace("f2", ewr_Encrypt($Page->GetDrillDownSQL($Page->starttime, "starttime", 0, -1)), $Chart->ChartDrillDownUrl);
$Chart->ChartDrillDownUrl = str_replace("f3", ewr_Encrypt($Page->GetDrillDownSQL($Page->class_name, "class_name", 3, 0)), $Chart->ChartDrillDownUrl);

// Set up page break
if (($Page->Export == "print" || $Page->Export == "pdf" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD")) && $Page->ExportChartPageBreak) {

	// Page_Breaking server event
	$Page->Page_Breaking($Page->ExportChartPageBreak, $Page->PageBreakContent);
	$Chart->PageBreakType = "before";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = ($Page->Export == "" || ($Page->Export == "print" && $Page->CustomExport == "") || ($Page->Export == "email" && @$_POST["contenttype"] == "url"));
$Chart->ShowTempImage = ($Page->Export == "pdf" || $Page->CustomExport <> "" || $Page->Export == "email" || $Page->Export == "excel" && defined("EWR_USE_PHPEXCEL") || $Page->Export == "word" && defined("EWR_USE_PHPWORD"));
?>
<?php include_once "cv1_ByWeekdaychart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom container (end) -->
</div>
<!-- container (end) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
