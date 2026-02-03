package com.forcepower.acedns.adapter;


import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OutstandingDetails;

import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class OutstandingAdapter extends ArrayAdapter<OutstandingDetails> {

    private final Context context;
    private final ArrayList<OutstandingDetails> nameValues;
    private final int resourceId;
    boolean showSelected;
    DecimalFormat formatter = new DecimalFormat("0.00");
    private ViewHolder viewHolder;

    public OutstandingAdapter(Context context, int resourceId, ArrayList<OutstandingDetails> nameValues, boolean showSelected) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.showSelected = showSelected;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
            viewHolder.invNo = (TextView) convertView.findViewById(R.id.txt_inv);
            viewHolder.date = (TextView) convertView.findViewById(R.id.txt_date);
            viewHolder.invAmt = (TextView) convertView.findViewById(R.id.txt_amt);
            viewHolder.dueAmt = (TextView) convertView.findViewById(R.id.txt_due);
            viewHolder.dueDays = (TextView) convertView.findViewById(R.id.txt_due_days);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (!showSelected) {
            if (position % 2 == 0) {
                viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
            } else {
                viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
            }
        }
        viewHolder.invNo.setText(nameValues.get(position).getInvoice_id());
        viewHolder.date.setText(nameValues.get(position).getDate());
        viewHolder.invAmt.setText(formatter.format(Float.parseFloat(nameValues.get(position).getInvoice_amount())));
        if (showSelected) {
            viewHolder.dueAmt.setText(formatter.format(Float.parseFloat(nameValues.get(position).getReceiptAmt())));
        } else {
            viewHolder.dueAmt.setText(formatter.format(Float.parseFloat(nameValues.get(position).getDue_amount())));
            try {
                viewHolder.dueDays.setText("" + ((new Date().getTime() - new SimpleDateFormat("yyyy-MM-dd").parse(nameValues.get(position).getDate()).getTime()) / (1000 * 60 * 60 * 24)));
            } catch (ParseException e) {
                System.out.println("Exception::::::::" + e);
                viewHolder.dueDays.setText("Unavailable");
            }
        }
        return convertView;
    }


    public class ViewHolder {
        TextView invNo, date, invAmt, dueAmt, dueDays;
        LinearLayout parentLayout;
    }
}