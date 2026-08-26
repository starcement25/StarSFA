package com.forcepower.acedns.activity.non_auth.main;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.res.AssetFileDescriptor;
import android.content.res.AssetManager;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.ColorDrawable;
import android.location.LocationManager;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.provider.MediaStore;
import android.provider.Settings;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.FrameLayout;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.SimpleAdapter;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.ActivittyOrderStatus;
import com.forcepower.acedns.activity.ActivityBargainFilter;
import com.forcepower.acedns.activity.ActivityDOFilter;
import com.forcepower.acedns.activity.ActivityEmployeeTargetAcheivement;
import com.forcepower.acedns.activity.ActivityGiftDelivery;
import com.forcepower.acedns.activity.ActivityIMEIStatusReport;
import com.forcepower.acedns.activity.ActivityMarketFeedbackLanding;
import com.forcepower.acedns.activity.ActivityMarketFeedbackStock;
import com.forcepower.acedns.activity.ActivityOrderFilter;
import com.forcepower.acedns.activity.ActivityOrderFilterAlternateDesign;
import com.forcepower.acedns.activity.ActivityOutStandingAgeing;
import com.forcepower.acedns.activity.ActivityPendingContract;
import com.forcepower.acedns.activity.ActivityProductReplacement;
import com.forcepower.acedns.activity.ActivityReportLanding;
import com.forcepower.acedns.activity.ActivitySalesPerformance;
import com.forcepower.acedns.activity.ActivitySurveyDCA;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.ActivitySurveyLanding;
import com.forcepower.acedns.activity.ActivityWholeSaleInfo;
import com.forcepower.acedns.activity.AddNewCustActivity;
import com.forcepower.acedns.activity.BdSisActivity;
import com.forcepower.acedns.activity.BusinessProspectActivity;
import com.forcepower.acedns.activity.CollectionActivity;
import com.forcepower.acedns.activity.CollectionForecastActivity;
import com.forcepower.acedns.activity.CustomerFeedbackActivity;
import com.forcepower.acedns.activity.DashboardActivity;
import com.forcepower.acedns.activity.DemoFormActivity;
import com.forcepower.acedns.activity.DsrPdfActivity;
import com.forcepower.acedns.activity.GrnActivity;
import com.forcepower.acedns.activity.GroupLeaderFormActivity;
import com.forcepower.acedns.activity.non_auth.help.HelpActivity;
import com.forcepower.acedns.activity.HierarchicalReportActivity;
import com.forcepower.acedns.activity.HoardingActivity;
import com.forcepower.acedns.activity.InSHopPromoterFormActivity;
import com.forcepower.acedns.activity.JointWorkObservationActivity;
import com.forcepower.acedns.activity.KnockingFormActivity;
import com.forcepower.acedns.activity.KycActivity;
import com.forcepower.acedns.activity.LeadGenerationApprovalActivity;
import com.forcepower.acedns.activity.LocalEventActivity;
import com.forcepower.acedns.activity.LoyaltyProgrammeActivity;
import com.forcepower.acedns.activity.LuxEventActivity;
import com.forcepower.acedns.activity.LuxInShopActivity;
import com.forcepower.acedns.activity.LuxSiteDisplayVisitActivity;
import com.forcepower.acedns.activity.MISActivity;
import com.forcepower.acedns.activity.MISReportSummaryActivity;
import com.forcepower.acedns.activity.MerchandisingActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.sis_summery.NewSisSummeryActivity;
import com.forcepower.acedns.activity.non_auth.notification.NotificationReceiverActivity;
import com.forcepower.acedns.activity.OrderApprovalActivity;
import com.forcepower.acedns.activity.OrderEditActivity;
import com.forcepower.acedns.activity.OrderSummaryPDFActivity;
import com.forcepower.acedns.activity.QuotationAddActivity;
import com.forcepower.acedns.activity.RemainderActivityLanding;
import com.forcepower.acedns.activity.RetailerStockInActivity;
import com.forcepower.acedns.activity.RetailerStockOutActivity;
import com.forcepower.acedns.activity.RetailerStockOutActivitySpecial;
import com.forcepower.acedns.activity.RetailerStockReallocationActivity;
import com.forcepower.acedns.activity.RoutePlanApprovalActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.route_plan.RoutePlanLandingActivity;
import com.forcepower.acedns.activity.SalesOptionActivity;
import com.forcepower.acedns.activity.SamplingActivity;
import com.forcepower.acedns.activity.SaudaAllocationActivity;
import com.forcepower.acedns.activity.SaudaFilterActivity;
import com.forcepower.acedns.activity.SelfAppraisalLandingActivityWeekWise;
import com.forcepower.acedns.activity.StarOutStandingActivity;
import com.forcepower.acedns.activity.StockAuditEditActivity;
import com.forcepower.acedns.activity.StockAuditFormActivity;
import com.forcepower.acedns.activity.StockAuditFormScanActivity;
import com.forcepower.acedns.activity.StockCustomerProductActivity;
import com.forcepower.acedns.activity.StockistVisitActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivityList;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivitySpecial;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyMenuActivity;
import com.forcepower.acedns.activity.TDAllocationActivity;
import com.forcepower.acedns.activity.TargetAchieveLandingActivity;
import com.forcepower.acedns.activity.TechnicalMeetApprovalActivity;
import com.forcepower.acedns.activity.TechnicalMeetApprovalStatusActivity;
import com.forcepower.acedns.activity.TelecallerActivity;
import com.forcepower.acedns.activity.TentFormActivity;
import com.forcepower.acedns.activity.TourExpenseLandingActivitySpecial;
import com.forcepower.acedns.activity.TourMultiTravelExpenseLandingActivity;
import com.forcepower.acedns.activity.TourTravelExpenseLandingActivity;
import com.forcepower.acedns.activity.TrackActivity;
import com.forcepower.acedns.activity.TrackOrderActivity;
import com.forcepower.acedns.activity.WebViewActivity;
import com.forcepower.acedns.activity.YellowCardLandingActivity;
import com.forcepower.acedns.activity.splash.SplashActivity;
import com.forcepower.acedns.api.clients.TRANS_Emp_level;
import com.forcepower.acedns.new_activity.credit_limit.CustomerWiseCreditLimitActivity;
import com.forcepower.acedns.new_activity.customer_outstanding.CustomerWiseOutstandingActivity;
import com.forcepower.acedns.new_activity.market_feedback.GPApprovalForASMActivity;
import com.forcepower.acedns.new_activity.nt_quotation.activity.lead_graph.LeadGenerationGraphActivity;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.CatalogueVerticalSelectionAdapter;
import com.forcepower.acedns.adapter.CommonModelListAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ExpandableListAdapterAslMrpReport;
import com.forcepower.acedns.adapter.HintRemarksCheckBoxAdapter;
import com.forcepower.acedns.adapter.IncotermsAdapter;
import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.adapter.MenuAgeingExpandableAdapter;
import com.forcepower.acedns.adapter.MenuClStkMRPAdapter;
import com.forcepower.acedns.adapter.MenuOutstandingExpandableAdapter;
import com.forcepower.acedns.adapter.MerchandisingAdapter;
import com.forcepower.acedns.adapter.NotificationAdapter;
import com.forcepower.acedns.adapter.PendingContractAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductMasterCheckBoxAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.PurposeVisitAdapter;
import com.forcepower.acedns.adapter.RecyclerViewAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.SaudaMRPAdapter;
import com.forcepower.acedns.adapter.StateAdapter;
import com.forcepower.acedns.adapter.ViewPagerImageSLiderAdapter;
import com.forcepower.acedns.backgroundTask.DATA_DeleteTransactionTask;
import com.forcepower.acedns.backgroundTask.DATA_DownloadInvoiceInformationTask;
import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;
import com.forcepower.acedns.backgroundTask.DATA_GenerateDCRTask;
import com.forcepower.acedns.backgroundTask.FILE_CataloguePdfDownload;
import com.forcepower.acedns.backgroundTask.MASTER_LoadGITMasterData;
import com.forcepower.acedns.backgroundTask.MASTER_LoadLoyaltyPurchaseData;
import com.forcepower.acedns.backgroundTask.TRANS_CallDurationTransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_CashDepositeReceiveTask;
import com.forcepower.acedns.backgroundTask.TRANS_CheckOutTask;
import com.forcepower.acedns.backgroundTask.TRANS_Doctor_Visit_TransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_LoginTimePendingInvoiceInformationUpload;
import com.forcepower.acedns.backgroundTask.TRANS_LoginTimePendingOrder;
import com.forcepower.acedns.backgroundTask.TRANS_LogintimePendingSAUDA;
import com.forcepower.acedns.backgroundTask.TRANS_OdometerJourneyTransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_Stokist_Visit_TransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCRMTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCashDepositTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCashTransferTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCheckInCheckOut;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCollectionForecastTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCounterBid;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCustomerClassUpdationTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitDOTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitDeviceInfo;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFeedBack_BackUp;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFootSoldier;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitGiftTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitGrnTransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitJointWorkObservationTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitMarketFeedbackStockAudit;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitMonthlyreportMailRequest;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewBid;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewCustomerDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewRoute;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNotesInfo;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNotificationTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRedudantSurvey;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockInTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockOutTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockReallocationTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRouteCustomerPlan;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRoutePlanTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockReturnTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyOfferTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyPublishTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitTDAllocation;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitWholeSaleTask;
import com.forcepower.acedns.backgroundTask.TRANS_SurveyImageTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourDaySwapTransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_TravelFoodingLodgingAttachmentExportTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CallDurationDetails;
import com.forcepower.acedns.bean.CommonModel;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.MenuAegingListChild;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.bean.MenuOutstandingChild;
import com.forcepower.acedns.bean.MerchandisingDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.PropAccessibility;
import com.forcepower.acedns.bean.SelfAppraisalDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsDatabase2;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.database.AndroidDatabaseManager;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.sync.DataForDownloadingLead;
import com.forcepower.acedns.new_activity.declaration.DeclarationListActivity;
import com.forcepower.acedns.new_activity.market_feedback.MarketFeedbackSBGStockConfirmationActivity;
import com.forcepower.acedns.new_activity.nt_quotation.activity.lead_quotation.LeadQuotationListActivity;
import com.forcepower.acedns.new_activity.sitelead.NewSiteLeadListActivity;
import com.forcepower.acedns.newDataBase.sync.DataForDownloading;
import com.forcepower.acedns.newDataBase.sync.SyncSiteLead;
import com.forcepower.acedns.new_activity.target_achievement.EmployeeTargetAchievementActivity;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.JsonsReceiver;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.commonAsyncTaskMaster;
import com.google.android.material.tabs.TabLayout;

import com.forcepower.acedns.R;
import com.forcepower.acedns.TRANS_BusinessProspectCustomizeTransactionTask;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.api.clients.LocalStorage;
import com.forcepower.acedns.api.clients.RestClient;
import com.forcepower.acedns.backgroundTask.DATA_LoadDatabaseDetails;
import com.forcepower.acedns.backgroundTask.TRANS_AttnendanceTransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitBusinessProspect;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFeedBack;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitQuotationDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitTravelFoodingLodgingExpenseTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitYellowCardTask;
import com.forcepower.acedns.bean.CatalogueInfoDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.MenuClStkMrpDetails;
import com.forcepower.acedns.bean.MenuOutstandingParent;
import com.forcepower.acedns.bean.NotificationDetails;
import com.forcepower.acedns.bean.PendingContract;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.PurposeVisitDetails;
import com.forcepower.acedns.bean.ReportSummery;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SaudaFormDetails;
import com.forcepower.acedns.bean.SurveyReportSumary;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Objects;

import androidx.annotation.NonNull;
import androidx.core.content.FileProvider;
import androidx.core.text.HtmlCompat;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.legacy.app.ActionBarDrawerToggle;
import androidx.recyclerview.widget.RecyclerView;
import androidx.viewpager.widget.ViewPager;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;
import static com.forcepower.acedns.R.id.linearLayoutBusinessProspect;
import static com.forcepower.acedns.constants.Constants.allAdditionalCustomersCodeWithLogic;
import static com.forcepower.acedns.constants.Constants.allCustomersWithLogic;
import static com.forcepower.acedns.constants.Constants.allNewCustomersCode;
import static com.forcepower.acedns.constants.Constants.allRecommendedCustomersCodeWithLogic;
import static com.forcepower.acedns.constants.Constants.attendanceAlias;
import static com.forcepower.acedns.constants.Constants.attendanceFilterString;
import static com.forcepower.acedns.constants.Constants.currency;
import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObj;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.isCheckInToNewCustomer;
import static com.forcepower.acedns.constants.Constants.isGettingCurrentLocation;
import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.retailerappTransactionReason;
import static com.forcepower.acedns.constants.Constants.selectedState;
import static com.forcepower.acedns.constants.Constants.stockOutProdGroupRetailerApp;
import static com.forcepower.acedns.constants.Constants.stockOutTypeRetailerApp;
import static com.forcepower.acedns.constants.Constants.timeVal;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.clearAccuracyLatLong;
import static com.forcepower.acedns.util.Utils.deleteTempFolderRecursive;
import static com.forcepower.acedns.util.Utils.openCatalogue;
import static com.forcepower.acedns.util.Utils.setCheckInOutLatLongAccuracyToLocationLatLong;
import static com.forcepower.acedns.util.Utils.setCustomerCheckInDataInPreferences;

import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MenuActivity extends AceDnsParentActivity implements OnClickListener {
    public RecyclerViewAdapter finalAdapter = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null, mImageViewUserPic = null, mImageViewImageNotification = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonLogout = null, mButtonAttendence = null, btn_ta_da_again = null;
    @SuppressLint("StaticFieldLeak")
    public static GridView mGridViewMenu = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout memuactivityMainLayout = null, mLinearLayoutLogOut = null, mLinearLayoutBackUp = null, mLinearLayoutCustomer = null, db_layout = null,
            catalogue_layout = null, scheme_pdf_layout = null, scheme_pdf_layout_without_branch = null, telephonic_transaction_layout = null, video_conference_layout = null,
            mLinearLayoutCheckOut = null, mLl_order_status_download_pdf_layout = null, mll_drs_pdf_layout = null, ll_route_plan_approval_layout = null, ll_kyc = null,
            mLinearLayoutHint = null, mLinearLayoutUser = null, mLinearLayoutReset = null, mLinearLayoutAgeing = null, mLinearLayoutHelp = null, mLinearLayoutOutstanding = null,
            mLinearLayoutPendingContract = null, manageractivity_layout = null, mLinearLayoutSaudaOutstanding = null, mLinearLayoutMerchandising = null, mLinearLayoutClosingStock = null,
            mLinearLayoutMRP = null, mLinearLayoutStockAuditCustomer = null, price_generation_layout = null, mcx_price_generation_layout = null, price_release_layout = null,
            mLinearLayoutSyncData = null, mLinearLayoutDeclarationRequest = null, mLinearLayoutGPRequest = null, mLinearLayoutProjectKhoj = null, mLinearLayoutsync_layout_feedback = null, mLinearLayoutRoute = null,
            mLinearLayoutNotification = null, mLinearLayoutOption = null, mLinearLayoutSalesPerformance = null;
    @SuppressLint("StaticFieldLeak")
    public static RelativeLayout mRelativeLayoutRefresh = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewUserName = null;
    @SuppressLint("StaticFieldLeak")
    static AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    @SuppressLint("StaticFieldLeak")
    static Context mContext;
    @SuppressLint("StaticFieldLeak")
    static TextView tv_NotificationOBJ;
    @SuppressLint("StaticFieldLeak")
    static TextView closingStockTv;
    public String mRouteName = "", mImagePath = "", mSImageName = "", mSaudaType = "";
    public int mWidth = 175, mHeight = 150;
    public File mImageFile;
    ImageView hintLayOutImageView;
    TextView taggedProductsET;
    ViewPager viewPager;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsDatabase2 mAceDnsDatabase2;
    ArrayList<RouteDetails> mRouteDetailsListForVisitSequence;
    SimpleDateFormat mSimpleDateFormat;
    Animation mAnimationBottomUp, mAnimationBottomDown, mAnimationLeftIn, mAnimationLeftOut;
    Handler mHandlerOutstanding, mHandlerRefresh, mHandlerDelete, mHandlerAgeing, mHandlerPendingContract;
    ProgressDialog mProgressDialogOutstanding, mProgressDialogAgeing;
    DrawerLayout mDrawerLayout;
    ActionBarDrawerToggle mDrawerToggle;
    ProgressDialog mProgressDialogPending;
    Handler mHandlerPending;
    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
    boolean isFinished = false;
    MenuAdapter mMenuAdapter;
    ArrayList<MenuObj> mMenuList;
    ArrayList<RoutePlanMasterDetails> todayPlanList;
    ArrayList<MenuOutstandingParent> outstandingParentItems;
    ArrayList<MenuOutstandingChild> outstandingChildItems;
    ArrayList<MenuAegingListChild> ageingChildItems;
    ArrayList<MenuClStkMrpDetails> clStkList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<PendingContract> mPendingContractList;
    RouteDetails mSelectedRouteDetailsVisitSequence;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<RouteDetails> mRouteDetailsList;
    ConnectionDetector cd;
    boolean isInternetPresent = false;
    ArrayList<String> hintRemarksValList;
    ArrayList<String> propelloFormValList;
    ArrayList<PropAccessibility> propelloFormAccessibilityValList;
    int filterNo;
    ArrayList<Button> filterButtonList;
    int lastProdPos = 0;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected, lastProductSelected = false, visitSequenceCheckIn;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    Dialog grpDialog, subGrpDialog, brandDialog, masterDialog;
    ProgressDialog ploader;
    Handler orderDataHandler;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<ProductMasterDetails> tempProductList;
    ArrayList<ProductGroupDetails> tempProductGroupList;
    ArrayList<ProductSubGrpDetails> tempProductSubGroupList;
    ArrayList<ProductBrandDetails> tempProductBrandList;
    boolean carryInSales = false;
    ProductMasterAdapter prodAdapter;
    ProductMasterCheckBoxAdapter ProductMasterWithQtyInputAdapterObject;
    HintRemarksCheckBoxAdapter HintRemarksCheckBoxAdapterObject;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    String lastStr = "";
    Uri mFileUri;
    int numberOfImageAdded = 0;
    List<String> listDataHeaderTemporary;
    List<String> listDataHeader;
    HashMap<String, List<MenuClStkMrpDetails>> listChildDataTemporary;
    HashMap<String, List<MenuClStkMrpDetails>> listChildData;
    ImageView attachmentImageView1, attachmentImageView2;
    HashMap<String, Bitmap> supportingAttachmentMap;
    LocalStorage localStorage;

    private String mRouteCode = "";
    private boolean isAttendanceGiven = false;
    private boolean isCheckedOutToday = false;
    private boolean isStockDataRefresh = false;
    private boolean isLoyaltyDataRefresh = false;

    private boolean isKeyLongPress = false;
    private String mStockDataReceivedFrom = "";
    private String mTotalotalOutstanding = "";
    private String mCustomerName = "";
    private int TAKE_PHOTO_CODE = 2;
    private int TAKE_PHOTO_journey_info = 22;
    LocationTracker LocationTrackerObject;
    int progress = 20;
    boolean processDone = false;
    String checkboxdata = "";
    String startingKm = "";
    String endingKm = "";
    String oddometerImage = "";
    String[] journeyListSplitted = null;
    Bitmap oddometerPicBitmap = null;
    ImageView oddometerPicImageView = null;
    String empLevel = "0";

    int selectedHintRemarksId = -1;
    String selectRem = "";
    SparseBooleanArray sparseBooleanArray;
    ProgressDialog progressDialog;
    String countOfNewPoint = "0";

    Runnable RunnableDelete = new Runnable() {
        @Override
        public void run() {
            mRelativeLayoutRefresh.startAnimation(mAnimationBottomDown);
            mRelativeLayoutRefresh.setVisibility(GONE);
            AlertDialog.Builder alert = new AlertDialog.Builder(MenuActivity.this);
            alert.setMessage("Data refresh module").setCancelable(false);
            alert.setMessage("Specific transaction needs to be Deleted.");
            alert.setPositiveButton("    OK    ", (dialog, which) -> {
                mGridViewMenu.setEnabled(true);
                mButtonAttendence.setEnabled(true);
                mButtonLogout.setEnabled(true);
                String libraryStatus = Utils.checkLibraryConditions(MenuActivity.this);
                if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                    try {
                        showTransactionDeleteDialog();
                    } catch (Exception ignored) {
                    }
                } else {
                    Utils.showToast(MenuActivity.this, libraryStatus + "\nPlease Synchronize Data.");
                }
            });
            alert.show();
        }
    };

    Runnable RunnableDataRefresh = new Runnable() {
        @Override
        public void run() {
            mRelativeLayoutRefresh.startAnimation(mAnimationBottomDown);
            mRelativeLayoutRefresh.setVisibility(GONE);
            mGridViewMenu.setEnabled(true);
            mButtonAttendence.setEnabled(true);
            mButtonLogout.setEnabled(true);
            String libraryStatus = Utils.checkLibraryConditions(MenuActivity.this);
            if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                if (isStockDataRefresh) {
                    File gitTxt = new File(Utils.getAppStoragePath(mContext) + "git_master.txt");
                    if (gitTxt.exists()) {
                        gitTxt.delete();
                    }
                    new MASTER_LoadGITMasterData(MenuActivity.this, true).execute();
                } else if (isLoyaltyDataRefresh) {
                    File purchaseTxt = new File(Utils.getAppStoragePath(mContext) + " loyalty_purchase_details.txt");
                    File loyaltyTxt = new File(Utils.getAppStoragePath(mContext) + " loyalty_customer.txt");
                    if (loyaltyTxt.exists()) {
                        loyaltyTxt.delete();
                    }
                    if (purchaseTxt.exists()) {
                        purchaseTxt.delete();
                    }
                    new MASTER_LoadLoyaltyPurchaseData(MenuActivity.this, true).execute();
                } else {
                    Utils.updateEmployeeMasterDate(MenuActivity.this);
                    new DATA_LoadDatabaseDetails(MenuActivity.this).execute(Constants.employeeDetailObject.getEmpCode());
                }
            } else {
                Utils.showToast(MenuActivity.this, libraryStatus + "\nPlease Synchronize Data.");
            }
        }
    };

    // New Site Lead Approval
    String userType = "";

    // Birth Day Popup
    String dobDate = "";
    RelativeLayout birthdayPopupLayout, birthdayCardContainer;
    ImageView birthdayBackgroundImage, birthdayPopupClose;
    TextView birthdayTitle, birthdayUserName, birthdayMessage;

    RelativeLayout dobEnterPopupLayout;
    LinearLayout dobEnterPopup, dateOfBirthLayout;
    TextView dateOfBirthTextView;
    Button updateDOBButton;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    // Birth Day Popup

    String sale_access = "";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_menu);
        mContext = this;
        try {
            mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
            mNewDatabaseForSiteLead.createDatabaseTableForSiteLead();
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            mAceDnsDatabase.addColumnsIfNotExist();
            mAceDnsDatabase.check();

            sale_access = mAceDnsDatabase.getEmpSaleAccess(Constants.employeeDetailObject.getEmpCode());
            primaryFunction();


        } catch (Exception e) {
            Log.d("TAG", "_DDDDD_ onCreate: " + e.getMessage());
        }
    }

    // GP layout
    private void GPLayoutFunction(){
        try {
            mLinearLayoutGPRequest = findViewById(R.id.gp_approve_layout);
            mLinearLayoutGPRequest.setOnClickListener(v -> {
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
                Intent intent = new Intent(MenuActivity.this, GPApprovalForASMActivity.class);
                startActivity(intent);
            });
            TRANS_Emp_level.fetchLevel("E0555", new TRANS_Emp_level.EmpLevelCallback() {
                @Override
                public void onResult(String level) {
                    // level = "L3"
                    Log.d("TAG", "_DDDDD_ onResult: " + level);
                    if (level.equalsIgnoreCase("l3")) {
                        mLinearLayoutGPRequest.setVisibility(VISIBLE);
                    } else {
                        mLinearLayoutGPRequest.setVisibility(GONE);
                    }
                }

                @Override
                public void onError(String error) {
                    // "Employee not found" etc.
                    mLinearLayoutGPRequest.setVisibility(GONE);
                }
            });
        } catch (Exception e) {
            Log.d("TAG", "_DDDDD_ primaryFunction: " + e.getMessage());
        }
    }

    @SuppressLint({"NewApi", "HandlerLeak", "SetTextI18n", "SimpleDateFormat"})
    private void primaryFunction() {
        supportingAttachmentMap = new HashMap<>();
        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        RegisterActivities.registerActivity(this);
        cd = new ConnectionDetector(mContext);
        Constants.isFirstLoginOfApp = false;
        Constants.login_status = "TRUE";
        Utils.InitialiseSETUPTableData(MenuActivity.this);
        hintRemarksValList = new ArrayList<>();
        filterButtonList = new ArrayList<>();
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(MenuActivity.this);
        List<NotificationDetails> al_store_Unread_NOTI = mAceDnsTransactionDatabase.get_unread_Notification();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsDatabase2 = new AceDnsDatabase2(mContext);
        LocationTrackerObject = new LocationTracker(mContext, "attendance");
        mAnimationBottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        mAnimationBottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        mAnimationLeftIn = AnimationUtils.loadAnimation(this, R.anim.left_in);
        mAnimationLeftOut = AnimationUtils.loadAnimation(this, R.anim.left_out);

        try {
            empLevel = mAceDnsDatabase.getEmpLevel(Constants.employeeDetailObject.getEmpCode());
            if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                    && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes")
                    && Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                if (mAceDnsTransactionDatabase.InvoiceListTableEmpty()) {
                    isInternetPresent = cd.isConnectingToInternet();
                    if (isInternetPresent) {
                        new DATA_DownloadInvoiceInformationTask(mContext).execute();
                    } else {
                        Toast.makeText(mContext, "Due to poor connectivity,\n invoice information could not be downloaded.", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        } catch (Exception ignored) {
        }

        new GPSTracker(mContext);
        autoCheckOutProcess();
//        userType=mNewDatabaseForSiteLead.getEmpDesignation(Constants.employeeDetailObject.getEmpCode());
//        menuUpdate();
        prepareFeatureList();
        initView();
        GPLayoutFunction();
        Utils.headerFooterIconChangesForRetailerApp(mContext, true);

        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") || Constants.nickName.equalsIgnoreCase("asl")) {
            memuactivityMainLayout.setBackgroundColor(getResources().getColor(R.color.white));
            closingStockTv.setText("Dashboard");
            showSlidingImages();
        }

        tv_NotificationOBJ.setText("" + al_store_Unread_NOTI.size());
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mStepHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                MenuActivity.this.runOnUiThread(() -> {
                    switch (step) {
                        case 1:
                            if (!Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes") || !Constants.orderFormDetailsObj.getvisit_sequence().equalsIgnoreCase("yes")) {
                                if (Constants.menuDetailsObj.getRoutePlan() != null && Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                                    if (mRoutePlanListofToday.size() == 1) {
                                        mRouteName = mRoutePlanListofToday.get(0).getRouteName();
                                        mRouteCode = mRoutePlanListofToday.get(0).getRoutecode();
                                        FlowofCheckINAndOut(2, mRouteCode);
                                    } else if ((mRoutePlanListofToday.size() > 1)) {
                                        ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
                                    } else {
                                        Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                    }
                                } else {
                                    if (mRouteDetailsList.size() > 1) {
                                        ShowRouteListDialog(mRouteDetailsList);
                                    } else if (mRouteDetailsList.size() == 1) {
                                        mRouteName = mRouteDetailsList.get(0).getRouteName();
                                        mRouteCode = mRouteDetailsList.get(0).getRouteCode();
                                        FlowofCheckINAndOut(2, mRouteCode);
                                    } else {
                                        Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                    }
                                }
                            }
                            break;
                        case 2:
                            LocationTrackerObject = new LocationTracker(mContext, "check in");
                            LocationTrackerObject.checkLocationUpdateSharing();
                            final ProgressDialog progressD = new ProgressDialog(mContext);
                            progressD.setMessage("Looking for coverage area...");
                            progressD.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
                            progressD.setIndeterminate(false);
                            progressD.setProgress(10);
                            progressD.setCancelable(false);
                            progressD.show();
                            new CountDownTimer(10000, 1000) {
                                public void onTick(long millisUntilFinished) {
                                    progressD.setProgress(progress);
                                    progress = progress + 10;
                                }

                                public void onFinish() {
                                    progressD.dismiss();
                                    progress = 10;
                                    if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && !isGettingCurrentLocation) {
                                        Utils.showToast(mContext, "Could not determine your location. Please Check Location Settings");
                                    } else {
                                        CheckinProcessAfterProperLocationFetching();
                                    }
                                }
                            }.start();
                            break;
                        case 3:
                            showExForTypeDialog();
                            break;
                    }
                });
            }
        };

        if (!Constants.isCommpleteDownLoadComplete) {
            ShowDownloadErrorDialog();
        } else {
            if (Constants.menuDetailsObj.getTargetAcheivement().trim().equalsIgnoreCase("yes") && Constants.isLoginnow) {
                ShowAcheivementDialog();
            } else if (Constants.isLoginnow && Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")
                    && mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(Constants.employeeDetailObject.getEmpCode()).size() > 2) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    goToRetailerappDashBoard();
                }
            } else if (Constants.isLoginnow && Constants.menuDetailsObj.getgolden_rules().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("golden_rules")) {
                if (cd.isConnectingToInternet()) {
//                    Utils.showProgressDialog(mContext, "Downloading data.Please wait.");
                    masterApiCallingFlag = false;
                    Log.d("TAG", "_DDDD_ golden_rules onCreate: Calling from");
                    initBirthDayPopup();
                    new Thread() {
                        public void run() {
//                            new commonAsyncTaskMaster(mContext, "golden_rules");
                        }
                    }.start();
                } else {
                    Utils.makePdfViewingProcess(mContext);
                }
            }
            Constants.isLoginnow = false;
        }

        try {
            isAttendanceGiven = mAceDnsTransactionDatabase.getAttendanceForToday();
            boolean isLeaveRequestAddedInRoutePlanToday = mAceDnsTransactionDatabase.getLeaveRequestAddedInRoutePlanToday();
            isCheckedOutToday = mAceDnsTransactionDatabase.isCheckedOutToday();
            if (isAttendanceGiven || isLeaveRequestAddedInRoutePlanToday) {
                if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                    mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                } else {
                    mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                }
            } else {
                LocationTrackerObject.checkLocationUpdateSharing();
            }
        } catch (Exception e) {
            mButtonAttendence.setEnabled(true);
            System.out.println("Exception:::::" + e.getMessage());
        }

        mSimpleDateFormat = new SimpleDateFormat("yyyyMMdd_HHmmss");
        mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String feature = mMenuList.get(arg2).getFeatureName();
            doOnItemClickJob(feature);
        });

        mHandlerOutstanding = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogOutstanding.cancel();
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    if (!outstandingParentItems.isEmpty() && !outstandingChildItems.isEmpty()) {
                        showOutstandingDetails();
                    } else {
                        Utils.showToast(MenuActivity.this, "You have no Outstanding.");
                    }
                }
            }
        };

        mHandlerPendingContract = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogOutstanding.cancel();
                String response = msg.getData().getString("message");
                assert response != null;
                if (response.equalsIgnoreCase("2")) {
                    if (!mPendingContractList.isEmpty()) {
                        ShowPendingContractDialog(mCustomerName);
                    } else {
                        Utils.showToast(MenuActivity.this, "You have no Pending Contract.");
                    }
                }
            }
        };

        mHandlerAgeing = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogAgeing.cancel();
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    if (!outstandingParentItems.isEmpty() && !ageingChildItems.isEmpty()) {
                        showAgeingDetailsDialog(msg.getData().getString("Period1"), msg.getData().getString("Period2"), msg.getData().getString("Period3"));
                    } else {
                        Utils.showToast(MenuActivity.this, "You have no Outstanding.");
                    }
                }
            }
        };

        mHandlerPending = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogPending.dismiss();
                String response = msg.getData().getString("message");
                assert response != null;
                if (response.equalsIgnoreCase("checkoutpending")) {
                    new TRANS_CheckOutTask(MenuActivity.this, true).execute();
                }
                if (response.equalsIgnoreCase("syncpending")) {
                    Constants.dataResfresh = true;
                    new DATA_LoadDatabaseDetails(MenuActivity.this).execute(Constants.employeeDetailObject.getEmpCode());
                }
            }
        };

        orderDataHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                MenuActivity.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            showGrpListDialog();
                            break;
                        case 2:
                            showSubGrpListDialog();
                            break;
                        case 3:
                            showBrandListDialog();
                            break;
                        case 4:
                            showMasterListDialog();
                            break;
                    }
                });
            }
        };
        if (Constants.dataResfresh || Constants.stockDataRefresh || Constants.loyaltyDataRefresh) {
            if (Constants.stockDataRefresh) {
                isStockDataRefresh = true;
                mStockDataReceivedFrom = Constants.stockSender;
                mStockDataReceivedFrom = mStockDataReceivedFrom.substring(2);
            } else if (Constants.loyaltyDataRefresh) {
                isLoyaltyDataRefresh = true;
            }
            Constants.stockDataRefresh = false;
            Constants.loyaltyDataRefresh = false;
            mGridViewMenu.setEnabled(false);
            mButtonAttendence.setEnabled(true);
            mButtonLogout.setEnabled(true);
            mRelativeLayoutRefresh.startAnimation(mAnimationBottomUp);
            mRelativeLayoutRefresh.setVisibility(VISIBLE);
            try {
                AssetFileDescriptor afd = getAssets().openFd("tone.mp3");
                MediaPlayer player = new MediaPlayer();
                player.setDataSource(afd.getFileDescriptor(),
                        afd.getStartOffset(), afd.getLength());
                player.prepare();
                player.start();
            } catch (Exception e) {
                Log.d("TAG", "onCreate: " + e.getMessage());
            }
            mHandlerRefresh = new Handler();
            mHandlerRefresh.postDelayed(RunnableDataRefresh, 5000);
        } else if (Constants.transactionDelete || Constants.transDeleteAndRefresh) {
            mGridViewMenu.setEnabled(false);
            mButtonAttendence.setEnabled(true);
            mButtonLogout.setEnabled(true);
            mRelativeLayoutRefresh.startAnimation(mAnimationBottomUp);
            mRelativeLayoutRefresh.setVisibility(VISIBLE);
            try {
                AssetFileDescriptor afd = getAssets().openFd("tone.mp3");
                MediaPlayer player = new MediaPlayer();
                player.setDataSource(afd.getFileDescriptor(),
                        afd.getStartOffset(), afd.getLength());
                player.prepare();
                player.start();
            } catch (Exception e) {
                Log.d("TAG", "onCreate: " + e.getMessage());
            }
            mHandlerDelete = new Handler();
            mHandlerDelete.postDelayed(RunnableDelete, 4000);
        }
        try {
            Intent intent = getIntent();
            if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && intent.hasExtra("transactionType")) {
                new Handler(Looper.getMainLooper()).post(() -> new TRANS_CallDurationTransactionTask(mContext, true).execute());
                if (!NotCheckedOut(mContext) && !Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
                    ShowCompletedTransactionDetailsDialog();
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "onCreate: " + e.getMessage());
        }
        if (getIntent().hasExtra("doSuccessMessage")) {
            String doSuccessMessage = Objects.requireNonNull(getIntent().getExtras()).getString("doSuccessMessage");
            ShowDoSuccessMessageDialog(doSuccessMessage);
        }
        mAceDnsDatabase.getSettingWeight();
        mAceDnsDatabase.getSettingSisReport();
        if (Constants.weightage != null && Constants.weightage.matches("yes")) {
            isInternetPresent = cd.isConnectingToInternet();
            if (isInternetPresent) {
                JsonsReceiver.getStateWiseWeitage(mContext);
            }
        }
        try {
            if (Constants.sis_emp_data_startTarget.toUpperCase().matches("YES1")) {
                isInternetPresent = cd.isConnectingToInternet();
                if (isInternetPresent) {
                    JsonsReceiver.getSisEmpData(mContext);
                    JsonsReceiver.getSisAppVisibility(mContext);
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "onCreate: " + e.getMessage());
        }
        isInternetPresent = cd.isConnectingToInternet();
        if (isInternetPresent) {
            if (Constants.nickName.equalsIgnoreCase("propello")) {
                JsonsReceiver.setProp_form_accessibility(mContext);
                JsonsReceiver.prop_demo_form_data(mContext);
                JsonsReceiver.prop_telecaller_form_data(mContext);
            }
            if (Constants.menuDetailsObj.getSis_report().equalsIgnoreCase("yes")) {
                JsonsReceiver.setSisSummary(mContext);
            }
        }
        if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes") && Constants.remainderFlg.matches("t")) {
            remainderDialog();
            Constants.remainderFlg = "f";
        }
        if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site") && Constants.remainderFlg.matches("t")) {
            remainderSiteDialog();
            Constants.remainderFlg = "f";
        }
        mAceDnsDatabase.GetMarketFeedbackDetailsAll();
    }

    @SuppressLint({"SimpleDateFormat"})
    @Override
    public void onClick(View v) {
        onClickDOB(v);
        if (v == mLinearLayoutOutstanding) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            if (Constants.menuDetailsObj.getDO_status().toLowerCase().matches("yes") || Constants.nickName.toLowerCase().matches("star")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    starsaathi_ledger_customer_list(mContext);
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else {
                chooseCustomerDialog();
            }
        } else if (v == mLinearLayoutMRP) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            Constants.saudaFormDetailsObj = new SaudaFormDetails();
            Constants.saudaFormDetailsObj = mAceDnsDatabase.GETSaudaFormDetails();
            if (Constants.nickName.equalsIgnoreCase("asl")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    FlowofCheckINAndOut(3, "");
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes")) {
                boolean order = mAceDnsDatabase.MenuAccess("order");
                boolean saudaaccess = mAceDnsDatabase.MenuAccess("sauda");
                if (order && saudaaccess) {
                    ShowPriceSelectionDialog();
                }
                if (!order && saudaaccess) {
                    if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(mAceDnsDatabase.getVerticalValueOfLoggedInEmployee())) {
                        ShowStateNameDialog();
                    } else {
                        ShowSaudaDepoNameDialog();
                    }
                }
                if (order && !saudaaccess) {
                    ShowOrderDepoNameDialog();
                }
            } else {
                if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                    if (Constants.saudaFormDetailsObj.getSaudaDepotWise().equalsIgnoreCase("yes")) {
                        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(mAceDnsDatabase.getVerticalValueOfLoggedInEmployee())) {
                            ShowStateNameDialog();
                        } else {
                            ShowSaudaDepoNameDialog();
                        }
                    } else {
                        ShowMRPDialog();
                    }
                } else {
                    if (Constants.productDetailsObj.getBranchWiseMRP().equalsIgnoreCase("yes")) {
                        String branchListForCurrentEmployee = mAceDnsDatabase.GETBranchOfCurrentEmp().trim();
                        if (branchListForCurrentEmployee.isEmpty()) {
                            Utils.showToast(mContext, "This employee is not mapped with a branch\nPlease Synchronize Data");
                        } else if (branchListForCurrentEmployee.contains(",")) {
                            ShowBranchListForMrpDialog();
                        } else {
                            Constants.selectedBranchForMrp = mAceDnsDatabase.getBranchListOfCurrentEmp().get(0);
                            ShowMRPDialog();
                        }
                    } else {
                        ShowMRPDialog();
                    }
                }
            }
        } else if (v == mLinearLayoutStockAuditCustomer) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            if (HTTPUtils.isConnectionPossible(mContext)) {
                JsonsReceiver.getCustomerProductStock(mContext);
                startActivity(new Intent(MenuActivity.this, StockCustomerProductActivity.class));
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }
        } else if (v == price_generation_layout) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                new Thread() {
                    public void run() {
                        new commonAsyncTaskMaster(mContext, "price_generation");
                    }
                }.start();
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }
        } else if (v == mcx_price_generation_layout) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                new Thread() {
                    public void run() {
                        new commonAsyncTaskMaster(mContext, "mcx_price_generation");
                    }
                }.start();
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }
        } else if (v == price_release_layout) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                new Thread() {
                    public void run() {
                        new commonAsyncTaskMaster(mContext, "release_pricing");
                    }
                }.start();
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }
        } else if (v == btn_ta_da_again) {
            if (!isAttendanceGiven) {
                Utils.showToast(mContext, "Please give Attendance first");
            } else {
                funTaDaChangeMode();
            }
        } else if (v == mButtonLogout) {
            if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
            } else {
                mDrawerLayout.openDrawer(mLinearLayoutOption);
            }
        } else if (v instanceof Button && v != mButtonAttendence) {
            int tag = (Integer) v.getTag();
            switch (tag) {
                case 1:
                    switch (filterNo) {
                        case 2:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(1).setEnabled(true);
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            filterButtonList.get(1).setEnabled(true);
                            break;
                    }
                    lastSubGroupSelected = false;
                    lastBrandSelected = false;
                    lastProductSelected = false;
                    prepareOrderData(1, "");
                    break;
                case 2:
                    switch (filterNo) {
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            break;
                    }
                    lastBrandSelected = false;
                    lastProductSelected = false;
                    if (selectedGrp != null) {
                        prepareOrderData(2, selectedGrp.getGroupCode());
                    } else {
                        Toast.makeText(mContext, "Please select the parent category", Toast.LENGTH_SHORT).show();
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(mContext, "Please select the parent category", Toast.LENGTH_SHORT).show();
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                prepareOrderData(4, selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(mContext, "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(mContext, "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(mContext, "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                    }
            }
        } else {
            if (v == db_layout) {
                Intent dbmanager = new Intent(mContext, AndroidDatabaseManager.class);
                startActivity(dbmanager);
            } else {
                if (v == mButtonAttendence) {
                    if (!isAttendanceGiven) {
                        if (Constants.userDetailsObj.getmenu_access_attendance().equalsIgnoreCase("")) {
                            attendanceProcess();
                        } else {
                            final LocationManager manager = (LocationManager) mContext.getSystemService(Context.LOCATION_SERVICE);
                            Utils.isDevOn(mContext);
                            if (Constants.nickName.equalsIgnoreCase("nimbus")) {
                                if (manager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
                                    if (Constants.isDeveloperOn && !BuildConfig.DEBUG) {
                                        Utils.showToast(mContext, "Please Disable Developer mode");
                                    } else {
                                        if (!Constants.currentLat.isEmpty()) {
                                            showAttendanceTypeDialog();
                                        } else {
                                            Utils.showToast(mContext, "Could not get your Location try again");
                                        }
                                    }
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please Enable GPS");
                                }
                            } else {
                                if (manager.isProviderEnabled(LocationManager.GPS_PROVIDER) && !Constants.currentLat.isEmpty()) {
                                    showAttendanceTypeDialog();
                                } else {
                                    Utils.showToast(mContext, "Could not get your Location try again");
                                }
                            }
                        }
                        LocalStorage localStorage;
                        localStorage = new LocalStorage(getApplicationContext());
                        localStorage.setTentID("");
                        localStorage.setStartImage("");
                        localStorage.setStartTime("");
                        localStorage.setKnoStartImage("");
                        localStorage.setKnoStartTime("");
                        localStorage.setKnockID("");
                    } else {
                        RouteListReportShowProcess();
                    }
                } else if (v == mLinearLayoutLogOut) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Constants.login_status = "";
                    finish();
                    moveTaskToBack(true);
                    android.os.Process.killProcess(android.os.Process.myPid());
                    System.exit(1);
                } else if (v == telephonic_transaction_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    ShowChooseTransactionTypeDialog();
                } else if (v == video_conference_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                } else if (v == mLinearLayoutSyncData) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    mProgressDialogAgeing = new ProgressDialog(MenuActivity.this);
                    mProgressDialogAgeing.setMessage("Downloading Data ...");
                    mProgressDialogAgeing.show();
                    SyncSiteLead syc = new SyncSiteLead(mContext);
                    syc.uploadAllPendingSiteLead();
                    DataForDownloading sycData = new DataForDownloading(mContext);
//                    DataForDownloadingLead sd = new DataForDownloadingLead(mContext);
//                    sd.addAllFormDataForLead(s -> {
                    sycData.addAllFormDataForSiteLead(success -> {
                        mProgressDialogAgeing.dismiss();
                        loadMenuAgain();
                        if (cd.isConnectingToInternet()) {
                            Utils.updateEmployeeMasterDate(MenuActivity.this);
                            new GPSTracker(this);
                            PendingingDataUpload("syncpending");
                        } else {
                            Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                        }
                    });
//                    });
                } else if (v == mLinearLayoutDeclarationRequest) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, DeclarationListActivity.class);
                    startActivity(intent);
                } else if (v == mLinearLayoutsync_layout_feedback) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (cd.isConnectingToInternet()) {
                        new GPSTracker(this);
                        PendingingDataUploadFeedback("syncpending");
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                } else if (v == mLinearLayoutPendingContract) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, ActivityPendingContract.class);
                    startActivity(intent);
                } else if (v == manageractivity_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                        new Thread() {
                            public void run() {
                                Constants.isOrederToNewCustomer = false;
                                timeVal = Utils.getCurrentDateTimeInGivenFormat("HH:mm:ss");
                                new commonAsyncTaskMaster(mContext, "manager_activity");
                            }
                        }.start();
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                } else if (v == mLinearLayoutSaudaOutstanding) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, ActivityOutStandingAgeing.class);
                    startActivity(intent);
                } else if (v == mLinearLayoutBackUp) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (cd.isConnectingToInternet()) {
                        Constants.isDataRefreshed = true;
                        String libraryStatus = Utils.checkLibraryConditions(MenuActivity.this);
                        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                            new DATA_EmailToDeveloperTask(MenuActivity.this, true, Constants.employeeDetailObject.getEmpCode(), "", Constants.employeeDetailObject.getEmpName(), true).execute();
                            String timeStamp = dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                            mAceDnsDatabase.insertToLogTable(timeStamp, "data_refresh");
                        } else {
                            Utils.showToast(MenuActivity.this, libraryStatus + "\nPlease Synchronize Data.");
                        }
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                } else if (v == mLinearLayoutAgeing) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    showAgeingPeriodDialog();
                } else if (v == mLinearLayoutHelp) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    CopyPdfAsset();
                    int currentApiVersion = android.os.Build.VERSION.SDK_INT;
                    if (currentApiVersion >= android.os.Build.VERSION_CODES.LOLLIPOP) {
                        Intent intent = new Intent(MenuActivity.this, HelpActivity.class);
                        startActivity(intent);
                    } else {
                        File pdfFile = new File(Utils.getAppStoragePath(mContext) + Constants.starHelpFileName);
                        if (Constants.nickName.toUpperCase().matches("GOLDSTONET") || Constants.nickName.toUpperCase().matches("GOLDSTONE")) {
                            pdfFile = new File(Utils.getAppStoragePath(mContext) + Constants.goldStoneHelpFileName);
                        }
                        Intent target = new Intent(Intent.ACTION_VIEW);
                        target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");
                        target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
                        Intent intent = Intent.createChooser(target, "Open File");
                        try {
                            startActivity(intent);
                        } catch (ActivityNotFoundException ignored) {
                        }
                    }
                } else if (v == mLinearLayoutClosingStock) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                        if (HTTPUtils.isConnectionPossible(mContext)) {
                            goToRetailerappDashBoard();
                        } else {
                            Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                        }
                    } else {
                        ShowClosingStockDialog();
                    }
                } else if (v == mLinearLayoutSalesPerformance) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, ActivitySalesPerformance.class);
                    startActivity(intent);
                } else if (v == mLinearLayoutNotification) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    showNotificationDialog();
                } else if (v == mLl_order_status_download_pdf_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, OrderSummaryPDFActivity.class);
                    startActivity(intent);
                } else if (v == mll_drs_pdf_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, DsrPdfActivity.class);
                    startActivity(intent);
                } else if (v == ll_route_plan_approval_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (cd.isConnectingToInternet()) {
                        Intent intent = new Intent(MenuActivity.this, RoutePlanApprovalActivity.class);
                        startActivity(intent);
                    } else {
                        Toast.makeText(mContext, "Internet connection not available. You can not access without an active internet connection on your device.", Toast.LENGTH_LONG).show();
                    }
                } else if (v == ll_kyc) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    Intent intent = new Intent(MenuActivity.this, KycActivity.class);
                    startActivity(intent);
                } else if (v == mLinearLayoutMerchandising) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    showUnresolvedMerchandisingListDialog();
                } else if (v == mLinearLayoutCheckOut) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    mProgressDialogAgeing = new ProgressDialog(MenuActivity.this);
                    mProgressDialogAgeing.setMessage("Downloading Data ...");
                    mProgressDialogAgeing.show();
                    SyncSiteLead syc = new SyncSiteLead(mContext);
                    syc.uploadAllPendingSiteLead();
                    DataForDownloading sycData = new DataForDownloading(mContext);
                    DataForDownloadingLead sd = new DataForDownloadingLead(mContext);
//                    sd.addAllFormDataForLead(s -> {
//                        sycData.addAllFormDataForSiteLead(success -> {
                    mProgressDialogAgeing.dismiss();
                    if (!isCheckedOutToday) {
                        if (cd.isConnectingToInternet()) {
                            if (isAttendanceGiven || !mAceDnsDatabase.MenuAccess("attendance")) {
                                final LocationManager manager = (LocationManager) mContext.getSystemService(Context.LOCATION_SERVICE);
                                if (Constants.nickName.toLowerCase().matches("star") || Constants.nickName.toLowerCase().matches("start")) {
                                    SimpleDateFormat sdf = new SimpleDateFormat("HH");
                                    String currentDateandTime = sdf.format(new Date());
                                    if (Integer.parseInt(currentDateandTime) >= 18) {
                                        if (isTimeAutomatic(mContext)) {
                                            if (manager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
                                                ShowCheckOutConfirmationDialog();

                                            } else {
                                                Utils.showToast(MenuActivity.this, "Please Enable GPS");
                                            }
                                        } else {
                                            Utils.showToast(MenuActivity.this, "Please enabled Automatic date & time");
                                        }
                                    } else {
                                        Utils.showToast(MenuActivity.this, "Please Submit After 6pm");
                                    }
                                } else if (Constants.nickName.toLowerCase().matches("nimbus") || Constants.nickName.toLowerCase().matches("supershakti")) {
                                    if (manager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
                                        ShowCheckOutConfirmationDialog();
                                    } else {
                                        Utils.showToast(MenuActivity.this, "Please Enable GPS");
                                    }
                                } else {
                                    ShowCheckOutConfirmationDialog();
                                }
                            } else {
                                if (Constants.nickName.equalsIgnoreCase("nimbus")) {
                                    Utils.showColorToast(MenuActivity.this, "Please give Attendance first");
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please give Attendance first");
                                }
                            }
                        } else {
                            Toast.makeText(mContext, "Internet connection not available. You can not checkout without an active internet connection on your device.", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        if (Constants.nickName.equalsIgnoreCase("nimbus")) {
                            Utils.showColorToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
//                        });
//                    });
                } else if (v == mLinearLayoutHint) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
                    AlertDG.setTitle("Please Note");
                    final String hintstatus = PreferenceData.getHelpStockAuditConfirm(mContext);
                    String alertMessage;
                    if (hintstatus.matches("disable")) {
                        alertMessage = "Are you sure you want to enable hints";
                    } else {
                        alertMessage = "Are you sure you want to disable hints";
                    }
                    AlertDG.setMessage(alertMessage);
                    AlertDG.setPositiveButton("OK", (dialog, which) -> {
                        try {
                            if (hintstatus.matches("disable")) {
                                PreferenceData.setHelpStockAuditConfirm(mContext, "enable");
                                hintLayOutImageView.setImageResource(R.drawable.on);
                            } else {
                                PreferenceData.setHelpStockAuditConfirm(mContext, "disable");
                                hintLayOutImageView.setImageResource(R.drawable.off);
                            }
                        } catch (Exception ignored) {
                        }
                    });
                    AlertDG.setNegativeButton("CANCEL", (dialog, which) -> {
                    });
                    AlertDG.setCancelable(true);
                    AlertDG.create().show();
                } else if (v == mLinearLayoutCustomer) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("LR") || mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("WO")
                            || mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("Holiday")) {
                        Utils.showToast(mContext, "You are on leave today");
                        return;
                    }
                    Intent intent = new Intent(MenuActivity.this, AddNewCustActivity.class);
                    Constants.isOrederToNewCustomer = false;
                    startActivity(intent);
                } else if (v == catalogue_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    ArrayList<CatalogueInfoDetails> catalogueList = mAceDnsDatabase.getCatalogueVal();
                    if (catalogueList != null && !catalogueList.isEmpty()) {
                        if (catalogueList.size() > 1) {
                            ArrayList<String> verticalsList = new ArrayList<>();
                            for (int i = 0; i < catalogueList.size(); i++) {
                                verticalsList.add(catalogueList.get(i).getVertical() + "^" + catalogueList.get(i).getFileName() + "^" + catalogueList.get(i).getFileVersion());
                            }
                            ShowCustomerListDialogToCheckIn(verticalsList);
                        } else {
                            Constants.catalogueorSchemeVal = catalogueList.get(0).getVertical() + "^" + catalogueList.get(0).getFileName() + "^" + catalogueList.get(0).getFileVersion();
                            makeCatalogLoadingProcess();
                        }
                    } else {
                        Toast.makeText(mContext, "No catalogue found in your database, Please Synchronize Data", Toast.LENGTH_SHORT).show();
                    }
                } else if (v == scheme_pdf_layout) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (cd.isConnectingToInternet()) {
                        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
                        masterApiCallingFlag = false;
                        new Thread() {
                            public void run() {
                                new commonAsyncTaskMaster(mContext, "branchwise_scheme_PDF");
                            }
                        }.start();
                    } else {
                        Utils.makePdfViewingProcess(mContext);
                    }
                } else if (v == scheme_pdf_layout_without_branch) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (cd.isConnectingToInternet()) {
                        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
                        masterApiCallingFlag = false;
                        new Thread() {
                            public void run() {
                                new commonAsyncTaskMaster(mContext, "scheme_pdf");
                            }
                        }.start();
                    } else {
                        Utils.makePdfViewingProcessWithoutBranch(mContext);
                    }
                } else if (v == mLinearLayoutRoute) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    showCreateRouteDialog();
                } else if (v == mLinearLayoutUser) {
                    showImageSourceDialog();
                } else if (v == mLinearLayoutReset) {
                    mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    if (Utils.NotCheckedOut(mContext) && !Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                        Utils.showCommonAlertDialog(mContext, "Please Note", "You have already Checked In at " + PreferenceData.getCheckInOutEmpName(mContext) + ". Please Check Out before go to next task.");
                    } else {
                        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
                        AlertDG.setTitle("Please Note");
                        AlertDG.setMessage("This will delete all your data stored locally.");
                        AlertDG.setPositiveButton("OK", (dialog, which) -> {
                            try {
                                File outputFile = new File(Utils.getAppStoragePath(mContext));
                                if (outputFile.exists()) {
                                    deleteTempFolderRecursive(outputFile);
                                }
                                Handler mHandler = new Handler();
                                mHandler.postDelayed(() -> {
                                    RegisterActivities.removeAllActivities();
                                    Intent intent = new Intent(MenuActivity.this, SplashActivity.class);
                                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                                    startActivity(intent);
                                }, 2000);
                            } catch (Exception e) {
                                Toast.makeText(mContext, "Data reset failed. Please restart the device and try again.", Toast.LENGTH_LONG).show();
                            }
                        });
                        AlertDG.setNegativeButton("CANCEL", (dialog, which) -> {
                        });
                        AlertDG.setCancelable(true);
                        AlertDG.create().show();
                    }
                }
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        Utils.InitialiseSETUPTableData(MenuActivity.this);
        refresh_noti_count();
        if (mAceDnsTransactionDatabase.checkUnuploadedPush()) {
            mImageViewImageNotification.setImageResource(R.drawable.notf2_unread);
        } else {
            mImageViewImageNotification.setImageResource(R.drawable.notf);
        }
    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            event.startTracking();
            if (!isKeyLongPress) {
                return true;
            }
        }
        return super.onKeyUp(keyCode, event);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            event.startTracking();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        mDrawerToggle.syncState();
    }

    @Override
    public void onConfigurationChanged(@NonNull Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        mDrawerToggle.onConfigurationChanged(newConfig);
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        if (mDrawerToggle.onOptionsItemSelected(item)) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        try {
            LocationTrackerObject.stopLocationUpdates();
        } catch (Exception ignored) {
        }
    }

    @Override
    public boolean onKeyLongPress(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            return true;
        }
        return super.onKeyLongPress(keyCode, event);
    }

    public void CheckinProcessAfterProperLocationFetching() {
        if (mCustomerDetailsList.size() == 1) {
            LocationTrackerObject = new LocationTracker(mContext, "check in");
            Constants.selectedCheckINCustomer = mCustomerDetailsList.get(0);
            Log.d("TAG", "_DOWNLOAD_ CheckinProcessAfterProperLocationFetching: " + Constants.selectedCheckINCustomer.getCustomerType());
            visitSequenceCheckIn = false;
            checkInProcess(false);
        } else {
            ShowCustomerListDialogToCheckIn();
        }
    }

    public void ShowDoSuccessMessageDialog(String doSuccessMessage) {
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.CustomMaterialDialogTheme);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.show_success_message_material);
        mDialogCustomer.setCancelable(false);
        Button btn_add = mDialogCustomer.findViewById(R.id.btn_add);
        TextView empty_text_view = mDialogCustomer.findViewById(R.id.empty_text_view);
        empty_text_view.setText(HtmlCompat.fromHtml(doSuccessMessage, HtmlCompat.FROM_HTML_MODE_LEGACY));
        btn_add.setOnClickListener(v -> mDialogCustomer.cancel());
        mDialogCustomer.show();
    }

    private void goToRetailerappDashBoard() {
        if (cd.isConnectingToInternet()) {
            Utils.showProgressDialog(mContext, "Updating Data Please Wait..");
            new Thread() {
                public void run() {
                    new commonAsyncTaskMaster(mContext, "splash_screen_details");
                }
            }.start();
        }
    }

    @SuppressLint("SetTextI18n")
    public static void refresh_noti_count() {
        AceDnsTransactionDatabase mAceDnsTransactionDatabaseOBJ;
        mAceDnsTransactionDatabaseOBJ = new AceDnsTransactionDatabase(mContext);
        final List<NotificationDetails> al_store_Unread_NOTI = mAceDnsTransactionDatabaseOBJ.get_unread_Notification();
        ((Activity) mContext).runOnUiThread(() -> {
            tv_NotificationOBJ.setText("" + al_store_Unread_NOTI.size());
            if (Constants.receive_notification.equals("TRUE")) {
                Constants.receive_notification = "";
                mAceDnsTransactionDatabase._updateNOTIFICATION(Constants.notification_id);
                boolean showManagerActivity = Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("yes") && Constants.notification_type.equalsIgnoreCase("manager_activity");
                String message = Constants.notification_message;
                Intent intentNot = new Intent(mContext, NotificationReceiverActivity.class);
                intentNot.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                intentNot.putExtra("Message", message);
                intentNot.putExtra("notification_id", Constants.notification_id);
                intentNot.putExtra("From", "App");
                intentNot.putExtra("ack_id", Constants.notification_ack_id);
                intentNot.putExtra("hasRead", !Constants.notification_ack_id.isEmpty());
                intentNot.putExtra("showManagerActivity", showManagerActivity);
                mContext.startActivity(intentNot);
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public static void showNotificationDialog() {
        final ArrayList<NotificationDetails> notfList = mAceDnsTransactionDatabase.getAllNotificationsReceived();
        if (!notfList.isEmpty()) {
            final Dialog stkDialog = new Dialog(mContext, R.style.PauseDialog);
            stkDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stkDialog.setContentView(R.layout.select_from_list);
            stkDialog.setCancelable(false);
            TextView title = stkDialog.findViewById(R.id.title);
            title.setText("Notification Hub");
            ListView dialogList = stkDialog.findViewById(R.id.list);
            NotificationAdapter adapter1 = new NotificationAdapter(mContext, R.layout.notf_child, notfList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                new GPSTracker(mContext);
                Handler handler = new Handler();
                handler.postDelayed(() -> {
                    NotificationDetails detailsObj = notfList.get(arg2);
                    String message = detailsObj.getMessage();
                    Intent intentNot = new Intent(mContext, NotificationReceiverActivity.class);
                    boolean showManagerActivity = false;
                    intentNot.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    intentNot.putExtra("Message", message);
                    intentNot.putExtra("notification_id", detailsObj.getNotificationId());
                    intentNot.putExtra("From", "App");
                    intentNot.putExtra("ack_id", detailsObj.getAckId());
                    boolean hasRead = !detailsObj.getAckId().isEmpty();
                    if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("yes") && detailsObj.getNotificationType().equalsIgnoreCase("manager_activity")) {
                        showManagerActivity = true;
                    }
                    intentNot.putExtra("hasRead", hasRead);
                    intentNot.putExtra("showManagerActivity", showManagerActivity);
                    mContext.startActivity(intentNot);
                    stkDialog.cancel();
                }, 1000);
            });
            Button cancel = stkDialog.findViewById(R.id.btn_cncl);
            cancel.setOnClickListener(arg0 -> stkDialog.cancel());
            Button create_route = stkDialog.findViewById(R.id.create_route);
            create_route.setVisibility(GONE);
            stkDialog.show();
        } else {
            Utils.showToast(mContext, "No Notifications found..");
        }
    }

    private void showSlidingImages() {
        RelativeLayout slidingImagesLayout = findViewById(R.id.slidingImagesLayout);
        slidingImagesLayout.setVisibility(VISIBLE);
        int[] images = {R.drawable.slider, R.drawable.slider, R.drawable.slider};
        viewPager = findViewById(R.id.viewPagerOne);
        ViewPagerImageSLiderAdapter myCustomPagerAdapter = new ViewPagerImageSLiderAdapter(mContext, images);
        viewPager.setAdapter(myCustomPagerAdapter);
        TabLayout tabLayout = findViewById(R.id.tabDots);
        tabLayout.setupWithViewPager(viewPager, true);
    }

    @SuppressLint("SimpleDateFormat")
    private void autoCheckOutProcess() {
        try {
            if (Utils.NotCheckedOut(mContext)) {
                PreferenceData pd = new PreferenceData();
                String checkin_time_str = pd.getCheckInTime(mContext);
                String[] checkinTimeArray = checkin_time_str.split(" ");
                if (checkinTimeArray.length > 0) {

                    String timeStamp = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
                    String checkinDate = checkinTimeArray[0];
                    if (!checkinDate.matches(timeStamp)) {
                        String out_time = checkinDate + " 23:59:59";
                        String customer_code = "";
                        if (Constants.selectedCheckINCustomer != null) {
                            customer_code = Constants.selectedCheckINCustomer.getCustomerCode();
                        } else {
                            customer_code = PreferenceData.getCheckInOutEmpCode(mContext);
                        }
                        setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
                        String timeStamps = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        String trans_id = "CI" + Constants.employeeDetailObject.getEmpCode() + timeStamps;
                        mAceDnsTransactionDatabase.insertCheckInOut(trans_id, checkin_time_str, customer_code, out_time, "auto check out", "", "", "");
                        mAceDnsTransactionDatabase.insertToLocationTable("CI", timeStamps);
                        clearAccuracyLatLong();
                        mAceDnsDatabase.deleteCheckInValue();
                        new TRANS_SubmitCheckInCheckOut(mContext, false, "SUBMIT").execute();
                    }
                }
            }
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SimpleDateFormat")
    private void launchCameraToTakeImage() {
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
        mSImageName = imageName;
        mImagePath = Utils.getAppStoragePath(mContext) + imageName;
        mImageFile = new File(mImagePath);
        try {
            mImageFile.createNewFile();
        } catch (IOException ignored) {
        }
        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            mFileUri = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", mImageFile);
        } else {
            mFileUri = Uri.fromFile(mImageFile);
        }
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }

    public void prepareFeatureList() {
        mMenuList = new ArrayList<>();
        try {
            if (!Constants.menuDetailsObj.getRoutePlan().isEmpty() && Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan") && !sale_access.equalsIgnoreCase("BD")) {
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.route_plan);
                    menuObj.setFeatureName("Route\nPlan");
                    mMenuList.add(menuObj);
                }
            }
        } catch (Exception ignored) {
        }
        boolean checkInOut = mAceDnsDatabase.MenuAccess("check_in_out");
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && checkInOut && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            if (!mAceDnsDatabase.isUserCheckedin()) {
                menuObj.setFeatureName("CheckInOut");
                menuObj.setResourceId(R.drawable.checkin);
            } else {
                menuObj.setFeatureName("CheckInOutOut");
                menuObj.setResourceId(R.drawable.checkout);
            }
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.gethierarchical_report().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("hierarchical_report") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("hierarchical_report")) {
                        addHNierarchicalReportToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("hierarchical_report")) {
                        addHNierarchicalReportToMenu();
                    }
                }
            } else {
                addHNierarchicalReportToMenu();
            }
        }
        boolean saudaallocationapp = mAceDnsDatabase.MenuAccess("sauda_allocation_app");
        if (Constants.menuDetailsObj.getSaudaAllocationfromApp().equalsIgnoreCase("yes") && saudaallocationapp && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("SaudaAllocationApp");
            menuObj.setResourceId(R.drawable.sauda_alloc);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("sauda") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("SaudaAllocation");
            menuObj.setResourceId(R.drawable.sauda_booking);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("van_sales") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("vanSales");
            menuObj.setResourceId(R.drawable.vansales);
            mMenuList.add(menuObj);
        }
        if (isStockAuditOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("stk_audit")) {
                        addStockAuditInMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("stk_audit")) {
                        addStockAuditInMenu();
                    }
                }
            } else {
                addStockAuditInMenu();
            }
        }
        if (isaJointWorkOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("joint_work")) {
                        addJointWorkToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("joint_work")) {
                        addJointWorkToMenu();
                    }
                }
            } else {
                addJointWorkToMenu();
            }
        }
        if (isaNotesInfoOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("notes_and_info")) {
                        addNotesInfoInMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("notes_and_info")) {
                        addNotesInfoInMenu();
                    }
                }
            } else {
                addNotesInfoInMenu();
            }
        }
        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("doctor_visit")) {
                        addDoctorVisitMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("doctor_visit")) {
                        addDoctorVisitMenu();
                    }
                }
            } else {
                addDoctorVisitMenu();
            }
        }
        if (isOrderOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("order")) {
                        addOrderOrSaleInMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("order")) {
                        addOrderOrSaleInMenu();
                    }
                }
            } else {
                addOrderOrSaleInMenu();
            }
        }
        boolean orderstatus = mAceDnsDatabase.MenuAccess("order_status");
        if (Constants.menuDetailsObj.getOrderStatus().equalsIgnoreCase("yes") && orderstatus && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn()) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("OrderStatus");
                menuObj.setResourceId(R.drawable.order_status);
                mMenuList.add(menuObj);
            }
        }
        if (Constants.menuDetailsObj.getcollection_forecast().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection_forecast") && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn()) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("CollectionForecast");
                menuObj.setResourceId(R.drawable.collectionforcast);
                mMenuList.add(menuObj);
            }
        }
        boolean business_prospect = mAceDnsDatabase.MenuAccess("business_prospect");
        if ((Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("customized") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) && business_prospect && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn() && !Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("business_prospect")) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("Business\nProspect");
                menuObj.setResourceId(R.drawable.business_prospect);
                mMenuList.add(menuObj);
            }
            if (NotCheckedOut(mContext) && Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("business_prospect")) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("Business\nProspect");
                menuObj.setResourceId(R.drawable.business_prospect);
                mMenuList.add(menuObj);
            }
        }
        if (Constants.menuDetailsObj.getgift_delivery().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("gift_delivery") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("gift_delivery")) {
                        AddGiftDeliveryToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("gift_delivery")) {
                        AddGiftDeliveryToMenu();
                    }
                }
            } else {
                AddGiftDeliveryToMenu();
            }
        }
        if (Constants.menuDetailsObj.getOdometer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("odometer") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("odometer")) {
                        addOdometer();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("odometer")) {
                        addOdometer();
                    }
                }
            } else {
                addOdometer();
            }
        }
        String addCust = mAceDnsDatabase.getSettingAddCustomer();
        if (Constants.menuDetailsObj.getCI_logic().equalsIgnoreCase("yes") && addCust.toLowerCase().matches("yes") && mAceDnsDatabase.MenuAccess("CI_logic") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("counter")) {
                        addCounter();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("counter")) {
                        addCounter();
                    }
                }
            } else {
                addCounter();
            }
        }
        mAceDnsDatabase.getSettingSisReport();
        if (Constants.sis_emp_data_startTarget.toUpperCase().matches("YES") && mAceDnsDatabase.MenuAccess("sis_report") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("sis_emp_data")) {
                        addSisEmpData();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("sis_emp_data")) {
                        addSisEmpData();
                    }
                }
            } else {
                addSisEmpData();
            }
        }
        if (Constants.sis_emp_data_startTarget.toUpperCase().matches("YES") && mAceDnsDatabase.MenuAccess("bd_sis_report") && sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("bd_sis_emp_data")) {
                        addBdSisEmpData();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("bd_sis_emp_data")) {
                        addBdSisEmpData();
                    }
                }
            } else {
                addBdSisEmpData();
            }
        }
        if (isMarketFeedbackOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("market_feedback")) {
                        marketfeedbackMenuAddingProcess();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("market_feedback")) {
                        marketfeedbackMenuAddingProcess();
                    }
                }
            } else {
                marketfeedbackMenuAddingProcess();
            }
        }
//        if (Constants.nickName.equalsIgnoreCase("STAR2")) {
//            if (checkIfCheckInOutOn()) {
//                if (NotCheckedOut(mContext)) {
//                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("leader_board")) {
//                        addLeaderBoardMenu();
//                        addmanchtechMenu();
//                    }
//                } else {
//                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("leader_board")) {
//                        addLeaderBoardMenu();
//                        addmanchtechMenu();
//                    }
//                }
//            } else {
//                if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("leader_board")) {
//                    addLeaderBoardMenu();
//                    addmanchtechMenu();
//                }
//                addmanchtechMenu();
//            }
//        }
        if (isManchtechOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("star_pravesh")) {
                        addmanchtechMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("star_pravesh")) {
                        addmanchtechMenu();
                    }
                }
            } else {
                addmanchtechMenu();
            }
        }

        try {
            empLevel = mAceDnsDatabase.getEmpLevel(Constants.employeeDetailObject.getEmpCode());
            Log.d("TAG", "_DOWNLOAD_ prepareFeatureList: " + empLevel);
            if (mAceDnsDatabase.isUserCheckedin() && !empLevel.equalsIgnoreCase("NT_TO") && !empLevel.equalsIgnoreCase("NT")) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("sbg_menu");
                menuObj.setResourceId(R.drawable.sbg);
                mMenuList.add(menuObj);
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ prepareFeatureList: " + e.getMessage());
        }

        // add new menu
        String[] surveymenu = Constants.surveyFormDetailsObj.getSurveySubMenuDetails().split(",");
        for (String menuname : surveymenu) {
            if (menuname.equalsIgnoreCase("Customer Duplicacy Check") && !sale_access.equalsIgnoreCase("BD")) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("CustomerDuplicacyCheck");
                menuObj.setResourceId(R.drawable.cdc);
                mMenuList.add(menuObj);
            }
        }


//        if (isLeader_boardOn()) {
//            if (checkIfCheckInOutOn()) {
//                if (NotCheckedOut(mContext)) {
//                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("leader_board")) {
//                        addLeaderBoardMenu();
//                    }
//                } else {
//                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("leader_board")) {
//                        addLeaderBoardMenu();
//                    }
//                }
//            } else {
//                addLeaderBoardMenu();
//            }
//        }
        if (isMarketFeedbackSiteLeadApproveRejectOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("site_visit_approval")) {
                        addSiteApprovalToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("site_visit_approval")) {
                        addSiteApprovalToMenu();
                    }
                }
            } else {
                addSiteApprovalToMenu();
            }
        }
        if (isLeadGenerationApproveRejectOn() && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("lead_generation_approval")) {
                        addLeadGenerationApprovalToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("lead_generation_approval")) {
                        addLeadGenerationApprovalToMenu();
                    }
                }
            } else {
                addLeadGenerationApprovalToMenu();
            }
        }
        boolean samplingaccess = mAceDnsDatabase.MenuAccess("product_promotion");
        if (Constants.menuDetailsObj.getSampling().equalsIgnoreCase("yes") && samplingaccess && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("Sampling");
            menuObj.setResourceId(R.drawable.product_prom);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getbargain().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("bargain") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("bragain");
            menuObj.setResourceId(R.drawable.bargain2);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getDO().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("do") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("do");
            menuObj.setResourceId(R.drawable.delivery_order2);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getDO_status().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("DO_status")) { //&&!sale_access.equalsIgnoreCase("BD")
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("do_status")) {
                        addDOToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("do_status")) {
                        addDOToMenu();
                    }
                }
            } else {
                addDOToMenu();
            }
        }
        if (Constants.menuDetailsObj.getDashboard().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("dashboard") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("dashboard")) {
                        addDashboardMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("dashboard")) {
                        addDashboardMenu();
                    }
                }
            } else {
                addDashboardMenu();
            }
        }
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("collection")) {
                        addCollectionToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("collection")) {
                        addCollectionToMenu();
                    }
                }
            } else {
                addCollectionToMenu();
            }
        }
        if (Constants.menuDetailsObj.getapp_order_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("app_order_approval") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("app_order_approval")) {
                        addOrderApprovalMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("app_order_approval")) {
                        addOrderApprovalMenu();
                    }
                }
            } else {
                addOrderApprovalMenu();
            }
        }
        if (Constants.menuDetailsObj.getgrn().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("grn") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObjT = new MenuObj();
            menuObjT.setFeatureName("grn");
            menuObjT.setResourceId(R.drawable.grn);
            mMenuList.add(menuObjT);
        }
        if (Constants.menuDetailsObj.getorder_edit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("order_edit") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("order_edit")) {
                        addOrderEditToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("order_edit")) {
                        addOrderEditToMenu();
                    }
                }
            } else {
                addOrderEditToMenu();
            }
        }
        if (Constants.menuDetailsObj.getstock_audit_edit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("stock_audit_edit") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("stock_audit_edit")) {
                        addStockAuditEditToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("stock_audit_edit")) {
                        addStockAuditEditToMenu();
                    }
                }
            } else {
                addStockAuditEditToMenu();
            }
        }
        if ((Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("consolidated") || Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) && mAceDnsDatabase.MenuAccess("tour_expense") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("tour_exp")) {
                        addTourExpenseInMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("tour_exp")) {
                        addTourExpenseInMenu();
                    }
                }
            } else {
                addTourExpenseInMenu();
            }
        }
        if (Constants.menuDetailsObj.getTM_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TM_approval") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("TM_approval")) {
                        addTMAToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("TM_approval")) {
                        addTMAToMenu();
                    }
                }
            } else {
                addTMAToMenu();
            }
        }
        if (Constants.menuDetailsObj.getTM_approved_meeting().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TM_approved_meeting") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("TM_approved_meeting")) {
                        addTMSToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("TM_approved_meeting")) {
                        addTMSToMenu();
                    }
                }
            } else {
                addTMSToMenu();
            }
        }
        boolean merchandising = mAceDnsDatabase.MenuAccess("merchandising");
        if (Constants.menuDetailsObj.getCaptureImage().equalsIgnoreCase("yes") && merchandising && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("Merchandising");
            menuObj.setResourceId(R.drawable.capture_image);
            mMenuList.add(menuObj);
        }
        boolean replacement = mAceDnsDatabase.MenuAccess("replacement");
        if (Constants.menuDetailsObj.getReplacement().equalsIgnoreCase("yes") && replacement && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("Replacement");
            menuObj.setResourceId(R.drawable.replacement);
            mMenuList.add(menuObj);
        }
        boolean loyalty = mAceDnsDatabase.MenuAccess("loyalty");
        if (Constants.menuDetailsObj.getLoyalty().equalsIgnoreCase("yes") && loyalty && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("Loyalty");
            menuObj.setResourceId(R.drawable.payback);
            mMenuList.add(menuObj);
        }
        if (isSurveyOn()) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("survey")) {
                        addSurveyInMenuList();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("survey")) {
                        addSurveyInMenuList();
                    }
                }
            } else {
                addSurveyInMenuList();
            }
        }
        if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_care") && !sale_access.equalsIgnoreCase("BD")) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("retailer_care")) {
                        addRetailerCareToMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("retailer_care")) {
                        addRetailerCareToMenu();
                    }
                }
            } else {
                addRetailerCareToMenu();
            }
        }
        boolean access = mAceDnsDatabase.checkAccess("delete_transaction");
        if (Constants.menuDetailsObj.getDeleteTransaction().equalsIgnoreCase("yes") && access && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("Delete\nTransaction");
            menuObj.setResourceId(R.drawable.delete_trans);
            mMenuList.add(menuObj);
        }
        EmployeeMasterDetails obj = mAceDnsDatabase.getEmpHierarchyDetails(Constants.employeeDetailObject.getEmpCode());
        String level = "0";
        if (obj != null) {
            level = obj.getLevel();
        }
        if (Constants.menuDetailsObj.getMisReport().equalsIgnoreCase("yes") && Integer.parseInt(level) > 1 && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("MIS Report");
            menuObj.setResourceId(R.drawable.mis_report);
            mMenuList.add(menuObj);
        }
        boolean wholesalemenu = mAceDnsDatabase.MenuAccess("wholesaler_info");
        if (Constants.menuDetailsObj.getWholeSaleInfo().equalsIgnoreCase("yes") && wholesalemenu && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("WholeSaleInfo");
            menuObj.setResourceId(R.drawable.wholesale);
            mMenuList.add(menuObj);
        }
        boolean selfAppraisal = mAceDnsDatabase.MenuAccess("self_appraisal");
        if (Constants.menuDetailsObj.getSelfAppraisalDetails().equalsIgnoreCase("yes") && selfAppraisal && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn()) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("SelfAppraisal");
                menuObj.setResourceId(R.drawable.self_appraisal_pri);
                mMenuList.add(menuObj);
                SelfAppraisalDetails selfAppraisalSetup = mAceDnsDatabase.getTargetAchievementSetupDetails();
                if (selfAppraisalSetup.getWeekWise().equalsIgnoreCase("yes")) {
                    menuObj = new MenuObj();
                    menuObj.setFeatureName("SelfAppraisalWeekWise");
                    menuObj.setResourceId(R.drawable.self_appraisal_secnd);
                    mMenuList.add(menuObj);
                }
            }
        }
        if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("TDAllocationApp");
            if (!Constants.nickName.equalsIgnoreCase("asl")) {
                menuObj.setResourceId(R.drawable.td_allocation);
            } else {
                menuObj.setResourceId(R.drawable.td_allocation2);
            }
            mMenuList.add(menuObj);
        }
        boolean yellowCard = mAceDnsDatabase.MenuAccess("yellow_card");
        if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes") && yellowCard && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn()) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("YellowCard");
                menuObj.setResourceId(R.drawable.yellow_card);
                mMenuList.add(menuObj);
            }
        }
        boolean quotation = mAceDnsDatabase.MenuAccess("quotation");
        if (Constants.menuDetailsObj.getquotation().equalsIgnoreCase("yes") && quotation && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("quotation");
            menuObj.setResourceId(R.drawable.quotation);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getCRM_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("CRM_app") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("crm");
            menuObj.setResourceId(R.drawable.crm);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getreverseAuction().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("ra_sauda") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("raSauda");
            menuObj.setResourceId(R.drawable.ra);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getISP().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("ISP") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("isp");
            menuObj.setResourceId(R.drawable.isp);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getmonthly_report_mail().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("monthly_report") && !sale_access.equalsIgnoreCase("BD")) {
            String todayDate = Utils.changeDateFormat("yyyyMMdd", "dd", dateString);
            String todayDayOfWeek = Utils.changeDateFormat("yyyyMMdd", "EEE", dateString);
            if ((todayDate.matches("01") && !todayDayOfWeek.matches("Sun")) || (todayDate.matches("02") && todayDayOfWeek.matches("Mon"))) {
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("MonthlyReport");
                menuObj.setResourceId(R.drawable.monthly_report);
                mMenuList.add(menuObj);
            }
        }
        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_app") && !sale_access.equalsIgnoreCase("BD")) {
            if (CheckInTrueButNotCheckedIn()) {
                MenuObj menuObj;
                if (mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(Constants.employeeDetailObject.getEmpCode()).isEmpty()) {
                    menuObj = new MenuObj();
                    menuObj.setFeatureName("stockOut");
                    menuObj.setResourceId(R.drawable.stockout2);
                    mMenuList.add(menuObj);
                }
                menuObj = new MenuObj();
                menuObj.setFeatureName("IMEI_STATUS_REPORT");
                menuObj.setResourceId(R.drawable.imei_search);
                mMenuList.add(menuObj);
            }
        }
        if (Constants.menuDetailsObj.getStockist_visit().toLowerCase().matches("yes") && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj3 = new MenuObj();
            menuObj3.setFeatureName("stockist_visit");
            menuObj3.setResourceId(R.drawable.stockist);
            mMenuList.add(menuObj3);
        }
        if (!Constants.menuDetailsObj.getProp_form_details().isEmpty() && !sale_access.equalsIgnoreCase("BD")) {
            propelloFormAccessibilityValList = new ArrayList<>();
            propelloFormAccessibilityValList = mAceDnsTransactionDatabase.getPropAccessList(Constants.employeeDetailObject.getEmpCode());
            propelloFormValList = new ArrayList<>();
            String hintRemarksValString = Constants.menuDetailsObj.getProp_form_details();
            if (hintRemarksValString.contains("#")) {
                String[] arrayOfData = hintRemarksValString.split("#");
                propelloFormValList.addAll(Arrays.asList(arrayOfData));
            } else {
                propelloFormValList.add(hintRemarksValString);
            }
            if (!propelloFormAccessibilityValList.isEmpty()) {
                for (int i = 0; i < propelloFormValList.size(); i++) {
                    if (propelloFormValList.get(i).matches("tent form") && propelloFormAccessibilityValList.get(0).getTent_sheet().toUpperCase().matches("Y")) {
                        MenuObj menuObj3 = new MenuObj();
                        menuObj3.setFeatureName("tentform");
                        menuObj3.setResourceId(R.drawable.tentform);
                        mMenuList.add(menuObj3);
                    } else if (propelloFormValList.get(i).matches("knocking form") && propelloFormAccessibilityValList.get(0).getKnocking_sheet().toUpperCase().matches("Y")) {
                        MenuObj menuObj3 = new MenuObj();
                        menuObj3.setFeatureName("knockingform");
                        menuObj3.setResourceId(R.drawable.knokingform);
                        mMenuList.add(menuObj3);
                    } else if (propelloFormValList.get(i).matches("demo form") && propelloFormAccessibilityValList.get(0).getDemo_sheet().toUpperCase().matches("Y")) {
                        MenuObj menuObj3 = new MenuObj();
                        menuObj3.setFeatureName("demoform");
                        menuObj3.setResourceId(R.drawable.demoform);
                        mMenuList.add(menuObj3);
                    } else if (propelloFormValList.get(i).matches("groupleader form") && propelloFormAccessibilityValList.get(0).getGroup_leader_form().toUpperCase().matches("Y")) {
                        MenuObj menuObj3 = new MenuObj();
                        menuObj3.setFeatureName("groupleader");
                        menuObj3.setResourceId(R.drawable.groupleader);
                        mMenuList.add(menuObj3);
                    } else if (propelloFormValList.get(i).matches("telecaller form") && propelloFormAccessibilityValList.get(0).getTele_caller_form().toUpperCase().matches("Y")) {
                        MenuObj menuObj3 = new MenuObj();
                        menuObj3.setFeatureName("telecaller");
                        menuObj3.setResourceId(R.drawable.telecallerform);
                        mMenuList.add(menuObj3);
                    }
                }
            }
        }
        boolean activityreport = mAceDnsDatabase.MenuAccess("activity_report");
        if (Constants.menuDetailsObj.getActivityReport().equalsIgnoreCase("yes") && activityreport) {
            if (checkIfCheckInOutOn()) {
                if (NotCheckedOut(mContext)) {
                    if (Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("activity_report")) {
                        addActivityReportMenu();
                    }
                } else {
                    if (!Constants.menuDetailsObj.getcheck_in_out_menu_access().contains("activity_report")) {
                        addActivityReportMenu();
                    }
                }
            } else {
                addActivityReportMenu();
            }
        }
        boolean saudamisReport = mAceDnsDatabase.MenuAccess("sauda_mis");
        if (Constants.menuDetailsObj.getSaudaMis().equalsIgnoreCase("yes") && saudamisReport && !sale_access.equalsIgnoreCase("BD")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("SaudaMisReport");
            menuObj.setResourceId(R.drawable.mis);
            mMenuList.add(menuObj);
        }
        try {
            if (Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC") && !sale_access.equalsIgnoreCase("BD")) {
                btn_ta_da_again = findViewById(R.id.btn_ta_da_again);
                btn_ta_da_again.setVisibility(VISIBLE);
            }
        } catch (Exception ignored) {
        }
        try {
            mAceDnsDatabase.GETSurveyFormDetails();
            if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes") || Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site") && !sale_access.equalsIgnoreCase("BD")) {
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.remainder);
                    menuObj.setFeatureName("remainder");
                    mMenuList.add(menuObj);
                }
            }
        } catch (Exception ignored) {
        }
        try {
            if (!Constants.menuDetailsObj.getHoarding_emp_vendor().isEmpty() && Constants.menuDetailsObj.getHoarding_emp_vendor().equalsIgnoreCase("yess") && !sale_access.equalsIgnoreCase("BD")) {
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.cs_visit);
                    menuObj.setFeatureName("display_site_visit");
                    mMenuList.add(menuObj);
                }
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.kyc);
                    menuObj.setFeatureName("hoarding");
                    mMenuList.add(menuObj);
                }
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.take_order);
                    menuObj.setFeatureName("event");
                    mMenuList.add(menuObj);
                }
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.checkin);
                    menuObj.setFeatureName("in_shop");
                    mMenuList.add(menuObj);
                }
                if (CheckInTrueButNotCheckedIn()) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.checkin);
                    menuObj.setFeatureName("local_event");
                    mMenuList.add(menuObj);
                }
            }
        } catch (Exception ignored) {
        }


//         add new menu
        if (!mAceDnsDatabase.isUserCheckedin() && !sale_access.equalsIgnoreCase("BD")) {
            for (String menuname : surveymenu) {
                if (menuname.equalsIgnoreCase("Customer Duplicacy Check")) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setFeatureName("funnel");
                    try {
                        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
                        Log.d("TAG", "_DOWNLOAD_ LOST LEAD prepareFeatureList: " + mNewDatabaseForSiteLead.getCountLeadListMasterTableData(12, false));
                        String count = String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(12, false));
                        menuObj.setCount(count);
                    } catch (Exception e) {
                        Log.d("TAG", "_DOWNLOAD_ LOST LEAD prepareFeatureList: " + e.getMessage());
                        menuObj.setCount("0");
                    }

                    menuObj.setResourceId(R.drawable.lead_funnel_management_icon);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("Customer Duplicacy Check")) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setFeatureName("quotation_po");
                    menuObj.setResourceId(R.drawable.quotation_management_icon);
                    mMenuList.add(menuObj);
                }

//                if (menuname.equalsIgnoreCase("Customer Duplicacy Check")) {
//                    MenuObj menuObj = new MenuObj();
//                    menuObj.setFeatureName("outstanding_report");
//                    menuObj.setResourceId(R.drawable.outstanding_report_icon);
//                    mMenuList.add(menuObj);
//                }
//
//                if (menuname.equalsIgnoreCase("Customer Duplicacy Check")) {
//                    MenuObj menuObj = new MenuObj();
//                    menuObj.setFeatureName("credit_limit");
//                    menuObj.setResourceId(R.drawable.customer_credit_limit_icon);
//                    mMenuList.add(menuObj);
//                }
            }
        }
    }

    public void addHNierarchicalReportToMenu() {
        MenuObj menuObjT = new MenuObj();
        menuObjT.setFeatureName("hierarchicalReport");
        menuObjT.setResourceId(R.drawable.report_card);
        mMenuList.add(menuObjT);
    }

    public void addStockAuditEditToMenu() {
        MenuObj menuObjT1 = new MenuObj();
        menuObjT1.setFeatureName("stock_audit_edit");
        menuObjT1.setResourceId(R.drawable.stock_audit_edit);
        mMenuList.add(menuObjT1);
    }

    public void addOdometer() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("odometer");
        menuObj.setResourceId(R.drawable.odometer);
        mMenuList.add(menuObj);
    }

    public void addCounter() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("counter");
        menuObj.setResourceId(R.drawable.counter);
        mMenuList.add(menuObj);
    }

    public void addSisEmpData() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("sis_emp_data");
        menuObj.setResourceId(R.drawable.sis);
        mMenuList.add(menuObj);
    }

    public void addBdSisEmpData() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("bd_sis_emp_data");
        menuObj.setResourceId(R.drawable.bd_sis);
        mMenuList.add(menuObj);
    }

    public void addOrderEditToMenu() {
        MenuObj menuObjT1 = new MenuObj();
        menuObjT1.setFeatureName("order_edit");
        menuObjT1.setResourceId(R.drawable.order_edit);
        mMenuList.add(menuObjT1);
    }

    public void addOrderApprovalMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("appOrderApproval");
        menuObj.setResourceId(R.drawable.app_order_approval);
        mMenuList.add(menuObj);
    }

    public void AddGiftDeliveryToMenu() {
        MenuObj menuObjGD = new MenuObj();
        menuObjGD.setFeatureName("giftDelivery");
        menuObjGD.setResourceId(R.drawable.gift_delivery);
        mMenuList.add(menuObjGD);
    }

    public void addJointWorkToMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("jointWOrkObservation");
        menuObj.setResourceId(R.drawable.jointwork);
        mMenuList.add(menuObj);
    }

    public boolean isaJointWorkOn() {
        return Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("joint_work");
    }

    public void addNotesInfoInMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Customer\nFeedback");
        menuObj.setResourceId(R.drawable.notes);
        mMenuList.add(menuObj);
    }

    public boolean isaNotesInfoOn() {
        return Constants.menuDetailsObj.getNotesInfo().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("customer_feedback");
    }

    public void addDOToMenu() {
        MenuObj menuObjx = new MenuObj();
        menuObjx.setFeatureName("DO_status");
        if (Constants.nickName.equalsIgnoreCase("asl")) {
            menuObjx.setResourceId(R.drawable.track_order2);
        } else {
            menuObjx.setResourceId(R.drawable.trackorder);
        }
        mMenuList.add(menuObjx);
    }

    public void addTMAToMenu() {
        MenuObj menuObjx = new MenuObj();
        menuObjx.setFeatureName("TM_approval");
        menuObjx.setResourceId(R.drawable.tma);
        mMenuList.add(menuObjx);
    }

    public void addTMSToMenu() {
        MenuObj menuObjx = new MenuObj();
        menuObjx.setFeatureName("TM_approved_meeting");
        menuObjx.setResourceId(R.drawable.tms);
        mMenuList.add(menuObjx);
    }

    public void addRetailerCareToMenu() {
        MenuObj menuObj4 = new MenuObj();
        menuObj4.setFeatureName("RetailerCare");
        menuObj4.setResourceId(R.drawable.retailer_care);
        mMenuList.add(menuObj4);
    }

    public void marketfeedbackMenuAddingProcess() {
        boolean shouldenablemarketFeedback = !Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") || !mAceDnsDatabase.MenuAccess("retailer_app")
                || mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(Constants.employeeDetailObject.getEmpCode()).isEmpty();
        if (shouldenablemarketFeedback) {
            addMarketFeedbackToMenu();
        }
    }

    public void addActivityReportMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Activity\nReport");
        if (!Constants.nickName.equalsIgnoreCase("asl")) {
            menuObj.setResourceId(R.drawable.activity_report);
        } else {
            menuObj.setResourceId(R.drawable.activity2);
        }
        mMenuList.add(menuObj);
    }

    public void addTourExpenseInMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Tour\nExpenses");
        menuObj.setResourceId(R.drawable.tour_exp);
        mMenuList.add(menuObj);
    }

    public void addCollectionToMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Collection");
        if (!Constants.nickName.equalsIgnoreCase("asl")) {
            menuObj.setResourceId(R.drawable.collection);
        } else {
            menuObj.setResourceId(R.drawable.collection_asl);
        }
        mMenuList.add(menuObj);
    }

    public void addMarketFeedbackToMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("FeedBack");
        menuObj.setResourceId(R.drawable.feedback);
        mMenuList.add(menuObj);
    }

    public void addSiteApprovalToMenu() {
        if (Constants.menuDetailsObj.getSite_visit_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("site_visit_approval")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("site_visit_approval");
            menuObj.setResourceId(R.drawable.sitelead_approval);
            mMenuList.add(menuObj);
        }
    }

    public void addLeadGenerationApprovalToMenu() {
        if (Constants.menuDetailsObj.getLead_generation_approval().equalsIgnoreCase("yes") && empLevel.equalsIgnoreCase("NT_TO") && mAceDnsDatabase.MenuAccess("lead_generation_approval")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("lead_generation_approval");
            menuObj.setResourceId(R.drawable.lead_generation_approval);
            mMenuList.add(menuObj);
        }
    }

    public void addLeaderBoardMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Leader_Board");
        menuObj.setResourceId(R.drawable.leaderboard);
        mMenuList.add(menuObj);
    }

    public void addmanchtechMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("manchtech");
        menuObj.setResourceId(R.drawable.co_new);
        mMenuList.add(menuObj);
    }

    public void addDashboardMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("dashboard");
        menuObj.setResourceId(R.drawable.dashboard);
        mMenuList.add(menuObj);
    }

    public void addOrderOrSaleInMenu() {
        if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            addSaleToMenu();
        } else {
            addOrderMenu();
        }
    }

    public void addSaleToMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Sales");
        menuObj.setResourceId(R.drawable.sales);
        mMenuList.add(menuObj);
    }

    public void addStockAuditInMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Stock\nAudit");
        menuObj.setResourceId(R.drawable.stk_audit);
        mMenuList.add(menuObj);
    }

    public void addOrderMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Order");
        menuObj.setResourceId(R.drawable.take_order);
        mMenuList.add(menuObj);
    }

    public void addDoctorVisitMenu() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("DoctorVisit");
        menuObj.setResourceId(R.drawable.doctorvisit);
        mMenuList.add(menuObj);
    }

    public boolean isCollectionOn() {
        return Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection");
    }

    public boolean isMarketFeedbackOn() {
        return Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("market_feedback");
    }

    public boolean isMarketFeedbackSiteLeadApproveRejectOn() {
        return Constants.menuDetailsObj.getSite_visit_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("site_visit_approval");
    }

    public boolean isLeadGenerationApproveRejectOn() {
        return Constants.menuDetailsObj.getLead_generation_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("lead_generation_approval");
    }

    public boolean isLeader_boardOn() {
        return Constants.menuDetailsObj.getLeaderboard().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("leader_board");
    }

    public boolean isManchtechOn() {
        return Constants.menuDetailsObj.getManchtech().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("star_pravesh");
    }

    public boolean isStockAuditOn() {
        return Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("stk_audit");
    }

    public boolean isOrderOn() {
        return Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("order");
    }

    private void addSurveyInMenuList() {
        MenuObj menuObj = new MenuObj();
        menuObj.setFeatureName("Survey");
        if (Constants.nickName.equalsIgnoreCase("TECPL")) {
            menuObj.setResourceId(R.drawable.kiosk);
        } else {
            if (Constants.surveyFormDetailsObj.getsurvey_menu_name().equalsIgnoreCase("opportunity")) {
                menuObj.setResourceId(R.drawable.opp);
            } else {
                if (Constants.nickName.equalsIgnoreCase("nimbus")) {
                    menuObj.setResourceId(R.drawable.installationexp);
                } else if (Constants.nickName.equalsIgnoreCase("coral")) {
                    menuObj.setResourceId(R.drawable.plumber);
                } else {
                    menuObj.setResourceId(R.drawable.marketoverview);
                }
            }
        }
        mMenuList.add(menuObj);
    }

    private boolean isSurveyOn() {
        return Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("survey");
    }

    private boolean CheckInTrueButNotCheckedIn() {
        boolean khmerFilterPassed;
        boolean checkInOut = mAceDnsDatabase.MenuAccess("check_in_out");
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && checkInOut) {
            khmerFilterPassed = !mAceDnsDatabase.isUserCheckedin();
        } else {
            khmerFilterPassed = true;
        }
        return khmerFilterPassed;
    }

    private boolean checkIfCheckInOutOn() {
        boolean khmerFilterPassed;
        khmerFilterPassed = Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("check_in_out");
        return khmerFilterPassed;
    }

    @SuppressLint({"NewApi", "SetTextI18n"})
    public void initView() {
        mButtonLogout = findViewById(R.id.btn_logout);
        mButtonAttendence = findViewById(R.id.btn_attendance);
        btn_ta_da_again = findViewById(R.id.btn_ta_da_again);
        localStorage = new LocalStorage(mContext);
        if (localStorage.getTADAPubPri().matches("public")) {
            btn_ta_da_again.setBackgroundResource(R.drawable.bus_icon);
        } else {
            btn_ta_da_again.setBackgroundResource(R.drawable.bike_icon);
        }
        if (!mAceDnsDatabase.MenuAccess("attendance")) {
            mButtonAttendence.setVisibility(GONE);
        }
        memuactivityMainLayout = findViewById(R.id.memuactivityMainLayout);
        tv_NotificationOBJ = findViewById(R.id.tv_Notification);
        closingStockTv = findViewById(R.id.closingStockTv);
        try {
            if (Constants.userDetailsObj.getFcm() != null && Constants.userDetailsObj.getFcm().contains("yes")) {
                tv_NotificationOBJ.setVisibility(VISIBLE);
            }
        } catch (Exception ignored) {
        }
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mImageViewUserPic = findViewById(R.id.img_user);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(GONE);
        }
        byte[] imgArray = mAceDnsDatabase.getUserImage();
        if (imgArray != null) {
            Bitmap bitmap = BitmapFactory.decodeByteArray(imgArray, 0, imgArray.length);
            mImageViewUserPic.setImageBitmap(bitmap);
        }
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonLogout.setOnClickListener(this);
        mButtonAttendence.setOnClickListener(this);
        btn_ta_da_again.setOnClickListener(this);
        mLinearLayoutSalesPerformance = findViewById(R.id.sales_performance_layout);
        if (Constants.menuDetailsObj.getSalePerformance().equalsIgnoreCase("yes")) {
            mLinearLayoutSalesPerformance.setVisibility(VISIBLE);
        } else {
            mLinearLayoutSalesPerformance.setVisibility(GONE);
        }
        mLinearLayoutOption = findViewById(R.id.option_layout);
        mLinearLayoutUser = findViewById(R.id.user_details_layout);
        mDrawerLayout = findViewById(R.id.drawer_layout);
        mDrawerToggle = new ActionBarDrawerToggle(this, mDrawerLayout,
                R.drawable.option_menubackup, R.string.drawer_open,
                R.string.drawer_close) {

            public void onDrawerClosed(View view) {
                super.onDrawerClosed(view);
            }

            public void onDrawerOpened(View drawerView) {
                super.onDrawerOpened(drawerView);
            }
        };
        mDrawerLayout.setDrawerListener(mDrawerToggle);
        mLinearLayoutLogOut = findViewById(R.id.logout_layout);
        mLinearLayoutBackUp = findViewById(R.id.backup_layout);
        mLinearLayoutCheckOut = findViewById(R.id.chkout_layout);
        mLl_order_status_download_pdf_layout = findViewById(R.id.ll_order_status_download_pdf_layout);
        mll_drs_pdf_layout = findViewById(R.id.ll_drs_pdf_layout);
        ll_route_plan_approval_layout = findViewById(R.id.ll_route_plan_approval_layout);
        ll_kyc = findViewById(R.id.ll_kyc);
        mLinearLayoutHint = findViewById(R.id.hint_layout);
        mLinearLayoutHint.setVisibility(GONE);
        mLinearLayoutReset = findViewById(R.id.reset_layout);
        mLinearLayoutReset.setVisibility(GONE);
        mLinearLayoutCustomer = findViewById(R.id.new_cust_layout);
        mLinearLayoutStockAuditCustomer = findViewById(R.id.stock_audit_layout);
        db_layout = findViewById(R.id.db_layout);
        catalogue_layout = findViewById(R.id.catalogue_layout);
        scheme_pdf_layout = findViewById(R.id.scheme_pdf_layout);
        scheme_pdf_layout_without_branch = findViewById(R.id.scheme_pdf_layout_without_branch);
        telephonic_transaction_layout = findViewById(R.id.telephonic_transaction_layout);
        video_conference_layout = findViewById(R.id.video_conference_layout);
        video_conference_layout.setVisibility(GONE);
        mLinearLayoutHelp = findViewById(R.id.help_layout);
        if (Constants.menuDetailsObj.getgolden_rules().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("golden_rules")) {
            mLinearLayoutHelp.setVisibility(VISIBLE);
        } else {
            mLinearLayoutHelp.setVisibility(GONE);
        }
        if (Constants.nickName.toLowerCase().matches("nimbus") || Constants.nickName.toUpperCase().matches("NIMBUS")) {
            mLinearLayoutLogOut.setVisibility(GONE);
        }
        if (Constants.nickName.toUpperCase().matches("GOLDSTONET") || Constants.nickName.toUpperCase().matches("GOLDSTONE")) {
            mLinearLayoutHelp.setVisibility(GONE);
        }
        mLinearLayoutAgeing = findViewById(R.id.ageing_layout);
        if (Constants.menuDetailsObj.getOutstandingAgeing().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("outstanding_ageing")) {
            mLinearLayoutAgeing.setVisibility(VISIBLE);
        } else {
            mLinearLayoutAgeing.setVisibility(GONE);
        }
        hintLayOutImageView = findViewById(R.id.hintLayOutImageView);
        if (PreferenceData.getHelpStockAuditConfirm(mContext).matches("disable")) {
            hintLayOutImageView.setImageResource(R.drawable.off);
        } else {
            hintLayOutImageView.setImageResource(R.drawable.on);
        }
        mLinearLayoutOutstanding = findViewById(R.id.outstanding_layout);
        if (Constants.menuDetailsObj.getOutstanding().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("outstanding")) {
            mLinearLayoutOutstanding.setVisibility(VISIBLE);
        } else {
            mLinearLayoutOutstanding.setVisibility(GONE);
        }
        boolean pendingcontract = mAceDnsDatabase.MenuAccess("pending_contract");
        manageractivity_layout = findViewById(R.id.manageractivity_layout);
        mLinearLayoutPendingContract = findViewById(R.id.pendingcontract_layout);
        if (Constants.menuDetailsObj.getPendingContract().equalsIgnoreCase("yes") && pendingcontract) {
            mLinearLayoutPendingContract.setVisibility(VISIBLE);
        } else {
            mLinearLayoutPendingContract.setVisibility(GONE);
        }
        boolean saudaoutstandingageing = mAceDnsDatabase.MenuAccess("outstanding_ageing");
        mLinearLayoutSaudaOutstanding = findViewById(R.id.sauda_outstanding_layout);
        if (Constants.menuDetailsObj.getPendingContract().equalsIgnoreCase("yes") && saudaoutstandingageing) {
            mLinearLayoutSaudaOutstanding.setVisibility(VISIBLE);
        } else {
            mLinearLayoutSaudaOutstanding.setVisibility(GONE);
        }
        mLinearLayoutMerchandising = findViewById(R.id.issue_layout);
        if (!Constants.menuDetailsObj.getCaptureImage().equalsIgnoreCase("yes")) {
            mLinearLayoutMerchandising.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getCatalogue().equalsIgnoreCase("yes")) {
            catalogue_layout.setVisibility(VISIBLE);
        }
        if (Constants.menuDetailsObj.getbranchwise_scheme_PDF().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("branchwise_scheme_PDF")) {
            scheme_pdf_layout.setVisibility(VISIBLE);
        }
        if (Constants.menuDetailsObj.getscheme_pdf().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("scheme_pdf")) {
            scheme_pdf_layout_without_branch.setVisibility(VISIBLE);
        }
        try {
            if (Constants.menuDetailsObj.getTelephonicTransaction().equalsIgnoreCase("yes") && ((isCollectionOn()) || isOrderOn())) {
                telephonic_transaction_layout.setVisibility(VISIBLE);
                if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                    TextView teleTransactionTV = findViewById(R.id.teleTransactionTV);
                    teleTransactionTV.setText("Unplanned Visit");
                }
            }
        } catch (Exception ignored) {
        }
        mLinearLayoutClosingStock = findViewById(R.id.stock_layout);
        try {
            if (!Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes")) {
                mLinearLayoutClosingStock.setVisibility(GONE);
            } else if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_app")
                    && mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(Constants.employeeDetailObject.getEmpCode()).isEmpty()) {
                mLinearLayoutClosingStock.setVisibility(GONE);
            }
        } catch (Exception ignored) {
        }
        mcx_price_generation_layout = findViewById(R.id.mcx_price_generation_layout);
        if (Constants.menuDetailsObj.getgenerate_pricing_MCX().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("generate_pricing_MCX")) {
            mcx_price_generation_layout.setVisibility(VISIBLE);
        } else {
            mcx_price_generation_layout.setVisibility(View.GONE);
        }
        price_generation_layout = findViewById(R.id.price_generation_layout);
        price_release_layout = findViewById(R.id.price_release_layout);
        if (!mAceDnsDatabase.MenuAccess("checkout")) {
            mLinearLayoutCheckOut.setVisibility(GONE);
        }
        RoutePlanDetails mRoutePlanDetails = mAceDnsDatabase.getRoutePlanDetailsObj();
        String routePlanApproval = mRoutePlanDetails.getRoutePlanApproval();
        if (routePlanApproval.toLowerCase().matches("customize") && mAceDnsDatabase.MenuAccess("route plan approval")) {
            ll_route_plan_approval_layout.setVisibility(VISIBLE);
        }
        if (Constants.menuDetailsObj.getgenerate_pricing().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("generate_pricing")) {
            price_generation_layout.setVisibility(VISIBLE);
            price_release_layout.setVisibility(GONE);
        } else {
            price_generation_layout.setVisibility(View.GONE);
            price_release_layout.setVisibility(View.GONE);
        }
        mLinearLayoutMRP = findViewById(R.id.mrp_layout);
        try {
            if (!mAceDnsDatabase.MenuAccess("menu_pricelist")) {
                mLinearLayoutMRP.setVisibility(GONE);
            } else if (!Constants.orderFormDetailsObj.getSaudaSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                    if (!Constants.orderFormDetailsObj.getMrpDrpdwn().equalsIgnoreCase("dropdown")) {
                        mLinearLayoutMRP.setVisibility(GONE);
                    }
                } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
                    if (!Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                        mLinearLayoutMRP.setVisibility(GONE);
                    }
                } else {
                    mLinearLayoutMRP.setVisibility(GONE);
                }
            }
        } catch (Exception ignored) {
        }
        mLinearLayoutSyncData = findViewById(R.id.sync_layout);
        mLinearLayoutDeclarationRequest = findViewById(R.id.declarations_layout);
        mLinearLayoutProjectKhoj = findViewById(R.id.khoj_layout);
        mLinearLayoutsync_layout_feedback = findViewById(R.id.sync_layout_feedback);
        if (Constants.menuDetailsObj.getFeedback_backup() != null) {
            if (Constants.menuDetailsObj.getFeedback_backup().length() > 8) {
                mLinearLayoutsync_layout_feedback.setVisibility(VISIBLE);
            }
        }
        mLinearLayoutRoute = findViewById(R.id.new_route_layout);
        mLinearLayoutRoute.setVisibility(GONE);
        if (Constants.orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer")) {
            if (Constants.menuDetailsObj.getCI_logic().equalsIgnoreCase("yes")) {
                mLinearLayoutCustomer.setVisibility(GONE);
            } else {
                mLinearLayoutCustomer.setVisibility(VISIBLE);
            }
        } else {
            mLinearLayoutCustomer.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getCustomer_product_stock().equalsIgnoreCase("yes")) {
            mLinearLayoutStockAuditCustomer.setVisibility(VISIBLE);
        } else {
            mLinearLayoutStockAuditCustomer.setVisibility(GONE);
        }
        try {
            if (Constants.menuDetailsObj.getOrder_summary_PDF().toLowerCase().matches("yes")) {
                mLl_order_status_download_pdf_layout.setVisibility(VISIBLE);
            }
        } catch (Exception ignored) {
        }
        try {
            if (Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("yes")) {
                mll_drs_pdf_layout.setVisibility(VISIBLE);
            }
            if (Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("distributor wise")) {
                mll_drs_pdf_layout.setVisibility(VISIBLE);
            }
        } catch (Exception ignored) {
        }
        if (Constants.appMode.matches("development")) {
            db_layout.setVisibility(VISIBLE);
        } else {
            db_layout.setVisibility(GONE);
        }
        mImageViewImageNotification = findViewById(R.id.img_notf);
        if (mAceDnsTransactionDatabase.checkUnuploadedPush()) {
            mImageViewImageNotification.setImageResource(R.drawable.notf2_unread);
        } else {
            mImageViewImageNotification.setImageResource(R.drawable.notf);
        }
        mLinearLayoutNotification = findViewById(R.id.notf_layout);
        mLinearLayoutLogOut.setOnClickListener(this);
        mLinearLayoutBackUp.setOnClickListener(this);
        mLl_order_status_download_pdf_layout.setOnClickListener(this);
        mll_drs_pdf_layout.setOnClickListener(this);
        ll_route_plan_approval_layout.setOnClickListener(this);
        ll_kyc.setOnClickListener(this);
        mLinearLayoutCheckOut.setOnClickListener(this);
        mLinearLayoutHint.setOnClickListener(this);
        mLinearLayoutUser.setOnClickListener(this);
        mLinearLayoutReset.setOnClickListener(this);
        mLinearLayoutCustomer.setOnClickListener(this);
        db_layout.setOnClickListener(this);
        catalogue_layout.setOnClickListener(this);
        scheme_pdf_layout.setOnClickListener(this);
        scheme_pdf_layout_without_branch.setOnClickListener(this);
        telephonic_transaction_layout.setOnClickListener(this);
        video_conference_layout.setOnClickListener(this);
        mLinearLayoutAgeing.setOnClickListener(this);
        mLinearLayoutHelp.setOnClickListener(this);
        mLinearLayoutOutstanding.setOnClickListener(this);
        mLinearLayoutPendingContract.setOnClickListener(this);
        manageractivity_layout.setOnClickListener(this);
        mLinearLayoutSaudaOutstanding.setOnClickListener(this);
        mLinearLayoutMerchandising.setOnClickListener(this);
        mLinearLayoutClosingStock.setOnClickListener(this);
        mLinearLayoutMRP.setOnClickListener(this);
        mLinearLayoutStockAuditCustomer.setOnClickListener(this);
        price_generation_layout.setOnClickListener(this);
        mcx_price_generation_layout.setOnClickListener(this);
        price_release_layout.setOnClickListener(this);
        mLinearLayoutSyncData.setOnClickListener(this);
        mLinearLayoutDeclarationRequest.setOnClickListener(this);
        mLinearLayoutsync_layout_feedback.setOnClickListener(this);
        mLinearLayoutRoute.setOnClickListener(this);
        mLinearLayoutNotification.setOnClickListener(this);
        mLinearLayoutSalesPerformance.setOnClickListener(this);
        mTextViewUserName = findViewById(R.id.txt_username);
        mTextViewUserName.setText(Constants.employeeDetailObject.getEmpName());
        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("manager_activity")) {
            manageractivity_layout.setVisibility(VISIBLE);
        }
        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize") && mAceDnsDatabase.MenuAccess("manager_activity")) {
            manageractivity_layout.setVisibility(VISIBLE);
        }
        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_app")) {
            if (mAceDnsDatabase.CheckIfLowerMostLevelEmpForRetailerApp()) {
                mTextViewUserName.setText(mAceDnsDatabase.getCustomerNameRetailer());
            }
        }
        mGridViewMenu = findViewById(R.id.grid_menu);
        mMenuAdapter = new MenuAdapter(MenuActivity.this, R.layout.grid_child, mMenuList, false);
        mGridViewMenu.setAdapter(mMenuAdapter);
        mRelativeLayoutRefresh = findViewById(R.id.data_refresh_layout);
    }

    public void ShowAcheivementDialog() {
        startActivity(new Intent(MenuActivity.this, ActivityEmployeeTargetAcheivement.class));
    }

    public void ShowDownloadErrorDialog() {
        Constants.isCommpleteDownLoadComplete = true;
        final Dialog downloadErrorDialog = new Dialog(mContext, R.style.PauseDialog);
        downloadErrorDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        downloadErrorDialog.setContentView(R.layout.dialog_download_error);
        downloadErrorDialog.setCancelable(false);
        TextView errortext = downloadErrorDialog.findViewById(R.id.textViewError);
        errortext.setText(Constants.DownloadErrorMsg);
        Button submiterror = downloadErrorDialog.findViewById(R.id.buttonSubmitToAdmin);
        submiterror.setOnClickListener(v -> {
            downloadErrorDialog.dismiss();
            new DATA_EmailToDeveloperTask(MenuActivity.this, Constants.employeeDetailObject != null ? Constants.employeeDetailObject.getEmpCode() : "NA",
                    "Data download error " + Constants.DownloadErrorMsg.trim(),
                    Constants.employeeDetailObject != null ? Constants.employeeDetailObject.getEmpName() : "NA", true).execute();
            RegisterActivities.removeAllActivities();
        });
        downloadErrorDialog.show();
    }

    @SuppressLint({"SimpleDateFormat", "SetTextI18n"})
    public void doOnItemClickJob(String featureName) {
        String currentDate = dateString.substring(6, 8) + "-" + dateString.substring(4, 6) + "-" + dateString.substring(0, 4);
        if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("logistics") || Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("aac")) {
            todayPlanList = mAceDnsTransactionDatabase.getPlanForToday(currentDate);
            if (featureName.equalsIgnoreCase("CheckInOut")) {
                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")
                        && !Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                    if (!todayPlanList.isEmpty()) {
                        ChooseTransactionTypeForCheckIn();
                    } else {
                        Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                    }
                } else {
                    ChooseTransactionTypeForCheckIn();
                }
            } else if (featureName.equalsIgnoreCase("CheckInOutOut")) {
                try {
                    if (!Constants.menuDetailsObj.getindependent_check_in_out().equalsIgnoreCase("yes")) {
                        showNotesInfoDialogOnCheckOut();
                    } else {
                        showNotesInfoDialogOnCheckOut();
                    }
                } catch (Exception e) {
                    showNotesInfoDialogOnCheckOut();
                }
            } else {
                if (featureName.equalsIgnoreCase("Route\nPlan")) {
                    GotoCreateRoutePlan(featureName);
                } else if (featureName.equalsIgnoreCase("display_site_visit")) {
                    GotoLuxSiteVisit(featureName);
                } else if (featureName.equalsIgnoreCase("event")) {
                    GotoLuxSiteVisit(featureName);
                } else if (featureName.equalsIgnoreCase("hoarding")) {
                    GotoLuxSiteVisit(featureName);
                } else if (featureName.equalsIgnoreCase("in_shop")) {
                    GotoLuxSiteVisit(featureName);
                } else if (featureName.equalsIgnoreCase("local_event")) {
                    GotoLuxSiteVisit(featureName);
                } else if (featureName.equalsIgnoreCase("Activity\nReport")) {
                    startActivity(new Intent(MenuActivity.this, ActivityReportLanding.class));
                } else if (featureName.equalsIgnoreCase("sis_emp_data")) {
                    startActivity(new Intent(MenuActivity.this, NewSisSummeryActivity.class));
                } else if (featureName.equalsIgnoreCase("bd_sis_emp_data")) {
                    startActivity(new Intent(MenuActivity.this, BdSisActivity.class));
                } else if (featureName.equalsIgnoreCase("remainder")) {
                    Intent intent = new Intent(MenuActivity.this, RemainderActivityLanding.class);
                    intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, attendanceFilterString);
                }
            }
        } else if (featureName.equalsIgnoreCase("Survey")) {
            Log.d("TAG", "Survey Hit");
            gotoSurveyPage();
        } else if ((isAttendanceGiven || !mAceDnsDatabase.MenuAccess("attendance"))) {
            if (mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("LR") || mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("WO") || mAceDnsTransactionDatabase.getAttendanceTypeToday().startsWith("Holiday")) {
                if (!featureName.equalsIgnoreCase("Route\nPlan") && !featureName.equalsIgnoreCase("Activity\nReport")) {
                    Utils.showToast(mContext, "You are on leave today");
                    return;
                }
            }
            todayPlanList = mAceDnsTransactionDatabase.getPlanForToday(currentDate);
            if (featureName.equalsIgnoreCase("Order") || featureName.equalsIgnoreCase("RetailerCare") || featureName.equalsIgnoreCase("vanSales")) {
                if (!isCheckedOutToday) {
                    Constants.isVanSales = featureName.equalsIgnoreCase("vanSales");
                    Constants.transactionStartTime = Calendar.getInstance().getTime();
                    Constants.CurrentOrderCollectionTransactionType = "SO";
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan") && !isCILogicOn()) {
                        if (!todayPlanList.isEmpty()) {
                            GoToOrderProcess();
                        } else {
                            Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                        }
                    } else {
                        GoToOrderProcess();
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("YellowCard")) {
                if (!isCheckedOutToday) {
                    if (!todayPlanList.isEmpty()) {
                        Intent intent = new Intent(MenuActivity.this, YellowCardLandingActivity.class);
                        startActivity(intent);
                    } else {
                        Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("giftDelivery")) {
                if (!isCheckedOutToday) {
                    Intent intent = new Intent(MenuActivity.this, ActivityGiftDelivery.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("jointWOrkObservation")) {
                if (!isCheckedOutToday) {
                    if (!todayPlanList.isEmpty()) {
                        if (!mAceDnsDatabase.geJointWorkObservationRouteList().isEmpty()) {
                            Intent intent = new Intent(MenuActivity.this, JointWorkObservationActivity.class);
                            startActivity(intent);
                        } else {
                            Utils.showToast(MenuActivity.this, "There is no employee to work with today...");
                        }
                    } else {
                        Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("hierarchicalReport")) {
                if (!isCheckedOutToday) {
                    Intent intent = new Intent(MenuActivity.this, HierarchicalReportActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("appOrderApproval")) {
                if (!isCheckedOutToday) {
                    if (!HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showToast(mContext, "You must have an active internet connection to use this feature.");
                    } else {
                        Intent intent = new Intent(MenuActivity.this, OrderApprovalActivity.class);
                        startActivity(intent);
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("TM_approval")) {
                if (!isCheckedOutToday) {
                    if (!HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showToast(mContext, "You must have an active internet connection to use this feature.");
                    } else {
                        Intent intent = new Intent(MenuActivity.this, TechnicalMeetApprovalActivity.class);
                        startActivity(intent);
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("TM_approved_meeting")) {
                if (!isCheckedOutToday) {
                    if (!HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showToast(mContext, "You must have an active internet connection to use this feature.");
                    } else {
                        Intent intent = new Intent(MenuActivity.this, TechnicalMeetApprovalStatusActivity.class);
                        startActivity(intent);
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("quotation")) {
                if (!isCheckedOutToday) {
                    Intent intent = new Intent(MenuActivity.this, QuotationAddActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("stockIn")) {
                if (!isCheckedOutToday) {
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showProgressDialog(mContext, "Updating Stock Data..");
                        new Thread() {
                            public void run() {
                                masterApiCallingFlag = false;
                                new commonAsyncTaskMaster(mContext, "stock_allocation");
                            }
                        }.start();
                    } else {
                        productMasterList = mAceDnsDatabase.getProductMasterListRetailserStockin("", Integer.parseInt(Constants.productDetailsObj.getNoFilter()));
                        if (!productMasterList.isEmpty()) {
                            Intent intent = new Intent(MenuActivity.this, RetailerStockInActivity.class);
                            startActivity(intent);
                        } else {
                            Utils.showToast(MenuActivity.this, "No stock left.");
                        }
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("Stock Reallocation")) {
                if (!isCheckedOutToday) {
                    Intent intent = new Intent(MenuActivity.this, RetailerStockReallocationActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("IMEI_STATUS_REPORT")) {
                Intent intent = new Intent(mContext, ActivityIMEIStatusReport.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("bragain")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Intent intent = new Intent(mContext, ActivityBargainFilter.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else if (featureName.equalsIgnoreCase("grn")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Intent intent = new Intent(mContext, GrnActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else if (featureName.equalsIgnoreCase("order_edit")) {
                Intent intent = new Intent(mContext, OrderEditActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("stock_audit_edit")) {
                Intent intent = new Intent(mContext, StockAuditEditActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Tracking")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Intent intent = new Intent(mContext, TrackActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else if (featureName.equalsIgnoreCase("do")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Intent intent = new Intent(mContext, ActivityDOFilter.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            } else if (featureName.equalsIgnoreCase("stockOut")) {
                if (!isCheckedOutToday) {
                    if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) == 1) {
                        stockOutTypeRetailerApp = "special";
                        retailerStockOutProcess();
                    } else {
                        ArrayList<String> stockOutType = mAceDnsDatabase.getDistinctProdGrpNameFromProdGrpMaster();
                        if (!stockOutType.isEmpty()) {
                            showStockOutTypeDialog(stockOutType);
                        } else {
                            Utils.showToast(MenuActivity.this, "Proper data not found. Please Synchronize Data.");
                        }
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("crm")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Utils.showProgressDialog(mContext, "Updating allocation data..");
                    new Thread() {
                        public void run() {
                            new commonAsyncTaskMaster(mContext, "emp_date_wise_route_allocation");
                        }
                    }.start();
                } else {
                    Utils.showToast(mContext, "You need to have an active internet connection to use this feature.");
                }
            } else if (featureName.equalsIgnoreCase("MonthlyReport")) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Utils.showToast(mContext, "Monthly report email request sent to server.");
                    new Thread() {
                        public void run() {
                            new TRANS_SubmitMonthlyreportMailRequest(mContext).execute();
                        }
                    }.start();
                } else {
                    Utils.showToast(mContext, "You need to have an active internet connection to use this feature.");
                }
                if (isCheckedOutToday) {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("Collection")) {
                if (!isCheckedOutToday) {
                    Constants.CurrentOrderCollectionTransactionType = "PC";
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                        if (!todayPlanList.isEmpty()) {
                            gotoCollectionPage();
                        } else {
                            Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                        }
                    } else {
                        gotoCollectionPage();
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("Stock\nAudit")) {
                if (!isCheckedOutToday) {
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                        if (!todayPlanList.isEmpty()) {
                            if (Constants.userDetailsObj.getStkAuditScan().equalsIgnoreCase("yes")) {
                                startActivity(new Intent(MenuActivity.this, StockAuditFormScanActivity.class));
                            } else {
                                gotoStockAuditProcess();
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                        }
                    } else {
                        if (Constants.userDetailsObj.getStkAuditScan().equalsIgnoreCase("yes")) {
                            startActivity(new Intent(MenuActivity.this, StockAuditFormScanActivity.class));
                        } else {
                            gotoStockAuditProcess();
                        }
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("isp")) {
                if (!isCheckedOutToday) {
                    startActivity(new Intent(MenuActivity.this, InSHopPromoterFormActivity.class));
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("DO_status")) {
                Intent intent = new Intent(mContext, TrackOrderActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("raSauda")) {
                if (!isCheckedOutToday) {
                    if (cd.isConnectingToInternet()) {
                        if (Utils.isTimeAutomatic(mContext)) {
                            Utils.showProgressDialog(mContext, "Updating auction related data..");
                            new Thread() {
                                public void run() {
                                    Constants.saudaFormDetailsObj = new SaudaFormDetails();
                                    Constants.saudaFormDetailsObj = mAceDnsDatabase.GETSaudaFormDetails();
                                    new commonAsyncTaskMaster(mContext, "ra_window_timing");
                                }
                            }.start();
                        } else {
                            Utils.showSettingsAlertToChangeTimeZone(mContext);
                        }
                    } else {
                        Utils.showToast(MenuActivity.this, "You need to have an active internet connection to use this feature.");
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("FeedBack")) {
                if (!isCheckedOutToday) {
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                        if (!todayPlanList.isEmpty()) {
                            mAceDnsDatabase.GetMarketFeedbackDetails();
                            if (!Constants.marketFeedbackDetailsObj.getMfSubMenuDetails().trim().isEmpty()) {
                                Intent intent = new Intent(MenuActivity.this, ActivityMarketFeedbackLanding.class);
                                startActivity(intent);
                            } else {
                                Log.d("TAG", "doOnItemClickJob 1 : ActivityMarketFeedbackStock");
//                                Intent intent = new Intent(MenuActivity.this, MarketFeedbackSBGStockConfirmationActivity.class);
                                Intent intent = new Intent(MenuActivity.this, ActivityMarketFeedbackStock.class);
                                startActivity(intent);
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                        }
                    } else {
                        mAceDnsDatabase.GetMarketFeedbackDetails();
                        if (!Constants.marketFeedbackDetailsObj.getMfSubMenuDetails().trim().isEmpty()) {
                            Intent intent = new Intent(MenuActivity.this, ActivityMarketFeedbackLanding.class);
                            startActivity(intent);
                        } else {
                            Log.d("TAG", "doOnItemClickJob 2 : ActivityMarketFeedbackStock");
                            Intent intent = new Intent(MenuActivity.this, ActivityMarketFeedbackStock.class);
                            startActivity(intent);
                        }
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("sbg_menu")) {
                if (!isCheckedOutToday) {
                    if (!todayPlanList.isEmpty()) {
                        Intent intent = new Intent(MenuActivity.this, MarketFeedbackSBGStockConfirmationActivity.class);
                        startActivity(intent);
                    }
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("site_visit_approval")) {
                if (!isCheckedOutToday) {
//                    Intent intent = new Intent(MenuActivity.this, SiteVisitApprovalActivity.class);NewSiteLeadListActivity
                    Intent intent = new Intent(MenuActivity.this, NewSiteLeadListActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("lead_generation_approval")) {
                if (!isCheckedOutToday) {
                    Intent intent = new Intent(MenuActivity.this, LeadGenerationApprovalActivity.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                }
            } else if (featureName.equalsIgnoreCase("Leader_Board")) {
                Intent intent = new Intent(MenuActivity.this, WebViewActivity.class);
                intent.putExtra("val", "Leader_Board");
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("bd_leader_board")) {
                Intent intent = new Intent(MenuActivity.this, WebViewActivity.class);
                intent.putExtra("val", "bd_leader_board");
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("manchtech")) {
                Intent intent = new Intent(MenuActivity.this, WebViewActivity.class);
                intent.putExtra("val", "manchtech");
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("CustomerDuplicacyCheck")) {
                Intent intent = new Intent(Intent.ACTION_VIEW);
                intent.setData(Uri.parse("https://mdm.starcement.co.in/dashboard/"));
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("funnel")) {
                Intent intent = new Intent(MenuActivity.this, LeadGenerationGraphActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("quotation_po")) {
                Intent intent = new Intent(MenuActivity.this, LeadQuotationListActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("outstanding_report")) {
                Intent intent = new Intent(MenuActivity.this, CustomerWiseOutstandingActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("credit_limit")) {
                Intent intent = new Intent(MenuActivity.this, CustomerWiseCreditLimitActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("dashboard")) {
                Intent intent = new Intent(MenuActivity.this, DashboardActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("CheckInOut")) {
                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")
                        && !Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                    if (!todayPlanList.isEmpty()) {
                        ChooseTransactionTypeForCheckIn();
                    } else {
                        Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                    }
                } else {
                    ChooseTransactionTypeForCheckIn();
                }
            } else if (featureName.equalsIgnoreCase("CheckInOutOut")) {
                try {
                    boolean Con_star = false;
                    String s_cust_name = PreferenceData.getCheckInOutEmpName(mContext);
                    if (s_cust_name.contains("(STAR)")) {
                        Con_star = true;
                    }
                    if (!Constants.menuDetailsObj.getindependent_check_in_out().equalsIgnoreCase("yes")) {
                        String endDate = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
                        PreferenceData pd = new PreferenceData();
                        String checkin_time_str = pd.getCheckInTime(mContext);
                        String startDate = Utils.changeDateFormat("yyyy-MM-dd HH:mm:ss", "yyyyMMddHHmmss", checkin_time_str);
                        int getNoOfTransactionInCurrentCheckIn = mAceDnsDatabase.getNoOfTransactionInCurrentCheckIn(startDate, endDate);

                        empLevel = mAceDnsDatabase.getEmpLevel(Constants.employeeDetailObject.getEmpCode());
                        Log.d("TAG", "_DOWNLOAD_ prepareFeatureList: " + empLevel);
                        if (mAceDnsDatabase.isUserCheckedin() && !empLevel.equalsIgnoreCase("NT_TO") && !empLevel.equalsIgnoreCase("NT")) {
                            if (PreferenceData.getAddSBG(mContext).equalsIgnoreCase("0")) {
                                Utils.showToast(mContext, "Before checking out, you need to do update SBG data.");
                                return;
                            }
                        }

                        if (getNoOfTransactionInCurrentCheckIn > 0) {
                            String checkInOutMenuAccessList = Constants.menuDetailsObj.getcheck_in_out_menu_access().toLowerCase();
                            if (checkInOutMenuAccessList.contains("y")) {
                                boolean mandatoryTransactionNotDone = false;
                                ArrayList<String> currentCHeckInTransList = mAceDnsDatabase2.getTransactionIdPrefixListOnCurrentCheckIn(startDate, endDate);
                                if (checkInOutMenuAccessList.contains("order;y")) {
                                    if (!currentCHeckInTransList.contains("o")) {
                                        mandatoryTransactionNotDone = true;
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one order.");
                                    }
                                }
                                if (checkInOutMenuAccessList.contains("collection;y")) {
                                    if (!currentCHeckInTransList.contains("p")) {
                                        mandatoryTransactionNotDone = true;
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one collection.");
                                    }
                                }
                                if (checkInOutMenuAccessList.contains("stk_audit;y")) {
                                    if (!currentCHeckInTransList.contains("s")) {
                                        mandatoryTransactionNotDone = true;
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one audit.");
                                    }
                                }
                                if (checkInOutMenuAccessList.contains("market_feedback;y")) {
                                    if (!Con_star && !currentCHeckInTransList.contains("mf")) {
                                        mandatoryTransactionNotDone = true;
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one market feedback.");
                                    }
                                }
                                if (Constants.menuDetailsObj.getMf_mandatory_details().toLowerCase().contains("yes")) {
                                    if (!Con_star && !currentCHeckInTransList.contains("mf")) {
                                        mandatoryTransactionNotDone = true;
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one market feedback.");
                                    }
                                }
                                if (!mandatoryTransactionNotDone) {
                                    showNotesInfoDialogOnCheckOut();
                                }
                            } else {
                                if (Constants.menuDetailsObj.getMf_mandatory_details().toLowerCase().contains("yes")) {
                                    ArrayList<String> currentCHeckInTransList = mAceDnsDatabase2.getTransactionIdPrefixListOnCurrentCheckIn(startDate, endDate);//example-> o,mf,p,s rtc
                                    if (!Con_star && !currentCHeckInTransList.contains("mf")) {
                                        Utils.showToast(mContext, "Before checking out, you need to do at least one market feedback.");
                                    } else {
                                        showNotesInfoDialogOnCheckOut();
                                    }
                                } else {
                                    showNotesInfoDialogOnCheckOut();
                                }
                            }
                        } else {
                            Utils.showToast(mContext, "Before checking out, you need to do at least one transaction.");
                        }
                    } else {
                        showNotesInfoDialogOnCheckOut();
                    }
                } catch (Exception e) {
                    showNotesInfoDialogOnCheckOut();
                }
            } else if (featureName.equalsIgnoreCase("DoctorVisit")) {
                if (orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("yes") || orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("no_order")) {
                    hintRemarksValList = new ArrayList<>();
                    String hintRemarksValString = orderFormDetailsObj.getHintsRemarksVal();
                    if (hintRemarksValString.contains("#")) {
                        String[] arrayOfData = hintRemarksValString.split("#");
                        Collections.addAll(hintRemarksValList, arrayOfData);
                    } else {
                        hintRemarksValList.add(hintRemarksValString);
                    }
                    showInstructionWithHintDialogDoctorVisit();
                } else {
                    Utils.showToast(MenuActivity.this, "Remarks not found");
                }
            } else {
                if (!Constants.menuDetailsObj.getindependent_check_in_out().equalsIgnoreCase("yes") && Utils.NotCheckedOut(mContext) && !isSurveyOn()) {
                    Utils.showCommonAlertDialog(mContext, "Please Note", "You have already Checked In at " + PreferenceData.getCheckInOutEmpName(mContext) + ". Please Check Out before go to next task.");
                } else {
                    if (featureName.equalsIgnoreCase("Activity\nReport")) {
                        startActivity(new Intent(MenuActivity.this, ActivityReportLanding.class));
                    }
                    if (featureName.equalsIgnoreCase("sis_emp_data")) {
                        startActivity(new Intent(MenuActivity.this, NewSisSummeryActivity.class));
                    }
                    if (featureName.equalsIgnoreCase("bd_sis_emp_data")) {
                        startActivity(new Intent(MenuActivity.this, BdSisActivity.class));
                    }
                    if (featureName.equalsIgnoreCase("remainder")) {
                        Intent intent = new Intent(MenuActivity.this, RemainderActivityLanding.class);
                        intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                        startActivity(intent);
                    }
                    GotoCreateRoutePlan(featureName);
                    if (featureName.equalsIgnoreCase("Sales")) {
                        if (!isCheckedOutToday) {
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (!todayPlanList.isEmpty()) {
                                    Intent intent = new Intent(MenuActivity.this, SalesOptionActivity.class);
                                    startActivity(intent);
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                                }
                            } else {
                                Intent intent = new Intent(MenuActivity.this, SalesOptionActivity.class);
                                startActivity(intent);
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
                    if (featureName.equalsIgnoreCase("Sampling")) {
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            if (!todayPlanList.isEmpty()) {
                                Intent intent = new Intent(MenuActivity.this, SamplingActivity.class);
                                startActivity(intent);
                            } else {
                                Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                            }
                        } else {
                            Intent intent = new Intent(MenuActivity.this, SamplingActivity.class);
                            startActivity(intent);
                        }
                    }
                    if (featureName.equalsIgnoreCase("Replacement")) {
                        if (!isCheckedOutToday) {
                            Intent intent = new Intent(MenuActivity.this, ActivityProductReplacement.class);
                            startActivity(intent);
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
                    if (featureName.equalsIgnoreCase("SaudaAllocationApp")) {
                        if (!isCheckedOutToday) {
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (!todayPlanList.isEmpty()) {
                                    Intent intent = new Intent(MenuActivity.this, SaudaAllocationActivity.class);
                                    startActivity(intent);
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                                }
                            } else {
                                Intent intent = new Intent(MenuActivity.this, SaudaAllocationActivity.class);
                                startActivity(intent);
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "Daily Report already submitted. Transactions are not allowed for the day! Activity & Tar -vs- Actual can be accessed");
                        }
                    }
                    if (featureName.equalsIgnoreCase("TDAllocationApp")) {
                        if (!isCheckedOutToday) {
                            isInternetPresent = cd.isConnectingToInternet();
                            if (isInternetPresent) {
                                Intent intent = new Intent(MenuActivity.this, TDAllocationActivity.class);
                                startActivity(intent);
                            } else {
                                Utils.showToast(MenuActivity.this, "You need to have an active internet connection to use this feature.");
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "Daily Report already submitted. Transactions are not allowed for the day! Activity & Tar -vs- Actual can be accessed");
                        }
                    }
                    if (featureName.equalsIgnoreCase("WholeSaleInfo")) {
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            if (!todayPlanList.isEmpty()) {
                                Intent intent = new Intent(MenuActivity.this, ActivityWholeSaleInfo.class);
                                startActivity(intent);
                            } else {
                                Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                            }
                        } else {
                            Intent intent = new Intent(MenuActivity.this, ActivityWholeSaleInfo.class);
                            startActivity(intent);
                        }
                    }
                    if (featureName.equalsIgnoreCase("SelfAppraisal")) {
                        SelfAppraisalDetails selfAppraisalSetup = mAceDnsDatabase.getTargetAchievementSetupDetails();
                        if (selfAppraisalSetup.getMultipleTargetAchievementVal().equalsIgnoreCase("route_wise@product_group_wise")) {
                            Intent intent = new Intent(MenuActivity.this, TargetAchieveLandingActivity.class);
                            startActivity(intent);
                        } else {
//                            Intent intent = new Intent(MenuActivity.this, SelfAppraisalLandingActivity.class);
                            Intent intent = new Intent(MenuActivity.this, EmployeeTargetAchievementActivity.class);
//                            Intent intent = new Intent(MenuActivity.this, CustomerWiseOutstandingActivity.class);
                            startActivity(intent);
                        }
                    }
                    if (featureName.equalsIgnoreCase("SelfAppraisalWeekWise")) {
                        Intent intent = new Intent(MenuActivity.this, SelfAppraisalLandingActivityWeekWise.class);
                        startActivity(intent);
                    }
                    if (featureName.equalsIgnoreCase("OrderStatus")) {
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            if (!todayPlanList.isEmpty()) {
                                Intent intent = new Intent(MenuActivity.this, ActivittyOrderStatus.class);
                                startActivity(intent);
                            } else {
                                Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                            }
                        } else {
                            Intent intent = new Intent(MenuActivity.this, ActivittyOrderStatus.class);
                            startActivity(intent);
                        }
                    }
                    if (featureName.equalsIgnoreCase("CollectionForecast")) {
                        Intent intent = new Intent(MenuActivity.this, CollectionForecastActivity.class);
                        startActivity(intent);
                    }
                    if (featureName.equalsIgnoreCase("SaudaAllocation")) {
                        if (!isCheckedOutToday) {
                            if (HTTPUtils.isConnectionPossible(mContext)) {
                                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                    if (!todayPlanList.isEmpty()) {
                                        Intent intent = new Intent(MenuActivity.this, SaudaFilterActivity.class);
                                        startActivity(intent);
                                    } else {
                                        Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                                    }
                                } else {
                                    Intent intent = new Intent(MenuActivity.this, SaudaFilterActivity.class);
                                    startActivity(intent);
                                }
                            } else {
                                Utils.showToast(mContext, "You need to have an active internet connection to use this feature.");
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
                    if (featureName.equalsIgnoreCase("Business\nProspect")) {
                        if (!isCheckedOutToday) {
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (!todayPlanList.isEmpty()) {
                                    startActivity(new Intent(MenuActivity.this, BusinessProspectActivity.class));
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                                }
                            } else {
                                startActivity(new Intent(MenuActivity.this, BusinessProspectActivity.class));
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
                    if (featureName.equalsIgnoreCase("Tour\nExpenses")) {
                        if (Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC") || Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC\n")) {
                            if (localStorage.getTADAPubPri().matches("public")) {
                                if ((Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("consolidated"))) {
                                    if (Constants.menuDetailsObj.getMulti_travel_mode().equalsIgnoreCase("yes")) {
                                        startActivity(new Intent(MenuActivity.this, TourMultiTravelExpenseLandingActivity.class));
                                    } else {
                                        startActivity(new Intent(MenuActivity.this, TourTravelExpenseLandingActivity.class));
                                    }
                                } else if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) {
                                    startActivity(new Intent(MenuActivity.this, TourExpenseLandingActivitySpecial.class));
                                }
                            } else {
                                Toast.makeText(mContext, "To Acess this menu change mode from private to public .", Toast.LENGTH_LONG).show();
                            }
                        } else {
                            if ((Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("consolidated"))) {
                                if (Constants.menuDetailsObj.getMulti_travel_mode().equalsIgnoreCase("yes")) {
                                    startActivity(new Intent(MenuActivity.this, TourMultiTravelExpenseLandingActivity.class));
                                } else {
                                    startActivity(new Intent(MenuActivity.this, TourTravelExpenseLandingActivity.class));
                                }
                            } else if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) {
                                startActivity(new Intent(MenuActivity.this, TourExpenseLandingActivitySpecial.class));
                            }
                        }
                    }
                    if (featureName.equalsIgnoreCase("Merchandising")) {
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            if (!todayPlanList.isEmpty()) {
                                startActivity(new Intent(MenuActivity.this, MerchandisingActivity.class));
                            } else {
                                Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                            }
                        } else {
                            startActivity(new Intent(MenuActivity.this, MerchandisingActivity.class));
                        }
                    }

                    if (featureName.equalsIgnoreCase("new_site_lead_and_conversion_tracking")) {
                        startActivity(new Intent(MenuActivity.this, NewSiteLeadListActivity.class));
                    }
                    if (featureName.equalsIgnoreCase("Customer\nFeedback")) {
                        if (!isCheckedOutToday) {
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (!todayPlanList.isEmpty()) {
                                    final Dialog checkoutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
                                    checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                                    checkoutDialog.setContentView(R.layout.dialog_checked_out);
                                    checkoutDialog.setCancelable(false);
                                    TextView title = checkoutDialog.findViewById(R.id.title);
                                    Button submit = checkoutDialog.findViewById(R.id.btn_submit);
                                    title.setText("Notes");
                                    if (Constants.userDetailsObj.getnotes_info_hint_remarks().equalsIgnoreCase("yes")) {
                                        LinearLayout RemarksLayOut = checkoutDialog.findViewById(R.id.RemarksLayOut);
                                        RemarksLayOut.setVisibility(VISIBLE);
                                    }
                                    if (Constants.userDetailsObj.getnotes_info_upload_photo().equalsIgnoreCase("yes")) {
                                        LinearLayout pictureLayOut = EnablePicLayOutAndInitializeVariables(checkoutDialog, submit);
                                        Button btn_add_attachment = checkoutDialog.findViewById(R.id.btn_add_attachment);
                                        btn_add_attachment.setOnClickListener(view -> {
                                            if (numberOfImageAdded < 2) {
                                                launchCameraToTakeImage();
                                            } else {
                                                Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                                            }
                                        });
                                        pictureLayOut.setVisibility(VISIBLE);
                                    }
                                    TextView customer_name = checkoutDialog.findViewById(R.id.customer_name);
                                    customer_name.setVisibility(GONE);
                                    final EditText remark_box = checkoutDialog.findViewById(R.id.remark_box);
                                    submit.setOnClickListener(v -> {
                                        String temark_box_str = remark_box.getText().toString().trim();
                                        String remarks = "";
                                        if (Constants.userDetailsObj.getnotes_info_hint_remarks().equalsIgnoreCase("yes")) {
                                            final EditText remarks_box = checkoutDialog.findViewById(R.id.remarks_box);
                                            remarks = remarks_box.getText().toString().trim();
                                        }
                                        if (!temark_box_str.isEmpty()) {
                                            new GPSTracker(mContext);
                                            checkoutDialog.cancel();
                                            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                            String emp_code = Constants.employeeDetailObject.getEmpCode();
                                            String timeStamps = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                            String trans_id = "NI" + emp_code + timeStamps;
                                            String attachmentIdSemecolonSeparated = "";
                                            if (Constants.userDetailsObj.getnotes_info_upload_photo().equalsIgnoreCase("yes")) {
                                                for (Map.Entry<String, Bitmap> entry : supportingAttachmentMap.entrySet()) {
                                                    String currentAttachmentName = entry.getKey();
                                                    mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentAttachmentName, "NOTES_INFO_ATTACHMENT");
                                                    if (attachmentIdSemecolonSeparated.matches("")) {
                                                        attachmentIdSemecolonSeparated = currentAttachmentName;
                                                    } else {
                                                        attachmentIdSemecolonSeparated = MessageFormat.format("{0};{1}", attachmentIdSemecolonSeparated, currentAttachmentName);
                                                    }
                                                }
                                            }
                                            mAceDnsTransactionDatabase.InsertNotesInfo(trans_id, temark_box_str, remarks, attachmentIdSemecolonSeparated);
                                            mAceDnsTransactionDatabase.insertToLocationTable("NI", timeStamps);
                                            numberOfImageAdded = 0;
                                            supportingAttachmentMap = new HashMap<>();
                                            new TRANS_SubmitNotesInfo(mContext, true).execute();
                                        } else {
                                            Utils.showToast(mContext, "Please take the notes");
                                        }
                                    });
                                    checkoutDialog.show();
                                } else {
                                    Utils.showToast(MenuActivity.this, "Please create Route Plan first");
                                }
                            } else {
                                startActivity(new Intent(MenuActivity.this, CustomerFeedbackActivity.class));
                            }
                        } else {
                            Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
                        }
                    }
                    if (featureName.equalsIgnoreCase("Loyalty")) {
                        startActivity(new Intent(MenuActivity.this, LoyaltyProgrammeActivity.class));
                    }
                    if (featureName.equalsIgnoreCase("SaudaMisReport")) {
                        Intent intent = new Intent(MenuActivity.this, MISActivity.class);
                        startActivity(intent);
                    }
                    if (featureName.equalsIgnoreCase("MIS Report")) {
                        startActivity(new Intent(MenuActivity.this, MISReportSummaryActivity.class));
                    }
                    if (featureName.equalsIgnoreCase("Delete\nTransaction")) {
                        Intent intent = new Intent(MenuActivity.this, SalesOptionActivity.class);
                        intent.putExtra("DELETE TRANSACTION", true);
                        startActivity(intent);
                    }
                }
            }
        } else {
            if (featureName.equalsIgnoreCase("Route\nPlan")) {
                GotoCreateRoutePlan(featureName);
            } else if (featureName.equalsIgnoreCase("display_site_visit")) {
                GotoLuxSiteVisit(featureName);
            } else if (featureName.equalsIgnoreCase("event")) {
                GotoLuxSiteVisit(featureName);
            } else if (featureName.equalsIgnoreCase("hoarding")) {
                GotoLuxSiteVisit(featureName);
            } else if (featureName.equalsIgnoreCase("in_shop")) {
                GotoLuxSiteVisit(featureName);
            } else if (featureName.equalsIgnoreCase("local_event")) {
                GotoLuxSiteVisit(featureName);
            } else if (featureName.equalsIgnoreCase("DO_status")) {
                Intent intent = new Intent(mContext, TrackOrderActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Activity\nReport")) {
                startActivity(new Intent(MenuActivity.this, ActivityReportLanding.class));
            } else if (featureName.equalsIgnoreCase("sis_emp_data")) {
                startActivity(new Intent(MenuActivity.this, NewSisSummeryActivity.class));
            } else if (featureName.equalsIgnoreCase("remainder")) {
                Intent intent = new Intent(MenuActivity.this, RemainderActivityLanding.class);
                intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                startActivity(intent);
            } else {
                Utils.showToast(MenuActivity.this, attendanceFilterString);
            }
        }
        if (featureName.equalsIgnoreCase("stockist_visit")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, StockistVisitActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("tentform")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, TentFormActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("demoform")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, DemoFormActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("knockingform")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, KnockingFormActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("groupleader")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, GroupLeaderFormActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("telecaller")) {
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else {
                startActivity(new Intent(MenuActivity.this, TelecallerActivity.class));
            }
        }
        if (featureName.equalsIgnoreCase("odometer")) {
            isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
            isCheckedOutToday = mAceDnsTransactionDatabase.isCheckedOutToday();
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else if (!HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showToast(MenuActivity.this, "Please Connect with internet ");
            } else if (isCheckedOutToday) {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            } else {
                odometerProcess();
            }
        }
        if (featureName.equalsIgnoreCase("counter")) {
            isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
            isCheckedOutToday = mAceDnsTransactionDatabase.isCheckedOutToday();
            if (!isAttendanceGiven) {
                Utils.showToast(MenuActivity.this, "Attendance First. Please try again ");
            } else if (isCheckedOutToday) {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            } else {
                Intent intent = new Intent(MenuActivity.this, AddNewCustActivity.class);
                Constants.isOrederToNewCustomer = false;
                startActivity(intent);
            }
        }
    }

    public boolean isCILogicOn() {
        return Constants.menuDetailsObj.getCI_logic().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("ci_logic");
    }

    public void gotoCollectionPage() {
        startActivity(new Intent(mContext, CollectionActivity.class));
    }

    @SuppressLint("SimpleDateFormat")
    private void gotoSurveyPage() {
        mAceDnsDatabase.GETSurveyFormDetails();
        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
            if (Constants.nickName.equalsIgnoreCase("nimbus")) {
                Log.d("TAG", "gotoSurveyPage: 1");
                Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
                Intent intent;
                intent = new Intent(mContext, SurveyActivity.class);
                intent.putExtra("SUBMENU", "Installation Expenses");
                startActivity(intent);
            } else if (Constants.nickName.equalsIgnoreCase("coral")) {
                Log.d("TAG", "gotoSurveyPage: 2");
                Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
                Intent intent;
                intent = new Intent(mContext, SurveyActivity.class);
                intent.putExtra("SUBMENU", "Plumber Meet");
                startActivity(intent);
            } else {
                Log.d("TAG", "gotoSurveyPage: 3");
                Intent intent = new Intent(MenuActivity.this, ActivitySurveyLanding.class);
                intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                startActivity(intent);
            }
        } else {
            if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                Log.d("TAG", "gotoSurveyPage: 4");
                Intent intent = new Intent(MenuActivity.this, SurveyMenuActivity.class);
                startActivity(intent);
            } else {
                if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "gotoSurveyPage: 5");
                    Intent intent = new Intent(MenuActivity.this, SurveyActivityList.class);
                    startActivity(intent);
                } else {
                    if (Constants.surveyFormDetailsObj.getspecial_input_screen().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "gotoSurveyPage: 6");
                        Intent intent = new Intent(MenuActivity.this, SurveyActivitySpecial.class);
                        startActivity(intent);
                    } else {
                        Log.d("TAG", "gotoSurveyPage: 7");
                        Intent intent = new Intent(MenuActivity.this, SurveyActivity.class);
                        startActivity(intent);
                    }
                }
            }
        }
    }

    public void retailerStockOutProcess() {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            retailerappTransactionReason = "sendDataBeforeStockOut";
            new TRANS_SubmitRetailerStockOutTask(mContext, true).execute();
        } else {
            if (stockOutTypeRetailerApp.equalsIgnoreCase("special")) {
                mAceDnsDatabase.getRetailerStockOutProductList();
                if (!Constants.retailerStockOutProductList.isEmpty()) {
                    Intent intent = new Intent(mContext, RetailerStockOutActivitySpecial.class);
                    startActivity(intent);
                } else {
                    Utils.showToast(mContext, "No product in stock.");
                }
            } else {
                Intent intent = new Intent(mContext, RetailerStockOutActivity.class);
                startActivity(intent);
            }
        }
    }

    public void showStockOutTypeDialog(final ArrayList<String> stockOutType) {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.dialog_dynamic_radio);
        instructionDialog.setCancelable(false);
        Button submit = instructionDialog.findViewById(R.id.btn_submit);
        submit.setVisibility(GONE);
        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            instructionDialog.cancel();

        });
        RadioGroup rgp = instructionDialog.findViewById(R.id.radiogroup);
        rgp.setOnCheckedChangeListener((radioGroup, id) -> {
            stockOutProdGroupRetailerApp = stockOutType.get(id - 1);
            stockOutTypeRetailerApp = mAceDnsDatabase.getDistinctStockOutTypeFromProdGrpName(stockOutProdGroupRetailerApp);
            instructionDialog.cancel();
            retailerStockOutProcess();
        });
        RadioGroup.LayoutParams rprms;
        for (int i = 0; i < stockOutType.size(); i++) {
            RadioButton radioButton = new RadioButton(this);
            radioButton.setText(stockOutType.get(i));
            radioButton.setId(i + 1);
            radioButton.setTextColor(getResources().getColor(R.color.text_color));
            rprms = new RadioGroup.LayoutParams(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
            rgp.addView(radioButton, rprms);
        }
        instructionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showNotesInfoDialogOnCheckOut() {
        if (Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance().trim().length() > 1
                && Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance().toLowerCase().contains(Constants.employeeDetailObject.getSaleAccess().toLowerCase())) {
            LocationTrackerObject = new LocationTracker(mContext, "check in");
            LocationTrackerObject.checkLocationUpdateSharing();
            final ProgressDialog progressD = new ProgressDialog(mContext);
            progressD.setMessage("Determining coverage area...");
            progressD.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
            progressD.setIndeterminate(false);
            progressD.setProgress(20);
            progressD.setCancelable(false);
            progressD.show();
            new CountDownTimer(5000, 1000) {
                public void onTick(long millisUntilFinished) {
                    progressD.setProgress(progress);
                    progress = progress + 20;
                }

                public void onFinish() {
                    progressD.dismiss();
                    progress = 20;
                    if (!isGettingCurrentLocation) {
                        Utils.showToast(mContext, "Could not determine your location. Please Check Location Settings");
                    } else {
                        String customer_code = PreferenceData.getCheckInOutEmpCode(mContext);
                        ArrayList<String> custLatLong = mAceDnsTransactionDatabase.getcurrentCustomerLatLong(customer_code);
                        String customerLat = custLatLong.get(0);
                        String customerLong = custLatLong.get(1);
                        Log.d("TAG", "_DOWNLOAD_ onFinish: " + custLatLong.get(2));
                        if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(currentLat) && Double.parseDouble(currentLong) > 0) {
                            float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                            String geoFencingVariance = Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance();
                            double geoFencingLimitInDouble = 0.00;
                            try {
                                if (geoFencingVariance.contains(",")) {
                                    String[] splittedgeofencingSaleAccessWise = geoFencingVariance.split(",");
                                    for (String s : splittedgeofencingSaleAccessWise) {
                                        String[] splittedgeofencing = s.split("#");
                                        if (splittedgeofencing[0].equalsIgnoreCase(Constants.employeeDetailObject.getSaleAccess())) {
                                            geoFencingLimitInDouble = Double.parseDouble(splittedgeofencing[1]);
                                            break;
                                        }
                                    }
                                } else {
                                    String[] splittedgeofencing = geoFencingVariance.split("#");
                                    geoFencingLimitInDouble = Double.parseDouble(splittedgeofencing[1]);
                                }
                            } catch (Exception ignored) {
                            }
                            if (distance > geoFencingLimitInDouble) {
                                Utils.showToast(mContext, "You are not under coverage area.");
                            } else {
                                String saleAccessOfCurrentEmployee = Constants.employeeDetailObject.getSaleAccess().trim();
                                if (saleAccessOfCurrentEmployee.equalsIgnoreCase("logistics") || saleAccessOfCurrentEmployee.equalsIgnoreCase("aac")) {
                                    checkinoutsavingprocess(null, null, PreferenceData.getAttachmentImageCheckInOut(mContext));
                                } else {
                                    checkinoutsavingprocess(null, null, "");
                                }
                            }
                        } else {
                            Utils.showToast(mContext, "Something went wrong while checking out. Make sure the checked in customer has proper location stored in db.");
                        }
                    }
                }
            }.start();
        } else {
            final Dialog checkoutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            checkoutDialog.setContentView(R.layout.dialog_checked_out);
            checkoutDialog.setCancelable(true);
            TextView title = checkoutDialog.findViewById(R.id.title);
            Button submit = checkoutDialog.findViewById(R.id.btn_submit);
            title.setText("Please add remarks");
            if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphoto") && !Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                LinearLayout pictureLayOut = EnablePicLayOutAndInitializeVariables(checkoutDialog, submit);
                Button btn_add_attachment = checkoutDialog.findViewById(R.id.btn_add_attachment);
                btn_add_attachment.setOnClickListener(view -> {
                    if (numberOfImageAdded < 2) {
                        launchCameraToTakeImage();
                    } else {
                        Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                    }
                });
                pictureLayOut.setVisibility(VISIBLE);
            }
            TextView customer_name = checkoutDialog.findViewById(R.id.customer_name);
            if (Constants.selectedCheckINCustomer != null) {
                customer_name.setText(Constants.selectedCheckINCustomer.getCustomerName());
            } else {
                customer_name.setVisibility(GONE);
                customer_name.setText("");
            }
            if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("hintremarks")) {
                drawHintLayout(checkoutDialog);
            }
            if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("producttagging")) {
                drawFilterLayout(checkoutDialog);
            }
            final EditText remark_box = checkoutDialog.findViewById(R.id.remark_box);
            submit.setOnClickListener(v -> {
                if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphoto") && !Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance()) && numberOfImageAdded < 1) {
                    Toast.makeText(mContext, "Please add at least one photo", Toast.LENGTH_SHORT).show();
                } else {
                    if (Constants.checkedHintItems != null && Constants.checkedHintItems.size() < 2) {
                        Utils.showCommonAlertDialog(mContext, "Alert!", "You must choose at least 2 hints");
                    } else if (Constants.taggedProducts != null && Constants.taggedProducts.isEmpty()) {
                        Utils.showCommonAlertDialog(mContext, "Alert!", "You must tag at least 1 product");
                    } else {
                        String attachmentIdSemecolonSeparated = "";
                        if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphoto") && !Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                            for (Map.Entry<String, Bitmap> entry : supportingAttachmentMap.entrySet()) {
                                String currentAttachmentName = entry.getKey();
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentAttachmentName, "CHECK_IN_OUT");
                                if (attachmentIdSemecolonSeparated.matches("")) {
                                    attachmentIdSemecolonSeparated = currentAttachmentName;
                                } else {
                                    attachmentIdSemecolonSeparated = MessageFormat.format("{0};{1}", attachmentIdSemecolonSeparated, currentAttachmentName);
                                }
                            }
                        }
                        if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphotocheckout") && !PreferenceData.getAttachmentIdSemecolonSeparatedCheckIn(mContext).isEmpty()) {
                            attachmentIdSemecolonSeparated = PreferenceData.getAttachmentIdSemecolonSeparatedCheckIn(mContext) + "#" + attachmentIdSemecolonSeparated;
                        }
                        checkinoutsavingprocess(remark_box, checkoutDialog, attachmentIdSemecolonSeparated);
                    }
                }
            });
            checkoutDialog.show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void showCaptureImageDialogOnCheckIn(final boolean visitSequenceCheckIn) {
        final Dialog checkoutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.dialog_checked_out);
        checkoutDialog.setCancelable(true);
        TextView title = checkoutDialog.findViewById(R.id.title);
        Button submit = checkoutDialog.findViewById(R.id.btn_submit);
        title.setText("Please add photos related to check in");
        LinearLayout pictureLayOut = EnablePicLayOutAndInitializeVariables(checkoutDialog, submit);
        Button btn_add_attachment = checkoutDialog.findViewById(R.id.btn_add_attachment);
        btn_add_attachment.setOnClickListener(view -> {
            if (numberOfImageAdded < 2) {
                launchCameraToTakeImage();
            } else {
                Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
            }
        });
        pictureLayOut.setVisibility(VISIBLE);
        TextView customer_name = checkoutDialog.findViewById(R.id.customer_name);
        if (Constants.selectedCheckINCustomer != null) {
            customer_name.setText(Constants.selectedCheckINCustomer.getCustomerName());
        } else {
            customer_name.setVisibility(GONE);
            customer_name.setText("");
        }
        final EditText remark_box = checkoutDialog.findViewById(R.id.remark_box);
        remark_box.setVisibility(GONE);
        submit.setOnClickListener(v -> {
            if (numberOfImageAdded > 0) {
                if (Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                    String customerLat = Constants.selectedCheckINCustomer.getbase_latt();
                    String customerLong = Constants.selectedCheckINCustomer.getbase_longi();
                    if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(customerLong) && Double.parseDouble(customerLong) > 0) {
                        float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                        if (distance > Double.parseDouble(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                            Utils.showToast(mContext, "You are not under coverage area.");
                        } else {
                            checkInWithPhoto(checkoutDialog, visitSequenceCheckIn);
                        }
                    } else {
                        mAceDnsDatabase.updateCustomerLatLongi(Constants.selectedCheckINCustomer.getCustomerCode());
                        checkInWithPhoto(checkoutDialog, visitSequenceCheckIn);
                    }
                } else {
                    checkInWithPhoto(checkoutDialog, visitSequenceCheckIn);
                }
            } else {
                Toast.makeText(mContext, "Please add at least one photo", Toast.LENGTH_SHORT).show();
            }
        });
        checkoutDialog.show();
        Toast.makeText(mContext, "Please add photos related to check in", Toast.LENGTH_SHORT).show();
        launchCameraToTakeImage();
    }

    public void checkInWithPhoto(Dialog checkoutDialog, boolean visitSequenceCheckIn) {
        String attachmentIdSemecolonSeparated = "";
        for (Map.Entry<String, Bitmap> entry : supportingAttachmentMap.entrySet()) {

            String currentAttachmentName = entry.getKey();
            mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentAttachmentName, "CHECK_IN_OUT");
            if (attachmentIdSemecolonSeparated.matches("")) {
                attachmentIdSemecolonSeparated = currentAttachmentName;
            } else {
                attachmentIdSemecolonSeparated = attachmentIdSemecolonSeparated + ";" + currentAttachmentName;
            }
            PreferenceData.setAttachmentIdSemecolonSeparatedCheckIn(mContext, attachmentIdSemecolonSeparated);
            PreferenceData.setAttachmentImageCheckInOut(mContext, attachmentIdSemecolonSeparated);
        }
        setCustomerCheckInDataInPreferences(mContext, mRouteCode);
        checkoutDialog.dismiss();
        if (!visitSequenceCheckIn) {
            loadMenuAgain();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ChooseTransactionTypeForCheckIn() {
        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
            orderAuditType = "Secondary";
            FlowofCheckINAndOut(2, "");
        } else {
            String saleAccessOfCurrentEmployee = Constants.employeeDetailObject.getSaleAccess().trim();
            if (!saleAccessOfCurrentEmployee.equalsIgnoreCase("primary")) {
                orderAuditType = "Secondary";
                FlowofCheckINAndOut(1, "");
            } else {
                final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
                dialgoCondition.setCancelable(false);
                dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
                dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
                Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
                TextView txtMsg = dialgoCondition.findViewById(R.id.title);
                ImageView imgBack = dialgoCondition.findViewById(R.id.imageViewback);
                txtMsg.setText("Select Transaction Type.");
                final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);
                final RadioButton radioEdit = dialgoCondition.findViewById(R.id.radioEdit);
                final RadioButton radioRedundant = dialgoCondition.findViewById(R.id.radioRedundant);
                radioEdit.setText("Primary");
                radioRedundant.setText("Secondary");
                imgBack.setVisibility(VISIBLE);
                imgBack.setOnClickListener(v -> dialgoCondition.cancel());
                radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
                    RadioButton radioSelection = dialgoCondition.findViewById(checkedId);
                    if (radioSelection.getText().toString().trim().equalsIgnoreCase("Primary")) {
                        orderAuditType = "Primary";
                        localStorage.setOrderAuditType("Primary");
                        if (!Constants.orderFormDetailsObj.getroute_wise_distributor().equalsIgnoreCase("yes")) {
                            FlowofCheckINAndOut(2, "");
                        } else {
                            FlowofCheckINAndOut(1, "");
                        }
                    } else {
                        orderAuditType = "Secondary";
                        localStorage.setOrderAuditType("Secondary");
                        FlowofCheckINAndOut(1, "");
                    }
                    dialgoCondition.cancel();
                });
                dialgoCondition.show();
            }
        }
    }

    private void gotoStockAuditProcess() {
        if (NotCheckedOut(mContext)) {
            if (Constants.userDetailsObj.getstk_audit_cust_type().contains(PreferenceData.getCheckInOutEmpType(mContext))) {
                startActivity(new Intent(MenuActivity.this, StockAuditFormActivity.class));
            } else {
                Utils.showToast(mContext, "Stock Audit disabled for checked in customer.");
            }
        } else {
            startActivity(new Intent(MenuActivity.this, StockAuditFormActivity.class));
        }
    }

    private LinearLayout EnablePicLayOutAndInitializeVariables(Dialog checkoutDialog, Button submit) {
        LinearLayout pictureLayOut = checkoutDialog.findViewById(R.id.pictureLayOut);
        attachmentImageView1 = checkoutDialog.findViewById(R.id.attachmentImageView1);
        attachmentImageView2 = checkoutDialog.findViewById(R.id.attachmentImageView2);
        Button addPictureButton = checkoutDialog.findViewById(R.id.btn_add_attachment);
        addPictureButton.setVisibility(VISIBLE);
        submit.setLayoutParams(new LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1));
        numberOfImageAdded = 0;
        supportingAttachmentMap = new HashMap<>();
        return pictureLayOut;
    }

    private void GotoCreateRoutePlan(String featureName) {
        if (featureName.equalsIgnoreCase("Route\nPlan")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, RoutePlanLandingActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
    }

    private void GotoLuxSiteVisit(String featureName) {
        if (featureName.equalsIgnoreCase("display_site_visit")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, LuxSiteDisplayVisitActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
        if (featureName.equalsIgnoreCase("event")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, LuxEventActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
        if (featureName.equalsIgnoreCase("hoarding")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, HoardingActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
        if (featureName.equalsIgnoreCase("in_shop")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, LuxInShopActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
        if (featureName.equalsIgnoreCase("local_event")) {
            if (!isCheckedOutToday) {
                startActivity(new Intent(MenuActivity.this, LocalEventActivity.class));
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        }
    }

    private void showAddAnotherImageDialog() {
        Constants.isSurveyImageTake = true;
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Please Note");
        AlertDG.setMessage("Do you want to take another picture?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> launchCameraToTakeImage());
        AlertDG.setNegativeButton("No", (dialog, which) -> {
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    @SuppressLint("SimpleDateFormat")
    private void checkinoutsavingprocess(EditText remark_box, Dialog checkoutDialog, String attachmentIdSemecolonSeparated) {
        String temark_box_str = "";
        if (remark_box != null) {
            temark_box_str = remark_box.getText().toString();
        }
        if (checkoutDialog != null) {
            checkoutDialog.cancel();
        }
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        String customer_code;
        if (Constants.selectedCheckINCustomer != null) {
            customer_code = Constants.selectedCheckINCustomer.getCustomerCode();
        } else {
            customer_code = PreferenceData.getCheckInOutEmpCode(mContext);
        }
        String emp_code = Constants.employeeDetailObject.getEmpCode();
        String out_time = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
        String timeStamps = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        String trans_id = "CI" + emp_code + timeStamps;
        String checkin_time_str = mAceDnsDatabase.getCheckInValueByColumnName("checkintime");
        String product_tagging = "";
        String hint = "";
        if (Constants.checkedHintItems != null) {
            hint = android.text.TextUtils.join("#", Constants.checkedHintItems);
            Constants.checkedHintItems.clear();
        }
        if (Constants.taggedProducts != null) {
            product_tagging = android.text.TextUtils.join(";", Constants.taggedProducts);
            Constants.taggedProducts.clear();
        }
        setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
        mAceDnsTransactionDatabase.insertCheckInOut(trans_id, checkin_time_str, customer_code, out_time, temark_box_str, hint, product_tagging, attachmentIdSemecolonSeparated);
        mAceDnsTransactionDatabase.insertToLocationTable("CI", timeStamps);
        clearAccuracyLatLong();
        mAceDnsDatabase.deleteCheckInValue();
        new TRANS_SubmitCheckInCheckOut(mContext, true, "SYNC").execute();
        loadMenuAgain();
    }

    private void drawHintLayout(Dialog checkoutDialog) {
        LinearLayout hintRemarksLayOut = checkoutDialog.findViewById(R.id.hintRemarksLayOut);
        hintRemarksLayOut.setVisibility(VISIBLE);
        if (Constants.checkedHintItems == null) {
            Constants.checkedHintItems = new ArrayList<>();
        }
        populateHintCheckBoxItems();
        HintRemarksCheckBoxAdapterObject = new HintRemarksCheckBoxAdapter(mContext, R.layout.list_item_textview_with_check_box, hintRemarksValList);
        ListView dialogList = checkoutDialog.findViewById(R.id.list);
        dialogList.setDivider(null);
        dialogList.setAdapter(HintRemarksCheckBoxAdapterObject);
    }

    private void populateHintCheckBoxItems() {
        try {
            String hintVal;
            if (Constants.userDetailsObj.getCheckInOutTypeVal().contains(";")) {
                hintVal = Constants.userDetailsObj.getCheckInOutTypeVal().split(";")[0];
            } else {
                hintVal = Constants.userDetailsObj.getCheckInOutTypeVal();
            }
            if (hintVal.contains("#")) {
                hintRemarksValList = new ArrayList<>(Arrays.asList(hintVal.split("#")));
                hintRemarksValList.remove(0);
            }
        } catch (Exception ignored) {
        }
    }

    @SuppressLint({"UseCompatLoadingForDrawables", "RtlHardcoded"})
    public void drawFilterLayout(Dialog checkoutDialog) {
        if (Constants.taggedProducts == null) {
            Constants.taggedProducts = new ArrayList<>();
        }
        LinearLayout taggedProductLayOut = checkoutDialog.findViewById(R.id.taggedProductLayOut);
        taggedProductLayOut.setVisibility(VISIBLE);
        taggedProductsET = checkoutDialog.findViewById(R.id.taggedProductsET);
        showTaggedProductsInCheckOutUi();
        ArrayList<String> filterList;
        LinearLayout filterLayout = checkoutDialog.findViewById(R.id.filter_layout);
        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
            filterList = new ArrayList<>();
            filterList.add("1");
            filterList.add("NA");
            filterList.add("NA");
            filterList.add("NA");
            filterList.add("BRAND FORM");
        } else {
            filterList = mAceDnsDatabase.getFilterList();
        }
        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
            filterNo = 1;
        } else {
            filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        }
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, 80, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayoutParams.setMargins(0, 0, 0, 5);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setHorizontallyScrolling(true);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                filterButton.setSingleLine(true);
                filterButton.setOnClickListener(this);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    private void showTaggedProductsInCheckOutUi() {
        if (taggedProductsET != null) {
            if (Constants.taggedProducts != null && !Constants.taggedProducts.isEmpty()) {
                String taggedProducts = android.text.TextUtils.join(", ", Constants.taggedProducts);
                taggedProductsET.setVisibility(VISIBLE);
                taggedProductsET.setText(Html.fromHtml("Tagged products: <font color='#F58322'>" + taggedProducts + "</font>"));
            } else {
                taggedProductsET.setVisibility(View.GONE);
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        final Dialog routePlanListDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routePlanListDialog.findViewById(R.id.list);
        RoutePlanTransAdapter adapter = new RoutePlanTransAdapter(MenuActivity.this, R.layout.route_list_child, routePlanListofToday);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routePlanListDialog.cancel();
            mRouteName = mRoutePlanListofToday.get(arg2).getRouteName();
            mRouteCode = mRoutePlanListofToday.get(arg2).getRoutecode();
            FlowofCheckINAndOut(2, mRouteCode);
        });
        ImageView image_cancel = routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(VISIBLE);
        image_cancel.setOnClickListener(view -> routePlanListDialog.cancel());
        Button cancel = routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(GONE);
        cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
        Button create_route = routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(GONE);
        routePlanListDialog.show();
    }

    public void FlowofCheckINAndOut(final int step, final String code) {
        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
        mStepProgressDialog.setCancelable(false);
        mStepProgressDialog.show();
        new Thread() {
            public void run() {
                switch (step) {
                    case 1:
                        if (orderAuditType.equalsIgnoreCase("primary") && !Constants.orderFormDetailsObj.getroute_wise_distributor().equalsIgnoreCase("yes")) {
                            FlowofCheckINAndOut(2, "");
                        } else if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                            String today = dateString.substring(6, 8) + "-" + dateString.substring(4, 6) + "-" + dateString.substring(0, 4);
                            mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForTodayCheckIn(today);
                        } else {
                            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                                getDayToShowRoutes();
                                if (Constants.orderFormDetailsObj.getvisit_sequence().equalsIgnoreCase("yes")) {
                                    mRouteDetailsListForVisitSequence = mAceDnsDatabase.getRouteListForVisitSequenceAlreadyVisitedRoutes();
                                    if (!mRouteDetailsListForVisitSequence.isEmpty()) {
                                        boolean isCustomerFound = false;
                                        for (int i = 0; i < mRouteDetailsListForVisitSequence.size(); i++) {
                                            mSelectedRouteDetailsVisitSequence = mRouteDetailsListForVisitSequence.get(i);
                                            mCustomerDetailsList = new ArrayList<>();
                                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWiseForRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
                                            if (!mCustomerDetailsList.isEmpty()) {
                                                isCustomerFound = true;
                                                MenuActivity.this.runOnUiThread(() -> {
                                                    mStepProgressDialog.dismiss();
                                                    int index = 0;
                                                    SelectCheckedInCustomerForVisitSequence(index);
                                                });
                                                break;
                                            }
                                        }
                                        if (!isCustomerFound) {
                                            showROutesForVisitSequence();
                                        }
                                    } else {
                                        showROutesForVisitSequence();
                                    }
                                } else {
                                    mCustomerDetailsList = new ArrayList<>();
                                    mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWise(Constants.dayOfWeekForCustomer);
                                    SelectCheckedInCustomerForVisitSequence(0);
                                }
                            } else {
                                mRouteDetailsList = mAceDnsDatabase.getRouteList();
                            }
                        }
                        break;
                    case 2:
                        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                            mCustomerDetailsList = mAceDnsDatabase.GetCustomerListAll();
                        } else if (orderAuditType.equalsIgnoreCase("primary") && !Constants.orderFormDetailsObj.getroute_wise_distributor().equalsIgnoreCase("yes")) {
                            mCustomerDetailsList = mAceDnsDatabase.getDistributorDetailsFromCustomerMaster();
                        } else if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                            RoutePlanDetails routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
                            if (routeplandetails.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                                String today = dateString.substring(6, 8) + "-" + dateString.substring(4, 6) + "-" + dateString.substring(0, 4);
                                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(today, code);
                            } else {
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(code);
                            }
                        } else {
                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(code);
                        }
                        break;
                    case 3:
                        new commonAsyncTaskMaster(mContext, "customer_master");
                        if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("secondary_freight")) {
                            new commonAsyncTaskMaster(mContext, "branch_route_freight");
                            new commonAsyncTaskMaster(mContext, "load_distribution");
                        }
                        if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("honeycomb_cost")) {
                            new commonAsyncTaskMaster(mContext, "honeycomb_cost");
                        }
                        if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("margin_cost")) {
                            new commonAsyncTaskMaster(mContext, "margin_cost");
                        }
                        if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("depot_cost")) {
                            new commonAsyncTaskMaster(mContext, "depot_cost");
                        }
                        if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("primary_freight")) {
                            new commonAsyncTaskMaster(mContext, "primary_freight");
                        }
                        new commonAsyncTaskMaster(mContext, "bargain_mrp");
                        new commonAsyncTaskMaster(mContext, "product_master");
                        break;
                    case 4:
                        if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                            mCustomerDetailsList = mAceDnsDatabase.GetCustomerListAll();
                        } else if (orderAuditType.equalsIgnoreCase("primary") && !Constants.orderFormDetailsObj.getroute_wise_distributor().equalsIgnoreCase("yes")) {
                            mCustomerDetailsList = mAceDnsDatabase.getDistributorDetailsFromCustomerMaster();
                        } else if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                            RoutePlanDetails routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
                            if (routeplandetails.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                                String today = dateString.substring(6, 8) + "-" + dateString.substring(4, 6) + "-" + dateString.substring(0, 4);
                                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(today, code);
                            } else {
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(code);
                            }
                        } else {
                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(code);
                        }
                        break;
                }
                Message msgObj = mStepHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("STEP", step);
                msgObj.setData(b);
                mStepHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void SelectCheckedInCustomerForVisitSequence(int index) {
        LocationTrackerObject = new LocationTracker(mContext, "check in");
        Constants.selectedCheckINCustomer = mCustomerDetailsList.get(index);
        Log.d("TAG", "_DOWNLOAD_ SelectCheckedInCustomerForVisitSequence: " + Constants.selectedCheckINCustomer.getCustomerType());
        visitSequenceCheckIn = true;
        checkInProcess(visitSequenceCheckIn);
    }

    public void checkInProcess(boolean visitSequenceCheckIn) {
        if (Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance().trim().length() > 1
                && Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance().toLowerCase().contains(Constants.employeeDetailObject.getSaleAccess().toLowerCase())) {
            String customerLat = Constants.selectedCheckINCustomer.getbase_latt();
            String customerLong = Constants.selectedCheckINCustomer.getbase_longi();
            if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(currentLat) && Double.parseDouble(currentLong) > 0) {
                float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                String geoFencingVariance = Constants.userDetailsObj.getdepartmentwise_geo_fencing_variance();
                double geoFencingLimitInDouble = 0.00;
                try {
                    if (geoFencingVariance.contains(",")) {
                        String[] splittedgeofencingSaleAccessWise = geoFencingVariance.split(",");
                        for (String s : splittedgeofencingSaleAccessWise) {
                            String[] splittedgeofencing = s.split("#");
                            if (splittedgeofencing[0].equalsIgnoreCase(Constants.employeeDetailObject.getSaleAccess())) {
                                geoFencingLimitInDouble = Double.parseDouble(splittedgeofencing[1]);
                                break;
                            }
                        }
                    } else {
                        String[] splittedgeofencing = geoFencingVariance.split("#");
                        geoFencingLimitInDouble = Double.parseDouble(splittedgeofencing[1]);
                    }
                } catch (Exception ignored) {
                }
                if (distance > geoFencingLimitInDouble * 1000) {
                    Utils.showToast(mContext, "You are not under coverage area.");
                } else {
                    if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                        ShowPurposeOfVisitListDialogToCheckIn();
                    } else {
                        checkInProcessFlow1(visitSequenceCheckIn);
                    }
                }
            } else {
                mAceDnsDatabase.updateCustomerLatLongi(Constants.selectedCheckINCustomer.getCustomerCode());
                showCaptureImageDialogOnCheckIn(visitSequenceCheckIn);
            }
        } else if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphotocheckout")) {
            if (Constants.menuDetailsObj.getbranchwise_geo_fencing().equalsIgnoreCase("yes") && mAceDnsDatabase.currentCustomerBranchGeoFencingYes(Constants.selectedCheckINCustomer.getBranchCode())
                    && Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                String customerLat = Constants.selectedCheckINCustomer.getbase_latt();
                String customerLong = Constants.selectedCheckINCustomer.getbase_longi();
                if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(currentLat) && Double.parseDouble(currentLong) > 0) {
                    float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                    if (distance > Double.parseDouble(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                        Utils.showToast(mContext, "You are not under coverage area.");
                    } else {
                        if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                            ShowPurposeOfVisitListDialogToCheckIn();
                        } else {
                            checkInProcessFlow1(visitSequenceCheckIn);
                        }
                    }
                } else {
                    mAceDnsDatabase.updateCustomerLatLongi(Constants.selectedCheckINCustomer.getCustomerCode());
                    showCaptureImageDialogOnCheckIn(visitSequenceCheckIn);
                }
            } else {
                showCaptureImageDialogOnCheckIn(visitSequenceCheckIn);
            }
        } else {
            if (Constants.menuDetailsObj.getbranchwise_geo_fencing().equalsIgnoreCase("yes") && mAceDnsDatabase.currentCustomerBranchGeoFencingYes(Constants.selectedCheckINCustomer.getBranchCode()) && Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                String customerLat = Constants.selectedCheckINCustomer.getbase_latt();
                String customerLong = Constants.selectedCheckINCustomer.getbase_longi();
                if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(currentLat) && Double.parseDouble(currentLong) > 0) {
                    float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                    Log.d("TAG", "_DOWNLOAD_ customerLat: " + customerLat);
                    Log.d("TAG", "_DOWNLOAD_ customerLong: " + customerLong);
                    Log.d("TAG", "_DOWNLOAD_ currentLat: " + currentLat);
                    Log.d("TAG", "_DOWNLOAD_ currentLong: " + currentLong);
                    Log.d("TAG", "_DOWNLOAD_ distance: " + distance);
                    Log.d("TAG", "_DOWNLOAD_ getGEO_fencing_variance: " + Constants.orderFormDetailsObj.getGEO_fencing_variance());
                    if (distance > Double.parseDouble(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                        Utils.showToast(mContext, "You are not under coverage area.");
                    } else {
                        if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                            ShowPurposeOfVisitListDialogToCheckIn();
                        } else {
                            checkInProcessFlow1(visitSequenceCheckIn);
                        }
                    }
                } else {
                    mAceDnsDatabase.updateCustomerLatLongi(Constants.selectedCheckINCustomer.getCustomerCode());
                    if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                        ShowPurposeOfVisitListDialogToCheckIn();
                    } else {
                        checkInProcessFlow1(visitSequenceCheckIn);
                    }
                }
            } else {
                if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes")) {
                    ShowPurposeOfVisitListDialogToCheckIn();
                } else {
                    checkInProcessFlow1(visitSequenceCheckIn);
                }
            }
        }
        LocationTrackerObject.stopLocationUpdates();
    }

    public void checkInProcessFlow1(boolean visitSequenceCheckIn) {
        setCustomerCheckInDataInPreferences(mContext, mRouteCode);
        if (!visitSequenceCheckIn) {
            loadMenuAgain();
        }
    }

    public void showROutesForVisitSequence() {
        mRouteDetailsListForVisitSequence = mAceDnsDatabase.getRouteListForVisitSequence(Constants.dayOfWeekForCustomer);
        if (mRouteDetailsListForVisitSequence.isEmpty()) {
            Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
        } else if (mRouteDetailsListForVisitSequence.size() == 1) {
            selectRouteFOrVisitSequenceAndGetCustomers(0);
        } else {
            MenuActivity.this.runOnUiThread(() -> ShowRouteListDialogVisitSequence(mRouteDetailsListForVisitSequence));
        }
    }

    private void selectRouteFOrVisitSequenceAndGetCustomers(int i) {
        boolean isCustomerFound = false;
        mSelectedRouteDetailsVisitSequence = mRouteDetailsListForVisitSequence.get(i);
        mCustomerDetailsList = new ArrayList<>();
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWiseForRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
        if (!mCustomerDetailsList.isEmpty()) {
            isCustomerFound = true;
            MenuActivity.this.runOnUiThread(() -> {
                mStepProgressDialog.dismiss();
                int index = 0;
                SelectCheckedInCustomerForVisitSequence(index);
            });
        }
        if (!isCustomerFound) {
            Utils.showToast(mContext, "No customer found on route " + mSelectedRouteDetailsVisitSequence.getRouteName() + ", Please select another.");
            showROutesForVisitSequence();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialogVisitSequence(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(MenuActivity.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            selectRouteFOrVisitSequenceAndGetCustomers(arg2);
        });
        ImageView image_cancel = routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(VISIBLE);
        image_cancel.setOnClickListener(arg0 -> {
            routeDialog.cancel();
            finish();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    private void CopyPdfAsset() {
        AssetManager assetManager = getAssets();
        InputStream in;
        OutputStream out;
        File file = new File(Utils.getAppStoragePath(mContext) + Constants.starHelpFileName);
        if (Constants.nickName.toUpperCase().matches("GOLDSTONET") || Constants.nickName.toUpperCase().matches("GOLDSTONE")) {
            file = new File(Utils.getAppStoragePath(mContext) + Constants.goldStoneHelpFileName);
        }
        try {
            if (!file.exists()) {
                in = assetManager.open(Constants.starHelpFileName);
                if (Constants.nickName.toUpperCase().matches("GOLDSTONET") || Constants.nickName.toUpperCase().matches("GOLDSTONE")) {
                    in = assetManager.open(Constants.goldStoneHelpFileName);
                }
                out = new FileOutputStream(file);
                copyFile(in, out);
                in.close();
                out.flush();
                out.close();
            }
        } catch (Exception ignored) {
        }
    }

    private void copyFile(InputStream in, OutputStream out) throws IOException {
        byte[] buffer = new byte[1024];
        int read;
        while ((read = in.read(buffer)) != -1) {
            out.write(buffer, 0, read);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowBranchListForMrpDialog() {
        final ArrayList<BranchMasterDetails> branchListForMrp = mAceDnsDatabase.getBranchListOfCurrentEmp();
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);
        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a Branch");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);
        BranchAdapter branchadapter = new BranchAdapter(mContext, R.layout.route_list_child, branchListForMrp);
        dialogList.setAdapter(branchadapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mDialogDepotName.cancel();
            Constants.selectedBranchForMrp = branchListForMrp.get(arg2);
            ShowMRPDialog();
        });
        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        mDialogDepotName.show();
    }

    @SuppressLint({"SimpleDateFormat"})
    private void attendanceProcess() {
        if (!isGettingCurrentLocation && attendanceAlias.equalsIgnoreCase("A") && Constants.currentLat.isEmpty()) {
            Utils.showToast(MenuActivity.this, "Could not determine your location. Please Check Location Settings");
            new GPSTracker(mContext);
        } else {
            if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_app") &&
                    mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(Constants.employeeDetailObject.getEmpCode()).isEmpty() &&
                    Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance()) && attendanceAlias.equalsIgnoreCase("A")) {
                Constants.selectedCustomer = mAceDnsDatabase.getCustomerDetailsRetailerApp();
                String customerLat = Constants.selectedCustomer.getbase_latt();
                String customerLong = Constants.selectedCustomer.getbase_longi();
                if (Utils.isNumeric(customerLat) && Double.parseDouble(customerLat) > 0 && Utils.isNumeric(customerLong) && Double.parseDouble(customerLong) > 0) {
                    float distance = Utils.linearDistanceBetweenTwoLatLong(customerLat, customerLong, currentLat, currentLong);
                    if (distance > Double.parseDouble(Constants.orderFormDetailsObj.getGEO_fencing_variance())) {
                        Utils.showToast(mContext, "You are not under coverage area.");
                        return;
                    }
                } else {
                    launchCameraToTakeImage();
                    return;
                }
            }
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                mAceDnsTransactionDatabase.UpdateSaudaAllocationLocationDataForFirstLoginOfDay();
                if (attendanceAlias.equalsIgnoreCase("A") && !Constants.menuDetailsObj.getattendance_journey_info().equalsIgnoreCase("null") &&
                        !Constants.menuDetailsObj.getattendance_journey_info().trim().equalsIgnoreCase("") && Constants.menuDetailsObj.getattendance_journey_info().contains("@")) {
                    showJourneyInfoSelectionList(timeStamp);
                } else {
                    boolean inserted = mAceDnsTransactionDatabase.insertToLocationTable(attendanceAlias, timeStamp);
                    if (inserted) {
                        mAceDnsTransactionDatabase.insertToAttendanceTable(attendanceAlias, timeStamp);
                    }
                    isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
                    if (!isAttendanceGiven) {
                        Utils.showToast(MenuActivity.this, "Could not get your Attendance. Please try again ");
                    } else {
                        LocationTrackerObject.stopLocationUpdates();
                        if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                            mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                        } else {
                            mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                        }
                        RouteListReportShowProcess();
                        makeAttendanceApiCall();
                    }
                }
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
            }
        }
    }

    private void showJourneyInfoSelectionList(String timeStamp) {
        if (Constants.nickName.equalsIgnoreCase("magik") || Constants.nickName.equalsIgnoreCase("abdos")) {
            String[] attenanceListSplitted = Constants.menuDetailsObj.getattendance_journey_info().split("@");
            if (attenanceListSplitted[1].contains(",")) {
                String dialogHeading = attenanceListSplitted[0].split("%")[0];
                String[] dialogBox = attenanceListSplitted[0].split("%");
                String[] journeyListSplitted = attenanceListSplitted[1].split(",");
                showJourneyCheckedList(new ArrayList<>(Arrays.asList(journeyListSplitted)), timeStamp, dialogHeading, dialogBox[1]);
            } else {
                boolean inserted = mAceDnsTransactionDatabase.insertToLocationTable(attendanceAlias, timeStamp);
                if (inserted) {
                    mAceDnsTransactionDatabase.insertToAttendanceTable(attendanceAlias, timeStamp);
                }
                isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
                if (!isAttendanceGiven) {
                    Utils.showToast(MenuActivity.this, "Could not get your Attendance. Please try again ");
                } else {
                    LocationTrackerObject.stopLocationUpdates();
                    if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                    } else {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                    }
                    RouteListReportShowProcess();
                    makeAttendanceApiCall();
                }
            }
        } else {
            if (Constants.menuDetailsObj.getattendance_journey_info().contains("#")) {
                PreferenceData.setjourneyInfoVehicleWheelType(mContext, "");
                try {
                    if (Constants.menuDetailsObj.getattendance_journey_info_options().contains("#")) {
                        showVehicleList(timeStamp, Constants.menuDetailsObj.getattendance_journey_info());
                    } else {
                        showJourneyList(timeStamp, "Attendance Journey Info", Constants.menuDetailsObj.getattendance_journey_info());
                    }
                } catch (Exception e) {
                    showJourneyList(timeStamp, "Attendance Journey Info", Constants.menuDetailsObj.getattendance_journey_info());
                }
            }
        }
    }

    @SuppressLint("SetTextI18n")
    private void showVehicleListByWheelType(String timeStamp, String attendanceCheckOutServerSetupData, String journeyVehicleTypesByWheel) {
        String[] journeyVehicleTypes = journeyVehicleTypesByWheel.split(",");
        ArrayList<String> vehicleTypeList = new ArrayList<>(Arrays.asList(journeyVehicleTypes));
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.layout_journey_vehicle_type_info);
        mDetailsDialog.setCancelable(false);
        TextView title = mDetailsDialog.findViewById(R.id.title);
        title.setText("Vehicle Details");
        ListView dialogList = mDetailsDialog.findViewById(R.id.list);
        dialogList.setOnItemClickListener((parent, view, position, id) -> {
            PreferenceData.setjourneyInfoVehicleWheelType(mContext, vehicleTypeList.get(position));
            mDetailsDialog.dismiss();
            showJourneyList(timeStamp, "Attendance Journey Info", attendanceCheckOutServerSetupData);
        });
        IncotermsAdapter finalAdapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio_material, vehicleTypeList);
        dialogList.setAdapter(finalAdapter);
        mDetailsDialog.show();
    }

    private void showVehicleList(String timeStamp, String attendanceCheckOutServerSetupData) {
        String[] journeyVehicleTypes = Constants.menuDetailsObj.getattendance_journey_info_options().split("#");
        ArrayList<String> vehicleTypeList = new ArrayList<>(Arrays.asList(journeyVehicleTypes));
        if (vehicleTypeList.get(0).contains("@")) {
            String dataAfterRemovingWheelsSuffix = vehicleTypeList.get(0).split("@")[0];
            vehicleTypeList.set(0, dataAfterRemovingWheelsSuffix);
        }
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.layout_journey_vehicle_type_info);
        mDetailsDialog.setCancelable(false);
        ListView dialogList = mDetailsDialog.findViewById(R.id.list);
        dialogList.setOnItemClickListener((parent, view, position, id) -> {
            PreferenceData.setjourneyInfoOwnOrPublicVehicle(mContext, vehicleTypeList.get(position));
            mDetailsDialog.dismiss();
            if (vehicleTypeList.get(position).equalsIgnoreCase("own vehicle")) {
                if (journeyVehicleTypes[0].contains("@")) {
                    String[] journeyVehicleTypesByWheels = journeyVehicleTypes[0].split("@");
                    showVehicleListByWheelType(timeStamp, attendanceCheckOutServerSetupData, journeyVehicleTypesByWheels[1]);
                } else {
                    showJourneyList(timeStamp, "Attendance Journey Info", attendanceCheckOutServerSetupData);
                }
            } else {
                boolean inserted = mAceDnsTransactionDatabase.insertToLocationTable(attendanceAlias, timeStamp);
                if (inserted) {
                    mAceDnsTransactionDatabase.insertToAttendanceTable(attendanceAlias, timeStamp);
                }
                isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
                if (!isAttendanceGiven) {
                    Utils.showToast(MenuActivity.this, "Could not get your Attendance. Please try again ");
                } else {
                    LocationTrackerObject.stopLocationUpdates();
                    if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                    } else {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                    }
                    RouteListReportShowProcess();
                    makeAttendanceApiCall();
                }
            }
        });
        IncotermsAdapter finalAdapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio_material, vehicleTypeList);
        dialogList.setAdapter(finalAdapter);
        mDetailsDialog.show();
    }

    public void showJourneyCheckedList(ArrayList<String> empActivityList, String timeStamp, String dialogHeading, String dialogBox) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.activity_checked_recyclerview);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setVisibility(GONE);
        mDetailsDialog.setCancelable(false);
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(arg0 -> {
            SparseBooleanArray selectedRows = finalAdapter.getSelectedIds();
            if (selectedRows.size() > 0) {
                StringBuilder stringBuilder = new StringBuilder();
                for (int i = 0; i < selectedRows.size(); i++) {
                    if (selectedRows.valueAt(i)) {
                        String selectedRowLabel = empActivityList.get(selectedRows.keyAt(i));
                        stringBuilder.append(selectedRowLabel).append(", ");
                    }
                }
                String chosenData = stringBuilder.toString();
                if (chosenData.endsWith(",")) {
                    chosenData = chosenData.substring(0, chosenData.length() - 1);
                }
                mDetailsDialog.dismiss();
                boolean inserted = mAceDnsTransactionDatabase.insertToLocationTable(attendanceAlias, timeStamp);
                if (inserted) {
                    mAceDnsTransactionDatabase.insertToAttendanceTable(attendanceAlias, timeStamp);
                    mAceDnsTransactionDatabase.insertToJourneyInfoTableForAccitity("A", timeStamp, chosenData);
                }
                isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
                if (!isAttendanceGiven) {
                    Utils.showToast(MenuActivity.this, "Could not get your Attendance. Please try again ");
                } else {
                    LocationTrackerObject.stopLocationUpdates();
                    if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                    } else {
                        mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                    }
                    RouteListReportShowProcess();
                    makeAttendanceApiCall();
                }
            } else {
                Toast.makeText(mContext, "At least one item must be selected to proceed", Toast.LENGTH_SHORT).show();
            }
        });
        textViewTitleName.setText(dialogHeading);
        RecyclerView dialogList = mDetailsDialog.findViewById(R.id.list);
        finalAdapter = new RecyclerViewAdapter(mContext, empActivityList, dialogBox);
        dialogList.setAdapter(finalAdapter);
        mDetailsDialog.show();
    }

    public void showJourneyList(String timeStamp, String dialogHeading, String attendanceCheckOutServerSetupData) {
        String[] attenanceListSplitted = attendanceCheckOutServerSetupData.split("#");
        processDone = false;
        checkboxdata = "";
        startingKm = "";
        endingKm = "";
        oddometerImage = "";
        journeyListSplitted = null;
        oddometerPicBitmap = null;
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.layout_journey_info);
        oddometerPicImageView = mDetailsDialog.findViewById(R.id.oddometerPicImageView);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setVisibility(GONE);
        mDetailsDialog.setCancelable(false);
        if (attendanceCheckOutServerSetupData.contains("image")) {
            LinearLayout odometerPicLayout = mDetailsDialog.findViewById(R.id.odometerPicLayout);
            odometerPicLayout.setVisibility(VISIBLE);
            Button buttonAddPicOutlet = mDetailsDialog.findViewById(R.id.buttonAddPicOutlet);
            buttonAddPicOutlet.setOnClickListener(arg0 -> {
                Intent takePicture = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                startActivityForResult(takePicture, TAKE_PHOTO_journey_info);
            });
        }
        if (attendanceCheckOutServerSetupData.contains("Starting Km%textbox")) {
            TextView StartingKmTextView = mDetailsDialog.findViewById(R.id.StartingKmTextView);
            StartingKmTextView.setVisibility(VISIBLE);
            StartingKmTextView.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    startingKm = StartingKmTextView.getText().toString();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
        }
        if (attendanceCheckOutServerSetupData.contains("Ending Km%textbox")) {
            TextView EndingKmTextView = mDetailsDialog.findViewById(R.id.EndingKmTextView);
            EndingKmTextView.setVisibility(VISIBLE);
            EndingKmTextView.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    endingKm = EndingKmTextView.getText().toString();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
        }
        if (attendanceCheckOutServerSetupData.contains("checkbox")) {
            int positionOfCHeckboxInArray = -1;
            for (int i = 0; i < attenanceListSplitted.length; i++) {
                if (attenanceListSplitted[i].contains("checkbox")) {
                    positionOfCHeckboxInArray = i;
                    break;
                }
            }
            String arrayItemWithCheckbox = attenanceListSplitted[positionOfCHeckboxInArray];
            String[] arrayItemWithCheckboxSplitted = arrayItemWithCheckbox.split("%");
            String[] arrayItemWithCheckboxSplitted2 = arrayItemWithCheckboxSplitted[1].split("@");
            journeyListSplitted = arrayItemWithCheckboxSplitted2[1].split(",");
            RecyclerView dialogList = mDetailsDialog.findViewById(R.id.list);
            dialogList.setVisibility(VISIBLE);
            finalAdapter = new RecyclerViewAdapter(mContext, new ArrayList<>(Arrays.asList(journeyListSplitted)));
            dialogList.setAdapter(finalAdapter);
        }
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        textViewTitleName.setText(dialogHeading);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(arg0 -> {
            if (attendanceCheckOutServerSetupData.contains("checkbox")) {
                SparseBooleanArray selectedRows = finalAdapter.getSelectedIds();
                if (selectedRows.size() > 0) {
                    StringBuilder stringBuilder = new StringBuilder();
                    for (int i = 0; i < selectedRows.size(); i++) {
                        if (selectedRows.valueAt(i)) {
                            String selectedRowLabel = new ArrayList<>(Arrays.asList(journeyListSplitted)).get(selectedRows.keyAt(i));
                            stringBuilder.append(selectedRowLabel).append(", ");
                        }
                    }
                    checkboxdata = stringBuilder.toString();
                    if (checkboxdata.endsWith(",")) {
                        checkboxdata = checkboxdata.substring(0, checkboxdata.length() - 1);
                    }
                }
            }
            if (dialogHeading.toLowerCase().contains("checkout journey info")) {
                if (endingKm.matches("")) {
                    Utils.showToast(mContext, "Please provide Ending Km before submitting");
                } else if (oddometerImage.matches("")) {
                    Utils.showToast(mContext, "Please add Odometer Image before submitting");
                } else {
                    mAceDnsTransactionDatabase.UpdateCheckoutJourneyInfo("CH", timeStamp, checkboxdata, startingKm, endingKm, "", oddometerImage);
                    processDone = true;
                }
            } else {
                if (startingKm.matches("")) {
                    Utils.showToast(mContext, "Please provide Starting Km before submitting");
                } else if (oddometerImage.matches("")) {
                    Utils.showToast(mContext, "Please add Odometer Image before submitting");
                } else {
                    mAceDnsTransactionDatabase.insertToJourneyInfoTableForAccitityEnlarged("A", timeStamp, checkboxdata, startingKm, endingKm, oddometerImage, "");
                    processDone = true;
                }
            }
            if (processDone) {
                if (!oddometerImage.matches("")) {
                    new TRANS_TourAttachmentExportTask(mContext, "add_oddometer", "", false, false).execute();
                }
                mDetailsDialog.dismiss();
                if (dialogHeading.toLowerCase().contains("checkout journey info")) {
//                    ShowCheckOutDialog(timeStamp);
                    new TRANS_count_AsyncTask(mContext, "0", timeStamp).execute();
                } else {
                    boolean inserted = mAceDnsTransactionDatabase.insertToLocationTable(attendanceAlias, timeStamp);
                    if (inserted) {
                        mAceDnsTransactionDatabase.insertToAttendanceTable(attendanceAlias, timeStamp);
                    }
                    isAttendanceGiven = mAceDnsTransactionDatabase.checkAttendanceForToday();
                    if (!isAttendanceGiven) {
                        Utils.showToast(MenuActivity.this, "Could not get your Attendance. Please try again ");
                    } else {
                        LocationTrackerObject.stopLocationUpdates();
                        if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                            mButtonAttendence.setBackgroundResource(R.drawable.attendance_clkdbackup);
                        } else {
                            mButtonAttendence.setBackgroundResource(R.drawable.attendance_done_retailerapp);
                        }
                        RouteListReportShowProcess();
                        makeAttendanceApiCall();
                    }
                }
            }
        });
        mDetailsDialog.show();
    }

    private void makeAttendanceApiCall() {
        isInternetPresent = cd.isConnectingToInternet();
        if (isInternetPresent) {
            PendingingDataUpload("attendencetimepending");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showAttendanceTypeDialog() {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.dialog_attendance_type);
        TextView txtMsg = payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Please choose an option");
        RadioGroup payTypeOption = payTypeDialog.findViewById(R.id.rg_pay_options);
        if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
            RadioButton rb = payTypeDialog.findViewById(R.id.radio3);
            rb.setVisibility(VISIBLE);
        }
        payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
            int radioButtonID = group.getCheckedRadioButtonId();
            View radioButton = group.findViewById(radioButtonID);
            int selectedRadio = group.indexOfChild(radioButton);
            if (selectedRadio == 0) {
                attendanceAlias = "A";
                attendanceProcess();
            } else if (selectedRadio == 1) {
                attendanceAlias = "LR";
                showConfirmationAttendance();
            } else {
                if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                    if (selectedRadio == 3) {
                        attendanceAlias = "Holiday";
                        showConfirmationHolidayAttendance();
                    } else if (selectedRadio == 2) {
                        attendanceAlias = "WO";
                        showConfirmationAttendance();
                    }
                } else {
                    attendanceAlias = "WO";
                    showConfirmationAttendance();
                }
            }
            payTypeDialog.cancel();
        });
        Button cancel = payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(VISIBLE);
        cancel.setOnClickListener(arg0 -> payTypeDialog.cancel());
        payTypeDialog.show();
    }

    public void RouteListReportShowProcess() {
        if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
            getDayToShowRoutes();
            ArrayList<String> listOfRoutes = mAceDnsDatabase.getRouteListDayOfWeekWise();
            if (!listOfRoutes.isEmpty())
                ShowRouteListForTOurPlanDayWiseDialog(listOfRoutes);
        }
    }

    public void getDayToShowRoutes() {
        if (mAceDnsTransactionDatabase.isAlreadySwappedForToday()) {
            Constants.dayOfWeekForCustomer = mAceDnsTransactionDatabase.getTheDaySwappedWithToday();
        } else if (mAceDnsTransactionDatabase.isTodaySwappedWithAnyPreviousDay()) {
            Constants.dayOfWeekForCustomer = mAceDnsTransactionDatabase.getThePreviousDaySwappedWithToday();
        } else {
            Constants.dayOfWeekForCustomer = Utils.dayOfWeek();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialogToCheckIn(final ArrayList<String> verticalList) {
        final CatalogueVerticalSelectionAdapter adapterCust = new CatalogueVerticalSelectionAdapter(mContext, R.layout.customer_list_child, verticalList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        Button back = mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(VISIBLE);
        back.setOnClickListener(view -> mDialogCustomer.cancel());
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a " + Constants.menuDetailsObj.getcatalogue_dependency() + ".");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            Constants.catalogueorSchemeVal = verticalList.get(arg2);
            makeCatalogLoadingProcess();
        });
        Button addnewcustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);
        mDialogCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListForTOurPlanDayWiseDialog(final ArrayList<String> verticalList) {
        final CatalogueVerticalSelectionAdapter adapterCust = new CatalogueVerticalSelectionAdapter(mContext, R.layout.customer_list_child, verticalList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Your route list for today. ");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
        });
        Button back = mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(VISIBLE);
        back.setOnClickListener(view -> mDialogCustomer.cancel());
        Button addnewcustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);
        mDialogCustomer.show();
    }

    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productGroupList = mAceDnsDatabase.getProductGroupList(carryInSales);
                        tempProductGroupList = new ArrayList<>();
                        reInitialiseProductGroupList();
                        break;
                    case 2:
                        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(param, carryInSales);
                        tempProductSubGroupList = new ArrayList<>();
                        reInitialiseProductSubGroupList();
                        break;
                    case 3:
                        productBrandList = mAceDnsDatabase.getProductBrandList(param, carryInSales);
                        tempProductBrandList = new ArrayList<>();
                        reInitialiseProductBrandList();
                        break;
                    case 4:
                        if (carryInSales || Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes")) {
                            productMasterList = mAceDnsDatabase.getProductMasterList(param, filterNo, carryInSales);
                        } else {
                            productMasterList = mAceDnsDatabase.getProductMasterListIgnoringStock(param, filterNo);
                        }
                        tempProductList = new ArrayList<>();
                        reInitialiseProductList();
                        break;
                    case 5, 6:
                        break;
                }
                Message msgObj = orderDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                orderDataHandler.sendMessage(msgObj);
            }
        }.start();
    }

    private void makeCatalogLoadingProcess() {
        String[] CatalogueArray = Constants.catalogueorSchemeVal.split("\\^");
        try {
            File outputFile = new File(Utils.getAppStoragePath(mContext) + CatalogueArray[2] + "-" + CatalogueArray[1]);
            if (outputFile.exists()) {
                openCatalogue(mContext);
            } else {
                downloadCatalogueIfInternetIsPresent();
            }
        } catch (Exception e) {
            downloadCatalogueIfInternetIsPresent();
        }
    }

    private void downloadCatalogueIfInternetIsPresent() {
        isInternetPresent = cd.isConnectingToInternet();
        if (isInternetPresent) {
            new FILE_CataloguePdfDownload(mContext, true).execute();
        } else {
            Toast.makeText(mContext, "Catalogue could not be downloaded. Please check your internet connection", Toast.LENGTH_SHORT).show();
        }
    }

    public void showTransactionDeleteDialog() {
        String empCode = Constants.employeeDetailObject.getEmpCode();
        try {
            new DATA_DeleteTransactionTask(MenuActivity.this, null, false, empCode).execute();
        } catch (Exception ignored) {
        }
    }

    public void showOutstandingDetails() {
        MenuOutstandingExpandableAdapter adapter = new MenuOutstandingExpandableAdapter(outstandingParentItems, outstandingChildItems);
        adapter.setInflater((LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE), this);
        final Dialog outstandingListDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        outstandingListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        outstandingListDialog.setContentView(R.layout.menu_outstanding_dialog);
        outstandingListDialog.setCancelable(false);
        ExpandableListView expandableList = outstandingListDialog.findViewById(R.id.list);
        expandableList.setAdapter(adapter);
        TextView txtOutstanding = outstandingListDialog.findViewById(R.id.txt_outstanding);
        txtOutstanding.setText(mTotalotalOutstanding);
        Button btnCancel = outstandingListDialog.findViewById(R.id.btn_cancel);
        btnCancel.setOnClickListener(v -> outstandingListDialog.cancel());
        expandableList.setOnChildClickListener((expandableListView, view, groupPosition, childPosition, id) -> {
            System.out.println("Group Pos : " + groupPosition);
            System.out.println("Child Pos : " + childPosition);
            if (outstandingChildItems.get(groupPosition).getOutstandingList().size() == childPosition + 1) {
                expandableListView.collapseGroup(groupPosition);
            }
            return true;
        });
        outstandingListDialog.show();
    }

    public void prepareOutstandingData(final String customerCode) {
        mProgressDialogOutstanding = new ProgressDialog(MenuActivity.this);
        mProgressDialogOutstanding.setMessage("Preparing Data. Please wait..");
        mProgressDialogOutstanding.show();
        new Thread() {
            public void run() {
                mTotalotalOutstanding = mAceDnsDatabase.getTotalOutstandingForMenu(customerCode);
                mTotalotalOutstanding = (currency + defaultFormat.format(Float.parseFloat(mTotalotalOutstanding)));
                outstandingParentItems = new ArrayList<>();
                outstandingParentItems = mAceDnsDatabase.getOutstandingSummaryDetails(customerCode);
                outstandingChildItems = new ArrayList<>();
                outstandingChildItems = mAceDnsDatabase.getOutstandingDetailsForMenu(outstandingParentItems);
                Message msgObj = mHandlerOutstanding.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandlerOutstanding.sendMessage(msgObj);
            }
        }.start();
    }

    public void PendingingDataUploadFeedback(final String pendingtype) {
        mProgressDialogPending = new ProgressDialog(MenuActivity.this);
        mProgressDialogPending.setMessage("Uploading pending Data--.\nPlease wait..");
        mProgressDialogPending.setCancelable(false);
        mProgressDialogPending.show();
        new Thread() {
            public void run() {
                if (Constants.menuDetailsObj.getFeedback_backup().length() > 8) {
                    TRANS_SubmitFeedBack_BackUp sbs = new TRANS_SubmitFeedBack_BackUp(MenuActivity.this, true, "SYNC");
                    sbs.execute();
                }
                Message msgObj = mHandlerPending.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", pendingtype);
                msgObj.setData(b);
                mHandlerPending.sendMessage(msgObj);
            }
        }.start();
    }

    public void PendingingDataUpload(final String pendingtype) {
        mProgressDialogPending = new ProgressDialog(MenuActivity.this);
        mProgressDialogPending.setMessage("Uploading pending Data--.\nPlease wait..");
        mProgressDialogPending.setCancelable(false);
        mProgressDialogPending.show();
        new Thread() {
            public void run() {
                if (Constants.menuDetailsObj.getAttendance().equalsIgnoreCase("yes")) {
                    ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("ATTENDANCE", "");
                    if (!unUploadedTransaction.isEmpty()) {
                        isFinished = false;
                        TRANS_AttnendanceTransactionTask sb2 = new TRANS_AttnendanceTransactionTask(MenuActivity.this, unUploadedTransaction);
                        sb2.execute();
                        while (!isFinished) {
                            if (sb2.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb2.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb2.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes")) {
                    new TRANS_SubmitTravelFoodingLodgingExpenseTask(mContext, false).execute();
                }
                if (orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer")) {
                    new TRANS_SubmitNewCustomerDetailsTask(mContext, true, false, "SYNCFROMMENU").execute();
                }
                if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes")) {
                    new TRANS_TravelFoodingLodgingAttachmentExportTask(mContext, "TOUR_TRAVEL", "1", false).execute();
                }
                ArrayList<RouteDetails> routeDetailsList = mAceDnsTransactionDatabase.getUnuploadedRouteList();
                if (!routeDetailsList.isEmpty()) {
                    isFinished = false;
                    TRANS_SubmitNewRoute sb2 = new TRANS_SubmitNewRoute(MenuActivity.this, "SYNC");
                    sb2.execute();
                    while (!isFinished) {
                        if (sb2.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb2.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb2.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
                    new TRANS_CashDepositeReceiveTask(mContext, false).execute();
                }
                ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnPublishNOTIFICATION();
                if (!unUploadedTransaction.isEmpty()) {
                    isFinished = false;
                    TRANS_SubmitNotificationTask async_notification = new TRANS_SubmitNotificationTask(MenuActivity.this);
                    async_notification.execute();
                    while (!isFinished) {
                        if (async_notification.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (async_notification.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (async_notification.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes")) {
                    isFinished = false;
                    TRANS_SubmitTDAllocation async_tdAllocation = new TRANS_SubmitTDAllocation(MenuActivity.this, false);
                    async_tdAllocation.execute();
                    while (!isFinished) {
                        if (async_tdAllocation.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (async_tdAllocation.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (async_tdAllocation.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                isFinished = false;
                RoutePlanDetails obj = mAceDnsDatabase.getRoutePlanDetailsObj();
                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                    if (obj.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                        ArrayList<RoutePlanMasterDetails> unUploadedDistinctRoute = mAceDnsTransactionDatabase.getUnuploadedRoute();
                        if (!unUploadedDistinctRoute.isEmpty()) {
                            isFinished = false;
                            TRANS_SubmitRouteCustomerPlan sb = new TRANS_SubmitRouteCustomerPlan(MenuActivity.this, true, "SYNC");
                            sb.execute();
                            while (!isFinished) {
                                if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                    isFinished = false;
                                }
                                if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                    isFinished = false;
                                }
                                if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                    isFinished = true;
                                }
                            }
                        }
                    } else {
                        ArrayList<RoutePlanMasterDetails> unUploadedRoute = mAceDnsTransactionDatabase.getUnuploadedRoute("");
                        if (!unUploadedRoute.isEmpty()) {
                            isFinished = false;
                            TRANS_SubmitRoutePlanTask sb = new TRANS_SubmitRoutePlanTask(MenuActivity.this, false, "SYNC");
                            sb.execute();
                            while (!isFinished) {
                                if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                    isFinished = false;
                                }
                                if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                    isFinished = false;
                                }
                                if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                    isFinished = true;
                                }
                            }
                        }
                    }
                }
                if (orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes") && orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes")
                        && orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                    ArrayList<RoutePlanMasterDetails> unUploadedDistinctRoute = mAceDnsTransactionDatabase.getUnuploadedRoute();
                    if (!unUploadedDistinctRoute.isEmpty()) {
                        isFinished = false;
                        TRANS_LoginTimePendingInvoiceInformationUpload sb = new TRANS_LoginTimePendingInvoiceInformationUpload(mContext);
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (orderFormDetailsObj.getadd_customer_activation().equalsIgnoreCase("yes")) {
                    new commonAsyncTaskMaster(mContext, "customerToDelete");
                }
                if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("customized")
                        || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) {
                    ArrayList<Location> al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitBusinessProspect sbExistingProspect = new TRANS_SubmitBusinessProspect(MenuActivity.this, true, "SYNC", "DC");
                        sbExistingProspect.execute();
                        TRANS_SubmitBusinessProspect sbNewProspect = new TRANS_SubmitBusinessProspect(MenuActivity.this, true, "SYNC", "DE");
                        sbNewProspect.execute();
                    }
                    if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("customized")) {
                        ArrayList<Location> unUploadedTransactionBP = mAceDnsTransactionDatabase.getUnuploadedTransaction("business_prospect_customize", "");
                        if (!unUploadedTransactionBP.isEmpty()) {
                            new TRANS_BusinessProspectCustomizeTransactionTask(mContext, unUploadedTransactionBP).execute();
                        }
                    }
                }
                if (Constants.menuDetailsObj.getDO().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        new TRANS_SubmitDOTask(mContext, false, "sync").execute();
                    }
                }
                if (Constants.menuDetailsObj.getgift_delivery().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        new TRANS_SubmitGiftTask(mContext, false, "sync").execute();
                        new TRANS_TourAttachmentExportTask(mContext, "add_gift", "", false, false).execute();
                    }
                }
                if (Constants.menuDetailsObj.getNotesInfo().equalsIgnoreCase("yes")) {
                    isFinished = false;
                    TRANS_SubmitNotesInfo sb = new TRANS_SubmitNotesInfo(mContext, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransaction_audit_location = mAceDnsTransactionDatabase.GetStockAuditLocation();
                    if (!al_unUploadedTransaction_audit_location.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitMarketFeedbackStockAudit sb = new TRANS_SubmitMarketFeedbackStockAudit(MenuActivity.this, true, "SYNC");
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                    ArrayList<Location> al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitFeedBack sbs = new TRANS_SubmitFeedBack(MenuActivity.this, true, "SYNC");
                        sbs.execute();
                        while (!isFinished) {
                            if (sbs.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sbs.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sbs.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getbargain().equalsIgnoreCase("yes")) {
                    ArrayList<Location> unUploadedTransaction_location = mAceDnsTransactionDatabase.GetLocation();
                    if (!unUploadedTransaction_location.isEmpty()) {
                        isFinished = false;
                        TRANS_LogintimePendingSAUDA sb = new TRANS_LogintimePendingSAUDA(MenuActivity.this, false);
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getreverseAuction().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("ra_sauda")) {
                    TRANS_SubmitNewBid sb = new TRANS_SubmitNewBid(mContext, false);
                    TRANS_SubmitCounterBid sb2 = new TRANS_SubmitCounterBid(mContext, false);
                    sb2.execute();
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransaction;
                    al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("STOCK", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitStockAuditTask sb = new TRANS_SubmitStockAuditTask(MenuActivity.this, false, "SYNC");
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransaction;
                    al_unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("STOCK_RETURN", "");
                    if (!al_unUploadedTransaction.isEmpty()) {
                        new TRANS_SubmitStockReturnTask(mContext, true, "SYNC").execute();
                    }
                }
                if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes")) {
                    ArrayList<Location> unUploadedTransaction_Survey_location = mAceDnsTransactionDatabase.GetSurveyLocation();
                    if (!unUploadedTransaction_Survey_location.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitSurveyTask sb = new TRANS_SubmitSurveyTask(MenuActivity.this, false, "SYNC");
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        isFinished = false;
                        TRANS_SurveyImageTask SurveyImage = new TRANS_SurveyImageTask(mContext, "SURVEY", "", false);
                        SurveyImage.execute();
                        while (!isFinished) {
                            if (SurveyImage.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (SurveyImage.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (SurveyImage.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        isFinished = false;
                        TRANS_SubmitRedudantSurvey sbx = new TRANS_SubmitRedudantSurvey(MenuActivity.this, false);
                        sbx.execute();
                        while (!isFinished) {
                            if (sbx.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sbx.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sbx.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                try (AceDnsDatabase helper = new AceDnsDatabase(mContext)) {
                    if (!helper.isDeviceDetailsSent()) {
                        new TRANS_SubmitDeviceInfo(mContext).execute();
                    }
                }
                if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes")) {
                    mAceDnsDatabase.GETSurveyFormDetails();
                    if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                        String data = Constants.surveyFormDetailsObj.getSurveySubMenuDetails().trim();
                        if (data.contains(",")) {
                            String[] surveymenu = data.split(",");
                            for (String menuname : surveymenu) {
                                if (menuname.equalsIgnoreCase("FS")) {
                                    boolean fsaccess = mAceDnsDatabase.MenuAccess("FS");
                                    if (fsaccess) {
                                        isFinished = false;
                                        TRANS_SubmitFootSoldier sb = new TRANS_SubmitFootSoldier(MenuActivity.this, false, "SYNC");
                                        sb.execute();
                                        while (!isFinished) {
                                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                                isFinished = false;
                                            }
                                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                                isFinished = false;
                                            }
                                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                                isFinished = true;
                                            }
                                        }
                                    }
                                }
                                if (menuname.equalsIgnoreCase("DCA")) {
                                    boolean dcaaccess = mAceDnsDatabase.MenuAccess("DCA");
                                    if (dcaaccess) {
                                        isFinished = false;
                                        TRANS_SubmitSurveyPublishTask sb = new TRANS_SubmitSurveyPublishTask(MenuActivity.this, "SYNC");
                                        sb.execute();
                                        while (!isFinished) {
                                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                                isFinished = false;
                                            }
                                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                                isFinished = false;
                                            }
                                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                                isFinished = true;
                                            }
                                        }
                                    }
                                }
                                if (menuname.equalsIgnoreCase("OFFER")) {
                                    ArrayList<Location> unUploadedTransaction_Offer_location = mAceDnsTransactionDatabase.GetSurveyOfferLocation();
                                    if (!unUploadedTransaction_Offer_location.isEmpty()) {
                                        boolean dcaaccess = mAceDnsDatabase.MenuAccess("OFFER");
                                        if (dcaaccess) {
                                            isFinished = false;
                                            TRANS_SubmitSurveyOfferTask sb = new TRANS_SubmitSurveyOfferTask(mContext, false);
                                            sb.execute();
                                            while (!isFinished) {
                                                if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                                    isFinished = false;
                                                }
                                                if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                                    isFinished = false;
                                                }
                                                if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                                    isFinished = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getAttendance().equalsIgnoreCase("yes")
                        || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
                    isFinished = false;
                    TRANS_LoginTimePendingOrder sb = new TRANS_LoginTimePendingOrder(MenuActivity.this, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes")) {
                    TRANS_SubmitYellowCardTask sb = new TRANS_SubmitYellowCardTask(mContext, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes")) {
                    TRANS_SubmitJointWorkObservationTask sb = new TRANS_SubmitJointWorkObservationTask(mContext, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                new TRANS_SubmitCustomerClassUpdationTask(mContext, false).execute();
                if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) {
                    TRANS_SubmitCashDepositTask sb = new TRANS_SubmitCashDepositTask(mContext, false);
                    sb.execute();
                    TRANS_SubmitCashTransferTask sb2 = new TRANS_SubmitCashTransferTask(mContext, false);
                    sb2.execute();
                }
                if ((Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("consolidated"))) {
                    new TRANS_SubmitTravelFoodingLodgingExpenseTask(mContext, false).execute();
                    if (Constants.menuDetailsObj.getfuel_bill_attachment().equalsIgnoreCase("yes")) {
                        new TRANS_TourAttachmentExportTask(mContext, "add_fuel_bill", "", false, false).execute();
                    }
                }
                if (Constants.menuDetailsObj.getquotation().equalsIgnoreCase("yes")) {
                    TRANS_SubmitQuotationDetailsTask sb = new TRANS_SubmitQuotationDetailsTask(mContext, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
                    TRANS_SubmitRetailerStockInTask sb = new TRANS_SubmitRetailerStockInTask(mContext, false);
                    new TRANS_SubmitRetailerStockReallocationTask(mContext, false).execute();
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                    TRANS_SubmitRetailerStockOutTask sb2 = new TRANS_SubmitRetailerStockOutTask(mContext, false);
                    sb2.execute();
                    while (!isFinished) {
                        if (sb2.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb2.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb2.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getcollection_forecast().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection_forecast")) {
                    TRANS_SubmitCollectionForecastTask sb = new TRANS_SubmitCollectionForecastTask(mContext, false);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getgrn().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("grn")) {
                    new TRANS_SubmitGrnTransactionTask(mContext, false).execute();
                }
                if (Constants.menuDetailsObj.getCRM_app().equalsIgnoreCase("yes")) {
                    new TRANS_SubmitCRMTask(mContext, false).execute();
                }
                ArrayList<Location> unUploadedTransaction_checkOUT = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
                if (!unUploadedTransaction_checkOUT.isEmpty()) {
                    TRANS_CheckOutTask sbCheckout = new TRANS_CheckOutTask(mContext, false);
                    sbCheckout.execute();
                    while (!isFinished) {
                        if (sbCheckout.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sbCheckout.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sbCheckout.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                Log.d("TAG", "runnnnnnnnnnnnnn: " + Constants.menuDetailsObj.getCheckInOut());
                if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
                    ArrayList<Location> get_LocationofCheckInOutList = mAceDnsTransactionDatabase.GetUnuploadedLocationofCheckInOut();
                    Log.d("TAG", "runnnnnnnnnnnnnn: " + get_LocationofCheckInOutList.size());
                    if (!get_LocationofCheckInOutList.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitCheckInCheckOut sb = new TRANS_SubmitCheckInCheckOut(MenuActivity.this, false, "SYNC");
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.menuDetailsObj.getWholeSaleInfo().equalsIgnoreCase("yes")) {
                    ArrayList<Location> al_unUploadedTransactionS = mAceDnsTransactionDatabase.GetWholeSaleLocation();
                    if (!al_unUploadedTransactionS.isEmpty()) {
                        isFinished = false;
                        TRANS_SubmitWholeSaleTask sb = new TRANS_SubmitWholeSaleTask(MenuActivity.this, "SYNC");
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                    ArrayList<Location> unUploadedTransactionTS = mAceDnsTransactionDatabase.getUnuploadedTransaction("TOUR SWAP", "");
                    if (!unUploadedTransactionTS.isEmpty()) {
                        isFinished = false;
                        TRANS_TourDaySwapTransactionTask sb = new TRANS_TourDaySwapTransactionTask(MenuActivity.this, false);
                        sb.execute();
                        while (!isFinished) {
                            if (sb.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                    }
                }
                ArrayList<CallDurationDetails> unUploadedTransactionCallDuration = mAceDnsTransactionDatabase.getUnuploadedCallDurationList();
                if (!unUploadedTransactionCallDuration.isEmpty()) {
                    isFinished = false;
                    TRANS_CallDurationTransactionTask sbCallDuration = new TRANS_CallDurationTransactionTask(MenuActivity.this, false);
                    sbCallDuration.execute();
                    while (!isFinished) {
                        if (sbCallDuration.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sbCallDuration.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sbCallDuration.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (pendingtype.equalsIgnoreCase("attendencetimepending")) {
                    isFinished = false;
                    DATA_GenerateDCRTask sb = new DATA_GenerateDCRTask(MenuActivity.this, true);
                    sb.execute();
                    while (!isFinished) {
                        if (sb.getStatus() == AsyncTask.Status.PENDING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.RUNNING) {
                            isFinished = false;
                        }
                        if (sb.getStatus() == AsyncTask.Status.FINISHED) {
                            isFinished = true;
                        }
                    }
                }
                if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
                    ArrayList<Location> unUploadedTransactionDR = mAceDnsTransactionDatabase.getUnuploadedTransaction("doctor_visit", "");
                    TRANS_Doctor_Visit_TransactionTask sb2 = new TRANS_Doctor_Visit_TransactionTask(MenuActivity.this, unUploadedTransactionDR);
                    sb2.execute();
                }
                if (Constants.menuDetailsObj.getStockist_visit().toLowerCase().matches("yes")) {
                    ArrayList<Location> unUploadedTransactionDR = mAceDnsTransactionDatabase.getUnuploadedTransaction("stockist_visit", "");
                    TRANS_Stokist_Visit_TransactionTask sb2 = new TRANS_Stokist_Visit_TransactionTask(MenuActivity.this, unUploadedTransactionDR);
                    sb2.execute();
                }
                Message msgObj = mHandlerPending.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", pendingtype);
                msgObj.setData(b);
                mHandlerPending.sendMessage(msgObj);
            }
        }.start();
    }

    public void prepareAgeingData(final String period1, final String period2, final String period3) {
        mProgressDialogAgeing = new ProgressDialog(MenuActivity.this);
        mProgressDialogAgeing.setMessage("Preparing Data. Please wait..");
        mProgressDialogAgeing.show();
        new Thread() {
            public void run() {
                outstandingParentItems = new ArrayList<>();
                outstandingParentItems = mAceDnsDatabase.getAgeingSummaryDetails();
                ageingChildItems = new ArrayList<>();
                ageingChildItems = mAceDnsDatabase.getAgeingDetailsForMenu(outstandingParentItems, Integer.parseInt(period1), Integer.parseInt(period2), Integer.parseInt(period3));
                Message msgObj = mHandlerAgeing.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                b.putString("Period1", period1);
                b.putString("Period2", period2);
                b.putString("Period3", period3);
                msgObj.setData(b);
                mHandlerAgeing.sendMessage(msgObj);
            }
        }.start();
    }

    public static boolean isTimeAutomatic(Context c) {
        return Settings.Global.getInt(c.getContentResolver(), Settings.Global.AUTO_TIME, 0) == 1;
    }

    @SuppressLint("SetTextI18n")
    public void showUnresolvedMerchandisingListDialog() {
        final ArrayList<MerchandisingDetails> unresolvedMerchandisinglist = mAceDnsTransactionDatabase.getUnresolvedMerchandisingList();
        if (!unresolvedMerchandisinglist.isEmpty()) {
            final Dialog merchandisingDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            merchandisingDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            merchandisingDialog.setContentView(R.layout.select_from_list);
            merchandisingDialog.setTitle("Please select an option");
            merchandisingDialog.setCancelable(false);
            TextView title = merchandisingDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = merchandisingDialog.findViewById(R.id.list);
            MerchandisingAdapter adapter1 = new MerchandisingAdapter(MenuActivity.this, R.layout.merchandising_list_child, unresolvedMerchandisinglist);
            dialogList.setAdapter(adapter1);
            Button cancel = merchandisingDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(VISIBLE);
            cancel.setOnClickListener(arg0 -> merchandisingDialog.cancel());
            merchandisingDialog.show();
        } else {
            Utils.showToast(MenuActivity.this, "No issues have been reported so far.");
        }
    }

    public void showImageSourceDialog() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(MenuActivity.this);
        alertDialogBuilder
                .setMessage("Select an Option")
                .setCancelable(true)
                .setPositiveButton("From Gallery", (dialog, id) -> {
                    Intent pickPhoto = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
                    startActivityForResult(pickPhoto, 1);
                })
                .setNegativeButton("Open Camera", (dialog, id) -> {
                    Intent imageIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                    mImagePath = Utils.getAppStoragePath(mContext) + "employeeImage";
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                        Uri uriSavedImage = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", new File(mImagePath));
                        imageIntent.putExtra(MediaStore.EXTRA_OUTPUT, uriSavedImage);
                        startActivityForResult(imageIntent, 0);
                    } else {
                        Uri uriSavedImage = Uri.fromFile(new File(mImagePath));
                        imageIntent.putExtra(MediaStore.EXTRA_OUTPUT, uriSavedImage);
                        startActivityForResult(imageIntent, 0);
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint({"SimpleDateFormat"})
    protected void onActivityResult(int requestCode, int resultCode, Intent imageReturnedIntent) {
        super.onActivityResult(requestCode, resultCode, imageReturnedIntent);
        switch (requestCode) {
            case 22:
                if (resultCode == RESULT_OK) {
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Bundle extras = imageReturnedIntent.getExtras();
                    assert extras != null;
                    oddometerPicBitmap = (Bitmap) extras.get("data");
                    oddometerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + oddometerImage;
                    File outputFile;
                    outputFile = new File(imagePath);
                    if (outputFile.exists())
                        outputFile.delete();
                    FileOutputStream out = null;
                    try {
                        out = new FileOutputStream(outputFile);
                    } catch (FileNotFoundException ignored) {
                    }
                    assert out != null;
                    oddometerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
                    oddometerPicImageView.setImageBitmap(Utils.getResizedBitmap(oddometerPicBitmap, 100, 100));
                    mAceDnsTransactionDatabase.insertToSupportingAttachTable(oddometerImage, "add_oddometer");
                }
                break;
            case 0:
                if (resultCode == RESULT_OK) {
                    Bitmap bitmap = BitmapFactory.decodeFile(mImagePath);
                    bitmap = Bitmap.createScaledBitmap(bitmap, 180, 180, true);
                    Bitmap finalBitmap = Utils.getCircleBitmap(bitmap);
                    mImageViewUserPic.setImageBitmap(finalBitmap);
                    ByteArrayOutputStream stream = new ByteArrayOutputStream();
                    finalBitmap.compress(Bitmap.CompressFormat.PNG, 100, stream);
                    byte[] byteArray = stream.toByteArray();
                    mAceDnsDatabase.saveUserImage(byteArray);
                }
                break;
            case 1:
                if (resultCode == RESULT_OK) {
                    Uri selectedImage = imageReturnedIntent.getData();
                    Bitmap bitmap = null;
                    try {
                        bitmap = MediaStore.Images.Media.getBitmap(this.getContentResolver(), selectedImage);
                        bitmap = Bitmap.createScaledBitmap(bitmap, 180, 180, true);
                    } catch (Exception ignored) {
                    }
                    assert bitmap != null;
                    Bitmap finalBitmap = Utils.getCircleBitmap(bitmap);
                    mImageViewUserPic.setImageBitmap(finalBitmap);
                    ByteArrayOutputStream stream = new ByteArrayOutputStream();
                    finalBitmap.compress(Bitmap.CompressFormat.PNG, 100, stream);
                    byte[] byteArray = stream.toByteArray();
                    mAceDnsDatabase.saveUserImage(byteArray);
                }
                break;
            case 2:
                if (resultCode == RESULT_OK) {
                    try {
                        Bitmap imageBitmap = ActivitySurveyDCA.decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
                        supportingAttachmentMap.put(mSImageName, imageBitmap);
                        Bitmap imageBitmapResized = Utils.getResizedBitmap(imageBitmap, mWidth, mHeight);
                        if (numberOfImageAdded == 0) {
                            if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_app")) {
                                mAceDnsDatabase.updateCustomerLatLongi(Constants.selectedCustomer.getCustomerCode());
                                attendanceProcess();
                            } else {
                                attachmentImageView1.setImageBitmap(imageBitmapResized);
                                numberOfImageAdded++;
                                showAddAnotherImageDialog();
                            }
                        } else if (numberOfImageAdded == 1) {
                            attachmentImageView2.setImageBitmap(imageBitmapResized);
                            numberOfImageAdded++;
                            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception ex) {
                        Toast.makeText(mContext, "Image is too large", Toast.LENGTH_SHORT).show();
                    }
                }
                break;
            case 9999:
                if (resultCode == RESULT_CANCELED) {
                    if (LocationTracker.transactionType.equalsIgnoreCase("attendance") || Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes")) {
                        LocationTrackerObject.checkLocationUpdateSharing();
                    }
                } else if (resultCode == RESULT_OK) {
                    LocationTrackerObject.startLocationUpdates();
                }
                break;
        }
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void ShowCheckOutConfirmationDialog() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(MenuActivity.this);
        alertDialogBuilder.setMessage("On submit you are unable to do any transaction for today.\nLike to continue ?")
                .setCancelable(false)
                .setPositiveButton("Yes",
                        (dialog, id) -> {
                            LocationTrackerObject = new LocationTracker(mContext, "CheckoutForDay");
                            LocationTrackerObject.startLocationUpdates();
                            final LocationManager manager = (LocationManager) mContext.getSystemService(Context.LOCATION_SERVICE);
                            if (manager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
                                if (!currentLat.isEmpty()) {
                                    if (Constants.isDeveloperOn && !BuildConfig.DEBUG) {
                                        Utils.showToast(mContext, "Please Disable Developer mode");
                                        startActivity(new Intent(Settings.ACTION_APPLICATION_DEVELOPMENT_SETTINGS));
                                        finish();
                                    } else {
                                        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                                            ShowSaudaCheckOutDialog();
                                        } else {
                                            String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                            if (!Constants.menuDetailsObj.getcheckout_journey_info().equalsIgnoreCase("null") &&
                                                    !Constants.menuDetailsObj.getcheckout_journey_info().trim().equalsIgnoreCase("") &&
                                                    Constants.menuDetailsObj.getcheckout_journey_info().contains("#")) {
                                                try {
                                                    if (Constants.menuDetailsObj.getcheckout_journey_info().contains("#")) {
                                                        if (PreferenceData.getjourneyInfoOwnOrPublicVehicle(mContext).equalsIgnoreCase("own vehicle")) {
                                                            showJourneyList(timeStamp, "Checkout Journey Info", Constants.menuDetailsObj.getcheckout_journey_info());
                                                        } else {
//                                                            ShowCheckOutDialog(timeStamp);
                                                            new TRANS_count_AsyncTask(mContext, "0", timeStamp).execute();
                                                        }
                                                    } else {
                                                        showJourneyList(timeStamp, "Checkout Journey Info", Constants.menuDetailsObj.getcheckout_journey_info());
                                                    }
                                                } catch (Exception e) {
                                                    showJourneyList(timeStamp, "Checkout Journey Info", Constants.menuDetailsObj.getcheckout_journey_info());
                                                }
                                            } else {
//                                                ShowCheckOutDialog(timeStamp);
                                                new TRANS_count_AsyncTask(mContext, "0", timeStamp).execute();
                                            }
                                        }
                                    }
                                } else {
                                    Utils.showToast(MenuActivity.this, "Could not get yor location try again");
                                }
                            } else {
                                Utils.showToast(MenuActivity.this, "Please Enable GPS");
                            }
                        })
                .setNegativeButton("No", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSaudaCheckOutDialog() {
        final Dialog mSaudaCheckOutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        mSaudaCheckOutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mSaudaCheckOutDialog.setContentView(R.layout.activity_sauda_checkout);
        mSaudaCheckOutDialog.setCancelable(false);
        TextView title = mSaudaCheckOutDialog.findViewById(R.id.title);
        title.setText("Hope you had a Productive day in Field.");
        TextView call = mSaudaCheckOutDialog.findViewById(R.id.textView1);
        call.setText("0.");
        TextView order = mSaudaCheckOutDialog.findViewById(R.id.textView2);
        order.setText("0");
        TextView noorder = mSaudaCheckOutDialog.findViewById(R.id.textView3);
        noorder.setText("0");
        String timeStamp = dateString;
        ReportSummery report = mAceDnsTransactionDatabase.GetSaudaReportSummery(timeStamp);
        noorder.setText(":  " + report.getNoOrdrRcvd());
        order.setText(":  " + report.getNoNoAct());
        call.setText(":  " + report.getNoCollcRcvd());
        Button submit = mSaudaCheckOutDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(arg0 -> {
            mSaudaCheckOutDialog.cancel();
            String libraryStatus = Utils.checkLibraryConditions(MenuActivity.this);
            if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                PendingingDataUpload("checkoutpending");
            } else {
                Utils.showToast(MenuActivity.this, libraryStatus + "\nPlease Synchronize Data.");
            }
        });
        mSaudaCheckOutDialog.show();
    }

    // Show count when check out
    private void progressDialogOpen() {
        runOnUiThread(() -> {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setMessage("Loading please wait...");
            progressDialog.setCancelable(false);
            progressDialog.show();
        });
    }

    private void progressDialogClose() {
        runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_count_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String conte1;
        String timeStamp;

        public TRANS_count_AsyncTask(Context context, String t, String timeStamp) {
            this.mContext = context;
            this.conte1 = t;
            this.timeStamp = timeStamp;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.khojCount + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    String a = HttpCalling.httpGetCallWithTextResponse(url).trim();
                    JSONObject obj = new JSONObject(a);
                    if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                        POST_result = obj.getInt("count_visit") + "";
                    } else {
                        POST_result = "0";
                    }
                } catch (Exception e) {
                    POST_result = "0";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                progressDialogClose();
                new TRANS_newSiteCount_AsyncTask(mContext, String.valueOf(Integer.parseInt(result) + Integer.parseInt(conte1)), timeStamp).execute();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_newSiteCount_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String conte1;
        String timeStamp;

        public TRANS_newSiteCount_AsyncTask(Context context, String t, String timeStamp) {
            this.mContext = context;
            this.conte1 = t;
            this.timeStamp = timeStamp;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl + "misreport/api_get_count_new_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    String a = HttpCalling.httpGetCallWithTextResponse(url).trim();
                    JSONObject obj = new JSONObject(a);
                    if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                        POST_result = obj.getInt("count_visit") + "";
                    } else {
                        POST_result = "0";
                    }
                } catch (Exception e) {
                    POST_result = "0";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                countOfNewPoint = String.valueOf(Integer.parseInt(result) + Integer.parseInt(conte1));
                ShowCheckOutDialog(timeStamp);
                progressDialogClose();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowCheckOutDialog(String timeStamp) {
        final Dialog checkoutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.checkout_dialog);
        checkoutDialog.setCancelable(false);
        LinearLayout layoutOrder = checkoutDialog.findViewById(R.id.linearLayoutOrder);
        LinearLayout layoutCollection = checkoutDialog.findViewById(R.id.linearLayoutCollection);
        LinearLayout layoutProspect = checkoutDialog.findViewById(linearLayoutBusinessProspect);
        LinearLayout layoutSurvey = checkoutDialog.findViewById(R.id.linearLayoutSurvey);
        LinearLayout linearLayoutMFS = checkoutDialog.findViewById(R.id.linearLayoutMFS);
        LinearLayout layoutStockAudit = checkoutDialog.findViewById(R.id.linearLayoutStockAudit);
        if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes")) {
            layoutOrder.setVisibility(VISIBLE);
        } else {
            layoutOrder.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes")) {
            layoutStockAudit.setVisibility(VISIBLE);
        } else {
            layoutStockAudit.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes")) {
            layoutCollection.setVisibility(VISIBLE);
        } else {
            layoutCollection.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes")) {
            layoutProspect.setVisibility(VISIBLE);
        } else {
            layoutProspect.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes")) {
            layoutSurvey.setVisibility(VISIBLE);
        } else {
            layoutSurvey.setVisibility(GONE);
        }
        if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
            linearLayoutMFS.setVisibility(VISIBLE);
        } else {
            linearLayoutMFS.setVisibility(GONE);
        }
        TextView title = checkoutDialog.findViewById(R.id.title);
        title.setText("Hope you had a productive day in field.");
        TextView call = checkoutDialog.findViewById(R.id.textView1);
        call.setText("0");
        TextView order = checkoutDialog.findViewById(R.id.textView2);
        order.setText("0");
        TextView stockAudit = checkoutDialog.findViewById(R.id.textViewStockAudit);
        stockAudit.setText("0");
        TextView collc = checkoutDialog.findViewById(R.id.textView3);
        collc.setText("0");
        TextView pros = checkoutDialog.findViewById(R.id.textView4);
        pros.setText("0");
        TextView survey = checkoutDialog.findViewById(R.id.textViewSurvey);
        survey.setText("0");
        TextView textViewMFS = checkoutDialog.findViewById(R.id.textViewMFS);
        textViewMFS.setText("0");
        ReportSummery reportObj = mAceDnsTransactionDatabase.getReportSummery(dateString);
        int callCount = (int) (Double.parseDouble(reportObj.getNoOrdrRcvd()) + Double.parseDouble(reportObj.getNoCollcRcvd()) + Double.parseDouble(reportObj.getNoNoAct())
                + Double.parseDouble(reportObj.getNoNewCustVisitd()));
        order.setText(reportObj.getNoOrdrRcvd());
        collc.setText(reportObj.getNoCollcRcvd());
        pros.setText(reportObj.getNoNewCustVisitd());
        survey.setText(String.valueOf(Integer.parseInt(reportObj.getNoofsurvey()) + Integer.parseInt(countOfNewPoint)));
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes")) {
            stockAudit.setText(reportObj.getNoofStockAudit());
            callCount = callCount + (int) (Double.parseDouble(reportObj.getNoofStockAudit()));
        }
        if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
            textViewMFS.setText(reportObj.getNoofMFS());
            callCount = callCount + (int) (Double.parseDouble(reportObj.getNoofMFS()));
        }
        call.setText(String.valueOf(callCount));
        Button submit = checkoutDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(arg0 -> {
            checkoutDialog.cancel();
            String libraryStatus = Utils.checkLibraryConditions(MenuActivity.this);
            if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                mAceDnsTransactionDatabase.insertToLocationTable("CH", timeStamp);
                mAceDnsTransactionDatabase.insertToAttendanceTable("CH", timeStamp);
                PendingingDataUpload("checkoutpending");
            } else {
                Utils.showToast(MenuActivity.this, libraryStatus + "\nPlease Synchronize Data.");
            }
        });
        checkoutDialog.show();
    }
    // Show count when check out

    @SuppressLint("SetTextI18n")
    public void ShowCompletedTransactionDetailsDialog() {
        final Dialog checkoutDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.current_transaction_details_dialog);
        checkoutDialog.setCancelable(false);
        TextView tvDurationOfCall = checkoutDialog.findViewById(R.id.tvDurationOfCall);
        LinearLayout linearLayoutCalls = checkoutDialog.findViewById(R.id.linearLayoutCalls);
        LinearLayout layoutOrder = checkoutDialog.findViewById(R.id.linearLayoutOrder);
        LinearLayout layoutCollection = checkoutDialog.findViewById(R.id.linearLayoutCollection);
        LinearLayout layoutProspect = checkoutDialog.findViewById(linearLayoutBusinessProspect);
        LinearLayout layoutSurvey = checkoutDialog.findViewById(R.id.linearLayoutSurvey);
        LinearLayout linearLayoutMFS = checkoutDialog.findViewById(R.id.linearLayoutMFS);
        LinearLayout layoutStockAudit = checkoutDialog.findViewById(R.id.linearLayoutStockAudit);
        linearLayoutCalls.setVisibility(VISIBLE);
        ArrayList<String> orderDetails = mAceDnsDatabase.getTransactionDetailsForOrder(Constants.CurrentOrderTransactionId);
        layoutOrder.setVisibility(VISIBLE);
        layoutStockAudit.setVisibility(VISIBLE);
        layoutCollection.setVisibility(VISIBLE);
        layoutProspect.setVisibility(View.GONE);
        layoutSurvey.setVisibility(View.GONE);
        linearLayoutMFS.setVisibility(View.GONE);
        TextView tvTransactionDetails1 = checkoutDialog.findViewById(R.id.tvTransactionDetails1);
        tvTransactionDetails1.setText(Html.fromHtml("No. Of lines: <font color='#F58322'>" + orderDetails.get(1) + "</font>"));
        tvDurationOfCall.setText(Html.fromHtml("Duration of Call: <font color='#F58322'>" + orderDetails.get(0) + "</font>"));
        TextView textView13 = checkoutDialog.findViewById(R.id.textView13);
        textView13.setText(Html.fromHtml("Volume (Total Qty): <font color='#F58322'>" + orderDetails.get(2) + "</font>"));
        TextView textView10 = checkoutDialog.findViewById(R.id.textView10);
        textView10.setText(Html.fromHtml("Value (Total): <font color='#F58322'>" + currency + " " + orderDetails.get(3) + "</font>"));
        TextView title = checkoutDialog.findViewById(R.id.title);
        title.setText("Transaction successfully posted.");
        Button submit = checkoutDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(arg0 -> {
            checkoutDialog.cancel();
            getIntent().removeExtra("transactionType");
        });
        checkoutDialog.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showAgeingPeriodDialog() {
        ArrayList<CustomerDetails> customerList = mAceDnsDatabase.getCustomerListWithOS();
        if (customerList != null && !customerList.isEmpty()) {
            final Dialog ageingPeriodDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            ageingPeriodDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            ageingPeriodDialog.setContentView(R.layout.ageing_days_dialog);
            ageingPeriodDialog.setCancelable(false);
            final EditText date1 = ageingPeriodDialog.findViewById(R.id.ed_period1);
            final EditText date2 = ageingPeriodDialog.findViewById(R.id.ed_period2);
            final EditText date3 = ageingPeriodDialog.findViewById(R.id.ed_period3);
            Button date = ageingPeriodDialog.findViewById(R.id.ed_date);
            date.setText(new SimpleDateFormat("dd-MM-yyyy").format(new Date()));
            Button submit = ageingPeriodDialog.findViewById(R.id.btn_submit);
            submit.setOnClickListener(arg0 -> {
                String strDate1 = date1.getText().toString();
                String strDate2 = date2.getText().toString();
                String strDate3 = date3.getText().toString();
                if (!strDate1.isEmpty() && !strDate2.isEmpty() && !strDate3.isEmpty()) {
                    ageingPeriodDialog.cancel();
                    prepareAgeingData(strDate1, strDate2, strDate3);
                } else {
                    Utils.showToast(MenuActivity.this, "Provide all details");
                }
            });
            Button cancel = ageingPeriodDialog.findViewById(R.id.btn_cancel);
            cancel.setOnClickListener(arg0 -> ageingPeriodDialog.cancel());
            ageingPeriodDialog.show();
        } else {
            Utils.showToast(MenuActivity.this, "No Outstanding Found");
        }
    }

    public void showAgeingDetailsDialog(String period1, String period2, String period3) {
        MenuAgeingExpandableAdapter adapter = new MenuAgeingExpandableAdapter(outstandingParentItems, ageingChildItems, period1, period2, period3);
        adapter.setInflater((LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE), this);
        final Dialog ageingDetailsDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        ageingDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        ageingDetailsDialog.setContentView(R.layout.menu_ageing_dialog);
        ageingDetailsDialog.setCancelable(false);
        ExpandableListView expandableList = ageingDetailsDialog.findViewById(R.id.list);
        expandableList.setAdapter(adapter);
        for (int i = 0; i < adapter.getGroupCount(); i++)
            expandableList.expandGroup(i);
        Button cancel = ageingDetailsDialog.findViewById(R.id.btn_cancel);
        cancel.setOnClickListener(arg0 -> ageingDetailsDialog.cancel());
        ageingDetailsDialog.show();
    }

    public void ShowPriceSelectionDialog() {
        final Dialog priceSelectionDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        priceSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        priceSelectionDialog.setContentView(R.layout.radio_mrp_selection_dialog);
        priceSelectionDialog.setCancelable(false);
        RadioGroup mRadioGroupSBT = priceSelectionDialog.findViewById(R.id.radioSelectPrice);
        mRadioGroupSBT.setOnCheckedChangeListener((group, checkedId) -> {
            final RadioButton radioSelection = priceSelectionDialog.findViewById(checkedId);
            if (radioSelection.getText().toString().equalsIgnoreCase("Primary")) {
                priceSelectionDialog.cancel();
                ShowSaudaDepoNameDialog();
            } else {
                priceSelectionDialog.cancel();
                ShowOrderDepoNameDialog();
            }
        });
        priceSelectionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowOrderDepoNameDialog() {
        final ArrayList<BranchMasterDetails> mBranchMasterDetailsList = mAceDnsDatabase.GETDEPOList("order");
        if (mBranchMasterDetailsList.size() > 1) {
            final Dialog saudsRDSDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            saudsRDSDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            saudsRDSDialog.setContentView(R.layout.select_from_list);
            saudsRDSDialog.setCancelable(false);
            Button btn_cncl = saudsRDSDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setOnClickListener(view -> saudsRDSDialog.dismiss());
            TextView title = saudsRDSDialog.findViewById(R.id.title);
            title.setText("Please select a Depot");
            ListView dialogList = saudsRDSDialog.findViewById(R.id.list);
            BranchAdapter branchadapter = new BranchAdapter(MenuActivity.this, R.layout.route_list_child, mBranchMasterDetailsList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                saudsRDSDialog.cancel();
                Constants.selectedBranch = mBranchMasterDetailsList.get(arg2);
                ShowOrderMRPDialog();
            });
            saudsRDSDialog.show();
        } else {
            if (mBranchMasterDetailsList.size() == 1) {
                Constants.selectedBranch = mBranchMasterDetailsList.get(0);
                ShowOrderMRPDialog();
            } else {
                Toast.makeText(MenuActivity.this, "No Depot found.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowSaudaDepoNameDialog() {
        final ArrayList<BranchMasterDetails> mBranchMasterDetailsList = mAceDnsDatabase.GETDEPOList("sauda");
        if (mBranchMasterDetailsList.size() > 1) {
            final Dialog saudsRDSDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            saudsRDSDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            saudsRDSDialog.setContentView(R.layout.select_from_list);
            saudsRDSDialog.setCancelable(false);
            Button btn_cncl = saudsRDSDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setOnClickListener(view -> saudsRDSDialog.dismiss());
            TextView title = saudsRDSDialog.findViewById(R.id.title);
            title.setText("Please select a Depot/Plant");
            ListView dialogList = saudsRDSDialog.findViewById(R.id.list);
            BranchAdapter branchadapter = new BranchAdapter(MenuActivity.this, R.layout.route_list_child, mBranchMasterDetailsList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                saudsRDSDialog.cancel();
                Constants.selectedBranch = mBranchMasterDetailsList.get(arg2);
                ShowSaudaMRPDialog();
            });
            saudsRDSDialog.show();
        } else {
            if (mBranchMasterDetailsList.size() == 1) {
                Constants.selectedBranch = mBranchMasterDetailsList.get(0);
                ShowSaudaMRPDialog();
            } else {
                Toast.makeText(MenuActivity.this, "No Depot/Plant found.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowStateNameDialog() {
        final ArrayList<String> stateList = mAceDnsDatabase.GETStateListFromCustomer();
        if (stateList.size() > 1) {
            final Dialog saudsRDSDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            saudsRDSDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            saudsRDSDialog.setContentView(R.layout.select_from_list);
            saudsRDSDialog.setCancelable(false);
            Button btn_cncl = saudsRDSDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setOnClickListener(view -> saudsRDSDialog.dismiss());
            TextView title = saudsRDSDialog.findViewById(R.id.title);
            title.setText("Please select a state");
            ListView dialogList = saudsRDSDialog.findViewById(R.id.list);
            StateAdapter branchadapter = new StateAdapter(MenuActivity.this, R.layout.route_list_child, stateList, true);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                saudsRDSDialog.cancel();
                selectedState = stateList.get(arg2).split(";")[0];
                ShowSaudaDepoNameDialog();
            });
            saudsRDSDialog.show();
        } else {
            if (stateList.size() == 1) {
                selectedState = stateList.get(0).split(";")[0];
                ShowSaudaDepoNameDialog();
            } else {
                Toast.makeText(MenuActivity.this, "No state found.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowOrderMRPDialog() {
        ArrayList<MenuClStkMrpDetails> mrpList;
        mrpList = mAceDnsDatabase.getMenuMrpList(Constants.selectedBranch.getBranchCode(), "order");
        if (!mrpList.isEmpty()) {
            final Dialog mDialogMRP = new Dialog(MenuActivity.this, R.style.PauseDialog);
            mDialogMRP.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogMRP.setContentView(R.layout.search_lable_layout);
            mDialogMRP.setCancelable(false);
            TextView title = mDialogMRP.findViewById(R.id.title);
            title.setText("Secondary Price List for " + Constants.selectedBranch.getBranchName());
            ListView dialogList = mDialogMRP.findViewById(R.id.list);
            final SaudaMRPAdapter adapter = new SaudaMRPAdapter(MenuActivity.this, R.layout.menu_stk_mrp_child, mrpList, true);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((parent, view, position, id) -> {
                mDialogMRP.cancel();
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            });

            EditText searchText = mDialogMRP.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            Button back = mDialogMRP.findViewById(R.id.back);
            back.setVisibility(VISIBLE);
            back.setOnClickListener(arg0 -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogMRP.cancel();
            });
            Button cancel = mDialogMRP.findViewById(R.id.btn_ok);
            cancel.setVisibility(GONE);
            mDialogMRP.show();
        } else {
            Toast.makeText(MenuActivity.this, "No product found", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void showExForTypeDialog() {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.CustomMaterialDialogTheme);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.exfor_selection_dialog);
        TextView txtMsg = payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Price for:");
        RadioGroup payTypeOption = payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
            int radioButtonID = group.getCheckedRadioButtonId();
            View radioButton = group.findViewById(radioButtonID);
            int selectedRadio = group.indexOfChild(radioButton);
            if (selectedRadio == 0) {
                mSaudaType = "ex";
                ShowMRPDialogAslExFor();
            } else {
                mSaudaType = "for";
                showRouteSelectionDialog();
            }
        });
        Button cancel = payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(VISIBLE);
        cancel.setOnClickListener(arg0 -> payTypeDialog.cancel());
        payTypeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSaudaMRPDialog() {
        boolean isIncotermsVertical = Constants.saudaFormDetailsObj.getincoterms_vertical().contains(mAceDnsDatabase.getVerticalValueOfLoggedInEmployee());
        ArrayList<MenuClStkMrpDetails> mrpList;
        if (!isIncotermsVertical) {
            mrpList = mAceDnsDatabase.getMenuMrpList(Constants.selectedBranch.getBranchCode(), "sauda");
        } else {
            mrpList = mAceDnsDatabase.getMenuMrpListForIncoTerms(Constants.selectedBranch.getBranchCode());
        }
        if (!mrpList.isEmpty()) {
            final Dialog mDialogMRP = new Dialog(MenuActivity.this, R.style.PauseDialog);
            mDialogMRP.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogMRP.setContentView(R.layout.search_lable_layout);
            mDialogMRP.setCancelable(false);
            TextView title = mDialogMRP.findViewById(R.id.title);
            title.setText("Primary Price List for " + Constants.selectedBranch.getBranchName());
            ListView dialogList = mDialogMRP.findViewById(R.id.list);
            if (isIncotermsVertical) {
                TextView textViewPrice = mDialogMRP.findViewById(R.id.textViewPrice);
                if (Constants.selectedBranch.getisPlant().equalsIgnoreCase("yes")) {
                    textViewPrice.setText("EX Plant");
                } else {
                    textViewPrice.setText("EX Depot");
                }
            }
            final SaudaMRPAdapter adapter = new SaudaMRPAdapter(MenuActivity.this, R.layout.menu_stk_mrp_child, mrpList, true, isIncotermsVertical);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((parent, view, position, id) -> getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN));
            EditText searchText = mDialogMRP.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            Button back = mDialogMRP.findViewById(R.id.back);
            back.setVisibility(VISIBLE);
            back.setOnClickListener(arg0 -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogMRP.cancel();
            });
            Button cancel = mDialogMRP.findViewById(R.id.btn_ok);
            cancel.setVisibility(GONE);
            mDialogMRP.show();
        } else {
            Toast.makeText(MenuActivity.this, "No product found", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowMRPDialog() {
        ArrayList<MenuClStkMrpDetails> mrpList;
        if (Constants.saudaFormDetailsObj.getSaudaDepotWise().equalsIgnoreCase("yes")) {
            mrpList = mAceDnsDatabase.getMenuMrpList(Constants.selectedBranch.getBranchCode());
        } else {
            mrpList = mAceDnsDatabase.getMenuMrpList("");
        }
        final MenuClStkMRPAdapter adapter = new MenuClStkMRPAdapter(MenuActivity.this, R.layout.menu_stk_mrp_child, mrpList, true);
        final Dialog mrpDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        mrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mrpDialog.setContentView(R.layout.select_with_search);
        mrpDialog.setCancelable(false);
        TextView title = mrpDialog.findViewById(R.id.title);
        title.setText("Product Price List");
        EditText searchText = mrpDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        ListView dialogList = mrpDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapter);
        Button cancel = mrpDialog.findViewById(R.id.btn_ok);
        cancel.setOnClickListener(arg0 -> mrpDialog.cancel());
        mrpDialog.show();
    }

    public void ShowMRPDialogAslExFor() {
        ArrayList<String> listDataHeaderTemp;
        listDataHeader = new ArrayList<>();
        listDataHeaderTemporary = new ArrayList<>();
        listChildData = new HashMap<>();
        listChildDataTemporary = new HashMap<>();
        listDataHeaderTemp = mAceDnsDatabase.getMenuMrpSubgroupListForAsl();
        for (int i = 0; i < listDataHeaderTemp.size(); i++) {
            String[] groupNameCode = listDataHeaderTemp.get(i).split("#");
            listDataHeaderTemporary.add(listDataHeaderTemp.get(i));
            ArrayList<MenuClStkMrpDetails> getMenuMrpListForAsl = new ArrayList<>();
            if (mSaudaType.equalsIgnoreCase("ex")) {
                getMenuMrpListForAsl = mAceDnsDatabase.getMenuMrpListForAsl(groupNameCode[0], groupNameCode[1]);
            } else {
                ArrayList<MenuClStkMrpDetails> getMenuMrpListForAslTemp = mAceDnsDatabase.getMenuMrpListForAsl(groupNameCode[0], groupNameCode[1]);
                ArrayList<commonDatabaseHelper> freightRateList = mAceDnsDatabase.getFreightRateByBranchRoute(mRouteCode, Constants.selectedBranch.getBranchCode());
                for (int x = 0; x < freightRateList.size(); x++) {
                    commonDatabaseHelper currentItem = freightRateList.get(x);
                    if (Utils.isNumeric(currentItem.getItem0()) && Double.parseDouble(currentItem.getItem0()) > 0) {
                        double freight = Double.parseDouble(currentItem.getItem0());
                        double finalFreightRate = 0.0;
                        String capacity = currentItem.getItem1();
                        String transportMode = currentItem.getItem2();
                        for (int y = 0; y < getMenuMrpListForAslTemp.size(); y++) {
                            MenuClStkMrpDetails currentProductItem = getMenuMrpListForAslTemp.get(y);
                            if (transportMode.equalsIgnoreCase("tanker")) {
                                if (currentProductItem.getuom1().equalsIgnoreCase("loose")) {
                                    String trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCodeBargain(currentProductItem.getdnsProdCOde(), capacity, transportMode, false);
                                    if (Utils.isNumeric(trackLoadQuantity) && Double.parseDouble(trackLoadQuantity) > 0) {
                                        finalFreightRate = freight / Double.parseDouble(trackLoadQuantity);
                                        finalFreightRate = Math.ceil(finalFreightRate);
                                        Double mrp = Double.parseDouble(currentProductItem.getValue()) + finalFreightRate;
                                        currentProductItem.setValue(mrp + "");
                                        currentProductItem.setcapacity(capacity);
                                        getMenuMrpListForAsl.add(currentProductItem);
                                    }
                                }
                            } else {
                                if (!currentProductItem.getuom1().equalsIgnoreCase("loose")) {
                                    String trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCodeBargain(currentProductItem.getdnsProdCOde(), capacity, transportMode, false);
                                    if (Utils.isNumeric(trackLoadQuantity) && Double.parseDouble(trackLoadQuantity) > 0) {
                                        finalFreightRate = freight / Double.parseDouble(trackLoadQuantity);
                                        finalFreightRate = Math.ceil(finalFreightRate);
                                        Double mrp = Double.parseDouble(currentProductItem.getValue()) + finalFreightRate;
                                        currentProductItem.setValue(mrp + "");
                                        currentProductItem.setcapacity(capacity);
                                        getMenuMrpListForAsl.add(currentProductItem);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            listChildDataTemporary.put(listDataHeaderTemp.get(i), getMenuMrpListForAsl);
        }
        for (int a = 0; a < listDataHeaderTemporary.size(); a++) {
            String key = listDataHeaderTemporary.get(a);
            List<MenuClStkMrpDetails> currentItemList = listChildDataTemporary.get(key);
            if (currentItemList != null && !currentItemList.isEmpty()) {
                listDataHeader.add(key);
                listChildData.put(key, currentItemList);
            }
        }
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.AppBaseTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_mrp_report_asl);
        mDetailsDialog.setCancelable(true);
        TextView textviewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        FrameLayout fl = mDetailsDialog.findViewById(R.id.colCapcity);
        if (mSaudaType.equalsIgnoreCase("ex")) {
            fl.setVisibility(GONE);
        }
        textviewTitleName.setText(HtmlCompat.fromHtml("Price List: <font color='#D7B56D'>" + mSaudaType.toUpperCase() + "</font>", HtmlCompat.FROM_HTML_MODE_LEGACY));
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        ExpandableListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        final ExpandableListAdapterAslMrpReport orderreportAdapter = new ExpandableListAdapterAslMrpReport(mContext, listDataHeader, listChildData, mSaudaType);
        dialogList.setAdapter(orderreportAdapter);
        for (int i = 0; i < orderreportAdapter.getGroupCount(); i++)
            dialogList.expandGroup(i);
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    private void showRouteSelectionDialog() {
        mRouteDetailsList = mAceDnsDatabase.getRouteListAslForMrp();
        if (!mRouteDetailsList.isEmpty()) {
            final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.MyMaterialTheme);
            incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            incotermsSelectionDialog.setContentView(R.layout.material_item_selection_list);
            incotermsSelectionDialog.setCancelable(false);
            Button btn_cncl = incotermsSelectionDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setText("Cancel");
            btn_cncl.setOnClickListener(arg0 -> incotermsSelectionDialog.dismiss());
            ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
            RouteAdapter adapter = new RouteAdapter(MenuActivity.this, R.layout.route_list_child_material, mRouteDetailsList);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mRouteName = mRouteDetailsList.get(arg2).getRouteName();
                mRouteCode = mRouteDetailsList.get(arg2).getRouteCode();
                incotermsSelectionDialog.cancel();
                showBranchSelectionDialog();
            });
            incotermsSelectionDialog.show();
        } else {
            Utils.showToast(mContext, "No route found. Please Synchronize Data.");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showBranchSelectionDialog() {
        final ArrayList<BranchMasterDetails> mBranchMasterDetailsList = mAceDnsDatabase.GETDEPOListAsl();
        if (mBranchMasterDetailsList.size() > 1) {
            final Dialog saudsRDSDialog = new Dialog(MenuActivity.this, R.style.MyMaterialTheme);
            saudsRDSDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            saudsRDSDialog.setContentView(R.layout.material_item_selection_list);
            saudsRDSDialog.setCancelable(false);
            Button btn_cncl = saudsRDSDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setText("Cancel");
            btn_cncl.setOnClickListener(arg0 -> saudsRDSDialog.dismiss());
            TextView title = saudsRDSDialog.findViewById(R.id.title);
            title.setText("Please select a Branch");
            ListView dialogList = saudsRDSDialog.findViewById(R.id.list);
            BranchAdapter branchadapter = new BranchAdapter(MenuActivity.this, R.layout.route_list_child_material, mBranchMasterDetailsList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                Constants.selectedBranch = mBranchMasterDetailsList.get(arg2);
                saudsRDSDialog.cancel();
                ShowMRPDialogAslExFor();
            });
            saudsRDSDialog.show();
        } else {
            if (mBranchMasterDetailsList.size() == 1) {
                Constants.selectedBranch = mBranchMasterDetailsList.get(0);
                ShowMRPDialogAslExFor();
            } else {
                Toast.makeText(MenuActivity.this, "No Depot found.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowClosingStockDialog() {
        clStkList = mAceDnsDatabase.getMenuClStkList();
        final MenuClStkMRPAdapter adapter = new MenuClStkMRPAdapter(MenuActivity.this, R.layout.menu_stk_mrp_child, clStkList, false);
        final Dialog stkDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        stkDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        stkDialog.setContentView(R.layout.select_with_search);
        stkDialog.setCancelable(false);
        TextView title = stkDialog.findViewById(R.id.title);
        title.setText("Closing Stock List");
        EditText searchText = stkDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        ListView dialogList = stkDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapter);
        Button cancel = stkDialog.findViewById(R.id.btn_ok);
        cancel.setOnClickListener(arg0 -> stkDialog.cancel());
        stkDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void chooseCustomerDialog() {
        ArrayList<CustomerDetails> customerList = mAceDnsDatabase.getCustomerListWithOS();
        if (customerList != null && !customerList.isEmpty()) {
            final CustomerAdapter adapterCust = new CustomerAdapter(MenuActivity.this, R.layout.multiple_cust_child, customerList);
            final Dialog custDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            custDialog.setContentView(R.layout.select_multiple_from_list);
            custDialog.setCancelable(false);
            TextView title = custDialog.findViewById(R.id.title);
            title.setText("Please select party");
            final ListView dialogList = custDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            dialogList.setAdapter(adapterCust);
            RelativeLayout chkAllLayout = custDialog.findViewById(R.id.select_all_layout);
            chkAllLayout.setVisibility(VISIBLE);
            final CheckBox chkSelectAll = custDialog.findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(v -> {
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i <= dialogList.getCount(); i++) {
                        dialogList.setItemChecked(i, true);
                    }
                } else {
                    for (int i = 0; i <= dialogList.getCount(); i++) {
                        dialogList.setItemChecked(i, false);
                    }
                }
            });
            Button submit = custDialog.findViewById(R.id.button1);
            submit.setOnClickListener(arg0 -> {
                String selectedCodes = "";
                String selectedNames = "";
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i < adapterCust.getCount(); i++) {
                        CustomerDetails detailsObj = adapterCust.getItem(i);
                        assert detailsObj != null;
                        String selectedName = detailsObj.getCustomerName();
                        String selectedCode = detailsObj.getCustomerCode();
                        selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                        selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                    }
                    selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                    prepareOutstandingData(selectedCodes);
                    custDialog.cancel();
                } else {
                    final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                CustomerDetails detailsObj = adapterCust.getItem(position);
                                assert detailsObj != null;
                                String selectedName = detailsObj.getCustomerName();
                                String selectedCode = detailsObj.getCustomerCode();
                                selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                                selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                            }
                        }
                        selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                        prepareOutstandingData(selectedCodes);
                        custDialog.cancel();
                    } else {
                        Utils.showToast(MenuActivity.this, "Please select an option");
                    }
                }
            });
            EditText searchText = custDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            custDialog.show();
        } else {
            Utils.showToast(MenuActivity.this, "No Outstanding Found");
        }
    }

    @SuppressLint("SetTextI18n")
    public void chooseCustomerDialogStar(ArrayList<CustomerDetails> _customerList) {
        if (_customerList != null && !_customerList.isEmpty()) {
            final CustomerAdapter adapterCust = new CustomerAdapter(MenuActivity.this, R.layout.multiple_cust_child, _customerList);
            final Dialog custDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
            custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            custDialog.setContentView(R.layout.select_multiple_from_list);
            custDialog.setCancelable(false);
            TextView title = custDialog.findViewById(R.id.title);
            title.setText("Please select party");
            final ListView dialogList = custDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_SINGLE);
            dialogList.setAdapter(adapterCust);
            RelativeLayout chkAllLayout = custDialog.findViewById(R.id.select_all_layout);
            chkAllLayout.setVisibility(View.GONE);
            final CheckBox chkSelectAll = custDialog.findViewById(R.id.chk_all);
            chkSelectAll.setVisibility(GONE);

            Button submit = custDialog.findViewById(R.id.button1);
            submit.setOnClickListener(arg0 -> {
                String selectedCodes = "";
                String selectedNames = "";
                String cdns = "";
                final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount == 1) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i)) {
                            CustomerDetails detailsObj = adapterCust.getItem(position);
                            assert detailsObj != null;
                            String selectedName = detailsObj.getCustomerName();
                            String selectedCode = detailsObj.getCustomerCode();
                            cdns = detailsObj.getDnsCustCode();
                            selectedCodes = selectedCode;
                            selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                        }
                    }
                    Intent intent = new Intent(mContext, StarOutStandingActivity.class);
                    intent.putExtra("cid", selectedCodes);
                    intent.putExtra("cdnsid", cdns);
                    startActivity(intent);
                    custDialog.cancel();
                } else {
                    custDialog.cancel();
                    Utils.showToast(MenuActivity.this, "Please select an option");
                }
            });
            EditText searchText = custDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            custDialog.show();
        } else {
            Utils.showToast(MenuActivity.this, "No Outstanding Found");
        }
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showCreateRouteDialog() {
        final Dialog instructionDialog = new Dialog(MenuActivity.this);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = instructionDialog.findViewById(R.id.title);
        title.setText("Provide the details of the new Route.");
        final EditText edInst = instructionDialog.findViewById(R.id.ed_input);
        final Button submit = instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String name;
            submit.setEnabled(false);
            name = edInst.getText().toString();
            if (!name.isEmpty()) {
                String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String selectRouteCode = "NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                RouteDetails routeObj = new RouteDetails();
                routeObj.setRouteCode(selectRouteCode);
                routeObj.setRouteName(name.toUpperCase());
                ArrayList<RouteDetails> routeList = new ArrayList<>();
                routeList.add(routeObj);
                mAceDnsDatabase.insertToRouteMaster(routeList);
                instructionDialog.cancel();
            } else {
                Utils.showToast(MenuActivity.this, "Name cannot be left blank");
            }
        });
        instructionDialog.show();
    }

    public void ShowPendingContractDialog(String customername) {
        final PendingContractAdapter adapterCust = new PendingContractAdapter(MenuActivity.this, R.layout.pending_contract_child, mPendingContractList);
        final Dialog mDialogPendingContractProduct = new Dialog(MenuActivity.this, R.style.PauseDialog);
        mDialogPendingContractProduct.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogPendingContractProduct.setContentView(R.layout.select_from_list);
        mDialogPendingContractProduct.setCancelable(false);
        TextView title = mDialogPendingContractProduct.findViewById(R.id.title);
        title.setText(customername);
        ListView dialogList = mDialogPendingContractProduct.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> mDialogPendingContractProduct.cancel());
        Button addCustomer = mDialogPendingContractProduct.findViewById(R.id.btn_cncl);
        addCustomer.setVisibility(GONE);
        mDialogPendingContractProduct.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(MenuActivity.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            mRouteName = routeList.get(arg2).getRouteName();
            mRouteCode = routeList.get(arg2).getRouteCode();
            FlowofCheckINAndOut(2, mRouteCode);
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialogToCheckIn() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(true);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
            title.setText("Please select a Doctor to check in");
        } else {
            title.setText("Please select a customer to check in");
        }
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setEmptyView(mDialogCustomer.findViewById(R.id.empty_text_view));
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            LocationTrackerObject = new LocationTracker(mContext, "check in");
            Constants.selectedCheckINCustomer = adapterCust.getItem(arg2);
            Log.d("TAG", "_DOWNLOAD_ ShowCustomerListDialogToCheckIn: " + Constants.selectedCheckINCustomer.getCustomerType());
            visitSequenceCheckIn = false;
            checkInProcess(visitSequenceCheckIn);
        });
        Button back = mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(VISIBLE);
        back.setOnClickListener(view -> mDialogCustomer.cancel());
        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        if (Constants.orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer")) {
            addCustomer.setText("Add New Customer and Check In");
            addCustomer.setOnClickListener(view -> {
                mDialogCustomer.cancel();
                Constants.isOrederToNewCustomer = true;
                isCheckInToNewCustomer = true;
                Intent intent = new Intent(mContext, AddNewCustActivity.class);
                intent.putExtra("ROUTENAME", mRouteName);
                intent.putExtra("ROUTECODE", mRouteCode);
                startActivity(intent);
            });
        } else {
            addCustomer.setVisibility(GONE);
        }
        mDialogCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowPurposeOfVisitListDialogToCheckIn() {
        ArrayList<PurposeVisitDetails> routeItemsList = new ArrayList<>();
        if (Constants.selectedCheckINCustomer.getCustomerType().equalsIgnoreCase("Dealer")) {
            PurposeVisitDetails rd = new PurposeVisitDetails("Market Storming Activity");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Products Stock Audit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor Products Stock Audit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("POP distribution");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Scheme Communication");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Order Generation follow-up");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Saathi App Training");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Routine Visit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor Schemes Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor's Price Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Payment follow-up");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Ledger Confirmation/Issues");
            routeItemsList.add(rd);
        } else if (Constants.selectedCheckINCustomer.getCustomerType().equalsIgnoreCase("Sub Dealer") || Constants.selectedCheckINCustomer.getCustomerType().equalsIgnoreCase("rssd")) {
            PurposeVisitDetails rd = new PurposeVisitDetails("Market Storming Activity");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Products Stock Audit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor Products Stock Audit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("POP distribution");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Scheme Communication");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Order Generation follow-up");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Star Saathi App Training");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Routine Visit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor Schemes Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor's Price Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Scheme Payment/Issues");
            routeItemsList.add(rd);
        } else {
            PurposeVisitDetails rd = new PurposeVisitDetails("Market Storming Activity");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor stock Audit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Occasional/festival gifts distribution");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Routine Visit");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor Schemes Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Competitor's Price Update");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Convert to Star Dealer");
            routeItemsList.add(rd);
            rd = new PurposeVisitDetails("Convert to Star Sub dealer");
            routeItemsList.add(rd);
        }
        PurposeVisitAdapter routeAdapterForAllROutes = new PurposeVisitAdapter(MenuActivity.this, R.layout.multiple_route_child, routeItemsList);

        final Dialog routeDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_multiple_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select Purpose of Visit(Single/Multiple)");
        LinearLayout ll = routeDialog.findViewById(R.id.llRemarks);
        EditText etRemarks = routeDialog.findViewById(R.id.etRemarks);
        ll.setVisibility(VISIBLE);
        ListView dialogList = routeDialog.findViewById(R.id.list);
        etRemarks.setHint("Other");
        dialogList.setAdapter(routeAdapterForAllROutes);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN));

        Button submit = routeDialog.findViewById(R.id.button1);
        ImageView image_cancel = routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(VISIBLE);
        image_cancel.setOnClickListener(v -> routeDialog.cancel());

        submit.setOnClickListener(v -> {
            final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
            int checkedItemsCount = checkedItems.size();
            String ss = "";
            if (checkedItemsCount > 0) {
                for (int i = 0; i < checkedItemsCount; ++i) {
                    int position = checkedItems.keyAt(i);
                    if (checkedItems.valueAt(i)) {
                        PurposeVisitDetails mRouteDetails = routeAdapterForAllROutes.getItem(position);
                        assert mRouteDetails != null;
                        ss = MessageFormat.format("{0}{1},", ss, mRouteDetails.getPurpose());
                    }
                }
                localStorage = new LocalStorage(mContext);
                ss = ss + etRemarks.getText().toString() + ",";
                localStorage.setPurpose_of_visit(ss);
                checkInProcessFlow1(visitSequenceCheckIn);
                routeDialog.cancel();
            } else if (!etRemarks.getText().toString().trim().isEmpty()) {
                localStorage = new LocalStorage(mContext);
                ss = etRemarks.getText().toString();
                localStorage.setPurpose_of_visit(ss);
                checkInProcessFlow1(visitSequenceCheckIn);
                routeDialog.cancel();
            } else {
                Utils.showToast(mContext, "Please select purpose of visit");
            }
        });
        final EditText autoCompleteTextView1 = routeDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(GONE);
        routeDialog.show();
    }

    public void loadMenuAgain() {
        mMenuList.clear();
        mMenuAdapter.clear();
        mMenuAdapter.notifyDataSetChanged();
//        userType=mNewDatabaseForSiteLead.getEmpDesignation(Constants.employeeDetailObject.getEmpCode());
//        menuUpdate();
        prepareFeatureList();
        initView();
        mGridViewMenu.setAdapter(mMenuAdapter);
    }

    public void show_Notification(View i) {
        showNotificationDialog();
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(Collections.unmodifiableList(tempProductList));
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductList.addAll(productMasterList);
    }

    public void reInitialiseProductGroupList() {
        tempProductGroupList.removeAll(Collections.unmodifiableList(tempProductGroupList));
        int size = tempProductGroupList.size();
        int size1 = productGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductGroupList.addAll(productGroupList);
    }

    public void reInitialiseProductSubGroupList() {
        tempProductSubGroupList.removeAll(Collections.unmodifiableList(tempProductSubGroupList));
        int size = tempProductSubGroupList.size();
        int size1 = productSubGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductSubGroupList.addAll(productSubGroupList);
    }

    public void reInitialiseProductBrandList() {
        tempProductBrandList.removeAll(Collections.unmodifiableList(tempProductBrandList));
        int size = tempProductBrandList.size();
        int size1 = productBrandList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductBrandList.addAll(productBrandList);
    }

    @SuppressLint("SetTextI18n")
    public void showGrpListDialog() {
        if (!productGroupList.isEmpty()) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            groupAdapter = new ProductGrpAdapter(mContext, R.layout.product_list_child, tempProductGroupList);
            grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_with_search);
            grpDialog.setTitle("Please select a product group");
            grpDialog.setCancelable(false);
            TextView title = grpDialog.findViewById(R.id.title);
            title.setText("Please select a product group");
            EditText searchText = grpDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductGroupList();
                    }
                    lastStr = str;
                    filterProductGroupArray(str.length(), str);
                    groupAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = grpDialog.findViewById(R.id.list);
            dialogList.setAdapter(groupAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                grpDialog.cancel();
                selectedGrp = tempProductGroupList.get(arg2);
                filterButtonList.get(0).setText(selectedGrp.getGroupName());
                switch (filterNo) {
                    case 2:
                        prepareOrderData(4, selectedGrp.getGroupCode());
                        break;
                    case 3:
                        prepareOrderData(2, selectedGrp.getGroupCode());
                        break;
                    case 4:
                        prepareOrderData(2, selectedGrp.getGroupCode());
                        break;
                }
            });
            Button cancel = grpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> grpDialog.cancel());
            grpDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left.Please submit order.", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void showSubGrpListDialog() {
        if (!productSubGroupList.isEmpty()) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(mContext, R.layout.product_list_child, tempProductSubGroupList);
            subGrpDialog = new Dialog(mContext, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_with_search);
            subGrpDialog.setCancelable(false);
            TextView title = subGrpDialog.findViewById(R.id.title);
            title.setText("Please select a product sub group");
            EditText searchText = subGrpDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductSubGroupList();
                    }
                    lastStr = str;
                    filterProductSubGroupArray(str.length(), str);
                    subGroupAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = subGrpDialog.findViewById(R.id.list);
            dialogList.setAdapter(subGroupAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                subGrpDialog.cancel();
                selectedSubGrp = tempProductSubGroupList.get(arg2);
                filterButtonList.get(1).setText(selectedSubGrp.getSubGrpName());
                switch (filterNo) {
                    case 3:
                        prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                        break;
                    case 4:
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                        break;
                }
            });
            Button cancel = subGrpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> subGrpDialog.cancel());
            subGrpDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showBrandListDialog() {
        if (!productBrandList.isEmpty()) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(mContext, R.layout.product_list_child, tempProductBrandList);
            brandDialog = new Dialog(mContext, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_with_search);
            brandDialog.setCancelable(false);
            TextView title = brandDialog.findViewById(R.id.title);
            title.setText("Please select a brand");
            EditText searchText = brandDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductBrandList();
                    }
                    lastStr = str;
                    filterProductBrandArray(str.length(), str);
                    brandAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = brandDialog.findViewById(R.id.list);
            dialogList.setAdapter(brandAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                brandDialog.cancel();
                selectedBrand = tempProductBrandList.get(arg2);
                filterButtonList.get(2).setText(selectedBrand.getBrandName());
                prepareOrderData(4, selectedBrand.getBrandCode());
            });
            Button cancel = brandDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> brandDialog.cancel());
            brandDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showMasterListDialog() {
        if (!productMasterList.isEmpty()) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
            }
            ProductMasterWithQtyInputAdapterObject = new ProductMasterCheckBoxAdapter(mContext, R.layout.list_item_textview_with_check_box, tempProductList);
            masterDialog = new Dialog(mContext, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = masterDialog.findViewById(R.id.title);
            EditText searchText = masterDialog.findViewById(R.id.autoCompleteTextView1);
            View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);
            list_header_item_planwise_input_screen.setVisibility(VISIBLE);
            TextView etProdQty = masterDialog.findViewById(R.id.etProdQty);
            etProdQty.setVisibility(View.GONE);
            TextView tv_last_month_purchase = masterDialog.findViewById(R.id.tv_last_month_purchase);
            TextView tv_order_plan = masterDialog.findViewById(R.id.tv_order_plan);
            TextView etProdRate = masterDialog.findViewById(R.id.etProdRate);
            tv_last_month_purchase.setVisibility(View.GONE);
            etProdRate.setVisibility(View.GONE);
            tv_order_plan.setVisibility(View.GONE);
            searchText.setVisibility(View.GONE);
            title.setVisibility(View.GONE);
            ImageView ivSideImage = masterDialog.findViewById(R.id.imageView1);
            ivSideImage.setVisibility(View.GONE);
            ImageView image_cancel = masterDialog.findViewById(R.id.image_cancel);
            LinearLayout applogoLayout = masterDialog.findViewById(R.id.applogoLayout);
            applogoLayout.setVisibility(VISIBLE);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(view -> {
                masterDialog.cancel();
                showTaggedProductsInCheckOutUi();
            });
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();
                }
            });
            prodQtyRateListView = masterDialog.findViewById(R.id.list);
            prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObject);
            if (lastProdPos != 0) {
                prodQtyRateListView.setSelection(lastProdPos - 1);
            } else {
                prodQtyRateListView.setSelection(0);
            }
            prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> lastProdPos = arg2);
            Button btnCancel = masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setText("Done");
            btnCancel.setOnClickListener(v -> {
                masterDialog.cancel();
                showTaggedProductsInCheckOutUi();
            });
            masterDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (!tempProductList.get(ii).getDesc().toUpperCase().contains(charVal.toUpperCase())) {
                    tempProductList.remove(tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductList.remove(tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductGroupArray(int strCnt, String charVal) {
        int size = tempProductGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductGroupList.get(ii).getGroupName().length() >= strCnt) {
                if (!tempProductGroupList.get(ii).getGroupName().toUpperCase().contains(charVal.toUpperCase())) {
                    tempProductGroupList.remove(tempProductGroupList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductGroupList.remove(tempProductGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductSubGroupArray(int strCnt, String charVal) {
        int size = tempProductSubGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductSubGroupList.get(ii).getSubGrpName().length() >= strCnt) {
                if (!tempProductSubGroupList.get(ii).getSubGrpName().toUpperCase().contains(charVal.toUpperCase())) {
                    tempProductSubGroupList.remove(tempProductSubGroupList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductSubGroupList.remove(tempProductSubGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductBrandArray(int strCnt, String charVal) {
        int size = tempProductBrandList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductBrandList.get(ii).getBrandName().length() >= strCnt) {
                if (!tempProductBrandList.get(ii).getBrandName().toUpperCase().contains(charVal.toUpperCase())) {
                    tempProductBrandList.remove(tempProductBrandList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductBrandList.remove(tempProductBrandList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void onPause() {
        super.onPause();
        if (mProgressDialogPending != null && mProgressDialogPending.isShowing()) {
            mProgressDialogPending.dismiss();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowChooseTransactionTypeDialog() {
        String[] fromArray = {"name", "image"};
        int[] to = {R.id.list_details, R.id.imageView1};
        if ((isCollectionOn()) && (isOrderOn())) {
            String[] transactionName = {"ORDER", "COLLECTION"};
            int[] transactionImages = {R.drawable.telephonic_order, R.drawable.telephonic_order};
            ArrayList<HashMap<String, String>> arrayList = new ArrayList<>();
            for (int i = 0; i < transactionName.length; i++) {
                HashMap<String, String> hashMap = new HashMap<>();
                hashMap.put("name", transactionName[i]);
                hashMap.put("image", transactionImages[i] + "");
                arrayList.add(hashMap);
            }
            final Dialog mDialogRoute = new Dialog(mContext, R.style.PauseDialog);
            mDialogRoute.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogRoute.setContentView(R.layout.select_with_search);
            mDialogRoute.setCancelable(false);
            TextView title = mDialogRoute.findViewById(R.id.title);
            title.setText("Please select transaction type");
            ListView dialogList = mDialogRoute.findViewById(R.id.list);
            dialogList.setOnItemClickListener((adapterView, view, i, l) -> {
                mDialogRoute.cancel();
                TextView titleSelectesdItem = view.findViewById(R.id.list_details);
                String selectedTransactionType = titleSelectesdItem.getText().toString();
                RedicrectToTransactionPage(selectedTransactionType);
            });
            SimpleAdapter simpleAdapter = new SimpleAdapter(this, arrayList, R.layout.route_list_child, fromArray, to);
            dialogList.setAdapter(simpleAdapter);
            EditText searchText = mDialogRoute.findViewById(R.id.autoCompleteTextView1);
            searchText.setVisibility(GONE);
            Button cancel = mDialogRoute.findViewById(R.id.btn_ok);
            cancel.setOnClickListener(view -> mDialogRoute.cancel());
            mDialogRoute.show();
        } else if (isOrderOn()) {
            RedicrectToTransactionPage("ORDER");
        } else {
            RedicrectToTransactionPage("COLLECTION");
        }
    }

    private void RedicrectToTransactionPage(String selectedTransactionType) {
        if (isAttendanceGiven || !mAceDnsDatabase.MenuAccess("attendance")) {
            if (!isCheckedOutToday) {
                if (selectedTransactionType.matches("ORDER")) {
                    Constants.transactionStartTime = Calendar.getInstance().getTime();
                    Constants.CurrentOrderCollectionTransactionType = "TO";
                    Constants.isVanSales = false;
                    GoToOrderProcess();
                } else {
                    Constants.CurrentOrderCollectionTransactionType = "TC";
                    gotoCollectionPage();
                }
            } else {
                Utils.showToast(MenuActivity.this, "You have already checked out. You can not do any transaction today.");
            }
        } else {
            Utils.showToast(MenuActivity.this, "Please give * Attendance first");
        }
    }

    private void GoToOrderProcess() {
        mAceDnsDatabase.getOrderFormDetailsObj();
        if (isCILogicOn()) {
            Utils.showProgressDialog(mContext, "Loading data, please wait...");
            new Thread() {
                public void run() {
                    Constants.selectedCustomer = null;
                    Constants.selectedRouteRecommended = null;
                    Constants.selectedRouteadditional = null;
                    Constants.selectedDistributorsRecommended = null;
                    Constants.selectedDistributoradditional = null;
                    Constants.selectedCustomerRecommended = null;
                    Constants.selectedCustomeradditional = null;
                    allCustomersWithLogic = new ArrayList<>();
                    Constants.allNewCustomers = new ArrayList<>();
                    Constants.allCustomersWithoutLogic = new ArrayList<>();
                    Constants.allRecommendedCustomersWithLogic = new ArrayList<>();
                    Constants.allAdditionalCustomersWithLogic = new ArrayList<>();
                    Constants.allRecommendedDistributorsWithLogic = new ArrayList<>();
                    Constants.allAdditionalDistributorsWithLogic = new ArrayList<>();
                    Constants.allNewDistributors = new ArrayList<>();
                    Constants.allRecommendedRoutesWithLogic = new ArrayList<>();
                    Constants.allAdditionalRoutesWithLogic = new ArrayList<>();
                    allCustomersWithLogic = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithLogic(1);
                    Constants.allCustomersWithoutLogic = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithLogic(2);
                    Constants.allNewCustomers = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithLogic(3);
                    allRecommendedCustomersCodeWithLogic = "";
                    allAdditionalCustomersCodeWithLogic = "";
                    if (!allCustomersWithLogic.isEmpty()) {
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                            Collections.sort(allCustomersWithLogic, Comparator.comparingInt(CustomerDetails::getciLogicPriority));
                        }
                        if (Utils.isNumeric(Constants.userDetailsObj.getCI_cust_display())) {
                            int displayCount = Integer.parseInt(Constants.userDetailsObj.getCI_cust_display());
                            String recommendedDistCodeList = "";
                            String additionalDistCodeList = "";
                            if (displayCount > allCustomersWithLogic.size()) {
                                displayCount = allCustomersWithLogic.size();
                            }
                            for (int count = 0; count < displayCount; count++) {
                                CustomerDetails currentCustomerItem = allCustomersWithLogic.get(count);
                                if (recommendedDistCodeList.matches("")) {
                                    recommendedDistCodeList = "'" + currentCustomerItem.getRdsTag() + "'";
                                } else {
                                    recommendedDistCodeList = MessageFormat.format("{0}, ''{1}''", recommendedDistCodeList, currentCustomerItem.getRdsTag());
                                }
                                Constants.allRecommendedCustomersWithLogic.add(currentCustomerItem);
                                if (allRecommendedCustomersCodeWithLogic.matches("")) {
                                    allRecommendedCustomersCodeWithLogic = "'" + currentCustomerItem.getCustomerCode() + "'";
                                } else {
                                    allRecommendedCustomersCodeWithLogic = MessageFormat.format("{0},''{1}''", allRecommendedCustomersCodeWithLogic, currentCustomerItem.getCustomerCode());
                                }
                            }
                            for (int count = displayCount; count < allCustomersWithLogic.size(); count++) {
                                CustomerDetails currentCustomerItem = allCustomersWithLogic.get(count);
                                if (additionalDistCodeList.matches("")) {
                                    additionalDistCodeList = "'" + currentCustomerItem.getRdsTag() + "'";
                                } else {
                                    additionalDistCodeList = MessageFormat.format("{0}, ''{1}''", additionalDistCodeList, currentCustomerItem.getRdsTag());
                                }
                                Constants.allAdditionalCustomersWithLogic.add(currentCustomerItem);
                                if (allAdditionalCustomersCodeWithLogic.matches("")) {
                                    allAdditionalCustomersCodeWithLogic = "'" + currentCustomerItem.getCustomerCode() + "'";
                                } else {
                                    allAdditionalCustomersCodeWithLogic = MessageFormat.format("{0},''{1}''", allAdditionalCustomersCodeWithLogic, currentCustomerItem.getCustomerCode());
                                }
                            }
                            for (int count = 0; count < Constants.allCustomersWithoutLogic.size(); count++) {
                                CustomerDetails currentCustomerItem = Constants.allCustomersWithoutLogic.get(count);
                                if (additionalDistCodeList.matches("")) {
                                    additionalDistCodeList = "'" + currentCustomerItem.getRdsTag() + "'";
                                } else {
                                    additionalDistCodeList = MessageFormat.format("{0}, ''{1}''", additionalDistCodeList, currentCustomerItem.getRdsTag());
                                }
                                Constants.allAdditionalCustomersWithLogic.add(currentCustomerItem);
                                if (allAdditionalCustomersCodeWithLogic.matches("")) {
                                    allAdditionalCustomersCodeWithLogic = "'" + currentCustomerItem.getCustomerCode() + "'";
                                } else {
                                    allAdditionalCustomersCodeWithLogic = MessageFormat.format("{0},''{1}''", allAdditionalCustomersCodeWithLogic, currentCustomerItem.getCustomerCode());
                                }
                            }
                            Constants.allRecommendedDistributorsWithLogic = mAceDnsDatabase.getRecommendedAdditionalDistributorListWithCustomerCode(recommendedDistCodeList);
                            if (!additionalDistCodeList.matches("")) {
                                Constants.allAdditionalDistributorsWithLogic = mAceDnsDatabase.getRecommendedAdditionalDistributorListWithCustomerCode(additionalDistCodeList);
                            }
                        }
                    }
                    if (!Constants.allNewCustomers.isEmpty()) {
                        String newDistCodeList = "";
                        for (int count = 0; count < Constants.allNewCustomers.size(); count++) {
                            CustomerDetails currentCustomerItem = Constants.allNewCustomers.get(count);
                            if (newDistCodeList.matches("")) {
                                newDistCodeList = "'" + currentCustomerItem.getRdsTag() + "'";
                            } else {
                                newDistCodeList = MessageFormat.format("{0}, ''{1}''", newDistCodeList, currentCustomerItem.getRdsTag());
                            }
                            if (allNewCustomersCode.matches("")) {
                                allNewCustomersCode = "'" + currentCustomerItem.getCustomerCode() + "'";
                            } else {
                                allNewCustomersCode = MessageFormat.format("{0},''{1}''", allNewCustomersCode, currentCustomerItem.getCustomerCode());
                            }
                        }
                        if (!newDistCodeList.matches("")) {
                            Constants.allNewDistributors = mAceDnsDatabase.getRecommendedAdditionalDistributorListWithCustomerCode(newDistCodeList);
                        }
                    }
                    Intent intent = new Intent(MenuActivity.this, ActivityOrderFilterAlternateDesign.class);
                    intent.putExtra("CARRY_IN", false);
                    startActivity(intent);
                }
            }.start();
        } else {
            Intent intent = new Intent(MenuActivity.this, ActivityOrderFilter.class);
            intent.putExtra("CARRY_IN", false);
            startActivity(intent);
        }
    }

    @SuppressLint("SimpleDateFormat")
    private void odometerProcess() {
        if (isTimeAutomatic(mContext)) {
            String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            showOdometerJourneyList(timeStamp, "Start journey info");
        } else {
            Utils.showSettingsAlertToChangeTimeZone(mContext);
        }
    }

    public void showOdometerJourneyList(String timeStamp, String dialogHeading) {
        processDone = false;
        checkboxdata = "";
        startingKm = "";
        endingKm = "";
        oddometerImage = "";
        journeyListSplitted = null;
        oddometerPicBitmap = null;
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.layout_odometer_journey_info);
        oddometerPicImageView = mDetailsDialog.findViewById(R.id.oddometerPicImageView);
        Button back = mDetailsDialog.findViewById(R.id.back);
        ImageView cancelBack = mDetailsDialog.findViewById(R.id.image_cancel);
        back.setVisibility(GONE);
        mDetailsDialog.setCancelable(false);
        LinearLayout odometerPicLayout = mDetailsDialog.findViewById(R.id.odometerPicLayout);
        odometerPicLayout.setVisibility(VISIBLE);
        Button buttonAddPicOutlet = mDetailsDialog.findViewById(R.id.buttonAddPicOutlet);
        buttonAddPicOutlet.setOnClickListener(arg0 -> {
            Intent takePicture = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            startActivityForResult(takePicture, TAKE_PHOTO_journey_info);
        });
        cancelBack.setOnClickListener(arg0 -> mDetailsDialog.dismiss());
        if (dialogHeading.toLowerCase().contains("start journey info")) {
            TextView StartingKmTextView = mDetailsDialog.findViewById(R.id.StartingKmTextView);
            StartingKmTextView.setVisibility(VISIBLE);
            StartingKmTextView.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    startingKm = StartingKmTextView.getText().toString();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
        }
        if (dialogHeading.toLowerCase().contains("checkout journey info")) {
            TextView EndingKmTextView = mDetailsDialog.findViewById(R.id.EndingKmTextView);
            EndingKmTextView.setVisibility(VISIBLE);
            EndingKmTextView.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    endingKm = EndingKmTextView.getText().toString();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
        }
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        textViewTitleName.setText(dialogHeading);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(arg0 -> {
            if (dialogHeading.toLowerCase().contains("checkout journey info")) {
                if (endingKm.matches("")) {
                    Utils.showToast(mContext, "Please provide Ending Km before submitting");
                } else if (oddometerImage.matches("")) {
                    Utils.showToast(mContext, "Please add Odometer Image before submitting");
                } else {
                    mAceDnsTransactionDatabase.UpdateCheckoutJourneyInfo("CH", timeStamp, checkboxdata, startingKm, endingKm, "", oddometerImage);
                    processDone = true;
                }
            } else {
                if (startingKm.matches("")) {
                    Utils.showToast(mContext, "Please provide Starting Km before submitting");
                } else if (oddometerImage.matches("")) {
                    Utils.showToast(mContext, "Please add Odometer Image before submitting");
                } else {
                    Utils.showToast(mContext, "Submitting");
                    mAceDnsTransactionDatabase.insertToJourneyInfoTableForExtra("A", timeStamp, checkboxdata, startingKm, endingKm, oddometerImage, "");
                    processDone = true;
                }
            }
            if (processDone) {
                if (!oddometerImage.matches("")) {
                    new TRANS_TourAttachmentExportTask(mContext, "add_oddometer", "", false, false).execute();
                }
                new TRANS_OdometerJourneyTransactionTask(mContext).execute();
                mDetailsDialog.dismiss();
            }
        });
        mDetailsDialog.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showInstructionWithHintDialogDoctorVisit() {
        final Dialog noOrderDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.hint_remarks_dialog_doctor_visit);
        noOrderDialog.setCancelable(false);
        TextView title = noOrderDialog.findViewById(R.id.title);
        TextView title2 = noOrderDialog.findViewById(R.id.title2);
        final Button submit;
        EditText et_metwith = noOrderDialog.findViewById(R.id.et_metwith);
        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
            title.setText("Please state the remarks");
            submit = noOrderDialog.findViewById(R.id.btn_submit);
            submit.setVisibility(VISIBLE);
        } else {
            title.setText("Please state the reason for no order");
            submit = noOrderDialog.findViewById(R.id.btn);
            submit.setVisibility(VISIBLE);
        }
        title2.setText("Any specific Requirement?");
        final EditText edReason = noOrderDialog.findViewById(R.id.ed_input);
        LinearLayout llMetWith = noOrderDialog.findViewById(R.id.llMetWith);
        submit.setOnClickListener(v -> {
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                submit.setEnabled(false);
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                boolean isSuccessHintRemarksTableInsertion;
                int i = 0, j = 0;
                boolean met = false;
                while (i < sparseBooleanArray.size()) {
                    if (sparseBooleanArray.valueAt(i)) {
                        j++;
                        if (hintRemarksValList.get(sparseBooleanArray.keyAt(i)).matches("Met with")) {
                            met = true;
                        } else {
                            selectRem = hintRemarksValList.get(sparseBooleanArray.keyAt(i));
                        }
                    }
                    i++;
                }
                if (j <= 1) {
                    if (met) {
                        if (!et_metwith.getText().toString().isEmpty()) {
                            if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
                                isSuccessHintRemarksTableInsertion = mAceDnsTransactionDatabase.insertToHintRemarksDetailsILS("DR", timeStamp, edReason.getText().toString(), selectRem, PreferenceData.getCheckInOutEmpCode(mContext), et_metwith.getText().toString());
                                mAceDnsTransactionDatabase.insertToLocationTable1("DR", timeStamp);
                                if (isSuccessHintRemarksTableInsertion) {
                                    ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("doctor_visit", "");
                                    TRANS_Doctor_Visit_TransactionTask sb2 = new TRANS_Doctor_Visit_TransactionTask(MenuActivity.this, unUploadedTransaction);
                                    sb2.execute();
                                }
                            }
                            noOrderDialog.cancel();
                        } else {
                            submit.setEnabled(true);
                            Utils.showToast(mContext, "Please Enter Met With");
                        }
                    } else {
                        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
                            isSuccessHintRemarksTableInsertion = mAceDnsTransactionDatabase.insertToHintRemarksDetailsILS("DR", timeStamp, edReason.getText().toString(),
                                    selectRem, PreferenceData.getCheckInOutEmpCode(mContext), et_metwith.getText().toString());
                            mAceDnsTransactionDatabase.insertToLocationTable1("DR", timeStamp);
                            if (isSuccessHintRemarksTableInsertion) {
                                ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("doctor_visit", "");
                                TRANS_Doctor_Visit_TransactionTask sb2 = new TRANS_Doctor_Visit_TransactionTask(MenuActivity.this, unUploadedTransaction);
                                sb2.execute();
                            }
                        }
                        noOrderDialog.cancel();
                    }
                } else if (j == 2 && met) {
                    if (!et_metwith.getText().toString().isEmpty()) {
                        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
                            isSuccessHintRemarksTableInsertion = mAceDnsTransactionDatabase.insertToHintRemarksDetailsILS("DR", timeStamp, edReason.getText().toString(),
                                    selectRem, PreferenceData.getCheckInOutEmpCode(mContext), et_metwith.getText().toString());
                            mAceDnsTransactionDatabase.insertToLocationTable1("DR", timeStamp);
                            if (isSuccessHintRemarksTableInsertion) {
                                ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("doctor_visit", "");
                                TRANS_Doctor_Visit_TransactionTask sb2 = new TRANS_Doctor_Visit_TransactionTask(MenuActivity.this, unUploadedTransaction);
                                sb2.execute();
                            }
                        }
                        noOrderDialog.cancel();
                    } else {
                        submit.setEnabled(true);
                        Utils.showToast(mContext, "Please Enter Met With");
                    }
                } else {
                    Utils.showToast(mContext, "Please Select One Reason");
                    submit.setEnabled(true);
                }
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
            }
        });
        RadioGroup rgp = noOrderDialog.findViewById(R.id.radiogroup);
        rgp.setOnCheckedChangeListener((radioGroup, id) -> selectedHintRemarksId = id);
        RadioGroup.LayoutParams rprms;
        for (int i = 0; i < hintRemarksValList.size(); i++) {
            RadioButton radioButton = new RadioButton(this);
            radioButton.setText(hintRemarksValList.get(i));
            radioButton.setId(i + 1);
            radioButton.setTextColor(getResources().getColor(R.color.text_color));
            if (selectedHintRemarksId == i + 1) {
                radioButton.setChecked(true);
            }
            rprms = new RadioGroup.LayoutParams(WindowManager.LayoutParams.WRAP_CONTENT, WindowManager.LayoutParams.WRAP_CONTENT);
            rgp.addView(radioButton, rprms);
        }
        ListView listview;
        listview = noOrderDialog.findViewById(R.id.listView);
        ArrayAdapter<String> adapter = new ArrayAdapter<>(MenuActivity.this, R.layout.drvisit, hintRemarksValList);
        listview.setAdapter(adapter);
        listview.setOnItemClickListener((parent, view, position, id) -> {
            sparseBooleanArray = listview.getCheckedItemPositions();
            String ValueHolder = "";
            int i = 0;
            llMetWith.setVisibility(GONE);
            while (i < sparseBooleanArray.size()) {
                if (sparseBooleanArray.valueAt(i)) {
                    if (hintRemarksValList.get(sparseBooleanArray.keyAt(i)).matches("Met with")) {
                        llMetWith.setVisibility(VISIBLE);
                    }
                }
                i++;
            }
            selectRem = ValueHolder;
        });
        noOrderDialog.show();
    }

    public void showConfirmationAttendance() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Want to get Leave");
        builder.setMessage("Are you sure?");
        builder.setPositiveButton("YES", (dialog, which) -> {
            attendanceProcess();
            dialog.dismiss();
        });
        builder.setNegativeButton("NO", (dialog, which) -> dialog.dismiss());
        AlertDialog alert = builder.create();
        alert.show();
    }

    public void showConfirmationHolidayAttendance() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Is This Holiday");
        builder.setMessage("Are you sure?");
        builder.setPositiveButton("YES", (dialog, which) -> {
            attendanceProcess();
            dialog.dismiss();
        });
        builder.setNegativeButton("NO", (dialog, which) -> dialog.dismiss());
        AlertDialog alert = builder.create();
        alert.show();
    }

    public void starsaathi_ledger_customer_list(Context mContext) {
        ArrayList<CustomerDetails> detailList = new ArrayList<>();
        Utils.showProgressDialog(mContext, "Downloding Customer data..");
        Log.d("TAG", "starsaathi_ledger_customer_list: " + Constants.employeeDetailObject.getEmpCode());
        Call<String> call = RestClient.getRestServiceString(mContext).starsaathi_cust_name(Constants.nickName, Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(@NonNull Call<String> call, @NonNull Response<String> response) {
                assert response.body() != null;
                Log.d("Response :=>", response.body());
                String jsonResult = response.body();
                try {
                    JSONObject obj = new JSONObject(jsonResult);
                    if (obj.getString("process_status").equals("YES")) {
                        JSONArray dataArray = obj.getJSONArray("customer_data");
                        for (int i = 0; i < dataArray.length(); i++) {
                            JSONObject dataobj = dataArray.getJSONObject(i);
                            CustomerDetails detailsObj = new CustomerDetails();
                            detailsObj.setCustomerCode(dataobj.getString("customer_code"));
                            detailsObj.setCustomerName(dataobj.getString("customer_name"));
                            detailsObj.setDnsCustCode(dataobj.getString("dns_customer_code"));
                            detailList.add(detailsObj);
                        }
                    }
                } catch (JSONException e) {
                    Log.d("sis_summary_error", e.toString());
                } finally {
                    if (!detailList.isEmpty()) {
                        chooseCustomerDialogStar(detailList);
                    }
                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(@NonNull Call<String> call, @NonNull Throwable t) {
                Log.d("Error==>", Objects.requireNonNull(t.getMessage()));
                Utils.cancelProgressDialog();
            }
        });
    }

    public void funTaDaChangeMode() {
        AlertDialog.Builder builder = new AlertDialog.Builder(mContext);
        builder.setTitle("Confirm");
        if (localStorage.getTADAPubPri().matches("public")) {
            builder.setMessage("Are you sure? To change Private Vehicle");
        } else {
            builder.setMessage("Are you sure? To change Public Vehicle");
        }
        builder.setPositiveButton("YES", (dialog, which) -> {
            if (localStorage.getTADAPubPri().matches("public")) {
                localStorage.setTADAPubPri("private");
                btn_ta_da_again.setBackgroundResource(R.drawable.bike_icon);
            } else {
                btn_ta_da_again.setBackgroundResource(R.drawable.bus_icon);
                localStorage.setTADAPubPri("public");
            }
            dialog.dismiss();
        });
        builder.setNegativeButton("NO", (dialog, which) -> dialog.dismiss());
        AlertDialog alert = builder.create();
        alert.show();
    }

    @SuppressLint("SetTextI18n")
    public void remainderDialog() {
        Dialog routeDialog;
        SurveyReportSumary mSurveyReportSumary, mSurveyReportSumaryT;
        routeDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.remainder_dialog);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Today's Reminder");
        TextView siteVisit = routeDialog.findViewById(R.id.txtSiteVisitCount);
        Date c = Calendar.getInstance().getTime();
        System.out.println("Current time => " + c);
        SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        String formattedDate = df.format(c);
        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummeryRemainderCounter(formattedDate, "Site Visit");
        mSurveyReportSumaryT = mAceDnsTransactionDatabase.getSurveyReportSummeryRemainderCounter("no", "Site Visit");
        siteVisit.setText("Site Visit   " + mSurveyReportSumary.getNoSiteVisit() + "/" + mSurveyReportSumaryT.getNoSiteVisit());
        TextView facilitator = routeDialog.findViewById(R.id.txtFacilitatorCount);
        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummeryRemainderCounter(formattedDate, "Facilitator Add");
        mSurveyReportSumaryT = mAceDnsTransactionDatabase.getSurveyReportSummeryRemainderCounter("no", "Facilitator Add");
        facilitator.setText("Facilitator   " + mSurveyReportSumary.getnoFacilitaorAdd() + "/" + mSurveyReportSumaryT.getnoFacilitaorAdd());
        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void remainderSiteDialog() {
        Dialog routeDialog;
        SurveyReportSumary mSurveyReportSumary;
        routeDialog = new Dialog(MenuActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.remainder_site_visit_dialog);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Today's Reminder");
        TextView siteVisit = routeDialog.findViewById(R.id.txtSiteVisitCount);
        TextView siteUp = routeDialog.findViewById(R.id.txtSiteVisitUpcommingCount);
        ListView dialogList = routeDialog.findViewById(R.id.list);
        ListView dialogListFaci = routeDialog.findViewById(R.id.listUp);
        Date c = Calendar.getInstance().getTime();
        System.out.println("Current time => " + c);
        SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        String formattedDate = df.format(c);
        ArrayList<CommonModel> data = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounter(formattedDate, "Site Visit");
        mSurveyReportSumary = mAceDnsTransactionDatabase.upcommingVisitCount(formattedDate, "Site Visit");
        siteVisit.setText("Site Visit Reminder - " + data.size() + "\n Upcoming " + mSurveyReportSumary.getNoUpcommingVisit());
        data = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounterWithUpcomming(formattedDate, "Site Visit");
        CommonModelListAdapter adapter1 = new CommonModelListAdapter(MenuActivity.this, data);
        dialogList.setAdapter(adapter1);
        data = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounter(formattedDate, "Facilitator Add");
        mSurveyReportSumary = mAceDnsTransactionDatabase.upcommingVisitCount(formattedDate, "Facilitator Add");
        siteUp.setText("Facilitator Reminder - " + data.size() + "\n Upcoming " + mSurveyReportSumary.getNoUpcommingVisit());
        data = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounterWithUpcomming(formattedDate, "Facilitator Add");
        CommonModelListAdapter adapter2 = new CommonModelListAdapter(MenuActivity.this, data);
        dialogListFaci.setAdapter(adapter2);
        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());
        routeDialog.show();
    }

    // New Site Lead Approval
    private void menuUpdate() {
        if (userType.equalsIgnoreCase("asm")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("new_site_lead_and_conversion_tracking");
            menuObj.setResourceId(R.drawable.new_sitelead_approval);
            mMenuList.add(menuObj);
            mMenuAdapter.notifyDataSetChanged();
        }
    }

    // Birth Day Popup
    private void initBirthDayPopup() {
        birthdayPopupLayout = findViewById(R.id.birthdayPopupLayout);
        birthdayCardContainer = findViewById(R.id.birthdayCardContainer);
        birthdayBackgroundImage = findViewById(R.id.birthdayBackgroundImage);
        birthdayPopupClose = findViewById(R.id.birthdayPopupClose);
        birthdayTitle = findViewById(R.id.birthdayTitle);
        birthdayUserName = findViewById(R.id.birthdayUserName);
        birthdayMessage = findViewById(R.id.birthdayMessage);

        birthdayPopupLayout.setVisibility(View.GONE);

        birthdayPopupLayout.setOnClickListener(this);
        birthdayPopupClose.setOnClickListener(this);
        initUpdateDateOfBirthPopup();
    }

    private void initUpdateDateOfBirthPopup() {
        dobEnterPopupLayout = findViewById(R.id.dobEnterPopupLayout);
        dobEnterPopup = findViewById(R.id.dobEnterPopup);
        dateOfBirthLayout = findViewById(R.id.dateOfBirthLayout);
        dateOfBirthTextView = findViewById(R.id.dateOfBirthTextView);
        updateDOBButton = findViewById(R.id.updateDOBButton);

        dobEnterPopupLayout.setVisibility(View.GONE);

        dobEnterPopupLayout.setOnClickListener(this);
        dateOfBirthLayout.setOnClickListener(this);
        updateDOBButton.setOnClickListener(v -> {
            if (dobDate == null || dobDate.isEmpty()) {
                Toast.makeText(mContext, "Please select DOB", Toast.LENGTH_SHORT).show();
                return;
            }
            requestForSeenAPI111();
        });

        new TRANS_CheckDOB_AsyncTask(mContext).execute();
    }

    private void onClickDOB(View v) {
        if (v == birthdayPopupLayout) {
            birthdayPopupLayout.setVisibility(View.GONE);
            requestForSeenAPI();
            primaryFunction();
        } else if (v == birthdayPopupClose) {
            birthdayPopupLayout.setVisibility(View.GONE);
            requestForSeenAPI();
            primaryFunction();
        } else if (v == dobEnterPopupLayout) {
            Toast.makeText(mContext, "Please update your Date of Birth", Toast.LENGTH_LONG).show();
        } else if (v == dateOfBirthLayout) {
            Calendar calendar = Calendar.getInstance();
            int year = calendar.get(Calendar.YEAR);
            int month = calendar.get(Calendar.MONTH);
            int day = calendar.get(Calendar.DAY_OF_MONTH);

            DatePickerDialog datePickerDialog = new DatePickerDialog(
                    mContext,
                    (view, selectedYear, selectedMonth, selectedDay) -> {
                        // Month is 0-based, so add 1
                        String date = "";
                        if (selectedDay < 10) {
                            date = date + "0" + selectedDay + "-";
                        } else {
                            date = selectedDay + "-";
                        }
                        if (selectedMonth < 9) {
                            date = date + "0" + (selectedMonth + 1) + "-" + selectedYear;
                        } else {
                            date = date + (selectedMonth + 1) + "-" + selectedYear;
                        }
                        dateOfBirthTextView.setText(date);
                        dobDate = date;
                    },
                    year, month, day
            );
            datePickerDialog.getDatePicker().setMaxDate(calendar.getTimeInMillis());
            datePickerDialog.show();
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_CheckDOB_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckDOB_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
            Log.d("TAG", "_DDDD_ onPreExecute: TRANS_CheckDOB_AsyncTask");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl + "misreport/get-employee-dob.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DDDD_ : " + url);
                    String a = HttpCalling.httpGetCallWithTextResponse(url).trim();
                    JSONObject obj = new JSONObject(a);
                    Log.d("TAG", "_DDDD_ : " + obj);
                    if (obj.getBoolean("status")) {
                        progressDialogClose();
                        runOnUiThread(() -> {
                            try {
                                Glide.with(mContext)
                                        .load(obj.getString("img"))
                                        .into(birthdayBackgroundImage);
                                birthdayTitle.setText(obj.getString("title"));
                                birthdayUserName.setText(obj.getString("emp_name"));
                                birthdayMessage.setText(obj.getString("message"));
                            } catch (JSONException e) {
                                throw new RuntimeException(e);
                            }
                            birthdayPopupLayout.setVisibility(VISIBLE);

                        });
                    } else {
                        if (obj.getString("message").equalsIgnoreCase("birthday not found")) {
                            progressDialogClose();
                            runOnUiThread(() -> {
                                dobEnterPopupLayout.setVisibility(VISIBLE);
                            });
                        } else {
                            runOnUiThread(() -> {
                                Utils.showProgressDialog(mContext, "Downloading data.Please wait.");
                                new commonAsyncTaskMaster(mContext, "golden_rules");
                            });
                        }
                    }
                } catch (Exception e) {
                    runOnUiThread(() -> {
                        Utils.showProgressDialog(mContext, "Downloading data.Please wait.");
                        new commonAsyncTaskMaster(mContext, "golden_rules");
                    });
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            progressDialogClose();
        }
    }

    private void requestForSeenAPI() {
        new Thread(() -> {
            try {
                OkHttpClient client = new OkHttpClient();
                RequestBody formBody = new FormBody.Builder()
                        .add("emp_code", Constants.employeeDetailObject.getEmpCode())
                        .build();
                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/birthday_wish_seen.php")
                        .post(formBody)
                        .build();
                okhttp3.Response response = client.newCall(request).execute();
                runOnUiThread(() -> {
                    Utils.showProgressDialog(mContext, "Downloading data.Please wait.");
                    new commonAsyncTaskMaster(mContext, "golden_rules");
                });
            } catch (Exception ignored) {
            }
        }).start();
    }

    private void requestForSeenAPI111() {
        new Thread(() -> {
            try {
                OkHttpClient client = new OkHttpClient();
                RequestBody formBody = new FormBody.Builder()
                        .add("emp_code", Constants.employeeDetailObject.getEmpCode())
                        .add("dob", dobDate)
                        .build();
                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/update_employee_dob.php")
                        .post(formBody)
                        .build();
                okhttp3.Response response = client.newCall(request).execute();
                String res = response.body().string();
                Log.d("TAG", "_DDDD_ API Response : " + res);
                runOnUiThread(() -> {
                    dobEnterPopupLayout.setVisibility(View.GONE);
                    new TRANS_CheckDOB_AsyncTask(mContext).execute();
                });
            } catch (Exception ignored) {
            }
        }).start();
    }
    // Birth Day Popup

}
