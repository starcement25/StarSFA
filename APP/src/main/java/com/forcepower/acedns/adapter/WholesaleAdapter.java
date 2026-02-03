package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.WholeSaleInfo;

import java.util.ArrayList;

public class WholesaleAdapter extends ArrayAdapter<WholeSaleInfo> implements Filterable {

    private final Context context;
    private final ArrayList<WholeSaleInfo> mOriginalCustomerDetailsList;
    private final int resourceId;
    private ArrayList<WholeSaleInfo> mFinalCustomerDetailsList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public WholesaleAdapter(Context context, int resourceId, ArrayList<WholeSaleInfo> customerDetailsList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mOriginalCustomerDetailsList = customerDetailsList;
        this.mFinalCustomerDetailsList = customerDetailsList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalCustomerDetailsList.size();
    }

    public WholeSaleInfo getItem(int position) {
        return mFinalCustomerDetailsList.get(position);
    }

    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView1 = (TextView) convertView.findViewById(R.id.txt_col1);
            viewHolder.txtView2 = (TextView) convertView.findViewById(R.id.txt_col2);
            viewHolder.txtView3 = (TextView) convertView.findViewById(R.id.txt_col3);
            viewHolder.txtView4 = (TextView) convertView.findViewById(R.id.txt_col4);
            viewHolder.txtView5 = (TextView) convertView.findViewById(R.id.txt_col5);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.txtView1.setText(mFinalCustomerDetailsList.get(position).getCustomerCode());
        viewHolder.txtView2.setText(mFinalCustomerDetailsList.get(position).getDebitnotCollected());
        viewHolder.txtView3.setText(mFinalCustomerDetailsList.get(position).getLastDebitNoteReceived());
        viewHolder.txtView4.setText(mFinalCustomerDetailsList.get(position).getClosingStockValue());
        viewHolder.txtView5.setText(mFinalCustomerDetailsList.get(position).getLogBook());
        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtView1, txtView2, txtView3, txtView4, txtView5;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<WholeSaleInfo> customerDetailslist = mOriginalCustomerDetailsList;

            int count = customerDetailslist.size();
            final ArrayList<WholeSaleInfo> newcustomerDetailslist = new ArrayList<WholeSaleInfo>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getCustomerCode();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newcustomerDetailslist.add(customerDetailslist.get(i));
                }
            }
            results.values = newcustomerDetailslist;
            results.count = newcustomerDetailslist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalCustomerDetailsList = (ArrayList<WholeSaleInfo>) results.values;
            notifyDataSetChanged();
        }

    }
}
