package com.forcepower.acedns.database;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.activity.OrderApprovalActivity;
import com.forcepower.acedns.activity.OrderEditActivity;
import com.forcepower.acedns.activity.StockAuditEditActivity;
import com.forcepower.acedns.api.clients.LocalStorage;
import com.forcepower.acedns.bean.AlocatedSauda;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;
import com.forcepower.acedns.bean.CallDurationDetails;
import com.forcepower.acedns.bean.CardTransactionDetails;
import com.forcepower.acedns.bean.CashDeposit;
import com.forcepower.acedns.bean.CashTransferReceive;
import com.forcepower.acedns.bean.CheckInOut;
import com.forcepower.acedns.bean.CommonModel;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.DemoForm;
import com.forcepower.acedns.bean.DoctorVisit;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.GroupLeaderFormList;
import com.forcepower.acedns.bean.InvoiceInformation;
import com.forcepower.acedns.bean.JointWorkObservation;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.KnokingFormDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.LoyaltyCustomerDetails;
import com.forcepower.acedns.bean.LoyaltyPurchaseDetails;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.bean.MenuOutstandingParent;
import com.forcepower.acedns.bean.MerchandisingDetails;
import com.forcepower.acedns.bean.NoOrderDetails;
import com.forcepower.acedns.bean.NotificationDetails;
import com.forcepower.acedns.bean.OrderDetails;
import com.forcepower.acedns.bean.OrderHeader;
import com.forcepower.acedns.bean.OrderStatus;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.PaymentDetails;
import com.forcepower.acedns.bean.PaymentHeader;
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductPromotionDetails;
import com.forcepower.acedns.bean.PropAccessibility;
import com.forcepower.acedns.bean.ProspectCustDetails;
import com.forcepower.acedns.bean.ProspectCustHeader;
import com.forcepower.acedns.bean.QuotationDetails;
import com.forcepower.acedns.bean.ReportData;
import com.forcepower.acedns.bean.ReportDetails;
import com.forcepower.acedns.bean.ReportSummery;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanCustomer;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SaudaAllocation;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.bean.SchemeSummary;
import com.forcepower.acedns.bean.StockAuditDetails;
import com.forcepower.acedns.bean.StokistDetails;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.bean.SurveyPublish;
import com.forcepower.acedns.bean.SurveyReportSumary;
import com.forcepower.acedns.bean.TDAllocation;
import com.forcepower.acedns.bean.TelecallerList;
import com.forcepower.acedns.bean.TentCust;
import com.forcepower.acedns.bean.TentFormDetails;
import com.forcepower.acedns.bean.TentProduct;
import com.forcepower.acedns.bean.TentProductList;
import com.forcepower.acedns.bean.TourSwapDetails;
import com.forcepower.acedns.bean.TransDeleteDetails;
import com.forcepower.acedns.bean.WholeSaleInfo;
import com.forcepower.acedns.bean.YellowCard;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.attendanceFilterString;
import static com.forcepower.acedns.constants.Constants.cashTransferIdForReceive;
import static com.forcepower.acedns.constants.Constants.cashTransferOrReceive;
import static com.forcepower.acedns.constants.Constants.currentCustomerCode;
import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;
import static com.forcepower.acedns.constants.Constants.currentRecordedFileName;
import static com.forcepower.acedns.constants.Constants.currentSiteValueForEdit;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.dayOfWeekForCustomer;
import static com.forcepower.acedns.constants.Constants.doBargainNo;
import static com.forcepower.acedns.constants.Constants.isOrederToNewCustomer;
import static com.forcepower.acedns.constants.Constants.locationAccuracy;
import static com.forcepower.acedns.constants.Constants.mFreightComponentAmountFor;
import static com.forcepower.acedns.constants.Constants.mOrderPriceValidationType;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.marginPoNo;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.recordedCallDuration;
import static com.forcepower.acedns.constants.Constants.retailercareOrderOrAudit;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterListFreebies;
import static com.forcepower.acedns.constants.Constants.surveyTableIdColumnName;
import static com.forcepower.acedns.constants.Constants.surveyTableIdValue;
import static com.forcepower.acedns.constants.Constants.totalAmountCollection;
import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;
import static com.forcepower.acedns.util.Utils.setCheckInOutLatLongAccuracyToLocationLatLong;

import com.forcepower.acedns.activity.GrnActivity;
import com.forcepower.acedns.activity.RetailerStockOutActivitySpecial;

public class AceDnsTransactionDatabase extends SQLiteOpenHelper {
    private static final String DATABASE_NAME = "AceDns.db";
    private static final int DATABASE_VERSION = 1;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String Lock = "dbLock";
    SQLiteDatabase database;
    Context mContext;

    public AceDnsTransactionDatabase(Context context) {
        super(context, Utils.getAppStoragePath(context) + DATABASE_NAME, null, DATABASE_VERSION);
        database = SQLiteDatabase.openDatabase(Utils.getAppStoragePath(context) + DATABASE_NAME, null, SQLiteDatabase.NO_LOCALIZED_COLLATORS);
        mContext = context;
    }

    @Override
    public void onCreate(SQLiteDatabase db) {

    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {

    }

    public int deleteTransaction(ArrayList<TransDeleteDetails> transList, boolean isMIS) {
        int status = 1;
        for (int ii = 0; ii < transList.size(); ii++) {
            TransDeleteDetails detailsObj = transList.get(ii);
            String transID = "";
            if (detailsObj.getMODE().equalsIgnoreCase("datewise")) {
                String query = "SELECT trans_id FROM location WHERE SUBSTR(trans_id,-14,8) BETWEEN '"
                        + detailsObj.getStartDate().replace("-", "")
                        + "' AND '"
                        + detailsObj.getEndDate().replace("-", "")
                        + "';";
                Cursor cursor = database.rawQuery(query, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        alterClosingStock(cursor.getString(0));
                        transID = transID + "'" + cursor.getString(0) + "',";
                        cursor.moveToNext();
                    }
                    transID = transID.substring(0, transID.length() - 1);
                }
            } else {
                transID = "'" + detailsObj.getTransID() + "'";
                alterClosingStock(detailsObj.getTransID());
            }

            if (!isMIS) {
                String locationDeleteQuery = "DELETE FROM location WHERE trans_id IN("
                        + transID + ");";
                String orderHeaderDeleteQuery = "DELETE FROM order_header WHERE order_no IN("
                        + transID + ");";
                String orderDetailsDeleteQuery = "DELETE FROM order_details WHERE order_no IN("
                        + transID + ");";
                String transactionLogDeleteQuery = "DELETE FROM transaction_log WHERE trans_id IN("
                        + transID + ");";
                String paymentHeaderDeleteQuery = "DELETE FROM payment_header WHERE receipt_id IN("
                        + transID + ");";
                String paymentDetailsDeleteQuery = "DELETE FROM payment_details WHERE receipt_id IN("
                        + transID + ");";
                String gitDeleteQuery = "DELETE FROM goods_in_transit WHERE grn_no IN("
                        + transID + ") AND trans_type = 'ST';";
                String gitSTUpdateQuery = "UPDATE goods_in_transit SET status = '0' WHERE grn_no IN(SELECT grn_no FROM goods_in_transit WHERE order_no IN("
                        + transID + "));";
                String gitBTDeleteQuery = "DELETE FROM goods_in_transit WHERE order_no IN("
                        + transID + ");";

                database.execSQL(locationDeleteQuery);
                database.execSQL(orderHeaderDeleteQuery);
                database.execSQL(orderDetailsDeleteQuery);
                database.execSQL(transactionLogDeleteQuery);
                database.execSQL(paymentHeaderDeleteQuery);
                database.execSQL(paymentDetailsDeleteQuery);
                database.execSQL(gitDeleteQuery);
                database.execSQL(gitSTUpdateQuery);
                database.execSQL(gitBTDeleteQuery);
            } else {
                String misDeleteQuery = "DELETE FROM mis_transaction_log WHERE trans_id IN("
                        + transID + ");";
                database.execSQL(misDeleteQuery);
            }
        }
        return status;
    }

    public void alterClosingStock(String transID) {
        String query = "SELECT OD.sku_code,OD.qty,CS.cl_stk,OH.transaction_type FROM order_header OH,order_details OD,closing_stock CS WHERE OH.order_no = OD.order_no AND OD.sku_code = CS.prod_code AND OH.order_no = '"
                + transID + "';";
        String transType = "";
        Cursor cursor = database.rawQuery(query, null);
        if (cursor.getCount() > 0) {
            cursor.moveToFirst();
            Constants.selectedProductMasterList = new ArrayList<ProductMasterDetails>();
            for (int i = 0; i < cursor.getCount(); i++) {
                ProductMasterDetails masterDetails = new ProductMasterDetails();
                masterDetails.setProdCode(cursor.getString(0));
                masterDetails.setQty(cursor.getString(1));
                masterDetails.setClosingStk(cursor.getString(2));
                transType = cursor.getString(3);
                Constants.selectedProductMasterList.add(masterDetails);
                cursor.moveToNext();
            }
            if (transType.equalsIgnoreCase("PB")
                    || transType.equalsIgnoreCase("BT")) {
                reduceClStk();
            } else {
                increaseClStk();
            }

        }
    }

    public Boolean insertToHintRemarksDetailsTable1(String alphabt, String timeStamp, String hintRemarks) {
        Boolean isInsertionDone = true;
        try {
            String trans_id = "";
            int flag = 0;
            trans_id = alphabt + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
//		database.beginTransaction();

            ContentValues cv = new ContentValues();
            cv.put("trans_id", trans_id);
            cv.put("hint_remarks", hintRemarks);
            cv.put("flag", flag);

            synchronized (Lock) {
                database.insertWithOnConflict("hint_remarks_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }


    public Boolean insertToHintRemarksDetailsILS(String alphabt, String timeStamp, String hintRemarks,String hintRemarksOther,String custCode,String et_metwith) {
        Boolean isInsertionDone = true;
        try {
            String trans_id = "";
            int flag = 0;
            trans_id = alphabt + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
//		database.beginTransaction();

            ContentValues cv = new ContentValues();
            cv.put("trans_id", trans_id);
            cv.put("customer_code",custCode);
            cv.put("remarks", hintRemarks);
            cv.put("other_remarks", hintRemarksOther);
            cv.put("met_with", et_metwith);

            synchronized (Lock) {
                database.insertWithOnConflict("doctor_visit_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }



    public Boolean insertToDeviceInfoTable(String emp_code, String device_id, String manufacturer, String model, String os_version, String app_installation_time) {
        Boolean isInsertionDone = true;
        try {
            database.beginTransaction();

            ContentValues cv = new ContentValues();
            cv.put("emp_code", emp_code);
            cv.put("device_id", device_id);
            cv.put("manufacturer", manufacturer);
            cv.put("model", model);
            cv.put("os_version", os_version);
            cv.put("app_installation_time", app_installation_time);

            synchronized (Lock) {
                database.insertWithOnConflict("device_information", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
            database.endTransaction();
        }
        return isInsertionDone;
    }

    //	yellow card trans
    public Boolean insertToYellowCardTable(String alphabt, String timeStamp, String customer_code, String date, String challan_no, String qty, String qty_UOM) {
        String trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int flag = 0;

        try {
            ContentValues cv = new ContentValues();
            cv.put("yellow_card_no", trans_id);
            cv.put("customer_code", customer_code);
            cv.put("challan_no", challan_no);
            cv.put("challan_date", date);
            cv.put("qty", qty);
            cv.put("qty_UOM", qty_UOM);
            cv.put("flag", flag);

            synchronized (Lock) {
                database.insertWithOnConflict("yellow_card_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
        } catch (Exception e) {
            return false;
        } finally {
        }
        return true;
    }

    public Boolean insertToJointWorkObservationTable(String alphabt, String timeStamp, String customer_code, String empCode, String routeCode, String observationOnCustomer, String observationOnEmployee) {
        String trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int flag = 0;

        Cursor cursor = null;
        try {
            String selectQuery = "SELECT joint_work_id from joint_work_observation where joint_work_id='"+trans_id+"' COLLATE NOCASE;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                return false;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }

        try {
            ContentValues cv = new ContentValues();
            cv.put("joint_work_id", trans_id);
            cv.put("customer_code", customer_code);
            cv.put("route_code", routeCode);
            cv.put("emp_code", empCode);
            cv.put("observation_on_customer", observationOnCustomer);
            cv.put("observation_on_emp", observationOnEmployee);

            synchronized (Lock) {
                database.insertWithOnConflict("joint_work_observation", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
        } catch (Exception e) {
            return false;
        } finally {
        }
        return true;
    }

    /*
     * LOCATION TABLE TRANSACTION
     */
    LocalStorage localStorage;
    public Boolean insertToLocationTable(String alphabt, String timeStamp)
    {
        String trans_id = "";
        int flag = 0;
        String date = Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp);
        trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        database.beginTransaction();
        try {

            String tada = "";
            localStorage = new LocalStorage(mContext);
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")) {
                if (localStorage.getTADAPubPri().matches("public")) {
                    tada = "PUBLIC";
                } else {
                    tada = "OWN";
                }
            }

            ContentValues cv = new ContentValues();
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("trans_id", trans_id);
            cv.put("date", date);
            cv.put("latt", currentLat);
            cv.put("longi", currentLong);
            cv.put("flag", flag);
            cv.put("network_response", locationAccuracy);
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")) {
                cv.put("TA_DA_mode", tada);
            }else{
                cv.put("TA_DA_mode", tada);
            }

            if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes"))
            {
                if (alphabt.equalsIgnoreCase("O") || alphabt.equalsIgnoreCase("MF")|| alphabt.equalsIgnoreCase("MS")|| alphabt.equalsIgnoreCase("P") || alphabt.equalsIgnoreCase("NS") || alphabt.equalsIgnoreCase("NC") || alphabt.equalsIgnoreCase("CC")) {
                    cv.put("purpose_of_visit", localStorage.getPurpose_of_visit());
                }
            }else {
                cv.put("purpose_of_visit", "");
            }

            synchronized (Lock) {
                database.insertWithOnConflict("location", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);

            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            return false;
        } finally {
            database.endTransaction();
        }
        /*if(Constants.isDeveloperOn){
            return false;
        }*/
        return true;
    }

    public Boolean insertToLocationTable1(String alphabt, String timeStamp) {
        Boolean isInsertionDone = true;
        try {
            String trans_id = "";
            int flag = 0;
            String currentDate = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8) + " ";
            String time = new SimpleDateFormat("HH:mm:ss").format(Calendar
                    .getInstance().getTime());
            String date = currentDate + time;

            trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
            String tada = "";
            localStorage = new LocalStorage(mContext);
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")) {
                if (localStorage.getTADAPubPri().matches("public")) {
                    tada = "PUBLIC";
                } else {
                    tada = "OWN";
                }
            }

            ContentValues cv = new ContentValues();
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("trans_id", trans_id);
            cv.put("date", date);
            cv.put("latt", currentLat);
            cv.put("longi", currentLong);
            cv.put("flag", flag);
            cv.put("network_response", locationAccuracy);
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")) {
                cv.put("TA_DA_mode", tada);
            }

            if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes"))
            {
                if (alphabt.equalsIgnoreCase("O") || alphabt.equalsIgnoreCase("MF")|| alphabt.equalsIgnoreCase("MS")|| alphabt.equalsIgnoreCase("P") || alphabt.equalsIgnoreCase("NS") || alphabt.equalsIgnoreCase("NC")) {
                    cv.put("purpose_of_visit", localStorage.getPurpose_of_visit());
                }
            }else {
                cv.put("purpose_of_visit", "");
            }

            synchronized (Lock) {
                database.insertWithOnConflict("location", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        if(Constants.isDeveloperOn && !BuildConfig.DEBUG){
            return false;
        }
        return isInsertionDone;
    }

    public Boolean inserttoCallDurationTable(String alphabt, String timeStamp) {
        Boolean isInsertionDone = true;
        try {
            String trans_id = "";
            int flag = 0;

            trans_id = alphabt + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

            ContentValues cv = new ContentValues();
            cv.put("transaction_id", trans_id);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("call_duration", Utils.getDifferenceBetweenTwoDateTime(Constants.transactionStartTime, Constants.transactionEndTime));
            cv.put("flag", flag);


            synchronized (Lock) {
                database.insertWithOnConflict("call_duration", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }


    public void UpdateOrderStatus(ArrayList<OrderStatus> orderStatusList, String orderno) {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < orderStatusList.size(); ii++) {
                OrderStatus obj = orderStatusList.get(ii);
                String alreadyDeliveredQuantity = obj.getAlreadyDeliveredQuantity();
                String currentDeliveredQuantity = obj.getCurrentDeliveredQuantity();
                double alreadyDeliveredQuantityInDouble = 0.0;
                double currentDeliveredQuantityInDouble = 0.0;
                if (Utils.isNumeric(alreadyDeliveredQuantity)) {
                    alreadyDeliveredQuantityInDouble = Double.parseDouble(alreadyDeliveredQuantity);
                }
                if (Utils.isNumeric(currentDeliveredQuantity)) {
                    currentDeliveredQuantityInDouble = Double.parseDouble(currentDeliveredQuantity);
                }
                Double totalDeliveredQuantity = alreadyDeliveredQuantityInDouble + currentDeliveredQuantityInDouble;
                database.execSQL("UPDATE order_status SET delivery_qty ='"
                        + Constants.defaultFormat.format(totalDeliveredQuantity) + "',status='" + obj.getStatus() + "', remarks='" + obj.getRemarks() + "' WHERE order_no = '"
                        + obj.getOrderNo() + "' AND product_code='" + obj.getProductCode() + "' AND flag='0'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public void UpdateOrderStatusItem(OrderStatus obj) {
        database.beginTransaction();
        try {
            String alreadyDeliveredQuantity = obj.getAlreadyDeliveredQuantity();
            String currentDeliveredQuantity = obj.getCurrentDeliveredQuantity();
            double alreadyDeliveredQuantityInDouble = 0.0;
            double currentDeliveredQuantityInDouble = 0.0;
            if (Utils.isNumeric(alreadyDeliveredQuantity)) {
                alreadyDeliveredQuantityInDouble = Double.parseDouble(alreadyDeliveredQuantity);
            }
            if (Utils.isNumeric(currentDeliveredQuantity)) {
                currentDeliveredQuantityInDouble = Double.parseDouble(currentDeliveredQuantity);
            }
            Double totalDeliveredQuantity = alreadyDeliveredQuantityInDouble + currentDeliveredQuantityInDouble;
            database.execSQL("UPDATE order_status SET delivery_qty ='"
                    + Constants.defaultFormat.format(totalDeliveredQuantity) + "',status='" + obj.getStatus() + "', remarks='" + obj.getRemarks() + "', flag='0' WHERE order_no = '"
                    + obj.getOrderNo() + "' AND product_code='" + obj.getProductCode() + "'");

            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<Location> GetSurveyPublishLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 and (trans_id LIKE 'SUA%' OR trans_id LIKE 'A%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetSurveyOfferLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 and trans_id LIKE 'OFR%'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetSurveyHeaderLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 AND (trans_id LIKE 'NSU%' OR trans_id LIKE 'A%')  ";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }


    public ArrayList<Location> GetSurveyLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 AND (trans_id LIKE 'SU%' OR trans_id LIKE 'A%') AND SUBSTR(trans_id,1,3)<> 'SUA' ";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetWholeSaleLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 AND (trans_id LIKE 'W%' OR trans_id LIKE 'A%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetSamplingLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' and (trans_id LIKE 'PP%' OR trans_id LIKE 'NPP%' OR trans_id LIKE 'A%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }


    public ArrayList<Location> GetLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' and (trans_id LIKE 'FT%' OR trans_id LIKE 'NFT%' OR trans_id LIKE 'A%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetUnSyncedNewBidLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' and (trans_id LIKE 'RB%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetUnSyncedCounterBidLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' and (trans_id LIKE 'CB%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> GetStockAuditLocation() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' and (trans_id LIKE 'MS%' OR trans_id LIKE 'NMS%' OR trans_id LIKE 'A%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    locationObj.setPurpose_of_visit(cursor.getString(8));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<OrderStatus> GetUnuploadedOrderStatus() {
        ArrayList<OrderStatus> orderStatusList = new ArrayList<OrderStatus>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT order_no,product_code,delivery_qty,status,remarks FROM order_status where flag ='0' ";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    OrderStatus obj = new OrderStatus();
                    obj.setOrderNo(cursor.getString(0));
                    obj.setProductCode(cursor.getString(1));
                    obj.setAlreadyDeliveredQuantity(cursor.getString(2));
                    obj.setStatus(cursor.getString(3));
                    obj.setRemarks(cursor.getString(4));
                    orderStatusList.add(obj);
                    obj = null;
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return orderStatusList;
    }

    public ArrayList<Location> GetUnuploadedLocationofNewCustomer() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,1)='N'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    locationObj.setTA_DA_mode(cursor.getString(7));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> getUnuploadedMerchandisingTransaction() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='MC' ";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    locationObj.setTA_DA_mode(cursor.getString(7));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }


    public ArrayList<Location> getUnuploadedTransaction(String type, String notype) {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "";
            if (type.equalsIgnoreCase("STOCK"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND (substr(trans_id,1,1)='S' OR substr(trans_id,1,2)='NS')  AND substr(trans_id,1,2)<>'SU' AND substr(trans_id,1,3)<>'NSU'";
            }
            else if (type.equalsIgnoreCase("TOUR SWAP"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='TS' ";
            }
            else if (type.equalsIgnoreCase("ATTENDANCE"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND (substr(trans_id,1,1)='A' OR  substr(trans_id,1,2)='WO' OR  substr(trans_id,1,2)='LR')";
            }
            else if (type.equalsIgnoreCase("CHECKOUT"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='CH'";
            }
            else if (type.equalsIgnoreCase("STOCK_RETURN"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='SR'";
            }
            else if (type.equalsIgnoreCase("DO_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='DO'";
            }
            else if (type.equalsIgnoreCase("gift_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='GD'";
            }
            else if (type.equalsIgnoreCase("grn_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,3)='GRN'";
            }
            else if (type.equalsIgnoreCase("business_prospect_customize"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,1)='B'";
            }
            else if (type.equalsIgnoreCase("doctor_visit"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='DR'";
            }
            else if (type.equalsIgnoreCase("stockist_visit"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='SV'";
            }
            else if (type.equalsIgnoreCase("tent_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='TF'";
            }
            else if (type.equalsIgnoreCase("demo_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='DF'";
            }
            else if (type.equalsIgnoreCase("knocking_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='KF'";
            }
            else
            {
                selectQuery = "SELECT * FROM location where flag = '0' ";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    locationObj.setTA_DA_mode(cursor.getString(7));
                    locationObj.setPurpose_of_visit(cursor.getString(8));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> getUnuploadedTransactionBackUp(String startDate, String endDate) {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "";

            selectQuery = "SELECT * FROM location where SUBSTR(trans_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));
                    locationObj.setTA_DA_mode(cursor.getString(7));
                    locationObj.setPurpose_of_visit(cursor.getString(8));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<commonDatabaseHelper> getUnuploadedCustomerClassUpdateList()
    {
        ArrayList<commonDatabaseHelper> unUploadedTransList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "";

                selectQuery = "SELECT customer_code,cust_class FROM customer_master where check_flag = '0' ";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper obj = new commonDatabaseHelper();
                    obj.setItem0(cursor.getString(0));
                    obj.setItem1(cursor.getString(1));
                    unUploadedTransList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> getUnPublishNOTIFICATION() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location WHERE SUBSTR(trans_id,1,2) = 'PA' AND flag = '0'";
//            String selectQuery = "SELECT * FROM location WHERE flag = '0' AND trans_id IN (select ack_id from notification_details where flag ='1')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> getUnuploadedPushAcknowledgement() {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location WHERE SUBSTR(trans_id,1,2) = 'PA' AND flag = '0'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<Location> getUnuploadedTransaction(String transID) {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where trans_id = '"
                    + transID + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public void UPDATESurveyOutPUT() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", "1");
            database.update("survey_output", cv, "flag=?", new String[]{"0"});
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Survey Output flag", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpdateWholeSalerDetails() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", "1");
            database.update("wholesaler_details", cv, "flag=?", new String[]{"0"});
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("WholeSalerDetailsflag", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UPDATEDCATransaction() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", "1");
            database.update("DCA_transaction", cv, "flag=?", new String[]{"0"});
            Log.d("dca_transaction:", "DCA Transaction flag Updated");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("DCA Transaction flag", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UPDATESaudaDetails() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", "1");
            database.update("sauda_details", cv, "flag=?",
                    new String[]{"0"});
            Log.d("sauda_details:", "Sauda Details flagUpdated");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Sauda Details flag", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    @SuppressLint("LongLogTag")
    public void UPDATESaudaAllocationDB(ArrayList<AlocatedSauda> AlocatedSaudaList) {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < AlocatedSaudaList.size(); ii++) {
                AlocatedSauda masterObj = AlocatedSaudaList.get(ii);
                String currentStock = masterObj.getBalance();
                database.execSQL("UPDATE sauda_allocation SET BAL = '"
                        + currentStock + "' WHERE product_filter_code = '"
                        + masterObj.getProductFilterCode() + "'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Error: Updated for sauda_allocation", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UPDATESaudaHeader() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", "1");
            database.update("sauda_header", cv, "flag=?",
                    new String[]{"0"});
            Log.d("sauda_header:", "Sauda flag Updated");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Sauda Header flag", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void updateUnuploadedLocation(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("location", cv, "trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void updateUnuploadedTourExp(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("tour_expenses", cv,
                        "tour_exp_trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Tour_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Tour_expenses Update status ::::::::::::"
                + updateResult);
    }

    public void updateUnuploadedLocation(String transId, String columnName, String tableName) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update(tableName, cv, columnName + "=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateCustomerLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,1)='N'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Location Update status ::::::::::::" + updateResult);
    }


    public void UpdateReplacementLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='RP'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateSaudaAllocationLocationDataForFirstLoginOfDay() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND substr(trans_id,1,2)='FA'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }


    public void UpdateSaudaAllocationLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='FA'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateTDAllocationLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND substr(trans_id,1,3)='TDA'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateFeedbackLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='MF'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }


    public void UpdateLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1) IN('O','A','P') OR substr(trans_id,1,2) IN('NO','NC') ";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }
    public void UpdateLocationDataByTransId(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  trans_id='"+transId+"'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }
    public void UpdateLocationDataForYellowCard() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE substr(trans_id,1,1)='Y' AND flag='0'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateLocationDataForOrderApproval() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE substr(trans_id,1,2)='TA' AND flag='0'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateLocationDataForBusinessProspect(String prefix) {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE substr(trans_id,1,2)='" + prefix + "' AND flag='0'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateLocationDataForCheckOut() {
        database.beginTransaction();
        String sql = "UPDATE  location SET flag='1' WHERE substr(trans_id,1,2)='CH' AND flag='0'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }

    }

    public void UpdateInVoiceInformationFlagTo1() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  invoice_information SET flag='1' WHERE  flag='0' ";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }

    }

    public void UpdateStatusFlagForCashTransfer() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  cash_transaction_details SET status='1' WHERE  cash_transaction_id='" + cashTransferIdForReceive + "' ";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }

    }

    public void UpdateTotalReceivedAmountForCashTransfer(String totalReceivedAmount) {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  cash_transaction_details SET rec_value='" + totalReceivedAmount + "' WHERE  cash_transaction_id='" + cashTransferIdForReceive + "' ";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }

    }

    public int GetUnuploadedCustomerCount() {
        int recordcount = 0;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM customer_master WHERE flag=0";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                recordcount = cursor.getCount();
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return recordcount;
    }


    public int GetEditedCustomerCount() {
        int recordcount = 0;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM customer_master WHERE check_flag=0";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                recordcount = cursor.getCount();
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return recordcount;
    }

    public void UpadateSurveyLocation() {
        database.beginTransaction();
        String sql = "UPDATE location SET flag='1' WHERE  flag='0' AND  substr(trans_id,1,3)<>'SUA' AND ( substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='SU')";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateWholeSaleLocation() {
        database.beginTransaction();
        String sql = "UPDATE location SET flag='1' WHERE  flag='0' AND ( substr(trans_id,1,1)='A' OR substr(trans_id,1,1)='W')";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateSurveyHeaderLocation() {
        database.beginTransaction();
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND ( substr(trans_id,1,1)='A' OR substr(trans_id,1,3)='NSU')";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateSurveyPublishLocation() {
        database.beginTransaction();
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,3)='SUA'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateLocationTableForOffers() {
        database.beginTransaction();
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND substr(trans_id,1,3)='OFR' ";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateSaudaLocation() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='FT' OR substr(trans_id,1,3)='NFT'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpadateNewBidLocation() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND substr(trans_id,1,2)='RB'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpadateCounterBidLocation() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND substr(trans_id,1,2)='CB'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpadateStockAuditLocation() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='MS' OR substr(trans_id,1,3)='NMS'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateSamplingLocation() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE  flag='0' AND trans_id NOT LIKE 'PA%' AND substr(trans_id,1,1)='A' OR substr(trans_id,1,2)='PP' OR substr(trans_id,1,3)='NPP'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Location Update status ::::::::::::" + updateResult);
    }
    /*
     * Location Table Transactin Ends
     */

    public boolean isCheckedOutToday() {
        Cursor cursor = null;
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);
            String selectQuery = "SELECT * FROM attendence where substr(trans_id,1,2)='CH' AND date = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                return true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return false;
    }

    /*
     * ATTENDANCE TABLE TRANSACTION STARTS
     */
    public boolean getAttendanceForToday() {
        Cursor cursor = null;
        Boolean isAttendanceGiven=false;
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);
            String selectQuery = "SELECT * FROM attendence where date = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0)
            {
                cursor.moveToFirst();
                String attendanceId=cursor.getString(1);
                if(attendanceId.startsWith("A"))
                {
                    isAttendanceGiven=true;
                }
                else if(attendanceId.startsWith("LR"))
                {
                    if(Constants.userDetailsObj.getmenu_access_attendance().contains("LR"))
                    {
                        isAttendanceGiven=true;
                    }
                    else
                    {
                        attendanceFilterString="You are on leave today";
                        isAttendanceGiven=false;
                    }
                }
                else if(attendanceId.startsWith("WO"))
                {
                    if(Constants.userDetailsObj.getmenu_access_attendance().contains("WO"))
                    {
                        isAttendanceGiven=true;
                    }
                    else
                    {
                        attendanceFilterString="Today is weekly off";
                        isAttendanceGiven=false;
                    }
                }

            }
            else
            {
                attendanceFilterString="Please give Attendance first";
                isAttendanceGiven=false;
            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isAttendanceGiven;
    }
    public boolean checkAttendanceForToday() {
        Cursor cursor = null;
        Boolean isAttendanceGiven=false;
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);
            String selectQuery = "SELECT * FROM attendence where date = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0)
            {
                isAttendanceGiven=true;
            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isAttendanceGiven;
    }

    public boolean checkOutJourneyForToday() {
        Cursor cursor = null;
        Boolean isAttendanceGiven=false;
        try {
            String date =""+getAttendanceIdToday();
            String selectQuery = "SELECT * FROM att_checkout_journey_info where attendance_id = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0)
            {
                isAttendanceGiven=true;
            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isAttendanceGiven;
    }

    public String getAttendanceIdToday() {
        Cursor cursor = null;
        String attendanceType="A";
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);
            String selectQuery = "SELECT * FROM attendence where date = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0)
            {
                cursor.moveToFirst();
                attendanceType=cursor.getString(1);

            }
            else
            {

            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return attendanceType;
    }

    public String getAttendanceTypeToday() {
        Cursor cursor = null;
        String attendanceType="A";
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);
            String selectQuery = "SELECT * FROM attendence where date = '"
                    + date + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0)
            {
                cursor.moveToFirst();
                attendanceType=cursor.getString(1);

            }
            else
            {

            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return attendanceType;
    }

    public boolean getLeaveRequestAddedInRoutePlanToday() {
        Cursor cursor = null;
        try {
            String date = Constants.dateString.substring(0, 4) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(6, 8);//yyyy-MM-dd
            String visitDate = Utils.changeDateFormat("yyyy-MM-dd", "dd-MM-yyyy", date);

            String selectQuery = "SELECT * FROM route_plan_transaction where visit_date = '"
                    + visitDate + "' AND route_name LIKE '%leave request%'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                return true;
            }
            cursor.close();
        } catch (Exception e) {
            ;
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return false;
    }

    public Boolean insertToAttendanceTable(String alphabt, String timeStamp) {
        String trans_id = "";
        int flag = 0;
        String date = Constants.dateString.substring(0, 4) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(6, 8);

        trans_id = alphabt + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("trans_id", trans_id);
            cv.put("date", date);
            cv.put("flag", flag);

            synchronized (Lock) {
                database.insertWithOnConflict("attendence", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Attendence:", "Data Inserted");
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
            return false;
        } finally {
            database.endTransaction();
        }
        return true;
    }
    public Boolean insertToJourneyInfoTableForAccitity(String alphabt, String timeStamp,String chosenData) {
        String trans_id = "";
        String date = Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp);

        trans_id = alphabt + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("attendance_id ", trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("create_date ", date);
            cv.put("att_activities", chosenData);

            synchronized (Lock) {
                database.insertWithOnConflict("att_checkout_journey_info", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Attendence:", "Data Inserted");
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
            return false;
        } finally {
            database.endTransaction();
        }
        return true;
    }
    public void UpdateCheckoutJourneyInfo(String alphabt, String timeStamp,String chosenData,String startingKm,String endingKm,String oddometerAtt,String oddometercheckOut) {
        database.beginTransaction();
        int updateResult = -1;
        String trans_id = "";

        trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        String sql = "UPDATE  att_checkout_journey_info SET checkout_id='"+trans_id+"', checkout_ending_km='"+endingKm+"', checkout_odometer='"+oddometercheckOut+"'   WHERE  attendance_id='"+ PreferenceData.getJourneyInfoAttendanceTransactionID(mContext)+"'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
        System.out.println("Location Update status ::::::::::::" + updateResult);
    }
    public Boolean insertToJourneyInfoTableForAccitityEnlarged(String alphabt, String timeStamp,String chosenData,String startingKm,String endingKm,String oddometerAtt,String oddometercheckOut) {
        String trans_id = "";
        String date = Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp);

        trans_id = alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        PreferenceData.setJourneyInfoAttendanceTransactionID(mContext,  trans_id);
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("attendance_id ", trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("create_date ", date);
            cv.put("att_starting_km", startingKm);
            cv.put("att_odometer", oddometerAtt);
            cv.put("att_necessary_items", chosenData);
            cv.put("att_activities", "");
            cv.put("checkout_ending_km", endingKm);
            cv.put("checkout_odometer", oddometercheckOut);
            cv.put("att_vehicle_type", PreferenceData.getjourneyInfoVehicleWheelType(mContext));


            synchronized (Lock) {
                database.insertWithOnConflict("att_checkout_journey_info", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Attendence:", "Data Inserted");
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
            return false;
        } finally {
            database.endTransaction();
        }
        return true;
    }

    public Boolean insertToJourneyInfoTableForExtra(String alphabt, String timeStamp,String chosenData,String startingKm,String endingKm,String oddometerAtt,String oddometercheckOut) {
        String trans_id = "";
        String date = Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp);

        trans_id = getAttendanceIdToday();//alphabt + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        PreferenceData.setJourneyInfoAttendanceTransactionID(mContext,  trans_id);
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("attendance_id ", "");
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("create_date ", date);
            cv.put("att_starting_km", startingKm);
            cv.put("att_odometer", oddometerAtt);
            cv.put("att_necessary_items", chosenData);
            cv.put("att_activities", "");
            cv.put("checkout_ending_km", endingKm);
            cv.put("checkout_odometer", oddometercheckOut);
            //cv.put("att_vehicle_type", PreferenceData.getjourneyInfoVehicleWheelType(mContext));


            synchronized (Lock) {
                database.insertWithOnConflict("att_checkout_journey_info", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Attendence:", "Data Inserted");
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
            return false;
        } finally {
            database.endTransaction();
        }
        return true;
    }

    public ArrayList<DoctorVisit> getUnuploadedDoctorVisit(String transId) {
        ArrayList<DoctorVisit> unUploadedList = new ArrayList<DoctorVisit>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM doctor_visit_details where trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    DoctorVisit detailObj = new DoctorVisit();
                    detailObj.setTransId(cursor.getString(0));
                    detailObj.setCustomer_code(cursor.getString(1));
                    detailObj.setRemarks(cursor.getString(2));
                    detailObj.setOther_remarks(cursor.getString(3));
                    detailObj.setMet_with(cursor.getString(4));

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<StokistDetails> getUnuploadedStokistVisit(String transId) {
        ArrayList<StokistDetails> unUploadedList = new ArrayList<StokistDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM stockist_visit where visit_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    StokistDetails detailObj = new StokistDetails();
                    detailObj.setTransactionId(cursor.getString(0));
                    detailObj.setStokistCode(cursor.getString(1));
                    detailObj.setCustomerCode(cursor.getString(2));
                    detailObj.setSale(cursor.getString(3));
                    detailObj.setFolder(cursor.getString(4));
                    detailObj.setProd_code(cursor.getString(5));

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }



    public ArrayList<Attendance> getUnuploadedAttendance(String transId) {
        ArrayList<Attendance> unUploadedAttendanceList = new ArrayList<Attendance>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM attendence where trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Attendance detailObj = new Attendance();
                    detailObj.setEmpCode(cursor.getString(0));
                    detailObj.setDate(cursor.getString(2));

                    unUploadedAttendanceList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedAttendanceList;
    }
    public ArrayList<commonDatabaseHelper> getUnuploadedAttendanceJourney(String transId,String columnName) {
        ArrayList<commonDatabaseHelper> unUploadedAttendanceList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM att_checkout_journey_info where "+columnName+" = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper detailObj = new commonDatabaseHelper();
                    detailObj.setItem0(cursor.getString(0));
                    detailObj.setItem1(cursor.getString(1));
                    detailObj.setItem2(cursor.getString(2));
                    detailObj.setItem3(cursor.getString(3));
                    detailObj.setItem4(cursor.getString(4));
                    detailObj.setItem5(cursor.getString(5));
                    detailObj.setItem6(cursor.getString(6));
                    detailObj.setItem7(cursor.getString(7));
                    detailObj.setItem8(cursor.getString(8));
                    detailObj.setItem9(cursor.getString(9));
                    detailObj.setItem10(cursor.getString(10));

                    unUploadedAttendanceList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedAttendanceList;
    }

    public ArrayList<commonDatabaseHelper> getOdometerJourney() {
        ArrayList<commonDatabaseHelper> unUploadedAttendanceList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM att_checkout_journey_info";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToLast();
                for (int i = cursor.getCount(); i <= cursor.getCount(); i++) {
                    commonDatabaseHelper detailObj = new commonDatabaseHelper();
                    detailObj.setItem0(cursor.getString(0));
                    detailObj.setItem1(cursor.getString(1));
                    detailObj.setItem2(cursor.getString(2));
                    detailObj.setItem3(cursor.getString(3));
                    detailObj.setItem4(cursor.getString(4));
                    detailObj.setItem5(cursor.getString(5));
                    detailObj.setItem6(cursor.getString(6));
                    detailObj.setItem7(cursor.getString(7));
                    detailObj.setItem8(cursor.getString(8));
                    detailObj.setItem9(cursor.getString(9));

                    unUploadedAttendanceList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedAttendanceList;
    }

    public ArrayList<commonDatabaseHelper> getUnuploadedBusinessProspectCustomize(String transId) {
        ArrayList<commonDatabaseHelper> unUploadedAttendanceList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM business_prospect_details where prospect_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper detailObj = new commonDatabaseHelper();
                    detailObj.setItem0(cursor.getString(0));
                    detailObj.setItem1(cursor.getString(1));
                    detailObj.setItem2(cursor.getString(2));
                    detailObj.setItem3(cursor.getString(3));
                    detailObj.setItem4(cursor.getString(4));
                    detailObj.setItem5(cursor.getString(5));
                    detailObj.setItem6(cursor.getString(6));
                    detailObj.setItem7(cursor.getString(7));
                    detailObj.setItem8(cursor.getString(8));
                    detailObj.setItem9(cursor.getString(9));
                    detailObj.setItem10(cursor.getString(10));
                    detailObj.setItem11(cursor.getString(11));
                    detailObj.setItem12(cursor.getString(12));
                    detailObj.setItem13(cursor.getString(13));
                    detailObj.setItem14(cursor.getString(14));

                    unUploadedAttendanceList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedAttendanceList;
    }
    public void updateUnuploadedAttendance() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("attendence", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Tour_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Tour_expenses Update status ::::::::::::"
                + updateResult);
    }

    public void updateUnuploadedAttendance(String transID) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("attendence", cv, "trans_id=?",
                        new String[]{transID});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Tour_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Tour_expenses Update status ::::::::::::"
                + updateResult);
    }

    /*
     * ATTENDANCE TABLE TRANSACTION ENDS
     */

    /*
     * TOUR EXPENSE TABLE TRANSACTION STARTS
     */
    public void insertToTourExpense(String transId, String date,
                                    String strtDest, String endDest, String fare, String support,
                                    String distance, String selectedCatId, String selectedSubCatId,
                                    String attachment_id) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("tour_exp_trans_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("start_destination", strtDest);
            cv.put("end_destination", endDest);
            cv.put("fare", fare);
            cv.put("tour_date", date);
            cv.put("transport_mode_sub_cat_id", selectedSubCatId);
            cv.put("transport_mode_cat_id", selectedCatId);
            cv.put("supporting_attached", support);
            cv.put("distance", distance);
            cv.put("flag", 0);
            cv.put("attachment_id", attachment_id);
            synchronized (Lock) {
                database.insertWithOnConflict("tour_expenses", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Tour_expenses:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Tour_expenses", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    /*
     * TOUR TRAVEL EXPENSE TABLE TRANSACTION STARTS
     */
    public void insertToTourTravelExpense(String tour_food_lodge_trans_id, String tour_date, String dep_time, String arr_time, String particulars, String local_conveyance,
                                          String travel_mode, String transport_fair, String fooding_allowance, String hotel_charge, String other_expenses, String remarks, String attachment_id) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("tour_food_lodge_trans_id", tour_food_lodge_trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("tour_date", tour_date);
            cv.put("dep_time", dep_time);
            cv.put("arr_time", arr_time);
            cv.put("particulars", particulars);
            cv.put("local_conveyance", local_conveyance);
            cv.put("travel_mode", travel_mode);
            cv.put("transport_fair", transport_fair);
            cv.put("fooding_allowance", fooding_allowance);
            cv.put("hotel_charge", hotel_charge);
            cv.put("other_expenses", other_expenses);
            cv.put("remarks", remarks);
            cv.put("flag", 0);
            cv.put("attachment_id", attachment_id);
            synchronized (Lock) {
                database.insertWithOnConflict("tour_fooding_lodging_expenses", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public void insertToTourExpenseDetails(String tour_food_lodge_trans_id,String toutType, String tour_date,String tour_date_to, String dep_time, String arr_time,String tour_place_from,String tour_place_to, String particulars, String local_conveyance,
                                          String travel_mode, String transport_fair, String fooding_allowance, String hotel_charge, String other_expenses, String remarks,
                                           String transport_attachment, String fooding_attachment, String lodging_attachment, String other_attachment,String fuel_bill_attachment,String misc, String daily_assistance,String km_traveled) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("tour_exp_trans_id", tour_food_lodge_trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("tour_type", toutType);
            cv.put("tour_date_from", tour_date);
            cv.put("dep_time", dep_time);
            cv.put("tour_date_to", tour_date_to);
            cv.put("arr_time", arr_time);
            cv.put("tour_place_from", tour_place_from );
            cv.put("tour_place_to", tour_place_to );
            cv.put("travel_mode", travel_mode);
            cv.put("local_conveyance", local_conveyance);
            cv.put("transport_fair", transport_fair);
            cv.put("fooding_allowance", fooding_allowance);
            cv.put("hotel_charge", hotel_charge);
            cv.put("other_expenses", other_expenses);
            cv.put("remarks", remarks);
            cv.put("flag", 0);
            cv.put("transport_attachment", transport_attachment);
            cv.put("fooding_attachment", fooding_attachment);
            cv.put("lodging_attachment", lodging_attachment);
            cv.put("attachment_id", other_attachment);
            cv.put("fuel_bill_attachment", fuel_bill_attachment);
            cv.put("misc", misc);
            cv.put("daily_assistance", daily_assistance);
            cv.put("km_traveled", km_traveled);
            synchronized (Lock) {
                database.insertWithOnConflict("tour_expenses_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e)
        {
            Log.d("tour_expenses_details",e.toString());
            e.printStackTrace();

        } finally {
            database.endTransaction();
        }
    }

    public void insertToTourExpenseDetails1(String tour_food_lodge_trans_id,String toutType, String tour_date,String tour_date_to, String dep_time, String arr_time,String tour_place_from,String tour_place_to, String particulars, String local_conveyance,
                                           String travel_mode, String transport_fair, String fooding_allowance, String hotel_charge, String other_expenses, String remarks,
                                           String transport_attachment, String fooding_attachment, String lodging_attachment, String other_attachment,String fuel_bill_attachment,String misc, String daily_assistance,String km_traveled,String travel_mode2,String travel_mode3,String travel_fare2,String travel_fare3) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("tour_exp_trans_id", tour_food_lodge_trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("tour_type", toutType);
            cv.put("tour_date_from", tour_date);
            cv.put("dep_time", dep_time);
            cv.put("tour_date_to", tour_date_to);
            cv.put("arr_time", arr_time);
            cv.put("tour_place_from", tour_place_from );
            cv.put("tour_place_to", tour_place_to );
            cv.put("travel_mode", travel_mode);
            cv.put("local_conveyance", local_conveyance);
            cv.put("transport_fair", transport_fair);
            cv.put("fooding_allowance", fooding_allowance);
            cv.put("hotel_charge", hotel_charge);
            cv.put("other_expenses", other_expenses);
            cv.put("remarks", remarks);
            cv.put("flag", 0);
            cv.put("transport_attachment", transport_attachment);
            cv.put("fooding_attachment", fooding_attachment);
            cv.put("lodging_attachment", lodging_attachment);
            cv.put("attachment_id", other_attachment);
            cv.put("fuel_bill_attachment", fuel_bill_attachment);
            cv.put("misc", misc);
            cv.put("daily_assistance", daily_assistance);
            cv.put("km_traveled", km_traveled);
            cv.put("travel_mode2", travel_mode2);
            cv.put("transport_fair2", travel_fare2);
            cv.put("travel_mode3", travel_mode3);
            cv.put("transport_fair3", travel_fare3);
            synchronized (Lock) {
                database.insertWithOnConflict("tour_expenses_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e)
        {
            Log.d("tour_expenses_details",e.toString());
            e.printStackTrace();

        } finally {
            database.endTransaction();
        }
    }

    public void insertToCrmTransaction(String trans_id) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("call_id", trans_id);
            cv.put("customer_code", currentCustomerCode);
            cv.put("call_duration", recordedCallDuration);
            cv.put("recorded_file", currentRecordedFileName);

            synchronized (Lock) {
                database.insertWithOnConflict("CRM_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public String[] getUnuploadedTourExp(String transId) {
        String[] resultArray = new String[14];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tour_fooding_lodging_expenses where tour_food_lodge_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
                resultArray[6] = (cursor.getString(6));
                resultArray[7] = (cursor.getString(7));
                resultArray[8] = (cursor.getString(8));
                resultArray[9] = (cursor.getString(9));
                resultArray[10] = (cursor.getString(10));
                resultArray[11] = (cursor.getString(11));
                resultArray[12] = (cursor.getString(13));
                resultArray[13] = (cursor.getString(14));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }
    public String[] getUnuploadedTourExp_(String transId)
    {
        String[] resultArray = new String[29];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tour_expenses_details where tour_exp_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.moveToFirst()) {
                do {

                    for(int i=0; i<29; i++)
                        resultArray[i] = (cursor.getString(i));

                } while (cursor.moveToNext());
            }
//            if (cursor.getCount() > 0)
//            {
//                for(int i=0; i<22; i++)
//                    resultArray[i] = (cursor.getString(i));
//            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public String[] getUnuploadedCrmDetail(String transId) {
        String[] resultArray = new String[14];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM CRM_transaction where call_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));

            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public void updateUnuploadedTourFoodingLodgingExp(String transId) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                database.update("tour_fooding_lodging_expenses", cv,
                        "tour_food_lodge_trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    /*
     * TOUR EXPENSE TABLE TRANSACTION ENDS
     */

    /*
     * MERCHANDISING DETAILS TABLE TRANSACTION STARTS
     */
    public void insertToMerchandisingDetails(String transId, String prod_code,
                                             String client_code, String transType, String remarks,
                                             String attachment_id, String issueStatus, String rectifyIssueId) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("merchandising_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("prod_code", prod_code);
            cv.put("client_id", client_code);
            cv.put("trans_type", transType);
            cv.put("remarks", remarks);
            cv.put("attachment_id", attachment_id);
            cv.put("issue_status", issueStatus);
            cv.put("rectifying_issue_id", rectifyIssueId);
            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("merchandising_details", null,
                        cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Merchandising_details:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Merchandising_details", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public String[] getUnuploadedMerchandisingDetails(String transId) {
        String[] resultArray = new String[9];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM merchandising_details where merchandising_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
                resultArray[6] = (cursor.getString(6));
                resultArray[7] = (cursor.getString(8));
                resultArray[8] = (cursor.getString(9));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public ArrayList<MerchandisingDetails> getUnresolvedMerchandisingList() {
        ArrayList<MerchandisingDetails> resultArray = new ArrayList<MerchandisingDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT MD.*,PM.product_group_code,PM.product_sub_group_code,PM.product_brand_code,PM.prod_desc FROM merchandising_details MD,product_master PM where MD.issue_status = 'NOT DONE' AND trans_type = 'REPORTING ISSUE' AND MD.prod_code = PM.prod_code";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    MerchandisingDetails detailsObj = new MerchandisingDetails();
                    detailsObj.setMerchandisingId(cursor.getString(0));
                    detailsObj.setEmpCode(cursor.getString(1));
                    detailsObj.setProdCode(cursor.getString(2));
                    detailsObj.setClientId(cursor.getString(3));
                    detailsObj.setTransType(cursor.getString(4));
                    detailsObj.setRemarks(cursor.getString(5));
                    detailsObj.setAttachment(cursor.getString(6));
                    detailsObj.setIssueStatus(cursor.getString(8));

                    detailsObj.setProdGroup(cursor.getString(10));
                    detailsObj.setProdSubGrp(cursor.getString(11));
                    detailsObj.setProdBrand(cursor.getString(12));
                    detailsObj.setProdDesc(cursor.getString(13));
                    resultArray.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    @SuppressLint("LongLogTag")
    public void updateUnuploadedMerchandisingDetails(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("merchandising_details", cv,
                        "merchandising_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Merchandising_details Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Merchandising_details Update status ::::::::::::"
                + updateResult);
    }

    public void updateIssueStatus(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("issue_status", "DONE");
            synchronized (Lock) {
                updateResult = database.update("merchandising_details", cv,
                        "merchandising_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Merchandising_details Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Merchandising_details Update status ::::::::::::"
                + updateResult);
    }

    /*
     * MERCHANDISING DETAILS TABLE TRANSACTION ENDS
     */

    /*
     * LODGING EXPENSE TABLE TRANSACTION STARTS
     */
    public void insertToLodgeExpense(String transId, String baseStn,
                                     String hotel, String rent, String chkinDate, String chkoutDate,
                                     String payAmt, String payMode, String attachment_id) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("lodg_exp_trans_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("base_station", baseStn);
            cv.put("hotel_name", hotel);
            cv.put("rent", rent);
            cv.put("chkin_date", chkinDate);
            cv.put("chkout_date", chkoutDate);
            cv.put("payment_amount", payAmt);
            cv.put("payment_mode", payMode);
            cv.put("flag", 0);
            cv.put("attachment_id", attachment_id);
            synchronized (Lock) {
                database.insertWithOnConflict("lodging_expenses", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Lodging_expenses:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Lodging_expenses", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public String[] getUnuploadedLodgingExp(String transId) {
        String[] resultArray = new String[10];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM lodging_expenses where lodg_exp_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
                resultArray[6] = (cursor.getString(6));
                resultArray[7] = (cursor.getString(7));
                resultArray[8] = (cursor.getString(8));
                resultArray[9] = (cursor.getString(10));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public void updateUnuploadedTourLodgingExp(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("lodging_expenses", cv,
                        "lodg_exp_trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Lodging_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Lodging_expenses Update status ::::::::::::"
                + updateResult);
    }

    /*
     * LODGING EXPENSE TABLE TRANSACTION ENDS
     */

    /*
     * FOODING EXPENSE TABLE TRANSACTION STARTS
     */
    public void insertToFoodExpense(String transId, String baseStn,
                                    String expType, String date, String accompany, String payAmt,
                                    String payMode, String attachment_id) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("food_exp_trans_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("base_station", baseStn);
            cv.put("expense_type", expType);
            cv.put("date", date);
            cv.put("accompany", accompany);
            cv.put("payment_amount", payAmt);
            cv.put("payment_mode", payMode);
            cv.put("flag", 0);
            cv.put("attachment_id", attachment_id);
            synchronized (Lock) {
                database.insertWithOnConflict("fooding_expenses", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Fooding_expenses:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Fooding_expenses", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public String[] getUnuploadedFoodingExp(String transId) {
        String[] resultArray = new String[9];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM fooding_expenses where food_exp_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
                resultArray[6] = (cursor.getString(6));
                resultArray[7] = (cursor.getString(7));
                resultArray[8] = (cursor.getString(9));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public void updateUnuploadedTourFoodingExp(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("fooding_expenses", cv,
                        "food_exp_trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Fooding_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Fooding_expenses Update status ::::::::::::"
                + updateResult);
    }

    /*
     * FOODING EXPENSE TABLE TRANSACTION ENDS
     */

    /*
     * FREIGHT EXPENSE TABLE TRANSACTION STARTS
     */
    public void insertToFreightExpense(String transId, String transType, String date, String amount, String remarks) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("freight_exp_trans_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("trans_type", transType);
            cv.put("date", date);
            cv.put("amount", amount);
            cv.put("remarks", remarks);
            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("freight_expenses", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Freight_expenses:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Freight_expenses", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void insertToCashDepositReceiveDetails(String transId, String transType, String date, String amount, String remarks) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("cash_deposit_recv_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("trans_type", transType);
            cv.put("date", date);
            cv.put("amount", amount);
            cv.put("remarks", remarks);
//			cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("cash_deposit_receive_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Freight_expenses:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public String[] getUnuploadedFreightExp(String transId) {
        String[] resultArray = new String[9];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM freight_expenses where freight_exp_trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
                resultArray[6] = (cursor.getString(6));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public String[] getUnuploadedCashReceivedDeposited(String transId) {
        String[] resultArray = new String[9];
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM cash_deposit_receive_details where cash_deposit_recv_id= '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            cursor.moveToFirst();
            if (cursor.getCount() > 0) {
                resultArray[0] = (cursor.getString(0));
                resultArray[1] = (cursor.getString(1));
                resultArray[2] = (cursor.getString(2));
                resultArray[3] = (cursor.getString(3));
                resultArray[4] = (cursor.getString(4));
                resultArray[5] = (cursor.getString(5));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public void updateUnuploadedFreightExp(String transId) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("freight_expenses", cv,
                        "freight_exp_trans_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Freight_expenses Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Freight_expenses Update status ::::::::::::"
                + updateResult);
    }

    /*
     * FREIGHT EXPENSE TABLE TRANSACTION ENDS
     */

    /*
     * SUPPORTING ATTACH TABLE TRANSACTION STARTS
     */
    public void insertToSupportingAttachTable(String transId, String sourceName) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("attachment_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("source_name", sourceName);
            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("supporting_attachment_details",
                        null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public void UpdateSurveyImage() {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                database.update("supporting_attachment_details", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public void UpdateSurveyImage(String transId) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                database.update("supporting_attachment_details", cv,
                        "attachment_id=?", new String[]{transId});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }


    public ArrayList<String> getUnuploadedSupportingAttachTable(String source) {
        ArrayList<String> resultArray = new ArrayList<String>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT attachment_id FROM supporting_attachment_details WHERE attachment_id != '' AND flag = 0 AND source_name = '"
                    + source + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    String attachmentId = cursor.getString(0);
                    File outputFile = new File(Utils.getAppStoragePath(mContext) + attachmentId);

                    if (outputFile.exists()) {
                        resultArray.add(attachmentId);
                    }

                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return resultArray;
    }

    public void insertToNoOrderDetailsTable(String alphbt, String timestamp,
                                            NoOrderDetails noOrder) {
        String order_no = "";
        int flag = 0;
        order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                + timestamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("order_no", order_no);
            cv.put("sku_code", "sku001");
            cv.put("qty", "12");
            cv.put("flag", flag);
            cv.put("mrp_code", "12");
            cv.put("TD", "");
            cv.put("sale_rate", "1");
            cv.put("amount", "10");
            cv.put("UOM", "");

            synchronized (Lock) {
                database.insertWithOnConflict("no_order_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("no_order_details:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("no_order_details", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }




    public void insertToOrderHeaderTable(String alphbt, String timestamp,
                                         OrderHeader orderHeader) {
        String order_no = "";
        int flag = 0;
        order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                + timestamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("order_no", order_no);
            cv.put("customer_code", orderHeader.getCustomerCode());
            cv.put("transferred", orderHeader.getTransferred());
            cv.put("flag", flag);
            cv.put("d_instruction", orderHeader.getInstruction());
            cv.put("sale_type", orderHeader.getSalesType());
            cv.put("TD", orderHeader.getTrdDiscnt());
            cv.put("order_value", orderHeader.getOrder_value());
            cv.put("tag_distributor_code", orderHeader.getTag_distributor_code());
            cv.put("transaction_type", orderHeader.getTransaction_type());
            cv.put("VAT", orderHeader.getVAT());
            cv.put("vertical_value", orderHeader.getVerticalValue());

            synchronized (Lock) {
                database.insertWithOnConflict("order_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("OrderHeader:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("OrderHeader", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    /*
     * SUPPORTING ATTACH TABLE TRANSACTION ENDS
     */

    /*
     * ORDER HEADER TABLE TRANSACTION STARTS
     */
    public void insertToOrderHeaderTable(String alphbt, String instruction,
                                         String timeStamp, String sale_type, String trdDisc,
                                         String order_value, String tagDistributorCode,
                                         String transactionType, String vat, String destinationcode, String otype, String freightc) {
        String order_no = "";
        int transferred = 0, flag = 0;
        order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("order_no", order_no);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("transferred", transferred);
            cv.put("sale_type", sale_type);
            cv.put("flag", flag);
            cv.put("d_instruction", instruction);
            cv.put("TD", trdDisc);
            cv.put("order_value", order_value);
            cv.put("tag_distributor_code", tagDistributorCode);
            cv.put("transaction_type", transactionType);
            cv.put("VAT", vat);
            cv.put("vertical_value", Constants.mVerticalValue);
            cv.put("destination_code", Constants.mDestinationCode);
            cv.put("order_type", Constants.mOrderType);
            cv.put("freight_component", Constants.mFreightComponent);
            cv.put("GST_type", Constants.mChosenGstType);
            synchronized (Lock) {
                database.insertWithOnConflict("order_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("OrderHeader:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("OrderHeader", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    //	adding rollback
    public Boolean insertToOrderHeaderTable1(String alphbt, String instruction,
                                             String timeStamp, String sale_type, String trdDisc,
                                             String order_value, String tagDistributorCode,
                                             String transactionType, String vat, String destinationcode, String otype, String freightc)
    {
        Boolean isInsertionDone = true;
        if (orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && retailercareOrderOrAudit.equalsIgnoreCase("audit"))
        {

        }
        else
        {
            String order_no = "";
            int transferred = 0, flag = 0;
            order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
//	database.beginTransaction();
            try {
                if(Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")))
                {
                    if(Utils.isNumeric(order_value) && Utils.isNumeric(trdDisc))
                    {
                        double orderValueInDOuble = Double.parseDouble(order_value);
                        double tdInDOuble = Double.parseDouble(trdDisc);
                        if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("order value wise#amount"))
                        {

                            order_value= String.valueOf(orderValueInDOuble - tdInDOuble);
                        }
                        else
                        {
                            orderValueInDOuble=orderValueInDOuble - ((tdInDOuble*orderValueInDOuble)/100);
                            order_value=String.valueOf(orderValueInDOuble);
                        }

                    }
                }
                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                cv.put("transferred", transferred);
                cv.put("sale_type", sale_type);
                cv.put("flag", flag);
                cv.put("d_instruction", instruction);
                cv.put("TD", trdDisc);
                cv.put("order_value", order_value);
                cv.put("tag_distributor_code", tagDistributorCode);
                cv.put("transaction_type", transactionType);
                cv.put("VAT", vat);
                cv.put("vertical_value", Constants.mVerticalValue);
                cv.put("destination_code", Constants.mDestinationCode);
                cv.put("order_type", Constants.mOrderType);
                cv.put("freight_component", Constants.mFreightComponent);
                cv.put("GST_type", Constants.mChosenGstType);
                cv.put("price_validation_type", mOrderPriceValidationType);
                cv.put("freight_component_value", mFreightComponentAmountFor);
                synchronized (Lock) {
                    database.insertWithOnConflict("order_header", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("OrderHeader:", "Data Inserted");
                }
            } catch (SQLException e) {
                isInsertionDone = false;
            } finally {

            }
        }


        return isInsertionDone;
    }

    public Boolean insertToQuotationHeaderTable(String alphbt,
                                                String timeStamp, String quotationDate,
                                                String customerName, String customerAddress,
                                                String customerEmail, String customerPhone, String contactPersonName, String contactPersonPhone, String contactPersonEmail, String quotationValidTill, String totalAmount) {
        Boolean isInsertionDone = true;
        String quotationNo = "";
        quotationNo = alphbt + Constants.employeeDetailObject.getEmpCode() + timeStamp;

        try {
            ContentValues cv = new ContentValues();
            cv.put("quotation_no", quotationNo);
            cv.put("quotation_date", quotationDate);
            cv.put("customer_name", customerName);
            cv.put("customer_address", customerAddress);
            cv.put("customer_email", customerEmail);
            cv.put("customer_phone", customerPhone);
            cv.put("contact_name", contactPersonName);
            cv.put("contact_phone", contactPersonPhone);
            cv.put("contact_email", contactPersonEmail);
            cv.put("quotation_validity", quotationValidTill);
            cv.put("total_amount", totalAmount);
            synchronized (Lock) {
                database.insertWithOnConflict("quotation_header", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {

        }
        return isInsertionDone;
    }

    public void insertToOrderHeaderSaudaTable(String alphbt,
                                              String instruction, String timeStamp, String sale_type,
                                              String trdDisc, String order_value, String tagDistributorCode,
                                              String transactionType, String vat, String branchCode) {
        String order_no = "";
        int transferred = 0, flag = 0;
        order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("order_no", order_no);
            cv.put("customer_code",
                    Constants.selectedCustomer.getCustomerCode());
            cv.put("transferred", transferred);
            cv.put("flag", flag);
            cv.put("d_instruction", instruction);
            cv.put("sale_type", sale_type);
            cv.put("TD", trdDisc);
            cv.put("order_value", order_value);
            cv.put("tag_distributor_code", tagDistributorCode);
            cv.put("transaction_type", transactionType);
            cv.put("VAT", vat);
            cv.put("branch_code", branchCode);

            synchronized (Lock) {
                database.insertWithOnConflict("order_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("OrderHeader:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("OrderHeader", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void insertToOrderHeaderTableForStockin(String alphbt,
                                                   String instruction, String timeStamp, String sale_type,
                                                   String trdDisc, String order_value, String tagDistributorCode,
                                                   String transactionType, String vat) {
        String order_no = "";
        int transferred = 0, flag = 0;
        order_no = alphbt + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("order_no", order_no);
            if (transactionType.equalsIgnoreCase("BT")
                    || transactionType.equalsIgnoreCase("ST")) {
                if (Constants.orderFormDetailsObj.getBranchRDSTransfer()
                        .equalsIgnoreCase("yes")) {
                    cv.put("customer_code", Constants.selectedRDS.getRdsCode());
                } else {
                    cv.put("customer_code",
                            Constants.selectedBranch.getBranchCode());
                }
            } else if (transactionType.equalsIgnoreCase("SR")) {
                cv.put("customer_code",
                        Constants.selectedBranch.getBranchCode());
            } else if (transactionType.equalsIgnoreCase("CN")) {
                cv.put("customer_code", Constants.selectedEmp.getEmpCode());
            } else if (transactionType.equalsIgnoreCase("CR")) {
                cv.put("customer_code",
                        Constants.selectedCustomer.getCustomerCode());
            } else if (transactionType.equalsIgnoreCase("PB")) {
                cv.put("customer_code",
                        Constants.selectedVendor.getVendorCode());
            } else if (transactionType.equalsIgnoreCase("SA")
                    || transactionType.equalsIgnoreCase("SH")) {
                cv.put("customer_code", "");
            }
            cv.put("transferred", transferred);
            cv.put("flag", flag);
            cv.put("d_instruction", instruction);
            cv.put("sale_type", sale_type);
            cv.put("TD", trdDisc);
            cv.put("order_value", order_value);
            cv.put("tag_distributor_code", tagDistributorCode);
            cv.put("transaction_type", transactionType);
            cv.put("VAT", vat);
            cv.put("vertical_value", Constants.mVerticalValue);
            synchronized (Lock) {
                database.insertWithOnConflict("order_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("OrderHeader:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("OrderHeader", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<MarketFeedback> getUnuploadedFeedBackData(String transId) {
        ArrayList<MarketFeedback> mMarketFeedbackList = new ArrayList<MarketFeedback>();
        Cursor cursor = null;
        Cursor cursorName = null;
        try {
            String selectQuery = "SELECT market_feedback_id,route_code,product_group,competitor_name,PTD,PTR,PTC,PV,customer_code,billing_ex_for,wsp_ex_for,rsp_ex_for,nod_ex_for FROM market_feedback WHERE market_feedback_id='" + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    MarketFeedback obj = new MarketFeedback();
                    obj.setFeedbackID(cursor.getString(0));
                    obj.setRouteCode(cursor.getString(1));
                    obj.setProductGroup(cursor.getString(2));
                    //obj.setCopmpetitorName(cursor.getString(3));
                    obj.setPtd(cursor.getString(4));
                    obj.setPtr(cursor.getString(5));
                    obj.setPtc(cursor.getString(6));
                    obj.setPv(cursor.getString(7));
                    obj.setCustomerCode(cursor.getString(8));
                    obj.setmBillingExFor(cursor.getString(9));
                    obj.setmWspExFor(cursor.getString(10));
                    obj.setmRspExFor(cursor.getString(11));
                    obj.setmNodExFor(cursor.getString(12));


                    selectQuery = "SELECT competitor_name FROM competitor_group_master WHERE display_name ='" + cursor.getString(3) + "'";
                    cursorName = database.rawQuery(selectQuery, null);
                    if (cursorName.getCount() > 0) {
                        cursorName.moveToFirst();
                        for (int j = 0; j < cursorName.getCount(); j++) {

                            if(cursorName.getString(0).isEmpty()){
                                obj.setCopmpetitorName(cursor.getString(3));
                            }else{
                                obj.setCopmpetitorName(cursorName.getString(0));
                            }
                            cursorName.moveToNext();
                        }
                    }else{
                        obj.setCopmpetitorName(cursor.getString(3));
                    }



                    mMarketFeedbackList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
            cursorName.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mMarketFeedbackList;
    }


    public ArrayList<SaudaAllocation> GetUnuploadedSaudaAllocationData(String transId) {
        ArrayList<SaudaAllocation> mSaudaAllocationList = new ArrayList<SaudaAllocation>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT allocation_id,date,emp_code,product_filter_code,qty_ton FROM sauda_allocation_log WHERE allocation_id='" + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SaudaAllocation obj = new SaudaAllocation();
                    obj.setAllocationId(cursor.getString(0));
                    obj.setDate(cursor.getString(1));
                    obj.setEmployeCode(cursor.getString(2));
                    obj.setProductFilterCode(cursor.getString(3));
                    obj.setQty(cursor.getString(4));
                    mSaudaAllocationList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mSaudaAllocationList;
    }

    public ArrayList<TDAllocation> GetUnuploadedTDAllocationData(String transId) {
        ArrayList<TDAllocation> TDAllocationList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM TD_allocation_log WHERE allocation_id='" + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TDAllocation obj = new TDAllocation();
                    obj.setAllocationId(cursor.getString(0));
                    obj.setDate(cursor.getString(1));
                    obj.setEmployeCode(cursor.getString(2));
                    obj.setProductFilterCode(cursor.getString(3));
                    obj.setTDAllocated(cursor.getString(4));
                    TDAllocationList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return TDAllocationList;
    }

    public ArrayList<OrderHeader> getUnuploadedOrdrHeadr(String transId) {
        ArrayList<OrderHeader> unUploadedOrdrHeadrList = new ArrayList<OrderHeader>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT OH.*,cm.base_latt, cm.base_longi,cm.image, (SELECT grn_no FROM goods_in_transit WHERE order_no = '" + transId
                    + "') FROM order_header OH, customer_master cm WHERE cm.customer_code=OH.customer_code and order_no = '" + transId + "' limit 1";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    OrderHeader headerObj = new OrderHeader();
                    headerObj.setOrderNo(cursor.getString(0));
                    headerObj.setCustomerCode(cursor.getString(1));
                    headerObj.setTransferred(cursor.getInt(2));
                    headerObj.setSalesType(cursor.getString(3));
                    headerObj.setInstruction(cursor.getString(5));
                    headerObj.setTrdDiscnt(cursor.getString(6));
                    headerObj.setOrder_value(cursor.getString(7));
                    headerObj.setTag_distributor_code(cursor.getString(8));
                    headerObj.setTransaction_type(cursor.getString(9));
                    headerObj.setVAT(cursor.getString(10));
                    headerObj.setVerticalValue(cursor.getString(11));
                    headerObj.setDestinationCode(cursor.getString(12));
                    headerObj.setOrderType(cursor.getString(13));
                    headerObj.setFreightComponent(cursor.getString(14));
                    headerObj.setGstType(cursor.getString(15));
                    headerObj.setPriceValidationType(cursor.getString(16));
                    headerObj.setFreight_component_value(cursor.getString(17));
                    headerObj.setcustomerLat(cursor.getString(18));
                    headerObj.setcustomerLong(cursor.getString(19));
                    headerObj.setcustomerImage(cursor.getString(20));
                    headerObj.setGrnNo(cursor.getString(21));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<QuotationDetails> getUnuploadedQuotationHeadr(String transId) {
        ArrayList<QuotationDetails> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM quotation_header WHERE quotation_no = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    QuotationDetails headerObj = new QuotationDetails();
                    headerObj.setQuotationNo(cursor.getString(0));
                    headerObj.setQuotationDate(cursor.getString(1));
                    headerObj.setQuotationForName(cursor.getString(2));
                    headerObj.setQuotationForAddress(cursor.getString(3));
                    headerObj.setQuotationForEmail(cursor.getString(4));
                    headerObj.setQuotationForPhone(cursor.getString(5));
                    headerObj.setQuotationContactPersonName(cursor.getString(6));
                    headerObj.setQuotationContactPersonPhone(cursor.getString(7));
                    headerObj.setQuotationContactPersonEmail(cursor.getString(8));
                    headerObj.setQuotationValidity(cursor.getString(9));
                    headerObj.setQuotationProductAmount(cursor.getString(10));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<MenuOutstandingParent> getUnuploadedCollectionForecastDetails (String transId) {
        ArrayList<MenuOutstandingParent> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM collection_forecast_details WHERE forecast_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    MenuOutstandingParent headerObj = new MenuOutstandingParent();
                    headerObj.setForecastId(cursor.getString(0));
                    headerObj.setCustomerCode(cursor.getString(1));
                    headerObj.setForecastDate(cursor.getString(2));
                    headerObj.setTotalInvoice(cursor.getString(3));
                    headerObj.setForecastAmount(cursor.getString(4));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }
    public ArrayList<commonDatabaseHelper> getUnuploadedGrnDetails (String transId) {
        ArrayList<commonDatabaseHelper> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT GRN_received_by,GRN_code, GRN_date, DO_no,sku_code, dispatch_qty,received_qty,remarks  FROM GRN_transaction WHERE GRN_code = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper headerObj = new commonDatabaseHelper();
                    headerObj.setItem0(cursor.getString(0));//GRN_received_by
                    headerObj.setItem1(cursor.getString(1));//GRN_code
                    headerObj.setItem2(cursor.getString(2));//GRN_date
                    headerObj.setItem3(cursor.getString(3));//DO_no
                    headerObj.setItem4(cursor.getString(4));//sku_code
                    headerObj.setItem5(cursor.getString(5));//dispatch_qty
                    headerObj.setItem6(cursor.getString(6));//received_qty
                    headerObj.setItem7(cursor.getString(7));//remarks
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }
    public ArrayList<ProductMasterDetails> getUnuploadedRequisationDetails(String transId) {
        ArrayList<ProductMasterDetails> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM requisition_details WHERE requisition_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ProductMasterDetails headerObj = new ProductMasterDetails();
                    headerObj.setallocation_id(cursor.getString(0));
                    headerObj.setProdCode(cursor.getString(1));
                    headerObj.setallocation_qty(cursor.getString(2));
                    headerObj.setrequisition_id(cursor.getString(3));
                    headerObj.setQty(cursor.getString(4));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }
    public ArrayList<ProductMasterDetails> getUnuploadedStockReallocationDetails(String transId) {
        ArrayList<ProductMasterDetails> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM customer_product_allocation WHERE allocation_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ProductMasterDetails headerObj = new ProductMasterDetails();
                    headerObj.setallocation_id(cursor.getString(0));
                    headerObj.setcustomerCode(cursor.getString(1));
                    headerObj.setProdCode(cursor.getString(2));
                    headerObj.setQty(cursor.getString(3));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<BillingInformationStockSummaryData> getUnuploadedStockOutDetails(String transId) {
        ArrayList<BillingInformationStockSummaryData> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM stock_out_details WHERE stock_out_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    BillingInformationStockSummaryData headerObj = new BillingInformationStockSummaryData();
                    headerObj.setsold_out_id(cursor.getString(0));
                    headerObj.setProductCode(cursor.getString(1));
                    String currentImei = cursor.getString(2);
                    headerObj.setimei(currentImei);
                    headerObj.setsold_out_date(cursor.getString(3));
                    headerObj.setqty(cursor.getString(4));
                    String selectQuery2 = "SELECT stock_out_customer_code FROM customer_product_billing WHERE IMEI = '" + currentImei + "'";
                    Cursor cursor2  = database.rawQuery(selectQuery2, null);
                    if (cursor2.getCount() > 0)
                    {
                        cursor2.moveToFirst();
                        headerObj.setcustomerCode(cursor2.getString(0));
                        cursor2.close();
                    }
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }


    public ArrayList<YellowCard> getUnuploadedYellowCard(String transId) {
        ArrayList<YellowCard> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM yellow_card_details WHERE yellow_card_no = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    YellowCard headerObj = new YellowCard();
                    headerObj.setyellow_card_no(cursor.getString(0));
                    headerObj.setcustomer_code(cursor.getString(1));
                    headerObj.setchallan_no(cursor.getString(2));
                    headerObj.setchallan_date(cursor.getString(3));
                    headerObj.setqty(cursor.getString(4));
                    headerObj.setqty_UOM(cursor.getString(5));
                    headerObj.setflag(cursor.getString(6));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<JointWorkObservation> getUnuploadedJointWork(String transId) {
        ArrayList<JointWorkObservation> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM joint_work_observation WHERE joint_work_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    JointWorkObservation headerObj = new JointWorkObservation();
                    headerObj.setjoint_work_observation(cursor.getString(0));
                    headerObj.setcustomer_code(cursor.getString(1));
                    headerObj.setroute_code(cursor.getString(2));
                    headerObj.setemp_code(cursor.getString(3));
                    headerObj.setobservation_on_customer(cursor.getString(4));
                    headerObj.setobservation_on_employee(cursor.getString(5));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<commonDatabaseHelper> getUnuploadedOrderApproval(String transId) {
        ArrayList<commonDatabaseHelper> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT approval_id,customer_code,changed_sub_dealer_code,APPORDERNO,changed_dns_prod_code,QTY_CHANGED,changed_dns_destination_code,changed_dump_code,plant_name,approval_status,approval_done_by,remarks,changed_order_for,changed_consignee_address FROM T_APPERPDO_APPROVAL WHERE approval_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper headerObj = new commonDatabaseHelper();
                    headerObj.setItem0(cursor.getString(0));
                    headerObj.setItem1(cursor.getString(1));
                    headerObj.setItem2(cursor.getString(2));
                    headerObj.setItem3(cursor.getString(3));
                    headerObj.setItem4(cursor.getString(4));
                    headerObj.setItem5(cursor.getString(5));
                    headerObj.setItem6(cursor.getString(6));
                    headerObj.setItem7(cursor.getString(7));
                    headerObj.setItem8(cursor.getString(8));
                    headerObj.setItem9(cursor.getString(9));
                    headerObj.setItem10(cursor.getString(10));
                    headerObj.setItem11(cursor.getString(11));
                    headerObj.setItem12(cursor.getString(12));
                    headerObj.setItem13(cursor.getString(13));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }
    public ArrayList<commonDatabaseHelper> getUnuploadedTMApproval() {
        ArrayList<commonDatabaseHelper> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT meet_id,meet_date,no_of_mason,dealer_code,dealer_name,is_approved,approved_date_time,approved_by FROM Tech_meet_details WHERE (lower(is_approved)  = 'approved' OR lower(is_approved)  = 'rejected') AND flag='0' ";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper headerObj = new commonDatabaseHelper();
                    headerObj.setItem0(cursor.getString(0));
                    headerObj.setItem1(cursor.getString(1));
                    headerObj.setItem2(cursor.getString(2));
                    headerObj.setItem3(cursor.getString(3));
                    headerObj.setItem4(cursor.getString(4));
                    headerObj.setItem5(cursor.getString(5));
                    headerObj.setItem6(cursor.getString(6));
                    headerObj.setItem7(cursor.getString(7));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }

    public ArrayList<commonDatabaseHelper> getUnuploadedTMStatus() {
        ArrayList<commonDatabaseHelper> unUploadedOrdrHeadrList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT meet_id,meet_date,no_of_mason,dealer_code,dealer_name,mason_details,meet_status,status_update_date_time,status_update_by,image,remarks FROM Tech_meet_meeting_status WHERE length(status_update_date_time)  > 4  AND flag='0' ";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper headerObj = new commonDatabaseHelper();
                    headerObj.setItem0(cursor.getString(0));
                    headerObj.setItem1(cursor.getString(1));
                    headerObj.setItem2(cursor.getString(2));
                    headerObj.setItem3(cursor.getString(3));
                    headerObj.setItem4(cursor.getString(4));
                    headerObj.setItem5(cursor.getString(5));
                    headerObj.setItem6(cursor.getString(6));
                    headerObj.setItem7(cursor.getString(7));
                    headerObj.setItem8(cursor.getString(8));
                    headerObj.setItem9(cursor.getString(9));
                    headerObj.setItem10(cursor.getString(10));
                    unUploadedOrdrHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrHeadrList;
    }
    public ArrayList<CashDeposit> getUnuploadedCashDeposit(String transId) {
        ArrayList<CashDeposit> unUploadedDataList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM cash_deposit_details WHERE cash_deposit_trans_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CashDeposit headerObj = new CashDeposit();
                    headerObj.setcash_deposit_trans_id(cursor.getString(0));
                    headerObj.setbank_name(cursor.getString(1));
                    headerObj.setdeposit_value(cursor.getString(2));
                    unUploadedDataList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedDataList;
    }

    public ArrayList<CashTransferReceive> getUnuploadedCashTransfer(String transId) {
        ArrayList<CashTransferReceive> unUploadedDataList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM cash_transaction_details WHERE cash_transaction_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CashTransferReceive headerObj = new CashTransferReceive();
                    headerObj.setcash_trans_rcv_trans_id(cursor.getString(0));
                    headerObj.setdespatcher_code(cursor.getString(1));
                    headerObj.setreceiver_code(cursor.getString(2));
                    headerObj.setdespatch_value(cursor.getString(3));
                    headerObj.setrec_value(cursor.getString(4));
                    headerObj.settransaction_type(cursor.getString(6));
                    headerObj.setcash_transfer_id(cursor.getString(7));
                    unUploadedDataList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedDataList;
    }

    public String getUnuploadedHintRemarks(String transId) {
        String hintRemarks = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT hint_remarks from hint_remarks_details WHERE trans_id = '" + transId + "'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                hintRemarks = cursor.getString(0);

            }
            cursor.close();
        } catch (Exception e) {

        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return hintRemarks;
    }

    public void updateUnuploadedOrderHeader() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("order_header", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Exceptio in OrderHeader Update :", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("OrderHeader Update status ::::::::::::"
                + updateResult);
    }

    public void updateUnUploadedHintRemarks(String TransactionType) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                database.execSQL("UPDATE hint_remarks_details SET flag = '1' WHERE SUBSTR(trans_id,1,1) = '" + TransactionType + "' AND flag = '0'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public void updateUnUploadedYellowCardDetails() {
        database.beginTransaction();
        try {
            synchronized (Lock) {
                database.execSQL("UPDATE yellow_card_details SET flag = '1' WHERE flag = '0'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public void updateUploadedCustomerFlag(ArrayList<commonDatabaseHelper> unUploadedTransaction)
    {
        database.beginTransaction();
        try
        {
            synchronized (Lock)
            {
                String customerCodes="";
                for(int i=0;i<unUploadedTransaction.size();i++)
                {
                    if(customerCodes.matches(""))
                    {
                        customerCodes="'"+unUploadedTransaction.get(i).getItem0()+"'";
                    }
                    else
                    {
                        customerCodes=customerCodes+",'"+unUploadedTransaction.get(i).getItem0()+"'";
                    }
                }
                String sql = "UPDATE customer_master SET check_flag = '1' WHERE customer_code in(" + customerCodes + ")";
                database.execSQL(sql);
                database.setTransactionSuccessful();
            }

        }
        catch (SQLException e)
        {

        }
        finally
        {
            database.endTransaction();
        }
    }

    public void updateUnUpdatedOrderApprovalData() {
        database.beginTransaction();
        try {
            synchronized (Lock) {
                database.execSQL("UPDATE T_APPERPDO_APPROVAL SET flag = '1' WHERE flag = '0' and lower(approval_status)!='pending'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }
    public void updateUnUpdatedTMApprovalData() {
        database.beginTransaction();
        try {
            synchronized (Lock) {
                database.execSQL("UPDATE Tech_meet_details SET flag = '1' WHERE flag = '0' and (lower(is_approved)  = 'approved' OR lower(is_approved)  = 'rejected')");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }
    public void updateUnUpdatedTMApprovalStatusData() {
        database.beginTransaction();
        try {
            synchronized (Lock) {
                database.execSQL("UPDATE Tech_meet_meeting_status SET flag = '1' WHERE flag = '0' and length(status_update_date_time ) > 4 ");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }
    public void insertToOrderDetailsTable(String transactiontype, String timeStamp) {
        String order_no = "", sale_rate = "";
        int flag = 0;
        order_no = transactiontype + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails masterObj = Constants.selectedProductMasterList
                    .get(ii);
            if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase(
                    "yes")) {
                sale_rate = masterObj.getMrpValue();
            }
            database.beginTransaction();

            try {
                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("sku_code", masterObj.getProdCode());
                cv.put("qty", masterObj.getQty());
                cv.put("TD", masterObj.getTradeDiscnt());
                cv.put("flag", flag);
                cv.put("mrp_code", masterObj.getMrpCode());
                cv.put("sale_rate", sale_rate);
                cv.put("VAT", masterObj.getVat());
                cv.put("amount", masterObj.getAmount());
                cv.put("weightage", masterObj.getWeightage());
                if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                    cv.put("remarks", masterObj.getOrder_wise_remarks());
                }
                if(Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes")){
                    cv.put("input_size", masterObj.getWeidth() + "ft*" + Constants.sylHight+"ft");
                }else{
                    cv.put("input_size", "");
                }

                if (Constants.menuDetailsObj.getPurpose_of_visit().equalsIgnoreCase("yes"))
                {
                    cv.put("purpose_of_visit", localStorage.getPurpose_of_visit());
                }else {
                    cv.put("purpose_of_visit", "");
                }

                synchronized (Lock) {
                    database.insertWithOnConflict("order_details", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("OrderDetails:", "Data Inserted");
                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("OrderDetails", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }

    /*
     * ORDER HEADER TABLE TRANSACTION ENDS
     */

    /*
     * INVOICE INFORMATION TABLE TRANSACTION STARTS
     */
    public Boolean insertToInvoiceInformationTable(String timeStamp, String invoice_no, String invoice_date, String freightCharge) {
        Boolean isInsertionDone = true;
        try {
            String[] splittedByHypen = invoice_no.split("-");
            int chronologicalNumber = Integer.parseInt(splittedByHypen[splittedByHypen.length - 1]);
            int flag = 0;
            String order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

//			database.beginTransaction();

            ContentValues cv = new ContentValues();
            cv.put("invoice_no", invoice_no);
            cv.put("invoice_date", invoice_date);//yyyy-MM-dd
            cv.put("order_no", order_no);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("chronological_no", chronologicalNumber);
            if (freightCharge != null) {
                cv.put("freight_charge", freightCharge);
            }
            cv.put("flag", flag);

            synchronized (Lock) {
                database.insertWithOnConflict("invoice_information", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
//				database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//				database.endTransaction();
        }
        return isInsertionDone;
    }

    /*
     * ORDER DETAILS TABLE TRANSACTION STARTS
     */
    public void insertToOrderDetailsTable(String timeStamp) {
        String order_no = "", sale_rate = "";
        int flag = 0;
        order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails masterObj = Constants.selectedProductMasterList
                    .get(ii);
            if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
                sale_rate = masterObj.getMrpValue();
            }
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("sku_code", masterObj.getProdCode());
                cv.put("qty", masterObj.getQty());
                cv.put("TD", masterObj.getTradeDiscnt());
                cv.put("flag", flag);
                cv.put("mrp_code", masterObj.getMrpCode());
                cv.put("sale_rate", sale_rate);
                cv.put("VAT", masterObj.getVat());
                cv.put("amount", masterObj.getAmount());
                cv.put("freight_charge", masterObj.getFreight());
                cv.put("premium", masterObj.getPremium());
                if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                    cv.put("remarks", masterObj.getOrder_wise_remarks());
                }

                synchronized (Lock) {
                    database.insertWithOnConflict("order_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("OrderDetails:", "Data Inserted");
                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("OrderDetails", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }
    public Boolean updateICustomerNew()
    {
        Boolean isInsertionDone = true;
        try
        {
            synchronized (Lock)
            {
                String sql = "UPDATE customer_master SET is_new_customer = 'no' WHERE customer_code ='" + Constants.selectedCustomer.getCustomerCode() + "'";
                database.execSQL(sql);
            }

        }
        catch (SQLException e)
        {
            isInsertionDone = false;
        }
        finally
        {
        }
        return isInsertionDone;
    }
    public Boolean insertToOrderDetailsTable1(String timeStamp) {
        Boolean isInsertionDone = true;
        String order_no = "", sale_rate = "";
        try {
            if (orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && retailercareOrderOrAudit.equalsIgnoreCase("audit"))
            {

            }
            else
            {
                int flag = 0;
                order_no = "O" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++)
                {
                    ProductMasterDetails masterObj = Constants.selectedProductMasterList.get(ii);
                    String qty = masterObj.getQty();
                    if(Utils.isNumeric(qty) && Double.parseDouble(qty)>0)
                    {
                        if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") || Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes"))
                        {
                            sale_rate = masterObj.getMrpValue();
                        }

                        ContentValues cv = new ContentValues();
                        cv.put("order_no", order_no);
                        String prodCode = masterObj.getProdCode();
                        cv.put("sku_code", prodCode);

                        cv.put("qty", qty);
                        String tradeDiscnt = masterObj.getTradeDiscnt();
                        if (Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes") && masterObj.getQuantityEligibleForTD()) {
                            tradeDiscnt = masterObj.getTDPercent();
                        }
                        cv.put("TD", tradeDiscnt);
                        cv.put("flag", flag);
                        cv.put("mrp_code", masterObj.getMrpCode());
                        cv.put("sale_rate", sale_rate);
                        cv.put("VAT", masterObj.getVat());
                        cv.put("amount", masterObj.getAmount());
                        cv.put("freight_charge", masterObj.getFreight());
                        cv.put("premium", masterObj.getPremium());
                        cv.put("weightage", masterObj.getWeightage());
                        if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                            cv.put("remarks", masterObj.getOrder_wise_remarks());
                        }
                        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                            cv.put("UOM", masterObj.getUomSelectedForProduct());
                        } else {
                            String uom = "";
                            if (uomToBeShownForProduct.equalsIgnoreCase("uom1")) {
                                uom = masterObj.getUom1();
                            } else if (uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
                                uom = masterObj.getUom2();
                            } else {
                                uom = masterObj.getUOM3();
                            }
                            cv.put("UOM", uom);
                        }

                        if(Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes")){
                            cv.put("input_size", masterObj.getWeidth() + "ft*" + masterObj.getHeight()+"ft");
                        }else{
                            cv.put("input_size", "");
                        }


                        if(Constants.isVanSales)
                        {
                            String newClosingStock=String .valueOf(Double.parseDouble(masterObj.getClosingStk())-Double.parseDouble(masterObj.getQty()));
                            String updateClosingStockQuery ="update van_stock_allocation set balance_qty ='"+newClosingStock+"' WHERE prod_code ='"+prodCode+"'";
                            database.execSQL(updateClosingStockQuery);
                        }
                        synchronized (Lock)
                        {
                            database.insertWithOnConflict("order_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

                        }
                    }

                }
            }


            //insert freebies to product details
            if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes") && selectedProductMasterListFreebies != null && !selectedProductMasterListFreebies.isEmpty())
            {
                for (int ii = 0; ii < Constants.selectedProductMasterListFreebies.size(); ii++) {
                    ProductMasterDetails masterObj = Constants.selectedProductMasterListFreebies.get(ii);

                    ContentValues cv = new ContentValues();
                    cv.put("order_no", order_no);
                    cv.put("sku_code", masterObj.getProdCode());
                    cv.put("qty", masterObj.getQty());
                    cv.put("scheme_type", "foc");
                    cv.put("UOM", masterObj.getfreeBieUom());
                    synchronized (Lock) {
                        database.insertWithOnConflict("order_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

                    }

                }
                selectedProductMasterListFreebies.clear();
            }

        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }

    public Boolean insertToDOTransactionTable(String timeStamp) {
        Boolean isInsertionDone = true;
        try {
            String order_no = "";
            int flag = 0;
            order_no = "DO" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++)
            {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList.get(ii);
                String qty = masterObj.getQty();
                if(Utils.isNumeric(qty) && Double.parseDouble(qty)>0)
                {

                    ContentValues cv = new ContentValues();
//                    String chosenBargainListWithoutQuote = getChosenBargainListWithoutQuote();
                    cv.put("sauda_no", masterObj.getsaudaNo());
                    doBargainNo= AceDnsDatabase.getChosenDnsBargainListWithoutQuote();
                    cv.put("customer_code", Constants.selectedCustomerDeliveryAddress.getCustomerCode());
                    cv.put("destination", Constants.selectedCustomerDeliveryAddress.getRouteCode());
                    cv.put("DO_no", order_no);
                    cv.put("sku_code", masterObj.getProdCode());
                    cv.put("DO_qty", qty);
                    cv.put("DO_rate", masterObj.getMrpValue());
                    cv.put("DO_amount", masterObj.getAmount());
                    cv.put("DO_status", "");
                    cv.put("DO_date", Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp));
                    cv.put("PO_no", marginPoNo);
                    cv.put("flag", "0");
                    cv.put("delivery_date", Constants.deliveryDate);
                        String updateClosingStockQuery ="update DO_master set bargain_status ='yes' WHERE sauda_no='"+masterObj.getsaudaNo()+"'";
                        database.execSQL(updateClosingStockQuery);

                    synchronized (Lock)
                    {
                        database.insertWithOnConflict("DO_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
                }

            }
            //insert freebies to product details
            if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes") && selectedProductMasterListFreebies != null && !selectedProductMasterListFreebies.isEmpty()) {
                for (int ii = 0; ii < Constants.selectedProductMasterListFreebies.size(); ii++) {
                    ProductMasterDetails masterObj = Constants.selectedProductMasterListFreebies.get(ii);

                    ContentValues cv = new ContentValues();
                    cv.put("order_no", order_no);
                    cv.put("sku_code", masterObj.getProdCode());
                    cv.put("qty", masterObj.getQty());
                    cv.put("scheme_type", "foc");
                    cv.put("UOM", masterObj.getfreeBieUom());
                    synchronized (Lock) {
                        database.insertWithOnConflict("order_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

                    }
                }
                selectedProductMasterListFreebies.clear();
            }

        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }
    public Boolean insertToGiftDeliveryDetailsTable(String timeStamp,String delivery_date,String gift_id,String gift_name,String customer_broad_option,String customer_code,String gift_delivery_option,String owner_name,String employee_name,String employee_mobile,String employee_relation_owner,String address,String route_code,String image_1,String image_2) {
        Boolean isInsertionDone = true;
        try {
            String order_no = "";
            int flag = 0;
            order_no = "GD" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

                    ContentValues cv = new ContentValues();
//                    String chosenBargainListWithoutQuote = getChosenBargainListWithoutQuote();
                    cv.put("delivery_id",order_no);
                    cv.put("delivery_date",delivery_date);
                    cv.put("gift_id",gift_id);
                    cv.put("gift_name", gift_name);
                    cv.put("customer_broad_option",customer_broad_option);
                    cv.put("customer_code", customer_code);
                    cv.put("gift_delivery_option", gift_delivery_option);
                    cv.put("owner_name", owner_name);
                    cv.put("employee_name", employee_name);
                    cv.put("employee_mobile", employee_mobile);
                    cv.put("employee_relation_owner", employee_relation_owner );
                    cv.put("location", address );
                    cv.put("route_code", route_code  );
                    cv.put("image_1", image_1);
                    cv.put("image_2", image_2);

                    synchronized (Lock)
                    {
                        database.insertWithOnConflict("gift_delivery_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }


        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }
    public Boolean insertToOrderSummaryTable(String timeStamp) {
        Boolean isInsertionDone = true;
        try {
            String order_no = "", sale_rate = "";
            order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
                if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
                    sale_rate = masterObj.getMrpValue();
                }

                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                cv.put("product_code", masterObj.getProdCode());
                cv.put("visit_qty", masterObj.getQty());
                cv.put("visit_date", Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", dateString));
                cv.put("rate", sale_rate);
                cv.put("amount", masterObj.getAmount());

                synchronized (Lock) {
                    database.insertWithOnConflict("order_summary", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

                }

            }
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
        }
        return isInsertionDone;
    }

    public Boolean insertToSchemeSummaryTable(String timeStamp, ArrayList<SchemeFreebiesDetails> selectedProductMasterListSchemeOffers) {
        Boolean isInsertionDone = true;
        try {
            String order_no = "";
            order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

            for (int ii = 0; ii < selectedProductMasterListSchemeOffers.size(); ii++) {
                SchemeFreebiesDetails masterObj = selectedProductMasterListSchemeOffers.get(ii);


                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("scheme_id", masterObj.getschemeId());
                cv.put("scheme_prod_code", masterObj.getprodCode());
                cv.put("freebies_prod_code", masterObj.getfreebiesProdCode());
                cv.put("freebies_prod_desc", masterObj.getfreebiesProdDesc());
                cv.put("freebies_qty", masterObj.getFreebieQty());
                cv.put("freebies_val_percent", masterObj.getvaluePercent());
                cv.put("freebies_val_amount", masterObj.getvalueAmount());

                synchronized (Lock) {
                    database.insertWithOnConflict("scheme_summary", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

                }

            }
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
        }
        return isInsertionDone;
    }

    public Boolean insertToQuotationDetailsTable(String timeStamp) {
        Boolean isInsertionDone = true;
        try {
            String quotation_no = "";
            quotation_no = "Q" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

            for (int ii = 0; ii < Constants.QuotationDetailsList.size(); ii++) {
                QuotationDetails masterObj = Constants.QuotationDetailsList.get(ii);

                ContentValues cv = new ContentValues();
                cv.put("quotation_no", quotation_no);
                cv.put("qty", masterObj.getQuotationQuantity());
                cv.put("product_id", masterObj.getQuotationProductCode());
                cv.put("product_desc", masterObj.getQuotationProductDesc());
                cv.put("unit_price", masterObj.getQuotationProductRate());
                cv.put("taxes", masterObj.getQuotationProductTax());
                cv.put("amount", masterObj.getQuotationProductAmount());
                synchronized (Lock) {
                    database.insertWithOnConflict("quotation_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                }
            }
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
        }
        return isInsertionDone;
    }

    public ArrayList<OrderDetails> getUnuploadedOrdrDetails(String orderNo, String transType) {
        String selectQuery = "";
        Cursor cursor = null;
        ArrayList<OrderDetails> unUploadedOrdrDetailsList = new ArrayList<OrderDetails>();
        try {

            selectQuery = "SELECT OD.* FROM order_details OD WHERE OD.order_no = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    OrderDetails detailsObj = new OrderDetails();
                    detailsObj.setOrderNo(cursor.getString(0));
                    detailsObj.setSkuCode(cursor.getString(1));
                    detailsObj.setQty(cursor.getDouble(2));
                    detailsObj.setMrpCode(cursor.getString(4));
                    detailsObj.setTD(cursor.getString(5));
                    detailsObj.setSaleRate(cursor.getString(6));
                    detailsObj.setVAT(cursor.getString(7));
                    detailsObj.setAmount(cursor.getString(8));
                    detailsObj.setFreight(cursor.getString(9));
                    detailsObj.setPremium(cursor.getString(10));
                    detailsObj.setUom(cursor.getString(11));
                    detailsObj.setSchemeType(cursor.getString(12));
                    detailsObj.setWeightage(cursor.getString(13));
                    detailsObj.setRemarks(cursor.getString(14));
                    detailsObj.setInput_size(cursor.getString(15));
                    //detailsObj.setPurpose_of_visit(cursor.getString(16));
                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<SchemeSummary> getUnuploadedSchemeDetails(String orderNo) {
        String selectQuery = "";
        Cursor cursor = null;
        ArrayList<SchemeSummary> unUploadedOrdrDetailsList = new ArrayList<>();
        try {

            selectQuery = "SELECT * FROM scheme_summary WHERE order_no = '" + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SchemeSummary detailsObj = new SchemeSummary();
                    detailsObj.setOrderNo(cursor.getString(0));
                    detailsObj.setschemeId(cursor.getString(1));
                    detailsObj.setschemeProdCode(cursor.getString(2));
                    detailsObj.setfreeBiesProdCode(cursor.getString(3));
                    detailsObj.setfreeBiesProdDesc(cursor.getString(4));
                    detailsObj.setfreeBiesQty(cursor.getString(5));
                    detailsObj.setfreeBiesValPercentage(cursor.getString(6));
                    detailsObj.setfreeBiesValAmount(cursor.getString(7));
                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<QuotationDetails> getUnuploadedQuotationDetails(String quotationNo) {
        String selectQuery = "";
        Cursor cursor = null;
        ArrayList<QuotationDetails> unUploadedOrdrDetailsList = new ArrayList<>();
        try {

            selectQuery = "SELECT * FROM quotation_details WHERE quotation_no = '"
                    + quotationNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    QuotationDetails detailsObj = new QuotationDetails();
                    detailsObj.setQuotationNo(cursor.getString(0));
                    detailsObj.setQuotationQuantity(cursor.getString(1));
                    detailsObj.setQuotationProductCode(cursor.getString(2));
                    detailsObj.setQuotationProductDesc(cursor.getString(3));
                    detailsObj.setQuotationProductRate(cursor.getString(4));
                    detailsObj.setQuotationProductTax(cursor.getString(5));
                    detailsObj.setQuotationProductAmount(cursor.getString(6));
                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<ProductMasterDetails> getOrderListByOrderNumber(String orderNo) {
        ArrayList<ProductMasterDetails> selectedProductMasterList = new ArrayList<>();

        String selectQuery = "";
        Cursor cursor = null;

        try {
            selectQuery = "SELECT PM.prod_desc, PM.vat,PM.addl_vat, OD.qty,OD.sale_rate FROM product_master PM, order_details OD WHERE order_no = '"
                    + orderNo + "' AND OD.sku_code = PM.prod_code";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ProductMasterDetails detailsObj = new ProductMasterDetails();
                    detailsObj.setDesc(cursor.getString(0));
                    detailsObj.setVatRate(cursor.getString(1));
                    detailsObj.setAdditionalVatRate(cursor.getString(2));
                    detailsObj.setQty(cursor.getString(3));
                    detailsObj.setMrpValue(cursor.getString(4));
                    selectedProductMasterList
                            .add(detailsObj);

                    cursor.moveToNext();
                }
            }
            cursor.close();
            return selectedProductMasterList;
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return null;
    }

    public ArrayList<String> getSaleTypeAndInstructionFromOrderHeader(String orderNo) {
        ArrayList<String> listOfData = new ArrayList<>();

        String selectQuery = "";
        Cursor cursor = null;

        try {
            selectQuery = "SELECT OH.sale_type, OH.d_instruction  FROM order_header OH WHERE order_no = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                listOfData.add(cursor.getString(0));
                listOfData.add(cursor.getString(1));
                cursor.moveToNext();

            }
            cursor.close();

        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return listOfData;
    }

    public CustomerDetails GetCustomerDetailsByCode(String customerCode) {
        CustomerDetails detailsObj = new CustomerDetails();
        Cursor cursor = null;
        try {
            String sqlquery = "SELECT customer_name, pin, address  FROM customer_master where customer_code='" + customerCode + "' LIMIT 1 ";
            cursor = database.rawQuery(sqlquery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                detailsObj.setCustomerName(cursor.getString(0));
                detailsObj.setPin(cursor.getString(1));
                detailsObj.setAddress(cursor.getString(2));

                cursor.close();
                return detailsObj;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return null;
    }

    public ArrayList<InvoiceInformation> getInvoiceList(boolean shouldCheckFlag) {
        String selectQuery = "";
        Cursor cursor = null;
        ArrayList<InvoiceInformation> InvoiceInformationList = new ArrayList<>();
        try {

            selectQuery = "SELECT * FROM invoice_information where EXISTS(SELECT * from order_header where invoice_information.order_no = order_header.order_no ) ORDER BY chronological_no DESC";
            if (shouldCheckFlag) {
                selectQuery = "SELECT * FROM invoice_information where flag=0";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    InvoiceInformation detailsObj = new InvoiceInformation();
                    detailsObj.setInvoiceNo(cursor.getString(0));
                    detailsObj.setInvoiceDate(cursor.getString(1));
                    detailsObj.setOrderNo(cursor.getString(2));
                    detailsObj.setCustomerCode(cursor.getString(3));
                    detailsObj.setChronologicalNumber(String.valueOf(cursor.getInt(4)));
                    detailsObj.setFreightCharge(cursor.getString(5));
                    detailsObj.setFlag(cursor.getString(6));
                    InvoiceInformationList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {

        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return InvoiceInformationList;
    }

    public boolean InvoiceListTableEmpty() {
        boolean InvoiceListTableEmpty = true;
        String selectQuery = "";
        Cursor cursor = null;
        try {

            selectQuery = "SELECT count(*) FROM invoice_information";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                int numberOfColumns = cursor.getInt(0);
                if (numberOfColumns > 0) {
                    InvoiceListTableEmpty = false;
                }

            }
            cursor.close();
        } catch (Exception e) {
            InvoiceListTableEmpty = true;
        }

        return InvoiceListTableEmpty;
    }

    public int getMaxChronologicalNumberFromInvoiceTable() {
        String selectQuery = "";
        int max = -1;
        Cursor cursor = null;
        try {
            selectQuery = "SELECT max(chronological_no) FROM invoice_information";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                max = cursor.getInt(0);
            }
            cursor.close();
        } catch (Exception e) {

        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return max;
    }

    public void updateUnuploadedOrderDetails() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("order_details", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("OrderDetails Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("OrderDetails Update status ::::::::::::"
                + updateResult);
    }

    /*
     * ORDER DETAILS TABLE TRANSACTION ENDS
     */

    /*
     * TRANSACTION LOG TABLE TRANSACTION STARTS
     */
    public void insertToTransactionLogTable(String timeStamp,
                                            String transactionType, String instruction) {
        String transId = "", customerName = "", timeValue = "";
        transId = "O" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        if (transactionType.equalsIgnoreCase("BT")
                || transactionType.equalsIgnoreCase("ST")) {
            if (Constants.orderFormDetailsObj.getBranchRDSTransfer()
                    .equalsIgnoreCase("yes")) {
                customerName = Constants.selectedRDS.getRdsName();
            } else {
                customerName = Constants.selectedBranch.getBranchName();
            }
        } else if (transactionType.equalsIgnoreCase("SR")) {
            customerName = Constants.selectedBranch.getBranchName();
        } else if (transactionType.equalsIgnoreCase("CN")) {
            customerName = Constants.selectedEmp.getEmpName();
        } else if (transactionType.equalsIgnoreCase("CR")) {
            customerName = Constants.selectedCustomer.getCustomerName();
        } else if (transactionType.equalsIgnoreCase("PB")) {
            customerName = Constants.selectedVendor.getVendorName();
        } else if (transactionType.equalsIgnoreCase("SA")
                || transactionType.equalsIgnoreCase("SH")) {
            customerName = "";
        } else if (transactionType.equalsIgnoreCase("SB")
                || transactionType.equalsIgnoreCase("SO")) {
            customerName = Constants.selectedCustomer.getCustomerName();
        }
        // timeValue= new
        // SimpleDateFormat("dd-MM-yyyy").format(Calendar.getInstance().getTime());
        timeValue = Constants.dateString.substring(6, 8) + "-"
                + Constants.dateString.substring(4, 6) + "-"
                + Constants.dateString.substring(0, 4);
        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails masterObj = Constants.selectedProductMasterList
                    .get(ii);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("branch_name", "");
                cv.put("rds_name", "");
                cv.put("emp_name", Constants.employeeDetailObject.getEmpName());
                cv.put("trans_date", timeValue);
                cv.put("trans_id", transId);
                cv.put("customer_name", customerName);
                cv.put("sku_name", masterObj.getDesc());
                cv.put("qty", masterObj.getQty());
                cv.put("sale_rate", masterObj.getMrpValue());
                cv.put("amount", masterObj.getAmount());
                cv.put("VAT", masterObj.getVat());
                cv.put("TD", masterObj.getTradeDiscnt());
                cv.put("trans_type", transactionType);
                cv.put("d_instruction", instruction);

                synchronized (Lock) {
                    database.insertWithOnConflict("transaction_log", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Transaction_log:", "Data Inserted");
                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("Transaction_log", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }

    public Boolean insertToTransactionLogTable1(String timeStamp,
                                                String transactionType, String instruction) {
        Boolean isInsertionDone = true;
        try {

            String transId = "", customerName = "", timeValue = "";
            transId = "O" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
            if (transactionType.equalsIgnoreCase("BT")
                    || transactionType.equalsIgnoreCase("ST")) {
                if (Constants.orderFormDetailsObj.getBranchRDSTransfer()
                        .equalsIgnoreCase("yes")) {
                    customerName = Constants.selectedRDS.getRdsName();
                } else {
                    customerName = Constants.selectedBranch.getBranchName();
                }
            } else if (transactionType.equalsIgnoreCase("SR")) {
                customerName = Constants.selectedBranch.getBranchName();
            } else if (transactionType.equalsIgnoreCase("CN")) {
                customerName = Constants.selectedEmp.getEmpName();
            } else if (transactionType.equalsIgnoreCase("CR")) {
                customerName = Constants.selectedCustomer.getCustomerName();
            } else if (transactionType.equalsIgnoreCase("PB")) {
                customerName = Constants.selectedVendor.getVendorName();
            } else if (transactionType.equalsIgnoreCase("SA")
                    || transactionType.equalsIgnoreCase("SH")) {
                customerName = "";
            } else if (transactionType.equalsIgnoreCase("SB")
                    || transactionType.equalsIgnoreCase("SO")) {
                customerName = Constants.selectedCustomer.getCustomerName();
            }
            // timeValue= new
            // SimpleDateFormat("dd-MM-yyyy").format(Calendar.getInstance().getTime());
            timeValue = Constants.dateString.substring(6, 8) + "-"
                    + Constants.dateString.substring(4, 6) + "-"
                    + Constants.dateString.substring(0, 4);
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
//			database.beginTransaction();

                ContentValues cv = new ContentValues();
                cv.put("branch_name", "");
                cv.put("rds_name", "");
                cv.put("emp_name", Constants.employeeDetailObject.getEmpName());
                cv.put("trans_date", timeValue);
                cv.put("trans_id", transId);
                cv.put("customer_name", customerName);
                cv.put("sku_name", masterObj.getDesc());
                cv.put("qty", masterObj.getQty());
                cv.put("sale_rate", masterObj.getMrpValue());
                cv.put("amount", masterObj.getAmount());
                cv.put("VAT", masterObj.getVat());
                cv.put("TD", masterObj.getTradeDiscnt());
                cv.put("trans_type", transactionType);
                cv.put("d_instruction", instruction);

                synchronized (Lock) {
                    database.insertWithOnConflict("transaction_log", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Transaction_log:", "Data Inserted");
                }
//				database.setTransactionSuccessful();
            }
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//				database.endTransaction();
        }

        return isInsertionDone;
    }

    /*
     * TRANSACTION LOG TABLE TRANSACTION ENDS
     */

    /*
     * PAYMENT HEADER TABLE TRANSACTION STARTS
     */
    public void insertToPaymentHeaderTableFromOrder(String instruction,
                                                    String timeStamp, String trdDiscnt) {
        String receipt = "";
        int transferred = 0, flag = 0;
        double amount = 0;
        receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;


        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount()
                    .equalsIgnoreCase("yes")
                    && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj
                            .getTradeDiscnt());
                    amount = amount
                            + ((mrp * qty) - (mrp * qty * discount / 100));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble(trdDiscnt) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("customer_code",
                    Constants.selectedCustomer.getCustomerCode());
            cv.put("amount", amount);
            cv.put("cash_cheque", 0);
            cv.put("cheque_no", 0);
            cv.put("date", "");
            cv.put("bank", "");
            cv.put("rdate", "");
            cv.put("transferred", transferred);
            cv.put("flag", flag);
            cv.put("p_remark", instruction);
            cv.put("sale_type", "CASH");

            synchronized (Lock) {
                database.insertWithOnConflict("payment_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentHeader:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("PaymentHeader", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public Boolean insertToPaymentHeaderTableFromOrder1(String instruction,
                                                        String timeStamp, String trdDiscnt) {
        String receipt = "";
        int transferred = 0, flag = 0;
        double amount = 0;
        receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;


        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount()
                    .equalsIgnoreCase("yes")
                    && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj
                            .getTradeDiscnt());
                    amount = amount
                            + ((mrp * qty) - (mrp * qty * discount / 100));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble(trdDiscnt) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        totalAmountCollection = amount;
//		database.beginTransaction();
        Boolean isInsertionDone = true;
        try {
            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("amount", amount);
            cv.put("cash_cheque", 0);
            cv.put("cheque_no", 0);
            cv.put("date", "");
            cv.put("bank", "");
            cv.put("rdate", "");
            cv.put("transferred", transferred);
            cv.put("flag", flag);
            cv.put("p_remark", instruction);
            cv.put("sale_type", "CASH");

            synchronized (Lock) {
                database.insertWithOnConflict("payment_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentHeader:", "Data Inserted");
            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }

    public Boolean insertToPaymentHeaderTableFromCollection(String instruction,
                                                            String timeStamp, String cash_cheque, String chequeNo,
                                                            String bankName, String chequeDate) {
        Boolean isSuccess = true;
        String receipt = "";
        int transferred = 0, flag = 0;
        double amount = 0;
        try {
            receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
            for (int ii = 0; ii < Constants.selectedOutstandingList.size(); ii++) {
                OutstandingDetails currentObj = Constants.selectedOutstandingList
                        .get(ii);
                amount = amount + (Double.parseDouble(currentObj.getReceiptAmt()));
            }
//		database.beginTransaction();

            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("amount", amount);
            cv.put("cash_cheque", cash_cheque);
            cv.put("cheque_no", chequeNo);
            cv.put("date", chequeDate);
            cv.put("bank", bankName);
            cv.put("rdate", "");// useless
            cv.put("transferred", transferred);// useless
            cv.put("flag", flag);
            cv.put("p_remark", instruction);
            cv.put("sale_type", Constants.CurrentOrderCollectionTransactionType);

            synchronized (Lock) {
                database.insertWithOnConflict("payment_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentHeader:", "Data Inserted");
            }
//			database.setTransactionSuccessful();
        } catch (SQLException e) {
            isSuccess = false;
        } finally {
//			database.endTransaction();
        }
        return isSuccess;
    }

    public Boolean insertToPaymentHeaderForNoCollection(String instruction, String timeStamp) {
        String receipt = "";
        receipt = "NC" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int transferred = 0, flag = 0;
        Boolean isSuccess = true;
//		database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("customer_code",
                    Constants.selectedCustomer.getCustomerCode());
            cv.put("amount", "");
            cv.put("cash_cheque", "");
            cv.put("cheque_no", "");
            cv.put("date", "");
            cv.put("bank", "");
            cv.put("rdate", "");// useless
            cv.put("transferred", transferred);// useless
            cv.put("flag", flag);
            cv.put("p_remark", instruction);
            cv.put("sale_type", Constants.CurrentOrderCollectionTransactionType);

            synchronized (Lock) {
                database.insertWithOnConflict("payment_header", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentHeader:", "Data Inserted");
            }
//			database.setTransactionSuccessful();
        } catch (SQLException e) {
            isSuccess = false;
        } finally {
//			database.endTransaction();
        }
        return isSuccess;
    }

    public ArrayList<SurveyPublish> GETSurveyPublishDetails(String surveyid) {
        ArrayList<SurveyPublish> surveyPublishList = new ArrayList<SurveyPublish>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM DCA_transaction where flag='0' AND DCA_trans_id='" + surveyid + "' ORDER BY row_id ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SurveyPublish detailsObj = new SurveyPublish();
                    detailsObj.setDcaTransId(cursor.getString(0));
                    detailsObj.setSurveyId(cursor.getString(1));
                    detailsObj.setRowId(cursor.getString(2));
                    detailsObj.setActionId(cursor.getString(3));
                    detailsObj.setValue(cursor.getString(4));
                    detailsObj.setStatus(cursor.getString(5));
                    detailsObj.setType(cursor.getString(6));
                    surveyPublishList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return surveyPublishList;
    }

    public ArrayList<SurveyInput> GETUnuploadedOfferDetails(String surveyid) {
        ArrayList<SurveyInput> surveyPublishList = new ArrayList<>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM offer_transaction where offer_trans_id='" + surveyid + "' ORDER BY row_id ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SurveyInput detailsObj = new SurveyInput();
                    detailsObj.setransId(cursor.getString(0));
                    detailsObj.setSurveyRowId(cursor.getString(1));
                    detailsObj.setSurveyActionId(cursor.getString(2));
                    detailsObj.setValue(cursor.getString(3));
                    detailsObj.setStatus(cursor.getString(4));
                    detailsObj.setmallID(cursor.getString(5));
                    detailsObj.setbusinessName(cursor.getString(6));
                    detailsObj.setSurveyType(cursor.getString(7));
                    detailsObj.setsurveyId(cursor.getString(8));
                    surveyPublishList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return surveyPublishList;
    }

    public ArrayList<SurveyDetails> GETSurveyHeader(String surveyid) {
        ArrayList<SurveyDetails> SurveyDetailsList = new ArrayList<SurveyDetails>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM survey_header where survey_id='" + surveyid + "' ORDER BY survey_id ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SurveyDetails detailsObj = new SurveyDetails();
                    detailsObj.setSurveyID(cursor.getString(0));
                    detailsObj.setType(cursor.getString(1));
                    detailsObj.setMenuName(cursor.getString(2));
                    detailsObj.setMallID(cursor.getString(3));
                    detailsObj.setMallName(cursor.getString(4));
                    detailsObj.setBusinessName(cursor.getString(5));
                    SurveyDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return SurveyDetailsList;
    }

    public SurveyDetails GetSurveyHeader(String surveyid) {
        SurveyDetails obj = new SurveyDetails();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM survey_header where survey_id='" + surveyid + "' ORDER BY survey_id ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                obj.setSurveyID(cursor.getString(0));
                obj.setType(cursor.getString(1));
                obj.setMenuName(cursor.getString(2));
                obj.setMallID(cursor.getString(3));
                obj.setMallName(cursor.getString(4));
                obj.setBusinessName(cursor.getString(5));
                obj.setContactName(cursor.getString(6));
                obj.setPhoneNo(cursor.getString(7));
                obj.setQuestion(cursor.getString(8));
                obj.setRouteCode(cursor.getString(9));
                obj.setCheck_in_time(cursor.getString(10));
                cursor.moveToNext();
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return obj;
    }

    public WholeSaleInfo GETWholeSaleInfoDetails(String wholsaleid) {
        WholeSaleInfo obj = new WholeSaleInfo();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM wholesaler_details WHERE wholesale_trans_id='" + wholsaleid + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                obj.setWholesaleID(cursor.getString(0));
                obj.setCustomerCode(cursor.getString(1));
                obj.setDebitnotCollected(cursor.getString(2));
                obj.setLastDebitNoteReceived(cursor.getString(3));
                obj.setClosingStockValue(cursor.getString(4));
                obj.setLogBook(cursor.getString(5));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return obj;
    }


    public ArrayList<SurveyDetails> GETSurveyDetails(String surveyid) {
        ArrayList<SurveyDetails> SurveyDetailsList = new ArrayList<SurveyDetails>();
        Cursor cursor = null;
        String selectQuery = "";
        try {
            selectQuery = "SELECT * From survey_output where flag='0' AND survey_id='" + surveyid + "' ORDER BY row_id ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SurveyDetails detailsObj = new SurveyDetails();
                    detailsObj.setSurveyID(cursor.getString(0));
                    detailsObj.setRowId(cursor.getString(1));
                    detailsObj.setActionId(cursor.getString(2));
                    detailsObj.setValue(cursor.getString(3));
                    detailsObj.setType(cursor.getString(4));
                    SurveyDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return SurveyDetailsList;
    }

    public void INSERTtoSaudaAllocation(String timeStamp, String transactiotype, String employeecode) {

        String saudaallocationid = "";
        saudaallocationid = transactiotype
                + Constants.employeeDetailObject.getEmpCode() + timeStamp;

        String currentDate = Constants.dateString.substring(0, 4) + "-"
                + Constants.dateString.substring(4, 6) + "-"
                + Constants.dateString.substring(6, 8) + " ";
        String time = new SimpleDateFormat("HH:mm:ss").format(Calendar
                .getInstance().getTime());
        String date = currentDate + time;

        for (int count = 0; count < Constants.mSaudaAllocationList.size(); count++) {
            SaudaAllocation obj = Constants.mSaudaAllocationList
                    .get(count);
            String quantity = Constants.mSaudaAllocationList.get(count).getQty().trim();
            if (quantity.equalsIgnoreCase("0") == false) {
                database.beginTransaction();
                try {
                    ContentValues cv = new ContentValues();
                    cv.put("allocation_id", saudaallocationid);
                    cv.put("date", date);
                    cv.put("emp_code", employeecode);
                    cv.put("product_filter_code", obj.getProductFilterCode());
                    cv.put("qty_ton", obj.getQty());
                    cv.put("qty", obj.getAllotedQuantityinLtr());
                    synchronized (Lock) {
                        database.insert("sauda_allocation_log", null, cv);
                        database.setTransactionSuccessful();
                        Log.d("Market Feedback:", "Data Inserted");
                    }
                    // database.setTransactionSuccessful();
                } catch (SQLException e) {
                    Log.e("Survey Output:", e.getMessage());
                } finally {
                    database.endTransaction();
                }
            }

        }
    }

    public void INSERTtoTDAllocationLog(String timeStamp, String transactiotype) {

        String saudaallocationid = transactiotype + Constants.employeeDetailObject.getEmpCode() + timeStamp;

        String currentDate = Constants.dateString.substring(0, 4) + "-"
                + Constants.dateString.substring(4, 6) + "-"
                + Constants.dateString.substring(6, 8) + " ";
        String time = new SimpleDateFormat("HH:mm:ss").format(Calendar
                .getInstance().getTime());
        String date = currentDate + time;

        for (int ii = 0; ii < Constants.mTDAllocationList.size(); ii++) {
            TDAllocation obj = Constants.mTDAllocationList.get(ii);
            String productGroupCode = obj.getProductFilterCode();
            String empCode = obj.getEmployeCode();
            String td = obj.getTDAllocated();
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("allocation_id", saudaallocationid);
                cv.put("date", date);
                cv.put("emp_code", empCode);
                cv.put("product_filter_code", productGroupCode);
                cv.put("TD", td);
                synchronized (Lock) {
                    database.insert("TD_allocation_log", null, cv);
                    database.setTransactionSuccessful();
                }
            } catch (SQLException e) {
            } finally {
                database.endTransaction();
            }
        }
    }


    public void INSERTtoMarketFeedback(String timeStamp, String transactiotype, String routecode) {

        String feedbackid = "";
        feedbackid = transactiotype + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
            MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("market_feedback_id", feedbackid);
                cv.put("route_code", routecode);
                cv.put("product_group", masterObj.getProductGroup());
                cv.put("competitor_name", masterObj.getCopmpetitorName());
                cv.put("PTD", masterObj.getPtd());
                cv.put("PTR", masterObj.getPtr());
                cv.put("PTC", masterObj.getPtc());
                cv.put("PV", masterObj.getPv());
                cv.put("customer_code", masterObj.getCustomerCode());
                cv.put("billing_ex_for", masterObj.getmBillingExFor());
                cv.put("wsp_ex_for", masterObj.getmWspExFor());
                if(Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                    cv.put("rsp_ex_for", masterObj.getmRspExFor());
                    cv.put("nod_ex_for", masterObj.getmNodExFor());
                }else{
                    cv.put("rsp_ex_for", "");
                    cv.put("nod_ex_for", "");
                }
                synchronized (Lock) {
                    database.insert("market_feedback", null, cv);
                    database.setTransactionSuccessful();
                    Log.d("Market Feedback:", "Data Inserted");
                }
                //database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("Survey Output:", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }

    public void INSERTtoMarketFeedbackWsp(String timeStamp, String transactiotype, String routecode) {

        String feedbackid = "";
        feedbackid = transactiotype + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
            MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("market_feedback_id", feedbackid);
                cv.put("route_code", routecode);
                cv.put("product_group", masterObj.getProductGroup());
                cv.put("competitor_name", masterObj.getCopmpetitorName());
                cv.put("PTD", masterObj.getPtr());
                cv.put("PTR", masterObj.getPtd());
                cv.put("PTC", masterObj.getPtc());
                cv.put("PV", masterObj.getPv());
                cv.put("customer_code", masterObj.getCustomerCode());

                synchronized (Lock) {
                    database.insert("market_feedback", null, cv);
                    database.setTransactionSuccessful();
                    Log.d("Market Feedback:", "Data Inserted");
                }
                //database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("Survey Output:", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }
    public void INSERTtoSampling(ProductPromotionDetails productPromotionDetails) {

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("prospect_code", productPromotionDetails.getProspectCode());
            cv.put("prospect_name", productPromotionDetails.getProspectName());
            cv.put("pin", productPromotionDetails.getPinCode());
            cv.put("street_name", productPromotionDetails.getStreetName());
            cv.put("street_no", productPromotionDetails.getStreetNo());
            cv.put("building_no", productPromotionDetails.getBuildingNo());
            cv.put("apartment_no", productPromotionDetails.getApartmentNo());
            cv.put("phone_no", productPromotionDetails.getPhoneNo());
            cv.put("oil_used", productPromotionDetails.getOilUsed());
            cv.put("email", productPromotionDetails.getEmailID());
            cv.put("competitor_name", productPromotionDetails.getCompetitorName());
            synchronized (Lock) {
                database.insert("product_promotion", null, cv);
                database.setTransactionSuccessful();
                Log.d("Product Promotion:", "Data Inserted");
            }

        } catch (SQLException e) {
            Log.e("Sauda Header", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateSurveyPublishStatus(String surveyid) {
        database.beginTransaction();
        String sql = "UPDATE survey_publish SET status='DONE' WHERE  survey_id='" + surveyid + "'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpadateFsSurveyPublishStatus(String fssurveyid) {
        database.beginTransaction();
        String sql = "UPDATE fs_survey_publish SET DCE_status='DONE' WHERE  fs_survey_id='" + fssurveyid + "'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void INSERTtoSurveyDCAOutput(String timeStamp, ArrayList<SurveyPublish> surveyPublishList, String type) {

        String surveyid = "";
        surveyid = "SUA" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int flag = 0;

        for (int count = 0; count < surveyPublishList.size(); count++) {
            SurveyPublish masterObj = surveyPublishList.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("DCA_trans_id", surveyid);
                cv.put("survey_id", masterObj.getSurveyId());
                cv.put("row_id", masterObj.getRowId());
                cv.put("action_id", masterObj.getActionId());
                cv.put("value", masterObj.getValue());
                cv.put("status", masterObj.getStatus());
                cv.put("type", type);
                cv.put("flag", flag);

                synchronized (Lock) {
                    database.insert("DCA_transaction", null, cv);
                    database.setTransactionSuccessful();
                    Log.d("Survey Output:", "Data Inserted");
                }
                //database.setTransactionSuccessful();
            } catch (SQLException e) {
                Log.e("Survey Output:", e.getMessage());
            } finally {
                database.endTransaction();
            }
        }
    }

    public void INSERTtoOffers(String timeStamp, ArrayList<SurveyDetails> surveyPublishList, String mMallorHighStreetID, String currentSelectedOutlet, String mType, String mSurveyId) {

        String surveyid = "";
        surveyid = "OFR" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

        for (int count = 0; count < surveyPublishList.size(); count++) {
            SurveyDetails masterObj = surveyPublishList.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("offer_trans_id", surveyid);
                cv.put("row_id", masterObj.getRowId());
                cv.put("action_id", masterObj.getActionId());
                cv.put("value", masterObj.getValue());
//				cv.put("status", masterObj.getStatus());
                cv.put("mall_id", mMallorHighStreetID);
                cv.put("business_name", currentSelectedOutlet);
                cv.put("type", mType);
                cv.put("survey_id", mSurveyId);

                synchronized (Lock) {
                    database.insert("offer_transaction", null, cv);
                    database.setTransactionSuccessful();
                }
                //database.setTransactionSuccessful();
            } catch (SQLException e) {

            } finally {
                database.endTransaction();
            }
        }
    }

    public long updateOrderApprovalData(ArrayList<commonDatabaseHelper> dataList,String timeStamp)
    {
        String approvalId = "";
        approvalId = "TA" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        long status = 0;
        int ii = 0;
        database.beginTransaction();
        try {
            for (ii = 0; ii < dataList.size(); ii++)
            {
                commonDatabaseHelper obj = dataList.get(ii);
                String getchangedqty = obj.getchangedqty();
                if(!Utils.isNumeric(getchangedqty))
                {
                    getchangedqty="";
                }
                String sql = "";
                if(OrderApprovalActivity.status.equalsIgnoreCase("authorize"))
                {
                    sql = "UPDATE T_APPERPDO_APPROVAL SET approval_id='"+approvalId+"',approval_done_by ='"+Constants.employeeDetailObject.getEmpCode() +"' , approval_status='"+OrderApprovalActivity.status.toUpperCase()+"'  WHERE APPORDERNO ='"+obj.getItem1()+"'";
                }
                else if(OrderApprovalActivity.status.equalsIgnoreCase("cancel"))
                {
                    sql = "UPDATE T_APPERPDO_APPROVAL SET approval_id='"+approvalId+"', approval_status='"+OrderApprovalActivity.status.toUpperCase().trim().replace(" ","")+"' ,approval_done_by ='"+Constants.employeeDetailObject.getEmpCode() +"' ,remarks='"+ OrderApprovalActivity.remarks+"'  WHERE APPORDERNO ='"+obj.getItem1()+"'";
                }
                else
                {
                    if(obj.getItem3().length()<2 && obj.getItem6().length()<2)
                    {
                        obj.setchangedOrderFor("");
                        obj.setchangedSubDealerCode("");
                    }
                    else if(obj.getItem3().length()>2)
                    {
                        obj.setchangedSubDealerCode("");
                    }
                    else
                    {
                        obj.setchangedOrderFor("");
                    }
                    sql = "UPDATE T_APPERPDO_APPROVAL SET approval_id='"+approvalId+"' , QTY_CHANGED='"+ getchangedqty +"' ,approval_done_by ='"+Constants.employeeDetailObject.getEmpCode() +"' , changed_sub_dealer_code ='"+obj.getchangedSubDealerCode()+"', changed_order_for ='"+obj.getchangedOrderFor()+"', changed_consignee_address ='"+obj.getchangedConsigneeAddress()+"' , changed_dns_prod_code ='"+obj.getchangedDnsProdCode()+"' , changed_dns_destination_code ='"+obj.getchangedDnsDestinationCode()+"', changed_dump_code ='"+obj.getchangedDumpCode()+"' , approval_status='"+OrderApprovalActivity.status.toUpperCase()+"'  , remarks='"+ OrderApprovalActivity.remarks+"'  WHERE APPORDERNO ='"+obj.getItem1()+"'";
                }
                database.execSQL(sql);
                database.setTransactionSuccessful();
            }
            status = ii;
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
        return status;
    }

    public void updateTechnicalMeetApprovalData(ArrayList<commonDatabaseHelper> dataList,String dateTime)
    {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < dataList.size(); ii++)
            {
                commonDatabaseHelper obj = dataList.get(ii);

                String sql = "";

                String approvalStatus = obj.getItem5();
                if(approvalStatus.equalsIgnoreCase("approved") || approvalStatus.equalsIgnoreCase("Rejected"))
                {
                    sql = "UPDATE Tech_meet_details SET is_approved ='"+ approvalStatus +"',approved_by  ='"+Constants.employeeDetailObject.getEmpCode() +"' , approved_date_time ='"+dateTime+"', flag='0'  WHERE meet_id  ='"+obj.getItem0()+"'";

                    database.execSQL(sql);

                }

            }
            database.setTransactionSuccessful();
            database.endTransaction();
        } catch (SQLException e) {
            e.printStackTrace();
        } finally {

        }

    }
    public void updateTechnicalMeetAttendance(String meetId,String mansonAttendance,String dateTime,String photoName,String remarks)
    {
        database.beginTransaction();
        try {

                String sql = "";
                    sql = "UPDATE Tech_meet_meeting_status SET meet_status ='"+ mansonAttendance +"', status_update_by  ='"+Constants.employeeDetailObject.getEmpCode() +"' , status_update_date_time ='"+dateTime+"', flag='0', image='"+photoName+"', remarks='"+remarks+"'  WHERE meet_id  ='"+meetId+"'";

                    database.execSQL(sql);
            database.setTransactionSuccessful();
            database.endTransaction();
        } catch (SQLException e) {
            e.printStackTrace();
        } finally {

        }

    }
    public void InsertSurveyHeader(String surveyid, String type, String menuname, String mallid, String mallname, String businessname,
                                   String contactname, String phoneno, String question, String routecode) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("survey_id", surveyid);
            cv.put("survey_type", type);
            cv.put("menu_name", menuname);
            cv.put("mall_id", mallid);
            cv.put("mall_name", mallname);
            cv.put("business_name", businessname);
            cv.put("contact_name", contactname);
            cv.put("phone_no", phoneno);
            cv.put("questions_answered", question);
            cv.put("route_code", routecode);
            if (getCheckInTimeCapturedMenu()){
                cv.put("check_in_time", Constants.mCheckInOutTimeSurvey);
            }else{
                cv.put("check_in_time", "");
            }

            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("survey_header", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("survey_header:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("survey_header", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void InsertWholeSaleDetails(String wholsaleid, String customercode, String debitnotcollected, String debitnotreceived,
                                       String closingstockvalue, String logbook) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("wholesale_trans_id ", wholsaleid);
            cv.put("customer_code", customercode);
            cv.put("debit_not_collected", debitnotcollected);
            cv.put("last_debit_note_received", debitnotreceived);
            cv.put("closing_stock_value", closingstockvalue);
            cv.put("log_book", logbook);
            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("wholesaler_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("wholesaler_details:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("wholesaler_details", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void INSERTtoSurveyOutput(String timeStamp, String transactiotype) {
        String surveyid = "";

        surveyid = transactiotype + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int flag = 0;
        try
        {
        database.beginTransaction();
        ArrayList<String> columnToBeInserted=new ArrayList<>();
        for (int count = 0; count < Constants.mFinalSurveyList.size(); count++)
        {
            SurveyDetails masterObj = Constants.mFinalSurveyList.get(count);


                ContentValues cv = new ContentValues();
                cv.put("survey_id", surveyid);
                cv.put("row_id", masterObj.getRowId());
                cv.put("action_id", masterObj.getActionId());
            String currentValue = masterObj.getValue().toUpperCase();
            cv.put("value", currentValue);
                cv.put("type", Constants.mSurveyMainType);
                cv.put("flag", flag);

            String insertOrUpdateDetails = masterObj.getinsert_table_detail();//insert#site_master#site_name;site_id;emp_code or update#site_master#facilitator_mapped

            if(insertOrUpdateDetails.contains("#") && (insertOrUpdateDetails.contains("insert") || insertOrUpdateDetails.contains("update")))
            {
                String [] InsertUpdateDetailsArray=insertOrUpdateDetails.split("#");
                if(InsertUpdateDetailsArray.length==3)
                {
                    String insertOrUpdate=InsertUpdateDetailsArray[0];
                    String tableName=InsertUpdateDetailsArray[1];

                    String columnName="";
                    String columnNameAndOtherInfo=InsertUpdateDetailsArray[2];
                    if(insertOrUpdate.equalsIgnoreCase("insert"))
                    {
                        Boolean multiTableInsertion=false;
                        String tableName2="";
                        if (tableName.contains("&"))
                        {
                            String[] splittedTabelname=tableName.split("&");
                            tableName=splittedTabelname[0];
                            if(splittedTabelname.length>1)
                            {
                                tableName2=splittedTabelname[1];
                                multiTableInsertion=true;
                            }

                        }
                        String [] columnNameAndOtherInfoArray=columnNameAndOtherInfo.split(";");
                        columnName=columnNameAndOtherInfoArray[0];
                        surveyTableIdColumnName=columnNameAndOtherInfoArray[1];
                        String emp_codeColumn=columnNameAndOtherInfoArray[2];
                        String prefix = String.valueOf(tableName.charAt(0)).toUpperCase();
                        surveyTableIdValue = prefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        ContentValues cv2 = new ContentValues();
                        cv2.put(surveyTableIdColumnName,  surveyTableIdValue);
                        cv2.put(emp_codeColumn, Constants.employeeDetailObject.getEmpCode());
                        cv2.put(columnName, currentValue);
                        database.insert(tableName, null, cv2);
                        if(multiTableInsertion)
                        {
                            database.insert(tableName2, null, cv2);
                        }
                        cv.put("value", currentValue+";"+surveyTableIdValue+";"+Constants.employeeDetailObject.getEmpCode());
                    }
                    else
                    {
                        columnName=columnNameAndOtherInfo;
                        String sql="";
                        if(columnToBeInserted.contains(columnName))//concat and update
                        {
                            sql = "UPDATE "+tableName+" SET "+columnName+"='"+currentValue+"'||"+columnName+" WHERE  "+surveyTableIdColumnName+"='" + surveyTableIdValue + "'";
                        }
                        else//update only
                        {
                            columnToBeInserted.add(columnName);
                            sql = "UPDATE "+tableName+" SET "+columnName+"='"+currentValue+"' WHERE  "+surveyTableIdColumnName+"='" + surveyTableIdValue + "'";
                        }
                        database.execSQL(sql);
                    }
                }

            }
            else if(insertOrUpdateDetails.contains("independent") || insertOrUpdateDetails.contains("masterviewedit"))
            {
                if(insertOrUpdateDetails.contains("independent") )
                {
                    surveyTableIdValue=currentValue;
                }
                else if( insertOrUpdateDetails.contains("masterviewedit") && insertOrUpdateDetails.contains("#"))
                {
                    String [] InsertUpdateDetailsArray=insertOrUpdateDetails.split("#");
                    if(InsertUpdateDetailsArray.length==4)
                    {
                        String tableName=InsertUpdateDetailsArray[1];
                        if(tableName.contains("&"))
                        {
                            String[] tableNameSplitted=  tableName.split("&");
                            tableName=tableNameSplitted[0];
                        }

                        String columnNameTobeUpdated=InsertUpdateDetailsArray[2];
                        if(columnNameTobeUpdated.contains("&"))
                        {
                            String[] columnNameToSelectSplitted=  columnNameTobeUpdated.split("&");
                            columnNameTobeUpdated=columnNameToSelectSplitted[0];

                        }
                        String columnNameAndOtherInfo=InsertUpdateDetailsArray[3];
                        String [] columnNameAndOtherInfoArray=columnNameAndOtherInfo.split(";");
                        String whereColumn=columnNameAndOtherInfoArray[0];

                        String sql = "UPDATE "+tableName+" SET "+columnNameTobeUpdated+"='"+currentValue+"' WHERE  "+whereColumn+"='" + surveyTableIdValue + "'";
                        database.execSQL(sql);
                    }
                }

            }
            synchronized (Lock) {
                database.insert("survey_output", null, cv);

            }

        }
            database.setTransactionSuccessful();
        }
        catch (SQLException e)
        {
            Log.e("Survey Output:", e.getMessage());
        }
        finally
        {

            database.endTransaction();
        }
    }

    public void INSERTtoSurveyOutputWithSpecialValues(String timeStamp, String transactiotype) {
        String surveyid = "";

        surveyid = transactiotype + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        int flag = 0;
        try
        {
            ArrayList<String> columnToBeInserted=new ArrayList<>();
            for (int count = 0; count < Constants.mFinalSurveyList.size(); count++)
            {
                database.beginTransaction();
                SurveyDetails masterObj = Constants.mFinalSurveyList.get(count);
                ContentValues cv = new ContentValues();
                cv.put("survey_id", surveyid);
                cv.put("row_id", masterObj.getRowId());
                cv.put("action_id", masterObj.getActionId());
                String currentValue = masterObj.getValue().toUpperCase();
                if(currentValue.endsWith(";"))
                {
                    currentValue = currentValue.substring(0, currentValue.length() - 1);
                }
                cv.put("value", currentValue);
                cv.put("type", Constants.mSurveyMainType);
                cv.put("flag", flag);

                String insertOrUpdateDetails = masterObj.getinsert_table_detail();//insert#site_master#site_name;site_id;emp_code or update#site_master#facilitator_mapped

                if(insertOrUpdateDetails.contains("#") && (insertOrUpdateDetails.contains("insert") || insertOrUpdateDetails.contains("update")))
                {
                    String [] InsertUpdateDetailsArray=insertOrUpdateDetails.split("#");
                    if(InsertUpdateDetailsArray.length==3)
                    {
                        String insertOrUpdate=InsertUpdateDetailsArray[0];
                        String tableName=InsertUpdateDetailsArray[1];

                        String columnName="";
                        String columnNameAndOtherInfo=InsertUpdateDetailsArray[2];
                        if(insertOrUpdate.equalsIgnoreCase("insert"))
                        {
                            Boolean multiTableInsertion=false;
                            String tableName2="";
                            if (tableName.contains("&"))
                            {
                                String[] splittedTabelname=tableName.split("&");
                                tableName=splittedTabelname[0];
                                if(splittedTabelname.length>1)
                                {
                                    tableName2=splittedTabelname[1];
                                    multiTableInsertion=true;
                                }

                            }
                            String [] columnNameAndOtherInfoArray=columnNameAndOtherInfo.split(";");
                            columnName=columnNameAndOtherInfoArray[0];
                            surveyTableIdColumnName=columnNameAndOtherInfoArray[1];
                            String emp_codeColumn=columnNameAndOtherInfoArray[2];
                            String prefix = String.valueOf(tableName.charAt(0)).toUpperCase();
                            if(Constants.isUpcoming.equalsIgnoreCase("Upcoming")){
                                prefix = "U";
                            }
                            surveyTableIdValue = prefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                            ContentValues cv2 = new ContentValues();
                            cv2.put(surveyTableIdColumnName,  surveyTableIdValue);
                            cv2.put(emp_codeColumn, Constants.employeeDetailObject.getEmpCode());
                            cv2.put(columnName, currentValue);
                            long status=database.insert(tableName, null, cv2);
                            Log.d("status",status+"");
                            if(multiTableInsertion)
                            {
                                database.insert(tableName2, null, cv2);
                            }
                            cv.put("value", currentValue+";"+surveyTableIdValue+";"+Constants.employeeDetailObject.getEmpCode());
                        }
                        else
                        {
                            columnName=columnNameAndOtherInfo;
                            String sql="";
                            if(columnToBeInserted.contains(columnName))//concat and update
                            {
                                sql = "UPDATE "+tableName+" SET "+columnName+"='"+currentValue+"'||"+columnName+" WHERE  "+surveyTableIdColumnName+"='" + surveyTableIdValue + "'";
                            }
                            else//update only
                            {
                                columnToBeInserted.add(columnName);
                                sql = "UPDATE "+tableName+" SET "+columnName+"='"+currentValue+"' WHERE  "+surveyTableIdColumnName+"='" + surveyTableIdValue + "'";
                            }
                            database.execSQL(sql);
                        }
                    }

                }
                else if(insertOrUpdateDetails.contains("independent") || insertOrUpdateDetails.contains("masterviewedit"))
                {
                    if(insertOrUpdateDetails.contains("independent") )
                    {
                        surveyTableIdValue=currentValue;
                        String id=surveyTableIdValue;
                        if(id.contains(":"))
                        {
                            String [] splittedArray= surveyTableIdValue.split(":");
                            id=splittedArray[splittedArray.length-1];
                        }
                        cv.put("value", currentSiteValueForEdit+";"+id+";"+Constants.employeeDetailObject.getEmpCode());
                    }
                    else if( insertOrUpdateDetails.contains("masterviewedit") && insertOrUpdateDetails.contains("#"))
                    {
                        String [] InsertUpdateDetailsArray=insertOrUpdateDetails.split("#");
                        if(InsertUpdateDetailsArray.length==4)
                        {
                            String tableName=InsertUpdateDetailsArray[1];
                            if(tableName.contains("&"))
                            {
                                String[] tableNameSplitted=  tableName.split("&");
                                tableName=tableNameSplitted[0];
                            }

                            String columnNameTobeUpdated=InsertUpdateDetailsArray[2];
                            if(columnNameTobeUpdated.contains("&"))
                            {
                                String[] columnNameToSelectSplitted=  columnNameTobeUpdated.split("&");
                                columnNameTobeUpdated=columnNameToSelectSplitted[0];

                            }
                            String columnNameAndOtherInfo=InsertUpdateDetailsArray[3];
                            String [] columnNameAndOtherInfoArray=columnNameAndOtherInfo.split(";");
                            String whereColumn=columnNameAndOtherInfoArray[0];
                            String sql="";
                            String id=surveyTableIdValue;
                            if(id.contains(":"))
                            {
                                String [] splittedArray= surveyTableIdValue.split(":");
                                id=splittedArray[splittedArray.length-1];
                            }

                            if(columnToBeInserted.contains(columnNameTobeUpdated))//concat and update
                            {

                                sql = "UPDATE "+tableName+" SET "+columnNameTobeUpdated+"='"+currentValue+"'||"+columnNameTobeUpdated+" WHERE  "+whereColumn+"='" + id + "'";
                            }
                            else//update only
                            {
                                columnToBeInserted.add(columnNameTobeUpdated);

                                sql = "UPDATE "+tableName+" SET "+columnNameTobeUpdated+"='"+currentValue+"' WHERE  "+whereColumn+"='" + id + "'";
                            }

                            database.execSQL(sql);
                        }
                    }

                }

                synchronized (Lock)
                {
                    database.insert("survey_output", null, cv);
                }
                database.setTransactionSuccessful();
                database.endTransaction();
            }

        }
        catch (SQLException e)
        {
            Log.e("Survey Output:", e.getMessage());
        }
        finally
        {

        }
    }

    public void INSERTtoStockAuditHeader(String timeStamp, String customercode, String remarks, String imagename) {

        String stockauditId = "";
        stockauditId = "MS" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("mf_stk_audit_id", stockauditId);
            cv.put("customer_code", customercode);
            cv.put("remarks", remarks);
            cv.put("image", imagename);

            synchronized (Lock) {
                database.insert("mf_stk_audit_header", null, cv);
                database.setTransactionSuccessful();
                Log.d("mf_stk_audit_header:", "Data Inserted");
            }

        } catch (SQLException e) {
            Log.e("mf_stk_audit_header", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }


    public void INSERTtoSaudaHeader(SaudaHeader saudaheader, String timeStamp, String transactiotype) {

        String saudano = "";
        saudano = transactiotype + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        int flag = 0;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("sauda_no", saudano);
            cv.put("customer_code", saudaheader.getCustomerCode());
            cv.put("transferred", saudaheader.getTransfered());
            cv.put("flag", flag);
            cv.put("d_instruction", Constants.bargainNarration);
            cv.put("TD", saudaheader.getTD());
            cv.put("sauda_value", saudaheader.getSaudaValue());
            cv.put("broker_id", saudaheader.getBrokerId());// useless
            cv.put("transaction_type", Constants.incoTermsOfCurentCustomer);
            cv.put("VAT", saudaheader.getVat());
            cv.put("branch_code", saudaheader.getBranchCode());
            cv.put("sauda_valid_from", saudaheader.getSaudaValidity());
            cv.put("PO_no", Constants.marginPoNo);

            synchronized (Lock) {
                database.insert("sauda_header", null, cv);
                database.setTransactionSuccessful();
                Log.d("Sauda Header:", "Data Inserted");
            }

        } catch (SQLException e) {
            Log.e("Sauda Header", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void INSERTtoSwapTable(String actual_tourday, String deviate_tourday, String swap_date_actual, String swap_date_deviate, String timeStamp, String transactiotype) {

        String tour_day_swap_trans_id = "";
        tour_day_swap_trans_id = transactiotype + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        int flag = 0;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("tour_day_swap_trans_id", tour_day_swap_trans_id);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("actual_tourday", actual_tourday);
            cv.put("deviate_tourday", deviate_tourday);
            cv.put("swap_date_actual", swap_date_actual);
            cv.put("swap_date_deviate", swap_date_deviate);
            cv.put("flag", "0");

            synchronized (Lock) {
                database.insert("tour_day_swapping", null, cv);
                database.setTransactionSuccessful();
            }

        } catch (SQLException e) {
            Log.e("Sauda Header", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ProductPromotionDetails GETSampling(String transid) {
        ProductPromotionDetails mProductPromotionDetails = new ProductPromotionDetails();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * From product_promotion where prospect_code ='" + transid + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    mProductPromotionDetails.setProspectCode(cursor.getString(0));
                    mProductPromotionDetails.setProspectName(cursor.getString(1));
                    mProductPromotionDetails.setPinCode(cursor.getString(2));
                    mProductPromotionDetails.setStreetName(cursor.getString(3));
                    mProductPromotionDetails.setStreetNo(cursor.getString(4));
                    mProductPromotionDetails.setBuildingNo(cursor.getString(5));
                    mProductPromotionDetails.setApartmentNo(cursor.getString(6));
                    mProductPromotionDetails.setPhoneNo(cursor.getString(7));
                    mProductPromotionDetails.setOilUsed(cursor.getString(8));
                    mProductPromotionDetails.setEmailID(cursor.getString(9));
                    mProductPromotionDetails.setCompetitorName(cursor.getString(10));
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mProductPromotionDetails;
    }

    public SaudaHeader GETMarketFeedbackHeader(String transid) {
        SaudaHeader mSaudaHeader = new SaudaHeader();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * FROM mf_stk_audit_header WHERE mf_stk_audit_id ='" + transid + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    mSaudaHeader.setSaudaNo(cursor.getString(0));
                    mSaudaHeader.setCustomerCode(cursor.getString(1));
                    mSaudaHeader.setRemarks(cursor.getString(2));
                    mSaudaHeader.setImageName(cursor.getString(3));
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mSaudaHeader;
    }

    public SaudaHeader GETSaudaHeader(String transid) {
        SaudaHeader mSaudaHeader = new SaudaHeader();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * From sauda_header where flag='0' and sauda_no='" + transid + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
//                for (int i = 0; i < cursor.getCount(); i++) {
                    mSaudaHeader.setSaudaNo(cursor.getString(0));
                    mSaudaHeader.setCustomerCode(cursor.getString(1));
                    mSaudaHeader.setTransfered(cursor.getString(2));
                    mSaudaHeader.setFlag(cursor.getString(3));
                    mSaudaHeader.setRemarks(cursor.getString(4));
                    mSaudaHeader.setTD(cursor.getString(5));
                    mSaudaHeader.setSaudaValue(cursor.getString(6));
                    mSaudaHeader.setBrokerId(cursor.getString(7));
                    mSaudaHeader.setTransactionType(cursor.getString(8));
                    mSaudaHeader.setVAT(cursor.getString(9));
                    mSaudaHeader.setBranchCode(cursor.getString(10));
                    mSaudaHeader.setSaudaValidity(cursor.getString(11));
                    mSaudaHeader.setpoNo(cursor.getString(12));

                    ArrayList<String> getCustomerPhoneEmail = getCustomerPhoneEmail(cursor.getString(1));
                    mSaudaHeader.setCustomerPhone(getCustomerPhoneEmail.get(0));
                    mSaudaHeader.setCustomerEmail(getCustomerPhoneEmail.get(1));
//                    cursor.moveToNext();
//                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mSaudaHeader;
    }


    public ArrayList<String> getCustomerPhoneEmail(String customerCode) {
        ArrayList<String> CustomerPhoneEmail = new ArrayList<>();
        Cursor cursor = null;
        try {
            cursor = database.rawQuery("SELECT phone_no,email FROM customer_master WHERE customer_code ='" + customerCode + "'", new String[]{});
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                if (cursor.getString(0) != null) {
                    CustomerPhoneEmail.add(cursor.getString(0));
                    CustomerPhoneEmail.add(cursor.getString(1));
                } else {
                    CustomerPhoneEmail.add("");
                    CustomerPhoneEmail.add("");
                }
                cursor.close();
                return CustomerPhoneEmail;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return CustomerPhoneEmail;
    }

    public ArrayList<MarketFeedbackStockAudit> GETMarketFeedbackStockAuditDetails(String saudano) {
        ArrayList<MarketFeedbackStockAudit> MarketFeedbackStockAuditList = new ArrayList<MarketFeedbackStockAudit>();
        Cursor cursor = null;
        Cursor cursorName = null;
        String selectQuery = "";
        try {
            selectQuery = "SELECT * FROM mf_stk_audit_details WHERE mf_stk_audit_id ='" + saudano + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    MarketFeedbackStockAudit detailsObj = new MarketFeedbackStockAudit();
                    detailsObj.setStockAuditId(cursor.getString(0));
                    //detailsObj.setCompetitorName(cursor.getString(1));
                    detailsObj.setQuantity(cursor.getString(2));
                    detailsObj.setDiscount(cursor.getString(3));



                    selectQuery = "SELECT competitor_name FROM competitor_group_master WHERE display_name ='" + cursor.getString(1) + "'";
                    cursorName = database.rawQuery(selectQuery, null);
                    if (cursorName.getCount() > 0) {
                        cursorName.moveToFirst();
                        for (int j = 0; j < cursorName.getCount(); j++) {

                            if(cursorName.getString(0).isEmpty()){
                                detailsObj.setCompetitorName(cursor.getString(1));
                            }else{
                                detailsObj.setCompetitorName(cursorName.getString(0));
                            }
                            cursorName.moveToNext();
                        }
                    }else{
                        detailsObj.setCompetitorName(cursor.getString(1));
                    }
                    MarketFeedbackStockAuditList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return MarketFeedbackStockAuditList;
    }

    public ArrayList<SaudaDetails> GETSaudaDetails(String saudano) {
        ArrayList<SaudaDetails> SaudaDetailsList = new ArrayList<SaudaDetails>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * From sauda_details where flag='0' AND sauda_no='" + saudano + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SaudaDetails detailsObj = new SaudaDetails();
                    detailsObj.setSaudaNo(cursor.getString(0));
                    detailsObj.setSkuCode(cursor.getString(1));
                    detailsObj.setQuantity(cursor.getString(2));
                    detailsObj.setFlag(cursor.getString(3));
                    detailsObj.setMrpCode(cursor.getString(4));
                    detailsObj.setTD(cursor.getString(5));
                    detailsObj.setSaleRate(cursor.getString(6));
                    detailsObj.setVAT(cursor.getString(7));
                    detailsObj.setAmount(cursor.getString(8));
                    detailsObj.setFreightCharge(cursor.getString(9));
                    detailsObj.setPremium(cursor.getString(10));
                    detailsObj.setPrimaryFreight(cursor.getString(11));
                    detailsObj.setDepotCost(cursor.getString(12));
                    detailsObj.setLiquidTD(cursor.getString(13));
                    detailsObj.setBrokarageCost(cursor.getString(14));
                    detailsObj.setHoneyCombCost(cursor.getString(15));
                    detailsObj.setMarginCost(cursor.getString(16));
                    detailsObj.setadditionalTD(cursor.getString(17));
                    detailsObj.setadditionalPremium(cursor.getString(18));
                    SaudaDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return SaudaDetailsList;
    }

    public ArrayList<PlantProductWiseRARate> GetNewBidDetails(String transactionId) {
        ArrayList<PlantProductWiseRARate> SaudaDetailsList = new ArrayList<>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT * From RA_bid_rate_details where bid_id='" + transactionId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    PlantProductWiseRARate detailsObj = new PlantProductWiseRARate();
                    detailsObj.setBidId(cursor.getString(0));
                    detailsObj.setPlantName(cursor.getString(1));
                    detailsObj.setProdCode(cursor.getString(2));
                    detailsObj.setReleaseRate(cursor.getString(3));
                    detailsObj.setBaseRate(cursor.getString(4));
                    detailsObj.setIndicativeRateServer(cursor.getString(5));
                    detailsObj.setIndicativeRateApp(cursor.getString(6));
                    detailsObj.setCustomerCode(cursor.getString(7));
                    detailsObj.setQty(cursor.getString(8));
                    detailsObj.setbidPrice(cursor.getString(9));
                    detailsObj.setPrimaryFreight(cursor.getString(14));
                    detailsObj.setSecondaryFreight(cursor.getString(15));
                    detailsObj.setDepotCost(cursor.getString(16));
                    detailsObj.setgstPercent(cursor.getString(17));
                    detailsObj.setgstValue(cursor.getString(18));
                    detailsObj.setbranchCode(cursor.getString(19));
                    detailsObj.setuserChosenIncoterms(cursor.getString(20));
                    detailsObj.setverticalOfEmployee(cursor.getString(21));
                    detailsObj.setmarginCost(cursor.getString(22));
                    detailsObj.sethoneyCombCost(cursor.getString(23));
                    detailsObj.setdetentionCost(cursor.getString(24));
                    SaudaDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return SaudaDetailsList;
    }

    public ArrayList<PlantProductWiseRARate> GetCounterBidDetails(String transactionId) {
        ArrayList<PlantProductWiseRARate> SaudaDetailsList = new ArrayList<>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT bid_id,prod_code,bid_status From RA_bid_rate_details where counter_bid_id='" + transactionId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    PlantProductWiseRARate detailsObj = new PlantProductWiseRARate();
                    detailsObj.setBidId(cursor.getString(0));
                    detailsObj.setProdCode(cursor.getString(1));
                    detailsObj.setCounterBidStatus(cursor.getString(2));
                    detailsObj.setCounterBidId(transactionId);
                    SaudaDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return SaudaDetailsList;
    }

    public void INSERTtoSaudaDetails(String timeStamp) {
        String saudano = "";
        int flag = 0;
        saudano = "FT" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        for (int count = 0; count < Constants.selectedSaudaDetailsList.size(); count++) {
            SaudaDetails masterObj = Constants.selectedSaudaDetailsList
                    .get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("sauda_no", saudano);
                cv.put("sku_code", masterObj.getSkuCode());
                cv.put("qty", masterObj.getQuantity());
                cv.put("flag", flag);
                cv.put("mrp_code", masterObj.getMrpCode());
                cv.put("TD", masterObj.getTD());
                cv.put("sale_rate", masterObj.getSaleRate());
                cv.put("VAT", masterObj.getVat());
                cv.put("amount", masterObj.getAmount());
                double freightCharge = Double.parseDouble(masterObj.getFreightCharge());
                cv.put("freight_charge", defaultFormat.format(freightCharge));
                cv.put("primary_freight", masterObj.getPrimaryFreight());
                cv.put("depot_cost", masterObj.getDepotCost());
                cv.put("premium", masterObj.getPremium());
                cv.put("liquid_TD", masterObj.getLiquidTD());
                cv.put("brokerage_cost", BrokerageCost);
                cv.put("honeycomb_cost", masterObj.getHoneyCombCost());
                cv.put("margin_cost", masterObj.getMarginCost());
                synchronized (Lock) {
                    database.insertWithOnConflict("sauda_details", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);

                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {

            } finally {
                database.endTransaction();
            }
        }
    }

    public void INSERTtoSaudaDetailsForBargain(String timeStamp) {
        String saudano = "";
        int flag = 0;
        saudano = "FT" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        for (int count = 0; count < Constants.selectedSaudaDetailsList.size(); count++) {
            SaudaDetails masterObj = Constants.selectedSaudaDetailsList
                    .get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("sauda_no", saudano);
                cv.put("sku_code", masterObj.getSkuCode());
                cv.put("qty", masterObj.getQuantity());
                cv.put("flag", flag);
                cv.put("mrp_code", masterObj.getMrpCode());
                cv.put("TD", masterObj.getTD());
                cv.put("sale_rate", masterObj.getSaleRate());

                cv.put("VAT", masterObj.getVat());
                Double vatRateInDouble=0.00,vatAmount=0.00;
                Double amountBeforeVat=Double.parseDouble(masterObj.getAmount());
                if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes"))
                {
                    String vatRate = masterObj.getVat();
                    if(Utils.isNumeric(vatRate) && Double.parseDouble(vatRate)>0)
                    {
                        vatRateInDouble=Double.parseDouble(vatRate);
                    }

                    if(vatRateInDouble>0)
                    {

                        vatAmount=(amountBeforeVat*vatRateInDouble)/100;
                    }


                }
                amountBeforeVat=amountBeforeVat+vatAmount;
                cv.put("amount", amountBeforeVat+"");

                double freightCharge = Double.parseDouble(masterObj.getFreightCharge());
                cv.put("freight_charge", defaultFormat.format(freightCharge));
                cv.put("primary_freight", masterObj.getPrimaryFreight());
                cv.put("depot_cost", masterObj.getDepotCost());
                cv.put("premium", masterObj.getPremium());
                cv.put("liquid_TD", masterObj.getLiquidTD());
                cv.put("brokerage_cost", masterObj.getBrokarageCost());
                cv.put("honeycomb_cost", masterObj.getHoneyCombCost());
                cv.put("margin_cost", masterObj.getMarginCost());
                cv.put("additional_TD", masterObj.getadditionalTD());
                cv.put("additional_premium", masterObj.getadditionalPremium());
                synchronized (Lock) {
                    database.insertWithOnConflict("sauda_details", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);

                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {

            } finally {
                database.endTransaction();
            }
        }
    }

    public void INSERTtoRABidRateDetails(String timeStamp, String verticalValueOfEmployee, String userSelectedIncoterms) {
        String transactionId = "";
        transactionId = "RB" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        for (int count = 0; count < Constants.plantListForRANewBid.size(); count++) {
            PlantProductWiseRARate masterObj = Constants.plantListForRANewBid.get(count);
            database.beginTransaction();
            try {

                String bidPrice = masterObj.getbidPrice();
                String qty = masterObj.getQty();
                if (Utils.isNumeric(qty) && Utils.isNumeric(bidPrice)) {
                    ContentValues cv = new ContentValues();
                    cv.put("bid_id", transactionId);
                    cv.put("plant_name", masterObj.getPlantName());
                    cv.put("prod_code", masterObj.getProdCode());
                    String releaseRate = masterObj.getReleaseRate();
                    cv.put("release_rate", defaultFormat.format(Double.parseDouble(releaseRate)));
                    String BaseRate = masterObj.getBaseRate();
                    cv.put("base_rate", defaultFormat.format(Double.parseDouble(BaseRate)));
                    String indicativeRateServer = masterObj.getIndicativeRateServer();
                    cv.put("server_indicative_rate", defaultFormat.format(Double.parseDouble(indicativeRateServer)));
                    String indicativeRateFinal = masterObj.getIndicativeRateApp();
                    cv.put("app_indicative_rate", defaultFormat.format(Double.parseDouble(indicativeRateFinal)));
                    cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                    cv.put("qty", qty);
                    cv.put("bid_rate", defaultFormat.format(Double.parseDouble(bidPrice)));
                    cv.put("counter_bid", "");
                    cv.put("counter_bid_rate", "");
                    cv.put("bid_status", "");
                    String primaryFreight = masterObj.getPrimaryFreight();
                    if (!Utils.isNumeric(primaryFreight)) {
                        primaryFreight = "0";
                    }
                    cv.put("primary_freight", defaultFormat.format(Double.parseDouble(primaryFreight)));
                    String secondaryFreight = masterObj.getSecondaryFreight();
                    if (!Utils.isNumeric(secondaryFreight)) {
                        secondaryFreight = "0";
                    }
                    cv.put("secondary_freight", defaultFormat.format(Double.parseDouble(secondaryFreight)));
                    String depotCost = masterObj.getDepotCost();
                    if (!Utils.isNumeric(depotCost)) {
                        depotCost = "0";
                    }
                    cv.put("depot_cost", defaultFormat.format(Double.parseDouble(depotCost)));
                    String gstPercent = masterObj.getgstPercent();
                    double gstValue = 0;
                    String currentGstValue = masterObj.getgstValue();
                    if (!Utils.isNumeric(gstPercent) || !Utils.isNumeric(currentGstValue)) {
                        gstPercent = "0";
                    } else {
//						double bidPriceWithoutFreightDepotCost=Double.parseDouble(bidPrice)-((Double.parseDouble(primaryFreight)+Double.parseDouble(secondaryFreight)+Double.parseDouble(depotCost)));
                        double gstPercentageInDouble = Double.parseDouble(gstPercent);
                        gstValue = (Double.parseDouble(bidPrice) * gstPercentageInDouble) / (100 + gstPercentageInDouble);
                    }
                    cv.put("GST_percent", gstPercent);
                    cv.put("GST_value", defaultFormat.format(gstValue));
                    cv.put("branch_code", mSaudaDepoCode);
                    cv.put("incoterms", userSelectedIncoterms);
                    cv.put("vertical_value", verticalValueOfEmployee);
                    String margin_cost = masterObj.getmarginCost();
                    if (!Utils.isNumeric(margin_cost)) {
                        margin_cost = "0";
                    }
                    cv.put("margin_cost", defaultFormat.format(Double.parseDouble(margin_cost)));

                    String honeycomb_cost = masterObj.gethoneyCombCost();
                    if (!Utils.isNumeric(honeycomb_cost)) {
                        honeycomb_cost = "0";
                    }
                    cv.put("honeycomb_cost", defaultFormat.format(Double.parseDouble(honeycomb_cost)));
                    String detention_cost = masterObj.getdetentionCost();
                    if (!Utils.isNumeric(detention_cost)) {
                        detention_cost = "0";
                    }
                    cv.put("detention_cost", defaultFormat.format(Double.parseDouble(detention_cost)));

                    synchronized (Lock) {
                        database.insertWithOnConflict("RA_bid_rate_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
                }

                database.setTransactionSuccessful();
            } catch (SQLException e) {

            } finally {
                database.endTransaction();
            }
        }
    }

    public void UpdtaeRABidRateDetails(String timeStamp) {
        String transactionId = "";
        transactionId = "CB" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        for (int count = 0; count < Constants.CounterBidListListForRAOnTodayByCustomerCode.size(); count++) {
            PlantProductWiseRARate masterObj = Constants.CounterBidListListForRAOnTodayByCustomerCode.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                String counterBidStatus = masterObj.getCounterBidStatus();
                if (counterBidStatus.equalsIgnoreCase("a")) {
                    counterBidStatus = "ACCEPT";
                } else {
                    counterBidStatus = "REJECT";
                }
                cv.put("bid_status", counterBidStatus);
                cv.put("counter_bid_id", transactionId);

                synchronized (Lock) {
                    database.update("RA_bid_rate_details", cv, "bid_id = ? AND prod_code= ? ", new String[]{masterObj.getBidId(), masterObj.getProdCode()});
                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {

            } finally {
                database.endTransaction();
            }
        }
    }


    public void INSERTtoStockAuditDetails(String timeStamp) {
        String stockauditid = "";
        stockauditid = "MS" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        for (int count = 0; count < Constants.mMarketFeedbackStockAuditList.size(); count++) {
            MarketFeedbackStockAudit masterObj = Constants.mMarketFeedbackStockAuditList.get(count);
            database.beginTransaction();
            try {
                ContentValues cv = new ContentValues();
                cv.put("mf_stk_audit_id", stockauditid);
                cv.put("competitor_name", masterObj.getCompetitorName());
                cv.put("qty_mt", masterObj.getQuantity());
                cv.put("scheme_discount", masterObj.getDiscount());

                synchronized (Lock) {
                    database.insertWithOnConflict("mf_stk_audit_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                }
                database.setTransactionSuccessful();
            } catch (SQLException e) {
            } finally {
                database.endTransaction();
            }
        }
    }

    public ArrayList<PaymentHeader> getUnuploadedPaymentHeadr(String receiptId) {
        ArrayList<PaymentHeader> unUploadedPaymentHeadrList = new ArrayList<PaymentHeader>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT ph.*,cm.base_latt,cm.base_longi,cm.image FROM payment_header ph,customer_master cm where cm.customer_code=ph.customer_code and receipt_id = '"
                    + receiptId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    PaymentHeader headerObj = new PaymentHeader();
                    headerObj.setReceiptId(cursor.getString(0));
                    headerObj.setCustomerCode(cursor.getString(1));
                    headerObj.setAmount(cursor.getInt(2));
                    headerObj.setCashCheque(cursor.getInt(3));
                    headerObj.setChequeNo(cursor.getString(4));
                    headerObj.setDate(cursor.getString(5));
                    headerObj.setBank(cursor.getString(6));
                    headerObj.setrDate(cursor.getString(7));
                    headerObj.setTransferred(cursor.getInt(8));
                    headerObj.setFlag(cursor.getInt(9));
                    headerObj.setInstruction(cursor.getString(10));
                    headerObj.setSaleType(cursor.getString(11));
                    headerObj.setcustomerLat(cursor.getString(12));
                    headerObj.setcustomerLong(cursor.getString(13));
                    headerObj.setcustomerImage(cursor.getString(14));

                    unUploadedPaymentHeadrList.add(headerObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedPaymentHeadrList;
    }

    public void updateUnuploadedPaymentHeader() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("payment_header", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("PaymentHeader Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("PaymentHeader Update status ::::::::::::"
                + updateResult);
    }

    public String[] getCollectionDetails(String reportType) {
        String[] resultArray = new String[3];
        String cashAmt = "0.00", chqDt = "0.00", chqPdc = "0.00";
        String cashQuery = "", currentChequeQuery = "", pdcChequeQuery = "";
        if (reportType.equalsIgnoreCase("TODAY")) {
            cashQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,8) = '"
                    + Constants.dateString + "' AND cash_cheque = '0'";
            currentChequeQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,8) = '"
                    + Constants.dateString
                    + "' AND cash_cheque != '0' AND REPLACE(date, '-', '') <= '"
                    + Constants.dateString + "' ";
            pdcChequeQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,8) = '"
                    + Constants.dateString
                    + "' AND cash_cheque != '0' AND REPLACE(date, '-', '') > '"
                    + Constants.dateString + "' ";
        } else {
            cashQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,6) = '"
                    + Constants.dateString.substring(0, 6)
                    + "' AND cash_cheque = '0'";
            currentChequeQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,6) = '"
                    + Constants.dateString.substring(0, 6)
                    + "' AND cash_cheque != '0' AND REPLACE(date, '-', '') <= '"
                    + Constants.dateString + "' ";
            pdcChequeQuery = "SELECT GROUP_CONCAT(amount) FROM payment_header WHERE SUBSTR(receipt_id,-14,6) = '"
                    + Constants.dateString.substring(0, 6)
                    + "' AND cash_cheque != '0' AND REPLACE(date, '-', '') > '"
                    + Constants.dateString + "' ";
        }

        Cursor cursor1 = database.rawQuery(cashQuery, null);
        Cursor cursor2 = database.rawQuery(currentChequeQuery, null);
        Cursor cursor3 = database.rawQuery(pdcChequeQuery, null);
        if (cursor1.getCount() > 0) {
            cursor1.moveToFirst();
            String concatenatedAmount = cursor1.getString(0);
            concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
            cashAmt = concatenatedAmount;
            if (cashAmt == null || cashAmt.length() == 0) {
                cashAmt = "0.00";
            }
        }
        if (cursor2.getCount() > 0) {
            cursor2.moveToFirst();
            String concatenatedAmount = cursor2.getString(0);
            concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
            chqDt = concatenatedAmount;
            if (chqDt == null || chqDt.length() == 0) {
                chqDt = "0.00";
            }
        }
        if (cursor3.getCount() > 0) {
            cursor3.moveToFirst();
            String concatenatedAmount = cursor3.getString(0);
            concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
            chqPdc = concatenatedAmount;
            if (chqPdc == null || chqPdc.length() == 0) {
                chqPdc = "0.00";
            }
        }

        resultArray[0] = cashAmt;
        resultArray[1] = chqDt;
        resultArray[2] = chqPdc;

        return resultArray;
    }

    /*
     * PAYMENT HEADER TABLE TRANSACTION ENDS
     */

    /*
     * PAYMENT DETAILS TABLE TRANSACTION STARTS
     */
    public void insertToPaymentDetailsTableFromOrder(String timeStamp,
                                                     String trdDiscnt) {
        String receipt = "";
        String min_sec = new SimpleDateFormat("mmss").format(Calendar
                .getInstance().getTime());
        String s_year = new SimpleDateFormat("yy").format(Calendar
                .getInstance().getTime());
        int current_year = Integer.parseInt(s_year);
        int prevYr = current_year - 1;
        String invoice = Constants.nickName + "/CS" + min_sec + "/" + prevYr
                + "-" + current_year;
        int flag = 0;
        double amount = 0;

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount()
                    .equalsIgnoreCase("yes")
                    && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj
                            .getTradeDiscnt());
                    amount = amount
                            + ((mrp * qty) - (mrp * qty * discount / 100));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble(trdDiscnt) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }

        receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("invoice_id", invoice);
            cv.put("amount", amount);
            cv.put("discount", 0);
            cv.put("flag", flag);
            cv.put("rec_id", "");

            synchronized (Lock) {
                database.insertWithOnConflict("payment_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentDetails:", "Data Inserted");
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("PaymentDetails", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public Boolean insertToPaymentDetailsTableFromOrder1(String timeStamp,
                                                         String trdDiscnt) {
        Boolean isInsertionDone = true;
        String receipt = "";
        String min_sec = new SimpleDateFormat("mmss").format(Calendar
                .getInstance().getTime());
        String s_year = new SimpleDateFormat("yy").format(Calendar
                .getInstance().getTime());
        int current_year = Integer.parseInt(s_year);
        int prevYr = current_year - 1;
        String invoice = Constants.nickName + "/CS" + min_sec + "/" + prevYr
                + "-" + current_year;
        int flag = 0;
        double amount = 0;

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount()
                    .equalsIgnoreCase("yes")
                    && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj
                            .getTradeDiscnt());
                    amount = amount
                            + ((mrp * qty) - (mrp * qty * discount / 100));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble(trdDiscnt) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }

        receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

//		database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("receipt_id", receipt);
            cv.put("invoice_id", invoice);
            cv.put("amount", amount);
            cv.put("discount", 0);
            cv.put("flag", flag);
            cv.put("rec_id", "");

            synchronized (Lock) {
                database.insertWithOnConflict("payment_details", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("PaymentDetails:", "Data Inserted");
            }

//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }

    public Boolean insertToPaymentDetailsTableFromCollection(String timeStamp) {
        Boolean isSuccess = true;
        String receipt = "";
        receipt = "P" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

//		database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedOutstandingList.size(); ii++) {
                OutstandingDetails detailsObj = Constants.selectedOutstandingList
                        .get(ii);
                ContentValues cv = new ContentValues();
                cv.put("receipt_id", receipt);
                cv.put("invoice_id", detailsObj.getInvoice_id());
                cv.put("amount", detailsObj.getReceiptAmt());
                cv.put("discount", detailsObj.getDiscount());
                cv.put("flag", 0);
                cv.put("rec_id", "");
                synchronized (Lock) {
                    database.insertWithOnConflict("payment_details", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                }
            }
//			database.setTransactionSuccessful();
        } catch (SQLException e) {
            isSuccess = false;
        } finally {
//			database.endTransaction();
        }
        return isSuccess;
    }

    public ArrayList<PaymentDetails> getUnuploadedPaymentDetails(String receiptId) {
        Cursor cursor = null;
        ArrayList<PaymentDetails> unUploadedPaymentDetailsList = new ArrayList<PaymentDetails>();
        try {
            String selectQuery = "SELECT * FROM payment_details where receipt_id = '"
                    + receiptId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    PaymentDetails detailsObj = new PaymentDetails();
                    detailsObj.setReceiptId(cursor.getString(0));
                    detailsObj.setInvoiceId(cursor.getString(1));
                    detailsObj.setAmount(cursor.getInt(2));
                    detailsObj.setDiscount(cursor.getInt(3));
                    detailsObj.setFlag(cursor.getInt(4));
                    detailsObj.setRecId(cursor.getString(5));

                    unUploadedPaymentDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedPaymentDetailsList;
    }


    public void updateUnuploadedPaymentDetails() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("payment_details", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("PaymentDetails Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("PaymentDetails Update status ::::::::::::"
                + updateResult);
    }

    /*
     * PAYMENT DETAILS TABLE TRANSACTION ENDS
     */

    /*
     * PROSPECTIVE CUSTOMER MASTER TABLE TRANSACTION STARTS
     */

    public void insertToCustomerMaster(CustomerDetails detailObj,Boolean isNewCustomer) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", detailObj.getEmpCode());
            cv.put("customer_code", detailObj.getCustomerCode());
            cv.put("customer_name", detailObj.getCustomerName());
            cv.put("pin", detailObj.getPin());
            cv.put("route_code", detailObj.getNewRouteCode());
            cv.put("phone_no", detailObj.getNumber());
            cv.put("route_name", detailObj.getNewRouteName());
            cv.put("black_list", "N");
            cv.put("acedns", "Y");
            cv.put("current_balance", "0");
            cv.put("cust_type", detailObj.getCustomerType());
            cv.put("TD", "0");
            cv.put("credit_limit", "0");
            cv.put("rds_tag", detailObj.getRdsTag());
            cv.put("flag", detailObj.getFlag());
            cv.put("sauda_validity_period ", detailObj.getSaudaValidityPeriod());
            cv.put("check_flag", detailObj.getCheckFlag());
            cv.put("address", detailObj.getAddress());
            cv.put("landline_no", detailObj.getLandlineNo());
            cv.put("owner_name", detailObj.getOwnerName());
            cv.put("owner_phone", detailObj.getOwnerPhone());
            cv.put("cust_class", detailObj.getCustClass());
            cv.put("weekly_closing_day", detailObj.getWeeklyClosingDay());
            cv.put("coverage_type", detailObj.getCoverageType());
            cv.put("branch_code", detailObj.getBranchCode());
            cv.put("TIN", detailObj.getTIN());
            cv.put("PAN", detailObj.getPAN());
            try{
                cv.put("category_of_store", detailObj.getcategoryOfStore());
                cv.put("instore_activity", detailObj.getinStoreActivityPossible());
            }
            catch (Exception e){
                cv.put("category_of_store", "");
                cv.put("instore_activity", "");
            }

            if(isNewCustomer)
            {
                cv.put("base_latt", currentLat);
                cv.put("base_longi", currentLong);
                cv.put("image", detailObj.getcustomerImage());
            }
            if (isOrederToNewCustomer && Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                cv.put("visit_day", dayOfWeekForCustomer);
            }
            cv.put("email", detailObj.getEmail());
            cv.put("owner_image", detailObj.getownerImage());
            cv.put("firm_image", detailObj.getoutletImage());
            cv.put("firm_name", detailObj.getfirmName());
            cv.put("GST_image", detailObj.getgstImage());
            cv.put("aadhar", detailObj.getadharNo());
            cv.put("aadhar_image", detailObj.getadharImage());
            cv.put("is_new_customer", "yes");
            synchronized (Lock) {
                database.insertWithOnConflict("customer_master", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }

            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public CustomerDetails getDetailsIfNewCustomer(String customerCode) {
        CustomerDetails customerObj = null;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM customer_master where customer_code = '"
                    + customerCode + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                customerObj = new CustomerDetails();
                customerObj.setEmpCode(cursor.getString(3));
                customerObj.setCustomerCode(cursor.getString(0));
                customerObj.setCustomerName(cursor.getString(1));
                customerObj.setAddress("");
                customerObj.setPin(cursor.getString(11));
                customerObj.setNewRouteouteCode(cursor.getString(2));
                customerObj.setNumber(cursor.getString(12));
                customerObj.setRemarks("");
                customerObj.setCustomerType(cursor.getString(9));
                customerObj.setNewRouteName(cursor.getString(10));
                customerObj.setRdsTag(cursor.getString(13));
                customerObj.setFlag(cursor.getString(14));
                customerObj.setSaudaValidityPeriod("");
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return customerObj;
    }

    /*
     * PROSPECTIVE CUSTOMER MASTER TABLE TRANSACTION ENDS
     */

    /*
     * ROUTE PLAN MASTER TABLE TRANSACTION STARTS
     */

    public void updateUnuploadedRoute() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("route_plan_transaction", cv,
                        "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Route_plan_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Route_plan_transaction Update status ::::::::::::"
                + updateResult);
    }

    public void updateUnuploadedRouteMaster() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("route_master", cv, "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Route_plan_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Route_plan_transaction Update status ::::::::::::" + updateResult);
    }


    public void updateFlagInRouteTransaction(String visitDate) {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 0);
            synchronized (Lock) {
                updateResult = database.update("route_plan_transaction", cv, "visit_date=?", new String[]{visitDate});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Route_plan_transaction Flag Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Route_plan_transaction Update status ::::::::::::" + updateResult);
    }

    public ArrayList<RoutePlanCustomer> getUnuploadedRouteCustomerPlan(String transid, String routecode) {
        ArrayList<RoutePlanCustomer> unUploadedRouteCustomerList = new ArrayList<RoutePlanCustomer>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM route_customer_plan_transaction WHERE route_plan_trans_id='" + transid + "' AND route_code='" + routecode + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanCustomer obj = new RoutePlanCustomer();
                    obj.setTranSactionId(cursor.getString(0));
                    obj.setRouteCode(cursor.getString(1));
                    obj.setVisitDate(cursor.getString(2));
                    obj.setCustomerCode(cursor.getString(3));
                    obj.setStatus(cursor.getString(5));
                    unUploadedRouteCustomerList.add(obj);
                    cursor.moveToNext();
                    obj = null;
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedRouteCustomerList;
    }


    public void InsertToRoutePlanCustomerTransactionTable(ArrayList<RoutePlanCustomer> routeCustomerList) {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < routeCustomerList.size(); ii++) {
                RoutePlanCustomer obj = routeCustomerList.get(ii);
                ContentValues cv = new ContentValues();
                cv.put("route_plan_trans_id", obj.getTranSactionId());
                cv.put("route_code", obj.getRouteCode());
                cv.put("visit_date", obj.getVisitDate());
                cv.put("customer_code", obj.getCustomerCode());
                cv.put("status", obj.getStatus());
                cv.put("flag", "0");
                synchronized (Lock) {
                    database.insertWithOnConflict("route_customer_plan_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Route customer plan transaction:", "Data Inserted");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Route_plan_transaction", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }


    public void insertToRoutePlanMasterTable(ArrayList<RoutePlanMasterDetails> routeList) {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < routeList.size(); ii++) {
                RoutePlanMasterDetails detailObj = routeList.get(ii);
                ContentValues cv = new ContentValues();
                cv.put("route_plan_trans_id", detailObj.getTranId());
                cv.put("emp_code", detailObj.getEmpCode());
                cv.put("route_code", detailObj.getRoutecode());
                cv.put("visit_date", detailObj.getVisitDate());
                cv.put("route_name", detailObj.getRouteName());
                cv.put("create_date", detailObj.getCreateDate());
                cv.put("flag", detailObj.getFlag());
                cv.put("previous_route_code", detailObj.getPrevious_route_code());
                cv.put("previous_route_name", detailObj.getPrevious_route_name());
                cv.put("remarks", detailObj.getRemarks());
                cv.put("distributor_code", detailObj.getDistributorCode());
                cv.put("status", detailObj.getStatus());
                String workingWith = detailObj.getWorkingWIth();
                if(workingWith==null)
                {
                    workingWith="";
                }
                if(workingWith.equals(" "))
                {
                    workingWith="";
                }
                cv.put("working_with", workingWith);
                synchronized (Lock) {
                    database.insertWithOnConflict("route_plan_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Route_plan_transaction:", "Data Inserted");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Route_plan_transaction", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<RoutePlanMasterDetails> getUnuploadedRoute() {
        ArrayList<RoutePlanMasterDetails> unUploadedRouteList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT DISTINCT route_plan_trans_id FROM route_plan_transaction where flag = '" + 0 + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    unUploadedRouteList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedRouteList;
    }


    public ArrayList<RoutePlanMasterDetails> getUnuploadedRoute(String transid) {
        ArrayList<RoutePlanMasterDetails> unUploadedRouteList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "";
            if (transid.length() > 0) {
                selectQuery = "SELECT DISTINCT route_plan_trans_id, emp_code, route_code, visit_date, create_date, route_name, flag, previous_route_code, " +
                        "previous_route_name, remarks, distributor_code,status,working_with  FROM route_plan_transaction where route_plan_trans_id='" + transid + "' AND flag = '0'";

            } else {
                selectQuery = "SELECT DISTINCT route_plan_trans_id, emp_code, route_code, visit_date, create_date, route_name, flag, previous_route_code, " +
                        "previous_route_name, remarks, distributor_code,status,working_with FROM route_plan_transaction where flag = '0'";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    routeObj.setEmpCode(cursor.getString(1));
                    routeObj.setRoutecode(cursor.getString(2));
                    routeObj.setVisitDate(cursor.getString(3));
                    routeObj.setCreateDate(cursor.getString(4));
                    routeObj.setRouteName(cursor.getString(5));
                    routeObj.setPrevious_route_code(cursor.getString(7));
                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routeObj.setRemarks(cursor.getString(9));
                    routeObj.setDistributorCode(cursor.getString(10));
                    routeObj.setStatus(cursor.getString(11));
                    routeObj.setWorkingWIth(cursor.getString(12));
                    unUploadedRouteList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedRouteList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForToday(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND length(RPT.working_with)<3 AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    String routeName = cursor.getString(5);
                    if (routeName != null && routeName.length() > 0 && !routeName.equalsIgnoreCase(" "))
                    {
                        RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                        routeObj.setTranId(cursor.getString(0));
                        routeObj.setEmpCode(cursor.getString(1));
                        routeObj.setRoutecode(cursor.getString(2));
                        routeObj.setVisitDate(cursor.getString(3));
                        routeObj.setCreateDate(cursor.getString(4));
                        routeObj.setRouteName(routeName);
                        routeObj.setPrevious_route_code(cursor.getString(7));
                        routeObj.setPrevious_route_name(cursor.getString(8));
                        routePlanList.add(routeObj);
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayForMultipleDistributorWiseRoutePlan(String visitDate)
    {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
//            String selectQuery = "SELECT distinct RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT where RPT.route_code IN(SELECT DISTINCT CM.route_code FROM customer_master CM , route_plan_transaction RPT WHERE RPT.distributor_code=CM.customer_code AND RPT.visit_date='" + visitDate + "' AND status='active')";
            String selectQuery = "SELECT distinct RPT.route_code,RPT.route_name  FROM route_plan_transaction RPT ,customer_master CM WHERE RPT.distributor_code=CM.customer_code AND RPT.visit_date='"+visitDate+"' AND RPT.status='active'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    String routeName = cursor.getString(1);
                    if (routeName != null && routeName.length() > 0 && !routeName.equalsIgnoreCase(" "))
                    {
                        RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                        routeObj.setTranId("");
                        routeObj.setEmpCode("");
                        routeObj.setRoutecode(cursor.getString(0));
                        routeObj.setVisitDate("");
                        routeObj.setCreateDate("");
                        routeObj.setRouteName(routeName);
                        routeObj.setPrevious_route_code("");
                        routeObj.setPrevious_route_name("");
                        routePlanList.add(routeObj);
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }



    public ArrayList<RoutePlanMasterDetails> getPlanForTodayInCollection(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getsecondary_cust_type(),"#")+")";
            if(orderAuditType.equalsIgnoreCase("primary"))
            {
                custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getprimary_cust_type(),"#")+")";
            }
            String sql = "SELECT distinct route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND " + custTypeFilter ;
            if (Constants.orderFormDetailsObj.getvisit_sequence().equalsIgnoreCase("yes"))
            {
                sql = "SELECT distinct route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND "+custTypeFilter+" AND customer_code not in(select customer_code from order_header where substr(order_no,-14,8)='" + dateString + "' UNION select customer_code from stock_audit where substr(transaction_id,-14,8)='" + dateString + "') ORDER BY visit_sequence LIMIT 1";
            }
            String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.route_code IN("+sql+") AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND length(RPT.working_with)<3 AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    String routeName = cursor.getString(5);
                    if (routeName != null && routeName.length() > 0 && !routeName.equalsIgnoreCase(" "))
                    {
                        RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                        routeObj.setTranId(cursor.getString(0));
                        routeObj.setEmpCode(cursor.getString(1));
                        routeObj.setRoutecode(cursor.getString(2));
                        routeObj.setVisitDate(cursor.getString(3));
                        routeObj.setCreateDate(cursor.getString(4));
                        routeObj.setRouteName(routeName);
                        routeObj.setPrevious_route_code(cursor.getString(7));
                        routeObj.setPrevious_route_name(cursor.getString(8));
                        routePlanList.add(routeObj);
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayPrimaryOrder(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {

            String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND length(RPT.working_with)<3 AND RPT.route_code IN(Select distinct route_code from customer_master where Lower(cust_type) IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getprimary_cust_type().toLowerCase(),"#")+")) AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";


            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    String routeName = cursor.getString(5);
                    if (routeName != null && routeName.length() > 0 && !routeName.equalsIgnoreCase(" "))
                    {
                        RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                        routeObj.setTranId(cursor.getString(0));
                        routeObj.setEmpCode(cursor.getString(1));
                        routeObj.setRoutecode(cursor.getString(2));
                        routeObj.setVisitDate(cursor.getString(3));
                        routeObj.setCreateDate(cursor.getString(4));
                        routeObj.setRouteName(routeName);
                        routeObj.setPrevious_route_code(cursor.getString(7));
                        routeObj.setPrevious_route_name(cursor.getString(8));
                        routePlanList.add(routeObj);
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayInRoutePlan(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";
//            if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes")  && MenuAccess("joint_work"))
//            {
//                selectQuery=     "select RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  from route_plan_transaction RPT where RPT.route_code not in (SELECT distinct RPT.route_code FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC)  GROUP BY RPT.route_code ORDER BY RPT.route_name ASC";
//            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    String routeName = cursor.getString(5);
                    if (routeName != null && routeName.length() > 0 && !routeName.equalsIgnoreCase(" "))
                    {
                        RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                        routeObj.setTranId(cursor.getString(0));
                        routeObj.setEmpCode(cursor.getString(1));
                        routeObj.setRoutecode(cursor.getString(2));
                        routeObj.setVisitDate(cursor.getString(3));
                        routeObj.setCreateDate(cursor.getString(4));
                        routeObj.setRouteName(routeName);
                        routeObj.setPrevious_route_code(cursor.getString(7));
                        routeObj.setPrevious_route_name(cursor.getString(8));
                        routePlanList.add(routeObj);
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<EmployeeMasterDetails> getJointWorkEmpForToday(String visitDate) {
        ArrayList<EmployeeMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT EM.emp_name,RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,\n" +
                    "RPT.previous_route_name,RPT.status FROM emp_master EM JOIN route_plan_transaction RPT ON EM.emp_code=RPT.emp_code JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction WHERE visit_date LIKE '%" + visitDate + "%' GROUP BY route_code, visit_date)" +
                    " SAT ON RPT.route_code = SAT.route_code AND RPT.create_date = SAT.timestamp and RPT.emp_code!='"+Constants.employeeDetailObject.getEmpCode()+"' AND RPT.visit_date=SAT.visit_date AND RPT.status='active' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";
//            String selectQuery = "SELECT emp_code,emp_name FROM emp_master where emp_code in(select distinct emp_code from route_plan_transaction where  visit_date like '%"+visitDate+"%')";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    EmployeeMasterDetails routeObj = new EmployeeMasterDetails();
                    routeObj.setEmpName(cursor.getString(0));
                    routeObj.setTranId(cursor.getString(1));
                    routeObj.setEmpCode(cursor.getString(2));
                    routeObj.setroute_code(cursor.getString(3));
                    routeObj.setVisitDate(cursor.getString(4));
//                    routeObj.setCreateDate(cursor.getString(5));
                    routeObj.setroute_name(cursor.getString(6));
//                    routeObj.setRouteName(cursor.getString(5));
//                    routeObj.setPrevious_route_code(cursor.getString(7));
////                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayCheckIn(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        try {
            String custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getsecondary_cust_type(),"#")+")";
            if(orderAuditType.equalsIgnoreCase("primary"))
            {
                custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getprimary_cust_type(),"#")+")";
            }
            String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND RPT.route_code IN(SELECT route_code FROM customer_master where"+custTypeFilter+")  AND length(RPT.working_with)<3 AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"'  GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";
            try{
                if(Constants.menuDetailsObj.getMarketFeedback().toLowerCase().matches("yes")) {
                    //String mft = AceDnsDatabase.getMfTagging();
                    if(Constants.marketFeedbackDetailsObjNewRoute.getMf_tagging().equalsIgnoreCase("yes")){

                        selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE visit_date LIKE '%" + visitDate + "%'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' AND RPT.route_code IN(SELECT route_code FROM customer_master)  AND length(RPT.working_with)<3 AND RPT.emp_code='"+Constants.employeeDetailObject.getEmpCode()+"'  GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC";
                    }
                }
            }catch (Exception e){

            }

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    routeObj.setEmpCode(cursor.getString(1));
                    routeObj.setRoutecode(cursor.getString(2));
                    routeObj.setVisitDate(cursor.getString(3));
                    routeObj.setCreateDate(cursor.getString(4));
                    routeObj.setRouteName(cursor.getString(5));
                    routeObj.setPrevious_route_code(cursor.getString(7));
                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodaysStockAudit(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<RoutePlanMasterDetails>();
        String custTypeFromServer = Constants.userDetailsObj.getstk_audit_cust_type();
        String custTypeForPrimaryOrSecondary = "R";
        if(orderAuditType.equalsIgnoreCase("Primary"))
        {
            custTypeForPrimaryOrSecondary = "D";
        }
        if (custTypeFromServer.contains(","))
        {
            String[] splited = custTypeFromServer.split(",");
            String custTypeSplitted ="";
            if(orderAuditType.equalsIgnoreCase("Primary"))
            {
                custTypeSplitted = splited[0];
            }
            else
            {
                custTypeSplitted = splited[splited.length - 1];
            }
            if (custTypeSplitted.contains("#"))
            {
                custTypeForPrimaryOrSecondary = custTypeSplitted.split("#")[0];
            }
            else
            {
                custTypeForPrimaryOrSecondary = custTypeSplitted;
            }
        }
        else
        {
            if (custTypeFromServer.contains("#")) {
                custTypeForPrimaryOrSecondary = custTypeFromServer.split("#")[0];
            } else {
                custTypeForPrimaryOrSecondary = custTypeFromServer;
            }
        }
        Cursor cursor = null;
        try {
            String selectQuery = "select * from route_plan_transaction where visit_date LIKE '%" + visitDate + "%'  AND length(working_with)<3 AND emp_code='"+Constants.employeeDetailObject.getEmpCode()+"' and status='active' and route_code in(SELECT distinct route_code FROM customer_master where LOWER(acedns)='y'  AND lower(black_list) = 'n' AND cust_type ='" + custTypeForPrimaryOrSecondary + "' ) GROUP BY route_code ORDER BY route_name ASC";
//            if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes")  && MenuAccess("joint_work"))
//            {
//                selectQuery="select * from route_plan_transaction where route_code not in (select distinct route_code from route_plan_transaction where visit_date LIKE '%" + visitDate + "%' and route_code in(SELECT distinct route_code FROM customer_master where LOWER(acedns)='y' AND lower(black_list) = 'n' AND cust_type ='" + custTypeForPrimaryOrSecondary + "' ) GROUP BY route_code ORDER BY route_name ASC)";
//            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    routeObj.setEmpCode(cursor.getString(1));
                    routeObj.setRoutecode(cursor.getString(2));
                    routeObj.setVisitDate(cursor.getString(3));
                    routeObj.setCreateDate(cursor.getString(4));
                    routeObj.setRouteName(cursor.getString(5));
                    routeObj.setPrevious_route_code(cursor.getString(7));
                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }
    public boolean MenuAccess(String menu) {
        boolean isAccess = true;
        Cursor cursor = null;
        try {
            cursor = database.rawQuery("SELECT * FROM menu_access WHERE not_accessibility_menu = '" + menu + "'", new String[]{});
            if (cursor.getCount() > 0) {
                isAccess = false;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isAccess;
    }
    //for telephonic order, show routes that are not in route plans of today
    public ArrayList<RoutePlanMasterDetails> getPlanNotForToday(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getsecondary_cust_type(),"#")+")";
            if(orderAuditType.equalsIgnoreCase("primary"))
            {
                custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getprimary_cust_type(),"#")+")";
            }
            String selectQuery = "SELECT distinct route_code, route_name, emp_code from route_master where lower(route_name) not in('office visit','leave request') and route_code IN(select distinct route_code from customer_master where "+custTypeFilter+") and route_code NOT IN( Select route_code from route_plan_transaction WHERE  visit_date LIKE '%" + visitDate + "%')";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId("");
                    routeObj.setEmpCode(cursor.getString(2));
                    routeObj.setRoutecode(cursor.getString(0));
                    routeObj.setVisitDate("");
                    routeObj.setCreateDate("");
                    routeObj.setRouteName(cursor.getString(1));
                    routeObj.setPrevious_route_code("");
                    routeObj.setPrevious_route_name("");
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    //for telephonic order, show routes that are not in route plans of today
    public ArrayList<RoutePlanMasterDetails> getRouteListForCustomersWhoseVisitDateToday(String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        String sqlCustomerList;
        try {
            if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") || Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")) {

                sqlCustomerList = "SELECT DISTINCT route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND visit_day LIKE '" + visitDate + "'";
            } else {
                sqlCustomerList = "SELECT DISTINCT route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND visit_day LIKE '" + visitDate + "'";
            }
            String selectQuery = "SELECT distinct route_code, route_name, emp_code from route_master where route_code NOT IN(" + sqlCustomerList + ")";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId("");
                    routeObj.setEmpCode(cursor.getString(2));
                    routeObj.setRoutecode(cursor.getString(0));
                    routeObj.setVisitDate("");
                    routeObj.setCreateDate("");
                    routeObj.setRouteName(cursor.getString(1));
                    routeObj.setPrevious_route_code("");
                    routeObj.setPrevious_route_name("");
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RouteDetails> getRouteListForCustomersWhoseVisitDateNotToday2(String visitDate) {
        ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
        boolean isAttendanceGiven = getAttendanceForToday();
        String StringToRemoveLeaveRequestRoute = "";
        String sqlCustomerList;
        if (isAttendanceGiven) {
            StringToRemoveLeaveRequestRoute = " AND route_name NOT LIKE '%leave request%' ";
        }
        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") || Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")) {

            sqlCustomerList = "SELECT DISTINCT route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND visit_day NOT LIKE '" + visitDate + "'";
        } else {
            sqlCustomerList = "SELECT DISTINCT route_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND visit_day NOT LIKE '" + visitDate + "'";
        }
        Cursor cursor = null;
        try {
            String sqlQuery = "SELECT * FROM route_master WHERE route_code IN(" + sqlCustomerList + ") AND route_name IS NOT null AND route_name != ''" + StringToRemoveLeaveRequestRoute + " ORDER BY route_name ASC";
            cursor = database.rawQuery(sqlQuery, new String[]{});
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    RouteDetails detailsObj = new RouteDetails();
                    detailsObj.setRouteCode(cursor.getString(0));
                    detailsObj.setRouteName(cursor.getString(1));
                    detailList.add(detailsObj);
                    cursor.moveToNext();
                }
                cursor.close();
                return detailList;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayStockAudit(
            String visitDate) {
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM route_plan_transaction where visit_date ='" + visitDate + "' AND status <> 'inactive' ORDER BY route_name ASC";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    routeObj.setEmpCode(cursor.getString(1));
                    routeObj.setRoutecode(cursor.getString(2));
                    routeObj.setVisitDate(cursor.getString(3));
                    routeObj.setCreateDate(cursor.getString(4));
                    routeObj.setRouteName(cursor.getString(5));
                    routeObj.setPrevious_route_code(cursor.getString(7));
                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    public ArrayList<RoutePlanMasterDetails> getPlanForTodayForCollection(
            String visitDate, Boolean isTelephonicCollection) {
        String queryFilter = "";
        if (!isTelephonicCollection) {
            queryFilter = "AND route_plan_transaction.visit_date='" + visitDate + "'";

        }

        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<RoutePlanMasterDetails>();
        Cursor cursor = null;
        try {
            String custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getsecondary_cust_type(),"#")+")";
            if(orderAuditType.equalsIgnoreCase("primary"))
            {
                custTypeFilter =" cust_type IN(" +Utils.convertCommaSeparatedListToProperFormat2(Constants.userDetailsObj.getprimary_cust_type(),"#")+")";
            }
            String customQueryCustomer = "SELECT DISTINCT route_code FROM customer_master where "+custTypeFilter+" AND customer_code IN(SELECT DISTINCT customer_master.customer_code "
                    + "FROM customer_master,outstanding_master "
                    + "WHERE customer_master.acedns = 'Y' and customer_master.black_list = 'N' "
                    + "and customer_master.customer_code = outstanding_master.customer_code  union select DISTINCT customer_code from order_header WHERE transaction_type='SB')";

            String customQuery = "SELECT DISTINCT route_plan_transaction.* "
                    + "FROM route_plan_transaction,customer_master,outstanding_master "
                    + "WHERE route_plan_transaction.route_code=customer_master.route_code "
                    + "AND customer_master.customer_code=outstanding_master.customer_code AND customer_master.route_code IN("+customQueryCustomer+")"
                    + queryFilter;

            cursor = database.rawQuery(customQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    RoutePlanMasterDetails routeObj = new RoutePlanMasterDetails();
                    routeObj.setTranId(cursor.getString(0));
                    routeObj.setEmpCode(cursor.getString(1));
                    routeObj.setRoutecode(cursor.getString(2));
                    routeObj.setVisitDate(cursor.getString(3));
                    routeObj.setCreateDate(cursor.getString(4));
                    routeObj.setRouteName(cursor.getString(5));
                    routeObj.setPrevious_route_code(cursor.getString(7));
                    routeObj.setPrevious_route_name(cursor.getString(8));
                    routePlanList.add(routeObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routePlanList;
    }

    /*
     * ROUTE PLAN MASTER TABLE TRANSACTION ENDS
     */

    /*
     * PROSPECTIVE CUSTOMER DETAILS TABLE TRANSACTION STARTS
     */

    public void insertToProspectCustDetails(String prefix, ArrayList<ProductMasterDetails> prodList, String timeStamp) {
        database.beginTransaction();
        String transId = prefix + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        try {
            for (int ii = 0; ii < prodList.size(); ii++) {
                ProductMasterDetails detailObj = prodList.get(ii);
                ContentValues cv = new ContentValues();
                cv.put("trans_id", transId);
                cv.put("product_code", detailObj.getProdCode());
                cv.put("flag", "0");
                synchronized (Lock) {
                    database.insertWithOnConflict(
                            "prospective_customer_details", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Prospective_customer_details:", "Data Inserted");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Prospective_customer_details", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<ProspectCustDetails> getUnuploadedProspectCustDetails(
            String transId) {
        ArrayList<ProspectCustDetails> unUploadedRouteList = new ArrayList<ProspectCustDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM prospective_customer_details where trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ProspectCustDetails detailsObj = new ProspectCustDetails();
                    detailsObj.setTransId(cursor.getString(0));
                    detailsObj.setProdCode(cursor.getString(1));
                    unUploadedRouteList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedRouteList;
    }

    public boolean IsUnuploadedRoutePlanExist() {
        boolean isExist = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM route_plan_transaction WHERE route_plan_trans_id  LIKE 'RP%' AND flag='0'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                isExist = true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isExist;

    }

    public String getTheDaySwappedWithToday() {
        String TheDaySwappeWithToday = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT deviate_tourday FROM tour_day_swapping WHERE actual_tourday='" + Utils.dayOfWeek() + "' AND swap_date_actual='" + Utils.getDateOfSelectedDay(0) + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                TheDaySwappeWithToday = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return TheDaySwappeWithToday;

    }

    public String getRouteNameFromRouteCode(String routeCode) {
        String routeName = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT route_name FROM route_master WHERE route_code='" + routeCode + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                routeName = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return routeName;

    }

    public String getBranchNameFromBranchCode(String branchCode)
    {
        String branchName = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT branch_name FROM branch_master WHERE branch_code='" + branchCode + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                branchName = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return branchName;

    }

    public boolean isAlreadySwappedForToday() {
        boolean isSwapped = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tour_day_swapping WHERE actual_tourday='" + Utils.dayOfWeek() + "' AND swap_date_actual='" + Utils.getDateOfSelectedDay(0) + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                isSwapped = true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isSwapped;

    }

    public boolean isTodaySwappedWithAnyPreviousDay() {
        boolean isSwapped = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tour_day_swapping WHERE deviate_tourday='" + Utils.dayOfWeek() + "' AND swap_date_deviate='" + Utils.getDateOfSelectedDay(0) + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                isSwapped = true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isSwapped;

    }

    public String getThePreviousDaySwappedWithToday() {
        String TheDaySwappeWithToday = "";
        Cursor cursor = null;
        try {
//			String selectQuery = "SELECT * FROM actual_tourday WHERE deviate_tourday='"+Utils.dayOfWeek()+"' AND swap_date_deviate='"+Utils.getDateOfSelectedDay(0)+"'";
            String selectQuery = "SELECT actual_tourday FROM tour_day_swapping WHERE deviate_tourday='" + Utils.dayOfWeek() + "' AND swap_date_deviate='" + Utils.getDateOfSelectedDay(0) + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                TheDaySwappeWithToday = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return TheDaySwappeWithToday;
    }

    public boolean IsTransactioIdExist(String id) {
        boolean isidpresent = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where trans_id LIKE '%" + id + "%'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                isidpresent = true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return isidpresent;

    }

    public void updateUnuploadedProspectCustDetails() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("prospective_customer_details",
                        cv, "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Prospective_customer_details Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Prospective_customer_details Update status ::::::::::::"
                        + updateResult);
    }

    /*
     * PROSPECTIVE CUSTOMER DETAILS TABLE TRANSACTION ENDS
     */

    /*
     * PROSPECTIVE CUSTOMER HEADER TABLE TRANSACTION STARTS
     */

    public void insertToProspectCustHeader(String customer_code, String timeStamp, String name, String remarks,String check_in_time, String prefix) {
        database.beginTransaction();
        String transId = prefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("trans_id", transId);
            cv.put("customer_code", customer_code);
            cv.put("customer_name", name);
            cv.put("remarks", remarks);
            cv.put("check_in_time", check_in_time);
            cv.put("flag", "0");
            synchronized (Lock) {
                database.insertWithOnConflict("prospective_customer_header",
                        null, cv, SQLiteDatabase.CONFLICT_IGNORE);

            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public void insertToProspectCustDrHeader(String customer_code, String timeStamp, String name, String remarks,String drStrCat, String prefix) {
        database.beginTransaction();
        String transId = prefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("trans_id", transId);
            cv.put("customer_code", customer_code);
            cv.put("customer_name", name);
            cv.put("remarks", remarks);
            cv.put("category_of_store", drStrCat);
            cv.put("flag", "0");
            synchronized (Lock) {
                database.insertWithOnConflict("prospective_customer_header",
                        null, cv, SQLiteDatabase.CONFLICT_IGNORE);

            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public void insertToProspectDetails(String timeStamp,  String prefix, String referring_cust_area,String referring_cust_phone, String referring_cust_tagged_dealer,
                                        String referred_person_name, String referred_person_profession,String referred_person_phone,String referred_person_email,String referred_person_firm,String referred_person_district,String referred_person_zone,String referred_person_route,String referred_person_dealer) {
        database.beginTransaction();
        String transId = prefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("prospect_id", transId);
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("create_date", Utils.changeDateFormat("yyyyMMdd","yyyy-MM-dd", dateString));
            cv.put("referring_cust_area", referring_cust_area);
            cv.put("referring_cust_phone", referring_cust_phone);
            cv.put("referring_cust_tagged_dealer", referring_cust_tagged_dealer);
            cv.put("referred_person_name", referred_person_name);
            cv.put("referred_person_profession", referred_person_profession);
            cv.put("referred_person_phone", referred_person_phone);
            cv.put("referred_person_email", referred_person_email);
            cv.put("referred_person_firm", referred_person_firm);
            cv.put("referred_person_district", referred_person_district);
            cv.put("referred_person_zone", referred_person_zone);
            cv.put("referred_person_route",referred_person_route);
            cv.put("referred_person_dealer",referred_person_dealer);
            synchronized (Lock) {
                database.insertWithOnConflict("business_prospect_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);

            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }
    public void insertToProspectCustomerMaster(String customer_code,
                                               String customer_name, String address, String pin, String area,
                                               String phone_no, String tagged_customer_code, String category_of_store) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", Constants.employeeDetailObject.getEmpCode());
            cv.put("customer_code", customer_code);
            cv.put("customer_name", customer_name);
            cv.put("address", address);
            cv.put("pin", pin);
            cv.put("area", area);
            cv.put("phone_no", phone_no);
            cv.put("tagged_customer_code", tagged_customer_code);
            cv.put("category_of_store", category_of_store);
            synchronized (Lock) {
                database.insertWithOnConflict("prospective_customer_master", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e)
        {
            e.printStackTrace();
        } finally {
            database.endTransaction();
        }
    }

    public CustomerDetails getProspectCustomerDetailsById(String id) {
        CustomerDetails detailsObj = new CustomerDetails();
        Cursor cursor = null;
        try {

            String sql = "SELECT * FROM prospective_customer_master where customer_code='" + id + "'";
            cursor = database.rawQuery(sql, new String[]{});

            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    detailsObj.setEmpCode(cursor.getString(0));
                    detailsObj.setCustomerCode(cursor.getString(1));
                    detailsObj.setCustomerName(cursor.getString(2));
                    detailsObj.setAddress(cursor.getString(3));
                    detailsObj.setPin(cursor.getString(4));
                    detailsObj.setRouteCode(cursor.getString(5));
                    detailsObj.setNumber(cursor.getString(6));
                    detailsObj.setTaggedCustomerCode(cursor.getString(8));
                    cursor.moveToNext();
                }
                cursor.close();
                return detailsObj;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailsObj;
    }

    public ArrayList<ProspectCustHeader> getUnuploadedProspectCustHeadr(String prefix, String transId) {
        ArrayList<ProspectCustHeader> unUploadedRouteList = new ArrayList<ProspectCustHeader>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM prospective_customer_header where trans_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ProspectCustHeader detailsObj = new ProspectCustHeader();
                    detailsObj.setTransId(cursor.getString(0));
                    String currentCustomerCode = cursor.getString(1);
                    detailsObj.setCode(currentCustomerCode);
                    detailsObj.setName(cursor.getString(2));
                    detailsObj.setRemarks(cursor.getString(3));
                    detailsObj.setCategory_of_store(cursor.getString(4));
                    if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) {
                        detailsObj.setCheck_in_time(cursor.getString(5));
                    }else{
                        detailsObj.setCheck_in_time("");
                    }
                    if (prefix.matches("DC")) {
                        CustomerDetails prospectiveCustDetailsObj = getProspectCustomerDetailsById(currentCustomerCode);
                        detailsObj.setAddress(prospectiveCustDetailsObj.getAddress());
                        detailsObj.setPin(prospectiveCustDetailsObj.getPin());
                        detailsObj.setRouteCode(prospectiveCustDetailsObj.getRouteCode());
                        detailsObj.setPhone(prospectiveCustDetailsObj.getNumber());
                        detailsObj.setTagCust(prospectiveCustDetailsObj.getTaggedCustomerCode());
                    }

                    unUploadedRouteList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedRouteList;
    }

    public void updateUnuploadedProspectCustHeader() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("prospective_customer_header",
                        cv, "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Prospective_customer_header Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Prospective_customer_header Update status ::::::::::::"
                        + updateResult);
    }

    /*
     * PROSPECTIVE CUSTOMER HEADER TABLE TRANSACTION ENDS
     */

    public ArrayList<RouteDetails> getUnuploadedRouteList() {
        ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
        Cursor cursor = null;
        try {
            cursor = database.rawQuery("SELECT RM.route_code,RM.route_name,DRR.distributor_code FROM route_master RM, distributor_route_relation DRR WHERE DRR.route_code=RM.route_code AND RM.flag=0 ", null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    RouteDetails obj = new RouteDetails();
                    obj.setRouteCode(cursor.getString(0));
                    obj.setRouteName(cursor.getString(1));
                    obj.setDistributorCode(cursor.getString(2));
                    detailList.add(obj);
                    cursor.moveToNext();
                }
                cursor.close();
                return detailList;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }


    public ArrayList<CustomerDetails> getUnuploadedCustomerList() {
        ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
        Cursor cursor = null;
        try {
            cursor = database.rawQuery("SELECT CM.customer_code,CM.customer_name,CM.route_code,CM.phone_no,CM.pin,CM.rds_tag,RM.route_name AS new_route_name FROM customer_master CM left join route_master RM ON RM.route_code=CM.route_code WHERE CM.customer_code LIKE 'N%' AND CM.route_code<>' ' AND  CM.customer_code NOT IN(SELECT trans_id FROM location)", null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    CustomerDetails detailsObj = new CustomerDetails();
                    detailsObj.setCustomerCode(cursor.getString(0));
                    detailsObj.setCustomerName(cursor.getString(1));
                    detailsObj.setNewRouteouteCode(cursor.getString(2));
                    detailsObj.setNumber(cursor.getString(3));
                    detailsObj.setPin(cursor.getString(4));
                    detailsObj.setRdsTag(cursor.getString(5));
                    detailsObj.setNewRouteName(cursor.getString(6));
                    detailList.add(detailsObj);
                    cursor.moveToNext();
                }
                cursor.close();
                return detailList;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }

    public ArrayList<CustomerDetails> GetUnuploadedEditedCustomerList() {
        ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
        Cursor cursor = null;
        try {
            cursor = database.rawQuery(
                    "SELECT * FROM customer_master where check_flag='0' ", null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    CustomerDetails detailsObj = new CustomerDetails();
                    detailsObj.setCustomerCode(cursor.getString(0));
                    detailsObj.setCustomerName(cursor.getString(1));
                    detailsObj.setNewRouteouteCode(cursor.getString(2));
                    detailsObj.setEmpCode(cursor.getString(3));
                    detailsObj.setIsBlackList(cursor.getString(4));
                    detailsObj.setIsACEDNS(cursor.getString(5));
                    detailsObj.setCreditLimit(cursor.getString(6));
                    detailsObj.setCurrentBalance(cursor.getString(7));
                    detailsObj.setTradeDiscount(cursor.getString(8));
                    detailsObj.setCustomerType(cursor.getString(9));
                    detailsObj.setNewRouteName(cursor.getString(10));
                    detailsObj.setPin(cursor.getString(11));
                    detailsObj.setNumber(cursor.getString(12));
                    detailsObj.setRdsTag(cursor.getString(13));
                    detailsObj.setFlag(cursor.getString(14));
                    detailsObj.setSaudaValidityPeriod(cursor.getString(15));
                    detailsObj.setCheckFlag(cursor.getString(16));
                    detailsObj.setAddress(cursor.getString(17));
                    detailList.add(detailsObj);
                    cursor.moveToNext();
                }
                cursor.close();
                return detailList;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }


    public ArrayList<CustomerDetails> getUnuploadedCustomerList(String id) {
        ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
        Cursor cursor = null;
        try {
            String query = "SELECT * FROM customer_master where flag='0' AND customer_code='" + id + "'";
            cursor = database.rawQuery(
                    query,
                    null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    CustomerDetails detailsObj = new CustomerDetails();
                    detailsObj.setCustomerCode(cursor.getString(0));
                    detailsObj.setCustomerName(cursor.getString(1));
                    detailsObj.setNewRouteouteCode(cursor.getString(2));
                    detailsObj.setEmpCode(cursor.getString(3));
                    detailsObj.setIsBlackList(cursor.getString(4));
                    detailsObj.setIsACEDNS(cursor.getString(5));
                    detailsObj.setCreditLimit(cursor.getString(6));
                    detailsObj.setCurrentBalance(cursor.getString(7));
                    detailsObj.setTradeDiscount(cursor.getString(8));
                    detailsObj.setCustomerType(cursor.getString(9));
                    detailsObj.setNewRouteName(cursor.getString(10));
                    detailsObj.setPin(cursor.getString(11));
                    detailsObj.setNumber(cursor.getString(12));
                    detailsObj.setRdsTag(cursor.getString(13));
                    detailsObj.setFlag(cursor.getString(14));
                    detailsObj.setSaudaValidityPeriod(cursor.getString(15));
                    detailsObj.setCheckFlag(cursor.getString(16));
                    detailsObj.setAddress(cursor.getString(17));
                    detailsObj.setLandlineNo(cursor.getString(18));
                    detailsObj.setOwnerName(cursor.getString(19));
                    detailsObj.setOwnerPhone(cursor.getString(20));
                    detailsObj.setCustClass(cursor.getString(21));
                    detailsObj.setWeeklyClosingDay(cursor.getString(22));
                    detailsObj.setCoverageType(cursor.getString(23));
                    detailsObj.setTIN(cursor.getString(24));
                    detailsObj.setPAN(cursor.getString(25));
                    detailsObj.setBranchCode(cursor.getString(27));
                    detailsObj.setEmail(cursor.getString(29));
                    detailsObj.setSaudaLimit(cursor.getString(30));
                    detailsObj.setbase_latt(cursor.getString(42));
                    detailsObj.setbase_longi(cursor.getString(43));
                    detailsObj.setcustomerImage(cursor.getString(46));
                    detailsObj.setcategoryOfStore(cursor.getString(48));
                    detailsObj.setinStoreActivityPossible(cursor.getString(49));
                    detailsObj.setownerImage(cursor.getString(51));
                    detailsObj.setfirmName(cursor.getString(52));
                    detailsObj.setoutletImage(cursor.getString(53));
                    detailsObj.setgstImage(cursor.getString(54));
                    detailsObj.setadharNo(cursor.getString(55));
                    detailsObj.setadharImage(cursor.getString(56));
                    detailList.add(detailsObj);
                    cursor.moveToNext();
                }
                cursor.close();
                return detailList;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }

    public void UpdateOrderStatus() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("order_status", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Order Status Update Error", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void updateEditedCustomer() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("check_flag", 1);
            synchronized (Lock) {
                updateResult = database.update("customer_master", cv, "check_flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("card_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("customer_master Update status ::::::::::::"
                + updateResult);
    }

    public void updateUnuploadedCustomer() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("customer_master", cv, "flag=?",
                        new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("card_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("customer_master Update status ::::::::::::"
                + updateResult);
    }

    /*
     * NOTES_INFO TABLE TRANSACTION STARTS
     */

    public void insertToNotesInfo(String timeStamp, String customerCode,
                                  String prodCode, String feedback) {
        database.beginTransaction();
        String transId = "CF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("trans_id", transId);
            cv.put("customer_code", customerCode);
            cv.put("prod_code", prodCode);
            cv.put("feedback", feedback);
            cv.put("flag", "0");
            synchronized (Lock) {
                database.insertWithOnConflict("notes_info", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Notes_info:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Notes_info", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<WholeSaleInfo> GETWholeSaleInfo(String condition) {
        ArrayList<WholeSaleInfo> wholeSaleInfoList = new ArrayList<WholeSaleInfo>();
        Cursor cursor = null;
        try {
            String query = "SELECT CM.customer_name,WD.debit_not_collected,WD.last_debit_note_received,WD.closing_stock_value,WD.log_book FROM customer_master CM,wholesaler_details WD WHERE CM.customer_code=WD.customer_code AND " + condition + "";
            cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    WholeSaleInfo detailsObj = new WholeSaleInfo();
                    detailsObj.setCustomerCode(cursor.getString(0));
                    detailsObj.setDebitnotCollected(cursor.getString(1));
                    detailsObj.setLastDebitNoteReceived(cursor.getString(2));
                    detailsObj.setClosingStockValue(cursor.getString(3));
                    detailsObj.setLogBook(cursor.getString(4));
                    wholeSaleInfoList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return wholeSaleInfoList;
    }

    /*
     * PROSPECTIVE CUSTOMER HEADER TABLE TRANSACTION ENDS
     */

    /*
     * GOODS IN TRANSIT TRANSACTION STARTS
     */
    public void insertToGITMasterFromTransaction(String grnNo,
                                                 String despatcherCode, String timeStamp, String transType) {
        database.beginTransaction();
        try {
            String order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
                ContentValues cv = new ContentValues();
                cv.put("grn_no", grnNo);
                cv.put("despatcher_code", despatcherCode);
                cv.put("prod_code", masterObj.getProdCode());
                cv.put("despatch_qty", masterObj.getDespatchQtyForStockIn());
                cv.put("bal_rec_qty ", masterObj.getQty());
                cv.put("status", masterObj.getStatusForStockIn());
                cv.put("order_no", order_no);
                cv.put("trans_type", transType);
                cv.put("sale_rate", masterObj.getMrpValue());
                synchronized (Lock) {
                    database.insertWithOnConflict("goods_in_transit", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Goods_in_transit:", "Data Inserted");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Goods_in_transit", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void updateStatusInGIT(String grnNo) {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
                if (masterObj.getStatusForStockIn().equalsIgnoreCase("1")) {
                    ContentValues cv = new ContentValues();
                    cv.put("status", 1);
                    synchronized (Lock) {
                        database.update("goods_in_transit", cv,
                                "prod_code = ? AND grn_no = ?", new String[]{
                                        masterObj.getProdCode(), grnNo});
                    }
                }
            }
        } catch (SQLException e) {
            Log.e("goods_in_transit", e.getMessage());
        }
        database.setTransactionSuccessful();
        database.endTransaction();
    }

    /*
     * GOODS IN TRANSIT TRANSACTION ENDS
     */

    /*****************************************************************************************/
    /*
     * REPORT related Transaction STARTS
     */
    public ArrayList<ReportData> getReportList(String initial, String criteria,
                                               String customerCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (initial.equalsIgnoreCase("O")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT CM.customer_name,OH.order_no,OH.order_value,OH.flag FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,8) LIKE '%"
                            + criteria
                            + "%' AND CM.customer_code='"
                            + customerCode + "'";
                } else {
                    selectQuery = "SELECT DISTINCT CM.customer_name,OH.order_no,OH.order_value,OH.flag FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,6) LIKE '%"
                            + criteria
                            + "%' AND CM.customer_code='"
                            + customerCode + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setFlag(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else if (initial.equalsIgnoreCase("P")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT CM.customer_name,PM.receipt_id,PM.amount,PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,8) LIKE '%"
                            + criteria
                            + "%' AND CM.customer_code='"
                            + customerCode + "'";
                } else {
                    selectQuery = "SELECT DISTINCT CM.customer_name,PM.receipt_id,PM.amount,PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,6) LIKE '%"
                            + criteria
                            + "%' AND CM.customer_code='"
                            + customerCode + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setPayMode(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%'";
                } else {
                    selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            }

        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomReportList(String initial,
                                                     String startDate, String endDate, String customerCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (initial.equalsIgnoreCase("O")) {
                selectQuery = "SELECT DISTINCT CM.customer_name,OH.order_no,OH.order_value,OH.flag FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' AND CM.customer_code='" + customerCode + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setFlag(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else if (initial.equalsIgnoreCase("P")) {
                selectQuery = "SELECT DISTINCT CM.customer_name,PM.receipt_id,PM.amount,PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' AND CM.customer_code='" + customerCode + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setPayMode(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate + "' AND '" + endDate + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            }

        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getReportListGrpWise(String initial,
                                                      String criteria) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (initial.equalsIgnoreCase("O")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(OH.order_value),OH.flag FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,8) LIKE '%"
                            + criteria + "%' GROUP BY CM.customer_code";
                } else {
                    selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(OH.order_value),OH.flag FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,6) LIKE '%"
                            + criteria + "%' GROUP BY CM.customer_code";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        String concatenatedAmount = cursor.getString(2);
                        concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
                        detailsObj.setAmount(concatenatedAmount);
                        detailsObj.setFlag(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else if (initial.equalsIgnoreCase("P")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(PM.amount),PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,8) LIKE '%"
                            + criteria + "%' GROUP BY CM.customer_code";
                } else {
                    selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(PM.amount),PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,6) LIKE '%"
                            + criteria + "%' GROUP BY CM.customer_code";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        String concatenatedAmount = cursor.getString(2);
                        concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
                        detailsObj.setAmount(concatenatedAmount);
                        detailsObj.setPayMode(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%'";
                    if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")){
                        selectQuery = "SELECT customer_name,trans_id,check_in_time FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) LIKE '%"
                                + criteria + "%'";
                    }
                } else {
                    selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%'";
                    if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")){
                        selectQuery = "SELECT customer_name,trans_id,check_in_time FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,6) LIKE '%"
                                + criteria + "%'";
                    }
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")){
                            detailsObj.setCheckIn(cursor.getString(2));
                        }else{
                            detailsObj.setCheckIn("");
                        }
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            }

        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomReportListGrpWise(String initial,
                                                            String startDate, String endDate) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (initial.equalsIgnoreCase("O")) {
                selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(OH.order_value) FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code  AND SUBSTR(OH.order_no,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY CM.customer_code";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        String concatenatedAmount = cursor.getString(2);
                        concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
                        detailsObj.setAmount(concatenatedAmount);
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else if (initial.equalsIgnoreCase("P")) {
                selectQuery = "SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(PM.amount),PM.cash_cheque FROM payment_header PM,customer_master CM WHERE PM.customer_code=CM.customer_code  AND SUBSTR(PM.receipt_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY CM.customer_code";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        String concatenatedAmount = cursor.getString(2);
                        concatenatedAmount = Utils.addAllItemsOfAnArray(concatenatedAmount);
                        detailsObj.setAmount(concatenatedAmount);
                        detailsObj.setPayMode(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                selectQuery = "SELECT customer_name,trans_id FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate + "' AND '" + endDate + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            }

        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportDetails> getReportDetailsList(String initial,
                                                         String searchKey) {
        ArrayList<ReportDetails> reportList = new ArrayList<ReportDetails>();
        String selectQuery = "";
        try {
            if (initial.equalsIgnoreCase("O")) {
                if (Constants.orderFormDetailsObj.getSaleRate()
                        .equalsIgnoreCase("yes")
                        && Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                        .equalsIgnoreCase("input")) {
                    selectQuery = "SELECT PM.prod_desc,OD.qty,OD.sale_rate,OD.TD,OD.flag"
                            + " FROM product_master PM,order_details OD"
                            + " WHERE OD.order_no = '"
                            + searchKey
                            + "' AND OD.sku_code = PM.prod_code;";
                } else if (Constants.orderFormDetailsObj.getSaleRate()
                        .equalsIgnoreCase("yes")
                        && Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                        .equalsIgnoreCase("dropdown")) {
                    selectQuery = "SELECT PM.prod_desc,OD.qty,MRP.sale_rate,OD.TD,OD.flag"
                            + " FROM product_master PM,order_details OD,mrp MRP"
                            + " WHERE OD.order_no = '"
                            + searchKey
                            + "' AND OD.sku_code = PM.prod_code AND OD.mrp_code = MRP.mrp_code;";
                } else if (Constants.orderFormDetailsObj.getSaleRate()
                        .equalsIgnoreCase("no")
                        && Constants.orderFormDetailsObj.getMrp()
                        .equalsIgnoreCase("no")) {
                    selectQuery = "SELECT PM.prod_desc,OD.qty,4-4,OD.TD,OD.flag"
                            + " FROM product_master PM,order_details OD"
                            + " WHERE OD.order_no = '"
                            + searchKey
                            + "' AND OD.sku_code = PM.prod_code;";
                } else {
                    selectQuery = "SELECT PM.prod_desc,OD.qty,MRP.mrp_value,OD.TD,OD.flag"
                            + " FROM product_master PM,order_details OD,mrp MRP"
                            + " WHERE OD.order_no = '"
                            + searchKey
                            + "' AND OD.sku_code = PM.prod_code AND OD.mrp_code = MRP.mrp_code;";
                }
                Log.d("TAG", "_DOWNLOAD_ product_master: " + selectQuery);
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportDetails detailsObj = new ReportDetails();
                        detailsObj.setProdCode(cursor.getString(0));
                        detailsObj.setQty(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setTD(cursor.getString(3));
                        detailsObj.setTransmitted(cursor.getString(4));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else if (initial.equalsIgnoreCase("P")) {
                selectQuery = "SELECT PM.invoice_id,OS.invoice_amount,PM.amount,PM.flag"
                        + " FROM payment_details PM,outstanding_master OS"
                        + " WHERE PM.receipt_id = '"
                        + searchKey
                        + "' AND PM.invoice_id = OS.invoice_id";

                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportDetails detailsObj = new ReportDetails();
                        detailsObj.setInvoiceNo(cursor.getString(0));
                        detailsObj.setInvoiceAmt(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setTransmitted(cursor.getString(3));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                selectQuery = "SELECT PM.prod_desc,PCD.flag"
                        + " FROM product_master PM,prospective_customer_details PCD"
                        + " WHERE PCD.product_code = PM.prod_code AND PCD.trans_id='"
                        + searchKey + "';";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportDetails detailsObj = new ReportDetails();
                        detailsObj.setProdCode(cursor.getString(0));
                        detailsObj.setTransmitted(cursor.getString(1));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            }
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public String getTransmitted(String transId) {
        String status = "0";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT flag FROM payment_details WHERE receipt_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                status = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return status;
    }

    /*
     * Card Transaction Table TRANSACTION STARTS
     */

    public long insertToCardTransaction(String timeStamp, String cardNo,
                                        String outletCode, String purchaseValue, String vertical,
                                        String vehicleNo, String vehicleType, String pointsEarned,
                                        String pointsRedeemed) {
        long status = 0;
        int flag = 0;
        database.beginTransaction();
        String transId = "L" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("transaction_id", transId);
            cv.put("loyalty_card_no", cardNo);
            cv.put("rds_code", outletCode);
            cv.put("purchase_value", purchaseValue);
            cv.put("trans_type", vertical);
            cv.put("vehicle_no", vehicleNo);
            cv.put("vehicle_type", vehicleType);
            cv.put("points_earned", pointsEarned);
            cv.put("points_redeemed", pointsRedeemed);
            cv.put("flag", flag);
            synchronized (Lock) {
                status = database.insertWithOnConflict("card_transaction",
                        null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Card_transaction", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Card_transaction", e.getMessage());
        } finally {
            database.endTransaction();
        }
        return status;
    }

    public long insertToCashDeposit(String timeStamp, String amount, String bankName) {
        long status = 0;
        database.beginTransaction();
        String transId = "CD" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("cash_deposit_trans_id", transId);
            cv.put("bank_name", bankName);
            cv.put("deposit_value", amount);
            synchronized (Lock) {
                status = database.insertWithOnConflict("cash_deposit_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
        return status;
    }

    public void insertToCashTransfer(String timeStamp, String amountDespatched, String receiverEmpCode, String amountReceived, String status, String despatcherCode) {

        database.beginTransaction();
        String transId = cashTransferOrReceive + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        try {
            ContentValues cv = new ContentValues();
            cv.put("cash_transaction_id", transId);
            cv.put("despatcher_code", despatcherCode);
            cv.put("receiver_code", receiverEmpCode);
            cv.put("despatch_value", amountDespatched);
            cv.put("rec_value", amountReceived);
            cv.put("status", status);
            cv.put("transaction_type", cashTransferOrReceive);
            cv.put("cash_transfer_id", cashTransferIdForReceive);
            synchronized (Lock) {
                database.insertWithOnConflict("cash_transaction_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }

    }

    public ArrayList<CardTransactionDetails> getUnuploadedCardTrans(
            String transId) {
        ArrayList<CardTransactionDetails> unUploadedTransList = new ArrayList<CardTransactionDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM card_transaction where transaction_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CardTransactionDetails locationObj = new CardTransactionDetails();
                    locationObj.setTransactionId(cursor.getString(0));
                    locationObj.setCardNumber(cursor.getString(1));
                    locationObj.setOutletCode(cursor.getString(2));
                    locationObj.setPurchaseValue(cursor.getString(3));
                    locationObj.setVerticalName(cursor.getString(4));
                    locationObj.setVehicleNo(cursor.getString(5));
                    locationObj.setVehicleType(cursor.getString(6));
                    locationObj.setPointsEarned(cursor.getString(7));
                    locationObj.setPointsRedeemed(cursor.getString(8));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public ArrayList<CardTransactionDetails> getLoyaltyTransList(
            String cardNumber, String reportDuration, String startDate,
            String endDate, String mode) {
        ArrayList<CardTransactionDetails> unUploadedTransList = new ArrayList<CardTransactionDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = null;
            if (mode.equalsIgnoreCase("purchase")) {
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString + "%'";
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString.substring(0, 6) + "%'";
                } else if (reportDuration.equalsIgnoreCase("CUSTOM")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) BETWEEN'"
                            + startDate + "' AND '" + endDate + "'";
                }
            } else if (mode.equalsIgnoreCase("reward")) {
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString + "%' AND points_earned > 0";
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString.substring(0, 6)
                            + "%' AND points_earned > 0";
                } else if (reportDuration.equalsIgnoreCase("CUSTOM")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' AND points_earned > 0";
                }

            } else if (mode.equalsIgnoreCase("redeemed")) {
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString
                            + "%' AND points_redeemed > 0";
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) LIKE '%"
                            + Constants.dateString.substring(0, 6)
                            + "%' AND points_redeemed > 0";
                } else if (reportDuration.equalsIgnoreCase("CUSTOM")) {
                    selectQuery = "SELECT * FROM card_transaction where loyalty_card_no = '"
                            + cardNumber
                            + "' AND  SUBSTR(transaction_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' AND points_redeemed > 0";
                }

            }

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CardTransactionDetails locationObj = new CardTransactionDetails();
                    locationObj.setTransactionId(cursor.getString(0));
                    locationObj.setCardNumber(cursor.getString(1));
                    locationObj.setOutletCode(cursor.getString(2));
                    locationObj.setPurchaseValue(cursor.getString(3));
                    locationObj.setVerticalName(cursor.getString(4));
                    locationObj.setVehicleNo(cursor.getString(5));
                    locationObj.setVehicleType(cursor.getString(6));
                    locationObj.setPointsEarned(cursor.getString(7));
                    locationObj.setPointsRedeemed(cursor.getString(8));
                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }

    public void updateUnuploadedCardTransaction() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("card_transaction", cv,
                        "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("card_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("card_transaction Update status ::::::::::::"
                + updateResult);
    }

    /*
     * Card Transaction Table TRANSACTION ENDS
     */

    public void insertLoyaltyPurchase(String card, String vertical,
                                      String purchase, String reward, String redmd) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("loyalty_card_no", card);
            cv.put("vertical_name", vertical);
            cv.put("purchase_value", purchase);
            cv.put("accumulated_points", reward);
            cv.put("redeemed_points", redmd);
            synchronized (Lock) {
                database.insertWithOnConflict("loyalty_purchase_details", null,
                        cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("LoyaltyPurchaseDetails:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("LoyaltyPurchaseDetails", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void updateLoyaltyPurchase(String card, String vertical,
                                      String purchase, String reward, String redmd) {
        // Update Accu Points & Redeemed Points
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("accumulated_points", reward);
            cv.put("redeemed_points", redmd);
            synchronized (Lock) {
                database.update("loyalty_purchase_details", cv,
                        "loyalty_card_no=?", new String[]{card});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("card_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        // Update Purchase Value
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("purchase_value", purchase);
            synchronized (Lock) {
                updateResult = database.update("loyalty_purchase_details", cv,
                        "loyalty_card_no=? AND vertical_name=?", new String[]{
                                card, vertical});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("card_transaction Update", e.getMessage());
        } finally {
            database.endTransaction();
        }

        System.out.println("card_transaction Update status ::::::::::::"
                + updateResult);
    }

    public LoyaltyCustomerDetails getLoyaltyCustomer(String transId) {
        LoyaltyCustomerDetails detailObj = new LoyaltyCustomerDetails();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM loyalty_card_holder_master where loyalty_card_no = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                detailObj.setCardHolderCode(cursor.getString(0));
                detailObj.setCardHolderName(cursor.getString(1));
                detailObj.setCardNumber(cursor.getString(2));
                detailObj.setCardType(cursor.getString(3));
                detailObj.setPurchaseValue(cursor.getString(4));
                detailObj.setRewardPoint(cursor.getString(5));
                detailObj.setLastUpdate(cursor.getString(6));
                detailObj.setRedeemed(cursor.getString(7));
                detailObj.setPhone(cursor.getString(8));
                detailObj.setAddress(cursor.getString(9));
                detailObj.setVehicleNo(cursor.getString(10));
                detailObj.setCardExpDate(cursor.getString(11));
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailObj;
    }

    public long insertToCardHolder(LoyaltyCustomerDetails detailObj) {
        long status = 0;
        int ii = 0;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("loyalty_card_holder_code", detailObj.getCardHolderCode());
            cv.put("loyalty_card_holder_name", detailObj.getCardHolderName());
            cv.put("loyalty_card_no", detailObj.getCardNumber());
            cv.put("card_type", detailObj.getCardType());
            cv.put("total_purchase_value", detailObj.getPurchaseValue());
            cv.put("total_reward_point", detailObj.getRewardPoint());
            cv.put("last_update_on", detailObj.getLastUpdate());
            cv.put("redeemed", detailObj.getRedeemed());
            cv.put("phone_no", detailObj.getPhone());
            cv.put("address", detailObj.getAddress());
            cv.put("vehicle_no", detailObj.getVehicleNo());
            cv.put("expiry_date", detailObj.getCardExpDate());
            synchronized (Lock) {
                database.insertWithOnConflict("loyalty_card_holder_master",
                        null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d(" Loyalty_card_holder_master:", "Data Inserted");
            }
            status = ii;
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Loyalty_card_holder_master", e.getMessage());
        } finally {
            database.endTransaction();
        }
        return status;
    }

    /*
     * LoyaltyLocation table TRANSACTION ENDS
     */

    /*
     * STOCK AUDIT table TRANSACTION STARTS
     */

    public Boolean insertToStockAuditTable(String timeStamp, String remarks) {
        Boolean isInsertionDone = true;
        String order_no = "";
        int flag = 0;
        try {
            order_no = "S" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
            for (int ii = 0; ii < Constants.selectedProductMasterListStockAudit.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterListStockAudit
                        .get(ii);
//			database.beginTransaction();

                ContentValues cv = new ContentValues();
                cv.put("transaction_id", order_no);
                cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                cv.put("product_code", masterObj.getProdCode());
                cv.put("quantity", masterObj.getQty());
                cv.put("product_mrp", masterObj.getMrpCode());
                cv.put("product_details", masterObj.getIMEINo());
                cv.put("remarks", remarks);
                cv.put("flag", flag);
                cv.put("mfd_date", masterObj.getmfgDate());
                cv.put("UOM", masterObj.getUomSelectedForProduct());
                if(Constants.weightage.matches("yes")){
                    cv.put("weightage", masterObj.getWeightage());
                }


                synchronized (Lock) {
                    database.insertWithOnConflict("stock_audit", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Stock_Audit:", "Data Inserted");
                }
//				database.setTransactionSuccessful();

            }
        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
//				database.endTransaction();
        }
        return isInsertionDone;
    }

    public Boolean insertToStockAuditTableFromOrder(String timeStamp, String remarks) {
        Boolean isInsertionDone = true;
        String order_no = "";
        int flag = 0;
        try {
            order_no = "S" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
//				ProductMasterDetails masterObj = Constants.selectedProductMasterListStockAudit.get(ii);
                ProductMasterDetails masterObj = Constants.selectedProductMasterList.get(ii);

                ContentValues cv = new ContentValues();
                cv.put("transaction_id", order_no);
                cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                cv.put("product_code", masterObj.getProdCode());
                cv.put("quantity", masterObj.getStkQty());
                cv.put("product_mrp", masterObj.getMrpCode());
                cv.put("product_details", masterObj.getDesc());
                cv.put("remarks", remarks);
                cv.put("flag", flag);

                synchronized (Lock) {
                    database.insertWithOnConflict("stock_audit", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Stock_Audit:", "Data Inserted");
                }
//				database.setTransactionSuccessful();

            }
        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
//				database.endTransaction();
        }
        return isInsertionDone;
    }

    public Boolean insertToStockReturn(String timeStamp, String remarks,String orderPrefix) {
        Boolean isInsertionDone = true;
        int flag = 0;
        try {
            String returnTransId = "SR" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
            String order_no = orderPrefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
            for (int ii = 0; ii < Constants.selectedProductStockReturn.size(); ii++)
            {
                ProductMasterDetails masterObj = Constants.selectedProductStockReturn.get(ii);
                String stkQty = masterObj.getQty();
                    ContentValues cv = new ContentValues();
                    cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                    cv.put("prod_code", masterObj.getProdCode());
                    cv.put("return_trans_id", returnTransId);
                    cv.put("return_qty", masterObj.getQty());
                    cv.put("order_no", order_no);
                    cv.put("return_reason", masterObj.getstockReturnReason());
                    synchronized (Lock)
                    {
                        database.insertWithOnConflict("van_stock_return", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }


            }
        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
        }
        return isInsertionDone;
    }

    public Boolean insertToOrderDetailsTableForISP(String timeStamp) {
        Boolean isInsertionDone = true;
        String order_no = "";
        int flag = 0;
        try {
            order_no = "O" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
            for (int ii = 0; ii < Constants.selectedProductMasterListStockAudit.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterListStockAudit.get(ii);

                ContentValues cv = new ContentValues();
                cv.put("order_no", order_no);
                cv.put("sku_code", masterObj.getProdCode());
                cv.put("qty", masterObj.getQty());
                cv.put("flag", flag);

                synchronized (Lock) {
                    database.insertWithOnConflict("order_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                }

            }
        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
//				database.endTransaction();
        }
        return isInsertionDone;
    }

    public void insertNoStockAuditTable(String timeStamp, String remarks) {
        String order_no = "";
        int flag = 0;
        order_no = "NS" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;
        database.beginTransaction();
        ContentValues cv = new ContentValues();
        cv.put("transaction_id", order_no);
        cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
        cv.put("product_code", "");
        cv.put("quantity", "");
        cv.put("product_mrp", "");
        cv.put("product_details", "");
        cv.put("remarks", remarks);
        cv.put("flag", flag);
        synchronized (Lock) {
            database.insertWithOnConflict("stock_audit", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("Stock_Audit:", "Data Inserted");
        }
        database.setTransactionSuccessful();
        database.endTransaction();
    }

    public Boolean insertNoStockAuditTable1(String timeStamp, String remarks) {
        Boolean isSuccess = true;
        try {
            String order_no = "";
            int flag = 0;
            order_no = "NS" + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;
            ContentValues cv = new ContentValues();
            cv.put("transaction_id", order_no);
            cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
            cv.put("product_code", "");
            cv.put("quantity", "");
            cv.put("product_mrp", "");
            cv.put("product_details", "");
            cv.put("remarks", remarks);
            cv.put("flag", flag);
            synchronized (Lock) {
                database.insertWithOnConflict("stock_audit", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
            }
        } catch (Exception e) {
            isSuccess = false;
        }
        return isSuccess;
    }

    public Boolean insertStockOutDetailsUpdateCustomerProductBilling(String timeStamp,Boolean isSpecial) {
        Boolean isSuccess = true;
        try {
            String order_no = "";
            order_no = "SO" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
    if(isSpecial)
    {
        for (int i = 0; i < Constants.retailerStockOutProductListStockOut.size(); i++)
        {
            String dateOfStockOut = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", Constants.dateString);
            ContentValues cv = new ContentValues();
            cv.put("stock_out_id", order_no);
            cv.put("prod_code", Constants.retailerStockOutProductListStockOut.get(i).getProductCode());
            cv.put("IMEI", Constants.retailerStockOutProductListStockOut.get(i).getimei());
            cv.put("stock_out_date", dateOfStockOut);
            cv.put("stock_out_qty", "");
            ContentValues cv2 = new ContentValues();
            cv2.put("stock_out_date", dateOfStockOut);
            cv2.put("stock_out_customer_code", RetailerStockOutActivitySpecial.selectedCustomerCode);

            synchronized (Lock)
            {
                database.insertWithOnConflict("stock_out_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                database.update("customer_product_billing", cv2, "IMEI=?", new String[]{Constants.retailerStockOutProductListStockOut.get(i).getimei()});
            }
        }
    }
    else
    {
        for (int i = 0; i < Constants.selectedProductMasterList.size(); i++)
        {
            String dateOfStockOut = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", Constants.dateString);
            ContentValues cv = new ContentValues();
            cv.put("stock_out_id", order_no);
            cv.put("prod_code", Constants.selectedProductMasterList.get(i).getProdCode());
            cv.put("IMEI", "");
            cv.put("stock_out_date", dateOfStockOut);
            cv.put("stock_out_qty", Constants.selectedProductMasterList.get(i).getQty());

            synchronized (Lock)
            {
                database.insertWithOnConflict("stock_out_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
        }
    }


        } catch (Exception e)
        {
            isSuccess = false;
        }
        return isSuccess;
    }

    public Boolean insertRequisitionDetails(String timeStamp) {
        Boolean isSuccess = true;
        try {
            String order_no = "";
            order_no = "RD" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                String qty = Constants.selectedProductMasterList.get(i).getQty();
                if (Utils.isNumeric(qty)) {
                    ContentValues cv = new ContentValues();
                    cv.put("allocation_id", Constants.selectedProductMasterList.get(i).getallocation_id());
                    cv.put("prod_code", Constants.selectedProductMasterList.get(i).getProdCode());
                    String allotedQty = Constants.selectedProductMasterList.get(i).getallocation_qty();
                    cv.put("allot_qty", allotedQty);
                    cv.put("requisition_id", order_no);
                    cv.put("requisition_qty", qty);
                    synchronized (Lock) {
                        database.insertWithOnConflict("requisition_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
                }

            }

        } catch (Exception e) {
            isSuccess = false;
        }
        return isSuccess;
    }

    public Boolean insertCollectionForecastDetails(String timeStamp) {
        Boolean isSuccess = true;
        try {
            String order_no = "";
            order_no = "CF" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            for (int i = 0; i < Constants.selectedForecastList.size(); i++) {
                String qty = Constants.selectedForecastList.get(i).getForecastAmount();
                if (Utils.isNumeric(qty)) {
                    ContentValues cv = new ContentValues();
                    cv.put("forecast_id", order_no);
                    cv.put("customer_code", Constants.selectedForecastList.get(i).getCustomerCode());
                    cv.put("forecast_date", Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", dateString));
                    cv.put("invoice_amount", Constants.selectedForecastList.get(i).getTotalInvoice());
                    cv.put("amount_received", Constants.selectedForecastList.get(i).getForecastAmount());
                    synchronized (Lock) {
                        database.insertWithOnConflict("collection_forecast_details ", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
                }

            }

        } catch (Exception e) {
            isSuccess = false;
        }
        return isSuccess;
    }
    public Boolean InsertToGrnTransaction(String timeStamp) {
        Boolean isSuccess = true;
        try {
            String transId = "";
            transId = "GRN" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            for (int i = 0; i < GrnActivity.grnMasterSkuListItemGlobal.size(); i++) {
                commonDatabaseHelper commonDatabaseHelper = GrnActivity.grnMasterSkuListItemGlobal.get(i);
                String inputQty = commonDatabaseHelper.getItem7();
                if(Utils.isNumeric(inputQty) && Double.parseDouble(inputQty)>0){
                    ContentValues cv = new ContentValues();
                    cv.put("GRN_received_by", Constants.employeeDetailObject.getEmpCode());
                    cv.put("GRN_code", transId);
                    cv.put("GRN_date", Utils.changeDateFormat("yyyyMMddHHmmss", "yyyy-MM-dd HH:mm:ss", timeStamp));
                    cv.put("DO_no", commonDatabaseHelper.getItem0());
                    cv.put("sku_code", commonDatabaseHelper.getItem2());
                    cv.put("dispatch_qty", commonDatabaseHelper.getItem5());

                    cv.put("received_qty", inputQty);
                    cv.put("remarks", Constants.bargainNarration);
                    synchronized (Lock) {
                        database.insertWithOnConflict("GRN_transaction ", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
                }


            }

        } catch (Exception e) {
            isSuccess = false;
        }
        return isSuccess;
    }
    public boolean UpdateGrnMasterTable() {
        Boolean isSuccess = true;
        for (int i = 0; i < GrnActivity.grnMasterSkuListItemGlobal.size(); i++) {
            String sql = "UPDATE  GRN_master SET status ='received' WHERE DO_no ='" + GrnActivity.grnMasterSkuListItemGlobal.get(i).getItem0() + "'";
            try {
                database.execSQL(sql);
            } catch (SQLException e) {
                isSuccess = false;
            } finally {

            }
        }
        return isSuccess;
    }
    public boolean UpdateOrderDetails(String chosenOrderNo) {
        Boolean isSuccess = true;
        Double amount=0.00;
        for (int i = 0; i < OrderEditActivity.grnMasterSkuListItemGlobal.size(); i++) {

            commonDatabaseHelper commonDatabaseHelper = OrderEditActivity.grnMasterSkuListItemGlobal.get(i);
            String oldQty = commonDatabaseHelper.getItem2();
            String newQty = commonDatabaseHelper.getItem7();
            String sRate = commonDatabaseHelper.getItem3();
            String skuCode = commonDatabaseHelper.getItem0();
            String oldWeightage = commonDatabaseHelper.getItem4();
            Double currentAmount=0.00;
            Double currentWeightage=0.00;
//            if(Utils.isNumeric(newQty) && Double.parseDouble(newQty)>0){
            if(Utils.isNumeric(newQty)){
                currentAmount=Double.parseDouble(sRate)*Double.parseDouble(newQty);
                String sql = "UPDATE order_details SET qty ='"+newQty+"', amount='"+defaultFormat.format(currentAmount)+"' WHERE sku_code ='" + skuCode + "' AND order_no='"+chosenOrderNo+"'";

                if(Constants.weightage.matches("yes")) {
                    currentWeightage=Double.parseDouble(oldWeightage)/Double.parseDouble(oldQty);
                    currentWeightage=currentWeightage*Double.parseDouble(newQty);
                    sql = "UPDATE order_details SET qty ='"+newQty+"', amount='"+defaultFormat.format(currentAmount)+"', weightage='"+defaultFormat.format(currentWeightage)+"' WHERE sku_code ='" + skuCode + "' AND order_no='"+chosenOrderNo+"'";
                }
                try {
                    database.execSQL(sql);
                } catch (SQLException e) {
                    isSuccess = false;
                } finally {

                }
            }
            else{
                currentAmount=Double.parseDouble(sRate)*Double.parseDouble(oldQty);
            }
            amount=amount+currentAmount;
        }
        if(isSuccess){
            String sql = "UPDATE order_header SET order_value ='"+amount+"' WHERE order_no='"+chosenOrderNo+"'";
            try {
                database.execSQL(sql);
            } catch (SQLException e) {
                isSuccess = false;
            } finally {

            }
        }
        return isSuccess;
    }
    public boolean UpdateStockDetails(String chosenOrderNo) {
        Boolean isSuccess = true;
        Double amount=0.00;
        for (int i = 0; i < StockAuditEditActivity.grnMasterSkuListItemGlobal.size(); i++) {

            commonDatabaseHelper commonDatabaseHelper = StockAuditEditActivity.grnMasterSkuListItemGlobal.get(i);
            String oldQty = commonDatabaseHelper.getItem2();
            String newQty = commonDatabaseHelper.getItem7();
//            String sRate = commonDatabaseHelper.getItem3();
            String skuCode = commonDatabaseHelper.getItem0();
//            Double currentAmount=0.00;
            if(Utils.isNumeric(newQty)){
//                currentAmount=Double.parseDouble(sRate)*Double.parseDouble(newQty);
                String sql = "UPDATE stock_audit SET quantity ='"+newQty+"' WHERE product_code ='" + skuCode + "' AND transaction_id='"+chosenOrderNo+"'";
                try {
                    database.execSQL(sql);
                } catch (SQLException e) {
                    isSuccess = false;
                } finally {

                }
            }
        }
        return isSuccess;
    }
    public Boolean insertCustomerProductReallocation(String timeStamp) {
        Boolean isSuccess = true;
        try {
            String order_no = "";
            order_no = "RA" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                String qty = Constants.selectedProductMasterList.get(i).getQty();
                if (Utils.isNumeric(qty) && Double.parseDouble(qty) > 0) {
                    ContentValues cv = new ContentValues();
                    cv.put("allocation_id", order_no);
                    cv.put("customer_code", Constants.selectedCustomer.getCustomerCode());
                    cv.put("prod_code", Constants.selectedProductMasterList.get(i).getProdCode());
                    cv.put("qty", qty);
                    cv.put("from_date", "");
                    cv.put("to_date", "");
                    cv.put("acedns", "Y");
                    synchronized (Lock) {
                        database.insertWithOnConflict("customer_product_allocation", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }

                }

            }

        } catch (Exception e) {
            isSuccess = false;
        }
        return isSuccess;
    }

    public boolean UpdateRemainStockForStockReallocation() {
        Boolean isSuccess = true;
        for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
            String sql = "UPDATE  stock_reallocation SET balance_qty=balance_qty-" + Constants.selectedProductMasterList.get(i).getQty() + " WHERE prod_code ='" + Constants.selectedProductMasterList.get(i).getProdCode() + "'";
            try {
                database.execSQL(sql);
            } catch (SQLException e) {
                isSuccess = false;
            } finally {

            }
        }
        return isSuccess;
    }


    public ArrayList<StockAuditDetails> getUnuploadedStockAuditDetails(String orderNo)
    {
        ArrayList<StockAuditDetails> unUploadedOrdrDetailsList = new ArrayList<StockAuditDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM stock_audit where transaction_id = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    StockAuditDetails detailsObj = new StockAuditDetails();
                    detailsObj.setTransactionId(cursor.getString(0));
                    detailsObj.setCustomerCode(cursor.getString(1));
                    detailsObj.setProductCode(cursor.getString(2));
                    detailsObj.setQuantity(cursor.getString(3));
                    detailsObj.setProductMrp(cursor.getString(4));
                    detailsObj.setProductDetails(cursor.getString(5));
                    detailsObj.setRemarks(cursor.getString(6));
                    detailsObj.setmfgDate(cursor.getString(8));
                    detailsObj.setuom(cursor.getString(9));
                    detailsObj.setWeightage(cursor.getString(10));

                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<StockAuditDetails> getUnuploadedStockReturnDetails(String orderNo)
    {
        ArrayList<StockAuditDetails> unUploadedOrdrDetailsList = new ArrayList<StockAuditDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM van_stock_return where return_trans_id = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    StockAuditDetails detailsObj = new StockAuditDetails();
                    detailsObj.setCustomerCode(cursor.getString(0));
                    detailsObj.setProductCode(cursor.getString(1));
                    detailsObj.setTransactionId(cursor.getString(2));
                    detailsObj.setQuantity(cursor.getString(3));
                    detailsObj.setReturnOrderNumber(cursor.getString(4));
                    detailsObj.setReturnReason(cursor.getString(5));

                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<SaudaDetails> getUnuploadedDODetails(String orderNo)
    {
        ArrayList<SaudaDetails> unUploadedOrdrDetailsList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM DO_transaction where DO_NO = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    SaudaDetails detailsObj = new SaudaDetails();
                    detailsObj.setSaudaNo(cursor.getString(0));
                    detailsObj.setCustomerCode(cursor.getString(1));
                    detailsObj.setdestinationCode(cursor.getString(2));
                    detailsObj.setdono(cursor.getString(3));
                    detailsObj.setSkuCode(cursor.getString(4));
                    detailsObj.setQuantity(cursor.getString(5));
                    detailsObj.setSaleRate(cursor.getString(6));
                    detailsObj.setAmount(cursor.getString(7));
                    detailsObj.setStatus(cursor.getString(8));
                    detailsObj.setdate(cursor.getString(9));
                    detailsObj.setpono(cursor.getString(10));
                    detailsObj.setFlag(cursor.getString(11));
                    detailsObj.setdate(cursor.getString(12));

                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }
    public ArrayList<commonDatabaseHelper> getUnuploadedGiftDetails(String orderNo)
    {
        ArrayList<commonDatabaseHelper> unUploadedOrdrDetailsList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM gift_delivery_details where delivery_id = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    commonDatabaseHelper detailsObj = new commonDatabaseHelper();
                    detailsObj.setItem0(orderNo);//delivery_id
                    detailsObj.setItem1(cursor.getString(1));//delivery_date
                    detailsObj.setItem2(cursor.getString(2));//gift_id
                    detailsObj.setItem3(cursor.getString(3));//gift_name
                    detailsObj.setItem4(cursor.getString(4));//customer_broad_option
                    detailsObj.setItem5(cursor.getString(5));//customer_code
                    detailsObj.setItem6(cursor.getString(6));//gift_delivery_option
                    detailsObj.setItem7(cursor.getString(7));//owner_name
                    detailsObj.setItem8(cursor.getString(8));//employee_name
                    detailsObj.setItem9(cursor.getString(9));//employee_mobile
                    detailsObj.setItem10(cursor.getString(10));//employee_relation_owner
                    detailsObj.setItem11(cursor.getString(11));//location
                    detailsObj.setItem12(cursor.getString(12));//route_code
                    detailsObj.setItem13(cursor.getString(13));//image_1
                    detailsObj.setItem14(cursor.getString(14));//image_2

                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }
    public ArrayList<TourSwapDetails> getUnuploadedTourSwapDetails(
            String orderNo) {
        ArrayList<TourSwapDetails> unUploadedOrdrDetailsList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tour_day_swapping where tour_day_swap_trans_id = '"
                    + orderNo + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TourSwapDetails detailsObj = new TourSwapDetails();
                    detailsObj.setTransactionId(cursor.getString(0));
                    detailsObj.setEmpCode(cursor.getString(1));
                    detailsObj.setActualTourDay(cursor.getString(2));
                    detailsObj.setDeviateTourDay(cursor.getString(3));
                    detailsObj.setSwapDateActual(cursor.getString(4));
                    detailsObj.setSwapDateDeviate(cursor.getString(5));
                    detailsObj.setFlag(cursor.getString(6));

                    unUploadedOrdrDetailsList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedOrdrDetailsList;
    }

    public ArrayList<CallDurationDetails> getUnuploadedCallDurationList() {
        ArrayList<CallDurationDetails> unUploadedCallDurationList = new ArrayList<>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM call_duration where flag=0";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CallDurationDetails detailsObj = new CallDurationDetails();
                    detailsObj.setTransId(cursor.getString(0));
                    detailsObj.setCustomerCode(cursor.getString(1));
                    detailsObj.setCallDuration(cursor.getString(2));
                    unUploadedCallDurationList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {

        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedCallDurationList;
    }

    public void updateUnuploadedStockAuditDetails() {
        database.beginTransaction();
        int updateResult = -1;
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            synchronized (Lock) {
                updateResult = database.update("stock_audit", cv, "flag=?", new String[]{"0"});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("StockAudit Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("StockAudit Update status ::::::::::::" + updateResult);
    }

    /*
     * VAT DETAILS Transaction Ends
     */

    /*
     * STOCK IN TRANSACTION STARTS
     */
    public void increaseClStk() {
        String previousStock = "0.00";
        database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);

                String selectQuery = "SELECT cl_stk FROM closing_stock where prod_code = '"
                        + masterObj.getProdCode() + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    previousStock = cursor.getString(0);
                }
                String currentStock = String.valueOf(Double
                        .parseDouble(previousStock)
                        + Double.parseDouble(masterObj.getQty()));
                ContentValues cv = new ContentValues();
                cv.put("prod_code", masterObj.getProdCode());
                cv.put("cl_stk", currentStock);
                synchronized (Lock) {
                    String query = "DELETE FROM closing_stock WHERE prod_code='"
                            + masterObj.getProdCode() + "'";
                    database.execSQL(query);
                    database.insertWithOnConflict("closing_stock", null, cv,
                            SQLiteDatabase.CONFLICT_IGNORE);
                    Log.d("Closing_stock:", "Data Inserted");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for STOCK IN", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    public void UpdateAllocation() {
        String previousStock = "0.00";
        database.beginTransaction();

        try {
            for (int ii = 0; ii < Constants.mSaudaAllocationList.size(); ii++) {
                SaudaAllocation obj = Constants.mSaudaAllocationList
                        .get(ii);

                String selectQuery = "SELECT allot_qty FROM sauda_allocation where product_filter_code = '"
                        + obj.getProductFilterCode() + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    previousStock = cursor.getString(0);
                }
                String currentStock = String.valueOf(Double
                        .parseDouble(previousStock)
                        + Double.parseDouble(obj.getAllotedQuantityinLtr()));
                database.execSQL("UPDATE sauda_allocation SET allot_qty = '"
                        + currentStock + "' WHERE product_filter_code = '"
                        + obj.getProductFilterCode() + "'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Alloted Quantity Updated for sauda_allocation", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void UpdateTDAllocation() {
        database.beginTransaction();

        try {
            for (int ii = 0; ii < Constants.mTDAllocationList.size(); ii++) {
                TDAllocation obj = Constants.mTDAllocationList.get(ii);
                String productGroupCode = obj.getProductFilterCode();
                String empCode = obj.getEmployeCode();
                String td = obj.getTDAllocated();
                Boolean isDataPresent;
                String sqlQuery1 = "Select * from TD_allocation where emp_code='" + empCode + "' AND product_filter_code='" + productGroupCode + "'";

                Cursor cursor1 = database.rawQuery(sqlQuery1, null);
                if (cursor1 != null && cursor1.getCount() > 0) {
                    isDataPresent = true;
                } else {
                    isDataPresent = false;
                }
                if (cursor1 != null) {
                    cursor1.close();
                }
                ContentValues cv = new ContentValues();
                cv.put("emp_code", empCode);
                cv.put("product_filter_code ", productGroupCode);
                cv.put("TD ", td);
                String sqlQuery2 = "";
                if (isDataPresent) {
//					sqlQuery2="UPDATE TD_allocation set TD='"+td+"' where emp_code= '"+empCode+"' AND product_filter_code='"+productGroupCode+"'";
//					database.rawQuery(sqlQuery2, null);
//					database.update("TD_allocation", cv, "_id="+id, null);

                    String[] args = new String[]{empCode, productGroupCode};
                    database.update("TD_allocation", cv, "emp_code=? AND product_filter_code=?", args);
                } else {

                    synchronized (Lock) {
                        database.insertWithOnConflict("TD_allocation", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                    }
//					sqlQuery2="INSERT INTO TD_allocation VALUES ('"+empCode+"','"+productGroupCode+"','"+td+"')";
                }

            }
            database.setTransactionSuccessful();
        } catch (Exception e) {

        } finally {
            database.endTransaction();
        }
    }

    public void decreaseBALSauda() {
        String previousStock = "0.00";
        database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);

                String selectQuery = "SELECT BAL FROM sauda_allocation where product_filter_code = '"
                        + masterObj.getFilterCode() + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    previousStock = cursor.getString(0);
                }
                String currentStock = String.valueOf(Double
                        .parseDouble(previousStock)
                        - Double.parseDouble(masterObj.getQty()));
                database.execSQL("UPDATE sauda_allocation SET BAL = '"
                        + currentStock + "' WHERE product_filter_code = '"
                        + masterObj.getFilterCode() + "'");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for sauda_allocation", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    public void decreaseBALSaudaProdWise(ProductMasterDetails masterObj) {
        String previousStock = "0.00";
        database.beginTransaction();
        try {
            String selectQuery = "SELECT BAL FROM sauda_allocation where product_filter_code = '"
                    + masterObj.getFilterCode() + "'";
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                previousStock = cursor.getString(0);
            }
            String currentStock = String.valueOf(Double
                    .parseDouble(previousStock)
                    - Double.parseDouble(masterObj.getQty()));
            database.execSQL("UPDATE sauda_allocation SET BAL = '"
                    + currentStock + "' WHERE product_filter_code = '"
                    + masterObj.getFilterCode() + "'");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for sauda_allocation", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    public void increaseBALSaudaProdWise(ProductMasterDetails masterObj) {
        String previousStock = "0.00";
        database.beginTransaction();
        try {
            String selectQuery = "SELECT BAL FROM sauda_allocation where product_filter_code = '"
                    + masterObj.getFilterCode() + "'";
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                previousStock = cursor.getString(0);
            }
            String currentStock = String.valueOf(Double
                    .parseDouble(previousStock)
                    + Double.parseDouble(masterObj.getQty()));
            database.execSQL("UPDATE sauda_allocation SET BAL = '"
                    + currentStock + "' WHERE product_filter_code = '"
                    + masterObj.getFilterCode() + "'");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for sauda_allocation", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    public void increaseClStkProductWise(ProductMasterDetails masterObj) {
        String previousStock = "0.00";
        try {
            String selectQuery = "SELECT cl_stk FROM closing_stock where prod_code = '"
                    + masterObj.getProdCode() + "'";
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                previousStock = cursor.getString(0);
            }
            database.beginTransaction();
            String currentStock = String.valueOf(Double
                    .parseDouble(previousStock)
                    + Double.parseDouble(masterObj.getQty()));
            ContentValues cv = new ContentValues();
            cv.put("prod_code", masterObj.getProdCode());
            cv.put("cl_stk", currentStock);
            synchronized (Lock) {
                String query = "DELETE FROM closing_stock WHERE prod_code='"
                        + masterObj.getProdCode() + "'";
                database.execSQL(query);
                database.insertWithOnConflict("closing_stock", null, cv,
                        SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("Closing_stock:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for STOCK IN", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    /*
     * STOCK IN TRANSACTION ENDS
     */

    /*
     * CARRY IN TRANSACTION STARTS
     */
    public void reduceClStk() {
        database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
                Double prevClStk = Double
                        .parseDouble(masterObj.getClosingStk());
                Double qtyOrderd = Double.parseDouble(masterObj.getQty());
                Double remaining = prevClStk - qtyOrderd;
                String remainStr = "" + remaining;
                System.out.println("REMAINING QTY:::::::" + remainStr);
                ContentValues cv = new ContentValues();
                cv.put("cl_stk", remainStr);
                synchronized (Lock) {
                    database.update("closing_stock", cv, "prod_code=?",
                            new String[]{masterObj.getProdCode()});
                    Log.d("Cl Stock Updated for CARRY IN:",
                            "Cl Stock Updated for CARRY IN");
                }
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for CARRY IN", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    //removing set transaction succesfull
    public Boolean reduceClStk1() {
        Boolean isInsertionDone = true;
//		database.beginTransaction();
        try {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterList
                        .get(ii);
                Double prevClStk = Double
                        .parseDouble(masterObj.getClosingStk());
                Double qtyOrderd = Double.parseDouble(masterObj.getQty());
                Double remaining = prevClStk - qtyOrderd;
                String remainStr = "" + remaining;
                System.out.println("REMAINING QTY:::::::" + remainStr);
                ContentValues cv = new ContentValues();
                cv.put("cl_stk", remainStr);
                synchronized (Lock) {
                    database.update("closing_stock", cv, "prod_code=?",
                            new String[]{masterObj.getProdCode()});
                }
            }
//			database.setTransactionSuccessful();
        } catch (Exception e) {
            isInsertionDone = false;
        } finally {
//			database.endTransaction();
        }
        return isInsertionDone;
    }

    public void reduceClStkProductWise(ProductMasterDetails masterObj) {
        String previousStock = "0.00";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT cl_stk FROM closing_stock where prod_code = '"
                    + masterObj.getProdCode() + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                previousStock = cursor.getString(0);
            }
        } catch (Exception e) {
            System.out.println("");
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        database.beginTransaction();
        try {
            Double prevClStk = Double.parseDouble(previousStock);
            Double qtyOrderd = Double.parseDouble(masterObj.getQty());
            Double remaining = prevClStk - qtyOrderd;
            String remainStr = "" + remaining;
            System.out.println("REMAINING QTY:::::::" + remainStr);
            ContentValues cv = new ContentValues();
            cv.put("cl_stk", remainStr);
            synchronized (Lock) {
                database.update("closing_stock", cv, "prod_code=?",
                        new String[]{masterObj.getProdCode()});
                Log.d("Cl Stock Updated for CARRY IN:",
                        "Cl Stock Updated for CARRY IN");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Cl Stock Updated for CARRY IN", e.getMessage());
        } finally {
            database.endTransaction();
        }

    }

    /*
     * CARRY IN TRANSACTION ENDS
     */

    /*
     * REPORT SUMMERY transaction STARTS
     */

    public ReportSummery GetSaudaReportSummery(String date) {
        ReportSummery report = new ReportSummery();
        int ordercount = 0;
        int noordercount = 0;
        Cursor cursor = null;
        String Query = "";
        try {
            Query = "SELECT COUNT(transaction_type) booking_count FROM sauda_header where substr(sauda_no,-14,8)='" + date + "' and sauda_no LIKE 'FT%'";
            cursor = database.rawQuery(Query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                ordercount = Integer.parseInt(cursor.getString(0));
                report.setNoOrdrRcvd(cursor.getString(0));
            }
            Query = "";
            Query = "SELECT COUNT(transaction_type) booking_count FROM sauda_header where substr(sauda_no,-14,8)='" + date + "' and sauda_no LIKE 'NFT%'";
            cursor = database.rawQuery(Query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                noordercount = Integer.parseInt(cursor.getString(0));
                report.setNoNoAct(cursor.getString(0));
            }
            cursor.close();
            int totalcustomervisited = ordercount + noordercount;
            report.setNoCollcRcvd(String.valueOf(totalcustomervisited));

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return report;
    }


    public ArrayList<KeyValue> GetNoActivityMtdandToday(String type, String condition) {
        ArrayList<KeyValue> keyValueList = new ArrayList<KeyValue>();
        String query = "";

        if (condition.length() == 8) {
            if (type.equalsIgnoreCase("order")) {
                query = "SELECT CM.customer_name, OH.d_instruction,SUBSTR(OH.order_no,-14,8) FROM customer_master CM,order_header OH WHERE OH.order_no LIKE 'NO%'  AND SUBSTR(OH.order_no,-14,8) LIKE '" + condition + "' AND CM.customer_code=OH.customer_code GROUP BY OH.customer_code";
            }
            if (type.equalsIgnoreCase("Collection")) {
                query = "SELECT CM.customer_name, PH.p_remark FROM customer_master CM,payment_header PH WHERE PH.receipt_id LIKE 'NC%'  AND SUBSTR(PH.receipt_id,-14,8) LIKE '" + condition + "' AND CM.customer_code=PH.customer_code GROUP BY PH.customer_code";
            }
            if (type.equalsIgnoreCase("sauda")) {
                query = "SELECT CM.customer_name, SH.d_instruction FROM customer_master CM,sauda_header SH WHERE SH.sauda_no LIKE 'NFT%'  AND SUBSTR(SH.sauda_no,-14,8) LIKE '" + condition + "' AND CM.customer_code=SH.customer_code GROUP BY SH.customer_code";
            }
            if (type.equalsIgnoreCase("stock")) {
                query = "SELECT CM.customer_name, SA.remarks FROM customer_master CM,stock_audit SA WHERE SA.transaction_id LIKE 'NS%'  AND SUBSTR(SA.transaction_id,-14,8) LIKE '" + condition + "' AND CM.customer_code=SA.customer_code GROUP BY SA.customer_code";
            }
        } else {
            if (type.equalsIgnoreCase("order")) {
                query = "SELECT CM.customer_name, OH.d_instruction,SUBSTR(OH.order_no,-14,8) FROM customer_master CM,order_header OH WHERE OH.order_no LIKE 'NO%'  AND SUBSTR(OH.order_no,-14,6) LIKE '" + condition + "' AND CM.customer_code=OH.customer_code GROUP BY OH.customer_code";
            }
            if (type.equalsIgnoreCase("Collection")) {
                query = "SELECT CM.customer_name, PH.p_remark FROM customer_master CM,payment_header PH WHERE PH.receipt_id LIKE 'NC%'  AND SUBSTR(PH.receipt_id,-14,6) LIKE '" + condition + "' AND CM.customer_code=PH.customer_code GROUP BY PH.customer_code";
            }
            if (type.equalsIgnoreCase("sauda")) {
                query = "SELECT CM.customer_name, SH.d_instruction FROM customer_master CM,sauda_header SH WHERE SH.sauda_no LIKE 'NFT%'  AND SUBSTR(SH.sauda_no,-14,6) LIKE '" + condition + "' AND CM.customer_code=SH.customer_code GROUP BY SH.customer_code";
            }
            if (type.equalsIgnoreCase("stock")) {
                query = "SELECT CM.customer_name, SA.remarks FROM customer_master CM,stock_audit SA WHERE SA.transaction_id LIKE 'NS%'  AND SUBSTR(SA.transaction_id,-14,6) LIKE '" + condition + "' AND CM.customer_code=SA.customer_code GROUP BY SA.customer_code";
            }
        }

        Cursor cursor = database.rawQuery(query, null);
        if (cursor.getCount() > 0) {
            cursor.moveToFirst();
            for (int i = 0; i < cursor.getCount(); i++) {
                KeyValue obj = new KeyValue();
                obj.setKey(cursor.getString(0));
                obj.setValue(cursor.getString(1));
                if (type.equalsIgnoreCase("order")) {
                    obj.setmShowColumn1(Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", cursor.getString(2)));
                }
                keyValueList.add(obj);
                cursor.moveToNext();
            }
        }
        cursor.close();
        return keyValueList;
    }

    public ArrayList<KeyValue> GetNoActivityMtdandToday(String type, String startdate, String enddate) {
        ArrayList<KeyValue> keyValueList = new ArrayList<KeyValue>();
        String query = "";

        if (type.equalsIgnoreCase("order")) {
            query = "SELECT CM.customer_name, OH.d_instruction,SUBSTR(OH.order_no,-14,8) FROM customer_master CM,order_header OH WHERE OH.order_no LIKE 'NO%'  AND SUBSTR(OH.order_no,-14,8) BETWEEN '" + startdate + "' AND '" + enddate + "' AND CM.customer_code=OH.customer_code GROUP BY OH.customer_code";
        }
        if (type.equalsIgnoreCase("Collection")) {
            query = "SELECT CM.customer_name, PH.p_remark FROM customer_master CM,payment_header PH WHERE PH.receipt_id LIKE 'NC%'  AND SUBSTR(PH.receipt_id,-14,8) BETWEEN '" + startdate + "' AND '" + enddate + "' AND CM.customer_code=PH.customer_code GROUP BY PH.customer_code";
        }
        if (type.equalsIgnoreCase("sauda")) {
            query = "SELECT CM.customer_name, SH.d_instruction FROM customer_master CM,sauda_header SH WHERE SH.sauda_no LIKE 'NFT%'  AND SUBSTR(SH.sauda_no,-14,8) BETWEEN '" + startdate + "' AND '" + enddate + "' AND CM.customer_code=SH.customer_code GROUP BY SH.customer_code";
        }
        if (type.equalsIgnoreCase("stock")) {
            query = "SELECT CM.customer_name, SA.remarks FROM customer_master CM,stock_audit SA WHERE SA.transaction_id LIKE 'NS%'  AND SUBSTR(SA.transaction_id,-14,8) BETWEEN '" + startdate + "' AND '" + enddate + "' AND CM.customer_code=SA.customer_code GROUP BY SA.customer_code";
        }

        Cursor cursor = database.rawQuery(query, null);
        if (cursor.getCount() > 0) {
            cursor.moveToFirst();
            for (int i = 0; i < cursor.getCount(); i++) {
                KeyValue obj = new KeyValue();
                obj.setKey(cursor.getString(0));
                obj.setValue(cursor.getString(1));
                if (type.equalsIgnoreCase("order")) {
                    obj.setmShowColumn1(Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", cursor.getString(2)));
                }
                keyValueList.add(obj);
                cursor.moveToNext();
            }
        }
        cursor.close();
        return keyValueList;
    }

    public SurveyReportSumary getSurveyReportSummery(String condition) {
        SurveyReportSumary obj = new SurveyReportSumary();

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("KYC")) {
            int kyc = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='KYC' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='KYC' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                kyc = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoKYC(String.valueOf(kyc));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New IHB")) {
            int newihb = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New IHB' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New IHB' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newihb = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewIHB(String.valueOf(newihb));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Existing IHB")) {
            int existingihb = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Existing IHB' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Existing IHB' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                existingihb = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoExistingIHB(String.valueOf(existingihb));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New Dealer")) {
            int newdealer = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Dealer' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Dealer' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newdealer = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewDealer(String.valueOf(newdealer));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New Sub Dealer")) {
            int newsubdealer = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Sub Dealer' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Sub Dealer' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newsubdealer = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewSubDealer(String.valueOf(newsubdealer));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("IHB Site & Complaint Visit")) {
            int ihbsite = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='IHB Site & Complaint Visit' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='IHB Site & Complaint Visit' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                ihbsite = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewIHBSiteVisit(String.valueOf(ihbsite));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Site Visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Site Visit' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Site Visit' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoSiteVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().toLowerCase().contains("farmer visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFarmerVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Facilitator Add' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Facilitator Add' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFacilitaorAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Customer Add"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Add' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Add' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoCustomerAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Technical Meets"))
        {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Technical Meets' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Technical Meets' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoTechnicalMeet(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Branding Verification"))
        {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding Verification' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding Verification' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoBrandingVerifiction(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Customer Feedback")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Feedback' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Feedback' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCustomerFeedback(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Corporate Branding")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Corporate Branding' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Corporate Branding' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCorporateBranding(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Counter Branding")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Branding' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Branding' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCounterBranding(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Branding")) {
            int branding = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                branding = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoBranding(String.valueOf(branding));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("FS")) {
            int fs = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT foot_soldier_id) FROM foot_soldier WHERE SUBSTR(foot_soldier_id,1,2)='FS' AND SUBSTR(foot_soldier_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT foot_soldier_id) FROM foot_soldier WHERE SUBSTR(foot_soldier_id,1,2)='FS' AND SUBSTR(foot_soldier_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                fs = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoFS(String.valueOf(fs));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("DCA")) {
            int dca = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT DCA_trans_id) FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT DCA_trans_id) FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                dca = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoDCA(String.valueOf(dca));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("DCE")) {
            int dce = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE (type='DCE' OR type='DCA') AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE (type='DCE' OR type='DCA') AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                dce = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoDCE(String.valueOf(dce));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Lead Generation")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Lead Generation' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Lead Generation' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoLeadGeneration(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Mason Skill Building Program")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Mason Skill Building Program' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Mason Skill Building Program' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMasonSkillBuildProgram(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Influencer")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Influencer' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Influencer' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoInfluencer(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Counter Visit")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Visit' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Visit' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCounterVisit(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Quality Complaint")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Quality Complaint' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Quality Complaint' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoQualityComplaint(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("MTL Testing Format")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MTL Testing Format' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MTL Testing Format' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMTLTestingFormat(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("MLE Site Visit")) {
            int technicalmeets = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MLE Site Visit' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MLE Site Visit' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMLESiteVisit(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }


        return obj;
    }


    public SurveyReportSumary getSurveyReportSummery(String fastdate, String enddate) {
        SurveyReportSumary obj = new SurveyReportSumary();

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("KYC")) {
            int kyc = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='KYC' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                kyc = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoKYC(String.valueOf(kyc));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New IHB")) {
            int newihb = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New IHB' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newihb = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewIHB(String.valueOf(newihb));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Existing IHB")) {
            int existingihb = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Existing IHB' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                existingihb = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoExistingIHB(String.valueOf(existingihb));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New Dealer")) {
            int newdealer = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Dealer' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newdealer = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewDealer(String.valueOf(newdealer));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("New Sub Dealer")) {
            int newsubdealer = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='New Sub Dealer' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                newsubdealer = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewSubDealer(String.valueOf(newsubdealer));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("IHB Site & Complaint Visit")) {
            int ihbsite = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='IHB Site & Complaint Visit' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                ihbsite = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoNewIHBSiteVisit(String.valueOf(ihbsite));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Site Visit")) {
            int sitevisit = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Site Visit' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoSiteVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().toLowerCase().contains("farmer visit")) {
            int sitevisit = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003'  AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFarmerVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Facilitator Add")) {
            int sitevisit = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Facilitator Add' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFacilitaorAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Customer Add")) {
            int sitevisit = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Add' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoCustomerAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Technical Meets")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Technical Meets' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoTechnicalMeet(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Branding Verification")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding Verification' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoBrandingVerifiction(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Customer Feedback")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Customer Feedback' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCustomerFeedback(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Branding")) {
            int branding = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Branding' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                branding = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoBranding(String.valueOf(branding));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("FS")) {
            int fs = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT foot_soldier_id) FROM foot_soldier WHERE SUBSTR(foot_soldier_id,1,2)='FS' AND SUBSTR(foot_soldier_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                fs = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoFS(String.valueOf(fs));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("DCA")) {
            int dca = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT DCA_trans_id) FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                dca = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoDCA(String.valueOf(dca));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("DCE")) {
            int dce = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE (type='DCE' OR type='DCA') AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                dce = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoDCE(String.valueOf(dce));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Corporate Branding")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Corporate Branding' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCorporateBranding(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Counter Branding")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Branding' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCounterBranding(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Lead Generation")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Lead Generation' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoLeadGeneration(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Mason Skill Building Program")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Mason Skill Building Program' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMasonSkillBuildProgram(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Influencer")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Influencer' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoInfluencer(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Counter Visit")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Counter Visit' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoCounterVisit(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("MTL Testing Format")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MTL Testing Format' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMTLTestingFormat(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Quality Complaint")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Quality Complaint' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoQualityComplaint(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }
        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("MLE Site Visit")) {
            int technicalmeets = 0;
            String query = "";
            query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='MLE Site Visit' AND SUBSTR(survey_id,-14,8) BETWEEN'" + fastdate + "' AND '" + enddate + "'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                technicalmeets = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoMLESiteVisit(String.valueOf(technicalmeets));
            if (cursor != null) {
                cursor.close();
            }
        }

        return obj;
    }


    public ReportSummery getReportSummery(String criteria) {
        ReportSummery reportObj = new ReportSummery();
        int ordrCount = 0, collcCount = 0, stockCount = 0, checkinoutcust = 0, totalCust = 0, mfsCount = 0, yellowCardCount = 0, cashDepositCount = 0, cashTransactionCount = 0, checkInOutCount = 0,orderApprovalCount=0;
        if (criteria.length() > 0) {
            String ordrQuery = "SELECT DISTINCT customer_code, SUBSTR(order_no,-14,8) FROM order_header WHERE SUBSTR(order_no,-14," + criteria.length() + ") LIKE '"
                    + criteria + "'";
            Cursor cursor = database.rawQuery(ordrQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                ordrCount = cursor.getCount();
            }
            String collcQuery = "SELECT COUNT(DISTINCT customer_code)FROM payment_header WHERE SUBSTR(receipt_id,-14," + criteria.length() + ") LIKE '"
                    + criteria
                    + "' "
                    + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14," + criteria.length() + ") "
                    + "LIKE '" + criteria + "' GROUP BY customer_code)";
            Cursor cursor1 = database.rawQuery(collcQuery, null);
            if (cursor1.getCount() > 0) {
                cursor1.moveToFirst();
                collcCount = Integer.parseInt(cursor1.getString(0));
            }
            if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
                String stockQuery = "SELECT COUNT(DISTINCT customer_code) FROM stock_audit WHERE SUBSTR(transaction_id,-14," + criteria.length() + ") LIKE '"
                        + criteria
                        + "' "
                        + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14," + criteria.length() + ") "
                        + "LIKE '" + criteria + "')";
                Cursor cursor2 = database.rawQuery(stockQuery, null);
                if (cursor2.getCount() > 0) {
                    cursor2.moveToFirst();
                    stockCount = Integer.parseInt(cursor2.getString(0));
                }
            }
            if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
                String stockQuery = "SELECT COUNT(DISTINCT customer_code) FROM mf_stk_audit_header WHERE SUBSTR(mf_stk_audit_id,-14," + criteria.length() + ") LIKE '"
                        + criteria
                        + "' ";
                Cursor cursor2 = database.rawQuery(stockQuery, null);
                if (cursor2.getCount() > 0) {
                    cursor2.moveToFirst();
                    mfsCount = Integer.parseInt(cursor2.getString(0));
                }
            }
            if (Constants.menuDetailsObj.getapp_order_approval().equalsIgnoreCase("yes"))
            {
                String stockQuery = "SELECT COUNT(DISTINCT APPORDERNO) FROM T_APPERPDO_APPROVAL WHERE SUBSTR(approval_id,-14," + criteria.length() + ") LIKE '"
                        + criteria
                        + "' ";
                Cursor cursor2 = database.rawQuery(stockQuery, null);
                if (cursor2.getCount() > 0) {
                    cursor2.moveToFirst();
                    orderApprovalCount = Integer.parseInt(cursor2.getString(0));
                    reportObj.setorderApproval(orderApprovalCount+"");
                }
            }
            if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
                String checkinout = "SELECT COUNT(DISTINCT trans_id) FROM check_in_out_details WHERE SUBSTR(trans_id,-14," + criteria.length() + ")='" + criteria + "'";
                Cursor cursor3 = database.rawQuery(checkinout, null);
                if (cursor3.getCount() > 0) {
                    cursor3.moveToFirst();
                    checkinoutcust = Integer.parseInt(cursor3.getString(0));
                }
            }
            if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes")) {

                String Query = "SELECT COUNT(DISTINCT customer_code) FROM yellow_card_details WHERE SUBSTR(yellow_card_no,-14," + criteria.length() + ") LIKE '"
                        + criteria
                        + "' "
                        + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14," + criteria.length() + ") "
                        + "LIKE '" + criteria + "')";
                Cursor cursor2 = database.rawQuery(Query, null);
                if (cursor2 != null && cursor2.getCount() > 0) {
                    cursor2.moveToFirst();
                    yellowCardCount = Integer.parseInt(cursor2.getString(0));
                }
            }
            if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {

                String Query = "SELECT COUNT(DISTINCT customer_code) FROM check_in_out_details WHERE SUBSTR(trans_id,-14," + criteria.length() + ") LIKE '"
                        + criteria
                        + "' "
                        + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14," + criteria.length() + ") "
                        + "LIKE '" + criteria + "')";
                Cursor cursor2 = database.rawQuery(Query, null);
                if (cursor2 != null && cursor2.getCount() > 0) {
                    cursor2.moveToFirst();
                    checkInOutCount = Integer.parseInt(cursor2.getString(0));
                }
            }

            if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) {
                String cashDepositCountQuery = "SELECT COUNT(DISTINCT cash_deposit_trans_id) FROM cash_deposit_details WHERE SUBSTR(cash_deposit_trans_id,-14," + criteria.length() + ")='" + criteria + "'";
                Cursor cursor3 = database.rawQuery(cashDepositCountQuery, null);
                if (cursor3.getCount() > 0) {
                    cursor3.moveToFirst();
                    cashDepositCount = Integer.parseInt(cursor3.getString(0));
                    reportObj.setnoofCashDeposit(cashDepositCount + "");
                } else {
                    reportObj.setnoofCashDeposit("0");
                }
                String cashTransactionCountQuery = "SELECT COUNT(DISTINCT cash_transaction_id) FROM cash_transaction_details WHERE SUBSTR(cash_transaction_id,-14" + criteria.length() + ")='" + criteria + "'";
                Cursor cursor4 = database.rawQuery(cashTransactionCountQuery, null);
                if (cursor4.getCount() > 0) {
                    cursor4.moveToFirst();
                    cashTransactionCount = Integer.parseInt(cursor4.getString(0));
                    reportObj.setnoofCashTransaction(cashTransactionCount + "");

                } else {
                    reportObj.setnoofCashTransaction("0");
                }


                String cashBalanceQuery = "SELECT COUNT(DISTINCT cash_deposit_recv_id) FROM cash_deposit_receive_details WHERE SUBSTR(cash_deposit_recv_id,-14" + criteria.length() + ")='" + criteria + "'";
                Cursor cursor5 = database.rawQuery(cashBalanceQuery, null);
                if (cursor5.getCount() > 0) {
                    cursor5.moveToFirst();
                    cashTransactionCount = Integer.parseInt(cursor5.getString(0));
                    reportObj.setCashBalance(cashTransactionCount + "");

                } else {
                    reportObj.setCashBalance("0");
                }
            }

            totalCust = ordrCount + collcCount + stockCount + checkinoutcust + mfsCount + yellowCardCount + cashDepositCount + cashTransactionCount + checkInOutCount+orderApprovalCount;
            reportObj.setNoCustVisitd(String.valueOf(totalCust));
        }

        // Ordr Rcvd
        if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes"))
        {
            int ordrRcvd = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM order_header WHERE order_no LIKE 'O%'  AND SUBSTR(order_no,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    ordrRcvd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoOrdrRcvd(String.valueOf(ordrRcvd));
            } else {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM order_header WHERE order_no LIKE 'O%'  AND SUBSTR(order_no,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    ordrRcvd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoOrdrRcvd(String.valueOf(ordrRcvd));
            }
        }
        // Stock Audit
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
            int stock = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM stock_audit WHERE transaction_id LIKE 'S%' AND SUBSTR(transaction_id,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    stock = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofStockAudit(String.valueOf(stock));
            } else {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM stock_audit WHERE transaction_id LIKE 'S%' AND SUBSTR(transaction_id,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    stock = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofStockAudit(String.valueOf(stock));
            }
        }
        if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes")) {
            int ycd = 0;
            if (criteria.length() > 0) {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM yellow_card_details WHERE SUBSTR(yellow_card_no,-14," + criteria.length() + ") LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    ycd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setnoofYellowCardDetails(String.valueOf(ycd));
            }
        }
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
            int ycd = 0;
            if (criteria.length() > 0) {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM check_in_out_details WHERE SUBSTR(trans_id,-14," + criteria.length() + ") LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    ycd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setnoofCheckInOut(String.valueOf(ycd));
            }
        }

        if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
            reportObj.setNoofMFS(String.valueOf(mfsCount));
        }
        // Collc Rcvd
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes")) {
            int collcRcvd = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM payment_header WHERE receipt_id LIKE 'P%'  AND SUBSTR(receipt_id,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    collcRcvd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoCollcRcvd(String.valueOf(collcRcvd));
            } else {
                String selectQuery = "SELECT COUNT( DISTINCT customer_code)FROM payment_header WHERE receipt_id LIKE 'P%'  AND SUBSTR(receipt_id,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    collcRcvd = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoCollcRcvd(String.valueOf(collcRcvd));
            }
        }

        // NoActvty
        int noTask = 0;
        if (criteria.length() == 8) {
            String selectQuery = "SELECT COUNT(trans_id) FROM location WHERE substr(trans_id,1,2) IN('NO','NC','NF') AND SUBSTR(trans_id,-14,8) LIKE '" + criteria + "'";
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                noTask = Integer.parseInt(cursor.getString(0));
            }
            reportObj.setNoNoAct(String.valueOf(noTask));
        } else {
            String selectQuery = "SELECT COUNT(trans_id) FROM location WHERE substr(trans_id,1,2) IN('NO','NC','NF') AND SUBSTR(trans_id,-14,6) LIKE '" + criteria + "'";
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                noTask = Integer.parseInt(cursor.getString(0));
            }
            reportObj.setNoNoAct(String.valueOf(noTask));
        }

        // Booking Sauda
        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
            int noSauda = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT(DISTINCT customer_code) FROM sauda_header WHERE sauda_no LIKE 'FT%' AND SUBSTR(sauda_no,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    noSauda = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setSaudaBooking(String.valueOf(noSauda));
            } else {
                String selectQuery = "SELECT COUNT(DISTINCT customer_code) FROM sauda_header WHERE sauda_no LIKE 'FT%' AND SUBSTR(sauda_no,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    noSauda = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setSaudaBooking(String.valueOf(noSauda));
            }
        }

        // New Cust Vstd
        if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) {
            int newCust = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT(trans_id)FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    newCust = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoNewCustVisitd(String.valueOf(newCust));
            } else {
                String selectQuery = "SELECT COUNT(trans_id)FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    newCust = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoNewCustVisitd(String.valueOf(newCust));
            }
        }

        // No of Survey
        if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes")) {
            int survey = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('FS','SU') or substr(trans_id,1,3)='SUA') AND SUBSTR(trans_id,-14,8) LIKE '" + criteria + "'";
                if(Constants.nickName.equalsIgnoreCase("nimbus"))
                {
                    selectQuery = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003' AND SUBSTR(survey_id,-14,8) LIKE '" + criteria + "'";
                    selectQuery = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE row_id='RA003' AND SUBSTR(survey_id,-14,8) LIKE '" + criteria + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    survey = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofsurvey(String.valueOf(survey));
            } else {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('FS','SU') or substr(trans_id,1,3)='SUA') AND SUBSTR(trans_id,-14,6) LIKE '" + criteria + "'";
                if(Constants.nickName.equalsIgnoreCase("nimbus"))
                {
                    selectQuery =  "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003' AND SUBSTR(survey_id,-14,6) LIKE '" + criteria + "'";
                    selectQuery =  "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE row_id='RA003' AND SUBSTR(survey_id,-14,6) LIKE '" + criteria + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    survey = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofsurvey(String.valueOf(survey));
            }
        }


        if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes")) {
            int survey = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('TT','TTE') or substr(trans_id,1,3)='TTE') AND SUBSTR(trans_id,-14,8) LIKE '" + criteria + "'";
                if(Constants.nickName.equalsIgnoreCase("nimbus"))
                {

                    selectQuery = "SELECT COUNT(DISTINCT tour_exp_trans_id) FROM tour_expenses_details WHERE SUBSTR(tour_exp_trans_id,-14,8) LIKE '" + criteria + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    survey = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setTour_expense(String.valueOf(survey));
            } else {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('FS','SU') or substr(trans_id,1,3)='SUA') AND SUBSTR(trans_id,-14,6) LIKE '" + criteria + "'";
                if(Constants.nickName.equalsIgnoreCase("nimbus"))
                {

                    selectQuery = "SELECT COUNT(DISTINCT tour_exp_trans_id) FROM tour_expenses_details WHERE SUBSTR(tour_exp_trans_id,-14,6) LIKE '" + criteria + "'";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    survey = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setTour_expense(String.valueOf(survey));
            }
        }




        //whole sale info
        if (Constants.menuDetailsObj.getWholeSaleInfo().equalsIgnoreCase("yes")) {
            int wholesale = 0;
            if (criteria.length() == 8) {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE substr(trans_id,1,1)='W' AND SUBSTR(trans_id,-14,8) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    wholesale = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofwholesale(String.valueOf(wholesale));
            } else {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE substr(trans_id,1,1)='W' AND SUBSTR(trans_id,-14,6) LIKE '" + criteria + "'";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    wholesale = Integer.parseInt(cursor.getString(0));
                }
                reportObj.setNoofwholesale(String.valueOf(wholesale));
            }
        }


        // Productvty
        int prod = 0;
        int performnc = (totalCust - noTask) * 100;
        prod = (totalCust != 0 ? (performnc / totalCust) : 0);
        reportObj.setProdctvty(String.valueOf(prod));
        return reportObj;
    }


    public ReportSummery getCustomReportSummery(String startDate, String endDate) {
        ReportSummery reportObj = new ReportSummery();

        // Cust Vstd
        int ordrCount = 0, collcCount = 0, stockCount = 0, checkinoutcust = 0, totalCust = 0, mfsCount = 0, yellowCardCount = 0, cashDepositCount = 0, cashTransactionCount = 0, checkInOutCount = 0,orderApprovalCount=0;
        String ordrQuery = "SELECT DISTINCT customer_code, SUBSTR(order_no,-14,8) FROM order_header WHERE SUBSTR(order_no,-14,8) BETWEEN'"
                + startDate + "' AND '" + endDate + "'";
        Cursor cursor = database.rawQuery(ordrQuery, null);
        if (cursor.getCount() > 0) {
            cursor.moveToFirst();
            ordrCount = cursor.getCount();
        }
        String collcQuery = "SELECT COUNT(DISTINCT customer_code)FROM payment_header WHERE SUBSTR(receipt_id,-14,8) BETWEEN'"
                + startDate
                + "' AND '"
                + endDate
                + "'"
                + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14,8) "
                + "BETWEEN'"
                + startDate
                + "' AND '"
                + endDate
                + "'"
                + " GROUP BY customer_code)";
        Cursor cursor1 = database.rawQuery(collcQuery, null);
        if (cursor1.getCount() > 0) {
            cursor1.moveToFirst();
            collcCount = Integer.parseInt(cursor1.getString(0));
        }
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
            String stockQuery = "SELECT COUNT(DISTINCT customer_code) FROM stock_audit WHERE SUBSTR(transaction_id,-14,8) BETWEEN'"
                    + startDate
                    + "' AND '"
                    + endDate
                    + "'"
                    + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14,8) "
                    + "BETWEEN'" + startDate + "' AND '" + endDate + "'" + ")";
            Cursor cursor2 = database.rawQuery(stockQuery, null);
            if (cursor2.getCount() > 0) {
                cursor2.moveToFirst();
                stockCount = Integer.parseInt(cursor2.getString(0));
            }
        }

        if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes")) {
            String Query = "SELECT COUNT(DISTINCT customer_code) FROM yellow_card_details WHERE SUBSTR(yellow_card_no,-14,8) BETWEEN'"
                    + startDate
                    + "' AND '"
                    + endDate
                    + "'"
                    + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14,8) "
                    + "BETWEEN'" + startDate + "' AND '" + endDate + "'" + ")";
            Cursor cursor2 = database.rawQuery(Query, null);
            if (cursor2.getCount() > 0) {
                cursor2.moveToFirst();
                yellowCardCount = Integer.parseInt(cursor2.getString(0));
            }
        }
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
            String Query = "SELECT COUNT(DISTINCT customer_code) FROM check_in_out_details WHERE SUBSTR(trans_id,-14,8) BETWEEN'"
                    + startDate
                    + "' AND '"
                    + endDate
                    + "'"
                    + "AND customer_code NOT IN(SELECT customer_code FROM order_header WHERE  SUBSTR(order_no,-14,8) "
                    + "BETWEEN'" + startDate + "' AND '" + endDate + "'" + ")";
            Cursor cursor2 = database.rawQuery(Query, null);
            if (cursor2.getCount() > 0) {
                cursor2.moveToFirst();
                checkInOutCount = Integer.parseInt(cursor2.getString(0));
            }
        }

        if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes")) {
            String stockQuery = "SELECT COUNT(DISTINCT customer_code) FROM mf_stk_audit_header WHERE SUBSTR(mf_stk_audit_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor2 = database.rawQuery(stockQuery, null);
            if (cursor2.getCount() > 0) {
                cursor2.moveToFirst();
                mfsCount = Integer.parseInt(cursor2.getString(0));
            }
            reportObj.setNoofMFS(String.valueOf(mfsCount));
        }
        if (Constants.menuDetailsObj.getapp_order_approval().equalsIgnoreCase("yes"))
        {
            String stockQuery = "SELECT COUNT(DISTINCT APPORDERNO) FROM T_APPERPDO_APPROVAL WHERE SUBSTR(approval_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor2 = database.rawQuery(stockQuery, null);
            if (cursor2.getCount() > 0) {
                cursor2.moveToFirst();
                orderApprovalCount = Integer.parseInt(cursor2.getString(0));
                reportObj.setorderApproval(orderApprovalCount+"");
            }
        }
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
            String checkinout = "SELECT COUNT(DISTINCT trans_id) FROM check_in_out_details WHERE SUBSTR(trans_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(checkinout, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                checkinoutcust = Integer.parseInt(cursor3.getString(0));
            }
        }
        if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated")) {
            String cashDepositCountQuery = "SELECT COUNT(DISTINCT cash_deposit_trans_id) FROM cash_deposit_details WHERE SUBSTR(cash_deposit_trans_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(cashDepositCountQuery, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                cashDepositCount = Integer.parseInt(cursor3.getString(0));
                reportObj.setnoofCashDeposit(cashDepositCount + "");
            } else {
                reportObj.setnoofCashDeposit("0");
            }
            String cashTransactionCountQuery = "SELECT COUNT(DISTINCT cash_transaction_id) FROM cash_transaction_details WHERE SUBSTR(cash_transaction_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";
            Cursor cursor4 = database.rawQuery(cashTransactionCountQuery, null);
            if (cursor4.getCount() > 0) {
                cursor4.moveToFirst();
                cashTransactionCount = Integer.parseInt(cursor4.getString(0));
                reportObj.setnoofCashTransaction(cashTransactionCount + "");
            } else {
                reportObj.setnoofCashTransaction("0");
            }

            String cashBalanceQuery = "SELECT COUNT(DISTINCT cash_deposit_recv_id) FROM cash_deposit_receive_details WHERE SUBSTR(cash_deposit_recv_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";
            Cursor cursor5 = database.rawQuery(cashBalanceQuery, null);
            if (cursor5.getCount() > 0) {
                cursor5.moveToFirst();
                cashTransactionCount = Integer.parseInt(cursor5.getString(0));
                reportObj.setCashBalance(cashTransactionCount + "");

            } else {
                reportObj.setCashBalance("0");
            }
        }

        if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes")) {
            int survey = 0;
            if (startDate.length() == 8) {
                String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('TT','TTE') or substr(trans_id,1,3)='TTE') AND SUBSTR(trans_id,-14,8) LIKE '" + startDate + "'";
                if(Constants.nickName.equalsIgnoreCase("nimbus"))
                {

                    selectQuery = "SELECT COUNT(DISTINCT tour_exp_trans_id) FROM tour_expenses_details WHERE SUBSTR(tour_exp_trans_id,-14,8) BETWEEN '" + startDate + "' AND '" + endDate + "'";
                }
                Cursor cursor100 = database.rawQuery(selectQuery, null);
                if (cursor100.getCount() > 0) {
                    cursor100.moveToFirst();
                    survey = Integer.parseInt(cursor100.getString(0));
                }
                reportObj.setTour_expense(String.valueOf(survey));
            }
        }

        totalCust = ordrCount + collcCount + stockCount + checkinoutcust + mfsCount + yellowCardCount + cashDepositCount + cashTransactionCount + checkInOutCount+orderApprovalCount;
        reportObj.setNoCustVisitd(String.valueOf(totalCust));

        // Ordr Rcvd
        if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes")) {
            int ordrRcvd = 0;
            String selectQuery1 = "SELECT COUNT( DISTINCT customer_code)FROM order_header WHERE order_no LIKE 'O%'  AND SUBSTR(order_no,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(selectQuery1, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                ordrRcvd = Integer.parseInt(cursor3.getString(0));
            }
            reportObj.setNoOrdrRcvd(String.valueOf(ordrRcvd));
        }

        // Stock Rcvd
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")) {
            int stock = 0;
            String selectQuery1 = "SELECT COUNT( DISTINCT customer_code)FROM stock_audit WHERE transaction_id LIKE 'S%'  AND SUBSTR(transaction_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(selectQuery1, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                stock = Integer.parseInt(cursor3.getString(0));
            }
            reportObj.setNoofStockAudit(String.valueOf(stock));
        }
        if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes")) {
            int ycd = 0;
            String selectQuery1 = "SELECT COUNT( DISTINCT customer_code)FROM yellow_card_details WHERE SUBSTR(yellow_card_no,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(selectQuery1, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                ycd = Integer.parseInt(cursor3.getString(0));
            }
            reportObj.setnoofYellowCardDetails(String.valueOf(ycd));
        }
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes")) {
            int ycd = 0;
            String selectQuery1 = "SELECT COUNT( DISTINCT customer_code)FROM check_in_out_details WHERE SUBSTR(trans_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor3 = database.rawQuery(selectQuery1, null);
            if (cursor3.getCount() > 0) {
                cursor3.moveToFirst();
                ycd = Integer.parseInt(cursor3.getString(0));
            }
            reportObj.setnoofCheckInOut(String.valueOf(ycd));
        }

        // Collc Rcvd
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes")) {
            int collcRcvd = 0;
            String selectQuery2 = "SELECT COUNT( DISTINCT customer_code)FROM payment_header WHERE receipt_id LIKE 'P%'  AND SUBSTR(receipt_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor4 = database.rawQuery(selectQuery2, null);
            if (cursor4.getCount() > 0) {
                cursor4.moveToFirst();
                collcRcvd = Integer.parseInt(cursor4.getString(0));
            }
            reportObj.setNoCollcRcvd(String.valueOf(collcRcvd));
        }


        // NoActvty
        int noTask = 0;
        String selectQuery3 = "SELECT COUNT(trans_id)FROM location WHERE substr(trans_id,1,2) IN('NO','NC','NF') AND SUBSTR(trans_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
        Cursor cursor5 = database.rawQuery(selectQuery3, null);
        if (cursor5.getCount() > 0) {
            cursor5.moveToFirst();
            noTask = Integer.parseInt(cursor5.getString(0));
        }
        reportObj.setNoNoAct(String.valueOf(noTask));

        // New Cust Vstd
        if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) {
            int newCust = 0;
            String selectQuery4 = "SELECT COUNT(trans_id)FROM prospective_customer_header WHERE SUBSTR(trans_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor6 = database.rawQuery(selectQuery4, null);
            if (cursor6.getCount() > 0) {
                cursor6.moveToFirst();
                newCust = Integer.parseInt(cursor6.getString(0));
            }
            reportObj.setNoNewCustVisitd(String.valueOf(newCust));
        }

        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
            int noSauda = 0;
            String selectQuery5 = "SELECT COUNT(DISTINCT customer_code)FROM sauda_header WHERE sauda_no LIKE 'FT%' AND SUBSTR(sauda_no,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor7 = database.rawQuery(selectQuery5, null);
            if (cursor7.getCount() > 0) {
                cursor7.moveToFirst();
                noSauda = Integer.parseInt(cursor7.getString(0));
            }
            reportObj.setSaudaBooking(String.valueOf(noSauda));
        }

        if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes")) {
            int survey = 0;
            String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE (substr(trans_id,1,2) IN('FS','SU') or substr(trans_id,1,3)='SUA') AND SUBSTR(trans_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            if(Constants.nickName.equalsIgnoreCase("nimbus"))
            {
                selectQuery = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE lower(type)='farmer visit' AND row_id='RA003' AND SUBSTR(survey_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
                selectQuery = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE row_id='RA003' AND SUBSTR(survey_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            }
            Cursor cursor8 = database.rawQuery(selectQuery, null);
            if (cursor8.getCount() > 0) {
                cursor8.moveToFirst();
                survey = Integer.parseInt(cursor8.getString(0));
            }
            reportObj.setNoofsurvey(String.valueOf(survey));
        }

        if (Constants.menuDetailsObj.getWholeSaleInfo().equalsIgnoreCase("yes")) {
            int wholesale = 0;
            String selectQuery = "SELECT COUNT(DISTINCT trans_id) FROM location WHERE substr(trans_id,1,1)='W' AND SUBSTR(trans_id,-14,8) BETWEEN'" + startDate + "' AND '" + endDate + "'";
            Cursor cursor8 = database.rawQuery(selectQuery, null);
            if (cursor8.getCount() > 0) {
                cursor8.moveToFirst();
                wholesale = Integer.parseInt(cursor8.getString(0));
            }
            reportObj.setNoofwholesale(String.valueOf(wholesale));
        }


        // Productvty
        int prod = 0;
        int performnc = (totalCust - noTask) * 100;
        prod = (totalCust != 0 ? (performnc / totalCust) : 0);
        reportObj.setProdctvty(String.valueOf(prod));

        return reportObj;
    }

    public int getOrderCountForPrint() {
        int orderCount = 0;
        Cursor cursor = null;
        try {
            String sqlQuery = "SELECT * FROM order_header;";
            cursor = database.rawQuery(sqlQuery, null);
            orderCount = cursor.getCount();
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception::" + e);
            cursor.close();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return orderCount;
    }

    public String getlastDownloadTime(String tableName) {
        String time = "";
        Cursor cursor = null;
        try {
            cursor = database.rawQuery("select last_download_time FROM data_download_log WHERE table_name = '" + tableName + "'", new String[]{});
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                time = cursor.getString(0);
            }
        } catch (SQLException e) {
            Log.e("last update time", e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return time;
    }

    public String getReportRemarks(String initial, String searchKey) {
        String remarks = "";
        String selectQuery = "";
        Cursor cursor = null;
        try {
            if (initial.equalsIgnoreCase("O")) {
                selectQuery = "SELECT d_instruction FROM order_header WHERE order_no = '"
                        + searchKey + "';";
            } else if (initial.equalsIgnoreCase("P")) {
                selectQuery = "SELECT p_remark FROM payment_header WHERE receipt_id = '"
                        + searchKey + "';";
            } else {
                selectQuery = "SELECT remarks FROM prospective_customer_header WHERE trans_id = '"
                        + searchKey + "';";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                remarks = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return remarks;
    }

    public void _insertNOTIFICATION(NotificationDetails detailsList) {
        ContentValues values = new ContentValues();
        values.put("notification_id", detailsList.notificationId);
        values.put("notification_type ", detailsList.notificationType);
        values.put("sender_id ", detailsList.senderId);
        values.put("message", detailsList.message);
        values.put("flag ", 0);
        values.put("ack_id ", detailsList.ackId);
        database.insert("notification_details", null, values);
        database.close();
    }

    public void _updateNOTIFICATION(String str_Notification_ID) {

        SQLiteDatabase myDb = this.getWritableDatabase();
        String last_query = "UPDATE notification_details " + " SET flag" + " = '1' WHERE notification_id" + " = '" + str_Notification_ID + "'";
        Cursor update_Cursor = myDb.rawQuery(last_query, null);
        if (update_Cursor.moveToFirst()) {
            //
        }
        update_Cursor.close();
        myDb.close();
    }

    public void _updateLOCATION(String getTrans_id) {
        SQLiteDatabase myDb = this.getWritableDatabase();

        ContentValues values = new ContentValues();

        values.put("latt", currentLat);
        values.put("longi", currentLong);

        String[] args = new String[]{getTrans_id};
        myDb.update("location", values, "trans_id=?", args);
        myDb.close();
    }


    public ArrayList<NotificationDetails> get_unread_Notification() {
        ArrayList<NotificationDetails> unread_LIST = new ArrayList<NotificationDetails>();
        Cursor cursor = null;
        SQLiteDatabase myDb = this.getWritableDatabase();

        try {
            cursor = myDb.rawQuery("SELECT flag FROM notification_details WHERE flag='" + "0" + "'", null);

            if (cursor.moveToFirst()) {
                do {

                    NotificationDetails info = new NotificationDetails();
                    info.setFlag(cursor.getInt(0));
                    unread_LIST.add(info);

                } while (cursor.moveToNext());
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        myDb.close();
        return unread_LIST;
    }

    public void insertToNotificationDetails(ArrayList<NotificationDetails> detailsList) {
        for (int ii = 0; ii < detailsList.size(); ii++) {
            NotificationDetails detailsObj = detailsList.get(ii);
            try {
                ContentValues cv = new ContentValues();
                cv.put("notification_id", detailsObj.getNotificationId());
                cv.put("notification_type ", detailsObj.getNotificationType());
                cv.put("sender_id ", detailsObj.getSenderId());
                cv.put("message", detailsObj.getMessage());
                cv.put("flag ", 0);
                cv.put("ack_id ", "");
                synchronized (Lock) {
                    database.insertWithOnConflict("notification_details", null,
                            cv, SQLiteDatabase.CONFLICT_IGNORE);
                }
            } catch (Exception e) {
                System.out.println("Exception::::::::" + e);
            }
        }
    }

    public String getNotfId(String transId) {
        String id = "";
        Cursor cursor = null;
        try {
            cursor = database.rawQuery(
                    "select notification_id FROM notification_details WHERE ack_id = '"
                            + transId + "'", new String[]{});
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                id = cursor.getString(0);
            }
        } catch (SQLException e) {
            Log.e("last update time", e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return id;
    }

    public void deleteNotification() {
        database.beginTransaction();
        try {
            database.execSQL("DELETE FROM notification_details");
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("last update time", e.getMessage());
        }
        database.endTransaction();
    }

    public ArrayList<NotificationDetails> getAllNotificationsReceived() {

        ArrayList<NotificationDetails> _notificationList = new ArrayList<NotificationDetails>();
        // Select All Query
        String selectQuery = "SELECT  * FROM notification_details";
//        if(Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("yes"))
//        {
//            selectQuery = "SELECT  * FROM notification_details where substr(notification_id,-14,8)='"+Constants.dateString+"'";
//        }

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                NotificationDetails notfObj = new NotificationDetails();

                notfObj.setNotificationId(cursor.getString(0));
                notfObj.setNotificationType(cursor.getString(1));
                notfObj.setSenderId(cursor.getString(2));
                notfObj.setMessage(cursor.getString(3));
                notfObj.setFlag(cursor.getInt(4));
                notfObj.setAckId(cursor.getString(5));
                // Adding contact to list
                _notificationList.add(notfObj);
            } while (cursor.moveToNext());
        }

        return _notificationList;
    }

    public boolean checkUnuploadedPush() {
        boolean status = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM notification_details WHERE ack_id = ''";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                status = true;
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return status;
    }

    public void closeDatabase() {
        if (database.isOpen()) {
            database.close();
        }
    }

    public void setTransactionSuccessEndTransactionAndCloseDatabase(Boolean isSuccess, Boolean shouldCloseDb) {
//		if(database.inTransaction())
//		{
        if (isSuccess) {
            database.setTransactionSuccessful();
        }

        database.endTransaction();
//		}
        if (database.isOpen() && shouldCloseDb) {
            database.close();
        }
    }

    public void beginTransaction() {
        if (!database.inTransaction()) {
            database.beginTransaction();
        }
    }

    public String[] getReportSummerySales(String criteria) {
        String[] reportSummaryArray = new String[4];
        String purchaseQuery = "", salesQuery = "", stockTransferQuery = "";
        if (criteria.length() == 8) {
            purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria + "'";
            salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria + "'";
            stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria + "'";
        } else {
            purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria + "'";
            salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria + "'";
            stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria + "'";
        }
        Cursor purchaseCursor = database.rawQuery(purchaseQuery, null);
        if (purchaseCursor.getCount() > 0) {
            purchaseCursor.moveToFirst();
            reportSummaryArray[0] = purchaseCursor.getString(0);
        }

        Cursor salesCursor = database.rawQuery(salesQuery, null);
        if (salesCursor.getCount() > 0) {
            salesCursor.moveToFirst();
            reportSummaryArray[1] = salesCursor.getString(0);
        }

        Cursor stockTransferCursor = database
                .rawQuery(stockTransferQuery, null);
        if (stockTransferCursor.getCount() > 0) {
            stockTransferCursor.moveToFirst();
            reportSummaryArray[2] = stockTransferCursor.getString(0);
        }

        return reportSummaryArray;
    }

    public String[] getCustomReportSummerySales(String startDate, String endDate) {
        String[] reportSummaryArray = new String[4];
        String purchaseQuery = "", salesQuery = "", stockTransferQuery = "";
        purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate + "' AND '" + endDate + "'";
        salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate + "' AND '" + endDate + "'";
        stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate + "' AND '" + endDate + "'";

        Cursor purchaseCursor = database.rawQuery(purchaseQuery, null);
        if (purchaseCursor.getCount() > 0) {
            purchaseCursor.moveToFirst();
            reportSummaryArray[0] = purchaseCursor.getString(0);
        }

        Cursor salesCursor = database.rawQuery(salesQuery, null);
        if (salesCursor.getCount() > 0) {
            salesCursor.moveToFirst();
            reportSummaryArray[1] = salesCursor.getString(0);
        }

        Cursor stockTransferCursor = database
                .rawQuery(stockTransferQuery, null);
        if (stockTransferCursor.getCount() > 0) {
            stockTransferCursor.moveToFirst();
            reportSummaryArray[2] = stockTransferCursor.getString(0);
        }

        return reportSummaryArray;
    }

    public ArrayList<ReportData> getSalesReportList(String reportType,
                                                    String criteria) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                } else {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                }
            } else if (reportType.equalsIgnoreCase("SALES")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                } else {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                }
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('ST','CN') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                } else {
                    selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('ST','CN') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' GROUP BY trans_id";
                }
            } else {
                selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportData detailsObj = new ReportData();
                    detailsObj.setCustomerName(cursor.getString(0));
                    detailsObj.setTransId(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    if (reportType.equalsIgnoreCase("PURCHASE")
                            || reportType.equalsIgnoreCase("SALES")
                            || reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                        Date date = new SimpleDateFormat("yyyyMMdd")
                                .parse(cursor.getString(3));
                        detailsObj
                                .setPayMode(new SimpleDateFormat("dd-MM-yyyy")
                                        .format(date));

                    } else {
                        detailsObj.setPayMode("");
                    }
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomSalesReportList(String reportType,
                                                          String startDate, String endDate) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY trans_id";
            } else if (reportType.equalsIgnoreCase("SALES")) {
                selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY trans_id";
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                selectQuery = "SELECT DISTINCT customer_name,trans_id,SUM(amount+VAT),SUBSTR(trans_id,-14,8) FROM transaction_log WHERE trans_type IN('ST','CN') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY trans_id";
            } else {
                selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportData detailsObj = new ReportData();
                    detailsObj.setCustomerName(cursor.getString(0));
                    detailsObj.setTransId(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    if (reportType.equalsIgnoreCase("PURCHASE")
                            || reportType.equalsIgnoreCase("SALES")
                            || reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                        Date date = new SimpleDateFormat("yyyyMMdd")
                                .parse(cursor.getString(3));
                        detailsObj
                                .setPayMode(new SimpleDateFormat("dd-MM-yyyy")
                                        .format(date));

                    } else {
                        detailsObj.setPayMode("");
                    }
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public ArrayList<ReportDetails> getSaleReportDetailsList(String searchKey) {
        ArrayList<ReportDetails> reportList = new ArrayList<ReportDetails>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT TL.sku_name,TL.qty,TL.amount+TL.VAT,TL.TD,LOC.flag FROM transaction_log TL,location LOC WHERE TL.trans_id = LOC.trans_id AND TL.trans_id = '"
                    + searchKey + "';";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportDetails detailsObj = new ReportDetails();
                    detailsObj.setProdCode(cursor.getString(0));
                    detailsObj.setQty(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    detailsObj.setTD(cursor.getString(3));
                    detailsObj.setTransmitted(cursor.getString(4));
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public String getSaleRemarks(String searchKey) {
        String remarks = "";
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT d_instruction FROM transaction_log WHERE trans_id = '"
                    + searchKey + "';";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                remarks = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return remarks;
    }

    public ArrayList<String> getMISListForBranch() {
        ArrayList<String> reportList = new ArrayList<String>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT BM.branch_name,MS.branch_code FROM mis_transaction_log MS,branch_master BM WHERE BM.branch_code = MS.branch_code GROUP BY MS.branch_code ORDER BY BM.branch_name;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    reportList.add(cursor.getString(0) + "*"
                            + cursor.getString(1));
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public ArrayList<String> getMISListGroupWise(String rdsCode) {
        ArrayList<String> reportList = new ArrayList<String>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT DISTINCT PG.product_group_name,PG.product_group_code FROM mis_transaction_log MS,product_group_master PG WHERE PG.product_group_code = MS.group_code AND MS.rds_code IN("
                    + rdsCode + ") GROUP BY PG.product_group_name";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    reportList.add(cursor.getString(0) + "*"
                            + cursor.getString(1));
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public ArrayList<String> getMISListForRDS(String branch_code) {
        ArrayList<String> reportList = new ArrayList<String>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            selectQuery = "SELECT RM.rds_name,MS.rds_code FROM mis_transaction_log MS,rds_master RM WHERE MS.rds_code = RM.rds_code AND MS.branch_code IN ("
                    + branch_code
                    + ") GROUP BY MS.rds_code ORDER BY RM.rds_name;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    reportList.add(cursor.getString(0) + "*"
                            + cursor.getString(1));
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public String[] getMISReportSummerySales(String criteria, String param,
                                             String groupCode) {
        String[] reportSummaryArray = new String[4];
        String purchaseQuery = "", salesQuery = "", stockTransferQuery = "";
        if (criteria.length() == 8) {
            purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
            salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
            stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,8) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
        } else {
            purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
            salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
            stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,6) LIKE '"
                    + criteria
                    + "' AND rds_code IN ("
                    + param
                    + ") AND group_code IN (" + groupCode + ")";
        }
        Cursor purchaseCursor = database.rawQuery(purchaseQuery, null);
        if (purchaseCursor.getCount() > 0) {
            purchaseCursor.moveToFirst();
            reportSummaryArray[0] = purchaseCursor.getString(0);
        }

        Cursor salesCursor = database.rawQuery(salesQuery, null);
        if (salesCursor.getCount() > 0) {
            salesCursor.moveToFirst();
            reportSummaryArray[1] = salesCursor.getString(0);
        }

        Cursor stockTransferCursor = database
                .rawQuery(stockTransferQuery, null);
        if (stockTransferCursor.getCount() > 0) {
            stockTransferCursor.moveToFirst();
            reportSummaryArray[2] = stockTransferCursor.getString(0);
        }

        return reportSummaryArray;
    }

    public String[] getMISCustomReportSummerySales(String startDate,
                                                   String endDate, String param, String groupCode) {
        String[] reportSummaryArray = new String[4];
        String purchaseQuery = "", salesQuery = "", stockTransferQuery = "";
        purchaseQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('BT','PB') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate
                + "' AND '"
                + endDate
                + "' AND rds_code IN ("
                + param + ") AND group_code IN (" + groupCode + ")";
        salesQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('SB','SO') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate
                + "' AND '"
                + endDate
                + "' AND rds_code IN ("
                + param + ") AND group_code IN (" + groupCode + ")";
        stockTransferQuery = "SELECT COUNT(DISTINCT trans_id)FROM mis_transaction_log WHERE trans_type IN ('ST','CN') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                + startDate
                + "' AND '"
                + endDate
                + "' AND rds_code IN ("
                + param + ") AND group_code IN (" + groupCode + ")";

        Cursor purchaseCursor = database.rawQuery(purchaseQuery, null);
        if (purchaseCursor.getCount() > 0) {
            purchaseCursor.moveToFirst();
            reportSummaryArray[0] = purchaseCursor.getString(0);
        }

        Cursor salesCursor = database.rawQuery(salesQuery, null);
        if (salesCursor.getCount() > 0) {
            salesCursor.moveToFirst();
            reportSummaryArray[1] = salesCursor.getString(0);
        }

        Cursor stockTransferCursor = database
                .rawQuery(stockTransferQuery, null);
        if (stockTransferCursor.getCount() > 0) {
            stockTransferCursor.moveToFirst();
            reportSummaryArray[2] = stockTransferCursor.getString(0);
        }

        return reportSummaryArray;
    }

    public ArrayList<LoyaltyCustomerDetails> getLoyaltyCustomerListForReport(
            String mode, String startDate, String endDate) {
        ArrayList<LoyaltyCustomerDetails> detailList = new ArrayList<LoyaltyCustomerDetails>();
        Cursor cursor = null;
        try {

            if (mode.equalsIgnoreCase("TODAY")) {
                cursor = database
                        .rawQuery(
                                "SELECT * FROM loyalty_card_holder_master WHERE loyalty_card_no IN(SELECT loyalty_card_no FROM card_transaction WHERE SUBSTR(transaction_id,-14,8) LIKE '%"
                                        + Constants.dateString + "%')",
                                new String[]{});
            } else if (mode.equalsIgnoreCase("MTD")) {
                cursor = database
                        .rawQuery(
                                "SELECT * FROM loyalty_card_holder_master WHERE loyalty_card_no IN(SELECT loyalty_card_no FROM card_transaction WHERE SUBSTR(transaction_id,-14,8) LIKE '%"
                                        + Constants.dateString.substring(0, 6)
                                        + "%')", new String[]{});
            } else if (mode.equalsIgnoreCase("CUSTOM")) {
                cursor = database
                        .rawQuery(
                                "SELECT * FROM loyalty_card_holder_master WHERE loyalty_card_no IN(SELECT loyalty_card_no FROM card_transaction WHERE SUBSTR(transaction_id,-14,8) BETWEEN'"
                                        + startDate
                                        + "' AND '"
                                        + endDate
                                        + "')", new String[]{});
            }
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    LoyaltyCustomerDetails detailsObj = new LoyaltyCustomerDetails();
                    detailsObj.setCardHolderCode(cursor.getString(0));
                    detailsObj.setCardHolderName(cursor.getString(1));
                    detailsObj.setCardNumber(cursor.getString(2));
                    detailsObj.setCardType(cursor.getString(3));
                    detailsObj.setPurchaseValue(cursor.getString(4));
                    detailsObj.setRewardPoint(cursor.getString(5));
                    detailsObj.setLastUpdate(cursor.getString(6));
                    detailsObj.setRedeemed(cursor.getString(7));
                    detailsObj.setPhone(cursor.getString(8));
                    detailsObj.setAddress(cursor.getString(9));
                    detailsObj.setVehicleNo(cursor.getString(10));
                    detailsObj.setCardExpDate(cursor.getString(11));
                    detailList.add(detailsObj);
                    cursor.moveToNext();
                }
                cursor.close();
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailList;
    }

    public LoyaltyPurchaseDetails getLoyaltyPurchaseDetailsForReport(
            String cardNo, String mode, String startDate, String endDate) {
        LoyaltyPurchaseDetails detailsObj = null;
        Cursor cursor = null;
        try {
            if (mode.equalsIgnoreCase("TODAY")) {
                cursor = database
                        .rawQuery(
                                "SELECT loyalty_card_no,'',SUM(purchase_value),SUM(points_earned),SUM(points_redeemed) FROM card_transaction WHERE loyalty_card_no = '"
                                        + cardNo
                                        + "' AND SUBSTR(transaction_id,-14,8) LIKE '%"
                                        + Constants.dateString + "%'",
                                new String[]{});
            } else if (mode.equalsIgnoreCase("MTD")) {
                cursor = database
                        .rawQuery(
                                "SELECT loyalty_card_no,'',SUM(purchase_value),SUM(points_earned),SUM(points_redeemed) FROM card_transaction WHERE loyalty_card_no = '"
                                        + cardNo
                                        + "' AND SUBSTR(transaction_id,-14,8) LIKE '%"
                                        + Constants.dateString.substring(0, 6)
                                        + "%'", new String[]{});
            } else if (mode.equalsIgnoreCase("CUSTOM")) {
                cursor = database
                        .rawQuery(
                                "SELECT loyalty_card_no,'',SUM(purchase_value),SUM(points_earned),SUM(points_redeemed) FROM card_transaction WHERE loyalty_card_no = '"
                                        + cardNo
                                        + "' AND SUBSTR(transaction_id,-14,8) BETWEEN'"
                                        + startDate + "' AND '" + endDate + "'",
                                new String[]{});
            }

            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    detailsObj = new LoyaltyPurchaseDetails();
                    detailsObj.setLoyaltyCardNo(cursor.getString(0));
                    detailsObj.setVerticalName(cursor.getString(1));
                    detailsObj.setPurchaseValue(cursor.getString(2));
                    detailsObj.setRwrdPoint(cursor.getString(3));
                    detailsObj.setRdmdPoint(cursor.getString(4));
                }
                cursor.close();
                return detailsObj;
            }
        } catch (Exception e) {
            System.out.println(e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return detailsObj;
    }

    public ArrayList<ReportData> getMISReportList(String reportType,
                                                  String criteria, String param, String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                } else {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ")  AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                }
            } else if (reportType.equalsIgnoreCase("SALES")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('SB','SO') AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                } else {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                }
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                if (criteria.length() == 8) {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                } else {
                    selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ")  AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                            + criteria
                            + "%' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
                }
            } else {
                selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
            }
            Log.d("TAG", "_DOWNLOAD_ product_master: " + selectQuery);
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportData detailsObj = new ReportData();
                    detailsObj.setCustomerName(cursor.getString(0));
                    detailsObj.setTransId(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    detailsObj.setRdsName(cursor.getString(3));
                    detailsObj.setTransAmt(cursor.getString(4));
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomMISReportList(String reportType,
                                                        String startDate, String endDate, String param, String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('PB','BT')  AND MS.rds_code IN ("
                        + param
                        + ") AND MS.group_code IN ("
                        + groupCode
                        + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY MS.sku_code,MS.rds_code  ORDER BY RDS.rds_name";
            } else if (reportType.equalsIgnoreCase("SALES")) {
                selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                        + param
                        + ") AND MS.group_code IN ("
                        + groupCode
                        + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                selectQuery = "SELECT DISTINCT PM.prod_desc,MS.sku_code,SUM(MS.qty),RDS.rds_name,SUM(MS.amount+MS.VAT) FROM rds_master RDS,mis_transaction_log MS,product_master PM WHERE MS.rds_code = RDS.rds_code AND PM.prod_code = MS.sku_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                        + param
                        + ") AND MS.group_code IN ("
                        + groupCode
                        + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' GROUP BY MS.sku_code,MS.rds_code ORDER BY RDS.rds_name";
            } else {
                selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
            }
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportData detailsObj = new ReportData();
                    detailsObj.setCustomerName(cursor.getString(0));
                    detailsObj.setTransId(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    detailsObj.setRdsName(cursor.getString(3));
                    detailsObj.setTransAmt(cursor.getString(4));
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getMISReportListGroupWise(String reportType,
                                                           String criteria, String param, String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (!reportType.equalsIgnoreCase("STOCK")) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    } else {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    }
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('SB','SO') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    } else {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    }
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    } else {
                        selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria + "%' GROUP BY MS.group_code";
                    }
                } else {
                    selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
                }
                Log.d("TAG", "_DOWNLOAD_ product_master: " + selectQuery);
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setRdsName(cursor.getString(3));
                        detailsObj.setTransAmt(cursor.getString(4));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                String[] grpArray = groupCode.split(",");
                if (grpArray.length > 0) {
                    for (int i = 0; i < grpArray.length; i++) {
                        String grpCode = grpArray[i];
                        String childQuery1 = "SELECT (SELECT SUM(qty) FROM mis_transaction_log WHERE trans_type IN('PB','BT') AND rds_code IN ("
                                + param + ") AND group_code =" + grpCode + ")";
                        String childQuery2 = "SELECT (SELECT SUM(qty) FROM mis_transaction_log WHERE trans_type IN('SB','ST','SH') AND rds_code IN ("
                                + param + ") AND group_code =" + grpCode + ")";
                        String childQuery3 = "SELECT product_group_name FROM product_group_master WHERE product_group_code = "
                                + grpCode + "";
                        Cursor cursor1 = database.rawQuery(childQuery1, null);
                        Cursor cursor2 = database.rawQuery(childQuery2, null);
                        Cursor cursor3 = database.rawQuery(childQuery3, null);
                        Double val1 = 0.00, val2 = 0.00;
                        if (cursor1.getCount() > 0) {
                            cursor1.moveToFirst();
                            try {
                                val1 = Double.parseDouble(cursor1.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        if (cursor2.getCount() > 0) {
                            cursor2.moveToFirst();
                            try {
                                val2 = Double.parseDouble(cursor2.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        cursor3.moveToFirst();
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor3.getString(0));
                        detailsObj.setAmount(String.valueOf(val1 - val2));
                        detailsObj.setTransId(grpCode);
                        reportList.add(detailsObj);
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomMISReportListGroupWise(
            String reportType, String startDate, String endDate, String param,
            String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (!reportType.equalsIgnoreCase("STOCK")) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('PB','BT')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.group_code";
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.group_code";
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    selectQuery = "SELECT DISTINCT PGM.product_group_name,MS.group_code,SUM(MS.qty),'      ',SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,product_group_master PGM WHERE PGM.product_group_code = MS.group_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.group_code";
                } else {
                    selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        detailsObj.setRdsName(cursor.getString(3));
                        detailsObj.setTransAmt(cursor.getString(4));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                String[] grpArray = groupCode.split(",");
                if (grpArray.length > 0) {
                    for (int i = 0; i < grpArray.length; i++) {
                        String grpCode = grpArray[i];
                        String childQuery1 = "SELECT SUM(qty) FROM mis_transaction_log WHERE trans_type IN('PB','BT') AND rds_code IN ("
                                + param
                                + ") AND group_code ="
                                + grpCode
                                + " AND SUBSTR(trans_id,-14,8) < '"
                                + endDate
                                + "'";
                        String childQuery2 = "SELECT SUM(qty) FROM mis_transaction_log WHERE trans_type IN('SB','ST','SH') AND rds_code IN ("
                                + param
                                + ") AND group_code ="
                                + grpCode
                                + " AND SUBSTR(trans_id,-14,8) < '"
                                + endDate
                                + "'";
                        String childQuery3 = "SELECT product_group_name FROM product_group_master WHERE product_group_code = "
                                + grpCode + "";
                        Cursor cursor1 = database.rawQuery(childQuery1, null);
                        Cursor cursor2 = database.rawQuery(childQuery2, null);
                        Cursor cursor3 = database.rawQuery(childQuery3, null);
                        Double val1 = 0.00, val2 = 0.00;
                        if (cursor1.getCount() > 0) {
                            cursor1.moveToFirst();
                            try {
                                val1 = Double.parseDouble(cursor1.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        if (cursor2.getCount() > 0) {
                            cursor2.moveToFirst();
                            try {
                                val2 = Double.parseDouble(cursor2.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        cursor3.moveToFirst();
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor3.getString(0));
                        detailsObj.setAmount(String.valueOf(val1 - val2));
                        detailsObj.setTransId(grpCode);
                        reportList.add(detailsObj);
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getMISReportListGroupRDSWise(
            String reportType, String criteria, String param, String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (!reportType.equalsIgnoreCase("STOCK")) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                    } else {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND MS.trans_type IN('PB','BT') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                    }
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND  MS.trans_type IN('SB','SO') AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                    } else {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND  MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ") AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                    }
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    if (criteria.length() == 8) {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),MS.customer_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM WHERE RM.rds_code = MS.rds_code AND  MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,8) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY RM.rds_name";
                    } else {
                        selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),MS.customer_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM WHERE RM.rds_code = MS.rds_code AND  MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                                + param
                                + ") AND MS.group_code IN ("
                                + groupCode
                                + ")  AND SUBSTR(MS.trans_id,-14,6) LIKE '%"
                                + criteria
                                + "%' GROUP BY MS.rds_code ORDER BY RM.rds_name";
                    }
                } else {
                    selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        if (!reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                            detailsObj.setRdsName("(" + cursor.getString(3)
                                    + ")");
                        } else {
                            detailsObj.setRdsName("To " + cursor.getString(3));
                        }
                        detailsObj.setTransAmt(cursor.getString(4));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                selectQuery = "SELECT DISTINCT sku_code FROM mis_transaction_log WHERE rds_code IN ("
                        + param + ") AND group_code =" + groupCode + "";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        String prodCode = cursor.getString(0);
                        String childQuery1 = "SELECT (SELECT SUM(qty) FROM mis_transaction_log WHERE sku_code = '"
                                + prodCode
                                + "' AND trans_type IN('PB','BT') AND rds_code IN ("
                                + param + "))";
                        String childQuery2 = "SELECT (SELECT SUM(qty) FROM mis_transaction_log WHERE sku_code = '"
                                + prodCode
                                + "' AND trans_type IN('SB','ST','SH') AND rds_code IN ("
                                + param + "))";
                        String childQuery3 = "SELECT prod_desc FROM product_master WHERE prod_code = '"
                                + prodCode + "'";
                        Cursor cursor1 = database.rawQuery(childQuery1, null);
                        Cursor cursor2 = database.rawQuery(childQuery2, null);
                        Cursor cursor3 = database.rawQuery(childQuery3, null);
                        Double val1 = 0.00, val2 = 0.00;
                        if (cursor1.getCount() > 0) {
                            cursor1.moveToFirst();
                            try {
                                val1 = Double.parseDouble(cursor1.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        if (cursor2.getCount() > 0) {
                            cursor2.moveToFirst();
                            try {
                                val2 = Double.parseDouble(cursor2.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        cursor3.moveToFirst();
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor3.getString(0));
                        detailsObj.setAmount(String.valueOf(val1 - val2));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportData> getCustomMISReportListGroupRDSWise(
            String reportType, String startDate, String endDate, String param,
            String groupCode) {
        ArrayList<ReportData> reportList = new ArrayList<ReportData>();
        String selectQuery = "";
        try {
            if (!reportType.equalsIgnoreCase("STOCK")) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND  MS.trans_type IN('PB','BT')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),BM.branch_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM,branch_master BM WHERE RM.rds_code = MS.rds_code AND MS.branch_code = BM.branch_code AND  MS.trans_type IN('SB','SO')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.rds_code ORDER BY BM.branch_name";
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    selectQuery = "SELECT DISTINCT RM.rds_name,MS.rds_code,SUM(MS.qty),MS.customer_name,SUM(MS.amount+MS.VAT) FROM mis_transaction_log MS,rds_master RM WHERE RM.rds_code = MS.rds_code AND MS.trans_type IN('ST','CN')  AND MS.rds_code IN ("
                            + param
                            + ") AND MS.group_code IN ("
                            + groupCode
                            + ") AND SUBSTR(MS.trans_id,-14,8) BETWEEN'"
                            + startDate
                            + "' AND '"
                            + endDate
                            + "' GROUP BY MS.rds_code ORDER BY RM.rds_name";
                } else {
                    selectQuery = "SELECT PM.prod_desc,'0',CS.cl_stk,'','' FROM product_master PM,closing_stock CS WHERE pm.prod_code = CS.prod_code AND CS.cl_stk > 0";
                }
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor.getString(0));
                        detailsObj.setTransId(cursor.getString(1));
                        detailsObj.setAmount(cursor.getString(2));
                        if (!reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                            detailsObj.setRdsName("(" + cursor.getString(3)
                                    + ")");
                        } else {
                            detailsObj.setRdsName("To " + cursor.getString(3));
                        }

                        detailsObj.setTransAmt(cursor.getString(4));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
                cursor.close();
            } else {
                selectQuery = "SELECT DISTINCT sku_code FROM mis_transaction_log WHERE rds_code IN ("
                        + param + ") AND group_code =" + groupCode + "";
                Cursor cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        String prodCode = cursor.getString(0);
                        String childQuery1 = "SELECT SUM(qty) FROM mis_transaction_log WHERE sku_code = '"
                                + prodCode
                                + "' AND trans_type IN('PB','BT') AND rds_code IN ("
                                + param
                                + ") AND SUBSTR(trans_id,-14,8) < '"
                                + endDate + "'";
                        String childQuery2 = "SELECT SUM(qty) FROM mis_transaction_log WHERE sku_code = '"
                                + prodCode
                                + "' AND trans_type IN('SB','ST','SH') AND rds_code IN ("
                                + param
                                + ") AND SUBSTR(trans_id,-14,8) < '"
                                + endDate + "'";
                        String childQuery3 = "SELECT prod_desc FROM product_master WHERE prod_code = '"
                                + prodCode + "'";
                                Log.d("TAG", "_DOWNLOAD_ product_master: " + childQuery3);
                        Cursor cursor1 = database.rawQuery(childQuery1, null);
                        Cursor cursor2 = database.rawQuery(childQuery2, null);
                        Cursor cursor3 = database.rawQuery(childQuery3, null);
                        Double val1 = 0.00, val2 = 0.00;
                        if (cursor1.getCount() > 0) {
                            cursor1.moveToFirst();
                            try {
                                val1 = Double.parseDouble(cursor1.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        if (cursor2.getCount() > 0) {
                            cursor2.moveToFirst();
                            try {
                                val2 = Double.parseDouble(cursor2.getString(0));
                            } catch (Exception e) {

                            }
                        }
                        cursor3.moveToFirst();
                        ReportData detailsObj = new ReportData();
                        detailsObj.setCustomerName(cursor3.getString(0));
                        detailsObj.setAmount(String.valueOf(val1 - val2));
                        reportList.add(detailsObj);
                        cursor.moveToNext();
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportDetails> getMISReportDetailsList(String searchKey,
                                                            String param, String reportType, String criteria) {
        ArrayList<ReportDetails> reportList = new ArrayList<ReportDetails>();
        String selectQuery = "";
        try {
            if (criteria.length() == 8) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT, d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('ST','CN') AND SUBSTR(trans_id,-14,8) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                }
            } else if (criteria.length() == 6) {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT, d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                            + searchKey
                            + "' AND rds_code IN ("
                            + param
                            + ") AND trans_type IN('ST','CN') AND SUBSTR(trans_id,-14,6) LIKE '%"
                            + criteria + "%' ORDER BY trans_date DESC;";
                }
            }
            Cursor cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportDetails detailsObj = new ReportDetails();
                    detailsObj.setProdCode(cursor.getString(0));
                    detailsObj.setQty(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    detailsObj.setTD(cursor.getString(3));
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }
        return reportList;
    }

    public ArrayList<ReportDetails> getCustomMISReportDetailsList(
            String searchKey, String param, String reportType,
            String startDate, String endDate) {
        ArrayList<ReportDetails> reportList = new ArrayList<ReportDetails>();
        String selectQuery = "";
        Cursor cursor = null;
        try {
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                selectQuery = "SELECT customer_name,qty,amount+VAT, d_instruction FROM mis_transaction_log WHERE sku_code = '"
                        + searchKey
                        + "' AND rds_code IN ("
                        + param
                        + ") AND trans_type IN('PB','BT') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' ORDER BY trans_date DESC;";
            } else if (reportType.equalsIgnoreCase("SALES")) {
                selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                        + searchKey
                        + "' AND rds_code IN ("
                        + param
                        + ") AND trans_type IN('SB','SO') AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' ORDER BY trans_date DESC;";
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                selectQuery = "SELECT customer_name,qty,amount+VAT,d_instruction FROM mis_transaction_log WHERE sku_code = '"
                        + searchKey
                        + "' AND rds_code IN ("
                        + param
                        + ") AND trans_type IN('ST','CN')  AND SUBSTR(trans_id,-14,8) BETWEEN'"
                        + startDate
                        + "' AND '"
                        + endDate
                        + "' ORDER BY trans_date DESC;";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    ReportDetails detailsObj = new ReportDetails();
                    detailsObj.setProdCode(cursor.getString(0));
                    detailsObj.setQty(cursor.getString(1));
                    detailsObj.setAmount(cursor.getString(2));
                    detailsObj.setTD(cursor.getString(3));
                    reportList.add(detailsObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return reportList;
    }

    public void InsertRedudantTransaction(String rid, String fsid, String mallid, String businessname, String status) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("redundant_trans_id", rid);
            cv.put("foot_soldier_id", fsid);
            cv.put("mall_id", mallid);
            cv.put("business_name", businessname);
            cv.put("status", status);
            cv.put("flag", 0);
            synchronized (Lock) {
                database.insertWithOnConflict("redundant_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("redundant_transaction:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("foot_soldier", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }


    public void InsertFootSoldier(String trans_id, String mallid, String mallname, String pincode, String businessname, String type, String menutype) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("foot_soldier_id", trans_id);
            cv.put("mall_id", mallid);
            cv.put("mall_name", mallname);
            cv.put("pincode", pincode);
            cv.put("business_name", businessname);
            cv.put("type", type);
            cv.put("menu_type", menutype);
            synchronized (Lock) {
                database.insertWithOnConflict("foot_soldier", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("foot_soldier:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("foot_soldier", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public void InsertFsSurveyPublish(String trans_id, String mallid, String mallname, String pincode, String businessname, String type) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("fs_survey_id", trans_id);
            cv.put("mall_id", mallid);
            cv.put("mall_name", mallname);
            cv.put("pincode", pincode);
            cv.put("business_name", businessname);
            cv.put("type", type);
            cv.put("DCE_status", "NOT DONE");
            synchronized (Lock) {
                database.insertWithOnConflict("fs_survey_publish", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
                Log.d("fs_survey_publish:", "Data Inserted");
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("foot_soldier", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }


    public void InsertNotesInfo(String trans_id, String feedback, String remarks, String uploaded_photo) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("trans_id", trans_id);
            cv.put("feedback", feedback);
            cv.put("hint_remarks", remarks);
            cv.put("uploaded_photo", uploaded_photo);
            synchronized (Lock) {
                database.insertWithOnConflict("notes_info", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Notes Info", e.getMessage());
        } finally {
            database.endTransaction();
        }
    }


    public void insertCheckInOut(String trans_id, String check_in_time, String customer_code, String check_out_time, String remarks, String hint, String product_tagging, String attachmentIdSemecolonSeparated)
    {
        setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("trans_id", trans_id);
            cv.put("check_in_time", check_in_time);
            cv.put("customer_code", customer_code);
            cv.put("check_out_time", check_out_time);
            cv.put("remarks", remarks);

            cv.put("hint_remarks", hint);
            cv.put("product_tagging", product_tagging);
            cv.put("uploaded_photo", attachmentIdSemecolonSeparated);
            synchronized (Lock) {
                database.insertWithOnConflict("check_in_out_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<Location> GetUnuploadedLocationofFootSoldier() {
        ArrayList<Location> unUploadedCheckInOutList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='FS'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedCheckInOutList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedCheckInOutList;
    }

    public ArrayList<Location> GetUnuploadedLocationofRedundant() {
        ArrayList<Location> unUploadedCheckInOutList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='RT'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedCheckInOutList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedCheckInOutList;
    }

    public ArrayList<Location> GetUnuploadedLocationofNotesInfo() {
        ArrayList<Location> unUploadedCheckInOutList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='NI'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedCheckInOutList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedCheckInOutList;
    }

    public ArrayList<Location> GetUnuploadedLocationofCheckInOut() {
        ArrayList<Location> unUploadedCheckInOutList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM location where flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='CI'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedCheckInOutList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedCheckInOutList;
    }

    public void UpdateCheckInOutLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='CI'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateNotesInfoData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='NI'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out
                .println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateFootSoldierLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='FS'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Location Update status ::::::::::::" + updateResult);
    }

    public void UpdateRedundantLocationData() {
        database.beginTransaction();
        int updateResult = -1;
        String sql = "UPDATE  location SET flag='1' WHERE flag ='0' AND substr(trans_id,1,2)  NOT  IN('NO','NC','NF','NS') AND substr(trans_id,1,2)='RT'";
        try {
            database.execSQL(sql);
            database.setTransactionSuccessful();
        } catch (SQLException e) {
            Log.e("Location Update", e.getMessage());
        } finally {
            database.endTransaction();
        }
        System.out.println("Location Update status ::::::::::::" + updateResult);
    }

    public ArrayList<CheckInOut> getUnuploadedCheckInOut(String trans_id) {
        ArrayList<CheckInOut> checkInOutList = new ArrayList<CheckInOut>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM check_in_out_details WHERE trans_id = '" + trans_id + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CheckInOut checkoutObj = new CheckInOut();
                    checkoutObj.setTrans_id(cursor.getString(0));
                    checkoutObj.setCheck_in_time(cursor.getString(1));
                    checkoutObj.setCustomer_code(cursor.getString(2));
                    checkoutObj.setCheck_out_time(cursor.getString(3));
                    checkoutObj.setRemark(cursor.getString(4));
                    checkoutObj.setHint(cursor.getString(5));
                    checkoutObj.setTaggedProduct(cursor.getString(6));
                    checkoutObj.setnotesInfoPicture(cursor.getString(7));

                    checkInOutList.add(checkoutObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return checkInOutList;
    }

    public ArrayList<CheckInOut> GetUnuploadedNotesandInfo(String trans_id) {
        ArrayList<CheckInOut> checkInOutList = new ArrayList<CheckInOut>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM notes_info WHERE trans_id = '" + trans_id + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CheckInOut checkoutObj = new CheckInOut();
                    checkoutObj.setTrans_id(cursor.getString(0));
                    checkoutObj.setRemark(cursor.getString(1));
                    checkoutObj.setnotesInfoRemarks(cursor.getString(2));
                    checkoutObj.setnotesInfoPicture(cursor.getString(3));
                    checkInOutList.add(checkoutObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return checkInOutList;
    }

    public ArrayList<MallMaster> getUnuploadedFootSoldier(String trans_id) {
        ArrayList<MallMaster> mallMasterList = new ArrayList<MallMaster>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM foot_soldier WHERE foot_soldier_id = '" + trans_id + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    MallMaster obj = new MallMaster();
                    obj.setFsId(cursor.getString(0));
                    obj.setMallId(cursor.getString(1));
                    obj.setMallName(cursor.getString(2));
                    obj.setPincode(cursor.getString(3));
                    obj.setArea(cursor.getString(4));
                    obj.setType(cursor.getString(5));
                    obj.setMarket(cursor.getString(6));
                    mallMasterList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mallMasterList;
    }

    public ArrayList<MallMaster> getUnuploadedRedundant(String trans_id) {
        ArrayList<MallMaster> mallMasterList = new ArrayList<MallMaster>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM redundant_transaction WHERE redundant_trans_id = '" + trans_id + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    MallMaster obj = new MallMaster();
                    obj.setRedudantTransId(cursor.getString(0));
                    obj.setFsId(cursor.getString(1));
                    obj.setMallId(cursor.getString(2));
                    obj.setArea(cursor.getString(3));
                    obj.setStatus(cursor.getString(4));
                    mallMasterList.add(obj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return mallMasterList;
    }


    public void updateCustomerEmailPhone(HashMap<String, String> customerCodeEmailPhone) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("email", customerCodeEmailPhone.get("email"));
            cv.put("phone_no", customerCodeEmailPhone.get("phone"));
            synchronized (Lock) {
                database.update("customer_master", cv, "customer_code=?",
                        new String[]{customerCodeEmailPhone.get("code")});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public void updateCustomerSaudaLimit(String saudaLimit) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("sauda_limit", saudaLimit);
            synchronized (Lock) {
                database.update("customer_master", cv, "customer_code=?",
                        new String[]{Constants.selectedCustomer.getCustomerCode()});
            }
            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<String> getcurrentCustomerLatLong(String customer_code)
    {
        ArrayList<String> custLatLong=new ArrayList<>();
        Cursor cursor=null;
        try
        {
            String sql="SELECT base_latt, base_longi FROM customer_master where customer_code ='"+customer_code+"'";
            cursor = database.rawQuery(sql, null);
            if (cursor.getCount() > 0)
            {
                cursor.moveToFirst();
                custLatLong.add(cursor.getString(0));
                custLatLong.add(cursor.getString(1));
            }
            cursor.close();
        }
        catch (Exception e)
        {
        } finally
        {
            if (cursor != null)
            {
                cursor.close();
            }
        }
        return custLatLong;
    }


    public String getAppOrderApprovalAdditional() {
        String approval = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT app_order_approval_additional FROM menu_details;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                approval = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return approval;

    }

    public String getWeightageValue() {
        String approval = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT *FROM state_product_wise_weightage;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                approval = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return approval;

    }

    public String getILSRemark(String dCode) {
        String remark = "",otherRemark = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT *from doctor_visit_details where customer_code='"+dCode+"' COLLATE NOCASE;;";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                remark = cursor.getString(2);
                Constants.ilsRemarkOther = cursor.getString(3);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return remark;

    }

    public void UpdateOrderStockCustomer(OrderStatus obj) {

        String stok = "";
        Cursor cursor = null;

        try {
            String selectQuery = "SELECT stock FROM customer_product_stock where customer_code='"+obj.getCustomerCode()+"' and prod_code='"+obj.getProductCode()+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                stok = cursor.getString(0);
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception:::::" + e.getMessage());
        }

        database.beginTransaction();
        try {
            String deliveredQuantity = obj.getOrderQuantity();
            String currentDeliveredQuantity = obj.getCurrentDeliveredQuantity();
            double deliveredQuantityInDouble = 0.0;
            double currentDeliveredQuantityInDouble = 0.0;
            double QuantityInDouble = 0.0;
            if (Utils.isNumeric(deliveredQuantity)) {
                deliveredQuantityInDouble = Double.parseDouble(deliveredQuantity);
            }
            if (Utils.isNumeric(currentDeliveredQuantity)) {
                currentDeliveredQuantityInDouble = Double.parseDouble(currentDeliveredQuantity);
            }
            if (Utils.isNumeric(stok)) {
                QuantityInDouble = Double.parseDouble(stok);
            }
            Double totalDeliveredQuantity = deliveredQuantityInDouble - currentDeliveredQuantityInDouble;
            QuantityInDouble = QuantityInDouble + totalDeliveredQuantity;
            /*database.execSQL("UPDATE customer_product_stock SET stock ='"
                    + Constants.defaultFormat.format(QuantityInDouble) + "' WHERE customer_code = '"
                    + obj.getCustomerCode() + "' AND prod_code='" + obj.getProductCode() + "'");*/
            database.execSQL("UPDATE customer_product_stock SET stock ='"
                    + Constants.defaultFormat.format(QuantityInDouble) + "' WHERE customer_code =(SELECT rds_tag FROM customer_master WHERE  customer_code='"
                            + obj.getCustomerCode() + "') AND prod_code='" + obj.getProductCode() + "'");

            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }


    public void UpdateOrderStockCustomerAddToCart(String prod_code,String stok) {

        String stock = "";

        database.beginTransaction();
        try {
            database.execSQL("UPDATE customer_product_stock SET stock ='"
                    + stok + "' WHERE customer_code =(SELECT rds_tag FROM customer_master WHERE  customer_code='"
                    + Constants.selectedCustomer.getCustomerCode() + "') AND prod_code='" + prod_code + "'");

            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public boolean AddStokistVisit(String timeStamp,String scod,String ccod,String sale,String folder,String prod_code) {

        Boolean isInsertionDone = true;
        String stock = "";
        String order_no = "";
        int transferred = 0, flag = 0;
        order_no = "SV" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("visit_trans_id", order_no);
            cv.put("stockist_code", ccod);
            cv.put("customer_code", scod);
            cv.put("sale", sale);
            cv.put("folder", folder);
            cv.put("prod_code", prod_code);

            database.insertWithOnConflict("stockist_visit", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("stockist_visit:", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public boolean AddTentFormProduct(String timeStamp,String product,String brand,String life,String mobile_no) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "TF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("tent_form_id", order_no);
            cv.put("product", product);
            cv.put("brand", brand);
            cv.put("life_of_product", life);
            cv.put("mobile_no", mobile_no);

            database.insertWithOnConflict("tent_form_product_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("tent_form_product", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public boolean AddTentFormDetails(String timeStamp,String starting_date_time,String end_date_time,String starting_latt,String starting_longi,String end_latt,String end_logi,String starting_image,String end_image,String customer_name,String mobile_no,String interested_for_demo,String demo_tentative_date_time,String remarks) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "TF" + Constants.employeeDetailObject.getEmpCode() + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("tent_form_id", order_no);
            cv.put("starting_date_time", starting_date_time);
            cv.put("end_date_time", end_date_time);
            cv.put("starting_latt", starting_latt);
            cv.put("starting_longi", starting_longi);
            cv.put("end_latt", end_latt);
            cv.put("end_logi", end_logi);
            cv.put("starting_image", starting_image);
            cv.put("end_image", end_image);
            cv.put("customer_name", customer_name);
            cv.put("mobile_no", mobile_no);
            cv.put("interested_for_demo", interested_for_demo);
            cv.put("demo_tentative_date_time", demo_tentative_date_time);
            cv.put("remarks", remarks);

            database.insertWithOnConflict("tent_form_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("tent_form_details", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public boolean AddTentFormCust(String timeStamp,String customer_address,String customer_other_details,String product_type, String mobile_no) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "TF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("tent_form_id", order_no);
            cv.put("customer_address", customer_address );
            cv.put("customer_other_details", customer_other_details);
            cv.put("product_type", product_type );
            cv.put("mobile_no", mobile_no );

            database.insertWithOnConflict("tent_form_customer_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("tent_form_customer", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }


    public ArrayList<TentFormDetails> getUnuploadedTentForm(String transId) {
        ArrayList<TentFormDetails> unUploadedList = new ArrayList<TentFormDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tent_form_details where tent_form_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentFormDetails detailObj = new TentFormDetails();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setStarting_date_time(cursor.getString(1));
                    detailObj.setEnd_date_time(cursor.getString(2));
                    detailObj.setStarting_latt(cursor.getString(3));
                    detailObj.setStarting_longi(cursor.getString(4));
                    detailObj.setEnd_latt(cursor.getString(5));
                    detailObj.setEnd_logi(cursor.getString(6));
                    detailObj.setStarting_image(cursor.getString(7));
                    detailObj.setEnd_image(cursor.getString(8));
                    detailObj.setCustomer_name(cursor.getString(9));
                    detailObj.setMobile_no(cursor.getString(10));
                    detailObj.setInterested_for_demo(cursor.getString(11));
                    detailObj.setDemo_tentative_date_time(cursor.getString(12));
                    detailObj.setRemarks(cursor.getString(13));


                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<TentProduct> getUnuploadedTentFormProduct(String transId,String mobile_no) {
        ArrayList<TentProduct> unUploadedList = new ArrayList<TentProduct>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tent_form_product_details where tent_form_id = '"
                    + transId + "' AND mobile_no = '"+mobile_no+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentProduct detailObj = new TentProduct();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setProduct(cursor.getString(1));
                    detailObj.setBrand(cursor.getString(2));
                    detailObj.setLife_of_product(cursor.getString(3));
                    detailObj.setMobile_no(cursor.getString(4));

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<TentProduct> getUnuploadedKnockingFormProduct(String transId,String mobile) {
        ArrayList<TentProduct> unUploadedList = new ArrayList<TentProduct>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM knocking_form_product_details where knocking_form_id = '"
                    + transId + "' AND mobile_no='"+mobile+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentProduct detailObj = new TentProduct();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setProduct(cursor.getString(1));
                    detailObj.setBrand(cursor.getString(2));
                    detailObj.setLife_of_product(cursor.getString(3));
                    detailObj.setMobile_no(cursor.getString(4));

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<TentCust> getUnuploadedTentFormCust(String transId, String mobile_no) {
        ArrayList<TentCust> unUploadedList = new ArrayList<TentCust>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tent_form_customer_details where tent_form_id = '"
                    + transId + "' AND mobile_no = '"+mobile_no+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentCust detailObj = new TentCust();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setCustomer_address(cursor.getString(1));
                    detailObj.setCustomer_alternate_phone_no(cursor.getString(2));
                    detailObj.setProduct_type(cursor.getString(3));
                    detailObj.setMobile_no(cursor.getString(4));
                    detailObj.setCustomer_other_details("");

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<TentCust> getUnuploadedKnockingFormCust(String transId , String mobile) {
        ArrayList<TentCust> unUploadedList = new ArrayList<TentCust>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM knocking_form_customer_details where knocking_form_id = '"
                    + transId + "' AND mobile_no='"+mobile+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentCust detailObj = new TentCust();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setCustomer_address(cursor.getString(1));
                    detailObj.setCustomer_alternate_phone_no(cursor.getString(2));
                    detailObj.setProduct_type(cursor.getString(3));
                    detailObj.setMobile_no(cursor.getString(4));
                    detailObj.setCustomer_other_details("");

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<TentFormDetails> getTentFormCustomerByPhone(String phone) {
        ArrayList<TentFormDetails> unUploadedList = new ArrayList<TentFormDetails>();
        Cursor cursor = null,cursork = null;
        try {
            String selectQuery = "SELECT * FROM tent_form_details where mobile_no = '"
                    + phone + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentFormDetails detailObj = new TentFormDetails();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setStarting_date_time(cursor.getString(1));
                    detailObj.setEnd_date_time(cursor.getString(2));
                    detailObj.setStarting_latt(cursor.getString(3));
                    detailObj.setStarting_longi(cursor.getString(4));
                    detailObj.setEnd_latt(cursor.getString(5));
                    detailObj.setEnd_logi(cursor.getString(6));
                    detailObj.setStarting_image(cursor.getString(7));
                    detailObj.setEnd_image(cursor.getString(8));
                    detailObj.setCustomer_name(cursor.getString(9));
                    detailObj.setMobile_no(cursor.getString(10));
                    detailObj.setInterested_for_demo(cursor.getString(11));
                    detailObj.setDemo_tentative_date_time(cursor.getString(12));
                    detailObj.setRemarks(cursor.getString(13));
                    unUploadedList.add(detailObj);
                    cursor.moveToNext();


                }
            }
            cursor.close();

            selectQuery = "SELECT * FROM knocking_form_details where mobile_no = '"
                    + phone + "'";
            cursork = database.rawQuery(selectQuery, null);
            if (cursork.getCount() > 0) {
                cursork.moveToFirst();
                for (int ii = 0; ii < cursork.getCount(); ii++) {
                    TentFormDetails detailObjN = new TentFormDetails();
                    detailObjN.setTent_form_id(cursork.getString(0));
                    detailObjN.setStarting_date_time(cursork.getString(1));
                    detailObjN.setEnd_date_time(cursork.getString(2));
                    detailObjN.setStarting_latt(cursork.getString(3));
                    detailObjN.setStarting_longi(cursork.getString(4));
                    detailObjN.setEnd_latt(cursork.getString(5));
                    detailObjN.setEnd_logi(cursork.getString(6));
                    detailObjN.setStarting_image(cursork.getString(7));
                    detailObjN.setEnd_image(cursork.getString(8));
                    detailObjN.setCustomer_name(cursork.getString(9));
                    detailObjN.setMobile_no(cursork.getString(10));
                    detailObjN.setInterested_for_demo(cursork.getString(11));
                    detailObjN.setDemo_tentative_date_time(cursork.getString(12));
                    detailObjN.setRemarks(cursork.getString(13));

                    unUploadedList.add(detailObjN);
                    cursork.moveToNext();
                }
            }
            cursork.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public boolean AddDemoFormDetails(String timeStamp,String demo_latt,String demo_longi,String customer_name,String prod_interested
            ,String demo_achieved,String future_appointment_date,String demo_given_by,String sales_achieved,
                                      String ask_details,String full_payment,String sale_model,String sale_price,String sale_exchange,
    String sale_payment_details,String booking_done,String booking_model,String booking_sale_price,String booking_adv_amount,String booking_full_payment,String balance_due) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "DF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("demo_form_id", order_no);
            cv.put("demo_latt", demo_latt);
            cv.put("demo_longi", demo_longi);
            cv.put("customer_name", customer_name);
            cv.put("prod_interested", prod_interested);
            cv.put("demo_achieved", demo_achieved);
            cv.put("future_appointment_date", future_appointment_date);
            cv.put("demo_given_by", demo_given_by);
            cv.put("sales_achieved", sales_achieved);
            cv.put("ask_details", ask_details);
            cv.put("full_payment", full_payment);
            cv.put("sale_model", sale_model);
            cv.put("sale_price", sale_price);
            cv.put("sale_exchange", sale_exchange);
            cv.put("sale_payment_details", sale_payment_details);
            cv.put("booking_done", booking_done);
            cv.put("booking_model", booking_model);
            cv.put("booking_sale_price", booking_sale_price);
            cv.put("booking_adv_amount", booking_adv_amount);
            cv.put("booking_full_payment", booking_full_payment);
            cv.put("balance_due", balance_due);

            database.insertWithOnConflict("demo_form_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("demo_form_details", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public ArrayList<Location> getUnuploadedTransactionPropello(String type, String notype) {
        ArrayList<Location> unUploadedTransList = new ArrayList<Location>();
        Cursor cursor = null;
        try {
            String selectQuery = "";
            if (type.equalsIgnoreCase("STOCK"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND (substr(trans_id,1,1)='S' OR substr(trans_id,1,2)='NS')  AND substr(trans_id,1,2)<>'SU' AND substr(trans_id,1,3)<>'NSU'";
            }
            else if (type.equalsIgnoreCase("TOUR SWAP"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='TS' ";
            }
            else if (type.equalsIgnoreCase("ATTENDANCE"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND (substr(trans_id,1,1)='A' OR  substr(trans_id,1,2)='WO' OR  substr(trans_id,1,2)='LR')";
            }
            else if (type.equalsIgnoreCase("CHECKOUT"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='CH'";
            }
            else if (type.equalsIgnoreCase("STOCK_RETURN"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='SR'";
            }
            else if (type.equalsIgnoreCase("DO_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='DO'";
            }
            else if (type.equalsIgnoreCase("gift_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='GD'";
            }
            else if (type.equalsIgnoreCase("grn_transaction"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,3)='GRN'";
            }
            else if (type.equalsIgnoreCase("business_prospect_customize"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,1)='B'";
            }
            else if (type.equalsIgnoreCase("doctor_visit"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='DR'";
            }
            else if (type.equalsIgnoreCase("stockist_visit"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND substr(trans_id,1,2)='SV'";
            }
            else if (type.equalsIgnoreCase("tent_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND trans_id='"+notype+"'";
            }
            else if (type.equalsIgnoreCase("demo_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND trans_id='"+notype+"'";
            }
            else if (type.equalsIgnoreCase("knocking_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND trans_id='"+notype+"'";
            }else if (type.equalsIgnoreCase("telecaller_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND trans_id='"+notype+"'";
            }else if (type.equalsIgnoreCase("GL_form"))
            {
                selectQuery = "SELECT * FROM location where flag = 0 AND trans_id='"+notype+"'";
            }

            else
            {
                selectQuery = "SELECT * FROM location where flag = '0' ";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    Location locationObj = new Location();
                    locationObj.setEmpCode(cursor.getString(0));
                    locationObj.setTransId(cursor.getString(1));
                    locationObj.setDate(cursor.getString(2));
                    locationObj.setLatitude(cursor.getString(3));
                    locationObj.setLongitude(cursor.getString(4));

                    unUploadedTransList.add(locationObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedTransList;
    }


    public ArrayList<DemoForm> getUnuploadedDemoForm(String transId) {
        ArrayList<DemoForm> unUploadedList = new ArrayList<DemoForm>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM demo_form_details where demo_form_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    DemoForm detailObj = new DemoForm();
                    detailObj.setDemo_form_id(cursor.getString(0));
                    detailObj.setDemo_latt(cursor.getString(1));
                    detailObj.setDemo_longi(cursor.getString(2));
                    detailObj.setCustomer_name(cursor.getString(3));
                    detailObj.setProd_interested(cursor.getString(4));
                    detailObj.setDemo_achieved(cursor.getString(5));
                    detailObj.setFuture_appointment_date(cursor.getString(6));
                    detailObj.setDemo_given_by(cursor.getString(7));
                    detailObj.setSales_achieved(cursor.getString(8));
                    detailObj.setAsk_details(cursor.getString(9));
                    detailObj.setFull_payment(cursor.getString(10));
                    detailObj.setSale_model(cursor.getString(11));
                    detailObj.setSale_price(cursor.getString(12));
                    detailObj.setSale_exchange(cursor.getString(13));
                    detailObj.setSale_payment_details(cursor.getString(14));
                    detailObj.setBooking_done(cursor.getString(15));
                    detailObj.setBooking_model(cursor.getString(16));
                    detailObj.setBooking_sale_price(cursor.getString(17));
                    detailObj.setBooking_adv_amount(cursor.getString(18));
                    detailObj.setBooking_full_payment(cursor.getString(19));
                    detailObj.setBalance_due(cursor.getString(20));


                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public ArrayList<KnokingFormDetails> getUnuploadedKnokingForm(String transId) {
        ArrayList<KnokingFormDetails> unUploadedList = new ArrayList<KnokingFormDetails>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM knocking_form_details where knocking_form_id  = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    KnokingFormDetails detailObj = new KnokingFormDetails();
                    detailObj.setKnocking_form_id(cursor.getString(0));
                    detailObj.setStarting_date_time(cursor.getString(1));
                    detailObj.setEnd_date_time(cursor.getString(2));
                    detailObj.setStarting_latt(cursor.getString(3));
                    detailObj.setStarting_longi(cursor.getString(4));
                    detailObj.setEnd_latt(cursor.getString(5));
                    detailObj.setEnd_logi(cursor.getString(6));
                    detailObj.setStarting_image(cursor.getString(7));
                    detailObj.setEnd_image(cursor.getString(8));
                    detailObj.setCustomer_name(cursor.getString(9));
                    detailObj.setMobile_no(cursor.getString(10));
                    detailObj.setInterested_for_demo(cursor.getString(11));
                    detailObj.setDemo_tentative_date_time(cursor.getString(12));
                    detailObj.setRemarks(cursor.getString(13));


                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }



    public boolean AddKnockingFormProduct(String timeStamp,String product,String brand,String life,String mobile) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "KF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("knocking_form_id", order_no);
            cv.put("product", product);
            cv.put("brand", brand);
            cv.put("life_of_product", life);
            cv.put("mobile_no", mobile);

            database.insertWithOnConflict("knocking_form_product_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("knocking_form_product", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public boolean AddKnockingFormDetails(String timeStamp,String starting_date_time,String end_date_time,String starting_latt,String starting_longi,String end_latt,String end_logi,String starting_image,String end_image,String customer_name,String mobile_no,String interested_for_demo,String demo_tentative_date_time,String remarks) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "KF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("knocking_form_id", order_no);
            cv.put("starting_date_time", starting_date_time);
            cv.put("end_date_time", end_date_time);
            cv.put("starting_latt", starting_latt);
            cv.put("starting_longi", starting_longi);
            cv.put("end_latt", end_latt);
            cv.put("end_logi", end_logi);
            cv.put("starting_image", starting_image);
            cv.put("end_image", end_image);
            cv.put("customer_name", customer_name);
            cv.put("mobile_no", mobile_no);
            cv.put("interested_for_demo", interested_for_demo);
            cv.put("demo_tentative_date_time", demo_tentative_date_time);
            cv.put("remarks", remarks);

            database.insertWithOnConflict("knocking_form_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("tent_form_details", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public boolean AddKnockingFormCust(String timeStamp,String customer_address,String customer_other_details,String product_type, String mobile_no ) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "KF" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;


        try {
            ContentValues cv = new ContentValues();
            cv.put("knocking_form_id", order_no);
            cv.put("customer_address", customer_address );
            cv.put("customer_other_details", customer_other_details);
            cv.put("product_type", product_type );
            cv.put("mobile_no", mobile_no);

            database.insertWithOnConflict("knocking_form_customer_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("knocking_form_customer", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public ArrayList<TelecallerList> getUnuploadedTelecallerForm(String transId) {
        ArrayList<TelecallerList> unUploadedList = new ArrayList<TelecallerList>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM telecaller_form_details where telecaller_form_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TelecallerList detailObj = new TelecallerList();
                    detailObj.setTelecaller_form_id(cursor.getString(0));
                    detailObj.setCustomer_name(cursor.getString(2));
                    detailObj.setMobile(cursor.getString(3));
                    detailObj.setIs_connected(cursor.getString(4));
                    detailObj.setNot_connected_reason(cursor.getString(5));
                    detailObj.setDemo_existing_product(cursor.getString(6));
                    detailObj.setDemo_achieved(cursor.getString(7));
                    detailObj.setDemo_appointment_datetime(cursor.getString(8));
                    detailObj.setAllocated_user(cursor.getString(9));
                    detailObj.setService_interest(cursor.getString(10));
                    detailObj.setService_type(cursor.getString(11));
                    detailObj.setCollected_amount(cursor.getString(12));
                    detailObj.setService_date(cursor.getString(13));
                    detailObj.setNext_appointment_date_time(cursor.getString(14));
                    detailObj.setRemarks(cursor.getString(15));
                    detailObj.setUpdate_by(cursor.getString(16));
                    detailObj.setDemo_interested_product(cursor.getString(17));


                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public boolean AddTelecallerFormDetails(String timeStamp,String tent_knocking_form_id,String customer_name
            ,String mobile,String is_connected
            ,String not_connected_reason,String demo_existing_product,String demo_achieved
            ,String demo_appointment_datetime,String allocated_user,String service_interest,String next_call_date_time
            ,String service_type,String collected_amount,String service_date,String next_appointment_date_time
            ,String remarks,String update_by,String demo_interested_product) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "TC" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        try {
            ContentValues cv = new ContentValues();
            cv.put("telecaller_form_id", order_no);
            cv.put("tent_knocking_form_id", tent_knocking_form_id);
            cv.put("customer_name", customer_name);
            cv.put("mobile", mobile);
            cv.put("is_connected", is_connected);
            cv.put("not_connected_reason", not_connected_reason);
            cv.put("demo_existing_product", demo_existing_product);
            cv.put("demo_achieved", demo_achieved);
            cv.put("demo_appointment_datetime", demo_appointment_datetime);
            cv.put("allocated_user", allocated_user);
            cv.put("service_interest", service_interest);
            cv.put("service_type", service_type);
            cv.put("collected_amount", collected_amount);
            cv.put("service_date", service_date);
            cv.put("next_appointment_date_time", next_appointment_date_time);
            cv.put("remarks", remarks);
            cv.put("update_by", update_by);
            cv.put("demo_interested_product", demo_interested_product);

            database.insertWithOnConflict("telecaller_form_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("telecaller_form_details", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }

    public ArrayList<GroupLeaderFormList> getUnuploadedGroupLeaderForm(String transId) {
        ArrayList<GroupLeaderFormList> unUploadedList = new ArrayList<GroupLeaderFormList>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM group_leader_form_details where group_l_form_id = '"
                    + transId + "'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    GroupLeaderFormList detailObj = new GroupLeaderFormList();
                    detailObj.setGroup_l_form_id(cursor.getString(0));
                    detailObj.setCustomer_name(cursor.getString(1));
                    detailObj.setMobile_no(cursor.getString(2));
                    detailObj.setDemo_acheive(cursor.getString(3));
                    detailObj.setGroup_l_photo(cursor.getString(4));
                    detailObj.setGroup_l_photo_datetime(cursor.getString(5));
                    detailObj.setGroup_l_photo_latt(cursor.getString(6));
                    detailObj.setGroup_l_photo_longi(cursor.getString(7));
                    detailObj.setTent_photo(cursor.getString(8));
                    detailObj.setTent_photo_datetime(cursor.getString(9));
                    detailObj.setTent_photo_latt(cursor.getString(10));
                    detailObj.setTent_photo_longi(cursor.getString(11));
                    detailObj.setDemo_photo(cursor.getString(12));
                    detailObj.setDemo_photo_datetime(cursor.getString(13));
                    detailObj.setDemo_photo_latt(cursor.getString(14));
                    detailObj.setDemo_photo_longi(cursor.getString(15));
                    detailObj.setNight_meet_photo(cursor.getString(16));
                    detailObj.setNight_meet_photo_datetime(cursor.getString(17));
                    detailObj.setNight_meet_photo_latt(cursor.getString(18));
                    detailObj.setNight_meet_photo_longi(cursor.getString(19));
                    detailObj.setUpdate_by(cursor.getString(20));


                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public List<String> getModelDemoForm(String brand) {
        List<String> list = new ArrayList<String>();
        Cursor cursor = null;//cursorRO = null;
        try {

            String selectQuery = "SELECT prod_code,prod_desc FROM product_master WHERE product_group_code IN(SELECT product_group_code FROM product_group_master WHERE product_group_name IN("+brand+" COLLATE NOCASE))";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    list.add(cursor.getString(1));
                    cursor.moveToNext();
                }
            }
            cursor.close();

            /*selectQuery = "SELECT prod_code,prod_desc FROM product_master WHERE product_group_code IN(SELECT product_group_code FROM product_group_master WHERE product_group_name IN("+brand+" COLLATE NOCASE))";
            cursorRO = database.rawQuery(selectQuery, null);
            if (cursorRO.getCount() > 0) {
                cursorRO.moveToFirst();
                for (int i = 0; i < cursorRO.getCount(); i++) {
                    list.add(cursorRO.getString(1));
                    cursorRO.moveToNext();
                }
            }
            cursorRO.close();*/

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public ArrayList<TentProduct> getDemoFormProduct(String transId,String mobile_no) {
        ArrayList<TentProduct> unUploadedList = new ArrayList<TentProduct>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT * FROM tent_form_customer_details where tent_form_id = '"
                    + transId + "' AND mobile_no = '"+mobile_no+"'";
            if (transId.contains("KF")) {
                selectQuery = "SELECT * FROM knocking_form_customer_details where knocking_form_id = '"
                        + transId + "' AND mobile_no = '"+mobile_no+"'";

            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentProduct detailObj = new TentProduct();
                    detailObj.setTent_form_id(cursor.getString(0));
                    detailObj.setProduct(cursor.getString(1));
                    detailObj.setBrand(cursor.getString(2));
                    detailObj.setLife_of_product(cursor.getString(3));
                    detailObj.setMobile_no(cursor.getString(4));

                    unUploadedList.add(detailObj);
                    cursor.moveToNext();
                }
            }else{

            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return unUploadedList;
    }

    public List<String> getMobileTelecallerForm(String brand) {
        List<String> list = new ArrayList<String>();
        Cursor cursor = null,cursork = null;
        try {
            String selectQuery = "SELECT mobile_no,customer_name,tent_form_id FROM tent_form_details WHERE interested_for_demo='no'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    list.add(cursor.getString(0)+"-"+cursor.getString(1)+"-"+cursor.getString(2)+"-t");
                    cursor.moveToNext();
                }
            }
            cursor.close();


            selectQuery = "SELECT mobile_no,customer_name,knocking_form_id FROM knocking_form_details WHERE interested_for_demo='no'";
            cursork = database.rawQuery(selectQuery, null);
            if (cursork.getCount() > 0) {
                cursork.moveToFirst();
                for (int i = 0; i < cursork.getCount(); i++) {
                    list.add(cursork.getString(0)+"-"+cursork.getString(1)+"-"+cursork.getString(2)+"-k");
                    cursork.moveToNext();
                }
            }
            cursork.close();


        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public ArrayList<CommonModel> getMobileCustTelecallerForm(String brand) {
        ArrayList<CommonModel> list = new ArrayList<CommonModel>();
        Cursor cursor = null,cursork = null;
        try {
            String selectQuery = "SELECT mobile_no,customer_name,tent_form_id FROM tent_form_details WHERE interested_for_demo='no' UNION SELECT mobile_no,customer_name,knocking_form_id FROM knocking_form_details WHERE interested_for_demo='no'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CommonModel cm = new CommonModel();
                    cm.setCom1(cursor.getString(0));
                    cm.setCom2(cursor.getString(1));
                    cm.setCom3(cursor.getString(2));
                    list.add(cm);
                    cursor.moveToNext();
                }
            }
            cursor.close();


          /*  selectQuery = "SELECT mobile_no,customer_name,knocking_form_id FROM knocking_form_details WHERE interested_for_demo='no'";
            cursork = database.rawQuery(selectQuery, null);
            if (cursork.getCount() > 0) {
                cursork.moveToFirst();
                for (int i = 0; i < cursork.getCount(); i++) {
                    CommonModel cm = new CommonModel();
                    cm.setCom1(cursork.getString(0));
                    cm.setCom2(cursork.getString(1));
                    cm.setCom3(cursork.getString(2));
                    list.add(cm);
                    cursork.moveToNext();
                }
            }
            cursork.close();*/


        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public String getNameTeleForm(String brand) {
        String list = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT customer_name FROM tent_form_details WHERE mobile_no = '"+brand+"'";
            if(brand.contains("KF")){
                selectQuery = "SELECT customer_name FROM knocking_form_details WHERE mobile_no = '"+brand+"'";
            }
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    list = cursor.getString(0);
                    cursor.moveToNext();
                }
            }else{
                selectQuery = "SELECT customer_name FROM knocking_form_details WHERE mobile_no = '"+brand+"'";
                cursor = database.rawQuery(selectQuery, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();
                    for (int i = 0; i < cursor.getCount(); i++) {
                        list = cursor.getString(0);
                        cursor.moveToNext();
                    }
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }


    public boolean AddGroupLeaderFormDetails(String timeStamp,String customer_name,String mobile_no,String demo_achieved,String group_l_photo,String group_l_photo_datetime,
                                             String group_l_photo_latt,String group_l_photo_longi,String tent_photo,String tent_photo_datetime,
                                             String tent_photo_latt,String demo_photo_latt,String tent_photo_longi,String demo_photo,
                                             String demo_photo_datetime,String demo_photo_longi,String night_meet_photo
    ,String night_meet_photo_datetime,String night_meet_photo_latt,String night_meet_photo_longi,String update_by) {

        Boolean isInsertionDone = true;
        String order_no = "";
        order_no = "GL" + Constants.employeeDetailObject.getEmpCode()
                + timeStamp;

        try {
            ContentValues cv = new ContentValues();
            cv.put("group_l_form_id", order_no);
            cv.put("customer_name", customer_name);
            cv.put("mobile_no", mobile_no);
            cv.put("demo_achieved", demo_achieved);
            cv.put("group_l_photo", group_l_photo);
            cv.put("group_l_photo_datetime", group_l_photo_datetime);
            cv.put("group_l_photo_latt", group_l_photo_latt);
            cv.put("group_l_photo_longi", group_l_photo_longi);
            cv.put("tent_photo", tent_photo);
            cv.put("tent_photo_datetime", tent_photo_datetime);
            cv.put("tent_photo_latt", tent_photo_latt);
            cv.put("demo_photo_latt", demo_photo_latt);
            cv.put("tent_photo_longi", tent_photo_longi);
            cv.put("demo_photo", demo_photo);
            cv.put("demo_photo_datetime", demo_photo_datetime);
            cv.put("demo_photo_longi", demo_photo_longi);
            cv.put("night_meet_photo", night_meet_photo);
            cv.put("night_meet_photo_datetime", night_meet_photo_datetime);
            cv.put("night_meet_photo_latt", night_meet_photo_latt);
            cv.put("night_meet_photo_longi", night_meet_photo_longi);
            cv.put("update_by", update_by);

            database.insertWithOnConflict("group_leader_form_details", null, cv,
                    SQLiteDatabase.CONFLICT_IGNORE);
            Log.d("group_leader_details", "Data Inserted");

        } catch (SQLException e) {
            isInsertionDone = false;
        } finally {
            isInsertionDone = true;
        }
        return isInsertionDone;
    }


    public void UpdateTentFormDetails(String timeStamp, String eImg, String eTime) {
        database.beginTransaction();
        try {
            String order_no = "";
            order_no = "TF" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

            database.execSQL("UPDATE tent_form_details SET end_date_time ='"
                    + eTime + "',end_image ='" + eImg + "' WHERE tent_form_id = '"
                    + order_no + "' ");

            database.setTransactionSuccessful();
        } catch (SQLException e) {

        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<CommonModel> getModelListDemoForm(String brand) {
        ArrayList<CommonModel> list = new ArrayList<CommonModel>();
        Cursor cursor = null;//cursorRO = null;
        try {

            String selectQuery = "SELECT prod_code,prod_desc FROM product_master WHERE product_group_code IN(SELECT product_group_code FROM product_group_master WHERE product_group_name IN("+brand+" COLLATE NOCASE))";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CommonModel cm = new CommonModel();
                    cm.setCom1(cursor.getString(1));
                    cm.setCom2("0");
                    list.add(cm);
                    cursor.moveToNext();
                }
            }
            cursor.close();

            /*selectQuery = "SELECT prod_code,prod_desc FROM product_master WHERE product_group_code IN(SELECT product_group_code FROM product_group_master WHERE product_group_name IN("+brand+" COLLATE NOCASE))";
            cursorRO = database.rawQuery(selectQuery, null);
            if (cursorRO.getCount() > 0) {
                cursorRO.moveToFirst();
                for (int i = 0; i < cursorRO.getCount(); i++) {
                    list.add(cursorRO.getString(1));
                    cursorRO.moveToNext();
                }
            }
            cursorRO.close();*/

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public String getKnoFormMobile(String brand) {
        String list = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT customer_name FROM knocking_form_details WHERE mobile_no = '"+brand+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    list = cursor.getString(0);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public String getTentFormMobile(String brand) {
        String list = "";
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT customer_name FROM knocking_form_details WHERE mobile_no = '"+brand+"'";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    list = cursor.getString(0);
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public boolean getCheckInTimeCapturedMenu() {
        boolean flg = false;
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT DISTINCT survey_sub_menu FROM survey_input";
            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {

                    if(Constants.surveyFormDetailsObj.getCheck_in_time_captured_menu().matches(cursor.getString(0))){
                        flg=true;
                    }
                    cursor.moveToNext();
                }
            }
            cursor.close();
        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return flg;
    }



    public ArrayList<TentProductList> getExistingProductTelecallerForm(String brand, String mobile_no) {
        ArrayList<TentProductList> list = new ArrayList<TentProductList>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT *FROM tent_form_product_details WHERE tent_form_id='"+brand+"' AND mobile_no = '"+mobile_no+"'";
            if(brand.contains("KF")){
                selectQuery = "SELECT *FROM knocking_form_product_details WHERE Knocking_form_id='"+brand+"' AND mobile_no = '"+mobile_no+"'";
            }

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    TentProductList tp = new TentProductList();
                    tp.setDate(cursor.getString(1));
                    tp.setBrand(""+cursor.getString(2));
                    tp.setLife(""+cursor.getString(3));
                    list.add(tp);
                    cursor.moveToNext();
                }
            }
            cursor.close();

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }


    public ArrayList<CommonModel> getCustomerGroupLeaderForm(String f,String t) {
        ArrayList<CommonModel> list = new ArrayList<CommonModel>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT customer_name,mobile_no,demo_tentative_date_time FROM  tent_form_details WHERE interested_for_demo='yes' AND strftime('%Y-%m-%d', starting_date_time) between '"+f+"' AND '"+t+"' UNION SELECT customer_name,mobile_no,demo_tentative_date_time FROM  knocking_form_details WHERE interested_for_demo='yes' AND strftime('%Y-%m-%d', starting_date_time) between '"+f+"' AND '"+t+"'";

            selectQuery = "SELECT customer_name,mobile_no,demo_tentative_date_time FROM  tent_form_details WHERE interested_for_demo='yes' UNION SELECT customer_name,mobile_no,demo_tentative_date_time FROM  knocking_form_details WHERE interested_for_demo='yes'";

            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    CommonModel tp = new CommonModel();
                    tp.setCom1(cursor.getString(0));
                    tp.setCom2(""+cursor.getString(1));
                    tp.setCom3(""+cursor.getString(2));
                    tp.setCom4("0");
                    list.add(tp);
                    cursor.moveToNext();
                }
            }
            cursor.close();

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public ArrayList<PropAccessibility> getPropAccessList(String f) {
        ArrayList<PropAccessibility> list = new ArrayList<PropAccessibility>();
        Cursor cursor = null;
        try {
            String selectQuery = "SELECT *FROM prop_form_accessibility join emp_master on prop_form_accessibility.hierarchy = emp_master.designation where emp_master.emp_code='"+f+"'";


            cursor = database.rawQuery(selectQuery, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                for (int i = 0; i < cursor.getCount(); i++) {
                    PropAccessibility tp = new PropAccessibility();
                    tp.setHierarchy(cursor.getString(0));
                    tp.setTent_sheet(cursor.getString(1));
                    tp.setKnocking_sheet(""+cursor.getString(2));
                    tp.setDemo_sheet(""+cursor.getString(3));
                    tp.setSales_closure_form(""+cursor.getString(4));
                    tp.setBooking_closure_form(""+cursor.getString(5));
                    tp.setGroup_leader_form(""+cursor.getString(6));
                    tp.setTele_caller_form(""+cursor.getString(7));
                    list.add(tp);
                    cursor.moveToNext();
                }
            }
            cursor.close();

        } catch (Exception e) {
            System.out.println("Exception :::::::" + e.getMessage());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return list;
    }

    public SurveyReportSumary getSurveyReportSummeryRemainder(String condition) {
        SurveyReportSumary obj = new SurveyReportSumary();

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Site Visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 8) {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Site Visit' AND SUBSTR(survey_id,-14,8) LIKE '" + condition + "'";
            } else {
                query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type='Site Visit' AND SUBSTR(survey_id,-14,6) LIKE '" + condition + "'";
            }
            query = "SELECT COUNT(sm.site_id) FROM site_master sm where  follow_up_date = '"+condition+"'";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setNoSiteVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";

            query = "SELECT COUNT(f_code) FROM facilitator_master where next_follow_up_date = '"+condition+"'";

            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFacilitaorAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        return obj;
    }
    public SurveyReportSumary upcommingVisitCount(String condition,String type){
            String query;
            SurveyReportSumary obj = new SurveyReportSumary();
        Cursor cursor2 = null;
            try {
                if(type.equalsIgnoreCase("Site Visit")) {

                    if (condition.length() == 2) {
                        query = "SELECT COUNT(site_id) FROM site_master";
                    } else {
                        query = "SELECT COUNT(DISTINCT site_id) FROM site_master WHERE site_name LIKE '%" + condition + "%'";
                    }

                    cursor2 = database.rawQuery(query, null);
                    if (cursor2.getCount() > 0) {
                        cursor2.moveToFirst();
                        String s1 = cursor2.getString(0);
                        obj.setNoUpcommingVisit(s1);
                    } else {
                        obj.setNoUpcommingVisit(String.valueOf("0"));
                    }
                }else if(type.equalsIgnoreCase("Facilitator Add")) {

                    if (condition.length() == 2) {
                        query = "SELECT COUNT(f_code) FROM facilitator_master";
                    } else {
                        query = "SELECT COUNT(DISTINCT f_code) FROM facilitator_master WHERE facilitator_name LIKE '%" + condition + "%'";
                    }

                    cursor2 = database.rawQuery(query, null);
                    if (cursor2.getCount() > 0) {
                        cursor2.moveToFirst();
                        String s1 = cursor2.getString(0);
                        obj.setNoUpcommingVisit(s1);
                    } else {
                        obj.setNoUpcommingVisit(String.valueOf("0"));
                    }
                }
            }catch (Exception e){
                obj.setNoUpcommingVisit(String.valueOf("0"));
                if (cursor2 != null) {
                    cursor2.close();
                }
            }
        if (cursor2 != null) {
            cursor2.close();
        }
            return obj;
    }
    public SurveyReportSumary getSurveyReportSummeryRemainderCounter(String condition,String type) {
        SurveyReportSumary obj = new SurveyReportSumary();

        if (type.contains("Site Visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 2) {
                query = "SELECT COUNT(site_id) FROM site_master";
            } else {
                query = "SELECT COUNT(DISTINCT site_id) FROM site_master WHERE follow_up_date = '"+condition+"'";
            }
            Cursor cursor=null,cursor1=null;
            cursor= database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                if (condition.length() != 2) {
                    for (int ii = 0; ii < cursor.getCount(); ii++) {
                        String s = cursor.getString(0);

                        query = "SELECT survey_id,flag,value,row_id  FROM survey_output WHERE value LIKE '%" + s + "%' GROUP BY survey_id";

                        cursor1 = database.rawQuery(query, null);
                        if (cursor1.getCount() > 0) {
                            sitevisit = sitevisit + 1;
                        }
                        cursor.moveToNext();
                    }
                }
                if (condition.length() == 2) {
                    sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }
            obj.setNoSiteVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        if (type.contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";

            if (condition.length() == 2) {
                query = "SELECT COUNT(f_code) FROM facilitator_master";
            } else {
                query = "SELECT COUNT(f_code) FROM facilitator_master where next_follow_up_date = '"+condition+"'";
            }


            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFacilitaorAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        return obj;
    }

    public ArrayList<CommonModel> getSurveyReportSummerySiteVisitRemainderCounter(String condition,String type) {
        CommonModel cm =new CommonModel();
        ArrayList<CommonModel> data = new ArrayList<>();
        if (type.contains("Site Visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 2) {
                query = "SELECT COUNT(site_id) FROM site_master";
            } else {
                query = "SELECT * FROM site_master WHERE follow_up_date = '"+condition+"'";
            }
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor=null,cursor1=null;
            try {
                cursor = database.rawQuery(query, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();

                    for (int ii = 0; ii < cursor.getCount(); ii++) {
                        cm =new CommonModel();
                        cm.setCom1(cursor.getString(2));
                        cm.setCom2(cursor.getString(3));
                        data.add(cm);
                        cursor.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("SiteVisitRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            if (cursor != null) {
                cursor.close();
            }
        }
        if (type.contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 2) {
                query = "SELECT COUNT(f_code) FROM facilitator_master";
            } else {
                query = "SELECT * FROM site_master WHERE follow_up_date = '"+condition+"'";
                query = "SELECT * FROM facilitator_master where next_follow_up_date = '"+condition+"'";
            }
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor=null,cursor1=null;
            try {
                cursor = database.rawQuery(query, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();

                    for (int ii = 0; ii < cursor.getCount(); ii++) {
                        cm =new CommonModel();
                        cm.setCom1(cursor.getString(2));
                        cm.setCom2(cursor.getString(3));
                        data.add(cm);
                        cursor.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("FaciRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            if (cursor != null) {
                cursor.close();
            }
        }

        return data;
    }

    public ArrayList<CommonModel> getSurveyReportSummerySiteVisitRemainderCounterWithUpcomming(String condition,String type) {
        CommonModel cm =new CommonModel();
        ArrayList<CommonModel> data = new ArrayList<>();
        if (type.contains("Site Visit"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 2) {
                query = "SELECT COUNT(site_id) FROM site_master";
            } else {
                query = "SELECT * FROM site_master WHERE follow_up_date = '"+condition+"'";
            }
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor=null,cursor1=null;
            try {
                cursor = database.rawQuery(query, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();

                    for (int ii = 0; ii < cursor.getCount(); ii++) {
                        cm =new CommonModel();
                        cm.setCom1(cursor.getString(2));
                        cm.setCom2(cursor.getString(3));
                        data.add(cm);
                        cursor.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("SiteVisitRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            try {
                query = "SELECT * FROM site_master WHERE site_name LIKE '%" + condition + "%'";
                if(Constants.nickName.equalsIgnoreCase("DURO")){
                    query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
                }
                cursor1 = database.rawQuery(query, null);
                if (cursor1.getCount() > 0) {
                    cursor1.moveToFirst();

                    for (int ii = 0; ii < cursor1.getCount(); ii++) {
                        String val = cursor1.getString(2);
                        if(val.contains("$")){
                            String[] subVal = val.split("$");
                            for(int jj=0;jj<subVal.length;jj++){
                                String[] v = subVal[jj].split("#");
                                cm =new CommonModel();
                                cm.setCom1(v[0]);
                                data.add(cm);
                            }
                        }else{
                            String[] v = val.split("#");
                            cm =new CommonModel();
                            cm.setCom1(v[0]);
                            data.add(cm);
                        }

                        //cm.setCom2(cursor.getString(3));

                        cursor1.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("SiteVisitRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            if (cursor != null) {
                cursor.close();
            }
            if (cursor1 != null) {
                cursor1.close();
            }
        }

        if (type.contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";
            if (condition.length() == 2) {
                query = "SELECT COUNT(f_code) FROM site_master";
            } else {
                query = "SELECT * FROM facilitator_master WHERE next_follow_up_date = '"+condition+"'";
            }
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor=null,cursor1=null;
            try {
                cursor = database.rawQuery(query, null);
                if (cursor.getCount() > 0) {
                    cursor.moveToFirst();

                    for (int ii = 0; ii < cursor.getCount(); ii++) {
                        cm =new CommonModel();
                        cm.setCom1(cursor.getString(2));
                        cm.setCom2(cursor.getString(3));
                        data.add(cm);
                        cursor.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("SiteVisitRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            try {
                query = "SELECT * FROM facilitator_master WHERE facilitator_name LIKE '%" + condition + "%'";
                if(Constants.nickName.equalsIgnoreCase("DURO")){
                    query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
                }
                cursor1 = database.rawQuery(query, null);
                if (cursor1.getCount() > 0) {
                    cursor1.moveToFirst();

                    for (int ii = 0; ii < cursor1.getCount(); ii++) {
                        String val = cursor1.getString(2);
                        if(val.contains("$")){
                            String[] subVal = val.split("$");
                            for(int jj=0;jj<subVal.length;jj++){
                                String[] v = subVal[jj].split("#");
                                cm =new CommonModel();
                                cm.setCom1(v[0]);
                                data.add(cm);
                            }

                        }else{
                            String[] v = val.split("#");
                            cm =new CommonModel();
                            cm.setCom1(v[0]);
                            data.add(cm);
                        }

                        //cm.setCom2(cursor.getString(3));

                        cursor1.moveToNext();
                    }


                    //sitevisit = Integer.parseInt(cursor.getString(0));
                }
            }catch (Exception e){
                Log.d("SiteVisitRemainder--", "getSurveyReportSummerySiteVisitRemainderCounter: "+e);
            }
            if (cursor != null) {
                cursor.close();
            }
            if (cursor1 != null) {
                cursor1.close();
            }
        }

        return data;
    }

    public SurveyReportSumary getSurveyReportSummeryRemainderSyl(String condition) {
        SurveyReportSumary obj = new SurveyReportSumary();

        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Site Visit")) {
            int sitevisit = 0;
            String query = "";

            query = "SELECT sm.site_id FROM site_master sm where  sm.follow_up_date = '" + condition + "'";
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND sm.emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor = null, cursor1 = null;
            cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();

                for (int ii = 0; ii < cursor.getCount(); ii++) {
                    String s = cursor.getString(0);

                    query = "SELECT survey_id,flag,value,row_id  FROM survey_output WHERE value LIKE '%" + s + "%' GROUP BY survey_id";

                    cursor1 = database.rawQuery(query, null);
                    if (cursor1.getCount() > 0) {
                        sitevisit = sitevisit + 1;
                        /*cursor1.moveToFirst();
                        for (int ij = 0; ij < cursor1.getCount(); ij++) {
                            sitevisit = sitevisit + 1;
                        }
                        cursor1.moveToNext();*/
                    }
                    cursor.moveToNext();
                }

            }


            if(Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site")){
                query = "SELECT sm.site_id FROM site_master sm where  sm.follow_up_date = '" + condition + "'";
                if(Constants.nickName.equalsIgnoreCase("DURO")){
                    query = query + " AND sm.emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
                }
                Cursor cursor2 = null;
                cursor2 = database.rawQuery(query, null);
                if (cursor2.getCount() > 0) {
                    sitevisit = Integer.parseInt(String.valueOf(cursor2.getCount()));
                }
            }

            obj.setNoSiteVisit(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
            if (cursor1 != null) {
                cursor1.close();
            }
            //sitevisit = Integer.parseInt(sitevisit);
        }



        if (Constants.surveyFormDetailsObj.getSurveySubMenuDetails().contains("Facilitator Add"))
        {
            int sitevisit = 0;
            String query = "";

            query = "SELECT COUNT(f_code) FROM facilitator_master where next_follow_up_date = '"+condition+"'";
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                query = query + " AND emp_code='"+ Constants.employeeDetailObject.getEmpCode() +"'";
            }
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.getCount() > 0) {
                cursor.moveToFirst();
                sitevisit = Integer.parseInt(cursor.getString(0));
            }
            obj.setnoFacilitaorAdd(String.valueOf(sitevisit));
            if (cursor != null) {
                cursor.close();
            }
        }

        return obj;
    }


}