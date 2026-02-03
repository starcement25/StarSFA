package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.TourExReport;

import java.util.ArrayList;

public class TourExpReportAdapter extends ArrayAdapter<TourExReport> implements Filterable {

    private final Context context;
    private final ArrayList<TourExReport> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public TourExpReportAdapter(Context context, int resourceId, ArrayList<TourExReport> nameValues) {

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
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.amount = convertView.findViewById(R.id.textViewCustType);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getDate();
        //Date dd=new Date(menuItem);
        //SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        //String date = format.format(dd);
        viewHolder.txtView.setText(menuItem);
        viewHolder.amount.setText(nameValues.get(position).getTotal());
        //viewHolder.txtView.setTypeface

        return convertView;
    }


    public class ViewHolder {
        TextView txtView,amount;
    }
}