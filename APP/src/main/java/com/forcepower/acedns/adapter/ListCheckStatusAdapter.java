package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SurveyStatus;

import java.util.ArrayList;

public class ListCheckStatusAdapter extends BaseAdapter {
    private final int resourceId;
    Context mContext;
    LayoutInflater mLayoutInflater;
    ArrayList<SurveyStatus> mSurveyStatusList;
    private ViewHolder viewHolder;

    public ListCheckStatusAdapter(Context context, int resourceid, ArrayList<SurveyStatus> SurveyStatusList) {
        mContext = context;
        mSurveyStatusList = SurveyStatusList;
        resourceId = resourceid;
        mLayoutInflater = (LayoutInflater) mContext
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    @Override
    public int getCount() {
        return mSurveyStatusList.size();
    }

    @Override
    public Object getItem(int position) {
        return mSurveyStatusList.get(position);
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
            viewHolder.cb = (CheckBox) convertView.findViewById(R.id.checkBox1);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = mSurveyStatusList.get(position).getSurveyLayout();
        String check = mSurveyStatusList.get(position).getStatus();
        viewHolder.txtView.setText(menuItem);

        if (check.equalsIgnoreCase("DONE")) {
            viewHolder.cb.setChecked(true);
        } else {
            viewHolder.cb.setChecked(false);
        }

        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
        CheckBox cb;
    }


}