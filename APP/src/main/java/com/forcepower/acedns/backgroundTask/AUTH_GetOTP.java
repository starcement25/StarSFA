package com.forcepower.acedns.backgroundTask;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.OtpDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

public class AUTH_GetOTP extends AsyncTask<String, Void, Void> {
    @SuppressLint("StaticFieldLeak")
    Context mContext;
    String mHttpResponse = "";
    String mPhoneNo = "";


    public AUTH_GetOTP(Context context) {
        this.mContext = context;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Constants.mOTP = null;
        Utils.showProgressDialog(mContext, "Generating OTP.\nPlease wait...");
    }

    @Override
    protected Void doInBackground(String... params) {
        mPhoneNo = params[0];

        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("mobile_no", mPhoneNo);

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_GetOTP: " + BaseUrl.baseUrl + AceDnsWebServiceURL.OTPValidationURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_GetOTP values: " + values);

        mHttpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.OTPValidationURL, values);
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_GetOTP result: " + mHttpResponse);
        Utils.cancelProgressDialog();
        if (!mHttpResponse.isEmpty()) {
            if (mHttpResponse.length() == 4) {
                OtpDetails otpDetails = new OtpDetails();
                otpDetails.setMobileNo(mPhoneNo.trim());
                otpDetails.setOTPCode(mHttpResponse.trim());
                otpDetails.setFlag("0");
                Constants.mOTP = mHttpResponse.trim();

                new TRANS_SendOTP(mContext, mPhoneNo.trim(), mHttpResponse.trim()).execute();
            } else {
                if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("no")) {
                    ((Activity) mContext).finish();
                }
            }
        } else {
            if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("no")) {
                ((Activity) mContext).finish();
            }
        }
    }
}
