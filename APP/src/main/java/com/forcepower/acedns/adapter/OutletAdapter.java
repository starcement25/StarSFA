package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OutletDetails;

import java.util.ArrayList;

public class    OutletAdapter extends ArrayAdapter<OutletDetails>
{

    private final Context context;
    private final ArrayList<OutletDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public OutletAdapter(Context context, int resourceId, ArrayList<OutletDetails> nameValues)
    {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {

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
        String menuItem = nameValues.get(position).getOutletName();
        if(menuItem.contains(";"))
        {
            String dateTime="";
            if(menuItem.contains("-"))
            {
                dateTime="-"+menuItem.split("-")[1];
            }
           String[] splittedOutlet= menuItem.split(";");
            menuItem=splittedOutlet[0]+dateTime;
        }
        String flag = nameValues.get(position).getOutletCode().trim();
        viewHolder.txtView.setText(menuItem);
        if (flag.equalsIgnoreCase("0")) {
            viewHolder.txtView.setTextColor(Color.RED);
        } else {
            viewHolder.txtView.setTextColor(Color.parseColor("#003399"));
        }
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}