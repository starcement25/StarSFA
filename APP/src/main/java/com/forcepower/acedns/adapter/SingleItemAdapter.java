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

public class SingleItemAdapter extends ArrayAdapter<KeyValue> implements Filterable {

    private final Context context;
    private final ArrayList<KeyValue> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public SingleItemAdapter(Context context, int resourceId, ArrayList<KeyValue> orderReportDetailsList ) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null)
        {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);

            viewHolder.col2_layout = (LinearLayout) convertView.findViewById(R.id.col2_layout);
            viewHolder.col3_layout = (LinearLayout) convertView.findViewById(R.id.col3_layout);
            viewHolder.col2_layout.setVisibility(View.GONE);
            viewHolder.col3_layout.setVisibility(View.GONE);
            convertView.setTag(viewHolder);
        } else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getValue()+"- "+mOrderReportDetailsList.get(position).getKey());

        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        LinearLayout col2_layout,col3_layout;
    }


}