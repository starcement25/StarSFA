package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Admin on 04-02-2017.
 */

public class DCA_QOIEVerification extends AsyncTask<String, Void, Void> {

    Context mContext;
    String mHttpResponse = "";
    String mType = "";
    String mURL = "";
    String mSurveyID = "";
    String mMobile = "";
    String mOtp = "";

    public DCA_QOIEVerification(Context context, String Url, String type) {
        this.mContext = context;
        this.mURL = Url;
        this.mType = type;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Void doInBackground(String... params) {
        mSurveyID = params[0];
        if (mType.equalsIgnoreCase("VERIFICATION")) {
            mMobile = params[1];
        }
        if (mType.equalsIgnoreCase("OTP")) {
            mOtp = params[1];
        }
        CheckNickName();
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DCA_QOIEVerification result: " +mHttpResponse);
        try {
            JSONObject jsonObject = new JSONObject(mHttpResponse);

            if ((!jsonObject.getString("process_sts").equals("null")) && (!jsonObject.getString("process_msg").equals(null))) {
                String status = jsonObject.getString("process_sts").trim();
                String msg = jsonObject.getString("process_msg").trim();
                Constants.QOIEVERIFICATIONStatus = msg;
                if (mType.equalsIgnoreCase("VERIFICATION")) {
                    if (status.equalsIgnoreCase("YES")) {
                        Constants.isQOIEVERIFICATION = true;
                    } else {
                        Constants.isQOIEVERIFICATION = false;
                    }
                }
                if (mType.equalsIgnoreCase("OTP")) {
                    if (status.equalsIgnoreCase("YES")) {
                        Constants.isQOIEOTP = true;
                    } else {
                        Constants.isQOIEOTP = false;
                    }
                }
            }
        } catch (JSONException je) {
            Toast.makeText(mContext, "Parsing error ", Toast.LENGTH_LONG).show();
        }

    }

    public void CheckNickName() {
        ContentValues values = new ContentValues();
        values.put("survey_id", mSurveyID);
        if (mType.equalsIgnoreCase("VERIFICATION")) {
            values.put("mobile_no", mMobile);
        } else if (mType.equalsIgnoreCase("OTP")) {
            values.put("otp", mOtp);
        } else {

        }

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DCA_QOIEVerification: " +mURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DCA_QOIEVerification values: " +values);

        mHttpResponse = HttpCalling.httpGetCallWithXmlResponse(mURL, values);

    }
}

