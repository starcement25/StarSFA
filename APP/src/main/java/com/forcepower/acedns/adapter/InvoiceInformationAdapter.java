package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.InvoiceInformation;

import java.util.ArrayList;

/**
 * Created by amit on 17/01/2017.
 */

public class InvoiceInformationAdapter extends ArrayAdapter<InvoiceInformation> {

    private final Context context;
    private final ArrayList<InvoiceInformation> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public InvoiceInformationAdapter(Context context, int resourceId, ArrayList<InvoiceInformation> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.HiddenValue1 = (TextView) convertView.findViewById(R.id.HiddenValue1);
            viewHolder.HiddenValue2 = (TextView) convertView.findViewById(R.id.HiddenValue2);
            viewHolder.HiddenValue3 = (TextView) convertView.findViewById(R.id.HiddenValue3);
            viewHolder.HiddenValue4 = (TextView) convertView.findViewById(R.id.HiddenValue4);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getInvoiceNo();
        String orderNumber = nameValues.get(position).getOrderNo();
        String InvoiceDate = nameValues.get(position).getInvoiceDate();
        String customerCode = nameValues.get(position).getCustomerCode();
        String freightCharge = nameValues.get(position).getFreightCharge();
        viewHolder.txtView.setText(menuItem);
        viewHolder.HiddenValue1.setText(orderNumber);
        viewHolder.HiddenValue2.setText(InvoiceDate);
        viewHolder.HiddenValue3.setText(customerCode);
        viewHolder.HiddenValue4.setText(freightCharge);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView, HiddenValue1, HiddenValue2, HiddenValue3, HiddenValue4;
    }
}
