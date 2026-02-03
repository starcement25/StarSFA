package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;


public class TRANS_SubmitSMS extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSMS(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Sending SMS.Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)
                && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String url = "http://smslive.in/push/default.aspx";
                ContentValues values = new ContentValues();
                values.put("user", "e_agro");
                values.put("pws", "emagro123");
                values.put("sms", Constants.EMAMIMSGBODY);
                values.put("Receipent", Constants.EMAMIMSGRECEIPENT);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSMS: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSMS value: " +values);
                POST_result = HttpCalling.httpGetCallWithXmlResponse(url, values);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSMS result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        mProgressDialog.cancel();
        if (result.equalsIgnoreCase("SEND_SUCCESS")) {
            Toast.makeText(mContext, "Message has been sent successfully", 15000).show();
        }
        Constants.EMAMIMSGRECEIPENT = "";
        Intent intent = new Intent(mContext, MenuActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        mContext.startActivity(intent);
    }
}