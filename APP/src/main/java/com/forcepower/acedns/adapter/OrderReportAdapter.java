package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.defaultFormat;

public class OrderReportAdapter extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    private boolean isStock = false;

    public OrderReportAdapter(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList, boolean isStock) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
        this.isStock = isStock;
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
            viewHolder.textViewweitage = (TextView) convertView.findViewById(R.id.textViewweitage);
            viewHolder.col4_layout = (LinearLayout) convertView.findViewById(R.id.col4_layout);
            if (true == isStock) {
                viewHolder.mTextViewBookedQtyCase.setVisibility(View.INVISIBLE);
            }


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }


        String custType = mOrderReportDetailsList.get(position).getCustType();
        if(custType!=null && !custType.matches(""))
        {
            custType=", "+custType;
        }
        else
            {
                custType="";
            }
        viewHolder.mTextViewSku.setText(Html.fromHtml(mOrderReportDetailsList.get(position).getName()+"<font color='#F58322'>" + custType + "</font>"));
        viewHolder.mTextViewBookedQtyCase.setText(mOrderReportDetailsList.get(position).getQuantity());


        double amount = 0;
        if (mOrderReportDetailsList.get(position).getAmount().trim().length() > 0) {
            amount = Double.parseDouble(mOrderReportDetailsList.get(position).getAmount());
        }
        viewHolder.mTextViewBookedQtyTon.setText(defaultFormat.format(amount));

        if(R.layout.sku_child==resourceId){

            if(Constants.weightage.toUpperCase().matches("YES")) {

                viewHolder.col4_layout.setVisibility(View.VISIBLE);
                viewHolder.textViewweitage.setText(mOrderReportDetailsList.get(position).getWeightage());
            }else{
                viewHolder.col4_layout.setVisibility(View.GONE);
                viewHolder.textViewweitage.setVisibility(View.GONE);
            }
        }
        if(Constants.weightage.toUpperCase().matches("YES")) {
            viewHolder.textViewweitage.setText(mOrderReportDetailsList.get(position).getWeightage());
        }else{
            viewHolder.textViewweitage.setVisibility(View.GONE);
            viewHolder.col4_layout.setVisibility(View.GONE);
        }
        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;
        TextView textViewweitage;
        LinearLayout col4_layout;

    }


}