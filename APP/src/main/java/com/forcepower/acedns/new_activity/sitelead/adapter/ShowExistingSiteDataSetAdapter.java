package com.forcepower.acedns.new_activity.sitelead.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Handler;
import android.os.Looper;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.ForegroundColorSpan;
import android.text.style.RelativeSizeSpan;
import android.text.style.StyleSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.sitelead.dataset.SiteLeadDataSet;

import java.util.ArrayList;

public class ShowExistingSiteDataSetAdapter extends ArrayAdapter<SiteLeadDataSet> implements Filterable {
    private final Context context;
    private final ArrayList<SiteLeadDataSet> nameValues;
    private final int resourceId;
    private ArrayList<SiteLeadDataSet> mFinalSalesOfficerList;
    private final ItemFilter mFilter = new ItemFilter();

    public ShowExistingSiteDataSetAdapter(final Context context, final int resourceId, final ArrayList<SiteLeadDataSet> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.mFinalSalesOfficerList = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalSalesOfficerList.size();
    }

    public SiteLeadDataSet getItem(int position) {
        return mFinalSalesOfficerList.get(position);
    }

    @SuppressLint("SetTextI18n")
    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull final ViewGroup parent) {
        ViewHolder viewHolder;
        if (convertView == null) {
            final LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.leadIdText = convertView.findViewById(R.id.leadIdText);
            viewHolder.leadStatusText=convertView.findViewById(R.id.leadStatusText);
            viewHolder.customerNameText=convertView.findViewById(R.id.customerNameText);
            viewHolder.customerContactNoText=convertView.findViewById(R.id.customerContactNoText);
            viewHolder.customerAddressText=convertView.findViewById(R.id.customerAddressText);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.leadIdText.setText(mFinalSalesOfficerList.get(position).getUniqueId()+" ("+mFinalSalesOfficerList.get(position).getVisitType()+")");
        viewHolder.leadStatusText.setText(mFinalSalesOfficerList.get(position).getApprovalStatus().toUpperCase());
        viewHolder.customerNameText.setText(mFinalSalesOfficerList.get(position).getCustomerName());
        viewHolder.customerContactNoText.setText(mFinalSalesOfficerList.get(position).getCustomerPhoneNo());
        viewHolder.customerAddressText.setText(mFinalSalesOfficerList.get(position).getAddress());
        return convertView;
    }

    @NonNull
    public Filter getFilter() {
        return mFilter;
    }

    public static final class ViewHolder {
        private TextView leadIdText;
        private TextView leadStatusText;
        private TextView customerNameText;
        private TextView customerContactNoText;
        private TextView customerAddressText;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();
            final ArrayList<SiteLeadDataSet> customerDetailslist = nameValues;
            int count = customerDetailslist.size();
            final ArrayList<SiteLeadDataSet> newcustomerDetailslist = new ArrayList<>(count);
            String filterableString1="", filterableString2="", filterableString3="";
            for (int i = 0; i < count; i++) {
                filterableString1 = customerDetailslist.get(i).getUniqueId();
                filterableString2 = customerDetailslist.get(i).getCustomerName();
                filterableString3 = customerDetailslist.get(i).getCustomerPhoneNo();
                if (filterableString1.toLowerCase().contains(filterString)
                        || filterableString2.toLowerCase().contains(filterString)
                        || filterableString3.toLowerCase().contains(filterString)) {
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
            mFinalSalesOfficerList = (ArrayList<SiteLeadDataSet>) results.values;
            notifyDataSetChanged();
        }
    }
}