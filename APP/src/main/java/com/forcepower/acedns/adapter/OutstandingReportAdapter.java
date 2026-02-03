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

public class OutstandingReportAdapter extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public OutstandingReportAdapter(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList) {
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
            viewHolder.customerNameTV = (TextView) convertView.findViewById(R.id.customerNameTV);
            viewHolder.invoiceTv = (TextView) convertView.findViewById(R.id.invoiceTv);
            viewHolder.invAmountTv = (TextView) convertView.findViewById(R.id.invAmountTv);
            viewHolder.PaidAmountTv = (TextView) convertView.findViewById(R.id.PaidAmountTv);
            viewHolder.OutstandingAmountTv = (TextView) convertView.findViewById(R.id.OutstandingAmountTv);


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        Double invoiceAmount = 0.0, paidAmount = 0.0, outstandingAmount = 0.0;
        viewHolder.customerNameTV.setText(mOrderReportDetailsList.get(position).getcustomerName());
        viewHolder.invoiceTv.setText(mOrderReportDetailsList.get(position).getTransactionId());

        if (Utils.isNumeric(mOrderReportDetailsList.get(position).getAmount())) {
            invoiceAmount = Double.parseDouble(mOrderReportDetailsList.get(position).getAmount());
        }
        if (Utils.isNumeric(mOrderReportDetailsList.get(position).getpaidAmount())) {
            paidAmount = Double.parseDouble(mOrderReportDetailsList.get(position).getpaidAmount());
        }
        outstandingAmount = invoiceAmount - paidAmount;
        viewHolder.invAmountTv.setText(defaultFormat.format(invoiceAmount));
        viewHolder.PaidAmountTv.setText(defaultFormat.format(paidAmount));
        viewHolder.OutstandingAmountTv.setText(defaultFormat.format(outstandingAmount));
        return convertView;
    }

    public class ViewHolder {
        TextView customerNameTV;
        TextView invoiceTv;
        TextView invAmountTv;
        TextView PaidAmountTv;
        TextView OutstandingAmountTv;
    }


}