package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.os.AsyncTask;
import android.os.Build;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

public class TRANS_SubmitDeviceInfo extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "", device = "", version = "", manu = "", model = "", os = "", installationTime = "";

    public TRANS_SubmitDeviceInfo(Context context) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        device = Constants.employeeDetailObject.getDeviceID();
        PackageInfo pInfo = null;
        try {
            pInfo = mContext.getApplicationContext().getPackageManager().getPackageInfo(mContext.getApplicationContext().getPackageName(), 0);
        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();
        }
        version = pInfo.versionName;
        manu = Build.MANUFACTURER;
        model = Build.MODEL;
        os = Utils.currentOsVersion();
        installationTime = Utils.getInstallationTime(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        xmlData = prepareXMLData();
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitDeviceInfo + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDeviceInfo: " +url);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDeviceInfo value: " +xmlData);
        return HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDeviceInfo result: " +result);
        if (result.equalsIgnoreCase("1")) {
            AceDnsTransactionDatabase mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
            mAceDnsTransactionDatabase.insertToDeviceInfoTable(Constants.employeeDetailObject.getEmpCode(), device, manu, model, os, installationTime);
            mAceDnsTransactionDatabase.closeDatabase();
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

        xmlData += "<device_info>"

                + "<emp_code><![CDATA[" + Constants.employeeDetailObject.getEmpCode() + "]]></emp_code>"
                + "<device_id><![CDATA[" + device + "]]></device_id>"
                + "<manufacturer><![CDATA[" + manu + "]]></manufacturer>"
                + "<model><![CDATA[" + model + "]]></model>"
                + "<os_version><![CDATA[" + os + "]]></os_version>"
                + "<app_installation_time><![CDATA[" + installationTime + "]]></app_installation_time>";
        xmlData += "</device_info>";


        xmlData += "</root>";
        return xmlData;

    }
}
