package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitTravelFoodingLodgingExpenseTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    ArrayList<String> tourTransIdList;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format


    public TRANS_SubmitTravelFoodingLodgingExpenseTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xmlData = prepareXMLData_();
        if (finish)
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTourLodgeFoodExpURL_601 + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelFoodingLodgingExpenseTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelFoodingLodgingExpenseTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelFoodingLodgingExpenseTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {

        super.onPostExecute(result);

        if (finish)
            Utils.cancelProgressDialog();

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
        {
            for (int ii = 0; ii < tourTransIdList.size(); ii++) {
                dataHelperObj.updateUnuploadedTourFoodingLodgingExp(tourTransIdList.get(ii));
                dataHelperObj.updateUnuploadedLocation(tourTransIdList.get(ii));
            }
            new TRANS_TravelFoodingLodgingAttachmentExportTask(mContext, "TOUR_TRAVEL", result, finish).execute();

            dataHelperObj.closeDatabase();
//            if (finish) {
//                Toast.makeText(mContext, "Updated successfully", Toast.LENGTH_LONG).show();
//                Intent intent = new Intent(mContext, MenuActivity.class);
//                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
//                mContext.startActivity(intent);
//            }

            if(Constants.nickName.toUpperCase().matches("NIMBUS")){
                Utils.showColorToast(mContext,"Submitted successfully");
            }
        }
        else
        {
            if (finish)
            {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }

        }

    }


    public String prepareXMLData() {
        String xmlData = "";
        try
       {
           tourTransIdList = new ArrayList<>();
           AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
           xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
           ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
           for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
               Location currentLocation = unUploadedTransaction.get(ii);

               String location = "<location>" +
                       "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                       "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                       "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                       "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                       "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                       "</location>";
               if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("TT")) {
                   xmlData += "<tour_expense>";
                   xmlData += location;
                   tourTransIdList.add(currentLocation.getTransId());
                   String[] unUploadedTourExp = dataHelperObj.getUnuploadedTourExp(currentLocation.getTransId());
                   String isSupportingAttached = "no";
                   if (!unUploadedTourExp[12].matches("")) {
                       isSupportingAttached = "yes";
                   }
                   xmlData += "<tour_expense_details>"
                           + "<TOUR_FOOD_LODGE_TRANS_ID><![CDATA[" + unUploadedTourExp[0] + "]]></TOUR_FOOD_LODGE_TRANS_ID>"
                           + "<emp_code><![CDATA[" + unUploadedTourExp[1] + "]]></emp_code>"
                           + "<DEP_TIME><![CDATA[" + unUploadedTourExp[2] + "]]></DEP_TIME>"
                           + "<ARR_TIME><![CDATA[" + unUploadedTourExp[3] + "]]></ARR_TIME>"
                           + "<PARTICULARS><![CDATA[" + unUploadedTourExp[4] + "]]></PARTICULARS>"
                           + "<LOCAL_CONVEYANCE><![CDATA[" + unUploadedTourExp[5] + "]]></LOCAL_CONVEYANCE>"
                           + "<TRAVEL_MODE><![CDATA[" + unUploadedTourExp[6] + "]]></TRAVEL_MODE>"
                           + "<TRANSPORT_FAIR><![CDATA[" + unUploadedTourExp[7] + "]]></TRANSPORT_FAIR>"
                           + "<FOODING_ALLOWANCE><![CDATA[" + unUploadedTourExp[8] + "]]></FOODING_ALLOWANCE>"
                           + "<HOTEL_CHARGE><![CDATA[" + unUploadedTourExp[9] + "]]></HOTEL_CHARGE>"
                           + "<OTHER_EXPENSES><![CDATA[" + unUploadedTourExp[10] + "]]></OTHER_EXPENSES>"
                           + "<REMARKS><![CDATA[" + unUploadedTourExp[11] + "]]></REMARKS>"
                           + "<SUPPORTING_ATTACHED><![CDATA[" + isSupportingAttached + "]]></SUPPORTING_ATTACHED>"
                           + "<ATTACHMENT_ID><![CDATA[" + unUploadedTourExp[12] + "]]></ATTACHMENT_ID>"
                           + "<tour_date><![CDATA[" + unUploadedTourExp[13] + "]]></tour_date>" +
                           "</tour_expense_details>";
                   xmlData += "</tour_expense>";
               }
           }
           xmlData += "</root>";
       }
       catch (Exception e)
       {
           e.printStackTrace();
       }
        return xmlData;

    }
    public String prepareXMLData_() {
        String xmlData = "";
        try
       {
           tourTransIdList = new ArrayList<>();
           AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
           xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
           ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
           for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
               Location currentLocation = unUploadedTransaction.get(ii);

               String location = "<location>" +
                       "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                       "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                       "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                       "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                       "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                       "</location>";
               if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("TT")) {
                   xmlData += "<tour_expense>";
                   xmlData += location;
                   tourTransIdList.add(currentLocation.getTransId());
                   String[] unUploadedTourExp = dataHelperObj.getUnuploadedTourExp_(currentLocation.getTransId());

                   /*String t_date_to = "0000-00-00";
                   if(unUploadedTourExp[4]!=null){
                       t_date_to = unUploadedTourExp[4];
                   }*/

                   if (Constants.menuDetailsObj.getMulti_travel_mode().equalsIgnoreCase("yes")) {
                       xmlData += "<tour_expense_details>"
                               + "<TOUR_EXP_TRANS_ID><![CDATA[" + unUploadedTourExp[0] + "]]></TOUR_EXP_TRANS_ID>"
                               + "<EMP_CODE><![CDATA[" + unUploadedTourExp[1] + "]]></EMP_CODE>"
                               + "<TOUR_TYPE><![CDATA[" + unUploadedTourExp[2] + "]]></TOUR_TYPE>"
                               + "<TOUR_DATE_FROM><![CDATA[" + unUploadedTourExp[3] + "]]></TOUR_DATE_FROM>"
                               + "<TOUR_DATE_TO><![CDATA[" + unUploadedTourExp[4] + "]]></TOUR_DATE_TO>"
                               + "<DEP_TIME><![CDATA[" + unUploadedTourExp[5] + "]]></DEP_TIME>"
                               + "<ARR_TIME><![CDATA[" + unUploadedTourExp[6] + "]]></ARR_TIME>"
                               + "<TOUR_PLACE_FROM><![CDATA[" + unUploadedTourExp[7] + "]]></TOUR_PLACE_FROM>"
                               + "<TOUR_PLACE_TO><![CDATA[" + unUploadedTourExp[8] + "]]></TOUR_PLACE_TO>"
                               + "<LOCAL_CONVEYANCE><![CDATA[" + unUploadedTourExp[9] + "]]></LOCAL_CONVEYANCE>"
                               + "<TRAVEL_MODE><![CDATA[" + unUploadedTourExp[10] + "]]></TRAVEL_MODE>"
                               + "<TRANSPORT_FAIR><![CDATA[" + unUploadedTourExp[11] + "]]></TRANSPORT_FAIR>"

                               + "<TRAVEL_MODE2><![CDATA[" + unUploadedTourExp[25] + "]]></TRAVEL_MODE2>"
                               + "<TRANSPORT_FAIR2><![CDATA[" + unUploadedTourExp[26] + "]]></TRANSPORT_FAIR2>"
                               + "<TRAVEL_MODE3><![CDATA[" + unUploadedTourExp[27] + "]]></TRAVEL_MODE3>"
                               + "<TRANSPORT_FAIR3><![CDATA[" + unUploadedTourExp[28] + "]]></TRANSPORT_FAIR3>"

                               + "<FOODING_ALLOWANCE><![CDATA[" + unUploadedTourExp[12] + "]]></FOODING_ALLOWANCE>"
                               + "<HOTEL_CHARGE><![CDATA[" + unUploadedTourExp[13] + "]]></HOTEL_CHARGE>"
                               + "<REMARKS><![CDATA[" + unUploadedTourExp[14] + "]]></REMARKS>"
                               + "<TRANSPORT_ATTACHMENT><![CDATA[" + unUploadedTourExp[15] + "]]></TRANSPORT_ATTACHMENT>"
                               + "<FOODING_ATTACHMENT><![CDATA[" + unUploadedTourExp[16] + "]]></FOODING_ATTACHMENT>"
                               + "<LODGING_ATTACHMENT><![CDATA[" + unUploadedTourExp[17] + "]]></LODGING_ATTACHMENT>"
                               + "<OTHER_EXPENSES><![CDATA[" + unUploadedTourExp[18] + "]]></OTHER_EXPENSES>"
//                           + "<FLAG><![CDATA[" + unUploadedTourExp[19] + "]]></FLAG>"
                               + "<ATTACHMENT_ID><![CDATA[" + unUploadedTourExp[20] + "]]></ATTACHMENT_ID>"
                               + "<FUEL_BILL_ATTACHMENT><![CDATA[" + unUploadedTourExp[21] + "]]></FUEL_BILL_ATTACHMENT>"
                               + "<MISC><![CDATA[" + unUploadedTourExp[22] + "]]></MISC>"
                               + "<DAILY_ASSISTANCE><![CDATA[" + unUploadedTourExp[23] + "]]></DAILY_ASSISTANCE>"
                               + "<KM_TRAVELED><![CDATA[" + unUploadedTourExp[24] + "]]></KM_TRAVELED>"
                               + "</tour_expense_details>";
                       xmlData += "</tour_expense>";
                   }else{
                       {
                           xmlData += "<tour_expense_details>"
                                   + "<TOUR_EXP_TRANS_ID><![CDATA[" + unUploadedTourExp[0] + "]]></TOUR_EXP_TRANS_ID>"
                                   + "<EMP_CODE><![CDATA[" + unUploadedTourExp[1] + "]]></EMP_CODE>"
                                   + "<TOUR_TYPE><![CDATA[" + unUploadedTourExp[2] + "]]></TOUR_TYPE>"
                                   + "<TOUR_DATE_FROM><![CDATA[" + unUploadedTourExp[3] + "]]></TOUR_DATE_FROM>"
                                   + "<TOUR_DATE_TO><![CDATA[" + unUploadedTourExp[4] + "]]></TOUR_DATE_TO>"
                                   + "<DEP_TIME><![CDATA[" + unUploadedTourExp[5] + "]]></DEP_TIME>"
                                   + "<ARR_TIME><![CDATA[" + unUploadedTourExp[6] + "]]></ARR_TIME>"
                                   + "<TOUR_PLACE_FROM><![CDATA[" + unUploadedTourExp[7] + "]]></TOUR_PLACE_FROM>"
                                   + "<TOUR_PLACE_TO><![CDATA[" + unUploadedTourExp[8] + "]]></TOUR_PLACE_TO>"
                                   + "<LOCAL_CONVEYANCE><![CDATA[" + unUploadedTourExp[9] + "]]></LOCAL_CONVEYANCE>"
                                   + "<TRAVEL_MODE><![CDATA[" + unUploadedTourExp[10] + "]]></TRAVEL_MODE>"
                                   + "<TRANSPORT_FAIR><![CDATA[" + unUploadedTourExp[11] + "]]></TRANSPORT_FAIR>"

                                   + "<TRAVEL_MODE2><![CDATA[" +""+ "]]></TRAVEL_MODE2>"
                                   + "<TRANSPORT_FAIR2><![CDATA[" + "" + "]]></TRANSPORT_FAIR2>"
                                   + "<TRAVEL_MODE3><![CDATA[" + "0" + "]]></TRAVEL_MODE3>"
                                   + "<TRANSPORT_FAIR3><![CDATA[" + "0" + "]]></TRANSPORT_FAIR3>"

                                   + "<FOODING_ALLOWANCE><![CDATA[" + unUploadedTourExp[12] + "]]></FOODING_ALLOWANCE>"
                                   + "<HOTEL_CHARGE><![CDATA[" + unUploadedTourExp[13] + "]]></HOTEL_CHARGE>"
                                   + "<REMARKS><![CDATA[" + unUploadedTourExp[14] + "]]></REMARKS>"
                                   + "<TRANSPORT_ATTACHMENT><![CDATA[" + unUploadedTourExp[15] + "]]></TRANSPORT_ATTACHMENT>"
                                   + "<FOODING_ATTACHMENT><![CDATA[" + unUploadedTourExp[16] + "]]></FOODING_ATTACHMENT>"
                                   + "<LODGING_ATTACHMENT><![CDATA[" + unUploadedTourExp[17] + "]]></LODGING_ATTACHMENT>"
                                   + "<OTHER_EXPENSES><![CDATA[" + unUploadedTourExp[18] + "]]></OTHER_EXPENSES>"
//                           + "<FLAG><![CDATA[" + unUploadedTourExp[19] + "]]></FLAG>"
                                   + "<ATTACHMENT_ID><![CDATA[" + unUploadedTourExp[20] + "]]></ATTACHMENT_ID>"
                                   + "<FUEL_BILL_ATTACHMENT><![CDATA[" + unUploadedTourExp[21] + "]]></FUEL_BILL_ATTACHMENT>"
                                   + "<MISC><![CDATA[" + unUploadedTourExp[22] + "]]></MISC>"
                                   + "<DAILY_ASSISTANCE><![CDATA[" + unUploadedTourExp[23] + "]]></DAILY_ASSISTANCE>"
                                   + "<KM_TRAVELED><![CDATA[" + unUploadedTourExp[24] + "]]></KM_TRAVELED>"
                                   + "</tour_expense_details>";
                           xmlData += "</tour_expense>";
                       }
                   }
               }
           }
           xmlData += "</root>";
       }
       catch (Exception e)
       {
           e.printStackTrace();
       }
        return xmlData;

    }
}
