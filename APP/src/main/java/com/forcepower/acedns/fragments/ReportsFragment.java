package com.forcepower.acedns.fragments;

import android.content.Context;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CounterBidListReportAdapter;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;
import java.util.HashMap;

import static com.forcepower.acedns.constants.Constants.CounterBidReportListListForRACustomers;

public class ReportsFragment extends Fragment {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";

    Context mContext;
    Spinner customerSpinner;
    AceDnsDatabase mAceDnsDatabase;
    ListView dialogList;
    RadioGroup rgBidCount;
    RadioButton radioTotal, radioAccepted, radioRejected;
    String bidType = "total";
    int currentCustomerIndex = 0;

    public ReportsFragment() {
        // Required empty public constructor
    }

    // TODO: Rename and change types and number of parameters
    public static ReportsFragment newInstance(String param1, String param2) {
        ReportsFragment fragment = new ReportsFragment();
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

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_reports, container, false);

        dialogList = (ListView) view.findViewById(R.id.prodQtyRateListView);
        rgBidCount = (RadioGroup) view.findViewById(R.id.rgBidCount);
        rgBidCount.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                if (checkedId == R.id.radioTotal) {
                    bidType = "total";
                } else if (checkedId == R.id.radioAccepted) {
                    bidType = "accepted";
                } else //if(checkedId==R.id.radioRejected)
                {
                    bidType = "rejected";
                }
                getCounterBidListForCustomer(currentCustomerIndex);
            }
        });
        radioTotal = (RadioButton) view.findViewById(R.id.radioTotal);
        radioAccepted = (RadioButton) view.findViewById(R.id.radioAccepted);
        radioRejected = (RadioButton) view.findViewById(R.id.radioRejected);
        customerSpinner = (Spinner) view.findViewById(R.id.customerSpinner);
        mAceDnsDatabase.getCustomerListForRAReports();
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < CounterBidReportListListForRACustomers.size(); i++) {
            spinnerArray.add(CounterBidReportListListForRACustomers.get(i).getCustomerName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);
        if (!CounterBidReportListListForRACustomers.isEmpty())
            customerSpinner.setSelection(0);

        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                currentCustomerIndex = i;
                HashMap<String, String> bidList = mAceDnsDatabase.getBidListByCustomerCodeForTotalAccepctedRejected(CounterBidReportListListForRACustomers.get(i).getCustomerCode());
                radioTotal.setText("Total Bid : " + bidList.get("total"));
                radioAccepted.setText("Accepted Bid : " + bidList.get("accept"));
                radioRejected.setText("Rejected Bid : " + bidList.get("reject"));
                radioRejected.setChecked(true);
//                radioTotal.setChecked(false);
                radioTotal.setChecked(true);
//                getCounterBidListForCustomer(i);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
        return view;
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
    }

    private void getCounterBidListForCustomer(int i) {
        String selectedCustomerCode = CounterBidReportListListForRACustomers.get(i).getCustomerCode();
        mAceDnsDatabase.getCounterBidListListForReportByCustomerCode(selectedCustomerCode, bidType);
        CounterBidListReportAdapter counterBidAdapter = new CounterBidListReportAdapter(mContext, R.layout.list_item_counter_bid_report);
        dialogList.setAdapter(counterBidAdapter);
    }

    @Override
    public void onDetach() {
        super.onDetach();
    }

}
