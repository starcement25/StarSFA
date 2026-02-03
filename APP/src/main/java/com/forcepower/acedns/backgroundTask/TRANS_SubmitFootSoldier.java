package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyMenuActivity;
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

public class TRANS_SubmitFootSoldier extends AsyncTask<String, Void, String> {

    public String getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean isDCAFS = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitFootSoldier(Context context, boolean finish, String status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.isDCAFS = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.getStatus = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait..");
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitFootSoldierURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFootSoldier: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFootSoldier value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFootSoldier result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (getStatus.equals("SUBMIT")) {
            Utils.cancelProgressDialog();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateFootSoldierLocationData();
                mAceDnsTransactionDatabase.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
            } else {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
            }
            mAceDnsTransactionDatabase.closeDatabase();
            if (false == isDCAFS) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            } else {
                Intent intent = new Intent(mContext, SurveyMenuActivity.class);
                //intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                Constants.isDCAtoDCE = true;
                mContext.startActivity(intent);
            }
        } else {

            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateFootSoldierLocationData();
                mAceDnsTransactionDatabase.closeDatabase();
            }
        }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> GetUnuploadedLocationofCheckInOutList = mAceDnsTransactionDatabase.GetUnuploadedLocationofFootSoldier();
        for (int ii = 0; ii < GetUnuploadedLocationofCheckInOutList.size(); ii++) {
            Location currentLocation = GetUnuploadedLocationofCheckInOutList.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            if (currentLocation.getTransId().startsWith("FS")) {
                ArrayList<MallMaster> unUploadedMalldata = mAceDnsTransactionDatabase.getUnuploadedFootSoldier(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedMalldata.size(); jj++) {
                    MallMaster obj = unUploadedMalldata.get(jj);
                    xmlData += "<foot_soldier>";
                    xmlData += location;
                    xmlData += "<footsoldierdata>"
                            + "<foot_soldier_id><![CDATA[" + obj.getFsId() + "]]></foot_soldier_id>"
                            + "<mall_id><![CDATA[" + obj.getMallId() + "]]></mall_id>"
                            + "<mall_name><![CDATA[" + obj.getMallName() + "]]></mall_name>"
                            + "<pincode><![CDATA[" + obj.getPincode() + "]]></pincode>"
                            + "<business_name><![CDATA[" + obj.getArea() + "]]></business_name>"
                            + "<type><![CDATA[" + obj.getType() + "]]></type>"
                            + "<menu_type><![CDATA[" + obj.getMarket() + "]]></menu_type>"
                            + "</footsoldierdata>";
                    xmlData += "</foot_soldier>";
                }
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
