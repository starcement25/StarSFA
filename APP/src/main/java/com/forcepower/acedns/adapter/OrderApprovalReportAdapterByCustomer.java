package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Html;
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

public class OrderApprovalReportAdapterByCustomer extends ArrayAdapter<OrderReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;


    public OrderApprovalReportAdapterByCustomer(Context context, int resourceId, ArrayList<OrderReportDetails> orderReportDetailsList) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {
        if (position == 0)
        {
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
            viewHolder.mTextViewBookedQtyCase.setVisibility(View.GONE);
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String color = "#73AC41";
        String getstatus = mOrderReportDetailsList.get(position).getstatus().trim().replace(" ","");

        if(getstatus.equalsIgnoreCase("cancel"))
        {
            color="#e7122B";
        }
        String formattedOrderDate = Utils.changeDateFormat("yyyy-MM-dd hh:mm:ss", "dd/MM/yyyy hh:mm:ss", mOrderReportDetailsList.get(position).getorder_date());

        viewHolder.mTextViewSku.setText(Html.fromHtml("<font color='" + color + "'>" +mOrderReportDetailsList.get(position).getappOrderNo()+"-"+formattedOrderDate+ "</font>"));
        if(getstatus.equalsIgnoreCase("modify"))
        {
            viewHolder.mTextViewBookedQtyCase.setText(Html.fromHtml("<font color='" + color + "'>"+mOrderReportDetailsList.get(position).getquantityChanged()+ "</font>"));
        }
        else
        {
            viewHolder.mTextViewBookedQtyCase.setText(Html.fromHtml("<font color='" + color + "'>"+mOrderReportDetailsList.get(position).getQuantity()+ "</font>"));
        }
        if(getstatus.equalsIgnoreCase("authorize") || getstatus.equalsIgnoreCase("modify"))
        {
            getstatus="Authorized";
        }
        else
        {
            getstatus="Cancel";
        }
        viewHolder.mTextViewBookedQtyTon.setText(Html.fromHtml("<font color='" + color + "'>"+ getstatus.trim().replace(" ","")+ "</font>"));
//        viewHolder.mTextViewSku.setText(Html.fromHtml(mOrderReportDetailsList.get(position).getName()+"<font color='#F58322'>" + custType + "</font>"));
        return convertView;
    }

    public class ViewHolder
    {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;
    }


}