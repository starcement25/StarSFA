package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSaudaOreder extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSaudaOreder(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        message = "Uploading data.Please wait.";
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xmlData = prepareXMLData();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Uploading data.Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        // mProgressDialog.cancel();
        if (HTTPUtils.isConnectionPossible(mContext)
               ) {
            try {

                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitSaudaURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaOreder: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaOreder value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);

            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaOreder result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            dataHelperObj.UpadateSaudaLocation();
            dataHelperObj.UPDATESaudaHeader();
            dataHelperObj.UPDATESaudaDetails();
            dataHelperObj.closeDatabase();
            Toast.makeText(mContext, "Transaction submitted successfully", 15000).show();
            if (finish) {
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                } else {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            dataHelperObj.closeDatabase();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" + "<emp_code><![CDATA["
                    + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId()
                    + "]]></trans_id>" + "<latt><![CDATA["
                    + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude()
                    + "]]></longi>" + "<date><![CDATA["
                    + currentLocation.getDate() + "]]></date>" + "</location>";

            if (currentLocation.getTransId().startsWith("FT") || currentLocation.getTransId().startsWith("NFT")) {
                xmlData += "<sauda>";
                xmlData += location;
                xmlData += "<saudadata>";

                SaudaHeader mSaudaHeader = dataHelperObj.GETSaudaHeader(currentLocation.getTransId());

                xmlData += "<sauda_header>" + "<sauda_no><![CDATA["
                        + mSaudaHeader.getSaudaNo() + "]]></sauda_no>"
                        + "<TD><![CDATA[" + mSaudaHeader.getTD() + "]]></TD>"
                        + "<d_instruction><![CDATA["
                        + mSaudaHeader.getRemarks() + "]]></d_instruction>"
                        + "<broker_id><![CDATA[" + mSaudaHeader.getBrokerId()
                        + "]]></broker_id>" + "<transaction_type><![CDATA["
                        + mSaudaHeader.getTransactionType()
                        + "]]></transaction_type>" + "<vat><![CDATA["
                        + mSaudaHeader.getVat() + "]]></vat>"
                        + "<branch_code><![CDATA["
                        + mSaudaHeader.getBranchCode() + "]]></branch_code>"
                        + "<valid_from><![CDATA["
                        + mSaudaHeader.getSaudaValidity() + "]]></valid_from>"
                        + "<customer_code><![CDATA[" + mSaudaHeader.getCustomerCode() + "]]></customer_code>"
                        + "<customer_phone><![CDATA[" + mSaudaHeader.getCustomerPhone() + "]]></customer_phone>"
                        + "<customer_email><![CDATA[" + mSaudaHeader.getCustomerEmail() + "]]></customer_email>"
                        + "<PO_no><![CDATA[" + mSaudaHeader.getpoNo() + "]]></PO_no>"
                        + "</sauda_header>";

                ArrayList<SaudaDetails> unUploadedSaudaDetails = dataHelperObj
                        .GETSaudaDetails(mSaudaHeader.getSaudaNo());
                for (int jj = 0; jj < unUploadedSaudaDetails.size(); jj++) {
                    SaudaDetails saudadetails = unUploadedSaudaDetails.get(jj);
                    xmlData += "<sauda_details>" + "<sauda_no><![CDATA["
                            + saudadetails.getSaudaNo() + "]]></sauda_no>"
                            + "<Sku_code><![CDATA[" + saudadetails.getSkuCode()
                            + "]]></Sku_code>" + "<qty><![CDATA["
                            + saudadetails.getQuantity() + "]]></qty>"
                            + "<TD><![CDATA[" + saudadetails.getTD()
                            + "]]></TD>" + "<premium><![CDATA["
                            + saudadetails.getPremium() + "]]></premium>"
                            + "<sale_rate><![CDATA["
                            + saudadetails.getSaleRate() + "]]></sale_rate>"
                            + "<VAT><![CDATA[" + saudadetails.getVat()
                            + "]]></VAT>" + "<amount><![CDATA["
                            + saudadetails.getAmount() + "]]></amount>"
                            + "<freight_charge><![CDATA[" + saudadetails.getFreightCharge() + "]]></freight_charge>"
                            + "<mrp_code><![CDATA[" + saudadetails.getMrpCode() + "]]></mrp_code>"
                            + "<primary_freight ><![CDATA[" + saudadetails.getPrimaryFreight() + "]]></primary_freight>"
                            + "<depot_cost><![CDATA[" + saudadetails.getDepotCost() + "]]></depot_cost>"
                            + "<LIQUID_TD><![CDATA[" + saudadetails.getLiquidTD() + "]]></LIQUID_TD>"
                            + "<BROKERAGE_COST><![CDATA[" + saudadetails.getBrokarageCost() + "]]></BROKERAGE_COST>"
                            + "<HONEYCOMB_COST><![CDATA[" + saudadetails.getHoneyCombCost() + "]]></HONEYCOMB_COST>"
                            + "<MARGIN_COST><![CDATA[" + saudadetails.getMarginCost() + "]]></MARGIN_COST>"
                            + "<additional_TD><![CDATA[" + saudadetails.getadditionalTD() + "]]></additional_TD>"
                            + "<additional_premium><![CDATA[" + saudadetails.getadditionalPremium() + "]]></additional_premium>"
                            + "</sauda_details>";
                }

                xmlData += "</saudadata></sauda>";
            } else if (currentLocation.getTransId().trim().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = dataHelperObj.getUnuploadedAttendance(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedAttendance.size(); jj++) {
                    Attendance detailsObj = unUploadedAttendance.get(jj);
                    xmlData += "<attendancedata>"
                            + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                            + "<date><![CDATA[" + detailsObj.getDate() + "]]></date>" +
                            "</attendancedata>";
                }
                xmlData += "</attendance>";
            }
        }
        xmlData += "</root>";
        return xmlData;
    }
}
