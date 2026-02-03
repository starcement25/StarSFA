package com.forcepower.acedns.util;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.preference.PreferenceManager;

import com.forcepower.acedns.database.AceDnsDatabase;

/**
 * This class will store the state of the user whether he has voted or not.
 */

public class PreferenceData {

    static final String DB_CLEAR_FLAG = "db_clear_flag";
    static final String CHECKIN_OUT = "check";
    static final String HELP_STOCK_AUDIT_CONFIRM = "HELP_STOCK_AUDIT_CONFIRM";
    static final String attachmentIdSemecolonSeparatedCheckIn = "attachmentIdSemecolonSeparatedCheckIn";
    static final String attachmentImageCheckInOut = "attachmentImageCheckInOut";
    static final String journeyInfoOwnOrPublicVehicle = "journeyInfoOwnOrPublicVehicle";
    static final String journeyInfoAttendanceTransactionID = "journeyInfoAttendanceTransactionID";
    static final String journeyInfoVehicleWheelType = "journeyInfoVehicleWheelType";


    public static SharedPreferences getSharedPreferences(Context ctx) {
        return PreferenceManager.getDefaultSharedPreferences(ctx);
    }

    public static void ClearPreferenceData(Context ctx) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.clear();
        editor.commit();
    }

    public static void setCheckInOutId(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(CHECKIN_OUT, id);
        editor.commit();
    }

    public static String getCheckInOutId(Context ctx) {
        return getSharedPreferences(ctx).getString(CHECKIN_OUT, "0");
    }
    public static void setDBClearFlag(Context ctx, int id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putInt(DB_CLEAR_FLAG, id);
        editor.commit();
    }

    public static int getDBClearFlag(Context ctx) {
        return getSharedPreferences(ctx).getInt(DB_CLEAR_FLAG, 0);
    }

    public static void setCheckInTime(Context ctx, String checkintime) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(CHECKIN_TIME, checkintime);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("checkintime",checkintime);
    }

//    public static String getCheckInTime(Context ctx)
//    {
//        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("checkintime");
////        return getSharedPreferences(ctx).getString(CHECKIN_TIME, "0");
//    }
    public String getCheckInTime(Context ctx)
    {
        AceDnsDatabase db=new AceDnsDatabase(ctx);
        return db.getCheckInValueByColumnName("checkintime");
//        return getSharedPreferences(ctx).getString(CHECKIN_TIME, "0");
    }

    public static void setCheckInOutEmpCode(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(EMPLOYEE_ID, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("customer_code",id);
    }

    public static String getCheckInOutEmpCode(Context ctx) {
//        return getSharedPreferences(ctx).getString(EMPLOYEE_ID, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("customer_code");
    }

    public static void setCheckInOutRouteCode(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(ROUTE_CODE, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("route_code",id);
    }

    public static String getCheckInOutRouteCode(Context ctx) {
//        return getSharedPreferences(ctx).getString(ROUTE_CODE, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("route_code");
    }

    public static void setCheckInOutEmpName(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(EMPLOYEE_NAME, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("customer_name",id);
    }

    public static String getCheckInOutEmpName(Context ctx) {
//        return getSharedPreferences(ctx).getString(EMPLOYEE_NAME, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("customer_name");
    }

    public static void setCheckInOutEmpType(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(EMPLOYEE_TYPE, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("customer_type",id);
    }

    public static String getCheckInOutEmpBrance(Context ctx) {
//        return getSharedPreferences(ctx).getString(EMPLOYEE_TYPE, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("custBranceCode");
    }

    public static void setCheckInOutEmpBrance(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(EMPLOYEE_TYPE, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("custBranceCode",id);
    }

    public static String getCheckInOutEmpType(Context ctx) {
//        return getSharedPreferences(ctx).getString(EMPLOYEE_TYPE, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("customer_type");
    }

    public static void setHelpStockAuditConfirm(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(HELP_STOCK_AUDIT_CONFIRM, id);
        editor.commit();
    }

    public static String getHelpStockAuditConfirm(Context ctx) {
        return getSharedPreferences(ctx).getString(HELP_STOCK_AUDIT_CONFIRM, "disable");
    }




    public static void setAttachmentIdSemecolonSeparatedCheckIn(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(attachmentIdSemecolonSeparatedCheckIn, id);
        editor.commit();
    }

    public static String getAttachmentIdSemecolonSeparatedCheckIn(Context ctx) {
        return getSharedPreferences(ctx).getString(attachmentIdSemecolonSeparatedCheckIn, "");
    }

    public static void setAttachmentImageCheckInOut(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(attachmentImageCheckInOut, id);
        editor.commit();
    }

    public static String getAttachmentImageCheckInOut(Context ctx) {
        return getSharedPreferences(ctx).getString(attachmentImageCheckInOut, "");
    }

    public static void setCheckInLAT(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(LAT, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("latitude",id);
    }

    public static String getCheckInLAT(Context ctx) {
//        return getSharedPreferences(ctx).getString(LAT, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("latitude");
    }

    public static void setCheckInLONG(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(LONG, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("longitude",id);
    }

    public static String getCheckInLONG(Context ctx) {
//        return getSharedPreferences(ctx).getString(LONG, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("longitude");
    }
    public static void setCheckInACCURACY(Context ctx, String id) {
//        Editor editor = getSharedPreferences(ctx).edit();
//        editor.putString(ACCURACY, id);
//        editor.commit();
        new AceDnsDatabase(ctx).setCheckInValueByColumnName("accuracy",id);
    }

    public static String getCheckInACCURACY(Context ctx) {
//        return getSharedPreferences(ctx).getString(ACCURACY, "0");
        return new AceDnsDatabase(ctx).getCheckInValueByColumnName("accuracy");
    }

    public static void setjourneyInfoOwnOrPublicVehicle(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(journeyInfoOwnOrPublicVehicle, id);
        editor.commit();
    }

    public static String getjourneyInfoOwnOrPublicVehicle(Context ctx) {
        return getSharedPreferences(ctx).getString(journeyInfoOwnOrPublicVehicle, "");
    }

    public static void setJourneyInfoAttendanceTransactionID(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(journeyInfoAttendanceTransactionID, id);
        editor.commit();
    }

    public static String getJourneyInfoAttendanceTransactionID(Context ctx) {
        return getSharedPreferences(ctx).getString(journeyInfoAttendanceTransactionID, "");
    }

    public static void setjourneyInfoVehicleWheelType(Context ctx, String id) {
        Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(journeyInfoVehicleWheelType, id);
        editor.commit();
    }

    public static String getjourneyInfoVehicleWheelType(Context ctx) {
        return getSharedPreferences(ctx).getString(journeyInfoVehicleWheelType, "");
    }
}
