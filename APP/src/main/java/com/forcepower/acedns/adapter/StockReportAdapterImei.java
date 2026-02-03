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

public class StockReportAdapterImei extends ArrayAdapter<ProductMasterDetails> implements Filterable {
    private final Context context;
    private final ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    public StockReportAdapterImei(Context context, int resourceId, ArrayList<ProductMasterDetails> orderReportDetailsList) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
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
            viewHolder.OpeningStockTV = (TextView) convertView.findViewById(R.id.OpeningStockTV);

            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.OpeningStockTV.setText(mOrderReportDetailsList.get(position).getIMEINo());

        return convertView;
    }


    public class ViewHolder {
        TextView  OpeningStockTV;
    }
}


