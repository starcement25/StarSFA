package com.forcepower.acedns.fragments;

import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProvider;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter;
import com.forcepower.acedns.bean.commonDatabaseHelper;

import java.util.ArrayList;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link FragmentQuarterly#newInstance} factory method to
 * create an instance of this fragment.
 */
public class FragmentQuarterly extends Fragment {

    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";
    private SharedViewModel viewModel;
    // TODO: Rename and change types of parameters
    private String reportType;
    private String customerType;
    TextView label;
    Context mContext;
    ListView dialogList;
    public FragmentQuarterly() {
        // Required empty public constructor
    }

    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     * @param param1 Parameter 1.
     * @param param2 Parameter 2.
     * @return A new instance of fragment FragmentThree.
     */
    // TODO: Rename and change types and number of parameters
    public static FragmentQuarterly newInstance(String param1, String param2) {
        FragmentQuarterly fragment = new FragmentQuarterly();
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
        if (getArguments() != null) {
            reportType = getArguments().getString(ARG_PARAM1);
            customerType = getArguments().getString(ARG_PARAM2);
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_three, container, false);

        return view;
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        label =   view.findViewById(R.id.label);
        viewModel = new ViewModelProvider(requireActivity()).get(SharedViewModel.class);
        dialogList = view.findViewById(R.id.list);
//        viewModel.getSelectedItem().observe(this, item -> {
            label.setText("Framgent "+ reportType+" Customer "+customerType);

//        });

        super.onViewCreated(view, savedInstanceState);
    }

    @Override
    public void onResume() {
        super.onResume();
        ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
        commonDatabaseHelper ob=new commonDatabaseHelper();
        ob.setItem0(viewModel.getSelectedItem().getValue()+" Quarter");
        ob.setItem1("300");
        ob.setItem2("12/01/2021");
        empActivityList.add(ob);
        ob.setItem0(viewModel.getSelectedItem().getValue()+" Quarter");
        ob.setItem1("300");
        ob.setItem2("12/01/2021");
        empActivityList.add(ob);
        ob.setItem0(viewModel.getSelectedItem().getValue()+" Quarter");
        ob.setItem1("300");
        ob.setItem2("12/01/2021");
        empActivityList.add(ob);
        CustomerInfoDetailsListAdapter ReportAdapterObject = new CustomerInfoDetailsListAdapter(mContext, R.layout.customer_info_light_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
    }
}