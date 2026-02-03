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
import com.forcepower.acedns.bean.SaudaBookingProductDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class SaudaProductAdapter extends ArrayAdapter<SaudaBookingProductDetails> {

    private final Context mContext;
    private final ArrayList<SaudaBookingProductDetails> mSaudaBookingProductDetails;
    private final int resourceId;
    private ViewHolder viewHolder;


    public SaudaProductAdapter(Context context, int resourceId, ArrayList<SaudaBookingProductDetails> saudaBookingProductDetails) {
        super(context, resourceId, saudaBookingProductDetails);
        this.mContext = context;
        this.mSaudaBookingProductDetails = saudaBookingProductDetails;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();

            viewHolder.ParentLayout = (LinearLayout) convertView.findViewById(R.id.linearLayoutParent);
            viewHolder.TextViewProductName = (TextView) convertView.findViewById(R.id.textViewCustomerName);
            viewHolder.TextViewQuantity = (TextView) convertView.findViewById(R.id.textViewQtyBookedValue);
            viewHolder.textViewAmount = (TextView) convertView.findViewById(R.id.textViewAmount);
            viewHolder.textViewSR = (TextView) convertView.findViewById(R.id.textViewSR);
            viewHolder.textViewSR.setVisibility(View.VISIBLE);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (position % 2 == 0) {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }

        viewHolder.TextViewProductName.setText(mSaudaBookingProductDetails.get(position).getProductName());
        Double quantity = Double.valueOf(mSaudaBookingProductDetails.get(position).getQuantity());
        Double value = Double.valueOf(mSaudaBookingProductDetails.get(position).getValue());
        Double rate = value / quantity;
        viewHolder.TextViewQuantity.setText(Constants.defaultFormat.format(quantity));
        viewHolder.textViewAmount.setText(Constants.defaultFormat.format(value));
        viewHolder.textViewSR.setText(Constants.defaultFormat.format(rate));
        return convertView;
    }

    public class ViewHolder {
        TextView TextViewProductName, TextViewQuantity, textViewSR, textViewAmount;
        LinearLayout ParentLayout;
    }

}
