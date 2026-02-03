package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;


public class CompetitorColorAdapter extends ArrayAdapter<MarketFeedbackStockAudit> implements Filterable {

    private final Context context;
    private final ArrayList<MarketFeedbackStockAudit> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public CompetitorColorAdapter(Context context, int resourceId, ArrayList<MarketFeedbackStockAudit> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.label);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getCompetitorName().toString();
        if (menuItem != null && menuItem.length() > 0 && !menuItem.equalsIgnoreCase(" ")) {
            viewHolder.txtView.setText(menuItem);
            if (Constants.productType.get(position).equalsIgnoreCase("MANDATORY STAR")) {
                viewHolder.txtView.setTextColor(Color.RED);
                viewHolder.txtView.setTypeface(null, Typeface.BOLD);
            } else if (Constants.productType.get(position).equalsIgnoreCase("OTHER COMPETITOR")) {
                viewHolder.txtView.setTextColor(Color.BLACK);
                viewHolder.txtView.setTypeface(null, Typeface.NORMAL);
            } else if (Constants.productType.get(position).equalsIgnoreCase("OPTIONAL STAR")) {
                viewHolder.txtView.setTextColor(Color.BLACK);
                viewHolder.txtView.setTypeface(null, Typeface.NORMAL);
            } else if (Constants.productType.get(position).equalsIgnoreCase("BENCHMARK COMPETITOR")) {
                viewHolder.txtView.setTextColor(Color.BLUE);
                viewHolder.txtView.setTypeface(null, Typeface.BOLD);
            } else {
                //viewHolder.txtView.setTypeface(null, Typeface.NORMAL);
            }
        }
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}