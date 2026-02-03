package com.forcepower.acedns;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.constants.BaseUrl;

import java.util.ArrayList;

public class TRANS_BusinessProspectCustomizeTransactionTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_BusinessProspectCustomizeTransactionTask(Context context, ArrayList<Location> unUploadedTransaction) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.unUploadedTransaction = unUploadedTransaction;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        prepareXMLData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.businessProspectCustomizeTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_BusinessProspectCustomizeTransactionTask: " + uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_BusinessProspectCustomizeTransactionTask values: " + xmlData);
            POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_BusinessProspectCustomizeTransactionTask result: " + POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(), "trans_id", "location");
            }
        }
        mAceDnsTransactionDatabase.closeDatabase();
    }

    public void prepareXMLData() {
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            ArrayList<commonDatabaseHelper> unUploadedProspect = mAceDnsTransactionDatabase.getUnuploadedBusinessProspectCustomize(currentLocation.getTransId());
            for (int jj = 0; jj < unUploadedProspect.size(); jj++) {
                commonDatabaseHelper detailsObj = unUploadedProspect.get(jj);
                xmlData += "<business_prospect>";
                xmlData += location;
                xmlData += "<business_prospect_details>"
                        + "<prospect_id><![CDATA[" + detailsObj.getItem0() + "]]></prospect_id>"
                        + "<emp_code><![CDATA[" + detailsObj.getItem1() + "]]></emp_code>"
                        + "<create_date><![CDATA[" + detailsObj.getItem2() + "]]></create_date>"
                        + "<referring_cust_area><![CDATA[" + detailsObj.getItem3() + "]]></referring_cust_area>"
                        + "<referring_cust_phone><![CDATA[" + detailsObj.getItem4() + "]]></referring_cust_phone>"
                        + "<referring_cust_tagged_dealer><![CDATA[" + detailsObj.getItem5() + "]]></referring_cust_tagged_dealer>"
                        + "<referred_person_name><![CDATA[" + detailsObj.getItem6() + "]]></referred_person_name>"
                        + "<referred_person_profession><![CDATA[" + detailsObj.getItem7() + "]]></referred_person_profession>"
                        + "<referred_person_phone><![CDATA[" + detailsObj.getItem8() + "]]></referred_person_phone>"
                        + "<referred_person_email><![CDATA[" + detailsObj.getItem9() + "]]></referred_person_email>"
                        + "<referred_person_firm><![CDATA[" + detailsObj.getItem10() + "]]></referred_person_firm>"
                        + "<referred_person_district><![CDATA[" + detailsObj.getItem11() + "]]></referred_person_district>"
                        + "<referred_person_zone><![CDATA[" + detailsObj.getItem12() + "]]></referred_person_zone>"
                        + "<referred_person_route><![CDATA[" + detailsObj.getItem13() + "]]></referred_person_route>"
                        + "<referred_person_dealer><![CDATA[" + detailsObj.getItem14() + "]]></referred_person_dealer>"
                        + "</business_prospect_details>";
                xmlData += "</business_prospect>";
            }
        }
        xmlData += "</root>";
    }
}
