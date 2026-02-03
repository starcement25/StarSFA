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
import com.forcepower.acedns.bean.CustomerDetails;

import java.util.ArrayList;

public class CustomerAdapter extends ArrayAdapter<CustomerDetails> implements Filterable {

    private final Context context;
    private final ArrayList<CustomerDetails> nameValues;
    private ArrayList<CustomerDetails> finalValues;
    private final int resourceId;
    private ViewHolder viewHolder;
    private ItemFilter mFilter = new ItemFilter();

    public CustomerAdapter(Context context, int resourceId, ArrayList<CustomerDetails> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.finalValues = nameValues;
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
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = finalValues.get(position).getCustomerName();
        viewHolder.txtView.setText(menuItem);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public int getCount() {
        return finalValues.size();
    }

    public CustomerDetails getItem(int position) {
        return finalValues.get(position);
    }

    public long getItemId(int position) {
        return position;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<CustomerDetails> brokermasterlist = nameValues;

            int count = brokermasterlist.size();
            final ArrayList<CustomerDetails> newMRPlist = new ArrayList<CustomerDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = brokermasterlist.get(i).getCustomerName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newMRPlist.add(brokermasterlist.get(i));
                }
            }

            results.values = newMRPlist;
            results.count = newMRPlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<CustomerDetails>) results.values;
            notifyDataSetChanged();
        }

    }

}