package com.forcepower.acedns.activity.auth;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.text.InputType;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.backgroundTask.AUTH_LoadEmployeeMasterData;
import com.forcepower.acedns.backgroundTask.AUTH_UpdateCheck_db;
import com.forcepower.acedns.backgroundTask.DATA_DeleteAppDBTask;
import com.forcepower.acedns.backgroundTask.DATA_EmailCrashLogToDeveloperTask;
import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;
import com.forcepower.acedns.backgroundTask.DATA_LoadDatabaseDetails;
import com.forcepower.acedns.backgroundTask.POST_SendFcmId;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOldCustomer;
import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.DatabaseHelperSqlite;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class LoginActivity extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;

    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextEmployeeId = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextEmployeeName = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextPassword = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewPhoneNumberHint = null;

    @SuppressLint("StaticFieldLeak")
    public static Button mButtonLogin = null;
    @SuppressLint("StaticFieldLeak")
    public static Button btn_dbbackup = null;

    public static String mEmployeeIdOrPhopneNumber = "";
    public static String mPassword = "";

    AceDnsDatabase mAceDnsDatabase;
    AppInfo mAppInfoObj;
    Context mContext;
    int MY_REQUEST_CODE = 1997;

    @SuppressLint({"MissingPermission", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        RegisterActivities.registerActivity(this);
        mContext = this;
        //Utils.acquireScreen(this);
        InitializeView();
        Constants.isFirstLoginOfDay = false;
        Constants.isFirstLoginOfApp = false;

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            btn_dbbackup = findViewById(R.id.btn_dbbackup);
            btn_dbbackup.setVisibility(View.VISIBLE);
            btn_dbbackup.setOnClickListener(view -> {
                if (new File(Utils.getAppStoragePath(mContext) + "AceDns.db").exists() && HTTPUtils.isConnectionPossible(mContext)) {
                    String empCode = "";
                    String empName = "";
                    Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                    if (Constants.employeeDetailObject != null) {
                        Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
                        empCode = Constants.employeeDetailObject.getEmpCode();
                        empName = Constants.employeeDetailObject.getEmpName();
                    }
                    new DATA_EmailToDeveloperTask(mContext, true, empCode, "", empName, true).execute();
                } else {
                    if (!new File(Utils.getAppStoragePath(mContext) + "AceDns.db").exists()) {
                        Utils.showToast(mContext, "DB file not found.");
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                }
            });
        }
        runOnUiThread(() -> {
            File dbFile = new File(Utils.getAppStoragePath(mContext) + "AceDns.db");
            if (dbFile.exists()) {
                mAceDnsDatabase = new AceDnsDatabase(LoginActivity.this);
                Utils.InitialiseSETUPTableData(LoginActivity.this);
                if (!Constants.isLARAVELAPI) {
                    Constants.isLARAVELAPI = Constants.userDetailsObj != null && Constants.userDetailsObj.getapp_phoneno_login().equalsIgnoreCase("yes");
                }
                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                if (Constants.employeeDetailObject != null) {
                    mEditTextEmployeeName.setVisibility(View.VISIBLE);
                    if (Constants.isLARAVELAPI && Constants.employeeDetailObject.getPhoneNumber() != null && !Constants.employeeDetailObject.getPhoneNumber().matches("")) {
                        mEditTextEmployeeId.setText(Constants.employeeDetailObject.getPhoneNumber());
                    } else {
                        mEditTextEmployeeId.setText(Constants.employeeDetailObject.getEmpCode());
                        mEditTextEmployeeId.setEnabled(false);
                    }
                    mEditTextEmployeeName.setText(Constants.employeeDetailObject.getEmpName());
                    mEditTextEmployeeName.setEnabled(false);
                    mEditTextPassword.requestFocus();
                } else {
                    mEditTextEmployeeName.setVisibility(View.GONE);
                }

                if (!getIntent().hasExtra("LogoutSession")) {
                    String dbCheckStatus = "";
                    mAppInfoObj = mAceDnsDatabase.getAppInfo();
                    if (mAppInfoObj != null && Constants.employeeDetailObject != null) {
                        Constants.nickName = mAppInfoObj.getNickName().trim();
                        Constants.mDBVersion = mAppInfoObj.getDbVersion();
                        if (Constants.orderFormDetailsObj != null && Constants.productDetailsObj != null && Constants.userDetailsObj != null && Constants.menuDetailsObj != null) {
                            Constants.isLARAVELAPI = Constants.userDetailsObj.getapp_phoneno_login().equalsIgnoreCase("yes");
                            try {
                                dbCheckStatus = new DATA_DeleteAppDBTask(LoginActivity.this).execute().get();
                            } catch (Exception ignored) {
                            }
                            if (!dbCheckStatus.matches("1")) {
                                Constants.logoBmp = BitmapFactory.decodeByteArray(mAppInfoObj.getLogo(), 0, mAppInfoObj.getLogo().length);
                                String currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
                                String status = mAceDnsDatabase.CheckLoginOfDay(currentDate);
                                if (status.equalsIgnoreCase("YES")) {
                                    Constants.isDbCheckinStatus = true;
                                    if (mAppInfoObj.getAppVersion().trim().equalsIgnoreCase("5.1.4")) {
                                        new TRANS_SubmitOldCustomer(LoginActivity.this, false).execute();
                                    } else {
                                        new DATA_LoadDatabaseDetails(LoginActivity.this).execute(Constants.employeeDetailObject.getEmpCode());
                                    }
                                }
                                ShowUpdationDialog();
                            } else {
                                showAppCloseDialog();
                            }
                        } else {
                            Constants.isFirstLoginOfApp = false;
                            startActivity(new Intent(LoginActivity.this, TakeNickNameActivity.class));
                        }
                    } else {
                        Constants.isFirstLoginOfApp = true;
                        startActivity(new Intent(LoginActivity.this, TakeNickNameActivity.class));
                    }
                }
            } else {
                Constants.isFirstLoginOfApp = true;
                mEditTextEmployeeName.setVisibility(View.GONE);
                startActivity(new Intent(LoginActivity.this, TakeNickNameActivity.class));
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void showAppCloseDialog() {
        final Dialog appCloseDialog = new Dialog(mContext);
        appCloseDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        appCloseDialog.setContentView(R.layout.printing_dialog);
        appCloseDialog.setCancelable(false);
        TextView title = appCloseDialog.findViewById(R.id.title);
        title.setText("Database deleted to maintain data integrity.\n Please Login again.");
        Button yes = appCloseDialog.findViewById(R.id.btn_yes);
        Button no = appCloseDialog.findViewById(R.id.btn_no);
        no.setVisibility(View.GONE);
        yes.setText("OK");
        yes.setOnClickListener(v -> {
            appCloseDialog.cancel();
            RegisterActivities.removeAllActivities();
        });
        appCloseDialog.show();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        mEditTextPassword.setText("");
        checkLaravelApiMakeUiChanges();
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mEditTextEmployeeId = findViewById(R.id.ed_emp_id);
        textViewPhoneNumberHint = findViewById(R.id.textViewPhoneNumberHint);
        mEditTextPassword = findViewById(R.id.ed_pwd);
        checkLaravelApiMakeUiChanges();
        mEditTextEmployeeName = findViewById(R.id.imeiStatusTV);
        mImageViewHeaderLogo = findViewById(R.id.imageView1);

        mButtonLogin = findViewById(R.id.btn_login);
        mButtonLogin.setOnClickListener(this);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(LoginActivity.this) + "~" + Utils.getDBVersion(LoginActivity.this));
        Button bk_clkd = findViewById(R.id.back);
        bk_clkd.setVisibility(View.GONE);

        try {
            mAceDnsDatabase = new AceDnsDatabase(LoginActivity.this);
            Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
            if (Constants.employeeDetailObject != null) {
                mEditTextEmployeeName.setVisibility(View.VISIBLE);
                if (Constants.isLARAVELAPI && Constants.employeeDetailObject.getPhoneNumber() != null && !Constants.employeeDetailObject.getPhoneNumber().matches("")) {
                    mEditTextEmployeeId.setText(Constants.employeeDetailObject.getPhoneNumber());
                    mEditTextEmployeeId.setText(Constants.employeeDetailObject.getPhoneNumber());
                }
            }
        } catch (Exception ignored) {
        }
    }

    public void checkLaravelApiMakeUiChanges() {
        if (Constants.isLARAVELAPI) {
            mEditTextPassword.setVisibility(View.GONE);
            mEditTextEmployeeId.setHint("Phone No.");
            mEditTextEmployeeId.setText("");
            mEditTextEmployeeId.setText("");
            mEditTextEmployeeId.setEnabled(true);
            mEditTextEmployeeId.setInputType(InputType.TYPE_CLASS_PHONE);
            textViewPhoneNumberHint.setVisibility(View.VISIBLE);
            mEditTextEmployeeId.setBackgroundResource(R.drawable.edit_text_background_left_corner_straight);

            try {
                mAceDnsDatabase = new AceDnsDatabase(LoginActivity.this);
                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                mEditTextEmployeeId.setText(Constants.employeeDetailObject.getPhoneNumber());
            } catch (Exception ignored) {
            }
        }
    }

    @SuppressLint("SimpleDateFormat")
    @Override
    public void onClick(View view) {
        if (view == mButtonLogin) {
            Log.d("TAG", "_DOWNLOAD_product_master: Call login");
            try {
                boolean properIdPhoneInputGiven = true, properPassGiven = true;
                mEmployeeIdOrPhopneNumber = mEditTextEmployeeId.getText().toString();
                if (!Constants.isLARAVELAPI) {
                    mPassword = mEditTextPassword.getText().toString();
                    if (mPassword.isEmpty()) {
                        properPassGiven = false;
                    }
                }
                if (mEmployeeIdOrPhopneNumber.isEmpty()) {
                    properIdPhoneInputGiven = false;
                }
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (properIdPhoneInputGiven && properPassGiven) {
                    Log.d("TAG", "_DOWNLOAD_product_master: Call login 1");
                    String libraryStatus = Utils.checkLibraryConditions(LoginActivity.this);
                    String currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
                    int loginStatus = mAceDnsDatabase.VerifyLoginStatus(mEmployeeIdOrPhopneNumber, mPassword, (!currentDate.isEmpty() ? currentDate : new Date().toString()));
                    if (loginStatus == 110 || loginStatus == 111) {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 2");
                        if (libraryStatus.trim().equalsIgnoreCase("Mobile Data Error")) {
                            Log.d("TAG", "_DOWNLOAD_product_master: Call login 3");
                            libraryStatus = "ALL OKK";
                        }
                    }
                    if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 4");
                        boolean installed = isapplicationInstalled();
                        if (installed) {
                            Log.d("TAG", "_DOWNLOAD_product_master: Call login 5");
                            Uri packageURI = Uri.parse("package:org.coral.acedns");
                            Intent uninstallIntent = new Intent(Intent.ACTION_DELETE, packageURI);
                            startActivity(uninstallIntent);
                        } else {
                            Log.d("TAG", "_DOWNLOAD_product_master: Call login 6");
                            VerifyLoginStatus();
                        }
                    } else {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 7");
                        Utils.showToast(LoginActivity.this, libraryStatus + " Please Synchronize Data.");
                    }
                } else {
                    Log.d("TAG", "_DOWNLOAD_product_master: Call login 8");
                    if (!Constants.isLARAVELAPI) {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 9");
                        Utils.showToast(LoginActivity.this, "EmployeeID or Password cannot be left blank");
                    } else {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 10");
                        Utils.showToast(LoginActivity.this, "Phone number cannot be left blank");
                    }
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_product_master: Call login " + e);
            }
        }
    }

    @SuppressLint("SimpleDateFormat")
    public void VerifyLoginStatus() {
        UpdateAppVersionInAppInfo();
        String currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
        int loginStatus = 0;
        loginStatus = mAceDnsDatabase.VerifyLoginStatus(mEmployeeIdOrPhopneNumber, mPassword, (!currentDate.isEmpty() ? currentDate : new Date().toString()));

        Constants.isLoginnow = true;
        switch (loginStatus) {
            case 0:
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 11 " + loginStatus);
                Constants.isFirstLoginOfApp = true;
                Constants.isFirstLoginOfDay = true;
                PreferenceData.ClearPreferenceData(getApplicationContext());
                UpdateCheckAuthenticateEmployee();
                break;
            case 1:
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 11 " + loginStatus);
                break;
            case 10:
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 11 " + loginStatus);
                Utils.InitialiseSETUPTableData(LoginActivity.this);
                Constants.isFirstLoginOfApp = false;
                Constants.isFirstLoginOfDay = true;
                mAceDnsDatabase.DeleteNotificationDataFromLocationAndNotificationTable();
                UpdateCheckAuthenticateEmployee();
                AutoEmailCrashLog();
                break;
            case 110:
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 11 " + loginStatus);
                if (!Constants.isLARAVELAPI) {
                    Utils.showToast(LoginActivity.this, "Please provide valid UserId and Password");
                } else {
                    Utils.showToast(LoginActivity.this, "Please provide valid Phone number");
                }
                Constants.isFirstLoginOfDay = false;
                Constants.isFirstLoginOfApp = false;
                break;
            case 111:
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 11 " + loginStatus);
                mAceDnsDatabase.DeleteNotificationDataFromLocationAndNotificationTable();
                AutoEmailCrashLog();
                Constants.isFirstLoginOfDay = false;
                Constants.isFirstLoginOfApp = false;
                Constants.dateString = Constants.employeeDetailObject.getDate().replace("-", "");
                if (Utils.lastLoginSuccessfull(LoginActivity.this) && CheckkAppInfoTable()) {
                    Log.d("TAG", "_DOWNLOAD_product_master: Call login 12 " + loginStatus);
                    if (Utils.InitialiseSETUPTableData(LoginActivity.this)) {
                        Log.d("TAG", "_DOWNLOAD_product_master: Call login 13 " + loginStatus);
                        FCMProcess();
                    } else {
                        Log.d("TAG", "_DOWNLOAD_ product_master: Call login 14 " + loginStatus);
                        Constants.dataResfresh = true;
                        new DATA_LoadDatabaseDetails(LoginActivity.this).execute(Constants.employeeDetailObject.getEmpCode());
                    }
                } else {
                    Log.d("TAG", "_DOWNLOAD_ product_master: Call login 15 " + loginStatus);
                    Constants.dataResfresh = true;
                    new DATA_LoadDatabaseDetails(LoginActivity.this).execute(Constants.employeeDetailObject.getEmpCode());
                }
                break;
        }
    }

    public void AutoEmailCrashLog() {
        try {
            File filename = new File(Utils.getAppStoragePath(mContext) + "ACEdnsCrashLog.txt");
            if (filename.exists() && HTTPUtils.isConnectionPossible(mContext)) {
                String empCode = "", empName = "";
                if (Constants.employeeDetailObject != null) {
                    Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
                    empCode = Constants.employeeDetailObject.getEmpCode();
                    empName = Constants.employeeDetailObject.getEmpName();
                }
                new DATA_EmailCrashLogToDeveloperTask(mContext, false, empCode, "", empName, true).execute();
            }
        } catch (Exception ignored) {
        }
    }

    public void UpdateCheckAuthenticateEmployee() {
        if (new ConnectionDetector(mContext).isConnectingToInternet()) {
            new AUTH_LoadEmployeeMasterData(LoginActivity.this, false).execute(mEmployeeIdOrPhopneNumber, mPassword);
        } else {
            Utils.showToast(mContext, "Please check your internet.");
        }
    }

    //if firebase cloud messaging registration id is changed, api call will be done to send the id to server
    public void FCMProcess() {
        try {
            Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-1");
            DatabaseHelperSqlite helper = new DatabaseHelperSqlite(mContext);
            ConnectionDetector cd = new ConnectionDetector(mContext);
            Boolean isInternetPresent = cd.isConnectingToInternet();
            Boolean updateAvailable = helper.isUpdateAvailable();
            if (updateAvailable && isInternetPresent) {
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-2");
                String registrationId = helper.getRegistrationId();
                ContentValues values = new ContentValues();
                values.put("nick_name", Constants.nickName);
                values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
                values.put("registrationid", registrationId);
                values.put("deviceid", Constants.employeeDetailObject.getDeviceID());
                //call api and change status to
                new POST_SendFcmId(mContext, registrationId, values, false, "").execute();
            } else {
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-3");
                new AUTH_UpdateCheck_db(mContext, true).execute(Constants.employeeDetailObject.getEmpCode());
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_product_master: Call login 3 " + e);
        }

    }

    public void ShowUpdationDialog() {
        final String currentVersion = Utils.getAppVersion(LoginActivity.this);
        String savedVersion = Constants.employeeDetailObject.getAppVersion();
        if (savedVersion != null && !savedVersion.isEmpty()) {
            if (!savedVersion.equalsIgnoreCase(currentVersion)
                    || Constants.employeeDetailObject.getUpdationFlag()
                    .equalsIgnoreCase("SHOW")) {
                UpdateDialog(currentVersion);
            }
        } else {
            UpdateDialog(currentVersion);
        }
    }

    @SuppressLint("SetTextI18n")
    public void UpdateDialog(final String currentVersion) {
        final Dialog dialogUpdate = new Dialog(LoginActivity.this, R.style.PauseDialog);
        dialogUpdate.setCancelable(false);
        dialogUpdate.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogUpdate.setContentView(R.layout.update_feature_dialog);
        TextView txtTitle = dialogUpdate.findViewById(R.id.txt_header);
        txtTitle.setText("Check whats new in\nACEdns~Version " + Utils.getAppVersion(LoginActivity.this) + "...");
        TextView txtMsg = dialogUpdate.findViewById(R.id.text);
        txtMsg.setText(Constants.updateFeatureData);
        final CheckBox showOption = dialogUpdate.findViewById(R.id.checkBox1);
        Button okk = dialogUpdate.findViewById(R.id.btn_ok);
        okk.setOnClickListener(arg0 -> {
            dialogUpdate.cancel();
            if (showOption.isChecked()) {
                mAceDnsDatabase.updateUpdationStatus(currentVersion, "HIDE");
            } else {
                mAceDnsDatabase.updateUpdationStatus(currentVersion, "SHOW");
            }
            if (!getIntent().hasExtra("LogoutSession")) {
                if (Constants.nickName.isEmpty()) {
                    startActivity(new Intent(LoginActivity.this, TakeNickNameActivity.class));
                }
            }
        });
        dialogUpdate.show();
    }

    public void UpdateAppVersionInAppInfo() {
        try {
            if (mAppInfoObj != null && !(mAppInfoObj.getAppVersion().equalsIgnoreCase(Utils.getAppVersion(LoginActivity.this)))) {
                mAceDnsDatabase.updateAppVersion(LoginActivity.this);
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_product_master: Call login 1 " + e);
        }
    }

    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 1) {
            Utils.directOutsideTheApplication(LoginActivity.this, "We will soon contact you with a feedback", false);
        } else if (requestCode == MY_REQUEST_CODE) {
            if (resultCode != RESULT_OK) {
                UpdateCheckAuthenticateEmployee();
            }
        }

    }

    public boolean CheckkAppInfoTable() {
        boolean status = false;
        status = mAceDnsDatabase.chkAppInfo();
        return status;
    }

    private boolean isapplicationInstalled() {
        PackageManager pm = getPackageManager();
        boolean app_installed = false;
        try {
            pm.getPackageInfo("org.coral.acedns", PackageManager.GET_ACTIVITIES);
            app_installed = true;
        } catch (PackageManager.NameNotFoundException ignored) {
        }
        return app_installed;
    }
}
