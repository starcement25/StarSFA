package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class ProductMasterStockinAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    Boolean isEditable;
    private ViewHolder viewHolder;

    public ProductMasterStockinAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues, Boolean isEditable) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.isEditable = isEditable;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtDetails = (TextView) convertView.findViewById(R.id.prod_details);
            viewHolder.txtqty = (TextView) convertView.findViewById(R.id.txt_despatch_qty);
            viewHolder.txtUom = (TextView) convertView.findViewById(R.id.txt_uom);
            viewHolder.edQty = (EditText) convertView.findViewById(R.id.ed_qty);
            if (!isEditable) {
                viewHolder.llEditImage = (LinearLayout) convertView.findViewById(R.id.llEditImage);
                viewHolder.llEditImage.setVisibility(View.GONE);
            }

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtDetails.setText(nameValues.get(position).getDesc());
        viewHolder.txtqty.setText(nameValues.get(position).getDespatchQtyForStockIn());
        viewHolder.txtUom.setText(nameValues.get(position).getUom1());
        viewHolder.edQty.setText(nameValues.get(position).getQty());

        return convertView;
    }

    @Override
    public boolean isEnabled(int position) {
        return isEditable;
    }


    public class ViewHolder {
        TextView txtDetails, txtqty, txtUom;
        EditText edQty;
        LinearLayout llEditImage;
    }
}