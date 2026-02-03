package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OrderDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class LedgerReportAdapter extends ArrayAdapter<OrderDetails> implements Filterable {
    private final Context context;
    private final ArrayList<OrderDetails> mOrderReportDetailsList;
    private final int resourceId;
    public Boolean isShowingDialog;
    private ViewHolder viewHolder;

    public LedgerReportAdapter(Context context, int resourceId, ArrayList<OrderDetails> orderReportDetailsList, Boolean isShowingDialog) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
        this.isShowingDialog = isShowingDialog;
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
            viewHolder.dateTimeTV = convertView.findViewById(R.id.prodDescTV);
            viewHolder.amountTV = convertView.findViewById(R.id.AllocQtyTV);
            viewHolder.typeTV = convertView.findViewById(R.id.reqQtyTv);
            viewHolder.stkInQtyTv = convertView.findViewById(R.id.stkInQtyTv);
            viewHolder.stkOutQtyTv = convertView.findViewById(R.id.stkOutQtyTv);
            viewHolder.stkInQtyTv.setVisibility(View.GONE);
            viewHolder.stkOutQtyTv.setVisibility(View.GONE);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        String orderNo = mOrderReportDetailsList.get(position).getOrderNo();
        String substringDate = orderNo.substring(6, 14);
        String substringTime = "";
        if (orderNo.length() == 28) {
            substringTime = orderNo.substring(22);
        } else {
            substringTime = orderNo.substring(14);
        }

        viewHolder.dateTimeTV.setText(Utils.changeDateFormat("yyyyMMddhhmmss", "dd-MM-yyyy hh:mm:ss", substringDate + substringTime));
        String amountPrefix = "";
        String type = mOrderReportDetailsList.get(position).getType();
        if (type.equalsIgnoreCase("p")) {
            amountPrefix = "-";
        }
        String amount = mOrderReportDetailsList.get(position).getAmount();
        Double amountInDouble = 0.00;
        if (Utils.isNumeric(amount)) {
            amountInDouble = Double.valueOf(amount);
        }
        viewHolder.amountTV.setText(amountPrefix + Constants.defaultFormat.format(amountInDouble));
        viewHolder.typeTV.setText(type);

        return convertView;
    }


    public class ViewHolder {
        TextView dateTimeTV, amountTV, typeTV, stkInQtyTv, stkOutQtyTv;
    }

}