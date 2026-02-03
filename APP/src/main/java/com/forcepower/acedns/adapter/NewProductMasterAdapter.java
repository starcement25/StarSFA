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
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class NewProductMasterAdapter extends ArrayAdapter<ProductMasterDetails> implements Filterable {

    private final Context mContext;
    private final int resourceId;
    private ItemFilter mFilter = new ItemFilter();
    private ArrayList<ProductMasterDetails> mProductMasterDetailsList;
    private ArrayList<ProductMasterDetails> mFinalProductMasterDetailsList;
    private ViewHolder viewHolder;

    public NewProductMasterAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> productmasterdetailslist) {

        super(context, resourceId, productmasterdetailslist);
        this.mContext = context;
        this.mProductMasterDetailsList = productmasterdetailslist;
        this.mFinalProductMasterDetailsList = productmasterdetailslist;
        this.resourceId = resourceId;
    }

    public ProductMasterDetails getItem(int position) {
        return mFinalProductMasterDetailsList.get(position);
    }

    public int getCount() {
        return mFinalProductMasterDetailsList.size();
    }

    public long getItemId(int position) {
        return position;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = mFinalProductMasterDetailsList.get(position).getDesc();
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
            int count = mProductMasterDetailsList.size();
            final ArrayList<ProductMasterDetails> newProductMasterList = new ArrayList<ProductMasterDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = mProductMasterDetailsList.get(i).getDesc();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newProductMasterList.add(mProductMasterDetailsList.get(i));
                }
            }
            results.values = newProductMasterList;
            results.count = newProductMasterList.size();
            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalProductMasterDetailsList = (ArrayList<ProductMasterDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}