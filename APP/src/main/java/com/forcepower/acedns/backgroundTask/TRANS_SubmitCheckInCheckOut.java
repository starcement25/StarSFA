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

public class TRANS_SubmitCheckInCheckOut extends AsyncTask<String, Void, String> {
    public String str_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitCheckInCheckOut(Context context, boolean finish, String status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.str_getStatus = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (str_getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData();
                //for check out data submit
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitCheckInOutURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCheckInCheckOut: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCheckInCheckOut value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCheckInCheckOut result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (str_getStatus.equals("SUBMIT")) {
            Utils.cancelProgressDialog();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateCheckInOutLocationData();
                if (finish) {
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    }
                }
            } else {

                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
            }
            mAceDnsTransactionDatabase.closeDatabase();
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateCheckInOutLocationData();
                mAceDnsTransactionDatabase.closeDatabase();
            }
        }
        if (Constants.userDetailsObj.getCheckInOutTypeVal().contains("uploadphoto")) {
            TRANS_SurveyImageTask notesInfoImage = new TRANS_SurveyImageTask(mContext, "CHECK_IN_OUT", "", false);
            notesInfoImage.execute();
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> GetUnuploadedLocationofCheckInOutList = mAceDnsTransactionDatabase.GetUnuploadedLocationofCheckInOut();
        for (int ii = 0; ii < GetUnuploadedLocationofCheckInOutList.size(); ii++) {
            Location currentLocation = GetUnuploadedLocationofCheckInOutList.get(ii);

            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";

            if (currentLocation.getTransId().startsWith("CI")) {
                ArrayList<CheckInOut> unUploadedCheckInOut = mAceDnsTransactionDatabase.getUnuploadedCheckInOut(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedCheckInOut.size(); jj++)
                {
                    CheckInOut detailsObj = unUploadedCheckInOut.get(jj);
                    xmlData += "<check_in_out>";
                    xmlData += location;
                    String customer_code = detailsObj.getCustomer_code();
                    ArrayList<String> custLatLong=mAceDnsTransactionDatabase.getcurrentCustomerLatLong(customer_code);
                    String currentCustLat="",currentCustomerLong="";
                    if(custLatLong.size()==2)
                    {
                        currentCustLat=custLatLong.get(0);
                        currentCustomerLong=custLatLong.get(1);
                    }
                    xmlData += "<checkinoutdata>"
                            + "<trans_id><![CDATA[" + detailsObj.getTrans_id() + "]]></trans_id>"
                            + "<check_in_time><![CDATA[" + detailsObj.getCheck_in_time() + "]]></check_in_time>"
                            + "<customer_code><![CDATA[" + customer_code + "]]></customer_code>"
                            + "<check_out_time><![CDATA[" + detailsObj.getCheck_out_time() + "]]></check_out_time>"
                            + "<remarks><![CDATA[" + detailsObj.getRemark() + "]]></remarks>"
                            + "<hint_remarks><![CDATA[" + detailsObj.getHint() + "]]></hint_remarks>"
                            + "<product_tagging><![CDATA[" + detailsObj.getTaggedProduct() + "]]></product_tagging>"
                            + "<uploaded_photo><![CDATA[" + detailsObj.getnotesInfoPicture() + "]]></uploaded_photo>"
                            + "<base_latt><![CDATA[" + currentCustLat + "]]></base_latt>"
                            + "<base_longi><![CDATA[" +currentCustomerLong + "]]></base_longi>"
                            + "</checkinoutdata>";
                    xmlData += "</check_in_out>";
                }
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
