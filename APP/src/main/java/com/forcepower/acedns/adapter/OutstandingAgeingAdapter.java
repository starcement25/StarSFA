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
import com.forcepower.acedns.bean.OutstandingAgeing;

import java.util.ArrayList;

public class OutstandingAgeingAdapter extends ArrayAdapter<OutstandingAgeing> implements Filterable {

    private final ArrayList<OutstandingAgeing> mOutstandingAgeingList;
    private final int mResourceId;
    Context mContext;
    private ArrayList<OutstandingAgeing> mFinalOutstandingAgeingList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder mViewHolder;

    public OutstandingAgeingAdapter(Context context, int resourceId, ArrayList<OutstandingAgeing> outstandingAgeingList) {
        super(context, resourceId, outstandingAgeingList);
        this.mContext = context;
        this.mResourceId = resourceId;
        this.mOutstandingAgeingList = outstandingAgeingList;
        this.mFinalOutstandingAgeingList = outstandingAgeingList;
    }

    @Override
    public int getCount() {
        return mFinalOutstandingAgeingList.size();
    }

    @Override
    public OutstandingAgeing getItem(int position) {
        return mFinalOutstandingAgeingList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView,
                        ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(mResourceId, parent, false);
            mViewHolder = new ViewHolder();
            mViewHolder.mTextViewCustomerName = (TextView) convertView.findViewById(R.id.textViewCustomerName);
            mViewHolder.mTextViewQuantity = (TextView) convertView.findViewById(R.id.textViewOutstanding);
            mViewHolder.mTextView0to15 = (TextView) convertView.findViewById(R.id.textView0to15);
            mViewHolder.mTextView16to30 = (TextView) convertView.findViewById(R.id.textView16to30);
            mViewHolder.mTextView31to45 = (TextView) convertView.findViewById(R.id.textView31to45);
            mViewHolder.mTextView46to90 = (TextView) convertView.findViewById(R.id.textView46to90);
            mViewHolder.mTextViewDaysGreater90 = (TextView) convertView.findViewById(R.id.textViewGreater90Days);

            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }


        mViewHolder.mTextViewCustomerName.setText(mFinalOutstandingAgeingList.get(position).getCustomerName());
        if (mFinalOutstandingAgeingList.get(position).getOutstandingAmount().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextViewQuantity.setText("-");
        } else {
            mViewHolder.mTextViewQuantity.setText(mFinalOutstandingAgeingList.get(position).getOutstandingAmount());
        }

        if (mFinalOutstandingAgeingList.get(position).getOutstanding0to15().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextView0to15.setText("-");
        } else {
            mViewHolder.mTextView0to15.setText(mFinalOutstandingAgeingList.get(position).getOutstanding0to15());
        }

        if (mFinalOutstandingAgeingList.get(position).getOutstanding16to30().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextView16to30.setText("-");
        } else {
            mViewHolder.mTextView16to30.setText(mFinalOutstandingAgeingList.get(position).getOutstanding16to30());
        }

        if (mFinalOutstandingAgeingList.get(position).getOutstanding31to45().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextView31to45.setText("-");
        } else {
            mViewHolder.mTextView31to45.setText(mFinalOutstandingAgeingList.get(position).getOutstanding31to45());
        }

        if (mFinalOutstandingAgeingList.get(position).getOutstanding46to90().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextView46to90.setText("-");
        } else {
            mViewHolder.mTextView46to90.setText(mFinalOutstandingAgeingList.get(position).getOutstanding46to90());
        }

        if (mFinalOutstandingAgeingList.get(position).getOutstandingGreater90().trim().equalsIgnoreCase("0")) {
            mViewHolder.mTextViewDaysGreater90.setText("-");
        } else {
            mViewHolder.mTextViewDaysGreater90.setText(mFinalOutstandingAgeingList.get(position).getOutstandingGreater90());
        }

        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView mTextViewCustomerName;
        TextView mTextViewQuantity;
        TextView mTextView0to15;
        TextView mTextView16to30;
        TextView mTextView31to45;
        TextView mTextView46to90;
        TextView mTextViewDaysGreater90;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<OutstandingAgeing> outstandingAgeinglist = mOutstandingAgeingList;

            int count = outstandingAgeinglist.size();
            final ArrayList<OutstandingAgeing> newOutstandingAgeinglist = new ArrayList<OutstandingAgeing>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = outstandingAgeinglist.get(i).getCustomerName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newOutstandingAgeinglist.add(outstandingAgeinglist.get(i));
                }
            }
            results.values = newOutstandingAgeinglist;
            results.count = newOutstandingAgeinglist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalOutstandingAgeingList = (ArrayList<OutstandingAgeing>) results.values;
            notifyDataSetChanged();
        }

    }
}