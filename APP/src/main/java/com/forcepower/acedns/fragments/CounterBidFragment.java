package com.forcepower.acedns.fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Spinner;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CounterBidListAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCounterBid;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.CounterBidListListForRAOnToday;
import static com.forcepower.acedns.constants.Constants.CounterBidListListForRAOnTodayByCustomerCode;

public class CounterBidFragment extends Fragment {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";

    Context mContext;
    Spinner customerSpinner;
    Button btn_submit;
    AceDnsDatabase mAceDnsDatabase;
    ListView dialogList;

    public CounterBidFragment() {
        // Required empty public constructor
    }

    // TODO: Rename and change types and number of parameters
    public static CounterBidFragment newInstance(String param1, String param2) {
        CounterBidFragment fragment = new CounterBidFragment();
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
        View view = inflater.inflate(R.layout.fragment_counter_bid, container, false);
        customerSpinner = (Spinner) view.findViewById(R.id.customerSpinner);
        btn_submit = (Button) view.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                new GPSTracker(mContext);

                AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
                String timeStamp = "";
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                boolean allStatusChecked = true;
                int unchekedStatusBidId = 0;
                for (int i = 0; i < CounterBidListListForRAOnTodayByCustomerCode.size(); i++) {
                    String status = CounterBidListListForRAOnTodayByCustomerCode.get(i).getCounterBidStatus();
                    if (!status.matches("A") && !status.matches("R")) {
                        allStatusChecked = false;
                        unchekedStatusBidId = i;
                        break;
                    }
                }
                if (allStatusChecked) {
                    dataHelperObj.UpdtaeRABidRateDetails(timeStamp);
                    dataHelperObj.insertToLocationTable("CB", timeStamp);
                    new TRANS_SubmitCounterBid(mContext, true).execute();
                } else {
                    Utils.showToast(mContext, "Please accept or reject counter offer for " + CounterBidListListForRAOnTodayByCustomerCode.get(unchekedStatusBidId).getprodName());
                }
            }
        });
        dialogList = (ListView) view.findViewById(R.id.prodQtyRateListView);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < CounterBidListListForRAOnToday.size(); i++) {
            spinnerArray.add(CounterBidListListForRAOnToday.get(i).getCustomerName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);
        if (!CounterBidListListForRAOnToday.isEmpty())
            customerSpinner.setSelection(0);

        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                getCounterBidListForCustomer(i);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
        return view;
    }

    private void getCounterBidListForCustomer(int i) {
        String selectedCustomerCode = CounterBidListListForRAOnToday.get(i).getCustomerCode();
        mAceDnsDatabase.getCounterBidListListForRAOnTodayByCustomerCode(selectedCustomerCode);
        CounterBidListAdapter counterBidAdapter = new CounterBidListAdapter(mContext, R.layout.list_item_counter_bid);
        dialogList.setAdapter(counterBidAdapter);
    }

    // TODO: Rename method, update argument and hook method into UI event
    public void onButtonPressed(Uri uri) {
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
