package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.BankDetails;

import java.util.ArrayList;

public class BankAdapter extends ArrayAdapter<BankDetails> {

    private final Context context;
    private final ArrayList<BankDetails> values;
    private final int resourceId;
    private ViewHolder viewHolder;

    public BankAdapter(Context context, int resourceId, ArrayList<BankDetails> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.txtItem = (TextView) convertView.findViewById(R.id.list_details);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtItem.setText(values.get(position).getBankName());

        return convertView;
    }

    public class ViewHolder {
        TextView txtItem;
    }
}
