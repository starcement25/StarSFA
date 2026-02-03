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
import com.forcepower.acedns.bean.BrokerMaster;

import java.util.ArrayList;

public class BrokerAdapter extends ArrayAdapter<BrokerMaster> implements Filterable {

    private final Context context;
    private final int resourceId;
    private ArrayList<BrokerMaster> nameValues;
    private ArrayList<BrokerMaster> finalValues;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public BrokerAdapter(Context context, int resourceId, ArrayList<BrokerMaster> nameValuess) {
        super(context, resourceId, nameValuess);
        this.context = context;
        this.nameValues = nameValuess;
        this.finalValues = nameValuess;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return finalValues.size();
    }

    public BrokerMaster getItem(int position) {
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
        viewHolder.txtView.setText(finalValues.get(position).getBrokerName());
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

            final ArrayList<BrokerMaster> brokermasterlist = nameValues;

            int count = brokermasterlist.size();
            final ArrayList<BrokerMaster> newBrokerlist = new ArrayList<BrokerMaster>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = brokermasterlist.get(i).getBrokerName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newBrokerlist.add(brokermasterlist.get(i));
                }
            }

            results.values = newBrokerlist;
            results.count = newBrokerlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<BrokerMaster>) results.values;
            notifyDataSetChanged();
        }

    }


}