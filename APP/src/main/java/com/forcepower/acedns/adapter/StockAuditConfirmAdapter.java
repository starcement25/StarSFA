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
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class StockAuditConfirmAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public StockAuditConfirmAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
            viewHolder.slNo = (TextView) convertView.findViewById(R.id.txt_sl);
            viewHolder.prodName = (TextView) convertView.findViewById(R.id.txt_name);
            viewHolder.qty = (TextView) convertView.findViewById(R.id.txt_qty);
            viewHolder.tdHeader = (TextView) convertView.findViewById(R.id.txt_td_vat);

            viewHolder.mrpTitle = (TextView) convertView.findViewById(R.id.mrp_title);
            viewHolder.mrpTitle.setVisibility(View.GONE);
            viewHolder.mrpVal = (TextView) convertView.findViewById(R.id.txt_mrp);
            viewHolder.mrpVal.setVisibility(View.GONE);
            viewHolder.discount = (TextView) convertView.findViewById(R.id.txt_discount);
            viewHolder.total = (TextView) convertView.findViewById(R.id.txt_total);
            viewHolder.total.setVisibility(View.GONE);
            viewHolder.totalheading = (TextView) convertView.findViewById(R.id.txt_slsss);
            viewHolder.totalheading.setVisibility(View.GONE);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (position % 2 == 0) {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }

        viewHolder.slNo.setText("" + (position + 1) + ".");
        viewHolder.prodName.setText(nameValues.get(position).getDesc());
        viewHolder.qty.setText(nameValues.get(position).getQty() +" "+nameValues.get(position).getUomSelectedForProduct());
        viewHolder.discount.setText(nameValues.get(position).getIMEINo());
        viewHolder.tdHeader.setText("");
        return convertView;
    }


    public class ViewHolder {
        TextView slNo, prodName, qty, mrpTitle, mrpVal, discount, total, totalheading, tdHeader;
        LinearLayout parentLayout;
    }
}