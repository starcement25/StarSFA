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
import com.forcepower.acedns.bean.SaudaBookingProductGroupDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class SaudaProductGroupAdapter extends ArrayAdapter<SaudaBookingProductGroupDetails> {

    private final Context mContext;
    private final ArrayList<SaudaBookingProductGroupDetails> mSaudaBookingProductGroupDetails;
    private final int resourceId;
    private ViewHolder viewHolder;


    public SaudaProductGroupAdapter(Context context, int resourceId, ArrayList<SaudaBookingProductGroupDetails> saudaBookingProductGroupDetails) {
        super(context, resourceId, saudaBookingProductGroupDetails);
        this.mContext = context;
        this.mSaudaBookingProductGroupDetails = saudaBookingProductGroupDetails;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();

            viewHolder.ParentLayout = (LinearLayout) convertView.findViewById(R.id.linearLayoutParent);
            viewHolder.TextViewProductGroupName = (TextView) convertView.findViewById(R.id.textViewCustomerName);
            viewHolder.TextViewQuantity = (TextView) convertView.findViewById(R.id.textViewQtyBookedValue);
            viewHolder.textViewAmount = (TextView) convertView.findViewById(R.id.textViewAmount);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (position % 2 == 0) {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }

        viewHolder.TextViewProductGroupName.setText(mSaudaBookingProductGroupDetails.get(position).getProductGroupName());
        Double quantity = Double.valueOf(mSaudaBookingProductGroupDetails.get(position).getQuantity());
        viewHolder.TextViewQuantity.setText(Constants.defaultFormat.format(quantity));
        Double value = Double.valueOf(mSaudaBookingProductGroupDetails.get(position).getValue());
        viewHolder.textViewAmount.setText(Constants.defaultFormat.format(value));
        return convertView;
    }

    public class ViewHolder {
        TextView TextViewProductGroupName, TextViewQuantity, textViewAmount;
        LinearLayout ParentLayout;
    }

}
