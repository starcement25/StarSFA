package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.UploadUnuploadedData;

public class TRANS_SubmitYellowCardTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    ProgressDialog pd;
    Boolean shouldFinish = false;


    public TRANS_SubmitYellowCardTask(Context context, Boolean shouldFinish) {
        this.mContext = context;
        this.shouldFinish = shouldFinish;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (shouldFinish) {
            pd = new ProgressDialog(mContext);
            pd.setMessage("Submitting transaction.Please wait");
            pd.setCancelable(false);
            pd.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {
        xmlData = UploadUnuploadedData.prepareXMLDataYellowCard(mContext);//No data present for no data
        String POST_result = "";
        if (!xmlData.matches("No data present")) {
            if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
                try {
                    String uri = BaseUrl.baseUrl +
                            AceDnsWebServiceURL.submitYellowCard
                            + "?nick_name=" + Constants.nickName
                            + "&emp_code="
                            + Constants.employeeDetailObject.getEmpCode();
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitYellowCardTask: " +uri);
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitYellowCardTask value: " +xmlData);
                    POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);


                } catch (Exception e) {
                    POST_result = "Network Failure";
                } finally {
                }
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitYellowCardTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (!xmlData.matches("No data present")) {
            if (result.length() == 1) {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    dataHelperObj.UpdateLocationDataForYellowCard();
                    dataHelperObj.updateUnUploadedYellowCardDetails();
                    dataHelperObj.closeDatabase();
                } else if (result.length() == 0) {
                    dataHelperObj.closeDatabase();
                    Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT)
                            .show();
                }
            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
            }
        }

        if (shouldFinish) {
            pd.cancel();
            Activity activity = (Activity) mContext;
            activity.finish();
        }

    }
}
