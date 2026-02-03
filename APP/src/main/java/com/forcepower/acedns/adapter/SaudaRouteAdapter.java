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
import com.forcepower.acedns.bean.RouteDetails;

import java.util.ArrayList;

public class SaudaRouteAdapter extends ArrayAdapter<RouteDetails> implements Filterable {

    private final Context context;
    private final ArrayList<RouteDetails> nameValues;
    private final int resourceId;
    private ArrayList<RouteDetails> finalValues;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public SaudaRouteAdapter(Context context, int resourceId, ArrayList<RouteDetails> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.finalValues = nameValues;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return finalValues.size();
    }

    public RouteDetails getItem(int position) {
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
        String menuItem = finalValues.get(position).getRouteName();
        if (menuItem != null && menuItem.length() > 0 && !menuItem.equalsIgnoreCase(" ")) {
            viewHolder.txtView.setText(menuItem);
        } else {
            viewHolder.txtView.setText("Blank Route Name");
        }
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
            final ArrayList<RouteDetails> routedetailslist = nameValues;
            int count = routedetailslist.size();
            final ArrayList<RouteDetails> newRouteDetailslist = new ArrayList<RouteDetails>(count);

            String filterableString;
            for (int i = 0; i < count; i++) {
                filterableString = routedetailslist.get(i).getRouteName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newRouteDetailslist.add(routedetailslist.get(i));
                }
            }

            results.values = newRouteDetailslist;
            results.count = newRouteDetailslist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<RouteDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}
