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
import com.forcepower.acedns.bean.ProductGroupDetails;

import java.util.ArrayList;

public class NewProductGroupAdapter extends ArrayAdapter<ProductGroupDetails> implements Filterable {

    private final Context context;
    private final int resourceId;
    private ArrayList<ProductGroupDetails> mProductGroupDetailsList;
    private ArrayList<ProductGroupDetails> mFinalProductGroupDetailsList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public NewProductGroupAdapter(Context context, int resourceId, ArrayList<ProductGroupDetails> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.mProductGroupDetailsList = nameValues;
        this.mFinalProductGroupDetailsList = nameValues;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalProductGroupDetailsList.size();
    }

    public ProductGroupDetails getItem(int position) {
        return mFinalProductGroupDetailsList.get(position);
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
        String menuItem = mFinalProductGroupDetailsList.get(position).getGroupName();
        viewHolder.txtView.setText(menuItem);
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
            final ArrayList<ProductGroupDetails> productGroupDetailslist = mProductGroupDetailsList;

            int count = productGroupDetailslist.size();
            final ArrayList<ProductGroupDetails> newproductGroupDetailslist = new ArrayList<ProductGroupDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = productGroupDetailslist.get(i).getGroupName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newproductGroupDetailslist.add(productGroupDetailslist.get(i));
                }
            }

            results.values = newproductGroupDetailslist;
            results.count = newproductGroupDetailslist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalProductGroupDetailsList = (ArrayList<ProductGroupDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}
