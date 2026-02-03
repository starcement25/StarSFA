package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class StateAdapter extends ArrayAdapter<String> implements Filterable {


    private final Context context;
    private final ArrayList<String> nameValues;
    private final int resourceId;
    private boolean isSplitted;
    private ViewHolder viewHolder;

    public StateAdapter(Context context, int resourceId, ArrayList<String> nameValues, boolean isSplitted) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.isSplitted = isSplitted;
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
        if (isSplitted) {
            viewHolder.txtView.setText(nameValues.get(position).split(";")[1]);
        } else {
            viewHolder.txtView.setText(nameValues.get(position));
        }
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}