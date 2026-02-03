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
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.defaultFormat;

public class SalesReportAdapter extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public SalesReportAdapter(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList) {
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
//			viewHolder.mTextViewSku 			= (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.textViewId = (TextView) convertView.findViewById(R.id.textViewId);
            viewHolder.mTextViewDate = (TextView) convertView.findViewById(R.id.textViewDate);
            viewHolder.mTextViewBookedQtyCase = (TextView) convertView.findViewById(R.id.textViewBookedQtyCase);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String amountPref = "";
        viewHolder.mTextViewDate.setText(Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mOrderReportDetailsList.get(position).getTransactionId().substring(14, 22)));
//		viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getName() );

        String type = mOrderReportDetailsList.get(position).getType();
        if (type.equalsIgnoreCase("sb") || type.equalsIgnoreCase("st")) {
            amountPref = "-";
        }
        viewHolder.mTextViewBookedQtyCase.setText(amountPref + defaultFormat.format(Double.parseDouble(mOrderReportDetailsList.get(position).getAmount())));
        viewHolder.mTextViewBookedQtyTon.setText(type);
        String d_Instruction = mOrderReportDetailsList.get(position).getremarks().trim();
        try {
            if (d_Instruction.contains(";")) {
                d_Instruction = d_Instruction.split(";")[0];
                if (d_Instruction.length() > 5) {
                    d_Instruction = d_Instruction.substring(0, 5);
                }
            }
        } catch (Exception e) {

        }

        viewHolder.textViewId.setText(d_Instruction);
        return convertView;
    }


    public class ViewHolder {
        TextView textViewId;
        TextView mTextViewDate;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;

    }


}