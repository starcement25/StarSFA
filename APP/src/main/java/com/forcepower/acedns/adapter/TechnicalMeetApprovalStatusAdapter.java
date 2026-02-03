package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;

import static com.forcepower.acedns.R.id.spinnerStatus;

import com.forcepower.acedns.activity.TechnicalMeetApprovalStatusActivity;


public class TechnicalMeetApprovalStatusAdapter extends ArrayAdapter<commonDatabaseHelper> {

    private final Context context;
    private final int resourceId;
    Activity activity;
//    AceDnsDatabase mAceDnsDatabase;
    private ViewHolder viewHolder;



    public TechnicalMeetApprovalStatusAdapter(Context context, int resourceId) {
        super(context, resourceId, TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList);
        this.context = context;
        activity = (Activity) context;
        this.resourceId = resourceId;
//        mAceDnsDatabase = new AceDnsDatabase(context);

    }


    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.tvEmpName = (TextView) convertView.findViewById(R.id.tvEmpName);
        viewHolder.tvMansionCount = (TextView) convertView.findViewById(R.id.tvMansionCount);
        viewHolder.tvDealerName = (TextView) convertView.findViewById(R.id.tvDealerName);

        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);

        viewHolder.spinnerStatus =  convertView.findViewById(R.id.spinnerStatus);
        viewHolder.tvStatus =  convertView.findViewById(R.id.tvStatus);

        viewHolder.spinnerStatus.setVisibility(View.GONE);
        viewHolder.tvStatus.setVisibility(View.VISIBLE);


        convertView.setTag(viewHolder);

        viewHolder.tvEmpName.setText(TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(position).getItem6());
        viewHolder.tvMansionCount.setText(TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(position).getItem2());
        viewHolder.tvDealerName.setText(TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(position).getItem4());
        viewHolder.tvStatus.setText(TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(position).getItem5());


        return convertView;
    }


    public class ViewHolder {
        TextView tvEmpName, invisibleTVProdCode, tvMansionCount,tvDealerName,tvStatus;
        Spinner spinnerStatus;
    }

}