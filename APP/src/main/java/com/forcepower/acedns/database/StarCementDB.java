package com.forcepower.acedns.database;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.os.Environment;

import com.forcepower.acedns.bean.commonDatabaseHelper;

import java.util.ArrayList;
import java.util.List;

public class StarCementDB extends SQLiteOpenHelper
{

    private static final int DB_VERSION = 2;
    private static final String DB_NAME = "StarCement.db";
    private static final String TABLE_LEDER = "ledger";
    private static final String TABLE_LEDER_BALANCE = "ledger_balance";
    private static final String T_APPERPDO = "T_APPERPDO";
    private static final String T_DOCHALLAN = "T_DOCHALLAN";
    private static final String PERFORMANCE = "PERFORMANCE";
    private static final String path = Environment.getExternalStorageDirectory().getAbsolutePath()+"/StarSaathiDB/StartCementDB/";
    private static final String DB_NAME_F = path + DB_NAME;
    Context mContext;

    public StarCementDB(Context context)
    {
        super(context,  DB_NAME_F, null, DB_VERSION);
        mContext = context;
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        try
        {
            //need to add dns_customer_code
            String CREATE_LEDGER = "CREATE TABLE ledger ( customer_code TEXT, voucher_date TEXT, voucher_no TEXT, quantity TEXT, amount_dr TEXT, amount_cr TEXT, narration TEXT, entry_date TEXT)";
            db.execSQL(CREATE_LEDGER);

            String CREATE_LEDGER_BAL = "CREATE TABLE ledger_balance ( customer_code TEXT, balance TEXT, date TEXT, link TEXT)";
            db.execSQL(CREATE_LEDGER_BAL);


            String T_APPERPDO = "CREATE TABLE T_APPERPDO ( apporderno text, erporderno text, erporderdt text, order_for text, customer_code text, dns_customer_code text, status text, prod_code text, dns_prod_code text, prod_display_name text, qty text )";
            db.execSQL(T_APPERPDO);


            String T_DOCHALLAN = "CREATE TABLE T_DOCHALLAN ( apporderno text, erporderno text, erporderdt text, challanno text, challandt text, prod_code text, qty text, challanqty text, truckno text, driverno text, customer_code text, dns_customer_code text, prod_display_name text )";
            db.execSQL(T_DOCHALLAN);

            String PERFORMANCE = "CREATE TABLE PERFORMANCE ( customer_code TEXT, customer_name TEXT, year TEXT, month TEXT, target TEXT, achievement TEXT)";
            db.execSQL(PERFORMANCE);

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion)
    {
        try
        {
            db.execSQL("DROP TABLE IF EXISTS ledger");
            db.execSQL("DROP TABLE IF EXISTS ledger_balance");
            db.execSQL("DROP TABLE IF EXISTS T_APPERPDO");
            db.execSQL("DROP TABLE IF EXISTS T_DOCHALLAN");
            db.execSQL("DROP TABLE IF EXISTS PERFORMANCE");
            onCreate(db);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void insertLeder(String customer_code, String voucher_date,
                            String voucher_no, String quantity,
                            String amount_dr, String amount_cr,
                            String narration, String entry_date)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("customer_code", customer_code+"");
            values.put("voucher_date", voucher_date+"");
            values.put("voucher_no", voucher_no+"");
            values.put("quantity", quantity+"");
            values.put("amount_dr", amount_dr+"");
            values.put("amount_cr", amount_cr+"");
            values.put("narration", narration+"");
            values.put("entry_date", entry_date+"");

            db.insert(TABLE_LEDER, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void insertLederBal(String customer_code, String balance, String date, String link)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("customer_code", customer_code+"");
            values.put("balance", balance+"");
            values.put("date", date+"");
            values.put("link", link+"");

            db.insert(TABLE_LEDER_BALANCE, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting key wise data
    public String getLedgerBalance(String key)
    {

        String value = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT  "+key+" FROM " + TABLE_LEDER_BALANCE;

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getString(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        // return dataList
        return value;
    }

    public void insertOrderData(String apporderno, String erporderno, String customer_code,
                                String dns_customer_code, String erporderdt,
                                String order_for, String status, String prod_code, String dns_prod_code,
                                String prod_display_name, String qty
    )
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("apporderno", apporderno+"");
            values.put("erporderno", erporderno+"");
            values.put("customer_code", customer_code+"");
            values.put("dns_customer_code", dns_customer_code+"");
            values.put("erporderdt", erporderdt+"");

//            values.put("order_challan_data", order_challan_data+"");
            values.put("order_for", order_for +"");
            values.put("status", status +"");
            values.put("prod_code", prod_code +"");
            values.put("dns_prod_code", dns_prod_code +"");
            values.put("prod_display_name", prod_display_name +"");
            values.put("qty", qty +"");

            db.insert(T_APPERPDO, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllLedgerDetails()
    {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<commonDatabaseHelper>();
        // Select All Query
        String selectQuery = "SELECT  * FROM " + TABLE_LEDER +" ORDER BY datetime(voucher_date) DESC LIMIT 50";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
                commonDatabaseHelper data = new commonDatabaseHelper();

                data.setItem0(cursor.getString(0)); // Dealer id
                data.setItem1(cursor.getString(1)); // Voucher Date
                data.setItem2(cursor.getString(2)); // Voucher No
                data.setItem3(cursor.getString(3)); //Quantity
                data.setItem4(cursor.getString(4)); // Amount dr
                data.setItem5(cursor.getString(5)); // amount cr
                data.setItem6(cursor.getString(6)); // Balance

                // Adding contact to list
                dataList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }

    public void insertChallan(String apporderno, String challandt, String challanno,
                              String challanqty, String driverno, String erporderdt, String erporderno,
                              String prod_code, String qty, String truckno, String prod_display_name)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("apporderno", apporderno+"");
            values.put("challandt", challandt+"");
            values.put("challanno", challanno+"");
            values.put("challanqty", challanqty+"");
            values.put("driverno", driverno+"");
            values.put("erporderdt", erporderdt+"");
            values.put("erporderno", erporderno+"");
            values.put("prod_code", prod_code+"");
            values.put("qty", qty+"");
            values.put("truckno", truckno+"");
            values.put("prod_display_name", prod_display_name+"");


            db.insert(T_DOCHALLAN, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllAppOrderDetails()
    {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<commonDatabaseHelper>();
        // Select All Query
        String selectQuery = "SELECT apporderno, status, qty, prod_display_name FROM " + T_APPERPDO +" WHERE apporderno != ''";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
                    commonDatabaseHelper data = new commonDatabaseHelper();
                    data.setItem0(cursor.getString(0)); // apporderno
                    data.setItem1(cursor.getString(1)); // status
                    data.setItem2(cursor.getString(2)); // qty
                    data.setItem3(cursor.getString(3)); //prod_display_name
                    dataList.add(data);

            } while (cursor.moveToNext());
        }
        cursor.close();

        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }
    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllOffLineOrderDetails()
    {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<commonDatabaseHelper>();
        // Select All Query
        String selectQuery = "SELECT apporderno, status, erporderno, qty, prod_display_name FROM " + T_APPERPDO + " WHERE apporderno ='' AND erporderno != '' ORDER BY datetime(erporderdt) DESC";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
//                if(!cursor.getString(2).trim().matches("") && !cursor.getString(2).trim().isEmpty())
//                {
                    commonDatabaseHelper data = new commonDatabaseHelper();

                    data.setItem0(cursor.getString(2)); // erporderno
                    data.setItem1(cursor.getString(1)); // status

                    data.setItem2(cursor.getString(3)); // qty
                    data.setItem3(cursor.getString(4)); //prod_display_name

//                    String selectQuery0 = "SELECT qty, prod_display_name FROM " + T_DOCHALLAN  + " WHERE erporderno = '" +cursor.getString(2)+"'";
//
//                    Cursor cursor0 = db.rawQuery(selectQuery0, null);
//                    // looping through all rows and adding to list
//                    if (cursor0.moveToFirst())
//                    {
//                        data.setItem2(cursor0.getString(0)); // Qty
////                        AceDnsTransactionDatabase oldDB = new AceDnsTransactionDatabase(mContext);
//                        data.setItem3(cursor0.getString(1)); //prod_desc
//                    }
//                    cursor0.close();
//                    String selectQuery3 = "SELECT prod_code, qty FROM " + T_DOCHALLAN  + " WHERE erporderno = '" +cursor.getString(2)+"'";
//
//                    Cursor cursor2 = db.rawQuery(selectQuery3, null);
//                    // looping through all rows and adding to list
//                    if (cursor2.moveToFirst())
//                    {
//                        ArrayList<CategoryItem>childArrayData = new ArrayList<>();
//
//                        do
//                        {
//                            CategoryItem e = new CategoryItem();
//                            print_log_d("VOLA_DATA ", cursor2.getString(0));
//                            e.setName(cursor2.getString(0)); //apporderno
//                            e.setDescription(cursor2.getString(1)); //prod_code
//                            childArrayData.add(e);
//                            data.setItem20(childArrayData); //
//                        }while (cursor2.moveToNext());
//
//                    }
//

                    // Adding contact to list
                    dataList.add(data);
//                    cursor2.close();
//                }

            } while (cursor.moveToNext());
        }
        cursor.close();

        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }

    public List<commonDatabaseHelper> getSubCategoryAppOrder(String item0)
    {
        List<commonDatabaseHelper> dataList = new ArrayList<>();
        try
        {
            //  used as a common class
            String selectQuery = "SELECT challanno, challandt, challanqty, truckno, driverno FROM " + T_DOCHALLAN  + " WHERE apporderno = '" +item0+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor2 = db.rawQuery(selectQuery, null);
            // looping through all rows and adding to list
            if (cursor2.moveToFirst())
            {
                do
                {
                    if(!cursor2.getString(0).matches("") &&
                            !cursor2.getString(0).equalsIgnoreCase("null"))
                    {
                        commonDatabaseHelper e = new commonDatabaseHelper();
                        e.setItem0(cursor2.getString(0)); //challanno
                        e.setItem1(cursor2.getString(1)); //erporderdt
                        e.setItem2(cursor2.getString(2)); //challanqty

                        e.setItem3(cursor2.getString(3)); //truckno
                        e.setItem4(cursor2.getString(4)); //driverno
                        dataList.add(e);
                    }


                }while (cursor2.moveToNext());

            }


            // Adding contact to list
            cursor2.close();
            db.close(); // Closing database connection

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return dataList;
    }
    public List<commonDatabaseHelper> getSubCategoryOfflineOrder(String item0)
    {
        List<commonDatabaseHelper> dataList = new ArrayList<commonDatabaseHelper>();

        try
        {
            //  used as a common class
            String selectQuery = "SELECT challanno, challandt, challanqty, truckno, driverno FROM " + T_DOCHALLAN  + " WHERE erporderno = '" +item0+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor2 = db.rawQuery(selectQuery, null);
            // looping through all rows and adding to list
            if (cursor2.moveToFirst())
            {
                do
                {
                    if(!cursor2.getString(0).matches("") &&
                            !cursor2.getString(0).equalsIgnoreCase("null"))
                    {
                        commonDatabaseHelper e = new commonDatabaseHelper();
                        e.setItem0(cursor2.getString(0)); //challanno
                        e.setItem1(cursor2.getString(1)); //erporderdt
                        e.setItem2(cursor2.getString(2)); //challanqty

                        e.setItem3(cursor2.getString(3)); //truckno
                        e.setItem4(cursor2.getString(4)); //driverno

                        dataList.add(e);
                    }

                }while (cursor2.moveToNext());

            }


            // Adding contact to list
            cursor2.close();
            db.close(); // Closing database connection

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return dataList;
    }

//    public ArrayList<SelfAppraisalDetailsCustomerWise> getTargetForAllMonths(String currentYear)
//    {
//
//        String mData = "April, May, June, July, August, September, October, November, December,January, February, March";
//    String[] mArray = mData.split(",");
//        ArrayList<SelfAppraisalDetailsCustomerWise> targetList = new ArrayList<>();
//        SQLiteDatabase db = this.getWritableDatabase();
//        for(int index=0;index<12;index++)
//        {
//            Cursor cursor=null;
//            try
//            {
////                String currentMonth=index+"";
////                if(currentMonth.length()<2)
////                {/home/amitabha/Downloads/performance.csv
////                    currentMonth="0"+currentMonth;
////                }
////                String selectQuery = "SELECT SUM(target), SUM(achievement) FROM "+tableName+ " WHERE month='"+currentMonth+"' AND year= '" +currentYear+"'";
//                String selectQuery = "SELECT target, achievement FROM PERFORMANCE WHERE month='"+mArray[index].trim()+"' AND year='"+currentYear+"'";
//                cursor = db.rawQuery(selectQuery, null);
//                cursor.moveToFirst();
//                SelfAppraisalDetailsCustomerWise targetListEachMonth=new SelfAppraisalDetailsCustomerWise();
//                targetListEachMonth.setmonth(index+"");
//                String target = cursor.getString(0);
//                if(target==null || target.matches("") || target.matches("null"))
//                {
//                    target= "0";
//                }
//                targetListEachMonth.settarget(target);
//                String achievement = cursor.getString(1);
//                if(achievement==null || achievement.matches("") || achievement.matches("null"))
//                {
//                    achievement= "0";
//                }
//                targetListEachMonth.setachievement(achievement);
//                targetList.add(targetListEachMonth);
//            }
//            catch (Exception e)
//            {
//                e.printStackTrace();
//            }
//            finally
//            {
//                if(cursor!=null)
//                {
//                    cursor.close();
//                }
//            }
//
//        }
//        return targetList;
//    }
    // Getting key wise data
    public String deleteTable(String TABLE_NAME)
    {

        String returnValue = "";
        try
        {
            // Select All Query
            SQLiteDatabase db = this.getWritableDatabase();
            db.execSQL("delete from "+ TABLE_NAME);
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        // return dataList
        return returnValue;
    }
}
