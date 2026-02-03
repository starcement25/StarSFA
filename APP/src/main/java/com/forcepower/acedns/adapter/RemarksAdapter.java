package com.forcepower.acedns.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.ActivittyOrderStatus;

import java.util.ArrayList;
import java.util.List;

public class RemarksAdapter extends ArrayAdapter<String> {

    private final Context context;
    private final List<String> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public RemarksAdapter(Context context, int resourceId, ArrayList<String> nameValues) {
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
                //Utils.showToast(context,nameValues.get(position));
                //viewHolder.txtView.setChecked(true);
                //viewHolder.txtView.setBackgroundColor(Color.YELLOW);
                if (context instanceof ActivittyOrderStatus) {
                    ((ActivittyOrderStatus)context).SaveRemarksOrder(nameValues.get(position),position);
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