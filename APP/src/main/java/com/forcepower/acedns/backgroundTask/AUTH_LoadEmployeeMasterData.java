package com.forcepower.acedns.backgroundTask;

import static com.forcepower.acedns.constants.Constants.isLARAVELAPI;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Build;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.DatabaseHelperSqlite;
import com.forcepower.acedns.parser.EmployeeDetailsXMLParser;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

public class AUTH_LoadEmployeeMasterData extends AsyncTask<String, Void, Void> {
    @SuppressLint("StaticFieldLeak")
    Context mContext;
    String httpResponse = "";
    AceDnsDatabase helper;
    String mEmployeeCodeOrPhoneNumber = "", password = "";
    String details = "";
    boolean isUpdatingFromMenu;

    public AUTH_LoadEmployeeMasterData(Context context, boolean isUpdatingFromMenu) {
        this.mContext = context;
        this.isUpdatingFromMenu = isUpdatingFromMenu;
        PhoneStateChangeListener.ringing = false;
        helper = new AceDnsDatabase(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (!isUpdatingFromMenu)
            Utils.showProgressDialog(mContext, "Authenticating User. Please wait...");
    }

    @Override
    protected Void doInBackground(String... params) {
        mEmployeeCodeOrPhoneNumber = params[0];
        password = params[1];
        details = "Id: " + mEmployeeCodeOrPhoneNumber + " Pwd: " + password;
        loadEmployeeDetails();
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData result: " + httpResponse);
        if (!isUpdatingFromMenu) {
            Utils.cancelProgressDialog();
        }

        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            Log.d("TAG", "_DOWNLOAD_product_master: "+httpResponse);
            if (httpResponse.equalsIgnoreCase("NOT LICENSED USER")) {
                Utils.directOutsideTheApplication(mContext, "Unlicensed User " + details + " . \n YOU ARE NOT A LICENSED USER", false);
            } else if (httpResponse.equalsIgnoreCase("NOT VALID USER")) {
                Utils.directOutsideTheApplication(mContext, "Invalid User. " + details + " \n Please provide valid UserId and Password", false);
            } else if (httpResponse.equalsIgnoreCase("6")) {
                Utils.directOutsideTheApplication(mContext, "Device initialization Error.\nPlease ReLogin after few mins.", false);
            } else if (httpResponse.length() > 2 && httpResponse.substring(0, 2).equalsIgnoreCase("4/")) {
                String[] dataArray = httpResponse.split("/");
                Utils.directOutsideTheApplication(mContext, "Device is already been registered to " + dataArray[1] + "\n Please contact your ADMIN", false);
            } else if (httpResponse.equalsIgnoreCase("5")) {
                Utils.directOutsideTheApplication(mContext, "Device is having compatibility ERROR.\nPlease contact your ADMIN", false);
            } else if (httpResponse.equalsIgnoreCase("0")) {
                Utils.directOutsideTheApplication(mContext, "Your ACEdns LOGIN credentials has been registered to different DEVICE.\nPlease contact your ADMIN.", false);
            } else {
                EmployeeDetailsXMLParser parser = new EmployeeDetailsXMLParser(httpResponse);
                Constants.employeeDetailObject = parser.getParsedData();
                if (Constants.employeeDetailObject != null) {
                    try {
                        if (isLARAVELAPI && !Constants.isFirstLoginOfApp && Constants.isFirstLoginOfDay) {
                            helper.updateEmployeePhoneNumber(mEmployeeCodeOrPhoneNumber);
                        }
                    } catch (Exception e) {
                        Log.d("TAG", "onPostExecute: "+e.getMessage());
                    }

                    Constants.employeeDetailObject.setDeviceID(Constants.deviceId);
                    if (!isUpdatingFromMenu) {
                        FCMProcess();
                    } else {
                        Utils.insertToEmployeeMaster(mContext);
                    }

                    if (!helper.isDeviceDetailsSent()) {
                        new TRANS_SubmitDeviceInfo(mContext).execute();
                    }

                } else {
                    Utils.directOutsideTheApplication(mContext, "Connection lost while authentication... " + details + "\n Please ReLogin.", false);
                }
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            Utils.directOutsideTheApplication(mContext, "Network Failure", false);
        }
    }

    public void loadEmployeeDetails() {
        Constants.employeeDetailObject = helper.getEmployeeObj();
        if (Constants.employeeDetailObject != null) {
            Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
        }
        //isLARAVELAPI = false;
        if (!isLARAVELAPI) {
            ContentValues values = new ContentValues();
            values.put("nick_name", Constants.nickName);
            values.put("emp_code", mEmployeeCodeOrPhoneNumber);
            values.put("newpassword", password);
            values.put("deviceid", !Constants.deviceId.isEmpty() ? Constants.deviceId : Utils.getDeviceId(mContext, mEmployeeCodeOrPhoneNumber));

            Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData: " + BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginURL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData: " + BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsOldLoginURL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData values: " + values);

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginURL, values);
            } else {
                httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsOldLoginURL, values);
            }

        } else {
            try {
                ContentValues values = new ContentValues();
                values.put("nick_name", Constants.nickName);
                values.put("emp_code", mEmployeeCodeOrPhoneNumber);
                //values.put("newpassword", password);
                values.put("deviceid", !Constants.deviceId.isEmpty() ? Constants.deviceId : Utils.getDeviceId(mContext, mEmployeeCodeOrPhoneNumber));

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData: " + BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginMobileURL);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData: " + BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginMobileURL);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_LoadEmployeeMasterData values: " + values);

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                    httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginMobileURL, values);
                } else {
                    httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.employeeDetailsLoginMobileURL, values);
                }
            } catch (Exception e) {
                Log.d("TAG", "loadEmployeeDetails: "+e.getMessage());
            }
        }
    }

    public void FCMProcess() {
        try {
            DatabaseHelperSqlite helper = new DatabaseHelperSqlite(mContext);
            ContentValues values = new ContentValues();
            values.put("nick_name", Constants.nickName);
            values.put("emp_code", mEmployeeCodeOrPhoneNumber);
            values.put("registrationid", helper.getRegistrationId());
            values.put("deviceid", Constants.deviceId);
            //call api and change status to
            new POST_SendFcmId(mContext, helper.getRegistrationId(), values, true, mEmployeeCodeOrPhoneNumber).execute();

        } catch (Exception e) {
            Log.d("TAG", "loadEmployeeDetails: "+e.getMessage());
        }
    }
}
