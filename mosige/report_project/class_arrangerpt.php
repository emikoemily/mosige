<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysqli.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "class_arrangerptinfo.php" ?>
<?php

//
// Page class
//

$class_arrange_rpt = NULL; // Initialize page object first

class crclass_arrange_rpt extends crclass_arrange {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{55EDB588-8BCE-4361-B533-47C11315EBC4}";

	// Page object name
	var $PageObjName = 'class_arrange_rpt';

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

		// Table object (class_arrange)
		if (!isset($GLOBALS["class_arrange"])) {
			$GLOBALS["class_arrange"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["class_arrange"];
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
			define("EWR_TABLE_NAME", 'class_arrange', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fclass_arrangerpt";
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
//		$item->Visible = TRUE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_class_arrange\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_class_arrange',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = TRUE;
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fclass_arrangerpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fclass_arrangerpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fclass_arrangerpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		// Handle drill down
		$sDrillDownFilter = $this->GetDrillDownFilter();
		$gbDrillDownInPanel = $this->DrillDownInPanel;
		if ($this->DrillDown)
			ewr_AddFilter($this->Filter, $sDrillDownFilter);

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 14;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

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
				$this->FirstRowData['arrange_id'] = ewr_Conv($rs->fields('arrange_id'),3);
				$this->FirstRowData['class_id'] = ewr_Conv($rs->fields('class_id'),200);
				$this->FirstRowData['teacher_id'] = ewr_Conv($rs->fields('teacher_id'),200);
				$this->FirstRowData['arrangedate'] = ewr_Conv($rs->fields('arrangedate'),133);
				$this->FirstRowData['starttime'] = ewr_Conv($rs->fields('starttime'),134);
				$this->FirstRowData['endtime'] = ewr_Conv($rs->fields('endtime'),134);
				$this->FirstRowData['maxposition'] = ewr_Conv($rs->fields('maxposition'),3);
				$this->FirstRowData['try_maxposition'] = ewr_Conv($rs->fields('try_maxposition'),3);
				$this->FirstRowData['registercount'] = ewr_Conv($rs->fields('registercount'),3);
				$this->FirstRowData['try_registercount'] = ewr_Conv($rs->fields('try_registercount'),3);
				$this->FirstRowData['classroom'] = ewr_Conv($rs->fields('classroom'),200);
				$this->FirstRowData['overlap'] = ewr_Conv($rs->fields('overlap'),200);
				$this->FirstRowData['otherid'] = ewr_Conv($rs->fields('otherid'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->arrange_id->setDbValue($rs->fields('arrange_id'));
			$this->class_id->setDbValue($rs->fields('class_id'));
			$this->teacher_id->setDbValue($rs->fields('teacher_id'));
			$this->arrangedate->setDbValue($rs->fields('arrangedate'));
			$this->starttime->setDbValue($rs->fields('starttime'));
			$this->endtime->setDbValue($rs->fields('endtime'));
			$this->maxposition->setDbValue($rs->fields('maxposition'));
			$this->try_maxposition->setDbValue($rs->fields('try_maxposition'));
			$this->registercount->setDbValue($rs->fields('registercount'));
			$this->try_registercount->setDbValue($rs->fields('try_registercount'));
			$this->classroom->setDbValue($rs->fields('classroom'));
			$this->overlap->setDbValue($rs->fields('overlap'));
			$this->otherid->setDbValue($rs->fields('otherid'));
			$this->Val[1] = $this->arrange_id->CurrentValue;
			$this->Val[2] = $this->class_id->CurrentValue;
			$this->Val[3] = $this->teacher_id->CurrentValue;
			$this->Val[4] = $this->arrangedate->CurrentValue;
			$this->Val[5] = $this->starttime->CurrentValue;
			$this->Val[6] = $this->endtime->CurrentValue;
			$this->Val[7] = $this->maxposition->CurrentValue;
			$this->Val[8] = $this->try_maxposition->CurrentValue;
			$this->Val[9] = $this->registercount->CurrentValue;
			$this->Val[10] = $this->try_registercount->CurrentValue;
			$this->Val[11] = $this->classroom->CurrentValue;
			$this->Val[12] = $this->overlap->CurrentValue;
			$this->Val[13] = $this->otherid->CurrentValue;
		} else {
			$this->arrange_id->setDbValue("");
			$this->class_id->setDbValue("");
			$this->teacher_id->setDbValue("");
			$this->arrangedate->setDbValue("");
			$this->starttime->setDbValue("");
			$this->endtime->setDbValue("");
			$this->maxposition->setDbValue("");
			$this->try_maxposition->setDbValue("");
			$this->registercount->setDbValue("");
			$this->try_registercount->setDbValue("");
			$this->classroom->setDbValue("");
			$this->overlap->setDbValue("");
			$this->otherid->setDbValue("");
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

			// arrange_id
			$this->arrange_id->HrefValue = "";

			// class_id
			$this->class_id->HrefValue = "";

			// teacher_id
			$this->teacher_id->HrefValue = "";

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";

			// endtime
			$this->endtime->HrefValue = "";

			// maxposition
			$this->maxposition->HrefValue = "";

			// try_maxposition
			$this->try_maxposition->HrefValue = "";

			// registercount
			$this->registercount->HrefValue = "";

			// try_registercount
			$this->try_registercount->HrefValue = "";

			// classroom
			$this->classroom->HrefValue = "";

			// overlap
			$this->overlap->HrefValue = "";

			// otherid
			$this->otherid->HrefValue = "";
		} else {

			// arrange_id
			$this->arrange_id->ViewValue = $this->arrange_id->CurrentValue;
			$this->arrange_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// class_id
			$this->class_id->ViewValue = $this->class_id->CurrentValue;
			$this->class_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// teacher_id
			$this->teacher_id->ViewValue = $this->teacher_id->CurrentValue;
			$this->teacher_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// arrangedate
			$this->arrangedate->ViewValue = $this->arrangedate->CurrentValue;
			$this->arrangedate->ViewValue = ewr_FormatDateTime($this->arrangedate->ViewValue, 5);
			$this->arrangedate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// starttime
			$this->starttime->ViewValue = $this->starttime->CurrentValue;
			$this->starttime->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// endtime
			$this->endtime->ViewValue = $this->endtime->CurrentValue;
			$this->endtime->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// maxposition
			$this->maxposition->ViewValue = $this->maxposition->CurrentValue;
			$this->maxposition->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// try_maxposition
			$this->try_maxposition->ViewValue = $this->try_maxposition->CurrentValue;
			$this->try_maxposition->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// registercount
			$this->registercount->ViewValue = $this->registercount->CurrentValue;
			$this->registercount->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// try_registercount
			$this->try_registercount->ViewValue = $this->try_registercount->CurrentValue;
			$this->try_registercount->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// classroom
			$this->classroom->ViewValue = $this->classroom->CurrentValue;
			$this->classroom->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// overlap
			$this->overlap->ViewValue = $this->overlap->CurrentValue;
			$this->overlap->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// otherid
			$this->otherid->ViewValue = $this->otherid->CurrentValue;
			$this->otherid->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// arrange_id
			$this->arrange_id->HrefValue = "";

			// class_id
			$this->class_id->HrefValue = "";

			// teacher_id
			$this->teacher_id->HrefValue = "";

			// arrangedate
			$this->arrangedate->HrefValue = "";

			// starttime
			$this->starttime->HrefValue = "";

			// endtime
			$this->endtime->HrefValue = "";

			// maxposition
			$this->maxposition->HrefValue = "";

			// try_maxposition
			$this->try_maxposition->HrefValue = "";

			// registercount
			$this->registercount->HrefValue = "";

			// try_registercount
			$this->try_registercount->HrefValue = "";

			// classroom
			$this->classroom->HrefValue = "";

			// overlap
			$this->overlap->HrefValue = "";

			// otherid
			$this->otherid->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// arrange_id
			$CurrentValue = $this->arrange_id->CurrentValue;
			$ViewValue = &$this->arrange_id->ViewValue;
			$ViewAttrs = &$this->arrange_id->ViewAttrs;
			$CellAttrs = &$this->arrange_id->CellAttrs;
			$HrefValue = &$this->arrange_id->HrefValue;
			$LinkAttrs = &$this->arrange_id->LinkAttrs;
			$this->Cell_Rendered($this->arrange_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// class_id
			$CurrentValue = $this->class_id->CurrentValue;
			$ViewValue = &$this->class_id->ViewValue;
			$ViewAttrs = &$this->class_id->ViewAttrs;
			$CellAttrs = &$this->class_id->CellAttrs;
			$HrefValue = &$this->class_id->HrefValue;
			$LinkAttrs = &$this->class_id->LinkAttrs;
			$this->Cell_Rendered($this->class_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// teacher_id
			$CurrentValue = $this->teacher_id->CurrentValue;
			$ViewValue = &$this->teacher_id->ViewValue;
			$ViewAttrs = &$this->teacher_id->ViewAttrs;
			$CellAttrs = &$this->teacher_id->CellAttrs;
			$HrefValue = &$this->teacher_id->HrefValue;
			$LinkAttrs = &$this->teacher_id->LinkAttrs;
			$this->Cell_Rendered($this->teacher_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

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

			// endtime
			$CurrentValue = $this->endtime->CurrentValue;
			$ViewValue = &$this->endtime->ViewValue;
			$ViewAttrs = &$this->endtime->ViewAttrs;
			$CellAttrs = &$this->endtime->CellAttrs;
			$HrefValue = &$this->endtime->HrefValue;
			$LinkAttrs = &$this->endtime->LinkAttrs;
			$this->Cell_Rendered($this->endtime, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// maxposition
			$CurrentValue = $this->maxposition->CurrentValue;
			$ViewValue = &$this->maxposition->ViewValue;
			$ViewAttrs = &$this->maxposition->ViewAttrs;
			$CellAttrs = &$this->maxposition->CellAttrs;
			$HrefValue = &$this->maxposition->HrefValue;
			$LinkAttrs = &$this->maxposition->LinkAttrs;
			$this->Cell_Rendered($this->maxposition, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// try_maxposition
			$CurrentValue = $this->try_maxposition->CurrentValue;
			$ViewValue = &$this->try_maxposition->ViewValue;
			$ViewAttrs = &$this->try_maxposition->ViewAttrs;
			$CellAttrs = &$this->try_maxposition->CellAttrs;
			$HrefValue = &$this->try_maxposition->HrefValue;
			$LinkAttrs = &$this->try_maxposition->LinkAttrs;
			$this->Cell_Rendered($this->try_maxposition, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// registercount
			$CurrentValue = $this->registercount->CurrentValue;
			$ViewValue = &$this->registercount->ViewValue;
			$ViewAttrs = &$this->registercount->ViewAttrs;
			$CellAttrs = &$this->registercount->CellAttrs;
			$HrefValue = &$this->registercount->HrefValue;
			$LinkAttrs = &$this->registercount->LinkAttrs;
			$this->Cell_Rendered($this->registercount, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// try_registercount
			$CurrentValue = $this->try_registercount->CurrentValue;
			$ViewValue = &$this->try_registercount->ViewValue;
			$ViewAttrs = &$this->try_registercount->ViewAttrs;
			$CellAttrs = &$this->try_registercount->CellAttrs;
			$HrefValue = &$this->try_registercount->HrefValue;
			$LinkAttrs = &$this->try_registercount->LinkAttrs;
			$this->Cell_Rendered($this->try_registercount, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// classroom
			$CurrentValue = $this->classroom->CurrentValue;
			$ViewValue = &$this->classroom->ViewValue;
			$ViewAttrs = &$this->classroom->ViewAttrs;
			$CellAttrs = &$this->classroom->CellAttrs;
			$HrefValue = &$this->classroom->HrefValue;
			$LinkAttrs = &$this->classroom->LinkAttrs;
			$this->Cell_Rendered($this->classroom, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// overlap
			$CurrentValue = $this->overlap->CurrentValue;
			$ViewValue = &$this->overlap->ViewValue;
			$ViewAttrs = &$this->overlap->ViewAttrs;
			$CellAttrs = &$this->overlap->CellAttrs;
			$HrefValue = &$this->overlap->HrefValue;
			$LinkAttrs = &$this->overlap->LinkAttrs;
			$this->Cell_Rendered($this->overlap, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// otherid
			$CurrentValue = $this->otherid->CurrentValue;
			$ViewValue = &$this->otherid->ViewValue;
			$ViewAttrs = &$this->otherid->ViewAttrs;
			$CellAttrs = &$this->otherid->CellAttrs;
			$HrefValue = &$this->otherid->HrefValue;
			$LinkAttrs = &$this->otherid->LinkAttrs;
			$this->Cell_Rendered($this->otherid, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->arrange_id->Visible) $this->DtlFldCount += 1;
		if ($this->class_id->Visible) $this->DtlFldCount += 1;
		if ($this->teacher_id->Visible) $this->DtlFldCount += 1;
		if ($this->arrangedate->Visible) $this->DtlFldCount += 1;
		if ($this->starttime->Visible) $this->DtlFldCount += 1;
		if ($this->endtime->Visible) $this->DtlFldCount += 1;
		if ($this->maxposition->Visible) $this->DtlFldCount += 1;
		if ($this->try_maxposition->Visible) $this->DtlFldCount += 1;
		if ($this->registercount->Visible) $this->DtlFldCount += 1;
		if ($this->try_registercount->Visible) $this->DtlFldCount += 1;
		if ($this->classroom->Visible) $this->DtlFldCount += 1;
		if ($this->overlap->Visible) $this->DtlFldCount += 1;
		if ($this->otherid->Visible) $this->DtlFldCount += 1;
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
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = TRUE;
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		return $sWrk;
	}

	// Return drill down filter
	function GetDrillDownFilter() {
		global $ReportLanguage;
		$sFilterList = "";
		$filter = "";
		$post = ewr_StripSlashes($_POST);
		$opt = @$post["d"];
		if ($opt == "1" || $opt == "2") {
			$mastertable = @$post["s"]; // Get source table
			$sql = @$post["starttime"];
			$sql = ewr_Decrypt($sql);
			$sql = str_replace("@starttime", "`starttime`", $sql);
			if ($sql <> "") {
				if ($filter <> "") $filter .= " AND ";
				$filter .= $sql;
				if ($sql <> "1=1")
					$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->starttime->FldCaption() . "</span><span class=\"ewFilterValue\">$sql</span></div>";
			}

			// Save to session
			$_SESSION[EWR_PROJECT_NAME . "_" . $this->TableVar . "_" . EWR_TABLE_MASTER_TABLE] = $mastertable;
			$_SESSION['do_class_arrange'] = $opt;
			$_SESSION['df_class_arrange'] = $filter;
			$_SESSION['dl_class_arrange'] = $sFilterList;
		} elseif (@$_GET["cmd"] == "resetdrilldown") { // Clear drill down
			$_SESSION[EWR_PROJECT_NAME . "_" . $this->TableVar . "_" . EWR_TABLE_MASTER_TABLE] = "";
			$_SESSION['do_class_arrange'] = "";
			$_SESSION['df_class_arrange'] = "";
			$_SESSION['dl_class_arrange'] = "";
		} else { // Restore from Session
			$opt = @$_SESSION['do_class_arrange'];
			$filter = @$_SESSION['df_class_arrange'];
			$sFilterList = @$_SESSION['dl_class_arrange'];
		}
		if ($opt == "1" || $opt == "2")
			$this->DrillDown = TRUE;
		if ($opt == "1") {
			$this->DrillDownInPanel = TRUE;
			$GLOBALS["gbSkipHeaderFooter"] = TRUE;
		}
		if ($filter <> "") {
			if ($sFilterList == "")
				$sFilterList = "<div><span class=\"ewFilterValue\">" . $ReportLanguage->Phrase("DrillDownAllRecords") . "</span></div>";
			$this->DrillDownList = "<div id=\"ewrDrillDownFilters\">" . $ReportLanguage->Phrase("DrillDownFilters") . "</div>" . $sFilterList;
		}
		return $filter;
	}

	// Show drill down filters
	function ShowDrillDownList() {
		$divstyle = "";
		$divdataclass = "";
		if ($this->DrillDownList <> "") {
			$sMessage = "<div id=\"ewrDrillDownList\" class=\"ewDisplayTable\"" . $divstyle . "><div class=\"alert alert-info\"" . $divdataclass . ">" . $this->DrillDownList . "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
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
				$this->arrange_id->setSort("");
				$this->class_id->setSort("");
				$this->teacher_id->setSort("");
				$this->arrangedate->setSort("");
				$this->starttime->setSort("");
				$this->endtime->setSort("");
				$this->maxposition->setSort("");
				$this->try_maxposition->setSort("");
				$this->registercount->setSort("");
				$this->try_registercount->setSort("");
				$this->classroom->setSort("");
				$this->overlap->setSort("");
				$this->otherid->setSort("");
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
if (!isset($class_arrange_rpt)) $class_arrange_rpt = new crclass_arrange_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$class_arrange_rpt;

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
var class_arrange_rpt = new ewr_Page("class_arrange_rpt");

// Page properties
class_arrange_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = class_arrange_rpt.PageID;

// Extend page with Chart_Rendering function
class_arrange_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
class_arrange_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
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
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<?php if ($Page->ShowDrillDownFilter) { ?>
<?php $Page->ShowDrillDownList() ?>
<?php } ?>
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
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
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->arrange_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="arrange_id"><div class="class_arrange_arrange_id"><span class="ewTableHeaderCaption"><?php echo $Page->arrange_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="arrange_id">
<?php if ($Page->SortUrl($Page->arrange_id) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_arrange_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrange_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_arrange_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->arrange_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrange_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->arrange_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->arrange_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->class_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="class_id"><div class="class_arrange_class_id"><span class="ewTableHeaderCaption"><?php echo $Page->class_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="class_id">
<?php if ($Page->SortUrl($Page->class_id) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_class_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_class_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->class_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->class_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->class_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->class_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->teacher_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="teacher_id"><div class="class_arrange_teacher_id"><span class="ewTableHeaderCaption"><?php echo $Page->teacher_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="teacher_id">
<?php if ($Page->SortUrl($Page->teacher_id) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_teacher_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->teacher_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_teacher_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->teacher_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->teacher_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->teacher_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->teacher_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="arrangedate"><div class="class_arrange_arrangedate"><span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="arrangedate">
<?php if ($Page->SortUrl($Page->arrangedate) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_arrangedate">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_arrangedate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->arrangedate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->arrangedate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->arrangedate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->arrangedate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="starttime"><div class="class_arrange_starttime"><span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="starttime">
<?php if ($Page->SortUrl($Page->starttime) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_starttime">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_starttime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->starttime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->starttime->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->starttime->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->starttime->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->endtime->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="endtime"><div class="class_arrange_endtime"><span class="ewTableHeaderCaption"><?php echo $Page->endtime->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="endtime">
<?php if ($Page->SortUrl($Page->endtime) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_endtime">
			<span class="ewTableHeaderCaption"><?php echo $Page->endtime->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_endtime" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->endtime) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->endtime->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->endtime->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->endtime->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->maxposition->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="maxposition"><div class="class_arrange_maxposition"><span class="ewTableHeaderCaption"><?php echo $Page->maxposition->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="maxposition">
<?php if ($Page->SortUrl($Page->maxposition) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_maxposition">
			<span class="ewTableHeaderCaption"><?php echo $Page->maxposition->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_maxposition" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->maxposition) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->maxposition->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->maxposition->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->maxposition->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->try_maxposition->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="try_maxposition"><div class="class_arrange_try_maxposition"><span class="ewTableHeaderCaption"><?php echo $Page->try_maxposition->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="try_maxposition">
<?php if ($Page->SortUrl($Page->try_maxposition) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_try_maxposition">
			<span class="ewTableHeaderCaption"><?php echo $Page->try_maxposition->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_try_maxposition" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->try_maxposition) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->try_maxposition->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->try_maxposition->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->try_maxposition->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->registercount->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="registercount"><div class="class_arrange_registercount"><span class="ewTableHeaderCaption"><?php echo $Page->registercount->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="registercount">
<?php if ($Page->SortUrl($Page->registercount) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_registercount">
			<span class="ewTableHeaderCaption"><?php echo $Page->registercount->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_registercount" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->registercount) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->registercount->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->registercount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->registercount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->try_registercount->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="try_registercount"><div class="class_arrange_try_registercount"><span class="ewTableHeaderCaption"><?php echo $Page->try_registercount->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="try_registercount">
<?php if ($Page->SortUrl($Page->try_registercount) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_try_registercount">
			<span class="ewTableHeaderCaption"><?php echo $Page->try_registercount->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_try_registercount" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->try_registercount) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->try_registercount->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->try_registercount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->try_registercount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->classroom->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="classroom"><div class="class_arrange_classroom"><span class="ewTableHeaderCaption"><?php echo $Page->classroom->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="classroom">
<?php if ($Page->SortUrl($Page->classroom) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_classroom">
			<span class="ewTableHeaderCaption"><?php echo $Page->classroom->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_classroom" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->classroom) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->classroom->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->classroom->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->classroom->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->overlap->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="overlap"><div class="class_arrange_overlap"><span class="ewTableHeaderCaption"><?php echo $Page->overlap->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="overlap">
<?php if ($Page->SortUrl($Page->overlap) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_overlap">
			<span class="ewTableHeaderCaption"><?php echo $Page->overlap->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_overlap" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->overlap) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->overlap->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->overlap->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->overlap->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->otherid->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="otherid"><div class="class_arrange_otherid"><span class="ewTableHeaderCaption"><?php echo $Page->otherid->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="otherid">
<?php if ($Page->SortUrl($Page->otherid) == "") { ?>
		<div class="ewTableHeaderBtn class_arrange_otherid">
			<span class="ewTableHeaderCaption"><?php echo $Page->otherid->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer class_arrange_otherid" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->otherid) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->otherid->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->otherid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->otherid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
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
<?php if ($Page->arrange_id->Visible) { ?>
		<td data-field="arrange_id"<?php echo $Page->arrange_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_arrange_id"<?php echo $Page->arrange_id->ViewAttributes() ?>><?php echo $Page->arrange_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->class_id->Visible) { ?>
		<td data-field="class_id"<?php echo $Page->class_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_class_id"<?php echo $Page->class_id->ViewAttributes() ?>><?php echo $Page->class_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->teacher_id->Visible) { ?>
		<td data-field="teacher_id"<?php echo $Page->teacher_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_teacher_id"<?php echo $Page->teacher_id->ViewAttributes() ?>><?php echo $Page->teacher_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->arrangedate->Visible) { ?>
		<td data-field="arrangedate"<?php echo $Page->arrangedate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_arrangedate"<?php echo $Page->arrangedate->ViewAttributes() ?>><?php echo $Page->arrangedate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->starttime->Visible) { ?>
		<td data-field="starttime"<?php echo $Page->starttime->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_starttime"<?php echo $Page->starttime->ViewAttributes() ?>><?php echo $Page->starttime->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->endtime->Visible) { ?>
		<td data-field="endtime"<?php echo $Page->endtime->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_endtime"<?php echo $Page->endtime->ViewAttributes() ?>><?php echo $Page->endtime->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->maxposition->Visible) { ?>
		<td data-field="maxposition"<?php echo $Page->maxposition->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_maxposition"<?php echo $Page->maxposition->ViewAttributes() ?>><?php echo $Page->maxposition->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->try_maxposition->Visible) { ?>
		<td data-field="try_maxposition"<?php echo $Page->try_maxposition->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_try_maxposition"<?php echo $Page->try_maxposition->ViewAttributes() ?>><?php echo $Page->try_maxposition->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->registercount->Visible) { ?>
		<td data-field="registercount"<?php echo $Page->registercount->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_registercount"<?php echo $Page->registercount->ViewAttributes() ?>><?php echo $Page->registercount->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->try_registercount->Visible) { ?>
		<td data-field="try_registercount"<?php echo $Page->try_registercount->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_try_registercount"<?php echo $Page->try_registercount->ViewAttributes() ?>><?php echo $Page->try_registercount->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->classroom->Visible) { ?>
		<td data-field="classroom"<?php echo $Page->classroom->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_classroom"<?php echo $Page->classroom->ViewAttributes() ?>><?php echo $Page->classroom->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->overlap->Visible) { ?>
		<td data-field="overlap"<?php echo $Page->overlap->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_overlap"<?php echo $Page->overlap->ViewAttributes() ?>><?php echo $Page->overlap->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->otherid->Visible) { ?>
		<td data-field="otherid"<?php echo $Page->otherid->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_class_arrange_otherid"<?php echo $Page->otherid->ViewAttributes() ?>><?php echo $Page->otherid->ListViewValue() ?></span></td>
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
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || FALSE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "class_arrangerptpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
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
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
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
