package com.forcepower.acedns.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.activity.BusinessProspectActivity;

import com.forcepower.acedns.R;

import java.util.ArrayList;
import java.util.List;

public class DrCategoryAdapter extends ArrayAdapter<String> {

    private final Context context;
    private final List<String> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public DrCategoryAdapter(Context context, int resourceId, ArrayList<String> nameValues) {
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

        viewHolder.txtView.setText(nameValues.get(position));

        viewHolder.txtView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (context instanceof BusinessProspectActivity) {
                    ((BusinessProspectActivity)context).SetDrCat(nameValues.get(position));
                }

            }
        });
        return convertView;
    }

    @SuppressLint("ResourceAsColor")
    public void chk(ViewHolder v){
        v.txtView.setBackgroundColor(android.R.color.holo_blue_bright);
    }

    public class ViewHolder {
        TextView txtView;
    }
}