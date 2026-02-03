package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.bean.CheckInOut;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitNotesInfo extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;

    public TRANS_SubmitNotesInfo(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNotesInfoURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotesInfo: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotesInfo value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotesInfo result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        if (finish) {
            Utils.cancelProgressDialog();
        }
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            mAceDnsTransactionDatabase.UpdateNotesInfoData();

            if (finish) {
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
            }
        } else {
            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
            }
        }
        mAceDnsTransactionDatabase.closeDatabase();
        if (Constants.userDetailsObj.getnotes_info_upload_photo().equalsIgnoreCase("yes")) {
            TRANS_SurveyImageTask notesInfoImage = new TRANS_SurveyImageTask(mContext, "NOTES_INFO_ATTACHMENT", "", false);
            notesInfoImage.execute();
        }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> GetUnuploadedLocationofCheckInOutList = mAceDnsTransactionDatabase.GetUnuploadedLocationofNotesInfo();
        for (int ii = 0; ii < GetUnuploadedLocationofCheckInOutList.size(); ii++) {
            Location currentLocation = GetUnuploadedLocationofCheckInOutList.get(ii);

            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";

            if (currentLocation.getTransId().startsWith("NI")) {
                ArrayList<CheckInOut> unUploadedCheckInOut = mAceDnsTransactionDatabase.GetUnuploadedNotesandInfo(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedCheckInOut.size(); jj++) {
                    CheckInOut detailsObj = unUploadedCheckInOut.get(jj);
                    xmlData += "<NOTES_INFO>";
                    xmlData += location;
                    xmlData += "<NOTES_INFO_DATA>"
                            + "<NOTES_INFO_ID><![CDATA[" + detailsObj.getTrans_id() + "]]></NOTES_INFO_ID>"
                            + "<FEEDBACK><![CDATA[" + detailsObj.getRemark() + "]]></FEEDBACK>"
                            + "<hint_remarks><![CDATA[" + detailsObj.getnotesInfoRemarks() + "]]></hint_remarks>"
                            + "<uploaded_photo><![CDATA[" + detailsObj.getnotesInfoPicture() + "]]></uploaded_photo>"
                            + "</NOTES_INFO_DATA>";
                    xmlData += "</NOTES_INFO>";
                }
            }

        }

        xmlData += "</root>";
        return xmlData;

    }
}

