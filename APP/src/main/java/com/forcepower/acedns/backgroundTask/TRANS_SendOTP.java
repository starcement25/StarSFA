package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.widget.Toast;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HttpCalling;

public class TRANS_SendOTP extends AsyncTask<String, Void, String> {

    Context mContext;
    String mResponse = "";
    String mMobileNo = "";
    String mOtpCode = "";
    ProgressDialog mProgressDialog;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;

    public TRANS_SendOTP(Context context, String phoneno, String otpcode) {
        this.mContext = context;
        this.mMobileNo = phoneno;
        this.mOtpCode = otpcode;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Sending OTP.\nPlease wait...");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        String basicApi = "http://smsalertbox.com/api/sms.php?";
        String uid = "4c49504c3136";
        String pin = "5a3c58d919f65cec8b80f8afa2fe4c93";
        String sender = "RWLIPL";
        String route = "5";
        String tempid = "2";
        String message = "";
        String pushid = "1";
        if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
            message = "asd";

        } else {
            message = Constants.LIPLMSGBODY1 + mOtpCode.trim() + "." + Constants.LIPLMSGBODY2;
        }
        String url = "http://smsalertbox.com/api/sms.php?uid=4c49504c3136&pin=5a3c58d919f65cec8b80f8afa2fe4c93&sender=RWLIPL&route=5&tempid=2&mobile=" + mMobileNo + "&message=Your%20mobile%20number%20verification%20code%20is%20%23" + mOtpCode.trim() + "%23.%20Please%20provide%20this%20to%20our%20executive%20to%20complete%20your%20registration%20with%20QOIE.%0A%20%0AVisit%20http%3A%2F%2Fqoie.in%2Fget&pushid=1";

        POST_result = HttpCalling.httpGetCallWithXmlResponse(url, null);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        mProgressDialog.cancel();
        result = result.trim();
        if (ContainsOnlyNumbers(result)) {
            if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
                Constants.LIPLMOBILENO = mMobileNo.trim();
            } else {
                if (true == Constants.isDCAOTP) {

                } else {
                    SetOTPData(Constants.SurveyRowID, mMobileNo + "#OTP;" + mOtpCode);
                    Constants.LIPLMOBILENO = mMobileNo.trim();
                }
            }
            Toast.makeText(mContext, "OTP has been sent successfully", 15000).show();
        }
        if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("no")) {
            if (true == Constants.isDCAOTP) {
                Constants.isDCAOTP = false;
            } else if (true == Constants.isRESENDOTPREQUEST) {
                Constants.isRESENDOTPREQUEST = false;
            } else {
                ((Activity) mContext).finish();
            }
        }
    }

    public void SetOTPData(String rowID, String value) {
        String rowid;
        for (int count = 0; count < Constants.mFinalSurveyList.size(); count++) {
            rowid = Constants.mFinalSurveyList.get(count).getRowId();
            if (rowid.equalsIgnoreCase(rowID)) {
                Constants.mFinalSurveyList.get(count).setValue(value);
            }
        }
    }

    private boolean ContainsOnlyNumbers(String str) {
        for (int i = 0; i < str.length(); i++) {
            if (!Character.isDigit(str.charAt(i)))
                return false;
        }
        return true;
    }
}
