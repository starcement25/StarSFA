<?php
/*

|--------------------------------------------------------------------------

| Application Routes

|--------------------------------------------------------------------------

|

| Here is where you can register all of the routes for an application.

| It's a breeze. Simply tell Laravel the URIs it should respond to

| and give it the controller to call when that URI is requested.

|
Route::get('/', function () {

    return view('welcome');

});*/

Route::get('/', 'LoginController@showLogin');

Route::post('login','LoginController@doLogin');

Route::group(['middleware' => 'usersession'], function () {

  Route::get('dashboard', 'DashboardController@showDashboard');

  Route::resource('branch', 'BranchController');

  Route::post('uploadbranchfile','BranchController@branchUploadFile');

  Route::resource('employee', 'EmployeeController');

  Route::post('uploademployeefile','EmployeeController@employeeUploadFile');

  Route::resource('vartical', 'VarticalController');

  Route::resource('route', 'RouteController');

  Route::post('uploadroutefile','RouteController@routeUploadFile');

  Route::resource('productgroup', 'ProductGroupController');

  Route::resource('productsubgroup', 'ProductSubGroupController');

  Route::resource('productbrand', 'ProductBrandController');

  Route::resource('product', 'ProductController');

  Route::resource('mrp', 'MrpController');

  Route::resource('customer', 'CustomerController');

  Route::get('customer/getCustomerListing', ['as'=>'customer.getCustomerListing','uses'=>'CustomerController@getCustomerListing']);

  Route::resource('customertype', 'CustomerTypeController');

  Route::get('attendance', 'AttendanceController@showtodayattendance');



  Route::get('attendancecalview', 'AttendanceController@showattendancecalview');

  Route::post('attendancecalview', 'AttendanceController@showsearchresult');



  Route::get('routplanreport', 'RouteController@routeplanreports');

  Route::post('routplanreport', 'RouteController@showrouteplanreports');

  Route::post('pjpexportcsv', 'RouteController@pjpexport');



  Route::get('routplanapproval', 'RouteController@routeplanapproval');

  Route::post('routplanapproval', 'RouteController@showrouteplanapproval');



  Route::get('visitanalysis', 'RouteController@showvisitanalysis');

  Route::post('showvisitanalysisreport', 'RouteController@show_visit_analysisreport');



  Route::get('dailyactivityanalysis', 'RouteController@dailyactivityanalysis');

  Route::post('dailyactivityanalysis', 'RouteController@showdailyactivityanalysis');

  Route::post('activityanalysiscsv', 'RouteController@getDailyActivityAnalysisCsv');



  Route::get('montlyactivityanalysis', 'RouteController@montlyactivityanalysis');

  Route::post('showmontlyactivityanalysis', 'RouteController@showmontlyactivityanalysis');

  Route::post('csvmontlyactivityanalysis', 'RouteController@getMonthlyAnalysisReportCsv');



  Route::get('saleregister', 'RouteController@saleregister');

  Route::post('saleregister', 'RouteController@showsaleregister');

  Route::post('csvsaleregister', 'RouteController@csvdownloadsaleregister');



  Route::get('collectionanalysis', 'AnalysisController@collectionanalysis');

  Route::post('showcollectionanalysis', 'AnalysisController@show_collectionanalysis');

  Route::get('invcollectionanalysis', 'AnalysisController@show_invcollectionanalysis');

  Route::get('statelisting', 'AnalysisController@show_statelisting');

  Route::get('districtlisting', 'AnalysisController@show_districtlisting');

  Route::get('hqlisting', 'AnalysisController@show_hqlisting');

  Route::get('designation', 'AnalysisController@show_designationlisting');

  Route::get('emplisting', 'AnalysisController@show_emplisting');



  Route::get('newcustomer', 'AnalysisController@newcustomeranalysis');

  Route::get('newcustomerlist/{id}', 'AnalysisController@newcustomerdetails');

  Route::post('empwisenewcustomer', 'AnalysisController@newcustomeremployeewise');

  Route::get('reverseauctionprice', 'ReverseauctionpriceController@reverseauctionpricegenerate');

  Route::post('reverseauctionsubmit', 'ReverseauctionpriceController@store');

  Route::get('homepage', 'ReverseauctionpriceController@homepageview');

  Route::get('showreleasedrate', 'ReverseauctionpriceController@showreleaserate');

  Route::get('releaseratereport', 'ReverseauctionpriceController@showreleaserate');

  Route::get('windowtime', 'ReverseauctionpriceController@windowtime');

  Route::post('windowtimesubmit', 'ReverseauctionpriceController@storewindowtime');

  Route::get('exportRAbid', 'ReverseauctionpriceController@pjpexportRAbid');
  Route::get('uploadfreight', 'ReverseauctionpriceController@uploadfreight');
  Route::post('uploadfreightsubmit', 'ReverseauctionpriceController@RAfreightupload');
  Route::get('uploadmargin', 'ReverseauctionpriceController@uploadmargin');
  Route::post('uploadmarginsubmit', 'ReverseauctionpriceController@RAmarginupload');
  Route::get('zonelisting', 'ReverseauctionpriceController@show_zonelisting');
  Route::get('logout', 'LoginController@getLogout');
  Route::get('RAcustomerbidreport', 'RACustomerBidReportController@customerbidreports');
  Route::post('RAcustomerbidreport', 'RACustomerBidReportController@showcustomerbidreports');
  Route::get('bidcenterreport', 'RACustomerBidReportController@bidcenterreports');
  Route::post('bidcenterreport', 'RACustomerBidReportController@showbidcenterreports');
  Route::post('bidcenterexportcsv','RACustomerBidReportController@bidcenterexportcsv');
  Route::post('bidstatuschange','RACustomerBidReportController@bidstatuschange');
  Route::post('showreleaseratereport', 'ReverseauctionpriceController@show_relaese_ratereport');
  Route::get('closewindow', 'ReverseauctionpriceController@close_windowtime');
  Route::post('closewindowsubmit', 'ReverseauctionpriceController@close_windowtime_submit');
  Route::get('RAfreightreportdownload', 'ReverseauctionpriceController@RAfreightdownload');
  Route::get('RAmarginreportdownload', 'ReverseauctionpriceController@RAmargindownload');
  Route::post('RAmarginexportcsv','ReverseauctionpriceController@RAmarginexportcsv');
  Route::get('smsreport', 'RACustomerBidReportController@smsreports');
  Route::post('smsreport', 'RACustomerBidReportController@showsmsreports');
  Route::post('smsreportexportcsv','RACustomerBidReportController@smsexportcsv');
  Route::get('uploadconversion', 'ReverseauctionpriceController@uploadconversion');
  Route::post('uploadconversionsubmit', 'ReverseauctionpriceController@RAconversionupload');
   Route::get('RAconversionreportdownload', 'ReverseauctionpriceController@RAconversiondownload');
  Route::post('RAconversionexportcsv','ReverseauctionpriceController@RAconversionexportcsv');

});

Route::group(array('namespace'=>'Store', 'prefix' => 'store'), function()
{

    Route::get('/', 'StoreLoginController@showLogin');

    Route::post('/login','StoreLoginController@doLogin');

    Route::group(['middleware' => 'storesession'], function () {

        Route::get('/dashboard', 'StoreDashboardController@showDashboard');



        Route::get('/createstore', 'StoreForcepowerController@create');

        Route::post('/createuser', 'StoreForcepowerController@Createuser');

        Route::get('/createuser/{userid}', 'StoreForcepowerController@showuser');

        Route::patch('/edituser/{userid}', 'StoreForcepowerController@do_edituser');



        Route::get('/createmenu', 'StoreForcepowerController@createmenu');

        Route::post('/addacemenu', 'StoreForcepowerController@do_addmenu');

        Route::get('/createmenu/{userid}', 'StoreForcepowerController@showmenu');

        Route::patch('/editmenu/{userid}', 'StoreForcepowerController@do_editmenu');



        Route::get('/createproduct', 'StoreForcepowerController@createproduct');

        Route::post('/addproduct', 'StoreForcepowerController@do_product');

        Route::get('/createproduct/{userid}', 'StoreForcepowerController@showproduct');

        Route::patch('/editproduct/{userid}', 'StoreForcepowerController@do_editproduct');





        Route::get('/createorderdetails', 'StoreForcepowerController@createorder');

        Route::post('/addorderdetails', 'StoreForcepowerController@do_orderdetails');

        Route::get('/createorderdetails/{userid}', 'StoreForcepowerController@showorderdetails');

        Route::patch('/editorderdetails/{userid}', 'StoreForcepowerController@do_editorderdetails');



        Route::get('/createsurvayformdetails', 'StoreForcepowerController@createsurvayform');

        Route::post('/addsurvayformdetails', 'StoreForcepowerController@do_survayformdetails');

        Route::get('/createsurvayformdetails/{userid}', 'StoreForcepowerController@showsurvayform');

        Route::patch('/editsurvayformdetails/{userid}', 'StoreForcepowerController@do_editsurvayformdetails');



        Route::get('/createsaudaformdetails', 'StoreForcepowerController@createsaudaform');

        Route::post('/addsaudaformdetails', 'StoreForcepowerController@do_saudaformdetails');

        Route::get('/createsaudaformdetails/{userid}', 'StoreForcepowerController@showsaudaform');

        Route::patch('/editsaudaformdetails/{userid}', 'StoreForcepowerController@do_editsaudaformdetails');



        Route::get('/createrouteplandetails', 'StoreForcepowerController@createrouteplan');

        Route::post('/addrouteplandetails', 'StoreForcepowerController@do_routeplandetails');

        Route::get('/createrouteplandetails/{userid}', 'StoreForcepowerController@showrouteplan');

        Route::patch('/editrouteplandetails/{userid}', 'StoreForcepowerController@do_editrouteplandetails');



        Route::get('/createmarketfeedback', 'StoreForcepowerController@createmarketfeedback');

        Route::post('/addmarketfeedback', 'StoreForcepowerController@do_addmarketfeedback');

        Route::get('/createmarketfeedback/{userid}', 'StoreForcepowerController@storemarketfeedback');

        Route::patch('/editmarketfeedback/{userid}', 'StoreForcepowerController@do_editmarketfeedback');



        Route::get('/createreportconfig', 'StoreForcepowerController@createreport');

        Route::post('/addreportconfig', 'StoreForcepowerController@do_addreportconfig');

        Route::get('/createreportconfig/{userid}', 'StoreForcepowerController@storereportconfig');

        Route::patch('/editreportconfig/{userid}', 'StoreForcepowerController@do_editreportconfig');



        Route::get('/logout', 'StoreLoginController@getLogout');

    });

});



Route::group(['prefix' => 'api/v1','namespace' => 'Api\v1'], function () {

    Route::post('/employeelogin','AuthController@login');

    Route::post('/userdetails','UserdetailsController@userdetailsincremental');

    Route::post('/menudetails','MenudetailsController@menudetailsincremental');

    Route::post('/productdetails','ProductdetailsController@productdetailsincremental');

    Route::post('/orderformdetails','OrderformdetailsController@orderformdetailsincremental');

    Route::post('/surveyformdetails','SurveyformdetailsController@surveyformdetailsincremental');

    Route::post('/saudaformdetails','SaudaformdetailsController@saudaformdetailsincremental');

    Route::post('/routeplandetails','RouteplandetailsController@routeplandetailsincremental');

    Route::post('/marketfeedbackdetails','MarketfeedbackdetailsController@marketfeedbackdetailsincremental');

    Route::post('/datadownloaddictionary','DatadownloadDictionaryController@datadownloaddictionaryincremental');

    Route::post('/customermasteraudit','CustomerMasterController@customermasterincremental');

    Route::post('/routedownload','RouteDownloadController@routedownloadincremental');

    Route::post('/productgroupmaster','ProductGroupMasterController@productgroupmasterincremental');

    Route::post('/productsubgroupmaster','ProductSubGroupMasterController@productsubgroupmasterincremental');

    Route::post('/productbrandmaster','ProductBrandMasterController@productbrandmasterincremental');

    Route::post('/productclstkmaster','ProductClStockController@productclstkmasterincremental');

    Route::post('/productmaster','ProductController@productmasterincremental');

    Route::post('/mrpmaster','MrpMasterController@mrpmasterincremental');

    Route::post('/outstandingmaster','OutstandingMasterController@outstandingmasterincremental');

    Route::post('/outstandingageing','OutstandingAgainMasterController@outstandingmasterincremental');

    Route::post('/customermastercreditlimit','CustomerMasterCreditLimitController@customermastercreditlimitincremental');

    Route::post('/prevstockcountingmaster','PrevStockCountingMasterController@prevstockcountingmasterincremental');

    Route::post('/routeplanmaster','RoutePlanMasterController@routeplanmasterincremental');

    Route::post('/banklistmaster','BankListMasterController@banklistmasterincremental');

    Route::post('/transportmodecategory','TransportModeCategoryController@transportmodecategoryincremental');

    Route::post('/transportmodesubcategory','TransportModeSubCategoryController@transportmodesubcategoryincremental');

    Route::post('/branchmaster','BranchMasterController@branchmastertxt');

    Route::post('/empmaster','EmpMasterController@empmastertxt');

    Route::post('/competitorgroupmaster','CompetitorGroupMasterController@competitorgroupmastertxt');

    Route::post('/prevordercountingmaster','PrevOrderCountingMasterController@prevordercountingmasterincremental');

    Route::post('/orderstatus','OrderStatusController@orderstatusincremental');

    Route::post('/operationdbtransaction','OperationdbTransactionController@operationdbtransactionfuction');



    /*

      Api for TD realtime validation

     */

	  Route::post('/logodownload','LogodownloadController@logodownload');

    Route::post('/tdemployeelogin','AuthController@tdrealtimelogin');
	
	
	
    Route::post('/confirmationdownloadtd','AuthController@updateFirstLoginDateTime');

    Route::post('/tdrealtimedata','TdrealtimevalidationController@tdrealtimevalidation');

    Route::post('/appupdatecheck','AuthController@updateApp');

	  Route::post('/tablestructureTDvalidation','TablestructureTDController@tablestructureTDdownload');

    Route::post('/empmasaterdownload','EmployeeMasterController@downloadempmaster');

    Route::post('/operationdbtdvalidation','TdrealtimevalidationUploadController@operationdbupdatetdvalidation');

	  Route::post('/updateregistrationid','UpdateregidController@Updateregid');

    Route::post('/tddatadownloaddictionary','TDdatadownloaddictionarycontroller@TDdatadownloaddictionary');

	  Route::post('/tdrealtimedataupload','TdrealtimevalidationUploadController@operationdbupdatetdvalidation');

    Route::post('/tdpendingnotification','TdPendingNotificationController@tdpendingnotificationtxt');

	  Route::post('/operationdbnotification','NotificationacknowledgeUploadController@operationdbnotification');



});



Route::group(['prefix' => 'api/v2','namespace' => 'Api\v2'], function () {

  Route::post('/checklogin','AuthController@checklogin');
  Route::post('/verifyotp','AuthController@verifyotp');
  
  Route::post('/userdetails','UserdetailsController@userdetailsincremental');

  Route::post('/mrpmaster','MrpMasterController@mrpmasterincremental');

  Route::post('/logodownload','LogodownloadController@logodownload');

  Route::post('/menudetails','MenudetailsController@menudetailsincremental');

  Route::post('/tdemployeelogin','AuthController@tdrealtimelogin');
  Route::post('/tdemployeeloginv1','AuthController@tdrealtimeloginv1');
  Route::post('/tdemployeeloginv2','AuthController@tdrealtimeloginv2');
  Route::post('/tdrealtimeloginv3','AuthController@tdrealtimeloginv3');
  Route::post('/tdrealtimeloginv4','AuthController@tdrealtimeloginv4');

  Route::post('/confirmationdownloadtd','AuthController@updateFirstLoginDateTime');

  Route::post('/tdrealtimedata','TdrealtimevalidationController@tdrealtimevalidation');

  Route::post('/tdrealtimedatamodified','TdrealtimevalidationControllermodified@tdrealtimevalidation');

  Route::post('/pricerealtimedata','PricerealtimevalidationController@pricerealtimevalidation');

  Route::post('/appupdatecheck','AuthController@updateApp');

  Route::post('/tablestructureTDvalidation','TablestructureTDController@tablestructureTDdownload');

  Route::post('/empmasaterdownload','EmployeeMasterController@downloadempmaster');

  Route::post('/operationdbtdvalidation','TdrealtimevalidationUploadController@operationdbupdatetdvalidation');

  Route::post('/updateregistrationid','UpdateregidController@Updateregid');

  Route::post('/tddatadownloaddictionary','TDdatadownloaddictionarycontroller@TDdatadownloaddictionary');

  Route::post('/tdrealtimedataupload','TdrealtimevalidationUploadController@operationdbupdatetdvalidation');

  Route::post('/tdmodifyrealtimedataupload','TdModifyrealtimevalidationUploadController@operationdbupdatetdvalidation');

  Route::post('/pricerealtimedataupload','pricerealtimevalidationUploadController@operationdbupdatetdvalidation');

  Route::post('/tdpendingnotification','TdPendingNotificationController@tdpendingnotificationtxt');

  Route::post('/operationdbnotification','NotificationacknowledgeUploadController@operationdbnotification');

  Route::post('/RAsaudaratedownload','RASaudaRateDownloadController@saudaratedownload');

  Route::post('/windowtimedownload','windowTimeDownloadController@windowtimedownload');

  Route::post('RAbidrateupload', 'RABidRateUploadController@operationdbuploadbidrate');
  Route::post('RAbidrateuploadV1', 'RABidRateUploadControllerV1@operationdbuploadbidrate');
  Route::post('RAbidrateuploadV2', 'RABidRateUploadControllerV2@operationdbuploadbidrate');
  Route::post('/counterbidratedownload','RACouterBIdRateDownloadController@counterbidratedownload');

  Route::post('/counterbidstatusupload','RACounterBidUploadController@uploadcounterbidstatus');
  Route::post('/RAfreightdownload','RAFreightDownloadController@freightdownload');
  Route::post('/RAfreightdownloadV1','RAFreightDownloadControllerV1@freightdownload');
  Route::post('/acceptbidratedownload','RAAcceptBidRateDownloadController@acceptbidratedownload');
  Route::post('/rejectbidratedownload','RARejectBidRateDownloadController@rejectbidratedownload');
  Route::post('/margindownload','RAMarginDownloadController@margindownloadincremental');
  Route::post('/counterbidacceptdownload','RACounterBidAcceptDownloadController@counterbidacceptdownload');
  Route::post('/detentiondownload','RADetentionDownloadController@detentiondownloadincremental');
});

Route::group(['prefix' => 'api/v3','namespace' => 'Api\v3'], function () {

  Route::post('/saudaformdetails','SaudaformdetailsController@saudaformdetailsincremental');

  Route::post('/saudaallocationaccess','SaudaallocationAccessController@saudaallocationaccess');

  Route::post('/saudaallocationdownload','SaudaallocationDownloadController@saudaallocationdownload');

  Route::post('/saudamrpdownload','SaudamrpDownloadController@saudamrpdownload');

  Route::post('/saudatransactiondownload','SaudatransactionDownloadController@saudatransactiondownload');

  Route::post('/productdownloadincremental','ProductdownloadController@productdownloadincremental');

});



