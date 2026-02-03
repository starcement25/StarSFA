package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CardTransactionDetails;

import java.text.DecimalFormat;
import java.util.ArrayList;

public class LoyaltyTransAdapter extends ArrayAdapter<CardTransactionDetails> implements Filterable {

    private final Context context;
    private final ArrayList<CardTransactionDetails> nameValues;
    private final int resourceId;
    String mode = "";
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    private ViewHolder viewHolder;

    public LoyaltyTransAdapter(Context context, int resourceId, ArrayList<CardTransactionDetails> nameValues, String mode) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.mode = mode;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtDate = (TextView) convertView.findViewById(R.id.txt_col1);
            viewHolder.txtTrans = (TextView) convertView.findViewById(R.id.txt_col2);
            viewHolder.txtVal = (TextView) convertView.findViewById(R.id.txt_col3);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtDate.setText(getDate(nameValues.get(position).getTransactionId()));
        viewHolder.txtTrans.setText(nameValues.get(position).getVerticalName());
        if (mode.equalsIgnoreCase("purchase")) {
            viewHolder.txtVal.setText("Rs" + defaultFormat.format(Double.parseDouble(nameValues.get(position).getPurchaseValue())));
        } else if (mode.equalsIgnoreCase("reward")) {
            viewHolder.txtVal.setText(defaultFormat.format(Double.parseDouble(nameValues.get(position).getPointsEarned())));
        } else if (mode.equalsIgnoreCase("redeemed")) {
            viewHolder.txtVal.setText(defaultFormat.format(Double.parseDouble(nameValues.get(position).getPointsRedeemed())));
        }
        return convertView;
    }

    public String getDate(String transId) {
        String date = "";
        String yrStr = transId.substring(6, 10);
        String mnthStr = transId.substring(10, 12);
        String daStr = transId.substring(12, 14);
        date = daStr + "-" + mnthStr + "-" + yrStr;
        return date;
    }

    public class ViewHolder {
        TextView txtDate, txtTrans, txtVal;
    }
}