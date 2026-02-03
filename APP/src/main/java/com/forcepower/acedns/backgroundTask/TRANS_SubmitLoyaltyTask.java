package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.CardTransactionDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.LoyaltyCustomerDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitLoyaltyTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String newCustomerCode = "", newCustomerName = "", newCustomerNumber = "";
    ArrayList<String> loyaltyTransIdList;
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";

    public TRANS_SubmitLoyaltyTask(Context context) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        message = "Uploading data.Please wait.";
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        loyaltyPurchaseUpdateTime = dataHelperObj.getlastDownloadTime("loyalty_purchase_details");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, message);
        xmlData = prepareXMLData();
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitLoyaltyTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&last_loyalty_purchase_update_time=" + loyaltyPurchaseUpdateTime;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitLoyaltyTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitLoyaltyTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitLoyaltyTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2") || result.equalsIgnoreCase("4")) {
            Utils.cancelProgressDialog();
            for (int ii = 0; ii < loyaltyTransIdList.size(); ii++) {
                dataHelperObj.updateUnuploadedLocation(loyaltyTransIdList.get(ii));
            }
            dataHelperObj.updateUnuploadedCardTransaction();
            dataHelperObj.closeDatabase();
            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            } else if (result.equalsIgnoreCase("4")) {
                Constants.loyaltyDataRefresh = true;
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            } else {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        } else {
            Utils.cancelProgressDialog();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            dataHelperObj.closeDatabase();
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        }
    }

//	public  String prepareXMLData(){
//		String xmlData = "";
//		xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
//		ArrayList<LoyaltyLocation> unUploadedTransactionAll = dataHelperObj.getUnuploadedLoyaltyTransaction();
//		for(int ii=0;ii<unUploadedTransactionAll.size();ii++){
//			LoyaltyLocation currentLocation = unUploadedTransactionAll.get(ii);
//			 xmlData += "<loyalty>";
//			 String location = "<location>" +
//					 				"<loyalty_card_no><![CDATA[" + currentLocation.getCardNumber() + "]]></loyalty_card_no>" + 
//					 				"<trans_id><![CDATA[" + currentLocation.getTransactionId() + "]]></trans_id>" + 
//					 				"<outlet_user_code><![CDATA[" + currentLocation.getOutletUserCode() + "]]></outlet_user_code>" + 
//					 				"<latt><![CDATA[" + currentLocation.getLatt() + "]]></latt>" + 
//					 				"<longi><![CDATA[" + currentLocation.getLongi() + "]]></longi>" +
//					 				"<date><![CDATA[" + currentLocation.getDate() + "]]></date>"+
//							   "</location>";
//			 xmlData += location;
//			 if(currentLocation.getTransactionId().contains(Constants.employeeDetailObject.getEmpCode())){
//				 LoyaltyCustomerDetails customerData = dataHelperObj.getNewLoyaltyCustomer(currentLocation.getCardNumber());
//				 xmlData += "<loyaltycustomerdata>"  
//			            	+ "<transaction_id><![CDATA[" + currentLocation.getTransactionId().replace("\n", "") + "]]></transaction_id>"	
//			            	+ "<loyalty_card_holder_code><![CDATA[" + customerData.getCardHolderCode().replace("\n", "") + "]]></loyalty_card_holder_code>" 
//			            	+ "<loyalty_card_holder_name><![CDATA[" + customerData.getCardHolderName().replace("\n", "") + "]]></loyalty_card_holder_name>" 
//			            	+ "<loyalty_card_no><![CDATA[" + customerData.getCardNumber().replace("\n", "") + "]]></loyalty_card_no>" 
//			            	+ "<card_type><![CDATA[" + customerData.getCardType().replace("\n", "") + "]]></card_type>"	
//			            	+ "<total_purchase_value><![CDATA[" + customerData.getPurchaseValue().replace("\n", "") + "]]></total_purchase_value>" 
//			            	+ "<total_reward_point><![CDATA[" + customerData.getRewardPoint().replace("\n", "") + "]]></total_reward_point>" 
//			            	+ "<last_update_on><![CDATA[" + customerData.getLastUpdate().replace("\n", "") + "]]></last_update_on>" 
//			            	+ "<redeemed><![CDATA[" + customerData.getRedeemed().replace("\n", "") + "]]></redeemed>" 
//			            	+ "<phone_no><![CDATA[" + customerData.getPhone().replace("\n", "") + "]]></phone_no>" 
//			            	+ "<address><![CDATA[" + customerData.getAddress().replace("\n", "") + "]]></address>" +
//						 "</loyaltycustomerdata>";
//			 }
//			 ArrayList<CardTransactionDetails> unUploadedCardTrans = dataHelperObj.getUnuploadedCardTrans(currentLocation.getTransactionId());
//			 for(int jj=0;jj<unUploadedCardTrans.size();jj++){
//				 CardTransactionDetails currentHeader = unUploadedCardTrans.get(jj);
//				 xmlData += "<loyaltydata>"  
//				            	+ "<transaction_id><![CDATA[" + currentHeader.getTransactionId().replace("\n", "") + "]]></transaction_id>"	
//				            	+ "<purchase_value><![CDATA[" + currentHeader.getPurchaseValue() + "]]></purchase_value>" 
//				            	+ "<trans_type><![CDATA[" + currentHeader.getVerticalName() + "]]></trans_type>" 
//				            	+ "<outlet_code><![CDATA[" + currentHeader.getOutletCode() + "]]></outlet_code>" +
//							 "</loyaltydata>";
//			 }
//			 xmlData += "</loyalty>";
//		}
//		xmlData += "</root>";
//		return xmlData;
//	}


    public String prepareXMLData() {
        String xmlData = "";
        loyaltyTransIdList = new ArrayList<String>();
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
            if (currentLocation.getTransId().startsWith("L")) {
                xmlData += "<loyalty>";
                xmlData += location;
                loyaltyTransIdList.add(currentLocation.getTransId());
                ArrayList<CardTransactionDetails> unUploadedCardTrans = dataHelperObj.getUnuploadedCardTrans(currentLocation.getTransId());
                LoyaltyCustomerDetails customerData = dataHelperObj.getLoyaltyCustomer(unUploadedCardTrans.get(0).getCardNumber());
                xmlData += "<loyaltycustomerdata>"
                        + "<transaction_id><![CDATA[" + currentLocation.getTransId().replace("\n", "") + "]]></transaction_id>"
                        + "<loyalty_card_holder_code><![CDATA[" + customerData.getCardHolderCode().replace("\n", "") + "]]></loyalty_card_holder_code>"
                        + "<loyalty_card_holder_name><![CDATA[" + customerData.getCardHolderName().replace("\n", "") + "]]></loyalty_card_holder_name>"
                        + "<loyalty_card_no><![CDATA[" + customerData.getCardNumber().replace("\n", "") + "]]></loyalty_card_no>"
                        + "<card_type><![CDATA[" + customerData.getCardType().replace("\n", "") + "]]></card_type>"
                        + "<total_purchase_value><![CDATA[" + customerData.getPurchaseValue().replace("\n", "") + "]]></total_purchase_value>"
                        + "<total_reward_point><![CDATA[" + customerData.getRewardPoint().replace("\n", "") + "]]></total_reward_point>"
                        + "<last_update_on><![CDATA[" + customerData.getLastUpdate().replace("\n", "") + "]]></last_update_on>"
                        + "<redeemed><![CDATA[" + customerData.getRedeemed().replace("\n", "") + "]]></redeemed>"
                        + "<phone_no><![CDATA[" + customerData.getPhone().replace("\n", "") + "]]></phone_no>"
                        + "<address><![CDATA[" + customerData.getAddress().replace("\n", "") + "]]></address>"
                        + "<vehicle_no><![CDATA[" + customerData.getVehicleNo().replace("\n", "") + "]]></vehicle_no>" +
                        "</loyaltycustomerdata>";
                for (int jj = 0; jj < unUploadedCardTrans.size(); jj++) {
                    CardTransactionDetails currentHeader = unUploadedCardTrans.get(jj);
                    xmlData += "<loyaltydata>"
                            + "<transaction_id><![CDATA[" + currentHeader.getTransactionId().replace("\n", "") + "]]></transaction_id>"
                            + "<purchase_value><![CDATA[" + currentHeader.getPurchaseValue() + "]]></purchase_value>"
                            + "<trans_type><![CDATA[" + currentHeader.getVerticalName() + "]]></trans_type>"
                            + "<vehicle_no><![CDATA[" + currentHeader.getVehicleNo() + "]]></vehicle_no>"
                            + "<vehicle_type><![CDATA[" + currentHeader.getVehicleType() + "]]></vehicle_type>"
                            + "<redeemed_points><![CDATA[" + currentHeader.getPointsRedeemed() + "]]></redeemed_points>"
                            + "<rds_code><![CDATA[" + currentHeader.getOutletCode() + "]]></rds_code>" +
                            "</loyaltydata>";
                }
                xmlData += "</loyalty>";
            }
        }
        xmlData += "</root>";
        dataHelperObj.closeDatabase();
        return xmlData;
    }
}
