package com.forcepower.acedns.newDataBase.sync;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Handler;
import android.os.Looper;
import android.preference.PreferenceManager;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.new_activity.sitelead.dataset.CounterNameDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DistrictDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProductDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProfileDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.Utils;

import org.json.JSONObject;

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
import java.util.Date;
import java.util.Locale;

public class DataForDownloading {
    Context mContext;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    boolean check = true;

    public DataForDownloading(Context context) {
        mContext = context;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
    }

    public void addAllFormDataForSiteLead(DownloadCallback callback) {
        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();
        _DOWNLOAD_CompetitorQuantity();
        _DOWNLOAD_BranchList(successBranchList -> {
            if (!successBranchList) {
                callback.onComplete(false);
            } else {
                Log.d("TAG", "_CheckDB_ _DOWNLOAD_BranchList");
                _DOWNLOAD_StateList(successStateList -> {
                    if (!successStateList) {
                        callback.onComplete(false);
                    } else {
                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_StateList");
                        _DOWNLOAD_DistrictList(successDistrictList -> {
                            if (!successDistrictList) {
                                callback.onComplete(false);
                            } else {
                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_DistrictList");
                                _DOWNLOAD_ReqdContractorLinkList(successReqdContractorLinkList -> {
                                    if (!successReqdContractorLinkList) {
                                        callback.onComplete(false);
                                    } else {
                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_ReqdContractorLinkList");
                                        _DOWNLOAD_ContractorLinkList(successContractorLinkList -> {
                                            if (!successContractorLinkList) {
                                                callback.onComplete(false);
                                            } else {
                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_ContractorLinkList");
                                                _DOWNLOAD_ReqdEngineerStellarList(successReqdEngineerStellarList -> {
                                                    if (!successReqdEngineerStellarList) {
                                                        callback.onComplete(false);
                                                    } else {
                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_ReqdEngineerStellarList");
                                                        _DOWNLOAD_EngineerStellarList(successEngineerStellarList -> {
                                                            if (!successEngineerStellarList) {
                                                                callback.onComplete(false);
                                                            } else {
                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_EngineerStellarList");
                                                                _DOWNLOAD_MeetingPersonList(successMeetingPersonList -> {
                                                                    if (!successMeetingPersonList) {
                                                                        callback.onComplete(false);
                                                                    } else {
                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_MeetingPersonList");
                                                                        _DOWNLOAD_DecisionMakerList(successDecisionMakerList -> {
                                                                            if (!successDecisionMakerList) {
                                                                                callback.onComplete(false);
                                                                            } else {
                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_DecisionMakerList");
                                                                                _DOWNLOAD_SiteSegmentList(successSiteSegmentList -> {
                                                                                    if (!successSiteSegmentList) {
                                                                                        callback.onComplete(false);
                                                                                    } else {
                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_SiteSegmentList");
                                                                                        _DOWNLOAD_VisitTypeList(successVisitTypeList -> {
                                                                                            if (!successVisitTypeList) {
                                                                                                callback.onComplete(false);
                                                                                            } else {
                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_VisitTypeList");
                                                                                                _DOWNLOAD_ProjectSegmentList(successProjectSegmentList -> {
                                                                                                    if (!successProjectSegmentList) {
                                                                                                        callback.onComplete(false);
                                                                                                    } else {
                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_ProjectSegmentList");
                                                                                                        _DOWNLOAD_TypeOfConstructionList(successTypeOfConstructionList -> {
                                                                                                            if (!successTypeOfConstructionList) {
                                                                                                                callback.onComplete(false);
                                                                                                            } else {
                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_TypeOfConstructionList");
                                                                                                                _DOWNLOAD_FloorCountList(successFloorCountList -> {
                                                                                                                    if (!successFloorCountList) {
                                                                                                                        callback.onComplete(false);
                                                                                                                    } else {
                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_FloorCountList");
                                                                                                                        _DOWNLOAD_CurrentStageOfConstructionList(successCurrentStageOfConstructionList -> {
                                                                                                                            if (!successCurrentStageOfConstructionList) {
                                                                                                                                callback.onComplete(false);
                                                                                                                            } else {
                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_CurrentStageOfConstructionList");
                                                                                                                                _DOWNLOAD_BrandUsedList(successBrandUsedList -> {
                                                                                                                                    if (!successBrandUsedList) {
                                                                                                                                        callback.onComplete(false);
                                                                                                                                    } else {
                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_BrandUsedList");
                                                                                                                                        _DOWNLOAD_ConversionList(successConversionList -> {
                                                                                                                                            if (!successConversionList) {
                                                                                                                                                callback.onComplete(false);
                                                                                                                                            } else {
                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_ConversionList");
                                                                                                                                                _DOWNLOAD_ProductList(successProductList -> {
                                                                                                                                                    if (!successProductList) {
                                                                                                                                                        callback.onComplete(false);
                                                                                                                                                    } else {
                                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_ProductList");
                                                                                                                                                        _DOWNLOAD_CounterType(successCounterType -> {
                                                                                                                                                            if (!successCounterType) {
                                                                                                                                                                callback.onComplete(false);
                                                                                                                                                            } else {
                                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_CounterType");
                                                                                                                                                                _DOWNLOAD_CounterNameList(successCounterNameList -> {
                                                                                                                                                                    if (!successCounterNameList) {
                                                                                                                                                                        callback.onComplete(false);
                                                                                                                                                                    } else {
                                                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_CounterNameList");
                                                                                                                                                                        _DOWNLOAD_ReasonsForNonConversionList(successReasonsForNonConversionList -> {
                                                                                                                                                                            if (!successReasonsForNonConversionList) {
                                                                                                                                                                                callback.onComplete(false);
                                                                                                                                                                            } else {
                                                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_ReasonsForNonConversionList");
                                                                                                                                                                                _DOWNLOAD_PriorityList(successPriorityList -> {
                                                                                                                                                                                    if (!successPriorityList) {
                                                                                                                                                                                        callback.onComplete(false);
                                                                                                                                                                                    } else {
                                                                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_PriorityList");
                                                                                                                                                                                        _DOWNLOAD_WeatherShieldDemoList(successWeatherShieldDemoList -> {
                                                                                                                                                                                            if (!successWeatherShieldDemoList) {
                                                                                                                                                                                                callback.onComplete(false);
                                                                                                                                                                                            } else {
                                                                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_WeatherShieldDemoList");
                                                                                                                                                                                                _DOWNLOAD_ApprovalStatusList(successApprovalStatusList -> {
                                                                                                                                                                                                    if (!successApprovalStatusList) {
                                                                                                                                                                                                        callback.onComplete(false);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_ApprovalStatusList");
                                                                                                                                                                                                        _DOWNLOAD_ASMNameList(successASMNameList -> {
                                                                                                                                                                                                            if (!successASMNameList) {
                                                                                                                                                                                                                callback.onComplete(false);
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_ASMNameList");
                                                                                                                                                                                                                _DOWNLOAD_SiteStatusList(successSiteStatusList -> {
                                                                                                                                                                                                                    if (!successSiteStatusList) {
                                                                                                                                                                                                                        callback.onComplete(false);
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        Log.d("TAG", "_CheckDB_ _DOWNLOAD_SiteStatusList");
                                                                                                                                                                                                                        _DOWNLOAD_ExistingSiteLeadList(successExistingSiteLeadList -> {
                                                                                                                                                                                                                            if (!successExistingSiteLeadList) {
                                                                                                                                                                                                                                callback.onComplete(false);
                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                Log.d("TAG", "_CheckDB_ _DOWNLOAD_ExistingSiteLeadList");
                                                                                                                                                                                                                                callback.onComplete(true);

                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        });
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                });
                                                                                                                                                                                                            }
                                                                                                                                                                                                        });
                                                                                                                                                                                                    }
                                                                                                                                                                                                });
                                                                                                                                                                                            }
                                                                                                                                                                                        });
                                                                                                                                                                                    }
                                                                                                                                                                                });
                                                                                                                                                                            }
                                                                                                                                                                        });
                                                                                                                                                                    }
                                                                                                                                                                });
                                                                                                                                                            }
                                                                                                                                                        });
                                                                                                                                                    }
                                                                                                                                                });
                                                                                                                                            }
                                                                                                                                        });
                                                                                                                                    }
                                                                                                                                });
                                                                                                                            }
                                                                                                                        });
                                                                                                                    }
                                                                                                                });
                                                                                                            }
                                                                                                        });
                                                                                                    }
                                                                                                });
                                                                                            }
                                                                                        });
                                                                                    }
                                                                                });
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
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

    public void _DOWNLOAD_BranchList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_branch_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
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
                            mNewDatabaseForSiteLead.insertBranch(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_StateList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_state_list_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "StateList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "StateList" + ".txt");
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
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertState(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_DistrictList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_district_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "DistrictList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "DistrictList" + ".txt");
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
                            temp.setStateName(RowData[1]);
                            mNewDatabaseForSiteLead.insertDistrict(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ReqdContractorLinkList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_petty_contractor_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "IsRegLink");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "IsRegLink" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertReqContractorLink(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ContractorLinkList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_star_link_contractor_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "RegLink");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "RegLink" + ".txt");
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
                            ProfileDataSet temp = new ProfileDataSet();
                            temp.setCode(RowData[0]);
                            temp.setName(RowData[1]);
                            temp.setNumber(RowData[2]);
                            mNewDatabaseForSiteLead.insertContractorLink(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ReqdEngineerStellarList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_engg_registered_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "IsRegStellar");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "IsRegStellar" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertReqEngineerStellar(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_EngineerStellarList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_star_stellar_engg_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "RegStellar");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "RegStellar" + ".txt");
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
                            ProfileDataSet temp = new ProfileDataSet();
                            temp.setCode(RowData[0]);
                            temp.setName(RowData[1]);
                            temp.setNumber(RowData[2]);
                            mNewDatabaseForSiteLead.insertEngineerStellar(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_MeetingPersonList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_meeting_person_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "MeetingPersonList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "MeetingPersonList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertMeetingPerson(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_DecisionMakerList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_decision_maker_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "DecisionMakerList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "DecisionMakerList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertDecisionMaker(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_SiteSegmentList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_site_segment_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "SiteSegmentList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "SiteSegmentList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertSiteSegment(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_VisitTypeList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_visit_type_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "VisitTypeList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "VisitTypeList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertVisitType(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ProjectSegmentList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_project_segment_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ProjectSegmentList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ProjectSegmentList" + ".txt");
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
                        DataSet temp = new DataSet();
                        temp.setTitle(RowData[1]);
                        temp.setValue(RowData[0]);
                        mNewDatabaseForSiteLead.insertProjectSegment(temp);
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_TypeOfConstructionList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_construction_category_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "TypeOfConstructionList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "TypeOfConstructionList" + ".txt");
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
                            temp.setTitle(RowData[1]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertTypeOfConstruction(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_FloorCountList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_floor_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "FloorCountList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "FloorCountList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertFloorCount(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_CurrentStageOfConstructionList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_current_stage_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "CurrentStageOfConstruction");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "CurrentStageOfConstruction" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertCurrentStageOfConstruction(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_BrandUsedList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_brand_used_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "BrandUsedList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "BrandUsedList" + ".txt");
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
                        DataSet temp = new DataSet();
                        temp.setTitle(RowData[0]);
                        temp.setValue(RowData[1]);
                        mNewDatabaseForSiteLead.insertBrandUsed(temp);
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ConversionList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_conversion_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ConversionList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ConversionList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[1]);
                            mNewDatabaseForSiteLead.insertConversion(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ProductList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_select_product_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ProductList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ProductList" + ".txt");
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
                            ProductDataSet temp = new ProductDataSet();
                            temp.setName(RowData[0]);
                            temp.setConversionType(RowData[1]);
                            temp.setVisitType(RowData[2]);
                            mNewDatabaseForSiteLead.insertProduct(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_CounterType(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_counter_type_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "CounterType");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "CounterType" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertCounterType(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_CounterNameList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_counter_name_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "CounterNameList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "CounterNameList" + ".txt");
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
                            CounterNameDataSet temp = new CounterNameDataSet();
                            temp.setName(RowData[1]);
                            temp.setCode(RowData[0]);
                            temp.setType(RowData[2]);
                            mNewDatabaseForSiteLead.insertCounterName(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ReasonsForNonConversionList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_non_conversion_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ReasonsForNonConversionList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ReasonsForNonConversionList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertReasonsForNonConversion(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_PriorityList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_site_priority_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "PriorityList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "PriorityList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertPriority(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_WeatherShieldDemoList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_weather_shield_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "WeatherShieldDemoList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "WeatherShieldDemoList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertWeatherShieldDemo(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ApprovalStatusList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_approval_status_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ApprovalStatusList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ApprovalStatusList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertApprovalStatus(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ASMNameList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_asm_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "ASMNameList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ASMNameList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[1]);
                            mNewDatabaseForSiteLead.insertASMName(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_SiteStatusList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_site_status_site_lead.php";
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "SiteStatusList");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "SiteStatusList" + ".txt");
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
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            mNewDatabaseForSiteLead.insertSiteStatus(temp);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_ExistingSiteLeadList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_get_site_list_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
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
                        String[] RowData = (line + " ").split("\\^");
                        if (RowData.length == noColumn[0]) {
                            DataForUpload dataForUpload = new DataForUpload();
                            dataForUpload.setSite_transaction_id(RowData[1]);
                            dataForUpload.setSite_unique_id(RowData[2]);
                            dataForUpload.setSite_creation_date(RowData[24]);
                            dataForUpload.setSite_visit_date(RowData[3]);
                            dataForUpload.setEmployee_code(RowData[4]);
                            dataForUpload.setEmployee_name(RowData[5]);
                            dataForUpload.setZone(RowData[6]);
                            dataForUpload.setBranch(RowData[7]);
                            dataForUpload.setState(RowData[9]);
                            dataForUpload.setDistrict(RowData[8]);
                            dataForUpload.setLatitude(RowData[11]);
                            dataForUpload.setLongitude(RowData[10]);
                            dataForUpload.setCustomer_name(RowData[12]);
                            dataForUpload.setCustomer_contact_number(RowData[13]);
                            dataForUpload.setCustomer_full_address(RowData[14]);
                            dataForUpload.setIs_register_contractor(RowData[28]);
                            dataForUpload.setContractor_name(RowData[29]);
                            dataForUpload.setContractor_contact_number(RowData[31]);
                            dataForUpload.setIs_register_engineer(RowData[32]);
                            dataForUpload.setEngineer_name(RowData[33]);
                            dataForUpload.setEngineer_contact_number(RowData[35]);
                            dataForUpload.setMeeting_person(RowData[36]);
                            dataForUpload.setDecision_maker(RowData[37]);
                            dataForUpload.setSite_segment(RowData[15]);
                            dataForUpload.setVisit_type(RowData[16]);
                            dataForUpload.setProject_segment(RowData[17]);
                            dataForUpload.setType_of_construction(RowData[18]);
                            dataForUpload.setFloor_count(RowData[61]);
                            dataForUpload.setCurrent_stage_of_construction(RowData[38]);
                            dataForUpload.setBuilt_up_area(RowData[19]);
                            dataForUpload.setSite_potential(RowData[39]);
                            dataForUpload.setConsumed_till_date(RowData[40]);
                            dataForUpload.setBalance_potential(RowData[41]);
                            dataForUpload.setBalance_potential_manual(RowData[62]);
                            dataForUpload.setSite_category(RowData[42]);
                            dataForUpload.setBrand_used(RowData[43]);
                            dataForUpload.setPrice_per_bag(RowData[44]);
                            dataForUpload.setConversion(RowData[21]);
                            dataForUpload.setProduct_name(RowData[45]);
                            dataForUpload.setOrder_quantity(RowData[46]);
                            dataForUpload.setRequested_date_of_delivery(RowData[47]);
                            dataForUpload.setCounter_type(RowData[48]);
                            dataForUpload.setCounter_name(RowData[49]);
                            dataForUpload.setCounter_code(RowData[23]);
                            dataForUpload.setReason_for_non_conversion(RowData[50]);
                            dataForUpload.setSite_priority(RowData[22]);
                            dataForUpload.setWeather_shield_demo(RowData[51]);
                            dataForUpload.setApproval_status(RowData[52]);
                            dataForUpload.setDate_time(RowData[53]);
                            dataForUpload.setAsm_name(RowData[54]);
                            dataForUpload.setAsm_employee_id(RowData[55]);
                            dataForUpload.setActual_date_of_delivery(RowData[57]);
                            dataForUpload.setDelivery_remarks(RowData[58]);
                            dataForUpload.setReason_for_not_delivery(RowData[59]);
                            dataForUpload.setSite_status(RowData[60]);
                            dataForUpload.setRemarks(RowData[63]);

                            mNewDatabaseForSiteLead.insertSiteLead(dataForUpload, 1);
                        }
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }

    public void _DOWNLOAD_CompetitorQuantity() {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = prefs.edit();
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/get_competitor_qty.php?emp_code=" + Constants.employeeDetailObject.getEmpCode()+"&download_time="+prefs.getString("download_time","");

        Log.d("TAG", "_DOWNLOAD_ CompetitorQuantity: " +System.currentTimeMillis()+"  ||  "+ URL);
        new Thread(() -> {
            Download_txt(URL, "CompetitorQuantity");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "CompetitorQuantity" + ".txt");

            try {
                FileReader file = new FileReader(csvFile);
                BufferedReader buffer = new BufferedReader(file);
                String line;
                int i = 0;

                // Collect all rows first
                ArrayList<String[]> rowList = new ArrayList<>();
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1].trim());
                    } else {
                        String[] rowData = line.split("\\^");
                        if (noColumn[0] != -1 && rowData.length == noColumn[0]) {
                            rowList.add(rowData);
                            i++;
                        }
                    }
                }
                buffer.close();
                Log.d("TAG", "_DOWNLOAD_ CompetitorQuantity total rows: " + i);

                // Batch insert in a single transaction
                mNewDatabaseForSiteLead.insertCustomerCompetitorQuantityBatch(rowList);
                String dateTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                        .format(new Date());
                editor.putString("download_time", dateTime);
                editor.apply();
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ CompetitorQuantity error: " + e.getMessage());
            }
        }).start();
    }

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
                    String url = BaseUrl.baseUrl + "misreport/api_get_employee_detail_site_lead.php?emp_code=" + emp_code;
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
                JSONObject obj = new JSONObject(result);
                try {
                    mNewDatabaseForSiteLead.insertEmpDetails(emp_code, obj.getString("employee_name"), obj.getString("designation"), obj.getString("zone"));
                } catch (Exception ignored) {
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }
}
