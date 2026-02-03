package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;

public class DATA_DBBackupCheckTask extends AsyncTask<Void, Void, Void> {
    private int option = 0;
    private Context mContext;

    public DATA_DBBackupCheckTask(Context context) {
        this.mContext = context;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Void doInBackground(Void... params) {
        try {
            ContentValues values = new ContentValues();
            values.put("nick_name", Constants.userDetailsObj.getNickName());
            values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            values.put("deviceid", Constants.deviceId);

            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DBBackupCheckTask: " + BaseUrl.baseUrl + AceDnsWebServiceURL.dbBackUpCheckURL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DBBackupCheckTask values: " + values);

            option = Integer.parseInt(HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.dbBackUpCheckURL, values));

        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DBBackupCheckTask result: " + option);
        if (option == 1) {
            new DATA_SubmitDBBackupTask(mContext.getApplicationContext()).execute();
        }

    }

}
