package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OrderReportDetails;

import java.util.ArrayList;

public class CheckInOutReportAdapter extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    private boolean isStock = false;

    public CheckInOutReportAdapter(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList, boolean isStock) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
        this.isStock = isStock;
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
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.mTextViewBookedQtyCase = (TextView) convertView.findViewById(R.id.textViewBookedQtyCase);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }


        viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getName());
        viewHolder.mTextViewBookedQtyCase.setText(mOrderReportDetailsList.get(position).getAmount());
        viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getQuantity());


        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;

    }


}