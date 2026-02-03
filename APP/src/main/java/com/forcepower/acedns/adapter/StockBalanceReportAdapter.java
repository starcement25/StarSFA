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

public class StockBalanceReportAdapter extends ArrayAdapter<ProductMasterDetails> implements Filterable {
    private final Context context;
    private final ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    private final int resourceId;
    public Boolean isShowingDialog;
    private ViewHolder viewHolder;


    public StockBalanceReportAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> orderReportDetailsList, Boolean isShowingDialog) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
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
            viewHolder.prodDescTV = (TextView) convertView.findViewById(R.id.prodDescTV);
            viewHolder.AllocQtyTV = (TextView) convertView.findViewById(R.id.AllocQtyTV);
            viewHolder.reqQtyTv = (TextView) convertView.findViewById(R.id.reqQtyTv);
            viewHolder.stkInQtyTv = (TextView) convertView.findViewById(R.id.stkInQtyTv);
            viewHolder.invisibleProdCode = (TextView) convertView.findViewById(R.id.invisibleProdCode);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (isShowingDialog) {
            viewHolder.prodDescTV.setText(mOrderReportDetailsList.get(position).getallocationDate());
        } else {
            viewHolder.prodDescTV.setText(mOrderReportDetailsList.get(position).getDesc());
        }

        viewHolder.AllocQtyTV.setText(mOrderReportDetailsList.get(position).getallocatedQty());
        viewHolder.reqQtyTv.setText(mOrderReportDetailsList.get(position).getQty());
        viewHolder.stkInQtyTv.setText(mOrderReportDetailsList.get(position).getbilledQty());
        viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getProdCode());
        return convertView;
    }


    public class ViewHolder {
        TextView prodDescTV, AllocQtyTV, reqQtyTv, stkInQtyTv, invisibleProdCode;
    }


}