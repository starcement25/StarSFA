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

import com.forcepower.acedns.bean.commonDatabaseHelper;

import java.util.ArrayList;

public class BeatWiseTADATotalReportAdapter extends ArrayAdapter<commonDatabaseHelper> implements Filterable {

    private final Context context;
    private final ArrayList<commonDatabaseHelper> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public BeatWiseTADATotalReportAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> orderReportDetailsList) {
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
            viewHolder.textViewVisitDate = (TextView) convertView.findViewById(R.id.textViewVisitDate);
            viewHolder.textViewBeat = (TextView) convertView.findViewById(R.id.textViewBeat);
            viewHolder.textViewTa = (TextView) convertView.findViewById(R.id.textViewTa);
            viewHolder.textViewDa = (TextView) convertView.findViewById(R.id.textViewDa);
            viewHolder.textViewTotal = (TextView) convertView.findViewById(R.id.textViewTotal);



            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.textViewVisitDate.setText(Html.fromHtml(mOrderReportDetailsList.get(position).getItem4()));
        viewHolder.textViewBeat.setText(Html.fromHtml(mOrderReportDetailsList.get(position).getItem0()));
        viewHolder.textViewTa.setText(mOrderReportDetailsList.get(position).getItem1());
        viewHolder.textViewDa.setText(mOrderReportDetailsList.get(position).getItem2());
        viewHolder.textViewTotal.setText(mOrderReportDetailsList.get(position).getItem3());


        return convertView;
    }


    public class ViewHolder {
        TextView textViewVisitDate;
        TextView textViewBeat;
        TextView textViewTa;
        TextView textViewDa;
        TextView textViewTotal;
    }


}