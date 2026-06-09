package com.forcepower.acedns.newDataBase.sync;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.EmployeeDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.PartyDataList;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.StateDataSet;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerMasterTableDataSet;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONArray;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

public class DataForDownloadingLead {
    Context mContext;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    String TYPE_OF_USER = "";

    public DataForDownloadingLead(Context context) {
        mContext = context;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
    }

    public void addAllFormDataForLead(DownloadCallback callback) {
        try {
            mNewDatabaseForSiteLead.createDatabaseTableForSiteLead();
            mNewDatabaseForSiteLead.createDatabaseTableForLeadFunnel();
            mNewDatabaseForSiteLead.createNewTable();
            mNewDatabaseForSiteLead.createNewTableForQueryAndLead();
        } catch (Exception e) {
            Log.d("TAG", "DataForDownloadingLead addAllFormDataForLead: " + e.getMessage());
        }

        dataSetSegmentList();
        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();


        _DOWNLOAD_SoldToPartyList(successSoldToPartyList -> {
            if (!successSoldToPartyList) {
                callback.onComplete(false);
            } else {
                _DOWNLOAD_ShipToPartyList(successShipToPartyList -> {
                    if (!successShipToPartyList) {
                        callback.onComplete(false);
                    } else {
                        _DOWNLOAD_LeadList(callback);
                    }
                });
            }
        });

//        _DOWNLOAD_SoldToPartyList(successSoldToPartyList -> {
//            if (!successSoldToPartyList) {
//                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_SoldToPartyList: false");
//                callback.onComplete(false);
//            } else {
//                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_SoldToPartyList: true");
//                _DOWNLOAD_ShipToPartyList(successShipToPartyList -> {
//                    if (!successShipToPartyList) {
//                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_ShipToPartyList: false");
//                        callback.onComplete(false);
//                    } else {
//                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_ShipToPartyList: true");
//                        _DOWNLOAD_LeadList(successLeadList->{
//                            if(!successLeadList){
//                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_LeadList: false");
//                                callback.onComplete(false);
//                            }else{
//                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_LeadList: true");
//                                _DOWNLOAD_emp_master(successEmpMaster -> {
//                                    if(!successEmpMaster){
//                                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_emp_master: false");
//                                        callback.onComplete(false);
//                                    }else{
//                                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_emp_master: true");
//                                        _DOWNLOAD_product_list(successProductList -> {
//                                            if(!successProductList){
//                                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_product_list: false");
//                                                callback.onComplete(false);
//                                            }else {
//                                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_product_list: true");
//                                                _DOWNLOAD_state_list(successStateList -> {
//                                                    if(!successStateList){
//                                                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_state_list: false");
//                                                        callback.onComplete(false);
//                                                    }else {
//                                                        Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_state_list: true");
//                                                        _DOWNLOAD_district_list(successDistrictList -> {
//                                                            if(!successDistrictList){
//                                                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_district_list: false");
//                                                                callback.onComplete(false);
//                                                            }else {
//                                                                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_district_list: true");
//                                                                ArrayList<EmployeeDataSet> list = mNewDatabaseForSiteLead.getAllEmployeeDetails();
//                                                                processEmployee(list, 0, () -> {
//                                                                    callback.onComplete(true);
//                                                                });
//                                                            }
//                                                        });
//                                                    }
//                                                });
//                                            }
//                                        });
//                                    }
//                                });
//                            }
//                        });
//                    }
//                });
//            }
//        });

    }

    private void processEmployee(ArrayList<EmployeeDataSet> list, int index, Runnable onAllDone) {
        if (index >= list.size()) {
            onAllDone.run(); // Base case: all processed
            return;
        }

        String empCode = list.get(index).getEmp_code();

        _DOWNLOAD_sold_to_party(soldSuccess -> {
            Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_sold_to_party (" + empCode + "): " + soldSuccess);
            _DOWNLOAD_ship_to_party(shipSuccess -> {
                Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_ship_to_party (" + empCode + "): " + shipSuccess);
                _DOWNLOAD_assigned_to(assignedSuccess -> {
                    Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_assigned_to (" + empCode + "): " + assignedSuccess);
                    processEmployee(list, index + 1, onAllDone);
                }, empCode);
            }, empCode);
        }, empCode);
    }

    public interface DownloadCallback {
        void onComplete(boolean success);
    }

    private void Download_txt(String URL, String data) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        java.net.URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + data + ".txt");
            if (outputFile.exists()) {
                outputFile.delete();
            }
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setConnectTimeout(0);
            c.connect();
            int responseCode = c.getResponseCode();
            if (responseCode == HttpURLConnection.HTTP_OK) {
                is = c.getInputStream();
                byte[] buffer = new byte[1024];
                int len1;
                while ((len1 = is.read(buffer)) != -1) {
                    fbo.write(buffer, 0, len1);
                }
                fbo.flush();
            }
        } catch (Exception ignored) {
        } finally {
            if (c != null) {
                c.disconnect();
            }
            if (fbo != null) {
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            }
            if (is != null) {
                try {
                    is.close();
                } catch (IOException ignored) {
                }
            }
        }
    }


    // <<<<<<<<<<<<<<<<<<<<<<<<<<< LEAD FUNNEL >>>>>>>>>>>>>>>>>>>>>>>>>>>
    @SuppressLint("StaticFieldLeak")
    public class TRANS_EmployeeDetails_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_EmployeeDetails_AsyncTask(Context context) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.sbDevUrl + "api/employee/?emp_code=" + emp_code;
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONArray arr = new JSONArray(result);
                if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt_to")) {
                    TYPE_OF_USER = "HOS";
                } else if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt")) {
                    TYPE_OF_USER = "SO";
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    public void _DOWNLOAD_SoldToPartyList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=sold&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "sold_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "sold_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            CustomerMasterTableDataSet temp = new CustomerMasterTableDataSet();
                            temp.setCust_code(RowData[1]);
                            temp.setCust_name(RowData[4]);
                            temp.setPhone_no(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            temp.setCust_type("sold_to_party");
                            mNewDatabaseForSiteLead.insertCustomerMasterTable(temp);
                            Log.d("TAG", "_DOWNLOAD_SoldToPartyList: " + RowData[1]);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = isSuccess;
            new Handler(Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ShipToPartyList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=ship&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ship_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ship_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            CustomerMasterTableDataSet temp = new CustomerMasterTableDataSet();
                            temp.setCust_code(RowData[1]);
                            temp.setCust_name(RowData[4]);
                            temp.setPhone_no(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            temp.setCust_type("ship_to_party");
                            mNewDatabaseForSiteLead.insertCustomerMasterTable(temp);
                            Log.d("TAG", "_DOWNLOAD_SoldToPartyList: 111" + RowData[1]);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = isSuccess;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_LeadList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/leadmaster/txt/?user_type=" + TYPE_OF_USER + "&login_username=" + Constants.employeeDetailObject.getEmpCode();
        Log.d("TAG", "_DOWNLOAD_LeadList: " + URL);
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "leadDetails");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "leadDetails" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                List<LeadListMasterTableDataSet> batch = new ArrayList<>();
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        Log.d("TAG", "_DOWNLOAD_LeadList: "+line);
                        line=line+" ";
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            LeadListMasterTableDataSet temp = new LeadListMasterTableDataSet();
                            temp.setLead_generation_id(RowData[0]);
                            temp.setEmp_code(RowData[1]);
                            temp.setLatitude(RowData[2]);
                            temp.setLongitude(RowData[3]);
                            temp.setLead_type(RowData[4]);
                            temp.setParty_name(RowData[5]);
                            temp.setBranch(RowData[6]);
                            temp.setDistrict(RowData[7]);
                            temp.setState(RowData[8]);
                            temp.setQty_req(RowData[9]);
                            temp.setProduct_packaging(RowData[10]);
                            temp.setExp_rate_per_bag(RowData[11]);
                            temp.setContact_person_name(RowData[12]);
                            temp.setDesignation(RowData[13]);
                            temp.setContact_number(RowData[14]);
                            temp.setMail_id(RowData[15]);
                            temp.setMode(RowData[16]);
                            temp.setQuotation(RowData[17]);
                            temp.setPO(RowData[18]);
                            temp.setStatus(RowData[19]);
                            temp.setRemarks(RowData[20]);
                            temp.setAssigned_to(RowData[21]);
                            temp.setSelf_other(RowData[22]);
                            temp.setAcc_block_is_required(RowData[23]);
                            temp.setCategory_type_construction(RowData[24]);
                            temp.setNext_visit_date(RowData[25]);
                            temp.setLead_status(RowData[26]);
                            temp.setCurrent_brand_used(RowData[27]);
                            temp.setCurrent_price(RowData[28]);
                            temp.setCurrent_price_competitor(RowData[29]);
                            temp.setR_timing(RowData[30]);
                            temp.setAction_on_lead(RowData[31]);
                            temp.setApproved_price(RowData[32]);
                            temp.setSales_org(RowData[33]);
                            temp.setDivision(RowData[34]);
                            temp.setDistribution_channel(RowData[35]);
                            temp.setDocument_type(RowData[36]);
                            temp.setCustomer_reference_no(RowData[37]);
                            temp.setCustomer_reference_date(RowData[38]);
                            temp.setValid_to_date(RowData[39]);
                            temp.setMaterial_number(RowData[40]);
                            temp.setSold_to_party(RowData[41]);
                            temp.setShip_to_party(RowData[42]);
                            temp.setPO_method(RowData[43]);
                            temp.setShare_lead_site_details_pic(RowData[44]);
                            temp.setType_lead(RowData[45]);
                            temp.setLead_remarks(RowData[46]);
                            temp.setLead_action(RowData[47]);
                            temp.setCredit_terms(RowData[48]);
                            temp.setMonth_qty(RowData[49]);
                            temp.setQuotation_provided(RowData[50]);
                            temp.setQuotation_provided_date(RowData[51]);
                            temp.setMis_submission_date(RowData[52]);
                            temp.setHos_submission_date(RowData[53]);
                            temp.setDownload_time(RowData[54]);
                            temp.setDestination(RowData[55]);
                            temp.setCompany_constraint(RowData[56]);
                            temp.setReason(RowData[57]);
                            temp.setNov(RowData[58]);
                            temp.setIncoterms(RowData[59]);
                            temp.setServing_location(RowData[60]);
                            temp.setQuoted_price(RowData[61]);
                            temp.setTpc(RowData[62]);
                            temp.setPayment(RowData[63]);
                            temp.setLast_price(RowData[64]);
                            temp.setPrev_last_price(RowData[65]);
                            temp.setLead_quotation_status(RowData[66]);
                            temp.setLost_reason(RowData[67]);
                            temp.setQuotation_number(RowData[68]);
                            temp.setQuotation_pdf(RowData[69]);
                            temp.setPo_number(RowData[70]);
                            temp.setPo_date(RowData[71]);
                            temp.setPo_image(RowData[72]);
                            temp.setPo_revert_note(RowData[73]);
                            temp.setPo_revert_level(RowData[74]);
                            temp.setPo_foward_note(RowData[75]);
                            temp.setContract_number(RowData[76]);
                            temp.setSales_order_number(RowData[77].equalsIgnoreCase(" ")?"":RowData[77]);
                            batch.add(temp);
                        }
                    }
                }
                mNewDatabaseForSiteLead.insertLeadListMasterTableDataBatch(batch);
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = isSuccess;
            new Handler(Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< LEAD FUNNEL >>>>>>>>>>>>>>>>>>>>>>>>>>>


    // <<<<<<<<<<<<<<<<<<<<<<<<<<< QUERY & LEAD GENERATION >>>>>>>>>>>>>>>>>>>>>>>>>>>
    // =========================== API Call ===========================
    public void _DOWNLOAD_emp_master(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/employee_list/?emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&level_type=so";
        new Thread(() -> {
            Download_txt(URL, "emp_data");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "emp_data" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            EmployeeDataSet temp = new EmployeeDataSet();
                            temp.setEmp_code(RowData[0]);
                            temp.setEmp_name(RowData[2]);
                            temp.setSelect(false);
                            mNewDatabaseForSiteLead.insertEmployeeDetails(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_sold_to_party(DownloadCallback callback, String emp_code) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=sold&emp_code=" + emp_code;
        new Thread(() -> {
            Download_txt(URL, "sold_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "sold_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            int count=0;
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    count++;
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            PartyDataList temp = new PartyDataList();
                            temp.setCode(RowData[1]);
                            temp.setName(RowData[4]);
                            temp.setCustomerCode(RowData[1]);
                            temp.setPhoneNo(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            mNewDatabaseForSiteLead.insertSoldToParty(temp, emp_code);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_sold_to_party ("+emp_code+"): "+URL+"\n"+count);
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_ship_to_party(DownloadCallback callback, String emp_code) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=ship&emp_code=" + emp_code;
        new Thread(() -> {
            Download_txt(URL, "ship_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ship_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            int count=0;
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    count++;
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            PartyDataList temp = new PartyDataList();
                            temp.setCode(RowData[1]);
                            temp.setName(RowData[4]);
                            temp.setCustomerCode(RowData[1]);
                            temp.setPhoneNo(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            mNewDatabaseForSiteLead.insertShipToParty(temp, emp_code);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_ship_to_party ("+emp_code+"): "+URL+"\n"+count);
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_assigned_to(DownloadCallback callback, String emp_code) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/employee_list/?emp_code=" + emp_code + "&level_type=hos";
        new Thread(() -> {
            Download_txt(URL, "assigned_to");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "assigned_to" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            int count=0;
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    count++;
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            EmployeeDataSet temp = new EmployeeDataSet();
                            temp.setEmp_code(RowData[0]);
                            temp.setEmp_name(RowData[2]);
                            temp.setSelect(false);
                            mNewDatabaseForSiteLead.insertAssignedTo(temp, emp_code);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            Log.d("TAG", "DataForDownloadingLead _DOWNLOAD_assigned_to ("+emp_code+"): "+URL+"\n"+count);
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_product_list(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_product_list/";
        new Thread(() -> {
            Download_txt(URL, "product_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "product_list" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            DataSet temp = new DataSet();
                            temp.setId(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setSelect(false);
                            mNewDatabaseForSiteLead.insertProductList(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_state_list(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_state_list/";
        new Thread(() -> {
            Download_txt(URL, "state_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "state_list" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            StateDataSet temp = new StateDataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setSelect(false);
                            mNewDatabaseForSiteLead.insertStateList(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }

    public void _DOWNLOAD_district_list(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_district_list/";
        new Thread(() -> {
            Download_txt(URL, "district_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "district_list" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            DistrictDataSet temp = new DistrictDataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setStateCode(RowData[1]);
                            temp.setSelect(false);
                            mNewDatabaseForSiteLead.insertDistrictList(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(true)
            );
        }).start();
    }
    // ========================================================

    // =========================== Local Call ===========================
    private void dataSetSegmentList() {
        String[][] data = {
                {"KEY", "Key"},
                {"NON-KEY", "Non-Key"},
                {"TENDER", "Tender"},
                {"ROE", "ROE"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertSegmentList(new DataSet(entry[0], entry[1], false));
        }
        dataSetLeadSourceList();
    }

    private void dataSetLeadSourceList() {
        String[][] data = {
                {"TENDER / GOVERNMENT PROJECT INFO", "Tender / Government Project Info"},
                {"AGENCIES", "Agencies"},
                {"SITE VISIT", "Site Visit"},
                {"COMPANY SELF", "Company Self"},
                {"PHONE CALL", "Phone Call"},
                {"SALES TEAM GENERATED", "Sales Team Generated"},
                {"SOCIAL MEDIA", "Social Media"},
                {"ONLINE ADS", "Online Ads"},
                {"EMAIL CAMPAIGNS", "Email Campaigns"},
                {"WALK-IN AT STAR OFFICE", "Walk-in at Star Office"},
                {"MEETING", "Meeting"},
                {"REFERRAL FROM EXISTING CUSTOMER", "Referral from Existing Customer"},
                {"DEALER / RSAR REFERENCE", "Dealer / RSAR Reference"},
                {"EXHIBITIONS / TRADE FAIRS", "Exhibitions / Trade Fairs"},
                {"HOARDINGS / BANNERS", "Hoardings / Banners"},
                {"NEWSPAPER ADS", "Newspaper Ads"},
                {"CAMPAIGNS / PROMOTIONAL ACTIVITIES", "Campaigns / Promotional Activities"},
                {"OLD LEADS", "Old Leads"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertLeadSourceList(new DataSet(entry[0], entry[1], false));
        }
        dataSetModeOfPaymentList();
    }

    private void dataSetModeOfPaymentList() {
        String[][] data = {
                {"ADVANCE", "Advance"},
                {"BANK GUARANTEE", "BG"},
                {"CLEAN CREDIT", "Clean Credit"},
                {"SECURITY CHEQUE", "Security Cheque"},
                {"POST DATED CHEQUE", "Post Dated Cheque"},
                {"OTHERS", "Others"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertModeOfPaymentList(new DataSet(entry[0], entry[1], false));
        }
        dataSetCreditTermsList();
    }

    private void dataSetCreditTermsList() {
        String[][] data = {
                {"7 DAYS", "7 Days"},
                {"15 DAYS", "15 Days"},
                {"30 DAYS", "30 Days"},
                {"45 DAYS", "45 Days"},
                {"60 DAYS", "60 Days"},
                {"75 DAYS", "75 Days"},
                {"90 DAYS", "90 Days"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertCreditTermsList(new DataSet(entry[0], entry[1], false));
        }
        dataSetAacBlockRequiredCheckList();
    }

    private void dataSetAacBlockRequiredCheckList() {
        String[][] data = {
                {"YES", "Yes "},
                {"NO", "No "}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertAacBlockRequiredList(new DataSet(entry[0], entry[1], false));
        }
        dataSetConstructionTypeList();
    }

    private void dataSetConstructionTypeList() {
        String[][] data = {
                {"BUILDING PROJECTS - PUBLIC OR PRIVATE", "Building Projects - Public or Private"},
                {"INDUSTRIAL - MANUFACTURING", "Industrial - Manufacturing"},
                {"INDUSTRIAL - WAREHOUSING", "Industrial - Warehousing"},
                {"ROADS, BRIDGES AND HIGHWAYS", "Roads, Bridges and Highways"},
                {"WATER SUPPLY & DISTRIBUTION", "Water Supply & Distribution"},
                {"POWER PROJECTS", "Power Projects"},
                {"RAILWAY PROJECT OR SLEEPER MANUFACTURING", "Railway Project or Sleeper Manufacturing"},
                {"PRE-CAST INDUSTRIES (PIPE AND POLES)", "Pre-cast Industries (Pipe and Poles)"},
                {"OTHER", "Other"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertConstructionTypeList(new DataSet(entry[0], entry[1], false));
        }
        dataSetLeadStatusList();
    }

    private void dataSetLeadStatusList() {
        String[][] data = {
                {"HOT", "Hot ( Immediate Requirement - within 7 days )"},
                {"WARM", "Warm ( Planned Required - within 8 to 14 days )"},
                {"COLD", "Cold ( Future requirement - after 15 or more days )"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertLeadStatusList(new DataSet(entry[0], entry[1], false));
        }
        dataSetRequirementTypeList();
    }

    private void dataSetRequirementTypeList() {
        String[][] data = {
                {"FOR", "Free on Road (FOR)"},
                {"EXW", "Ex. Works (ExW)"},
                {"FOS", "Free on Siding (FOS)"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertRequirementTypeList(new DataSet(entry[0], entry[1], false));
        }
        dataSetExWorkList();
    }

    private void dataSetExWorkList() {
        String[][] data = {
                {"EX. LUMS PLANT", "Ex. LUMS Plant"},
                {"EX. GGU LINE-1/SCNEL", "Ex. GGU Line-1/SCNEL"},
                {"EX. SGU PLANT", "Ex. SGU Plant"},
                {"EX. BYRNIHAT DUMP", "Ex. Byrnihat Dump"},
                {"EX. JORABAT DUMP", "Ex. Jorabat Dump"},
                {"EX. FULERTAL DUMP", "Ex. Fulertal Dump"},
                {"EX. SILIGURI-2 DUMP", "Ex. Siliguri-2 Dump"},
                {"EX. VAIRENGTE DUMP", "Ex. Vairengte Dump"},
                {"EX. AIZWAL DUMP", "Ex. Aizwal Dump"},
                {"EX. SILCHAR DUMP", "Ex. Silchar Dump"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertExWorksList(new DataSet(entry[0], entry[1], false));
        }
        dataSetRequirementTimingList();
    }

    private void dataSetRequirementTimingList() {
        String[][] data = {
                {"7 DAYS", "7 Days"},
                {"15 DAYS", "15 Days"},
                {"30 DAYS", "30 Days"},
                {"45 DAYS", "45 Days"},
                {"60 DAYS", "60 Days"},
                {"75 DAYS", "75 Days"},
                {"90 DAYS", "90 Days"}
        };
        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertRequirementTimingList(new DataSet(entry[0], entry[1], false));
        }
        dataSetLeadActionList();
    }

    private void dataSetLeadActionList() {
        String[][] data = {
                {"YES", "Yes, send quotation"},
                {"NO", "No, not required quotation"},
                {"HOLD", "Hold"},
                {"REVISION", "Send back for revision"}
        };

        for (String[] entry : data) {
            mNewDatabaseForSiteLead.insertLeadActionList(new DataSet(entry[0], entry[1], false));
        }
    }
    // ========================================================
    // <<<<<<<<<<<<<<<<<<<<<<<<<<< QUERY & LEAD GENERATION >>>>>>>>>>>>>>>>>>>>>>>>>>>
}
