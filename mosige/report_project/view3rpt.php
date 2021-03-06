<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "view3rptinfo.php" ?>
<?php

//
// Page class
//

$view3_rpt = NULL; // Initialize page object first

class crview3_rpt extends crview3 {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'view3_rpt';

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

		// Table object (view3)
		if (!isset($GLOBALS["view3"])) {
			$GLOBALS["view3"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["view3"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'rpt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'view3', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fview3rpt";
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
		$item->Visible = FALSE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = FALSE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = FALSE;
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = FALSE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_view3\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_view3',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fview3rpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fview3rpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fview3rpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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
	var $GrpCounter = array(); // Group counter
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
	var $SubGrpFldCount = 0;
	var $DtlFldCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
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

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 11;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(TRUE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->member_name->SelectionList = "";
		$this->member_name->DefaultSelectionList = "";
		$this->member_name->ValueList = "";
		$this->is_attended->SelectionList = "";
		$this->is_attended->DefaultSelectionList = "";
		$this->is_attended->ValueList = "";
		$this->arrangedate->SelectionList = "";
		$this->arrangedate->DefaultSelectionList = "";
		$this->arrangedate->ValueList = "";
		$this->starttime->SelectionList = "";
		$this->starttime->DefaultSelectionList = "";
		$this->starttime->ValueList = "";
		$this->class_name->SelectionList = "";
		$this->class_name->DefaultSelectionList = "";
		$this->class_name->ValueList = "";
		$this->class_description->SelectionList = "";
		$this->class_description->DefaultSelectionList = "";
		$this->class_description->ValueList = "";
		$this->inner_id->SelectionList = "";
		$this->inner_id->DefaultSelectionList = "";
		$this->inner_id->ValueList = "";
		$this->register_time->SelectionList = "";
		$this->register_time->DefaultSelectionList = "";
		$this->register_time->ValueList = "";
		$this->register_id->SelectionList = "";
		$this->register_id->DefaultSelectionList = "";
		$this->register_id->ValueList = "";
		$this->member_id->SelectionList = "";
		$this->member_id->DefaultSelectionList = "";
		$this->member_id->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get sort
		$this->Sort = $this->GetSort();

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
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

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		$conn = &$this->Connection();
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row

	//		$rs->MoveFirst(); // NOTE: no need to move position
				$this->FirstRowData = array();
				$this->FirstRowData['member_name'] = ewr_Conv($rs->fields('member_name'),200);
				$this->FirstRowData['is_attended'] = ewr_Conv($rs->fields('is_attended'),2);
				$this->FirstRowData['arrangedate'] = ewr_Conv($rs->fields('arrangedate'),133);
				$this->FirstRowData['starttime'] = ewr_Conv($rs->fields('starttime'),134);
				$this->FirstRowData['class_name'] = ewr_Conv($rs->fields('class_name'),200);
				$this->FirstRowData['class_description'] = ewr_Conv($rs->fields('class_description'),200);
				$this->FirstRowData['inner_id'] = ewr_Conv($rs->fields('inner_id'),3);
				$this->FirstRowData['register_time'] = ewr_Conv($rs->fields('register_time'),135);
				$this->FirstRowData['register_id'] = ewr_Conv($rs->fields('register_id'),3);
				$this->FirstRowData['member_id'] = ewr_Conv($rs->fields('member_id'),3);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->member_name->setDbValue($rs->fields('member_name'));
			$this->is_attended->setDbValue($rs->fields('is_attended'));
			$this->arrangedate->setDbValue($rs->fields('arrangedate'));
			$this->starttime->setDbValue($rs->fields('starttime'));
			$this->class_name->setDbValue($rs->fields('class_name'));
			$this->class_description->setDbValue($rs->fields('class_description'));
			$this->inner_id->setDbValue($rs->fields('inner_id'));
			$this->register_time->setDbValue($rs->fields('register_time'));
			$this->register_id->setDbValue($rs->fields('register_id'));
			$this->member_id->setDbValue($rs->fields('member_id'));
			$this->Val[1] = $this->member_name->CurrentValue;
			$this->Val[2] = $this->is_attended->CurrentValue;
			$this->Val[3] = $this->arrangedate->CurrentValue;
			$this->Val[4] = $this->starttime->CurrentValue;
			$this->Val[5] = $this->class_name->CurrentValue;
			$this->Val[6] = $this->class_description->CurrentValue;
			$this->Val[7] = $this->inner_id->CurrentValue;
			$this->Val[8] = $this->register_time->CurrentValue;
			$this->Val[9] = $this->register_id->CurrentValue;
			$this->Val[10] = $this->member_id->CurrentValue;
		} else {
			$this->member_name->setDbValue("");
			$this->is_attended->setDbValue("");
			$this->arrangedate->setDbValue("");
			$this->starttime->setDbValue("");
			$this->class_name->setDbValue("");
			$this->class_description->setDbValue("");
			$this->inner_id->setDbValue("");
			$this->register_time->setDbValue("");
			$this->register_id->setDbValue("");
			$this->member_id->setDbValue("");
		}
	}

	//  Set up starting group
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

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for member_name

			if ($popupname == 'view3_member_name') {
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
						$this->member_name->ViewValue = $this->member_name->CurrentValue;
						ewr_SetupDistinctValues($this->member_name->ValueList, $this->member_name->CurrentValue, $this->member_name->ViewValue, FALSE, $this->member_name->FldDelimiter);
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

			// Build distinct values for is_attended
			if ($popupname == 'view3_is_attended') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->is_attended->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->is_attended->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->is_attended->setDbValue($rswrk->fields[0]);
					if (is_null($this->is_attended->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->is_attended->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->is_attended->ViewValue = $this->is_attended->CurrentValue;
						ewr_SetupDistinctValues($this->is_attended->ValueList, $this->is_attended->CurrentValue, $this->is_attended->ViewValue, FALSE, $this->is_attended->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->is_attended->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->is_attended->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->is_attended;
			}

			// Build distinct values for arrangedate
			if ($popupname == 'view3_arrangedate') {
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
						$this->arrangedate->ViewValue = ewr_FormatDateTime($this->arrangedate->CurrentValue, 5);
						ewr_SetupDistinctValues($this->arrangedate->ValueList, $this->arrangedate->CurrentValue, $this->arrangedate->ViewValue, FALSE, $this->arrangedate->FldDelimiter);
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
			if ($popupname == 'view3_starttime') {
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
						$this->starttime->ViewValue = $this->starttime->CurrentValue;
						ewr_SetupDistinctValues($this->starttime->ValueList, $this->starttime->CurrentValue, $this->starttime->ViewValue, FALSE, $this->starttime->FldDelimiter);
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

			// Build distinct values for class_name
			if ($popupname == 'view3_class_name') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->class_name->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->class_name->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->class_name->setDbValue($rswrk->fields[0]);
					if (is_null($this->class_name->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->class_name->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->class_name->ViewValue = $this->class_name->CurrentValue;
						ewr_SetupDistinctValues($this->class_name->ValueList, $this->class_name->CurrentValue, $this->class_name->ViewValue, FALSE, $this->class_name->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->class_name->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->class_name->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->class_name;
			}

			// Build distinct values for class_description
			if ($popupname == 'view3_class_description') {
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
						$this->class_description->ViewValue = $this->class_description->CurrentValue;
						ewr_SetupDistinctValues($this->class_description->ValueList, $this->class_description->CurrentValue, $this->class_description->ViewValue, FALSE, $this->class_description->FldDelimiter);
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
			if ($popupname == 'view3_inner_id') {
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
						$this->inner_id->ViewValue = $this->inner_id->CurrentValue;
						ewr_SetupDistinctValues($this->inner_id->ValueList, $this->inner_id->CurrentValue, $this->inner_id->ViewValue, FALSE, $this->inner_id->FldDelimiter);
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

			// Build distinct values for register_time
			if ($popupname == 'view3_register_time') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->register_time->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->register_time->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->register_time->setDbValue($rswrk->fields[0]);
					if (is_null($this->register_time->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->register_time->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->register_time->ViewValue = ewr_FormatDateTime($this->register_time->CurrentValue, 5);
						ewr_SetupDistinctValues($this->register_time->ValueList, $this->register_time->CurrentValue, $this->register_time->ViewValue, FALSE, $this->register_time->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->register_time->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->register_time->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->register_time;
			}

			// Build distinct values for register_id
			if ($popupname == 'view3_register_id') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->register_id->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->register_id->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->register_id->setDbValue($rswrk->fields[0]);
					if (is_null($this->register_id->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->register_id->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->register_id->ViewValue = $this->register_id->CurrentValue;
						ewr_SetupDistinctValues($this->register_id->ValueList, $this->register_id->CurrentValue, $this->register_id->ViewValue, FALSE, $this->register_id->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->register_id->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->register_id->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->register_id;
			}

			// Build distinct values for member_id
			if ($popupname == 'view3_member_id') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
				$sSql = ewr_BuildReportSql($this->member_id->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->member_id->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->member_id->setDbValue($rswrk->fields[0]);
					if (is_null($this->member_id->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->member_id->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->member_id->ViewValue = $this->member_id->CurrentValue;
						ewr_SetupDistinctValues($this->member_id->ValueList, $this->member_id->CurrentValue, $this->member_id->ViewValue, FALSE, $this->member_id->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->member_id->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->member_id->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->member_id;
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
				$this->ClearSessionSelection('member_name');
				$this->ClearSessionSelection('is_attended');
				$this->ClearSessionSelection('arrangedate');
				$this->ClearSessionSelection('starttime');
				$this->ClearSessionSelection('class_name');
				$this->ClearSessionSelection('class_description');
				$this->ClearSessionSelection('inner_id');
				$this->ClearSessionSelection('register_time');
				$this->ClearSessionSelection('register_id');
				$this->ClearSessionSelection('member_id');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get member_name selected values

		if (is_array(@$_SESSION["sel_view3_member_name"])) {
			$this->LoadSelectionFromSession('member_name');
		} elseif (@$_SESSION["sel_view3_member_name"] == EWR_INIT_VALUE) { // Select all
			$this->member_name->SelectionList = "";
		}

		// Get is_attended selected values
		if (is_array(@$_SESSION["sel_view3_is_attended"])) {
			$this->LoadSelectionFromSession('is_attended');
		} elseif (@$_SESSION["sel_view3_is_attended"] == EWR_INIT_VALUE) { // Select all
			$this->is_attended->SelectionList = "";
		}

		// Get arrangedate selected values
		if (is_array(@$_SESSION["sel_view3_arrangedate"])) {
			$this->LoadSelectionFromSession('arrangedate');
		} elseif (@$_SESSION["sel_view3_arrangedate"] == EWR_INIT_VALUE) { // Select all
			$this->arrangedate->SelectionList = "";
		}

		// Get starttime selected values
		if (is_array(@$_SESSION["sel_view3_starttime"])) {
			$this->LoadSelectionFromSession('starttime');
		} elseif (@$_SESSION["sel_view3_starttime"] == EWR_INIT_VALUE) { // Select all
			$this->starttime->SelectionList = "";
		}

		// Get class_name selected values
		if (is_array(@$_SESSION["sel_view3_class_name"])) {
			$this->LoadSelectionFromSession('class_name');
		} elseif (@$_SESSION["sel_view3_class_name"] == EWR_INIT_VALUE) { // Select all
			$this->class_name->SelectionList = "";
		}

		// Get class_description selected values
		if (is_array(@$_SESSION["sel_view3_class_description"])) {
			$this->LoadSelectionFromSession('class_description');
		} elseif (@$_SESSION["sel_view3_class_description"] == EWR_INIT_VALUE) { // Select all
			$this->class_description->SelectionList = "";
		}

		// Get inner_id selected values
		if (is_array(@$_SESSION["sel_view3_inner_id"])) {
			$this->LoadSelectionFromSession('inner_id');
		} elseif (@$_SESSION["sel_view3_inner_id"] == EWR_INIT_VALUE) { // Select all
			$this->inner_id->SelectionList = "";
		}

		// Get register_time selected values
		if (is_array(@$_SESSION["sel_view3_register_time"])) {
			$this->LoadSelectionFromSession('register_time');
		} elseif (@$_SESSION["sel_view3_register_time"] == EWR_INIT_VALUE) { // Select all
			$this->register_time->SelectionList = "";
		}

		// Get register_id selected values
		if (is_array(@$_SESSION["sel_view3_register_id"])) {
			$this->LoadSelectionFromSession('register_id');
		} elseif (@$_SESSION["sel_view3_register_id"] == EWR_INIT_VALUE) { // Select all
			$this->register_id->SelectionList = "";
		}

		// Get member_id selected values
		if (is_array(@$_SESSION["sel_view3_member_id"])) {
			$this->LoadSelectionFromSession('member_id');
		} elseif (@$_SESSION["sel_view3_member_id"] == EWR_INIT_VALUE) { // Select all
			$this->member_id->SelectionList = "";
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
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND && !$this->GrandSummarySetup) { // Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}

			// Get total from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectAgg(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$sSql = $this->getSqlAggPfx() . $sSql . $this->getSqlAggSfx();
			$rsagg = $conn->Execute($sSql);
			if ($rsagg) {
				$this->GrandCnt[1] = $this->TotCount;
				$this->GrandCnt[2] = $this->TotCount;
				$this->GrandCnt[3] = $this->TotCount;
				$this->GrandCnt[4] = $this->TotCount;
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandCnt[7] = $this->TotCount;
				$this->GrandCnt[8] = $this->TotCount;
				$this->GrandCnt[9] = $this->TotCount;
				$this->GrandCnt[10] = $this->TotCount;
				$this->GrandCnt[10] = $rsagg->fields("cnt_member_id");
				$rsagg->Close();
				$bGotSummary = TRUE;
			}

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
			$this->RowAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel; // Set up row class

			// member_id
			$this->member_id->CntViewValue = $this->member_id->CntValue;
			$this->member_id->CntViewValue = ewr_FormatNumber($this->member_id->CntViewValue, 0, -2, -2, -2);
			$this->member_id->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// member_name
			$this->member_name->HrefValue = "";

			// is_attended
			$this->is_attended->HrefValue = "";

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";

			// class_name
			$this->class_name->HrefValue = "";

			// class_description
			$this->class_description->HrefValue = "";

			// inner_id
			$this->inner_id->HrefValue = "";

			// register_time
			$this->register_time->HrefValue = "";

			// register_id
			$this->register_id->HrefValue = "";

			// member_id
			$this->member_id->HrefValue = "";
		} else {

			// member_name
			$this->member_name->ViewValue = $this->member_name->CurrentValue;
			$this->member_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// is_attended
			$this->is_attended->ViewValue = $this->is_attended->CurrentValue;
			$this->is_attended->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// arrangedate
			$this->arrangedate->ViewValue = $this->arrangedate->CurrentValue;
			$this->arrangedate->ViewValue = ewr_FormatDateTime($this->arrangedate->ViewValue, 5);
			$this->arrangedate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// starttime
			$this->starttime->ViewValue = $this->starttime->CurrentValue;
			$this->starttime->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// class_name
			$this->class_name->ViewValue = $this->class_name->CurrentValue;
			$this->class_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// class_description
			$this->class_description->ViewValue = $this->class_description->CurrentValue;
			$this->class_description->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// inner_id
			$this->inner_id->ViewValue = $this->inner_id->CurrentValue;
			$this->inner_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// register_time
			$this->register_time->ViewValue = $this->register_time->CurrentValue;
			$this->register_time->ViewValue = ewr_FormatDateTime($this->register_time->ViewValue, 5);
			$this->register_time->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// register_id
			$this->register_id->ViewValue = $this->register_id->CurrentValue;
			$this->register_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_id
			$this->member_id->ViewValue = $this->member_id->CurrentValue;
			$this->member_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_name
			$this->member_name->HrefValue = "";

			// is_attended
			$this->is_attended->HrefValue = "";

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";

			// class_name
			$this->class_name->HrefValue = "";

			// class_description
			$this->class_description->HrefValue = "";

			// inner_id
			$this->inner_id->HrefValue = "";

			// register_time
			$this->register_time->HrefValue = "";

			// register_id
			$this->register_id->HrefValue = "";

			// member_id
			$this->member_id->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// member_id
			$CurrentValue = $this->member_id->CntValue;
			$ViewValue = &$this->member_id->CntViewValue;
			$ViewAttrs = &$this->member_id->ViewAttrs;
			$CellAttrs = &$this->member_id->CellAttrs;
			$HrefValue = &$this->member_id->HrefValue;
			$LinkAttrs = &$this->member_id->LinkAttrs;
			$this->Cell_Rendered($this->member_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

			// member_name
			$CurrentValue = $this->member_name->CurrentValue;
			$ViewValue = &$this->member_name->ViewValue;
			$ViewAttrs = &$this->member_name->ViewAttrs;
			$CellAttrs = &$this->member_name->CellAttrs;
			$HrefValue = &$this->member_name->HrefValue;
			$LinkAttrs = &$this->member_name->LinkAttrs;
			$this->Cell_Rendered($this->member_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// is_attended
			$CurrentValue = $this->is_attended->CurrentValue;
			$ViewValue = &$this->is_attended->ViewValue;
			$ViewAttrs = &$this->is_attended->ViewAttrs;
			$CellAttrs = &$this->is_attended->CellAttrs;
			$HrefValue = &$this->is_attended->HrefValue;
			$LinkAttrs = &$this->is_attended->LinkAttrs;
			$this->Cell_Rendered($this->is_attended, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// arrangedate
			$CurrentValue = $this->arrangedate->CurrentValue;
			$ViewValue = &$this->arrangedate->ViewValue;
			$ViewAttrs = &$this->arrangedate->ViewAttrs;
			$CellAttrs = &$this->arrangedate->CellAttrs;
			$HrefValue = &$this->arrangedate->HrefValue;
			$LinkAttrs = &$this->arrangedate->LinkAttrs;
			$this->Cell_Rendered($this->arrangedate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// starttime
			$CurrentValue = $this->starttime->CurrentValue;
			$ViewValue = &$this->starttime->ViewValue;
			$ViewAttrs = &$this->starttime->ViewAttrs;
			$CellAttrs = &$this->starttime->CellAttrs;
			$HrefValue = &$this->starttime->HrefValue;
			$LinkAttrs = &$this->starttime->LinkAttrs;
			$this->Cell_Rendered($this->starttime, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// class_name
			$CurrentValue = $this->class_name->CurrentValue;
			$ViewValue = &$this->class_name->ViewValue;
			$ViewAttrs = &$this->class_name->ViewAttrs;
			$CellAttrs = &$this->class_name->CellAttrs;
			$HrefValue = &$this->class_name->HrefValue;
			$LinkAttrs = &$this->class_name->LinkAttrs;
			$this->Cell_Rendered($this->class_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// class_description
			$CurrentValue = $this->class_description->CurrentValue;
			$ViewValue = &$this->class_description->ViewValue;
			$ViewAttrs = &$this->class_description->ViewAttrs;
			$CellAttrs = &$this->class_description->CellAttrs;
			$HrefValue = &$this->class_description->HrefValue;
			$LinkAttrs = &$this->class_description->LinkAttrs;
			$this->Cell_Rendered($this->class_description, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// inner_id
			$CurrentValue = $this->inner_id->CurrentValue;
			$ViewValue = &$this->inner_id->ViewValue;
			$ViewAttrs = &$this->inner_id->ViewAttrs;
			$CellAttrs = &$this->inner_id->CellAttrs;
			$HrefValue = &$this->inner_id->HrefValue;
			$LinkAttrs = &$this->inner_id->LinkAttrs;
			$this->Cell_Rendered($this->inner_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// register_time
			$CurrentValue = $this->register_time->CurrentValue;
			$ViewValue = &$this->register_time->ViewValue;
			$ViewAttrs = &$this->register_time->ViewAttrs;
			$CellAttrs = &$this->register_time->CellAttrs;
			$HrefValue = &$this->register_time->HrefValue;
			$LinkAttrs = &$this->register_time->LinkAttrs;
			$this->Cell_Rendered($this->register_time, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// register_id
			$CurrentValue = $this->register_id->CurrentValue;
			$ViewValue = &$this->register_id->ViewValue;
			$ViewAttrs = &$this->register_id->ViewAttrs;
			$CellAttrs = &$this->register_id->CellAttrs;
			$HrefValue = &$this->register_id->HrefValue;
			$LinkAttrs = &$this->register_id->LinkAttrs;
			$this->Cell_Rendered($this->register_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_id
			$CurrentValue = $this->member_id->CurrentValue;
			$ViewValue = &$this->member_id->ViewValue;
			$ViewAttrs = &$this->member_id->ViewAttrs;
			$CellAttrs = &$this->member_id->CellAttrs;
			$HrefValue = &$this->member_id->HrefValue;
			$LinkAttrs = &$this->member_id->LinkAttrs;
			$this->Cell_Rendered($this->member_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpFldCount = 0;
		$this->SubGrpFldCount = 0;
		$this->DtlFldCount = 0;
		if ($this->member_name->Visible) $this->DtlFldCount += 1;
		if ($this->is_attended->Visible) $this->DtlFldCount += 1;
		if ($this->arrangedate->Visible) $this->DtlFldCount += 1;
		if ($this->starttime->Visible) $this->DtlFldCount += 1;
		if ($this->class_name->Visible) $this->DtlFldCount += 1;
		if ($this->class_description->Visible) $this->DtlFldCount += 1;
		if ($this->inner_id->Visible) $this->DtlFldCount += 1;
		if ($this->register_time->Visible) $this->DtlFldCount += 1;
		if ($this->register_id->Visible) $this->DtlFldCount += 1;
		if ($this->member_id->Visible) $this->DtlFldCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("rpt", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_view3_$parm"] = "";
		$_SESSION["rf_view3_$parm"] = "";
		$_SESSION["rt_view3_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_view3_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_view3_$parm"];
		$fld->RangeTo = @$_SESSION["rt_view3_$parm"];
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
		// Field is_attended
		// $this->is_attended->DefaultSelectionList = array("val1", "val2");
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
		// Field register_time
		// $this->register_time->DefaultSelectionList = array("val1", "val2");
		// Field register_id
		// $this->register_id->DefaultSelectionList = array("val1", "val2");
		// Field member_id
		// $this->member_id->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check member_name popup filter
		if (!ewr_MatchedArray($this->member_name->DefaultSelectionList, $this->member_name->SelectionList))
			return TRUE;

		// Check is_attended popup filter
		if (!ewr_MatchedArray($this->is_attended->DefaultSelectionList, $this->is_attended->SelectionList))
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

		// Check register_time popup filter
		if (!ewr_MatchedArray($this->register_time->DefaultSelectionList, $this->register_time->SelectionList))
			return TRUE;

		// Check register_id popup filter
		if (!ewr_MatchedArray($this->register_id->DefaultSelectionList, $this->register_id->SelectionList))
			return TRUE;

		// Check member_id popup filter
		if (!ewr_MatchedArray($this->member_id->DefaultSelectionList, $this->member_id->SelectionList))
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

		// Field is_attended
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->is_attended->SelectionList))
			$sWrk = ewr_JoinArray($this->is_attended->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->is_attended->FldCaption() . "</span>" . $sFilter . "</div>";

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

		// Field register_time
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->register_time->SelectionList))
			$sWrk = ewr_JoinArray($this->register_time->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->register_time->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field register_id
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->register_id->SelectionList))
			$sWrk = ewr_JoinArray($this->register_id->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->register_id->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field member_id
		$sExtWrk = "";
		$sWrk = "";
		if (is_array($this->member_id->SelectionList))
			$sWrk = ewr_JoinArray($this->member_id->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->member_id->FldCaption() . "</span>" . $sFilter . "</div>";
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

		// Field is_attended
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->is_attended->SelectionList <> EWR_INIT_VALUE) ? $this->is_attended->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_is_attended\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field register_time
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->register_time->SelectionList <> EWR_INIT_VALUE) ? $this->register_time->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_register_time\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field register_id
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->register_id->SelectionList <> EWR_INIT_VALUE) ? $this->register_id->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_register_id\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field member_id
		$sWrk = "";
		if ($sWrk == "") {
			$sWrk = ($this->member_id->SelectionList <> EWR_INIT_VALUE) ? $this->member_id->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_member_id\":\"" . ewr_JsEncode2($sWrk) . "\"";
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
			$_SESSION["sel_view3_member_name"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field is_attended
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_is_attended", $filter)) {
			$sWrk = $filter["sel_is_attended"];
			$sWrk = explode("||", $sWrk);
			$this->is_attended->SelectionList = $sWrk;
			$_SESSION["sel_view3_is_attended"] = $sWrk;
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
			$_SESSION["sel_view3_arrangedate"] = $sWrk;
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
			$_SESSION["sel_view3_starttime"] = $sWrk;
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
			$_SESSION["sel_view3_class_name"] = $sWrk;
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
			$_SESSION["sel_view3_class_description"] = $sWrk;
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
			$_SESSION["sel_view3_inner_id"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field register_time
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_register_time", $filter)) {
			$sWrk = $filter["sel_register_time"];
			$sWrk = explode("||", $sWrk);
			$this->register_time->SelectionList = $sWrk;
			$_SESSION["sel_view3_register_time"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field register_id
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_register_id", $filter)) {
			$sWrk = $filter["sel_register_id"];
			$sWrk = explode("||", $sWrk);
			$this->register_id->SelectionList = $sWrk;
			$_SESSION["sel_view3_register_id"] = $sWrk;
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
		}

		// Field member_id
		$bRestoreFilter = FALSE;
		if (array_key_exists("sel_member_id", $filter)) {
			$sWrk = $filter["sel_member_id"];
			$sWrk = explode("||", $sWrk);
			$this->member_id->SelectionList = $sWrk;
			$_SESSION["sel_view3_member_id"] = $sWrk;
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
				$sFilter = ewr_FilterSQL($this->member_name, "`member_name`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->member_name, $sFilter, "popup");
				$this->member_name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->is_attended->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->is_attended, "`is_attended`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->is_attended, $sFilter, "popup");
				$this->is_attended->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->arrangedate->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->arrangedate, "`arrangedate`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->arrangedate, $sFilter, "popup");
				$this->arrangedate->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->starttime->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->starttime, "`starttime`", EWR_DATATYPE_TIME, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->starttime, $sFilter, "popup");
				$this->starttime->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->class_name->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->class_name, "`class_name`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->class_name, $sFilter, "popup");
				$this->class_name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->class_description->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->class_description, "`class_description`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->class_description, $sFilter, "popup");
				$this->class_description->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->inner_id->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->inner_id, "`inner_id`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->inner_id, $sFilter, "popup");
				$this->inner_id->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->register_time->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->register_time, "`register_time`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->register_time, $sFilter, "popup");
				$this->register_time->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->register_id->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->register_id, "`register_id`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->register_id, $sFilter, "popup");
				$this->register_id->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
			if (is_array($this->member_id->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->member_id, "`member_id`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->member_id, $sFilter, "popup");
				$this->member_id->CurrentFilter = $sFilter;
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

		// Handle group/row/column field
		if ($parm >= 0 && $sql == "") {
			switch (substr($fld->FldVar,2)) {
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
				$this->member_name->setSort("");
				$this->is_attended->setSort("");
				$this->arrangedate->setSort("");
				$this->starttime->setSort("");
				$this->class_name->setSort("");
				$this->class_description->setSort("");
				$this->inner_id->setSort("");
				$this->register_time->setSort("");
				$this->register_id->setSort("");
				$this->member_id->setSort("");
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
if (!isset($view3_rpt)) $view3_rpt = new crview3_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$view3_rpt;

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
<script type="text/javascript">

// Create page object
var view3_rpt = new ewr_Page("view3_rpt");

// Page properties
view3_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = view3_rpt.PageID;

// Extend page with Chart_Rendering function
view3_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
view3_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php if (!$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fview3rpt = new ewr_Form("fview3rpt");
</script>
<?php } ?>
<?php if (!$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<!-- top slot -->
<div class="ewToolbar">
<?php if (!$Page->DrillDown || !$Page->DrillDownInPanel) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
}
?>
<?php if (!$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
	<!-- Left slot -->
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
	<!-- center slot -->
<!-- summary report starts -->
<div id="report_summary">
<?php if (!$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fview3rpt" id="fview3rpt" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
</form>
<script type="text/javascript">
fview3rpt.Init();
fview3rpt.FilterList = <?php echo $Page->GetFilterList() ?>;
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
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->member_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_name"><div class="view3_member_name"><span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_name">
<?php if ($Page->SortUrl($Page->member_name) == "") { ?>
		<div class="ewTableHeaderBtn view3_member_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_member_name', false, '<?php echo $Page->member_name->RangeFrom; ?>', '<?php echo $Page->member_name->RangeTo; ?>');" id="x_member_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_member_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_member_name', false, '<?php echo $Page->member_name->RangeFrom; ?>', '<?php echo $Page->member_name->RangeTo; ?>');" id="x_member_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->is_attended->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="is_attended"><div class="view3_is_attended"><span class="ewTableHeaderCaption"><?php echo $Page->is_attended->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="is_attended">
<?php if ($Page->SortUrl($Page->is_attended) == "") { ?>
		<div class="ewTableHeaderBtn view3_is_attended">
			<span class="ewTableHeaderCaption"><?php echo $Page->is_attended->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_is_attended', false, '<?php echo $Page->is_attended->RangeFrom; ?>', '<?php echo $Page->is_attended->RangeTo; ?>');" id="x_is_attended<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_is_attended" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->is_attended) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->is_attended->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->is_attended->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->is_attended->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_is_attended', false, '<?php echo $Page->is_attended->RangeFrom; ?>', '<?php echo $Page->is_attended->RangeTo; ?>');" id="x_is_attended<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="arrangedate"><div class="view3_arrangedate"><span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="arrangedate">
<?php if ($Page->SortUrl($Page->arrangedate) == "") { ?>
		<div class="ewTableHeaderBtn view3_arrangedate">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_arrangedate', false, '<?php echo $Page->arrangedate->RangeFrom; ?>', '<?php echo $Page->arrangedate->RangeTo; ?>');" id="x_arrangedate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_arrangedate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->arrangedate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->arrangedate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->arrangedate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_arrangedate', false, '<?php echo $Page->arrangedate->RangeFrom; ?>', '<?php echo $Page->arrangedate->RangeTo; ?>');" id="x_arrangedate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="starttime"><div class="view3_starttime"><span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="starttime">
<?php if ($Page->SortUrl($Page->starttime) == "") { ?>
		<div class="ewTableHeaderBtn view3_starttime">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_starttime', false, '<?php echo $Page->starttime->RangeFrom; ?>', '<?php echo $Page->starttime->RangeTo; ?>');" id="x_starttime<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_starttime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->starttime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->starttime->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->starttime->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_starttime', false, '<?php echo $Page->starttime->RangeFrom; ?>', '<?php echo $Page->starttime->RangeTo; ?>');" id="x_starttime<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->class_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="class_name"><div class="view3_class_name"><span class="ewTableHeaderCaption"><?php echo $Page->class_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="class_name">
<?php if ($Page->SortUrl($Page->class_name) == "") { ?>
		<div class="ewTableHeaderBtn view3_class_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_name->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_class_name', false, '<?php echo $Page->class_name->RangeFrom; ?>', '<?php echo $Page->class_name->RangeTo; ?>');" id="x_class_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_class_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->class_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->class_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->class_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_class_name', false, '<?php echo $Page->class_name->RangeFrom; ?>', '<?php echo $Page->class_name->RangeTo; ?>');" id="x_class_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="class_description"><div class="view3_class_description"><span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="class_description">
<?php if ($Page->SortUrl($Page->class_description) == "") { ?>
		<div class="ewTableHeaderBtn view3_class_description">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_class_description', false, '<?php echo $Page->class_description->RangeFrom; ?>', '<?php echo $Page->class_description->RangeTo; ?>');" id="x_class_description<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_class_description" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->class_description) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_description->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->class_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->class_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_class_description', false, '<?php echo $Page->class_description->RangeFrom; ?>', '<?php echo $Page->class_description->RangeTo; ?>');" id="x_class_description<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="inner_id"><div class="view3_inner_id"><span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="inner_id">
<?php if ($Page->SortUrl($Page->inner_id) == "") { ?>
		<div class="ewTableHeaderBtn view3_inner_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_inner_id', false, '<?php echo $Page->inner_id->RangeFrom; ?>', '<?php echo $Page->inner_id->RangeTo; ?>');" id="x_inner_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_inner_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->inner_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->inner_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->inner_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->inner_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_inner_id', false, '<?php echo $Page->inner_id->RangeFrom; ?>', '<?php echo $Page->inner_id->RangeTo; ?>');" id="x_inner_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->register_time->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="register_time"><div class="view3_register_time"><span class="ewTableHeaderCaption"><?php echo $Page->register_time->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="register_time">
<?php if ($Page->SortUrl($Page->register_time) == "") { ?>
		<div class="ewTableHeaderBtn view3_register_time">
			<span class="ewTableHeaderCaption"><?php echo $Page->register_time->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_register_time', false, '<?php echo $Page->register_time->RangeFrom; ?>', '<?php echo $Page->register_time->RangeTo; ?>');" id="x_register_time<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_register_time" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->register_time) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->register_time->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->register_time->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->register_time->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_register_time', false, '<?php echo $Page->register_time->RangeFrom; ?>', '<?php echo $Page->register_time->RangeTo; ?>');" id="x_register_time<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->register_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="register_id"><div class="view3_register_id"><span class="ewTableHeaderCaption"><?php echo $Page->register_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="register_id">
<?php if ($Page->SortUrl($Page->register_id) == "") { ?>
		<div class="ewTableHeaderBtn view3_register_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->register_id->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_register_id', false, '<?php echo $Page->register_id->RangeFrom; ?>', '<?php echo $Page->register_id->RangeTo; ?>');" id="x_register_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_register_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->register_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->register_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->register_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->register_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_register_id', false, '<?php echo $Page->register_id->RangeFrom; ?>', '<?php echo $Page->register_id->RangeTo; ?>');" id="x_register_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_id"><div class="view3_member_id"><span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_id">
<?php if ($Page->SortUrl($Page->member_id) == "") { ?>
		<div class="ewTableHeaderBtn view3_member_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_member_id', false, '<?php echo $Page->member_id->RangeFrom; ?>', '<?php echo $Page->member_id->RangeTo; ?>');" id="x_member_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view3_member_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'view3_member_id', false, '<?php echo $Page->member_id->RangeFrom; ?>', '<?php echo $Page->member_id->RangeTo; ?>');" id="x_member_id<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->member_name->Visible) { ?>
		<td data-field="member_name"<?php echo $Page->member_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_member_name"<?php echo $Page->member_name->ViewAttributes() ?>><?php echo $Page->member_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->is_attended->Visible) { ?>
		<td data-field="is_attended"<?php echo $Page->is_attended->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_is_attended"<?php echo $Page->is_attended->ViewAttributes() ?>><?php echo $Page->is_attended->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
		<td data-field="arrangedate"<?php echo $Page->arrangedate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_arrangedate"<?php echo $Page->arrangedate->ViewAttributes() ?>><?php echo $Page->arrangedate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
		<td data-field="starttime"<?php echo $Page->starttime->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_starttime"<?php echo $Page->starttime->ViewAttributes() ?>><?php echo $Page->starttime->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->class_name->Visible) { ?>
		<td data-field="class_name"<?php echo $Page->class_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_class_name"<?php echo $Page->class_name->ViewAttributes() ?>><?php echo $Page->class_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
		<td data-field="class_description"<?php echo $Page->class_description->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_class_description"<?php echo $Page->class_description->ViewAttributes() ?>><?php echo $Page->class_description->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
		<td data-field="inner_id"<?php echo $Page->inner_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_inner_id"<?php echo $Page->inner_id->ViewAttributes() ?>><?php echo $Page->inner_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->register_time->Visible) { ?>
		<td data-field="register_time"<?php echo $Page->register_time->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_register_time"<?php echo $Page->register_time->ViewAttributes() ?>><?php echo $Page->register_time->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->register_id->Visible) { ?>
		<td data-field="register_id"<?php echo $Page->register_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_register_id"<?php echo $Page->register_id->ViewAttributes() ?>><?php echo $Page->register_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_id->Visible) { ?>
		<td data-field="member_id"<?php echo $Page->member_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view3_member_id"<?php echo $Page->member_id->ViewAttributes() ?>><?php echo $Page->member_id->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<?php if (($Page->StopGrp - $Page->StartGrp + 1) <> $Page->TotalGrps) { ?>
<?php
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_PAGE;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptPageSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpFldCount + $Page->DtlFldCount) ?>"><?php echo $ReportLanguage->Phrase("RptPageTotal") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->Cnt[0][0],0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
<?php
	$Page->ResetAttrs();
	$Page->member_id->Count = $Page->Cnt[0][10];
	$Page->member_id->CntValue = $Page->Cnt[0][10]; // Load CNT
	$Page->RowTotalSubType = EWR_ROWTOTAL_CNT;
	$Page->RowAttrs["class"] = "ewRptPageSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->member_name->Visible) { ?>
		<td data-field="member_name"<?php echo $Page->member_name->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->is_attended->Visible) { ?>
		<td data-field="is_attended"<?php echo $Page->is_attended->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
		<td data-field="arrangedate"<?php echo $Page->arrangedate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
		<td data-field="starttime"<?php echo $Page->starttime->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->class_name->Visible) { ?>
		<td data-field="class_name"<?php echo $Page->class_name->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
		<td data-field="class_description"<?php echo $Page->class_description->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
		<td data-field="inner_id"<?php echo $Page->inner_id->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->register_time->Visible) { ?>
		<td data-field="register_time"<?php echo $Page->register_time->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->register_id->Visible) { ?>
		<td data-field="register_id"<?php echo $Page->register_id->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->member_id->Visible) { ?>
		<td data-field="member_id"<?php echo $Page->member_id->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span>
<span data-class="tppc_view3_member_id"<?php echo $Page->member_id->ViewAttributes() ?>><?php echo $Page->member_id->CntViewValue ?></span></td>
<?php } ?>
	</tr>
<?php } ?>
<?php
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpFldCount + $Page->DtlFldCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandTotal") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
<?php
	$Page->ResetAttrs();
	$Page->member_id->Count = $Page->GrandCnt[10];
	$Page->member_id->CntValue = $Page->GrandCnt[10]; // Load CNT
	$Page->RowTotalSubType = EWR_ROWTOTAL_CNT;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->member_name->Visible) { ?>
		<td data-field="member_name"<?php echo $Page->member_name->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->is_attended->Visible) { ?>
		<td data-field="is_attended"<?php echo $Page->is_attended->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
		<td data-field="arrangedate"<?php echo $Page->arrangedate->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
		<td data-field="starttime"<?php echo $Page->starttime->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->class_name->Visible) { ?>
		<td data-field="class_name"<?php echo $Page->class_name->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->class_description->Visible) { ?>
		<td data-field="class_description"<?php echo $Page->class_description->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->inner_id->Visible) { ?>
		<td data-field="inner_id"<?php echo $Page->inner_id->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->register_time->Visible) { ?>
		<td data-field="register_time"<?php echo $Page->register_time->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->register_id->Visible) { ?>
		<td data-field="register_id"<?php echo $Page->register_id->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->member_id->Visible) { ?>
		<td data-field="member_id"<?php echo $Page->member_id->CellAttributes() ?>><span class="ewAggregate"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span>
<span data-class="tptc_view3_member_id"<?php echo $Page->member_id->ViewAttributes() ?>><?php echo $Page->member_id->CntViewValue ?></span></td>
<?php } ?>
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
<?php if (!($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "view3rptpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
</div>
<!-- Summary Report Ends -->
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
	<!-- Right slot -->
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
	<!-- Bottom slot -->
<a id="cht_view3_CTV3"></a>
<div class="">
<div id="div_ctl_view3_CTV3" class="ewChart">
<div id="div_view3_CTV3" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_view3_CTV3_grid" class="ewChartGrid"></div>
</div>
</div>
<?php

// Set up chart object
$Chart = &$Table->CTV3;

// Set up chart SQL
$SqlSelect = $Table->getSqlSelect();
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
$Chart->ShowTempImage = (FALSE);
?>
<?php include_once "view3_CTV3chart.php" ?>
<?php if ($Page->Export <> "email" && !$Page->DrillDown) { ?>
<?php if (!$Page->DrillDown) { ?>
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><?php echo $ReportLanguage->Phrase("Top") ?></a>
<?php } ?>
<?php } ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if (!$Page->DrillDown) { ?>
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
