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
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class ActivationReportAdapter extends ArrayAdapter<ProductMasterDetails> implements Filterable {

    private final Context context;
    private final ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    Boolean isShowingDialog;

    public ActivationReportAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> orderReportDetailsList, Boolean isShowingDialog) {
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
            viewHolder.col1_layout = convertView.findViewById(R.id.col1_layout);
//            if(isShowingDialog)
//            {
//                viewHolder.col1_layout.setVisibility(View.GONE);
//            }
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if(!isShowingDialog)
        {
            viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getcustomerCode());
            viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getCustomerName());
            viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getIMEINo());
        }
        else
        {
            viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getIMEINo());
            viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getCustomerName());
        }



//        if(!isShowingDialog)
//        {
//            viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getemp_name());
//        }
//        else
//        {
//            viewHolder.mTextViewSku.setText(Utils.changeDateFormat("yyyy-mm-dd","dd/mm/yyyy",mOrderReportDetailsList.get(position).getdate()));
//        }


        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyTon;
        TextView invisibleProdCode;
        LinearLayout col1_layout;

    }


}