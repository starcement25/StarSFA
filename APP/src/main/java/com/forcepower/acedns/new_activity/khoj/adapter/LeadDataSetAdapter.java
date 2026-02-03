package com.forcepower.acedns.new_activity.khoj.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity_ntquotation.dataset.CustomerFilterModel;
import com.forcepower.acedns.new_activity.khoj.data_set.LeadDataSet;
import com.google.gson.Gson;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class LeadDataSetAdapter extends ArrayAdapter<LeadDataSet> implements Filterable {
    private final Context context;
    private final ArrayList<LeadDataSet> nameValues;
    private final int resourceId;
    private ArrayList<LeadDataSet> mFinalSalesOfficerList;
    private final ItemFilter mFilter = new ItemFilter();

    public LeadDataSetAdapter(final Context context, final int resourceId, final ArrayList<LeadDataSet> nameValue) {
        super(context, resourceId, nameValue);

        this.context = context;
        this.nameValues = nameValue;
        this.mFinalSalesOfficerList=nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalSalesOfficerList.size();
    }

    public LeadDataSet getItem(int position) {
        return mFinalSalesOfficerList.get(position);
    }

    @SuppressLint("SetTextI18n")
    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull final ViewGroup parent) {
        ViewHolder viewHolder;
        if(convertView == null)
        {
            final LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.leadIdText = convertView.findViewById(R.id.leadIdText);
            viewHolder.leadStatusText =convertView.findViewById(R.id.leadStatusText);
            viewHolder.soldToPartyNameText = convertView.findViewById(R.id.soldToPartyNameText);
            viewHolder.shipToPartyNameText = convertView.findViewById(R.id.shipToPartyNameText);
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.leadIdText.setText(mFinalSalesOfficerList.get(position).getLeadId());

        viewHolder.soldToPartyNameText.setText(mFinalSalesOfficerList.get(position).getSoldToPartyName());
        viewHolder.shipToPartyNameText.setText(mFinalSalesOfficerList.get(position).getShipToPartyName());

        if(mFinalSalesOfficerList.get(position).getLeadStatus().equalsIgnoreCase("pending")){
            viewHolder.leadStatusText.setText("PENDING");
            viewHolder.leadStatusText.setTextColor(Color.parseColor("#d1e108"));
        }else if(mFinalSalesOfficerList.get(position).getLeadStatus().equalsIgnoreCase("yes")){
            viewHolder.leadStatusText.setText("APPROVED");
            viewHolder.leadStatusText.setTextColor(Color.parseColor("#35b60c"));
        }else if(mFinalSalesOfficerList.get(position).getLeadStatus().equalsIgnoreCase("no")){
            viewHolder.leadStatusText.setText("REJECT");
            viewHolder.leadStatusText.setTextColor(Color.parseColor("#b60c0c"));
        }else if(mFinalSalesOfficerList.get(position).getLeadStatus().equalsIgnoreCase("hold")){
            viewHolder.leadStatusText.setText("HOLD");
            viewHolder.leadStatusText.setTextColor(Color.parseColor("#e17e08"));
        }else if(mFinalSalesOfficerList.get(position).getLeadStatus().equalsIgnoreCase("REVISION")){
            viewHolder.leadStatusText.setText("REVISION");
            viewHolder.leadStatusText.setTextColor(Color.parseColor("#2f08e1"));
        }

        return convertView;
    }

    @NonNull
    public Filter getFilter() {
        return mFilter;
    }

    public static final class ViewHolder {
        private TextView leadIdText;
        private TextView leadStatusText;
        private TextView soldToPartyNameText;
        private TextView shipToPartyNameText;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            FilterResults results = new FilterResults();
            final ArrayList<LeadDataSet> customerDetailslist = nameValues;

            if (Build.VERSION.SDK_INT >= 35) {
                if (constraint == null || constraint.isEmpty()) {
                    results.values = customerDetailslist;
                    results.count = customerDetailslist.size();
                    return results;
                }
            }

            Gson gson = new Gson();
            CustomerFilterModel filterModel = gson.fromJson(constraint.toString(), CustomerFilterModel.class);

            String filterString = safeLower(filterModel.searchText);
            String filterFromDateString = safeLower(filterModel.fromDate);
            String filterToDateString = safeLower(filterModel.toDate);
            String filterStatusString = safeLower(filterModel.status);

            // Prepare date formats
            SimpleDateFormat isoFormat = null;
            if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.N) {
                isoFormat = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssX", Locale.US);
            }
            SimpleDateFormat searchFormat = new SimpleDateFormat("dd/MM/yyyy", Locale.US);

            ArrayList<LeadDataSet> filteredList = new ArrayList<>();

            for (LeadDataSet item : customerDetailslist) {
                boolean match = true;

                // ✅ 1. Text filter
                if (!filterString.isEmpty()) {
                    String soldTo = safeLower(item.getSoldToPartyName());
                    String shipTo = safeLower(item.getShipToPartyName());
                    String leadId = safeLower(item.getLeadId());

                    if (!(soldTo.contains(filterString) ||
                            shipTo.contains(filterString) ||
                            leadId.contains(filterString))) {
                        match = false;
                    }
                }

                // ✅ 2. Date filters (if still matching)
                if (match && (!filterFromDateString.isEmpty() || !filterToDateString.isEmpty())) {
                    try {
                        String orderDateStr = item.getFullData().getString("download_time");
                        assert isoFormat != null;
                        Date orderDate = isoFormat.parse(orderDateStr);

                        if (!filterFromDateString.isEmpty()) {
                            Date fromDate = searchFormat.parse(filterFromDateString);
                            assert orderDate != null;
                            if (orderDate.before(fromDate)) {
                                match = false;
                            }
                        }

                        if (match && !filterToDateString.isEmpty()) {
                            Date toDate = searchFormat.parse(filterToDateString);
                            Calendar cal = Calendar.getInstance();
                            cal.setTime(toDate);
                            cal.add(Calendar.DAY_OF_MONTH, 1);
                            toDate = cal.getTime();
                            assert orderDate != null;
                            if (orderDate.after(toDate)) {
                                match = false;
                            }
                        }
                    } catch (Exception ignored) {
                        match = false;
                    }
                }

                // ✅ 3. Status filter (if still matching)
                if (match && !filterStatusString.isEmpty()) {
                    try {
                        String orderStatus = item.getFullData().getString("lead_action");

                        if (!filterStatusString.equalsIgnoreCase("Select Status")) {
                            match = switch (filterStatusString) {
                                case "pending" -> orderStatus.equalsIgnoreCase("PENDING");
                                case "hold" -> orderStatus.equalsIgnoreCase("HOLD");
                                case "revision" -> orderStatus.equalsIgnoreCase("REVISION");
                                case "accepted" -> orderStatus.equalsIgnoreCase("YES");
                                case "rejected" -> orderStatus.equalsIgnoreCase("NO");
                                default -> true;
                            };
                        }
                    } catch (Exception ignored) {
                        match = false;
                    }
                }

                // ✅ If all filters pass, add item
                if (match) {
                    filteredList.add(item);
                }
            }

            results.values = filteredList;
            results.count = filteredList.size();
            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalSalesOfficerList = (ArrayList<LeadDataSet>) results.values;
            notifyDataSetChanged();
        }

        // Helper: safely handle nulls and lowercase
        private String safeLower(String str) {
            return (str == null) ? "" : str.toLowerCase();
        }
    }
}