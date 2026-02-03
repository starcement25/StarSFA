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
import com.forcepower.acedns.bean.SalesPerformance;

import java.text.DecimalFormat;
import java.util.ArrayList;

public class SalesPerformanceAdapter extends ArrayAdapter<SalesPerformance> implements Filterable {
    private final ArrayList<SalesPerformance> mSalesPerformanceList;
    private final int mResourceId;
    Context mContext;
    DecimalFormat df = new DecimalFormat("0.00");
    private ArrayList<SalesPerformance> mFinalSalesPerformanceList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder mViewHolder;


    public SalesPerformanceAdapter(Context context, int resourceId, ArrayList<SalesPerformance> salesPerformanceList) {
        super(context, resourceId, salesPerformanceList);
        this.mContext = context;
        this.mResourceId = resourceId;
        this.mSalesPerformanceList = salesPerformanceList;
        this.mFinalSalesPerformanceList = salesPerformanceList;
    }

    @Override
    public int getCount() {
        return mFinalSalesPerformanceList.size();
    }

    @Override
    public SalesPerformance getItem(int position) {
        return mFinalSalesPerformanceList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(mResourceId, parent, false);
            mViewHolder = new ViewHolder();
            mViewHolder.mTextViewEmployeeName = (TextView) convertView.findViewById(R.id.textViewValue);
            mViewHolder.mTextViewMTD = (TextView) convertView.findViewById(R.id.textViewMTD);
            mViewHolder.mTextViewYTD = (TextView) convertView.findViewById(R.id.textViewYTD);

            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }

        mViewHolder.mTextViewEmployeeName.setText(mFinalSalesPerformanceList.get(position).getEmployeeName());

        double mtd = 0.0;
        double ytd = 0.0;
        if (mFinalSalesPerformanceList.get(position).getMTDSale().length() > 0) {
            mtd = Double.valueOf(mFinalSalesPerformanceList.get(position).getMTDSale()) + 0.00;
        }

        if (mFinalSalesPerformanceList.get(position).getYTDSale().length() > 0) {
            ytd = Double.valueOf(mFinalSalesPerformanceList.get(position).getYTDSale()) + 0.00;
        }
        mViewHolder.mTextViewMTD.setText(df.format(mtd));
        mViewHolder.mTextViewYTD.setText(df.format(ytd));

        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView mTextViewEmployeeName;
        TextView mTextViewMTD;
        TextView mTextViewYTD;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<SalesPerformance> salesPerformancelist = mSalesPerformanceList;

            int count = salesPerformancelist.size();
            final ArrayList<SalesPerformance> newsalesPerformancelist = new ArrayList<SalesPerformance>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = salesPerformancelist.get(i).getEmployeeName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newsalesPerformancelist.add(salesPerformancelist.get(i));
                }
            }
            results.values = newsalesPerformancelist;
            results.count = newsalesPerformancelist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalSalesPerformanceList = (ArrayList<SalesPerformance>) results.values;
            notifyDataSetChanged();
        }

    }
}
