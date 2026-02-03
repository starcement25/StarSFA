package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class RetailerStockOutIemiListAdapter extends ArrayAdapter<BillingInformationStockSummaryData> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    private ArrayList<BillingInformationStockSummaryData> nameValuesProductListLocal;

    public RetailerStockOutIemiListAdapter(Context context, int resourceId, ArrayList<BillingInformationStockSummaryData> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
//    viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.etProdRate = (EditText) convertView.findViewById(R.id.etProdRate);
        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
        viewHolder.iemiTV = (TextView) convertView.findViewById(R.id.iemiTV);
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.schemeDetailsIcon = (ImageView) convertView.findViewById(R.id.schemeDetailsIcon);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        viewHolder.iemiTV.setVisibility(View.VISIBLE);
        viewHolder.convTV.setVisibility(View.GONE);
        viewHolder.etProdRate.setVisibility(View.GONE);
        viewHolder.list_details_ll.setVisibility(View.GONE);
        viewHolder.qtyET.setVisibility(View.GONE);
        convertView.setTag(viewHolder);

//    viewHolder.txtViewProductDesc.setText(nameValuesProductListLocalDo.get(position).getproductName());
        viewHolder.iemiTV.setText(nameValuesProductListLocal.get(position).getimei());
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }


        return convertView;
    }


    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, convTV, iemiTV;
        EditText qtyET, etProdRate;
        ImageView addtocartIV, schemeDetailsIcon;
        LinearLayout list_details_ll;
    }


    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view) {

            Constants.retailerStockOutProductListStockOut.add(nameValuesProductListLocal.get(pos));
            Constants.retailerStockOutProductIemiListByCustomer.remove(pos);
            notifyDataSetChanged();
            if (Constants.retailerStockOutProductIemiListByCustomer.size() <= 0) {
                Constants.retailerStockOutmasterDialog.dismiss();
            }
        }
    }

}