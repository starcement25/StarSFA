package com.forcepower.acedns.new_activity.customer_outstanding;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingInvoiceNumberDataSet;
import com.forcepower.acedns.newDataBase.sync.DataForDownloadingCustomerAgeing;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.customer_outstanding.adapter.AgeWiseOutstandingAdapter;
import com.forcepower.acedns.new_activity.customer_outstanding.adapter.CategoryAdapter;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.CategoryDataSet;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.GraphDataSet;
import com.forcepower.acedns.new_activity.target_achievement.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;

import java.util.ArrayList;
import java.util.Objects;

public class CustomerWiseOutstandingActivity extends AceDnsParentActivity {
    Context mContext;
    private Button backButton,syncButton;
    private LinearLayout selectCustomerLayoutButton;
    private LinearLayout outstandingLayout;
    private TextView selectCustomerText;
    private RecyclerView ageWiseOutstandingDataList;
    private GridView gridMenu;

    AceDnsDatabase mAceDnsDatabase;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    DataForDownloadingCustomerAgeing mDataForDownloadingCustomerAgeing;
    ArrayList<GraphDataSet> graphDataSet=new ArrayList<>();
    ArrayList<CategoryDataSet> categoryDataSet=new ArrayList<>();
    ArrayList<DataSet> customerList = new ArrayList<>();
    ArrayList<CustomerAgeingDataSet> mCustomerAgeingDataSet=new ArrayList<>();
    ArrayList<CustomerAgeingInvoiceNumberDataSet> mCustomerAgeingInvoiceNumberDataSet=new ArrayList<>();
    String customerCode="";
    ProgressDialog mProgressDialogAgeing;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_customer_wise_outstanding);
        mContext = CustomerWiseOutstandingActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        mDataForDownloadingCustomerAgeing=new DataForDownloadingCustomerAgeing(mContext);
        customerList.clear();
        customerList=mNewDatabaseForSiteLead.getCustomerAgeingList();
        init();
    }

    @Override
    public void onClick(View v) {
        if(v==backButton){
            finish();
        }
        if(v==selectCustomerLayoutButton){
            show_list_data_dialog(customerList,  "Select Customer");
        }
        if(v==syncButton){
            mProgressDialogAgeing = new ProgressDialog(mContext);
            mProgressDialogAgeing.setMessage("Downloading Data ...");
            mProgressDialogAgeing.show();
            mDataForDownloadingCustomerAgeing.addAllFormDataForCustomerAgeing(success-> {
                mProgressDialogAgeing.dismiss();
                customerList.clear();
                customerList=mNewDatabaseForSiteLead.getCustomerAgeingList();
            });
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        selectCustomerLayoutButton=findViewById(R.id.selectCustomerLayoutButton);
        selectCustomerText=findViewById(R.id.selectCustomerText);
        outstandingLayout=findViewById(R.id.outstandingLayout);
        outstandingLayout.setVisibility(GONE);
        ageWiseOutstandingDataList=findViewById(R.id.ageWiseOutstandingDataList);
        ageWiseOutstandingDataList.setLayoutManager(new LinearLayoutManager(this));
        gridMenu=findViewById(R.id.gridMenu);
        syncButton=findViewById(R.id.syncButton);

        backButton.setOnClickListener(this);
        selectCustomerLayoutButton.setOnClickListener(this);
        syncButton.setOnClickListener(this);
    }
    @SuppressLint("DefaultLocale")
    private void loadDummyData(String customerCode) {
        try {
            mCustomerAgeingDataSet=mNewDatabaseForSiteLead.getCustomerAgeing(customerCode);
            mCustomerAgeingInvoiceNumberDataSet=mNewDatabaseForSiteLead.getCustomerAgeingInvoiceNumber(customerCode);

            int total = parseValue(mCustomerAgeingDataSet.get(0).getValue1())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue2())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue3())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue4())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue5())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue6())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue7())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue8())
                    + parseValue(mCustomerAgeingDataSet.get(0).getValue9());

            String percentage1 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue1()) * 100f) / total);
            String percentage2 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue2()) * 100f) / total);
            String percentage3 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue3()) * 100f) / total);
            String percentage4 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue4()) * 100f) / total);
            String percentage5 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue5()) * 100f) / total);
            String percentage6 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue6()) * 100f) / total);
            String percentage7 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue7()) * 100f) / total);
            String percentage8 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue8()) * 100f) / total);
            String percentage9 = total==0?"0%":String.format("%05.2f%%", (parseValue(mCustomerAgeingDataSet.get(0).getValue9()) * 100f) / total);


            GraphDataSet obj1=new GraphDataSet(
                    "#2ECC71",
                    "0 To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle1().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue1())),
                    percentage1
            );
            GraphDataSet obj2=new GraphDataSet(
                    "#73D467",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle1().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle2().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue2())),
                    percentage2
            );
            GraphDataSet obj3=new GraphDataSet(
                    "#B8DF46",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle2().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle3().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue3())),
                    percentage3
            );
            GraphDataSet obj4=new GraphDataSet(
                    "#F4C430",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle3().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle4().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue4())),
                    percentage4
            );
            GraphDataSet obj5=new GraphDataSet(
                    "#F4A020",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle4().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle5().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue5())),
                    percentage5
            );
            GraphDataSet obj6=new GraphDataSet(
                    "#F47C20",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle5().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle6().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue6())),
                    percentage6
            );
            GraphDataSet obj7=new GraphDataSet(
                    "#F05050",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle6().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle7().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue7())),
                    percentage7
            );
            GraphDataSet obj8=new GraphDataSet(
                    "#D63030",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle7().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle8().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue8())),
                    percentage8
            );
            GraphDataSet obj9=new GraphDataSet(
                    "#8B0000",
                    "+"+(Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle8().replace("Day","")))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue9())),
                    percentage9
            );
            graphDataSet.clear();
            graphDataSet.add(obj1);
            graphDataSet.add(obj2);
            graphDataSet.add(obj3);
            graphDataSet.add(obj4);
            graphDataSet.add(obj5);
            graphDataSet.add(obj6);
            graphDataSet.add(obj7);
            graphDataSet.add(obj8);
            graphDataSet.add(obj9);

            CategoryDataSet obj11=new CategoryDataSet(
                    "#2ECC71",
                    "0 To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle1().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue1())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount1()
            );
            CategoryDataSet obj12=new CategoryDataSet(
                    "#73D467",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle1().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle2().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue2())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount2()
            );
            CategoryDataSet obj13=new CategoryDataSet(
                    "#B8DF46",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle2().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle3().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue3())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount3()
            );
            CategoryDataSet obj14=new CategoryDataSet(
                    "#F4C430",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle3().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle4().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue4())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount4()
            );
            CategoryDataSet obj15=new CategoryDataSet(
                    "#F4A020",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle4().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle5().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue5())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount5()
            );
            CategoryDataSet obj16=new CategoryDataSet(
                    "#F47C20",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle5().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle6().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue6())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount6()
            );
            CategoryDataSet obj17=new CategoryDataSet(
                    "#F05050",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle6().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle7().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue7())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount7()
            );
            CategoryDataSet obj18=new CategoryDataSet(
                    "#D63030",
                    (Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle7().replace("Day",""))+1)+" To "+Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle8().replace("Day",""))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue8())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount8()
            );
            CategoryDataSet obj19=new CategoryDataSet(
                    "#8B0000",
                    "+"+(Integer.parseInt(mCustomerAgeingDataSet.get(0).getTitle8().replace("Day","")))+" Days",
                    String.format("%.2f",Double.parseDouble(mCustomerAgeingDataSet.get(0).getValue9())),
                    mCustomerAgeingDataSet.get(0).getInvoiceCount9()
            );

            categoryDataSet.clear();
            categoryDataSet.add(obj11);
            categoryDataSet.add(obj12);
            categoryDataSet.add(obj13);
            categoryDataSet.add(obj14);
            categoryDataSet.add(obj15);
            categoryDataSet.add(obj16);
            categoryDataSet.add(obj17);
            categoryDataSet.add(obj18);
            categoryDataSet.add(obj19);


            AgeWiseOutstandingAdapter adapter=new AgeWiseOutstandingAdapter(mContext,graphDataSet);
            ageWiseOutstandingDataList.setAdapter(adapter);

            CategoryAdapter mMenuAdapter = new CategoryAdapter(CustomerWiseOutstandingActivity.this, R.layout.grid_item_child, categoryDataSet);
            gridMenu.setAdapter(mMenuAdapter);
            gridMenu.post(() -> setGridViewHeightBasedOnChildren(gridMenu));

//            gridMenu.setOnItemClickListener((parent, view, position, id) -> {
//                CategoryDataSet selected = categoryDataSet.get(position);
//                Intent intent = new Intent(mContext, CustomerWiseOutstandingDetailsActivity.class);
//                intent.putExtra("customer_code", customerCode);
//                intent.putExtra("color", selected.getColorCode());
//                intent.putExtra("label", selected.getTitle());
//                intent.putExtra("amount", selected.getAmount());
//                intent.putExtra("count", selected.getInvoiceCount());
//                startActivity(intent);
//            });
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ loadDummyData: "+e.getMessage());
        }

    }
    private int parseValue(String value) {
        try {
            return (int) Double.parseDouble(value);
        } catch (Exception e) {
            return 0;
        }
    }
    public void show_list_data_dialog(ArrayList<DataSet> dataSet, String titleValue) {
        try {
            final ShowDataSetAdapter adapterCust = new ShowDataSetAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                DataSet selected = adapterCust.getItem(position);
                selectCustomerText.setText(Objects.requireNonNull(selected).getValue());
                customerCode=selected.getId();
                loadDummyData(selected.getId());
                outstandingLayout.setVisibility(VISIBLE);
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    private void setGridViewHeightBasedOnChildren(GridView gridView) {
        if (gridView.getAdapter() == null) return;
        int totalItems = gridView.getAdapter().getCount();
        int rows = (int) Math.ceil((double) totalItems / 3);

        gridView.measure(View.MeasureSpec.makeMeasureSpec(gridView.getWidth(), View.MeasureSpec.EXACTLY),
                View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED));

        int itemHeight = 0;
        if (gridView.getAdapter().getCount() > 0) {
            View item = gridView.getAdapter().getView(0, null, gridView);
            item.measure(0, 0);
            itemHeight = item.getMeasuredHeight();
        }

        LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) gridView.getLayoutParams();
        params.height = (itemHeight * rows) + (gridView.getVerticalSpacing() * (rows - 1));
        gridView.setLayoutParams(params);
        gridView.requestLayout();
    }

}