package com.forcepower.acedns.new_activity.customer_outstanding;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingInvoiceNumberDataSet;
import com.forcepower.acedns.new_activity.customer_outstanding.adapter.CategoryFilterAdaptor;
import com.forcepower.acedns.new_activity.customer_outstanding.adapter.InvoiceDataAdapter;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.DataSet;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.InvoiceDataSet;

import java.util.ArrayList;
import android.graphics.Rect;

public class CustomerWiseOutstandingDetailsActivity extends AceDnsParentActivity {
    Context mContext;
    private RecyclerView horizontalFilterList,verticalDataList;

    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    ArrayList<DataSet> listData=new ArrayList<>();
    ArrayList<InvoiceDataSet> invoiceListData=new ArrayList<>();
    ArrayList<InvoiceDataSet> filterInvoiceListData=new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_customer_wise_outstanding_details);
        mContext = CustomerWiseOutstandingDetailsActivity.this;

        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);

        setDataList();
        init();
    }

    private void init() {
        Button backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(v -> finish());
        horizontalFilterList=findViewById(R.id.horizontalFilterList);
        horizontalFilterList.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false));
        horizontalFilterList.addItemDecoration(new RecyclerView.ItemDecoration() {
            @Override
            public void getItemOffsets(@NonNull Rect outRect, @NonNull View view, @NonNull RecyclerView parent, @NonNull RecyclerView.State state) {
                int position = parent.getChildAdapterPosition(view);
                assert parent.getAdapter() != null;
                if (position != parent.getAdapter().getItemCount() - 1) {
                    outRect.right = 12; // space in pixels between items
                }
            }
        });
        verticalDataList=findViewById(R.id.verticalDataList);
        verticalDataList.setLayoutManager(new LinearLayoutManager(this));

        DataSet obj=new DataSet(0,999,"All",true);
        DataSet obj1=new DataSet(0,3,"0 To 3 Days",false);
        DataSet obj2=new DataSet(4,7,"4 To 7 Days",false);
        DataSet obj3=new DataSet(8,17,"8 To 17 Days",false);
        DataSet obj4=new DataSet(18,25,"18 To 25 Days",false);
        DataSet obj5=new DataSet(26,30,"26 To 30 Days",false);
        DataSet obj6=new DataSet(31,45,"31 To 45 Days",false);
        DataSet obj7=new DataSet(46,60,"46 To 60 Days",false);
        DataSet obj8=new DataSet(61,90,"61 To 90 Days",false);
        DataSet obj9=new DataSet(91,999,">90 Days",false);

        listData.add(obj);
        listData.add(obj1);
        listData.add(obj2);
        listData.add(obj3);
        listData.add(obj4);
        listData.add(obj5);
        listData.add(obj6);
        listData.add(obj7);
        listData.add(obj8);
        listData.add(obj9);

        @SuppressLint("NotifyDataSetChanged") CategoryFilterAdaptor adapter=new CategoryFilterAdaptor(mContext,listData, (item, position) -> {
            for (DataSet data : listData) {
                data.setIsSelect(false);
            }
            listData.get(position).setIsSelect(true);
            assert horizontalFilterList.getAdapter() != null;
            horizontalFilterList.getAdapter().notifyDataSetChanged();

            filterInvoiceListData.clear();
            for(InvoiceDataSet data:invoiceListData){
                if(data.getInvoicePendingDays()<=listData.get(position).getEndDate() && data.getInvoicePendingDays()>=listData.get(position).getStartDate()){
                    filterInvoiceListData.add(data);
                }
            }
            assert verticalDataList.getAdapter() != null;
            verticalDataList.getAdapter().notifyDataSetChanged();
        });
        horizontalFilterList.setAdapter(adapter);

        InvoiceDataAdapter adapter1=new InvoiceDataAdapter(mContext,filterInvoiceListData,listData);
        verticalDataList.setAdapter(adapter1);
    }

    private void setDataList(){
        Log.d("TAG", "_DOWNLOAD_ CUSTOMER_CODE setDataList: "+getIntent().getStringExtra("customer_code") );
        ArrayList<CustomerAgeingInvoiceNumberDataSet> dataSet=mNewDatabaseForSiteLead.getCustomerAgeingInvoiceNumber(getIntent().getStringExtra("customer_code"));
        invoiceListData.clear();
        for(int i=0;i<dataSet.size();i++){
            Log.d("TAG", "_DOWNLOAD_ dataSet: "+i+":-"+  dataSet.get(i).getCustomerCode()+", Imvoice :-"+  dataSet.get(i).getInvoiceNo());
            InvoiceDataSet obj=new InvoiceDataSet(
                    dataSet.get(i).getInvoiceNo(),
                    dataSet.get(i).getInvoiceDate(),
                    dataSet.get(i).getInvoiceValue(),
                    Integer.parseInt(dataSet.get(i).getInvoiceAge())
            );
            invoiceListData.add(obj);
        }
        filterInvoiceListData.addAll(invoiceListData);
    }
}