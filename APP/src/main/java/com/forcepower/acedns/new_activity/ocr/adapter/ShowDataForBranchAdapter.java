package com.forcepower.acedns.new_activity.ocr.adapter;

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
import com.forcepower.acedns.bean.KeyValue;

import java.util.ArrayList;

public class ShowDataForBranchAdapter extends ArrayAdapter<KeyValue> implements Filterable {
    private final Context context;
    private final ArrayList<KeyValue> nameValues;
    private final int resourceId;
    private ArrayList<KeyValue> mFinalSalesOfficerList;
    private final ShowDataForBranchAdapter.ItemFilter mFilter = new ShowDataForBranchAdapter.ItemFilter();

    public ShowDataForBranchAdapter(final Context context, final int resourceId, final ArrayList<KeyValue> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.mFinalSalesOfficerList = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalSalesOfficerList.size();
    }

    public KeyValue getItem(int position) {
        return mFinalSalesOfficerList.get(position);
    }

    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull final ViewGroup parent) {
        ShowDataForBranchAdapter.ViewHolder viewHolder;
        if (convertView == null) {
            final LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ShowDataForBranchAdapter.ViewHolder();
            viewHolder.txtView = convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ShowDataForBranchAdapter.ViewHolder) convertView.getTag();
        }
        viewHolder.txtView.setText(mFinalSalesOfficerList.get(position).getValue());
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
            final ArrayList<KeyValue> customerDetailslist = nameValues;
            int count = customerDetailslist.size();
            final ArrayList<KeyValue> newcustomerDetailslist = new ArrayList<>(count);
            String filterableString;
            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getValue();
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
            mFinalSalesOfficerList = (ArrayList<KeyValue>) results.values;
            notifyDataSetChanged();
        }
    }
}
