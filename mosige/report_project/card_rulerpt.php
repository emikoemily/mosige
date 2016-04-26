<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "card_rulerptinfo.php" ?>
<?php

//
// Page class
//

$card_rule_rpt = NULL; // Initialize page object first

class crcard_rule_rpt extends crcard_rule {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'card_rule_rpt';

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

		// Table object (card_rule)
		if (!isset($GLOBALS["card_rule"])) {
			$GLOBALS["card_rule"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["card_rule"];
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
			define("EWR_TABLE_NAME", 'card_rule', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fcard_rulerpt";
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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_card_rule\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_card_rule',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fcard_rulerpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fcard_rulerpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fcard_rulerpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 13;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

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
				$this->FirstRowData['rule_id'] = ewr_Conv($rs->fields('rule_id'),3);
				$this->FirstRowData['rule_name'] = ewr_Conv($rs->fields('rule_name'),200);
				$this->FirstRowData['level_actual'] = ewr_Conv($rs->fields('level_actual'),200);
				$this->FirstRowData['rule_alias'] = ewr_Conv($rs->fields('rule_alias'),200);
				$this->FirstRowData['rule_displayname'] = ewr_Conv($rs->fields('rule_displayname'),200);
				$this->FirstRowData['rule_days'] = ewr_Conv($rs->fields('rule_days'),3);
				$this->FirstRowData['rule_startdate'] = ewr_Conv($rs->fields('rule_startdate'),135);
				$this->FirstRowData['rule_enddate'] = ewr_Conv($rs->fields('rule_enddate'),135);
				$this->FirstRowData['rule_maxcount'] = ewr_Conv($rs->fields('rule_maxcount'),3);
				$this->FirstRowData['has_kongzhong'] = ewr_Conv($rs->fields('has_kongzhong'),16);
				$this->FirstRowData['has_ertong'] = ewr_Conv($rs->fields('has_ertong'),16);
				$this->FirstRowData['time_rule'] = ewr_Conv($rs->fields('time_rule'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->rule_id->setDbValue($rs->fields('rule_id'));
			$this->rule_name->setDbValue($rs->fields('rule_name'));
			$this->level_actual->setDbValue($rs->fields('level_actual'));
			$this->rule_alias->setDbValue($rs->fields('rule_alias'));
			$this->rule_displayname->setDbValue($rs->fields('rule_displayname'));
			$this->rule_days->setDbValue($rs->fields('rule_days'));
			$this->rule_startdate->setDbValue($rs->fields('rule_startdate'));
			$this->rule_enddate->setDbValue($rs->fields('rule_enddate'));
			$this->rule_description->setDbValue($rs->fields('rule_description'));
			$this->rule_maxcount->setDbValue($rs->fields('rule_maxcount'));
			$this->has_kongzhong->setDbValue($rs->fields('has_kongzhong'));
			$this->has_ertong->setDbValue($rs->fields('has_ertong'));
			$this->time_rule->setDbValue($rs->fields('time_rule'));
			$this->Val[1] = $this->rule_id->CurrentValue;
			$this->Val[2] = $this->rule_name->CurrentValue;
			$this->Val[3] = $this->level_actual->CurrentValue;
			$this->Val[4] = $this->rule_alias->CurrentValue;
			$this->Val[5] = $this->rule_displayname->CurrentValue;
			$this->Val[6] = $this->rule_days->CurrentValue;
			$this->Val[7] = $this->rule_startdate->CurrentValue;
			$this->Val[8] = $this->rule_enddate->CurrentValue;
			$this->Val[9] = $this->rule_maxcount->CurrentValue;
			$this->Val[10] = $this->has_kongzhong->CurrentValue;
			$this->Val[11] = $this->has_ertong->CurrentValue;
			$this->Val[12] = $this->time_rule->CurrentValue;
		} else {
			$this->rule_id->setDbValue("");
			$this->rule_name->setDbValue("");
			$this->level_actual->setDbValue("");
			$this->rule_alias->setDbValue("");
			$this->rule_displayname->setDbValue("");
			$this->rule_days->setDbValue("");
			$this->rule_startdate->setDbValue("");
			$this->rule_enddate->setDbValue("");
			$this->rule_description->setDbValue("");
			$this->rule_maxcount->setDbValue("");
			$this->has_kongzhong->setDbValue("");
			$this->has_ertong->setDbValue("");
			$this->time_rule->setDbValue("");
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

			// rule_id
			$this->rule_id->HrefValue = "";

			// rule_name
			$this->rule_name->HrefValue = "";

			// level_actual
			$this->level_actual->HrefValue = "";

			// rule_alias
			$this->rule_alias->HrefValue = "";

			// rule_displayname
			$this->rule_displayname->HrefValue = "";

			// rule_days
			$this->rule_days->HrefValue = "";

			// rule_startdate
			$this->rule_startdate->HrefValue = "";

			// rule_enddate
			$this->rule_enddate->HrefValue = "";

			// rule_maxcount
			$this->rule_maxcount->HrefValue = "";

			// has_kongzhong
			$this->has_kongzhong->HrefValue = "";

			// has_ertong
			$this->has_ertong->HrefValue = "";

			// time_rule
			$this->time_rule->HrefValue = "";
		} else {

			// rule_id
			$this->rule_id->ViewValue = $this->rule_id->CurrentValue;
			$this->rule_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_name
			$this->rule_name->ViewValue = $this->rule_name->CurrentValue;
			$this->rule_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// level_actual
			$this->level_actual->ViewValue = $this->level_actual->CurrentValue;
			$this->level_actual->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_alias
			$this->rule_alias->ViewValue = $this->rule_alias->CurrentValue;
			$this->rule_alias->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_displayname
			$this->rule_displayname->ViewValue = $this->rule_displayname->CurrentValue;
			$this->rule_displayname->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_days
			$this->rule_days->ViewValue = $this->rule_days->CurrentValue;
			$this->rule_days->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_startdate
			$this->rule_startdate->ViewValue = $this->rule_startdate->CurrentValue;
			$this->rule_startdate->ViewValue = ewr_FormatDateTime($this->rule_startdate->ViewValue, 5);
			$this->rule_startdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_enddate
			$this->rule_enddate->ViewValue = $this->rule_enddate->CurrentValue;
			$this->rule_enddate->ViewValue = ewr_FormatDateTime($this->rule_enddate->ViewValue, 5);
			$this->rule_enddate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_maxcount
			$this->rule_maxcount->ViewValue = $this->rule_maxcount->CurrentValue;
			$this->rule_maxcount->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// has_kongzhong
			$this->has_kongzhong->ViewValue = $this->has_kongzhong->CurrentValue;
			$this->has_kongzhong->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// has_ertong
			$this->has_ertong->ViewValue = $this->has_ertong->CurrentValue;
			$this->has_ertong->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// time_rule
			$this->time_rule->ViewValue = $this->time_rule->CurrentValue;
			$this->time_rule->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rule_id
			$this->rule_id->HrefValue = "";

			// rule_name
			$this->rule_name->HrefValue = "";

			// level_actual
			$this->level_actual->HrefValue = "";

			// rule_alias
			$this->rule_alias->HrefValue = "";

			// rule_displayname
			$this->rule_displayname->HrefValue = "";

			// rule_days
			$this->rule_days->HrefValue = "";

			// rule_startdate
			$this->rule_startdate->HrefValue = "";

			// rule_enddate
			$this->rule_enddate->HrefValue = "";

			// rule_maxcount
			$this->rule_maxcount->HrefValue = "";

			// has_kongzhong
			$this->has_kongzhong->HrefValue = "";

			// has_ertong
			$this->has_ertong->HrefValue = "";

			// time_rule
			$this->time_rule->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// rule_id
			$CurrentValue = $this->rule_id->CurrentValue;
			$ViewValue = &$this->rule_id->ViewValue;
			$ViewAttrs = &$this->rule_id->ViewAttrs;
			$CellAttrs = &$this->rule_id->CellAttrs;
			$HrefValue = &$this->rule_id->HrefValue;
			$LinkAttrs = &$this->rule_id->LinkAttrs;
			$this->Cell_Rendered($this->rule_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_name
			$CurrentValue = $this->rule_name->CurrentValue;
			$ViewValue = &$this->rule_name->ViewValue;
			$ViewAttrs = &$this->rule_name->ViewAttrs;
			$CellAttrs = &$this->rule_name->CellAttrs;
			$HrefValue = &$this->rule_name->HrefValue;
			$LinkAttrs = &$this->rule_name->LinkAttrs;
			$this->Cell_Rendered($this->rule_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// level_actual
			$CurrentValue = $this->level_actual->CurrentValue;
			$ViewValue = &$this->level_actual->ViewValue;
			$ViewAttrs = &$this->level_actual->ViewAttrs;
			$CellAttrs = &$this->level_actual->CellAttrs;
			$HrefValue = &$this->level_actual->HrefValue;
			$LinkAttrs = &$this->level_actual->LinkAttrs;
			$this->Cell_Rendered($this->level_actual, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_alias
			$CurrentValue = $this->rule_alias->CurrentValue;
			$ViewValue = &$this->rule_alias->ViewValue;
			$ViewAttrs = &$this->rule_alias->ViewAttrs;
			$CellAttrs = &$this->rule_alias->CellAttrs;
			$HrefValue = &$this->rule_alias->HrefValue;
			$LinkAttrs = &$this->rule_alias->LinkAttrs;
			$this->Cell_Rendered($this->rule_alias, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_displayname
			$CurrentValue = $this->rule_displayname->CurrentValue;
			$ViewValue = &$this->rule_displayname->ViewValue;
			$ViewAttrs = &$this->rule_displayname->ViewAttrs;
			$CellAttrs = &$this->rule_displayname->CellAttrs;
			$HrefValue = &$this->rule_displayname->HrefValue;
			$LinkAttrs = &$this->rule_displayname->LinkAttrs;
			$this->Cell_Rendered($this->rule_displayname, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_days
			$CurrentValue = $this->rule_days->CurrentValue;
			$ViewValue = &$this->rule_days->ViewValue;
			$ViewAttrs = &$this->rule_days->ViewAttrs;
			$CellAttrs = &$this->rule_days->CellAttrs;
			$HrefValue = &$this->rule_days->HrefValue;
			$LinkAttrs = &$this->rule_days->LinkAttrs;
			$this->Cell_Rendered($this->rule_days, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_startdate
			$CurrentValue = $this->rule_startdate->CurrentValue;
			$ViewValue = &$this->rule_startdate->ViewValue;
			$ViewAttrs = &$this->rule_startdate->ViewAttrs;
			$CellAttrs = &$this->rule_startdate->CellAttrs;
			$HrefValue = &$this->rule_startdate->HrefValue;
			$LinkAttrs = &$this->rule_startdate->LinkAttrs;
			$this->Cell_Rendered($this->rule_startdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_enddate
			$CurrentValue = $this->rule_enddate->CurrentValue;
			$ViewValue = &$this->rule_enddate->ViewValue;
			$ViewAttrs = &$this->rule_enddate->ViewAttrs;
			$CellAttrs = &$this->rule_enddate->CellAttrs;
			$HrefValue = &$this->rule_enddate->HrefValue;
			$LinkAttrs = &$this->rule_enddate->LinkAttrs;
			$this->Cell_Rendered($this->rule_enddate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rule_maxcount
			$CurrentValue = $this->rule_maxcount->CurrentValue;
			$ViewValue = &$this->rule_maxcount->ViewValue;
			$ViewAttrs = &$this->rule_maxcount->ViewAttrs;
			$CellAttrs = &$this->rule_maxcount->CellAttrs;
			$HrefValue = &$this->rule_maxcount->HrefValue;
			$LinkAttrs = &$this->rule_maxcount->LinkAttrs;
			$this->Cell_Rendered($this->rule_maxcount, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// has_kongzhong
			$CurrentValue = $this->has_kongzhong->CurrentValue;
			$ViewValue = &$this->has_kongzhong->ViewValue;
			$ViewAttrs = &$this->has_kongzhong->ViewAttrs;
			$CellAttrs = &$this->has_kongzhong->CellAttrs;
			$HrefValue = &$this->has_kongzhong->HrefValue;
			$LinkAttrs = &$this->has_kongzhong->LinkAttrs;
			$this->Cell_Rendered($this->has_kongzhong, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// has_ertong
			$CurrentValue = $this->has_ertong->CurrentValue;
			$ViewValue = &$this->has_ertong->ViewValue;
			$ViewAttrs = &$this->has_ertong->ViewAttrs;
			$CellAttrs = &$this->has_ertong->CellAttrs;
			$HrefValue = &$this->has_ertong->HrefValue;
			$LinkAttrs = &$this->has_ertong->LinkAttrs;
			$this->Cell_Rendered($this->has_ertong, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// time_rule
			$CurrentValue = $this->time_rule->CurrentValue;
			$ViewValue = &$this->time_rule->ViewValue;
			$ViewAttrs = &$this->time_rule->ViewAttrs;
			$CellAttrs = &$this->time_rule->CellAttrs;
			$HrefValue = &$this->time_rule->HrefValue;
			$LinkAttrs = &$this->time_rule->LinkAttrs;
			$this->Cell_Rendered($this->time_rule, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->rule_id->Visible) $this->DtlFldCount += 1;
		if ($this->rule_name->Visible) $this->DtlFldCount += 1;
		if ($this->level_actual->Visible) $this->DtlFldCount += 1;
		if ($this->rule_alias->Visible) $this->DtlFldCount += 1;
		if ($this->rule_displayname->Visible) $this->DtlFldCount += 1;
		if ($this->rule_days->Visible) $this->DtlFldCount += 1;
		if ($this->rule_startdate->Visible) $this->DtlFldCount += 1;
		if ($this->rule_enddate->Visible) $this->DtlFldCount += 1;
		if ($this->rule_maxcount->Visible) $this->DtlFldCount += 1;
		if ($this->has_kongzhong->Visible) $this->DtlFldCount += 1;
		if ($this->has_ertong->Visible) $this->DtlFldCount += 1;
		if ($this->time_rule->Visible) $this->DtlFldCount += 1;
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
				$this->rule_id->setSort("");
				$this->rule_name->setSort("");
				$this->level_actual->setSort("");
				$this->rule_alias->setSort("");
				$this->rule_displayname->setSort("");
				$this->rule_days->setSort("");
				$this->rule_startdate->setSort("");
				$this->rule_enddate->setSort("");
				$this->rule_maxcount->setSort("");
				$this->has_kongzhong->setSort("");
				$this->has_ertong->setSort("");
				$this->time_rule->setSort("");
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
if (!isset($card_rule_rpt)) $card_rule_rpt = new crcard_rule_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$card_rule_rpt;

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
var card_rule_rpt = new ewr_Page("card_rule_rpt");

// Page properties
card_rule_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = card_rule_rpt.PageID;

// Extend page with Chart_Rendering function
card_rule_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
card_rule_rpt.Chart_Rendered = 
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
<?php if ($Page->rule_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_id"><div class="card_rule_rule_id"><span class="ewTableHeaderCaption"><?php echo $Page->rule_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_id">
<?php if ($Page->SortUrl($Page->rule_id) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_name"><div class="card_rule_rule_name"><span class="ewTableHeaderCaption"><?php echo $Page->rule_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_name">
<?php if ($Page->SortUrl($Page->rule_name) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->level_actual->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="level_actual"><div class="card_rule_level_actual"><span class="ewTableHeaderCaption"><?php echo $Page->level_actual->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="level_actual">
<?php if ($Page->SortUrl($Page->level_actual) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_level_actual">
			<span class="ewTableHeaderCaption"><?php echo $Page->level_actual->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_level_actual" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->level_actual) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->level_actual->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->level_actual->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->level_actual->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_alias->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_alias"><div class="card_rule_rule_alias"><span class="ewTableHeaderCaption"><?php echo $Page->rule_alias->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_alias">
<?php if ($Page->SortUrl($Page->rule_alias) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_alias">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_alias->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_alias" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_alias) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_alias->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_alias->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_alias->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_displayname->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_displayname"><div class="card_rule_rule_displayname"><span class="ewTableHeaderCaption"><?php echo $Page->rule_displayname->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_displayname">
<?php if ($Page->SortUrl($Page->rule_displayname) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_displayname">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_displayname->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_displayname" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_displayname) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_displayname->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_displayname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_displayname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_days->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_days"><div class="card_rule_rule_days"><span class="ewTableHeaderCaption"><?php echo $Page->rule_days->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_days">
<?php if ($Page->SortUrl($Page->rule_days) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_days">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_days->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_days" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_days) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_days->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_days->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_days->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_startdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_startdate"><div class="card_rule_rule_startdate"><span class="ewTableHeaderCaption"><?php echo $Page->rule_startdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_startdate">
<?php if ($Page->SortUrl($Page->rule_startdate) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_startdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_startdate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_startdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_startdate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_startdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_startdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_startdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_enddate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_enddate"><div class="card_rule_rule_enddate"><span class="ewTableHeaderCaption"><?php echo $Page->rule_enddate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_enddate">
<?php if ($Page->SortUrl($Page->rule_enddate) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_enddate">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_enddate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_enddate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_enddate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_enddate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_enddate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_enddate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rule_maxcount->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rule_maxcount"><div class="card_rule_rule_maxcount"><span class="ewTableHeaderCaption"><?php echo $Page->rule_maxcount->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rule_maxcount">
<?php if ($Page->SortUrl($Page->rule_maxcount) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_rule_maxcount">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_maxcount->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_rule_maxcount" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rule_maxcount) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rule_maxcount->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rule_maxcount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rule_maxcount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->has_kongzhong->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="has_kongzhong"><div class="card_rule_has_kongzhong"><span class="ewTableHeaderCaption"><?php echo $Page->has_kongzhong->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="has_kongzhong">
<?php if ($Page->SortUrl($Page->has_kongzhong) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_has_kongzhong">
			<span class="ewTableHeaderCaption"><?php echo $Page->has_kongzhong->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_has_kongzhong" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->has_kongzhong) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->has_kongzhong->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->has_kongzhong->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->has_kongzhong->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->has_ertong->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="has_ertong"><div class="card_rule_has_ertong"><span class="ewTableHeaderCaption"><?php echo $Page->has_ertong->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="has_ertong">
<?php if ($Page->SortUrl($Page->has_ertong) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_has_ertong">
			<span class="ewTableHeaderCaption"><?php echo $Page->has_ertong->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_has_ertong" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->has_ertong) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->has_ertong->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->has_ertong->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->has_ertong->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->time_rule->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="time_rule"><div class="card_rule_time_rule"><span class="ewTableHeaderCaption"><?php echo $Page->time_rule->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="time_rule">
<?php if ($Page->SortUrl($Page->time_rule) == "") { ?>
		<div class="ewTableHeaderBtn card_rule_time_rule">
			<span class="ewTableHeaderCaption"><?php echo $Page->time_rule->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer card_rule_time_rule" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->time_rule) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->time_rule->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->time_rule->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->time_rule->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->rule_id->Visible) { ?>
		<td data-field="rule_id"<?php echo $Page->rule_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_id"<?php echo $Page->rule_id->ViewAttributes() ?>><?php echo $Page->rule_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_name->Visible) { ?>
		<td data-field="rule_name"<?php echo $Page->rule_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_name"<?php echo $Page->rule_name->ViewAttributes() ?>><?php echo $Page->rule_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->level_actual->Visible) { ?>
		<td data-field="level_actual"<?php echo $Page->level_actual->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_level_actual"<?php echo $Page->level_actual->ViewAttributes() ?>><?php echo $Page->level_actual->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_alias->Visible) { ?>
		<td data-field="rule_alias"<?php echo $Page->rule_alias->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_alias"<?php echo $Page->rule_alias->ViewAttributes() ?>><?php echo $Page->rule_alias->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_displayname->Visible) { ?>
		<td data-field="rule_displayname"<?php echo $Page->rule_displayname->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_displayname"<?php echo $Page->rule_displayname->ViewAttributes() ?>><?php echo $Page->rule_displayname->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_days->Visible) { ?>
		<td data-field="rule_days"<?php echo $Page->rule_days->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_days"<?php echo $Page->rule_days->ViewAttributes() ?>><?php echo $Page->rule_days->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_startdate->Visible) { ?>
		<td data-field="rule_startdate"<?php echo $Page->rule_startdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_startdate"<?php echo $Page->rule_startdate->ViewAttributes() ?>><?php echo $Page->rule_startdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_enddate->Visible) { ?>
		<td data-field="rule_enddate"<?php echo $Page->rule_enddate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_enddate"<?php echo $Page->rule_enddate->ViewAttributes() ?>><?php echo $Page->rule_enddate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rule_maxcount->Visible) { ?>
		<td data-field="rule_maxcount"<?php echo $Page->rule_maxcount->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_rule_maxcount"<?php echo $Page->rule_maxcount->ViewAttributes() ?>><?php echo $Page->rule_maxcount->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->has_kongzhong->Visible) { ?>
		<td data-field="has_kongzhong"<?php echo $Page->has_kongzhong->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_has_kongzhong"<?php echo $Page->has_kongzhong->ViewAttributes() ?>><?php echo $Page->has_kongzhong->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->has_ertong->Visible) { ?>
		<td data-field="has_ertong"<?php echo $Page->has_ertong->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_has_ertong"<?php echo $Page->has_ertong->ViewAttributes() ?>><?php echo $Page->has_ertong->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->time_rule->Visible) { ?>
		<td data-field="time_rule"<?php echo $Page->time_rule->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_card_rule_time_rule"<?php echo $Page->time_rule->ViewAttributes() ?>><?php echo $Page->time_rule->ListViewValue() ?></span></td>
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
<?php include "card_rulerptpager.php" ?>
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
