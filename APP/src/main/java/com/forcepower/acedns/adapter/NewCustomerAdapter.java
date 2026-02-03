package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.bean.CustomerDetails;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class NewCustomerAdapter extends ArrayAdapter<CustomerDetails> implements Filterable {

    private final Context context;
    private final ArrayList<CustomerDetails> mOriginalCustomerDetailsList;
    private final int resourceId;
    private ArrayList<CustomerDetails> mFinalCustomerDetailsList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;


    public NewCustomerAdapter(Context context, int resourceId, ArrayList<CustomerDetails> customerDetailsList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mOriginalCustomerDetailsList = customerDetailsList;
        this.mFinalCustomerDetailsList = customerDetailsList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalCustomerDetailsList.size();
    }

    public CustomerDetails getItem(int position) {
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
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.textViewCustType = (TextView) convertView.findViewById(R.id.textViewCustType);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String customer = mFinalCustomerDetailsList.get(position).getCustomerName();
        viewHolder.txtView.setText(customer);
        String customertype = mFinalCustomerDetailsList.get(position).getCustomerType();
        if (customertype.equalsIgnoreCase("Dealer")) {
            viewHolder.textViewCustType.setText("D");
        } else if (customertype.equalsIgnoreCase("Sub Dealer")) {
            viewHolder.textViewCustType.setText("S");
        } else if (customertype.equalsIgnoreCase("Non Star")) {
            viewHolder.textViewCustType.setText("N");
        } else if (customertype.equalsIgnoreCase("Exclusive Dealer")) {
            viewHolder.textViewCustType.setText("E");
        } else {
            viewHolder.textViewCustType.setText(customertype);
        }
        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtView, textViewCustType;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<CustomerDetails> customerDetailslist = mOriginalCustomerDetailsList;

            int count = customerDetailslist.size();
            final ArrayList<CustomerDetails> newcustomerDetailslist = new ArrayList<CustomerDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getCustomerName();
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
            mFinalCustomerDetailsList = (ArrayList<CustomerDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}