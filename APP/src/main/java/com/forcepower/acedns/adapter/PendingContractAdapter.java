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
import com.forcepower.acedns.bean.PendingContract;

import java.util.ArrayList;

public class PendingContractAdapter extends ArrayAdapter<PendingContract> implements Filterable {
    private final ArrayList<PendingContract> mPendingContractList;
    private final int mResourceId;
    Context mContext;
    private ArrayList<PendingContract> mFinalPendingContractList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder mViewHolder;

    public PendingContractAdapter(Context context, int resourceId, ArrayList<PendingContract> pendingContractList) {
        super(context, resourceId, pendingContractList);
        this.mContext = context;
        this.mResourceId = resourceId;
        this.mPendingContractList = pendingContractList;
        this.mFinalPendingContractList = pendingContractList;
    }

    @Override
    public int getCount() {
        return mFinalPendingContractList.size();
    }

    @Override
    public PendingContract getItem(int position) {
        return mFinalPendingContractList.get(position);
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
            mViewHolder.mTextViewBranchName = (TextView) convertView.findViewById(R.id.textViewBranchName);
            mViewHolder.mTextViewProductName = (TextView) convertView.findViewById(R.id.textViewProductName);
            mViewHolder.mTextViewCustomerName = (TextView) convertView.findViewById(R.id.textViewCustomerName);
            mViewHolder.mTextView0to15 = (TextView) convertView.findViewById(R.id.textView0to15);
            mViewHolder.mTextView16to30 = (TextView) convertView.findViewById(R.id.textView16to30);
            mViewHolder.mTextView31to45 = (TextView) convertView.findViewById(R.id.textView31to45);
            mViewHolder.mTextView46to60 = (TextView) convertView.findViewById(R.id.textView46to60);
            mViewHolder.mTextViewQtyGreater60 = (TextView) convertView.findViewById(R.id.textViewGreater60Days);
            mViewHolder.mTextViewNoofDays = (TextView) convertView.findViewById(R.id.textViewNoofDays);

            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }


        mViewHolder.mTextViewBranchName.setText(mFinalPendingContractList.get(position).getBranchName());
        mViewHolder.mTextViewProductName.setText(mFinalPendingContractList.get(position).getProductName());
        mViewHolder.mTextViewCustomerName.setText(mFinalPendingContractList.get(position).getCustomerName());

        if (mFinalPendingContractList.get(position).getQuantity0to15().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getQuantity0to15().length() <= 0) {
            mViewHolder.mTextView0to15.setText("-");
        } else {
            mViewHolder.mTextView0to15.setText(mFinalPendingContractList.get(position).getQuantity0to15());
        }

        if (mFinalPendingContractList.get(position).getQuantity16to30().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getQuantity16to30().length() <= 0) {
            mViewHolder.mTextView16to30.setText("-");
        } else {
            mViewHolder.mTextView16to30.setText(mFinalPendingContractList.get(position).getQuantity16to30());
        }

        if (mFinalPendingContractList.get(position).getQuantity31to45().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getQuantity31to45().length() <= 0) {
            mViewHolder.mTextView31to45.setText("-");
        } else {
            mViewHolder.mTextView31to45.setText(mFinalPendingContractList.get(position).getQuantity31to45());
        }

        if (mFinalPendingContractList.get(position).getQuantity46to60().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getQuantity46to60().length() <= 0) {
            mViewHolder.mTextView46to60.setText("-");
        } else {
            mViewHolder.mTextView46to60.setText(mFinalPendingContractList.get(position).getQuantity46to60());

        }

        if (mFinalPendingContractList.get(position).getQuantityGreater60().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getQuantityGreater60().length() <= 0) {
            mViewHolder.mTextViewQtyGreater60.setText("-");
        } else {
            mViewHolder.mTextViewQtyGreater60.setText(mFinalPendingContractList.get(position).getQuantityGreater60());
        }

        if (mFinalPendingContractList.get(position).getGreater60Days().trim().equalsIgnoreCase("0") ||
                mFinalPendingContractList.get(position).getGreater60Days().length() <= 0) {
            mViewHolder.mTextViewNoofDays.setText("-");
        } else {
            mViewHolder.mTextViewNoofDays.setText(mFinalPendingContractList.get(position).getGreater60Days());

        }


        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView mTextViewBranchName;
        TextView mTextViewProductName;
        TextView mTextViewCustomerName;
        TextView mTextView0to15;
        TextView mTextView16to30;
        TextView mTextView31to45;
        TextView mTextView46to60;
        TextView mTextViewQtyGreater60;
        TextView mTextViewNoofDays;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<PendingContract> pendingContractlist = mPendingContractList;

            int count = pendingContractlist.size();
            final ArrayList<PendingContract> newpendingContractlistlist = new ArrayList<PendingContract>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = pendingContractlist.get(i).getCustomerName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newpendingContractlistlist.add(pendingContractlist.get(i));
                }
            }
            results.values = newpendingContractlistlist;
            results.count = newpendingContractlistlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalPendingContractList = (ArrayList<PendingContract>) results.values;
            notifyDataSetChanged();
        }

    }
}