package com.forcepower.acedns.activity;

import static android.view.View.GONE;

import androidx.fragment.app.FragmentActivity;

import android.app.Dialog;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerStockAdapter;
import com.forcepower.acedns.adapter.MenuStockCustomerAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.StockCustomerProduct;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class StockCustomerProductActivity extends FragmentActivity {

    AceDnsDatabase mAceDnsDatabase;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stock_customer_product);

        mAceDnsDatabase = new AceDnsDatabase(StockCustomerProductActivity.this);

        LinearLayout llSelectCustomer = (LinearLayout) findViewById(R.id.llSelectCustomer);

        llSelectCustomer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                chooseCustomerDialogForStockAudit();
            }
        });
    }
    public void backButtonClicked(View v){
        finish();
    }

    public void chooseCustomerDialogForStockAudit() {
        ArrayList<CustomerDetails> customerList = mAceDnsDatabase
                .getCustomerListStockAudit();
        if (customerList != null && customerList.size() > 0) {
            final CustomerStockAdapter adapterCustStock = new CustomerStockAdapter(
                    StockCustomerProductActivity.this, R.layout.multiple_cust_child_stock,
                    customerList);
            final Dialog custDialog = new Dialog(StockCustomerProductActivity.this,
                    R.style.PauseDialog);
            custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            custDialog.setContentView(R.layout.select_multiple_from_list_stock_audit);
            custDialog.setCancelable(false);
            TextView title = (TextView) custDialog.findViewById(R.id.title);
            title.setText("Please select party");

            RelativeLayout chkAllLayout = (RelativeLayout) custDialog
                    .findViewById(R.id.select_all_layout);
            chkAllLayout.setVisibility(GONE);
            final CheckBox chkSelectAll = (CheckBox) custDialog
                    .findViewById(R.id.chk_all);
            chkSelectAll.setVisibility(GONE);
            EditText searchText = (EditText) custDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    adapterCustStock.getFilter().filter(s.toString());
                    //Utils.showToast(mContext,s.toString()+"  "+arg2);

                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                }
            });
            final ListView dialogList = (ListView) custDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_SINGLE);
            dialogList.setAdapter(adapterCustStock);

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                    String selectedCodes = "";
                    String selectedNames = "";
                    CustomerDetails detailsObj = adapterCustStock
                            .getItem(position);
                    String selectedName = detailsObj
                            .getCustomerName();
                    String selectedCode = detailsObj
                            .getCustomerCode();
                    selectedCodes = selectedCodes + ""
                            + selectedCode + "";
                    selectedNames = selectedNames
                            + selectedName + "";

                    //Utils.showToast(mContext,selectedCodes);
                    stockListShow(selectedCodes,selectedNames);
                    custDialog.cancel();

                }
            });
            custDialog.show();
        } else {
            Utils.showToast(StockCustomerProductActivity.this, "No Outstanding Found");
        }
    }

    private void stockListShow(String c,String cname) {
        ArrayList<StockCustomerProduct> mrpList;
        mrpList = mAceDnsDatabase.getMenuStockProductList("" + c);
        final MenuStockCustomerAdapter adapter = new MenuStockCustomerAdapter(
                StockCustomerProductActivity.this, R.layout.menu_stk_mrp_child, mrpList, false);
        ListView dialogList = (ListView) findViewById(R.id.list);
        dialogList.setAdapter(adapter);
        TextView custname = (TextView) findViewById(R.id.custname);
        custname.setText(cname);
    }
}