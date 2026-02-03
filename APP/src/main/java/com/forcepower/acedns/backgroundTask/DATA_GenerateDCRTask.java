package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;

public class DATA_GenerateDCRTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String mDaysFirstLogin = "";

    public DATA_GenerateDCRTask(Context context, boolean isLoginCheck) {
        this.mContext = context;
        if (true == isLoginCheck) {
            mDaysFirstLogin = "yes";
        } else {
            mDaysFirstLogin = "no";
        }
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.createDCRURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&days_first_login=" + mDaysFirstLogin;

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_GenerateDCRTask: " + url);

        String POST_result = HttpCalling.httpGetCallWithXmlResponse(url, null);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_GenerateDCRTask result: " + POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
    }
}
