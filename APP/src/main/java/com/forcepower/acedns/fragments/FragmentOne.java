package com.forcepower.acedns.fragments;

import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import androidx.lifecycle.ViewModelProvider;

import androidx.viewpager.widget.ViewPager;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.TextView;

import com.google.android.material.tabs.TabLayout;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.ActivityOrderFilterAlternateDesign;
import com.forcepower.acedns.adapter.RecommendedAditionalCustomerSpinnerAdapter;
import com.forcepower.acedns.adapter.RecommendedAditionalRouteSpinnerAdapter;
import com.forcepower.acedns.adapter.TabFragmentAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.allAdditionalCustomersWithLogicRoute;
import static com.forcepower.acedns.constants.Constants.allAdditionalDistributorsWithLogic;
import static com.forcepower.acedns.constants.Constants.allAdditionalRoutesWithLogic;
import static com.forcepower.acedns.constants.Constants.allNewRoutes;
import static com.forcepower.acedns.constants.Constants.allRecommendedDistributorsWithLogic;
import static com.forcepower.acedns.constants.Constants.allRecommendedRoutesWithLogic;
import static com.forcepower.acedns.constants.Constants.selectedCustomerNew;
import static com.forcepower.acedns.constants.Constants.selectedCustomerRecommended;
import static com.forcepower.acedns.constants.Constants.selectedCustomeradditional;


public class FragmentOne extends Fragment {
//    Spinner customerSpinner;
//    Spinner DealerSpinner;
    Context mContext;
    TabLayout tabReports;
    ViewPager mViewPager;
    private static final String ARG_PARAM1 = "param1";
    private static final String ARG_PARAM2 = "param2";
    private String customerType;
    private String mParam2;
    String currwentCust,rds="",rc="";
    TextView tv_dealer;
    TextView tv_route;
    TextView cart_badge_route;
    TextView tv_customer;
    public static String recommendedCustomer="",additionalCustomer="",newCustomer="";
    private SharedViewModel viewModel;
    AceDnsDatabase mAceDnsDatabase;
    public FragmentOne() {
        // Required empty public constructor
    }


    public static FragmentOne newInstance() {
        return new FragmentOne();
    }
    // TODO: Rename and change types and number of parameters
    public static FragmentOne newInstance(String param1, String param2) {
        FragmentOne fragment = new FragmentOne();
        Bundle args = new Bundle();
        args.putString(ARG_PARAM1, param1);
        args.putString(ARG_PARAM2, param2);
        fragment.setArguments(args);
        return fragment;
    }
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
//            customerType = getArguments().getString(ARG_PARAM1);
            customerType = "additional";
            mParam2 = getArguments().getString(ARG_PARAM2);
        }
        mContext = getActivity();
        viewModel = new ViewModelProvider(requireActivity()).get(SharedViewModel.class);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_one, container, false);

        return view;
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
//        customerSpinner =   view.findViewById(R.id.customerSpinner);
//        DealerSpinner =   view.findViewById(R.id.DealerSpinner);
        tv_dealer =   view.findViewById(R.id.tv_dealer);
        tv_route =   view.findViewById(R.id.tv_route);
        cart_badge_route =   view.findViewById(R.id.cart_badge_route);
        tv_customer =   view.findViewById(R.id.tv_customer);
        tv_customer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                if(Constants.selectedRouteadditional!=null)
                {
                    allAdditionalCustomersWithLogicRoute=new ArrayList<>();
                    Constants.allAdditionalCustomersWithLogic = mAceDnsDatabase.getallCustomerListWithRoute(rc,rds);
                    //Toast.makeText(getContext(),rc,Toast.LENGTH_LONG).show();

//                    for(int i=0;i< Constants.allAdditionalCustomersWithLogic.size();i++){
//                        String routeCode = Constants.allAdditionalCustomersWithLogic.get(i).getRouteCode();
//                        String rds = Constants.allAdditionalCustomersWithLogic.get(i).getRdsTag();
//                        //   if(Constants.selectedRouteadditional.getRoutecode().equalsIgnoreCase("all")){
//                        //       if(selectedAllRouteForCurrentDistributor.contains(routeCode) && Constants.selectedDistributoradditional.getCustomerCode().matches(rds)){
//
//                        //          allAdditionalCustomersWithLogicRoute.add(Constants.allAdditionalCustomersWithLogic.get(i));
//                        //      }
//                        //   }
//                        //   else{
//                        //       if(routeCode.matches(Constants.selectedRouteadditional.getRoutecode()) && Constants.selectedDistributoradditional.getCustomerCode().matches(rds)){
//                        allAdditionalCustomersWithLogicRoute.add(Constants.allAdditionalCustomersWithLogic.get(i));
//                        //       }
//                        //   }
//
//                    }

                    if(Constants.allAdditionalCustomersWithLogic.size() > 1)
                    {
                        openCustomerSelectionDialog(tv_customer);
                    }
                    else if(Constants.allAdditionalCustomersWithLogic.size() ==1){
                        setCustomerValue(0);
                    }
                }

//                if(customerType.toLowerCase().contains("recommended")){
//                    if(Constants.selectedRouteRecommended!=null){
//                        allRecommendedCustomersWithLogicRoute=new ArrayList<>();
//                        for(int i=0;i< Constants.allRecommendedCustomersWithLogic.size();i++){
//                            String routeCode = Constants.allRecommendedCustomersWithLogic.get(i).getRouteCode();
//                            String rds = Constants.allRecommendedCustomersWithLogic.get(i).getRdsTag();
//
//                            if(Constants.selectedRouteRecommended.getRoutecode().equalsIgnoreCase("all")){
//                                if(selectedAllRouteForCurrentDistributor.contains(routeCode) && Constants.selectedDistributorsRecommended.getCustomerCode().matches(rds)){
//
//                                    allRecommendedCustomersWithLogicRoute.add(Constants.allRecommendedCustomersWithLogic.get(i));
//                                }
//                            }
//                            else{
//                                if(routeCode.matches(Constants.selectedRouteRecommended.getRoutecode()) && Constants.selectedDistributorsRecommended.getCustomerCode().matches(rds)){
//
//                                    allRecommendedCustomersWithLogicRoute.add(Constants.allRecommendedCustomersWithLogic.get(i));
//                                }
//                            }
//
//                        }
//
//                        if(allRecommendedCustomersWithLogicRoute.size() > 1)
//                        {
//                            openCustomerSelectionDialog(tv_customer);
//                        }
//                        else if(allRecommendedCustomersWithLogicRoute.size() ==1){
//                            setCustomerValue(0);
//                        }
//                    }
//                }
//                else if(customerType.toLowerCase().contains("additional")){
//                    if(Constants.selectedRouteadditional!=null){
//                        allAdditionalCustomersWithLogicRoute=new ArrayList<>();
//                        Constants.allAdditionalCustomersWithLogic = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithRoute(rc,rds);
//                        //Toast.makeText(getContext(),rc,Toast.LENGTH_LONG).show();
//
//                        for(int i=0;i< Constants.allAdditionalCustomersWithLogic.size();i++){
//                            String routeCode = Constants.allAdditionalCustomersWithLogic.get(i).getRouteCode();
//                            String rds = Constants.allAdditionalCustomersWithLogic.get(i).getRdsTag();
//                         //   if(Constants.selectedRouteadditional.getRoutecode().equalsIgnoreCase("all")){
//                         //       if(selectedAllRouteForCurrentDistributor.contains(routeCode) && Constants.selectedDistributoradditional.getCustomerCode().matches(rds)){
//
//                          //          allAdditionalCustomersWithLogicRoute.add(Constants.allAdditionalCustomersWithLogic.get(i));
//                          //      }
//                         //   }
//                         //   else{
//                         //       if(routeCode.matches(Constants.selectedRouteadditional.getRoutecode()) && Constants.selectedDistributoradditional.getCustomerCode().matches(rds)){
//                                    allAdditionalCustomersWithLogicRoute.add(Constants.allAdditionalCustomersWithLogic.get(i));
//                         //       }
//                         //   }
//
//                        }
//
//                        if(allAdditionalCustomersWithLogicRoute.size() > 1)
//                        {
//                            openCustomerSelectionDialog(tv_customer);
//                        }
//                        else if(allAdditionalCustomersWithLogicRoute.size() ==1){
//                            setCustomerValue(0);
//                        }
//                    }
//                }
//                else {
//                    if(Constants.selectedRouteNew!=null){
//                        allNewCustomersForChosenRoute=new ArrayList<>();
//                        for(int i=0;i< Constants.allNewCustomers.size();i++){
//                            String routeCode = Constants.allNewCustomers.get(i).getRouteCode();
//                            String rds = Constants.allNewCustomers.get(i).getRdsTag();
//                            if(Constants.selectedRouteNew.getRoutecode().equalsIgnoreCase("all")){
//                                if(selectedAllRouteForCurrentDistributor.contains(routeCode) && Constants.selectedDistributorNew.getCustomerCode().matches(rds)){
//
//                                    allNewCustomersForChosenRoute.add(Constants.allNewCustomers.get(i));
//                                }
//                            }
//                            else{
//                                if(routeCode.matches(Constants.selectedRouteNew.getRoutecode()) && Constants.selectedDistributorNew.getCustomerCode().matches(rds)){
//                                    allNewCustomersForChosenRoute.add(Constants.allNewCustomers.get(i));
//                                }
//                            }
//
//                        }
//
//                        if(allNewCustomersForChosenRoute.size() > 1)
//                        {
//                            openCustomerSelectionDialog(tv_customer);
//                        }
//                        else if(allNewCustomersForChosenRoute.size() ==1){
//                            setCustomerValue(0);
//                        }
//                    }
//                }
            }
        });
        tv_route.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                if(Constants.selectedDistributoradditional!=null){
                    Constants.allAdditionalRoutesWithLogic=new ArrayList<>();
                    //Constants.allAdditionalRoutesWithLogic=mAceDnsDatabase.getPlanByCode(customerType);
                    Constants.allAdditionalRoutesWithLogic=mAceDnsDatabase.getRouteByDealerCode(Constants.selectedDistributoradditional.getCustomerCode());
                    if(allAdditionalRoutesWithLogic.size() > 1)
                    {
                        openRouteSelectionDialog(tv_route);
                    }
                    else if(allAdditionalRoutesWithLogic.size() ==1){
                        setRouteValue(0);
                    }
                }

//                if(customerType.toLowerCase().contains("recommended")){
//                    if(Constants.selectedDistributorsRecommended!=null){
//                        Constants.allRecommendedRoutesWithLogic=new ArrayList<>();
//                        Constants.allRecommendedRoutesWithLogic=mAceDnsDatabase.getPlanByCode(customerType);
//                        if(allRecommendedRoutesWithLogic.size() > 1)
//                        {
//                            openRouteSelectionDialog(tv_route);
//                        }
//                        else if(allRecommendedRoutesWithLogic.size() ==1){
//                            setRouteValue(0);
//                        }
//                    }
//                }
//                else if(customerType.toLowerCase().contains("additional")){
//                    if(Constants.selectedDistributoradditional!=null){
//                        Constants.allAdditionalRoutesWithLogic=new ArrayList<>();
//                        //Constants.allAdditionalRoutesWithLogic=mAceDnsDatabase.getPlanByCode(customerType);
//                        Constants.allAdditionalRoutesWithLogic=mAceDnsDatabase.getRouteByCode(customerType,Constants.selectedDistributoradditional.getCustomerCode());
//                        if(allAdditionalRoutesWithLogic.size() > 1)
//                        {
//                            openRouteSelectionDialog(tv_route);
//                        }
//                        else if(allAdditionalRoutesWithLogic.size() ==1){
//                            setRouteValue(0);
//                        }
//                    }
//                }
//                else if(customerType.toLowerCase().contains("new")){
//                    //allNewCustomersCode
//                    if(Constants.selectedDistributorNew!=null){ ;
//                        Constants.allNewRoutes=new ArrayList<>();
//                        Constants.allNewRoutes=mAceDnsDatabase.getPlanByCode(customerType);
//                        if(Constants.allNewRoutes.size() > 1)
//                        {
//                            openRouteSelectionDialog(tv_dealer);
//                        }
//                        else if(Constants.allNewRoutes.size() ==1){
//                            setRouteValue(0);
//                        }
//                    }
//                }
            }
        });
        tv_dealer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                allAdditionalDistributorsWithLogic = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithLogic(4);
                if(allAdditionalDistributorsWithLogic.size() > 1)
                {
                    openDealerSelectionDialog(tv_dealer);
                }
                else if(allAdditionalDistributorsWithLogic.size() ==1){
                    setDealerValue(0);
                }

//                if(customerType.toLowerCase().contains("recommended")){
//                    if(allRecommendedDistributorsWithLogic.size() > 1)
//                    {
//                        openDealerSelectionDialog(tv_dealer);
//                    }
//                    else if(allRecommendedDistributorsWithLogic.size() ==1){
//                        setDealerValue(0);
//                    }
//                }
//                else if(customerType.toLowerCase().contains("additional")){
//                    allAdditionalDistributorsWithLogic = mAceDnsDatabase.getRecommendedAdditionalCustomerListWithLogic(2);
//                    if(allAdditionalDistributorsWithLogic.size() > 1)
//                    {
//                        openDealerSelectionDialog(tv_dealer);
//                    }
//                    else if(allAdditionalDistributorsWithLogic.size() ==1){
//                        setDealerValue(0);
//                    }
//                }
//                else if(customerType.toLowerCase().contains("new")){
//                    if(Constants.allNewDistributors.size() > 1)
//                    {
//                        openDealerSelectionDialog(tv_dealer);
//                    }
//                    else if(Constants.allNewDistributors.size() ==1){
//                        setDealerValue(0);
//                    }
//                }
            }
        });
        final ArrayList<String> spinnerArray = new ArrayList<>();
        spinnerArray.add("Please Choose Customer");
        for (int i = 1; i < 6; i++) {
            spinnerArray.add(customerType +" Customer "+i);
        }
        final ArrayList<String> spinnerArrayD = new ArrayList<>();
        spinnerArrayD.add("Please Choose Distributor");
        for (int i = 1; i < 3; i++) {
            spinnerArrayD.add(customerType +" Distributor "+i);
        }
        //Assign view reference
        tabReports = view.findViewById(R.id.tabReports);
        mViewPager = view.findViewById(R.id.viewPagerOne);
        mViewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

            }

            @Override
            public void onPageSelected(int i) {
                Constants.CUSTOMER_INFO_REPORTS_FLAG=i;
            }
            @Override
            public void onPageScrollStateChanged(int state) {

            }
        });
        mViewPager.setOffscreenPageLimit(4);

        //Set up the view pager and fragments
         final List<String> fragmentTitleList = new ArrayList<>();
//        ViewPagerAdapter adapter2 = new ViewPagerAdapter(requireActivity());
        TabFragmentAdapter adapter2 = new TabFragmentAdapter(getChildFragmentManager());
        String [] customerInfoTAbListArr=Constants.userDetailsObj.getCI_customer_info_tab().split("#");
        if(customerInfoTAbListArr.length>0){
            for(int tabCount = 0; tabCount <customerInfoTAbListArr.length; tabCount++){
                String currentTab=customerInfoTAbListArr[tabCount];
                adapter2.addFragment(FragmentFocusProduct.newInstance(currentTab, customerType), currentTab);
                fragmentTitleList.add(currentTab);
            }

        }

        mViewPager.setAdapter(adapter2);
        tabReports.setupWithViewPager(mViewPager);

        super.onViewCreated(view, savedInstanceState);
    }
    public void openCustomerSelectionDialog(final TextView textView)
    {
        try
        {
            final PopupWindow popup = new PopupWindow(requireContext());
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
            final RecommendedAditionalCustomerSpinnerAdapter mAdapter;

            Collections.sort(Constants.allAdditionalCustomersWithLogic, new Comparator<CustomerDetails>() {
                public int compare(CustomerDetails v1, CustomerDetails v2) {
                    return v1.getCustomerName().compareTo(v2.getCustomerName());
                }
            });
            mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), Constants.allAdditionalCustomersWithLogic);


//            if(customerType.toLowerCase().contains("recommended")){
//                Collections.sort(allRecommendedCustomersWithLogicRoute, new Comparator<CustomerDetails>() {
//                    public int compare(CustomerDetails v1, CustomerDetails v2) {
//                        return v1.getCustomerName().compareTo(v2.getCustomerName());
//                    }
//                });
//                mAdapter = new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allRecommendedCustomersWithLogicRoute);
//            }
//            else if(customerType.toLowerCase().contains("additional")){
//                Collections.sort(allAdditionalCustomersWithLogicRoute, new Comparator<CustomerDetails>() {
//                    public int compare(CustomerDetails v1, CustomerDetails v2) {
//                        return v1.getCustomerName().compareTo(v2.getCustomerName());
//                    }
//                });
//                mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allAdditionalCustomersWithLogicRoute);
//            }
//            else{
//            Collections.sort(allNewCustomersForChosenRoute, new Comparator<CustomerDetails>() {
//                public int compare(CustomerDetails v1, CustomerDetails v2) {
//                    return v1.getCustomerName().compareTo(v2.getCustomerName());
//                }
//            });
//            mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allNewCustomersForChosenRoute);
//        }

            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(mAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    try
                    {
                        popup.dismiss();
                        setCustomerValue(i);

                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void openDealerSelectionDialog(final TextView textView)
    {
        try
        {
            final PopupWindow popup = new PopupWindow(requireContext());
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

            final RecommendedAditionalCustomerSpinnerAdapter mAdapter;
//            if(customerType.toLowerCase().contains("recommended")){
//                mAdapter = new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allRecommendedDistributorsWithLogic,true);
//            }
//            else if(customerType.toLowerCase().contains("additional")){
//                mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allAdditionalDistributorsWithLogic,true);
//            }
//            else{
//                mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allNewDistributors,true);
//            }
            mAdapter= new RecommendedAditionalCustomerSpinnerAdapter(requireContext(), allAdditionalDistributorsWithLogic,true);
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(mAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    try
                    {
                        popup.dismiss();
                        setDealerValue(i);

                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void openRouteSelectionDialog(final TextView textView)
    {
        try
        {
            final PopupWindow popup = new PopupWindow(requireContext());
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

            final RecommendedAditionalRouteSpinnerAdapter mAdapter;
            if(customerType.toLowerCase().contains("recommended")){
                mAdapter = new RecommendedAditionalRouteSpinnerAdapter(requireActivity(), allRecommendedRoutesWithLogic);
            }
             else if(customerType.toLowerCase().contains("additional")){
                mAdapter= new RecommendedAditionalRouteSpinnerAdapter(requireActivity(), allAdditionalRoutesWithLogic);
            }
            else {
                mAdapter= new RecommendedAditionalRouteSpinnerAdapter(requireActivity(), allNewRoutes);
            }
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(mAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    try
                    {
                        popup.dismiss();
                        setRouteValue(i);

                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void setCustomerValue(int pos) {
        String custClass;
        Constants.selectedCustomeradditional=Constants.allAdditionalCustomersWithLogic.get(pos);
        currwentCust=selectedCustomeradditional.getCustomerName();
        custClass=selectedCustomeradditional.getCustClass();


//        if(customerType.toLowerCase().contains("recommended")){
//            selectedCustomerRecommended=allRecommendedCustomersWithLogicRoute.get(pos);
//            currwentCust=selectedCustomerRecommended.getCustomerName();
//            custClass=selectedCustomerRecommended.getCustClass();
//        }
//        else if(customerType.toLowerCase().contains("additional")){
//            Constants.selectedCustomeradditional=allAdditionalCustomersWithLogicRoute.get(pos);
//            currwentCust=selectedCustomeradditional.getCustomerName();
////            Toast.makeText(getActivity(), ""+Constants.selectedCustomeradditional.getCustomerCode(), Toast.LENGTH_SHORT).show();
////            Constants.selectedCustomer.setCustomerCode(Constants.selectedCustomeradditional.getCustomerCode());
//            custClass=selectedCustomeradditional.getCustClass();
//        }
//        else{
//            Constants.selectedCustomerNew=allNewCustomersForChosenRoute.get(pos);
//            currwentCust=selectedCustomerNew.getCustomerName();
//            custClass=selectedCustomerNew.getCustClass();
//        }

        tv_customer.setText(currwentCust);
        if(custClass.equalsIgnoreCase("a")){
            tv_customer.setTextColor(getResources().getColor(R.color.red));
        }
        else if(custClass.equalsIgnoreCase("b")){
            tv_customer.setTextColor(getResources().getColor(R.color.colorOrangeAppGreyDark));
        }
        else if(custClass.equalsIgnoreCase("c")){
            tv_customer.setTextColor(getResources().getColor(R.color.text_color));
        }
            viewModel.selectItem(currwentCust);
            if(Utils.isNumeric(viewModel.getRefreshFlag().getValue())){
                if(viewModel.getRefreshFlag().getValue().equalsIgnoreCase("0")){
                    recommendedCustomer=currwentCust;
                }
                else  if(viewModel.getRefreshFlag().getValue().equalsIgnoreCase("1")){
                    additionalCustomer=currwentCust;
                }
                else  if(viewModel.getRefreshFlag().getValue().equalsIgnoreCase("2")){
                    newCustomer=currwentCust;
                }
            }
            ActivityOrderFilterAlternateDesign.customerName=currwentCust;
    }
    public void setDealerValue(int pos) {
        if(customerType.toLowerCase().contains("recommended")){
            Constants.selectedDistributorsRecommended= allRecommendedDistributorsWithLogic.get(pos);
            selectedCustomerRecommended=null;
            Constants.selectedRouteRecommended=null;
            tv_dealer.setText(Constants.selectedDistributorsRecommended.getCustomerName());
        }
        else if(customerType.toLowerCase().contains("additional")){
            Constants.selectedDistributoradditional= allAdditionalDistributorsWithLogic.get(pos);
            selectedCustomeradditional=null;
            Constants.selectedRouteadditional=null;
            tv_dealer.setText(Constants.selectedDistributoradditional.getCustomerName());
            rds=Constants.selectedDistributoradditional.getCustomerCode();
        }
        else if(customerType.toLowerCase().contains("new")){
            Constants.selectedDistributorNew= Constants.allNewDistributors.get(pos);
            selectedCustomerNew=null;
            Constants.selectedRouteNew=null;
            tv_dealer.setText(Constants.selectedDistributorNew.getCustomerName());
        }
        tv_customer.setText("");
        tv_route.setText("");
        cart_badge_route.setText("0");
    }
    public void setRouteValue(int pos)
    {

            Constants.selectedRouteadditional= allAdditionalRoutesWithLogic.get(pos);
            selectedCustomeradditional=null;

            String routeName = Constants.selectedRouteadditional.getRouteName();
            rc = Constants.selectedRouteadditional.getRoutecode();
            tv_route.setText(routeName);
            if(routeName.equalsIgnoreCase("all")){
                tv_route.setTextColor(Color.parseColor("#ffff8800"));
            }
            else{
                tv_route.setTextColor(Color.parseColor("#000000"));
            }
            cart_badge_route.setText(Constants.selectedRouteadditional.getcustomerCount());

//        if(customerType.toLowerCase().contains("recommended")){
//            Constants.selectedRouteRecommended= allRecommendedRoutesWithLogic.get(pos);
//            selectedCustomerRecommended=null;
//
//            String routeName = Constants.selectedRouteRecommended.getRouteName();
//            tv_route.setText(routeName);
//            if(routeName.equalsIgnoreCase("all")){
//                tv_route.setTextColor(Color.parseColor("#ffff8800"));
//            }
//            else{
//                tv_route.setTextColor(Color.parseColor("#000000"));
//            }
//
//            cart_badge_route.setText(Constants.selectedRouteRecommended.getcustomerCount());
//        }
//        else  if(customerType.toLowerCase().contains("additional")){
//            Constants.selectedRouteadditional= allAdditionalRoutesWithLogic.get(pos);
//            selectedCustomeradditional=null;
//
//            String routeName = Constants.selectedRouteadditional.getRouteName();
//            rc = Constants.selectedRouteadditional.getRoutecode();
//            tv_route.setText(routeName);
//            if(routeName.equalsIgnoreCase("all")){
//                tv_route.setTextColor(Color.parseColor("#ffff8800"));
//            }
//            else{
//                tv_route.setTextColor(Color.parseColor("#000000"));
//            }
//            cart_badge_route.setText(Constants.selectedRouteadditional.getcustomerCount());
//        }
//        else  if(customerType.toLowerCase().contains("new")){
//            Constants.selectedRouteNew= Constants.allNewRoutes.get(pos);
//            selectedCustomerNew=null;
//
//            String routeName = Constants.selectedRouteNew.getRouteName();
//            tv_route.setText(routeName);
//            if(routeName.equalsIgnoreCase("all")){
//                tv_route.setTextColor(Color.parseColor("#ffff8800"));
//            }
//            else{
//                tv_route.setTextColor(Color.parseColor("#000000"));
//            }
//            cart_badge_route.setText(Constants.selectedRouteNew.getcustomerCount());
//        }
        tv_customer.setText("");
    }
}