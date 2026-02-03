package com.forcepower.acedns.database;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.MatrixCursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import java.util.ArrayList;

/**
 * Created by Amit on 29/04/2017.
 */
public class DatabaseHelperSqlite extends SQLiteOpenHelper {
    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "HelperDatabase";

    // Contacts table name
    private static final String TABLE_FIREBASE = "firebase";
    private static final String TABLE_DEVICE_DETAILS = "device_details";

    // Contacts Table Columns names
    private static final String KEY_ID = "registration_id";
    private static final String KEY_UPDATE_AVAILABLE = "is_update_available";

    public DatabaseHelperSqlite(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    // Creating Tables
    @Override
    public void onCreate(SQLiteDatabase db) {
        String CREATE_CONTACTS_TABLE = "CREATE TABLE " + TABLE_FIREBASE + "("
                + KEY_ID + " TEXT," + KEY_UPDATE_AVAILABLE + " TEXT)";

        String CREATE_EMAIL_DEVICE_DETAILS_TABLE = "CREATE TABLE " + TABLE_DEVICE_DETAILS + "("
                + KEY_UPDATE_AVAILABLE + " TEXT)";

        db.execSQL(CREATE_CONTACTS_TABLE);
        db.execSQL(CREATE_EMAIL_DEVICE_DETAILS_TABLE);
    }

    public ArrayList<Cursor> getData(String Query) {
        //get writable database
        SQLiteDatabase sqlDB = this.getWritableDatabase();
        String[] columns = new String[]{"message"};
        //an array list of cursor to save two cursors one has results from the query
        //other cursor stores error message if any errors are triggered
        ArrayList<Cursor> alc = new ArrayList<Cursor>(2);
        MatrixCursor Cursor2 = new MatrixCursor(columns);
        alc.add(null);
        alc.add(null);

        try {
            String maxQuery = Query;
            //execute the query results will be save in Cursor c
            Cursor c = sqlDB.rawQuery(maxQuery, null);

            //add value to cursor2
            Cursor2.addRow(new Object[]{"Success"});

            alc.set(1, Cursor2);
            if (null != c && c.getCount() > 0) {

                alc.set(0, c);
                c.moveToFirst();

                return alc;
            }
            return alc;
        } catch (SQLException sqlEx) {
            Log.d("printing exception", sqlEx.getMessage());
            //if any exceptions are triggered save the error message to cursor an return the arraylist
            Cursor2.addRow(new Object[]{"" + sqlEx.getMessage()});
            alc.set(1, Cursor2);
            return alc;
        } catch (Exception ex) {
            Log.d("printing exception", ex.getMessage());

            //if any exceptions are triggered save the error message to cursor an return the arraylist
            Cursor2.addRow(new Object[]{"" + ex.getMessage()});
            alc.set(1, Cursor2);
            return alc;
        }
    }


    // Upgrading database
    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        // Drop older table if existed
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_FIREBASE);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_DEVICE_DETAILS);
        // Create tables again
        onCreate(db);
    }

    // Adding new contact
    public void addRegistrationIdAndStatus(String id) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_ID, id);
        values.put(KEY_UPDATE_AVAILABLE, "yes");
        // Inserting Row
        long status = db.insert(TABLE_FIREBASE, null, values);
        Log.d("status", status + "");
        db.close(); // Closing database connection
    }

    public void updateRegistrationIdAndStatus(String id, String isUpdateAvailable) {
        try {
            SQLiteDatabase db = this.getWritableDatabase();

//            String updateQuery = "UPDATE "+ TABLE_FIREBASE +" SET " + KEY_ID+"='"+id+"' AND "+KEY_UPDATE_AVAILABLE+"='"+isUpdateAvailable+"'";
            ContentValues cv = new ContentValues();
            cv.put(KEY_ID, id); //These Fields should be your String values of actual column names
            cv.put(KEY_UPDATE_AVAILABLE, isUpdateAvailable);
            int returnVal = db.update(TABLE_FIREBASE, cv, null, null);
            Log.d("returnVal", returnVal + "");
//            db.execSQL(updateQuery);
//            Cursor cursor = db.rawQuery(updateQuery, null);
//            cursor.moveToFirst();
//            cursor.close();
            db.close();
        } catch (Exception e) {

        }


    }

    public Boolean isUpdateAvailable() {
        Boolean isUpdateAvailable = false;
        try {
            // Select All Query
            String selectQuery = "SELECT " + KEY_UPDATE_AVAILABLE + " FROM " + TABLE_FIREBASE + " LIMIT 1";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst()) {
                if (cursor.getString(0).matches("yes")) {
                    isUpdateAvailable = true;
                }
                cursor.close();
                db.close();
            }
        } catch (Exception e) {
            isUpdateAvailable = false;
        }

        // return contact list
        return isUpdateAvailable;
    }

    public Boolean isDeviceDetailsSent() {
        Boolean isUpdated = false;
        try {
            // Select All Query
            String selectQuery = "SELECT " + KEY_UPDATE_AVAILABLE + " FROM " + TABLE_FIREBASE + " LIMIT 1";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst()) {
                isUpdated = true;
                cursor.close();
                db.close();
            }
        } catch (Exception e) {
            isUpdated = false;
        }

        return isUpdated;
    }

    public String getRegistrationId() {
        String RegistrationId = "";
        try {
            // Select All Query
            String selectQuery = "SELECT " + KEY_ID + " FROM " + TABLE_FIREBASE + " LIMIT 1";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst()) {
                RegistrationId = cursor.getString(0);

            }
            cursor.close();
            db.close();

        } catch (Exception e) {
            RegistrationId = "";
        }

        // return contact list
        return RegistrationId;
    }

}
