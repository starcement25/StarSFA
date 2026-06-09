package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.ActivityDownloadStatus;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.util.Date;

public class UpdateDeviceIdAsynctask2 extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsDatabase dbHelper;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public UpdateDeviceIdAsynctask2(Context context) {
        this.mContext = context;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime("download_dictionary");
        Constants.dictDownldStartTime = new Date();
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        values.put("deviceId", Constants.employeeDetailObject.getDeviceID());
        String httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.updateDeviceIdURL, values);
        return httpResponse;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (result.equalsIgnoreCase("1")) {
            Utils.insertToEmployeeMaster(mContext);
            if (Constants.downloadTableList.size() > 0) {

            } else {
                new DATA_ConfirmDownloadTask2(mContext).execute();
            }
        } else {
            Utils.showToast(mContext, "Login failed. Please try again.");
        }
    }
}
