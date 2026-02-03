package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

public class AUTH_UpdateCheck_db extends AsyncTask<String, Void, Void> {
    Context mContext;
    String httpResponse = "";
    String empCode = "";
    String version = "1.0";
    String versionCodeDB = "1.0";
    boolean downloadData = true;

    public AUTH_UpdateCheck_db(Context context, boolean downloadData) {
        this.mContext = context;
        this.downloadData = downloadData;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Checking for AppDB Updates..");
        version = Utils.getAppVersion(mContext);
        versionCodeDB = Utils.getDBVersion(mContext);
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
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck_db result: " + httpResponse);
        Utils.cancelProgressDialog();
        try {
            if (httpResponse.equalsIgnoreCase("0")) {
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-3-1");
                new DATA_LoadDataDictionaryData(mContext).execute();
            } else {
                Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-3-2");
                mContext.startActivity(new Intent(mContext, MenuActivity.class));
            }
        }catch (Exception e){
            Log.d("TAG", "_DOWNLOAD_product_master: Call login 13-3-1 "+e);
        }
    }

    public void checkUpdate() {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("deviceId", Constants.deviceId);
        values.put("emp_code", empCode);
        values.put("versionCode", version);
        values.put("versionCodeDB", versionCodeDB);

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck_db: " + BaseUrl.baseUrl + AceDnsWebServiceURL.updateCheckURL_db);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_UpdateCheck_db values: " + values);

        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.updateCheckURL_db, values);
    }
}