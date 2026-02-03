package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;

public class DATA_ConfirmDownloadTask extends AsyncTask<String, Void, String> {
    Context mContext;
    String dbVersion = "";
    AceDnsDatabase mAceDnsDatabase;

    public DATA_ConfirmDownloadTask(Context context) {
        this.mContext = context;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dbVersion = mAceDnsDatabase.getDatabaseVersion();
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Confirming Download. Please wait..");
    }

    @Override
    protected String doInBackground(String... params) {
        String date = new SimpleDateFormat("yyyy-MM-dd_HH:mm:ss").format(Calendar.getInstance().getTime());
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.confirmDownloadURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&downloaddate=" + date
                + "&device_id=" + (Constants.deviceId)
                + "&db_version=" + dbVersion;

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_ConfirmDownloadTask: " + url);

        try {
            if (!Constants.userDetailsObj.getStockAuditRate().isEmpty() && Constants.userDetailsObj.getStockAuditRate() != null) {
                if (true == Constants.isFirstLoginOfDay && Constants.userDetailsObj.getStockAuditRate().equalsIgnoreCase("yes")) {
                    mAceDnsDatabase.UpdateMRPDetails();
                }
            }
        } catch (Exception e) {
            System.out.println("Exception" + e.toString());
        }

        if (Constants.nickName.trim().equalsIgnoreCase("STAR")) {
            int count = mAceDnsDatabase.SurveyTypeCount();
            if (count > 0) {
                mAceDnsDatabase.UpadateSurveyType();
            }
        }

        Constants.isFirstLoginOfDay = false;
        Constants.isFirstLoginOfApp = false;

        String POST_result = HttpCalling.httpGetCallWithXmlResponse(url, null);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_ConfirmDownloadTask result: " + result);
        Utils.cancelProgressDialog();
        if (Constants.downloadTableList != null && Constants.downloadTableList.contains("mis_transaction_delete")) {
            ProgressDialog pd = new ProgressDialog(mContext);
            pd.setMessage("Deleting Transaction..");
            pd.show();
            new DATA_DeleteTransactionTask(mContext, pd, true, Constants.employeeDetailObject.getEmpCode()).execute();
        } else {
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
            ((Activity) mContext).finish();
        }
    }
}
