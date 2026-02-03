package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.forcepower.acedns.R;

public class ListCheckAdapter extends BaseAdapter {
    private final int resourceId;
    Context mContext;
    LayoutInflater mLayoutInflater;
    String[] mValues;
    private ViewHolder viewHolder;

    public ListCheckAdapter(Context context, int resourceid, String[] values) {
        mContext = context;
        mValues = values;
        resourceId = resourceid;
        mLayoutInflater = (LayoutInflater) mContext
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    @Override
    public int getCount() {
        return mValues.length;
    }

    @Override
    public Object getItem(int position) {
        return mValues[position];
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.mCheckBox = (CheckBox) convertView.findViewById(R.id.checkBox1);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = mValues[position];
        viewHolder.txtView.setText(menuItem);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
        CheckBox mCheckBox;
    }


}
