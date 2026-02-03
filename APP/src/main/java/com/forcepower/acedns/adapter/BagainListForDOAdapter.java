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
import com.forcepower.acedns.bean.SaudaDetails;

import java.util.ArrayList;

public class BagainListForDOAdapter extends ArrayAdapter<SaudaDetails> implements Filterable {

    private final Context context;
    private final ArrayList<SaudaDetails> mOriginalCustomerDetailsList;
    private final int resourceId;
    private ArrayList<SaudaDetails> mFinalCustomerDetailsList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;


    public BagainListForDOAdapter(Context context, int resourceId, ArrayList<SaudaDetails> customerDetailsList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mOriginalCustomerDetailsList = customerDetailsList;
        this.mFinalCustomerDetailsList = customerDetailsList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalCustomerDetailsList.size();
    }

    public SaudaDetails getItem(int position) {
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
            viewHolder.textViewCustType.setVisibility(View.GONE);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.txtView.setText(mFinalCustomerDetailsList.get(position).getbargainDisplayValue());

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

            final ArrayList<SaudaDetails> customerDetailslist = mOriginalCustomerDetailsList;

            int count = customerDetailslist.size();
            final ArrayList<SaudaDetails> newcustomerDetailslist = new ArrayList<SaudaDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getbargainDisplayValue();
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
            mFinalCustomerDetailsList = (ArrayList<SaudaDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}