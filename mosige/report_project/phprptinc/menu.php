<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(6, "mi_package_subscribe", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("6", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "package_subscriberpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(20, "mi_CTREPORT1", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("20", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "CTREPORT1ctb.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
