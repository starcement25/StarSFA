package com.forcepower.acedns.constants;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.graphics.Bitmap;
import android.os.Environment;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.adapter.OrderConfirmAdapterFreebies;
import com.forcepower.acedns.bean.AlocatedSauda;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.BranchWisePdfMaster;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.DestinationMaster;
import com.forcepower.acedns.bean.EmployeeDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.bean.OrderFormDetails;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.RDSDetails;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaProductConversion;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetails;
import com.forcepower.acedns.bean.SurveyFormDetails;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.bean.SurveyMenuDetails;
import com.forcepower.acedns.bean.SurveyStatus;
import com.forcepower.acedns.bean.TentFormDetails;
import com.forcepower.acedns.bean.TentProductList;
import com.forcepower.acedns.bean.VendorDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.bean.emp_sis;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;
import com.forcepower.acedns.bean.BrokerMaster;
import com.forcepower.acedns.bean.CashTransferReceive;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.FsSurveyPublish;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.MarketFeedbackDetails;
import com.forcepower.acedns.bean.MenuDetails;
import com.forcepower.acedns.bean.MenuOutstandingParent;
import com.forcepower.acedns.bean.ProductDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.QuotationDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SaudaAllocation;
import com.forcepower.acedns.bean.SaudaFormDetails;
import com.forcepower.acedns.bean.SplashScreenDetails;
import com.forcepower.acedns.bean.StokistDetails;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.TDAllocation;
import com.forcepower.acedns.bean.UserDetails;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

public class Constants {
    public static String checkInternetConnection = "Please check internet connection.";
    //firebase
    // global topic to receive app wide push notifications
    public static final String TOPIC_GLOBAL = "global";
    // broadcast receiver intent filters
    public static final String REGISTRATION_COMPLETE = "registrationComplete";
    public static final String PUSH_NOTIFICATION = "pushNotification";
    public static final int NOTIFICATION_ID = 100;
    public static final int NOTIFICATION_ID_BIG_IMAGE = 101;
    public static boolean isLARAVELAPI = false;
//    	public static String appMode 		= "development";
    public static String appMode = "production";
    public static String nickName = "" ;

    public static int transitTime = 0;
    public static String currency = "Rs.";
    public static String deliveryDate = "";
    public static String mDBVersion = "0";
    public static String mVerticalValue = "";
    public static int connectionTimeOutFlag = 0;//used to determine how many times should we try to connect to server fo api calls
    public static boolean isFirstLoginOfDay = false;
    public static boolean isFirstLoginOfApp = false;
    public static boolean isSurveyImageTake = false;
    public static boolean isLoginnow = false;
    public static boolean isNewRoute = false;
    public static boolean closeSubActionDialog = false;
    public static ArrayList<SurveyInput> surveyInputListSubActionDialog;
    public static String rowidSubActionDialog;
    public static boolean isDCAOTP = false;
    public static boolean isRESENDOTPREQUEST = false;
    public static boolean isMallSurveyRelationDataAvailable = false;
    public static boolean isOrederToNewCustomer = false;
    public static boolean isCheckInToNewCustomer = false;
    public static String dbDeleteCheckStatus = "";
    public static String mBusinessProspectType = "";//new or existing
    public static String mBusinessProspectCustomerCode = "";
    public static String mBusinessProspectTaggedCustomerCode = "";
    public static String mOrderPriceValidationType = "";//empty=normal flow, DOPS="smart input screen"  SPA=normal flow with aditional api call
    public static String mOrderType = "";
    public static String mFreightComponent = "";
    public static String mFreightComponentAmountFor = "";
    public static String mDestinationCode = "";
    public static String mSurveyRouteCode = "";
    public static String dayOfWeekForCustomer = "";//for storing which days's customers are being shown if route plan is day wise
    public static String mSurveyType = "";
    public static String mSurveyMainType = "";
    public static String mCurrentOrderNoList = "";
    public static String mChosenGstType = "";
    public static String mChosenUomType = "";
    public static String starHelpFileName = "starhelp.pdf";
    public static String goldStoneHelpFileName = "goldstone.pdf";
    public static boolean isDCAtoDCE = false;
    public static String mOTP = null;
    public static String mxcOpenORClose = "";
    public static String currentLat = "";
    public static String currentLong = "";
    public static String locationAccuracy = "";
    public static Boolean doBackToShippingAddressList = false;
    public static Boolean isGettingCurrentLocation = false;
    public static Boolean isCheckedIn = false;
    public static Boolean isVanSales = false;
    public static Boolean isPrimaryFreightIncluded = false;
    public static Boolean isSecondaryFreightIncluded = false;
    public static Boolean isDepotCostIncluded = false;
    public static Boolean isMarginCostIncluded = false;
    public static Boolean submitEnabledFlag = false;//becomes tru if at least one item is selected
    public static int StorageWritePermissionId = 1;
    public static int StorageReadPermissionId = 2;
    public static int LocationPermissionId = 3;
    public static int CameraPermissionId = 4;
    public static int PhonePermissionId = 5;
    public static int ContactPermissionId = 6;
    public static String StorageWritePermissionString = "storage write";
    public static String StorageReadPermissionString = "storage read";
    public static String LocationPermissionString = "location";
    public static String CameraPermissionString = "camera";
    public static String PhonePermissionString = "phone";
    public static String ContactPermissionString = "contacts";
    public static String CurrentOrderCollectionTransactionType = "SO";//direct or telephonic--default direct
    public static String maxLiquidationDiscountForCurrentCustomer = "0";//allowed max liquidation discount for sauda
    public static String selectedVerticalOfUser = "";//allowed max liquidation discount for sauda
    public static String attendanceFilterString = "Please give Attendance first";
    public static double maxallocation = 0.0;
    public static double remainingAllocation = 0.0;
    //public static String mBusinessName="";
    public static String CurrentOrderTransactionId = "";
    public static String CurrentMinimumStockForCustomer = "0";
    public static String currentSiteValueForEdit = "";
    public static String OrderTransactionTaskCalledFrom = "";
    public static String expenseOrReceivedOrPaidAlias = "";
    public static String taggedCustomerCode = "";
    public static String uomToBeShownForProduct = "uom1";
    public static String surveyTableIdValue = "";
    public static String surveyTableIdColumnName = "";
    public static String doBargainNo = "";
    public static String doNo = "";
    public static String doAmount = "";
    public static String doDate = "";
    public static String timeVal = "";
    public static String orderTypePrimarySeconder = "";
    public static DecimalFormat defaultFormatWithComma = new DecimalFormat("#,##,###.00");
    public static DecimalFormat defaultFormat = new DecimalFormat("0.00");
    public static DecimalFormat defaultFormat3 = new DecimalFormat("0.000");
    public static int mNoOfCapture = 0;
//											 "\n2. RDS wise Route selection." +
//											 "\n3. New Option Menu" +
//											 "\n4. UOM Feature in Order." ;
//											 "\n5. " ;
//											 "\n6. UOM Implementation." +
//											 "\n7. Implementation of VAT." +
//											 "\n8. Multiple remarks feature" +
//											 "\n9. Loading and Freight" +
//			                                 "\n10. Bug Fixes of last version." ;
    //	public static String date = "";
    public static String dateString = "";//20150302 --yyyyMMdd
    public static String marginPoNo = "";
    public static String bargainNarration = "";
    public static String deviceId = "";
    public static EmployeeDetails employeeDetailObject;
    public static Bitmap logoBmp;
    //public static Bitmap logoBmp1;
    public static String updateFeatureData = "1. Existing bug fixes.";
    //	public static String appDataPath = Environment.getExternalStorageDirectory().getAbsolutePath()+"/.AceDnsDB/";//hidden
    public static String appDataPath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/AceDnsDB/";
//    public static String appDataPath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/";
    /*
     * DATABASE STRUCTURE DATA
     */
    public static ArrayList<String> downloadTableList= new ArrayList<>();
    public static ArrayList<String> taggedProducts;
    public static ArrayList<String> checkedHintItems;
    public static ArrayList<BranchWisePdfMaster> goldenRulesFileList =null;
    public static ArrayList<Location> unUploadedTransactionGlobal=null;
    /*
     * SETUP TABLE DATA
     */

    public static MenuDetails menuDetailsObj;
    public static MarketFeedbackDetails marketFeedbackDetailsObj;
    public static MarketFeedbackDetails marketFeedbackDetailsObjNewRoute;
    public static UserDetails userDetailsObj;
    public static OrderFormDetails orderFormDetailsObj;
    public static OrderFormDetails orderFormDetailsObjFixed;//this setup is needed because I might overwrite the orderFormDetailsObj setup on certain conditions
    public static SaudaFormDetails saudaFormDetailsObj;
    public static SelfAppraisalDetails selfAppraisalDetails;
    public static SurveyFormDetails surveyFormDetailsObj;
    public static ProductDetails productDetailsObj;
    public static RoutePlanMasterDetails routeDetailsObj;
    public static String weightage="n";
    public static String ilsRemark="n";
    public static String mCheckInOutTimeSurvey ="";
    public static String ilsRemarkOther="n";
    public static String sis_emp_data_startTarget="n";
    public static ArrayList<emp_sis> empSisList= new ArrayList<>();

    public static String deleverySuccessMsg = "Data Submitted Successfully" ;
    public static String deleveryFailedMsg = "Due to Poor Connectivity...The Transaction has not been posted @Server" +
            "...Will post in next Transaction....";
    public static String EMAMIMSGBODY = "Thanks for taking interest in Emami Healthy %26 Tasty Rice Bran Oil, enriched with vitamin A, D %26 E and rich in Oryzanol. Enjoy Healthy %26 Tasty food.";//"Thanking you for participating in Healthy %26 Tasty Rice Bran oil sampling";
    public static String EMAMIMSGRECEIPENT = "";
    public static String LIPLMSGBODY1 = "Thank you for your time. Your OTP is ";
    public static String LIPLMSGBODY2 = " Please provide this to our executive to complete your registration with LIPL. Visit laranyainfoedge.com for more";
    public static String LIPLMOBILENO = "";
    public static String SurveyRowID = "";
    public static String DownloadErrorMsg = "";
    public static String catalogueorSchemeVal = "";
    public static String SchemeBranchName = "";
    public static String SchemeBranchCode = "";
    public static String currentTdPercent = "";
    public static String addFreight = "0";
    public static String minusFreight = "0";
    //	public static String expectedDeviceTimeZone="GMT+05:30";
    public static String expectedDeviceTimeZone = "Asia/Phnom_Penh";//combodia
    //	public static String expectedDeviceTimeZone="Asia/Kolkata";//india
    public static ListView prodQtyRateListView;
    public static ListView retailerRequisitionList;
    public static ExpandableListView expListView;
    public static ProgressDialog mProgressDialog;
    /*
     * Order Activity
     */
    public static boolean isQOIEOTP = false;
    public static boolean isQOIEVERIFICATION = false;
    public static boolean shouldUpdateCustomerMasterWithEmail = false;
    public static String QOIEVERIFICATIONStatus = "";
    public static EditText qtySrate = null;
    public static TextView carttv = null;
    public static TextView remainingAllocationTV = null;
    public static double totalTa= 0.0;
    public static double totalDa= 0.0;
    public static double totalTaDa= 0.0;
    public static double totalDoQty= 0.0;
    public static TextView remainingDOLimit = null;
    public static Boolean isQtySelected = false;
    public static Boolean isTdSelected = false;
    public static Boolean isStkQtySelected = false;
    public static boolean isFromConfirmationActivity = false;
    public static ArrayList<ProductMasterDetails> zeroOutletMasterList;
    public static ArrayList<SplashScreenDetails> splashScreenDetailsList;
    public static ArrayList<ProductMasterDetails> selectedProductMasterListFreebies;
    public static ArrayList<SchemeFreebiesDetails> selectedProductMasterListSchemeOffers;
    public static ArrayList<ProductMasterDetails> selectedProductMasterList;
    public static ArrayList<ProductMasterDetails> selectedProductMasterListTemp;
    public static ArrayList<ProductMasterDetails> selectedProductStockReturn;
    public static ArrayList<MenuOutstandingParent> selectedForecastList;
    public static ArrayList<ProductMasterDetails> selectedProductMasterListStockAudit;
    public static ArrayList<SaudaDetails> selectedSaudaDetailsList;
    public static ArrayList<AlocatedSauda> selectedAlocatedSaudaList;
    public static ArrayList<ProductGroupDetails> selectedGroupList;
    public static ArrayList<ProductSubGrpDetails> selectedSubGroupList;
    public static ArrayList<ProductBrandDetails> selectedBrandList;
    public static ArrayList<ProductMasterDetails> nameValuesProductList;
    public static ArrayList<ProductMasterDetails> tempProductList;
    public static ArrayList<CustomerDetails> tempStokistRetailList;
    public static ArrayList<StokistDetails> tempStokistVisitlList;
    //	public static ArrayList<SchemeFreebiesDetails> schemeListForCurrentProducts;
    public static HashMap<String, ArrayList<SchemeFreebiesDetails>> schemeListForCurrentProducts;
    public static ArrayList<commonDatabaseHelper> publishRateList;
    public static ArrayList<SchemeFreebiesDetails> schemeListForProductGroups;
    public static ArrayList<MarketFeedback> selectedFeedBackList;
    public static ArrayList<SaudaAllocation> mSaudaAllocationList;
    public static ArrayList<TDAllocation> mTDAllocationList;
    public static ArrayList<SaudaProductConversion> mSaudaProductConversionList;
    public static ArrayList<SurveyDetails> mFinalSurveyList;
    public static ArrayList<QuotationDetails> QuotationDetailsList;
    public static ArrayList<MarketFeedbackStockAudit> mMarketFeedbackStockAuditList;
    public static ArrayList<CashTransferReceive> UnVerifiedCashReceiveList;
    public static ArrayList<BillingInformationStockSummaryData> retailerStockOutProductList;
    public static ArrayList<BillingInformationStockSummaryData> retailerStockOutProductListByCustomer;
    public static ArrayList<CustomerDetails> customerListRA;
    public static ArrayList<BillingInformationStockSummaryData> retailerStockOutProductIemiListByCustomer;
    //public static ArrayList<UserAccessDetails> mUserAccessDetailsList;
    public static ArrayList<BillingInformationStockSummaryData> retailerStockOutProductListStockOut;
    public static Dialog retailerStockOutmasterDialog;
    public static OrderConfirmAdapterFreebies freeBiesAdapter;
    public static SchemeFreebiesDetails schemeOnTotalOrder;
    public static ArrayList<SurveyStatus> mSurveyStatusList;
    public static boolean isSelectCustomer = true;
    public static boolean shouldAskForOTP = false;
    public static boolean isAddingAnotherOffer = false;
    public static String mMallorHighStreetID = "";
    public static String currentSelectedOutlet = "";
    public static String mSelectedType = "";
    public static String mSurveyId = "";
    public static String attendanceAlias = "A";
    public static String mType = "";
    public static String HoneyCombCost = "0.00";
    public static String marginCost = "0.00";
    public static String mSaudaDepoCode = "";
    public static String directOrBroker = "direct";
    public static String BrokerageCost = "0.00";
    public String mSaudaType = "";
    public static String mDepotOrPlant = "Depot";
    public static String orderAuditType = "";//primary-distributor order, secondary- retailer order
    public static String stockOutTypeRetailerApp = "";
    public static String stockOutProdGroupRetailerApp = "";
    public static String imei = "";
    public static String selectedFY = "";
    public static ArrayList<String> schemesListCurrentProdCode;
    public static CustomerDetails selectedCustomer;
    public static RouteDetails selectedRouteWise;
    public static RoutePlanMasterDetails selectedRouteRecommended;
    public static ArrayList<String> selectedAllRouteForCurrentDistributor;
    public static RoutePlanMasterDetails selectedRouteadditional;
    public static RoutePlanMasterDetails selectedRouteNew;
    public static CustomerDetails selectedCustomerRecommended;
    public static CustomerDetails selectedCustomeradditional;
        public static CustomerDetails selectedCustomerNew;
    public static CustomerDetails selectedDistributorsRecommended;
    public static CustomerDetails selectedDistributoradditional;
    public static CustomerDetails selectedDistributorNew;
    public static ArrayList<CustomerDetails> allCustomersWithLogic;
    public static ArrayList<CustomerDetails> allNewCustomers;
    public static ArrayList<CustomerDetails> allCustomersWithoutLogic;
    public static ArrayList<CustomerDetails> allRecommendedDistributorsWithLogic;
    public static ArrayList<CustomerDetails> allAdditionalDistributorsWithLogic;
    public static ArrayList<CustomerDetails> allNewDistributors;
    public static ArrayList<RoutePlanMasterDetails> allRecommendedRoutesWithLogic;
    public static ArrayList<RoutePlanMasterDetails> allAdditionalRoutesWithLogic;
    public static ArrayList<RoutePlanMasterDetails> allNewRoutes;
    public static ArrayList<RoutePlanMasterDetails> DistWiseRecommendedRoutesWithLogic;
    public static ArrayList<RoutePlanMasterDetails> DistWiseAdditionalRoutesWithLogic;
    public static String allRecommendedCustomersCodeWithLogic="";
    public static String allAdditionalCustomersCodeWithLogic="";
    public static String allNewCustomersCode="";
    public static ArrayList<CustomerDetails> allRecommendedCustomersWithLogic;
    public static ArrayList<CustomerDetails> allAdditionalCustomersWithLogic;
    public static ArrayList<CustomerDetails> allRecommendedCustomersWithLogicRoute;
    public static ArrayList<CustomerDetails> allAdditionalCustomersWithLogicRoute;
    public static ArrayList<CustomerDetails> allNewCustomersForChosenRoute;
    public static CustomerDetails selectedSSOfCustomer;
    public static EmployeeMasterDetails selectedEmp;
    public static RoutePlanMasterDetails selectedRoute;
    public static CustomerDetails selectedCustomerDeliveryAddress;
    public static String selectedMonth;
    public static String selectedMonthTarget="";
    public static String selectedMonthAchv="";
    public static String selectedEmpCode;
    public static SaudaDetails selectedBargain;
    public static ArrayList<SaudaDetails> selectedBargainList;
    public static ProductMasterDetails selectedProductOfBargain;
    public static CustomerDetails selectedCheckINCustomer;
    public static BranchMasterDetails selectedBranch;
    public static BranchMasterDetails selectedBranchForMrp;
    public static String selectedState;
    public static BrokerMaster selectedBroker;
    public static DestinationMaster selectedDestination;
    public static MallMaster selectedMallMaster;
    public static FsSurveyPublish selectedFsSurveyPublish;
    public static ArrayList<BrokerMaster> mBrokerMasterList;
    public static RoutePlanMasterDetails oderToCustomerRoute;
    public static String selectedEmpRetailerApp;
    public static VendorDetails selectedVendor;
    public static RDSDetails selectedRDS;
    public static ArrayList<String> SelectedSkuCode;
    public static String stockSender = "";
    public static String currentPrinterIp = "";//storing ip of the printer found from network
    public static String[] mEmployeeList;
    public static String[] mSurveyLayoutList;
    public static String[] mProductGrpouList;
    public static String[] mStreetList;
    public static String[] mPincodeList;
    public static String[] mVerticalValueList;
    public static String[] mGeneralFacilityList;
    public static ArrayList<SurveyMenuDetails> mSurveyMenuDetailsList;
    public static ArrayList<String> sendColumnDataForSurveyCheckBox;
    public static ArrayList<commonDatabaseHelper> priceListOilGrpFinalList;
    /*
     * Collection Activity
     */
    public static ArrayList<OutstandingDetails> selectedOutstandingList;
    public static ArrayList<String> allocatedRouteCodeTodayCrm;
    public static ArrayList<PlantProductWiseRARate> plantListForRANewBid;
    public static ArrayList<ProductGroupDetails> productGroupMasterListForNewBid;
    public static ArrayList<PlantProductWiseRARate> plantListForRANewBidCopy;
    public static ArrayList<PlantProductWiseRARate> CounterBidReportListListForRA;
    public static ArrayList<PlantProductWiseRARate> CounterBidReportListListForRACustomers;
    public static ArrayList<PlantProductWiseRARate> CounterBidListListForRAOnToday;
    public static ArrayList<PlantProductWiseRARate> CounterBidListListForRAOnTodayByCustomerCode;
    public static ArrayList<CustomerDetails> customerDetailsListReverseAuction;
    public static ArrayList<String> customerListForRANewBid;
    public static ArrayList<String> allocatedRouteNameTodayCrm;
    /*
     * Incremental Data download in case of DROP/CREATE TABLE
     */
    public static Date transactionStartTime;//get current time when clicked on order/collection/stock audit etc
    public static Date transactionEndTime;//get current time when clicked on submit order/collection/stock audit etc
    public static Date dictDownldStartTime;
    public static Calendar dictDownldSrverTime;
    public static boolean isMenuDetailsUpdated = false;
    public static boolean isEmpMasterLoginUpdated = false;
    public static boolean isMarketFeedbackDetailsUpdated = false;
    public static boolean isOrderFormDetailsUpdated = false;
    public static boolean isProductDetailsUpdated = false;
    public static boolean isUserDetailsUpdated = false;
    public static boolean isRoutePlanDetailsUpdated = false;
    public static boolean isSaudaFormDetailsUpdated = false;
    public static boolean isSurveyFormDetailsUpdated = false;
    public static boolean isLoadDistributionTableUpdated = false;
    public static boolean isBranchRouteFreightTableUpdated = false;
    public static boolean isBankTableUpdated = false;
    public static boolean isAttendanceReportTableUpdated = false;
    public static boolean isClosingStockUpdated = false;
    public static boolean isCustomerTableUpdated = false;
    public static boolean isSteLeadConversionMasterUpdated = false;
    public static boolean isMrpTableUpdated = false;
    public static boolean isFacilitatorTableUpdated = false;
    public static boolean isSiteTableUpdated = false;
    public static boolean isSurveyPublishTableUpdated = false;
    public static boolean isOfferPublishTableUpdated = false;
    public static boolean isFsSurveyPublishTableUpdated = false;
    public static boolean isSaudaMrpTableUpdated = false;
    public static boolean isCustomerProductTableUpdated = false;
    public static boolean isCustomerProductInfoTableUpdated = false;
    public static boolean isMcxRateTableUpdated = false;
    public static boolean isOrderApprovalTableUpdated = false;
    public static boolean isBranchDestinationTableUpdated = false;
    public static boolean isBranchDumpTableUpdated = false;
    public static boolean isCustomerBrokerRelationTableUpdated = false;
    public static boolean isBrokarageCostTableUpdated = false;
    public static boolean isDepotCostTableUpdated = false;
    public static boolean isBargainTransactionTableUpdated = false;
    public static boolean isFreightCostTableUpdated = false;
    public static boolean isPrevStockCountingUpdated = false;
    public static boolean isProductBrandTableUpdated = false;
    public static boolean isProductGroupTableUpdated = false;
    public static boolean isProductTableUpdated = false;
    public static boolean isStockBalanceReportTableUpdated = false;
    public static boolean isCustomerProductBillingUpdated = false;
    public static boolean isdealerTransactionUpdated = false;
    public static boolean isSurveyTableUpdated = false;
    public static boolean isSelfAppraisalDetailsTableUpdated = false;
    public static boolean isSelfAppraisalCustomerWiseTableUpdated = false;
    public static boolean isSelfAppraisalBranchWiseTableUpdated = false;
    public static boolean isProductSubGroupTableUpdated = false;
    public static boolean isRDSTableUpdated = false;
    public static boolean isRouteTableUpdated = false;
    public static boolean isDistributorRouteTableUpdated = false;
    public static boolean isLoyaltyTableUpdated = false;
    public static boolean isMISTransactionTableUpdated = false;
    public static boolean isSampleMasterTableUpdated = false;
    public static boolean isGITTableUpdated = false;
    public static boolean dataResfresh = false;
    public static boolean stockDataRefresh = false;
    public static boolean loyaltyDataRefresh = false;
    public static boolean transactionDelete = false;
    public static boolean transDeleteAndRefresh = false;
    public static boolean isLoyaltyPurchaseUpdated = false;
    public static boolean isCustBranchUpdated = false;
    public static boolean isShowValueSendValueDifferentForChcekBOx = false;
    public static boolean isCurrentCheckBoxItemDependant = false;
    public static String CurrentCheckBoxItemDependantOn = "";
    public static String incoTermsOfCurentCustomer = "";
    public static boolean isBrokerUpdated = false;
    public static boolean isNonTradeCustomer = false;
    public static boolean isBranchUpdated = false;
    public static boolean isHoneyCombUpdated = false;
    public static boolean isMarginCostUpdated = false;
    public static boolean isDestinationUpdated = false;
    public static boolean isSchemePdfMasterUpdated = false;
    public static boolean isSchemePdfMasterWithoutBranchUpdated = false;
    public static boolean isBranchWiseGoldenRuleMasterUpdated = false;
    public static boolean isEmployeeUpdated = false;
    public static boolean isSaudaAllocationUpdated = false;
    public static boolean isSaudaTransactionUpdated = false;
    public static boolean retailerAppisLowerMostLevelEmp = false;
    public static boolean isTargetTableUpdated = false;
    public static commonDatabaseHelper totalData;

//	public static boolean isRDSUpdated = false;
    public static boolean isStreetUpdated = false;
    public static boolean isPreviousOrderCounting = false;
    public static boolean isFarmerMaster = false;
    public static boolean isOrderStatus = false;
    public static boolean isRouteCustomer = false;
    public static boolean isTableView = false;
    public static boolean isDownLoadComplete = true;
    public static boolean isCommpleteDownLoadComplete = true;
    /*
     *   For App Data back up from Menu Screen
     */
    public static boolean isDataRefreshed = false;
    public static boolean isDbCheckinStatus = false;
    public static boolean isDCAImageExist = false;
    public static View selectedItemView = null;
    // id to handle the notification in the notification tray
    public static int REVERSE_AUCTION_FLAG = 0;//0=reports;1=reports,new bid;2=reports, counter bid
    public static String Registration_id_firebase = "";
    public static int CUSTOMER_INFO_FLAG = 0;//0=recommended,1=additional
    public static int CUSTOMER_INFO_REPORTS_FLAG = 0;//0
    public static String OTP_ID = "";
    //Check LOGIN or NOT
    public static String login_status = "";
    public static String receive_notification = "";
    public static String notification_id = "";
    public static String notification_message = "";
    public static String notification_sender_id = "";
    public static String notification_ack_id = "";
    public static String notification_type = "";
    public static String cashTransferOrReceive = "";//CT or CR
    public static String cashTransferIdForReceive = "";
    public static boolean masterApiCallingFlag = true;//if true, I am calling the api from activity download status else downloading from other place like on menu click
    public static boolean masterApiCallingFlagFromReport = false;//if true, I am calling the api from activity download status else downloading from other place like on menu click
    public static double alreadyReceivedAmount = 0, transferredAmount, totalAmountCollection,totalOrderAmount=0.00;
    public static boolean SchemeCheckedBeforeSubmittingOrder = true;

    //CRM call recorder related variables

    public static boolean isCallingFromApp = false;
    public static boolean isCallRecordingStarted = false;
    public static String currentCustomerCode = "";
    public static String currentCustomerNumber = "";
    public static String recordedCallDuration = "";
    public static String currentRecordedFileName = "";
    public static String currentWindowStartsAt = "";
    public static String currentWindowClosesAt = "";
    public static String lodabilityToneFORDEPOT = "";
    public static String retailerappTransactionReason = "sendDataAfterTransaction";//sendDataAfterTransaction,sendDataBeforeStockOut,sendDataBeforeReport
    public static String retailerappReportType = "stockreport";//stockreport or activationreport
    public static String retailercareOrderOrAudit = "both";//both or order or audit
    public static Date recordStartTime;
    public static Date recordEndTime;

    public static ArrayList<OrderReportDetails> mOrderReportDetailsListGlobal;
    public static List<String> listDataHeaderGlobal;
    public static HashMap<String, List<OrderReportDetails>> listChildDataGlobal;
    public static String mode="";
    public static String from_date ="";
    public static String to_date ="";
    public static String customerCode ="";
    public static ArrayList<TentProductList> tentExistingProductList;
    public static String tentFormIDCode ="";
    public static String tentFormStratImage ="";
    public static String tentFormEndImage ="";
    public static String tentFormStratDateTime ="";
    public static String knoFormIDCode ="";
    public static String knoFormStratImage ="";
    public static String knoFormEndImage ="";
    public static String knoFormStratDateTime ="";
    public static String telecallerSelectedId ="";
    public static String businessProspectCheckIn = "";
    public static String[] s = {""};
    public static ArrayList<TentFormDetails> demoFormCust = new ArrayList<>();
    public static ArrayList<TentFormDetails> telecallerFormCust = new ArrayList<>();


    public static boolean isMenuAccessDownloaded         		= false;
    public static boolean isProductMasterUpdated 	    		= false;
    public static boolean isRouteMasterUpdated       			= false;
    public static boolean isProductRelationDownloaded 			= false;

    public static boolean isVehicleDataDownloaded 			= false;
    public static String orderRemarks = "";
    public static String remainderFlg = "";
    public static String sylWeidth = "";
    public static String sylHight = "";

    public static boolean isComplaintMasterTableUpdated = false;
    public static boolean isLeadGenerationMasterTableUpdated = false;
    public static boolean isQualityComplaintMasterTableUpdated = false;
    public static boolean isMtlTestingMasterTableUpdated = false;

    public static boolean isDeveloperOn = false;
    public static ArrayList<String> productType;
    public static ArrayList<MarketFeedbackStockAudit> competitorPoductType;

    public static String cust_emp_selected = "";
    public static String isUpcoming = "";


    public static void print_Log_d(final String key, final String val) {
        if (BuildConfig.DEBUG)
            Log.d(key, val);
    }
}

