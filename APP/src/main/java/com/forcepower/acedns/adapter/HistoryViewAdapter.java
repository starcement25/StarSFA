package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;

import java.util.ArrayList;

public class HistoryViewAdapter extends ArrayAdapter<KeyValue> implements Filterable {

    private final Context context;
    private final ArrayList<KeyValue> mOrderReportDetailsList;
    private final int resourceId;
    private final int mShowColumnCount;
    private ViewHolder viewHolder;

    public HistoryViewAdapter(Context context, int resourceId, ArrayList<KeyValue> orderReportDetailsList,int mShowColumnCount ) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
        this.mShowColumnCount = mShowColumnCount;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (position == 0) {
        }
        if (convertView == null)
        {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.mTextViewBookedQtyCase = (TextView) convertView.findViewById(R.id.textViewBookedQtyCase);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);
            viewHolder.col4 = (TextView) convertView.findViewById(R.id.col4);
            viewHolder.col5 = (TextView) convertView.findViewById(R.id.col5);
            viewHolder.col6 = (TextView) convertView.findViewById(R.id.col6);
            viewHolder.col4_layout = (LinearLayout) convertView.findViewById(R.id.col4_layout);
            viewHolder.col5_layout = (LinearLayout) convertView.findViewById(R.id.col5_layout);
            viewHolder.col6_layout = (LinearLayout) convertView.findViewById(R.id.col6_layout);
            if(mShowColumnCount==6)
            {
                viewHolder.col4_layout.setVisibility(View.VISIBLE);
                viewHolder.col5_layout.setVisibility(View.VISIBLE);
                viewHolder.col6_layout.setVisibility(View.VISIBLE);
            }

            convertView.setTag(viewHolder);
        } else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getmShowColumn1());
        viewHolder.mTextViewBookedQtyCase.setText(mOrderReportDetailsList.get(position).getmShowColumn2());
        viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getmShowColumn3());
        if(mShowColumnCount==6)
        {
            viewHolder.col4.setText(mOrderReportDetailsList.get(position).getmShowColumn4());
            viewHolder.col5.setText(mOrderReportDetailsList.get(position).getmShowColumn5());
            viewHolder.col6.setText(mOrderReportDetailsList.get(position).getmmShowColumn6());
        }

        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;
        TextView col4;
        TextView col5;
        TextView col6;
        LinearLayout col4_layout,col5_layout,col6_layout;
    }


}