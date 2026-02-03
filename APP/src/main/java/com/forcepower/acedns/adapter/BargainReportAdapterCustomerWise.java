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
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class BargainReportAdapterCustomerWise extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public BargainReportAdapterCustomerWise(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList) {
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
            viewHolder.bargainNo = (TextView) convertView.findViewById(R.id.textViewCustomer);
            viewHolder.textViewDate = (TextView) convertView.findViewById(R.id.textViewBargain);
            viewHolder.textViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.textViewQty = (TextView) convertView.findViewById(R.id.textViewQty);
            viewHolder.textViewAmount = (TextView) convertView.findViewById(R.id.textViewAmount);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String transactionId = mOrderReportDetailsList.get(position).getTransactionId();
        viewHolder.bargainNo.setText(transactionId);
        String substringTrsnId = transactionId.substring(7);
        String dateInString= Utils.changeDateFormat("yyyyMMddhhmmss","dd/MM/yyyy hh:mm:ss", substringTrsnId);
        viewHolder.textViewDate.setText(dateInString);
        viewHolder.textViewSku.setText(mOrderReportDetailsList.get(position).getdesc());
        viewHolder.textViewQty.setText(mOrderReportDetailsList.get(position).getQuantity());
        viewHolder.textViewAmount.setText(Constants.defaultFormat.format(Double.parseDouble(mOrderReportDetailsList.get(position).getAmount())));
        return convertView;
    }

    public class ViewHolder {
        TextView bargainNo;
        TextView textViewDate;
        TextView textViewSku;
        TextView textViewQty;
        TextView textViewAmount;
    }

}