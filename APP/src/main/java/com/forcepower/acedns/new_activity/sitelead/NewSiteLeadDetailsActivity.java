package com.forcepower.acedns.new_activity.sitelead;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowExistingSiteDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.SiteLeadDataSet;
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
import java.util.ArrayList;
import java.util.Collections;
import java.util.Objects;

public class NewSiteLeadDetailsActivity extends AceDnsParentActivity implements View.OnClickListener {

    private Button backButton, filterButton;
    private TextView textTransactionId, textUniqueLeadId, textSiteCreationDate, textVisitDate, textEmployeeCode, textEmployeeName, textZone, textState, textBranch,
            textDistrict, textLatitude, textLongitude, textCustomerName, textCustomerContactNo, textPettyContractorRegdInStarLink, textHeadMasonName, textHeadMasonContactNo,
            textEngineerRegdInStarStellar, textEngineerName, textEngineerContactNo, textMeetingPerson, textDecisionMaker, textSiteSegment, textVisitType, textProjectSegment,
            textTypeOfConstruction, textCurrentStageOfConstruction, textBuiltUpArea, textSitePotential, textConsumedTillDate, textBalancePotential, textBalancePotentialManual,
            textSiteCategory, textBrandUsed, textPricePerBag, textConversion, textProduct, textNoOfBagsOrdered, textRequestedDateOfDelivery, textCounterType, textCounterName,
            textCounterCode, textReasonsForNonConversion, textSitePriority, textWeatherShieldDemo, textApprovalStatus, textDateAndTime, textAsmName, textAsmEmployeeId,
            textActualDateOfDelivery, textDeliveryRemarks, textReasonForNotDelivery, textSiteRemarks, textSiteStatus, textCustomerAddress, textFloorCount;

    Context mContext;
    ArrayList<DataSet> branchList = new ArrayList<>();
    ArrayList<SiteLeadDataSet> existingSiteLeadList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_site_lead_details);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mContext=this;
        init();
        _DOWNLOAD_ExistingSiteLeadList();
        _DOWNLOAD_BranchList();
    }

    @Override
    public void onClick(View v) {
        if(backButton==v){
            finish();
        }
        if(filterButton==v){
            Log.d("TAG", "_DOWNLOAD_ : hi");
            showExistingSiteListDataDialog(existingSiteLeadList,"Select Site Lead");
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        filterButton = findViewById(R.id.filterButton);

        textTransactionId = findViewById(R.id.textTransactionId);
        textUniqueLeadId = findViewById(R.id.textUniqueLeadId);
        textSiteCreationDate = findViewById(R.id.textSiteCreationDate);
        textVisitDate = findViewById(R.id.textVisitDate);
        textEmployeeCode = findViewById(R.id.textEmployeeCode);
        textEmployeeName = findViewById(R.id.textEmployeeName);
        textZone = findViewById(R.id.textZone);
        textState = findViewById(R.id.textState);
        textBranch = findViewById(R.id.textBranch);
        textDistrict = findViewById(R.id.textDistrict);
        textLatitude = findViewById(R.id.textLatitude);
        textLongitude = findViewById(R.id.textLongitude);
        textCustomerName = findViewById(R.id.textCustomerName);
        textCustomerContactNo = findViewById(R.id.textCustomerContactNo);
        textPettyContractorRegdInStarLink = findViewById(R.id.textPettyContractorRegdInStarLink);
        textHeadMasonName = findViewById(R.id.textHeadMasonName);
        textHeadMasonContactNo = findViewById(R.id.textHeadMasonContactNo);
        textEngineerRegdInStarStellar = findViewById(R.id.textEngineerRegdInStarStellar);
        textEngineerName = findViewById(R.id.textEngineerName);
        textEngineerContactNo = findViewById(R.id.textEngineerContactNo);
        textMeetingPerson = findViewById(R.id.textMeetingPerson);
        textDecisionMaker = findViewById(R.id.textDecisionMaker);
        textSiteSegment = findViewById(R.id.textSiteSegment);
        textVisitType = findViewById(R.id.textVisitType);
        textProjectSegment = findViewById(R.id.textProjectSegment);
        textTypeOfConstruction = findViewById(R.id.textTypeOfConstruction);
        textCurrentStageOfConstruction = findViewById(R.id.textCurrentStageOfConstruction);
        textBuiltUpArea = findViewById(R.id.textBuiltUpArea);
        textSitePotential = findViewById(R.id.textSitePotential);
        textConsumedTillDate = findViewById(R.id.textConsumedTillDate);
        textBalancePotential = findViewById(R.id.textBalancePotential);
        textBalancePotentialManual = findViewById(R.id.textBalancePotentialManual);
        textSiteCategory = findViewById(R.id.textSiteCategory);
        textBrandUsed = findViewById(R.id.textBrandUsed);
        textPricePerBag = findViewById(R.id.textPricePerBag);
        textConversion = findViewById(R.id.textConversion);
        textProduct = findViewById(R.id.textProduct);
        textNoOfBagsOrdered = findViewById(R.id.textNoOfBagsOrdered);
        textRequestedDateOfDelivery = findViewById(R.id.textRequestedDateOfDelivery);
        textCounterType = findViewById(R.id.textCounterType);
        textCounterName = findViewById(R.id.textCounterName);
        textCounterCode = findViewById(R.id.textCounterCode);
        textReasonsForNonConversion = findViewById(R.id.textReasonsForNonConversion);
        textSitePriority = findViewById(R.id.textSitePriority);
        textWeatherShieldDemo = findViewById(R.id.textWeatherShieldDemo);
        textApprovalStatus = findViewById(R.id.textApprovalStatus);
        textDateAndTime = findViewById(R.id.textDateAndTime);
        textAsmName = findViewById(R.id.textAsmName);
        textAsmEmployeeId = findViewById(R.id.textAsmEmployeeId);
        textActualDateOfDelivery = findViewById(R.id.textActualDateOfDelivery);
        textDeliveryRemarks = findViewById(R.id.textDeliveryRemarks);
        textReasonForNotDelivery = findViewById(R.id.textReasonForNotDelivery);
        textSiteRemarks = findViewById(R.id.textSiteRemarks);
        textSiteStatus = findViewById(R.id.textSiteStatus);
        textCustomerAddress =findViewById(R.id.textCustomerAddress);
        textFloorCount=findViewById(R.id.textFloorCount);

        onClickSetup();
    }
    private void onClickSetup(){
        backButton.setOnClickListener(this);
        filterButton.setOnClickListener(this);
    }

    @SuppressLint("SetTextI18n")
    public void showExistingSiteListDataDialog(ArrayList<SiteLeadDataSet> dataSet, String titleValue) {
        try {
            final ShowExistingSiteDataSetAdapter pAdapter = new ShowExistingSiteDataSetAdapter(this, R.layout.existing_site_lead_item, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                showExistingSiteLeadInfo(Objects.requireNonNull(pAdapter.getItem(position)));
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
            Log.d("TAG", "_DOWNLOAD_ showExistingSiteListDataDialog: "+ignored.getMessage());
        }
    }
    private void showExistingSiteLeadInfo(SiteLeadDataSet dataSet) {
        runOnUiThread(() -> {
            String branchName = "";
            for (int i = 0; i < branchList.size(); i++) {
                if (branchList.get(i).getTitle().equalsIgnoreCase(dataSet.getBranch())) {
                    branchName = branchList.get(i).getValue();
                    break;
                }
            }

            textTransactionId.setText(dataSet.getTransactionId());
            textUniqueLeadId.setText(dataSet.getUniqueId());
            textSiteCreationDate.setText(dataSet.getCreatedAt().split(" ")[0]);
            textVisitDate.setText(dataSet.getVisitDate().split(" ")[0]);
            textEmployeeCode.setText(dataSet.getEmpCode());
            textEmployeeName.setText(dataSet.getEmpName());
            textZone.setText(dataSet.getZone());
            textState.setText(dataSet.getState());
            textBranch.setText(branchName);
            textDistrict.setText(dataSet.getDistrict());
            textLatitude.setText(dataSet.getLatitude());
            textLongitude.setText(dataSet.getLongitude());
            textCustomerName.setText(dataSet.getCustomerName());
            textCustomerContactNo.setText(dataSet.getCustomerPhoneNo());
            textCustomerAddress.setText(dataSet.getAddress());
            textPettyContractorRegdInStarLink.setText(dataSet.getPettyContractorRegistered());
            textHeadMasonName.setText(dataSet.getHeadMasonName());
            textHeadMasonContactNo.setText(dataSet.getHeadMasonContact());
            textEngineerRegdInStarStellar.setText(dataSet.getEngineerRegistered());
            textEngineerName.setText(dataSet.getEngineerName());
            textEngineerContactNo.setText(dataSet.getEngineerContact());
            textMeetingPerson.setText(dataSet.getMeetingPerson());
            textDecisionMaker.setText(dataSet.getDecisionMaker());
            textSiteSegment.setText(dataSet.getSiteSegment());
            textVisitType.setText(dataSet.getVisitType());
            textProjectSegment.setText(dataSet.getProjectSegment());
            textTypeOfConstruction.setText(dataSet.getTypeOfConst());
            textCurrentStageOfConstruction.setText(dataSet.getCurrentStageOfConstruction());
            textBuiltUpArea.setText(dataSet.getBuiltUpArea());
            textSitePotential.setText(dataSet.getSitePotential());
            textConsumedTillDate.setText(dataSet.getConsumedTillDate());
            textBalancePotential.setText(dataSet.getBalancePotential());
            textBalancePotentialManual.setText(dataSet.getBalancePotentialManual());
            textSiteRemarks.setText(dataSet.getRemarks());
            textSiteCategory.setText(dataSet.getSiteCategory());
            textBrandUsed.setText(dataSet.getBrandUsed());
            textPricePerBag.setText(dataSet.getPricePerBag());
            textConversion.setText(dataSet.getConversion());
            textProduct.setText(dataSet.getSelectProduct());
            textNoOfBagsOrdered.setText(dataSet.getNoOfBagsOrdered());
            textRequestedDateOfDelivery.setText(dataSet.getRequestedDate());
            textCounterType.setText(dataSet.getCounterType());
            textCounterName.setText(dataSet.getCounterName());
            textCounterCode.setText(dataSet.getCounterCode());
            textReasonsForNonConversion.setText(dataSet.getReasonForNonConversion());
            textSitePriority.setText(dataSet.getSitePriority());
            textWeatherShieldDemo.setText(dataSet.getWeatherShieldDemo());
            textSiteStatus.setText(dataSet.getSiteStatus());
            textApprovalStatus.setText(dataSet.getApprovalStatus());
            textDateAndTime.setText(dataSet.getApprovalDateTime());
            textAsmName.setText(dataSet.getAsmName());
            textAsmEmployeeId.setText(dataSet.getAsmId());
            textActualDateOfDelivery.setText(dataSet.getActualDateOfDelivery());
            textDeliveryRemarks.setText(dataSet.getDeliveryRemarks());
            textReasonForNotDelivery.setText(dataSet.getReasonForNotDelivery());
            textFloorCount.setText(dataSet.getFloorCount());
        });
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
    public void _DOWNLOAD_ExistingSiteLeadList() {
//        progressDialogUpdate("Downloading Existing Site Lead List ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_get_site_list_site_lead_today.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Log.d("TAG", "_DOWNLOAD_ ExistingSiteLeadList: " + URL);
        new Thread(() -> {
            Download_txt(URL, "ExistingSiteLeadList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ExistingSiteLeadList" + ".txt");
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
                    } else if (line.indexOf("#") > 0) {
                        // Data not save in list
                        String a = "";
                    } else {
                        Log.d("TAG", "_DOWNLOAD_ ExistingSiteLeadList: " + line);
                        String[] RowData = (line + " ").split("\\^");
                        Log.d("TAG", "_DOWNLOAD_ ExistingSiteLeadList: " + RowData.length + " / " + noColumn[0]);
                        if (RowData.length == noColumn[0]) {
                            SiteLeadDataSet temp = new SiteLeadDataSet();
                            temp.setId(RowData[0]);
                            temp.setTransactionId(RowData[1]);
                            temp.setUniqueId(RowData[2]);
                            temp.setVisitDate(RowData[3]);
                            temp.setEmpCode(RowData[4]);
                            temp.setEmpName(RowData[5]);
                            temp.setZone(RowData[6]);
                            temp.setBranch(RowData[7]);
                            temp.setDistrict(RowData[8]);
                            temp.setState(RowData[9]);
                            temp.setLongitude(RowData[10]);
                            temp.setLatitude(RowData[11]);
                            temp.setCustomerName(RowData[12]);
                            temp.setCustomerPhoneNo(RowData[13]);
                            temp.setAddress(RowData[14]);
                            temp.setSiteSegment(RowData[15]);
                            temp.setVisitType(RowData[16]);
                            temp.setProjectSegment(RowData[17]);
                            temp.setTypeOfConst(RowData[18]);
                            temp.setBuiltUpArea(RowData[19]);
                            temp.setNoOfBag(RowData[20]);
                            temp.setConversion(RowData[21]);
                            temp.setSitePriority(RowData[22]);
                            temp.setCounterCode(RowData[23]);
                            temp.setCreatedAt(RowData[24]);
                            temp.setUpdatedAt(RowData[25]);
                            temp.setNewSiteLeadId(RowData[26]);
                            temp.setNewSiteLeadUniqueId(RowData[27]);
                            temp.setPettyContractorRegistered(RowData[28]);
                            temp.setHeadMasonName(RowData[29]);
                            temp.setContractorId(RowData[30]);
                            temp.setHeadMasonContact(RowData[31]);
                            temp.setEngineerRegistered(RowData[32]);
                            temp.setEngineerName(RowData[33]);
                            temp.setEngineerId(RowData[34]);
                            temp.setEngineerContact(RowData[35]);
                            temp.setMeetingPerson(RowData[36]);
                            temp.setDecisionMaker(RowData[37]);
                            temp.setCurrentStageOfConstruction(RowData[38]);
                            temp.setSitePotential(RowData[39]);
                            temp.setConsumedTillDate(RowData[40]);
                            temp.setBalancePotential(RowData[41]);
                            temp.setSiteCategory(RowData[42]);
                            temp.setBrandUsed(RowData[43]);
                            temp.setPricePerBag(RowData[44]);
                            temp.setSelectProduct(RowData[45]);
                            temp.setNoOfBagsOrdered(RowData[46]);
                            temp.setRequestedDate(RowData[47]);
                            temp.setCounterType(RowData[48]);
                            temp.setCounterName(RowData[49]);
                            temp.setReasonForNonConversion(RowData[50]);
                            temp.setWeatherShieldDemo(RowData[51]);
                            temp.setApprovalStatus(RowData[52]);
                            temp.setApprovalDateTime(RowData[53]);
                            temp.setAsmName(RowData[54]);
                            temp.setAsmId(RowData[55]);
                            temp.setActualDateOfDelivery(RowData[57]);
                            temp.setDeliveryRemarks(RowData[58]);
                            temp.setReasonForNotDelivery(RowData[59]);
                            temp.setSiteStatus(RowData[60]);
                            temp.setFloorCount(RowData[61]);
                            temp.setBalancePotentialManual(RowData[62].trim());
                            temp.setRemarks(RowData[63].trim());

                            existingSiteLeadList.add(temp);
                            Log.d("TAG", "_DOWNLOAD_ ExistingSiteLeadList DATA ADDED");
                        }
                    }
                }
                buffer.close();
//                progressDialogClose();
//                showExistingSiteListDataDialog(existingSiteLeadList,"Select Site Lead");
            } catch (IOException ex) {
                Log.d("TAG", "_DOWNLOAD_ExistingSiteLeadList: " + ex.getMessage());
//                progressDialogClose();
            }
        }).start();
    }
    public void _DOWNLOAD_BranchList() {
//        progressDialogOpen("Downloading Branch List ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_branch_site_lead.php";
        new Thread(() -> {
            Download_txt(URL, "BranchList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "BranchList" + ".txt");
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
                            DataSet temp = new DataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[1]);
                            branchList.add(temp);
                        }
                    }
                }
                buffer.close();
                Collections.sort(branchList, (o1, o2) ->
                        o1.getValue().compareToIgnoreCase(o2.getValue())
                );
//                _DOWNLOAD_StateList();
            } catch (IOException ignored) {
//                progressDialogClose();
            }
        }).start();
    }
}