package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SelfAppraisalDetailsBranchWise;

import java.util.ArrayList;

/**
 * Created by Suvradip on 16/02/2017.
 */

public class SelfAppraisalBranchWiseListAdapter extends BaseAdapter {
    private final int resourceId;
    ArrayList<SelfAppraisalDetailsBranchWise> values;
    Context mContext;
    private ViewHolder viewHolder;

    public SelfAppraisalBranchWiseListAdapter(Context context, int resourceid, ArrayList<SelfAppraisalDetailsBranchWise> values) {
        mContext = context;
        this.values = values;
        resourceId = resourceid;

    }

    @Override
    public int getCount() {
        return values.size();
    }

    @Override
    public Object getItem(int i) {
        return null;
    }

    @Override
    public long getItemId(int i) {
        return 0;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.tvCustomerName = (TextView) convertView.findViewById(R.id.customerBranchName);
            viewHolder.tvTarget = (TextView) convertView.findViewById(R.id.tvtarget);
            viewHolder.tvAchievement = (TextView) convertView.findViewById(R.id.tvachievement);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.tvCustomerName.setText(values.get(position).getbranchName());
        viewHolder.tvTarget.setText(values.get(position).gettarget());
        viewHolder.tvAchievement.setText(values.get(position).getachievement());
        return convertView;
    }

    public class ViewHolder {
        TextView tvCustomerName, tvTarget, tvAchievement;

    }
}