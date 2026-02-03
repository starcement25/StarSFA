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
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.util.Utils;

import static com.forcepower.acedns.R.id.qtyTV;
import static com.forcepower.acedns.constants.Constants.CounterBidReportListListForRA;

public class CounterBidListReportAdapter extends ArrayAdapter<PlantProductWiseRARate> {

    private final Context mContext;
    private final int resourceId;
    private ViewHolder viewHolder;

    public CounterBidListReportAdapter(Context context, int resourceId) {
        super(context, resourceId, CounterBidReportListListForRA);
        this.mContext = context;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();

        viewHolder.dateTV = (TextView) convertView.findViewById(R.id.dateTV);
        viewHolder.productTV = (TextView) convertView.findViewById(R.id.productTV);
        viewHolder.qtyTV = (TextView) convertView.findViewById(qtyTV);
        viewHolder.bidRateTV = (TextView) convertView.findViewById(R.id.bidRateTV);
        viewHolder.counterBidStatusTV = (TextView) convertView.findViewById(R.id.counterBidStatusTV);
        viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parentLayout);

        convertView.setTag(viewHolder);

        PlantProductWiseRARate plantProductWiseRARateCurrentItem = CounterBidReportListListForRA.get(position);
        viewHolder.productTV.setText(plantProductWiseRARateCurrentItem.getprodName());
        String bidId = plantProductWiseRARateCurrentItem.getBidId();
        viewHolder.dateTV.setText(Utils.changeDateFormat("yyyyMMddhhmmss", "dd-MM-yyyy", bidId.substring(7, 21)) + "\n" + Utils.changeDateFormat("yyyyMMddhhmmss", "hh:mm:ss", bidId.substring(7, 21)));
        viewHolder.qtyTV.setText(plantProductWiseRARateCurrentItem.getQty());
        viewHolder.bidRateTV.setText(plantProductWiseRARateCurrentItem.getbidPrice());
        String counterBidStatus = plantProductWiseRARateCurrentItem.getCounterBidStatus().trim();

        if (counterBidStatus.equalsIgnoreCase("accept")) {
            counterBidStatus = "ACCEPT";
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#4a973a"));
        } else if (counterBidStatus.equalsIgnoreCase("reject")) {
            counterBidStatus = "REJECT";
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#e00000"));
        } else if (counterBidStatus.equalsIgnoreCase("") && plantProductWiseRARateCurrentItem.getCounterBid().equalsIgnoreCase("Y")) {
            counterBidStatus = "CB";
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#ffec40"));
        } else {
            counterBidStatus = "PENDING";
        }
        viewHolder.counterBidStatusTV.setText(counterBidStatus);

        return convertView;
    }

    public class ViewHolder {
        TextView dateTV, productTV, qtyTV, bidRateTV, counterBidStatusTV;
        LinearLayout parentLayout;
    }

}