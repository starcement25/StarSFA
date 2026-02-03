package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SurveyReport;

import java.util.ArrayList;

public class SurveyReportAdapter extends ArrayAdapter<SurveyReport> {

    private final Context mContext;
    private final ArrayList<SurveyReport> mSurveyReportList;
    private final int mResourceId;
    private ViewHolder mViewHolder;

    public SurveyReportAdapter(Context context, int layoutResourceId, ArrayList<SurveyReport> surveyreport) {
        super(context, layoutResourceId, surveyreport);
        this.mContext = context;
        this.mResourceId = layoutResourceId;
        this.mSurveyReportList = surveyreport;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(mResourceId, parent, false);
            mViewHolder = new ViewHolder();
            mViewHolder.ParentLayout = (LinearLayout) convertView.findViewById(R.id.linearLayoutParent);
            mViewHolder.TextViewProductGroupName = (TextView) convertView.findViewById(R.id.textViewProductGroupNameValue);
            mViewHolder.TextViewQuantity = (TextView) convertView.findViewById(R.id.textViewQuantityValue);
            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }
        if (position % 2 == 0) {
            mViewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            mViewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }
        if (mSurveyReportList.size() > 0) {
            mViewHolder.TextViewProductGroupName.setText(mSurveyReportList.get(position).getMenuName());
            mViewHolder.TextViewQuantity.setText(mSurveyReportList.get(position).getTotal());
        } else {
            mViewHolder.TextViewProductGroupName.setText("No Record Found");
            mViewHolder.TextViewQuantity.setText("0");
        }
        return convertView;
    }

    public class ViewHolder {
        TextView TextViewProductGroupName, TextViewQuantity;
        LinearLayout ParentLayout;
    }

}
