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
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class MallAdapter extends ArrayAdapter<MallMaster> implements Filterable {

    private final Context context;
    private final int resourceId;
    private ArrayList<MallMaster> nameValues;
    private ArrayList<MallMaster> finalValues;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public MallAdapter(Context context, int resourceId, ArrayList<MallMaster> nameValuess) {
        super(context, resourceId, nameValuess);
        this.context = context;
        this.nameValues = nameValuess;
        this.finalValues = nameValuess;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return finalValues.size();
    }

    public MallMaster getItem(int position) {
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
            viewHolder.textArea = (TextView) convertView.findViewById(R.id.textViewArea);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (Constants.mSurveyType.equalsIgnoreCase("mall")) {
            viewHolder.txtView.setText(finalValues.get(position).getMallName());
            viewHolder.textArea.setText(finalValues.get(position).getCity());
        }
        if (Constants.mSurveyType.equalsIgnoreCase("pincode")) {
            viewHolder.txtView.setText(finalValues.get(position).getPincode());
            viewHolder.textArea.setVisibility(View.GONE);
        }
        if (Constants.mSurveyType.equalsIgnoreCase("area")) {
            viewHolder.txtView.setText(finalValues.get(position).getArea());
            viewHolder.textArea.setText(finalValues.get(position).getPincode());
        }

        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtView;
        TextView textArea;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<MallMaster> mallmasterlist = nameValues;

            int count = mallmasterlist.size();
            final ArrayList<MallMaster> newMallMasterlist = new ArrayList<MallMaster>(count);

            String filterableString = "";

            for (int i = 0; i < count; i++) {

                if (Constants.mSurveyType.equalsIgnoreCase("mall")) {
                    filterableString = mallmasterlist.get(i).getMallName();
                }
                if (Constants.mSurveyType.equalsIgnoreCase("pincode")) {
                    filterableString = mallmasterlist.get(i).getPincode();
                }
                if (Constants.mSurveyType.equalsIgnoreCase("area")) {
                    filterableString = mallmasterlist.get(i).getArea();
                }

                if (filterableString.toLowerCase().contains(filterString)) {
                    newMallMasterlist.add(mallmasterlist.get(i));
                }
            }

            results.values = newMallMasterlist;
            results.count = newMallMasterlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<MallMaster>) results.values;
            notifyDataSetChanged();
        }

    }


}