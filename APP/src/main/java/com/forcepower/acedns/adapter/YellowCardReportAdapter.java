package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.YellowCard;

import java.util.ArrayList;

public class YellowCardReportAdapter extends ArrayAdapter<YellowCard> implements Filterable {

    private final Context context;
    private final ArrayList<YellowCard> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public YellowCardReportAdapter(Context context, int resourceId, ArrayList<YellowCard> orderReportDetailsList) {
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
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.mTextViewBookedQtyCase = (TextView) convertView.findViewById(R.id.textViewBookedQtyCase);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getchallan_no());
        viewHolder.mTextViewBookedQtyCase.setText(mOrderReportDetailsList.get(position).getchallan_date());
        viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getqty() + " " + mOrderReportDetailsList.get(position).getqty_UOM());
        return convertView;
    }

    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;
    }

}