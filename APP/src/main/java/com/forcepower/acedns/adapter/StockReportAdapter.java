package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class StockReportAdapter extends ArrayAdapter<ProductMasterDetails> implements Filterable {
    private final Context context;
    private final ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    private final int resourceId;
    public Boolean isShowingDialog;
    private ViewHolder viewHolder;
    String customerOrProduct;
    public StockReportAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> orderReportDetailsList, Boolean isShowingDialog,String customerOrProduct) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
        this.customerOrProduct = customerOrProduct;
        this.isShowingDialog = isShowingDialog;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (position == 0) {
        }
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.custnameTV = (TextView) convertView.findViewById(R.id.custnameTV);
            viewHolder.OpeningStockTV = (TextView) convertView.findViewById(R.id.OpeningStockTV);
            viewHolder.billedQtyTv = (TextView) convertView.findViewById(R.id.billedQtyTv);
            viewHolder.stkOutQtyTv = (TextView) convertView.findViewById(R.id.stkOutQtyTv);
            viewHolder.stkOutQtyOthersTv = (TextView) convertView.findViewById(R.id.stkOutQtyOthersTv);
            viewHolder.closingStockTV = (TextView) convertView.findViewById(R.id.closingStockTV);
            viewHolder.invisibleProdCode = (TextView) convertView.findViewById(R.id.invisibleProdCode);
            viewHolder.invisibleProdCode2 = (TextView) convertView.findViewById(R.id.invisibleProdCode2);
            if(isShowingDialog)
            {
                viewHolder.billedQtyTv.setVisibility(View.GONE);
                viewHolder.stkOutQtyTv.setVisibility(View.GONE);
                viewHolder.stkOutQtyOthersTv.setVisibility(View.GONE);
                viewHolder.closingStockTV.setVisibility(View.GONE);
                viewHolder.custnameTV.setVisibility(View.GONE);
            }
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (isShowingDialog)
        {
            viewHolder.OpeningStockTV.setText(mOrderReportDetailsList.get(position).getIMEINo());
//            viewHolder.OpeningStockTV.setText(mOrderReportDetailsList.get(position).getallocationDate());
        }
        else
        {
            String openingStock = mOrderReportDetailsList.get(position).getOpeningStock();
            String billedQty = mOrderReportDetailsList.get(position).getbilledQty();
            String stkOutQty = mOrderReportDetailsList.get(position).getQty();
            String stkOutQtyUnreg = mOrderReportDetailsList.get(position).getunregisteredStockOut();

            if(customerOrProduct.equalsIgnoreCase("product"))
            {
                viewHolder.custnameTV.setText(mOrderReportDetailsList.get(position).getDesc());
                viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getProdCode());
                viewHolder.invisibleProdCode2.setText(mOrderReportDetailsList.get(position).getcustomerRds());
            }
            else
            {
                viewHolder.custnameTV.setText(mOrderReportDetailsList.get(position).getCustomerName());
                viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getcustomerCode());
            }

            viewHolder.OpeningStockTV.setText(openingStock);
            viewHolder.billedQtyTv.setText(billedQty);
            viewHolder.stkOutQtyTv.setText(stkOutQty);
            viewHolder.stkOutQtyOthersTv.setText(stkOutQtyUnreg);

            viewHolder.closingStockTV.setText(mOrderReportDetailsList.get(position).getClosingStk());

        }
        return convertView;
    }


    public class ViewHolder {
        TextView custnameTV, OpeningStockTV, billedQtyTv, stkOutQtyTv,stkOutQtyOthersTv,closingStockTV, invisibleProdCode,invisibleProdCode2;
    }
}


