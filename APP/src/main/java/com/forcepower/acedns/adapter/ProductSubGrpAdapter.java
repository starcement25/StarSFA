package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductSubGrpDetails;

import java.util.ArrayList;

public class ProductSubGrpAdapter extends ArrayAdapter<ProductSubGrpDetails> {

    private final Context context;
    private final ArrayList<ProductSubGrpDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public ProductSubGrpAdapter(Context context, int resourceId, ArrayList<ProductSubGrpDetails> nameValues) {

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
        String menuItem = nameValues.get(position).getSubGrpName();
        viewHolder.txtView.setText(menuItem);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}