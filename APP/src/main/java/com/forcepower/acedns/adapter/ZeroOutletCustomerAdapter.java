package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class ZeroOutletCustomerAdapter extends ArrayAdapter<ProductMasterDetails> implements Filterable {

    private final Context context;
    private final ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;
    Boolean isShowingProduct;
    public ZeroOutletCustomerAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> orderReportDetailsList, Boolean isShowingProduct) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.isShowingProduct = isShowingProduct;
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
            if(isShowingProduct)
            {
                viewHolder.mTextViewBookedQtyTon.setVisibility(View.GONE);
            }
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if(!isShowingProduct)
        {
            viewHolder.invisibleProdCode.setText(mOrderReportDetailsList.get(position).getcustomerCode());
            viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getCustomerName());
            viewHolder.mTextViewBookedQtyTon.setText(mOrderReportDetailsList.get(position).getcustomerPhone());
        }
        else
        {
            viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getDesc());
        }


        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyTon;
        TextView invisibleProdCode;

    }


}