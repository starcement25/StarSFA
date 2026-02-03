package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.StokistDetails;

import java.util.ArrayList;

public class StokistReportAdapter extends ArrayAdapter<StokistDetails> implements Filterable {

    private final Context context;
    private final ArrayList<StokistDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public StokistReportAdapter(Context context, int resourceId, ArrayList<StokistDetails> nameValues) {

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
            viewHolder.list_intra = convertView.findViewById(R.id.list_intra);
            viewHolder.list_start = convertView.findViewById(R.id.list_start);
            viewHolder.prod = convertView.findViewById(R.id.list_product);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getStokistCode();

        viewHolder.list_start.setText(menuItem);

        viewHolder.txtView.setText(nameValues.get(position).getCustomerCode());
        viewHolder.list_intra.setText(nameValues.get(position).getSale());
        viewHolder.amount.setText(nameValues.get(position).getFolder());
        viewHolder.prod.setText(nameValues.get(position).getProd_name());
        return convertView;
    }


    public class ViewHolder {
        TextView txtView,amount,list_intra,list_start,prod;
    }
}