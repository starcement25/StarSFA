package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitRedudantSurvey extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean isLoaderShow = false;

    public TRANS_SubmitRedudantSurvey(Context context, boolean loaderShow) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.isLoaderShow = loaderShow;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (true == this.isLoaderShow) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait..");
        }
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitSurveyRedundantURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRedudantSurvey: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRedudantSurvey value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRedudantSurvey result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (true == this.isLoaderShow) {
            Utils.cancelProgressDialog();
        }
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            mAceDnsTransactionDatabase.UpdateRedundantLocationData();
            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
            }
        } else {
            if (true == this.isLoaderShow) {
//				Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            }
        }
        mAceDnsTransactionDatabase.closeDatabase();
        if (true == this.isLoaderShow) {
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> GetUnuploadedLocationofCheckInOutList = mAceDnsTransactionDatabase.GetUnuploadedLocationofRedundant();
        for (int ii = 0; ii < GetUnuploadedLocationofCheckInOutList.size(); ii++) {
            Location currentLocation = GetUnuploadedLocationofCheckInOutList.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            if (currentLocation.getTransId().startsWith("RT")) {
                ArrayList<MallMaster> unUploadedMalldata = mAceDnsTransactionDatabase.getUnuploadedRedundant(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedMalldata.size(); jj++) {
                    MallMaster obj = unUploadedMalldata.get(jj);
                    xmlData += "<redundant_transaction>";
                    xmlData += location;
                    xmlData += "<redundantdata>"
                            + "<redundant_trans_id><![CDATA[" + obj.getRedudantTransId() + "]]></redundant_trans_id>"
                            + "<foot_soldier_id><![CDATA[" + obj.getFsId() + "]]></foot_soldier_id>"
                            + "<mall_id><![CDATA[" + obj.getMallId() + "]]></mall_id>"
                            + "<business_name><![CDATA[" + obj.getArea() + "]]></business_name>"
                            + "<status><![CDATA[" + obj.getStatus() + "]]></status>"
                            + "</redundantdata>";
                    xmlData += "</redundant_transaction>";
                }
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
