package com.forcepower.acedns.util;

import android.content.Context;

import com.forcepower.acedns.backgroundTask.TRANS_NewCustomerImageTask;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.OrderDetails;
import com.forcepower.acedns.bean.OrderHeader;
import com.forcepower.acedns.bean.PaymentDetails;
import com.forcepower.acedns.bean.PaymentHeader;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.bean.InvoiceInformation;
import com.forcepower.acedns.bean.JointWorkObservation;
import com.forcepower.acedns.bean.SchemeSummary;
import com.forcepower.acedns.bean.YellowCard;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class UploadUnuploadedData {

    public static String prepareXMLData(Context mContext) {
        if (!Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
            String xmlData = "";
            AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
            xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
            ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
            if(unUploadedTransaction.size()>0)
            {
                Constants.unUploadedTransactionGlobal=new ArrayList<>();
                Constants.unUploadedTransactionGlobal=unUploadedTransaction;
            }
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
                        "<PURPOSE_OF_VISIT><![CDATA["+ currentLocation.getPurpose_of_visit() +"]]></PURPOSE_OF_VISIT>"+
                        "</location>";

                if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
                    location = "<location>" +
                            "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                            "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                            "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                            "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                            "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                            "<purpose_of_visit><![CDATA["+ currentLocation.getPurpose_of_visit() +"]]></purpose_of_visit>"+
                            "</location>";
                }


                if (currentLocation.getTransId().startsWith("O")) {
                    xmlData += "<order>";
                    xmlData += location;
                    ArrayList<OrderHeader> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrdrHeadr(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                        String hintRemarks = dataHelperObj.getUnuploadedHintRemarks(currentLocation.getTransId());
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
                                + "<vertical_value><![CDATA[" + currentHeader.getVerticalValue() + "]]></vertical_value>"
                                + "<DESTINATION_CODE><![CDATA[" + currentHeader.getDestinationCode() + "]]></DESTINATION_CODE>"
                                + "<ORDER_TYPE><![CDATA[" + currentHeader.getOrderType() + "]]></ORDER_TYPE>"
                                + "<FREIGHT_COMPONENT><![CDATA[" + currentHeader.getFreightComponent() + "]]></FREIGHT_COMPONENT>"
                                + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                + "<GST_type><![CDATA[" + currentHeader.getGstType() + "]]></GST_type>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                + "<base_latt><![CDATA[" + currentHeader.getcustomerLat() + "]]></base_latt>"
                                + "<base_longi><![CDATA[" + currentHeader.getcustomerLong() + "]]></base_longi>"
                                + "<image><![CDATA[" + currentHeader.getcustomerImage() + "]]></image>"
                                + "<freight_component_value><![CDATA[" + currentHeader.getFreight_component_value() + "]]></freight_component_value>"
//                                + "<PRICE_VALIDATION_TYPE><![CDATA[" + currentHeader.getPriceValidationType() + "]]></PRICE_VALIDATION_TYPE>"
                                + "</order_header>";
                        ArrayList<OrderDetails> unUploadedOrdrDetails = dataHelperObj.getUnuploadedOrdrDetails(currentHeader.getOrderNo(), currentHeader.getTransaction_type());
                        for (int kk = 0; kk < unUploadedOrdrDetails.size(); kk++) {
                            OrderDetails currentDetails = unUploadedOrdrDetails.get(kk);
                            String mrp = currentDetails.getMrpCode();
                            String mrpCode = "";
                            if (mrp != null && mrp.contains("-")) {
                                String[] dataArray = mrp.split("-");
                                mrpCode = dataArray[0];
                            }

                            xmlData += "<order_details>"
                                    + "<Order_no><![CDATA[" + currentDetails.getOrderNo() + "]]></Order_no>"
                                    + "<Sku_code><![CDATA[" + currentDetails.getSkuCode() + "]]></Sku_code>"
                                    + "<qty><![CDATA[" + currentDetails.getQty() + "]]></qty>"
                                    + "<TD><![CDATA[" + currentDetails.getTD() + "]]></TD>"
                                    + "<PREMIUM><![CDATA[" + currentDetails.getPremium() + "]]></PREMIUM>"
                                    + "<sale_rate><![CDATA[" + currentDetails.getSaleRate() + "]]></sale_rate>"
                                    + "<VAT><![CDATA[" + currentDetails.getVAT() + "]]></VAT>"
                                    + "<amount><![CDATA[" + currentDetails.getAmount() + "]]></amount>"
                                    + "<UOM><![CDATA[" + currentDetails.getUom() + "]]></UOM>"
                                    + "<mrp_code><![CDATA[" + mrpCode + "]]></mrp_code>"
                                    +"<weightage><![CDATA[" + currentDetails.getWeightage() + "]]></weightage>"
                                    + "<remarks><![CDATA["+ currentDetails.getRemarks() +"]]></remarks>"
                                    +"<input_size><![CDATA["+ currentDetails.getInput_size() +"]]></input_size>"
                                    +
                                    "</order_details>";
                        }
                        xmlData += "</orderdata>";
                    }
                    xmlData += "</order>";
                }
                else if ((currentLocation.getTransId().startsWith("P")) && !(currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("PA")))
                {
                    xmlData += "<payment>";
                    xmlData += location;
                    ArrayList<PaymentHeader> unUploadedPaymentHeader = dataHelperObj.getUnuploadedPaymentHeadr(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedPaymentHeader.size(); jj++) {
                        PaymentHeader currentHeader = unUploadedPaymentHeader.get(jj);
                        String payMode = "";
                        switch (currentHeader.getCashCheque()) {
                            case 0:
                                payMode = "CASH";
                                break;
                            case 1:
                                payMode = "CHEQUE";
                                break;
                            case 2:
                                payMode = "NEFT";
                                break;
                            case 3:
                                payMode = "RTGS";
                                break;
                            case 4:
                                payMode = "IMPS";
                                break;
                        }
                        xmlData += "<paymentdata>";
                        xmlData += "<payment_header>"
                                + "<receipt_id><![CDATA[" + currentHeader.getReceiptId() + "]]></receipt_id>"
                                + "<amount><![CDATA[" + currentHeader.getAmount() + "]]></amount>"
                                + "<cash_cheque><![CDATA[" + payMode + "]]></cash_cheque>"
                                + "<cheque_no><![CDATA[" + currentHeader.getChequeNo() + "]]></cheque_no>"
                                + "<date><![CDATA[" + currentHeader.getDate() + "]]></date>"
                                + "<bank><![CDATA[" + currentHeader.getBank() + "]]></bank>"
                                + "<sale_type><![CDATA[" + currentHeader.getSaleType() + "]]></sale_type>"
                                + "<p_remark><![CDATA[" + currentHeader.getInstruction() + "]]></p_remark>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                + "<base_latt><![CDATA[" + currentHeader.getcustomerLat() + "]]></base_latt>"
                                + "<base_longi><![CDATA[" + currentHeader.getcustomerLong() + "]]></base_longi>"
                                + "<image><![CDATA[" + currentHeader.getcustomerImage() + "]]></image>"
                                +"</payment_header>";
                        ArrayList<PaymentDetails> unUploadedPaymentDetails = dataHelperObj.getUnuploadedPaymentDetails(currentHeader.getReceiptId());
                        for (int kk = 0; kk < unUploadedPaymentDetails.size(); kk++) {
                            PaymentDetails currentDetails = unUploadedPaymentDetails.get(kk);
                            xmlData += "<payment_details>"
                                    + "<Receipt_id><![CDATA[" + currentDetails.getReceiptId() + "]]></Receipt_id>"
                                    + "<Invoice_id><![CDATA[" + currentDetails.getInvoiceId() + "]]></Invoice_id>"
                                    + "<Recid><![CDATA[" + currentDetails.getRecId() + "]]></Recid>"
                                    + "<Amount><![CDATA[" + currentDetails.getAmount() + "]]></Amount>"
                                    + "<Discount><![CDATA[" + currentDetails.getDiscount() + "]]></Discount>" +
                                    "</payment_details>";
                        }
                        xmlData += "</paymentdata>";
                    }
                    xmlData += "</payment>";
                }
                else if (currentLocation.getTransId().startsWith("N"))
                {
                    if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("NO")) {
                        xmlData += "<order>";
                        xmlData += location;
                        ArrayList<OrderHeader> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrdrHeadr(currentLocation.getTransId());
                        for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++)
                        {
                            String hintRemarks = dataHelperObj.getUnuploadedHintRemarks(currentLocation.getTransId());
                            OrderHeader currentHeader = unUploadedOrdrHeadr.get(jj);
                            xmlData += "<orderdata>";
                            xmlData += "<order_header>"
                                    + "<order_no><![CDATA[" + currentHeader.getOrderNo() + "]]></order_no>"
                                    + "<customer_name><![CDATA[" + "" + "]]></customer_name>"
                                    + "<Phone_no><![CDATA[" + "" + "]]></Phone_no>"
                                    + "<address><![CDATA[" + "" + "]]></address>"
                                    + "<pin_code><![CDATA[" + "" + "]]></pin_code>"
                                    + "<area><![CDATA[" + "" + "]]></area>"
                                    + "<area_name><![CDATA[" + "" + "]]></area_name>"
                                    + "<TD><![CDATA[" + currentHeader.getTrdDiscnt() + "]]></TD>"
                                    + "<sale_type><![CDATA[" + currentHeader.getSalesType() + "]]></sale_type>"
                                    + "<order_value><![CDATA[" + currentHeader.getOrder_value() + "]]></order_value>"
                                    + "<d_instruction><![CDATA[" + currentHeader.getInstruction() + "]]></d_instruction>"
                                    + "<tag_distributor_code><![CDATA[" + currentHeader.getTag_distributor_code() + "]]></tag_distributor_code>"
                                    + "<cust_type><![CDATA[" + "" + "]]></cust_type>"
                                    + "<customer_flag><![CDATA[" + "" + "]]></customer_flag>"
                                    + "<transaction_type><![CDATA[" + currentHeader.getTransaction_type() + "]]></transaction_type>"
                                    + "<vat><![CDATA[" + currentHeader.getVAT() + "]]></vat>"
                                    + "<grn_no><![CDATA[" + currentHeader.getGrnNo() + "]]></grn_no>"
                                    + "<vertical_value><![CDATA[" + currentHeader.getVerticalValue() + "]]></vertical_value>"
                                    + "<DESTINATION_CODE><![CDATA[" + currentHeader.getDestinationCode() + "]]></DESTINATION_CODE>"
                                    + "<ORDER_TYPE><![CDATA[" + currentHeader.getOrderType() + "]]></ORDER_TYPE>"
                                    + "<FREIGHT_COMPONENT><![CDATA[" + currentHeader.getFreightComponent() + "]]></FREIGHT_COMPONENT>"
                                    + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                    + "<GST_type><![CDATA[" + currentHeader.getGstType() + "]]></GST_type>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                    + "<base_latt><![CDATA[" + "" + "]]></base_latt>"
                                    + "<base_longi><![CDATA[" + "" + "]]></base_longi>"
                                    + "<image><![CDATA[" + "" + "]]></image>"
                                    + "<freight_component_value><![CDATA[" + currentHeader.getFreight_component_value() + "]]></freight_component_value>"
//                                    + "<PRICE_VALIDATION_TYPE><![CDATA[" + currentHeader.getPriceValidationType() + "]]></PRICE_VALIDATION_TYPE>"
                                    + "</order_header>";
                            xmlData += "</orderdata>";
                        }
                        xmlData += "</order>";
                    }
                    else if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("NC"))
                    {
                        xmlData += "<payment>";
                        xmlData += location;
                        ArrayList<PaymentHeader> unUploadedPaymentHeader = dataHelperObj.getUnuploadedPaymentHeadr(currentLocation.getTransId());
                        for (int jj = 0; jj < unUploadedPaymentHeader.size(); jj++) {
                            PaymentHeader currentHeader = unUploadedPaymentHeader.get(jj);
                            xmlData += "<paymentdata>";
                            xmlData += "<payment_header>"
                                    + "<receipt_id><![CDATA[" + currentHeader.getReceiptId() + "]]></receipt_id>"
                                    + "<amount><![CDATA[" + currentHeader.getAmount() + "]]></amount>"
                                    + "<cash_cheque><![CDATA[]]></cash_cheque>"
                                    + "<cheque_no><![CDATA[" + currentHeader.getChequeNo() + "]]></cheque_no>"
                                    + "<date><![CDATA[" + currentHeader.getDate() + "]]></date>"
                                    + "<bank><![CDATA[" + currentHeader.getBank() + "]]></bank>"
                                    + "<sale_type><![CDATA[" + currentHeader.getSaleType() + "]]></sale_type>"
                                    + "<p_remark><![CDATA[" + currentHeader.getInstruction() + "]]></p_remark>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>" +
                                    "</payment_header>";
                            xmlData += "</paymentdata>";
                        }
                        xmlData += "</payment>";
                    }
                }
                else if (currentLocation.getTransId().startsWith("A"))
                {
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
        } else {
            String xmlData = "";
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
                if (currentLocation.getTransId().startsWith("O")) {
                    xmlData += "<order>";
                    xmlData += location;
                    ArrayList<OrderHeader> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrdrHeadr(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                        String hintRemarks = dataHelperObj.getUnuploadedHintRemarks(currentLocation.getTransId());
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
                                + "<vertical_value><![CDATA[" + currentHeader.getVerticalValue() + "]]></vertical_value>"
                                + "<DESTINATION_CODE><![CDATA[" + currentHeader.getDestinationCode() + "]]></DESTINATION_CODE>"
                                + "<ORDER_TYPE><![CDATA[" + currentHeader.getOrderType() + "]]></ORDER_TYPE>"
                                + "<FREIGHT_COMPONENT><![CDATA[" + currentHeader.getFreightComponent() + "]]></FREIGHT_COMPONENT>"
                                + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                + "<GST_type><![CDATA[" + currentHeader.getGstType() + "]]></GST_type>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                + "<base_latt><![CDATA[" + currentHeader.getcustomerLat() + "]]></base_latt>"
                                + "<base_longi><![CDATA[" + currentHeader.getcustomerLong() + "]]></base_longi>"
                                + "<image><![CDATA[" + currentHeader.getcustomerImage() + "]]></image>"
                                + "<Freight_component_value><![CDATA[" + currentHeader.getFreight_component_value() + "]]></Freight_component_value>"
//                                + "<PRICE_VALIDATION_TYPE><![CDATA[" + currentHeader.getPriceValidationType() + "]]></PRICE_VALIDATION_TYPE>"
                                + "</order_header>";
                        ArrayList<OrderDetails> unUploadedOrdrDetails = dataHelperObj.getUnuploadedOrdrDetails(currentHeader.getOrderNo(), currentHeader.getTransaction_type());
                        for (int kk = 0; kk < unUploadedOrdrDetails.size(); kk++) {
                            OrderDetails currentDetails = unUploadedOrdrDetails.get(kk);
                            String mrp = currentDetails.getMrpCode();
                            String mrpCode = "";
                            if (mrp != null && mrp.contains("-")) {
                                String[] dataArray = mrp.split("-");
                                mrpCode = dataArray[0];
                            }

                            xmlData += "<order_details>"
                                    + "<Order_no><![CDATA[" + currentDetails.getOrderNo() + "]]></Order_no>"
                                    + "<Sku_code><![CDATA[" + currentDetails.getSkuCode() + "]]></Sku_code>"
                                    + "<qty><![CDATA[" + currentDetails.getQty() + "]]></qty>"
                                    + "<TD><![CDATA[" + currentDetails.getTD() + "]]></TD>"
                                    + "<PREMIUM><![CDATA[" + currentDetails.getPremium() + "]]></PREMIUM>"
                                    + "<sale_rate><![CDATA[" + currentDetails.getSaleRate() + "]]></sale_rate>"
                                    + "<VAT><![CDATA[" + currentDetails.getVAT() + "]]></VAT>"
                                    + "<amount><![CDATA[" + currentDetails.getAmount() + "]]></amount>"
                                    + "<UOM><![CDATA[" + currentDetails.getUom() + "]]></UOM>"
                                    + "<mrp_code><![CDATA[" + mrpCode + "]]></mrp_code>"
                                    + "<scheme_type><![CDATA[" + currentDetails.getSchemeType() + "]]></scheme_type>"
                                    +
                                    "</order_details>";
                        }
                        ArrayList<SchemeSummary> unUploadedSchemeSummary = dataHelperObj.getUnuploadedSchemeDetails(currentHeader.getOrderNo());
                        for (int kk = 0; kk < unUploadedSchemeSummary.size(); kk++) {
                            SchemeSummary currentDetails = unUploadedSchemeSummary.get(kk);

                            xmlData += "<scheme_summary>"
                                    + "<Order_no><![CDATA[" + currentDetails.getOrderNo() + "]]></Order_no>"
                                    + "<scheme_id><![CDATA[" + currentDetails.getschemeId() + "]]></scheme_id>"
                                    + "<scheme_prod_code><![CDATA[" + currentDetails.getschemeProdCode() + "]]></scheme_prod_code>"
                                    + "<freebies_prod_code><![CDATA[" + currentDetails.getfreeBiesProdCode() + "]]></freebies_prod_code>"
                                    + "<freebies_prod_desc><![CDATA[" + currentDetails.getfreeBiesProdDesc() + "]]></freebies_prod_desc>"
                                    + "<freebies_qty><![CDATA[" + currentDetails.getfreeBiesQty() + "]]></freebies_qty>"
                                    + "<freebies_val_percent><![CDATA[" + currentDetails.getfreeBiesValPercentage() + "]]></freebies_val_percent>"
                                    + "<freebies_val_amount><![CDATA[" + currentDetails.getfreeBiesValAmount() + "]]></freebies_val_amount>"
                                    +
                                    "</scheme_summary>";
                        }
                        xmlData += "</orderdata>";
                    }
                    xmlData += "</order>";
                } else if ((currentLocation.getTransId().startsWith("P")) && !(currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("PA"))) {
                    xmlData += "<payment>";
                    xmlData += location;
                    ArrayList<PaymentHeader> unUploadedPaymentHeader = dataHelperObj.getUnuploadedPaymentHeadr(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedPaymentHeader.size(); jj++) {
                        PaymentHeader currentHeader = unUploadedPaymentHeader.get(jj);
                        String payMode = "";
                        switch (currentHeader.getCashCheque()) {
                            case 0:
                                payMode = "CASH";
                                break;
                            case 1:
                                payMode = "CHEQUE";
                                break;
                            case 2:
                                payMode = "NEFT";
                                break;
                            case 3:
                                payMode = "RTGS";
                                break;
                            case 4:
                                payMode = "IMPS";
                                break;
                        }
                        xmlData += "<paymentdata>";
                        xmlData += "<payment_header>"
                                + "<receipt_id><![CDATA[" + currentHeader.getReceiptId() + "]]></receipt_id>"
                                + "<amount><![CDATA[" + currentHeader.getAmount() + "]]></amount>"
                                + "<cash_cheque><![CDATA[" + payMode + "]]></cash_cheque>"
                                + "<cheque_no><![CDATA[" + currentHeader.getChequeNo() + "]]></cheque_no>"
                                + "<date><![CDATA[" + currentHeader.getDate() + "]]></date>"
                                + "<bank><![CDATA[" + currentHeader.getBank() + "]]></bank>"
                                + "<sale_type><![CDATA[" + currentHeader.getSaleType() + "]]></sale_type>"
                                + "<p_remark><![CDATA[" + currentHeader.getInstruction() + "]]></p_remark>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>" +
                                "</payment_header>";
                        ArrayList<PaymentDetails> unUploadedPaymentDetails = dataHelperObj.getUnuploadedPaymentDetails(currentHeader.getReceiptId());
                        for (int kk = 0; kk < unUploadedPaymentDetails.size(); kk++) {
                            PaymentDetails currentDetails = unUploadedPaymentDetails.get(kk);
                            xmlData += "<payment_details>"
                                    + "<Receipt_id><![CDATA[" + currentDetails.getReceiptId() + "]]></Receipt_id>"
                                    + "<Invoice_id><![CDATA[" + currentDetails.getInvoiceId() + "]]></Invoice_id>"
                                    + "<Recid><![CDATA[" + currentDetails.getRecId() + "]]></Recid>"
                                    + "<Amount><![CDATA[" + currentDetails.getAmount() + "]]></Amount>"
                                    + "<Discount><![CDATA[" + currentDetails.getDiscount() + "]]></Discount>" +
                                    "</payment_details>";
                        }
                        xmlData += "</paymentdata>";
                    }
                    xmlData += "</payment>";
                } else if (currentLocation.getTransId().startsWith("N")) {
                    if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("NO")) {
                        xmlData += "<order>";
                        xmlData += location;
                        ArrayList<OrderHeader> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrdrHeadr(currentLocation.getTransId());
                        for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                            OrderHeader currentHeader = unUploadedOrdrHeadr.get(jj);
                            xmlData += "<orderdata>";
                            xmlData += "<order_header>"
                                    + "<order_no><![CDATA[" + currentHeader.getOrderNo() + "]]></order_no>"
                                    + "<d_instruction><![CDATA[" + currentHeader.getInstruction() + "]]></d_instruction>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>" +
                                    "</order_header>";
                            xmlData += "</orderdata>";
                        }
                        xmlData += "</order>";
                    } else if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("NC")) {
                        xmlData += "<payment>";
                        xmlData += location;
                        ArrayList<PaymentHeader> unUploadedPaymentHeader = dataHelperObj.getUnuploadedPaymentHeadr(currentLocation.getTransId());
                        for (int jj = 0; jj < unUploadedPaymentHeader.size(); jj++) {
                            PaymentHeader currentHeader = unUploadedPaymentHeader.get(jj);
                            xmlData += "<paymentdata>";
                            xmlData += "<payment_header>"
                                    + "<receipt_id><![CDATA[" + currentHeader.getReceiptId() + "]]></receipt_id>"
                                    + "<amount><![CDATA[" + currentHeader.getAmount() + "]]></amount>"
                                    + "<cash_cheque><![CDATA[]]></cash_cheque>"
                                    + "<cheque_no><![CDATA[" + currentHeader.getChequeNo() + "]]></cheque_no>"
                                    + "<date><![CDATA[" + currentHeader.getDate() + "]]></date>"
                                    + "<bank><![CDATA[" + currentHeader.getBank() + "]]></bank>"
                                    + "<sale_type><![CDATA[" + currentHeader.getSaleType() + "]]></sale_type>"
                                    + "<p_remark><![CDATA[" + currentHeader.getInstruction() + "]]></p_remark>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>" +
                                    "</payment_header>";
                            xmlData += "</paymentdata>";
                        }
                        xmlData += "</payment>";
                    }
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

    public static String prepareXMLDataForInvoiceInformation(Context mContext) {
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        boolean shouldCheckFlag = true;
        ArrayList<InvoiceInformation> InvoiceInformationList = dataHelperObj.getInvoiceList(shouldCheckFlag);
        int sizeOfUnUploadedInvoiceInfo = InvoiceInformationList.size();
        if (sizeOfUnUploadedInvoiceInfo > 0) {
            StringBuilder sb = new StringBuilder();
            sb.append("<?xml version='1.0' encoding='UTF-8'?><root><invoice_information>");
            for (int i = 0; i < sizeOfUnUploadedInvoiceInfo; i++) {
                sb.append("<invoice_details>");
                String invoiceNumber = InvoiceInformationList.get(i).getInvoiceNo();
                String invoiceDate = InvoiceInformationList.get(i).getInvoiceDate();
                String order_no = InvoiceInformationList.get(i).getOrderNo();
                String customer_code = InvoiceInformationList.get(i).getCustomerCode();
                String chronological_no = InvoiceInformationList.get(i).getChronologicalNumber();
                String freight_charge = InvoiceInformationList.get(i).getFreightCharge();
                if (freight_charge == null) {
                    freight_charge = "";
                }
                sb.append("<invoice_no><![CDATA[" + invoiceNumber + "]]></invoice_no>");
                sb.append("<invoice_date><![CDATA[" + invoiceDate + "]]></invoice_date>");
                sb.append("<order_no><![CDATA[" + order_no + "]]></order_no>");
                sb.append("<customer_code><![CDATA[" + customer_code + "]]></customer_code>");
                sb.append("<chronological_no><![CDATA[" + chronological_no + "]]></chronological_no>");
                sb.append("<freight_charge><![CDATA[" + freight_charge + "]]></freight_charge>");
                sb.append("</invoice_details>");
            }
            sb.append("</invoice_information></root>");
            return String.valueOf(sb);
        } else {
            return null;
        }

    }

    public static String prepareXMLDataYellowCard(Context mContext) {
        Boolean isUnsyncedDataPresent = false;
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransaction.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("Y")) {
                    isUnsyncedDataPresent = true;
                    xmlData += "<yellowcard_info>";
                    xmlData += location;
                    ArrayList<YellowCard> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedYellowCard(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {

                        YellowCard currentHeader = unUploadedOrdrHeadr.get(jj);
                        xmlData += "<yellowcard_details>"
                                + "<yellowcard_no><![CDATA[" + currentHeader.getyellow_card_no() + "]]></yellowcard_no>"
                                + "<customer_code><![CDATA[" + currentHeader.getcustomer_code() + "]]></customer_code>"
                                + "<challan_no><![CDATA[" + currentHeader.getchallan_no() + "]]></challan_no>"
                                + "<challan_date><![CDATA[" + currentHeader.getchallan_date() + "]]></challan_date>"
                                + "<qty><![CDATA[" + currentHeader.getqty() + "]]></qty>"
                                + "<qty_UOM><![CDATA[" + currentHeader.getqty_UOM() + "]]></qty_UOM>" +
                                "</yellowcard_details>";
                    }
                    xmlData += "</yellowcard_info>";
                } else {
//                    dataHelperObj.closeDatabase();
//					return "No data present";
                }
            }
            xmlData += "</root>";
        }

        if (!isUnsyncedDataPresent) {
            xmlData = "No data present";
        }
        dataHelperObj.closeDatabase();
        return xmlData;
    }

    public static String prepareXMLDataJointWorkObservation(Context mContext) {
        Boolean isUnsyncedDataPresent = false;
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransaction.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("JW"))
                {
                    isUnsyncedDataPresent = true;
                    xmlData += "<JOINT_WORK>";
                    xmlData += location;
                    ArrayList<JointWorkObservation> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedJointWork(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {

                        JointWorkObservation currentHeader = unUploadedOrdrHeadr.get(jj);
                        xmlData += "<JOINT_WORK_DATA>"
                                + "<JOINT_WORK_ID><![CDATA[" + currentHeader.getjoint_work_observation() + "]]></JOINT_WORK_ID>"
                                + "<customer_code><![CDATA[" + currentHeader.getcustomer_code() + "]]></customer_code>"
                                + "<ROUTE_CODE><![CDATA[" + currentHeader.getroute_code() + "]]></ROUTE_CODE>"
                                + "<EMP_CODE><![CDATA[" + currentHeader.getemp_code() + "]]></EMP_CODE>"
                                + "<OBSERVATION_ON_CUSTOMER><![CDATA[" + currentHeader.getobservation_on_customer() + "]]></OBSERVATION_ON_CUSTOMER>"
                                + "<OBSERVATION_ON_EMP><![CDATA[" + currentHeader.getobservation_on_employee() + "]]></OBSERVATION_ON_EMP>" +
                                "</JOINT_WORK_DATA>";
                    }
                    xmlData += "</JOINT_WORK>";
                } else {
//                    dataHelperObj.closeDatabase();
//					return "No data present";
                }
            }
            xmlData += "</root>";
        }

        if (!isUnsyncedDataPresent) {
            xmlData = "No data present";
        }
        dataHelperObj.closeDatabase();
        return xmlData;
    }

    public static String prepareXMLDataOrderApproval(Context mContext) {
        Boolean isUnsyncedDataPresent = false;
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransaction.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("TA"))
                {
                    isUnsyncedDataPresent = true;
                    xmlData += "<ORDER_APPROVAL>";
                    xmlData += location;
                    ArrayList<commonDatabaseHelper> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedOrderApproval(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++)
                    {
                        commonDatabaseHelper currentHeader = unUploadedOrdrHeadr.get(jj);
                        xmlData += "<ORDER_APPROVAL_DETAILS>"
                                + "<approval_id><![CDATA[" + currentHeader.getItem0() + "]]></approval_id>"
                                + "<customer_code><![CDATA[" + currentHeader.getItem1() + "]]></customer_code>"
                                + "<changed_sub_dealer_code><![CDATA[" + currentHeader.getItem2() + "]]></changed_sub_dealer_code>"
                                + "<APPORDERNO><![CDATA[" + currentHeader.getItem3() + "]]></APPORDERNO>"
                                + "<changed_dns_prod_code><![CDATA[" + currentHeader.getItem4() + "]]></changed_dns_prod_code>"
                                + "<QTY_CHANGED><![CDATA[" + currentHeader.getItem5() + "]]></QTY_CHANGED>"
                                + "<changed_dns_destination_code><![CDATA[" + currentHeader.getItem6() + "]]></changed_dns_destination_code>"
                                + "<changed_dump_code><![CDATA[" + currentHeader.getItem7() + "]]></changed_dump_code>"
                                + "<plant_name><![CDATA[" + currentHeader.getItem8() + "]]></plant_name>"
                                + "<approval_status><![CDATA[" + currentHeader.getItem9() + "]]></approval_status>"
                                + "<approval_done_by><![CDATA[" + currentHeader.getItem10() + "]]></approval_done_by>"
                                + "<remarks><![CDATA[" + currentHeader.getItem11() + "]]></remarks>"
                                + "<changed_order_for><![CDATA[" + currentHeader.getItem12() + "]]></changed_order_for>"
                                + "<changed_consignee_address><![CDATA[" + currentHeader.getItem13() + "]]></changed_consignee_address>"
                                +"</ORDER_APPROVAL_DETAILS>";
                    }
                    xmlData += "</ORDER_APPROVAL>";
                } else {
//                    dataHelperObj.closeDatabase();
//					return "No data present";
                }
            }
            xmlData += "</root>";
        }

        if (!isUnsyncedDataPresent) {
            xmlData = "No data present";
        }
        dataHelperObj.closeDatabase();
        return xmlData;
    }

    public static String PrepareXMLForNewCustomer(Context mContext) {
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetUnuploadedLocationofNewCustomer();
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
                    "<TA_DA_MODE><![CDATA[" + tada+ "]]></TA_DA_MODE>" +
                    "</location>";

            if (currentLocation.getTransId().startsWith("N")) {
                ArrayList<CustomerDetails> unUploadedCust = dataHelperObj.getUnuploadedCustomerList(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedCust.size(); jj++) {
                    CustomerDetails detailsObj = unUploadedCust.get(jj);
                    xmlData += "<new_customer>";
                    xmlData += location;
                    String customerImage = detailsObj.getcustomerImage();
                    String customerCode = detailsObj.getCustomerCode();
                    xmlData += "<new_customer_details>"
                            + "<customer_code><![CDATA[" + customerCode + "]]></customer_code>"
                            + "<customer_name><![CDATA[" + detailsObj.getCustomerName() + "]]></customer_name>"
                            + "<Phone_no><![CDATA[" + detailsObj.getNumber() + "]]></Phone_no>"
                            + "<pin_code><![CDATA[" + detailsObj.getPin() + "]]></pin_code>"
                            + "<area><![CDATA[" + detailsObj.getNewRouteCode() + "]]></area>"
                            + "<area_name><![CDATA[" + detailsObj.getNewRouteName() + "]]></area_name>"
                            + "<rds_tag><![CDATA[" + detailsObj.getRdsTag() + "]]></rds_tag>"
                            + "<address><![CDATA[" + detailsObj.getAddress() + "]]></address>"
                            + "<landline_no><![CDATA[" + detailsObj.getLandlineNo() + "]]></landline_no>"
                            + "<owner_name><![CDATA[" + detailsObj.getOwnerName() + "]]></owner_name>"
                            + "<owner_phone><![CDATA[" + detailsObj.getOwnerPhone() + "]]></owner_phone>"
                            + "<cust_class><![CDATA[" + detailsObj.getCustClass() + "]]></cust_class>"
                            + "<weekly_closing_day><![CDATA[" + detailsObj.getWeeklyClosingDay() + "]]></weekly_closing_day>"
                            + "<coverage_type><![CDATA[" + detailsObj.getCoverageType() + "]]></coverage_type>"
                            + "<TIN><![CDATA[" + detailsObj.getTIN() + "]]></TIN>"
                            + "<PAN><![CDATA[" + detailsObj.getPAN() + "]]></PAN>"
//                            + "<image><![CDATA[" + detailsObj.getImage() + "]]></image>"
                            + "<email><![CDATA[" + detailsObj.getEmail() + "]]></email>"
                            + "<acedns><![CDATA[" + detailsObj.getIsACEDNS() + "]]></acedns>"
                            + "<branch_code><![CDATA[" + detailsObj.getBranchCode() + "]]></branch_code>"
                            + "<base_latt><![CDATA[" + detailsObj.getbase_latt() + "]]></base_latt>"
                            + "<base_longi><![CDATA[" + detailsObj.getbase_longi() + "]]></base_longi>"
                            + "<image><![CDATA[" + customerImage + "]]></image>"
                            + "<category_of_store><![CDATA[" + detailsObj.getcategoryOfStore() + "]]></category_of_store>"
                            + "<instore_activity><![CDATA[" + detailsObj.getinStoreActivityPossible() + "]]></instore_activity>"
                            + "</new_customer_details>";
                    xmlData += "</new_customer>";
                    if(customerImage.contains("jpeg")) {
                        ArrayList<String> mFileNamesList=new ArrayList<>();
                        mFileNamesList.add(customerImage);

                       // new TRANS_NewCustomerImageTask(mContext, mFileNamesList, customerCode, false).execute();
                    }

                }
            }
        }

        xmlData += "</root>";
        return xmlData;
    }
    public static String PrepareXMLForNewCustomerCustomize(Context mContext) {
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetUnuploadedLocationofNewCustomer();
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
                    "<TA_DA_MODE><![CDATA[" + tada+ "]]></TA_DA_MODE>" +
                    "</location>";

            if (currentLocation.getTransId().startsWith("N")) {
                ArrayList<CustomerDetails> unUploadedCust = dataHelperObj.getUnuploadedCustomerList(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedCust.size(); jj++) {
                    CustomerDetails detailsObj = unUploadedCust.get(jj);
                    xmlData += "<new_customer>";
                    xmlData += location;
                    String customerImage = detailsObj.getcustomerImage();
                    String customerCode = detailsObj.getCustomerCode();
                    xmlData += "<new_customer_details>"
                            + "<customer_code><![CDATA[" + customerCode + "]]></customer_code>"
                            + "<customer_name><![CDATA[" + detailsObj.getCustomerName() + "]]></customer_name>"
                            + "<Phone_no><![CDATA[" + detailsObj.getNumber() + "]]></Phone_no>"
                            + "<pin_code><![CDATA[" + detailsObj.getPin() + "]]></pin_code>"
                            + "<area><![CDATA[" + detailsObj.getNewRouteCode() + "]]></area>"
                            + "<area_name><![CDATA[" + detailsObj.getNewRouteName() + "]]></area_name>"
                            + "<rds_tag><![CDATA[" + detailsObj.getRdsTag() + "]]></rds_tag>"
                            + "<address><![CDATA[" + detailsObj.getAddress() + "]]></address>"
                            + "<landline_no><![CDATA[" + detailsObj.getLandlineNo() + "]]></landline_no>"
                            + "<owner_name><![CDATA[" + detailsObj.getOwnerName() + "]]></owner_name>"
                            + "<owner_image><![CDATA[" + detailsObj.getownerImage() + "]]></owner_image>"
                            + "<firm_name><![CDATA[" + detailsObj.getfirmName() + "]]></firm_name>"
                            + "<firm_image><![CDATA[" + detailsObj.getoutletImage() + "]]></firm_image>"
                            + "<GST><![CDATA[" + detailsObj.getTIN() + "]]></GST>"
                            + "<GST_image><![CDATA[" + detailsObj.getgstImage() + "]]></GST_image>"
                            + "<aadhar><![CDATA[" + detailsObj.getadharNo() + "]]></aadhar>"
                            + "<aadhar_image><![CDATA[" + detailsObj.getadharImage() + "]]></aadhar_image>"
                            + "<aadhar_image><![CDATA[" + detailsObj.getadharImage() + "]]></aadhar_image>"
                            + "<cust_type><![CDATA[" + detailsObj.getCustomerType() + "]]></cust_type>"
                            + "<weekly_closing_day><![CDATA[" + detailsObj.getWeeklyClosingDay() + "]]></weekly_closing_day>"
                            + "<email><![CDATA[" + detailsObj.getEmail() + "]]></email>"
                            + "<acedns><![CDATA[" + detailsObj.getIsACEDNS() + "]]></acedns>"
                            + "<branch_code><![CDATA[" + detailsObj.getBranchCode() + "]]></branch_code>"
                            + "<base_latt><![CDATA[" + detailsObj.getbase_latt() + "]]></base_latt>"
                            + "<base_longi><![CDATA[" + detailsObj.getbase_longi() + "]]></base_longi>"
                            + "</new_customer_details>";
                    xmlData += "</new_customer>";
                    if(customerImage.contains("jpeg")) {
                        ArrayList<String> mFileNamesList=new ArrayList<>();
                        mFileNamesList.add(customerImage);

                        new TRANS_NewCustomerImageTask(mContext, mFileNamesList, customerCode, false).execute();
                    }

                }
            }
        }

        xmlData += "</root>";
        return xmlData;
    }
}
