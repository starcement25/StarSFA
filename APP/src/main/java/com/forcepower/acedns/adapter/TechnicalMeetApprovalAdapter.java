package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.commonDatabaseHelper;

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.spinnerStatus;

import com.forcepower.acedns.activity.TechnicalMeetApprovalActivity;


public class TechnicalMeetApprovalAdapter extends ArrayAdapter<commonDatabaseHelper> {

    private final Context context;
    private final int resourceId;
    Activity activity;
//    AceDnsDatabase mAceDnsDatabase;
    private ViewHolder viewHolder;



    public TechnicalMeetApprovalAdapter(Context context, int resourceId) {
        super(context, resourceId, TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList);
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



        viewHolder.spinnerStatus = (Spinner) convertView.findViewById(R.id.spinnerStatus);


            final ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray.add("Pending");
            spinnerArray.add("Approved");
            spinnerArray.add("Rejected");

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(context, R.layout.custom_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            viewHolder.spinnerStatus.setAdapter(spinnerArrayAdapter);
            if (TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).getItem5().equalsIgnoreCase("Pending")) {
                viewHolder.spinnerStatus.setSelection(0);
            }
            else if (TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).getItem5().equalsIgnoreCase("Approved")) {
                viewHolder.spinnerStatus.setSelection(1);
            }
            else {
                viewHolder.spinnerStatus.setSelection(2);
            }
            viewHolder.spinnerStatus.setOnItemSelectedListener(new GenericOnItemSelectedListenerUom(position, viewHolder.spinnerStatus));



        convertView.setTag(viewHolder);

        viewHolder.tvEmpName.setText(TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).getItem6());
        viewHolder.tvMansionCount.setText(TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).getItem2());
        viewHolder.tvDealerName.setText(TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).getItem4());


        return convertView;
    }


    public class ViewHolder {
        TextView tvEmpName, invisibleTVProdCode, tvMansionCount,tvDealerName;
        Spinner spinnerStatus;
    }


    private class GenericOnItemSelectedListenerUom implements Spinner.OnItemSelectedListener {
        View view;
        private int position;

        private GenericOnItemSelectedListenerUom(int pos, View view) {
            this.position = pos;
            this.view = (View) view.getParent();
        }

        @Override
        public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
        {
            ((TextView) adapterView.getChildAt(0)).setTextSize(10);
            if (i == 0)
            {
                TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).setItem5("Pending");
            }
            else if (i == 1)
            {
                TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).setItem5("Approved");
            }
            else
            {
                TechnicalMeetApprovalActivity.UnapprovedTechnicalMeetsList.get(position).setItem5("Rejected");

            }

        }

        @Override
        public void onNothingSelected(AdapterView<?> parent) {

        }
    }

}