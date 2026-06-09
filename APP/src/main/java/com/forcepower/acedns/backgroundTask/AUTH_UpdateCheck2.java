package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

public class AUTH_UpdateCheck2 extends AsyncTask<String, Void, Void> {
    Context mContext;
    String httpResponse = "";
    String empCode = "";
    String version = "1.0";
    boolean downloadData = true;

    public AUTH_UpdateCheck2(Context context) {
        this.mContext = context;
        downloadData = true;
        PhoneStateChangeListener.ringing = false;
    }

    public AUTH_UpdateCheck2(Context context, boolean downloadData) {
        this.mContext = context;
        this.downloadData = downloadData;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        version = Utils.getAppVersion(mContext);
    }

    @Override
    protected Void doInBackground(String... params) {
        empCode = params[0];
        checkUpdate();
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck result: " + httpResponse);
        if (httpResponse.substring(0, 1).equalsIgnoreCase("1")) {
        } else {
            if (downloadData) {
                if (httpResponse.equalsIgnoreCase("0")) {
                    new DATA_LoadDataDictionaryData2(mContext).execute();
                } else if (httpResponse.equalsIgnoreCase("20")) {
                    new DATA_LoadDataDictionaryData2(mContext).execute();
                } else if (httpResponse.length() > 2 && httpResponse.substring(0, 1).equalsIgnoreCase("4")) {
                    String[] dataArray = httpResponse.split("/");
                    if (dataArray[1].equalsIgnoreCase(version)) {
                        new DATA_LoadDataDictionaryData2(mContext).execute();
                    } else {


                        //final String appPackageName = getPackageName(); // getPackageName() from Context or Activity object
                        if (Constants.nickName.equalsIgnoreCase("STAR")) {
                            Utils.showToast(mContext, "Please update the app from Play Store without uninstalling it.");
                            try {
                                mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.forcepower.starsfa")));
                            } catch (android.content.ActivityNotFoundException anfe) {
                                mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + "com.forcepower.starsfa")));
                            }
                        } else {
                            Utils.directOutsideTheApplication(mContext, "Invalid APP Version \n Please install latest version of App", true);
                        }
                    }
                } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
                    if (PhoneStateChangeListener.ringing) {
                        new AUTH_UpdateCheck2(mContext).execute(empCode);
                    } else {
                        Utils.directOutsideTheApplication(mContext, "Network Failure", true);
                    }
                }
            } else {
                mContext.startActivity(new Intent(mContext, MenuActivity.class));
            }
        }
    }

    public void checkUpdate() {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("deviceId", Constants.deviceId);
        values.put("emp_code", empCode);
        values.put("versionCode", version);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck: " + BaseUrl.baseUrl + AceDnsWebServiceURL.updateCheckURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck values: " + values);
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.updateCheckURL, values);
    }
}