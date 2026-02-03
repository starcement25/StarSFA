package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.EditText;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

public class AUTH_PhoneNoValidation extends AsyncTask<String, Void, String> {

    Context mContext;
    ProgressDialog mProgressDialog;
    EditText mEditTextPhoneNo;


    public AUTH_PhoneNoValidation(Context context) {
        this.mContext = context;
        mEditTextPhoneNo = (EditText) ((Activity) context).findViewById(R.id.ed_name);

    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Checking validation of phone no....");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                ContentValues values = new ContentValues();
                values.put("nick_name", Constants.nickName);
                values.put("phone_no", Constants.EMAMIMSGRECEIPENT);

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_PhoneNoValidation: "+BaseUrl.baseUrl + AceDnsWebServiceURL.phnoeNumberValidationURL);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_PhoneNoValidation values: "+values);

                POST_result = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.phnoeNumberValidationURL, values);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_PhoneNoValidation result: " + result);
        mProgressDialog.cancel();
        if (result.equalsIgnoreCase("Network Failure")) {
            Toast.makeText(mContext, "Valid phone no", 15000).show();
            ((Activity) mContext).finish();
        } else {
            if (result.trim().startsWith("1")) {
                Toast.makeText(mContext, "Phone no already exist.\n Please provide valid phone no", 15000).show();
                Constants.EMAMIMSGRECEIPENT = "";
                mEditTextPhoneNo.setText("");
                String allertmessage = "";
                if (result.contains("#")) {
                    String[] RowData = result.split("\\#");
                    for (int count = 1; count < RowData.length; count++) {
                        allertmessage += RowData[count] + "\n";
                    }
                    Utils.ShowAlertDialog(mContext, allertmessage);
                }
            } else if (result.equalsIgnoreCase("0")) {
                Toast.makeText(mContext, "Valid phone no", 15000).show();
                ((Activity) mContext).finish();
            } else {
                Toast.makeText(mContext, "Valid phone no", 15000).show();
                ((Activity) mContext).finish();
            }
        }
    }
}
