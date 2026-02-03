package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

public class ProductMasterWithQtyInputAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        Constants.nameValuesProductList = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();
        String customerType = Constants.selectedCustomer.getCustomerType();
        String getMultipleRate = Constants.productDetailsObj.getMultipleRate();
        for (int i = 0; i < nameValues.size(); i++) {
            Constants.nameValuesProductList.get(i).setQty("NA");
            String prodCode = nameValues.get(i).getProdCode();
            String currentMonth = Constants.dateString.substring(4, 6);
            String plan = mAceDnsDatabase.getPlanOrPurchaseByCustomerAndMonth(Constants.selectedCustomer.getCustomerCode(), prodCode, currentMonth, "plan");
            if (plan.matches("")) {
                plan = "0";
            }
            String uom1 = nameValues.get(i).getUom1();
            String purchase = "0";
            if (!currentMonth.matches("04") || !currentMonth.matches("4")) {
                String previousMonth = calculatePreviousMonth(currentMonth);

                purchase = mAceDnsDatabase.getPlanOrPurchaseByCustomerAndMonth(Constants.selectedCustomer.getCustomerCode(), prodCode, previousMonth, "purchase");
                if (purchase.matches("")) {
                    purchase = "0";
                }
            }
            ArrayList<MRPDetails> mrpSpinnerList = mAceDnsDatabase.getMRPList(prodCode, "");
            if (mrpSpinnerList != null && mrpSpinnerList.size() > 0)
            {
                Constants.nameValuesProductList.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
                if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes"))
                {
                    Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getMrpValue());
                }
                else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))
                {

                    if (getMultipleRate.equalsIgnoreCase("yes"))
                    {
                        if (customerType.matches("R") || customerType.matches("")) {
                            Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getSaleRate());
                        } else if (customerType.matches("D")) {
                            Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getDistributorRate());
                        } else if (customerType.matches("WS")) {
                            Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getWSRate());
                        } else if (customerType.matches("SS")) {
                            Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getSSRate());
                        } else if (customerType.matches("DEPOT")) {
                            Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getDepotRate());
                        }

                    }
                    else
                        {
                        Constants.nameValuesProductList.get(i).setMrpValue(mrpSpinnerList.get(0).getSaleRate());
                    }
                }

            } else {
                Constants.nameValuesProductList.get(i).setMrpCode("0");
                Constants.nameValuesProductList.get(i).setMrpValue("0");
            }
            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                Constants.nameValuesProductList.get(i).setMrpValue("NA");
            }
            {

            }
            Constants.nameValuesProductList.get(i).setPlan(plan + " " + uom1);
            Constants.nameValuesProductList.get(i).setPurchase(purchase + " " + uom1);

        }


    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);

        viewHolder.tvLastMonthPurchase = (TextView) convertView.findViewById(R.id.tv_last_month_purchase);
        viewHolder.tvOrderPlan = (TextView) convertView.findViewById(R.id.tv_order_plan);
        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.etProdRate = (EditText) convertView.findViewById(R.id.etProdRate);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);

        convertView.setTag(viewHolder);


        String menuItem = Constants.nameValuesProductList.get(position).getDesc();
        String plan = Constants.nameValuesProductList.get(position).getPlan();
        String purchase = Constants.nameValuesProductList.get(position).getPurchase();
        viewHolder.txtViewProductDesc.setText(menuItem);
        CharSequence text = viewHolder.invisibleTVProdCode.getText();
        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
            if (Constants.nameValuesProductList.get(position).getFocus().equalsIgnoreCase("y")) {
                viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
            }
            if (text == null || text.equals("")) {
                viewHolder.etProdRate.setVisibility(View.VISIBLE);
                viewHolder.etProdRate.addTextChangedListener(new GenericTextWatcherRate(position));
            }
        } else {
            viewHolder.tvOrderPlan.setVisibility(View.VISIBLE);
            viewHolder.tvLastMonthPurchase.setVisibility(View.VISIBLE);
            viewHolder.tvOrderPlan.setText(plan);
            viewHolder.tvLastMonthPurchase.setText(purchase);
        }

        if (text == null || text.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
        }
        String prodCode = Constants.nameValuesProductList.get(position).getProdCode();


        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = Constants.nameValuesProductList.get(position).getQty();
        String currentMrp = Constants.nameValuesProductList.get(position).getMrpValue();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
        if (!currentMrp.matches("NA"))
            viewHolder.etProdRate.setText(currentMrp);



        return convertView;
    }

    private String calculatePreviousMonth(String currentMonth) {
        String previousMonth = "01";
        int currentMonthInInt = Integer.parseInt(currentMonth);
        if (currentMonthInInt == 1) {
            previousMonth = "12";
        } else {
            currentMonthInInt = currentMonthInInt - 1;
            previousMonth = String.format("%02d", currentMonthInInt);
        }
        return previousMonth;
    }


    public class ViewHolder {
        TextView txtViewProductDesc, tvLastMonthPurchase, tvOrderPlan, invisibleTVProdCode,weigthage;
        EditText qtyET, etProdRate;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;

        private GenericTextWatcherQty(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            Constants.nameValuesProductList.get(pos).setQty(text);
        }
    }

    private class GenericTextWatcherRate implements TextWatcher {

        private int pos;

        private GenericTextWatcherRate(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            Constants.nameValuesProductList.get(pos).setMrpValue(text);
        }
    }

    private class GenericEditorActionListener implements TextView.OnEditorActionListener {
        EditText tv;
        private int pos;

        private GenericEditorActionListener(int pos, EditText tv) {
            this.pos = pos;
            this.tv = tv;
        }

        @Override
        public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {

            if (actionId == EditorInfo.IME_ACTION_DONE) {
                if (sizeOfList - (pos + 1) > 4) {
                    prodQtyRateListView.smoothScrollToPosition(pos + 1 + 5);
                } else if (pos + 1 == sizeOfList) {
                    Toast.makeText(context, "End of List", Toast.LENGTH_SHORT).show();
                } else {
                    prodQtyRateListView.smoothScrollToPosition(sizeOfList);
                }


                return true;
            }
            return false;
        }
    }
}