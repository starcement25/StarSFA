package com.forcepower.acedns.new_activity.customer_outstanding.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.CheckedTextView;
import android.widget.Filter;
import android.widget.Filterable;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CustomerDetails;

import java.util.ArrayList;

public class ShowCustomerDataSetAdapter extends ArrayAdapter<CustomerDetails> implements Filterable {
    private final Context context;
    private final ArrayList<CustomerDetails> nameValues;
    private  ArrayList<CustomerDetails> mFinalNameValues;
    private final int resourceId;
    private final ItemFilter mFilter = new ItemFilter();

    public ShowCustomerDataSetAdapter(final Context context, final int resourceId, final ArrayList<CustomerDetails> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.mFinalNameValues = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalNameValues.size();
    }
    public CustomerDetails getItem(int position) {
        return mFinalNameValues.get(position);
    }
    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull final ViewGroup parent) {
        ViewHolder viewHolder;
        if (convertView == null) {
            final LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.txtView.setText(mFinalNameValues.get(position).getCustomerName()+" ("+mFinalNameValues.get(position).getCustomerType()+")");
        viewHolder.txtView.setChecked(false);
        return convertView;
    }

    public static final class ViewHolder {
        private CheckedTextView txtView;
    }
    @NonNull
    public Filter getFilter() {
        return mFilter;
    }
    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();
            final ArrayList<CustomerDetails> customerDetailslist = nameValues;
            int count = customerDetailslist.size();
            final ArrayList<CustomerDetails> newcustomerDetailslist = new ArrayList<>(count);
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
            mFinalNameValues = (ArrayList<CustomerDetails>) results.values;
            notifyDataSetChanged();
        }
    }
}
