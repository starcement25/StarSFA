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
import com.forcepower.acedns.bean.FsSurveyPublish;

import java.util.ArrayList;

public class FsOutletAdapter extends ArrayAdapter<FsSurveyPublish> implements Filterable {

    private final Context context;
    private final ArrayList<FsSurveyPublish> mOriginalFsSurveyPublishList;
    private final int resourceId;
    private ArrayList<FsSurveyPublish> mFinalFsSurveyPublishListList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public FsOutletAdapter(Context context, int resourceId, ArrayList<FsSurveyPublish> customerDetailsList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mOriginalFsSurveyPublishList = customerDetailsList;
        this.mFinalFsSurveyPublishListList = customerDetailsList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalFsSurveyPublishListList.size();
    }

    public FsSurveyPublish getItem(int position) {
        return mFinalFsSurveyPublishListList.get(position);
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
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String businessname = mFinalFsSurveyPublishListList.get(position).getBusinessName();
        viewHolder.txtView.setText(businessname);
        viewHolder.textViewCustType.setVisibility(View.GONE);

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

            final ArrayList<FsSurveyPublish> fsSurveyPublishList = mOriginalFsSurveyPublishList;

            int count = fsSurveyPublishList.size();
            final ArrayList<FsSurveyPublish> newfsSurveyPublishListList = new ArrayList<FsSurveyPublish>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = fsSurveyPublishList.get(i).getBusinessName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newfsSurveyPublishListList.add(fsSurveyPublishList.get(i));
                }
            }
            results.values = newfsSurveyPublishListList;
            results.count = newfsSurveyPublishListList.size();
            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalFsSurveyPublishListList = (ArrayList<FsSurveyPublish>) results.values;
            notifyDataSetChanged();
        }

    }
}