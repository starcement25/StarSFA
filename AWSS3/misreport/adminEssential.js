// JavaScript Document

<!--

function mmLoadMenus() 
{
	if (window.mm_menu_0716141133_0) return;

	window.mm_menu_0716141133_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);

	mm_menu_0716141133_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");

	mm_menu_0716141133_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141133_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	
	//mm_menu_0716141133_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	//mm_menu_0716141133_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	//mm_menu_0716141133_0.addMenuItem("Upload Data","location='zipUpload.php'");
	
	/*mm_menu_0716141133_0.addMenuItem("Mail&nbsp;Management","location='adminSendMail.php'");*/

	mm_menu_0716141133_0.fontWeight="bold";

	mm_menu_0716141133_0.hideOnMouseOut=true;

	mm_menu_0716141133_0.bgColor='#FFFFFF';

	mm_menu_0716141133_0.menuBorder=1;

	mm_menu_0716141133_0.menuLiteBgColor='#FFFFFF';

	mm_menu_0716141133_0.menuBorderBgColor='#DEDEDE';
	
	/*--------> HALDIRAM REPORT MENU <--------*/
	window.mm_menu_0716141174_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141174_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	mm_menu_0716141174_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141174_0.addMenuItem("MIS Report","location='adminMisReportEmphierarchy.php'");
	mm_menu_0716141174_0.fontWeight="bold";
	mm_menu_0716141174_0.hideOnMouseOut=true;
	mm_menu_0716141174_0.bgColor='#FFFFFF';
	mm_menu_0716141174_0.menuBorder=1;
	mm_menu_0716141174_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141174_0.menuBorderBgColor='#DEDEDE';
		
	window.mm_menu_0716141153_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141153_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	//mm_menu_0716141153_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141153_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141153_0.addMenuItem("Checkin/Checkout Report","location='adminCheckinoutReport.php'");
	mm_menu_0716141153_0.fontWeight="bold";
	mm_menu_0716141153_0.hideOnMouseOut=true;
	mm_menu_0716141153_0.bgColor='#FFFFFF';
	mm_menu_0716141153_0.menuBorder=1;
	mm_menu_0716141153_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141153_0.menuBorderBgColor='#DEDEDE';
	
	/*----> VIPL Order Track Menu <----*/
	window.mm_menu_0716141866_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141866_0.addMenuItem("Track Order","location='order_tracking_report.php'");
	mm_menu_0716141866_0.addMenuItem("Spl Permission/Hold","location='ordertrack_splpermission_report.php'");
	mm_menu_0716141866_0.addMenuItem("General Report","location='order_track_normalized_report.php'");
	mm_menu_0716141866_0.addMenuItem("Detailed Report","location='order_track_admin_report.php'");
	mm_menu_0716141866_0.addMenuItem("Dispatch Order","location='dispatch_report.php'");
	mm_menu_0716141866_0.addMenuItem("Dispatch Report","location='dispatch_tracking_report.php'");
	mm_menu_0716141866_0.fontWeight="bold";
	mm_menu_0716141866_0.hideOnMouseOut=true;
	mm_menu_0716141866_0.bgColor='#FFFFFF';
	mm_menu_0716141866_0.menuBorder=1;
	mm_menu_0716141866_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141866_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417117_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417117_0.addMenuItem("Stock Audit","location='stock_audit_report.php'");
	mm_menu_07161417117_0.addMenuItem("Stock Analysis","location='stockAnalysisReport.php'");
	mm_menu_07161417117_0.fontWeight="bold";
	mm_menu_07161417117_0.hideOnMouseOut=true;
	mm_menu_07161417117_0.bgColor='#FFFFFF';
	mm_menu_07161417117_0.menuBorder=1;
	mm_menu_07161417117_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417117_0.menuBorderBgColor='#DEDEDE';
	
	/*----> Report Menu With Check In/Out Details <----*/
	window.mm_menu_0716141165_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141165_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	mm_menu_0716141165_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141165_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141165_0.addMenuItem("Survey Report","location='adminMisReportSurvey.php'");
   // mm_menu_0716141165_0.addMenuItem("Checkin/Checkout Report","location='adminCheckinoutReport.php'");
	mm_menu_0716141165_0.fontWeight="bold";
	mm_menu_0716141165_0.hideOnMouseOut=true;
	mm_menu_0716141165_0.bgColor='#FFFFFF';
	mm_menu_0716141165_0.menuBorder=1;
	mm_menu_0716141165_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141165_0.menuBorderBgColor='#DEDEDE';
	
	
	window.mm_menu_0716141155_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141155_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	mm_menu_0716141155_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141155_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141155_0.addMenuItem("Survey Report","location='adminMisReportSurvey.php'");
	mm_menu_0716141155_0.fontWeight="bold";
	mm_menu_0716141155_0.hideOnMouseOut=true;
	mm_menu_0716141155_0.bgColor='#FFFFFF';
	mm_menu_0716141155_0.menuBorder=1;
	mm_menu_0716141155_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141155_0.menuBorderBgColor='#DEDEDE';
	
	/*--------> Report Menu For CASHLESS <--------*/
	window.mm_menu_0716141161_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141161_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	//mm_menu_0716141161_0.addMenuItem("Survey Report","location='adminMisReportSurvey.php'");
	mm_menu_0716141161_0.fontWeight="bold";
	mm_menu_0716141161_0.hideOnMouseOut=true;
	mm_menu_0716141161_0.bgColor='#FFFFFF';
	mm_menu_0716141161_0.menuBorder=1;
	mm_menu_0716141161_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141161_0.menuBorderBgColor='#DEDEDE';
	
	/*--------> Report Menu For STAR <--------*/
	window.mm_menu_0716141159_0 = new Menu("root",150,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141159_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	//mm_menu_0716141159_0.addMenuItem("Activity","location='adminActivity.php'");
	mm_menu_0716141159_0.addMenuItem("MIS Report","location='admin_mis_report_star.php'");
	//mm_menu_0716141159_0.addMenuItem("MIS Report","location='dashboard.php'");
	mm_menu_0716141159_0.fontWeight="bold";
	mm_menu_0716141159_0.hideOnMouseOut=true;
	mm_menu_0716141159_0.bgColor='#FFFFFF';
	mm_menu_0716141159_0.menuBorder=1;
	mm_menu_0716141159_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141159_0.menuBorderBgColor='#DEDEDE';
	
	
	/*Report Menu For Rupa*/
	window.mm_menu_0716141136_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141136_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	mm_menu_0716141136_0.addMenuItem("Survey Report","location='survey_name_report.php'");
	//mm_menu_0716141136_0.addMenuItem("Activity","location='adminActivity.php'");
	//mm_menu_0716141136_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	//mm_menu_0716141133_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	//mm_menu_0716141133_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	//mm_menu_0716141133_0.addMenuItem("Upload Data","location='zipUpload.php'");
	/*mm_menu_0716141133_0.addMenuItem("Mail&nbsp;Management","location='adminSendMail.php'");*/
	mm_menu_0716141136_0.fontWeight="bold";
	mm_menu_0716141136_0.hideOnMouseOut=true;
	mm_menu_0716141136_0.bgColor='#FFFFFF';
	mm_menu_0716141136_0.menuBorder=1;
	mm_menu_0716141136_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141136_0.menuBorderBgColor='#DEDEDE';
	/*---------------------*/
	
	/*Survey Edit Menu For Store*/
	window.mm_menu_0716141230_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141230_0.addMenuItem("Edit Survey","location='survey_output_edit.php'");
	//mm_menu_0716141230_0.addMenuItem("Show Gallery","location='show_gallery.php'");
	mm_menu_0716141230_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
	//mm_menu_0716141136_0.addMenuItem("Activity","location='adminActivity.php'");
	//mm_menu_0716141136_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	//mm_menu_0716141133_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	//mm_menu_0716141133_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	//mm_menu_0716141133_0.addMenuItem("Upload Data","location='zipUpload.php'");
	/*mm_menu_0716141133_0.addMenuItem("Mail&nbsp;Management","location='adminSendMail.php'");*/
	mm_menu_0716141230_0.fontWeight="bold";
	mm_menu_0716141230_0.hideOnMouseOut=true;
	mm_menu_0716141230_0.bgColor='#FFFFFF';
	mm_menu_0716141230_0.menuBorder=1;
	mm_menu_0716141230_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141230_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161412107_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141230_0.addMenuItem("Edit Survey","location='survey_output_edit.php'");
	//mm_menu_0716141230_0.addMenuItem("Show Gallery","location='show_gallery.php'");
	mm_menu_07161412107_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
	mm_menu_07161412107_0.addMenuItem("Site Visit","location='site_visit_report_modified.php'");
	mm_menu_07161412107_0.addMenuItem("Site Maturity Report","location='site_maturity_report.php'");
	mm_menu_07161412107_0.addMenuItem("Entity Wise Report","location='employee_entity_wise_report.php'");
	mm_menu_07161412107_0.addMenuItem("Excel Download Site Data","location='excel_download_site_data.php'");
	//mm_menu_0716141136_0.addMenuItem("Activity","location='adminActivity.php'");
	//mm_menu_0716141136_0.addMenuItem("MIS Report","location='adminMisReport.php'");
	//mm_menu_0716141133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	//mm_menu_0716141133_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	//mm_menu_0716141133_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	//mm_menu_0716141133_0.addMenuItem("Upload Data","location='zipUpload.php'");
	/*mm_menu_0716141133_0.addMenuItem("Mail&nbsp;Management","location='adminSendMail.php'");*/
	mm_menu_07161412107_0.fontWeight="bold";
	mm_menu_07161412107_0.hideOnMouseOut=true;
	mm_menu_07161412107_0.bgColor='#FFFFFF';
	mm_menu_07161412107_0.menuBorder=1;
	mm_menu_07161412107_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161412107_0.menuBorderBgColor='#DEDEDE';

	/*---------------------*/
	
	/*-----------Product Promotion Menu----------------*/
	window.mm_menu_0716141838_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141838_0.addMenuItem("Add","location='product_promotion.php'");
	mm_menu_0716141838_0.addMenuItem("View","location='product_promotion_details.php'");
	mm_menu_0716141838_0.fontWeight="bold";
	mm_menu_0716141838_0.hideOnMouseOut=true;
	mm_menu_0716141838_0.bgColor='#FFFFFF';
	mm_menu_0716141838_0.menuBorder=1;
	mm_menu_0716141838_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141838_0.menuBorderBgColor='#DEDEDE';
	/*-------------------------------------------------*/
	
	/*-----------Master Input Menu----------------*/
	window.mm_menu_0716141835_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141835_0.addMenuItem("Mall","location='mall_add_edit.php'");
	mm_menu_0716141835_0.addMenuItem("Hi Street","location='highstreet_add_edit.php'");
	mm_menu_0716141835_0.fontWeight="bold";
	mm_menu_0716141835_0.hideOnMouseOut=true;
	mm_menu_0716141835_0.bgColor='#FFFFFF';
	mm_menu_0716141835_0.menuBorder=1;
	mm_menu_0716141835_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141835_0.menuBorderBgColor='#DEDEDE';
	/*-------------------------------------------------*/
	
	/*-----------Pricing Menu EMAMI----------------*/
	window.mm_menu_0716141845_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141845_0.addMenuItem("Generate Pricing","location='generate_pricing_details.php'");
	/*mm_menu_0716141845_0.addMenuItem("Generate Freight","location='generate_basic_freight.php'");
	mm_menu_0716141845_0.addMenuItem("Generate Load Distribution","location='generate_load_distribution.php'");*/
	/*mm_menu_0716141845_0.addMenuItem("Generate Depot Freight Cost","location='generate_depot_cost.php'");*/
	mm_menu_0716141845_0.addMenuItem("Oil Loose Rate Report","location='pricing_details_report_modified.php'");
	mm_menu_0716141845_0.addMenuItem("Freight Cost","location='freight_cost_report.php'");
	mm_menu_0716141845_0.fontWeight="bold";
	mm_menu_0716141845_0.hideOnMouseOut=true;
	mm_menu_0716141845_0.bgColor='#FFFFFF';
	mm_menu_0716141845_0.menuBorder=1;
	mm_menu_0716141845_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141845_0.menuBorderBgColor='#DEDEDE';
	/*-------------------------------------------------*/
	
	/*-----------Pricing Menu EMAMIT----------------*/
	window.mm_menu_0716141851_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141851_0.addMenuItem("Hire Cost","location='generate_basic_freight.php'");
	mm_menu_0716141851_0.addMenuItem("Depot Cost","location='generate_depot_cost.php'");
	mm_menu_0716141851_0.addMenuItem("Margin","location='generate_margin_cost_modified.php'");
	mm_menu_0716141851_0.addMenuItem("Load distribution","location='generate_load_distribution.php'");
	mm_menu_0716141851_0.addMenuItem("Generate Pricing","location='generate_pricing_details_formulation.php'");
	mm_menu_0716141851_0.addMenuItem("Oil Loose Rate Report","location='pricing_details_report_modified.php'");
	mm_menu_0716141851_0.addMenuItem("Freight Cost","location='freight_cost_report.php'");
	mm_menu_0716141851_0.addMenuItem("Depot Cost Report","location='depot_cost_report.php'");
	mm_menu_0716141851_0.addMenuItem("Sale Rate Report","location='sale-rate-data-download.php'");
	mm_menu_0716141851_0.fontWeight="bold";
	mm_menu_0716141851_0.hideOnMouseOut=true;
	mm_menu_0716141851_0.bgColor='#FFFFFF';
	mm_menu_0716141851_0.menuBorder=1;
	mm_menu_0716141851_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141851_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141850_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141850_0.addMenuItem("Plantwise Load Capacity","location='generate_plantwise_transport_mode_capacity.php'");
	mm_menu_0716141850_0.addMenuItem("Primary Freight","location='generate_basic_freight_plantwise.php'");
	mm_menu_0716141850_0.addMenuItem("Depot Cost","location='generate_depot_cost_modified.php'");
	mm_menu_0716141850_0.addMenuItem("Margin","location='generate_margin_cost_depotwise.php'");
	mm_menu_0716141850_0.addMenuItem("Margin Rasoi","location='generate_margin_cost_rasoi.php'");
	mm_menu_0716141850_0.addMenuItem("Load distribution","location='generate_load_distribution_plantwise.php'");
	mm_menu_0716141850_0.addMenuItem("Honeycomb Cost","location='genrate_honeycomb_cost_plantwise.php'");
	mm_menu_0716141850_0.addMenuItem("Detention Cost","location='generate_detension_cost.php'");
	mm_menu_0716141850_0.addMenuItem("Generate Pricing","location='generate_pricing_details_formulationmodified.php'");
	mm_menu_0716141850_0.addMenuItem("Release Pricing","location='generate_pricing_issue_to_released.php'");
	mm_menu_0716141850_0.addMenuItem("Oil Loose Rate Report","location='pricing_details_report_modified.php'");
	//mm_menu_0716141850_0.addMenuItem("Freight Cost","location='freight_cost_report.php'");
	//mm_menu_0716141850_0.addMenuItem("Depot Cost Report","location='depot_cost_report.php'");
	mm_menu_0716141850_0.fontWeight="bold";
	mm_menu_0716141850_0.hideOnMouseOut=true;
	mm_menu_0716141850_0.bgColor='#FFFFFF';
	mm_menu_0716141850_0.menuBorder=1;
	mm_menu_0716141850_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141850_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141877_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141877_0.addMenuItem("Plantwise Load Capacity","location='generate_plantwise_transport_mode_capacity.php'");
	mm_menu_0716141877_0.addMenuItem("Primary Freight","location='generate_basic_freight_plantwise.php'");
	mm_menu_0716141877_0.addMenuItem("Depot Cost","location='generate_depot_cost_modified.php'");
	mm_menu_0716141877_0.addMenuItem("Margin","location='generate_margin_cost_rasoi.php'");
	mm_menu_0716141877_0.addMenuItem("Load distribution","location='generate_load_distribution_plantwise.php'");
	//mm_menu_0716141877_0.addMenuItem("Honeycomb Cost","location='genrate_honeycomb_cost_plantwise.php'");
	mm_menu_0716141877_0.addMenuItem("Detention Cost","location='generate_detension_cost.php'");
	mm_menu_0716141877_0.addMenuItem("Generate Pricing","location='generate_pricing_details_formulationSF.php'");
	mm_menu_0716141877_0.addMenuItem("Release Pricing","location='generate_pricing_issue_to_released_SF.php'");
	mm_menu_0716141877_0.addMenuItem("Oil Loose Rate Report","location='pricing_details_report_modified.php'");
	mm_menu_0716141877_0.addMenuItem("Freight Cost","location='freight_cost_report.php'");
	mm_menu_0716141877_0.addMenuItem("Depot Cost Report","location='depot_cost_report.php'");
	mm_menu_0716141877_0.fontWeight="bold";
	mm_menu_0716141877_0.hideOnMouseOut=true;
	mm_menu_0716141877_0.bgColor='#FFFFFF';
	mm_menu_0716141877_0.menuBorder=1;
	mm_menu_0716141877_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141877_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418112_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418112_0.addMenuItem("Generate Pricing","location='generate_pricing_formulationwise_modified.php'");
	//mm_menu_07161418112_0.addMenuItem("Release Pricing","location='generated_price_release_modified.php'");
	mm_menu_07161418112_0.addMenuItem("Crude Oil Procurement","location='genarate_crude_oil_procurement.php'");
	mm_menu_07161418112_0.fontWeight="bold";
	mm_menu_07161418112_0.hideOnMouseOut=true;
	mm_menu_07161418112_0.bgColor='#FFFFFF';
	mm_menu_07161418112_0.menuBorder=1;
	mm_menu_07161418112_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418112_0.menuBorderBgColor='#DEDEDE';

	window.mm_menu_0716141881_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141881_0.addMenuItem("Hire Cost","location='generate_basic_freight.php'");
	mm_menu_0716141881_0.addMenuItem("Depot Cost","location='generate_depot_cost.php'");
	mm_menu_0716141881_0.addMenuItem("Margin","location='generate_margin_cost_modified.php'");
	mm_menu_0716141881_0.addMenuItem("Load distribution","location='generate_load_distribution.php'");
	mm_menu_0716141881_0.addMenuItem("Generate Pricing","location='generate_pricing_details_formulation.php'");
	mm_menu_0716141881_0.addMenuItem("Oil Loose Rate Report","location='pricing_details_report_modified.php'");
	mm_menu_0716141881_0.addMenuItem("Freight Cost","location='freight_cost_report.php'");
	mm_menu_0716141881_0.addMenuItem("Depot Cost Report","location='depot_cost_report.php'");
	mm_menu_0716141881_0.fontWeight="bold";
	mm_menu_0716141881_0.hideOnMouseOut=true;
	mm_menu_0716141881_0.bgColor='#FFFFFF';
	mm_menu_0716141881_0.menuBorder=1;
	mm_menu_0716141881_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141881_0.menuBorderBgColor='#DEDEDE';
	/*-------------------------------------------------*/
	
	/*-----> EMAMI Pricelist Menu <-----*/
	window.mm_menu_0716141129_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141129_0.addMenuItem("Depotwise Pricelist","location='depotwise_pricelist_report.php'");
	mm_menu_0716141129_0.addMenuItem("Datewise Pricelist","location='depotwise_pricelist_report_new.php'");
	mm_menu_0716141129_0.fontWeight="bold";
	mm_menu_0716141129_0.hideOnMouseOut=true;
	mm_menu_0716141129_0.bgColor='#FFFFFF';
	mm_menu_0716141129_0.menuBorder=1;
	mm_menu_0716141129_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141129_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141190_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141190_0.addMenuItem("Depotwise Pricelist","location='depotwise_pricelist_report_verticalwise.php'");
	mm_menu_0716141190_0.addMenuItem("Datewise Pricelist","location='depotwise_pricelist_report_new_verticalwise.php'");
	mm_menu_0716141190_0.fontWeight="bold";
	mm_menu_0716141190_0.hideOnMouseOut=true;
	mm_menu_0716141190_0.bgColor='#FFFFFF';
	mm_menu_0716141190_0.menuBorder=1;
	mm_menu_0716141190_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141190_0.menuBorderBgColor='#DEDEDE';
	
   window.mm_menu_0716141839_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);

	//mm_menu_0716141839_0.addMenuItem("Employee&nbsp;Wise","location='employeehierarchywisereport.php'");
	mm_menu_0716141839_0.addMenuItem("Employee&nbsp;Wise","location='rupa_empwise_vertical_report.php'");
	mm_menu_0716141839_0.addMenuItem("Zone&nbsp;Wise","location='zonestateverticalwisereport.php'");

	mm_menu_0716141839_0.fontWeight="bold";

	mm_menu_0716141839_0.hideOnMouseOut=true;

	mm_menu_0716141839_0.bgColor='#FFFFFF';

	mm_menu_0716141839_0.menuBorder=1;

	mm_menu_0716141839_0.menuLiteBgColor='#FFFFFF';

	mm_menu_0716141839_0.menuBorderBgColor='#DEDEDE';
	
	
	/*------> RUPA Customize Report <--------*/
	window.mm_menu_0716141834_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141834_0.addMenuItem("Frequency Report","location='customerVisitFrequencyReport.php'");
	mm_menu_0716141834_0.addMenuItem("Frequency Report Retail","location='customerVisitFrequencyReportModified.php'");
	mm_menu_0716141834_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	//mm_menu_0716141834_0.addMenuItem("Estimated Secondary Sales","location='distributorPreviousCurrentStock.php'");
	mm_menu_0716141834_0.addMenuItem("Daily Activity Analysis","location='vertical_employee_report.php'");
	mm_menu_0716141834_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141834_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141834_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141834_0.addMenuItem("KYC Download","location='excel_download_customer_KYC.php'");
	mm_menu_0716141834_0.addMenuItem("Secondary Sales Report","location='secondarySaleReport.php'");
	mm_menu_0716141834_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141834_0.addMenuItem("Distributor Report","location='distributorReport.php'");
	mm_menu_0716141834_0.addMenuItem("Historical Data Secondary","location='secondarySalesHistorical.php'");
	mm_menu_0716141834_0.addMenuItem("Edit Customer","location='adminCustomerEdit.php'");
	mm_menu_0716141834_0.addMenuItem("Order Edit","location='adminOrderEdit.php'");
	mm_menu_0716141834_0.addMenuItem("Attendance Location","location='employeeVisitRouteDetails.php'");
	mm_menu_0716141834_0.fontWeight="bold";
	mm_menu_0716141834_0.hideOnMouseOut=true;
	mm_menu_0716141834_0.bgColor='#FFFFFF';
	mm_menu_0716141834_0.menuBorder=1;
	mm_menu_0716141834_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141834_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141854_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141854_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141854_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141854_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141854_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141854_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141854_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141854_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141854_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141854_0.addMenuItem("Frequency Report","location='customerVisitFrequencyReport.php'");
	mm_menu_0716141854_0.addMenuItem("Gift Delivery Report","location='gift_delivery_report.php'");
	mm_menu_0716141854_0.addMenuItem("Business Prospect Export","location='business_prospect_report_export.php'");
	mm_menu_0716141854_0.addMenuItem("Kilometer Reading Report","location='kilometer_reading_report.php'");
	mm_menu_0716141854_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141854_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	mm_menu_0716141854_0.fontWeight="bold";
	mm_menu_0716141854_0.hideOnMouseOut=true;
	mm_menu_0716141854_0.bgColor='#FFFFFF';
	mm_menu_0716141854_0.menuBorder=1;
	mm_menu_0716141854_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141854_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141144_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141144_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	mm_menu_0716141144_0.addMenuItem("Add Employee","location='employee_entry_form.php'");
	mm_menu_0716141144_0.addMenuItem("Back End Access","location='employee_access_backend.php'");
	mm_menu_0716141144_0.fontWeight="bold";
	mm_menu_0716141144_0.hideOnMouseOut=true;
	mm_menu_0716141144_0.bgColor='#FFFFFF';
	mm_menu_0716141144_0.menuBorder=1;
	mm_menu_0716141144_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141144_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141837_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141837_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141837_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141837_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141837_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141837_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141837_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141837_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141837_0.fontWeight="bold";
	mm_menu_0716141837_0.hideOnMouseOut=true;
	mm_menu_0716141837_0.bgColor='#FFFFFF';
	mm_menu_0716141837_0.menuBorder=1;
	mm_menu_0716141837_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141837_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418135_0 = new Menu("root",175,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418135_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418135_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418135_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	//mm_menu_07161418135_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418135_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418135_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418135_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418135_0.addMenuItem("Stockist Visit Report","location='stockist_visit_report.php'");
	mm_menu_07161418135_0.fontWeight="bold";
	mm_menu_07161418135_0.hideOnMouseOut=true;
	mm_menu_07161418135_0.bgColor='#FFFFFF';
	mm_menu_07161418135_0.menuBorder=1;
	mm_menu_07161418135_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418135_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418136_0 = new Menu("root",175,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418136_0.addMenuItem("Sales executive wise","location='sales_executive_wise_report.php'");
	mm_menu_07161418136_0.addMenuItem("Telecaller Achieved Report ","location='telecaller_achieved_report.php'");
	mm_menu_07161418136_0.addMenuItem("Group leader Report ","location='group_leader_report.php'");
	/*mm_menu_07161418136_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418136_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	//mm_menu_07161418135_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418136_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418136_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418136_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418136_0.addMenuItem("Stockist Visit Report","location='stockist_visit_report.php'");*/
	mm_menu_07161418136_0.fontWeight="bold";
	mm_menu_07161418136_0.hideOnMouseOut=true;
	mm_menu_07161418136_0.bgColor='#FFFFFF';
	mm_menu_07161418136_0.menuBorder=1;
	mm_menu_07161418136_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418136_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418132_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418132_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418132_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418132_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418132_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418132_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418132_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418132_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418132_0.addMenuItem("Check In Check Out","location='check_in_check_out_summary.php'");
	mm_menu_07161418132_0.addMenuItem("Customer Location Edit","location='customer_latt_long_edit.php'");
	mm_menu_07161418132_0.addMenuItem("Check In Check Out Customize","location='check_in_check_out_customize.php'");
	mm_menu_07161418132_0.fontWeight="bold";
	mm_menu_07161418132_0.hideOnMouseOut=true;
	mm_menu_07161418132_0.bgColor='#FFFFFF';
	mm_menu_07161418132_0.menuBorder=1;
	mm_menu_07161418132_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418132_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418134_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_07161418134_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	//mm_menu_07161418134_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	//mm_menu_07161418134_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418134_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418134_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	//mm_menu_07161418134_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	//mm_menu_07161418134_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418134_0.addMenuItem("Check In Check Out","location='check_in_check_out_summary.php'");
	mm_menu_07161418134_0.addMenuItem("Business Prospect Export","location='business_prospect_report_export.php'");
	mm_menu_07161418134_0.addMenuItem("Doctor Visit Report","location='doctor_visit_details.php'");
	mm_menu_07161418134_0.fontWeight="bold";
	mm_menu_07161418134_0.hideOnMouseOut=true;
	mm_menu_07161418134_0.bgColor='#FFFFFF';
	mm_menu_07161418134_0.menuBorder=1;
	mm_menu_07161418134_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418134_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418131_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418131_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418131_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418131_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418131_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418131_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418131_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418131_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418131_0.addMenuItem("Dashboard","location='dashboard_sales_customerwise.php'");
	mm_menu_07161418131_0.addMenuItem("Call Centre","location='adminOrderSubmit.php'");
	mm_menu_07161418131_0.addMenuItem("Dashboard Attendance","location='attendance_dashboard_report.php'");
	mm_menu_07161418131_0.addMenuItem("Vistaar Dashboard","location='vistaar_dashboard_report.php'");
	mm_menu_07161418131_0.addMenuItem("Visit Adherence & Productivity","location='dashboard_sales_emp_wise.php'");
	mm_menu_07161418131_0.addMenuItem("First Outlet Visit Report","location='check_in_outlet_report.php'");
	mm_menu_07161418131_0.fontWeight="bold";
	mm_menu_07161418131_0.hideOnMouseOut=true;
	mm_menu_07161418131_0.bgColor='#FFFFFF';
	mm_menu_07161418131_0.menuBorder=1;
	mm_menu_07161418131_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418131_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418130_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418130_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418130_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_report.php'");
	mm_menu_07161418130_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418130_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418130_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418130_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418130_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418130_0.addMenuItem("Outlet Wise SKU Booking","location='outlet_wise_sku_booking.php'");
	mm_menu_07161418130_0.addMenuItem("Customer Visit & Productivity","location='outlet_wise_productivity_optimize_final.php'");
	mm_menu_07161418130_0.addMenuItem("Joint Work Report","location='csvDownloadJointWorkModified.php'");
	mm_menu_07161418130_0.addMenuItem("Beat wise TA DA Report","location='routewise_TA_DA_report.php'");
	mm_menu_07161418130_0.addMenuItem("Order Status Report","location='order_status_report.php'");
	mm_menu_07161418130_0.addMenuItem("Customer Location Edit","location='customer_latt_long_edit.php'");
	mm_menu_07161418130_0.addMenuItem("Beat wise TC PC Report","location='beatwise_TC_PC_report.php'");
	mm_menu_07161418130_0.addMenuItem("Work Summary Report","location='work_summary_modified_report.php'");
	mm_menu_07161418130_0.addMenuItem("Customer Activity Report","location='customer_activity_report.php'");
	mm_menu_07161418130_0.fontWeight="bold";
	mm_menu_07161418130_0.hideOnMouseOut=true;
	mm_menu_07161418130_0.bgColor='#FFFFFF';
	mm_menu_07161418130_0.menuBorder=1;
	mm_menu_07161418130_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418130_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_07161418104_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418104_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418104_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418104_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418104_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418104_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418104_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_07161418104_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418104_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418104_0.fontWeight="bold";
	mm_menu_07161418104_0.hideOnMouseOut=true;
	mm_menu_07161418104_0.bgColor='#FFFFFF';
	mm_menu_07161418104_0.menuBorder=1;
	mm_menu_07161418104_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418104_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418120_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418120_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418120_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418120_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418120_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418120_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418120_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_07161418120_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418120_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418120_0.addMenuItem("Target VS Sale Report","location='target_sale_order_vertcalwise.php'");
	mm_menu_07161418120_0.addMenuItem("SKU Wise Report","location='monthly_activity_report_productgrpouwise.php'");
	mm_menu_07161418120_0.addMenuItem("Attendance Location","location='employeeVisitRouteDetails.php'");
	mm_menu_07161418120_0.fontWeight="bold";
	mm_menu_07161418120_0.hideOnMouseOut=true;
	mm_menu_07161418120_0.bgColor='#FFFFFF';
	mm_menu_07161418120_0.menuBorder=1;
	mm_menu_07161418120_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418120_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_07161418103_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418103_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418103_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_07161418103_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418103_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418103_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418103_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	//mm_menu_07161418103_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	//mm_menu_07161418103_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	mm_menu_07161418103_0.fontWeight="bold";
	mm_menu_07161418103_0.hideOnMouseOut=true;
	mm_menu_07161418103_0.bgColor='#FFFFFF';
	mm_menu_07161418103_0.menuBorder=1;
	mm_menu_07161418103_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418103_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418114_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418114_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_07161418114_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	mm_menu_07161418114_0.fontWeight="bold";
	mm_menu_07161418114_0.hideOnMouseOut=true;
	mm_menu_07161418114_0.bgColor='#FFFFFF';
	mm_menu_07161418114_0.menuBorder=1;
	mm_menu_07161418114_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418114_0.menuBorderBgColor='#DEDEDE';

	window.mm_menu_07161417115_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417115_0.addMenuItem("Bargain register","location='adminBargainRegister.php'");
	mm_menu_07161417115_0.addMenuItem("Pending Bargain","location='adminPendingBargain.php'");
	//mm_menu_07161417115_0.addMenuItem("Bargain wise","location='bargainwisereport.php'");
	//mm_menu_07161417115_0.addMenuItem("Customer wise","location='customerwisereportbargain.php'");
	mm_menu_07161417115_0.fontWeight="bold";
	mm_menu_07161417115_0.hideOnMouseOut=true;
	mm_menu_07161417115_0.bgColor='#FFFFFF';
	mm_menu_07161417115_0.menuBorder=1;
	mm_menu_07161417115_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417115_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417122_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417122_0.addMenuItem("App Bargain","location='adminBargainTransfer.php'");
	mm_menu_07161417122_0.addMenuItem("Opening Bargain","location='adminBargainTransferOpening.php'");
	//mm_menu_07161417115_0.addMenuItem("Bargain wise","location='bargainwisereport.php'");
	//mm_menu_07161417115_0.addMenuItem("Customer wise","location='customerwisereportbargain.php'");
	mm_menu_07161417122_0.fontWeight="bold";
	mm_menu_07161417122_0.hideOnMouseOut=true;
	mm_menu_07161417122_0.bgColor='#FFFFFF';
	mm_menu_07161417122_0.menuBorder=1;
	mm_menu_07161417122_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417122_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417121_0 = new Menu("root",160,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417121_0.addMenuItem("Pending Approval Bargain","location='adminBargainApproved.php'");
	mm_menu_07161417121_0.addMenuItem("DO Approval","location='adminDOapproved.php'");
	mm_menu_07161417121_0.addMenuItem("Expiry Date Adjustment","location='adminBargainvlalidityreduction.php'");
	mm_menu_07161417121_0.addMenuItem("Expired Bargain Settlement","location='adminExpiryBargainApproved.php'");
	mm_menu_07161417121_0.fontWeight="bold";
	mm_menu_07161417121_0.hideOnMouseOut=true;
	mm_menu_07161417121_0.bgColor='#FFFFFF';
	mm_menu_07161417121_0.menuBorder=1;
	mm_menu_07161417121_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417121_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417120_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417120_0.addMenuItem("Add Customer","location='adminCustomerAddModifiedfinal.php'");
	mm_menu_07161417120_0.addMenuItem("Edit Customer","location='adminCustomerListingModified.php'");
	mm_menu_07161417120_0.addMenuItem("Add Product","location='adminSKUadd.php'");
	mm_menu_07161417120_0.addMenuItem("Edit Product","location='adminProductListing.php'");
	mm_menu_07161417120_0.addMenuItem("Add Batch wise Stk","location='adminBatchWiseStockPreperation.php'");
	mm_menu_07161417120_0.fontWeight="bold";
	mm_menu_07161417120_0.hideOnMouseOut=true;
	mm_menu_07161417120_0.bgColor='#FFFFFF';
	mm_menu_07161417120_0.menuBorder=1;
	mm_menu_07161417120_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417120_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417125_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417125_0.addMenuItem("Approval Matrix","location='adminApprovalMatrixListing.php'");
	mm_menu_07161417125_0.addMenuItem("Barbain Approval","location='adminBargainApprovedTierwise.php'");
	mm_menu_07161417125_0.fontWeight="bold";
	mm_menu_07161417125_0.hideOnMouseOut=true;
	mm_menu_07161417125_0.bgColor='#FFFFFF';
	mm_menu_07161417125_0.menuBorder=1;
	mm_menu_07161417125_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417125_0.menuBorderBgColor='#DEDEDE'

	/*--------> Customize Report For UCLINDIA <--------*/
	window.mm_menu_0716141867_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141867_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141867_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_0716141867_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141867_0.addMenuItem("Route Plan Approval","location='route_plan_approval.php'");
	mm_menu_0716141867_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141867_0.fontWeight="bold";
	mm_menu_0716141867_0.hideOnMouseOut=true;
	mm_menu_0716141867_0.bgColor='#FFFFFF';
	mm_menu_0716141867_0.menuBorder=1;
	mm_menu_0716141867_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141867_0.menuBorderBgColor='#DEDEDE';
	
	/*--------> Customize Report For PARLE <--------*/
	window.mm_menu_0716141863_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141863_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141863_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_0716141863_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141863_0.addMenuItem("Route Plan Approval","location='route_plan_approval.php'");
	mm_menu_0716141863_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141863_0.addMenuItem("FF Activity Report","location='emp_date_activity_report.php'");
	mm_menu_0716141863_0.fontWeight="bold";
	mm_menu_0716141863_0.hideOnMouseOut=true;
	mm_menu_0716141863_0.bgColor='#FFFFFF';
	mm_menu_0716141863_0.menuBorder=1;
	mm_menu_0716141863_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141863_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141765_0= new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141765_0.addMenuItem("Brokerage report","location='brokerage-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Depot Cost Report","location='depot-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Secondary Freight Report","location='depot-route-freight-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Detention Charges Report","location='detention-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Honeycomb Cost Report","location='honeycomb-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Load Distribution Report","location='load-distribution-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Margin Cost Report","location='margin-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Packing Cost Report","location='packing-data-download-modified.php'");
	mm_menu_0716141765_0.addMenuItem("Primary Freight Report","location='primary-freight-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Sale Rate Report","location='sale-rate-data-download.php'");
	mm_menu_0716141765_0.addMenuItem("Sale Rate Report Details","location='sale-rate-data-download-FOR.php'");
	mm_menu_0716141765_0.addMenuItem("Sauda Limit Report","location='customer_sauda_limit_update_report.php'");
	mm_menu_0716141765_0.fontWeight="bold";
	mm_menu_0716141765_0.hideOnMouseOut=true;
	mm_menu_0716141765_0.bgColor='#FFFFFF';
	mm_menu_0716141765_0.menuBorder=1;
	mm_menu_0716141765_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141765_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417113_0= new Menu("root",210,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417113_0.addMenuItem("Margin Cost Report","location='margin-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Packing Cost Report","location='packing-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Packing Material Costing Sheet","location='packing-costing-report.php'");
	mm_menu_07161417113_0.addMenuItem("Primary Freight Report","location='primary-freight-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Secondary Freight Report","location='depot-route-freight-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Load Distribution Report","location='load-distribution-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Loose Rate Report","location='pricing_input_details_report.php'");
	mm_menu_07161417113_0.addMenuItem("Process Cost Report","location='process-cost-data-download.php'");
	mm_menu_07161417113_0.addMenuItem("Published Rate Report","location='published_rate_details.php'");
	mm_menu_07161417113_0.addMenuItem("Detail Costing Report","location='sale-rate-data-download-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Detail Costing Report MCX","location='mcx-sale-rate-data-download.php'");
	mm_menu_07161417113_0.addMenuItem("Detail Costing Destination Wise","location='sale-rate-data-download-FOR-versionone.php'");
	mm_menu_07161417113_0.addMenuItem("Detail Costing Destination Wise MCX","location='sale-rate-data-download-FOR-mcx.php'");
	mm_menu_07161417113_0.fontWeight="bold";
	mm_menu_07161417113_0.hideOnMouseOut=true;
	mm_menu_07161417113_0.bgColor='#FFFFFF';
	mm_menu_07161417113_0.menuBorder=1;
	mm_menu_07161417113_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417113_0.menuBorderBgColor='#DEDEDE';

	window.mm_menu_0716141798_0= new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141798_0.addMenuItem("Sale Rate Report","location='sale-rate-data-download.php'");
	mm_menu_0716141798_0.addMenuItem("Sale Rate Report Details","location='sale-rate-data-download-FOR.php'");
	mm_menu_0716141798_0.fontWeight="bold";
	mm_menu_0716141798_0.hideOnMouseOut=true;
	mm_menu_0716141798_0.bgColor='#FFFFFF';
	mm_menu_0716141798_0.menuBorder=1;
	mm_menu_0716141798_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141798_0.menuBorderBgColor='#DEDEDE';
	

	/*--------> Customize Report For STAR <--------*/
	window.mm_menu_0716141858_0 = new Menu("root",230,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141858_0.addMenuItem("Order Generation","location='admin_mis_order_register.php'");
	mm_menu_0716141858_0.addMenuItem("Route Plan Approval","location='route_plan_approve.php'");
	mm_menu_0716141858_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_0716141858_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback_customize.php'");
	mm_menu_0716141858_0.addMenuItem("Market Feedback - Price one","location='admin_mis_market_pricing.php'");
	mm_menu_0716141858_0.addMenuItem("Market Feedback - Price two","location='admin_mis_market_pricing_customize.php'");
	mm_menu_0716141858_0.addMenuItem("Market Feedback - Price three","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_0716141858_0.addMenuItem("No Activity Report","location='no_activity_report_star.php'");
	mm_menu_0716141858_0.addMenuItem("Other Reports","location='star_survey_report.php'");
	mm_menu_0716141858_0.addMenuItem("Other Reports Modified","location='star_survey_report_modified.php'");
	mm_menu_0716141858_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_0716141858_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_0716141858_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_0716141858_0.addMenuItem("Tech Dashboard Summary","location='STAR_tech_summary_report.php'");
	mm_menu_0716141858_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_0716141858_0.addMenuItem("DCR Report","location='admin_dcr_report_star.php'");
	mm_menu_0716141858_0.addMenuItem("Yellow Card Date Validation","location='yellow_card_date_validation.php'");
	mm_menu_0716141858_0.addMenuItem("Yellow Card Date Validation Exceptional","location='yellow_card_date_validation_exceptional.php'");
	mm_menu_0716141858_0.addMenuItem("Customer Visit Report Daywise","location='star_customer_visit_report_daywise.php'");
	mm_menu_0716141858_0.addMenuItem("Office Visit Report","location='star_office_plant_visit_report.php'");
	mm_menu_0716141858_0.addMenuItem("Customer Location Edit","location='customer_base_latt_longi_edit.php'");
	mm_menu_0716141858_0.addMenuItem("Branch Geo Fencing Activate","location='branchwise_geo_fencing_activate.php'");
	mm_menu_0716141858_0.addMenuItem("Manual Attendance","location='attendance_manual_intervention.php'");
	mm_menu_0716141858_0.addMenuItem("Attendance download","location='attendance_download_star_modified.php'");
	mm_menu_0716141858_0.addMenuItem("Branch Schemes Summary","location='branchwise_schemes_PDF.php'");
	mm_menu_0716141858_0.addMenuItem("Logistics Activity","location='logistics_checkin_checkout.php'");
	mm_menu_0716141858_0.addMenuItem("Order Access","location='star_order_authorize.php'");
	mm_menu_0716141858_0.addMenuItem("RSSD Running Sheet","location='distributorwise_yellowcard_count.php'");
	mm_menu_0716141858_0.addMenuItem("Unique Visit Report","location='star_unique_visit_report.php'");
	mm_menu_0716141858_0.fontWeight="bold";
	mm_menu_0716141858_0.hideOnMouseOut=true;	
	mm_menu_0716141858_0.bgColor='#FFFFFF';
	mm_menu_0716141858_0.menuBorder=1;
	mm_menu_0716141858_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141858_0.menuBorderBgColor='#DEDEDE';
	
		/*--------> Customize Report For START <--------*/
	window.mm_menu_07161418116_0 = new Menu("root",230,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418116_0.addMenuItem("Order Generation","location='admin_mis_order_register.php'");
	mm_menu_07161418116_0.addMenuItem("Route Plan Approval","location='route_plan_approve.php'");
	mm_menu_07161418116_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_07161418116_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback_customize.php'");
	mm_menu_07161418116_0.addMenuItem("Market Feedback - Price one","location='admin_mis_market_pricing.php'");
	mm_menu_07161418116_0.addMenuItem("Market Feedback - Price two","location='admin_mis_market_pricing_customize.php'");
	mm_menu_07161418116_0.addMenuItem("Market Feedback - Price RSP","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_07161418116_0.addMenuItem("Market Feedback - Price WSP","location='admin_mis_market_pricing_WSP.php'");
	mm_menu_07161418116_0.addMenuItem("No Activity Report","location='no_activity_report_star.php'");
	mm_menu_07161418116_0.addMenuItem("Other Reports","location='star_survey_report_modified.php'");
	mm_menu_07161418116_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_07161418116_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_07161418116_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_07161418116_0.addMenuItem("Tech Dashboard Summary","location='STAR_tech_summary_report.php'");
	mm_menu_07161418116_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_07161418116_0.addMenuItem("DCR Report","location='admin_dcr_report_star.php'");
	mm_menu_07161418116_0.addMenuItem("Yellow Card Date Validation","location='yellow_card_date_validation.php'");
	mm_menu_07161418116_0.addMenuItem("Yellow Card Date Validation Exceptional","location='yellow_card_date_validation_exceptional.php'");
	mm_menu_07161418116_0.addMenuItem("Customer Visit Report Daywise","location='star_customer_visit_report_daywise.php'");
	mm_menu_07161418116_0.addMenuItem("Office Visit Report","location='star_office_plant_visit_report.php'");
	mm_menu_07161418116_0.addMenuItem("Customer Location Edit","location='customer_base_latt_longi_edit.php'");
	mm_menu_07161418116_0.addMenuItem("Branch Geo Fencing Activate","location='branchwise_geo_fencing_activate.php'");
	mm_menu_07161418116_0.addMenuItem("Manual Attendance","location='attendance_manual_intervention.php'");
	mm_menu_07161418116_0.addMenuItem("Attendance download","location='attendance_download_star_modified.php'");
	mm_menu_07161418116_0.addMenuItem("Branch Schemes Summary","location='branchwise_schemes_PDF.php'");
	mm_menu_07161418116_0.addMenuItem("RSSD Running Sheet","location='distributorwise_yellowcard_count.php'");
	mm_menu_07161418116_0.addMenuItem("WhatsApp Notification Report","location='whatsapp_log_PDF.php'");
	mm_menu_07161418116_0.addMenuItem("SIS Report ROE","location='SIS_report_ROE.php'");
	mm_menu_07161418116_0.addMenuItem("SIS Report NE","location='SIS_report_NE.php'");
	mm_menu_07161418116_0.addMenuItem("Unique Visit Report","location='star_unique_visit_report.php'");
	mm_menu_07161418116_0.fontWeight="bold";
	mm_menu_07161418116_0.hideOnMouseOut=true;	
	mm_menu_07161418116_0.bgColor='#FFFFFF';
	mm_menu_07161418116_0.menuBorder=1;
	mm_menu_07161418116_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418116_0.menuBorderBgColor='#DEDEDE';
	

	
		/*mm_menu_0716141847_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141847_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141847_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141847_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141847_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141847_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141847_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141847_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141847_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141847_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	mm_menu_0716141847_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");*/

	
	window.mm_menu_0716141897_0 = new Menu("root",175,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141897_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141897_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141897_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback.php'");
	mm_menu_0716141897_0.addMenuItem("Market Feedback - Price","location='admin_mis_market_pricing.php'");
	mm_menu_0716141897_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141897_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141897_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141897_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	//mm_menu_0716141897_0.addMenuItem("Other Reports","location='star_survey_report.php'");
	//mm_menu_0716141897_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_0716141897_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_0716141897_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");
	mm_menu_0716141897_0.fontWeight="bold";
	mm_menu_0716141897_0.hideOnMouseOut=true;
	mm_menu_0716141897_0.bgColor='#FFFFFF';
	mm_menu_0716141897_0.menuBorder=1;
	mm_menu_0716141897_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141897_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141893_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141893_0.addMenuItem("Order Generation","location='admin_mis_order_register.php'");
	mm_menu_0716141893_0.addMenuItem("Route Plan Approval","location='route_plan_approve.php'");
	mm_menu_0716141893_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Price","location='admin_mis_market_pricing.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Price two","location='admin_mis_market_pricing_customize.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Price three","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Price RSP","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_0716141893_0.addMenuItem("Market Feedback - Price WSP","location='admin_mis_market_pricing_WSP.php'");
	mm_menu_0716141893_0.addMenuItem("No Activity Report","location='no_activity_report_star.php'");
	mm_menu_0716141893_0.addMenuItem("Other Reports","location='star_survey_report.php'");
	mm_menu_0716141893_0.addMenuItem("Other Reports Modified","location='star_survey_report_modified.php'");
	mm_menu_0716141893_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_0716141893_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_0716141893_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_0716141893_0.addMenuItem("Tech Dashboard Summary","location='STAR_tech_summary_report.php'");
	mm_menu_0716141893_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_0716141893_0.addMenuItem("DCR Report","location='admin_dcr_report_star.php'");
	mm_menu_0716141893_0.addMenuItem("Customer Visit Report Daywise","location='star_customer_visit_report_daywise.php'");
	mm_menu_0716141893_0.addMenuItem("Office Visit Report","location='star_office_plant_visit_report.php'");
	mm_menu_0716141893_0.addMenuItem("Order Access","location='star_order_authorize.php'");
	mm_menu_0716141893_0.addMenuItem("RSSD Running Sheet","location='distributorwise_yellowcard_count.php'");
	mm_menu_0716141893_0.addMenuItem("Unique Visit Report","location='star_unique_visit_report.php'");
	mm_menu_0716141893_0.fontWeight="bold";
	mm_menu_0716141893_0.hideOnMouseOut=true;
	mm_menu_0716141893_0.bgColor='#FFFFFF';
	mm_menu_0716141893_0.menuBorder=1;
	mm_menu_0716141893_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141893_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418126_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418126_0.addMenuItem("Order Generation","location='admin_mis_order_register.php'");
	mm_menu_07161418126_0.addMenuItem("Route Plan Approval","location='route_plan_approve.php'");
	mm_menu_07161418126_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Price","location='admin_mis_market_pricing.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Price two","location='admin_mis_market_pricing_customize.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Price three","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Price RSP","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_07161418126_0.addMenuItem("Market Feedback - Price WSP","location='admin_mis_market_pricing_WSP.php'");
	mm_menu_07161418126_0.addMenuItem("No Activity Report","location='no_activity_report_star.php'");
	mm_menu_07161418126_0.addMenuItem("Other Reports","location='star_survey_report.php'");
	mm_menu_07161418126_0.addMenuItem("Other Reports Modified","location='star_survey_report_modified.php'");
	mm_menu_07161418126_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_07161418126_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_07161418126_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_07161418126_0.addMenuItem("Tech Dashboard Summary","location='STAR_tech_summary_report.php'");
	mm_menu_07161418126_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_07161418126_0.addMenuItem("DCR Report","location='admin_dcr_report_star.php'");
	mm_menu_07161418126_0.addMenuItem("Customer Visit Report Daywise","location='star_customer_visit_report_daywise.php'");
	mm_menu_07161418126_0.addMenuItem("Office Visit Report","location='star_office_plant_visit_report.php'");
	mm_menu_07161418126_0.addMenuItem("Order Access","location='star_order_authorize.php'");
	mm_menu_07161418126_0.addMenuItem("RSSD Running Sheet","location='distributorwise_yellowcard_count.php'");
	mm_menu_07161418126_0.addMenuItem("Unique Visit Report","location='star_unique_visit_report.php'");
	mm_menu_07161418126_0.fontWeight="bold";
	mm_menu_07161418126_0.hideOnMouseOut=true;
	mm_menu_07161418126_0.bgColor='#FFFFFF';
	mm_menu_07161418126_0.menuBorder=1;
	mm_menu_07161418126_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418126_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418127_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418127_0.addMenuItem("Order Generation","location='admin_mis_order_register.php'");
	mm_menu_07161418127_0.addMenuItem("Route Plan Approval","location='route_plan_approve.php'");
	mm_menu_07161418127_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Stock","location='admin_mis_market_feedback.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Price","location='admin_mis_market_pricing.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Price two","location='admin_mis_market_pricing_customize.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Price three","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Price RSP","location='admin_mis_market_pricing_customize_active.php'");
	mm_menu_07161418127_0.addMenuItem("Market Feedback - Price WSP","location='admin_mis_market_pricing_WSP.php'");
	mm_menu_07161418127_0.addMenuItem("No Activity Report","location='no_activity_report_star.php'");
	mm_menu_07161418127_0.addMenuItem("Other Reports","location='star_survey_report.php'");
	mm_menu_07161418127_0.addMenuItem("Other Reports Modified","location='star_survey_report_modified.php'");
	mm_menu_07161418127_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_07161418127_0.addMenuItem("Customer Visit Report","location='star_customer_visit_report.php'");
	mm_menu_07161418127_0.addMenuItem("Actionable Report","location='actionable_report.php'");
	mm_menu_07161418127_0.addMenuItem("Tech Dashboard Summary","location='STAR_tech_summary_report.php'");
	mm_menu_07161418127_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_07161418127_0.addMenuItem("DCR Report","location='admin_dcr_report_star.php'");
	mm_menu_07161418127_0.addMenuItem("Customer Visit Report Daywise","location='star_customer_visit_report_daywise.php'");
	mm_menu_07161418127_0.addMenuItem("Office Visit Report","location='star_office_plant_visit_report.php'");
	mm_menu_07161418127_0.addMenuItem("Order Access","location='star_order_authorize.php'");
	mm_menu_07161418127_0.addMenuItem("Attendance download","location='attendance_download_star_modified.php'");
	mm_menu_07161418127_0.addMenuItem("RSSD Running Sheet","location='distributorwise_yellowcard_count.php'");
	mm_menu_07161418127_0.addMenuItem("Unique Visit Report","location='star_unique_visit_report.php'");
	mm_menu_07161418127_0.fontWeight="bold";
	mm_menu_07161418127_0.hideOnMouseOut=true;
	mm_menu_07161418127_0.bgColor='#FFFFFF';
	mm_menu_07161418127_0.menuBorder=1;
	mm_menu_07161418127_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418127_0.menuBorderBgColor='#DEDEDE';
	
	/*window.mm_menu_0716141898_0 = new Menu("root",175,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141898_0.addMenuItem("Route Plan Report","location='star_pjp_report.php'");
	mm_menu_0716141898_0.addMenuItem("Yellow Card","location='yellow_card_report.php'");
	mm_menu_0716141898_0.addMenuItem("Yellow Card Summary","location='yellow_card_excel_report.php'");
	mm_menu_0716141898_0.fontWeight="bold";
	mm_menu_0716141898_0.hideOnMouseOut=true;
	mm_menu_0716141898_0.bgColor='#FFFFFF';
	mm_menu_0716141898_0.menuBorder=1;
	mm_menu_0716141898_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141898_0.menuBorderBgColor='#DEDEDE';*/

	window.mm_menu_0716141847_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141847_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141847_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141847_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141847_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141847_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141847_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141847_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141847_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141847_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141847_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	//mm_menu_0716141847_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");
	mm_menu_0716141847_0.fontWeight="bold";
	mm_menu_0716141847_0.hideOnMouseOut=true;
	mm_menu_0716141847_0.bgColor='#FFFFFF';
	mm_menu_0716141847_0.menuBorder=1;
	mm_menu_0716141847_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141847_0.menuBorderBgColor='#DEDEDE';
	
		window.mm_menu_0716141139_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141139_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141139_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141139_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141139_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141139_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141139_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141139_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141139_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141139_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141139_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	//mm_menu_0716141847_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");
	mm_menu_0716141139_0.addMenuItem("Dealer Visit Feedback","location='dealer_visit_feedback_display.php'");
	mm_menu_0716141139_0.addMenuItem("Business Prospect Export","location='business_prospect_refering_report.php'");
	mm_menu_0716141139_0.addMenuItem("Kilometer Reading Report","location='kilometer_reading_report.php'");
	mm_menu_0716141139_0.fontWeight="bold";
	mm_menu_0716141139_0.hideOnMouseOut=true;
	mm_menu_0716141139_0.bgColor='#FFFFFF';
	mm_menu_0716141139_0.menuBorder=1;
	mm_menu_0716141139_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141139_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_07161418100_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418100_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418100_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418100_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_07161418100_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_07161418100_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418100_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418100_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_07161418100_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	mm_menu_07161418100_0.fontWeight="bold";
	mm_menu_07161418100_0.hideOnMouseOut=true;
	mm_menu_07161418100_0.bgColor='#FFFFFF';
	mm_menu_07161418100_0.menuBorder=1;
	mm_menu_07161418100_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418100_0.menuBorderBgColor='#DEDEDE';
	
		window.mm_menu_0716141899_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141899_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141899_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141899_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141899_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	//mm_menu_0716141899_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141899_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141899_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141899_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141899_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141899_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	//mm_menu_0716141899_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");
	mm_menu_0716141899_0.fontWeight="bold";
	mm_menu_0716141899_0.hideOnMouseOut=true;
	mm_menu_0716141899_0.bgColor='#FFFFFF';
	mm_menu_0716141899_0.menuBorder=1;
	mm_menu_0716141899_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141899_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418106_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418106_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418106_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_07161418106_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_07161418106_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418106_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_07161418106_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418106_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_07161418106_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");
	//mm_menu_07161418106_0.addMenuItem("Market Feedback Stock","location='admin_mis_market_feedback_customize.php'");
	//mm_menu_0716141899_0.addMenuItem("Technical Meet Summary","location='tech_meet_summary.php'");
	mm_menu_07161418106_0.addMenuItem("Market Feedback Summary","location='market_feedback_summary_report.php'");
	mm_menu_07161418106_0.fontWeight="bold";
	mm_menu_07161418106_0.hideOnMouseOut=true;
	mm_menu_07161418106_0.bgColor='#FFFFFF';
	mm_menu_07161418106_0.menuBorder=1;
	mm_menu_07161418106_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418106_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_0716141898_0 = new Menu("root",175,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	/*mm_menu_0716141898_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141898_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141898_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	mm_menu_0716141898_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141898_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141898_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141898_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141898_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_0716141898_0.addMenuItem("Activity Report Details","location='daily_activity_SKIPPER.php'");
	mm_menu_0716141898_0.addMenuItem("Activity Report Summary","location='daily_activity_summary_SKIPPER.php'");*/
	mm_menu_0716141898_0.addMenuItem("Stock Balance Report","location='stock_balance_report_modified.php'");
	mm_menu_0716141898_0.addMenuItem("Stock Out Report","location='stock_out_report.php'");
	mm_menu_0716141898_0.addMenuItem("IMEI Search","location='adminIMEIstatus.php'");
	mm_menu_0716141898_0.addMenuItem("Registered - Activatated IMEI","location='adminactiveIMEIwithsaleout.php'");
	mm_menu_0716141898_0.addMenuItem("Unregistered - Activated IMEI","location='adminactiveIMEIwithoutsale.php'");
	mm_menu_0716141898_0.addMenuItem("Zero Outlet","location='adminZeroOutlet.php'");
	mm_menu_0716141898_0.addMenuItem("Collection Forecast","location='adminCollectionForecast.php'");
	mm_menu_0716141898_0.fontWeight="bold";
	mm_menu_0716141898_0.hideOnMouseOut=true;
	mm_menu_0716141898_0.bgColor='#FFFFFF';
	mm_menu_0716141898_0.menuBorder=1;
	mm_menu_0716141898_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141898_0.menuBorderBgColor='#DEDEDE';

	/*---------------------> Customize Report HALDIRAM <--------------------------------*/
window.mm_menu_0716141855_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141855_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141855_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	mm_menu_0716141855_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	//mm_menu_0716141855_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	mm_menu_0716141855_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_0716141855_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141855_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141855_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141855_0.addMenuItem("Sale Register","location='sale_register.php'");
	//mm_menu_0716141855_0.addMenuItem("New Customer Activation","location='adminNewCustomerUnblocked.php'");
	mm_menu_0716141855_0.fontWeight="bold";
	mm_menu_0716141855_0.hideOnMouseOut=true;
	mm_menu_0716141855_0.bgColor='#FFFFFF';
	mm_menu_0716141855_0.menuBorder=1;
	mm_menu_0716141855_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141855_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161418111_0 = new Menu("root",190,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161418111_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_07161418111_0.addMenuItem("Route Plan Report","location='pjp_report.php'");
	//mm_menu_07161418111_0.addMenuItem("Route Plan Approval","location='route_plan_approval_all.php'");
	//mm_menu_0716141855_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis.php'");
	//mm_menu_07161418111_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_07161418111_0.addMenuItem("Daily Activity Vertical Wise","location='daily_activity_analysis_verticalemp.php'");
	//mm_menu_07161418111_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	//mm_menu_07161418111_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_07161418111_0.addMenuItem("Monthly Activity Vertical Wise","location='monthly_activity_report_verticalwise.php'");
	mm_menu_07161418111_0.addMenuItem("SKU Wise Report","location='monthly_activity_report_productgrpouwise.php'");
	mm_menu_07161418111_0.addMenuItem("Target VS Sale Report","location='target_sale_order_vertcalwise.php'");
	mm_menu_07161418111_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_07161418111_0.addMenuItem("Sale Register","location='sale_register.php'");
	mm_menu_07161418111_0.addMenuItem("Stock Analysis Report","location='stockAnalysisReport.php'");
	mm_menu_07161418111_0.addMenuItem("Stock Analysis Report Value Wise","location='stockAnalysisReportValue.php'");
	mm_menu_07161418111_0.addMenuItem("Order Edit","location='adminOrderEdit.php'");
	mm_menu_07161418111_0.addMenuItem("Closing Stock Report","location='closing_stock_report_verticalwise.php'");
	mm_menu_07161418111_0.fontWeight="bold";
	mm_menu_07161418111_0.hideOnMouseOut=true;
	mm_menu_07161418111_0.bgColor='#FFFFFF';
	mm_menu_07161418111_0.menuBorder=1;
	mm_menu_07161418111_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161418111_0.menuBorderBgColor='#DEDEDE';
	
	/*---------------------> Customize Report MAITHAN <--------------------------------*/
	window.mm_menu_0716141868_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141868_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141868_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_0716141868_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141868_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141868_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141868_0.fontWeight="bold";
	mm_menu_0716141868_0.hideOnMouseOut=true;
	mm_menu_0716141868_0.bgColor='#FFFFFF';
	mm_menu_0716141868_0.menuBorder=1;
	mm_menu_0716141868_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141868_0.menuBorderBgColor='#DEDEDE';
	
	/*---------------------> For MINU <--------------------------------*/
	window.mm_menu_0716141848_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141848_0.addMenuItem("New Customer","location='excel_download_new_customer.php'");
	mm_menu_0716141848_0.addMenuItem("Daily Activity Analysis","location='daily_activity_analysis_optimize.php'");
	mm_menu_0716141848_0.addMenuItem("Collection","location='collection_report_emp_main.php'");
	mm_menu_0716141848_0.addMenuItem("Market Visit Report","location='minu_order_report.php'");
	mm_menu_0716141848_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	mm_menu_0716141848_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141848_0.fontWeight="bold";
	mm_menu_0716141848_0.hideOnMouseOut=true;
	mm_menu_0716141848_0.bgColor='#FFFFFF';
	mm_menu_0716141848_0.menuBorder=1;
	mm_menu_0716141848_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141848_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141842_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141842_0.addMenuItem("Daily Activity Analysis","location='emami_daily_activity_analysis.php'");
	mm_menu_0716141842_0.addMenuItem("Competitor Activity","location='competitor_pricing_report.php'");
	mm_menu_0716141842_0.addMenuItem("Stock Report (Dealers)","location='product_quantity_monthwise.php'");
	mm_menu_0716141842_0.addMenuItem("Outstanding","location='outstanding_ageing_report.php'");
	mm_menu_0716141842_0.addMenuItem("Pending Contract","location='pending_contract_ageing_report.php'");
	//mm_menu_0716141842_0.addMenuItem("Monthly Activity Report","location='monthly_activity_report.php'");
	//mm_menu_0716141842_0.addMenuItem("No Activity Report","location='no_activity_report.php'");
	mm_menu_0716141842_0.fontWeight="bold";
	mm_menu_0716141842_0.hideOnMouseOut=true;
	mm_menu_0716141842_0.bgColor='#FFFFFF';
	mm_menu_0716141842_0.menuBorder=1;
	mm_menu_0716141842_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141842_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141892_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141892_0.addMenuItem("Packing Master","location='packing-data-download.php'");
	mm_menu_0716141892_0.addMenuItem("Pending Contract","location='pending_contract_ageing_report.php'");
	mm_menu_0716141892_0.addMenuItem("Sauda Limit","location='customer_wise_sauda_limit_report.php'");
	mm_menu_0716141892_0.addMenuItem("Outstanding","location='outstanding_ageing_report.php'");
	mm_menu_0716141892_0.fontWeight="bold";
	mm_menu_0716141892_0.hideOnMouseOut=true;
	mm_menu_0716141892_0.bgColor='#FFFFFF';
	mm_menu_0716141892_0.menuBorder=1;
	mm_menu_0716141892_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141892_0.menuBorderBgColor='#DEDEDE';

	/*------Report Menu For EMAMI(Sauda Allocation 'yes')-----------*/
	window.mm_menu_0716141404_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141404_0.addMenuItem("Sauda Report","location='sauda_report_main.php'");
	mm_menu_0716141404_0.addMenuItem("Attendance&nbsp;Tracker","location='adminAttendanceTracker.php'");
	//mm_menu_0716141133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	//mm_menu_0716141133_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	//mm_menu_0716141133_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	//mm_menu_0716141133_0.addMenuItem("Upload Data","location='zipUpload.php'");
	/*mm_menu_0716141133_0.addMenuItem("Mail&nbsp;Management","location='adminSendMail.php'");*/
	mm_menu_0716141404_0.fontWeight="bold";
	mm_menu_0716141404_0.hideOnMouseOut=true;
	mm_menu_0716141404_0.bgColor='#FFFFFF';
	mm_menu_0716141404_0.menuBorder=1;
	mm_menu_0716141404_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141404_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141933_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141933_0.addMenuItem("All Transactions Download","location='adminAllTransactionDataDownload.php'");
	mm_menu_0716141933_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141933_0.addMenuItem("Sale & Purchase Download","location='adminMonthlySaleReportDownload.php'");
	mm_menu_0716141933_0.addMenuItem("Edit Transaction Flag","location='adminEditTransactionFlag.php'");
	mm_menu_0716141933_0.fontWeight="bold";
	mm_menu_0716141933_0.hideOnMouseOut=true;
	mm_menu_0716141933_0.bgColor='#FFFFFF';
	mm_menu_0716141933_0.menuBorder=1;
	mm_menu_0716141933_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141933_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141633_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141633_0.addMenuItem("Add reward","location='add_edit_reward.php'");
	mm_menu_0716141633_0.addMenuItem("Reward Details","location='loyalty_reward_details.php'");
	mm_menu_0716141633_0.fontWeight="bold";
	mm_menu_0716141633_0.hideOnMouseOut=true;
	mm_menu_0716141633_0.bgColor='#FFFFFF';
	mm_menu_0716141633_0.menuBorder=1;
	mm_menu_0716141633_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141633_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141101_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141101_0.addMenuItem("Application Access","location='employee_access_verticalwise.php'");
	mm_menu_0716141101_0.addMenuItem("Allocation Access","location='allocation_access_main.php'");
	mm_menu_0716141101_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible.php'");
	mm_menu_0716141101_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141101_0.addMenuItem("TD Allocation Access","location='TD_allocation_access_main.php'");
	mm_menu_0716141101_0.fontWeight="bold";
	mm_menu_0716141101_0.hideOnMouseOut=true;
	mm_menu_0716141101_0.bgColor='#FFFFFF';
	mm_menu_0716141101_0.menuBorder=1;
	mm_menu_0716141101_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141101_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141172_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141172_0.addMenuItem("Application Access","location='employee_access_verticalwise.php'");
	mm_menu_0716141172_0.addMenuItem("Allocation Access","location='allocation_access_main.php'");
	mm_menu_0716141172_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible.php'");
	mm_menu_0716141172_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141172_0.fontWeight="bold";
	mm_menu_0716141172_0.hideOnMouseOut=true;
	mm_menu_0716141172_0.bgColor='#FFFFFF';
	mm_menu_0716141172_0.menuBorder=1;
	mm_menu_0716141172_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141172_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141173_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141173_0.addMenuItem("Application Access","location='employee_access_verticalwise.php'");
	mm_menu_0716141173_0.addMenuItem("Allocation Access","location='allocation_access_main.php'");
	mm_menu_0716141173_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible.php'");
	mm_menu_0716141173_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141173_0.addMenuItem("TD Allocation Access","location='TD_allocation_access_main.php'");
	mm_menu_0716141173_0.fontWeight="bold";
	mm_menu_0716141173_0.hideOnMouseOut=true;
	mm_menu_0716141173_0.bgColor='#FFFFFF';
	mm_menu_0716141173_0.menuBorder=1;
	mm_menu_0716141173_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141173_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141149_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141149_0.addMenuItem("Unblocked Employee Access","location='adminEmployeeAccess.php'");
	mm_menu_0716141149_0.addMenuItem("Blocked Employee Access ","location='adminEmployeeAccessBlocked.php'");
	mm_menu_0716141149_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141149_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible_other.php'");
	mm_menu_0716141149_0.fontWeight="bold";
	mm_menu_0716141149_0.hideOnMouseOut=true;
	mm_menu_0716141149_0.bgColor='#FFFFFF';
	mm_menu_0716141149_0.menuBorder=1;
	mm_menu_0716141149_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141149_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141123_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141123_0.addMenuItem("Unblocked Employee Access","location='adminEmployeeAccess.php'");
	mm_menu_0716141123_0.addMenuItem("Blocked Employee Access ","location='adminEmployeeAccessBlocked.php'");
	mm_menu_0716141123_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141123_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible_other.php'");
	mm_menu_0716141123_0.addMenuItem("TD Allocation Access","location='TD_allocation_access_main.php'");
	mm_menu_0716141123_0.addMenuItem("Customer Access","location='adminCustomerloginAccess.php'");
	mm_menu_0716141123_0.addMenuItem("Broker Access","location='adminBrokerAccess.php'");
	mm_menu_0716141123_0.addMenuItem("OTP Menu Access","location='menu_access_OTP.php'");
	mm_menu_0716141123_0.fontWeight="bold";
	mm_menu_0716141123_0.hideOnMouseOut=true;
	mm_menu_0716141123_0.bgColor='#FFFFFF';
	mm_menu_0716141123_0.menuBorder=1;
	mm_menu_0716141123_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141123_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141171_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141171_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	mm_menu_0716141171_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141171_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible.php'");
	mm_menu_0716141171_0.fontWeight="bold";
	mm_menu_0716141171_0.hideOnMouseOut=true;
	mm_menu_0716141171_0.bgColor='#FFFFFF';
	mm_menu_0716141171_0.menuBorder=1;
	mm_menu_0716141171_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141171_0.menuBorderBgColor='#DEDEDE';

	
window.mm_menu_0716141167_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141167_0.addMenuItem("Employee Access","location='adminEmployeeAccess.php'");
	mm_menu_0716141167_0.fontWeight="bold";
	mm_menu_0716141167_0.hideOnMouseOut=true;
	mm_menu_0716141167_0.bgColor='#FFFFFF';
	mm_menu_0716141167_0.menuBorder=1;
	mm_menu_0716141167_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141167_0.menuBorderBgColor='#DEDEDE';

	
	/*--------> Menu For STAR<--------*/
	window.mm_menu_0716141156_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141156_0.addMenuItem("Employee Access","location='adminEmployeeAccess_selectionwise.php'");
	mm_menu_0716141156_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141156_0.addMenuItem("Not Accessible Menu","location='menu_access_notaccessible.php'");
	mm_menu_0716141156_0.fontWeight="bold";
	mm_menu_0716141156_0.hideOnMouseOut=true;
	mm_menu_0716141156_0.bgColor='#FFFFFF';
	mm_menu_0716141156_0.menuBorder=1;
	mm_menu_0716141156_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141156_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141150_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141150_0.addMenuItem("Unblocked Employee Access","location='employee_access_verticalwise.php'");
	mm_menu_0716141150_0.addMenuItem("Blocked Employee Access ","location='employee_access_verticalwise_blocked.php'");
	mm_menu_0716141150_0.addMenuItem("MIS Report Access","location='employee_access_backend.php'");
	mm_menu_0716141150_0.fontWeight="bold";
	mm_menu_0716141150_0.hideOnMouseOut=true;
	mm_menu_0716141150_0.bgColor='#FFFFFF';
	mm_menu_0716141150_0.menuBorder=1;
	mm_menu_0716141150_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141150_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141022_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141022_0.addMenuItem("Send Notification","location='adminPushNotification.php'");
	mm_menu_0716141022_0.addMenuItem("Notification Details","location='notification_report_details.php'");
	mm_menu_0716141022_0.fontWeight="bold";
	mm_menu_0716141022_0.hideOnMouseOut=true;
	mm_menu_0716141022_0.bgColor='#FFFFFF';
	mm_menu_0716141022_0.menuBorder=1;
	mm_menu_0716141022_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141022_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161410119_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161410119_0.addMenuItem("Send Notification","location='adminPushNotificationStar.php'");
	mm_menu_07161410119_0.addMenuItem("Notification Details","location='notification_report_details.php'");
	mm_menu_07161410119_0.fontWeight="bold";
	mm_menu_07161410119_0.hideOnMouseOut=true;
	mm_menu_07161410119_0.bgColor='#FFFFFF';
	mm_menu_07161410119_0.menuBorder=1;
	mm_menu_07161410119_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161410119_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141046_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141046_0.addMenuItem("Send Notification","location='pushnotification_vertical_branch_select.php'");
	mm_menu_0716141046_0.addMenuItem("Notification Details","location='notification_report_details.php'");
	mm_menu_0716141046_0.fontWeight="bold";
	mm_menu_0716141046_0.hideOnMouseOut=true;
	mm_menu_0716141046_0.bgColor='#FFFFFF';
	mm_menu_0716141046_0.menuBorder=1;
	mm_menu_0716141046_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141046_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141733_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141733_0.addMenuItem("All Transactions Download","location='CsvDownloadAllTransaction.php'");
	mm_menu_0716141733_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141733_0.addMenuItem("Edit Transaction Flag","location='adminEditTransactionFlagAll.php'");
	mm_menu_0716141733_0.addMenuItem("Order Transaction Download","location='order_download_all.php'");
	mm_menu_0716141733_0.fontWeight="bold";
	mm_menu_0716141733_0.hideOnMouseOut=true;
	mm_menu_0716141733_0.bgColor='#FFFFFF';
	mm_menu_0716141733_0.menuBorder=1;
	mm_menu_0716141733_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141733_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141773_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141773_0.addMenuItem("All Transactions Download","location='CsvDownloadAllTransaction.php'");
	mm_menu_0716141773_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141773_0.addMenuItem("Edit Transaction Flag","location='adminEditTransactionFlagAll.php'");
	mm_menu_0716141773_0.addMenuItem("Order Transaction Download","location='order_download_all_skipper.php'");
	mm_menu_0716141773_0.fontWeight="bold";
	mm_menu_0716141773_0.hideOnMouseOut=true;
	mm_menu_0716141773_0.bgColor='#FFFFFF';
	mm_menu_0716141773_0.menuBorder=1;
	mm_menu_0716141773_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141773_0.menuBorderBgColor='#DEDEDE';

	
	/*----> Download Data <----*/
	window.mm_menu_0716141769_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141769_0.addMenuItem("All Transactions Download","location='CsvDownloadAllTransaction.php'");
	mm_menu_0716141769_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141769_0.addMenuItem("Edit Transaction Flag","location='adminEditTransactionFlagAll.php'");
	mm_menu_0716141769_0.addMenuItem("Order Transaction Download","location='order_download_all_rupa.php'");
	mm_menu_0716141769_0.fontWeight="bold";
	mm_menu_0716141769_0.hideOnMouseOut=true;
	mm_menu_0716141769_0.bgColor='#FFFFFF';
	mm_menu_0716141769_0.menuBorder=1;
	mm_menu_0716141769_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141769_0.menuBorderBgColor='#DEDEDE';
	
	
	window.mm_menu_0716141757_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141757_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141757_0.addMenuItem("Edit Transaction Flag","location='adminEditTransactionFlagAll.php'");
	mm_menu_0716141757_0.addMenuItem("Order Transaction Download","location='order_download_all.php'");
	mm_menu_0716141757_0.fontWeight="bold";
	mm_menu_0716141757_0.hideOnMouseOut=true;
	mm_menu_0716141757_0.bgColor='#FFFFFF';
	mm_menu_0716141757_0.menuBorder=1;
	mm_menu_0716141757_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141757_0.menuBorderBgColor='#DEDEDE';

	window.mm_menu_0716141731_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141731_0.addMenuItem("Attendance Download","location='attendance_download.php'");
    mm_menu_0716141731_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141731_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
		mm_menu_0716141731_0.addMenuItem("Order Transaction Download","location='order_download_all.php'");
	mm_menu_0716141731_0.fontWeight="bold";
	mm_menu_0716141731_0.hideOnMouseOut=true;
	mm_menu_0716141731_0.bgColor='#FFFFFF';
	mm_menu_0716141731_0.menuBorder=1;
	mm_menu_0716141731_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141731_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417102_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417102_0.addMenuItem("Attendance Download","location='attendance_download_modified.php'");
	mm_menu_07161417102_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
	mm_menu_07161417102_0.fontWeight="bold";
	mm_menu_07161417102_0.hideOnMouseOut=true;
	mm_menu_07161417102_0.bgColor='#FFFFFF';
	mm_menu_07161417102_0.menuBorder=1;
	mm_menu_07161417102_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417102_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417128_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417128_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_07161417128_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
	mm_menu_07161417128_0.fontWeight="bold";
	mm_menu_07161417128_0.hideOnMouseOut=true;
	mm_menu_07161417128_0.bgColor='#FFFFFF';
	mm_menu_07161417128_0.menuBorder=1;
	mm_menu_07161417128_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417128_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417133_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417133_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_07161417133_0.addMenuItem("Order Transaction Download","location='order_download_all.php'");
	mm_menu_07161417133_0.fontWeight="bold";
	mm_menu_07161417133_0.hideOnMouseOut=true;
	mm_menu_07161417133_0.bgColor='#FFFFFF';
	mm_menu_07161417133_0.menuBorder=1;
	mm_menu_07161417133_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417133_0.menuBorderBgColor='#DEDEDE';

	/*--> CASHLESS Transaction Download Menu <--*/
	window.mm_menu_0716141762_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141762_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141762_0.fontWeight="bold";
	mm_menu_0716141762_0.hideOnMouseOut=true;
	mm_menu_0716141762_0.bgColor='#FFFFFF';
	mm_menu_0716141762_0.menuBorder=1;
	mm_menu_0716141762_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141762_0.menuBorderBgColor='#DEDEDE';
	
	/*--> Transaction Download Menu STAR <--*/
	window.mm_menu_0716141752_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141752_0.addMenuItem("Attendance Download","location='attendance_download_star.php'");
	mm_menu_0716141752_0.addMenuItem("Order Transaction Download","location='order_download_all.php'");
	mm_menu_0716141752_0.addMenuItem("Survey Download","location='exceldownloadSurvey.php'");
	mm_menu_0716141752_0.fontWeight="bold";
	mm_menu_0716141752_0.hideOnMouseOut=true;
	mm_menu_0716141752_0.bgColor='#FFFFFF';
	mm_menu_0716141752_0.menuBorder=1;
	mm_menu_0716141752_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141752_0.menuBorderBgColor='#DEDEDE';
	

	window.mm_menu_0716141223_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141223_0.addMenuItem("Sauda","location='CsvDownloadSaudaModified.php'");
	mm_menu_0716141223_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141223_0.addMenuItem("Product Promotion","location='csvdownloadProductpromotion.php'");
	mm_menu_0716141223_0.fontWeight="bold";
	mm_menu_0716141223_0.hideOnMouseOut=true;
	mm_menu_0716141223_0.bgColor='#FFFFFF';
	mm_menu_0716141223_0.menuBorder=1;
	mm_menu_0716141223_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141223_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141767_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141767_0.addMenuItem("Sauda Download Report","location='CsvDownloadSaudaModified.php'");
	mm_menu_0716141767_0.addMenuItem("Daily Margin report","location='csvDownloadSaudaReorganized.php'");
	mm_menu_0716141767_0.addMenuItem("Sauda Release layout","location='csvDownloadSaudaLayoutone.php'");
	mm_menu_0716141767_0.addMenuItem("Temporary RA Sauda Download","location='csvDownlodRASauda.php'");
	mm_menu_0716141767_0.fontWeight="bold";
	mm_menu_0716141767_0.hideOnMouseOut=true;
	mm_menu_0716141767_0.bgColor='#FFFFFF';
	mm_menu_0716141767_0.menuBorder=1;
	mm_menu_0716141767_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141767_0.menuBorderBgColor='#DEDEDE';
	
	
		window.mm_menu_0716141261_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141261_0.addMenuItem("Sauda","location='csvdownloadSauda.php'");
	mm_menu_0716141261_0.addMenuItem("Attendance Download","location='adminMonthlyAttendencePrint.php'");
	mm_menu_0716141261_0.addMenuItem("Product Promotion","location='csvdownloadProductpromotion.php'");
	mm_menu_0716141261_0.fontWeight="bold";
	mm_menu_0716141261_0.hideOnMouseOut=true;
	mm_menu_0716141261_0.bgColor='#FFFFFF';
	mm_menu_0716141261_0.menuBorder=1;
	mm_menu_0716141261_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141261_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_0716141833_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141833_0.addMenuItem("Depot&nbsp;Wise","location='adminTransactionDeletion.php'");
	mm_menu_0716141833_0.addMenuItem("Refference&nbsp;No&nbsp;Wise","location='adminDeletionOrderNo.php'");
	mm_menu_0716141833_0.fontWeight="bold";
	mm_menu_0716141833_0.hideOnMouseOut=true;
	mm_menu_0716141833_0.bgColor='#FFFFFF';
	mm_menu_0716141833_0.menuBorder=1;
	mm_menu_0716141833_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141833_0.menuBorderBgColor='#DEDEDE';
	
	
	window.mm_menu_0716141830_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716141830_0.addMenuItem("Create Survey","location='input_layer.php'");
	mm_menu_0716141830_0.addMenuItem("Edit Survey","location='survey_edit.php'");
	mm_menu_0716141830_0.fontWeight="bold";
	mm_menu_0716141830_0.hideOnMouseOut=true;
	mm_menu_0716141830_0.bgColor='#FFFFFF';
	mm_menu_0716141830_0.menuBorder=1;
	mm_menu_0716141830_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141830_0.menuBorderBgColor='#DEDEDE';
	
	/*----> TECPL KIOSK MENU <----*/
	window.mm_menu_0716141869_0 = new Menu("root",185,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141869_0.addMenuItem("Cheque Deposit","location='kiosk_cash_cheque_report.php?type=Cheque%20Deposit'");
    mm_menu_0716141869_0.addMenuItem("Cash Deposit","location='kiosk_cash_cheque_report.php?type=Cash%20Deposit'");
	mm_menu_0716141869_0.fontWeight="bold";
	mm_menu_0716141869_0.hideOnMouseOut=true;
	mm_menu_0716141869_0.bgColor='#FFFFFF';
	mm_menu_0716141869_0.menuBorder=1;
	mm_menu_0716141869_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141869_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_0716141743_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141743_0.addMenuItem("Upload Master Data And Pricing","location='adminCsvReadIncrementalSaudaverticalwise.php'");
	//mm_menu_0716141743_0.addMenuItem("Upload Pending Contract","location='adminCsvReadPendingContract-new.php'");
	mm_menu_0716141743_0.addMenuItem("Upload Pending Contract","location='adminCsvReadPendingContract-new-modified.php'");
	mm_menu_0716141743_0.addMenuItem("Upload Outstanding","location='adminCsvReadOutstanding.php'");
	//mm_menu_0716141743_0.addMenuItem("Upload Pricing Data","location='adminCsvReadPricingGenerationData.php'");
	mm_menu_0716141743_0.addMenuItem("Upload Pricing Data","location='adminCsvReadPricingGenerationDatareconstruct.php'");
	mm_menu_0716141743_0.addMenuItem("Upload RA Sauda","location='adminCsvReadSaudaUpload.php'");
	mm_menu_0716141743_0.fontWeight="bold";
	mm_menu_0716141743_0.hideOnMouseOut=true;
	mm_menu_0716141743_0.bgColor='#FFFFFF';
	mm_menu_0716141743_0.menuBorder=1;
	mm_menu_0716141743_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141743_0.menuBorderBgColor='#DEDEDE';
	
		window.mm_menu_0716141791_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141791_0.addMenuItem("Upload Master Data","location='adminCsvReadIncremental.php'");
	mm_menu_0716141791_0.addMenuItem("Upload Stock Data","location='adminCsvReadStockdata.php'");
	mm_menu_0716141791_0.addMenuItem("Upload Billing Data","location='adminExcelReadBillingDataFinal.php'");
	mm_menu_0716141791_0.addMenuItem("Upload Opening Billing Data","location='adminExcelReadBillingDataOpening.php'");
	mm_menu_0716141791_0.addMenuItem("Upload Stock Return Data","location='adminExcelReadStockReturn.php'");
	mm_menu_0716141791_0.addMenuItem("Upload IMEI Activation Data","location='adminExcelReadActivation.php'");
	mm_menu_0716141791_0.fontWeight="bold";
	mm_menu_0716141791_0.hideOnMouseOut=true;
	mm_menu_0716141791_0.bgColor='#FFFFFF';
	mm_menu_0716141791_0.menuBorder=1;
	mm_menu_0716141791_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141791_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417105_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417105_0.addMenuItem("Upload Master Data","location='adminCsvReadIncremental.php'");
	mm_menu_07161417105_0.addMenuItem("Upload Branch Scheme PDF","location='adminBranchwiseSchemeUpload.php'");
	mm_menu_07161417105_0.addMenuItem("Upload Branch Golden Rules","location='adminBranchwisegoldenrulesupload.php'");
	mm_menu_07161417105_0.addMenuItem("Upload SIS Data","location='adminCsvReadSisData.php'");
	mm_menu_07161417105_0.fontWeight="bold";
	mm_menu_07161417105_0.hideOnMouseOut=true;
	mm_menu_07161417105_0.bgColor='#FFFFFF';
	mm_menu_07161417105_0.menuBorder=1;
	mm_menu_07161417105_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417105_0.menuBorderBgColor='#DEDEDE';


	
	window.mm_menu_0716141797_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141797_0.addMenuItem("Upload Master Data And Pricing","location='adminCsvReadIncrementalSaudaverticalwise.php'");
	mm_menu_0716141797_0.addMenuItem("Upload Pending Contract","location='adminCsvReadPendingContract-new-modified.php'");
	mm_menu_0716141797_0.fontWeight="bold";
	mm_menu_0716141797_0.hideOnMouseOut=true;
	mm_menu_0716141797_0.bgColor='#FFFFFF';
	mm_menu_0716141797_0.menuBorder=1;
	mm_menu_0716141797_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141797_0.menuBorderBgColor='#DEDEDE';

	
	window.mm_menu_0716141763_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_0716141763_0.addMenuItem("Upload Master Data","location='adminCsvReadIncrementalSaudaverticalwise.php'");
	mm_menu_0716141763_0.addMenuItem("Upload Pending Contract","location='adminCsvReadPendingContract-new-modified.php'");
	//mm_menu_0716141763_0.addMenuItem("Upload Pricing Data","location='adminCsvReadPricingGenerationDatareconstruct.php'");
	mm_menu_0716141763_0.addMenuItem("Upload Pricing Data","location='adminCsvReadPricingGenerationDataFinal.php'");
	mm_menu_0716141763_0.addMenuItem("Upload RA Sauda","location='adminCsvReadSaudaUpload.php'");
	mm_menu_0716141763_0.fontWeight="bold";
	mm_menu_0716141763_0.hideOnMouseOut=true;
	mm_menu_0716141763_0.bgColor='#FFFFFF';
	mm_menu_0716141763_0.menuBorder=1;
	mm_menu_0716141763_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716141763_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161417108_0 = new Menu("root",170,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161417108_0.addMenuItem("Upload Master Data","location='adminCsvReadIncremental.php'");
	mm_menu_07161417108_0.addMenuItem("Upload Pricing Data","location='adminPricingComponentPreperation.php'");
	mm_menu_07161417108_0.fontWeight="bold";
	mm_menu_07161417108_0.hideOnMouseOut=true;
	mm_menu_07161417108_0.bgColor='#FFFFFF';
	mm_menu_07161417108_0.menuBorder=1;
	mm_menu_07161417108_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161417108_0.menuBorderBgColor='#DEDEDE';

	window.mm_menu_0716142019_0 = new Menu("root",125,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	//mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Category","location='adminCategory.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Location","location='adminLocation.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Shop","location='adminShop.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Category","location='adminCategory.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Product","location='adminProduct.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Rate","location='adminRate.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Transaction","location='adminProductOrder.php'");
	/*mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Gift&nbsp;Certificate","location='adminGiftCertificate.php'");
	mm_menu_0716142019_0.addMenuItem("Help&nbsp;Center","location='adminHelpCenter.php'");
	mm_menu_0716142019_0.addMenuItem("Newsletter&nbsp;Subscribers","location='adminNewsletter.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Student","location='adminStudent.php'");
	mm_menu_0716142019_0.addMenuItem("Manage&nbsp;Volunteer","location='adminVolunteer.php'");*/
	mm_menu_0716142019_0.fontWeight="bold";
	mm_menu_0716142019_0.hideOnMouseOut=true;
	mm_menu_0716142019_0.bgColor='#FFFFFF';
	mm_menu_0716142019_0.menuBorder=1;
	mm_menu_0716142019_0.menuLiteBgColor='#FFFFFF';
	mm_menu_0716142019_0.menuBorderBgColor='#DEDEDE';
	
	window.mm_menu_07161411015_0 = new Menu("root",155,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);
	mm_menu_07161411015_0.addMenuItem("New","location='tour_fooding_lodging_expenses.php'");
	mm_menu_07161411015_0.addMenuItem("Old","location='tourtravelexpensereport.php'");
	mm_menu_07161411015_0.fontWeight="bold";
	mm_menu_07161411015_0.hideOnMouseOut=true;
	mm_menu_07161411015_0.bgColor='#FFFFFF';
	mm_menu_07161411015_0.menuBorder=1;
	mm_menu_07161411015_0.menuLiteBgColor='#FFFFFF';
	mm_menu_07161411015_0.menuBorderBgColor='#DEDEDE';



	window.mm_menu_0716142232_0 = new Menu("root",137,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);

	mm_menu_0716142232_0.addMenuItem("About&nbsp;Us","location='adminContent.php?content_id=1'");

	mm_menu_0716142232_0.addMenuItem("Contact&nbsp;Us","location='adminContent.php?content_id=2'");

	mm_menu_0716142232_0.addMenuItem("Terms&nbsp;of&nbsp;Use","location='adminContent.php?content_id=3'");

	mm_menu_0716142232_0.addMenuItem("Privacy&nbsp;Policy","location='adminContent.php?content_id=4'");

	mm_menu_0716142232_0.addMenuItem("Due&nbsp;Diligence","location='adminContent.php?content_id=5'");

	mm_menu_0716142232_0.addMenuItem("Donate","location='adminContent.php?content_id=6'");

	mm_menu_0716142232_0.addMenuItem("What&nbsp;is&nbsp;Microfinance","location='adminContent.php?content_id=7'");

	mm_menu_0716142232_0.addMenuItem("How&nbsp;It&nbsp;Works","location='adminContent.php?content_id=8'");

	mm_menu_0716142232_0.addMenuItem("Peace&nbsp;And&nbsp;Microfinance","location='adminContent.php?content_id=9'");

	mm_menu_0716142232_0.addMenuItem("Team","location='adminContent.php?content_id=10'");

	mm_menu_0716142232_0.addMenuItem("Buzz","location='adminContent.php?content_id=11'");

	mm_menu_0716142232_0.addMenuItem("Invite&nbsp;a&nbsp;Friend","location='adminContent.php?content_id=14'");

	mm_menu_0716142232_0.addMenuItem("Student&nbsp;Internship","location='adminContent.php?content_id=15'");

	mm_menu_0716142232_0.addMenuItem("Volunteer","location='adminContent.php?content_id=16'");

	mm_menu_0716142232_0.addMenuItem("Help","location='adminContent.php?content_id=17'");

	mm_menu_0716142232_0.addMenuItem("Message&nbsp;from&nbsp;Founders","location='adminContent.php?content_id=13'");

		

	mm_menu_0716142232_0.fontWeight="bold";

	mm_menu_0716142232_0.hideOnMouseOut=true;

	mm_menu_0716142232_0.bgColor='#FFFFFF';

	mm_menu_0716142232_0.menuBorder=1;

	mm_menu_0716142232_0.menuLiteBgColor='#FFFFFF';

	mm_menu_0716142232_0.menuBorderBgColor='#DEDEDE';



	window.mm_menu_0716142530_0 = new Menu("root",137,20,"Arial, Helvetica, sans-serif",11,"#000000","#FFFFFF","#FFFFFF","#80A537","left","middle",3,0,100,-5,7,true,true,true,0,false,false);

	//mm_menu_0716142530_0.addMenuItem("Report","location='adminSiteIncomeReport.php'");

	//mm_menu_0716142530_0.addMenuItem("Monthly&nbsp;Lend&nbsp;Report","location='#'");

	mm_menu_0716142530_0.fontWeight="bold";

	mm_menu_0716142530_0.hideOnMouseOut=true;

	mm_menu_0716142530_0.bgColor='#FFFFFF';

	mm_menu_0716142530_0.menuBorder=1;

	mm_menu_0716142530_0.menuLiteBgColor='#FFFFFF';

	mm_menu_0716142530_0.menuBorderBgColor='#DEDEDE';

	mm_menu_0716142530_0.writeMenus();
	
	
	
	
} // mmLoadMenus()

//-->


function logout()

{

	if(window.confirm("Are you sure to logout from Admin Panel?"))

	{

		document.frm_logout.submit();

	}

}



// For blinking text                  

window.onerror = null;

var bName = navigator.appName;

var bVer = parseInt(navigator.appVersion);

var NS4 = (bName == "Netscape" && bVer >= 4);

var IE4 = (bName == "Microsoft Internet Explorer" 

&& bVer >= 4);

var NS3 = (bName == "Netscape" && bVer < 4);

var IE3 = (bName == "Microsoft Internet Explorer" 

&& bVer < 4);

var blink_speed=250;

var i=0;

if (NS4 || IE4) 

{

	if (navigator.appName == "Netscape") 

	{

		layerStyleRef="layer.";

		layerRef="document.layers";

		styleSwitch="";

	}

	else

	{

		layerStyleRef="layer.style.";

		layerRef="document.all";

		styleSwitch=".style";

	}

}





function Blink(layerName)

{

	 if (NS4 || IE4) 

	 { 

		 if(i%2==0)

		 {

			 eval(layerRef+'["'+layerName+'"]'+styleSwitch+'.visibility="visible"');

		 }

		 else

		 {

			 eval(layerRef+'["'+layerName+'"]'+styleSwitch+'.visibility="hidden"');

		 }

	 } 

	 if(i<1)

	 {

		 i++;

	 } 

	 else

	 {

		 i--;

	 }

	 setTimeout("Blink('"+layerName+"')",blink_speed);

}

//BLINKING  End -->

