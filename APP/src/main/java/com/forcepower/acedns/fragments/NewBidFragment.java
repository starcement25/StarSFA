package com.forcepower.acedns.fragments;

import android.app.Dialog;
import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterRetailerNewBid;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewBid;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.bean.RADateWiseWindowTime;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;

import static java.lang.Double.parseDouble;
import static com.forcepower.acedns.constants.Constants.currentWindowClosesAt;
import static com.forcepower.acedns.constants.Constants.currentWindowStartsAt;
import static com.forcepower.acedns.constants.Constants.customerDetailsListReverseAuction;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.lodabilityToneFORDEPOT;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.plantListForRANewBid;
import static com.forcepower.acedns.constants.Constants.plantListForRANewBidCopy;
import static com.forcepower.acedns.constants.Constants.productGroupMasterListForNewBid;
import static com.forcepower.acedns.constants.Constants.selectedState;

/**
 * A simple {@link Fragment} subclass.
 * Activities that contain this fragment must implement the
 * {@link NewBidFragment.OnFragmentInteractionListener} interface
 * to handle interaction events.
 * Use the {@link NewBidFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class NewBidFragment extends Fragment {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";
    public static TextView mTextViewRateType = null;
    public static TextView textViewWindowCloseTime = null;
    public static TextView mTextViewDepotName = null;
    public static TextView submitNewBidButton = null;
    public static TextView chooseCustomerBtn = null;
    public static TextView textViewCalculatedAllocation = null;
    public String mSaudaType = "";
    public String mSaudaDepoName = "";
    ArrayList<BranchMasterDetails> saudaRDSList = new ArrayList<>();
    String verticalValueOfEmployee = "", userSelectedIncoterms = "", currentWindowClosingTime = "";
    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    ListView prodQtyRateListView;
    String CurrentSelectedPlantName = "";
    Spinner productGroupSpinner, incotermsSpinner;
    int currentProductMasterIndex;
    LinearLayout product_group_layout, incoterms_layout;
    double maxAllocation = 0.00, maxAllocationMt = 0.00, pendingQty = 0.00;

    public NewBidFragment() {
        // Required empty public constructor
    }

    // TODO: Rename and change types and number of parameters
    public static NewBidFragment newInstance(String param1, String param2) {
        NewBidFragment fragment = new NewBidFragment();
        Bundle args = new Bundle();
        args.putString(ARG_PARAM1, param1);
        args.putString(ARG_PARAM2, param2);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        mContext = getActivity();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        verticalValueOfEmployee = mAceDnsDatabase.getVerticalValueOfLoggedInEmployee();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_new_bid, container, false);
        mTextViewRateType = (TextView) view.findViewById(R.id.textViewRateTypeValue);
        textViewWindowCloseTime = (TextView) view.findViewById(R.id.textViewWindowCloseTime);
        mTextViewDepotName = (TextView) view.findViewById(R.id.textViewDepotValue);
        textViewCalculatedAllocation = (TextView) view.findViewById(R.id.textViewCalculateedAllocation);
        productGroupSpinner = (Spinner) view.findViewById(R.id.productGroupSpinner);
        incotermsSpinner = (Spinner) view.findViewById(R.id.incotermsSpinner);
        product_group_layout = (LinearLayout) view.findViewById(R.id.product_group_layout);
        incoterms_layout = (LinearLayout) view.findViewById(R.id.incoterms_layout);
        prodQtyRateListView = (ListView) view.findViewById(R.id.prodQtyRateListView);
        Constants.prodQtyRateListView.setEmptyView(view.findViewById(R.id.empty_text_view));
        submitNewBidButton = (Button) view.findViewById(R.id.submitNewBidButton);
        chooseCustomerBtn = (Button) view.findViewById(R.id.chooseCustomerBtn);
        chooseCustomerBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                customerBranchSelectionFlow();
            }
        });
        submitNewBidButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (isBidWindowOpen()) {
                    boolean isAtLeastOneInputGiven = false;
                    maxAllocationMt = maxAllocation;
                    for (int count = 0; count < Constants.plantListForRANewBid.size(); count++) {
                        PlantProductWiseRARate masterObj = Constants.plantListForRANewBid.get(count);
                        String bidPrice = masterObj.getbidPrice();
                        String qty = masterObj.getQty();
                        if (Utils.isNumeric(qty) && Utils.isNumeric(bidPrice)) {
                            isAtLeastOneInputGiven = true;
                            String convFactorOne = masterObj.getconversionFactorOne();
                            String convFactorTwo = masterObj.getconversionFactorTwo();
                            double convFactorOneDouble = 0.00, convFactorTwoDouble = 0.00, qtyInDoubleCases = Double.parseDouble(qty);
                            if (Utils.isNumeric(convFactorOne)) {
                                convFactorOneDouble = Double.parseDouble(convFactorOne);
                            }
                            if (Utils.isNumeric(convFactorTwo)) {
                                convFactorTwoDouble = Double.parseDouble(convFactorTwo);
                            }

                            double qtyInDoubleMt = (qtyInDoubleCases * convFactorOneDouble) / convFactorTwoDouble;
                            maxAllocationMt = maxAllocationMt - qtyInDoubleMt;
                        }
                    }
                    if (!isAtLeastOneInputGiven) {
                        Utils.showToast(mContext, "You have to provide both quantity and rate for at least one product");
                    } else {
                        if (maxAllocationMt < 0) {
                            Utils.showToast(mContext, "Exceed max allocation(" + maxAllocation + " M.T.)");
                        } else {
                            new GPSTracker(mContext);
                            AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
                            String timeStamp = "";
                            timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                            dataHelperObj.INSERTtoRABidRateDetails(timeStamp, verticalValueOfEmployee, userSelectedIncoterms);
                            dataHelperObj.insertToLocationTable("RB", timeStamp);
                            double remainingAllocation = maxAllocationMt + pendingQty;
                            dataHelperObj.updateCustomerSaudaLimit(String.valueOf(remainingAllocation));
                            productGroupGenerationProcess();
                            showProductListForChosenCustomerAndPlant();
                            new TRANS_SubmitNewBid(mContext, true).execute();
                        }
                    }

                } else {
                    Utils.showToast(mContext, "Sorry, bidding time is over.");
                }


            }
        });
        isBidWindowOpen();
        return view;
    }

    private boolean isBidWindowOpen() {
        Boolean isCurrentTimeInWindowOpenTime = false;
        ArrayList<RADateWiseWindowTime> WindowOpenCloseTimingForToday = mAceDnsDatabase.getWindowOpenCloseTimingForToday();
        if (WindowOpenCloseTimingForToday.size() > 0) {

            for (int i = 0; i < WindowOpenCloseTimingForToday.size(); i++) {
                isCurrentTimeInWindowOpenTime = Utils.checkCurrentTimeIsWithinGivenRangeOrNot(dateString + "-" + WindowOpenCloseTimingForToday.get(i).getTimeFrom(), dateString + "-" + WindowOpenCloseTimingForToday.get(i).getTimeTo(), "yyyyMMdd-HH:mm:ss");
                if (isCurrentTimeInWindowOpenTime) {
                    currentWindowStartsAt = Utils.changeDateFormat("HH:mm:ss", "HHmmss", WindowOpenCloseTimingForToday.get(i).getTimeFrom());
                    currentWindowClosesAt = Utils.changeDateFormat("HH:mm:ss", "HHmmss", WindowOpenCloseTimingForToday.get(i).getTimeTo());
                    currentWindowClosingTime = Utils.changeDateFormat("HH:mm:ss", "hh:mm:ss a", WindowOpenCloseTimingForToday.get(i).getTimeTo());
                    textViewWindowCloseTime.setText(currentWindowClosingTime);
                    break;
                }

            }
        }
        return isCurrentTimeInWindowOpenTime;
    }

    private void productGroupGenerationProcess() {
        mAceDnsDatabase.getProductGroupListForRaNewBid(CurrentSelectedPlantName);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productGroupMasterListForNewBid.size(); i++) {
            spinnerArray.add(productGroupMasterListForNewBid.get(i).getGroupName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        productGroupSpinner.setAdapter(spinnerArrayAdapter);
        if (!productGroupMasterListForNewBid.isEmpty()) {
            productGroupSpinner.setSelection(0);
            product_group_layout.setVisibility(View.VISIBLE);
        }


        productGroupSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                currentProductMasterIndex = i;
                if (!CurrentSelectedPlantName.matches("")) {
                    showProductListForChosenCustomerAndPlant();
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    private void customerBranchSelectionFlow() {
        incoterms_layout.setVisibility(View.GONE);
        mSaudaType = "";
        mDepotOrPlant = "";
        if (customerDetailsListReverseAuction.size() > 1) {
            ShowCustomerListDialog();
        } else {
            chooseCustomerBtn.setText(customerDetailsListReverseAuction.get(0).getCustomerName());
            Constants.selectedCustomer = customerDetailsListReverseAuction.get(0);
            maxAllocationCalculationProcess();
            incotermsSelectionProcess();
        }
    }

    private void maxAllocationCalculationProcess() {
        String SaudaLimitForSelectedCustomerCode = Constants.selectedCustomer.getSaudaLimit();
        String PendingQuantityForSelectedCustomerCode = Constants.selectedCustomer.getPendingQty();
        double saudaLimit = 0.0;
        if (Utils.isNumeric(SaudaLimitForSelectedCustomerCode)) {
            saudaLimit = Double.parseDouble(SaudaLimitForSelectedCustomerCode);
        }
        if (Utils.isNumeric(PendingQuantityForSelectedCustomerCode)) {
            pendingQty = Double.parseDouble(PendingQuantityForSelectedCustomerCode);
        }

        try {
            maxAllocation = saudaLimit - pendingQty;
//                totalQty=(totalQty*conversionfactorTwo)/conversionfactor;
//                double finalValue = Math.round(totalQty * 100.0) / 100.0;

        } catch (Exception e) {
            maxAllocation = 0;
        }
        textViewCalculatedAllocation.setText(defaultFormat.format(maxAllocation) + "(M.T.)");
    }

    private void incotermsSelectionProcess() {
        String incoTermsOfCurentCustomer = Constants.selectedCustomer.getIncoTerms().trim();
        if (incoTermsOfCurentCustomer.contains(";"))//multiple incoterms
        {
            ArrayList<String> incotermsArrayMultiple = new ArrayList<String>(Arrays.asList(incoTermsOfCurentCustomer.split(";")));
            ShowIncotermsSelectionSpinner(incotermsArrayMultiple);
        } else {
            ArrayList<String> incotermsArrayMultiple = new ArrayList<>();
            incotermsArrayMultiple.add(incoTermsOfCurentCustomer);
            ShowIncotermsSelectionSpinner(incotermsArrayMultiple);
//            incotermsSplitProcess(incoTermsOfCurentCustomer);
        }

    }

    public void ShowIncotermsSelectionSpinner(final ArrayList<String> incotermsArrayMultiple) {
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, incotermsArrayMultiple);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        incotermsSpinner.setAdapter(spinnerArrayAdapter);
        if (!incotermsArrayMultiple.isEmpty()) {
            incotermsSpinner.setSelection(0);
            incoterms_layout.setVisibility(View.VISIBLE);
        } else {
            Toast.makeText(mContext, "Improper incoterms data.", Toast.LENGTH_SHORT).show();
        }


        incotermsSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                selectedState = incotermsArrayMultiple.get(i);
                incotermsSplitProcess(incotermsArrayMultiple.get(i));
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
//        if (incotermsArrayMultiple.size() > 1)
//        {
//            final Dialog incotermsSelectionDialog = new Dialog(mContext,
//                    R.style.PauseDialog);
//            incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
//            incotermsSelectionDialog.setContentView(R.layout.select_from_list);
//            incotermsSelectionDialog.setCancelable(false);
//
//            Button btn_cncl = (Button) incotermsSelectionDialog.findViewById(R.id.btn_cncl);
//            btn_cncl.setOnClickListener(new View.OnClickListener()
//            {
//                @Override
//                public void onClick(View view)
//                {
//                    incotermsSelectionDialog.dismiss();
//                }
//            });
//            TextView title = (TextView) incotermsSelectionDialog.findViewById(R.id.title);
//            title.setText("Please select incoterms");
//            ListView dialogList = (ListView) incotermsSelectionDialog
//                    .findViewById(R.id.list);
//
//            StateAdapter branchadapter = new StateAdapter(mContext,
//                    R.layout.route_list_child, incotermsArrayMultiple,false);
//            dialogList.setAdapter(branchadapter);
//            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
//                @Override
//                public void onItemClick(AdapterView<?> arg0, View arg1,
//                                        int arg2, long arg3) {
//                    incotermsSelectionDialog.cancel();
//                    selectedState = incotermsArrayMultiple.get(arg2);
//                    incotermsSplitProcess(incotermsArrayMultiple.get(arg2));
//                }
//            });
//
//            incotermsSelectionDialog.show();
//
//        }
//        else
//        {
//            if (incotermsArrayMultiple.size() == 1)
//            {
//                incotermsSplitProcess(incotermsArrayMultiple.get(0));
//            }
//            else
//            {
//                Toast.makeText(mContext, "Improper incoterms data.", Toast.LENGTH_SHORT).show();
//            }
//        }
    }

    private void incotermsSplitProcess(String incoTermsOfCurentCustomer) {
        userSelectedIncoterms = incoTermsOfCurentCustomer;
        if (incoTermsOfCurentCustomer.toLowerCase().contains("for depot") || incoTermsOfCurentCustomer.toLowerCase().contains("for plant") || incoTermsOfCurentCustomer.toLowerCase().contains("ex depot")
                || incoTermsOfCurentCustomer.toLowerCase().contains("ex plant")) {
            String[] inctermsArray = incoTermsOfCurentCustomer.split(" ");
            if (inctermsArray.length == 2) {
                mSaudaType = inctermsArray[0].toUpperCase();
                mDepotOrPlant = inctermsArray[1];
                ShowSaudaDepoNameDialog();
            } else {
                Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
            }

        } else {
            Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
        }
    }

    // TODO: Rename method, update argument and hook method into UI event
    public void onButtonPressed(Uri uri) {

    }

    public void ShowSaudaDepoNameDialog() {

        String plantDepotFilter = "";
        if (mDepotOrPlant.equalsIgnoreCase("plant")) {
            plantDepotFilter = "LOWER(is_plant)='yes'  AND ";
        } else if (mDepotOrPlant.equalsIgnoreCase("depot")) {
            plantDepotFilter = "LOWER(is_plant)='no' AND  ";
        }
        saudaRDSList = mAceDnsDatabase.getSaudaRDSListForOrder(Constants.selectedCustomer.getCustomerCode(), plantDepotFilter);

        if (saudaRDSList.size() > 1) {
            final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
            mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogDepotName.setContentView(R.layout.select_from_list);
            mDialogDepotName.setCancelable(false);

            TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
            title.setText("Please select a " + mDepotOrPlant);
            ListView dialogList = (ListView) mDialogDepotName.findViewById(R.id.list);

            BranchAdapter branchadapter = new BranchAdapter(mContext,
                    R.layout.route_list_child, saudaRDSList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogDepotName.cancel();

                    depotOrPlantSelectionProcess(arg2);
                }
            });

            Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);

            mDialogDepotName.show();

        } else {
            if (saudaRDSList.size() == 1) {
                depotOrPlantSelectionProcess(0);
            } else {
                Toast.makeText(mContext, "No " + mDepotOrPlant + " found.", Toast.LENGTH_LONG).show();
            }
        }
    }

    private void depotOrPlantSelectionProcess(int i) {
        Constants.selectedBranch = saudaRDSList.get(i);
        mSaudaDepoName = Constants.selectedBranch.getBranchName();
        mSaudaDepoCode = Constants.selectedBranch.getBranchCode();
        mTextViewRateType.setText("  Rate : " + mSaudaType + " - " + mDepotOrPlant);
        mTextViewDepotName.setText("  " + mDepotOrPlant + " Name : " + mSaudaDepoName);
        CurrentSelectedPlantName = mAceDnsDatabase.getPlantNameFromBranchCode();
        productGroupGenerationProcess();
        showProductListForChosenCustomerAndPlant();

    }

    private void showProductListForChosenCustomerAndPlant() {
        if (productGroupMasterListForNewBid.isEmpty() || currentProductMasterIndex < 0) {
            mAceDnsDatabase.getProductListForRaNewBid(CurrentSelectedPlantName, "");
        } else {
            mAceDnsDatabase.getProductListForRaNewBid(CurrentSelectedPlantName, productGroupMasterListForNewBid.get(currentProductMasterIndex).getGroupCode());
        }

        if (mSaudaType.equalsIgnoreCase("ex")) {
            if (mDepotOrPlant.equalsIgnoreCase("depot"))//ex depot:  rcvd rate+margin cost+honeycomb cost+ primary freight+depot cost+detention cost
            {
                for (int i2 = 0; i2 < plantListForRANewBidCopy.size(); i2++) {
                    Boolean isPrimaryFreightOk = false;
                    PlantProductWiseRARate plantProductWiseRARateItem = plantListForRANewBidCopy.get(i2);
                    ArrayList<String> DepotCostPrimaryFreight = mAceDnsDatabase.getTotalDepotCostPrimaryFreightFromSaudaMrpBySkuCode(plantProductWiseRARateItem.getProdCode());
                    double totalRate = Double.parseDouble(plantProductWiseRARateItem.getIndicativeRateServer());
                    if (Utils.isNumeric(plantProductWiseRARateItem.getmarginCost())) {
                        totalRate = totalRate + Double.parseDouble(plantProductWiseRARateItem.getmarginCost());
                    }
                    String currentPrimaryFreight = "";
                    String currentDepotCost = "";
                    if (DepotCostPrimaryFreight.size() > 0) {
                        currentPrimaryFreight = DepotCostPrimaryFreight.get(0);
                        currentDepotCost = DepotCostPrimaryFreight.get(1);
                    }
                    if (DepotCostPrimaryFreight.size() > 0 && Utils.isNumeric(currentPrimaryFreight) && parseDouble(currentPrimaryFreight) > 0) {
                        isPrimaryFreightOk = true;
                        totalRate = totalRate + parseDouble(currentPrimaryFreight);
                        plantProductWiseRARateItem.setPrimaryFreight(currentPrimaryFreight);
                    }

                    String honeyCombCost = mAceDnsDatabase.GetHoneyCombCostByProductCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getPlantName());
                    String detentionCost = mAceDnsDatabase.GetHoneyCombCostByProductCodeBranchCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getBranchCode());
                    if (Utils.isNumeric(detentionCost)) {
                        totalRate = totalRate + parseDouble(detentionCost);
                        plantProductWiseRARateItem.setdetentionCost(detentionCost);
                    }
                    if (Utils.isNumeric(honeyCombCost)) {
                        totalRate = totalRate + parseDouble(honeyCombCost);
                        plantProductWiseRARateItem.sethoneyCombCost(honeyCombCost);
                    }
                    if (Utils.isNumeric(currentDepotCost)) {
                        totalRate = totalRate + parseDouble(currentDepotCost);
                        plantProductWiseRARateItem.setDepotCost(currentDepotCost);
                    } else {
                        plantProductWiseRARateItem.setDepotCost("0");
                    }
                    if (isPrimaryFreightOk) {
                        double gstValue = (totalRate * parseDouble(plantProductWiseRARateItem.getgstPercent())) / 100;
                        totalRate = totalRate + gstValue;
                        plantProductWiseRARateItem.setIndicativeRateApp(String.valueOf(defaultFormat.format(totalRate)));
                        plantProductWiseRARateItem.setgstValue(String.valueOf(defaultFormat.format(gstValue)));
                        plantListForRANewBid.add(plantProductWiseRARateItem);
                    }

                }
            } else//ex plant: rcvd rates+honeycomb cost+margin cost
            {
                for (int i2 = 0; i2 < plantListForRANewBidCopy.size(); i2++) {
                    PlantProductWiseRARate plantProductWiseRARateItem = plantListForRANewBidCopy.get(i2);
                    double totalRate = Double.parseDouble(plantProductWiseRARateItem.getIndicativeRateServer());
                    String honeyCombCost = mAceDnsDatabase.GetHoneyCombCostByProductCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getPlantName());
                    if (Utils.isNumeric(honeyCombCost)) {
                        totalRate = totalRate + parseDouble(honeyCombCost);
                        plantProductWiseRARateItem.sethoneyCombCost(honeyCombCost);
                    }
                    if (Utils.isNumeric(plantProductWiseRARateItem.getmarginCost())) {
                        totalRate = totalRate + Double.parseDouble(plantProductWiseRARateItem.getmarginCost());
                    }
                    double gstValue = (totalRate * parseDouble(plantProductWiseRARateItem.getgstPercent())) / 100;
                    totalRate = totalRate + gstValue;
                    plantProductWiseRARateItem.setIndicativeRateApp(String.valueOf(defaultFormat.format(totalRate)));
                    plantProductWiseRARateItem.setgstValue(String.valueOf(defaultFormat.format(gstValue)));
                    plantListForRANewBid.add(plantProductWiseRARateItem);

                }

//                plantListForRANewBid=new ArrayList<>(plantListForRANewBidCopy);
            }
        } else {
            if (mDepotOrPlant.equalsIgnoreCase("depot"))//for depot: rcvd rate+margin cost+honeycomb cost+ primary freight+2ndary freight+depot cost+detention cost
            {
                for (int i2 = 0; i2 < plantListForRANewBidCopy.size(); i2++) {
                    Boolean isSecondaryFreightOk = false, isPrimaryFreightOk = false;
                    PlantProductWiseRARate plantProductWiseRARateItem = plantListForRANewBidCopy.get(i2);
                    ArrayList<String> DepotCostPrimaryFreight = mAceDnsDatabase.getTotalDepotCostPrimaryFreightFromSaudaMrpBySkuCode(plantProductWiseRARateItem.getProdCode());
                    double totalRate = parseDouble(plantProductWiseRARateItem.getIndicativeRateServer());
                    String honeyCombCost = mAceDnsDatabase.GetHoneyCombCostByProductCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getPlantName());
                    if (Utils.isNumeric(honeyCombCost)) {
                        totalRate = totalRate + parseDouble(honeyCombCost);
                        plantProductWiseRARateItem.sethoneyCombCost(honeyCombCost);
                    }
                    String detentionCost = mAceDnsDatabase.GetHoneyCombCostByProductCodeBranchCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getBranchCode());
                    if (Utils.isNumeric(detentionCost)) {
                        totalRate = totalRate + parseDouble(detentionCost);
                        plantProductWiseRARateItem.setdetentionCost(detentionCost);
                    }
                    if (Utils.isNumeric(plantProductWiseRARateItem.getmarginCost())) {
                        totalRate = totalRate + Double.parseDouble(plantProductWiseRARateItem.getmarginCost());
                    }
                    String currentPrimaryFreight = "";
                    String currentDepotCost = "";
                    if (DepotCostPrimaryFreight.size() > 0) {
                        currentPrimaryFreight = DepotCostPrimaryFreight.get(0);
                        currentDepotCost = DepotCostPrimaryFreight.get(1);
                    }
                    if (DepotCostPrimaryFreight.size() > 0 && Utils.isNumeric(currentPrimaryFreight) && parseDouble(currentPrimaryFreight) > 0) {
                        isPrimaryFreightOk = true;
                        totalRate = totalRate + parseDouble(currentPrimaryFreight);
                        plantProductWiseRARateItem.setPrimaryFreight(currentPrimaryFreight);
                    }

                    if (Utils.isNumeric(currentDepotCost)) {
                        totalRate = totalRate + parseDouble(currentDepotCost);
                        plantProductWiseRARateItem.setDepotCost(currentDepotCost);
                    } else {
                        plantProductWiseRARateItem.setDepotCost("0");
                    }
//                    String selectedFreightRateSecondary=mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMasterNewBid(mSaudaDepoCode, Constants.selectedCustomer.getRouteCode(),Constants.selectedCustomer.getLoadabilityTon(),verticalValueOfEmployee);
                    String selectedFreightRateSecondary = mAceDnsDatabase.GetFreightRateByBranchCodeFromRARouteFreightMasterNewBid(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), Constants.selectedCustomer.getLoadabilityTon(), verticalValueOfEmployee, false);
                    String selectedFreightRateBranch = mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMaster(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), Constants.selectedCustomer.getLoadabilityTon(), verticalValueOfEmployee);
                    lodabilityToneFORDEPOT = mAceDnsDatabase.GetLoadabilityTonForDepotSAUDA(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), verticalValueOfEmployee, "RA_route_freight");
                    if (Utils.isNumeric(selectedFreightRateSecondary) && Double.parseDouble(selectedFreightRateSecondary) > 0 && Utils.isNumeric(selectedFreightRateBranch) && Double.parseDouble(selectedFreightRateBranch) > 0) {
                        String trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCode(plantProductWiseRARateItem.getDnsProdCode(), lodabilityToneFORDEPOT, Constants.selectedCustomer.getTransportMode(), true);
                        if (trackLoadQuantity != null && !trackLoadQuantity.matches("")) {
                            double freightRateSecondary = parseDouble(selectedFreightRateSecondary) / parseDouble(trackLoadQuantity);
                            totalRate = totalRate + freightRateSecondary;
                            isSecondaryFreightOk = true;
                            plantProductWiseRARateItem.setSecondaryFreight(String.valueOf(freightRateSecondary));
                        }

                    }
                    if (isSecondaryFreightOk && isPrimaryFreightOk) {
                        double gstValue = (totalRate * parseDouble(plantProductWiseRARateItem.getgstPercent())) / 100;
                        totalRate = totalRate + gstValue;
                        plantProductWiseRARateItem.setIndicativeRateApp(String.valueOf(defaultFormat.format(totalRate)));
                        plantProductWiseRARateItem.setgstValue(String.valueOf(defaultFormat.format(gstValue)));
                        plantListForRANewBid.add(plantProductWiseRARateItem);
                    }
                }
            } else//for plant: rcvd rates+margin cost+honeycomb cost+secondary freight+detention cost
            {
                for (int i2 = 0; i2 < plantListForRANewBidCopy.size(); i2++) {
                    Boolean isSecondaryFreightOk = false;
                    PlantProductWiseRARate plantProductWiseRARateItem = plantListForRANewBidCopy.get(i2);
                    double totalRate = parseDouble(plantProductWiseRARateItem.getIndicativeRateServer());
                    String honeyCombCost = mAceDnsDatabase.GetHoneyCombCostByProductCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getPlantName());
                    if (Utils.isNumeric(honeyCombCost)) {
                        totalRate = totalRate + parseDouble(honeyCombCost);
                        plantProductWiseRARateItem.sethoneyCombCost(honeyCombCost);
                    }
                    String detentionCost = mAceDnsDatabase.GetHoneyCombCostByProductCodeBranchCode(plantProductWiseRARateItem.getProdCode(), Constants.selectedBranch.getBranchCode());
                    if (Utils.isNumeric(detentionCost)) {
                        totalRate = totalRate + parseDouble(detentionCost);
                        plantProductWiseRARateItem.setdetentionCost(detentionCost);
                    }
                    if (Utils.isNumeric(plantProductWiseRARateItem.getmarginCost())) {
                        totalRate = totalRate + Double.parseDouble(plantProductWiseRARateItem.getmarginCost());
                    }
//                    String selectedFreightRateSecondary=mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMasterNewBid(mSaudaDepoCode,Constants.selectedCustomer.getRouteCode(),Constants.selectedCustomer.getLoadabilityTon(),verticalValueOfEmployee);
                    //different table(RA_route_freight) to get freight in case of for plant
                    String selectedFreightRateSecondary = mAceDnsDatabase.GetFreightRateByBranchCodeFromRARouteFreightMasterNewBid(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), Constants.selectedCustomer.getLoadabilityTon(), verticalValueOfEmployee, true);
                    String selectedFreightRateBranch = mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMaster(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), Constants.selectedCustomer.getLoadabilityTon(), verticalValueOfEmployee);
                    if (Utils.isNumeric(selectedFreightRateSecondary) && Double.parseDouble(selectedFreightRateSecondary) > 0 && Utils.isNumeric(selectedFreightRateBranch) && Double.parseDouble(selectedFreightRateBranch) > 0) {
                        String trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCode(plantProductWiseRARateItem.getDnsProdCode(), Constants.selectedCustomer.getLoadabilityTon(), Constants.selectedCustomer.getTransportMode(), true);
                        if (trackLoadQuantity != null && !trackLoadQuantity.matches("")) {
                            double freightRateSecondary = parseDouble(selectedFreightRateSecondary) / parseDouble(trackLoadQuantity);
                            totalRate = totalRate + freightRateSecondary;
                            isSecondaryFreightOk = true;
                            plantProductWiseRARateItem.setSecondaryFreight(String.valueOf(freightRateSecondary));
                        }
                    }
                    if (isSecondaryFreightOk) {
                        double gstValue = (totalRate * parseDouble(plantProductWiseRARateItem.getgstPercent())) / 100;
                        totalRate = totalRate + gstValue;
                        plantProductWiseRARateItem.setgstValue(String.valueOf(defaultFormat.format(gstValue)));
                        plantProductWiseRARateItem.setIndicativeRateApp(String.valueOf(defaultFormat.format(totalRate)));
                        plantListForRANewBid.add(plantProductWiseRARateItem);
                    }

                }
            }
        }
        ProductMasterWithQtyInputAdapterRetailerNewBid ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterRetailerNewBid(mContext, R.layout.product_list_item_with_quantity_input_new_bid, plantListForRANewBid);
        prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
        if (plantListForRANewBid.size() > 0) {

        } else {
            Utils.showToast(mContext, "No Product found.");
        }
    }


    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext,
                R.layout.customer_list_child, customerDetailsListReverseAuction);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) mDialogCustomer
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                Constants.isSelectCustomer = true;
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                chooseCustomerBtn.setText(Constants.selectedCustomer.getCustomerName());
                maxAllocationCalculationProcess();
                incotermsSelectionProcess();
            }
        });

        Button addCustomer = (Button) mDialogCustomer
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }


    @Override
    public void onAttach(Context context) {
        super.onAttach(context);

    }

    @Override
    public void onDetach() {
        super.onDetach();
    }

    public interface OnFragmentInteractionListener {
        // TODO: Update argument type and name
        void onFragmentInteraction(Uri uri);
    }
}
