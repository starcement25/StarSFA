package com.forcepower.acedns.backgroundTask;

import static com.forcepower.acedns.constants.Constants.isLARAVELAPI;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.parser.UserDetailsXMLParsing;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

public class AUTH_CheckNickNameTask extends AsyncTask<String, Void, Void> {
    @SuppressLint("StaticFieldLeak")
    Context mContext;
    String mHttpResponse = "";
    String mNickname = "";

    public AUTH_CheckNickNameTask(Context context) {
        this.mContext = context;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Checking Nick Name..");
    }

    @Override
    protected Void doInBackground(String... params) {
        mNickname = params[0];
        CheckNickName();
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_CheckNickNameTask result: " + mHttpResponse);
        if (mHttpResponse.equalsIgnoreCase("0")) {
            Utils.cancelProgressDialog();
            Utils.directOutsideTheApplication(mContext, "Invalid NickName:" + mNickname + ". \n Please provide a valid Nick Name", false);
        } else if (mHttpResponse.contains("<?xml version='1.0' encoding='UTF-8'?>")) {
            checkPhoneNumberLoginOrNot();
            Utils.showToast(mContext, "Valid Nick Name");
            Constants.nickName = mNickname.trim();
            new SETUP_DownloadLogoTask(mContext).execute();
        } else if (mHttpResponse.contains("Network Failure")) {
            Utils.cancelProgressDialog();
            Utils.directOutsideTheApplication(mContext, mHttpResponse, false);
        }
    }

    public void CheckNickName() {
        ContentValues values = new ContentValues();
        values.put("nick_name", mNickname);
        values.put("mode", "SETUP");

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AUTH_CheckNickNameTask values: " + values);
        mHttpResponse = HttpCalling.httpGetCallWithXmlResponseAndTimeOutParam(BaseUrl.baseUrl + AceDnsWebServiceURL.checkNickNameURL, values, 10, 0, 30);
    }

    public void checkPhoneNumberLoginOrNot() {
        UserDetailsXMLParsing parser = new UserDetailsXMLParsing(mHttpResponse);
        isLARAVELAPI = parser.getParsedData().getapp_phoneno_login().equalsIgnoreCase("yes");
    }
}
