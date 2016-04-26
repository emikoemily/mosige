<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(6, "mmi_package_subscribe", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("6", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "package_subscriberpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(20, "mmi_CTREPORT1", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("20", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "CTREPORT1ctb.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
