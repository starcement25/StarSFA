package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.ActivitySurveyOffer;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSurveyOfferTask extends AsyncTask<String, Void, String> {

    public ActivitySurveyOffer.AsyncResponse delegate = null;//Call back interface
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSurveyOfferTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
    }

    public TRANS_SubmitSurveyOfferTask(Context context, boolean finish, ActivitySurveyOffer.AsyncResponse asyncResponse) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        this.delegate = asyncResponse;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.Please wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitOfferSurveyURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode();
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyOfferTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyOfferTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyOfferTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (finish) {
            if ((mProgressDialog != null) && mProgressDialog.isShowing()) {
                mProgressDialog.dismiss();
            }
        }

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            dataHelperObj.UpadateLocationTableForOffers();

            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
            }
        }
        dataHelperObj.closeDatabase();
        new TRANS_UploadImagesOffer(mContext, "SURVEY_OFFER", result, finish, delegate).execute();
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetSurveyOfferLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>"
                    + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "</location>";

            xmlData += "<offer>";
            xmlData += location;

            ArrayList<SurveyInput> unUploadedSurveyDetails = dataHelperObj.GETUnuploadedOfferDetails(currentLocation.getTransId());
            for (int jj = 0; jj < unUploadedSurveyDetails.size(); jj++) {
                SurveyInput mSaudaDetails = unUploadedSurveyDetails.get(jj);
                xmlData += "<offerdata>"
                        + "<offer_trans_id><![CDATA[" + mSaudaDetails.gettransId() + "]]></offer_trans_id>"
                        + "<row_id><![CDATA[" + mSaudaDetails.getSurveyRowId() + "]]></row_id>"
                        + "<action_id><![CDATA[" + mSaudaDetails.getSurveyActionId() + "]]></action_id>"
                        + "<mall_id><![CDATA[" + mSaudaDetails.getmallID() + "]]></mall_id>"
                        + "<business_name><![CDATA[" + mSaudaDetails.getbusinessName() + "]]></business_name>"
                        + "<value><![CDATA[" + mSaudaDetails.getValue() + "]]></value>"
                        + "<type><![CDATA[" + mSaudaDetails.getSurveyType() + "]]></type>"
                        + "<survey_id><![CDATA[" + mSaudaDetails.getsurveyId() + "]]></survey_id>"
                        + "</offerdata>";
            }
            xmlData += "</offer>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
