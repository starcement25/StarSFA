package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class HintRemarksCheckBoxAdapter extends BaseAdapter {

    private final Context context;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    ArrayList<String> nameValues;
    private ViewHolder viewHolder;

    public HintRemarksCheckBoxAdapter(Context context, int resourceId, ArrayList<String> nameValues) {
        super();
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
    }

    @Override
    public int getCount() {

        return nameValues.size();
    }

    @Override
    public Object getItem(int position) {

        return null;
    }

    @Override
    public long getItemId(int position) {

        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.checkProduct = (CheckBox) convertView.findViewById(R.id.checkProduct);
//        viewHolder.etRateInput.setVisibility(View.GONE);
        convertView.setTag(viewHolder);

        final String menuItem = nameValues.get(position);
        viewHolder.txtViewProductDesc.setText(menuItem);
        if (Constants.checkedHintItems.contains(menuItem)) {
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
                Constants.checkedHintItems.add(nameValues.get(position));

            } else {
                Constants.checkedHintItems.remove(nameValues.get(position));
            }
        }
    }


}