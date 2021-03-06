<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "copy_userrptinfo.php" ?>
<?php

//
// Page class
//

$copy_user_rpt = NULL; // Initialize page object first

class crcopy_user_rpt extends crcopy_user {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'copy_user_rpt';

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

		// Table object (copy_user)
		if (!isset($GLOBALS["copy_user"])) {
			$GLOBALS["copy_user"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["copy_user"];
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
			define("EWR_TABLE_NAME", 'copy_user', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fcopy_userrpt";
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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_copy_user\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_copy_user',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fcopy_userrpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fcopy_userrpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fcopy_userrpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 23;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

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
				$this->FirstRowData['member_id'] = ewr_Conv($rs->fields('member_id'),3);
				$this->FirstRowData['member_name'] = ewr_Conv($rs->fields('member_name'),200);
				$this->FirstRowData['member_sex'] = ewr_Conv($rs->fields('member_sex'),200);
				$this->FirstRowData['member_cell'] = ewr_Conv($rs->fields('member_cell'),200);
				$this->FirstRowData['member_email'] = ewr_Conv($rs->fields('member_email'),200);
				$this->FirstRowData['member_level'] = ewr_Conv($rs->fields('member_level'),200);
				$this->FirstRowData['member_points'] = ewr_Conv($rs->fields('member_points'),200);
				$this->FirstRowData['member_weixinid'] = ewr_Conv($rs->fields('member_weixinid'),200);
				$this->FirstRowData['member_weixinname'] = ewr_Conv($rs->fields('member_weixinname'),200);
				$this->FirstRowData['member_cardid'] = ewr_Conv($rs->fields('member_cardid'),200);
				$this->FirstRowData['member_password'] = ewr_Conv($rs->fields('member_password'),200);
				$this->FirstRowData['member_enddate'] = ewr_Conv($rs->fields('member_enddate'),135);
				$this->FirstRowData['member_birthday'] = ewr_Conv($rs->fields('member_birthday'),133);
				$this->FirstRowData['member_regtime'] = ewr_Conv($rs->fields('member_regtime'),135);
				$this->FirstRowData['member_startdate'] = ewr_Conv($rs->fields('member_startdate'),135);
				$this->FirstRowData['member_days'] = ewr_Conv($rs->fields('member_days'),3);
				$this->FirstRowData['member_classcount'] = ewr_Conv($rs->fields('member_classcount'),3);
				$this->FirstRowData['member_attendmax'] = ewr_Conv($rs->fields('member_attendmax'),3);
				$this->FirstRowData['member_isleave'] = ewr_Conv($rs->fields('member_isleave'),3);
				$this->FirstRowData['member_leavecount'] = ewr_Conv($rs->fields('member_leavecount'),3);
				$this->FirstRowData['member_leavedays'] = ewr_Conv($rs->fields('member_leavedays'),3);
				$this->FirstRowData['member_intro'] = ewr_Conv($rs->fields('member_intro'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->member_id->setDbValue($rs->fields('member_id'));
			$this->member_name->setDbValue($rs->fields('member_name'));
			$this->member_sex->setDbValue($rs->fields('member_sex'));
			$this->member_cell->setDbValue($rs->fields('member_cell'));
			$this->member_email->setDbValue($rs->fields('member_email'));
			$this->member_level->setDbValue($rs->fields('member_level'));
			$this->member_points->setDbValue($rs->fields('member_points'));
			$this->member_weixinid->setDbValue($rs->fields('member_weixinid'));
			$this->member_weixinname->setDbValue($rs->fields('member_weixinname'));
			$this->member_cardid->setDbValue($rs->fields('member_cardid'));
			$this->member_password->setDbValue($rs->fields('member_password'));
			$this->member_enddate->setDbValue($rs->fields('member_enddate'));
			$this->member_birthday->setDbValue($rs->fields('member_birthday'));
			$this->member_regtime->setDbValue($rs->fields('member_regtime'));
			$this->member_startdate->setDbValue($rs->fields('member_startdate'));
			$this->member_days->setDbValue($rs->fields('member_days'));
			$this->member_classcount->setDbValue($rs->fields('member_classcount'));
			$this->member_attendmax->setDbValue($rs->fields('member_attendmax'));
			$this->member_isleave->setDbValue($rs->fields('member_isleave'));
			$this->member_leavecount->setDbValue($rs->fields('member_leavecount'));
			$this->member_leavedays->setDbValue($rs->fields('member_leavedays'));
			$this->member_intro->setDbValue($rs->fields('member_intro'));
			$this->Val[1] = $this->member_id->CurrentValue;
			$this->Val[2] = $this->member_name->CurrentValue;
			$this->Val[3] = $this->member_sex->CurrentValue;
			$this->Val[4] = $this->member_cell->CurrentValue;
			$this->Val[5] = $this->member_email->CurrentValue;
			$this->Val[6] = $this->member_level->CurrentValue;
			$this->Val[7] = $this->member_points->CurrentValue;
			$this->Val[8] = $this->member_weixinid->CurrentValue;
			$this->Val[9] = $this->member_weixinname->CurrentValue;
			$this->Val[10] = $this->member_cardid->CurrentValue;
			$this->Val[11] = $this->member_password->CurrentValue;
			$this->Val[12] = $this->member_enddate->CurrentValue;
			$this->Val[13] = $this->member_birthday->CurrentValue;
			$this->Val[14] = $this->member_regtime->CurrentValue;
			$this->Val[15] = $this->member_startdate->CurrentValue;
			$this->Val[16] = $this->member_days->CurrentValue;
			$this->Val[17] = $this->member_classcount->CurrentValue;
			$this->Val[18] = $this->member_attendmax->CurrentValue;
			$this->Val[19] = $this->member_isleave->CurrentValue;
			$this->Val[20] = $this->member_leavecount->CurrentValue;
			$this->Val[21] = $this->member_leavedays->CurrentValue;
			$this->Val[22] = $this->member_intro->CurrentValue;
		} else {
			$this->member_id->setDbValue("");
			$this->member_name->setDbValue("");
			$this->member_sex->setDbValue("");
			$this->member_cell->setDbValue("");
			$this->member_email->setDbValue("");
			$this->member_level->setDbValue("");
			$this->member_points->setDbValue("");
			$this->member_weixinid->setDbValue("");
			$this->member_weixinname->setDbValue("");
			$this->member_cardid->setDbValue("");
			$this->member_password->setDbValue("");
			$this->member_enddate->setDbValue("");
			$this->member_birthday->setDbValue("");
			$this->member_regtime->setDbValue("");
			$this->member_startdate->setDbValue("");
			$this->member_days->setDbValue("");
			$this->member_classcount->setDbValue("");
			$this->member_attendmax->setDbValue("");
			$this->member_isleave->setDbValue("");
			$this->member_leavecount->setDbValue("");
			$this->member_leavedays->setDbValue("");
			$this->member_intro->setDbValue("");
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

			// member_id
			$this->member_id->HrefValue = "";

			// member_name
			$this->member_name->HrefValue = "";

			// member_sex
			$this->member_sex->HrefValue = "";

			// member_cell
			$this->member_cell->HrefValue = "";

			// member_email
			$this->member_email->HrefValue = "";

			// member_level
			$this->member_level->HrefValue = "";

			// member_points
			$this->member_points->HrefValue = "";

			// member_weixinid
			$this->member_weixinid->HrefValue = "";

			// member_weixinname
			$this->member_weixinname->HrefValue = "";

			// member_cardid
			$this->member_cardid->HrefValue = "";

			// member_password
			$this->member_password->HrefValue = "";

			// member_enddate
			$this->member_enddate->HrefValue = "";

			// member_birthday
			$this->member_birthday->HrefValue = "";

			// member_regtime
			$this->member_regtime->HrefValue = "";

			// member_startdate
			$this->member_startdate->HrefValue = "";

			// member_days
			$this->member_days->HrefValue = "";

			// member_classcount
			$this->member_classcount->HrefValue = "";

			// member_attendmax
			$this->member_attendmax->HrefValue = "";

			// member_isleave
			$this->member_isleave->HrefValue = "";

			// member_leavecount
			$this->member_leavecount->HrefValue = "";

			// member_leavedays
			$this->member_leavedays->HrefValue = "";

			// member_intro
			$this->member_intro->HrefValue = "";
		} else {

			// member_id
			$this->member_id->ViewValue = $this->member_id->CurrentValue;
			$this->member_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_name
			$this->member_name->ViewValue = $this->member_name->CurrentValue;
			$this->member_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_sex
			$this->member_sex->ViewValue = $this->member_sex->CurrentValue;
			$this->member_sex->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_cell
			$this->member_cell->ViewValue = $this->member_cell->CurrentValue;
			$this->member_cell->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_email
			$this->member_email->ViewValue = $this->member_email->CurrentValue;
			$this->member_email->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_level
			$this->member_level->ViewValue = $this->member_level->CurrentValue;
			$this->member_level->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_points
			$this->member_points->ViewValue = $this->member_points->CurrentValue;
			$this->member_points->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_weixinid
			$this->member_weixinid->ViewValue = $this->member_weixinid->CurrentValue;
			$this->member_weixinid->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_weixinname
			$this->member_weixinname->ViewValue = $this->member_weixinname->CurrentValue;
			$this->member_weixinname->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_cardid
			$this->member_cardid->ViewValue = $this->member_cardid->CurrentValue;
			$this->member_cardid->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_password
			$this->member_password->ViewValue = $this->member_password->CurrentValue;
			$this->member_password->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_enddate
			$this->member_enddate->ViewValue = $this->member_enddate->CurrentValue;
			$this->member_enddate->ViewValue = ewr_FormatDateTime($this->member_enddate->ViewValue, 5);
			$this->member_enddate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_birthday
			$this->member_birthday->ViewValue = $this->member_birthday->CurrentValue;
			$this->member_birthday->ViewValue = ewr_FormatDateTime($this->member_birthday->ViewValue, 5);
			$this->member_birthday->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_regtime
			$this->member_regtime->ViewValue = $this->member_regtime->CurrentValue;
			$this->member_regtime->ViewValue = ewr_FormatDateTime($this->member_regtime->ViewValue, 5);
			$this->member_regtime->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_startdate
			$this->member_startdate->ViewValue = $this->member_startdate->CurrentValue;
			$this->member_startdate->ViewValue = ewr_FormatDateTime($this->member_startdate->ViewValue, 5);
			$this->member_startdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_days
			$this->member_days->ViewValue = $this->member_days->CurrentValue;
			$this->member_days->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_classcount
			$this->member_classcount->ViewValue = $this->member_classcount->CurrentValue;
			$this->member_classcount->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_attendmax
			$this->member_attendmax->ViewValue = $this->member_attendmax->CurrentValue;
			$this->member_attendmax->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_isleave
			$this->member_isleave->ViewValue = $this->member_isleave->CurrentValue;
			$this->member_isleave->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_leavecount
			$this->member_leavecount->ViewValue = $this->member_leavecount->CurrentValue;
			$this->member_leavecount->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_leavedays
			$this->member_leavedays->ViewValue = $this->member_leavedays->CurrentValue;
			$this->member_leavedays->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_intro
			$this->member_intro->ViewValue = $this->member_intro->CurrentValue;
			$this->member_intro->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// member_id
			$this->member_id->HrefValue = "";

			// member_name
			$this->member_name->HrefValue = "";

			// member_sex
			$this->member_sex->HrefValue = "";

			// member_cell
			$this->member_cell->HrefValue = "";

			// member_email
			$this->member_email->HrefValue = "";

			// member_level
			$this->member_level->HrefValue = "";

			// member_points
			$this->member_points->HrefValue = "";

			// member_weixinid
			$this->member_weixinid->HrefValue = "";

			// member_weixinname
			$this->member_weixinname->HrefValue = "";

			// member_cardid
			$this->member_cardid->HrefValue = "";

			// member_password
			$this->member_password->HrefValue = "";

			// member_enddate
			$this->member_enddate->HrefValue = "";

			// member_birthday
			$this->member_birthday->HrefValue = "";

			// member_regtime
			$this->member_regtime->HrefValue = "";

			// member_startdate
			$this->member_startdate->HrefValue = "";

			// member_days
			$this->member_days->HrefValue = "";

			// member_classcount
			$this->member_classcount->HrefValue = "";

			// member_attendmax
			$this->member_attendmax->HrefValue = "";

			// member_isleave
			$this->member_isleave->HrefValue = "";

			// member_leavecount
			$this->member_leavecount->HrefValue = "";

			// member_leavedays
			$this->member_leavedays->HrefValue = "";

			// member_intro
			$this->member_intro->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// member_id
			$CurrentValue = $this->member_id->CurrentValue;
			$ViewValue = &$this->member_id->ViewValue;
			$ViewAttrs = &$this->member_id->ViewAttrs;
			$CellAttrs = &$this->member_id->CellAttrs;
			$HrefValue = &$this->member_id->HrefValue;
			$LinkAttrs = &$this->member_id->LinkAttrs;
			$this->Cell_Rendered($this->member_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_name
			$CurrentValue = $this->member_name->CurrentValue;
			$ViewValue = &$this->member_name->ViewValue;
			$ViewAttrs = &$this->member_name->ViewAttrs;
			$CellAttrs = &$this->member_name->CellAttrs;
			$HrefValue = &$this->member_name->HrefValue;
			$LinkAttrs = &$this->member_name->LinkAttrs;
			$this->Cell_Rendered($this->member_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_sex
			$CurrentValue = $this->member_sex->CurrentValue;
			$ViewValue = &$this->member_sex->ViewValue;
			$ViewAttrs = &$this->member_sex->ViewAttrs;
			$CellAttrs = &$this->member_sex->CellAttrs;
			$HrefValue = &$this->member_sex->HrefValue;
			$LinkAttrs = &$this->member_sex->LinkAttrs;
			$this->Cell_Rendered($this->member_sex, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_cell
			$CurrentValue = $this->member_cell->CurrentValue;
			$ViewValue = &$this->member_cell->ViewValue;
			$ViewAttrs = &$this->member_cell->ViewAttrs;
			$CellAttrs = &$this->member_cell->CellAttrs;
			$HrefValue = &$this->member_cell->HrefValue;
			$LinkAttrs = &$this->member_cell->LinkAttrs;
			$this->Cell_Rendered($this->member_cell, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_email
			$CurrentValue = $this->member_email->CurrentValue;
			$ViewValue = &$this->member_email->ViewValue;
			$ViewAttrs = &$this->member_email->ViewAttrs;
			$CellAttrs = &$this->member_email->CellAttrs;
			$HrefValue = &$this->member_email->HrefValue;
			$LinkAttrs = &$this->member_email->LinkAttrs;
			$this->Cell_Rendered($this->member_email, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_level
			$CurrentValue = $this->member_level->CurrentValue;
			$ViewValue = &$this->member_level->ViewValue;
			$ViewAttrs = &$this->member_level->ViewAttrs;
			$CellAttrs = &$this->member_level->CellAttrs;
			$HrefValue = &$this->member_level->HrefValue;
			$LinkAttrs = &$this->member_level->LinkAttrs;
			$this->Cell_Rendered($this->member_level, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_points
			$CurrentValue = $this->member_points->CurrentValue;
			$ViewValue = &$this->member_points->ViewValue;
			$ViewAttrs = &$this->member_points->ViewAttrs;
			$CellAttrs = &$this->member_points->CellAttrs;
			$HrefValue = &$this->member_points->HrefValue;
			$LinkAttrs = &$this->member_points->LinkAttrs;
			$this->Cell_Rendered($this->member_points, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_weixinid
			$CurrentValue = $this->member_weixinid->CurrentValue;
			$ViewValue = &$this->member_weixinid->ViewValue;
			$ViewAttrs = &$this->member_weixinid->ViewAttrs;
			$CellAttrs = &$this->member_weixinid->CellAttrs;
			$HrefValue = &$this->member_weixinid->HrefValue;
			$LinkAttrs = &$this->member_weixinid->LinkAttrs;
			$this->Cell_Rendered($this->member_weixinid, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_weixinname
			$CurrentValue = $this->member_weixinname->CurrentValue;
			$ViewValue = &$this->member_weixinname->ViewValue;
			$ViewAttrs = &$this->member_weixinname->ViewAttrs;
			$CellAttrs = &$this->member_weixinname->CellAttrs;
			$HrefValue = &$this->member_weixinname->HrefValue;
			$LinkAttrs = &$this->member_weixinname->LinkAttrs;
			$this->Cell_Rendered($this->member_weixinname, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_cardid
			$CurrentValue = $this->member_cardid->CurrentValue;
			$ViewValue = &$this->member_cardid->ViewValue;
			$ViewAttrs = &$this->member_cardid->ViewAttrs;
			$CellAttrs = &$this->member_cardid->CellAttrs;
			$HrefValue = &$this->member_cardid->HrefValue;
			$LinkAttrs = &$this->member_cardid->LinkAttrs;
			$this->Cell_Rendered($this->member_cardid, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_password
			$CurrentValue = $this->member_password->CurrentValue;
			$ViewValue = &$this->member_password->ViewValue;
			$ViewAttrs = &$this->member_password->ViewAttrs;
			$CellAttrs = &$this->member_password->CellAttrs;
			$HrefValue = &$this->member_password->HrefValue;
			$LinkAttrs = &$this->member_password->LinkAttrs;
			$this->Cell_Rendered($this->member_password, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_enddate
			$CurrentValue = $this->member_enddate->CurrentValue;
			$ViewValue = &$this->member_enddate->ViewValue;
			$ViewAttrs = &$this->member_enddate->ViewAttrs;
			$CellAttrs = &$this->member_enddate->CellAttrs;
			$HrefValue = &$this->member_enddate->HrefValue;
			$LinkAttrs = &$this->member_enddate->LinkAttrs;
			$this->Cell_Rendered($this->member_enddate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_birthday
			$CurrentValue = $this->member_birthday->CurrentValue;
			$ViewValue = &$this->member_birthday->ViewValue;
			$ViewAttrs = &$this->member_birthday->ViewAttrs;
			$CellAttrs = &$this->member_birthday->CellAttrs;
			$HrefValue = &$this->member_birthday->HrefValue;
			$LinkAttrs = &$this->member_birthday->LinkAttrs;
			$this->Cell_Rendered($this->member_birthday, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_regtime
			$CurrentValue = $this->member_regtime->CurrentValue;
			$ViewValue = &$this->member_regtime->ViewValue;
			$ViewAttrs = &$this->member_regtime->ViewAttrs;
			$CellAttrs = &$this->member_regtime->CellAttrs;
			$HrefValue = &$this->member_regtime->HrefValue;
			$LinkAttrs = &$this->member_regtime->LinkAttrs;
			$this->Cell_Rendered($this->member_regtime, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_startdate
			$CurrentValue = $this->member_startdate->CurrentValue;
			$ViewValue = &$this->member_startdate->ViewValue;
			$ViewAttrs = &$this->member_startdate->ViewAttrs;
			$CellAttrs = &$this->member_startdate->CellAttrs;
			$HrefValue = &$this->member_startdate->HrefValue;
			$LinkAttrs = &$this->member_startdate->LinkAttrs;
			$this->Cell_Rendered($this->member_startdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_days
			$CurrentValue = $this->member_days->CurrentValue;
			$ViewValue = &$this->member_days->ViewValue;
			$ViewAttrs = &$this->member_days->ViewAttrs;
			$CellAttrs = &$this->member_days->CellAttrs;
			$HrefValue = &$this->member_days->HrefValue;
			$LinkAttrs = &$this->member_days->LinkAttrs;
			$this->Cell_Rendered($this->member_days, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_classcount
			$CurrentValue = $this->member_classcount->CurrentValue;
			$ViewValue = &$this->member_classcount->ViewValue;
			$ViewAttrs = &$this->member_classcount->ViewAttrs;
			$CellAttrs = &$this->member_classcount->CellAttrs;
			$HrefValue = &$this->member_classcount->HrefValue;
			$LinkAttrs = &$this->member_classcount->LinkAttrs;
			$this->Cell_Rendered($this->member_classcount, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_attendmax
			$CurrentValue = $this->member_attendmax->CurrentValue;
			$ViewValue = &$this->member_attendmax->ViewValue;
			$ViewAttrs = &$this->member_attendmax->ViewAttrs;
			$CellAttrs = &$this->member_attendmax->CellAttrs;
			$HrefValue = &$this->member_attendmax->HrefValue;
			$LinkAttrs = &$this->member_attendmax->LinkAttrs;
			$this->Cell_Rendered($this->member_attendmax, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_isleave
			$CurrentValue = $this->member_isleave->CurrentValue;
			$ViewValue = &$this->member_isleave->ViewValue;
			$ViewAttrs = &$this->member_isleave->ViewAttrs;
			$CellAttrs = &$this->member_isleave->CellAttrs;
			$HrefValue = &$this->member_isleave->HrefValue;
			$LinkAttrs = &$this->member_isleave->LinkAttrs;
			$this->Cell_Rendered($this->member_isleave, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_leavecount
			$CurrentValue = $this->member_leavecount->CurrentValue;
			$ViewValue = &$this->member_leavecount->ViewValue;
			$ViewAttrs = &$this->member_leavecount->ViewAttrs;
			$CellAttrs = &$this->member_leavecount->CellAttrs;
			$HrefValue = &$this->member_leavecount->HrefValue;
			$LinkAttrs = &$this->member_leavecount->LinkAttrs;
			$this->Cell_Rendered($this->member_leavecount, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_leavedays
			$CurrentValue = $this->member_leavedays->CurrentValue;
			$ViewValue = &$this->member_leavedays->ViewValue;
			$ViewAttrs = &$this->member_leavedays->ViewAttrs;
			$CellAttrs = &$this->member_leavedays->CellAttrs;
			$HrefValue = &$this->member_leavedays->HrefValue;
			$LinkAttrs = &$this->member_leavedays->LinkAttrs;
			$this->Cell_Rendered($this->member_leavedays, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// member_intro
			$CurrentValue = $this->member_intro->CurrentValue;
			$ViewValue = &$this->member_intro->ViewValue;
			$ViewAttrs = &$this->member_intro->ViewAttrs;
			$CellAttrs = &$this->member_intro->CellAttrs;
			$HrefValue = &$this->member_intro->HrefValue;
			$LinkAttrs = &$this->member_intro->LinkAttrs;
			$this->Cell_Rendered($this->member_intro, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->member_id->Visible) $this->DtlFldCount += 1;
		if ($this->member_name->Visible) $this->DtlFldCount += 1;
		if ($this->member_sex->Visible) $this->DtlFldCount += 1;
		if ($this->member_cell->Visible) $this->DtlFldCount += 1;
		if ($this->member_email->Visible) $this->DtlFldCount += 1;
		if ($this->member_level->Visible) $this->DtlFldCount += 1;
		if ($this->member_points->Visible) $this->DtlFldCount += 1;
		if ($this->member_weixinid->Visible) $this->DtlFldCount += 1;
		if ($this->member_weixinname->Visible) $this->DtlFldCount += 1;
		if ($this->member_cardid->Visible) $this->DtlFldCount += 1;
		if ($this->member_password->Visible) $this->DtlFldCount += 1;
		if ($this->member_enddate->Visible) $this->DtlFldCount += 1;
		if ($this->member_birthday->Visible) $this->DtlFldCount += 1;
		if ($this->member_regtime->Visible) $this->DtlFldCount += 1;
		if ($this->member_startdate->Visible) $this->DtlFldCount += 1;
		if ($this->member_days->Visible) $this->DtlFldCount += 1;
		if ($this->member_classcount->Visible) $this->DtlFldCount += 1;
		if ($this->member_attendmax->Visible) $this->DtlFldCount += 1;
		if ($this->member_isleave->Visible) $this->DtlFldCount += 1;
		if ($this->member_leavecount->Visible) $this->DtlFldCount += 1;
		if ($this->member_leavedays->Visible) $this->DtlFldCount += 1;
		if ($this->member_intro->Visible) $this->DtlFldCount += 1;
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
				$this->member_id->setSort("");
				$this->member_name->setSort("");
				$this->member_sex->setSort("");
				$this->member_cell->setSort("");
				$this->member_email->setSort("");
				$this->member_level->setSort("");
				$this->member_points->setSort("");
				$this->member_weixinid->setSort("");
				$this->member_weixinname->setSort("");
				$this->member_cardid->setSort("");
				$this->member_password->setSort("");
				$this->member_enddate->setSort("");
				$this->member_birthday->setSort("");
				$this->member_regtime->setSort("");
				$this->member_startdate->setSort("");
				$this->member_days->setSort("");
				$this->member_classcount->setSort("");
				$this->member_attendmax->setSort("");
				$this->member_isleave->setSort("");
				$this->member_leavecount->setSort("");
				$this->member_leavedays->setSort("");
				$this->member_intro->setSort("");
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
if (!isset($copy_user_rpt)) $copy_user_rpt = new crcopy_user_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$copy_user_rpt;

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
var copy_user_rpt = new ewr_Page("copy_user_rpt");

// Page properties
copy_user_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = copy_user_rpt.PageID;

// Extend page with Chart_Rendering function
copy_user_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
copy_user_rpt.Chart_Rendered = 
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
<?php if ($Page->member_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_id"><div class="copy_user_member_id"><span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_id">
<?php if ($Page->SortUrl($Page->member_id) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_name"><div class="copy_user_member_name"><span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_name">
<?php if ($Page->SortUrl($Page->member_name) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_sex->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_sex"><div class="copy_user_member_sex"><span class="ewTableHeaderCaption"><?php echo $Page->member_sex->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_sex">
<?php if ($Page->SortUrl($Page->member_sex) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_sex">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_sex->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_sex" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_sex) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_sex->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_sex->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_sex->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_cell->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_cell"><div class="copy_user_member_cell"><span class="ewTableHeaderCaption"><?php echo $Page->member_cell->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_cell">
<?php if ($Page->SortUrl($Page->member_cell) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_cell">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_cell->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_cell" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_cell) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_cell->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_cell->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_cell->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_email->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_email"><div class="copy_user_member_email"><span class="ewTableHeaderCaption"><?php echo $Page->member_email->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_email">
<?php if ($Page->SortUrl($Page->member_email) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_email">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_email->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_email" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_email) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_email->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_level->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_level"><div class="copy_user_member_level"><span class="ewTableHeaderCaption"><?php echo $Page->member_level->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_level">
<?php if ($Page->SortUrl($Page->member_level) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_level">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_level->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_level" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_level) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_level->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_level->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_level->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_points->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_points"><div class="copy_user_member_points"><span class="ewTableHeaderCaption"><?php echo $Page->member_points->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_points">
<?php if ($Page->SortUrl($Page->member_points) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_points">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_points->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_points" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_points) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_points->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_points->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_points->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_weixinid->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_weixinid"><div class="copy_user_member_weixinid"><span class="ewTableHeaderCaption"><?php echo $Page->member_weixinid->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_weixinid">
<?php if ($Page->SortUrl($Page->member_weixinid) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_weixinid">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_weixinid->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_weixinid" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_weixinid) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_weixinid->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_weixinid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_weixinid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_weixinname->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_weixinname"><div class="copy_user_member_weixinname"><span class="ewTableHeaderCaption"><?php echo $Page->member_weixinname->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_weixinname">
<?php if ($Page->SortUrl($Page->member_weixinname) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_weixinname">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_weixinname->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_weixinname" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_weixinname) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_weixinname->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_weixinname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_weixinname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_cardid->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_cardid"><div class="copy_user_member_cardid"><span class="ewTableHeaderCaption"><?php echo $Page->member_cardid->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_cardid">
<?php if ($Page->SortUrl($Page->member_cardid) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_cardid">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_cardid->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_cardid" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_cardid) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_cardid->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_cardid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_cardid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_password->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_password"><div class="copy_user_member_password"><span class="ewTableHeaderCaption"><?php echo $Page->member_password->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_password">
<?php if ($Page->SortUrl($Page->member_password) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_password">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_password->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_password" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_password) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_password->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_password->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_password->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_enddate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_enddate"><div class="copy_user_member_enddate"><span class="ewTableHeaderCaption"><?php echo $Page->member_enddate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_enddate">
<?php if ($Page->SortUrl($Page->member_enddate) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_enddate">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_enddate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_enddate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_enddate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_enddate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_enddate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_enddate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_birthday->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_birthday"><div class="copy_user_member_birthday"><span class="ewTableHeaderCaption"><?php echo $Page->member_birthday->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_birthday">
<?php if ($Page->SortUrl($Page->member_birthday) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_birthday">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_birthday->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_birthday" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_birthday) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_birthday->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_birthday->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_birthday->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_regtime->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_regtime"><div class="copy_user_member_regtime"><span class="ewTableHeaderCaption"><?php echo $Page->member_regtime->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_regtime">
<?php if ($Page->SortUrl($Page->member_regtime) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_regtime">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_regtime->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_regtime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_regtime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_regtime->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_regtime->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_regtime->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_startdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_startdate"><div class="copy_user_member_startdate"><span class="ewTableHeaderCaption"><?php echo $Page->member_startdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_startdate">
<?php if ($Page->SortUrl($Page->member_startdate) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_startdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_startdate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_startdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_startdate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_startdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_startdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_startdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_days->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_days"><div class="copy_user_member_days"><span class="ewTableHeaderCaption"><?php echo $Page->member_days->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_days">
<?php if ($Page->SortUrl($Page->member_days) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_days">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_days->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_days" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_days) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_days->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_days->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_days->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_classcount->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_classcount"><div class="copy_user_member_classcount"><span class="ewTableHeaderCaption"><?php echo $Page->member_classcount->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_classcount">
<?php if ($Page->SortUrl($Page->member_classcount) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_classcount">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_classcount->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_classcount" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_classcount) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_classcount->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_classcount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_classcount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_attendmax->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_attendmax"><div class="copy_user_member_attendmax"><span class="ewTableHeaderCaption"><?php echo $Page->member_attendmax->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_attendmax">
<?php if ($Page->SortUrl($Page->member_attendmax) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_attendmax">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_attendmax->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_attendmax" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_attendmax) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_attendmax->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_attendmax->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_attendmax->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_isleave->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_isleave"><div class="copy_user_member_isleave"><span class="ewTableHeaderCaption"><?php echo $Page->member_isleave->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_isleave">
<?php if ($Page->SortUrl($Page->member_isleave) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_isleave">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_isleave->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_isleave" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_isleave) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_isleave->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_isleave->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_isleave->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_leavecount->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_leavecount"><div class="copy_user_member_leavecount"><span class="ewTableHeaderCaption"><?php echo $Page->member_leavecount->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_leavecount">
<?php if ($Page->SortUrl($Page->member_leavecount) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_leavecount">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_leavecount->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_leavecount" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_leavecount) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_leavecount->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_leavecount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_leavecount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_leavedays->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_leavedays"><div class="copy_user_member_leavedays"><span class="ewTableHeaderCaption"><?php echo $Page->member_leavedays->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_leavedays">
<?php if ($Page->SortUrl($Page->member_leavedays) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_leavedays">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_leavedays->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_leavedays" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_leavedays) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_leavedays->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_leavedays->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_leavedays->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->member_intro->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="member_intro"><div class="copy_user_member_intro"><span class="ewTableHeaderCaption"><?php echo $Page->member_intro->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="member_intro">
<?php if ($Page->SortUrl($Page->member_intro) == "") { ?>
		<div class="ewTableHeaderBtn copy_user_member_intro">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_intro->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer copy_user_member_intro" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->member_intro) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->member_intro->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->member_intro->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->member_intro->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->member_id->Visible) { ?>
		<td data-field="member_id"<?php echo $Page->member_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_id"<?php echo $Page->member_id->ViewAttributes() ?>><?php echo $Page->member_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_name->Visible) { ?>
		<td data-field="member_name"<?php echo $Page->member_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_name"<?php echo $Page->member_name->ViewAttributes() ?>><?php echo $Page->member_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_sex->Visible) { ?>
		<td data-field="member_sex"<?php echo $Page->member_sex->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_sex"<?php echo $Page->member_sex->ViewAttributes() ?>><?php echo $Page->member_sex->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_cell->Visible) { ?>
		<td data-field="member_cell"<?php echo $Page->member_cell->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_cell"<?php echo $Page->member_cell->ViewAttributes() ?>><?php echo $Page->member_cell->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_email->Visible) { ?>
		<td data-field="member_email"<?php echo $Page->member_email->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_email"<?php echo $Page->member_email->ViewAttributes() ?>><?php echo $Page->member_email->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_level->Visible) { ?>
		<td data-field="member_level"<?php echo $Page->member_level->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_level"<?php echo $Page->member_level->ViewAttributes() ?>><?php echo $Page->member_level->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_points->Visible) { ?>
		<td data-field="member_points"<?php echo $Page->member_points->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_points"<?php echo $Page->member_points->ViewAttributes() ?>><?php echo $Page->member_points->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_weixinid->Visible) { ?>
		<td data-field="member_weixinid"<?php echo $Page->member_weixinid->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_weixinid"<?php echo $Page->member_weixinid->ViewAttributes() ?>><?php echo $Page->member_weixinid->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_weixinname->Visible) { ?>
		<td data-field="member_weixinname"<?php echo $Page->member_weixinname->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_weixinname"<?php echo $Page->member_weixinname->ViewAttributes() ?>><?php echo $Page->member_weixinname->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_cardid->Visible) { ?>
		<td data-field="member_cardid"<?php echo $Page->member_cardid->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_cardid"<?php echo $Page->member_cardid->ViewAttributes() ?>><?php echo $Page->member_cardid->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_password->Visible) { ?>
		<td data-field="member_password"<?php echo $Page->member_password->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_password"<?php echo $Page->member_password->ViewAttributes() ?>><?php echo $Page->member_password->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_enddate->Visible) { ?>
		<td data-field="member_enddate"<?php echo $Page->member_enddate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_enddate"<?php echo $Page->member_enddate->ViewAttributes() ?>><?php echo $Page->member_enddate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_birthday->Visible) { ?>
		<td data-field="member_birthday"<?php echo $Page->member_birthday->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_birthday"<?php echo $Page->member_birthday->ViewAttributes() ?>><?php echo $Page->member_birthday->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_regtime->Visible) { ?>
		<td data-field="member_regtime"<?php echo $Page->member_regtime->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_regtime"<?php echo $Page->member_regtime->ViewAttributes() ?>><?php echo $Page->member_regtime->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_startdate->Visible) { ?>
		<td data-field="member_startdate"<?php echo $Page->member_startdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_startdate"<?php echo $Page->member_startdate->ViewAttributes() ?>><?php echo $Page->member_startdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_days->Visible) { ?>
		<td data-field="member_days"<?php echo $Page->member_days->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_days"<?php echo $Page->member_days->ViewAttributes() ?>><?php echo $Page->member_days->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_classcount->Visible) { ?>
		<td data-field="member_classcount"<?php echo $Page->member_classcount->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_classcount"<?php echo $Page->member_classcount->ViewAttributes() ?>><?php echo $Page->member_classcount->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_attendmax->Visible) { ?>
		<td data-field="member_attendmax"<?php echo $Page->member_attendmax->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_attendmax"<?php echo $Page->member_attendmax->ViewAttributes() ?>><?php echo $Page->member_attendmax->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_isleave->Visible) { ?>
		<td data-field="member_isleave"<?php echo $Page->member_isleave->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_isleave"<?php echo $Page->member_isleave->ViewAttributes() ?>><?php echo $Page->member_isleave->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_leavecount->Visible) { ?>
		<td data-field="member_leavecount"<?php echo $Page->member_leavecount->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_leavecount"<?php echo $Page->member_leavecount->ViewAttributes() ?>><?php echo $Page->member_leavecount->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_leavedays->Visible) { ?>
		<td data-field="member_leavedays"<?php echo $Page->member_leavedays->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_leavedays"<?php echo $Page->member_leavedays->ViewAttributes() ?>><?php echo $Page->member_leavedays->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->member_intro->Visible) { ?>
		<td data-field="member_intro"<?php echo $Page->member_intro->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_copy_user_member_intro"<?php echo $Page->member_intro->ViewAttributes() ?>><?php echo $Page->member_intro->ListViewValue() ?></span></td>
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
<?php include "copy_userrptpager.php" ?>
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
