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
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter2;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter2Additional;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter2New;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter3;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter3Additional;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter3New;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter4;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter4Additional;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter4New;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter5;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter5Additional;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapter5New;
import com.forcepower.acedns.adapter.CustomerInfoDetailsListAdapterNew;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

import static com.forcepower.acedns.fragments.FragmentOne.additionalCustomer;
import static com.forcepower.acedns.fragments.FragmentOne.newCustomer;
import static com.forcepower.acedns.fragments.FragmentOne.recommendedCustomer;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link FragmentFocusProduct#newInstance} factory method to
 * create an instance of this fragment.
 */
public class FragmentFocusProduct extends Fragment {

    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";
    private SharedViewModel viewModel;
    // TODO: Rename and change types of parameters
    private String reportType;
    private String customerType;
    TextView label,list_details1,list_details2,list_details3;
    Context mContext;
    ListView dialogList;
    AceDnsDatabase mAceDnsDatabase;
    public FragmentFocusProduct() {
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
    public static FragmentFocusProduct newInstance(String param1, String param2) {
        FragmentFocusProduct fragment = new FragmentFocusProduct();
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
        mAceDnsDatabase = new AceDnsDatabase(mContext);
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
        super.onViewCreated(view, savedInstanceState);
        label =   view.findViewById(R.id.label);
        list_details1 =   view.findViewById(R.id.list_details1);
        list_details2 =   view.findViewById(R.id.list_details2);
        list_details3 =   view.findViewById(R.id.list_details3);
        viewModel = new ViewModelProvider(requireActivity()).get(SharedViewModel.class);

        viewModel.getSelectedItem().observe(this, item -> {
            String customer = viewModel.getSelectedItem().getValue();
//            label.setText("Framgent "+ reportType+" Customer "+ customer);
            label.setText("Framgent "+ reportType);

            if(reportType.equalsIgnoreCase("Focus Product")){
                list_details3.setVisibility(View.GONE);
//                list_details2.setVisibility(View.GONE);
                list_details2.setText("Rate");
                if(Constants.CUSTOMER_INFO_FLAG!=2){
                    if(customerType.toLowerCase().contains("recommended")){
                        if(Constants.selectedCustomerRecommended!=null){
                            hideSchemePeriodText(view);
                            dialogList = view.findViewById(R.id.list);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getFocusProductList();
                            CustomerInfoDetailsListAdapter ReportAdapterObject = new CustomerInfoDetailsListAdapter(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }
                    else if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            hideSchemePeriodText(view);
                            dialogList = view.findViewById(R.id.list);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getFocusProductList();
                            CustomerInfoDetailsListAdapter ReportAdapterObject = new CustomerInfoDetailsListAdapter(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                if(customerType.toLowerCase().contains("new")){
                    hideSchemePeriodText(view);
                    dialogList = view.findViewById(R.id.list);
                    dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                    ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                    CustomerInfoDetailsListAdapterNew ReportAdapterObject = new CustomerInfoDetailsListAdapterNew(mContext, R.layout.customer_info_light_material, empActivityList);
                    dialogList.setAdapter(ReportAdapterObject);
                }

            }
//            else if(reportType.equalsIgnoreCase("Top 10 Proposed SKU")){
            else if(reportType.toLowerCase().contains("top") && reportType.toLowerCase().contains("proposed")){
                list_details3.setVisibility(View.GONE);
                list_details1.setText("SKU Name");
                list_details2.setText("Qty");
                if(Constants.CUSTOMER_INFO_FLAG==0){
                                    if(customerType.toLowerCase().contains("recommended")){
                                        if(Constants.selectedCustomerRecommended!=null){
                                            String customerCode=Constants.selectedCustomerRecommended.getCustomerCode();
                                            dialogList = view.findViewById(R.id.list);
                                            hideSchemePeriodText(view);
                                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                                            CustomerInfoDetailsListAdapter2 ReportAdapterObject = new CustomerInfoDetailsListAdapter2(mContext, R.layout.customer_info_light_material, empActivityList);
                                            dialogList.setAdapter(ReportAdapterObject);
                                            empActivityList=  mAceDnsDatabase.getTopTenProposedProductListByCustomerCode(customerCode,reportType.split(" ")[1]);
                                            ReportAdapterObject = new CustomerInfoDetailsListAdapter2(mContext, R.layout.customer_info_light_material, empActivityList);
                                            dialogList.setAdapter(ReportAdapterObject);
                                        }
                                    }

                }
                else if(Constants.CUSTOMER_INFO_FLAG==1){
                    if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            String customerCode=Constants.selectedCustomeradditional.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter2Additional ReportAdapterObject = new CustomerInfoDetailsListAdapter2Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getTopTenProposedProductListByCustomerCode(customerCode,reportType.split(" ")[1]);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter2Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                    if(customerType.toLowerCase().contains("new")){
                    dialogList = view.findViewById(R.id.list);
                    hideSchemePeriodText(view);
                    dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                    ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                        CustomerInfoDetailsListAdapter2New ReportAdapterObject = new CustomerInfoDetailsListAdapter2New(mContext, R.layout.customer_info_light_material, empActivityList);
                    dialogList.setAdapter(ReportAdapterObject);
                }
            }
//            else if(reportType.equalsIgnoreCase("Last 2 Weeks SKU")){
            else if(reportType.toLowerCase().contains("last") && reportType.toLowerCase().contains("week")){

                if(Constants.CUSTOMER_INFO_FLAG==0){
                    if(customerType.toLowerCase().contains("recommended")){
                        if(Constants.selectedCustomerRecommended!=null){
                            String customerCode=Constants.selectedCustomerRecommended.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter3 ReportAdapterObject = new CustomerInfoDetailsListAdapter3(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByWeeksAndCustomerCode(customerCode, Integer.parseInt(reportType.split(" ")[1]), reportType.split(" ")[2]);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter3(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }

                    }
                }
                else if(Constants.CUSTOMER_INFO_FLAG==1){
                    if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            String customerCode=Constants.selectedCustomeradditional.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter3Additional ReportAdapterObject = new CustomerInfoDetailsListAdapter3Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByWeeksAndCustomerCode(customerCode,Integer.parseInt(reportType.split(" ")[1]), reportType.split(" ")[2]);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter3Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                    if(customerType.toLowerCase().contains("new")){
                    dialogList = view.findViewById(R.id.list);
                        hideSchemePeriodText(view);
                    dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                    ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                        CustomerInfoDetailsListAdapter3New ReportAdapterObject = new CustomerInfoDetailsListAdapter3New(mContext, R.layout.customer_info_light_material, empActivityList);
                    dialogList.setAdapter(ReportAdapterObject);
                }
            }

            else if(reportType.toLowerCase().contains("last") && reportType.toLowerCase().contains("month")){
                if(Constants.CUSTOMER_INFO_FLAG==0){
                    if(customerType.toLowerCase().contains("recommended")){
                        if(Constants.selectedCustomerRecommended!=null){
                            String customerCode=Constants.selectedCustomerRecommended.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter3 ReportAdapterObject = new CustomerInfoDetailsListAdapter3(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByWeeksAndCustomerCode(customerCode, Integer.parseInt(reportType.split(" ")[1]), reportType.split(" ")[2]);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter3(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }

                    }
                }
                else if(Constants.CUSTOMER_INFO_FLAG==1){
                    if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            String customerCode=Constants.selectedCustomeradditional.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter3Additional ReportAdapterObject = new CustomerInfoDetailsListAdapter3Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByWeeksAndCustomerCode(customerCode,Integer.parseInt(reportType.split(" ")[1]), reportType.split(" ")[2]);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter3Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                    if(customerType.toLowerCase().contains("new")){
                    dialogList = view.findViewById(R.id.list);
                        hideSchemePeriodText(view);
                    dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                    ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                        CustomerInfoDetailsListAdapter3New ReportAdapterObject = new CustomerInfoDetailsListAdapter3New(mContext, R.layout.customer_info_light_material, empActivityList);
                    dialogList.setAdapter(ReportAdapterObject);
                    }

            }

//            else if(reportType.equalsIgnoreCase("Last 10 Days SKU")){
            else if(reportType.toLowerCase().contains("last") && reportType.toLowerCase().contains("days")){
                list_details1.setText("SKU Name");
                list_details2.setText("Qty");
                list_details3.setText("Date of Entry");


                if(Constants.CUSTOMER_INFO_FLAG==0){
                    if(customerType.toLowerCase().contains("recommended")){
                        if(Constants.selectedCustomerRecommended!=null){
                            String customerCode=Constants.selectedCustomerRecommended.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter4 ReportAdapterObject = new CustomerInfoDetailsListAdapter4(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            int noOfDays = Integer.parseInt(reportType.split(" ")[1]);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByDaysAndCustomerCode(customerCode, noOfDays);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter4(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }
                }
                else if(Constants.CUSTOMER_INFO_FLAG==1){
                    if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            String customerCode=Constants.selectedCustomeradditional.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            hideSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter4Additional ReportAdapterObject = new CustomerInfoDetailsListAdapter4Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            int noOfDays = Integer.parseInt(reportType.split(" ")[1]);
                            empActivityList=mAceDnsDatabase.getPreviousSkuByDaysAndCustomerCode(customerCode,noOfDays);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter4Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                    if(customerType.toLowerCase().contains("new")){
                        dialogList = view.findViewById(R.id.list);
                        hideSchemePeriodText(view);
                        dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                        ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                        CustomerInfoDetailsListAdapter4New ReportAdapterObject = new CustomerInfoDetailsListAdapter4New(mContext, R.layout.customer_info_light_material, empActivityList);
                        dialogList.setAdapter(ReportAdapterObject);
                    }


            }
            else if(reportType.toLowerCase().contains("target") && reportType.toLowerCase().contains("achievement")){
                list_details1.setText("Scheme");
                list_details2.setText("Target");
                list_details3.setText("Achievement");


                if(Constants.CUSTOMER_INFO_FLAG==0){
                    if(customerType.toLowerCase().contains("recommended")){
                        if(Constants.selectedCustomerRecommended!=null){
                            String customerCode=Constants.selectedCustomerRecommended.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            showSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter5 ReportAdapterObject = new CustomerInfoDetailsListAdapter5(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);

                            empActivityList=mAceDnsDatabase.getTarAchDataByCustomerCode(customerCode);
                             ReportAdapterObject = new CustomerInfoDetailsListAdapter5(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }
                }
                else if(Constants.CUSTOMER_INFO_FLAG==1){
                    if(customerType.toLowerCase().contains("additional")){
                        if(Constants.selectedCustomeradditional!=null){
                            String customerCode=Constants.selectedCustomeradditional.getCustomerCode();
                            dialogList = view.findViewById(R.id.list);
                            showSchemePeriodText(view);
                            dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                            ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                            CustomerInfoDetailsListAdapter5Additional ReportAdapterObject = new CustomerInfoDetailsListAdapter5Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                            empActivityList=mAceDnsDatabase.getTarAchDataByCustomerCode(customerCode);
                            ReportAdapterObject = new CustomerInfoDetailsListAdapter5Additional(mContext, R.layout.customer_info_light_material, empActivityList);
                            dialogList.setAdapter(ReportAdapterObject);
                        }
                    }

                }
                else
                if(customerType.toLowerCase().contains("new")){
                    dialogList = view.findViewById(R.id.list);
                    showSchemePeriodText(view);
                    dialogList.setEmptyView(view.findViewById(R.id.empty_text_view));
                    ArrayList<commonDatabaseHelper> empActivityList=new ArrayList<>();
                    CustomerInfoDetailsListAdapter5New ReportAdapterObject = new CustomerInfoDetailsListAdapter5New(mContext, R.layout.customer_info_light_material, empActivityList);
                    dialogList.setAdapter(ReportAdapterObject);
                }


            }

        });
        viewModel.getRefreshFlag().observe(this, item -> {
            String customer ="";
            String additionalOrRecommended = viewModel.getRefreshFlag().getValue();
            if(additionalOrRecommended.equalsIgnoreCase("0")){
                customer=recommendedCustomer;
            }
            else if(additionalOrRecommended.equalsIgnoreCase("1")){
                customer=additionalCustomer;
            }
            else if(additionalOrRecommended.equalsIgnoreCase("2")){
                customer=newCustomer;
            }

            label.setText("Framgent "+ reportType+" Customer "+ customer);
        });


    }

    public void hideSchemePeriodText(@NonNull View view) {
        TextView tvSchemePeriod = view.findViewById(R.id.tvSchemePeriod);
        tvSchemePeriod.setVisibility(View.GONE);
    }
    public void showSchemePeriodText(@NonNull View view) {
        TextView tvSchemePeriod = view.findViewById(R.id.tvSchemePeriod);
        tvSchemePeriod.setVisibility(View.VISIBLE);
    }
    @Override
    public void onResume() {
        super.onResume();
    }
}