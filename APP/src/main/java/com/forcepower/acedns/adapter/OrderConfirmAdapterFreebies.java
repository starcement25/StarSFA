package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;


public class OrderConfirmAdapterFreebies extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public OrderConfirmAdapterFreebies(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
        viewHolder.qtyLayout = (LinearLayout) convertView.findViewById(R.id.qty_lay);
        viewHolder.slNo = (TextView) convertView.findViewById(R.id.txt_sl);
        viewHolder.prodName = (TextView) convertView.findViewById(R.id.txt_name);
        viewHolder.qty = (TextView) convertView.findViewById(R.id.txt_qty);
        viewHolder.mrpTitle = (TextView) convertView.findViewById(R.id.mrp_title);
        viewHolder.mrpVal = (TextView) convertView.findViewById(R.id.txt_mrp);
        viewHolder.discountLayout = (LinearLayout) convertView.findViewById(R.id.discount_layout);
        viewHolder.discount = (TextView) convertView.findViewById(R.id.txt_discount);
        viewHolder.total = (TextView) convertView.findViewById(R.id.txt_total);
        viewHolder.txt_slsss = (TextView) convertView.findViewById(R.id.txt_slsss);
        viewHolder.txtTDVatTitle = (TextView) convertView.findViewById(R.id.txt_td_vat);
        viewHolder.txtPack = (TextView) convertView.findViewById(R.id.txt_pack);
        viewHolder.txtFreight = (TextView) convertView.findViewById(R.id.txt_freight);

        viewHolder.slNo.setText("" + (position + 1) + ".");
        viewHolder.qty.setText(nameValues.get(position).getQty() + " " + nameValues.get(position).getfreeBieUom());
        viewHolder.prodName.setText(nameValues.get(position).getDesc());
        viewHolder.mrpTitle.setText("Type : ");
        viewHolder.mrpVal.setText("FOC");
        viewHolder.discountLayout.setVisibility(View.GONE);
        viewHolder.total.setText("0.00");

        convertView.setTag(viewHolder);

        return convertView;
    }


    public class ViewHolder {
        TextView slNo, prodName, qty, mrpTitle, mrpVal, discount, total, txt_slsss, txtTDVatTitle, txtPack, txtFreight;
        LinearLayout discountLayout;
        LinearLayout parentLayout, qtyLayout;
    }
}