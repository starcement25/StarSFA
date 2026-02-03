package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.util.Log;

import com.forcepower.acedns.bean.DO_despatch_details;
import com.forcepower.acedns.bean.MenuAccess;
import com.forcepower.acedns.bean.VehicleList;
import com.forcepower.acedns.constants.AllUrlOtp;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.AllUrlOtp.OTP_checklist_download;
import static com.forcepower.acedns.constants.AllUrlOtp.menu_access;
import static com.forcepower.acedns.constants.AllUrlOtp.vehicle_data_download_tracking;


/**
 * Created by Force Power Intellij Amiyo  on 07-06-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public class commonAsyncTaskMasterOtp
{
    Activity mContext;
    AceDnsDatabase dbHelper;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    String operation_type = "",status=""; // Just to know the format
    String currentDate = "";

    public commonAsyncTaskMasterOtp(Activity context, String statusOBJ, String operation_type) {

     this.mContext = context;
     this.status=statusOBJ;
     this.operation_type = operation_type;
     dbHelper = new AceDnsDatabase(mContext);
     currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());

          if(status.equalsIgnoreCase("vehicle_data_download_tracking"))
         {
             _DOWNLOAD_vehicle_data_download(vehicle_data_download_tracking);
         }
         else if(status.equalsIgnoreCase("check_list_gate_keeper2_out") ||
                 status.equalsIgnoreCase("check_list_security_out"))
         {
             _DOWNLOAD_check_list_gate_keeper2_out();
         }

     }

    public void _DOWNLOAD_check_list_gate_keeper2_out() {

        String URL = OTP_checklist_download
                + "?nick_name="+ Constants.nickName
                + "&emp_code="+ Constants.employeeDetailObject.getEmpCode();

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ check_list_gate_keeper2_out commonAsyncTaskMasterOtp:  "+URL);

        Download_txt(URL);


    }
    public void _DOWNLOAD_vehicle_data_download(String url) {

        String URL = url
                + "?nick_name="+ Constants.nickName
                + "&emp_code="+ Constants.employeeDetailObject.getEmpCode();

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ vehicle_data_download commonAsyncTaskMasterOtp:  "+URL);

        Download_txt(URL);

        File csvFile = new File(        Utils.getAppStoragePath(mContext) + status+".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }

        ArrayList<VehicleList> mVehicleList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn)
                    {
                        VehicleList temp = new VehicleList();

                        temp.set_customer_code(RowData[0]);
                        temp.set_destination(RowData[1]);
                        temp.set_DO_no(RowData[2]);

                        temp.set_vehicle_no(RowData[3]);
                        temp.set_trans_response_id(RowData[4]);
                        temp.set_transporter(RowData[5]);

                        temp.set_arrival_gate_id(RowData[6]);
                        temp.set_gate_keeper1(RowData[7]);
                        temp.set_despatch_approval_id(RowData[8]);

                        temp.set_despatch_in(RowData[9]);
                        temp.set_gate_vehicle_in_id(RowData[10]);
                        temp.set_gate_keeper2(RowData[11]);

                        temp.set_wb_in_id(RowData[12]);
                        temp.set_weighbridge_in(RowData[13]);
                        temp.set_tare_weight(RowData[14]);
                        temp.set_loading_id(RowData[15]);

                        temp.set_loading(RowData[16]);
                        temp.set_GRN_attachment(RowData[17]);
                        temp.set_GRN_attachment_date(RowData[18]);
                        temp.set_bay_no(RowData[19]);
                        temp.set_wb_out_id(RowData[20]);
                        temp.set_weighbridge_out(RowData[21]);

                        temp.set_despatch_tarns_id(RowData[22]);
                        temp.set_despatch_out(RowData[23]);
                        temp.set_exit_approval_id(RowData[24]);

                        temp.set_gate_keeper2_out(RowData[25]);
                        temp.set_security_chk_id(RowData[26]);
                        temp.set_security_out(RowData[27]);

                        temp.set_exit_gate_id(RowData[28]);
                        temp.set_gate_keeper1_out(RowData[29]);

                        mVehicleList.add(temp);

                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        long insertStatus=0;
        insertStatus = dbHelper.InserttoVehicleListTable(mVehicleList);

        if (insertStatus == noRows || noRows == 0)
        {
            decideNavigation();
            Constants.isVehicleDataDownloaded=true;
        }
        else
        {
            Constants.isVehicleDataDownloaded=false;
        }
    }
    public void _DOWNLOAD_menu_access() {

        String URL = menu_access
                + "?nick_name="+ Constants.nickName
                + "&emp_code="+ Constants.employeeDetailObject.getEmpCode();
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ menu_access commonAsyncTaskMasterOtp:  "+URL);
        Download_txt(URL);

        File csvFile = new File(        Utils.getAppStoragePath(mContext) + status+".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }

        ArrayList<MenuAccess> mMenuAccessList = new ArrayList<MenuAccess>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MenuAccess temp = new MenuAccess();
                        temp.setNotAccessibilityMenu(RowData[0]);
                        mMenuAccessList.add(temp);
                        temp = null;
                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        long insertStatus=0;
        insertStatus = dbHelper.InserttoMenuAccessTable(mMenuAccessList);

        if (insertStatus == noRows || noRows == 0)
        {
            decideNavigation();
            Constants.isMenuAccessDownloaded=true;
        }
        else
        {
            Constants.isMenuAccessDownloaded=false;
        }
    }

    private void Download_txt(String URL) {
        if(!operation_type.matches(""))
        {
            URL = URL + "&operation_type=" + operation_type;
        }

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        java.net.URL url = null;

        try {
            outputFile = new File(        Utils.getAppStoragePath(mContext) + status+".txt");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);
            c.connect();
            is = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }

            fbo.flush();

        } catch (Exception e) {
            e.printStackTrace();
        } finally {

            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }

        }
    }
    public void decideNavigation() {
        if (!(noRows == 0 && noColumn != 0))
        {
            dbHelper.insertToLogTable(timeStamp, status);
        }
    }
}
