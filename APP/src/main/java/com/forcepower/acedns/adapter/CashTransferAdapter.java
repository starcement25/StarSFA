package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CashTransferReceive;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class CashTransferAdapter extends ArrayAdapter<CashTransferReceive> implements Filterable {

    private final Context context;
    private final ArrayList<CashTransferReceive> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public CashTransferAdapter(Context context, int resourceId, ArrayList<CashTransferReceive> nameValues) {

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
        String menuItem = Utils.changeDateFormat("MMddhhmmss", "dd/MM hh:mm:ss", nameValues.get(position).getcash_transfer_date_time()) + "--" + nameValues.get(position).getdespatch_value();
        viewHolder.txtView.setText(menuItem);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}