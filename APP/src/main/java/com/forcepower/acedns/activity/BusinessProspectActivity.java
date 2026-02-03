package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.TRANS_BusinessProspectCustomizeTransactionTask;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.DrCategoryAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;

import static android.view.View.GONE;

import androidx.annotation.NonNull;

public class BusinessProspectActivity extends AceDnsParentActivity {
    ImageView imgLogo;
    Button btnBack, btnNext, buttonDrCategory;
    EditText edName, edAddress, edePin, edArea, edPhone;
    String name = "", address = "", pin = "", phone = "",
            custType = "retailer", prospectType = "";
    boolean boolName, boolAddress, boolPin, boolArea, boolPhone;
    LinearLayout prospectLayout;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase transDataHelperObj;
    Context mContext;
    Dialog routePlanListDialog, routeDialog;
    String selectRouteCode = "", selectRouteName = "";
    boolean isNewRoute = false;
    RadioGroup radioGroupProspect;
    RadioButton radioBtnPros;
    RadioButton radioBtnCustomer, radioBtnMechanic;
    ProgressDialog progressDialog;
    Handler saveHandler;
    String remarks = "";
    String currentDate, lastStr = "";
    Dialog prospectcustomerListDialog;
    String[] values;
    ArrayList<CustomerDetails> tempCustomerList;
    ArrayList<CustomerDetails> customerList;
    CustomerAdapter adapterCust;

    String referringDealerName = "", referringDealerArea = "", referringDealerPhone = "", referringDealertag = "", referredPersonName = "", referredPersoPrefession = "",
            referredPersoPhone = "", referredPersoEmail = "", referredPersonFirm = "", referredPersonDistrict = "", referredPersonBlock = "", referredPersonRoute = "",
            chosenDealerName = "", chosenDealerCode = "";
    ArrayList<String> drCategoryList;


    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_prospect);
        RegisterActivities.registerActivity(this);
        mContext = BusinessProspectActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);

        currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);

        initView();

        saveHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("ProspectJobDone")) {
                    progressDialog.cancel();
                    BusinessProspectActivity.this.runOnUiThread(() -> {
                    });
                }
            }
        };
        if (Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("customized")) {
            showReferringDealerReferredPersonDetailsInputDialog();
        } else {
            ShowProspectTypeSelectionDialog();
        }
    }

    private void VerticalRouteCustomerSelectionProcess() {
        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
            int max;
            max = mAceDnsDatabase.GetVerticalValue();
            values = new String[max];
            System.arraycopy(Constants.mVerticalValueList, 0, values, 0, Constants.mVerticalValueList.length);
            if (values.length > 0) {
                if (values.length > 1) {
                    ShowVericalValueList();
                } else {
                    Constants.mVerticalValue = values[0];
                    customerRouteSelectionProcess();
                }
            }
        } else {
            customerRouteSelectionProcess();
        }
    }

    private void customerRouteSelectionProcess() {
        if (!Constants.mBusinessProspectType.matches("existing")) {
            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                ArrayList<RoutePlanMasterDetails> todayPlanList = transDataHelperObj.getPlanForToday(currentDate);
                showRoutePlanListDialog(todayPlanList);
            } else {
                showRouteListDialog();
            }
        }
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void initView() {
        imgLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        edName = findViewById(R.id.ed_name);
        edName.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        edAddress = findViewById(R.id.ed_address);
        edAddress.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        edePin = findViewById(R.id.ed_pin);
        edePin.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        edArea = findViewById(R.id.ed_area);
        edArea.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        edPhone = findViewById(R.id.ed_phone);
        edPhone.setImeOptions(EditorInfo.IME_ACTION_DONE);

        btnBack = findViewById(R.id.back);
        btnNext = findViewById(R.id.btn_next);
        buttonDrCategory = findViewById(R.id.buttonTag);
        btnBack.setOnClickListener(this);
        btnNext.setOnClickListener(this);
        buttonDrCategory.setOnClickListener(this);

        prospectLayout = findViewById(R.id.prospect_layout);
        radioGroupProspect = findViewById(R.id.radioGroup101);
        radioBtnCustomer = findViewById(R.id.radio_1);
        radioBtnMechanic = findViewById(R.id.radio_2);

        if (Constants.userDetailsObj.getMultipleProspect().equalsIgnoreCase("yes")) {
            prospectLayout.setVisibility(View.VISIBLE);
            String[] valArray = Constants.userDetailsObj.getMultipleProspectValue().split(",");
            radioBtnCustomer.setText(valArray[0]);
            radioBtnMechanic.setText(valArray[1]);
        } else {
            prospectLayout.setVisibility(View.GONE);
        }

        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
            LinearLayout ll = findViewById(R.id.category_of_store_layout);
            ll.setVisibility(View.VISIBLE);
        }

        Constants.businessProspectCheckIn = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            finish();
        } else if (v == btnNext) {
            if (!edName.getText().toString().isEmpty()) {
                name = getFormatString(edName.getText().toString());
                boolName = true;
            } else {
                boolName = false;
            }

            if (!edAddress.getText().toString().isEmpty()) {
                address = edAddress.getText().toString().toUpperCase();
                boolAddress = true;
            } else {
                boolAddress = false;
            }

            if (edePin.getText().toString().length() == 6) {
                pin = edePin.getText().toString();
                boolPin = true;
            } else {
                boolPin = false;
            }

            boolArea = !selectRouteCode.isEmpty();

            String phNumberInPut = edPhone.getText().toString();
            if (!phNumberInPut.isEmpty()) {
                if (Utils.isValidIndianMobile(phNumberInPut)) {
                    phone = phNumberInPut;
                    boolPhone = true;
                } else {
                    boolPhone = false;
                }
            } else {
                if (Constants.userDetailsObj.getbusiness_prospect_phone_mandatory().equalsIgnoreCase("yes")) {
                    if (Utils.isValidIndianMobile(phNumberInPut)) {
                        phone = phNumberInPut;
                        boolPhone = true;
                    } else {
                        boolPhone = false;
                    }
                } else {
                    boolPhone = true;
                }
            }
            int selected = radioGroupProspect.getCheckedRadioButtonId();
            radioBtnPros = findViewById(selected);
            prospectType = radioBtnPros.getText().toString();

            if (boolName && boolAddress && boolPin && boolArea && boolPhone) {

                if (Constants.menuDetailsObj.getDoctor_visit().matches("yes")) {
                    if (buttonDrCategory.getText().toString().matches("Select Dr. Category")) {
                        Utils.showToast(BusinessProspectActivity.this, "Please provide valid Dr. Category");
                    } else {
                        if (Constants.productDetailsObj.getProductBusinessProspect().equalsIgnoreCase("yes")) {
                            Intent intent = new Intent(BusinessProspectActivity.this, BusinessProspectConfirmationActivity.class);
                            intent.putExtra("NAME", name);
                            intent.putExtra("DRCAT", buttonDrCategory.getText().toString());
                            intent.putExtra("ADDRESS", address);
                            intent.putExtra("PIN", pin);
                            intent.putExtra("ROUTE CODE", selectRouteCode);
                            intent.putExtra("ROUTE NAME", selectRouteName.toUpperCase());
                            intent.putExtra("PHONE", phone);
                            intent.putExtra("NEW ROUTE", isNewRoute);
                            intent.putExtra("CUST TYPE", custType.equalsIgnoreCase("Distributor") ? "D" : "R");
                            intent.putExtra("PROS TYPE", prospectType);
                            startActivity(intent);
                        } else {
                            ShowRemarksDialog();

                        }
                    }

                } else {
                    if (Constants.productDetailsObj.getProductBusinessProspect().equalsIgnoreCase("yes")) {
                        Intent intent = new Intent(BusinessProspectActivity.this, BusinessProspectConfirmationActivity.class);
                        intent.putExtra("NAME", name);
                        intent.putExtra("ADDRESS", address);
                        intent.putExtra("PIN", pin);
                        intent.putExtra("ROUTE CODE", selectRouteCode);
                        intent.putExtra("ROUTE NAME", selectRouteName.toUpperCase());
                        intent.putExtra("PHONE", phone);
                        intent.putExtra("NEW ROUTE", isNewRoute);
                        intent.putExtra("CUST TYPE", custType.equalsIgnoreCase("Distributor") ? "D" : "R");
                        intent.putExtra("PROS TYPE", prospectType);
                        startActivity(intent);
                    } else {
                        ShowRemarksDialog();
                    }
                }
            } else {
                if (!boolName) {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide a valid Name");
                }
                if (!boolAddress) {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide a valid Address");
                }
                if (!boolPin) {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide a 6 digit pin");
                }
                if (!boolArea) {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide a valid Area");
                }
                if (!boolPhone) {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide a 10 digit Phone Number");
                } else {
                    Utils.showToast(BusinessProspectActivity.this, "Please provide valid inputs");
                }
            }
        } else if (v == buttonDrCategory) {
            ShowChooseCategoryDialog();
        }
    }

    public String getFormatString(String unformatString) {
        String formatString;
        StringBuilder res = new StringBuilder();

        String[] strArr = unformatString.split(" ");
        for (String str : strArr) {
            char[] stringArray = str.trim().toCharArray();
            for (int ii = 0; ii < stringArray.length; ii++) {
                if (ii == 0) {
                    stringArray[ii] = Character.toUpperCase(stringArray[ii]);
                } else {
                    stringArray[ii] = Character.toLowerCase(stringArray[ii]);
                }
            }
            str = new String(stringArray);
            res.append(str).append(" ");
        }
        formatString = res.toString().trim();
        return formatString;
    }

    @SuppressLint("SetTextI18n")
    public void showRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> todayList) {
        RoutePlanMasterDetails detailsObj = new RoutePlanMasterDetails();
        detailsObj.setRoutecode("Other");
        detailsObj.setRouteName("Other");
        todayList.add(detailsObj);
        routePlanListDialog = new Dialog(BusinessProspectActivity.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routePlanListDialog.findViewById(R.id.list);
        RoutePlanTransAdapter adapter1 = new RoutePlanTransAdapter(BusinessProspectActivity.this, R.layout.route_list_child, todayList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routePlanListDialog.cancel();
            RoutePlanMasterDetails detailsObj1 = todayList.get(arg2);
            bussinessArea(detailsObj1.getRoutecode(), detailsObj1.getRouteName());
        });
        Button cancel = routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
        Button create_route = routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        create_route.setOnClickListener(arg0 -> {
            showCreateRouteDialog();
            routePlanListDialog.cancel();
        });
        routePlanListDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showRouteListDialog() {
        final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListBusinessProspect();

        if (routeList.size() == 1) {
            RouteDetails detailsObj = routeList.get(0);
            bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (routeList.size() > 1) {
            routeDialog = new Dialog(BusinessProspectActivity.this, R.style.PauseDialog);
            routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routeDialog.setContentView(R.layout.select_from_list);
            routeDialog.setCancelable(false);
            TextView title = routeDialog.findViewById(R.id.title);
            title.setText("Please select a Route");
            ListView dialogList = routeDialog.findViewById(R.id.list);
            RouteAdapter adapter1 = new RouteAdapter(BusinessProspectActivity.this, R.layout.route_list_child, routeList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                routeDialog.cancel();
                RouteDetails detailsObj = routeList.get(arg2);
                String routeName = detailsObj.getRouteName();
                bussinessArea(detailsObj.getRouteCode(), routeName);

            });

            Button cancel = routeDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> routeDialog.cancel());

            Button create_route = routeDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(arg0 -> {
                showCreateRouteDialog();
                routeDialog.cancel();
            });

            routeDialog.show();
        } else {
            Utils.showToast(BusinessProspectActivity.this, "There is no predefined route");
        }
    }

    public void bussinessArea(String routecode, String routename) {
        selectRouteCode = routecode;
        selectRouteName = routename;
        edArea.setKeyListener(null);
        edArea.setEnabled(false);
        if (selectRouteName.matches("Other")) {
            ProvideRouteNameDialog();
        } else {
            edArea.setText(routename);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowRemarksDialog() {
        final Dialog remarksDialog = new Dialog(mContext);
        remarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        remarksDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = remarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = remarksDialog.findViewById(R.id.ed_input);
        final Button submit = remarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            submit.setEnabled(false);
            remarks = edInst.getText().toString();
            if (!remarks.isEmpty()) {
                saveProspectData();
                remarksDialog.cancel();
            } else {
                Utils.showToast(BusinessProspectActivity.this, "Please provide remarks");
            }
        });
        remarksDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ProvideRouteNameDialog() {
        final Dialog remarksDialog = new Dialog(mContext);
        remarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        remarksDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = remarksDialog.findViewById(R.id.title);
        title.setText("Provide Route Name");
        final EditText edInst = remarksDialog.findViewById(R.id.ed_input);
        final Button submit = remarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            selectRouteName = edInst.getText().toString();
            if (!selectRouteName.isEmpty()) {
                selectRouteCode = selectRouteName;
                edArea.setText(selectRouteCode);
                remarksDialog.cancel();
            } else {
                Utils.showToast(BusinessProspectActivity.this, "Please provide route name");
            }
        });
        remarksDialog.show();
    }

    public void saveProspectData() {
        progressDialog = new ProgressDialog(mContext);
        progressDialog.setMessage("Saving Data.Please wait..");
        progressDialog.show();
        new Thread() {
            public void run() {
                if (isNewRoute) {
                    RouteDetails rootobj = new RouteDetails();
                    rootobj.setRouteCode(selectRouteCode);
                    rootobj.setRouteName(selectRouteName);
                    ArrayList<RouteDetails> routeList = new ArrayList<>();
                    routeList.add(rootobj);
                    mAceDnsDatabase.insertToRouteMaster(routeList);
                }
                Message msgObj = saveHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "ProspectJobDone");
                msgObj.setData(b);
                saveHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showCreateRouteDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = instructionDialog.findViewById(R.id.title);
        title.setText("Provide the name of the new Route.");
        final EditText edInst = instructionDialog.findViewById(R.id.ed_input);
        final Button submit = instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String name = "";
            submit.setEnabled(false);
            name = edInst.getText().toString();
            if (!name.isEmpty()) {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                selectRouteCode = "NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                selectRouteName = name.toUpperCase();
                instructionDialog.cancel();
                edArea.setText(selectRouteName);
                edArea.setEnabled(false);
                isNewRoute = true;
            } else {
                Utils.showToast(mContext, "Name cannot be left blank");
            }
        });
        instructionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowVericalValueList() {
        final Dialog mVerticalDialog = new Dialog(BusinessProspectActivity.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a vertical");
        ListView dialogList = mVerticalDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, values);
        dialogList.setAdapter(adapter);

        EditText searchText = mVerticalDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            Constants.mVerticalValue = adapter.getItem(position);
            mVerticalDialog.cancel();
            customerRouteSelectionProcess();
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mVerticalDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowProspectTypeSelectionDialog() {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.uom_dialog);
        TextView txtMsg = payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Please select a Type");

        RadioButton uom1RadioButton = payTypeDialog.findViewById(R.id.radio0);
        uom1RadioButton.setText("NEW");

        RadioButton uom2RadioButton = payTypeDialog.findViewById(R.id.radio1);
        uom2RadioButton.setText("EXISTING");

        RadioGroup payTypeOption = payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
            int radioButtonID = group.getCheckedRadioButtonId();
            View radioButton = group.findViewById(radioButtonID);
            int selectedRadio = group.indexOfChild(radioButton);

            if (selectedRadio == 0) {
                payTypeDialog.cancel();
                Constants.mBusinessProspectType = "new";
                VerticalRouteCustomerSelectionProcess();
            } else {
                Constants.mBusinessProspectType = "existing";
                customerList = mAceDnsDatabase.getProspectCustomerList();
                if (customerList.size() == 1) {
                    payTypeDialog.cancel();
                    CustomerDetails currentObj = customerList.get(0);
                    setExisitingProspectDataInEditTexts(currentObj);
                    VerticalRouteCustomerSelectionProcess();
                } else if (!customerList.isEmpty()) {
                    payTypeDialog.cancel();
                    tempCustomerList = new ArrayList<>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(BusinessProspectActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseProspectCustomerDialog();
                    VerticalRouteCustomerSelectionProcess();
                } else {
                    Toast.makeText(mContext, "No existing prospect found. Please create a new one", Toast.LENGTH_LONG).show();
                }
            }
        });
        Button cancel = payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.GONE);
        payTypeDialog.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showReferringDealerReferredPersonDetailsInputDialog() {
        referringDealerName = "";
        referringDealerArea = "";
        referringDealerPhone = "";
        referringDealertag = "";
        referredPersonName = "";
        referredPersoPrefession = "";
        referredPersoPhone = "";
        referredPersoEmail = "";
        referredPersonFirm = "";
        referredPersonDistrict = "";
        referredPersonBlock = "";
        referredPersonRoute = "";
        chosenDealerName = "";
        chosenDealerCode = "";
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.layout_referred_person_business_prospect_info);
        mDetailsDialog.setCancelable(false);

        TextView tv_referring_dealer_tag = mDetailsDialog.findViewById(R.id.tv_referring_dealer_tag);
        tv_referring_dealer_tag.setOnClickListener(v -> {
        });
        TextView tv_referred_person_profession = mDetailsDialog.findViewById(R.id.tv_referred_person_profession);
        tv_referred_person_profession.setOnClickListener(v -> openPopUpWindow("profession", tv_referred_person_profession));
        TextView tv_referred_person_dealer = mDetailsDialog.findViewById(R.id.tv_referred_person_dealer);
        tv_referred_person_dealer.setOnClickListener(v -> openPopUpWindow("dealer", tv_referred_person_dealer));
        TextView tv_referred_person_district = mDetailsDialog.findViewById(R.id.tv_referred_person_district);
        tv_referred_person_district.setOnClickListener(v -> {
        });
        TextView tv_referred_person_route = mDetailsDialog.findViewById(R.id.tv_referred_person_route);
        tv_referred_person_route.setOnClickListener(v -> {
        });
        TextView tv_referred_person_block = mDetailsDialog.findViewById(R.id.tv_referred_person_block);
        tv_referred_person_block.setOnClickListener(v -> {
        });

        EditText referringDealerNameTextView = mDetailsDialog.findViewById(R.id.referringDealerNameTextView);
        EditText referringDealerAreaTextView = mDetailsDialog.findViewById(R.id.referringDealerAreaTextView);
        EditText referringDealerPhoneTextView = mDetailsDialog.findViewById(R.id.referringDealerPhoneTextView);
        EditText referredPersonNameTextView = mDetailsDialog.findViewById(R.id.referredPersonNameTextView);
        EditText referredPersonPhoneTextView = mDetailsDialog.findViewById(R.id.referredPersonPhoneTextView);
        EditText referredPersonEmailTextView = mDetailsDialog.findViewById(R.id.referredPersonEmailTextView);
        EditText referredPersonNameOfFirmTextView = mDetailsDialog.findViewById(R.id.referredPersonNameOfFirmTextView);

        referringDealerNameTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referringDealerName = referringDealerNameTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referringDealerAreaTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referringDealerArea = referringDealerAreaTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referringDealerPhoneTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referringDealerPhone = referringDealerPhoneTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referredPersonNameTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referredPersonName = referredPersonNameTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referredPersonPhoneTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referredPersoPhone = referredPersonPhoneTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referredPersonEmailTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referredPersoEmail = referredPersonEmailTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referredPersonNameOfFirmTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referredPersonFirm = referredPersonNameOfFirmTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
        referringDealerNameTextView.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                referringDealerName = referringDealerNameTextView.getText().toString();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view -> {
            mDetailsDialog.dismiss();
            finish();
        });
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(view -> {
            new GPSTracker(mContext);
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            String newProspectPrefix = "B";
            transDataHelperObj.insertToLocationTable(newProspectPrefix, timeStamp);
            transDataHelperObj.insertToProspectDetails(timeStamp, newProspectPrefix, referringDealerArea, referringDealerPhone, referringDealertag, referredPersonName, referredPersoPrefession, referredPersoPhone, referredPersoEmail, referredPersonFirm, referredPersonDistrict, referredPersonBlock, referredPersonRoute, chosenDealerCode);
            mDetailsDialog.dismiss();
            ArrayList<Location> unUploadedTransaction = transDataHelperObj.getUnuploadedTransaction("business_prospect_customize", "");
            if (!unUploadedTransaction.isEmpty()) {
                new TRANS_BusinessProspectCustomizeTransactionTask(mContext, unUploadedTransaction).execute();
            }

            finish();
        });
        textViewTitleName.setText("Please Provide inputs");
        mDetailsDialog.show();
    }

    @SuppressLint("InflateParams")
    public void openPopUpWindow(String inputType, final TextView textView) {
        try {
            ArrayList<CustomerDetails> dealers = new ArrayList<>();
            final PopupWindow popup = new PopupWindow(this);
            View layout = getLayoutInflater().inflate(R.layout.list_item_dialog, null);
            popup.setContentView(layout);

            // Set content width and height
            popup.setHeight(WindowManager.LayoutParams.WRAP_CONTENT);
            popup.setWidth(textView.getWidth());

            // Closes the popup window when touch outside of it - when looses focus
            popup.setOutsideTouchable(true);
            popup.setFocusable(true);
            popup.setBackgroundDrawable(new BitmapDrawable());

            popup.showAsDropDown(textView, 0, 0);
            final ArrayList<String> spinnerArray = new ArrayList<>();
            if (inputType.equalsIgnoreCase("profession")) {
                spinnerArray.add("Dealer");
                spinnerArray.add("Sub-dealer");
                spinnerArray.add("Mason");
                spinnerArray.add("Engineer");
                spinnerArray.add("Customer");
                spinnerArray.add("IHB");
                spinnerArray.add("Contractor");
            } else {
                dealers = mAceDnsDatabase.getCustomerListByCustomerTypeDealer();
                for (int i = 0; i < dealers.size(); i++) {
                    spinnerArray.add(dealers.get(i).getCustomerName());
                }
            }

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_dropdown_item_1line, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            ListView listView = layout.findViewById(R.id.lvPopup);
            listView.setAdapter(spinnerArrayAdapter);
            ArrayList<CustomerDetails> finalDealers = dealers;
            listView.setOnItemClickListener((adapterView, view, i, l) -> {
                if (inputType.equalsIgnoreCase("profession")) {
                    referredPersoPrefession = spinnerArray.get(i);
                    textView.setText(referredPersoPrefession);
                } else {
                    chosenDealerName = finalDealers.get(i).getCustomerName();
                    chosenDealerCode = finalDealers.get(i).getCustomerCode();
                    textView.setText(chosenDealerName);
                }
                popup.dismiss();
            });
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showChooseProspectCustomerDialog() {
        prospectcustomerListDialog = new Dialog(BusinessProspectActivity.this, R.style.PauseDialog);
        prospectcustomerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        prospectcustomerListDialog.setContentView(R.layout.choose_customer_search);
        prospectcustomerListDialog.setCancelable(false);
        TextView title = prospectcustomerListDialog.findViewById(R.id.title);
        title.setText("Please select a Prospect");
        EditText searchText = prospectcustomerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                String str = s.toString();
                if (lastStr.length() > str.length()) {
                    reInitialiseCustomerList();
                }
                lastStr = str;
                filterCustomerArray(str.length(), str);
                adapterCust.notifyDataSetChanged();
                System.out.println("String::::::::" + str);
            }
        });

        ListView dialogList = prospectcustomerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.isSelectCustomer = true;
            CustomerDetails currentObj = tempCustomerList.get(arg2);
            setExisitingProspectDataInEditTexts(currentObj);
            prospectcustomerListDialog.cancel();
        });

        Button addCustomer = prospectcustomerListDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        prospectcustomerListDialog.show();
    }

    private void setExisitingProspectDataInEditTexts(CustomerDetails currentObj) {
        Constants.mBusinessProspectCustomerCode = currentObj.getCustomerCode();
        Constants.mBusinessProspectTaggedCustomerCode = currentObj.getTaggedCustomerCode();
        edName.setText(currentObj.getCustomerName());
        edAddress.setText(currentObj.getAddress());
        edePin.setText(currentObj.getPin());

        selectRouteCode = currentObj.getRouteCode();
        selectRouteName = mAceDnsDatabase.getRouteNameFromRouteCode(currentObj.getRouteCode());

        edArea.setText(selectRouteName);
        edPhone.setText(currentObj.getNumber());

        edName.setEnabled(false);
        edAddress.setEnabled(false);
        edePin.setEnabled(false);
        edArea.setEnabled(false);
        edPhone.setEnabled(false);
        if (Constants.menuDetailsObj.getDoctor_visit().matches("yes")) {
            buttonDrCategory.setEnabled(false);
            buttonDrCategory.setText(currentObj.getDrCategory());
        }
    }

    public void reInitialiseCustomerList() {
        tempCustomerList.removeAll(tempCustomerList);
        int size = tempCustomerList.size();
        int size1 = customerList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempCustomerList.addAll(customerList);
    }

    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCustomerList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempCustomerList.get(ii).getCustomerName().length() >= strCnt) {
                if (!tempCustomerList.get(ii).getCustomerName().toUpperCase().contains(charVal.toUpperCase())) {
                    tempCustomerList.remove(tempCustomerList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempCustomerList.remove(tempCustomerList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    Dialog customerListDialog;

    @SuppressLint("SetTextI18n")
    public void ShowChooseCategoryDialog() {
        customerListDialog = new Dialog(BusinessProspectActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.select_with_search);
        customerListDialog.setCancelable(false);
        TextView title = customerListDialog.findViewById(R.id.title);
        title.setText("Please select Dr. Category");
        EditText searchText = customerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        final ListView dialogList = customerListDialog.findViewById(R.id.list);

        drCategoryList = new ArrayList<>();
        String[] arrayOfData = mAceDnsDatabase.getDrCategory();
        Collections.addAll(drCategoryList, arrayOfData);

        DrCategoryAdapter adapter = new DrCategoryAdapter(BusinessProspectActivity.this, R.layout.activity_listview_remarks, drCategoryList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            buttonDrCategory.setText(arrayOfData[arg2]);
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            customerListDialog.cancel();
        });
        Button btnCancel = customerListDialog.findViewById(R.id.btn_ok);
        btnCancel.setOnClickListener(v -> customerListDialog.cancel());
        customerListDialog.show();
    }

    public void SetDrCat(String s) {
        buttonDrCategory.setText(s);
        customerListDialog.cancel();
    }
}
