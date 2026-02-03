package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.AttendanceReportDetails;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import com.forcepower.acedns.activity.ActivityAttendanceReport;

public class AttendanceReportAdapter extends ArrayAdapter<AttendanceReportDetails> implements Filterable {

    private final Context context;
    private final ArrayList<AttendanceReportDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    Boolean isShowingDialog;

    public AttendanceReportAdapter(Context context, int resourceId, ArrayList<AttendanceReportDetails> orderReportDetailsList,Boolean isShowingDialog) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.isShowingDialog = isShowingDialog;
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
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);
            viewHolder.invisibleProdCode = (TextView) convertView.findViewById(R.id.invisibleProdCode);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getemp_code());
        if(!isShowingDialog)
        {
            viewHolder.mTextViewSku.setText(Html.fromHtml(mOrderReportDetailsList.get(position).getemp_name()+", <font color='#F58322'>"+mOrderReportDetailsList.get(position).getemp_designation()+"</font>"));
        }
        else
        {
            viewHolder.mTextViewSku.setText(Utils.changeDateFormat("yyyy-mm-dd","dd/mm/yyyy",mOrderReportDetailsList.get(position).getdate()));
        }
        if(ActivityAttendanceReport.showingTimeOrCount.equalsIgnoreCase("Count") && !isShowingDialog)
        {
            viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getTime());
        }
        else
        {
            if(mOrderReportDetailsList.get(position).gettrans_id().contains("AE"))
            {
                viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getTime());
            }
            else if(mOrderReportDetailsList.get(position).gettrans_id().contains("WO"))
            {
                viewHolder.mTextViewBookedQtyTon.setText("Weekly Off");
            }
            else
            {
                viewHolder.mTextViewBookedQtyTon.setText("Leave Request");
            }
        }

        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyTon;
        TextView invisibleProdCode;

    }


}