package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.database.DatabaseHelperSqlite;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

public class POST_SendFcmId extends AsyncTask<String, Void, Void> {
    Context mContext;
    Boolean redirectionFlag;
    String mEmployeeCode, id;
    ContentValues values;

    public POST_SendFcmId(Context context, String id, ContentValues values, Boolean redirectionFlag, String mEmployeeCode) {
        mContext = context;
        this.values = values;
        this.redirectionFlag = redirectionFlag;
        this.mEmployeeCode = mEmployeeCode;
        this.id = id;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Registering for broadcast...");
    }

    @Override
    protected Void doInBackground(String... params) {
        String response = HttpCalling.httpPostCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.updateFireBaseRegistrationIdUrl, values);
        try {
            if (response.matches("1")) {
                DatabaseHelperSqlite helper = new DatabaseHelperSqlite(mContext);
                helper.updateRegistrationIdAndStatus(id, "no");
            }

        } catch (Exception e) {
        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (redirectionFlag) {
            new AUTH_UpdateCheck(mContext, true).execute(mEmployeeCode);//first login of day or first time login
        } else {
            mContext.startActivity(new Intent(mContext, MenuActivity.class));
            Activity activityobject = (Activity) mContext;
            activityobject.finish();
        }
    }
}
