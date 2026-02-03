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
import com.forcepower.acedns.bean.OrdernoWithDate;

import java.util.ArrayList;

public class OrderNowithDateAdapter extends ArrayAdapter<OrdernoWithDate> implements Filterable {

    private final Context context;
    private final ArrayList<OrdernoWithDate> mOriginalOrdernoWithDateListList;
    private final int resourceId;
    private ArrayList<OrdernoWithDate> mFinalOrdernoWithDateList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public OrderNowithDateAdapter(Context context, int resourceId, ArrayList<OrdernoWithDate> ordernoWithDateList) {
        super(context, resourceId, ordernoWithDateList);
        this.context = context;
        this.mOriginalOrdernoWithDateListList = ordernoWithDateList;
        this.mFinalOrdernoWithDateList = ordernoWithDateList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalOrdernoWithDateList.size();
    }

    public OrdernoWithDate getItem(int position) {
        return mFinalOrdernoWithDateList.get(position);
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
        String menuItem = mFinalOrdernoWithDateList.get(position).getOrderDate();
        int orderCount = position + 1;
        viewHolder.txtView.setText("Order " + orderCount);
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

            final ArrayList<OrdernoWithDate> ordernoWithDatelist = mOriginalOrdernoWithDateListList;

            int count = ordernoWithDatelist.size();
            final ArrayList<OrdernoWithDate> newordernoWithDatelist = new ArrayList<OrdernoWithDate>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = ordernoWithDatelist.get(i).getOrderDate();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newordernoWithDatelist.add(ordernoWithDatelist.get(i));
                }
            }
            results.values = newordernoWithDatelist;
            results.count = newordernoWithDatelist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalOrdernoWithDateList = (ArrayList<OrdernoWithDate>) results.values;
            notifyDataSetChanged();
        }

    }
}