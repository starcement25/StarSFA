package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.TourSwapDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_TourDaySwapTransactionTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    Boolean isUnuploadedDataPresent = false;

    public TRANS_TourDaySwapTransactionTask(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish) {
            Utils.showProgressDialog(mContext, "Uploading swap information. Please wait..");
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (isUnuploadedDataPresent) {
            if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
                try {
                    prepareXMLData();
                    String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.tourDaySwappingTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_TourDaySwapTransactionTask: " +uri);
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_TourDaySwapTransactionTask value: " +xmlData);
                    POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
                } catch (Exception e) {
                    POST_result = "Network Failure";
                } finally {

                }
            }
        }

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_TourDaySwapTransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {

            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
            }
            if (finish) {
                Utils.cancelProgressDialog();
//				if (result.equalsIgnoreCase("2")) {
//					Constants.dataResfresh = true;
//				}
            }
        } else {
            if (finish) {
                Utils.cancelProgressDialog();
                if (isUnuploadedDataPresent)
                    Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
            }
        }
        mAceDnsTransactionDatabase.closeDatabase();
    }


    public void prepareXMLData() {
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("TOUR SWAP", "");
        if (unUploadedTransaction.size() > 0) {
            isUnuploadedDataPresent = true;
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

                xmlData += "<tour_swap>";
                xmlData += location;
                ArrayList<TourSwapDetails> unUploadedStockAudit = mAceDnsTransactionDatabase.getUnuploadedTourSwapDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedStockAudit.size(); jj++) {
                    TourSwapDetails currentHeader = unUploadedStockAudit.get(jj);

                    xmlData += "<tour_swap_details>"
                            + "<tour_day_swap_trans_id><![CDATA[" + currentHeader.getTransactionId() + "]]></tour_day_swap_trans_id>"
                            + "<emp_code><![CDATA[" + currentHeader.getEmpCode() + "]]></emp_code>"
                            + "<actual_tourday><![CDATA[" + currentHeader.getActualTourDay() + "]]></actual_tourday>"
                            + "<deviate_tourday><![CDATA[" + currentHeader.getDeviateTourDay() + "]]></deviate_tourday>"
                            + "<swap_date_actual><![CDATA[" + currentHeader.getSwapDateActual() + "]]></swap_date_actual>"
                            + "<swap_date_deviate><![CDATA[" + currentHeader.getSwapDateDeviate() + "]]></swap_date_deviate>" +
                            "</tour_swap_details>";

                }
                xmlData += "</tour_swap>";

            }
            xmlData += "</root>";
        }

    }
}
