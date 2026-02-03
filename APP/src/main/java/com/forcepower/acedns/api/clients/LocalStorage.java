package com.forcepower.acedns.api.clients;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class LocalStorage {

    //Satya  9800970578
    public static final String KEY_ORDER_AUDIT_TYPE = "order_type_primary_secondary";
    public static final String KEY_TENT_ID = "tent_id";
    public static final String KEY_KNO_ID = "kno_id";
    public static final String KEY_TENT_START_IMAGE = "tent_start_image";
    public static final String KEY_TENT_START_TIME = "tent_start_time";
    public static final String KEY_TENT_START_TIME_KNO = "tent_start_time_kno";
    public static final String KEY_TENT_END_IMAGE = "tent_end_image";
    public static final String KEY_TENT_END_IMAGE_KNO = "tent_end_image_kno";
    public static final String KEY_TENT_START_IMAGE_KNO = "kno_image";
    public static final String KEY_TA_DA_PUBLIC_PRIVATE = "public_private";
    public static final String KEY_Purpose_of_visit = "Purpose_of_visit";
    private static LocalStorage instance = null;
    SharedPreferences sharedPreferences;
    Editor editor;

    public LocalStorage(Context context) {
        sharedPreferences = context.getSharedPreferences("Preferences", 0);
    }

    public static LocalStorage getInstance(Context context) {
        if (instance == null) {
            synchronized (LocalStorage.class) {
                if (instance == null) {
                    instance = new LocalStorage(context);
                }
            }
        }
        return instance;
    }

    public String getOrderAuditType() {
        return sharedPreferences.getString(KEY_ORDER_AUDIT_TYPE, null);
    }

    public void setOrderAuditType(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_ORDER_AUDIT_TYPE, type);
        editor.commit();
    }

    public String getTentID() {
        return sharedPreferences.getString(KEY_TENT_ID, "");
    }

    public void setTentID(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_ID, type);
        editor.commit();
    }

    public String getKnockID() {
        return sharedPreferences.getString(KEY_KNO_ID, "");
    }

    public void setKnockID(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_KNO_ID, type);
        editor.commit();
    }

    public String getStartImage() {
        return sharedPreferences.getString(KEY_TENT_START_IMAGE, "");
    }

    public String getKnoStartImage() {
        return sharedPreferences.getString(KEY_TENT_START_IMAGE_KNO, "");
    }

    public void setEndImage(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_END_IMAGE, type);
        editor.commit();
    }

    public String getEndImage() {
        return sharedPreferences.getString(KEY_TENT_END_IMAGE, "");
    }

    public void setKnoEndImage(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_END_IMAGE_KNO, type);
        editor.commit();
    }

    public void setStartImage(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_START_IMAGE, type);
        editor.commit();
    }

    public void setKnoStartImage(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_START_IMAGE_KNO, type);
        editor.commit();
    }

    public String getStartTime() {
        return sharedPreferences.getString(KEY_TENT_START_TIME, "");
    }

    public void setStartTime(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_START_TIME, type);
        editor.commit();
    }

    public String getKnoStartTime() {
        return sharedPreferences.getString(KEY_TENT_START_TIME_KNO, "");
    }

    public void setKnoStartTime(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TENT_START_TIME_KNO, type);
        editor.commit();
    }

    public String getTADAPubPri() {
        return sharedPreferences.getString(KEY_TA_DA_PUBLIC_PRIVATE, "private");
    }

    public void setTADAPubPri(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_TA_DA_PUBLIC_PRIVATE, type);
        editor.commit();
    }

    public String getPurpose_of_visit() {
        return sharedPreferences.getString(KEY_Purpose_of_visit, "private");
    }

    public void setPurpose_of_visit(String type) {
        editor = sharedPreferences.edit();
        editor.putString(KEY_Purpose_of_visit, type);
        editor.commit();
    }
}
