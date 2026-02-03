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
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class BargainReportAdapter extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public BargainReportAdapter(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList) {
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
            viewHolder.OutstandingAmountTv.setVisibility(View.GONE);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.customerNameTV.setText(mOrderReportDetailsList.get(position).getcustomerName());
        viewHolder.invoiceTv.setText(mOrderReportDetailsList.get(position).getTransactionId());

        viewHolder.invAmountTv.setText(mOrderReportDetailsList.get(position).getQuantity());
        String amount = mOrderReportDetailsList.get(position).getAmount();
        double amountInDouble = Double.parseDouble(amount);
        viewHolder.PaidAmountTv.setText(Constants.defaultFormatWithComma.format(amountInDouble));

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