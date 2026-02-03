package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.OrderDetails;
import com.forcepower.acedns.bean.OrderHeader;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitReplaceMentOrder extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog pd;

    public TRANS_SubmitReplaceMentOrder(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        pd = new ProgressDialog(mContext);
        pd.setMessage("Submitting transaction.Please wait");
        pd.setCancelable(false);
        pd.show();
        xmlData = XmlData();
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitReplaceMentOrder: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitReplaceMentOrder value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitReplaceMentOrder result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        pd.cancel();
        if (result.length() > 0) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpdateReplacementLocationData();
                dataHelperObj.updateUnuploadedOrderHeader();
                dataHelperObj.updateUnuploadedOrderDetails();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT)
                        .show();
            }
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
//			if (finish) {
//				new DATA_DownloadPendingBroadcastTask(mContext, true).execute();
//			} else {
//				new DATA_DownloadPendingBroadcastTask(mContext, false)
//						.execute();
//			}
        }
    }

    private String XmlData() {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String tada = "";
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")){
                tada = currentLocation.getTA_DA_mode();
            }else{
                tada = "";
            }
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "<TA_DA_MODE><![CDATA[" + tada + "]]></TA_DA_MODE>" +
                    "</location>";
            if (currentLocation.getTransId().startsWith("RP")) {
                xmlData += "<order>";
                xmlData += location;
                ArrayList<OrderHeader> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrdrHeadr(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                    OrderHeader currentHeader = unUploadedOrdrHeadr.get(jj);
                    String customerName = "", customerAddress = "", customerNumbr = "", customerPin = "", customerAreaCode = "", customerFlag = "", customerAreaName = "", cust_type = "";
                    CustomerDetails addedObj = dataHelperObj.getDetailsIfNewCustomer(currentHeader.getCustomerCode());
                    if (addedObj != null && addedObj.getCustomerCode().substring(0, 1).equalsIgnoreCase("N") && addedObj.getFlag().equalsIgnoreCase("0")) {
                        customerName = addedObj.getCustomerName();
                        customerAddress = addedObj.getAddress();
                        customerNumbr = addedObj.getNumber();
                        customerPin = addedObj.getPin();
                        customerAreaCode = addedObj.getNewRouteCode();
                        customerAreaName = addedObj.getNewRouteName();
                        cust_type = addedObj.getCustomerType();
                        customerFlag = "N";
                    }
                    xmlData += "<orderdata>";
                    xmlData += "<order_header>"
                            + "<order_no><![CDATA[" + currentHeader.getOrderNo() + "]]></order_no>"
                            + "<customer_name><![CDATA[" + customerName + "]]></customer_name>"
                            + "<Phone_no><![CDATA[" + customerNumbr + "]]></Phone_no>"
                            + "<address><![CDATA[" + customerAddress + "]]></address>"
                            + "<pin_code><![CDATA[" + customerPin + "]]></pin_code>"
                            + "<area><![CDATA[" + customerAreaCode + "]]></area>"
                            + "<area_name><![CDATA[" + customerAreaName + "]]></area_name>"
                            + "<TD><![CDATA[" + currentHeader.getTrdDiscnt() + "]]></TD>"
                            + "<sale_type><![CDATA[" + currentHeader.getSalesType() + "]]></sale_type>"
                            + "<order_value><![CDATA[" + currentHeader.getOrder_value() + "]]></order_value>"
                            + "<d_instruction><![CDATA[" + currentHeader.getInstruction() + "]]></d_instruction>"
                            + "<tag_distributor_code><![CDATA[" + currentHeader.getTag_distributor_code() + "]]></tag_distributor_code>"
                            + "<cust_type><![CDATA[" + cust_type + "]]></cust_type>"
                            + "<customer_flag><![CDATA[" + customerFlag + "]]></customer_flag>"
                            + "<transaction_type><![CDATA[" + currentHeader.getTransaction_type() + "]]></transaction_type>"
                            + "<vat><![CDATA[" + currentHeader.getVAT() + "]]></vat>"
                            + "<grn_no><![CDATA[" + currentHeader.getGrnNo() + "]]></grn_no>"
                            + "<FREIGHT_COMPONENT><![CDATA[" + "" + "]]></FREIGHT_COMPONENT>"
                            + "<HINT_REMARKS><![CDATA[" + "" + "]]></HINT_REMARKS>"
                            + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>" +
                            "</order_header>";
                    ArrayList<OrderDetails> unUploadedOrdrDetails = dataHelperObj.getUnuploadedOrdrDetails(currentHeader.getOrderNo(), currentHeader.getTransaction_type());
                    for (int kk = 0; kk < unUploadedOrdrDetails.size(); kk++) {
                        OrderDetails currentDetails = unUploadedOrdrDetails.get(kk);
                        String mrp = currentDetails.getMrpCode();
                        String[] dataArray = mrp.split("-");
                        xmlData += "<order_details>"
                                + "<Order_no><![CDATA[" + currentDetails.getOrderNo() + "]]></Order_no>"
                                + "<Sku_code><![CDATA[" + currentDetails.getSkuCode() + "]]></Sku_code>"
                                + "<qty><![CDATA[" + currentDetails.getQty() + "]]></qty>"
                                + "<TD><![CDATA[" + currentDetails.getTD() + "]]></TD>"
                                + "<sale_rate><![CDATA[" + currentDetails.getSaleRate() + "]]></sale_rate>"
                                + "<VAT><![CDATA[" + currentDetails.getVAT() + "]]></VAT>"
                                + "<amount><![CDATA[" + currentDetails.getAmount() + "]]></amount>"
                                + "<mrp_code><![CDATA[" + dataArray[0] + "]]></mrp_code>" +
                                "</order_details>";
                    }
                    xmlData += "</orderdata>";
                }
                xmlData += "</order>";
            } else if (currentLocation.getTransId().startsWith("A")) {
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
        dataHelperObj.closeDatabase();
        return xmlData;
    }
}
