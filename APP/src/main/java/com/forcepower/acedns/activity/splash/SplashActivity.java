package com.forcepower.acedns.activity.splash;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Display;
import android.view.View;
import android.view.Window;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.CustomExceptionHandler;
import com.forcepower.acedns.activity.auth.LoginActivity;
import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.RuntimePermissionChecking;
import com.forcepower.acedns.util.Utils;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.messaging.FirebaseMessaging;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.documentfile.provider.DocumentFile;

import static com.forcepower.acedns.R.id.companyLogoImageView;
import static com.forcepower.acedns.constants.Constants.print_Log_d;
import static com.forcepower.acedns.database.SharedPrefData.get_firebase_token;
import static com.forcepower.acedns.database.SharedPrefData.set_firebase_token;

import org.json.JSONObject;

public class SplashActivity extends AceDnsParentActivity {
    Handler mHandler;
    Boolean isSDPresent;
    AceDnsDatabase mAceDnsDatabase;
    Boolean StorageWritePermissionGiven = false, StorageReadPermissionGiven = false, LocationPermissionGiven = false, CameraPermissionGiven = false, PhonePermissionGiven = false, ContactsPermissionGiven = false;
    RuntimePermissionChecking RuntimePermissionCheckingObject;
    Context mContext;
    int currentapiVersion = android.os.Build.VERSION.SDK_INT;
    private static final int NEW_FOLDER_REQUEST_CODE = 43;

    //new popup
    LinearLayout maintenancePopupLayout, btnDesignLayout, openWebsiteButton;
    TextView popupMessage;
    String link = "";
    //new popup

    public static String[] storge_permissions = {
            android.Manifest.permission.WRITE_EXTERNAL_STORAGE,
            android.Manifest.permission.READ_EXTERNAL_STORAGE,
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION,
            Manifest.permission.CAMERA,
            Manifest.permission.POST_NOTIFICATIONS
    };

    @RequiresApi(api = Build.VERSION_CODES.TIRAMISU)
    public static String[] storge_permissions_33 = {
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION,
            Manifest.permission.CAMERA,
            Manifest.permission.POST_NOTIFICATIONS
    };

    public static String[] permissions() {
        String[] p;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            p = storge_permissions_33;
        } else {
            p = storge_permissions;
        }
        return p;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);
        mContext = this;
        abcd();
//        SharedPreferences sharedPreferences = getSharedPreferences("MyPrefs", MODE_PRIVATE);
//        String availableDatabase = sharedPreferences.getString("available_database", "");
//        if(availableDatabase.isEmpty()){
//            deleteFullDatabaseAndFiles();
//            Log.d("TAG", "_DOWNLOAD_: Clear Database");
//            SharedPreferences.Editor editor = sharedPreferences.edit();
//            editor.putString("available_database", "yes");
//            editor.apply();
//            abcd();
//        }else{
//            Log.d("TAG", "_DOWNLOAD_: Previous Database");
//            abcd();
//        }
    }
    private void abcd(){
        //new popup
        init();
        //new popup

        RegisterActivities.registerActivity(this);
        mHandler = new Handler();
        Display display = getWindowManager().getDefaultDisplay();
        DisplayMetrics outMetrics = new DisplayMetrics();
        display.getMetrics(outMetrics);
        float dpHeight = outMetrics.heightPixels;
        float dpWidth = outMetrics.widthPixels;
        View positiveButton = findViewById(companyLogoImageView);
        int dynamicImageViewWidth = (int) (dpWidth / 2.8);
        positiveButton.getLayoutParams().width = dynamicImageViewWidth;
        positiveButton.getLayoutParams().height = dynamicImageViewWidth;
        RelativeLayout.LayoutParams layoutParams = (RelativeLayout.LayoutParams) positiveButton.getLayoutParams();
        int left = (int) ((dpWidth / 3.4));
        int top = (int) ((dpHeight / 4.5));
        layoutParams.setMargins(left, top, 0, 0);
        positiveButton.setLayoutParams(layoutParams);
        ImageView companyLogoImageView = findViewById(R.id.companyLogoImageView);
        if (currentapiVersion > android.os.Build.VERSION_CODES.LOLLIPOP && currentapiVersion < Build.VERSION_CODES.TIRAMISU) {
            RuntimePermissionCheckingObject = new RuntimePermissionChecking(mContext);
            StorageWritePermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.StorageWritePermissionString);
            if (StorageWritePermissionGiven) {
                makeDynamicLogoChangeProcess(companyLogoImageView);
            } else {
                companyLogoImageView.setImageResource(R.drawable.logo_splash);
            }
        }
        else if (currentapiVersion >= Build.VERSION_CODES.TIRAMISU) {
            RuntimePermissionCheckingObject = new RuntimePermissionChecking(mContext);
            if (CheckPermissions()) {
                makeDynamicLogoChangeProcess(companyLogoImageView);
            } else {
                ActivityCompat.requestPermissions(SplashActivity.this, permissions(), 1);
            }
        }
        else {
            makeDynamicLogoChangeProcess(companyLogoImageView);
        }
        Thread.setDefaultUncaughtExceptionHandler(new CustomExceptionHandler(getApplicationContext()));
        if (currentapiVersion > android.os.Build.VERSION_CODES.LOLLIPOP && currentapiVersion < Build.VERSION_CODES.TIRAMISU) {
            RuntimePermissionCheckingObject = new RuntimePermissionChecking(mContext);
            RunTimePermissionCheckingProcess();
        } else if (currentapiVersion >= Build.VERSION_CODES.TIRAMISU) {
            RuntimePermissionCheckingObject = new RuntimePermissionChecking(mContext);
            if (CheckPermissions()) {
                Log.d("TAG", "_DOWNLOAD_ call from onCreate 1");
                CreatingDatabaseFileGettingDeviceId();
//                takeUserToNextActivityAfterFiveSeconds();
            } else {
                ActivityCompat.requestPermissions(SplashActivity.this, permissions(), 1);
            }
        } else {
            Log.d("TAG", "_DOWNLOAD_ call from onCreate 2");
            CreatingDatabaseFileGettingDeviceId();
//            takeUserToNextActivityAfterFiveSeconds();
        }
        try {
            FirebaseMessaging.getInstance().getToken()
                    .addOnCompleteListener(task -> {
                        Log.d("TAG", "onComplete: "+task.getResult());
                        if (!task.isSuccessful()) {
                            print_Log_d("Fetching FCM registration token failed", Objects.requireNonNull(task.getException()).toString());
                            set_firebase_token(mContext, "dummy");
                            return;
                        }
                        set_firebase_token(mContext, task.getResult());
                    });
            print_Log_d("Refreshed_token_s ", get_firebase_token(mContext));
            Constants.remainderFlg = "t";
            Utils.isDevOn(mContext);
        } catch (Exception ignored) {
        }
    }
    private void deleteFullDatabaseAndFiles() {
        try {
            String dirPath = Utils.getAppStoragePath(mContext); // folder path
            File dir = new File(dirPath);

            if (dir.exists() && dir.isDirectory()) {
                deleteRecursive(dir);
                Log.d("TAG", "All database and files deleted successfully");
            } else {
                Log.d("TAG", "Directory not found");
            }

        } catch (Exception e) {
            e.printStackTrace();
            Log.e("TAG", "Error deleting database/files: " + e.getMessage());
        }
    }
    private void deleteRecursive(File fileOrDirectory) {
        if (fileOrDirectory.isDirectory()) {
            for (File child : fileOrDirectory.listFiles()) {
                deleteRecursive(child);
            }
        }
        fileOrDirectory.delete();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent resultData) {
        super.onActivityResult(requestCode, resultCode, resultData);
        Uri currentUri = null;
        if (resultCode == Activity.RESULT_OK) {
            if (requestCode == NEW_FOLDER_REQUEST_CODE) {
                if (resultData != null) {
                    currentUri = resultData.getData();
                    DocumentFile pickedDir = DocumentFile.fromTreeUri(this, currentUri);
                    pickedDir.createFile("", "/AceDns.db");
                    Log.d("TAG", "_DOWNLOAD_ call from onActivityResult");
                    takeUserToNextActivityAfterFiveSeconds();
                }
            }
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (grantResults.length > 0 && grantResults[0] != PackageManager.PERMISSION_GRANTED) {
            Toast.makeText(mContext, "You will not be able to use Star SFA if you do not accept all the permissions", Toast.LENGTH_SHORT).show();
        }
        if (currentapiVersion < Build.VERSION_CODES.TIRAMISU) {
            RunTimePermissionCheckingProcess();
        }
        if (currentapiVersion >= Build.VERSION_CODES.TIRAMISU) {
            if (CheckPermissions()) {
                Log.d("TAG", "_DOWNLOAD_ call from onCreate 3");
                CreatingDatabaseFileGettingDeviceId();
                Log.d("TAG", "_DOWNLOAD_ call from onRequestPermissionsResult");
                takeUserToNextActivityAfterFiveSeconds();
            }
        }
    }

    private void makeDynamicLogoChangeProcess(ImageView companyLogoImageView) {
        File dbFile = new File(Utils.getAppStoragePath(mContext) + "AceDns.db");
        if (dbFile.exists()) {
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            AppInfo mAppInfoObj = mAceDnsDatabase.getAppInfo();
            if (mAppInfoObj != null) {
                Constants.logoBmp = BitmapFactory.decodeByteArray(mAppInfoObj.getLogo(), 0, mAppInfoObj.getLogo().length);
                companyLogoImageView.setImageBitmap(Constants.logoBmp);
            } else {
                companyLogoImageView.setImageResource(R.drawable.logo_splash);
            }
        } else {
            companyLogoImageView.setImageResource(R.drawable.logo_splash);
        }
    }

    private void RunTimePermissionCheckingProcess() {
        StorageWritePermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.StorageWritePermissionString);
        StorageReadPermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.StorageReadPermissionString);
        LocationPermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.LocationPermissionString);
        CameraPermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.CameraPermissionString);
        PhonePermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.PhonePermissionString);
        ContactsPermissionGiven = RuntimePermissionCheckingObject.isPermissionGiven(Constants.ContactPermissionString);
        if (StorageWritePermissionGiven && StorageReadPermissionGiven && LocationPermissionGiven && CameraPermissionGiven && PhonePermissionGiven) {
            // Check whether the external storage present or not
            Log.d("TAG", "_DOWNLOAD_ call from onCreate 4");
            CreatingDatabaseFileGettingDeviceId();
            Log.d("TAG", "_DOWNLOAD_ call from RunTimePermissionCheckingProcess");
//            takeUserToNextActivityAfterFiveSeconds();
        } else {

            if (!StorageWritePermissionGiven) {
                requestStoragePermission(Constants.StorageWritePermissionString);
            } else if (!StorageReadPermissionGiven) {
                requestStoragePermission(Constants.StorageReadPermissionString);
            } else if (!LocationPermissionGiven) {
                requestStoragePermission(Constants.LocationPermissionString);
            } else if (!CameraPermissionGiven) {
                requestStoragePermission(Constants.CameraPermissionString);
            } else {
                requestStoragePermission(Constants.PhonePermissionString);
            }
        }
    }

    private void CreatingDatabaseFileGettingDeviceId() {
        isSDPresent = android.os.Environment.getExternalStorageState().equals(android.os.Environment.MEDIA_MOUNTED);
        File dbFile = new File(Utils.getAppStoragePath(mContext) + "AceDns.db");

        if (dbFile.exists()) {
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
            if (Constants.employeeDetailObject != null) {
                Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
            }
            Log.d("TAG", "_DOWNLOAD_ call from CreatingDatabaseFileGettingDeviceId");
            takeUserToNextActivityAfterFiveSeconds();
        } else {
            try {
                if (isSDPresent) {
                    String dirName = Utils.getAppStoragePath(mContext);
                    File dir = new File(dirName);
                    if (!dir.exists()) {
                        dir.mkdirs();
                    }
                    File fileName = new File(Utils.getAppStoragePath(mContext) + "AceDns.db");
                    if (!fileName.exists())
                        fileName.createNewFile();

                } else {
                    Utils.directOutsideTheApplication(SplashActivity.this, "No Storage Found.\nContact admin.", false);
                }
            } catch (Exception e) {
                Utils.directOutsideTheApplication(SplashActivity.this, e.getMessage() + " Error in creating file \n Please relogin.", false);
            }
        }
    }

    private void takeUserToNextActivityAfterFiveSeconds() {
        mHandler.postDelayed(() -> {
            if (isSDPresent) {
                try {
                    Utils.isDevOn(mContext);
                    if (Constants.isDeveloperOn && !BuildConfig.DEBUG) {
                        Utils.showToast(mContext, "Please Disable Developer mode");
                    } else {
                        Log.d("TAG", "_DOWNLOAD_ calling takeUserToNextActivityAfterFiveSeconds");
                        new TRANS_CheckingData_AsyncTask(this).execute();
//                        gotoLoginPage();
                    }
                } catch (Exception ignored) {}
            } else {
                Utils.directOutsideTheApplication(SplashActivity.this, "No DeviceId/SDCard.Contact Admin.", false);
            }
        }, 3000);
    }

    private void requestStoragePermission(String permissionType) {
        int permissionCode = 0;
        if (currentapiVersion < Build.VERSION_CODES.TIRAMISU) {
            if (permissionType.matches(Constants.LocationPermissionString)) {
                permissionType = Manifest.permission.ACCESS_FINE_LOCATION;
                permissionCode = Constants.LocationPermissionId;
            } else if (permissionType.matches(Constants.StorageWritePermissionString)) {
                permissionType = Manifest.permission.WRITE_EXTERNAL_STORAGE;
                permissionCode = Constants.StorageWritePermissionId;
            } else if (permissionType.matches(Constants.StorageReadPermissionString)) {
                permissionType = Manifest.permission.READ_EXTERNAL_STORAGE;
                permissionCode = Constants.StorageReadPermissionId;
            } else if (permissionType.matches(Constants.CameraPermissionString)) {
                permissionType = Manifest.permission.CAMERA;
                permissionCode = Constants.CameraPermissionId;
            } else if (permissionType.matches(Constants.PhonePermissionString)) {
                permissionType = Manifest.permission.READ_PHONE_STATE;
                permissionCode = Constants.PhonePermissionId;
            } else if (permissionType.matches(Constants.ContactPermissionString)) {
                permissionType = Manifest.permission.READ_CONTACTS;
                permissionCode = Constants.ContactPermissionId;
            }
            ActivityCompat.requestPermissions((Activity) mContext, new String[]{permissionType}, permissionCode);
        }
    }

    public boolean CheckPermissions() {
        boolean flg = false;
        int result1 = ContextCompat.checkSelfPermission(mContext, Manifest.permission.ACCESS_FINE_LOCATION);
        int result2 = ContextCompat.checkSelfPermission(mContext, Manifest.permission.ACCESS_COARSE_LOCATION);
        int result3 = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA);
        if ( result1 == PackageManager.PERMISSION_GRANTED && result2 == PackageManager.PERMISSION_GRANTED && result3 == PackageManager.PERMISSION_GRANTED) {

            flg = true;

        }
        return flg;
    }

    // new code

    public class TRANS_CheckingData_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckingData_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl+ "sfa_downtime_api.php";
                    Log.d("TAG", "_DOWNLOAD_ URL 1: "+url);
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONObject obj = new JSONObject(result);
                if (obj.getString("app_status").trim().equalsIgnoreCase("start")) {
                    Log.d("TAG", "_DOWNLOAD_ 2: ");
//                    gotoLoginPage();
                    new TRANS_CheckingAppVersion_AsyncTask(mContext).execute();
                } else {
                    new TRANS_CheckingAppVersion_AsyncTask(mContext).execute();
                    maintenancePopupLayout.setVisibility(View.VISIBLE);
                    if (obj.getString("is_link_available").trim().equalsIgnoreCase("n")) {
                        btnDesignLayout.setVisibility(View.GONE);
                    } else {
                        link = obj.getString("body_link").trim();
                        btnDesignLayout.setVisibility(View.VISIBLE);
                    }
                    popupMessage.setText(obj.getString("body_message").trim());
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ 1 : "+e.getMessage());
//                gotoLoginPage();
                new TRANS_CheckingAppVersion_AsyncTask(mContext).execute();
            }
        }
    }

    public class TRANS_CheckingAppVersion_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckingAppVersion_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl+ AceDnsWebServiceURL.updateCheckURL;
                    Log.d("TAG", "_DOWNLOAD_ URL 2: "+url);
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                if(result.substring(0, 1).equalsIgnoreCase("4")){
                    if(Utils.getAppVersion(mContext).equalsIgnoreCase(result.split("/")[1])){
                        gotoLoginPage();
                    }else{
                        Utils.showToast(mContext, "Please update the app from Play Store without uninstalling it.");
                        try {
                            mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.forcepower.starsfa")));
                        } catch (Exception anfe) {
                            mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + "com.forcepower.starsfa")));
                        }
                    }
                }else{
                    gotoLoginPage();
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ 3 : "+e.getMessage());
            }
        }
    }

    private void gotoLoginPage() {
        Log.d("TAG", "_DOWNLOAD_ call gotoLoginPage function");
        startActivity(new Intent(SplashActivity.this, LoginActivity.class));
//        finish();
    }

    private void init() {
        maintenancePopupLayout = findViewById(R.id.maintenancePopupLayout);
        btnDesignLayout = findViewById(R.id.btnDesignLayout);
        openWebsiteButton = findViewById(R.id.openWebsiteButton);
        popupMessage = findViewById(R.id.popupMessage);

        maintenancePopupLayout.setVisibility(View.GONE);
        btnDesignLayout.setVisibility(View.GONE);
        openWebsiteButton.setOnClickListener(view -> {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(link));
            try {
                startActivity(intent);
            } catch (Exception e) {
                Toast.makeText(SplashActivity.this, "No browser app found", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
