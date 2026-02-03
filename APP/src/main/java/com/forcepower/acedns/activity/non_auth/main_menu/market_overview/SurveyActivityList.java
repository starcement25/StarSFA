package com.forcepower.acedns.activity.non_auth.main_menu.market_overview;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentManager;
import android.text.Editable;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.AUTH_GetOTP;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyHeader;
import com.forcepower.acedns.fragment.DialogMapFragment;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ListCheckStatusAdapter;
import com.forcepower.acedns.adapter.MallAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyTask;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.SurveyStatus;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Objects;

/**
 * An activity which will show layer List of particular Menu
 *
 * @author Sourav Das <souravd@coral.in>
 * @version 5.2.0
 */

@SuppressWarnings("unused")
public class SurveyActivityList extends AceDnsParentActivity {

    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;

    @SuppressLint("StaticFieldLeak")
    public static ListView mSurveyList = null;

    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoSurvey = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;

    public static String mMenuID = "";
    public static String mType = "";
    public static ProgressDialog mPrepareSurveyProgressDialog;
    @SuppressLint("StaticFieldLeak")
    static AceDnsDatabase mAceDnsDatabase;
    @SuppressLint("StaticFieldLeak")
    public static AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    static Handler mPrepareSurveyHandler;
    static Handler mPrepareDataSaveHandler;
    static String[] mTypeList;
    @SuppressLint("StaticFieldLeak")
    static Context mContext;
    public boolean isSurveyType = false;
    public GPSTracker gpsTracker;
    ArrayList<MallMaster> mMallMasterList;

    public static void _saveData() {
        String libraryStatus = Utils.checkLibraryConditions(mContext);
        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
            mButtonSubmit.setEnabled(false);
            if (Constants.surveyFormDetailsObj.getSurveyOTP().equalsIgnoreCase("yes")) {
                //_showDialogMAP();
                if (Constants.shouldAskForOTP) {
                    ShowOTPDialog(Constants.LIPLMOBILENO);
                } else {
                    SaveDatatoDatabase(1);
                }

            } else {

                SaveDatatoDatabase(1);
            }
        }
    }

    public static void _dataSend() {
        String libraryStatus = Utils.checkLibraryConditions(mContext);
        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
            mButtonSubmit.setEnabled(false);
            if (Constants.surveyFormDetailsObj.getSurveyOTP().equalsIgnoreCase("yes")) {
                if (Constants.shouldAskForOTP) {
                    ShowOTPDialog(Constants.LIPLMOBILENO);
                } else {
                    SaveDatatoDatabase(1);
                }

            } else {

                SaveDatatoDatabase(1);
            }
        }
    }

    public static void SaveDatatoDatabase(final int task) {
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Saving data to database.\n Please wait...");
        mPrepareSurveyProgressDialog.setCancelable(false);
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {

                SaveSurveyDataTODatabase();
                Message msg = mPrepareDataSaveHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareDataSaveHandler.sendMessage(msg);
            }
        }.start();
    }

    /**
     * This Function is used to save the <b>Survey Data</b> to <b>Local Database</b>.
     */
    @SuppressLint("SimpleDateFormat")
    public static void SaveSurveyDataTODatabase() {

        String timeStamp = "";
        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        if (Constants.selectedFsSurveyPublish != null) {
            mAceDnsTransactionDatabase.UpadateFsSurveyPublishStatus(Constants.selectedFsSurveyPublish.getFsSurveyId());
        }
        String transid = "SU" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        mAceDnsTransactionDatabase.INSERTtoSurveyOutput(timeStamp, "SU");
        if (Constants.selectedFsSurveyPublish != null) {
            mAceDnsTransactionDatabase.InsertSurveyHeader(transid, mType, mMenuID, Constants.selectedFsSurveyPublish.getMallId(), Constants.selectedFsSurveyPublish.getMallName(), Constants.selectedFsSurveyPublish.getBusinessName(), "", "", "", Constants.mSurveyRouteCode);
        } else {
            mAceDnsTransactionDatabase.InsertSurveyHeader(transid, mType, mMenuID, Constants.selectedMallMaster.getMallId(), Constants.selectedMallMaster.getMallName(), "", "", "", "", Constants.mSurveyRouteCode);
        }
        mAceDnsTransactionDatabase.insertToLocationTable("SU", timeStamp);

//		}
//		else
//		{
//			//ask user to enable gps
//			gpstracker.showSettingsAlertToChangeTimeZone();
//		}

    }

    private static void ShowOTPDialog(final String mobileno) {

        final Dialog mDialogOTPName = new Dialog(mContext, R.style.PauseDialog);
        mDialogOTPName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOTPName.setContentView(R.layout.dialog_otp_verification);
        mDialogOTPName.setCancelable(false);
        final EditText edvalue =  mDialogOTPName.findViewById(R.id.editTextOTP);
        edvalue.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                String otp = s.toString().trim();
                if (otp.length() == 4) {
                    if (Constants.mOTP != null && otp.matches(Constants.mOTP)) {
                        mDialogOTPName.cancel();
							/*getWindow().setSoftInputMode(
											WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);*/
                        String libraryStatus = Utils.checkLibraryConditions(mContext);
                        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                            mButtonSubmit.setEnabled(false);
                            SaveDatatoDatabase(1);
                        }
                    }
                }
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        Button submit =  mDialogOTPName.findViewById(R.id.btn_submit);
        submit.setOnClickListener(v -> {
            Constants.isRESENDOTPREQUEST = true;
            new AUTH_GetOTP(mContext).execute(mobileno);
        });

        Button cancel =  mDialogOTPName.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(v -> {
            mDialogOTPName.cancel();
            ShowAlertDialogForConfirmation();
        });

        mDialogOTPName.show();
    }

    public static void ShowAlertDialogForConfirmation() {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Information");
        AlertDG.setMessage("All information will be lost.\nDo you still want to exit?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> {
            //new TRANS_SubmitSurveyTask(mContext, true).execute();
            mAceDnsDatabase.DeleteSurveyTempOutData();
            //finish();
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                    | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        });
        AlertDG.setNegativeButton("No", (dialog, which) -> ShowOTPDialog(Constants.LIPLMOBILENO));
        AlertDG.setCancelable(false);
        AlertDG.create().show();
    }

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activty_survey_list);
        RegisterActivities.registerActivity(this);
        Constants.shouldAskForOTP = false;
        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
            mMenuID = getIntent().getStringExtra("SURVEYMENUID");
        }

        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            mType = getIntent().getStringExtra("SURVEYMADEAT");
        }

        if (!Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") &&
                Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            isSurveyType = true;
        }

        mContext = SurveyActivityList.this;

        gpsTracker = new GPSTracker(mContext);
        boolean LocationEnabled = gpsTracker.canGetLocation();
        if (!LocationEnabled) {
            //System.out.println("LOCATION NO");
            Toast.makeText(mContext, "Please enable your location service", Toast.LENGTH_SHORT).show();
        }
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        InitializeView();
        Constants.mFinalSurveyList = new ArrayList<>();
        Constants.mSurveyStatusList = new ArrayList<>();
        Constants.mNoOfCapture = 0;

        mAceDnsDatabase = new AceDnsDatabase(mContext);


        mPrepareSurveyHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivityList.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            ShowSurveyList();
                            break;
                        case 2:
                            if (mTypeList.length > 0) {
                                if (mTypeList.length == 1) {
                                    mType = mTypeList[0];
                                    PrepareSurveyData(3);
                                } else {
                                    ShowSurveyTypeListDialog();
                                }
                            } else {
                                Utils.showToast(mContext, "You have no type details");
                            }
                            break;

                        case 3:
                            ShowMallHighStreetListDialog();

                    }
                });
            }
        };

        mPrepareDataSaveHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivityList.this.runOnUiThread(() -> {
                    if (dojob == 1) {
                        mAceDnsDatabase.DeleteSurveyTempOutData();
                        new TRANS_SubmitSurveyTask(mContext, true, "SUBMIT").execute();
                    }
                });
            }
        };


        if (isSurveyType) {
            PrepareSurveyData(2);
        } else {
            PrepareSurveyData(1);
        }

        mButtonNoSurvey.setOnClickListener(v -> ShowNoSurveyAlertDialog());


        mButtonSubmit.setOnClickListener(v -> {
            if (CheckSurveyStatus()) {

                if (!TextUtils.isEmpty(Constants.userDetailsObj.getLocation_drag_drop()) && Constants.userDetailsObj.getLocation_drag_drop().equals("yes")) {
                    GPSTracker gpstracker = new GPSTracker(mContext);
                    boolean LocationEnabled1 = gpstracker.canGetLocation();
                    if (LocationEnabled1) {

                        ConnectionDetector cd = new ConnectionDetector(mContext);
                        if (cd.isConnectingToInternet()) {
                            if (isGoogleMapsInstalled()) {
                                new GPSTracker(SurveyActivityList.this);

                                Handler handler = new Handler();
                                handler.postDelayed(this::_showDialogMAP, 1000);

                            } else {
                                AlertDialog.Builder builder = new AlertDialog.Builder(SurveyActivityList.this);
                                builder.setMessage("Install Google Maps");
                                builder.setCancelable(false);
                                builder.setPositiveButton("Install", getGoogleMapsListener());
                                AlertDialog dialog = builder.create();
                                dialog.show();
                            }

                        } else {
                            Toast.makeText(mContext, "Please connect to the internet", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        gpstracker.showSettingsAlertToEnableLocation("Enable GPS");
                    }

                } else {
                    //String libraryStatus = Utils.checkLibraryConditions(SurveyActivityList.this);
                    //if (libraryStatus.equalsIgnoreCase("ALL OKK"))
                    //{
                    mButtonSubmit.setEnabled(false);
                    if (Constants.surveyFormDetailsObj.getSurveyOTP().equalsIgnoreCase("yes")) {

                        if (Constants.shouldAskForOTP) {
                            ShowOTPDialog(Constants.LIPLMOBILENO);
                        } else {
                            SaveDatatoDatabase(1);
                        }

                    } else {

                        SaveDatatoDatabase(1);
                    }
                    //}
                }

            } else {
                Toast.makeText(mContext, "Please complete the survey", Toast.LENGTH_SHORT).show();
            }
        });

        mButtonBack.setOnClickListener(v -> {
            mAceDnsDatabase.DeleteSurveyTempOutData();
            finish();
        });
    }

    public boolean isGoogleMapsInstalled() {
        try {
            ApplicationInfo info = getPackageManager().getApplicationInfo("com.google.android.apps.maps", 0);
            return true;
        } catch (PackageManager.NameNotFoundException e) {
            return false;
        }
    }

    public DialogInterface.OnClickListener getGoogleMapsListener() {
        return (dialog, which) -> {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.google.android.apps.maps"));
            startActivity(intent);

            //Finish the activity so they can't circumvent the check
            finish();
        };
    }

    private void _showDialogMAP() {
        FragmentManager fmOBJ = getSupportFragmentManager();
        DialogMapFragment dFragment = new DialogMapFragment();
        dFragment.setCancelable(false);
        dFragment.show(fmOBJ, "SURVEY_ACTIVITY_LIST");

    }

    /**
     * This Function is used to check the survey status of the every layer of the selected menu .
     *
     * @return true if all layer status is <b>DONE</b>
     */

    public boolean CheckSurveyStatus() {
        boolean isCheck = true;
        String status = "DONE";
        for (int count = 0; count < Constants.mSurveyStatusList.size(); count++) {
            String getstatus = Constants.mSurveyStatusList.get(count).getStatus();
            if (!getstatus.equalsIgnoreCase(status)) {
                isCheck = false;
                break;
            }
        }
        return isCheck;
    }

    public void ShowSurveyList() {
        ListCheckStatusAdapter adapter = new ListCheckStatusAdapter(this, R.layout.activity_check_list,
                Constants.mSurveyStatusList);

        mSurveyList.setAdapter(adapter);
        mSurveyList.setOnItemClickListener((parent, view, position, id) -> {
            String surveytype = Constants.mSurveyStatusList.get(position).getSurveyLayout();
            String status = Constants.mSurveyStatusList.get(position).getStatus();
            if (surveytype.equalsIgnoreCase("Capture Image") && status.equalsIgnoreCase("DONE")) {
                Toast.makeText(mContext, surveytype + " is not editable", Toast.LENGTH_SHORT).show();
            } else {
                Intent intent = new Intent(SurveyActivityList.this, SurveyActivity.class);
                intent.putExtra("SURVEY", surveytype);
                intent.putExtra("SURVEYMENUID", mMenuID);
                intent.putExtra("SURVEYMADEAT", mType);
                startActivity(intent);
            }
        });

        adapter.notifyDataSetChanged();
    }

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(SurveyActivityList.this) + "~" + Utils.getDBVersion(SurveyActivityList.this));
        mSurveyList =  findViewById(R.id.listsurvey);
        mButtonNoSurvey = findViewById(R.id.no_ordr);
        mButtonBack = findViewById(R.id.back);
        //mButtonBack.setVisibility(View.INVISIBLE);
        mButtonSubmit = findViewById(R.id.btn_submit);
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);
    }

    public void PrepareSurveyData(final int task) {
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {

                switch (task) {
                    case 1:
//                        int max = 0;
//                        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
//                            max = mAceDnsDatabase.GetLayoutName(mMenuID);
//                        } else {
//                            max = mAceDnsDatabase.GetLayoutName();
//                        }
                        for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
//						values[i]=Constants.mSurveyLayoutList[i];
                            SurveyStatus mStatus = new SurveyStatus();
                            String currentSurveyLayout = Constants.mSurveyLayoutList[i];
                            if (currentSurveyLayout.matches("Contact Information")) {
                                Constants.shouldAskForOTP = true;
                            }

                            mStatus.setSurveyLayout(currentSurveyLayout);
                            mStatus.setStatus("NOTDONE");
                            Constants.mSurveyStatusList.add(mStatus);
                        }
                        break;

                    case 2:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split(",");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;

                    case 3:
                        mMallMasterList = mAceDnsDatabase.GetMallMasterData(mType);
                        break;

                }
                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (!Constants.mSurveyStatusList.isEmpty()) {
            ShowSurveyList();
        }
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    /**
     * This Function is Called when a user don't want to survey of the selected menu.
     * If user press <b>yes</b> user can back to the SurveyMenuActivity.
     * If user press <b>no</b> user can stay on the SurveyActivityList activity.
     */
    public void ShowNoSurveyAlertDialog() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(SurveyActivityList.this);
        alertDialogBuilder
                .setMessage("All informatios will be erased.\nDo you want to exit?")
                .setCancelable(false)
                .setPositiveButton("Yes",
                        (dialog, id) -> {

                            mAceDnsDatabase.DeleteSurveyTempOutData();
                            if (Constants.selectedFsSurveyPublish != null) {
                                @SuppressLint("SimpleDateFormat") String timeStamps = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                String trans_id = "NSU" + Constants.employeeDetailObject.getEmpCode() + timeStamps;
                                mAceDnsTransactionDatabase.InsertSurveyHeader(trans_id, mType, mMenuID, Constants.selectedFsSurveyPublish.getMallId(), Constants.selectedFsSurveyPublish.getMallName(), Constants.selectedFsSurveyPublish.getBusinessName(), "", "", "", "");
                                mAceDnsTransactionDatabase.insertToLocationTable("NSU", timeStamps);
                                mAceDnsTransactionDatabase.UpadateFsSurveyPublishStatus(Constants.selectedFsSurveyPublish.getFsSurveyId());
                                new TRANS_SubmitSurveyHeader(mContext).execute();
                            } else {
                                finish();
                            }


                        })
                .setNegativeButton("No", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    public void onDestroy() {
        super.onDestroy();
        Constants.mOTP = null;
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyTypeListDialog() {

        final Dialog mDialogTypeName = new Dialog(mContext, R.style.PauseDialog);
        mDialogTypeName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogTypeName.setContentView(R.layout.select_from_list);
        mDialogTypeName.setCancelable(false);

        TextView title =  mDialogTypeName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList =  mDialogTypeName
                .findViewById(R.id.list);

        for (int count = 0; count < mTypeList.length; count++) {
            mTypeList[count] = mTypeList[count].toUpperCase();
        }

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this,
                R.layout.simple_list_child, R.id.list_details, mTypeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mDialogTypeName.cancel();
            PrepareSurveyData(3);
        });
        mDialogTypeName.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowMallHighStreetListDialog() {
        if (!mMallMasterList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(SurveyActivityList.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title =  mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList =  mMallHighStreetListDialog.findViewById(R.id.list);

            final MallAdapter malladapter = new MallAdapter(SurveyActivityList.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);


            EditText searchText =  mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1,
                                          int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                Constants.selectedMallMaster = malladapter.getItem(arg2);
                PrepareSurveyData(1);
            });

            Button cancel = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

}
