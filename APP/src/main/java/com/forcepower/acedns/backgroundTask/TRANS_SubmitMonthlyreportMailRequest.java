package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

public class TRANS_SubmitMonthlyreportMailRequest extends AsyncTask<String, Void, String> {
    Context mContext;

    public TRANS_SubmitMonthlyreportMailRequest(Context context) {
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
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitMonthlyReportMail + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMonthlyreportMailRequest: " +url);
                HttpCalling.httpGetCallWithXmlResponse(url, null);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMonthlyreportMailRequest result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
    }
}
