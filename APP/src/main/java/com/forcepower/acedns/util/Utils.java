package com.forcepower.acedns.util;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffXfermode;
import android.graphics.Rect;
import android.graphics.RectF;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.location.Location;
import android.location.LocationListener;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Message;
import android.provider.Settings;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.ActivityGoldenRulesSplashReport;
import com.forcepower.acedns.activity.CatalogueLandingActivity;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.activity.SchemesPdfActivity;
import com.forcepower.acedns.adapter.SchemePdfBranchSelectionAdapter;
import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;
import com.forcepower.acedns.backgroundTask.commonAsyncTaskMasterOtp;
import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.bean.BranchWisePdfMaster;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProdQtyCustClassWiseTDDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetails;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import org.json.JSONObject;
import org.mozilla.javascript.Scriptable;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.math.BigDecimal;
import java.net.FileNameMap;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.StandardCharsets;
import java.text.DateFormat;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.ParsePosition;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import androidx.core.content.FileProvider;
import androidx.core.text.HtmlCompat;

import static android.view.View.GONE;

public class Utils implements LocationListener {
    public static ProgressDialog loaderDialog;
    static boolean canShowToast = true;
    static AceDnsDatabase dbHelper;
    static LocationListener mLocationListner;


    public static Date getStartOfFinancialYear() {
        Date date=null;
        int CurrentYear = Calendar.getInstance().get(Calendar.YEAR);
        int CurrentMonth = (Calendar.getInstance().get(Calendar.MONTH)+1);
        String financiyalYearFrom="";
//        String financiyalYearTo="";
        if (CurrentMonth<4) {
            financiyalYearFrom="01-04-"+(CurrentYear-1);
//            financiyalYearTo="31-03-"+(CurrentYear);
        } else {
            financiyalYearFrom="01-04-"+CurrentYear;
//            financiyalYearTo="31-03-"+(CurrentYear+1);
        }
        SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        try {
             date = format.parse(financiyalYearFrom);
            System.out.println(date);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return date;
    }
    public static void showToast(Context mContext, String msg) {
        if (canShowToast) {
            Toast toast = Toast.makeText(mContext, msg, Toast.LENGTH_LONG);
            toast.setGravity(Gravity.CENTER, 0, 0);
            toast.show();
        }
    }


    public static void showColorToast(Context mContext, String msg){
        Toast toast = new Toast(mContext);
        toast.setDuration(Toast.LENGTH_LONG);

        LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View view = inflater.inflate(R.layout.toast_layout, null);
        TextView txt = view.findViewById(R.id.txt_toast);
        toast.setGravity(Gravity.CENTER_VERTICAL | Gravity.CENTER_HORIZONTAL, 0, 0);
        toast.setView(view);
        txt.setText(msg);
        toast.show();
    }


    public String removeLastChar(String s) {

            s = s.substring(0, s.length()-1);

        return s;
    }
    public static void showProgressDialog(Context mContext, String msg) {
        if (loaderDialog == null) {
            loaderDialog = new ProgressDialog(mContext);
            loaderDialog.setCancelable(false);
            loaderDialog.setMessage(msg);
            loaderDialog.show();
        }
    }
    public static void storeImageInLocalStorageShowOnImageView(Bitmap customerPicBitmap,String imagePath)
            throws FileNotFoundException
    {
        File outputFile = null;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out = null;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);

    }
    public static void writeDebugData(String data,Context context)  {
        try{
            data="\n"+data;
            File myFile = new File(Utils.getAppStoragePath(context)+"debugdata.txt");
            if (!myFile.exists())
                myFile.createNewFile();

            FileOutputStream fOut = new FileOutputStream(myFile, true);
            OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
            myOutWriter.append(data);
            myOutWriter.close();
            fOut.close();
        }
        catch (Exception e){

        }

    }
    public static void cancelProgressDialog()
    {

        if (loaderDialog != null && loaderDialog.isShowing())
        {
            loaderDialog.dismiss();
            loaderDialog = null;
        }
    }

    public static void changeProgressDialogMsg(Context mContext, String msg) {
        if (loaderDialog != null) {
            loaderDialog.setMessage(msg);
        } else {
            showProgressDialog(mContext, msg);
        }
    }

    public static String checkLibraryConditions(Context mContext) {
        String status = "";
        mLocationListner = new Utils();
        boolean locationStatus = true, mobileDataStatus = false, unknownResourceStatus = false, dateTimeStatus = false;
        // CHECK 1
//        if (AceDnsLibrary.turnOnLocationProvider(mContext, mLocationListner) == 1) {
//            locationStatus = true;
//        } else {
//            locationStatus = false;
//            Utils.showToast(mContext, "Please turn on Location Sharing");
//        }

        // CHECK 2
        if (HTTPUtils.isConnectionPossible(mContext)) {
            mobileDataStatus = true;
        } else {
            try {
                if (AceDnsLibrary.turnOnMobileDataEnable(mContext, true)) {
                    mobileDataStatus = true;
                }
            } catch (Exception e) {
                mobileDataStatus = false;
                Utils.showToast(mContext, "Please turn on Mobile Data");
            }
        }
        // CHECK 3
//        if (AceDnsLibrary.turnOnInstallUnknownResourceAppliation(mContext) == 1) {
            unknownResourceStatus = true;
//        } else {
//            unknownResourceStatus = false;
//            Utils.showToast(mContext, "Please check Install Unknown Resource Application");
//        }
        // CHECK 4
        try {
            AceDnsLibrary.turnOnAutomaticDateTime(mContext);
            dateTimeStatus = true;
        } catch (Exception e) {
            dateTimeStatus = false;
        }

        if (locationStatus && mobileDataStatus && unknownResourceStatus
                && dateTimeStatus) {
            status = "ALL OKK";
        } else {
            if (!locationStatus) {
                status = "GPS Error";
            }
            if (!mobileDataStatus) {
                status = status + " " + "Mobile Data Error";
            }
            if (!unknownResourceStatus) {
                status = status + " " + "Unknown Resource Error";
            }
            if (!dateTimeStatus) {
                status = status + " " + "Auto Date&Time Error";
            }
        }
        return status;
    }


    public static void ShowAlertDialog(final Context context, final String Message) {

        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(context);
        alertDialogBuilder
                .setMessage(Message)
                .setCancelable(false)
                .setPositiveButton("   OK   ", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                        Intent intent = new Intent(context, MenuActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        context.startActivity(intent);

                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.setTitle("This number is already registered");
        alertDialog.show();
    }
    public static void showAlertDialogMaterial(String alert,Context mContext,String title)
    {
        final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.CustomMaterialDialogTheme);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.alert_dialog_material);
        incotermsSelectionDialog.setCancelable(false);

        Button btn_cncl =  incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
//        TextView title = incotermsSelectionDialog.findViewById(R.id.title);
//        title.setText("Pending Bargain List");
        TextView titleTV = incotermsSelectionDialog.findViewById(R.id.title);
        TextView alertTv = incotermsSelectionDialog.findViewById(R.id.text);
        alertTv.setText(alert);
        titleTV.setText(title);
        incotermsSelectionDialog.show();
    }
    public static void ShowAlertDialogCommon(final Context context, final String Title, final String Message, String buttonText) {
        final Dialog commonDialog = new Dialog(context, R.style.PauseDialog);
        commonDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        commonDialog.setContentView(R.layout.dialog_common_single_button);
        commonDialog.setCancelable(true);
        TextView title = (TextView) commonDialog.findViewById(R.id.title);
        TextView messgaeTV = (TextView) commonDialog.findViewById(R.id.messgaeTV);
        Button submit = (Button) commonDialog.findViewById(R.id.btn_submit);
        submit.setText(buttonText);
        title.setText(Title);

        messgaeTV.setText(HtmlCompat.fromHtml(Message,HtmlCompat.FROM_HTML_MODE_LEGACY));
        submit.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                commonDialog.dismiss();
            }
        });
        commonDialog.show();
    }

    public static void directOutsideTheApplication(final Context context,
                                                   final String Message, final boolean sendData) {

        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                context);
        alertDialogBuilder
                .setMessage(Message)
                .setCancelable(false)
                .setPositiveButton("   OK   ",
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int id) {
                                String[] msgArray = Message.split("\\n");
                                // String mailMessage = Message.replace("\\n",
                                // ",");
                                new DATA_EmailToDeveloperTask(
                                        context,
                                        Constants.employeeDetailObject != null ? Constants.employeeDetailObject
                                                .getEmpCode() : "NA",
                                        msgArray[0] + BaseUrl.baseUrl,
                                        Constants.employeeDetailObject != null ? Constants.employeeDetailObject
                                                .getEmpName() : "NA", sendData)
                                        .execute();
                                RegisterActivities.removeAllActivities();
                            }
                        });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    public static Bitmap decodeBitmapFromSdCard(String filePath){
        Bitmap bitmap = BitmapFactory.decodeFile(filePath);
        return bitmap;
    }
    public static String capitalizeWord(String str){
        String words[]=str.split("\\s");
        String capitalizeWord="";
        for(String w:words){
            String first=w.substring(0,1);
            String afterfirst=w.substring(1);
            capitalizeWord+=first.toUpperCase()+afterfirst+" ";
        }
        return capitalizeWord.trim();
    }
    public static boolean InitialiseSETUPTableData(Context mContext) {
        try {
            AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
            aceDnsDatabase.getMenuDetailsObj();
            aceDnsDatabase.getUserDetailsObj();
            aceDnsDatabase.getOrderFormDetailsObj();
            aceDnsDatabase.getProductDetailsObj();
            aceDnsDatabase.GETSurveyFormDetails();
            Constants.employeeDetailObject = aceDnsDatabase.getEmployeeObj();
            aceDnsDatabase.close();
            if (Constants.menuDetailsObj != null
                    && Constants.userDetailsObj != null
                    && Constants.orderFormDetailsObj != null
                    && Constants.productDetailsObj != null) {
                return true;
            }
            return false;
        } catch (Exception e) {
            return false;
        }

    }

    public static void insertToEmployeeMaster(Context mContext) {
        dbHelper = new AceDnsDatabase(mContext);
        dbHelper.insertToEmployeeMaster();
        dbHelper.closeDatabase();
    }

    public static void updateEmployeeMasterDate(Context mContext) {
        dbHelper = new AceDnsDatabase(mContext);
        dbHelper.updateEmployeeMaster();
        dbHelper.closeDatabase();
    }

    public static void updateEmployeeMasterFlag(Context mContext) {
        dbHelper = new AceDnsDatabase(mContext);
        dbHelper.updateEmployeeMasterFlag();
        dbHelper.closeDatabase();
    }

    public static boolean lastLoginSuccessfull(Context mContext) {
        dbHelper = new AceDnsDatabase(mContext);
        boolean bool = dbHelper.checkLastLoginSuccessfull();
        dbHelper.closeDatabase();
        return bool;
    }
    public static String getFileType(String  file)
    {
        String mimeType="";
        try
        {
            FileNameMap fileNameMap = URLConnection.getFileNameMap();
             mimeType = fileNameMap.getContentTypeFor(file);//application/pdf || video/mp4
//            InputStream is = new BufferedInputStream(new FileInputStream(file));
//            mimeType = URLConnection.guessContentTypeFromStream(is);
//            is.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return mimeType;
    }

    public static void openCatalogue(Context mContext)
    {
        String[] CatalogueArray1 = Constants.catalogueorSchemeVal.split("\\^");
        String fileName = CatalogueArray1[1];
        File pdfOrVideoFile = new File(Utils.getAppStoragePath(mContext) + CatalogueArray1[2] + "-" + fileName);
//        if(CatalogueArray1[1].contains("mp4") || getFileType(pdfOrVideoFile).contains("media"))
        if(getFileType(fileName).contains("video") || fileName.contains("mp4"))
        {
            Intent intent = new Intent(mContext, CatalogueLandingActivity.class);
            mContext.startActivity(intent);
            return;
        }
        int currentApiVersion = Build.VERSION.SDK_INT;
        if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP)
        {
            Intent intent = new Intent(mContext, CatalogueLandingActivity.class);
            mContext.startActivity(intent);
        }
        else
        {

            Intent target = new Intent(Intent.ACTION_VIEW);

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N)
            {
                target.setDataAndType(FileProvider.getUriForFile(mContext,
                        BuildConfig.APPLICATION_ID + ".provider",
                        pdfOrVideoFile), "application/pdf");
            } else {

                target.setDataAndType(Uri.fromFile(pdfOrVideoFile), "application/pdf");

            }

            target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

            Intent intent = Intent.createChooser(target, "Open File");
            try {
                mContext.startActivity(intent);
            } catch (ActivityNotFoundException e) {
                // Instruct the user to install a PDF reader here, or something
            }
        }
    }
    public static String getInstallationTime(Context context) {
        SimpleDateFormat dateFormat = new SimpleDateFormat(
                "dd-MM-yyyy HH:mm:ss");
        String installed = "";
        try {
            PackageManager pm = context.getPackageManager();
            ApplicationInfo appInfo = pm.getApplicationInfo("org.forcepower.acedns",
                    0);
            String appFile = appInfo.sourceDir;
            installed = dateFormat.format(new Date(new File(appFile)
                    .lastModified())); // Epoch Time
        } catch (Exception e) {
            System.out.println("Exception:::::::" + e);
        }
        return installed;
    }

    public static String getAppVersion(Context mContext) {
        String version = "1.0.0";
        try {
            PackageInfo pInfo = mContext.getPackageManager().getPackageInfo(mContext.getPackageName(), 0);
            version = pInfo.versionName;
        } catch (Exception e) {

        }
        return version;
    }

    public static String getAppStoragePath(Context mContext)
    {
        String path = "";
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q)//android 10 or later
            {
                path =mContext.getExternalFilesDir(null).getAbsolutePath()+ "/AceDnsDB/";
            }
            else
            {
                path = Environment.getExternalStorageDirectory().getAbsolutePath() + "/AceDnsDB/";
            }
        } catch (Exception e) {

        }
        return path;
    }

    public static String getAppStoragePathParent(Context mContext)
    {
        String path = "";
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q)
            {
                path =mContext.getExternalFilesDir(null).getAbsolutePath()+ "/";
            }
            else
            {
                path = Environment.getExternalStorageDirectory().getAbsolutePath() + "/";
            }
        } catch (Exception e) {

        }
        return path;
    }

    public static String getDBVersion(Context mContext) {
        String version = "0.0";
        try {
            dbHelper = new AceDnsDatabase(mContext);
            AppInfo infoObj = dbHelper.getAppInfo();
            if (infoObj != null) {
                version = infoObj.getDbVersion();
            }
        } catch (Exception e) {
        }
        return version;
    }

    public static String getDeviceId(Context mContext,String mEmployeeCodeOrPhoneNumber)
    {
        String deviceId="";
        try {
            TelephonyManager tm = (TelephonyManager) mContext.getSystemService(Context.TELEPHONY_SERVICE);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O)
            {
                if(Build.VERSION.SDK_INT >=Build.VERSION_CODES.Q)
                {
//                    deviceId =  UUID.randomUUID().toString();
                    deviceId = new SimpleDateFormat("yyyyMMddHHmmssSSSSSSS", Locale.getDefault()).format(new Date());
                    deviceId= Constants.nickName+mEmployeeCodeOrPhoneNumber+deviceId;
                }
                else
                {
                    deviceId = tm.getImei();
                }
            }
            else
            {
                deviceId= tm.getDeviceId();
            }

        } catch (SecurityException se) {
            deviceId = "";//this line is to run the app on simulator only
        }
        Constants.deviceId=deviceId;
        return deviceId;
    }
    public static Handler mPrepareSurveyHandler;
    public static void DownloadData(final Activity myActivity, final int task, final String params, final String operation_type)
    {
        new Thread()
        {
            public void run()
            {
                if(params.equalsIgnoreCase("OTP_menu_details") ||
                        params.equalsIgnoreCase("user_details") ||
                        params.equalsIgnoreCase("product_details"))
                {
//                    new commonAsyncTaskSETUPOtp(myActivity,params, operation_type);
                }


                //Master Table Starts From Here
                else if(params.equalsIgnoreCase("route_master") ||
                        params.equalsIgnoreCase("customer_master") ||
                        params.equalsIgnoreCase("product_master") ||
                        params.equalsIgnoreCase("product_sub_group_master") ||
                        params.equalsIgnoreCase("menu_access") ||
                        params.equalsIgnoreCase("DO_transaction") ||
                        params.equalsIgnoreCase("product_batch_relation") ||
                        params.equalsIgnoreCase("vehicle_data_download") ||
                        params.equalsIgnoreCase("vehicle_data_download_tracking") ||
                        params.equalsIgnoreCase("DO_despatch_details") ||
                        params.equalsIgnoreCase("check_list_gate_keeper2_out") ||
                        params.equalsIgnoreCase("check_list_security_out") ||
                        params.equalsIgnoreCase("emp_master"))
                {
                    new commonAsyncTaskMasterOtp(myActivity,params, operation_type);
                }



//                isFinished=false;
                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }
    public static Bitmap getCircleBitmap(Bitmap bitmap) {
        final Bitmap output = Bitmap.createBitmap(bitmap.getWidth(),
                bitmap.getHeight(), Bitmap.Config.ARGB_8888);
        final Canvas canvas = new Canvas(output);

        final int color = Color.RED;
        final Paint paint = new Paint();
        final Rect rect = new Rect(0, 0, bitmap.getWidth(), bitmap.getHeight());
        final RectF rectF = new RectF(rect);

        paint.setAntiAlias(true);
        canvas.drawARGB(0, 0, 0, 0);
        paint.setColor(color);
        canvas.drawOval(rectF, paint);

        paint.setXfermode(new PorterDuffXfermode(PorterDuff.Mode.SRC_IN));
        canvas.drawBitmap(bitmap, rect, rect, paint);

        bitmap.recycle();

        return output;
    }

    public static boolean isTimeAutomatic(Context c) {
        if (Constants.userDetailsObj.getcountry() == null || Constants.userDetailsObj.gettimeZone().matches(""))//failsafe
        {
            return true;
        } else {
            TimeZone tz = TimeZone.getDefault();
            TimeZone tzFromServer = TimeZone.getTimeZone(Constants.userDetailsObj.gettimeZone());

            if (tz.getDisplayName(false, TimeZone.SHORT).equals(tzFromServer.getDisplayName(false, TimeZone.SHORT))) {
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
                    return Settings.Global.getInt(c.getContentResolver(), Settings.Global.AUTO_TIME, 0) == 1;
                } else {
                    return android.provider.Settings.System.getInt(c.getContentResolver(), android.provider.Settings.System.AUTO_TIME, 0) == 1;
                }
            } else {
                return false;
            }
        }

    }

    public static void showSettingsAlertToChangeTimeZone(final Context context) {
        AlertDialog.Builder alertDialog = new AlertDialog.Builder(context);

        // Setting Dialog Title
        alertDialog.setTitle("Automatic date and time is off");

        // Setting Dialog Message
        alertDialog.setMessage("Automatic date time must be enabled and timezone must be " + TimeZone.getTimeZone(Constants.userDetailsObj.gettimeZone()).getDisplayName(false, TimeZone.SHORT) + " to use the app. Press OK to enable.");

        // On pressing Settings button
        alertDialog.setPositiveButton("OK", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                Intent intent = new Intent(android.provider.Settings.ACTION_DATE_SETTINGS);
                context.startActivity(intent);
            }
        });

        // on pressing cancel button
        alertDialog.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
            }
        });

        // Showing Alert Message
        alertDialog.show();
    }

    //
    public static String changeDateFormat(String inputDateFormat, String outputDateFormat, String inputDateString) {
        SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat);
        SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat);

        Date date = null;
        String outputDateString = null;

        try {
            date = inputFormat.parse(inputDateString);
            outputDateString = outputFormat.format(date);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return outputDateString;
    }

    public static String[] suffixes =
            //    0     1     2     3     4     5     6     7     8     9
            { "th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th",
                    //    10    11    12    13    14    15    16    17    18    19
                    "th", "th", "th", "th", "th", "th", "th", "th", "th", "th",
                    //    20    21    22    23    24    25    26    27    28    29
                    "th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th",
                    //    30    31
                    "th", "st" };


    public static String getCurrentDateTimeInGivenFormat(String inputDateFormat)
    {
       String date= new SimpleDateFormat(inputDateFormat).format(new Date());
      return date;
    }


    public static long DaybetweenDates(String date1,String date2,String pattern,String pattern2)
    {
        SimpleDateFormat sdf = new SimpleDateFormat(pattern,Locale.ENGLISH);
        SimpleDateFormat sdf2 = new SimpleDateFormat(pattern2,Locale.ENGLISH);
        Date Date1 = null,Date2 = null;
        try{
            Date1 = sdf.parse(date1);
            Date2 = sdf2.parse(date2);
        }catch(Exception e)
        {
            e.printStackTrace();
        }
        return (Date2.getTime() - Date1.getTime())/(24*60*60*1000);
    }

    public static String addDaysToDate(String inputDate,String dateFormat,int days)
    {
        SimpleDateFormat sdf = new SimpleDateFormat(dateFormat);
        Calendar c = Calendar.getInstance();
        try {
            c.setTime(sdf.parse(inputDate));
        } catch (ParseException e) {
            e.printStackTrace();
        }
        c.add(Calendar.DATE, days);  // number of days to add, can also use Calendar.DAY_OF_MONTH in place of Calendar.DATE
        return sdf.format(c.getTime());
    }

    public static String getTodaysDateInGivenFormat(String inputDateFormat)
    {
        Date c = Calendar.getInstance().getTime();

        SimpleDateFormat df = new SimpleDateFormat(inputDateFormat);
        String formattedDate = df.format(c);
        return formattedDate;
    }
    public static String getTomorrowsDate(String inputDateFormat)
    {
        Calendar calendar = Calendar.getInstance();

        calendar.add(Calendar.DAY_OF_YEAR, 1);
        Date tomorrow = calendar.getTime();
        DateFormat dateFormat = new SimpleDateFormat(inputDateFormat);
        return dateFormat.format(tomorrow);
    }

    //HH:mm:ss
    public static Boolean checkCurrentTimeIsWithinGivenRangeOrNot(String fromDate, String toDate, String format) {
        SimpleDateFormat Format = new SimpleDateFormat(format);

        Date FromTime = null;
        Date ToTime = null;
        Date currentTime = null;

        try {
            FromTime = Format.parse(fromDate);
            ToTime = Format.parse(toDate);
            currentTime = Format.parse(Constants.dateString + "-" + new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime()));
            if (currentTime.after(FromTime) && currentTime.before(ToTime)) {
                return true;
            } else {
                return false;
            }
        } catch (ParseException e) {
            e.printStackTrace();
            return false;
        }
//		return outputDateString;
    }
    public static Boolean checkIfToDateGraterThanFromDate(String fromDate, String toDate, String format)
    {
        SimpleDateFormat Format = new SimpleDateFormat(format);

        Date FromTime = null;
        Date ToTime = null;

        try {
            FromTime = Format.parse(fromDate);
            ToTime = Format.parse(toDate);
            if (ToTime.after(FromTime))
            {
                return true;
            }
            else
            {
                return false;
            }
        } catch (ParseException e) {
            e.printStackTrace();
            return false;
        }
//		return outputDateString;
    }

    public static String getPreviousMonthYearOfGivenDate(String inputDateFormat, String outputDateFormat, String inputDateString) {
        SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat);
        SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat);

        Date date = null;
        String outputDateString = null;

        try {
            date = inputFormat.parse(inputDateString);
            Calendar c = Calendar.getInstance();
            c.setTime(date);
            c.add(Calendar.MONTH, -1);
            date = c.getTime();
            outputDateString = outputFormat.format(date);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return outputDateString;
    }

    public static Date stringToDate(String aDate, String aFormat) {

        if (aDate == null) return null;
        ParsePosition pos = new ParsePosition(0);
        SimpleDateFormat simpledateformat = new SimpleDateFormat(aFormat);
        Date stringDate = simpledateformat.parse(aDate, pos);
        return stringDate;

    }

    public static Bitmap getResizedBitmap(Bitmap bm, int newWidth, int newHeight) {
        int width = bm.getWidth();
        int height = bm.getHeight();
        float scaleWidth = ((float) newWidth) / width;
        float scaleHeight = ((float) newHeight) / height;
        // CREATE A MATRIX FOR THE MANIPULATION
        Matrix matrix = new Matrix();
        // RESIZE THE BIT MAP
        matrix.postScale(scaleWidth, scaleHeight);

        // "RECREATE" THE NEW BITMAP
        Bitmap resizedBitmap = Bitmap.createBitmap(
                bm, 0, 0, width, height, matrix, false);
        bm.recycle();
        return resizedBitmap;
    }

    public static boolean doesQuantityMatchesQtySlab(String currentqty, String currentQtySlab) {
        Boolean QuantityMatchesQtySlab = false;
        org.mozilla.javascript.Context rhino = org.mozilla.javascript.Context.enter();
        rhino.setOptimizationLevel(-1);
        Scriptable scope = rhino.initStandardObjects();
        String[] splittedCondition = currentQtySlab.split("&&");
        if (splittedCondition.length == 1) {
            QuantityMatchesQtySlab = Boolean.valueOf(rhino.evaluateString(scope, currentqty + splittedCondition[0], "JavaScript", 1, null).toString());
        } else if (splittedCondition.length == 2) {
            Boolean QuantityMatchesQtySlabCondition1 = Boolean.valueOf(rhino.evaluateString(scope, currentqty + splittedCondition[0], "JavaScript", 1, null).toString());
            Boolean QuantityMatchesQtySlabCondition2 = Boolean.valueOf(rhino.evaluateString(scope, currentqty + splittedCondition[1], "JavaScript", 1, null).toString());
            if (QuantityMatchesQtySlabCondition1 && QuantityMatchesQtySlabCondition2) {
                QuantityMatchesQtySlab = true;
            }
        }
        return QuantityMatchesQtySlab;
    }
    public static boolean doesQuantityMatchesQtySlabModified(String currentqty, String currentQtySlab)// <=9:12;>=10&<=20:20;>=21&<=40:30;>=41;50
    {
        Boolean QuantityMatchesQtySlab = false;
        org.mozilla.javascript.Context rhino = org.mozilla.javascript.Context.enter();
        rhino.setOptimizationLevel(-1);
        Scriptable scope = rhino.initStandardObjects();
        if(currentQtySlab.contains("&"))
        {
            String[] splittedCondition = currentQtySlab.split("&");
            Boolean QuantityMatchesQtySlabCondition1 = Boolean.valueOf(rhino.evaluateString(scope, currentqty + splittedCondition[0], "JavaScript", 1, null).toString());
            Boolean QuantityMatchesQtySlabCondition2 = Boolean.valueOf(rhino.evaluateString(scope, currentqty + splittedCondition[1], "JavaScript", 1, null).toString());
            if (QuantityMatchesQtySlabCondition1 && QuantityMatchesQtySlabCondition2) {
                QuantityMatchesQtySlab = true;
            }
        }
        else
        {
            Boolean QuantityMatchesQtySlabCondition = Boolean.valueOf(rhino.evaluateString(scope, currentqty + currentQtySlab, "JavaScript", 1, null).toString());
            if ( QuantityMatchesQtySlabCondition) {
                QuantityMatchesQtySlab = true;
            }
        }

        return QuantityMatchesQtySlab;
    }

    public static boolean checkIfGivenQuantityEligibleForTD(Context context, String currentqty, String prodCode, String currentCustomerBranchCode, String currentCustomerClass) {
        Boolean GivenQuantityEligibleForTD = false;
        Double qtyInDouble = Double.parseDouble(currentqty);
        if (qtyInDouble > 0) {
            AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(context);
            ArrayList<ProdQtyCustClassWiseTDDetails> ProdQtyCustClassWiseTDDetailsList = mAceDnsDatabase.getProductQtyCustClassWiseTDForGivenProductCodeBranchCode(prodCode, currentCustomerBranchCode, currentCustomerClass);
            if (ProdQtyCustClassWiseTDDetailsList.size() > 0) {
                for (int i = 0; i < ProdQtyCustClassWiseTDDetailsList.size(); i++) {
                    String currentQtySlab = ProdQtyCustClassWiseTDDetailsList.get(i).getQtySlab();
                    if (doesQuantityMatchesQtySlab(currentqty, currentQtySlab)) {
                        Constants.currentTdPercent = ProdQtyCustClassWiseTDDetailsList.get(i).getTDPercent();
                        GivenQuantityEligibleForTD = true;
                    }
                }
            }

        }

        return GivenQuantityEligibleForTD;
    }

    public static boolean isNumeric(String str)
    {
        try {
            Double.parseDouble(str);
        } catch (Exception nfe) {
            return false;
        }
        return true;
    }
//    private static HashMap<String,ArrayList<commonDatabaseHelper>> sortMap( HashMap<String,ArrayList<commonDatabaseHelper>> unsortedMap)
//    {
//        List<Map.Entry<String,ArrayList<commonDatabaseHelper>>> list = new LinkedList<Map.Entry<String,ArrayList<commonDatabaseHelper>>(unsortedMap.entrySet());
//
//        Collections.sort(list,
//                new Comparator<Map.Entry<String, commonDatabaseHelper>>() {
//
//                    @Override
//                    public int compare(Map.Entry<String, commonDatabaseHelper> o1,
//                                       Map.Entry<String, commonDatabaseHelper> o2) {
//                        return o1.getValue().getItem0().compareTo(o1.getValue().getItem0());
//                    }
//                });
//
//        Map<String, ModelX.ContactModel> sortedMap = new LinkedHashMap<String, ModelX.ContactModel>();
//        for(Entry<String, ModelX.ContactModel> item : list){
//            sortedMap.put(item.getKey(), item.getValue());
//        }
//        return sortedMap;
//    }
    public static String toTitleCase(String str)
    {

        if (str == null) {
            return null;
        }

        boolean space = true;
        StringBuilder builder = new StringBuilder(str);
        final int len = builder.length();

        for (int i = 0; i < len; ++i) {
            char c = builder.charAt(i);
            if (space) {
                if (!Character.isWhitespace(c)) {
                    // Convert to title case and switch out of whitespace mode.
                    builder.setCharAt(i, Character.toTitleCase(c));
                    space = false;
                }
            } else if (Character.isWhitespace(c)) {
                space = true;
            } else {
                builder.setCharAt(i, Character.toLowerCase(c));
            }
        }

        return builder.toString();
    }

    public static String addAllItemsOfAnArray(String amount) {
        BigDecimal  finalAmount = BigDecimal.valueOf(0);
        if (amount != null && !amount.matches("null") && amount.contains(",")) {
            String[] amountArray = amount.split(",");
            for (String tempAmount : amountArray) {
                if (isNumeric(tempAmount)) {
                    BigDecimal bigDecimal = new BigDecimal(tempAmount);
                    finalAmount = finalAmount.add(bigDecimal) ;
                }
            }
        } else {
            if (isNumeric(amount))
            {
                BigDecimal bigDecimalamount = new BigDecimal(amount);
                finalAmount=finalAmount.add(bigDecimalamount) ;
            }

        }


        String s = finalAmount.toString();
        return s;
    }

    public static String addAllItemsOfAnArrayForOrder(String amount, SQLiteDatabase database) {
        double finalAmount = 0;
        if (amount != null && !amount.matches("null") && amount.contains(",")) {
            String[] amountArray = amount.split(",");
            String[] orderNoArray = Constants.mCurrentOrderNoList.split(",");
            int i = 0;
            for (String tempAmount : amountArray) {
                if (isNumeric(tempAmount)) {
                    double currentAmount = Double.parseDouble(tempAmount);
                    currentAmount = calculateTDForOrderValueAndPercentageWiseTD(currentAmount, orderNoArray[i], database);
                    finalAmount = finalAmount + currentAmount;
                }
                i++;
            }
        } else {
            if (isNumeric(amount)) {
                double currentAmount = Double.parseDouble(amount);
                currentAmount = calculateTDForOrderValueAndPercentageWiseTD(currentAmount, Constants.mCurrentOrderNoList, database);
                finalAmount = finalAmount + currentAmount;
            }
        }


        return String.valueOf(finalAmount);
    }

    private static double calculateTDForOrderValueAndPercentageWiseTD(double currentAmount, String order_no, SQLiteDatabase database) {
        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))
                && Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("percentage")) {
            Cursor cursor = null;
            String query = "SELECT TD from order_header where order_no ='" + order_no + "'";
            cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                String TDPercent = cursor.getString(0);
                if (isNumeric(TDPercent)) {
                    currentAmount = currentAmount - (currentAmount * Double.parseDouble(TDPercent)) / 100;
                }
            }
            cursor.close();
        }
        return currentAmount;
    }

    public static void SaveImageToExternalStorage(Bitmap finalBitmap, String filePath, String fileName) {
        File myDir = new File(filePath);
        myDir.mkdirs();
        File file = new File(myDir, fileName);
        if (file.exists())
            file.delete();
        try {
            FileOutputStream out = new FileOutputStream(file);
            finalBitmap.compress(Bitmap.CompressFormat.PNG, 100, out);
            out.flush();
            out.close();

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void showCommonAlertDialog(Context context, String title, String message) {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(context);
        AlertDG.setTitle(title);
        AlertDG.setMessage(message);
        AlertDG.setPositiveButton("OK", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
            }
        });

        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    public static Boolean checkIfRateIsProper(String rate) {
        if (!Utils.isNumeric(rate)) {
            return false;
        } else {
            double rateInDouble = Double.parseDouble(rate);
            {
                if (rateInDouble <= 0) {
                    return false;
                } else {
                    return true;
                }
            }

        }
    }

    public static String dayOfWeek() {
        try {
            Calendar calendar = Calendar.getInstance();
            Date date = calendar.getTime();
            // 3 letter name form of the day
            // System.out.println(new SimpleDateFormat("EE", Locale.ENGLISH).format(date.getTime()));//Sat
            // full name form of the day
            return new SimpleDateFormat("EEEE", Locale.ENGLISH).format(date.getTime());//Saturday
        } catch (Exception e) {
            return "";
        }

    }
    public static String  GetLastDayOfMonth (String dateFormat)
    {
            Date today = new Date();

            Calendar calendar = Calendar.getInstance();
            calendar.setTime(today);

            calendar.add(Calendar.MONTH, 1);
            calendar.set(Calendar.DAY_OF_MONTH, 1);
            calendar.add(Calendar.DATE, -1);
            Date lastDayOfMonth = calendar.getTime();

            DateFormat sdf = new SimpleDateFormat(dateFormat);
//            System.out.println("Today            : " + sdf.format(today));
//            System.out.println("Last Day of Month: " + sdf.format(lastDayOfMonth));

        return sdf.format(lastDayOfMonth);
    }

    public static ArrayList<String> daysOfWeekExceptToday() {
        ArrayList<String> days = new ArrayList<>();
        try {
            for (int i = 1; i < 7; i++) {
                Calendar calendar = Calendar.getInstance();
                calendar.add(Calendar.DAY_OF_YEAR, i);
                Date date = calendar.getTime();
                days.add(new SimpleDateFormat("EEEE", Locale.ENGLISH).format(date.getTime()));
            }

            return days;
        } catch (Exception e) {
            return days;
        }

    }

    public static String getDateOfSelectedDay(int numOfDaysToAdd) {
        try {
            Calendar calendar = Calendar.getInstance();
            calendar.add(Calendar.DAY_OF_YEAR, numOfDaysToAdd);
            Date foundDate = calendar.getTime();
            DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
            return dateFormat.format(foundDate);
        } catch (Exception e) {
            return "";
        }
    }

    public static String getDifferenceBetweenTwoDateTime(Date startDate, Date endDate) {
        String finalTimeDifference = "";
        try {
            //milliseconds
            long different = endDate.getTime() - startDate.getTime();
            long secondsInMilli = 1000;
            long minutesInMilli = secondsInMilli * 60;
            long hoursInMilli = minutesInMilli * 60;
            long daysInMilli = hoursInMilli * 24;

//		long elapsedDays = different / daysInMilli;
//		different = different % daysInMilli;
//
            long elapsedHours = different / hoursInMilli;
            different = different % hoursInMilli;

            long elapsedMinutes = different / minutesInMilli;
            different = different % minutesInMilli;

            long elapsedSeconds = different / secondsInMilli;

            finalTimeDifference = new DecimalFormat("00").format(elapsedHours) + ":" + new DecimalFormat("00").format(elapsedMinutes) + ":" + new DecimalFormat("00").format(elapsedSeconds);
        } catch (Exception e) {

        }

        return finalTimeDifference;

    }

    public static Boolean NotCheckedOut(Context context) {
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(context);
        boolean checkInOut = mAceDnsDatabase.MenuAccess("check_in_out");
//        String check_str = PreferenceData.getCheckInOutId(context);
//        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && checkInOut == true && check_str.equalsIgnoreCase("uncheck"))
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && checkInOut)
        {
            return mAceDnsDatabase.isUserCheckedin();
        }
        else
        {
            return false;
        }
    }

    public static int getPositionOfCurrentCheckedInRoute(Boolean isRoutePlanYes, Context context, ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday, ArrayList<RouteDetails> mRouteDetailsList) {
        int position = 0;
        if (isRoutePlanYes) {
            for (int i = 0; i < mRoutePlanListofToday.size(); i++) {
                if (mRoutePlanListofToday.get(i).getRoutecode().matches(PreferenceData.getCheckInOutRouteCode(context))) {
                    position = i;
                }
            }
        } else {
            for (int i = 0; i < mRouteDetailsList.size(); i++) {
                if (mRouteDetailsList.get(i).getRouteCode().matches(PreferenceData.getCheckInOutRouteCode(context))) {
                    position = i;
                }
            }
        }

        return position;
    }

    public static int getPositionOfCurrentCheckedInRouteInvioiceWise(Boolean isRoutePlanYes, Context context, ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday, ArrayList<RouteDetails> mRouteDetailsList)
    {
        int position = -1;
        try
        {
            if (isRoutePlanYes) {
                for (int i = 0; i < mRoutePlanListofToday.size(); i++) {
                    if (mRoutePlanListofToday.get(i).getRoutecode().matches(PreferenceData.getCheckInOutRouteCode(context))) {
                        position = i;
                    }
                }
            } else {
                for (int i = 0; i < mRouteDetailsList.size(); i++) {
                    if (mRouteDetailsList.get(i).getRouteCode().matches(PreferenceData.getCheckInOutRouteCode(context))) {
                        position = i;
                    }
                }
            }
        }
        catch (Exception e)
        {

        }



        return position;
    }

    public static int getPositionOfCurrentCheckedInCustomer(Context context, ArrayList<CustomerDetails> mCustomerDetailsList) {
        int position = 0;
        for (int i = 0; i < mCustomerDetailsList.size(); i++) {
            if (mCustomerDetailsList.get(i).getCustomerCode().matches(PreferenceData.getCheckInOutEmpCode(context))) {
                position = i;
            }
        }


        return position;
    }

    public static int getPositionOfCurrentCheckedInCustomerInvoiceWise(Context context, ArrayList<CustomerDetails> mCustomerDetailsList) {
        int position = -1;
        for (int i = 0; i < mCustomerDetailsList.size(); i++) {
            if (mCustomerDetailsList.get(i).getCustomerCode().matches(PreferenceData.getCheckInOutEmpCode(context))) {
                position = i;
            }
        }
        return position;
    }

    //check if Customer Wise self appraisal type is enabled from setup or not
    public static Boolean currentCustomerWiseSelfAppraisalPresentInSetup(Context context) {
        Boolean isGivenAppraisal = false;
        try {
            AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(context);
            new SelfAppraisalDetails();
            SelfAppraisalDetails selfAppraisalSetup = mAceDnsDatabase.getTargetAchievementSetupDetails();
            String multipleTargetAchievement = selfAppraisalSetup.getMultipleTargetAchievement();
            if (multipleTargetAchievement.matches("yes")) {
                String multipleTargetAchievementVal = selfAppraisalSetup.getMultipleTargetAchievementVal();
                if (multipleTargetAchievementVal.contains("customer_wise")) {
                    isGivenAppraisal = true;
                }
            } else if (selfAppraisalSetup.getCustomerWise().contains("yes")) {
                isGivenAppraisal = true;
            }
        } catch (Exception e) {

        }
        return isGivenAppraisal;
    }

    public static void addQtyProductToList(SchemeFreebiesDetails item, double qtyBought, double amntBought, Context ctx) {
        if (!item.getqty().matches("0")) {

            qtyBought = convertQtyFromInputUomToOfferUom(qtyBought, item, ctx);
//			if(qtyBought>=Double.parseDouble(item.getqty()))
            if (doesQuantityMatchesQtySlab(qtyBought + "", item.getqty())) {
                int freeItemsTimes = (int) (qtyBought / Double.parseDouble(item.getqty().replaceAll("[^\\d.]", "")));//.replaceAll("[^\\d.]", "") remove non numeric characters
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToList(item, freeItemNumber);
//				calculateAndAddToList(item,Double.parseDouble(item.getFreebieQty()));

            } else {
                removeFreebieFromList(item);
            }

        } else {
            if (amntBought >= Double.parseDouble(item.getamount())) {
                int freeItemsTimes = (int) (amntBought / Double.parseDouble(item.getamount()));
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToList(item, freeItemNumber);
            } else {
                removeFreebieFromList(item);

            }
        }
        if (Constants.freeBiesAdapter != null) {
            Constants.freeBiesAdapter.notifyDataSetChanged();
        }
    }


    public static void addQtyProductToList(SchemeFreebiesDetails item, double qtyBought, double amntBought,String weightage, Context ctx) {
        if (!item.getqty().matches("0")) {

            qtyBought = convertQtyFromInputUomToOfferUom(qtyBought, item, ctx);
//			if(qtyBought>=Double.parseDouble(item.getqty()))
            if (doesQuantityMatchesQtySlab(qtyBought + "", item.getqty())) {
                int freeItemsTimes = (int) (qtyBought / Double.parseDouble(item.getqty().replaceAll("[^\\d.]", "")));//.replaceAll("[^\\d.]", "") remove non numeric characters
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToList(item, freeItemNumber,weightage);
//				calculateAndAddToList(item,Double.parseDouble(item.getFreebieQty()));

            } else {
                removeFreebieFromList(item);
            }

        } else {
            if (amntBought >= Double.parseDouble(item.getamount())) {
                int freeItemsTimes = (int) (amntBought / Double.parseDouble(item.getamount()));
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToList(item, freeItemNumber,weightage);
            } else {
                removeFreebieFromList(item);

            }
        }
        if (Constants.freeBiesAdapter != null) {
            Constants.freeBiesAdapter.notifyDataSetChanged();
        }
    }

    public static void addQtyProductToListWithRemarks(SchemeFreebiesDetails item, double qtyBought, double amntBought,String weightage, Context ctx,String remarks) {
        if (!item.getqty().matches("0")) {

            qtyBought = convertQtyFromInputUomToOfferUom(qtyBought, item, ctx);
//			if(qtyBought>=Double.parseDouble(item.getqty()))
            if (doesQuantityMatchesQtySlab(qtyBought + "", item.getqty())) {
                int freeItemsTimes = (int) (qtyBought / Double.parseDouble(item.getqty().replaceAll("[^\\d.]", "")));//.replaceAll("[^\\d.]", "") remove non numeric characters
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToListWithRemarks(item, freeItemNumber,weightage,remarks);
//				calculateAndAddToList(item,Double.parseDouble(item.getFreebieQty()));

            } else {
                removeFreebieFromList(item);
            }

        } else {
            if (amntBought >= Double.parseDouble(item.getamount())) {
                int freeItemsTimes = (int) (amntBought / Double.parseDouble(item.getamount()));
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToListWithRemarks(item, freeItemNumber,weightage,remarks);
            } else {
                removeFreebieFromList(item);

            }
        }
        if (Constants.freeBiesAdapter != null) {
            Constants.freeBiesAdapter.notifyDataSetChanged();
        }
    }

    public static void addQtyProductToListWithRemarks(SchemeFreebiesDetails item, double qtyBought, double amntBought, Context ctx,String remarks) {
        if (!item.getqty().matches("0")) {

            qtyBought = convertQtyFromInputUomToOfferUom(qtyBought, item, ctx);
//			if(qtyBought>=Double.parseDouble(item.getqty()))
            if (doesQuantityMatchesQtySlab(qtyBought + "", item.getqty())) {
                int freeItemsTimes = (int) (qtyBought / Double.parseDouble(item.getqty().replaceAll("[^\\d.]", "")));//.replaceAll("[^\\d.]", "") remove non numeric characters
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToListWithRemarks(item, freeItemNumber,remarks);
//				calculateAndAddToList(item,Double.parseDouble(item.getFreebieQty()));

            } else {
                removeFreebieFromList(item);
            }

        } else {
            if (amntBought >= Double.parseDouble(item.getamount())) {
                int freeItemsTimes = (int) (amntBought / Double.parseDouble(item.getamount()));
                double freeItemNumber = freeItemsTimes * Double.parseDouble(item.getFreebieQty());
                calculateAndAddToListWithRemarks(item, freeItemNumber,remarks);
            } else {
                removeFreebieFromList(item);

            }
        }
        if (Constants.freeBiesAdapter != null) {
            Constants.freeBiesAdapter.notifyDataSetChanged();
        }
    }


    public static double convertQtyFromInputUomToOfferUom(double qtyBought, SchemeFreebiesDetails item, Context ctx) {
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(ctx);
        String OfferUom = item.getProductUom();
        String currentProductFilter = item.getscheme_filter();
        if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom1")) {
            if (OfferUom.equalsIgnoreCase("uom2"))//uom1 to uom2
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom3"))//uom1 to uom3
            {
                qtyBought = qtyBought / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//uom1 to pack_unit
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);///uom1 to uom2
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);//uom2 to pack_unit
            }
        } else if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
            if (OfferUom.equalsIgnoreCase("uom1"))//uom2 to uom1
            {
                qtyBought = qtyBought / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom3"))//uom2 to uom3
            {
                qtyBought = qtyBought / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom2 to uom1
                qtyBought = qtyBought / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom1 to uom3
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//uom2 to pack_unit
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);
            }
        } else if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom3")) {
            if (OfferUom.equalsIgnoreCase("uom1"))//uom3 to uom1
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom2"))//uom3 to uom2
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom3 to uom1
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom1 to uom2
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//uom3 to pack_unit
            {
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom3 to uom1
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom1 to uom2
                qtyBought = qtyBought * mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);//uom2 to pack_unit
            }
        }

        return qtyBought;
    }

    public static double convertQtyFromOfferUomToInputUom(double minQtyToGetOffer, SchemeFreebiesDetails item, Context ctx) {
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(ctx);
        String OfferUom = item.getProductUom();
        String currentProductFilter = item.getscheme_filter();
        if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom1")) {
            if (OfferUom.equalsIgnoreCase("uom2"))//uom2 to uom1
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom3"))//uom3 to uom1
            {
                minQtyToGetOffer = minQtyToGetOffer * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//pack_unit to uom1
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);//pack_unit to uom2
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);
            }
        } else if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
            if (OfferUom.equalsIgnoreCase("uom1"))//uom1 to uom2
            {
                minQtyToGetOffer = minQtyToGetOffer * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom3"))//uom3 to uom2
            {
                minQtyToGetOffer = minQtyToGetOffer * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom3 to uom1
                minQtyToGetOffer = minQtyToGetOffer * mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom1 to uom2
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//pack_unit to uom2
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);
            }
        } else if (Constants.uomToBeShownForProduct.equalsIgnoreCase("uom3")) {
            if (OfferUom.equalsIgnoreCase("uom1"))//uom1 to uom3
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);
            } else if (OfferUom.equalsIgnoreCase("uom2"))//uom2 to uom3
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom2 to uom1
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom1 to uom3
            } else if (OfferUom.equalsIgnoreCase("pack_unit"))//pack_unit to uom3
            {
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("pack_size", item.getprodCode(), currentProductFilter);//pack_unit to uom2
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor", item.getprodCode(), currentProductFilter);//uom2 to uom1
                minQtyToGetOffer = minQtyToGetOffer / mAceDnsDatabase.conversionfactorForCurrentProduct("conversion_factor_two", item.getprodCode(), currentProductFilter);//uom1 to uom3
            }
        }


        return minQtyToGetOffer;
    }

    private static void removeFreebieFromList(SchemeFreebiesDetails item) {
        removeAmnt(item);
        if (!Constants.selectedProductMasterListFreebies.isEmpty()) {
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListFreebies.get(i);
//                if(currentItem.getProdCode().matches(item.getfreebiesProdCode()))
                if (currentItem.getSchemeIds().matches(item.getschemeId())) {
                    Constants.selectedProductMasterListFreebies.remove(i);
                    break;
                }
            }
        }
    }

    public static void calculateAndAddToList(SchemeFreebiesDetails item, double freeItemNumber) {
        addSchemeToSchemeSummaryList(item);
        ProductMasterDetails freeBieProducts = new ProductMasterDetails();
        freeBieProducts.setProdCode(item.getfreebiesProdCode());
        freeBieProducts.setDesc(item.getfreebiesProdDesc());
        freeBieProducts.setSchemeIds(item.getschemeId());
        freeBieProducts.setfreeBieUom(item.getfreebieUomDisplayValue());
        freeBieProducts.setfreebieFilter(item.getscheme_filter());
        freeBieProducts.setQty(Constants.defaultFormat.format(freeItemNumber));
        if (Constants.selectedProductMasterListFreebies.isEmpty()) {
            Constants.selectedProductMasterListFreebies.add(freeBieProducts);
        } else {
            Boolean isSchemeAlreadyPresent = false;
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListFreebies.get(i);
                String prodDesc = currentItem.getDesc();
                String freeBieProdDesc = item.getfreebiesProdDesc();
                if (currentItem.getSchemeIds().matches(item.getschemeId()) && prodDesc.equalsIgnoreCase(freeBieProdDesc)) {
                    Constants.selectedProductMasterListFreebies.set(i, freeBieProducts);
                    isSchemeAlreadyPresent = true;
                    break;
                }
            }
            if (!isSchemeAlreadyPresent) {
                Constants.selectedProductMasterListFreebies.add(freeBieProducts);
            }
        }
    }

    public static void calculateAndAddToList(SchemeFreebiesDetails item, double freeItemNumber, String weightage) {
        addSchemeToSchemeSummaryList(item);
        ProductMasterDetails freeBieProducts = new ProductMasterDetails();
        freeBieProducts.setProdCode(item.getfreebiesProdCode());
        freeBieProducts.setDesc(item.getfreebiesProdDesc());
        freeBieProducts.setSchemeIds(item.getschemeId());
        freeBieProducts.setfreeBieUom(item.getfreebieUomDisplayValue());
        freeBieProducts.setfreebieFilter(item.getscheme_filter());
        freeBieProducts.setQty(Constants.defaultFormat.format(freeItemNumber));
        freeBieProducts.setWeightage(weightage);
        if (Constants.selectedProductMasterListFreebies.isEmpty()) {
            Constants.selectedProductMasterListFreebies.add(freeBieProducts);
        } else {
            Boolean isSchemeAlreadyPresent = false;
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListFreebies.get(i);
                String prodDesc = currentItem.getDesc();
                String freeBieProdDesc = item.getfreebiesProdDesc();
                if (currentItem.getSchemeIds().matches(item.getschemeId()) && prodDesc.equalsIgnoreCase(freeBieProdDesc)) {
                    Constants.selectedProductMasterListFreebies.set(i, freeBieProducts);
                    isSchemeAlreadyPresent = true;
                    break;
                }
            }
            if (!isSchemeAlreadyPresent) {
                Constants.selectedProductMasterListFreebies.add(freeBieProducts);
            }
        }
    }

    public static void calculateAndAddToListWithRemarks(SchemeFreebiesDetails item, double freeItemNumber,String remarks) {
        addSchemeToSchemeSummaryList(item);
        ProductMasterDetails freeBieProducts = new ProductMasterDetails();
        freeBieProducts.setProdCode(item.getfreebiesProdCode());
        freeBieProducts.setDesc(item.getfreebiesProdDesc());
        freeBieProducts.setSchemeIds(item.getschemeId());
        freeBieProducts.setfreeBieUom(item.getfreebieUomDisplayValue());
        freeBieProducts.setfreebieFilter(item.getscheme_filter());
        freeBieProducts.setQty(Constants.defaultFormat.format(freeItemNumber));
        freeBieProducts.setOrder_wise_remarks(remarks);
        if (Constants.selectedProductMasterListFreebies.isEmpty()) {
            Constants.selectedProductMasterListFreebies.add(freeBieProducts);
        } else {
            Boolean isSchemeAlreadyPresent = false;
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListFreebies.get(i);
                String prodDesc = currentItem.getDesc();
                String freeBieProdDesc = item.getfreebiesProdDesc();
                if (currentItem.getSchemeIds().matches(item.getschemeId()) && prodDesc.equalsIgnoreCase(freeBieProdDesc)) {
                    Constants.selectedProductMasterListFreebies.set(i, freeBieProducts);
                    isSchemeAlreadyPresent = true;
                    break;
                }
            }
            if (!isSchemeAlreadyPresent) {
                Constants.selectedProductMasterListFreebies.add(freeBieProducts);
            }
        }
    }

    public static void calculateAndAddToListWithRemarks(SchemeFreebiesDetails item, double freeItemNumber, String weightage,String remarks) {
        addSchemeToSchemeSummaryList(item);
        ProductMasterDetails freeBieProducts = new ProductMasterDetails();
        freeBieProducts.setProdCode(item.getfreebiesProdCode());
        freeBieProducts.setDesc(item.getfreebiesProdDesc());
        freeBieProducts.setSchemeIds(item.getschemeId());
        freeBieProducts.setfreeBieUom(item.getfreebieUomDisplayValue());
        freeBieProducts.setfreebieFilter(item.getscheme_filter());
        freeBieProducts.setQty(Constants.defaultFormat.format(freeItemNumber));
        freeBieProducts.setWeightage(weightage);
        freeBieProducts.setOrder_wise_remarks(remarks);
        if (Constants.selectedProductMasterListFreebies.isEmpty()) {
            Constants.selectedProductMasterListFreebies.add(freeBieProducts);
        } else {
            Boolean isSchemeAlreadyPresent = false;
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListFreebies.get(i);
                String prodDesc = currentItem.getDesc();
                String freeBieProdDesc = item.getfreebiesProdDesc();
                if (currentItem.getSchemeIds().matches(item.getschemeId()) && prodDesc.equalsIgnoreCase(freeBieProdDesc)) {
                    Constants.selectedProductMasterListFreebies.set(i, freeBieProducts);
                    isSchemeAlreadyPresent = true;
                    break;
                }
            }
            if (!isSchemeAlreadyPresent) {
                Constants.selectedProductMasterListFreebies.add(freeBieProducts);
            }
        }
    }

    public static boolean isValidMail(String email) {
        try {
            return android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches();
        } catch (Exception e) {
            return false;
        }

    }

    public static boolean isValidIndianMobile(String mobile) {
        try {
            if (mobile.length() == 10 && (mobile.startsWith("9") || mobile.startsWith("8") || mobile.startsWith("7") || mobile.startsWith("6"))) {
                return true;
            } else {
                return false;
            }
        } catch (Exception e) {
            return false;
        }
    }

    //make sub dealer, retailer to 'sub dealer','retailer'
    public static String convertCommaSeparatedListToProperFormat(String dataTobeFormatted) {
//		String dataTobeFormatted=Constants.userDetailsObj.getstk_audit_cust_type();
        String finalData = "";
        if (dataTobeFormatted.contains(",")) {
            String[] custTypeSplitted = dataTobeFormatted.split(",");
            for (int i = 0; i < custTypeSplitted.length; i++) {
                if (finalData.matches("")) {
                    finalData = "'" + custTypeSplitted[i] + "'";
                } else {
                    finalData = finalData + ", " + "'" + custTypeSplitted[i] + "'";
                }
            }
        } else {
            finalData = "'" + dataTobeFormatted + "'";
        }
        return finalData;
    }

    //make sub dealer; retailer to 'sub dealer','retailer' or Dealer#Sub Dealer to 'sub dealer','Sub Dealer'
    public static String convertCommaSeparatedListToProperFormat2(String dataTobeFormatted,String spearator) {
        String finalData = "";
        if (dataTobeFormatted.contains(spearator)) {
            String[] custTypeSplitted = dataTobeFormatted.split(spearator);
            for (int i = 0; i < custTypeSplitted.length; i++) {
                if (finalData.matches("")) {
                    finalData = "'" + custTypeSplitted[i] + "'";
                } else {
                    finalData = finalData + "," + "'" + custTypeSplitted[i] + "'";
                }
            }
        } else {
            finalData = "'" + dataTobeFormatted + "'";
        }
        return finalData;
    }

    public static String getFormattedCustomerCode(ArrayList<String> finalCustomerList) {
        String customerCodeFormatted = "";
        for (int i = 0; i < finalCustomerList.size(); i++) {
            if (customerCodeFormatted.matches("")) {
                customerCodeFormatted = "'" + finalCustomerList.get(i) + "'";
            } else {
                customerCodeFormatted = customerCodeFormatted + ", " + "'" + finalCustomerList.get(i) + "'";
            }
        }
        return customerCodeFormatted;
    }

    //delete a folder and all the files inside it
    public static void deleteTempFolderRecursive(File fileOrDirectory) {
        if (fileOrDirectory.isDirectory())
            for (File child : fileOrDirectory.listFiles())
                deleteTempFolderRecursive(child);

        fileOrDirectory.delete();
    }

    public static void getAppVersionDbVersion(TextView txtVersion, Context mContext) {
        txtVersion.setText(getAppVersion(mContext) + "~"
                + getDBVersion(mContext));
    }

    public static void getAppLogo(ImageView headerLogo) {
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }
    }

    public static double decimal3round(double value, int places) {
        long factor = (long) Math.pow(10, places);
        value = value * factor;
        long tmp = Math.round(value);
        return (double) tmp / factor;
    }

    public static double getAmnt(double amnt, SchemeFreebiesDetails item) {
        addSchemeToSchemeSummaryList(item);


        if (!item.getvaluePercent().matches("0")) {
            double percent = Double.parseDouble(item.getvaluePercent());
            amnt = amnt - (amnt * percent) / 100;
        } else if (!item.getvalueAmount().matches("0")) {
            double amountToBeOff = Double.parseDouble(item.getvalueAmount());
            amnt = amnt - amountToBeOff;
        }
        return amnt;
    }

    private static void addSchemeToSchemeSummaryList(SchemeFreebiesDetails item) {
        //adding scheme items to list
        if (Constants.selectedProductMasterListSchemeOffers.isEmpty()) {
            Constants.selectedProductMasterListSchemeOffers.add(item);
        } else//updating scheme items to list
        {
            Boolean isSchemeAlreadyPresent = false;
            for (int i = 0; i < Constants.selectedProductMasterListSchemeOffers.size(); i++) {
                SchemeFreebiesDetails currentItem = Constants.selectedProductMasterListSchemeOffers.get(i);
                String currentItemProdDesc = currentItem.getfreebiesProdDesc();
                String freebieProdDesc = item.getfreebiesProdDesc();
                if (currentItem.getschemeId().matches(item.getschemeId()) && currentItemProdDesc.equalsIgnoreCase(freebieProdDesc)) {
                    Constants.selectedProductMasterListSchemeOffers.set(i, item);
                    isSchemeAlreadyPresent = true;
                    break;
                }
            }
            if (!isSchemeAlreadyPresent) {
                Constants.selectedProductMasterListSchemeOffers.add(item);
            }
        }
    }

    private static void removeAmnt(SchemeFreebiesDetails item) {
        //removing scheme items to list
        if (!Constants.selectedProductMasterListSchemeOffers.isEmpty()) {
            for (int i = 0; i < Constants.selectedProductMasterListSchemeOffers.size(); i++) {
                SchemeFreebiesDetails currentItem = Constants.selectedProductMasterListSchemeOffers.get(i);
                if (currentItem.getschemeId().matches(item.getschemeId())) {
                    Constants.selectedProductMasterListSchemeOffers.remove(i);
                    break;
                }
            }
        }

    }

    public static BigDecimal round(BigDecimal d, int scale) {
        return d.setScale(scale, BigDecimal.ROUND_HALF_EVEN);
//		int mode = (roundUp) ? BigDecimal.ROUND_UP : BigDecimal.ROUND_DOWN;
//		return d.setScale(scale, mode);
    }
    public static void closeApp(Context context,String msg)
    {
        Activity activity=(Activity)context;
        if(!msg.matches(""))
        {
            showToast(context,msg);
        }
//        activity.finish();//finishAffinity() method that will finish the current activity and all parent activities, but it works only in Android 4.1 or higher
    }
    public static void downloadDecryptTextFile(String URL, JSONObject jo, String filename) {
        InputStream is = null;
        FileOutputStream fbo = null;
        File outputFile = null;
//        InputStream is = new HttpCalling().httpPostCallWithInputStreamResponse(URL,jo.toString());
        String textFileData = new HttpCalling().httpPostCallWithXmlResponseDecrypted(URL, jo.toString());
//        String textFileData=convertStreamToString(is);
//        MCrypt mcrypt = new MCrypt();

        try {
//            textFileData =new String( mcrypt.decrypt(textFileData) );
            is = convertStringToStream(textFileData);

            outputFile = new File(filename);
            if (outputFile.exists()) {
                outputFile.delete();
            }

            fbo = new FileOutputStream(outputFile, false);
            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }

            fbo.flush();

        } catch (Exception e) {
            e.printStackTrace();

        } finally {
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }

        }

    }

    @SuppressLint("NewApi")
    public static InputStream convertStringToStream(String source) throws UnsupportedEncodingException {
        return new ByteArrayInputStream(source.getBytes(StandardCharsets.UTF_8.name()));

    }

    //Current Android version data
    public static String currentOsVersion() {
        double release = Double.parseDouble(Build.VERSION.RELEASE.replaceAll("(\\d+[.]\\d+)(.*)", "$1"));
        String codeName = "Unsupported";//below Jelly bean OR above Pie
        if (release >= 4.1 && release < 4.4) codeName = "Jelly Bean";
        else if (release < 5) codeName = "Kit Kat";
        else if (release < 6) codeName = "Lollipop";
        else if (release < 7) codeName = "Marshmallow";
        else if (release < 8) codeName = "Nougat";
        else if (release < 9) codeName = "Oreo";
        else if (release < 10) codeName = "Pie";
        return codeName + " v" + release + ", API Level: " + Build.VERSION.SDK_INT;
    }

    public static void setCustomerCheckInDataInPreferences(Context context, String mRouteCode)
    {
        String timeStamp = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
        PreferenceData.setCheckInOutRouteCode(context, mRouteCode);
        PreferenceData.setCheckInTime(context, timeStamp);
        PreferenceData.setCheckInOutId(context, "uncheck");
        PreferenceData.setCheckInOutEmpCode(context, Constants.selectedCheckINCustomer.getCustomerCode());
        PreferenceData.setCheckInOutEmpName(context, Constants.selectedCheckINCustomer.getCustomerName());
        PreferenceData.setCheckInOutEmpType(context, Constants.selectedCheckINCustomer.getCustomerType());
        PreferenceData.setCheckInLAT(context, Constants.currentLat);
        PreferenceData.setCheckInLONG(context, Constants.currentLong);
        PreferenceData.setCheckInACCURACY(context, Constants.locationAccuracy);


//        AceDnsTransactionDatabase mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(context);
//        mAceDnsTransactionDatabase.insertToCheckInOutForCheckIn("checkin", timeStamp, Constants.selectedCheckINCustomer.getCustomerCode(), "", "", "", "", "");
    }

    public static void makePdfViewingProcess(final Context mContext)
    {
        Activity activity = (Activity) mContext;
            activity.runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    Utils.cancelProgressDialog();
                    AceDnsDatabase mAceDnsDatabase=new AceDnsDatabase(mContext);
                    ArrayList<BranchWisePdfMaster> schemePdfList = mAceDnsDatabase.getSchemePdfVal();
                    if(schemePdfList !=null && schemePdfList.size()>0)
                    {
                        if (schemePdfList.size() > 1)
                        {
                            ShowBranchListDialogToShowScheme(schemePdfList,mContext);
                        }
                        else
                        {
                            Constants.catalogueorSchemeVal = schemePdfList.get(0).getPDF_file_name();
                            Constants.SchemeBranchName = schemePdfList.get(0).getbranch_name();
                            Constants.SchemeBranchCode = schemePdfList.get(0).getbranch_code();
                            makePdfProcessLevel2(mContext);
                        }
                    }
                    else
                    {
                        Toast.makeText(mContext, "No scheme found in your database, please contact admin", Toast.LENGTH_SHORT).show();
                    }
                }
            });


    }
    public static void makePdfViewingProcessWithoutBranch(final Context mContext)
    {
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(new Runnable() {
            @Override
            public void run() {
                Utils.cancelProgressDialog();
                AceDnsDatabase mAceDnsDatabase=new AceDnsDatabase(mContext);
                ArrayList<BranchWisePdfMaster> schemePdfList = mAceDnsDatabase.getSchemePdfValWithoutBranch();
                if(schemePdfList !=null && schemePdfList.size()>0)
                {
                    if (schemePdfList.size() > 1)
                    {
                        ShowSchemeListDialogToShowScheme(schemePdfList,mContext);
                    }
                    else
                    {
                        Constants.catalogueorSchemeVal = schemePdfList.get(0).getPDF_file_name();
                        Constants.SchemeBranchName = schemePdfList.get(0).getbranch_name();
//                        Constants.SchemeBranchCode = schemePdfList.get(0).getbranch_code();
                    makeCatalogLoadingProcess(mContext);
                    }
                }
                else
                {
                    Toast.makeText(mContext, "No scheme found in your database, please contact admin", Toast.LENGTH_SHORT).show();
                }
            }
        });


    }
    public static void makePdfViewingProcessGoldenRule(final Context mContext)
    {
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            AceDnsDatabase mAceDnsDatabase=new AceDnsDatabase(mContext);
//                ArrayList<BranchWisePdfMaster> schemePdfList = mAceDnsDatabase.getSchemePdfVal();
            Constants.goldenRulesFileList = mAceDnsDatabase.getgoldenRulesImageVal();
            if(Constants.goldenRulesFileList !=null && Constants.goldenRulesFileList.size()>0)
            {
//                    if (goldenRulesFileList.size() > 1)
//                    {
//                        ShowBranchListDialogToShowScheme(goldenRulesFileList,mContext);
//                    }
//                    else
//                    {
                    Constants.catalogueorSchemeVal = Constants.goldenRulesFileList.get(0).getgr_file_name();
                    Constants.SchemeBranchName = Constants.goldenRulesFileList.get(0).getbranch_name();
                    makeGoldenRuleLoadingProcess(mContext);
//                    }
            }
            else
            {
                Toast.makeText(mContext, "No data found related to Golden Rules, please contact admin", Toast.LENGTH_SHORT).show();
            }
        });


    }
    private static void makeGoldenRuleLoadingProcess(final Context mContext)
    {
        final Activity activity = (Activity) mContext;

        new Thread() {
            public void run() {
                File outputFile = new File(Utils.getAppStoragePath(mContext)+ Constants.catalogueorSchemeVal);
                int currentApiVersion = Build.VERSION.SDK_INT;
                if (outputFile.exists())
                {
                    loadPdfAsPerOsVersionGoldenRule(mContext, outputFile, currentApiVersion);
                }
                else
                {
                    String urlstr=BaseUrl.baseUrl +"golden_rules/"+ Constants.catalogueorSchemeVal;
                    Log.d("TAG", "_DOWNLOAD_ run: "+urlstr);
                    Boolean isDownloadSuccess=DownloadFile(urlstr,outputFile);
                    if(isDownloadSuccess)
                    {
//                      copyInputStreamToFile(inputStream,outputFile);
                        if (outputFile.exists())
                        {
                            loadPdfAsPerOsVersionGoldenRule(mContext, outputFile, currentApiVersion);
                        }
                        else
                        {
                            activity.runOnUiThread(new Runnable() {
                                @Override
                                public void run() {
                                    Utils.showToast(mContext,"Something went wrong while downloading schemes. Please contact admin.");
                                }
                            });

                        }
                    }
                    else
                    {
                        activity.runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                Utils.showToast(mContext,"Something went wrong while downloading schemes. Please contact admin.");
                            }
                        });
                    }


                }
            }
        }.start();

    }


    public static void makeGoldenRulesImageViewingProcess(final Context mContext)
    {
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(new Runnable() {
            @Override
            public void run() {
                Utils.cancelProgressDialog();
            }
        });
                AceDnsDatabase mAceDnsDatabase=new AceDnsDatabase(mContext);
                Constants.goldenRulesFileList = mAceDnsDatabase.getgoldenRulesImageVal();
                if(Constants.goldenRulesFileList !=null && Constants.goldenRulesFileList.size()>0)
                {
                    for (int i = 0; i< Constants.goldenRulesFileList.size(); i++)
                    {
                        BranchWisePdfMaster currentItem= Constants.goldenRulesFileList.get(i);
                        String fileName=currentItem.getgr_file_name();
                        File outputFile = new File(Utils.getAppStoragePath(mContext) + fileName);
                        if (!outputFile.exists() && HTTPUtils.isConnectionPossible(mContext))
                        {
                            InputStream inputStream = new HttpCalling().httpGetCallWithInputStreamResponse(BaseUrl.baseUrl+"golden_rules/"+ fileName);
                            commonAsyncTaskMaster.Download_txt_laravel(inputStream, fileName,mContext);
                        }
                    }

                    Intent intent = new Intent(mContext, ActivityGoldenRulesSplashReport.class);
                    mContext.startActivity(intent);
                }
                else
                {
                    activity.runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            Toast.makeText(mContext, "Proper data not found, please contact admin", Toast.LENGTH_SHORT).show();
                        }
                    });

                }



    }

    private static void makePdfProcessLevel2(final Context mContext)
    {
        AceDnsDatabase mAceDnsDatabase=new AceDnsDatabase(mContext);
        ArrayList<BranchWisePdfMaster> schemePdfList = mAceDnsDatabase.getSchemePdfValByBranchCode(Constants.SchemeBranchCode);
        if(schemePdfList !=null && schemePdfList.size()>0)
        {
            if (schemePdfList.size() > 1)
            {
                ShowBranchListDialogToShowSchemeByBranchCode(schemePdfList,mContext);
            }
            else
            {
                Constants.catalogueorSchemeVal = schemePdfList.get(0).getPDF_file_name();
                Constants.SchemeBranchName = schemePdfList.get(0).getbranch_name();
                makeCatalogLoadingProcess(mContext);
            }
        }
        else
        {
            Toast.makeText(mContext, "No scheme found in your database, please contact admin", Toast.LENGTH_SHORT).show();
        }
    }

    private static void makeCatalogLoadingProcess(final Context mContext)
    {
        final Activity activity = (Activity) mContext;

        new Thread() {
            public void run() {
                File outputFile = new File(Utils.getAppStoragePath(mContext) + Constants.catalogueorSchemeVal);
                int currentApiVersion = android.os.Build.VERSION.SDK_INT;
                if (outputFile.exists())
                {
                    loadPdfAsPerOsVersion(mContext, outputFile, currentApiVersion);
                }
                else
                {
                    String urlstr=BaseUrl.baseUrl +"/schemes/"+ Constants.catalogueorSchemeVal;
                    Boolean isDownloadSuccess=DownloadFile(urlstr,outputFile);
                    if(isDownloadSuccess)
                    {
//                      copyInputStreamToFile(inputStream,outputFile);
                        if (outputFile.exists())
                        {
                            loadPdfAsPerOsVersion(mContext, outputFile, currentApiVersion);
                        }
                        else
                        {
                            activity.runOnUiThread(new Runnable() {
                                @Override
                                public void run() {
                                    Utils.showToast(mContext,"Something went wrong while downloading schemes. Please contact admin.");
                                }
                            });

                        }
                    }
                    else
                    {
                        activity.runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                Utils.showToast(mContext,"Something went wrong while downloading schemes. Please contact admin.");
                            }
                        });
                    }


                }
            }
        }.start();

    }
    public static  Boolean DownloadFile(String fileURL, File directory) {
        Boolean isDownloadSuccess;
        try {

            FileOutputStream f = new FileOutputStream(directory);
            URL u = new URL(fileURL);
//			URL u = new URL(URLEncoder.encode(fileURL, "utf-8"));
            HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();

            InputStream in = c.getInputStream();

            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            isDownloadSuccess = true;
        } catch (Exception e) {
            e.printStackTrace();
            isDownloadSuccess = false;
        }
        return isDownloadSuccess;
    }

    private static void loadPdfAsPerOsVersion(Context mContext, File outputFile, int currentApiVersion) {
        if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP)
        {
            Intent intent = new Intent(mContext, SchemesPdfActivity.class);
            mContext.startActivity(intent);
        }
        else
        {
            Intent target = new Intent(Intent.ACTION_VIEW);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                target.setDataAndType(FileProvider.getUriForFile(mContext,
                        BuildConfig.APPLICATION_ID + ".provider",
                        outputFile), "application/pdf");
            } else {

                target.setDataAndType(Uri.fromFile(outputFile), "application/pdf");

            }

            target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

            Intent intent = Intent.createChooser(target, "Open File");
            try
            {
                mContext.startActivity(intent);
            }
            catch (ActivityNotFoundException e)
            {
                // Instruct the user to install a PDF reader here, or something
            }
        }
    }

    private static void loadPdfAsPerOsVersionGoldenRule(Context mContext, File outputFile, int currentApiVersion) {
        if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP)
        {
            Intent intent = new Intent(mContext, ActivityGoldenRulesSplashReport.class);
            mContext.startActivity(intent);
        }
        else
        {
            Intent target = new Intent(Intent.ACTION_VIEW);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                target.setDataAndType(FileProvider.getUriForFile(mContext,
                        BuildConfig.APPLICATION_ID + ".provider",
                        outputFile), "application/pdf");
            } else {

                target.setDataAndType(Uri.fromFile(outputFile), "application/pdf");

            }

            target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

            Intent intent = Intent.createChooser(target, "Open File");
            try
            {
                mContext.startActivity(intent);
            }
            catch (ActivityNotFoundException e)
            {
                // Instruct the user to install a PDF reader here, or something
            }
        }
    }

    private static void copyInputStreamToFile(InputStream in, File file) {
        OutputStream out = null;

        try {
            out = new FileOutputStream(file);
            byte[] buf = new byte[1024];
            int len;
            while((len=in.read(buf))>0){
                out.write(buf,0,len);
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
        finally {
            // Ensure that the InputStreams are closed even if there's an exception.
            try {
                if ( out != null ) {
                    out.close();
                }

                // If you want to close the "in" InputStream yourself then remove this
                // from here but ensure that you close it yourself eventually.
                in.close();
            }
            catch ( IOException e ) {
                e.printStackTrace();
            }
        }
    }
    private static void ShowBranchListDialogToShowScheme(final ArrayList<BranchWisePdfMaster> branchList, final Context mContext)
    {
        final SchemePdfBranchSelectionAdapter adapterCust = new SchemePdfBranchSelectionAdapter(mContext, R.layout.customer_list_child, branchList,false);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
            }
        });
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Branch.");
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {

                TextView textViewHidden = (TextView) arg1.findViewById(R.id.HiddenValue1);
                TextView textViewBranchName = (TextView) arg1.findViewById(R.id.list_details);
                Constants.catalogueorSchemeVal = textViewHidden.getText().toString().trim();
                Constants.SchemeBranchName = textViewBranchName.getText().toString().trim();
                Constants.SchemeBranchCode = branchList.get(arg2).getbranch_code();
                makePdfProcessLevel2(mContext);
            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);


        mDialogCustomer.show();
    }
    private static void ShowSchemeListDialogToShowScheme(final ArrayList<BranchWisePdfMaster> schemeList, final Context mContext)
    {
        final SchemePdfBranchSelectionAdapter adapterCust = new SchemePdfBranchSelectionAdapter(mContext, R.layout.customer_list_child, schemeList,false);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
            }
        });
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Scheme.");
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {

                Constants.catalogueorSchemeVal = schemeList.get(arg2).getPDF_file_name();
                Constants.SchemeBranchName = schemeList.get(arg2).getbranch_name();
                makeCatalogLoadingProcess(mContext);
            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);


        mDialogCustomer.show();
    }
    private static void ShowBranchListDialogToShowSchemeByBranchCode(final ArrayList<BranchWisePdfMaster> branchList, final Context mContext)
    {
        final SchemePdfBranchSelectionAdapter adapterCust = new SchemePdfBranchSelectionAdapter(mContext, R.layout.customer_list_child, branchList,true);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Scheme for branch "+ Constants.SchemeBranchName);
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
            }
        });
        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                TextView textViewHidden = (TextView) arg1.findViewById(R.id.HiddenValue1);
                TextView textViewBranchName = (TextView) arg1.findViewById(R.id.list_details);
                Constants.catalogueorSchemeVal = textViewHidden.getText().toString().trim();
                Constants.SchemeBranchName = branchList.get(arg2).getbranch_name();
                makeCatalogLoadingProcess(mContext);
            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);


        mDialogCustomer.show();
    }

    @Override
    public void onLocationChanged(Location location) {


    }

    @Override
    public void onProviderDisabled(String provider) {


    }

    @Override
    public void onProviderEnabled(String provider) {


    }

    @Override
    public void onStatusChanged(String provider, int status, Bundle extras) {


    }

    public static class CustomDatePickerDialogWithPermanentTitle extends DatePickerDialog {

        private CharSequence title;

        public CustomDatePickerDialogWithPermanentTitle(Context context, OnDateSetListener callBack, int year, int monthOfYear, int dayOfMonth) {
            super(context, callBack, year, monthOfYear, dayOfMonth);
        }

        public void setPermanentTitle(CharSequence title) {
            this.title = title;
            setTitle(title);
        }

        @Override
        public void onDateChanged(DatePicker view, int year, int month, int day) {
            super.onDateChanged(view, year, month, day);
            setTitle(title);
        }
    }
    public static void setCheckInOutLatLongAccuracyToLocationLatLong(Context mContext) {
        Constants.currentLat = PreferenceData.getCheckInLAT(mContext);
        Constants.currentLong = PreferenceData.getCheckInLONG(mContext);
        Constants.locationAccuracy = PreferenceData.getCheckInACCURACY(mContext);
    }
    public static void clearAccuracyLatLong() {
        Constants.currentLat = "";
        Constants.currentLong = "";
        Constants.locationAccuracy = "";
    }
    public static Bitmap drawableToBitmap (Drawable drawable) {
        Bitmap bitmap = null;

        if (drawable instanceof BitmapDrawable) {
            BitmapDrawable bitmapDrawable = (BitmapDrawable) drawable;
            if(bitmapDrawable.getBitmap() != null) {
                return bitmapDrawable.getBitmap();
            }
        }

        if(drawable.getIntrinsicWidth() <= 0 || drawable.getIntrinsicHeight() <= 0) {
            bitmap = Bitmap.createBitmap(1, 1, Bitmap.Config.ARGB_8888); // Single color bitmap will be created of 1x1 pixel
        } else {
            bitmap = Bitmap.createBitmap(drawable.getIntrinsicWidth(), drawable.getIntrinsicHeight(), Bitmap.Config.ARGB_8888);
        }

        Canvas canvas = new Canvas(bitmap);
        drawable.setBounds(0, 0, canvas.getWidth(), canvas.getHeight());
        drawable.draw(canvas);
        return bitmap;
    }
    public static void headerFooterIconChangesForRetailerApp(Context context, Boolean isMenuActivity) {
        if (Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes"))
        {
            Activity activity=(Activity)context;
            FrameLayout header_layout= activity.findViewById(R.id.header_layout);
            header_layout.setBackgroundResource(R.drawable.header_dark_blue);
            if(isMenuActivity)
            {
                Button btn_attendance=  activity.findViewById(R.id.btn_attendance);
                Button btn_logout=  activity.findViewById(R.id.btn_logout);
                btn_logout.setBackgroundResource(R.drawable.option_menu);
                btn_attendance.setBackgroundResource(R.drawable.attendance_retailerapp);
            }
            else
            {
                try
                {
                    Button bk_clkd=  activity.findViewById(R.id.back);
                    bk_clkd.setBackgroundResource(R.drawable.bk_clkd_white);
                }
                catch (Exception E)
                {

                }

            }
            ImageView mImageViewHeaderLogo = activity.findViewById(R.id.imagelogo);
            mImageViewHeaderLogo.setImageBitmap(drawableToBitmap(context.getResources().getDrawable(R.drawable.mi)));
            try
            {
                ImageView imagelogoFooter= activity.findViewById(R.id.imagelogoFooter);
                if (Constants.logoBmp != null) {
                    imagelogoFooter.setVisibility(View.VISIBLE);
                    imagelogoFooter.setImageBitmap(Constants.logoBmp);
                } else {
                    imagelogoFooter.setVisibility(GONE);
                }
            }
            catch (Exception e)
            {

            }


        }
    }

    public static float linearDistanceBetweenTwoLatLong(String latA,String longA, String latB, String longB)
    {
        android.location.Location locationA = new android.location.Location("point A");

        locationA.setLatitude(Double.parseDouble(latA));
        locationA.setLongitude(Double.parseDouble(longA));

        android.location.Location locationB = new android.location.Location("point B");

        locationB.setLatitude(Double.parseDouble(latB));
        locationB.setLongitude(Double.parseDouble(longB));

        return locationA.distanceTo(locationB);
    }
    public static void setImageOnImageView(String imagePath,ImageView myImage)
    {
        try
        {
            File imgFile = new  File(imagePath);
            if(imgFile.exists())
            {
                Bitmap myBitmap = BitmapFactory.decodeFile(imgFile.getAbsolutePath());
                myImage.setImageBitmap(myBitmap);

            }
        }
        catch (Exception e)
        {

        }

    }

    public static int numberOfAfterCheckedInMenuOn(Context context)
    {
        int i=0;
        AceDnsDatabase mAceDnsDatabase=  new AceDnsDatabase(context);
        try
        {
            if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("order"))
            {
                i=i+1;
            }
            if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("stk_audit"))
            {
                i=i+1;
            }
            if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("market_feedback"))
            {
                i=i+1;
            }
            if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection"))
            {
                i=i+1;
            }
            if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("survey"))
            {
                i=i+1;
            }

        }
        catch (Exception e)
        {
        }
            return i;
    }

    public static String capitalize(String capString)
    {
        StringBuffer capBuffer = new StringBuffer();
        Matcher capMatcher = Pattern.compile("([a-z])([a-z]*)", Pattern.CASE_INSENSITIVE).matcher(capString);
        while (capMatcher.find())
        {
            capMatcher.appendReplacement(capBuffer, capMatcher.group(1).toUpperCase() + capMatcher.group(2).toLowerCase());
        }

        return capMatcher.appendTail(capBuffer).toString();
    }
    public static void HideSoftKeyBoard(EditText autoCompleteTextView1OBJ,Context context) {
        try {
            InputMethodManager imm = (InputMethodManager) context.getSystemService(Context.INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(autoCompleteTextView1OBJ.getWindowToken(), 0);
        } catch (Exception e) {

        }
    }
    //we can check whether MockSetting option is turned ON
    public static boolean isMockSettingsON(Context context)
    {
        // returns true if mock location enabled, false if not enabled.
        if (Settings.Secure.getString(context.getContentResolver(),
                Settings.Secure.ALLOW_MOCK_LOCATION).equals("0"))
            return false;
        else
            return true;
    }
    //we can check whether are there other apps in the device, which are using android.permission.ACCESS_MOCK_LOCATION (Location Spoofing Apps)
    public static boolean areThereMockPermissionApps(Context context)
    {
        int count = 0;

        PackageManager pm = context.getPackageManager();
        List<ApplicationInfo> packages =
                pm.getInstalledApplications(PackageManager.GET_META_DATA);

        for (ApplicationInfo applicationInfo : packages) {
            try {
                PackageInfo packageInfo = pm.getPackageInfo(applicationInfo.packageName,
                        PackageManager.GET_PERMISSIONS);

                // Get Permissions
                String[] requestedPermissions = packageInfo.requestedPermissions;

                if (requestedPermissions != null) {
                    for (int i = 0; i < requestedPermissions.length; i++) {
                        if (requestedPermissions[i]
                                .equals("android.permission.ACCESS_MOCK_LOCATION")
                                && !applicationInfo.packageName.equals(context.getPackageName())) {
                            count++;
                        }
                    }
                }
            } catch (PackageManager.NameNotFoundException e) {
                Log.e("Got exception " , e.getMessage());
            }
        }

        if (count > 0)
            return true;
        return false;
    }


    public static Boolean checkDatePick(String fromDate, String toDate, String format)
    {
        SimpleDateFormat Format = new SimpleDateFormat(format);

        Date FromTime = null;
        Date ToTime = null;

        try {
            FromTime = Format.parse(fromDate);
            ToTime = Format.parse(toDate);
            if (ToTime.before(FromTime))
            {
                return true;
            }
            else
            {
                return false;
            }
        } catch (ParseException e) {
            e.printStackTrace();
            return false;
        }
//		return outputDateString;
    }


    public static void isDevOn(Context context){
        int adb = Settings.Secure.getInt(context.getContentResolver(),
                Settings.Global.DEVELOPMENT_SETTINGS_ENABLED , 0);
        if(adb == 0){
            Constants.isDeveloperOn = false;
        }else{
            Constants.isDeveloperOn = true;
        }
    }
}
