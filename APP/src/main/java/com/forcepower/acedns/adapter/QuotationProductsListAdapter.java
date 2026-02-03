package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.QuotationDetails;

import java.util.ArrayList;


public class QuotationProductsListAdapter extends ArrayAdapter<QuotationDetails> {

    private final Context mContext;
    private final ArrayList<QuotationDetails> DetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public QuotationProductsListAdapter(Context context, int resourceId, ArrayList<QuotationDetails> DetailsList) {
        super(context, resourceId, DetailsList);
        this.mContext = context;
        this.DetailsList = DetailsList;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();

            viewHolder.qty = (TextView) convertView.findViewById(R.id.qty);
            viewHolder.desc = (TextView) convertView.findViewById(R.id.desc);
            viewHolder.price = (TextView) convertView.findViewById(R.id.price);
            viewHolder.tax = (TextView) convertView.findViewById(R.id.tax);
            viewHolder.amount = (TextView) convertView.findViewById(R.id.amount);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.qty.setText(DetailsList.get(position).getQuotationQuantity());
        viewHolder.desc.setText(DetailsList.get(position).getQuotationProductDesc());
        viewHolder.price.setText(DetailsList.get(position).getQuotationProductRate());
        viewHolder.tax.setText(DetailsList.get(position).getQuotationProductTax());
        viewHolder.amount.setText(DetailsList.get(position).getQuotationProductAmount());

        return convertView;
    }

    public class ViewHolder {
        TextView qty, desc, price, tax, amount;
    }

}