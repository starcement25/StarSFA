package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.bean.MarketFeedbackStockAudit;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class MarketFeedbackStockAuditAdapter extends ArrayAdapter<MarketFeedbackStockAudit> {

    private final Context context;
    private final ArrayList<MarketFeedbackStockAudit> mMarketFeedbackStockAuditList;
    private final int resourceId;
    private ViewHolder viewHolder;
    String menuType="";

    public MarketFeedbackStockAuditAdapter(Context context, int resourceId, ArrayList<MarketFeedbackStockAudit> nameValues,String menuType) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.mMarketFeedbackStockAuditList = nameValues;
        this.resourceId = resourceId;
        this.menuType = menuType;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
            viewHolder.textViewSerialNo = (TextView) convertView.findViewById(R.id.txt_sl);
            viewHolder.textViewCompetitorName = (TextView) convertView.findViewById(R.id.txt_name);
            viewHolder.textViewQuantity = (TextView) convertView.findViewById(R.id.txt_qty);
            viewHolder.textViewSchemeDiscount = (TextView) convertView.findViewById(R.id.txt_discount);
            viewHolder.txt_price = (TextView) convertView.findViewById(R.id.txt_price);

if(menuType.equalsIgnoreCase("wsp") || menuType.equalsIgnoreCase("rsp")){
    viewHolder.txt_price.setVisibility(View.VISIBLE);
}
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (position % 2 == 0) {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }

        viewHolder.textViewSerialNo.setText("" + (position + 1) + ".");
        viewHolder.textViewCompetitorName.setText(mMarketFeedbackStockAuditList.get(position).getCompetitorName());
        if(menuType.equalsIgnoreCase("wsp") || menuType.equalsIgnoreCase("rsp")){
            viewHolder.txt_price.setText((mMarketFeedbackStockAuditList.get(position).getprice())+" RS/Bag");
        }
        /*if(Constants.menuDetailsObj.getMf_mandatory_details().toLowerCase().contains("yes"))
        {
            viewHolder.txt_price.setText((mMarketFeedbackStockAuditList.get(position).getprice())+" RS/Bag");
        }*/
        viewHolder.textViewQuantity.setText(mMarketFeedbackStockAuditList.get(position).getQuantity());
        viewHolder.textViewSchemeDiscount.setText(mMarketFeedbackStockAuditList.get(position).getDiscount());
        return convertView;
    }


    public class ViewHolder {
        TextView textViewSerialNo, textViewCompetitorName, textViewQuantity, textViewSchemeDiscount,txt_price;
        LinearLayout parentLayout;
    }
}