package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class SimpleStringAdapter extends ArrayAdapter<String> {

    private final Context context;
    private final ArrayList<String> values;
    private final int resourceId;
    boolean isGIT;
    String hideRest = "";
    private ViewHolder viewHolder;

    public SimpleStringAdapter(Context context, int resourceId, ArrayList<String> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
    }

    public SimpleStringAdapter(Context context, int resourceId, ArrayList<String> values, boolean isGIT) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        this.isGIT = isGIT;
    }

    public SimpleStringAdapter(Context context, int resourceId, ArrayList<String> values, String hideRest) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        this.hideRest = hideRest;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.txtItem = (TextView) convertView.findViewById(R.id.list_details);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (isGIT) {
            String gitNo = values.get(position);
            String yearVal = gitNo.substring(gitNo.length() - 14, gitNo.length() - 10);
            String monthVal = gitNo.substring(gitNo.length() - 10, gitNo.length() - 8);
            String dayVal = gitNo.substring(gitNo.length() - 8, gitNo.length() - 6);
            viewHolder.txtItem.setText(gitNo + "\nDespatched on " + dayVal + "-" + monthVal + "-" + yearVal);
        } else {
            if (hideRest.length() > 0) {
                String[] valueArray = values.get(position).split("\\*");
                viewHolder.txtItem.setText(valueArray[0]);
            } else {
                viewHolder.txtItem.setText(values.get(position));
            }
        }

        return convertView;
    }

    public class ViewHolder {
        TextView txtItem;
    }
}
