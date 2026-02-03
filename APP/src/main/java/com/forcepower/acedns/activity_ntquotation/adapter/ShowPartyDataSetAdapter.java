package com.forcepower.acedns.activity_ntquotation.adapter;

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
import com.forcepower.acedns.activity_ntquotation.dataset.PartyDataList;

import java.util.ArrayList;

public class ShowPartyDataSetAdapter extends ArrayAdapter<PartyDataList> implements Filterable {
    private final Context context;
    private final ArrayList<PartyDataList> nameValues;
    private final int resourceId;
    private ArrayList<PartyDataList> mFinalSalesOfficerList;
    private final ItemFilter mFilter = new ItemFilter();

    public ShowPartyDataSetAdapter(final Context context, final int resourceId, final ArrayList<PartyDataList> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.mFinalSalesOfficerList = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalSalesOfficerList.size();
    }

    public PartyDataList getItem(int position) {
        return mFinalSalesOfficerList.get(position);
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
        viewHolder.txtView.setText(mFinalSalesOfficerList.get(position).getName());
        return convertView;
    }

    @NonNull
    public Filter getFilter() {
        return mFilter;
    }

    public static final class ViewHolder {
        private CheckedTextView txtView;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();
            final ArrayList<PartyDataList> customerDetailslist = nameValues;
            int count = customerDetailslist.size();
            final ArrayList<PartyDataList> newcustomerDetailslist = new ArrayList<>(count);
            String filterableString;
            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getName();
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
            mFinalSalesOfficerList = (ArrayList<PartyDataList>) results.values;
            notifyDataSetChanged();
        }
    }
}
