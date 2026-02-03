package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class ManagerActivityReportAdapter extends ArrayAdapter<EmployeeMasterDetails> implements Filterable {
    private final Context context;
    private final ArrayList<EmployeeMasterDetails> empActivityList;
    private final int resourceId;
    private ViewHolder viewHolder;


    public ManagerActivityReportAdapter(Context context, int resourceId, ArrayList<EmployeeMasterDetails> empActivityList) {
        super(context, resourceId, empActivityList);
        this.context = context;
        this.empActivityList = empActivityList;
        this.resourceId = resourceId;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (position == 0) {
        }
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.prodDescTV = (TextView) convertView.findViewById(R.id.prodDescTV);
            viewHolder.AllocQtyTV = (TextView) convertView.findViewById(R.id.AllocQtyTV);
            viewHolder.reqQtyTv = (TextView) convertView.findViewById(R.id.reqQtyTv);
            viewHolder.stkInQtyTv = (TextView) convertView.findViewById(R.id.stkInQtyTv);
            viewHolder.invisibleProdCode = (TextView) convertView.findViewById(R.id.invisibleProdCode);
            viewHolder.total_survey = (TextView) convertView.findViewById(R.id.total_survey);
            viewHolder.col4_layout = (LinearLayout) convertView.findViewById(R.id.col4_layout);

            viewHolder.stkInQtyTv.setVisibility(View.GONE);

            convertView.setTag(viewHolder);
        }
        else
            {
            viewHolder = (ViewHolder) convertView.getTag();
        }

            viewHolder.prodDescTV.setText(empActivityList.get(position).getEmpName());


        viewHolder.AllocQtyTV.setText(empActivityList.get(position).getattendanceTime());
        viewHolder.reqQtyTv.setText(empActivityList.get(position).gettotalVisit());
        viewHolder.invisibleProdCode.setText(empActivityList.get(position).getEmpCode());

        if(Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize")){
            viewHolder.col4_layout.setVisibility(View.VISIBLE);
            viewHolder.total_survey.setText(empActivityList.get(position).getTotal_survey());
        }

        return convertView;
    }


    public class ViewHolder {
        TextView prodDescTV, AllocQtyTV, reqQtyTv, stkInQtyTv, invisibleProdCode,total_survey;
        LinearLayout col4_layout;
    }


}