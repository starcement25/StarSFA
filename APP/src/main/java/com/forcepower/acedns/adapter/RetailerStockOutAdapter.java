package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;

import java.util.ArrayList;

public class RetailerStockOutAdapter extends ArrayAdapter<BillingInformationStockSummaryData> {

    private final Context context;
    private final ArrayList<BillingInformationStockSummaryData> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public RetailerStockOutAdapter(Context context, int resourceId, ArrayList<BillingInformationStockSummaryData> nameValues) {

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
            viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.list_details2 = (TextView) convertView.findViewById(R.id.list_details2);
            viewHolder.list_details2.setVisibility(View.VISIBLE);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getproductName();
        viewHolder.txtViewProductDesc.setText(menuItem);
        viewHolder.list_details2.setText(nameValues.get(position).getstock());

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, list_details2;

    }

}