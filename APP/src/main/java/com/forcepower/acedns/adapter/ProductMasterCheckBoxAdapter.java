package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class ProductMasterCheckBoxAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterCheckBoxAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        Constants.nameValuesProductList = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);

        viewHolder.checkProduct = (CheckBox) convertView.findViewById(R.id.checkProduct);
        convertView.setTag(viewHolder);

        final String menuItem = Constants.nameValuesProductList.get(position).getDesc();

        viewHolder.txtViewProductDesc.setText(menuItem);
        if (Constants.taggedProducts.contains(menuItem)) {
            viewHolder.checkProduct.setChecked(true);
        }

        viewHolder.checkProduct.setOnClickListener(new MyOnClickListener(position, viewHolder.checkProduct));
        return convertView;
    }


    public class ViewHolder {
        TextView txtViewProductDesc;
        CheckBox checkProduct;
    }

    public class MyOnClickListener implements View.OnClickListener {

        int position;
        CheckBox checkProduct;

        public MyOnClickListener(int position, CheckBox checkProduct) {
            this.position = position;
            this.checkProduct = checkProduct;
        }

        @Override
        public void onClick(View arg0) {

            final boolean isChecked = checkProduct.isChecked();
            if (isChecked) {
                Constants.taggedProducts.add(Constants.nameValuesProductList.get(position).getDesc());

            } else {
                Constants.taggedProducts.remove(Constants.nameValuesProductList.get(position).getDesc());
            }
        }
    }


}