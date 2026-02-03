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
import com.forcepower.acedns.bean.BranchMasterDetails;

import java.util.ArrayList;

public class MultipleBranchSelectionAdapter extends ArrayAdapter<BranchMasterDetails> implements Filterable
{
    private final Context context;
    private ArrayList<BranchMasterDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;
    private ItemFilter mFilter = new ItemFilter();
    public MultipleBranchSelectionAdapter(Context context, int resourceId, ArrayList<BranchMasterDetails> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getBranchName();
        viewHolder.txtView.setText(menuItem);

        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint)
        {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<BranchMasterDetails> customerDetailslist = nameValues;

            int count = customerDetailslist.size();
            final ArrayList<BranchMasterDetails> newcustomerDetailslist = new ArrayList<BranchMasterDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = customerDetailslist.get(i).getBranchName();
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
            nameValues = (ArrayList<BranchMasterDetails>) results.values;
            notifyDataSetChanged();
        }

    }
    public Filter getFilter() {
        return mFilter;
    }
}