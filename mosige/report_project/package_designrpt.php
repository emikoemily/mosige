<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "package_designrptinfo.php" ?>
<?php

//
// Page class
//

$package_design_rpt = NULL; // Initialize page object first

class crpackage_design_rpt extends crpackage_design {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'package_design_rpt';

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

		// Table object (package_design)
		if (!isset($GLOBALS["package_design"])) {
			$GLOBALS["package_design"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["package_design"];
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
			define("EWR_TABLE_NAME", 'package_design', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fpackage_designrpt";
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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_package_design\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_package_design',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fpackage_designrpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = FALSE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = FALSE;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fpackage_designrpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fpackage_designrpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 21;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

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

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// No filter
		$this->FilterApplied = FALSE;
		$this->FilterOptions->GetItem("savecurrentfilter")->Visible = FALSE;
		$this->FilterOptions->GetItem("deletefilter")->Visible = FALSE;

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
		$this->ShowHeader = ($this->TotalGrps > 0);

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
				$this->FirstRowData['idclass_package_design'] = ewr_Conv($rs->fields('idclass_package_design'),3);
				$this->FirstRowData['package_id'] = ewr_Conv($rs->fields('package_id'),200);
				$this->FirstRowData['package_name'] = ewr_Conv($rs->fields('package_name'),200);
				$this->FirstRowData['package_description'] = ewr_Conv($rs->fields('package_description'),200);
				$this->FirstRowData['package_course_count'] = ewr_Conv($rs->fields('package_course_count'),3);
				$this->FirstRowData['package_price'] = ewr_Conv($rs->fields('package_price'),3);
				$this->FirstRowData['weiddianlink'] = ewr_Conv($rs->fields('weiddianlink'),200);
				$this->FirstRowData['ref_common'] = ewr_Conv($rs->fields('ref_common'),200);
				$this->FirstRowData['star_nandu'] = ewr_Conv($rs->fields('star_nandu'),3);
				$this->FirstRowData['star_liliang'] = ewr_Conv($rs->fields('star_liliang'),3);
				$this->FirstRowData['star_shiyong'] = ewr_Conv($rs->fields('star_shiyong'),3);
				$this->FirstRowData['star_suxing'] = ewr_Conv($rs->fields('star_suxing'),3);
				$this->FirstRowData['star_liliao'] = ewr_Conv($rs->fields('star_liliao'),3);
				$this->FirstRowData['star_quwei'] = ewr_Conv($rs->fields('star_quwei'),3);
				$this->FirstRowData['star_ranzhi'] = ewr_Conv($rs->fields('star_ranzhi'),3);
				$this->FirstRowData['star_shushi'] = ewr_Conv($rs->fields('star_shushi'),3);
				$this->FirstRowData['star_lachen'] = ewr_Conv($rs->fields('star_lachen'),3);
				$this->FirstRowData['star_pingheng'] = ewr_Conv($rs->fields('star_pingheng'),3);
				$this->FirstRowData['star_rouren'] = ewr_Conv($rs->fields('star_rouren'),3);
				$this->FirstRowData['star_chuanlian'] = ewr_Conv($rs->fields('star_chuanlian'),3);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->idclass_package_design->setDbValue($rs->fields('idclass_package_design'));
			$this->package_id->setDbValue($rs->fields('package_id'));
			$this->package_name->setDbValue($rs->fields('package_name'));
			$this->package_description->setDbValue($rs->fields('package_description'));
			$this->package_course_count->setDbValue($rs->fields('package_course_count'));
			$this->package_price->setDbValue($rs->fields('package_price'));
			$this->weiddianlink->setDbValue($rs->fields('weiddianlink'));
			$this->ref_common->setDbValue($rs->fields('ref_common'));
			$this->star_nandu->setDbValue($rs->fields('star_nandu'));
			$this->star_liliang->setDbValue($rs->fields('star_liliang'));
			$this->star_shiyong->setDbValue($rs->fields('star_shiyong'));
			$this->star_suxing->setDbValue($rs->fields('star_suxing'));
			$this->star_liliao->setDbValue($rs->fields('star_liliao'));
			$this->star_quwei->setDbValue($rs->fields('star_quwei'));
			$this->star_ranzhi->setDbValue($rs->fields('star_ranzhi'));
			$this->star_shushi->setDbValue($rs->fields('star_shushi'));
			$this->star_lachen->setDbValue($rs->fields('star_lachen'));
			$this->star_pingheng->setDbValue($rs->fields('star_pingheng'));
			$this->star_rouren->setDbValue($rs->fields('star_rouren'));
			$this->star_chuanlian->setDbValue($rs->fields('star_chuanlian'));
			$this->Val[1] = $this->idclass_package_design->CurrentValue;
			$this->Val[2] = $this->package_id->CurrentValue;
			$this->Val[3] = $this->package_name->CurrentValue;
			$this->Val[4] = $this->package_description->CurrentValue;
			$this->Val[5] = $this->package_course_count->CurrentValue;
			$this->Val[6] = $this->package_price->CurrentValue;
			$this->Val[7] = $this->weiddianlink->CurrentValue;
			$this->Val[8] = $this->ref_common->CurrentValue;
			$this->Val[9] = $this->star_nandu->CurrentValue;
			$this->Val[10] = $this->star_liliang->CurrentValue;
			$this->Val[11] = $this->star_shiyong->CurrentValue;
			$this->Val[12] = $this->star_suxing->CurrentValue;
			$this->Val[13] = $this->star_liliao->CurrentValue;
			$this->Val[14] = $this->star_quwei->CurrentValue;
			$this->Val[15] = $this->star_ranzhi->CurrentValue;
			$this->Val[16] = $this->star_shushi->CurrentValue;
			$this->Val[17] = $this->star_lachen->CurrentValue;
			$this->Val[18] = $this->star_pingheng->CurrentValue;
			$this->Val[19] = $this->star_rouren->CurrentValue;
			$this->Val[20] = $this->star_chuanlian->CurrentValue;
		} else {
			$this->idclass_package_design->setDbValue("");
			$this->package_id->setDbValue("");
			$this->package_name->setDbValue("");
			$this->package_description->setDbValue("");
			$this->package_course_count->setDbValue("");
			$this->package_price->setDbValue("");
			$this->weiddianlink->setDbValue("");
			$this->ref_common->setDbValue("");
			$this->star_nandu->setDbValue("");
			$this->star_liliang->setDbValue("");
			$this->star_shiyong->setDbValue("");
			$this->star_suxing->setDbValue("");
			$this->star_liliao->setDbValue("");
			$this->star_quwei->setDbValue("");
			$this->star_ranzhi->setDbValue("");
			$this->star_shushi->setDbValue("");
			$this->star_lachen->setDbValue("");
			$this->star_pingheng->setDbValue("");
			$this->star_rouren->setDbValue("");
			$this->star_chuanlian->setDbValue("");
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
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
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
		$bGotSummary = TRUE;

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

			// idclass_package_design
			$this->idclass_package_design->HrefValue = "";

			// package_id
			$this->package_id->HrefValue = "";

			// package_name
			$this->package_name->HrefValue = "";

			// package_description
			$this->package_description->HrefValue = "";

			// package_course_count
			$this->package_course_count->HrefValue = "";

			// package_price
			$this->package_price->HrefValue = "";

			// weiddianlink
			$this->weiddianlink->HrefValue = "";

			// ref_common
			$this->ref_common->HrefValue = "";

			// star_nandu
			$this->star_nandu->HrefValue = "";

			// star_liliang
			$this->star_liliang->HrefValue = "";

			// star_shiyong
			$this->star_shiyong->HrefValue = "";

			// star_suxing
			$this->star_suxing->HrefValue = "";

			// star_liliao
			$this->star_liliao->HrefValue = "";

			// star_quwei
			$this->star_quwei->HrefValue = "";

			// star_ranzhi
			$this->star_ranzhi->HrefValue = "";

			// star_shushi
			$this->star_shushi->HrefValue = "";

			// star_lachen
			$this->star_lachen->HrefValue = "";

			// star_pingheng
			$this->star_pingheng->HrefValue = "";

			// star_rouren
			$this->star_rouren->HrefValue = "";

			// star_chuanlian
			$this->star_chuanlian->HrefValue = "";
		} else {

			// idclass_package_design
			$this->idclass_package_design->ViewValue = $this->idclass_package_design->CurrentValue;
			$this->idclass_package_design->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// package_id
			$this->package_id->ViewValue = $this->package_id->CurrentValue;
			$this->package_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// package_name
			$this->package_name->ViewValue = $this->package_name->CurrentValue;
			$this->package_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// package_description
			$this->package_description->ViewValue = $this->package_description->CurrentValue;
			$this->package_description->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// package_course_count
			$this->package_course_count->ViewValue = $this->package_course_count->CurrentValue;
			$this->package_course_count->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// package_price
			$this->package_price->ViewValue = $this->package_price->CurrentValue;
			$this->package_price->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// weiddianlink
			$this->weiddianlink->ViewValue = $this->weiddianlink->CurrentValue;
			$this->weiddianlink->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ref_common
			$this->ref_common->ViewValue = $this->ref_common->CurrentValue;
			$this->ref_common->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_nandu
			$this->star_nandu->ViewValue = $this->star_nandu->CurrentValue;
			$this->star_nandu->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_liliang
			$this->star_liliang->ViewValue = $this->star_liliang->CurrentValue;
			$this->star_liliang->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_shiyong
			$this->star_shiyong->ViewValue = $this->star_shiyong->CurrentValue;
			$this->star_shiyong->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_suxing
			$this->star_suxing->ViewValue = $this->star_suxing->CurrentValue;
			$this->star_suxing->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_liliao
			$this->star_liliao->ViewValue = $this->star_liliao->CurrentValue;
			$this->star_liliao->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_quwei
			$this->star_quwei->ViewValue = $this->star_quwei->CurrentValue;
			$this->star_quwei->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_ranzhi
			$this->star_ranzhi->ViewValue = $this->star_ranzhi->CurrentValue;
			$this->star_ranzhi->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_shushi
			$this->star_shushi->ViewValue = $this->star_shushi->CurrentValue;
			$this->star_shushi->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_lachen
			$this->star_lachen->ViewValue = $this->star_lachen->CurrentValue;
			$this->star_lachen->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_pingheng
			$this->star_pingheng->ViewValue = $this->star_pingheng->CurrentValue;
			$this->star_pingheng->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_rouren
			$this->star_rouren->ViewValue = $this->star_rouren->CurrentValue;
			$this->star_rouren->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// star_chuanlian
			$this->star_chuanlian->ViewValue = $this->star_chuanlian->CurrentValue;
			$this->star_chuanlian->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// idclass_package_design
			$this->idclass_package_design->HrefValue = "";

			// package_id
			$this->package_id->HrefValue = "";

			// package_name
			$this->package_name->HrefValue = "";

			// package_description
			$this->package_description->HrefValue = "";

			// package_course_count
			$this->package_course_count->HrefValue = "";

			// package_price
			$this->package_price->HrefValue = "";

			// weiddianlink
			$this->weiddianlink->HrefValue = "";

			// ref_common
			$this->ref_common->HrefValue = "";

			// star_nandu
			$this->star_nandu->HrefValue = "";

			// star_liliang
			$this->star_liliang->HrefValue = "";

			// star_shiyong
			$this->star_shiyong->HrefValue = "";

			// star_suxing
			$this->star_suxing->HrefValue = "";

			// star_liliao
			$this->star_liliao->HrefValue = "";

			// star_quwei
			$this->star_quwei->HrefValue = "";

			// star_ranzhi
			$this->star_ranzhi->HrefValue = "";

			// star_shushi
			$this->star_shushi->HrefValue = "";

			// star_lachen
			$this->star_lachen->HrefValue = "";

			// star_pingheng
			$this->star_pingheng->HrefValue = "";

			// star_rouren
			$this->star_rouren->HrefValue = "";

			// star_chuanlian
			$this->star_chuanlian->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// idclass_package_design
			$CurrentValue = $this->idclass_package_design->CurrentValue;
			$ViewValue = &$this->idclass_package_design->ViewValue;
			$ViewAttrs = &$this->idclass_package_design->ViewAttrs;
			$CellAttrs = &$this->idclass_package_design->CellAttrs;
			$HrefValue = &$this->idclass_package_design->HrefValue;
			$LinkAttrs = &$this->idclass_package_design->LinkAttrs;
			$this->Cell_Rendered($this->idclass_package_design, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// package_id
			$CurrentValue = $this->package_id->CurrentValue;
			$ViewValue = &$this->package_id->ViewValue;
			$ViewAttrs = &$this->package_id->ViewAttrs;
			$CellAttrs = &$this->package_id->CellAttrs;
			$HrefValue = &$this->package_id->HrefValue;
			$LinkAttrs = &$this->package_id->LinkAttrs;
			$this->Cell_Rendered($this->package_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// package_name
			$CurrentValue = $this->package_name->CurrentValue;
			$ViewValue = &$this->package_name->ViewValue;
			$ViewAttrs = &$this->package_name->ViewAttrs;
			$CellAttrs = &$this->package_name->CellAttrs;
			$HrefValue = &$this->package_name->HrefValue;
			$LinkAttrs = &$this->package_name->LinkAttrs;
			$this->Cell_Rendered($this->package_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// package_description
			$CurrentValue = $this->package_description->CurrentValue;
			$ViewValue = &$this->package_description->ViewValue;
			$ViewAttrs = &$this->package_description->ViewAttrs;
			$CellAttrs = &$this->package_description->CellAttrs;
			$HrefValue = &$this->package_description->HrefValue;
			$LinkAttrs = &$this->package_description->LinkAttrs;
			$this->Cell_Rendered($this->package_description, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// package_course_count
			$CurrentValue = $this->package_course_count->CurrentValue;
			$ViewValue = &$this->package_course_count->ViewValue;
			$ViewAttrs = &$this->package_course_count->ViewAttrs;
			$CellAttrs = &$this->package_course_count->CellAttrs;
			$HrefValue = &$this->package_course_count->HrefValue;
			$LinkAttrs = &$this->package_course_count->LinkAttrs;
			$this->Cell_Rendered($this->package_course_count, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// package_price
			$CurrentValue = $this->package_price->CurrentValue;
			$ViewValue = &$this->package_price->ViewValue;
			$ViewAttrs = &$this->package_price->ViewAttrs;
			$CellAttrs = &$this->package_price->CellAttrs;
			$HrefValue = &$this->package_price->HrefValue;
			$LinkAttrs = &$this->package_price->LinkAttrs;
			$this->Cell_Rendered($this->package_price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// weiddianlink
			$CurrentValue = $this->weiddianlink->CurrentValue;
			$ViewValue = &$this->weiddianlink->ViewValue;
			$ViewAttrs = &$this->weiddianlink->ViewAttrs;
			$CellAttrs = &$this->weiddianlink->CellAttrs;
			$HrefValue = &$this->weiddianlink->HrefValue;
			$LinkAttrs = &$this->weiddianlink->LinkAttrs;
			$this->Cell_Rendered($this->weiddianlink, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ref_common
			$CurrentValue = $this->ref_common->CurrentValue;
			$ViewValue = &$this->ref_common->ViewValue;
			$ViewAttrs = &$this->ref_common->ViewAttrs;
			$CellAttrs = &$this->ref_common->CellAttrs;
			$HrefValue = &$this->ref_common->HrefValue;
			$LinkAttrs = &$this->ref_common->LinkAttrs;
			$this->Cell_Rendered($this->ref_common, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_nandu
			$CurrentValue = $this->star_nandu->CurrentValue;
			$ViewValue = &$this->star_nandu->ViewValue;
			$ViewAttrs = &$this->star_nandu->ViewAttrs;
			$CellAttrs = &$this->star_nandu->CellAttrs;
			$HrefValue = &$this->star_nandu->HrefValue;
			$LinkAttrs = &$this->star_nandu->LinkAttrs;
			$this->Cell_Rendered($this->star_nandu, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_liliang
			$CurrentValue = $this->star_liliang->CurrentValue;
			$ViewValue = &$this->star_liliang->ViewValue;
			$ViewAttrs = &$this->star_liliang->ViewAttrs;
			$CellAttrs = &$this->star_liliang->CellAttrs;
			$HrefValue = &$this->star_liliang->HrefValue;
			$LinkAttrs = &$this->star_liliang->LinkAttrs;
			$this->Cell_Rendered($this->star_liliang, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_shiyong
			$CurrentValue = $this->star_shiyong->CurrentValue;
			$ViewValue = &$this->star_shiyong->ViewValue;
			$ViewAttrs = &$this->star_shiyong->ViewAttrs;
			$CellAttrs = &$this->star_shiyong->CellAttrs;
			$HrefValue = &$this->star_shiyong->HrefValue;
			$LinkAttrs = &$this->star_shiyong->LinkAttrs;
			$this->Cell_Rendered($this->star_shiyong, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_suxing
			$CurrentValue = $this->star_suxing->CurrentValue;
			$ViewValue = &$this->star_suxing->ViewValue;
			$ViewAttrs = &$this->star_suxing->ViewAttrs;
			$CellAttrs = &$this->star_suxing->CellAttrs;
			$HrefValue = &$this->star_suxing->HrefValue;
			$LinkAttrs = &$this->star_suxing->LinkAttrs;
			$this->Cell_Rendered($this->star_suxing, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_liliao
			$CurrentValue = $this->star_liliao->CurrentValue;
			$ViewValue = &$this->star_liliao->ViewValue;
			$ViewAttrs = &$this->star_liliao->ViewAttrs;
			$CellAttrs = &$this->star_liliao->CellAttrs;
			$HrefValue = &$this->star_liliao->HrefValue;
			$LinkAttrs = &$this->star_liliao->LinkAttrs;
			$this->Cell_Rendered($this->star_liliao, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_quwei
			$CurrentValue = $this->star_quwei->CurrentValue;
			$ViewValue = &$this->star_quwei->ViewValue;
			$ViewAttrs = &$this->star_quwei->ViewAttrs;
			$CellAttrs = &$this->star_quwei->CellAttrs;
			$HrefValue = &$this->star_quwei->HrefValue;
			$LinkAttrs = &$this->star_quwei->LinkAttrs;
			$this->Cell_Rendered($this->star_quwei, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_ranzhi
			$CurrentValue = $this->star_ranzhi->CurrentValue;
			$ViewValue = &$this->star_ranzhi->ViewValue;
			$ViewAttrs = &$this->star_ranzhi->ViewAttrs;
			$CellAttrs = &$this->star_ranzhi->CellAttrs;
			$HrefValue = &$this->star_ranzhi->HrefValue;
			$LinkAttrs = &$this->star_ranzhi->LinkAttrs;
			$this->Cell_Rendered($this->star_ranzhi, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_shushi
			$CurrentValue = $this->star_shushi->CurrentValue;
			$ViewValue = &$this->star_shushi->ViewValue;
			$ViewAttrs = &$this->star_shushi->ViewAttrs;
			$CellAttrs = &$this->star_shushi->CellAttrs;
			$HrefValue = &$this->star_shushi->HrefValue;
			$LinkAttrs = &$this->star_shushi->LinkAttrs;
			$this->Cell_Rendered($this->star_shushi, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_lachen
			$CurrentValue = $this->star_lachen->CurrentValue;
			$ViewValue = &$this->star_lachen->ViewValue;
			$ViewAttrs = &$this->star_lachen->ViewAttrs;
			$CellAttrs = &$this->star_lachen->CellAttrs;
			$HrefValue = &$this->star_lachen->HrefValue;
			$LinkAttrs = &$this->star_lachen->LinkAttrs;
			$this->Cell_Rendered($this->star_lachen, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_pingheng
			$CurrentValue = $this->star_pingheng->CurrentValue;
			$ViewValue = &$this->star_pingheng->ViewValue;
			$ViewAttrs = &$this->star_pingheng->ViewAttrs;
			$CellAttrs = &$this->star_pingheng->CellAttrs;
			$HrefValue = &$this->star_pingheng->HrefValue;
			$LinkAttrs = &$this->star_pingheng->LinkAttrs;
			$this->Cell_Rendered($this->star_pingheng, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_rouren
			$CurrentValue = $this->star_rouren->CurrentValue;
			$ViewValue = &$this->star_rouren->ViewValue;
			$ViewAttrs = &$this->star_rouren->ViewAttrs;
			$CellAttrs = &$this->star_rouren->CellAttrs;
			$HrefValue = &$this->star_rouren->HrefValue;
			$LinkAttrs = &$this->star_rouren->LinkAttrs;
			$this->Cell_Rendered($this->star_rouren, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// star_chuanlian
			$CurrentValue = $this->star_chuanlian->CurrentValue;
			$ViewValue = &$this->star_chuanlian->ViewValue;
			$ViewAttrs = &$this->star_chuanlian->ViewAttrs;
			$CellAttrs = &$this->star_chuanlian->CellAttrs;
			$HrefValue = &$this->star_chuanlian->HrefValue;
			$LinkAttrs = &$this->star_chuanlian->LinkAttrs;
			$this->Cell_Rendered($this->star_chuanlian, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->idclass_package_design->Visible) $this->DtlFldCount += 1;
		if ($this->package_id->Visible) $this->DtlFldCount += 1;
		if ($this->package_name->Visible) $this->DtlFldCount += 1;
		if ($this->package_description->Visible) $this->DtlFldCount += 1;
		if ($this->package_course_count->Visible) $this->DtlFldCount += 1;
		if ($this->package_price->Visible) $this->DtlFldCount += 1;
		if ($this->weiddianlink->Visible) $this->DtlFldCount += 1;
		if ($this->ref_common->Visible) $this->DtlFldCount += 1;
		if ($this->star_nandu->Visible) $this->DtlFldCount += 1;
		if ($this->star_liliang->Visible) $this->DtlFldCount += 1;
		if ($this->star_shiyong->Visible) $this->DtlFldCount += 1;
		if ($this->star_suxing->Visible) $this->DtlFldCount += 1;
		if ($this->star_liliao->Visible) $this->DtlFldCount += 1;
		if ($this->star_quwei->Visible) $this->DtlFldCount += 1;
		if ($this->star_ranzhi->Visible) $this->DtlFldCount += 1;
		if ($this->star_shushi->Visible) $this->DtlFldCount += 1;
		if ($this->star_lachen->Visible) $this->DtlFldCount += 1;
		if ($this->star_pingheng->Visible) $this->DtlFldCount += 1;
		if ($this->star_rouren->Visible) $this->DtlFldCount += 1;
		if ($this->star_chuanlian->Visible) $this->DtlFldCount += 1;
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

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		return $sWrk;
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
				$this->idclass_package_design->setSort("");
				$this->package_id->setSort("");
				$this->package_name->setSort("");
				$this->package_description->setSort("");
				$this->package_course_count->setSort("");
				$this->package_price->setSort("");
				$this->weiddianlink->setSort("");
				$this->ref_common->setSort("");
				$this->star_nandu->setSort("");
				$this->star_liliang->setSort("");
				$this->star_shiyong->setSort("");
				$this->star_suxing->setSort("");
				$this->star_liliao->setSort("");
				$this->star_quwei->setSort("");
				$this->star_ranzhi->setSort("");
				$this->star_shushi->setSort("");
				$this->star_lachen->setSort("");
				$this->star_pingheng->setSort("");
				$this->star_rouren->setSort("");
				$this->star_chuanlian->setSort("");
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
if (!isset($package_design_rpt)) $package_design_rpt = new crpackage_design_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$package_design_rpt;

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
var package_design_rpt = new ewr_Page("package_design_rpt");

// Page properties
package_design_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = package_design_rpt.PageID;

// Extend page with Chart_Rendering function
package_design_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
package_design_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php if (!$Page->DrillDown) { ?>
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
<?php if ($Page->idclass_package_design->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="idclass_package_design"><div class="package_design_idclass_package_design"><span class="ewTableHeaderCaption"><?php echo $Page->idclass_package_design->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="idclass_package_design">
<?php if ($Page->SortUrl($Page->idclass_package_design) == "") { ?>
		<div class="ewTableHeaderBtn package_design_idclass_package_design">
			<span class="ewTableHeaderCaption"><?php echo $Page->idclass_package_design->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_idclass_package_design" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->idclass_package_design) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->idclass_package_design->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->idclass_package_design->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->idclass_package_design->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->package_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="package_id"><div class="package_design_package_id"><span class="ewTableHeaderCaption"><?php echo $Page->package_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="package_id">
<?php if ($Page->SortUrl($Page->package_id) == "") { ?>
		<div class="ewTableHeaderBtn package_design_package_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_package_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->package_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->package_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->package_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->package_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="package_name"><div class="package_design_package_name"><span class="ewTableHeaderCaption"><?php echo $Page->package_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="package_name">
<?php if ($Page->SortUrl($Page->package_name) == "") { ?>
		<div class="ewTableHeaderBtn package_design_package_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_package_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->package_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->package_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->package_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->package_description->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="package_description"><div class="package_design_package_description"><span class="ewTableHeaderCaption"><?php echo $Page->package_description->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="package_description">
<?php if ($Page->SortUrl($Page->package_description) == "") { ?>
		<div class="ewTableHeaderBtn package_design_package_description">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_description->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_package_description" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->package_description) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_description->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->package_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->package_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->package_course_count->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="package_course_count"><div class="package_design_package_course_count"><span class="ewTableHeaderCaption"><?php echo $Page->package_course_count->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="package_course_count">
<?php if ($Page->SortUrl($Page->package_course_count) == "") { ?>
		<div class="ewTableHeaderBtn package_design_package_course_count">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_course_count->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_package_course_count" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->package_course_count) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_course_count->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->package_course_count->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->package_course_count->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->package_price->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="package_price"><div class="package_design_package_price"><span class="ewTableHeaderCaption"><?php echo $Page->package_price->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="package_price">
<?php if ($Page->SortUrl($Page->package_price) == "") { ?>
		<div class="ewTableHeaderBtn package_design_package_price">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_price->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_package_price" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->package_price) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->package_price->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->package_price->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->package_price->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->weiddianlink->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="weiddianlink"><div class="package_design_weiddianlink"><span class="ewTableHeaderCaption"><?php echo $Page->weiddianlink->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="weiddianlink">
<?php if ($Page->SortUrl($Page->weiddianlink) == "") { ?>
		<div class="ewTableHeaderBtn package_design_weiddianlink">
			<span class="ewTableHeaderCaption"><?php echo $Page->weiddianlink->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_weiddianlink" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->weiddianlink) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->weiddianlink->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->weiddianlink->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->weiddianlink->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ref_common->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ref_common"><div class="package_design_ref_common"><span class="ewTableHeaderCaption"><?php echo $Page->ref_common->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="ref_common">
<?php if ($Page->SortUrl($Page->ref_common) == "") { ?>
		<div class="ewTableHeaderBtn package_design_ref_common">
			<span class="ewTableHeaderCaption"><?php echo $Page->ref_common->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_ref_common" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ref_common) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ref_common->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ref_common->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ref_common->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_nandu->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_nandu"><div class="package_design_star_nandu"><span class="ewTableHeaderCaption"><?php echo $Page->star_nandu->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_nandu">
<?php if ($Page->SortUrl($Page->star_nandu) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_nandu">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_nandu->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_nandu" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_nandu) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_nandu->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_nandu->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_nandu->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_liliang->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_liliang"><div class="package_design_star_liliang"><span class="ewTableHeaderCaption"><?php echo $Page->star_liliang->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_liliang">
<?php if ($Page->SortUrl($Page->star_liliang) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_liliang">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_liliang->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_liliang" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_liliang) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_liliang->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_liliang->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_liliang->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_shiyong->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_shiyong"><div class="package_design_star_shiyong"><span class="ewTableHeaderCaption"><?php echo $Page->star_shiyong->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_shiyong">
<?php if ($Page->SortUrl($Page->star_shiyong) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_shiyong">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_shiyong->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_shiyong" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_shiyong) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_shiyong->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_shiyong->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_shiyong->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_suxing->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_suxing"><div class="package_design_star_suxing"><span class="ewTableHeaderCaption"><?php echo $Page->star_suxing->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_suxing">
<?php if ($Page->SortUrl($Page->star_suxing) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_suxing">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_suxing->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_suxing" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_suxing) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_suxing->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_suxing->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_suxing->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_liliao->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_liliao"><div class="package_design_star_liliao"><span class="ewTableHeaderCaption"><?php echo $Page->star_liliao->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_liliao">
<?php if ($Page->SortUrl($Page->star_liliao) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_liliao">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_liliao->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_liliao" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_liliao) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_liliao->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_liliao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_liliao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_quwei->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_quwei"><div class="package_design_star_quwei"><span class="ewTableHeaderCaption"><?php echo $Page->star_quwei->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_quwei">
<?php if ($Page->SortUrl($Page->star_quwei) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_quwei">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_quwei->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_quwei" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_quwei) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_quwei->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_quwei->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_quwei->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_ranzhi->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_ranzhi"><div class="package_design_star_ranzhi"><span class="ewTableHeaderCaption"><?php echo $Page->star_ranzhi->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_ranzhi">
<?php if ($Page->SortUrl($Page->star_ranzhi) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_ranzhi">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_ranzhi->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_ranzhi" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_ranzhi) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_ranzhi->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_ranzhi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_ranzhi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_shushi->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_shushi"><div class="package_design_star_shushi"><span class="ewTableHeaderCaption"><?php echo $Page->star_shushi->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_shushi">
<?php if ($Page->SortUrl($Page->star_shushi) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_shushi">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_shushi->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_shushi" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_shushi) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_shushi->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_shushi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_shushi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_lachen->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_lachen"><div class="package_design_star_lachen"><span class="ewTableHeaderCaption"><?php echo $Page->star_lachen->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_lachen">
<?php if ($Page->SortUrl($Page->star_lachen) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_lachen">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_lachen->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_lachen" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_lachen) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_lachen->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_lachen->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_lachen->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_pingheng->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_pingheng"><div class="package_design_star_pingheng"><span class="ewTableHeaderCaption"><?php echo $Page->star_pingheng->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_pingheng">
<?php if ($Page->SortUrl($Page->star_pingheng) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_pingheng">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_pingheng->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_pingheng" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_pingheng) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_pingheng->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_pingheng->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_pingheng->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_rouren->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_rouren"><div class="package_design_star_rouren"><span class="ewTableHeaderCaption"><?php echo $Page->star_rouren->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_rouren">
<?php if ($Page->SortUrl($Page->star_rouren) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_rouren">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_rouren->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_rouren" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_rouren) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_rouren->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_rouren->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_rouren->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->star_chuanlian->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="star_chuanlian"><div class="package_design_star_chuanlian"><span class="ewTableHeaderCaption"><?php echo $Page->star_chuanlian->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="star_chuanlian">
<?php if ($Page->SortUrl($Page->star_chuanlian) == "") { ?>
		<div class="ewTableHeaderBtn package_design_star_chuanlian">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_chuanlian->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer package_design_star_chuanlian" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->star_chuanlian) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->star_chuanlian->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->star_chuanlian->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->star_chuanlian->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->idclass_package_design->Visible) { ?>
		<td data-field="idclass_package_design"<?php echo $Page->idclass_package_design->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_idclass_package_design"<?php echo $Page->idclass_package_design->ViewAttributes() ?>><?php echo $Page->idclass_package_design->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->package_id->Visible) { ?>
		<td data-field="package_id"<?php echo $Page->package_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_package_id"<?php echo $Page->package_id->ViewAttributes() ?>><?php echo $Page->package_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->package_name->Visible) { ?>
		<td data-field="package_name"<?php echo $Page->package_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_package_name"<?php echo $Page->package_name->ViewAttributes() ?>><?php echo $Page->package_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->package_description->Visible) { ?>
		<td data-field="package_description"<?php echo $Page->package_description->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_package_description"<?php echo $Page->package_description->ViewAttributes() ?>><?php echo $Page->package_description->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->package_course_count->Visible) { ?>
		<td data-field="package_course_count"<?php echo $Page->package_course_count->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_package_course_count"<?php echo $Page->package_course_count->ViewAttributes() ?>><?php echo $Page->package_course_count->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->package_price->Visible) { ?>
		<td data-field="package_price"<?php echo $Page->package_price->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_package_price"<?php echo $Page->package_price->ViewAttributes() ?>><?php echo $Page->package_price->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->weiddianlink->Visible) { ?>
		<td data-field="weiddianlink"<?php echo $Page->weiddianlink->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_weiddianlink"<?php echo $Page->weiddianlink->ViewAttributes() ?>><?php echo $Page->weiddianlink->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->ref_common->Visible) { ?>
		<td data-field="ref_common"<?php echo $Page->ref_common->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_ref_common"<?php echo $Page->ref_common->ViewAttributes() ?>><?php echo $Page->ref_common->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_nandu->Visible) { ?>
		<td data-field="star_nandu"<?php echo $Page->star_nandu->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_nandu"<?php echo $Page->star_nandu->ViewAttributes() ?>><?php echo $Page->star_nandu->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_liliang->Visible) { ?>
		<td data-field="star_liliang"<?php echo $Page->star_liliang->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_liliang"<?php echo $Page->star_liliang->ViewAttributes() ?>><?php echo $Page->star_liliang->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_shiyong->Visible) { ?>
		<td data-field="star_shiyong"<?php echo $Page->star_shiyong->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_shiyong"<?php echo $Page->star_shiyong->ViewAttributes() ?>><?php echo $Page->star_shiyong->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_suxing->Visible) { ?>
		<td data-field="star_suxing"<?php echo $Page->star_suxing->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_suxing"<?php echo $Page->star_suxing->ViewAttributes() ?>><?php echo $Page->star_suxing->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_liliao->Visible) { ?>
		<td data-field="star_liliao"<?php echo $Page->star_liliao->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_liliao"<?php echo $Page->star_liliao->ViewAttributes() ?>><?php echo $Page->star_liliao->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_quwei->Visible) { ?>
		<td data-field="star_quwei"<?php echo $Page->star_quwei->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_quwei"<?php echo $Page->star_quwei->ViewAttributes() ?>><?php echo $Page->star_quwei->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_ranzhi->Visible) { ?>
		<td data-field="star_ranzhi"<?php echo $Page->star_ranzhi->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_ranzhi"<?php echo $Page->star_ranzhi->ViewAttributes() ?>><?php echo $Page->star_ranzhi->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_shushi->Visible) { ?>
		<td data-field="star_shushi"<?php echo $Page->star_shushi->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_shushi"<?php echo $Page->star_shushi->ViewAttributes() ?>><?php echo $Page->star_shushi->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_lachen->Visible) { ?>
		<td data-field="star_lachen"<?php echo $Page->star_lachen->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_lachen"<?php echo $Page->star_lachen->ViewAttributes() ?>><?php echo $Page->star_lachen->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_pingheng->Visible) { ?>
		<td data-field="star_pingheng"<?php echo $Page->star_pingheng->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_pingheng"<?php echo $Page->star_pingheng->ViewAttributes() ?>><?php echo $Page->star_pingheng->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_rouren->Visible) { ?>
		<td data-field="star_rouren"<?php echo $Page->star_rouren->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_rouren"<?php echo $Page->star_rouren->ViewAttributes() ?>><?php echo $Page->star_rouren->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->star_chuanlian->Visible) { ?>
		<td data-field="star_chuanlian"<?php echo $Page->star_chuanlian->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_package_design_star_chuanlian"<?php echo $Page->star_chuanlian->ViewAttributes() ?>><?php echo $Page->star_chuanlian->ListViewValue() ?></span></td>
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
	</tfoot>
<?php } elseif (!$Page->ShowHeader && FALSE) { // No header displayed ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || FALSE) { // Show footer ?>
</table>
</div>
<?php if (!($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "package_designrptpager.php" ?>
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
