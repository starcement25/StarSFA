package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitSamplingTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ListCheckAdapter;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.ProductPromotionDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class SamplingActivity extends AceDnsParentActivity {

    public static ImageView mImageViewHeaderLogo = null;

    public static EditText mEditTextCustomerName = null;
    public static EditText mEditTextAddress = null;
    public static EditText mEditTextAddress2 = null;
    public static EditText mEditTextEmail = null;

    public static Button mButtonBack = null;
    public static Button mButtonSubmit = null;
    public static Button mButtonPinCode = null;
    public static Button mButtonOilTypeOne = null;
    public static Button mButtonOilTypeTwo = null;
    //public static Button mButtonSelectRoad	 	= null;
    public ProgressDialog mPrepareSamplingProgressDialog;
    String[] values;
    String[] OilList;
    String[] OilListTwo;
    String[] GenericOilList;
    String[] StreetNameList;
    String type = "";
    int maxx = 0;
    Handler mPrepareSamplingHandler;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    private String mCustomerName = "";
    private String mPinCode = "";
    private String mRoadName = "";
    private String mRoadNo = "";
    private String mBuildingNo = "";
    private String mApartmentNo = "";
    private String mEmailId = "";
    private String mOilName = "";
    private String mOil = "";
    private String mCompetitorName = "";
    private String mCompetitorOilName = "";
    private String mOilType = "";
    private String mSelectedOil = "";
    private boolean mboolCustomerName = false;
    private boolean mboolPincode = false;
    private boolean mboolAddress = false;
    private boolean mboolOil = false;
    private boolean mboolNewOil = false;
    private boolean mboolNewPin = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sampling);
        RegisterActivities.registerActivity(this);

        mContext = SamplingActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        InitializeView();

        if (Constants.EMAMIMSGRECEIPENT.length() <= 0) {
            startActivity(new Intent(SamplingActivity.this,
                    ActivityPhoneNo.class));
        }

        mPrepareSamplingHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSamplingProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SamplingActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                ShowPinCodeList();
                                break;
                            case 2:
                                break;
                            case 3:
                                if (StreetNameList.length > 0) {
                                    ShowStreetNameDialog();
                                } else {
                                    ShowActionLayout("Please provide route\\street  name", "", "Y", "", "STREET");
                                }
                            case 4:
                                ParseCompanyList(mCompetitorName);
                                if (mCompetitorName.contains(",")) {
                                    if (GenericOilList.length > 0) {
                                        if (GenericOilList.length == 1) {
                                            if (mOilType.equalsIgnoreCase("ONE")) {
                                                mCompetitorOilName = GenericOilList[0];
                                            } else {
                                                mCompetitorOilName += "#";
                                                mCompetitorOilName += GenericOilList[0];
                                            }

                                        } else {
                                            //ShowOilList();
                                            ShowSingleSelectOilList();
                                        }

                                    } else {
                                        if (mOilType.equalsIgnoreCase("ONE")) {
                                            mCompetitorOilName = "";
                                        } else {
                                            mCompetitorOilName += "#";
                                            mCompetitorOilName = "";
                                        }
                                    }
                                } else {
                                    if (mOilType.equalsIgnoreCase("ONE")) {
                                        mCompetitorOilName = mCompetitorName;
                                    } else {
                                        mCompetitorOilName += "#";
                                        mCompetitorOilName += mCompetitorName;
                                    }
                                }

                                break;
                        }
                    }
                });
            }
        };

        PrepareSamplingData(2);

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Constants.EMAMIMSGRECEIPENT = "";
                finish();
            }
        });

        mButtonOilTypeOne.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mButtonOilTypeOne.setEnabled(false);
                mButtonOilTypeTwo.setEnabled(true);
                mOilType = "ONE";
                //ShowOilList();
                ShowProductList();
            }
        });

        mButtonPinCode.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                PrepareSamplingData(1);
            }
        });

        mButtonOilTypeTwo.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mButtonOilTypeTwo.setEnabled(false);
                RemoveSelectedOil(mSelectedOil);
                mOilType = "TWO";
                //ShowActionLayout("Please provide oil name","","Y","","OIL");
                ShowProductList();
            }

        });
		
		/*mButtonSelectRoad.setOnClickListener(new View.OnClickListener() {			
			@Override
			public void onClick(View v) {
				if(mPinCode.length()>0){
					PrepareSamplingData(3);
				}else{
					Utils.showToast(mContext, "Please select pincode");
				}
			}
		});*/

        mButtonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (mEditTextCustomerName.getText().toString().trim().length() > 0) {
                    mCustomerName = mEditTextCustomerName.getText().toString();
                    mboolCustomerName = true;
                } else {
                    mCustomerName = "";
                    mboolCustomerName = false;
                }

                if (mEditTextEmail.getText().toString().length() > 0) {
                    mEmailId = mEditTextEmail.getText().toString();
                } else {
                    mEmailId = "";
                }

                if (mEditTextAddress.getText().toString().length() > 0) {
                    mRoadName = mEditTextAddress.getText().toString();
                    mboolAddress = true;
                } else {
                    mRoadName = "";
                    mboolAddress = false;
                }

                if (mEditTextAddress2.getText().toString().length() > 0) {
                    mRoadNo = mEditTextAddress2.getText().toString();
                } else {
                    mRoadNo = "";
                }

                if (true == mboolCustomerName && true == mboolPincode
                        && true == mboolAddress && true == mboolOil) {
                    if (SaveSamplingDataToDatabase(mCustomerName, mPinCode, mRoadName, mRoadNo,
                            mBuildingNo, mApartmentNo, mEmailId, mOilName, mCompetitorOilName) == true) {
                        mButtonSubmit.setEnabled(false);
                        getWindow()
                                .setSoftInputMode(
                                        WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        new TRANS_SubmitSamplingTask(mContext, true).execute();
                    }
                } else {
                    if (mboolCustomerName == false) {
                        Toast.makeText(mContext, "Please provide valid Customer Name", 1500).show();
                    }
                    if (mboolPincode == false) {
                        Toast.makeText(mContext, "Please provide valid PinCode", 1500).show();
                    }
                    if (mboolAddress == false) {
                        Toast.makeText(mContext, "Please provide valid address", 1500).show();
                    }
                    if (mboolOil == false) {
                        Toast.makeText(mContext, "Please select oil", 1500).show();
                    }
                }
            }
        });
    }

    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        mEditTextCustomerName = (EditText) findViewById(R.id.editTextCustomerName);
        mEditTextCustomerName.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextCustomerName.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS);


        mEditTextAddress = (EditText) findViewById(R.id.editTextAddress);
        mEditTextAddress.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextAddress.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS);

        mEditTextAddress2 = (EditText) findViewById(R.id.editTextAddress2);
        mEditTextAddress2.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextAddress2.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS);

        mEditTextEmail = (EditText) findViewById(R.id.editTextEmail);
        mEditTextEmail.setImeOptions(EditorInfo.IME_ACTION_DONE);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonSubmit = (Button) findViewById(R.id.buttonSubmit);

        mButtonPinCode = (Button) findViewById(R.id.buttonPincode);
        mButtonOilTypeOne = (Button) findViewById(R.id.buttonOilTypeOne);
        mButtonOilTypeTwo = (Button) findViewById(R.id.buttonOilTypeTwo);
        mButtonOilTypeTwo.setEnabled(false);
        //mButtonSelectRoad	= (Button) findViewById(R.id.buttonStreet);
    }


    public boolean SaveSamplingDataToDatabase(String
                                                      customername, String pincode, String roadname, String roadno,
                                              String buildingno, String apartmentno, String email, String oilname, String competitorName) {
        boolean isSucess = false;
        String prospectcode = "";
        String transactiotype = "PP";
        try {
            String timeStamp = "";
            timeStamp = Constants.dateString
                    + new SimpleDateFormat("HHmmss").format(Calendar
                    .getInstance().getTime());

            prospectcode = transactiotype + Constants.employeeDetailObject.getEmpCode()
                    + timeStamp;

            ProductPromotionDetails mProductPromotionDetails = new ProductPromotionDetails();
            mProductPromotionDetails.setProspectCode(prospectcode);
            mProductPromotionDetails.setProspectName(customername);
            mProductPromotionDetails.setPinCode(pincode);
            mProductPromotionDetails.setStreetName(roadname);
            mProductPromotionDetails.setStreetNo(roadno);
            mProductPromotionDetails.setBuildingNo(buildingno);
            mProductPromotionDetails.setApartmentNo(apartmentno);
            mProductPromotionDetails.setPhoneNo(Constants.EMAMIMSGRECEIPENT);
            mProductPromotionDetails.setEmailID(email);
            mProductPromotionDetails.setOilUsed(oilname);
            mProductPromotionDetails.setCompetitorName(competitorName);

            if (mboolNewOil == true) {
                mAceDnsDatabase.InsertToGenericOilMaster(mOilName);
                mboolNewOil = false;
            }

            if (mboolNewPin) {
                mAceDnsDatabase.InsertToPinCodeMaster(mPinCode);
                mboolNewPin = false;
            }
            mAceDnsTransactionDatabase.INSERTtoSampling(mProductPromotionDetails);
            mAceDnsTransactionDatabase.insertToLocationTable(transactiotype, timeStamp);
            isSucess = true;
        } catch (Exception ex) {
            isSucess = false;
        }
        return isSucess;
    }

    public void PrepareSamplingData(final int task) {
        mPrepareSamplingProgressDialog = new ProgressDialog(mContext);
        mPrepareSamplingProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSamplingProgressDialog.show();
        new Thread() {
            public void run() {

                switch (task) {
                    case 1:
                        int max = 0;
                        max = mAceDnsDatabase.GetPincode();
                        values = new String[max];
                        for (int i = 0; i < Constants.mPincodeList.length; i++) {
                            values[i] = Constants.mPincodeList[i];
                        }
                        break;
                    case 2:

                        maxx = mAceDnsDatabase.GetOilName();
                        OilList = new String[maxx];
                        for (int i = 0; i < Constants.mProductGrpouList.length; i++) {
                            OilList[i] = Constants.mProductGrpouList[i];
                        }
                        break;
                    case 3:
                        int maxxx = 0;
                        maxxx = mAceDnsDatabase.GetStreetName(mPinCode);
                        StreetNameList = new String[maxxx];
                        if (maxxx > 0) {
                            for (int i = 0; i < Constants.mStreetList.length; i++) {
                                StreetNameList[i] = Constants.mStreetList[i];
                            }
                        }
                        break;
                    case 4:
                        mCompetitorName = mAceDnsDatabase.GetCompetitorName(mOil);
                        break;

                }
                Message msg = mPrepareSamplingHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSamplingHandler.sendMessage(msg);
            }
        }.start();
    }

    public void ShowOilList() {
        if (mOilType.equalsIgnoreCase("ONE")) {
            mCompetitorOilName = "";
        }

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);
        grpDialog.setCancelable(false);

        TextView title = (TextView) grpDialog.findViewById(R.id.title);
        title.setText("Please select a oil");

        final ListView List = (ListView) grpDialog.findViewById(R.id.list);

        List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        final ListCheckAdapter adapter = new ListCheckAdapter(this,
                R.layout.activity_check_list, GenericOilList);
        List.setAdapter(adapter);

        RelativeLayout chkAllLayout = (RelativeLayout) grpDialog
                .findViewById(R.id.select_all_layout);
        chkAllLayout.setVisibility(View.VISIBLE);

        final CheckBox chkSelectAll = (CheckBox) grpDialog
                .findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, true);
                    }
                } else {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, false);
                    }
                }
            }
        });

        Button submit = (Button) grpDialog.findViewById(R.id.button1);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                final SparseBooleanArray checkedItems = List
                        .getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    if (mOilType.equalsIgnoreCase("ONE")) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                mCompetitorOilName += GenericOilList[position] + ",";
                            }
                        }
                        if (mCompetitorOilName.endsWith(",")) {
                            mCompetitorOilName = mCompetitorOilName.substring(0, mCompetitorOilName.length() - 1);
                        }

                    } else {
                        mCompetitorOilName += "#";
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                mCompetitorOilName += GenericOilList[position] + ",";
                            }
                        }
                        if (mCompetitorOilName.endsWith(",")) {
                            mCompetitorOilName = mCompetitorOilName.substring(0, mCompetitorOilName.length() - 1);
                        }
                    }
                    Log.i("Oil Name", mCompetitorOilName);
                    grpDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please select oil brand from the list");
                }
            }
        });
        grpDialog.show();
		

		/*List.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				grpDialog.cancel();
				mOilName = RoutePlanAdapter.getItem(position);
				if (mOilName.equalsIgnoreCase("Others")) {

				} else {
					mboolOil = true;
					mButtonOilSelection.setText(mOilName);
				}

			}
		});*/


    }

    public void ShowStreetNameDialog() {
        final Dialog mStreetDialog = new Dialog(SamplingActivity.this,
                R.style.PauseDialog);
        mStreetDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mStreetDialog.setContentView(R.layout.select_with_search);
        mStreetDialog.setCancelable(false);

        TextView title = (TextView) mStreetDialog.findViewById(R.id.title);
        title.setText("Please select a road name");
        ListView dialogList = (ListView) mStreetDialog
                .findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                R.layout.activity_listview, StreetNameList);
        dialogList.setAdapter(adapter);


        EditText searchText = (EditText) mStreetDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1,
                                      int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int position, long arg3) {
                mRoadName = adapter.getItem(position);
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mStreetDialog.cancel();
            }
        });

        Button cancel = (Button) mStreetDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mStreetDialog.show();
    }

    public void ShowActionLayout(String displayname, final String actiontype, final String mandatory,
                                 final String tablename, final String menu) {

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_ation);
        grpDialog.setCancelable(false);

        TextView headertitle = (TextView) grpDialog.findViewById(R.id.title);
        headertitle.setText(menu);

        TextView title = (TextView) grpDialog.findViewById(R.id.textView1);
        title.setText(displayname);

        final EditText mEditText = (EditText) grpDialog.findViewById(R.id.editText1);
        if (actiontype.equalsIgnoreCase("double")) {
            mEditText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        } else {
            mEditText.setImeOptions(EditorInfo.IME_ACTION_DONE);
            mEditText.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS);
        }
        Button mSelectButton = (Button) grpDialog.findViewById(R.id.button2);
        mSelectButton.setText("Select " + displayname);

        if (tablename.length() > 0) {
            mEditText.setVisibility(View.GONE);
        } else {
            mSelectButton.setVisibility(View.GONE);
        }

        mSelectButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

            }
        });

        Button submit = (Button) grpDialog.findViewById(R.id.button1);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (tablename.length() > 0) {
                    grpDialog.cancel();
                } else {
                    if (menu.equalsIgnoreCase("OIL")) {
                        if (mandatory.equalsIgnoreCase("Y")) {
                            String editval = mEditText.getText().toString();
                            if (editval.length() > 0) {
                                mOilName = editval;
                                if (CheckOilName(mOilName) == true) {
                                    getWindow()
                                            .setSoftInputMode(
                                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                    Toast.makeText(mContext, "This oil name already exist", Toast.LENGTH_LONG).show();
                                } else {
                                    getWindow()
                                            .setSoftInputMode(
                                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                    grpDialog.cancel();
                                    mboolOil = true;
                                    mboolNewOil = true;
                                    mButtonOilTypeTwo.setText(mOilName);
                                }
                            } else {
                                Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_LONG).show();
                            }
                        } else {
                            grpDialog.cancel();
                        }
                    }
                    if (menu.equalsIgnoreCase("STREET")) {
                        if (mandatory.equalsIgnoreCase("Y")) {
                            String editval = mEditText.getText().toString().trim();
                            if (editval.length() > 0) {
                                mRoadName = editval;
                                //mboolRoadName=true;
                                mAceDnsDatabase.InsertToStreetMaster(mRoadName, mPinCode);
                                //mButtonSelectRoad.setText(mRoadName);
                                grpDialog.cancel();
                            } else {
                                Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_LONG).show();
                            }
                        } else {
                            grpDialog.cancel();
                        }
                    }

                    if (menu.equalsIgnoreCase("PIN")) {
                        if (mandatory.equalsIgnoreCase("Y")) {
                            String editval = mEditText.getText().toString().trim();
                            if (editval.length() > 0) {
                                if (editval.length() == 6) {
                                    mPinCode = editval;
                                    if (CheckPincode(mPinCode)) {
                                        mEditText.setText("");
                                        Toast.makeText(mContext, "Pincode is already exist", Toast.LENGTH_LONG).show();
                                    } else {
                                        //mAceDnsDatabase.InsertToStreetMaster(mRoadName,mPinCode);
                                        mButtonPinCode.setText(mPinCode);
                                        mboolPincode = true;
                                        mboolNewPin = true;
                                        grpDialog.cancel();
                                    }
                                } else {
                                    Toast.makeText(mContext, "Please provide six digit pincode", Toast.LENGTH_LONG).show();
                                }
                            } else {
                                Toast.makeText(mContext, "Please provide valid pincode", Toast.LENGTH_LONG).show();
                            }
                        }
                    }
                }
            }
        });
        grpDialog.show();
    }


    public void ShowPinCodeList() {
        final Dialog mPincodeDialog = new Dialog(SamplingActivity.this,
                R.style.PauseDialog);
        mPincodeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mPincodeDialog.setContentView(R.layout.select_double_with_search);
        mPincodeDialog.setCancelable(false);

        TextView title = (TextView) mPincodeDialog.findViewById(R.id.title);
        title.setText("Please select a pincode");
        ListView dialogList = (ListView) mPincodeDialog
                .findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                R.layout.activity_listview, values);
        dialogList.setAdapter(adapter);


        EditText searchText = (EditText) mPincodeDialog.findViewById(R.id.autoCompleteTextView1);
        //searchText.setInputType(InputType.TYPE_NUMBER_FLAG_SIGNED);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1,
                                      int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int position, long arg3) {
                mPinCode = adapter.getItem(position);
                if (mPinCode.equalsIgnoreCase("Others")) {
                    mPincodeDialog.cancel();
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    ShowActionLayout("Please provide pincode ", "double", "Y", "", "PIN");

                } else {
                    mButtonPinCode.setText(mPinCode);
                    mboolPincode = true;
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    mPincodeDialog.cancel();
                }
            }
        });

        Button cancel = (Button) mPincodeDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mPincodeDialog.show();
    }

    private boolean CheckOilName(String oilname) {
        boolean isPresent = false;
        for (int count = 0; count < OilList.length; count++) {
            if (oilname.equalsIgnoreCase(OilList[count])) {
                isPresent = true;
            }
        }
        return isPresent;
    }

    private boolean CheckPincode(String pincode) {
        boolean isPresent = false;
        for (int count = 0; count < values.length; count++) {
            if (pincode.equalsIgnoreCase(values[count])) {
                isPresent = true;
                break;
            }
        }
        return isPresent;
    }

    public void ShowSingleSelectOilList() {

        final Dialog mCompetitorDialog = new Dialog(SamplingActivity.this,
                R.style.PauseDialog);
        mCompetitorDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mCompetitorDialog.setContentView(R.layout.select_with_search);
        mCompetitorDialog.setCancelable(false);

        TextView title = (TextView) mCompetitorDialog.findViewById(R.id.title);
        title.setText("Please select a product");
        ListView dialogList = (ListView) mCompetitorDialog
                .findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                R.layout.activity_listview, GenericOilList);

        dialogList.setAdapter(adapter);


        EditText searchText = (EditText) mCompetitorDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1,
                                      int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int position, long arg3) {

                if (mOilType.equalsIgnoreCase("ONE")) {
                    mCompetitorOilName += GenericOilList[position];
                } else {
                    mCompetitorOilName += "#";
                    mCompetitorOilName += GenericOilList[position];
                }
                mCompetitorDialog.cancel();
				/*if(mOilType.equalsIgnoreCase("ONE")){
					mOilName=RoutePlanAdapter.getItem(position);
					mSelectedOil=mOilName;
					mboolOil = true;
					mButtonOilTypeOne.setText(mOilName);
					mOil=mOilName;
					PrepareSamplingData(4);
					mCompetitorDialog.cancel();	
				}else{
					mOilName+=",";
					mOilName+=RoutePlanAdapter.getItem(position);
					mOil=RoutePlanAdapter.getItem(position);
					mboolOil = true;
					mButtonOilTypeTwo.setText(RoutePlanAdapter.getItem(position));
					PrepareSamplingData(4);
					mCompetitorDialog.cancel();	
				}*/
            }
        });

        Button cancel = (Button) mCompetitorDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mCompetitorDialog.show();

    }

    public void ShowProductList() {
        final Dialog mCompetitorDialog = new Dialog(SamplingActivity.this,
                R.style.PauseDialog);
        mCompetitorDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mCompetitorDialog.setContentView(R.layout.select_with_search);
        mCompetitorDialog.setCancelable(false);

        TextView title = (TextView) mCompetitorDialog.findViewById(R.id.title);
        title.setText("Please select a product");
        ListView dialogList = (ListView) mCompetitorDialog
                .findViewById(R.id.list);

        final ArrayAdapter<String> adapter;
        if (mOilType.equalsIgnoreCase("ONE")) {
            adapter = new ArrayAdapter<String>(this,
                    R.layout.activity_listview, OilList);
        } else {
            adapter = new ArrayAdapter<String>(this,
                    R.layout.activity_listview, OilListTwo);
        }

        dialogList.setAdapter(adapter);


        EditText searchText = (EditText) mCompetitorDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1,
                                      int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int position, long arg3) {

                if (mOilType.equalsIgnoreCase("ONE")) {
                    mOilName = adapter.getItem(position);
                    mSelectedOil = mOilName;
                    mboolOil = true;
                    mButtonOilTypeOne.setText(mOilName);
                    mOil = mOilName;
                    PrepareSamplingData(4);
                    mCompetitorDialog.cancel();
                } else {
                    mOilName += ",";
                    mOilName += adapter.getItem(position);
                    mOil = adapter.getItem(position);
                    mboolOil = true;
                    mButtonOilTypeTwo.setText(adapter.getItem(position));
                    PrepareSamplingData(4);
                    mCompetitorDialog.cancel();
                }
            }
        });

        Button cancel = (Button) mCompetitorDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mCompetitorDialog.show();

    }

    public void ParseCompanyList(String name) {
        Constants.selectedFeedBackList = new ArrayList<MarketFeedback>();
        if (name.contains(",")) {
            String[] RowData = name.split("\\,");
            if (RowData.length > 0) {
                GenericOilList = new String[RowData.length];
                for (int count = 0; count < RowData.length; count++) {
                    GenericOilList[count] = RowData[count];
                }
            }
        }
    }

    public void RemoveSelectedOil(String selectedoilname) {
        OilListTwo = new String[maxx - 1];
        int i = 0;
        for (int count = 0; count < OilList.length; count++) {
            if (!selectedoilname.equalsIgnoreCase(OilList[count])) {
                OilListTwo[i] = OilList[count];
                i++;
            }
        }

    }


}
