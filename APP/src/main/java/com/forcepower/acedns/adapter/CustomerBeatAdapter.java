package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CustomerDetails;

import java.util.ArrayList;

public class CustomerBeatAdapter extends ArrayAdapter<CustomerDetails> implements Filterable {

    private final Context context;
    private final ArrayList<CustomerDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public CustomerBeatAdapter(Context context, int resourceId, ArrayList<CustomerDetails> nameValues) {

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
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getCustomerName();
        viewHolder.txtView.setText(menuItem);
        //viewHolder.txtView.setTypeface
        if(nameValues.get(position).getIsNewCustomer().matches("g")) {
            viewHolder.txtView.setTextColor(Color.parseColor("#349c05"));
        }else if(nameValues.get(position).getIsNewCustomer().matches("r")){
            viewHolder.txtView.setTextColor(Color.RED);
        }else{

        }
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}