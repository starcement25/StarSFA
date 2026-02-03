package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class ProductMasterAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public ProductMasterAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {

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
            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                if (nameValues.get(position).getFocus().equalsIgnoreCase("y")) {
                    viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
                }
            }
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getDesc();
        viewHolder.txtViewProductDesc.setText(menuItem);

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc;

    }

}