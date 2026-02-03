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
import com.forcepower.acedns.bean.DestinationMaster;

import java.util.ArrayList;

public class DestinationAdapter extends ArrayAdapter<DestinationMaster> implements Filterable {

    private final Context context;
    private final int resourceId;
    private ArrayList<DestinationMaster> nameValues;
    private ArrayList<DestinationMaster> finalValues;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public DestinationAdapter(Context context, int resourceId, ArrayList<DestinationMaster> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.finalValues = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return finalValues.size();
    }

    public DestinationMaster getItem(int position) {
        return finalValues.get(position);
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
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.txtView.setText(finalValues.get(position).getDestinationName());
        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtView;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<DestinationMaster> destinationlist = nameValues;

            int count = destinationlist.size();
            final ArrayList<DestinationMaster> newDestinationlist = new ArrayList<DestinationMaster>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = destinationlist.get(i).getDestinationName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newDestinationlist.add(destinationlist.get(i));
                }
            }
            results.values = newDestinationlist;
            results.count = newDestinationlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<DestinationMaster>) results.values;
            notifyDataSetChanged();
        }

    }


}