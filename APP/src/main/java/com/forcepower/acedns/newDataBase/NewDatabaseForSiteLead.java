package com.forcepower.acedns.newDataBase;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteStatement;
import android.util.Log;

import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingInvoiceNumberDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.EmployeeDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.PartyDataList;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.StateDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerCompetitorQuantityDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerMasterTableDataSet;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;
import com.forcepower.acedns.newDataBase.data_set.SBGFeedbackDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.CounterNameDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DistrictDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProductDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProfileDataSet;
import com.forcepower.acedns.newDataBase.sync.DataForUpload;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class NewDatabaseForSiteLead extends SQLiteOpenHelper {
    private static final String DATABASE_NAME = "NewDatabaseStore.db";
    private static final int DATABASE_VERSION = 1;
    SQLiteDatabase database;
    Context mContext;

    // <<<<<<<<<<<<<<<<<<<<<<<<<<< PRIMARY FUNCTION AND TABLE CREATE >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // ===========================  Primary and Override function ===========================
    public NewDatabaseForSiteLead(Context context) {
        super(context, Utils.getAppStoragePath(context) + DATABASE_NAME, null, DATABASE_VERSION);
        mContext = context;
        database = this.getWritableDatabase();
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
    }

    // =========================== Table Create ===========================
    public boolean isTableExists(String tableName) {
        if (tableName == null) return false;
        Cursor cursor = database.rawQuery("SELECT name FROM sqlite_master WHERE type='table' AND name=?", new String[]{tableName});
        boolean exists = (cursor.getCount() > 0);
        cursor.close();
        return exists;
    }

    public void createDatabaseTableForSiteLead() {
        if (!isTableExists("new_site_lead_and_conversion_tracking")) {
            String createTable = "CREATE TABLE new_site_lead_and_conversion_tracking" +
                    "(" + "site_transaction_id TEXT PRIMARY KEY," + "site_unique_id TEXT NOT NULL," + "site_creation_date TEXT NOT NULL," + "site_visit_date TEXT NOT NULL," + "employee_code TEXT NOT NULL," +
                    "employee_name TEXT NOT NULL," + "zone TEXT NOT NULL," + "branch TEXT NOT NULL," + "state TEXT NOT NULL," + "district TEXT NOT NULL," + "latitude TEXT NOT NULL," + "longitude TEXT NOT NULL," +
                    "customer_name TEXT NOT NULL," + "customer_contact_number TEXT NOT NULL," + "customer_full_address TEXT NOT NULL," + "is_register_contractor TEXT NOT NULL," + "contractor_name TEXT NOT NULL," +
                    "contractor_contact_number TEXT NOT NULL," + "is_register_engineer TEXT NOT NULL," + "engineer_name TEXT NOT NULL," + "engineer_contact_number TEXT NOT NULL," + "meeting_person TEXT NOT NULL," +
                    "decision_maker TEXT NOT NULL," + "site_segment TEXT NOT NULL," + "visit_type TEXT NOT NULL," + "project_segment TEXT NOT NULL," + "type_of_construction TEXT NOT NULL," + "floor_count TEXT NOT NULL," +
                    "current_stage_of_construction TEXT NOT NULL," + "built_up_area TEXT NOT NULL," + "site_potential TEXT NOT NULL," + "consumed_till_date TEXT NOT NULL," + "balance_potential TEXT NOT NULL," +
                    "balance_potential_manual TEXT NOT NULL," + "site_category TEXT NOT NULL," + "brand_used TEXT NOT NULL," + "price_per_bag TEXT NOT NULL," + "conversion TEXT NOT NULL," + "product_name TEXT NOT NULL," +
                    "order_quantity TEXT NOT NULL," + "requested_date_of_delivery TEXT NOT NULL," + "counter_type TEXT NOT NULL," + "counter_name TEXT NOT NULL," + "counter_code TEXT NOT NULL," +
                    "reason_for_non_conversion TEXT NOT NULL," + "site_priority TEXT NOT NULL," + "weather_shield_demo TEXT NOT NULL," + "approval_status TEXT NOT NULL," + "date_time TEXT NOT NULL," +
                    "asm_name TEXT NOT NULL," + "asm_employee_id TEXT NOT NULL," + "actual_date_of_delivery TEXT NOT NULL," + "delivery_remarks TEXT NOT NULL," + "reason_for_not_delivery TEXT NOT NULL," +
                    "site_status TEXT NOT NULL," + "remarks TEXT NOT NULL," + "flag INTEGER DEFAULT 0" + ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("branch_list")) {
            String createTable = "CREATE TABLE branch_list ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("state_list")) {
            String createTable = "CREATE TABLE state_list ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("district_list")) {
            String createTable = "CREATE TABLE district_list ( title TEXT, value TEXT, state_name TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("req_contractor_link_list")) {
            String createTable = "CREATE TABLE req_contractor_link_list ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("contractor_link_list")) {
            String createTable = "CREATE TABLE contractor_link_list ( code TEXT, name TEXT, number TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("req_engineer_stellar_list")) {
            String createTable = "CREATE TABLE req_engineer_stellar_list ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("engineer_stellar_list")) {
            String createTable = "CREATE TABLE engineer_stellar_list ( code TEXT, name TEXT, number TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("meeting_person")) {
            String createTable = "CREATE TABLE meeting_person ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("decision_maker")) {
            String createTable = "CREATE TABLE decision_maker ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("site_segment")) {
            String createTable = "CREATE TABLE site_segment ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("visit_type")) {
            String createTable = "CREATE TABLE visit_type ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("project_segment")) {
            String createTable = "CREATE TABLE project_segment ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("type_of_construction")) {
            String createTable = "CREATE TABLE type_of_construction ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("floor_count")) {
            String createTable = "CREATE TABLE floor_count ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("current_stage_of_construction")) {
            String createTable = "CREATE TABLE current_stage_of_construction ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("brand_used")) {
            String createTable = "CREATE TABLE brand_used ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("conversion")) {
            String createTable = "CREATE TABLE conversion ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("product")) {
            String createTable = "CREATE TABLE product ( name TEXT, visitType TEXT, conversionType TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("counter_type")) {
            String createTable = "CREATE TABLE counter_type ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("counter_name")) {
            String createTable = "CREATE TABLE counter_name ( name TEXT, code TEXT, type TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("reasons_for_non_conversion")) {
            String createTable = "CREATE TABLE reasons_for_non_conversion ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("priority")) {
            String createTable = "CREATE TABLE priority ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("weather_shield_demo")) {
            String createTable = "CREATE TABLE weather_shield_demo ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("approval_status")) {
            String createTable = "CREATE TABLE approval_status ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("asm_name")) {
            String createTable = "CREATE TABLE asm_name ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("site_status")) {
            String createTable = "CREATE TABLE site_status ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("emp_details")) {
            String createTable = "CREATE TABLE emp_details ( emp_code TEXT, name TEXT, designation TEXT, zone TEXT );";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("customer_competitor_quantity")) {
            String createTable = "CREATE TABLE customer_competitor_quantity ( " +
                    "competitor_quantity_id TEXT, " +
                    "customer_code TEXT, " +
                    "customer_name TEXT, " +
                    "mandatory TEXT, " +
                    "competitor_name TEXT, " +
                    "type TEXT, " +
                    "quantity TEXT, " +
                    "customer_dns_code TEXT, " +
                    "flag INTEGER DEFAULT 0 " +
                    ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        } else {
            Cursor cursor1 = database.rawQuery("PRAGMA table_info(customer_competitor_quantity)", null);
            while (cursor1.moveToNext()) {
                String columnName = cursor1.getString(cursor1.getColumnIndexOrThrow("name"));
            }
            cursor1.close();

            // Define all required columns with their types
            Map<String, String> requiredColumns = new LinkedHashMap<>();
            requiredColumns.put("competitor_quantity_id", "TEXT");
            requiredColumns.put("customer_code", "TEXT");
            requiredColumns.put("customer_name", "TEXT");
            requiredColumns.put("mandatory", "TEXT");
            requiredColumns.put("competitor_name", "TEXT");
            requiredColumns.put("type", "TEXT");
            requiredColumns.put("quantity", "TEXT");
            requiredColumns.put("customer_dns_code", "TEXT");
            requiredColumns.put("flag", "INTEGER DEFAULT 0");

            // Get existing columns
            Set<String> existingColumns = new HashSet<>();
            Cursor cursor = database.rawQuery("PRAGMA table_info(customer_competitor_quantity)", null);
            while (cursor.moveToNext()) {
                String columnName = cursor.getString(cursor.getColumnIndexOrThrow("name"));
                existingColumns.add(columnName.toLowerCase());
            }
            cursor.close();

            // Add missing columns
            database.beginTransaction();
            try {
                for (Map.Entry<String, String> entry : requiredColumns.entrySet()) {
                    if (!existingColumns.contains(entry.getKey().toLowerCase())) {
                        String alterQuery = "ALTER TABLE customer_competitor_quantity ADD COLUMN " +
                                entry.getKey() + " " + entry.getValue();
                        database.execSQL(alterQuery);
                    }
                }
                database.setTransactionSuccessful();
            } finally {
                database.endTransaction();
            }
        }
    }

    public void createDatabaseTableForLeadFunnel() {
        if (!isTableExists("lead_list_master_table")) {
            String createTable = "CREATE TABLE lead_list_master_table" +
                    "(" + "lead_generation_id TEXT PRIMARY KEY," + "emp_code TEXT," + "latitude TEXT," + "longitude TEXT," + "lead_type TEXT," + "party_name TEXT," +
                    "branch TEXT," + "district TEXT," + "state TEXT," + "qty_req TEXT," + "product_packaging TEXT," + "exp_rate_per_bag TEXT," +
                    "contact_person_name TEXT," + "designation TEXT," + "contact_number TEXT," + "mail_id TEXT," + "mode TEXT," + "quotation TEXT," +
                    "PO TEXT," + "status TEXT," + "remarks TEXT," + "assigned_to TEXT," + "self_other TEXT," + "acc_block_is_required TEXT," +
                    "category_type_construction TEXT," + "next_visit_date TEXT," + "lead_status TEXT," + "current_brand_used TEXT," + "current_price TEXT," +
                    "current_price_competitor TEXT," + "r_timing TEXT," + "action_on_lead TEXT," + "approved_price TEXT," + "sales_org TEXT," + "division TEXT," +
                    "distribution_channel TEXT," + "document_type TEXT," + "customer_reference_no TEXT," + "customer_reference_date TEXT," + "valid_to_date TEXT," +
                    "material_number TEXT," + "sold_to_party TEXT," + "ship_to_party TEXT," + "PO_method TEXT," + "share_lead_site_details_pic TEXT," +
                    "type_lead TEXT," + "lead_remarks TEXT," + "lead_action TEXT," + "credit_terms TEXT," + "month_qty TEXT," + "quotation_provided TEXT," +
                    "quotation_provided_date TEXT," + "mis_submission_date TEXT," + "hos_submission_date TEXT," + "download_time TEXT," + "destination TEXT," +
                    "company_constraint TEXT," + "reason TEXT," + "nov TEXT," + "incoterms TEXT," + "serving_location TEXT," + "quoted_price TEXT," +
                    "tpc TEXT," + "payment TEXT," + "last_price TEXT," + "prev_last_price TEXT," + "lead_quotation_status TEXT," + "lost_order_reason TEXT,"
                    + "quotation_number TEXT,"+ "quotation_pdf TEXT,"+ "po_number TEXT,"+ "po_date TEXT,"+ "po_image TEXT,"+ "po_revert_note TEXT,"
                    + "po_revert_level TEXT,"+ "po_foward_note TEXT,"+ "contract_number TEXT,"+ "sales_order_number TEXT"+ ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        } else {
            // Define all required columns with their types (matching the CREATE TABLE above)
            Map<String, String> requiredColumns = new LinkedHashMap<>();
            requiredColumns.put("lead_generation_id", "TEXT");
            requiredColumns.put("emp_code", "TEXT");
            requiredColumns.put("latitude", "TEXT");
            requiredColumns.put("longitude", "TEXT");
            requiredColumns.put("lead_type", "TEXT");
            requiredColumns.put("party_name", "TEXT");
            requiredColumns.put("branch", "TEXT");
            requiredColumns.put("district", "TEXT");
            requiredColumns.put("state", "TEXT");
            requiredColumns.put("qty_req", "TEXT");
            requiredColumns.put("product_packaging", "TEXT");
            requiredColumns.put("exp_rate_per_bag", "TEXT");
            requiredColumns.put("contact_person_name", "TEXT");
            requiredColumns.put("designation", "TEXT");
            requiredColumns.put("contact_number", "TEXT");
            requiredColumns.put("mail_id", "TEXT");
            requiredColumns.put("mode", "TEXT");
            requiredColumns.put("quotation", "TEXT");
            requiredColumns.put("PO", "TEXT");
            requiredColumns.put("status", "TEXT");
            requiredColumns.put("remarks", "TEXT");
            requiredColumns.put("assigned_to", "TEXT");
            requiredColumns.put("self_other", "TEXT");
            requiredColumns.put("acc_block_is_required", "TEXT");
            requiredColumns.put("category_type_construction", "TEXT");
            requiredColumns.put("next_visit_date", "TEXT");
            requiredColumns.put("lead_status", "TEXT");
            requiredColumns.put("current_brand_used", "TEXT");
            requiredColumns.put("current_price", "TEXT");
            requiredColumns.put("current_price_competitor", "TEXT");
            requiredColumns.put("r_timing", "TEXT");
            requiredColumns.put("action_on_lead", "TEXT");
            requiredColumns.put("approved_price", "TEXT");
            requiredColumns.put("sales_org", "TEXT");
            requiredColumns.put("division", "TEXT");
            requiredColumns.put("distribution_channel", "TEXT");
            requiredColumns.put("document_type", "TEXT");
            requiredColumns.put("customer_reference_no", "TEXT");
            requiredColumns.put("customer_reference_date", "TEXT");
            requiredColumns.put("valid_to_date", "TEXT");
            requiredColumns.put("material_number", "TEXT");
            requiredColumns.put("sold_to_party", "TEXT");
            requiredColumns.put("ship_to_party", "TEXT");
            requiredColumns.put("PO_method", "TEXT");
            requiredColumns.put("share_lead_site_details_pic", "TEXT");
            requiredColumns.put("type_lead", "TEXT");
            requiredColumns.put("lead_remarks", "TEXT");
            requiredColumns.put("lead_action", "TEXT");
            requiredColumns.put("credit_terms", "TEXT");
            requiredColumns.put("month_qty", "TEXT");
            requiredColumns.put("quotation_provided", "TEXT");
            requiredColumns.put("quotation_provided_date", "TEXT");
            requiredColumns.put("mis_submission_date", "TEXT");
            requiredColumns.put("hos_submission_date", "TEXT");
            requiredColumns.put("download_time", "TEXT");
            requiredColumns.put("destination", "TEXT");
            requiredColumns.put("company_constraint", "TEXT");
            requiredColumns.put("reason", "TEXT");
            requiredColumns.put("nov", "TEXT");
            requiredColumns.put("incoterms", "TEXT");
            requiredColumns.put("serving_location", "TEXT");
            requiredColumns.put("quoted_price", "TEXT");
            requiredColumns.put("tpc", "TEXT");
            requiredColumns.put("payment", "TEXT");
            requiredColumns.put("last_price", "TEXT");
            requiredColumns.put("prev_last_price", "TEXT");
            requiredColumns.put("lead_quotation_status", "TEXT");
            requiredColumns.put("lost_order_reason", "TEXT");
            requiredColumns.put("quotation_number", "TEXT");
            requiredColumns.put("quotation_pdf", "TEXT");
            requiredColumns.put("po_number", "TEXT");
            requiredColumns.put("po_date", "TEXT");
            requiredColumns.put("po_image", "TEXT");
            requiredColumns.put("po_revert_note", "TEXT");
            requiredColumns.put("po_revert_level", "TEXT");
            requiredColumns.put("po_foward_note", "TEXT");
            requiredColumns.put("contract_number", "TEXT");
            requiredColumns.put("sales_order_number", "TEXT");

            // Get existing columns from the correct table
            Set<String> existingColumns = new HashSet<>();
            Cursor cursor = database.rawQuery("PRAGMA table_info(lead_list_master_table)", null);
            while (cursor.moveToNext()) {
                String columnName = cursor.getString(cursor.getColumnIndexOrThrow("name"));
                existingColumns.add(columnName.toLowerCase());
            }
            cursor.close();

            // Add missing columns to the correct table
            database.beginTransaction();
            try {
                for (Map.Entry<String, String> entry : requiredColumns.entrySet()) {
                    if (!existingColumns.contains(entry.getKey().toLowerCase())) {
                        String alterQuery = "ALTER TABLE lead_list_master_table ADD COLUMN " +
                                entry.getKey() + " " + entry.getValue();
                        database.execSQL(alterQuery);
                    }
                }
                database.setTransactionSuccessful();
            } finally {
                database.endTransaction();
            }
        }

        if (!isTableExists("customer_master_table")) {
            String createTable = "CREATE TABLE customer_master_table" +
                    "(" +
                    "cust_code TEXT," + "cust_name TEXT," + "phone_no TEXT," + "district TEXT," + "state TEXT," + "address TEXT," + "cust_type TEXT " +
                    ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("branch_list")) {
            String createTable = "CREATE TABLE branch_list ( value TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
    }

    public void createNewTable() {
        Log.d("TAG", "_DOWNLOAD_ createNewTable: 1");
        if (!isTableExists("sbg_feedback")) {
            Log.d("TAG", "_DOWNLOAD_ createNewTable: 2");
            String createTable = "CREATE TABLE sbg_feedback ( " +
                    "customer_code TEXT, " +
                    "competitor_code TEXT, " +
                    "quantity TEXT," +
                    "date_time TEXT, " +
                    "flag INTEGER DEFAULT 1 " +
                    ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
    }

    public void createNewTableForQueryAndLead() {
        if (!isTableExists("sold_to_party_query_list")) {
            String createTable = "CREATE TABLE sold_to_party_query_list (code TEXT, name TEXT, customer_code TEXT, phone_no TEXT, district TEXT, state TEXT, address TEXT, related_emp_code TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("ship_to_party_query_list")) {
            String createTable = "CREATE TABLE ship_to_party_query_list (code TEXT, name TEXT, customer_code TEXT, phone_no TEXT, district TEXT, state TEXT, address TEXT, related_emp_code TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("assigned_to_query_list")) {
            String createTable = "CREATE TABLE assigned_to_query_list (emp_code TEXT, emp_name TEXT, related_emp_code TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("product_query_list")) {
            String createTable = "CREATE TABLE product_query_list (product_id TEXT, product_name TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("state_query_list")) {
            String createTable = "CREATE TABLE state_query_list (state_id TEXT, state_name TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("district_query_list")) {
            String createTable = "CREATE TABLE district_query_list (state_id TEXT, district_id TEXT, district_name TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("emp_master_query_list")) {
            String createTable = "CREATE TABLE emp_master_query_list (emp_code TEXT, emp_name TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("segment_query_list")) {
            String createTable = "CREATE TABLE segment_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("lead_source_query_list")) {
            String createTable = "CREATE TABLE lead_source_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("mode_of_payment_query_list")) {
            String createTable = "CREATE TABLE mode_of_payment_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("credit_terms_query_list")) {
            String createTable = "CREATE TABLE credit_terms_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("aac_block_required_check_query_list")) {
            String createTable = "CREATE TABLE aac_block_required_check_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("construction_type_query_list")) {
            String createTable = "CREATE TABLE construction_type_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("lead_status_query_list")) {
            String createTable = "CREATE TABLE lead_status_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("requirement_type_query_list")) {
            String createTable = "CREATE TABLE requirement_type_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("ex_work_query_list")) {
            String createTable = "CREATE TABLE ex_work_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("requirement_timing_query_list")) {
            String createTable = "CREATE TABLE requirement_timing_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
        if (!isTableExists("lead_action_query_list")) {
            String createTable = "CREATE TABLE lead_action_query_list (id TEXT, title TEXT);";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
    }

    public void createNewTableForCustomerAgeing(){
        if (!isTableExists("customer_ageing")) {
            Log.d("TAG", "_DOWNLOAD_ createNewTable: 2");
            String createTable = "CREATE TABLE customer_ageing ( " +
                    "customer_name TEXT, " +
                    "customer_code TEXT, " +
                    "title_1 TEXT, " +"value_1 TEXT, " +"invoice_count_1 TEXT, " +
                    "title_2 TEXT, " +"value_2 TEXT, " +"invoice_count_2 TEXT, " +
                    "title_3 TEXT, " +"value_3 TEXT, " +"invoice_count_3 TEXT, " +
                    "title_4 TEXT, " +"value_4 TEXT, " +"invoice_count_4 TEXT, " +
                    "title_5 TEXT, " +"value_5 TEXT, " +"invoice_count_5 TEXT, " +
                    "title_6 TEXT, " +"value_6 TEXT, " +"invoice_count_6 TEXT, " +
                    "title_7 TEXT, " +"value_7 TEXT, " +"invoice_count_7 TEXT, " +
                    "title_8 TEXT, " +"value_8 TEXT, " +"invoice_count_8 TEXT, " +
                    "title_9 TEXT, " +"value_9 TEXT, " +"invoice_count_9 TEXT, " +
                    "total_amount TEXT, " +
                    "total_invoice_count TEXT " +
                    ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }

        if (!isTableExists("customer_ageing_invoice_no")) {
            Log.d("TAG", "_DOWNLOAD_ createNewTable: 2");
            String createTable = "CREATE TABLE customer_ageing_invoice_no ( " +
                    "customer_name TEXT, " +
                    "customer_code TEXT, " +
                    "invoice_no TEXT, " +
                    "invoice_date TEXT, " +
                    "invoice_value TEXT, " +
                    "invoice_age TEXT " +
                    ");";
            database.beginTransaction();
            database.execSQL(createTable);
            database.setTransactionSuccessful();
            database.endTransaction();
        }
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< === >>>>>>>>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<<<<<<< CUSTOMER AGEING >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== Customer Ageing ===========================
    public void insertCustomerAgeing(CustomerAgeingDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM customer_ageing WHERE customer_code=?",
                new String[]{data.getCustomerCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            database.delete(
                    "customer_ageing",
                    "customer_code = ?",
                    new String[]{data.getCustomerCode()}
            );
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("customer_name", data.getCustomerName());
            cv.put("customer_code", data.getCustomerCode());
            cv.put("title_1", data.getTitle1());cv.put("value_1", data.getValue1());cv.put("invoice_count_1", data.getInvoiceCount1());
            cv.put("title_2", data.getTitle2());cv.put("value_2", data.getValue2());cv.put("invoice_count_2", data.getInvoiceCount2());
            cv.put("title_3", data.getTitle3());cv.put("value_3", data.getValue3());cv.put("invoice_count_3", data.getInvoiceCount3());
            cv.put("title_4", data.getTitle4());cv.put("value_4", data.getValue4());cv.put("invoice_count_4", data.getInvoiceCount4());
            cv.put("title_5", data.getTitle5());cv.put("value_5", data.getValue5());cv.put("invoice_count_5", data.getInvoiceCount5());
            cv.put("title_6", data.getTitle6());cv.put("value_6", data.getValue6());cv.put("invoice_count_6", data.getInvoiceCount6());
            cv.put("title_7", data.getTitle7());cv.put("value_7", data.getValue7());cv.put("invoice_count_7", data.getInvoiceCount7());
            cv.put("title_8", data.getTitle8());cv.put("value_8", data.getValue8());cv.put("invoice_count_8", data.getInvoiceCount8());
            cv.put("title_9", data.getTitle9());cv.put("value_9", data.getValue9());cv.put("invoice_count_9", data.getInvoiceCount9());
            cv.put("total_amount", data.getTotalAmount());
            cv.put("total_invoice_count", data.getTotalInvoiceCount());
            synchronized ("dbLock") {
                database.insertWithOnConflict("customer_ageing", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
            Log.d("TAG", "_DOWNLOAD_ insertCustomerAgeing: "+ignored.getMessage());
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getCustomerAgeingList(){
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM customer_ageing ORDER BY customer_name ASC";
            Cursor cursor = database.rawQuery(query,null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();
                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("customer_code")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("customer_name")));
                    leadList.add(leadData);
                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }
    public ArrayList<CustomerAgeingDataSet> getCustomerAgeing(String customerCode) {
        ArrayList<CustomerAgeingDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM customer_ageing WHERE customer_code = ?";
            Cursor cursor = database.rawQuery(query, new String[]{customerCode});

            if (cursor.moveToFirst()) {
                do {
                    CustomerAgeingDataSet leadData = new CustomerAgeingDataSet(
                            cursor.getString(cursor.getColumnIndexOrThrow("customer_name")),
                            cursor.getString(cursor.getColumnIndexOrThrow("customer_code")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_1")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_1")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_1")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_2")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_2")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_2")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_3")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_3")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_3")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_4")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_4")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_4")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_5")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_5")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_5")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_6")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_6")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_6")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_7")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_7")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_7")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_8")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_8")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_8")),
                            cursor.getString(cursor.getColumnIndexOrThrow("title_9")),
                            cursor.getString(cursor.getColumnIndexOrThrow("value_9")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_count_9")),
                            cursor.getString(cursor.getColumnIndexOrThrow("total_amount")),
                            cursor.getString(cursor.getColumnIndexOrThrow("total_invoice_count"))
                    );
                    leadList.add(leadData);
                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }
    // =========================== Customer Ageing Invoice No ===========================
    public void deleteCustomerAgeingInvoiceNumber(){
        database.delete("customer_ageing_invoice_no",null,null);
    }
    public void insertCustomerAgeingInvoiceNumber(CustomerAgeingInvoiceNumberDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM customer_ageing_invoice_no WHERE invoice_no=?",
                new String[]{data.getInvoiceNo()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            database.delete(
                    "customer_ageing_invoice_no",
                    "invoice_no = ?",
                    new String[]{data.getInvoiceNo()}
            );
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("customer_name", data.getCustomerName());
            cv.put("customer_code", data.getCustomerCode());
            cv.put("invoice_no", data.getInvoiceNo());
            cv.put("invoice_date", data.getInvoiceDate());
            cv.put("invoice_value", data.getInvoiceValue());
            cv.put("invoice_age", data.getInvoiceAge());
            synchronized ("dbLock") {
                database.insertWithOnConflict("customer_ageing_invoice_no", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<CustomerAgeingInvoiceNumberDataSet> getCustomerAgeingInvoiceNumber(String customerCode) {
        ArrayList<CustomerAgeingInvoiceNumberDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM customer_ageing_invoice_no WHERE customer_code = ? ORDER BY CAST(invoice_age AS INTEGER) ASC";
            Cursor cursor = database.rawQuery(query, new String[]{customerCode});

            if (cursor.moveToFirst()) {
                do {
                    CustomerAgeingInvoiceNumberDataSet leadData = new CustomerAgeingInvoiceNumberDataSet(
                            cursor.getString(cursor.getColumnIndexOrThrow("customer_name")),
                            cursor.getString(cursor.getColumnIndexOrThrow("customer_code")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_no")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_date")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_value")),
                            cursor.getString(cursor.getColumnIndexOrThrow("invoice_age"))
                    );
                    leadList.add(leadData);
                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< CUSTOMER AGEING >>>>>>>>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<<<<<<< QUERY & LEAD GENERATION >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== Sold To Party DataSet ===========================
    public void insertSoldToParty(PartyDataList data, String emp_code) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM sold_to_party_query_list WHERE code=?",
                new String[]{data.getCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("code", data.getCode());
            cv.put("name", data.getName());
            cv.put("customer_code", data.getCustomerCode());
            cv.put("phone_no", data.getPhoneNo());
            cv.put("district", data.getDistrict());
            cv.put("state", data.getState());
            cv.put("address", data.getAddress());
            cv.put("related_emp_code", emp_code);
            synchronized ("dbLock") {
                database.insertWithOnConflict("sold_to_party_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<PartyDataList> getAllSoldToParty(String emp_code) {
        ArrayList<PartyDataList> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM sold_to_party_query_list WHERE related_emp_code = ?";
            Cursor cursor = database.rawQuery(query, new String[]{emp_code});

            if (cursor.moveToFirst()) {
                do {
                    PartyDataList leadData = new PartyDataList();

                    leadData.setCode(cursor.getString(cursor.getColumnIndexOrThrow("code")));
                    leadData.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                    leadData.setCustomerCode(cursor.getString(cursor.getColumnIndexOrThrow("customer_code")));
                    leadData.setPhoneNo(cursor.getString(cursor.getColumnIndexOrThrow("phone_no")));
                    leadData.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                    leadData.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                    leadData.setAddress(cursor.getString(cursor.getColumnIndexOrThrow("address")));

                    Log.d("TAG", "_DOWNLOAD_ getAllSoldToParty: " + cursor.getString(cursor.getColumnIndexOrThrow("related_emp_code")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Ship To Party DataSet ===========================
    public void insertShipToParty(PartyDataList data, String emp_code) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM ship_to_party_query_list WHERE code=?",
                new String[]{data.getCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("code", data.getCode());
            cv.put("name", data.getName());
            cv.put("customer_code", data.getCustomerCode());
            cv.put("phone_no", data.getPhoneNo());
            cv.put("district", data.getDistrict());
            cv.put("state", data.getState());
            cv.put("address", data.getAddress());
            cv.put("related_emp_code", emp_code);
            synchronized ("dbLock") {
                database.insertWithOnConflict("ship_to_party_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<PartyDataList> getAllShipToParty(String emp_code) {
        ArrayList<PartyDataList> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM ship_to_party_query_list WHERE related_emp_code = ?";
            Cursor cursor = database.rawQuery(query, new String[]{emp_code});

            if (cursor.moveToFirst()) {
                do {
                    PartyDataList leadData = new PartyDataList();

                    leadData.setCode(cursor.getString(cursor.getColumnIndexOrThrow("code")));
                    leadData.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                    leadData.setCustomerCode(cursor.getString(cursor.getColumnIndexOrThrow("customer_code")));
                    leadData.setPhoneNo(cursor.getString(cursor.getColumnIndexOrThrow("phone_no")));
                    leadData.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                    leadData.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                    leadData.setAddress(cursor.getString(cursor.getColumnIndexOrThrow("address")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Assigned To DataSet ===========================
    public void insertAssignedTo(EmployeeDataSet data, String emp_code) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM assigned_to_query_list WHERE emp_code=? AND related_emp_code=?",
                new String[]{data.getEmp_code(), emp_code}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", data.getEmp_code());
            cv.put("emp_name", data.getEmp_name());
            synchronized ("dbLock") {
                database.insertWithOnConflict("assigned_to_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<EmployeeDataSet> getAllAssignedTo(String emp_code) {
        ArrayList<EmployeeDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM assigned_to_query_list WHERE related_emp_code = ?";
            Cursor cursor = database.rawQuery(query, new String[]{emp_code});

            if (cursor.moveToFirst()) {
                do {
                    EmployeeDataSet leadData = new EmployeeDataSet();

                    leadData.setEmp_code(cursor.getString(cursor.getColumnIndexOrThrow("emp_code")));
                    leadData.setEmp_name(cursor.getString(cursor.getColumnIndexOrThrow("emp_name")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Product DataSet ===========================
    public void insertProductList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM product_query_list WHERE product_id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", data.getId());
            cv.put("emp_name", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("product_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllProductList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM product_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("product_id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("product_name")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== State DataSet ===========================
    public void insertStateList(StateDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM state_query_list WHERE state_id=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("state_id", data.getTitle());
            cv.put("state_name", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("state_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<StateDataSet> getAllStateList() {
        ArrayList<StateDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM state_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    StateDataSet leadData = new StateDataSet();

                    leadData.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("state_id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("state_name")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== District DataSet ===========================
    public void insertDistrictList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM district_query_list WHERE state_id=? AND district_id=?",
                new String[]{data.getStateCode(), data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("state_id", data.getStateCode());
            cv.put("district_id", data.getTitle());
            cv.put("district_name", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("district_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet> getAllDistrictList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM district_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet();

                    leadData.setStateCode(cursor.getString(cursor.getColumnIndexOrThrow("state_id")));
                    leadData.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("district_id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("district_name")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Employee Details DataSet ===========================
    public void insertEmployeeDetails(EmployeeDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM emp_master_query_list WHERE emp_code=?",
                new String[]{data.getEmp_code()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", data.getEmp_code());
            cv.put("emp_name", data.getEmp_name());
            synchronized ("dbLock") {
                database.insertWithOnConflict("emp_master_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<EmployeeDataSet> getAllEmployeeDetails() {
        ArrayList<EmployeeDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM emp_master_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    EmployeeDataSet leadData = new EmployeeDataSet();

                    leadData.setEmp_code(cursor.getString(cursor.getColumnIndexOrThrow("emp_code")));
                    leadData.setEmp_name(cursor.getString(cursor.getColumnIndexOrThrow("emp_name")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Segment DataSet ===========================
    public void insertSegmentList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM segment_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("segment_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllSegmentList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM segment_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Lead Source DataSet ===========================
    public void insertLeadSourceList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM lead_source_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("lead_source_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllLeadSourceList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM lead_source_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Mode Of Payment DataSet ===========================
    public void insertModeOfPaymentList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM mode_of_payment_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("mode_of_payment_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllModeOfPaymentList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM mode_of_payment_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Credit Terms DataSet ===========================
    public void insertCreditTermsList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM credit_terms_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("credit_terms_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllCreditTermsList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM credit_terms_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== AAC Block Required DataSet ===========================
    public void insertAacBlockRequiredList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM aac_block_required_check_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("aac_block_required_check_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllAacBlockRequiredList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM aac_block_required_check_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Construction Type DataSet ===========================
    public void insertConstructionTypeList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM construction_type_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("construction_type_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllConstructionTypeList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM construction_type_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Lead Status DataSet ===========================
    public void insertLeadStatusList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM lead_status_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("lead_status_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllLeadStatusList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM lead_status_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Requirement Type DataSet ===========================
    public void insertRequirementTypeList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM requirement_type_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("requirement_type_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllRequirementTypeList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM requirement_type_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Ex Works DataSet ===========================
    public void insertExWorksList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM ex_work_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("ex_work_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllExWorksList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM ex_work_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Requirement Timing DataSet ===========================
    public void insertRequirementTimingList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM requirement_timing_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("requirement_timing_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllRequirementTimingList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM requirement_timing_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }

    // =========================== Lead Action DataSet ===========================
    public void insertLeadActionList(com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM lead_action_query_list WHERE id=?",
                new String[]{data.getId()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("id", data.getId());
            cv.put("title", data.getValue());
            synchronized ("dbLock") {
                database.insertWithOnConflict("lead_action_query_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> getAllLeadActionList() {
        ArrayList<com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM lead_action_query_list";
            Cursor cursor = database.rawQuery(query, null);

            if (cursor.moveToFirst()) {
                do {
                    com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet leadData = new com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet();

                    leadData.setId(cursor.getString(cursor.getColumnIndexOrThrow("id")));
                    leadData.setValue(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception ignored) {
        }

        return leadList;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< === >>>>>>>>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<<<<<<< LEAD FUNNEL >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== Existing Lead List DataSet ===========================
    public void insertLeadListMasterTableDataBatch(List<LeadListMasterTableDataSet> dataList) {
        database.beginTransaction();
        try {
            // Delete all existing data first
            database.delete("lead_list_master_table", null, null);

            Log.d("TAG", "_DOWNLOAD_ insertLeadListMasterTableDataBatch: "+dataList.size());

            // Insert all fresh
            for (LeadListMasterTableDataSet data : dataList) {
                ContentValues cv = new ContentValues();
                cv.put("lead_generation_id", data.getLead_generation_id());
                cv.put("emp_code", data.getEmp_code());
                cv.put("latitude", data.getLatitude());
                cv.put("longitude", data.getLongitude());
                cv.put("lead_type", data.getLead_type());
                cv.put("party_name", data.getParty_name());
                cv.put("branch", data.getBranch());
                cv.put("district", data.getDistrict());
                cv.put("state", data.getState());
                cv.put("qty_req", data.getQty_req());
                cv.put("product_packaging", data.getProduct_packaging());
                cv.put("exp_rate_per_bag", data.getExp_rate_per_bag());
                cv.put("contact_person_name", data.getContact_person_name());
                cv.put("designation", data.getDesignation());
                cv.put("contact_number", data.getContact_number());
                cv.put("mail_id", data.getMail_id());
                cv.put("mode", data.getMode());
                cv.put("quotation", data.getQuotation());
                cv.put("PO", data.getPO());
                cv.put("status", data.getStatus());
                cv.put("remarks", data.getRemarks());
                cv.put("assigned_to", data.getAssigned_to());
                cv.put("self_other", data.getSelf_other());
                cv.put("acc_block_is_required", data.getAcc_block_is_required());
                cv.put("category_type_construction", data.getCategory_type_construction());
                cv.put("next_visit_date", data.getNext_visit_date());
                cv.put("lead_status", data.getLead_status());
                cv.put("current_brand_used", data.getCurrent_brand_used());
                cv.put("current_price", data.getCurrent_price());
                cv.put("current_price_competitor", data.getCurrent_price_competitor());
                cv.put("r_timing", data.getR_timing());
                cv.put("action_on_lead", data.getAction_on_lead());
                cv.put("approved_price", data.getApproved_price());
                cv.put("sales_org", data.getSales_org());
                cv.put("division", data.getDivision());
                cv.put("distribution_channel", data.getDistribution_channel());
                cv.put("document_type", data.getDocument_type());
                cv.put("customer_reference_no", data.getCustomer_reference_no());
                cv.put("customer_reference_date", data.getCustomer_reference_date());
                cv.put("valid_to_date", data.getValid_to_date());
                cv.put("material_number", data.getMaterial_number());
                cv.put("sold_to_party", data.getSold_to_party());
                cv.put("ship_to_party", data.getShip_to_party());
                cv.put("PO_method", data.getPO_method());
                cv.put("share_lead_site_details_pic", data.getShare_lead_site_details_pic());
                cv.put("type_lead", data.getType_lead());
                cv.put("lead_remarks", data.getLead_remarks());
                cv.put("lead_action", data.getLead_action());
                cv.put("credit_terms", data.getCredit_terms());
                cv.put("month_qty", data.getMonth_qty());
                cv.put("quotation_provided", data.getQuotation_provided());
                cv.put("quotation_provided_date", data.getQuotation_provided_date());
                cv.put("mis_submission_date", data.getMis_submission_date());
                cv.put("hos_submission_date", data.getHos_submission_date());
                cv.put("download_time", data.getDownload_time());
                cv.put("destination", data.getDestination());
                cv.put("company_constraint", data.getCompany_constraint());
                cv.put("reason", data.getReason());
                cv.put("nov", data.getNov());
                cv.put("incoterms", data.getIncoterms());
                cv.put("serving_location", data.getServing_location());
                cv.put("quoted_price", data.getQuoted_price());
                cv.put("tpc", data.getTpc());
                cv.put("payment", data.getPayment());
                cv.put("last_price", data.getLast_price());
                cv.put("prev_last_price", data.getPrev_last_price());
                cv.put("lead_quotation_status", data.getLead_quotation_status());
                cv.put("lost_order_reason", data.getLost_reason());
                cv.put("quotation_number", data.getQuotation_number());
                cv.put("quotation_pdf", data.getQuotation_pdf());
                cv.put("po_number", data.getPo_number());
                cv.put("po_date", data.getPo_date());
                cv.put("po_image", data.getPo_image());
                cv.put("po_revert_note", data.getPo_revert_note());
                cv.put("po_revert_level", data.getPo_revert_level());
                cv.put("po_foward_note", data.getPo_foward_note());
                cv.put("contract_number", data.getContract_number());
                cv.put("sales_order_number", data.getSales_order_number());
                database.insertWithOnConflict("lead_list_master_table", null, cv, SQLiteDatabase.CONFLICT_REPLACE);
            }

            database.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ insertLeadListMasterTableDataBatch: " + e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<LeadListMasterTableDataSet> getAllLeadListMasterTableData(int value) {
        ArrayList<LeadListMasterTableDataSet> leadList = new ArrayList<>();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM lead_list_master_table " +
                    "WHERE CAST(lead_quotation_status AS INTEGER) >= ?";
            Cursor cursor = database.rawQuery(query, new String[]{String.valueOf(value)});

            if (cursor.moveToFirst()) {
                do {
                    LeadListMasterTableDataSet leadData = new LeadListMasterTableDataSet();

                    leadData.setLead_generation_id(cursor.getString(cursor.getColumnIndexOrThrow("lead_generation_id")));
                    leadData.setEmp_code(cursor.getString(cursor.getColumnIndexOrThrow("emp_code")));
                    leadData.setParty_name(cursor.getString(cursor.getColumnIndexOrThrow("party_name")));
                    leadData.setBranch(cursor.getString(cursor.getColumnIndexOrThrow("branch")));
                    leadData.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                    leadData.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                    leadData.setContact_person_name(cursor.getString(cursor.getColumnIndexOrThrow("contact_person_name")));
                    leadData.setContact_number(cursor.getString(cursor.getColumnIndexOrThrow("contact_number")));
                    leadData.setLead_type(cursor.getString(cursor.getColumnIndexOrThrow("lead_type")));
                    leadData.setLead_status(cursor.getString(cursor.getColumnIndexOrThrow("lead_status")));
                    leadData.setLead_quotation_status(cursor.getString(cursor.getColumnIndexOrThrow("lead_quotation_status")));
                    leadData.setStatus(cursor.getString(cursor.getColumnIndexOrThrow("status")));
                    leadData.setRemarks(cursor.getString(cursor.getColumnIndexOrThrow("remarks")));
                    leadData.setNext_visit_date(cursor.getString(cursor.getColumnIndexOrThrow("next_visit_date")));
                    leadData.setAssigned_to(cursor.getString(cursor.getColumnIndexOrThrow("assigned_to")));
                    leadData.setQuotation(cursor.getString(cursor.getColumnIndexOrThrow("quotation")));
                    leadData.setApproved_price(cursor.getString(cursor.getColumnIndexOrThrow("approved_price")));
                    leadData.setQuoted_price(cursor.getString(cursor.getColumnIndexOrThrow("quoted_price")));
                    leadData.setQty_req(cursor.getString(cursor.getColumnIndexOrThrow("qty_req")));
                    leadData.setProduct_packaging(cursor.getString(cursor.getColumnIndexOrThrow("product_packaging")));
                    leadData.setMode(cursor.getString(cursor.getColumnIndexOrThrow("mode")));
                    leadData.setAction_on_lead(cursor.getString(cursor.getColumnIndexOrThrow("action_on_lead")));
                    leadData.setDownload_time(cursor.getString(cursor.getColumnIndexOrThrow("download_time")));
                    leadData.setDestination(cursor.getString(cursor.getColumnIndexOrThrow("destination")));
                    leadData.setPayment(cursor.getString(cursor.getColumnIndexOrThrow("payment")));
                    leadData.setCredit_terms(cursor.getString(cursor.getColumnIndexOrThrow("credit_terms")));
                    leadData.setMonth_qty(cursor.getString(cursor.getColumnIndexOrThrow("month_qty")));
                    leadData.setTpc(cursor.getString(cursor.getColumnIndexOrThrow("tpc")));
                    leadData.setIncoterms(cursor.getString(cursor.getColumnIndexOrThrow("incoterms")));
                    leadData.setServing_location(cursor.getString(cursor.getColumnIndexOrThrow("serving_location")));
                    leadData.setLast_price(cursor.getString(cursor.getColumnIndexOrThrow("last_price")));
                    leadData.setPrev_last_price(cursor.getString(cursor.getColumnIndexOrThrow("prev_last_price")));
                    leadData.setCurrent_brand_used(cursor.getString(cursor.getColumnIndexOrThrow("current_brand_used")));
                    leadData.setCurrent_price(cursor.getString(cursor.getColumnIndexOrThrow("current_price")));
                    leadData.setCurrent_price_competitor(cursor.getString(cursor.getColumnIndexOrThrow("current_price_competitor")));
                    leadData.setSales_org(cursor.getString(cursor.getColumnIndexOrThrow("sales_org")));
                    leadData.setDivision(cursor.getString(cursor.getColumnIndexOrThrow("division")));
                    leadData.setDistribution_channel(cursor.getString(cursor.getColumnIndexOrThrow("distribution_channel")));
                    leadData.setDocument_type(cursor.getString(cursor.getColumnIndexOrThrow("document_type")));
                    leadData.setPO(cursor.getString(cursor.getColumnIndexOrThrow("PO")));
                    leadData.setPO_method(cursor.getString(cursor.getColumnIndexOrThrow("PO_method")));
                    leadData.setSold_to_party(cursor.getString(cursor.getColumnIndexOrThrow("sold_to_party")));
                    leadData.setShip_to_party(cursor.getString(cursor.getColumnIndexOrThrow("ship_to_party")));
                    leadData.setCustomer_reference_no(cursor.getString(cursor.getColumnIndexOrThrow("customer_reference_no")));
                    leadData.setCustomer_reference_date(cursor.getString(cursor.getColumnIndexOrThrow("customer_reference_date")));
                    leadData.setValid_to_date(cursor.getString(cursor.getColumnIndexOrThrow("valid_to_date")));
                    leadData.setMaterial_number(cursor.getString(cursor.getColumnIndexOrThrow("material_number")));
                    leadData.setMis_submission_date(cursor.getString(cursor.getColumnIndexOrThrow("mis_submission_date")));
                    leadData.setHos_submission_date(cursor.getString(cursor.getColumnIndexOrThrow("hos_submission_date")));
                    leadData.setShare_lead_site_details_pic(cursor.getString(cursor.getColumnIndexOrThrow("share_lead_site_details_pic")));
                    leadData.setType_lead(cursor.getString(cursor.getColumnIndexOrThrow("type_lead")));
                    leadData.setLead_remarks(cursor.getString(cursor.getColumnIndexOrThrow("lead_remarks")));
                    leadData.setLead_action(cursor.getString(cursor.getColumnIndexOrThrow("lead_action")));
                    leadData.setQuotation_provided(cursor.getString(cursor.getColumnIndexOrThrow("quotation_provided")));
                    leadData.setQuotation_provided_date(cursor.getString(cursor.getColumnIndexOrThrow("quotation_provided_date")));
                    leadData.setCompany_constraint(cursor.getString(cursor.getColumnIndexOrThrow("company_constraint")));
                    leadData.setReason(cursor.getString(cursor.getColumnIndexOrThrow("reason")));
                    leadData.setNov(cursor.getString(cursor.getColumnIndexOrThrow("nov")));
                    leadData.setR_timing(cursor.getString(cursor.getColumnIndexOrThrow("r_timing")));
                    leadData.setAcc_block_is_required(cursor.getString(cursor.getColumnIndexOrThrow("acc_block_is_required")));
                    leadData.setCategory_type_construction(cursor.getString(cursor.getColumnIndexOrThrow("category_type_construction")));
                    leadData.setSelf_other(cursor.getString(cursor.getColumnIndexOrThrow("self_other")));
                    leadData.setExp_rate_per_bag(cursor.getString(cursor.getColumnIndexOrThrow("exp_rate_per_bag")));
                    leadData.setMail_id(cursor.getString(cursor.getColumnIndexOrThrow("mail_id")));
                    leadData.setDesignation(cursor.getString(cursor.getColumnIndexOrThrow("designation")));
                    leadData.setLatitude(cursor.getString(cursor.getColumnIndexOrThrow("latitude")));
                    leadData.setLongitude(cursor.getString(cursor.getColumnIndexOrThrow("longitude")));
                    leadData.setLost_reason(cursor.getString(cursor.getColumnIndexOrThrow("lost_order_reason")));
                    leadData.setQuotation_number(cursor.getString(cursor.getColumnIndexOrThrow("quotation_number")));
                    leadData.setQuotation_pdf(cursor.getString(cursor.getColumnIndexOrThrow("quotation_pdf")));
                    leadData.setPo_number(cursor.getString(cursor.getColumnIndexOrThrow("po_number")));
                    leadData.setPo_date(cursor.getString(cursor.getColumnIndexOrThrow("po_date")));
                    leadData.setPo_image(cursor.getString(cursor.getColumnIndexOrThrow("po_image")));
                    leadData.setPo_revert_note(cursor.getString(cursor.getColumnIndexOrThrow("po_revert_note")));
                    leadData.setPo_revert_level(cursor.getString(cursor.getColumnIndexOrThrow("po_revert_level")));
                    leadData.setPo_foward_note(cursor.getString(cursor.getColumnIndexOrThrow("po_foward_note")));
                    leadData.setContract_number(cursor.getString(cursor.getColumnIndexOrThrow("contract_number")));
                    leadData.setSales_order_number(cursor.getString(cursor.getColumnIndexOrThrow("sales_order_number")));

                    leadList.add(leadData);

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception e) {
        }

        return leadList;
    }

    public LeadListMasterTableDataSet getLeadGenerationDetails(String value) {
        LeadListMasterTableDataSet leadData = new LeadListMasterTableDataSet();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadData;
        }

        try {
            String query = "SELECT * FROM lead_list_master_table " +
                    "WHERE  lead_generation_id = ?";

            Cursor cursor = database.rawQuery(query, new String[]{String.valueOf(value)});

            if (cursor.moveToFirst()) {
                do {

                    leadData.setLead_generation_id(cursor.getString(cursor.getColumnIndexOrThrow("lead_generation_id")));
                    leadData.setEmp_code(cursor.getString(cursor.getColumnIndexOrThrow("emp_code")));
                    leadData.setParty_name(cursor.getString(cursor.getColumnIndexOrThrow("party_name")));
                    leadData.setBranch(cursor.getString(cursor.getColumnIndexOrThrow("branch")));
                    leadData.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                    leadData.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                    leadData.setContact_person_name(cursor.getString(cursor.getColumnIndexOrThrow("contact_person_name")));
                    leadData.setContact_number(cursor.getString(cursor.getColumnIndexOrThrow("contact_number")));
                    leadData.setLead_type(cursor.getString(cursor.getColumnIndexOrThrow("lead_type")));
                    leadData.setLead_status(cursor.getString(cursor.getColumnIndexOrThrow("lead_status")));
                    leadData.setLead_quotation_status(cursor.getString(cursor.getColumnIndexOrThrow("lead_quotation_status")));
                    leadData.setStatus(cursor.getString(cursor.getColumnIndexOrThrow("status")));
                    leadData.setRemarks(cursor.getString(cursor.getColumnIndexOrThrow("remarks")));
                    leadData.setNext_visit_date(cursor.getString(cursor.getColumnIndexOrThrow("next_visit_date")));
                    leadData.setAssigned_to(cursor.getString(cursor.getColumnIndexOrThrow("assigned_to")));
                    leadData.setQuotation(cursor.getString(cursor.getColumnIndexOrThrow("quotation")));
                    leadData.setApproved_price(cursor.getString(cursor.getColumnIndexOrThrow("approved_price")));
                    leadData.setQuoted_price(cursor.getString(cursor.getColumnIndexOrThrow("quoted_price")));
                    leadData.setQty_req(cursor.getString(cursor.getColumnIndexOrThrow("qty_req")));
                    leadData.setProduct_packaging(cursor.getString(cursor.getColumnIndexOrThrow("product_packaging")));
                    leadData.setMode(cursor.getString(cursor.getColumnIndexOrThrow("mode")));
                    leadData.setAction_on_lead(cursor.getString(cursor.getColumnIndexOrThrow("action_on_lead")));
                    leadData.setDownload_time(cursor.getString(cursor.getColumnIndexOrThrow("download_time")));
                    leadData.setDestination(cursor.getString(cursor.getColumnIndexOrThrow("destination")));
                    leadData.setPayment(cursor.getString(cursor.getColumnIndexOrThrow("payment")));
                    leadData.setCredit_terms(cursor.getString(cursor.getColumnIndexOrThrow("credit_terms")));
                    leadData.setMonth_qty(cursor.getString(cursor.getColumnIndexOrThrow("month_qty")));
                    leadData.setTpc(cursor.getString(cursor.getColumnIndexOrThrow("tpc")));
                    leadData.setIncoterms(cursor.getString(cursor.getColumnIndexOrThrow("incoterms")));
                    leadData.setServing_location(cursor.getString(cursor.getColumnIndexOrThrow("serving_location")));
                    leadData.setLast_price(cursor.getString(cursor.getColumnIndexOrThrow("last_price")));
                    leadData.setPrev_last_price(cursor.getString(cursor.getColumnIndexOrThrow("prev_last_price")));
                    leadData.setCurrent_brand_used(cursor.getString(cursor.getColumnIndexOrThrow("current_brand_used")));
                    leadData.setCurrent_price(cursor.getString(cursor.getColumnIndexOrThrow("current_price")));
                    leadData.setCurrent_price_competitor(cursor.getString(cursor.getColumnIndexOrThrow("current_price_competitor")));
                    leadData.setSales_org(cursor.getString(cursor.getColumnIndexOrThrow("sales_org")));
                    leadData.setDivision(cursor.getString(cursor.getColumnIndexOrThrow("division")));
                    leadData.setDistribution_channel(cursor.getString(cursor.getColumnIndexOrThrow("distribution_channel")));
                    leadData.setDocument_type(cursor.getString(cursor.getColumnIndexOrThrow("document_type")));
                    leadData.setPO(cursor.getString(cursor.getColumnIndexOrThrow("PO")));
                    leadData.setPO_method(cursor.getString(cursor.getColumnIndexOrThrow("PO_method")));
                    leadData.setSold_to_party(cursor.getString(cursor.getColumnIndexOrThrow("sold_to_party")));
                    leadData.setShip_to_party(cursor.getString(cursor.getColumnIndexOrThrow("ship_to_party")));
                    leadData.setCustomer_reference_no(cursor.getString(cursor.getColumnIndexOrThrow("customer_reference_no")));
                    leadData.setCustomer_reference_date(cursor.getString(cursor.getColumnIndexOrThrow("customer_reference_date")));
                    leadData.setValid_to_date(cursor.getString(cursor.getColumnIndexOrThrow("valid_to_date")));
                    leadData.setMaterial_number(cursor.getString(cursor.getColumnIndexOrThrow("material_number")));
                    leadData.setMis_submission_date(cursor.getString(cursor.getColumnIndexOrThrow("mis_submission_date")));
                    leadData.setHos_submission_date(cursor.getString(cursor.getColumnIndexOrThrow("hos_submission_date")));
                    leadData.setShare_lead_site_details_pic(cursor.getString(cursor.getColumnIndexOrThrow("share_lead_site_details_pic")));
                    leadData.setType_lead(cursor.getString(cursor.getColumnIndexOrThrow("type_lead")));
                    leadData.setLead_remarks(cursor.getString(cursor.getColumnIndexOrThrow("lead_remarks")));
                    leadData.setLead_action(cursor.getString(cursor.getColumnIndexOrThrow("lead_action")));
                    leadData.setQuotation_provided(cursor.getString(cursor.getColumnIndexOrThrow("quotation_provided")));
                    leadData.setQuotation_provided_date(cursor.getString(cursor.getColumnIndexOrThrow("quotation_provided_date")));
                    leadData.setCompany_constraint(cursor.getString(cursor.getColumnIndexOrThrow("company_constraint")));
                    leadData.setReason(cursor.getString(cursor.getColumnIndexOrThrow("reason")));
                    leadData.setNov(cursor.getString(cursor.getColumnIndexOrThrow("nov")));
                    leadData.setR_timing(cursor.getString(cursor.getColumnIndexOrThrow("r_timing")));
                    leadData.setAcc_block_is_required(cursor.getString(cursor.getColumnIndexOrThrow("acc_block_is_required")));
                    leadData.setCategory_type_construction(cursor.getString(cursor.getColumnIndexOrThrow("category_type_construction")));
                    leadData.setSelf_other(cursor.getString(cursor.getColumnIndexOrThrow("self_other")));
                    leadData.setExp_rate_per_bag(cursor.getString(cursor.getColumnIndexOrThrow("exp_rate_per_bag")));
                    leadData.setMail_id(cursor.getString(cursor.getColumnIndexOrThrow("mail_id")));
                    leadData.setDesignation(cursor.getString(cursor.getColumnIndexOrThrow("designation")));
                    leadData.setLatitude(cursor.getString(cursor.getColumnIndexOrThrow("latitude")));
                    leadData.setLongitude(cursor.getString(cursor.getColumnIndexOrThrow("longitude")));
                    leadData.setLost_reason(cursor.getString(cursor.getColumnIndexOrThrow("lost_order_reason")));
                    leadData.setQuotation_number(cursor.getString(cursor.getColumnIndexOrThrow("quotation_number")));
                    leadData.setQuotation_pdf(cursor.getString(cursor.getColumnIndexOrThrow("quotation_pdf")));
                    leadData.setPo_number(cursor.getString(cursor.getColumnIndexOrThrow("po_number")));
                    leadData.setPo_date(cursor.getString(cursor.getColumnIndexOrThrow("po_date")));
                    leadData.setPo_image(cursor.getString(cursor.getColumnIndexOrThrow("po_image")));
                    leadData.setPo_revert_note(cursor.getString(cursor.getColumnIndexOrThrow("po_revert_note")));
                    leadData.setPo_revert_level(cursor.getString(cursor.getColumnIndexOrThrow("po_revert_level")));
                    leadData.setPo_foward_note(cursor.getString(cursor.getColumnIndexOrThrow("po_foward_note")));
                    leadData.setContract_number(cursor.getString(cursor.getColumnIndexOrThrow("contract_number")));
                    leadData.setSales_order_number(cursor.getString(cursor.getColumnIndexOrThrow("sales_order_number")));

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception e) {
        }

        return leadData;
    }

    public int getCountLeadListMasterTableData(int value, boolean checker) {
        int count = 0;

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return count;
        }

        try {
            String query;

            if (checker) {
                query = "SELECT * FROM lead_list_master_table " +
                        "WHERE CAST(lead_quotation_status AS INTEGER) >= ?";
            } else {
                query = "SELECT * FROM lead_list_master_table " +
                        "WHERE CAST(lead_quotation_status AS INTEGER) = ?";
            }

            Log.d("TAG", "LeadListMasterTableData query: " + query);

            Cursor cursor = database.rawQuery(query, new String[]{String.valueOf(value)});

            count = cursor.getCount();

        } catch (Exception e) {
            Log.d("TAG", "LeadListMasterTableData Count error: " + e.getMessage());
        }

        Log.d("TAG", "LeadListMasterTableData Count: " + count);

        return count;
    }

    public String getTotalQtyListMasterTableData(int value, boolean checker) {
        double totalQty = 0.0;

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return totalQty + "";
        }

        try {
            String query;

            if (checker) {
                query = "SELECT SUM(CAST(qty_req AS REAL)) FROM lead_list_master_table " +
                        "WHERE CAST(lead_quotation_status AS INTEGER) >= ?";
            } else {
                query = "SELECT SUM(CAST(qty_req AS REAL)) FROM lead_list_master_table " +
                        "WHERE CAST(lead_quotation_status AS INTEGER) = ?";
            }
            Log.d("TAG", "LeadListMasterTableData total qty query: " + query);
            Cursor cursor = database.rawQuery(query, new String[]{String.valueOf(value)});

            if (cursor != null && cursor.moveToFirst()) {
                totalQty = cursor.isNull(0) ? 0.0 : cursor.getDouble(0);
                cursor.close();
            }

        } catch (Exception e) {
            Log.d("TAG", "LeadListMasterTableData total qty: " + e.getMessage());
        }
        Log.d("TAG", "LeadListMasterTableData total qty: " + totalQty);
        return formatToKLCrRounded(totalQty);
    }

    @SuppressLint("DefaultLocale")
    public String formatToKLCrRounded(double value) {
        if (value >= 10000000) { // Crore
            return "~" + String.format("%.2f", value / 10000000) + "Cr";
        } else if (value >= 100000) { // Lakh
            return "~" + String.format("%.2f", value / 100000) + "L";
        } else if (value >= 1000) { // Thousand
            return "~" + String.format("%.2f", value / 1000) + "K";
        } else {
            return String.format("%.2f", value);
        }
    }

    // =========================== Customer List DataSet ===========================
    public void insertCustomerMasterTable(CustomerMasterTableDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM customer_master_table WHERE cust_code=? AND cust_type=?",
                new String[]{data.getCust_code(), data.getCust_type()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("cust_code", data.getCust_code());
            cv.put("cust_name", data.getCust_name());
            cv.put("phone_no", data.getPhone_no());
            cv.put("district", data.getDistrict());
            cv.put("state", data.getState());
            cv.put("address", data.getAddress());
            cv.put("cust_type", data.getCust_type());

            synchronized ("dbLock") {
                database.insertWithOnConflict("customer_master_table", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public CustomerMasterTableDataSet getCustomerDetails(String value) {
        CustomerMasterTableDataSet leadList = new CustomerMasterTableDataSet();

        if (database == null || !database.isOpen()) {
            Log.d("DB_ERROR", "Database is not open");
            return leadList;
        }

        try {
            String query = "SELECT * FROM customer_master_table " + "WHERE cust_code= ?";

            Cursor cursor = database.rawQuery(query, new String[]{value});

            if (cursor.moveToFirst()) {
                do {
                    leadList.setCust_code(cursor.getString(cursor.getColumnIndexOrThrow("cust_code")));
                    leadList.setCust_name(cursor.getString(cursor.getColumnIndexOrThrow("cust_name")));
                    leadList.setPhone_no(cursor.getString(cursor.getColumnIndexOrThrow("phone_no")));
                    leadList.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                    leadList.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                    leadList.setAddress(cursor.getString(cursor.getColumnIndexOrThrow("address")));
                    leadList.setCust_type(cursor.getString(cursor.getColumnIndexOrThrow("cust_type")));

                } while (cursor.moveToNext());

                cursor.close();
            }

        } catch (Exception e) {
        }

        return leadList;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< === >>>>>>>>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<<<<<<< MARKET FEEDBACK >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== Customer Competitor Quantity DataSet ===========================
    public void insertCustomerCompetitorQuantityBatch(ArrayList<String[]> rowList) {
        SQLiteDatabase db = this.getWritableDatabase();
        final int CHUNK_SIZE = 5000; // Safe chunk size for Samsung

        try {
            // Clear old data in its own transaction first
            db.beginTransaction();
            db.execSQL("DELETE FROM customer_competitor_quantity");
            db.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ delete error: " + e.getMessage());
            return;
        } finally {
            db.endTransaction();
        }

        String sql = "INSERT INTO customer_competitor_quantity " +
                "(competitor_quantity_id, customer_code, customer_name, mandatory, " +
                "competitor_name, type, quantity, customer_dns_code, flag) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        int total = rowList.size();
        int start = 0;

        while (start < total) {
            int end = Math.min(start + CHUNK_SIZE, total);
            List<String[]> chunk = rowList.subList(start, end);

            try {
                db.beginTransaction();
                SQLiteStatement stmt = db.compileStatement(sql);

                for (String[] row : chunk) {
                    stmt.clearBindings();
                    stmt.bindString(1, row[0]);
                    stmt.bindString(2, row[1]);
                    stmt.bindString(3, row[2]);
                    stmt.bindString(4, row[3]);
                    stmt.bindString(5, row[4]);
                    stmt.bindString(6, row[5]);
                    stmt.bindString(7, row[6]);
                    stmt.bindString(8, row[7]);
                    stmt.bindLong(9, 0);
                    stmt.executeInsert();
                }

                db.setTransactionSuccessful();
                Log.d("TAG", "_DOWNLOAD_ inserted chunk: " + end + " / " + total);
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ chunk insert error at " + start + ": " + e.getMessage());
            } finally {
                db.endTransaction();
            }

            start = end;
        }

        Log.d("TAG", "_DOWNLOAD_ batch insert done: " + total + " rows");
    }

    public int updateCustomerCompetitorQuantity(String customer_code, String competitor_code, String quantity) {
        int rowsUpdated = 0;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("quantity", quantity);
            cv.put("flag", 1);
            rowsUpdated = database.update(
                    "customer_competitor_quantity",
                    cv,
                    "customer_code=? AND competitor_quantity_id=?",
                    new String[]{customer_code, competitor_code}
            );
            database.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ updateCustomerCompetitorQuantity: " + e.getMessage());
        } finally {
            database.endTransaction();
        }
        return rowsUpdated;
    }

    public ArrayList<CustomerCompetitorQuantityDataSet> getAllCustomerCompetitorQuantity(String customer_code) {
        ArrayList<CustomerCompetitorQuantityDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM customer_competitor_quantity WHERE customer_code='" + customer_code + "'", null);
        if (cursor.moveToFirst()) {
            do {
                CustomerCompetitorQuantityDataSet data = new CustomerCompetitorQuantityDataSet();
                data.setCompetitorQuantityId(cursor.getString(cursor.getColumnIndexOrThrow("competitor_quantity_id")));
                data.setCustomerCode(cursor.getString(cursor.getColumnIndexOrThrow("customer_code")));
                data.setCustomerName(cursor.getString(cursor.getColumnIndexOrThrow("customer_name")));
                data.setMandatory(cursor.getString(cursor.getColumnIndexOrThrow("mandatory")));
                data.setCompetitorName(cursor.getString(cursor.getColumnIndexOrThrow("competitor_name")));
                data.setType(cursor.getString(cursor.getColumnIndexOrThrow("type")));
                data.setQuantity(cursor.getString(cursor.getColumnIndexOrThrow("quantity")));
                data.setCustomerDnsCode(cursor.getString(cursor.getColumnIndexOrThrow("customer_dns_code")));
                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        return totalList;
    }

    public String getQuantityAgainstCustomerAndCompetitor(String customer_code, String competitor_code) {
        String qty = "0";
        String query = "SELECT * FROM customer_competitor_quantity WHERE customer_code='" + customer_code + "' AND competitor_quantity_id='" + competitor_code + "'";
        Log.d("TAG", "_DOWNLOAD_ getQuantityAgainstCustomerAndCompetitor: " + query);
        Cursor cursor = database.rawQuery(query, null);
        if (cursor.moveToFirst()) {
            qty = cursor.getString(cursor.getColumnIndexOrThrow("quantity"));
        }
        cursor.close();
        return qty;
    }

    // =========================== SBG Feedback DataSet ===========================
    public void deleteAllDataFromSbgFeedback() {
        database.beginTransaction();
        try {
            database.delete("sbg_feedback", null, null);
            database.setTransactionSuccessful();
        } finally {
            database.endTransaction();
        }
    }

    @SuppressLint("SimpleDateFormat")
    public void insertDataFromSbgFeedback(CustomerCompetitorQuantityDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM sbg_feedback WHERE customer_code=? AND competitor_code=?",
                new String[]{data.getCustomerCode(), data.getCompetitorQuantityId()}
        );
        Log.d("TAG", "_DOWNLOAD_ insertDataFromSbgFeedback: " + data.getCustomerCode() + "  //  " + data.getCompetitorQuantityId());
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("TAG", "insertDataFromSbgFeedback: " + data.getCompetitorName());
            updateDataFromSbgFeedback(data);
            return;
        }
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        database.beginTransaction();
        try {
            ContentValues values = new ContentValues();
            values.put("customer_code", data.getCustomerCode());
            values.put("competitor_code", data.getCompetitorQuantityId());
            values.put("quantity", data.getQuantity());
            values.put("date_time", sdf.format(new Date()));
            values.put("flag", 0);
            database.insert("sbg_feedback", null, values);
            database.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ insertDataFromSbgFeedback: " + e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    @SuppressLint("SimpleDateFormat")
    public void updateDataFromSbgFeedback(CustomerCompetitorQuantityDataSet data) {
        int rowsUpdated = 0;
        database.beginTransaction();
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");

            ContentValues cv = new ContentValues();
            cv.put("quantity", data.getQuantity());
            cv.put("date_time", sdf.format(new Date()));
            cv.put("flag", 0);
            rowsUpdated = database.update(
                    "sbg_feedback",
                    cv,
                    "customer_code=? AND competitor_code=?",
                    new String[]{data.getCustomerCode(), data.getCompetitorQuantityId()}
            );
            database.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ updateDataFromSbgFeedback: " + e.getMessage());
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<SBGFeedbackDataSet> getAllSbgFeedbackWhereFlag1() {
        ArrayList<SBGFeedbackDataSet> list = new ArrayList<>();
        try {
            String query = "SELECT * FROM sbg_feedback";
            Cursor cursor = database.rawQuery(query, null);
            if (cursor.moveToFirst()) {
                do {
                    SBGFeedbackDataSet item = new SBGFeedbackDataSet();
                    item.setCustomerCode(cursor.getString(cursor.getColumnIndexOrThrow("customer_code")));
                    item.setCompetitorQuantityId(cursor.getString(cursor.getColumnIndexOrThrow("competitor_code")));
                    item.setQuantity(cursor.getString(cursor.getColumnIndexOrThrow("quantity")));
                    item.setDateTime(cursor.getString(cursor.getColumnIndexOrThrow("date_time")));
                    list.add(item);
                } while (cursor.moveToNext());
            }
            cursor.close();
        } catch (Exception e) {
            Log.d("TAG", "getAllSbgFeedbackWhereFlag1: " + e.getMessage());
        }
        return list;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< === >>>>>>>>>>>>>>>>>>>>>>>>>>>


    // <<<<<<<<<<<<<<<<<<<<<<<<<<< NEW SITE LEAD AND CONVERSION TRACKING >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== Site Lead DataSet ===========================
    public void insertSiteLead(DataForUpload dataForUpload, int flag) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM new_site_lead_and_conversion_tracking WHERE site_transaction_id=? AND site_unique_id=? AND customer_contact_number=?",
                new String[]{dataForUpload.getSite_transaction_id(), dataForUpload.getSite_unique_id(), dataForUpload.getCustomer_contact_number()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            updateFullSiteLead(dataForUpload, 1);
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("site_transaction_id", dataForUpload.getSite_transaction_id());
            cv.put("site_unique_id", dataForUpload.getSite_unique_id());
            cv.put("site_creation_date", dataForUpload.getSite_creation_date());
            cv.put("site_visit_date", dataForUpload.getSite_visit_date());
            cv.put("employee_code", dataForUpload.getEmployee_code());
            cv.put("employee_name", dataForUpload.getEmployee_name());
            cv.put("zone", dataForUpload.getZone());
            cv.put("branch", dataForUpload.getBranch());
            cv.put("state", dataForUpload.getState());
            cv.put("district", dataForUpload.getDistrict());
            cv.put("latitude", dataForUpload.getLatitude());
            cv.put("longitude", dataForUpload.getLongitude());
            cv.put("customer_name", dataForUpload.getCustomer_name());
            cv.put("customer_contact_number", dataForUpload.getCustomer_contact_number());
            cv.put("customer_full_address", dataForUpload.getCustomer_full_address());
            cv.put("is_register_contractor", dataForUpload.getIs_register_contractor());
            cv.put("contractor_name", dataForUpload.getContractor_name());
            cv.put("contractor_contact_number", dataForUpload.getContractor_contact_number());
            cv.put("is_register_engineer", dataForUpload.getIs_register_engineer());
            cv.put("engineer_name", dataForUpload.getEngineer_name());
            cv.put("engineer_contact_number", dataForUpload.getEngineer_contact_number());
            cv.put("meeting_person", dataForUpload.getMeeting_person());
            cv.put("decision_maker", dataForUpload.getDecision_maker());
            cv.put("site_segment", dataForUpload.getSite_segment());
            cv.put("visit_type", dataForUpload.getVisit_type());
            cv.put("project_segment", dataForUpload.getProject_segment());
            cv.put("type_of_construction", dataForUpload.getType_of_construction());
            cv.put("floor_count", dataForUpload.getFloor_count());
            cv.put("current_stage_of_construction", dataForUpload.getCurrent_stage_of_construction());
            cv.put("built_up_area", dataForUpload.getBuilt_up_area());
            cv.put("site_potential", dataForUpload.getSite_potential());
            cv.put("consumed_till_date", dataForUpload.getConsumed_till_date());
            cv.put("balance_potential", dataForUpload.getBalance_potential());
            cv.put("balance_potential_manual", dataForUpload.getBalance_potential_manual());
            cv.put("site_category", dataForUpload.getSite_category());
            cv.put("brand_used", dataForUpload.getBrand_used());
            cv.put("price_per_bag", dataForUpload.getPrice_per_bag());
            cv.put("conversion", dataForUpload.getConversion());
            cv.put("product_name", dataForUpload.getProduct_name());
            cv.put("order_quantity", dataForUpload.getOrder_quantity());
            cv.put("requested_date_of_delivery", dataForUpload.getRequested_date_of_delivery());
            cv.put("counter_type", dataForUpload.getCounter_type());
            cv.put("counter_name", dataForUpload.getCounter_name());
            cv.put("counter_code", dataForUpload.getCounter_code());
            cv.put("reason_for_non_conversion", dataForUpload.getReason_for_non_conversion());
            cv.put("site_priority", dataForUpload.getSite_priority());
            cv.put("weather_shield_demo", dataForUpload.getWeather_shield_demo());
            cv.put("approval_status", dataForUpload.getApproval_status());
            cv.put("date_time", dataForUpload.getDate_time());
            cv.put("asm_name", dataForUpload.getAsm_name());
            cv.put("asm_employee_id", dataForUpload.getAsm_employee_id());
            cv.put("actual_date_of_delivery", dataForUpload.getActual_date_of_delivery());
            cv.put("delivery_remarks", dataForUpload.getDelivery_remarks());
            cv.put("reason_for_not_delivery", dataForUpload.getReason_for_not_delivery());
            cv.put("site_status", dataForUpload.getSite_status());
            cv.put("remarks", dataForUpload.getRemarks());
            cv.put("flag", flag);

            synchronized ("dbLock") {
                database.insertWithOnConflict("new_site_lead_and_conversion_tracking", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public int updateSiteLead(String site_transaction_id, String site_unique_id, String customer_contact_number) {
        int rowsUpdated = 0;
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("flag", 1);
            rowsUpdated = database.update(
                    "new_site_lead_and_conversion_tracking",
                    cv,
                    "site_transaction_id=? AND site_unique_id=? AND customer_contact_number=?",
                    new String[]{site_transaction_id, site_unique_id, customer_contact_number}
            );
            database.setTransactionSuccessful();
        } catch (Exception e) {
            Log.d("DB_ERROR", "updateSiteLead error: " + e.getMessage());
        } finally {
            database.endTransaction();
        }
        return rowsUpdated;
    }

    public void updateFullSiteLead(DataForUpload dataForUpload, int flag) {
        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();

            cv.put("site_transaction_id", dataForUpload.getSite_transaction_id());
            cv.put("site_unique_id", dataForUpload.getSite_unique_id());
            cv.put("site_creation_date", dataForUpload.getSite_creation_date());
            cv.put("site_visit_date", dataForUpload.getSite_visit_date());
            cv.put("employee_code", dataForUpload.getEmployee_code());
            cv.put("employee_name", dataForUpload.getEmployee_name());
            cv.put("zone", dataForUpload.getZone());
            cv.put("branch", dataForUpload.getBranch());
            cv.put("state", dataForUpload.getState());
            cv.put("district", dataForUpload.getDistrict());
            cv.put("latitude", dataForUpload.getLatitude());
            cv.put("longitude", dataForUpload.getLongitude());
            cv.put("customer_name", dataForUpload.getCustomer_name());
            cv.put("customer_contact_number", dataForUpload.getCustomer_contact_number());
            cv.put("customer_full_address", dataForUpload.getCustomer_full_address());
            cv.put("is_register_contractor", dataForUpload.getIs_register_contractor());
            cv.put("contractor_name", dataForUpload.getContractor_name());
            cv.put("contractor_contact_number", dataForUpload.getContractor_contact_number());
            cv.put("is_register_engineer", dataForUpload.getIs_register_engineer());
            cv.put("engineer_name", dataForUpload.getEngineer_name());
            cv.put("engineer_contact_number", dataForUpload.getEngineer_contact_number());
            cv.put("meeting_person", dataForUpload.getMeeting_person());
            cv.put("decision_maker", dataForUpload.getDecision_maker());
            cv.put("site_segment", dataForUpload.getSite_segment());
            cv.put("visit_type", dataForUpload.getVisit_type());
            cv.put("project_segment", dataForUpload.getProject_segment());
            cv.put("type_of_construction", dataForUpload.getType_of_construction());
            cv.put("floor_count", dataForUpload.getFloor_count());
            cv.put("current_stage_of_construction", dataForUpload.getCurrent_stage_of_construction());
            cv.put("built_up_area", dataForUpload.getBuilt_up_area());
            cv.put("site_potential", dataForUpload.getSite_potential());
            cv.put("consumed_till_date", dataForUpload.getConsumed_till_date());
            cv.put("balance_potential", dataForUpload.getBalance_potential());
            cv.put("balance_potential_manual", dataForUpload.getBalance_potential_manual());
            cv.put("site_category", dataForUpload.getSite_category());
            cv.put("brand_used", dataForUpload.getBrand_used());
            cv.put("price_per_bag", dataForUpload.getPrice_per_bag());
            cv.put("conversion", dataForUpload.getConversion());
            cv.put("product_name", dataForUpload.getProduct_name());
            cv.put("order_quantity", dataForUpload.getOrder_quantity());
            cv.put("requested_date_of_delivery", dataForUpload.getRequested_date_of_delivery());
            cv.put("counter_type", dataForUpload.getCounter_type());
            cv.put("counter_name", dataForUpload.getCounter_name());
            cv.put("counter_code", dataForUpload.getCounter_code());
            cv.put("reason_for_non_conversion", dataForUpload.getReason_for_non_conversion());
            cv.put("site_priority", dataForUpload.getSite_priority());
            cv.put("weather_shield_demo", dataForUpload.getWeather_shield_demo());
            cv.put("approval_status", dataForUpload.getApproval_status());
            cv.put("date_time", dataForUpload.getDate_time());
            cv.put("asm_name", dataForUpload.getAsm_name());
            cv.put("asm_employee_id", dataForUpload.getAsm_employee_id());
            cv.put("actual_date_of_delivery", dataForUpload.getActual_date_of_delivery());
            cv.put("delivery_remarks", dataForUpload.getDelivery_remarks());
            cv.put("reason_for_not_delivery", dataForUpload.getReason_for_not_delivery());
            cv.put("site_status", dataForUpload.getSite_status());
            cv.put("remarks", dataForUpload.getRemarks());
            cv.put("flag", flag);

            if (database != null) {

                String transId = String.valueOf(dataForUpload.getSite_transaction_id());
                String uniqueId = String.valueOf(dataForUpload.getSite_unique_id());
                String mobile = String.valueOf(dataForUpload.getCustomer_contact_number());

                int rows = database.update(
                        "new_site_lead_and_conversion_tracking",
                        cv,
                        "site_transaction_id=? AND site_unique_id=? AND customer_contact_number=?",
                        new String[]{transId, uniqueId, mobile}
                );

                Log.d("DB", "uploadAllPendingSiteLead Rows updated: " + flag + "   " + rows);
            }
            assert database != null;
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            assert database != null;
            database.endTransaction();
        }
    }

    public void deleteSiteLead(String site_transaction_id) {
        database.beginTransaction();
        try {
            database.delete(
                    "new_site_lead_and_conversion_tracking",
                    "site_transaction_id=?",
                    new String[]{site_transaction_id}
            );
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataForUpload> getAllUnUploadedSiteLead() {
        ArrayList<DataForUpload> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM new_site_lead_and_conversion_tracking WHERE flag=0", null);
        if (cursor.moveToFirst()) {
            do {
                DataForUpload data = new DataForUpload();
                data.setSite_transaction_id(cursor.getString(cursor.getColumnIndexOrThrow("site_transaction_id")));
                data.setSite_unique_id(cursor.getString(cursor.getColumnIndexOrThrow("site_unique_id")));
                data.setSite_creation_date(cursor.getString(cursor.getColumnIndexOrThrow("site_creation_date")));
                data.setSite_visit_date(cursor.getString(cursor.getColumnIndexOrThrow("site_visit_date")));
                data.setEmployee_code(cursor.getString(cursor.getColumnIndexOrThrow("employee_code")));
                data.setEmployee_name(cursor.getString(cursor.getColumnIndexOrThrow("employee_name")));
                data.setZone(cursor.getString(cursor.getColumnIndexOrThrow("zone")));
                data.setBranch(cursor.getString(cursor.getColumnIndexOrThrow("branch")));
                data.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                data.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                data.setLatitude(cursor.getString(cursor.getColumnIndexOrThrow("latitude")));
                data.setLongitude(cursor.getString(cursor.getColumnIndexOrThrow("longitude")));
                data.setCustomer_name(cursor.getString(cursor.getColumnIndexOrThrow("customer_name")));
                data.setCustomer_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("customer_contact_number")));
                data.setCustomer_full_address(cursor.getString(cursor.getColumnIndexOrThrow("customer_full_address")));
                data.setIs_register_contractor(cursor.getString(cursor.getColumnIndexOrThrow("is_register_contractor")));
                data.setContractor_name(cursor.getString(cursor.getColumnIndexOrThrow("contractor_name")));
                data.setContractor_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("contractor_contact_number")));
                data.setIs_register_engineer(cursor.getString(cursor.getColumnIndexOrThrow("is_register_engineer")));
                data.setEngineer_name(cursor.getString(cursor.getColumnIndexOrThrow("engineer_name")));
                data.setEngineer_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("engineer_contact_number")));
                data.setMeeting_person(cursor.getString(cursor.getColumnIndexOrThrow("meeting_person")));
                data.setDecision_maker(cursor.getString(cursor.getColumnIndexOrThrow("decision_maker")));
                data.setSite_segment(cursor.getString(cursor.getColumnIndexOrThrow("site_segment")));
                data.setVisit_type(cursor.getString(cursor.getColumnIndexOrThrow("visit_type")));
                data.setProject_segment(cursor.getString(cursor.getColumnIndexOrThrow("project_segment")));
                data.setType_of_construction(cursor.getString(cursor.getColumnIndexOrThrow("type_of_construction")));
                data.setFloor_count(cursor.getString(cursor.getColumnIndexOrThrow("floor_count")));
                data.setCurrent_stage_of_construction(cursor.getString(cursor.getColumnIndexOrThrow("current_stage_of_construction")));
                data.setBuilt_up_area(cursor.getString(cursor.getColumnIndexOrThrow("built_up_area")));
                data.setSite_potential(cursor.getString(cursor.getColumnIndexOrThrow("site_potential")));
                data.setConsumed_till_date(cursor.getString(cursor.getColumnIndexOrThrow("consumed_till_date")));
                data.setBalance_potential(cursor.getString(cursor.getColumnIndexOrThrow("balance_potential")));
                data.setBalance_potential_manual(cursor.getString(cursor.getColumnIndexOrThrow("balance_potential_manual")));
                data.setSite_category(cursor.getString(cursor.getColumnIndexOrThrow("site_category")));
                data.setBrand_used(cursor.getString(cursor.getColumnIndexOrThrow("brand_used")));
                data.setPrice_per_bag(cursor.getString(cursor.getColumnIndexOrThrow("price_per_bag")));
                data.setConversion(cursor.getString(cursor.getColumnIndexOrThrow("conversion")));
                data.setProduct_name(cursor.getString(cursor.getColumnIndexOrThrow("product_name")));
                data.setOrder_quantity(cursor.getString(cursor.getColumnIndexOrThrow("order_quantity")));
                data.setRequested_date_of_delivery(cursor.getString(cursor.getColumnIndexOrThrow("requested_date_of_delivery")));
                data.setCounter_type(cursor.getString(cursor.getColumnIndexOrThrow("counter_type")));
                data.setCounter_name(cursor.getString(cursor.getColumnIndexOrThrow("counter_name")));
                data.setCounter_code(cursor.getString(cursor.getColumnIndexOrThrow("counter_code")));
                data.setReason_for_non_conversion(cursor.getString(cursor.getColumnIndexOrThrow("reason_for_non_conversion")));
                data.setSite_priority(cursor.getString(cursor.getColumnIndexOrThrow("site_priority")));
                data.setWeather_shield_demo(cursor.getString(cursor.getColumnIndexOrThrow("weather_shield_demo")));
                data.setApproval_status(cursor.getString(cursor.getColumnIndexOrThrow("approval_status")));
                data.setDate_time(cursor.getString(cursor.getColumnIndexOrThrow("date_time")));
                data.setAsm_name(cursor.getString(cursor.getColumnIndexOrThrow("asm_name")));
                data.setAsm_employee_id(cursor.getString(cursor.getColumnIndexOrThrow("asm_employee_id")));
                data.setActual_date_of_delivery(cursor.getString(cursor.getColumnIndexOrThrow("actual_date_of_delivery")));
                data.setDelivery_remarks(cursor.getString(cursor.getColumnIndexOrThrow("delivery_remarks")));
                data.setReason_for_not_delivery(cursor.getString(cursor.getColumnIndexOrThrow("reason_for_not_delivery")));
                data.setSite_status(cursor.getString(cursor.getColumnIndexOrThrow("site_status")));
                data.setRemarks(cursor.getString(cursor.getColumnIndexOrThrow("remarks")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "getAllUnUploadedSiteLead: " + totalList.size());
        return totalList;
    }

    public ArrayList<DataForUpload> getAllSiteLead() {
        ArrayList<DataForUpload> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM new_site_lead_and_conversion_tracking", null);
        if (cursor.moveToFirst()) {
            do {
                DataForUpload data = new DataForUpload();
                data.setSite_transaction_id(cursor.getString(cursor.getColumnIndexOrThrow("site_transaction_id")));
                data.setSite_unique_id(cursor.getString(cursor.getColumnIndexOrThrow("site_unique_id")));
                data.setSite_creation_date(cursor.getString(cursor.getColumnIndexOrThrow("site_creation_date")));
                data.setSite_visit_date(cursor.getString(cursor.getColumnIndexOrThrow("site_visit_date")));
                data.setEmployee_code(cursor.getString(cursor.getColumnIndexOrThrow("employee_code")));
                data.setEmployee_name(cursor.getString(cursor.getColumnIndexOrThrow("employee_name")));
                data.setZone(cursor.getString(cursor.getColumnIndexOrThrow("zone")));
                data.setBranch(cursor.getString(cursor.getColumnIndexOrThrow("branch")));
                data.setState(cursor.getString(cursor.getColumnIndexOrThrow("state")));
                data.setDistrict(cursor.getString(cursor.getColumnIndexOrThrow("district")));
                data.setLatitude(cursor.getString(cursor.getColumnIndexOrThrow("latitude")));
                data.setLongitude(cursor.getString(cursor.getColumnIndexOrThrow("longitude")));
                data.setCustomer_name(cursor.getString(cursor.getColumnIndexOrThrow("customer_name")));
                data.setCustomer_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("customer_contact_number")));
                data.setCustomer_full_address(cursor.getString(cursor.getColumnIndexOrThrow("customer_full_address")));
                data.setIs_register_contractor(cursor.getString(cursor.getColumnIndexOrThrow("is_register_contractor")));
                data.setContractor_name(cursor.getString(cursor.getColumnIndexOrThrow("contractor_name")));
                data.setContractor_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("contractor_contact_number")));
                data.setIs_register_engineer(cursor.getString(cursor.getColumnIndexOrThrow("is_register_engineer")));
                data.setEngineer_name(cursor.getString(cursor.getColumnIndexOrThrow("engineer_name")));
                data.setEngineer_contact_number(cursor.getString(cursor.getColumnIndexOrThrow("engineer_contact_number")));
                data.setMeeting_person(cursor.getString(cursor.getColumnIndexOrThrow("meeting_person")));
                data.setDecision_maker(cursor.getString(cursor.getColumnIndexOrThrow("decision_maker")));
                data.setSite_segment(cursor.getString(cursor.getColumnIndexOrThrow("site_segment")));
                data.setVisit_type(cursor.getString(cursor.getColumnIndexOrThrow("visit_type")));
                data.setProject_segment(cursor.getString(cursor.getColumnIndexOrThrow("project_segment")));
                data.setType_of_construction(cursor.getString(cursor.getColumnIndexOrThrow("type_of_construction")));
                data.setFloor_count(cursor.getString(cursor.getColumnIndexOrThrow("floor_count")));
                data.setCurrent_stage_of_construction(cursor.getString(cursor.getColumnIndexOrThrow("current_stage_of_construction")));
                data.setBuilt_up_area(cursor.getString(cursor.getColumnIndexOrThrow("built_up_area")));
                data.setSite_potential(cursor.getString(cursor.getColumnIndexOrThrow("site_potential")));
                data.setConsumed_till_date(cursor.getString(cursor.getColumnIndexOrThrow("consumed_till_date")));
                data.setBalance_potential(cursor.getString(cursor.getColumnIndexOrThrow("balance_potential")));
                data.setBalance_potential_manual(cursor.getString(cursor.getColumnIndexOrThrow("balance_potential_manual")));
                data.setSite_category(cursor.getString(cursor.getColumnIndexOrThrow("site_category")));
                data.setBrand_used(cursor.getString(cursor.getColumnIndexOrThrow("brand_used")));
                data.setPrice_per_bag(cursor.getString(cursor.getColumnIndexOrThrow("price_per_bag")));
                data.setConversion(cursor.getString(cursor.getColumnIndexOrThrow("conversion")));
                data.setProduct_name(cursor.getString(cursor.getColumnIndexOrThrow("product_name")));
                data.setOrder_quantity(cursor.getString(cursor.getColumnIndexOrThrow("order_quantity")));
                data.setRequested_date_of_delivery(cursor.getString(cursor.getColumnIndexOrThrow("requested_date_of_delivery")));
                data.setCounter_type(cursor.getString(cursor.getColumnIndexOrThrow("counter_type")));
                data.setCounter_name(cursor.getString(cursor.getColumnIndexOrThrow("counter_name")));
                data.setCounter_code(cursor.getString(cursor.getColumnIndexOrThrow("counter_code")));
                data.setReason_for_non_conversion(cursor.getString(cursor.getColumnIndexOrThrow("reason_for_non_conversion")));
                data.setSite_priority(cursor.getString(cursor.getColumnIndexOrThrow("site_priority")));
                data.setWeather_shield_demo(cursor.getString(cursor.getColumnIndexOrThrow("weather_shield_demo")));
                data.setApproval_status(cursor.getString(cursor.getColumnIndexOrThrow("approval_status")));
                data.setDate_time(cursor.getString(cursor.getColumnIndexOrThrow("date_time")));
                data.setAsm_name(cursor.getString(cursor.getColumnIndexOrThrow("asm_name")));
                data.setAsm_employee_id(cursor.getString(cursor.getColumnIndexOrThrow("asm_employee_id")));
                data.setActual_date_of_delivery(cursor.getString(cursor.getColumnIndexOrThrow("actual_date_of_delivery")));
                data.setDelivery_remarks(cursor.getString(cursor.getColumnIndexOrThrow("delivery_remarks")));
                data.setReason_for_not_delivery(cursor.getString(cursor.getColumnIndexOrThrow("reason_for_not_delivery")));
                data.setSite_status(cursor.getString(cursor.getColumnIndexOrThrow("site_status")));
                data.setRemarks(cursor.getString(cursor.getColumnIndexOrThrow("remarks")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        return totalList;
    }

    // =========================== Branch DataSet ===========================
    public void insertBranch(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM branch_list WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("branch_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllBranch() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM branch_list", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllBranch: " + totalList.size());
        return totalList;
    }

    // =========================== State DataSet ===========================
    public void insertState(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM state_list WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("state_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllState() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM state_list", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllState: " + totalList.size());
        return totalList;
    }

    // =========================== District DataSet ===========================
    public void insertDistrict(DistrictDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM district_list WHERE title=? AND value=?",
                new String[]{data.getTitle(), data.getValue()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());
            cv.put("state_name", data.getStateName());

            synchronized ("dbLock") {
                database.insertWithOnConflict("district_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DistrictDataSet> getAllDistrict() {
        ArrayList<DistrictDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM district_list", null);
        if (cursor.moveToFirst()) {
            do {
                DistrictDataSet data = new DistrictDataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));
                data.setStateName(cursor.getString(cursor.getColumnIndexOrThrow("state_name")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllDistrict: " + totalList.size());
        return totalList;
    }

    // =========================== Req Contractor Link DataSet ===========================
    public void insertReqContractorLink(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM req_contractor_link_list WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("req_contractor_link_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllReqContractorLink() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM req_contractor_link_list", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllReqContractorLink: " + totalList.size());
        return totalList;
    }

    // =========================== Contractor Link DataSet ===========================
    public void insertContractorLink(ProfileDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM contractor_link_list WHERE code=?",
                new String[]{data.getCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("code", data.getCode());
            cv.put("name", data.getName());
            cv.put("number", data.getNumber());

            synchronized ("dbLock") {
                database.insertWithOnConflict("contractor_link_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<ProfileDataSet> getAllContractorLink() {
        ArrayList<ProfileDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM contractor_link_list", null);
        if (cursor.moveToFirst()) {
            do {
                ProfileDataSet data = new ProfileDataSet();
                data.setCode(cursor.getString(cursor.getColumnIndexOrThrow("code")));
                data.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                data.setNumber(cursor.getString(cursor.getColumnIndexOrThrow("number")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllContractorLink: " + totalList.size());
        return totalList;
    }

    // =========================== Req Engineer Stellar DataSet ===========================
    public void insertReqEngineerStellar(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM req_engineer_stellar_list WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("req_engineer_stellar_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllReqEngineerStellar() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM req_engineer_stellar_list", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllReqEngineerStellar: " + totalList.size());
        return totalList;
    }

    // =========================== Engineer Stellar DataSet ===========================
    public void insertEngineerStellar(ProfileDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM engineer_stellar_list WHERE code=?",
                new String[]{data.getCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("code", data.getCode());
            cv.put("name", data.getName());
            cv.put("number", data.getNumber());

            synchronized ("dbLock") {
                database.insertWithOnConflict("engineer_stellar_list", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<ProfileDataSet> getAllEngineerStellar() {
        ArrayList<ProfileDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM engineer_stellar_list", null);
        if (cursor.moveToFirst()) {
            do {
                ProfileDataSet data = new ProfileDataSet();
                data.setCode(cursor.getString(cursor.getColumnIndexOrThrow("code")));
                data.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                data.setNumber(cursor.getString(cursor.getColumnIndexOrThrow("number")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllEngineerStellar: " + totalList.size());
        return totalList;
    }

    // =========================== Meeting Person DataSet ===========================
    public void insertMeetingPerson(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM meeting_person WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("meeting_person", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllMeetingPerson() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM meeting_person", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllMeetingPerson: " + totalList.size());
        return totalList;
    }

    // =========================== Decision Maker DataSet ===========================
    public void insertDecisionMaker(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM decision_maker WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("decision_maker", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllDecisionMaker() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM decision_maker", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllDecisionMaker: " + totalList.size());
        return totalList;
    }

    // =========================== Site Segment DataSet ===========================
    public void insertSiteSegment(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM site_segment WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("site_segment", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllSiteSegment() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM site_segment", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllSiteSegment: " + totalList.size());
        return totalList;
    }

    // =========================== Visit Type DataSet ===========================
    public void insertVisitType(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM visit_type WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("visit_type", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllVisitType() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM visit_type", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllVisitType: " + totalList.size());
        return totalList;
    }

    // =========================== Project Segment DataSet ===========================
    public void insertProjectSegment(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM project_segment WHERE value=?",
                new String[]{data.getValue()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("project_segment", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllProjectSegment() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM project_segment", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllProjectSegment: " + totalList.size());
        return totalList;
    }

    // =========================== Type of Construction DataSet ===========================
    public void insertTypeOfConstruction(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM type_of_construction WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("type_of_construction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllTypeOfConstruction() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM type_of_construction", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllTypeOfConstruction: " + totalList.size());
        return totalList;
    }

    // =========================== Floor Count DataSet ===========================
    public void insertFloorCount(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM floor_count WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("floor_count", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }

    public ArrayList<DataSet> getAllFloorCount() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM floor_count", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllFloorCount: " + totalList.size());
        return totalList;
    }
    // =========================== Current Stage of Construction DataSet ===========================
    public void insertCurrentStageOfConstruction(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM current_stage_of_construction WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("current_stage_of_construction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllCurrentStageOfConstruction() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM current_stage_of_construction", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllCurrentStageOfConstruction: " + totalList.size());
        return totalList;
    }
    // =========================== Brand Used DataSet ===========================
    public void insertBrandUsed(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM brand_used WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("brand_used", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllBrandUsed() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM brand_used", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllBrandUsed: " + totalList.size());
        return totalList;
    }
    // =========================== Conversion DataSet ===========================
    public void insertConversion(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM conversion WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("conversion", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllConversion() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM conversion", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllConversion: " + totalList.size());
        return totalList;
    }
    // =========================== Product DataSet ===========================
    public void insertProduct(ProductDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM product WHERE name=? AND visitType=? AND conversionType=?",
                new String[]{data.getName(), data.getVisitType(), data.getConversionType()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("name", data.getName());
            cv.put("visitType", data.getVisitType());
            cv.put("conversionType", data.getConversionType());

            synchronized ("dbLock") {
                database.insertWithOnConflict("product", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<ProductDataSet> getAllProduct() {
        ArrayList<ProductDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM product", null);
        if (cursor.moveToFirst()) {
            do {
                ProductDataSet data = new ProductDataSet();
                data.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                data.setVisitType(cursor.getString(cursor.getColumnIndexOrThrow("visitType")));
                data.setConversionType(cursor.getString(cursor.getColumnIndexOrThrow("conversionType")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllProduct: " + totalList.size());
        return totalList;
    }
    // =========================== Counter Type DataSet ===========================
    public void insertCounterType(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM counter_type WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("counter_type", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllCounterType() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM counter_type", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllCounterType: " + totalList.size());
        return totalList;
    }
    // =========================== Counter Name DataSet ===========================
    public void insertCounterName(CounterNameDataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM counter_name WHERE code=?",
                new String[]{data.getCode()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("name", data.getName());
            cv.put("code", data.getCode());
            cv.put("type", data.getType());

            synchronized ("dbLock") {
                database.insertWithOnConflict("counter_name", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<CounterNameDataSet> getAllCounterName() {
        ArrayList<CounterNameDataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM counter_name", null);
        if (cursor.moveToFirst()) {
            do {
                CounterNameDataSet data = new CounterNameDataSet();
                data.setName(cursor.getString(cursor.getColumnIndexOrThrow("name")));
                data.setCode(cursor.getString(cursor.getColumnIndexOrThrow("code")));
                data.setType(cursor.getString(cursor.getColumnIndexOrThrow("type")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllCounterName: " + totalList.size());
        return totalList;
    }
    // =========================== Reasons for Non-conversion DataSet ===========================
    public void insertReasonsForNonConversion(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM reasons_for_non_conversion WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("reasons_for_non_conversion", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllReasonsForNonConversion() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM reasons_for_non_conversion", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllReasonsForNonConversion: " + totalList.size());
        return totalList;
    }
    // =========================== Priority DataSet ===========================
    public void insertPriority(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM priority WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("priority", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllPriority() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM priority", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllPriority: " + totalList.size());
        return totalList;
    }
    // =========================== Weather Shield Demo DataSet ===========================
    public void insertWeatherShieldDemo(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM weather_shield_demo WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("weather_shield_demo", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllWeatherShieldDemo() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM weather_shield_demo", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllWeatherShieldDemo: " + totalList.size());
        return totalList;
    }
    // =========================== Approval Status DataSet ===========================
    public void insertApprovalStatus(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM approval_status WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("approval_status", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllApprovalStatus() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM approval_status", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllApprovalStatus: " + totalList.size());
        return totalList;
    }
    // =========================== ASM Name DataSet ===========================
    public void insertASMName(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM asm_name WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("asm_name", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllASMName() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM asm_name", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllASMName: " + totalList.size());
        return totalList;
    }
    // =========================== Site Status DataSet ===========================
    public void insertSiteStatus(DataSet data) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM site_status WHERE title=?",
                new String[]{data.getTitle()}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("value", data.getValue());
            cv.put("title", data.getTitle());

            synchronized ("dbLock") {
                database.insertWithOnConflict("site_status", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public ArrayList<DataSet> getAllSiteStatus() {
        ArrayList<DataSet> totalList = new ArrayList<>();
        Cursor cursor = database.rawQuery("SELECT * FROM site_status", null);
        if (cursor.moveToFirst()) {
            do {
                DataSet data = new DataSet();
                data.setValue(cursor.getString(cursor.getColumnIndexOrThrow("value")));
                data.setTitle(cursor.getString(cursor.getColumnIndexOrThrow("title")));

                totalList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        Log.d("TAG", "_CheckDB_ getAllSiteStatus: " + totalList.size());
        return totalList;
    }
    // =========================== Emp Details DataSet ===========================
    public void insertEmpDetails(String emp_code, String name, String designation, String zone) {
        Cursor cursor = database.rawQuery(
                "SELECT * FROM emp_details WHERE emp_code=?",
                new String[]{emp_code}
        );
        boolean exists = cursor.moveToFirst();
        cursor.close();
        if (exists) {
            Log.d("DB", "Record already exists. Insert skipped.");
            return;
        }

        database.beginTransaction();
        try {
            ContentValues cv = new ContentValues();
            cv.put("emp_code", emp_code);
            cv.put("name", name);
            cv.put("designation", designation);
            cv.put("zone", zone);

            synchronized ("dbLock") {
                database.insertWithOnConflict("emp_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
            }
            database.setTransactionSuccessful();
        } catch (Exception ignored) {
        } finally {
            database.endTransaction();
        }
    }
    public String getEmpName(String emp_code) {
        Cursor cursor = database.rawQuery("SELECT * FROM emp_details WHERE emp_code = ?",
                new String[]{emp_code});
        String name = "";
        if (cursor.moveToFirst()) {
            name = cursor.getString(cursor.getColumnIndexOrThrow("name"));
        }
        cursor.close();
        return name;
    }
    public String getEmpDesignation(String emp_code) {
        Cursor cursor = database.rawQuery("SELECT * FROM emp_details WHERE emp_code = ?",
                new String[]{emp_code});
        String designation = "";
        if (cursor.moveToFirst()) {
            designation = cursor.getString(cursor.getColumnIndexOrThrow("designation"));
        }
        cursor.close();
        return designation;
    }
    public String getEmpZone(String emp_code) {
        Cursor cursor = database.rawQuery("SELECT * FROM emp_details WHERE emp_code = ?",
                new String[]{emp_code});
        String zone = "";
        if (cursor.moveToFirst()) {
            zone = cursor.getString(cursor.getColumnIndexOrThrow("zone"));
        }
        cursor.close();
        return zone;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< === >>>>>>>>>>>>>>>>>>>>>>>>>>>
}