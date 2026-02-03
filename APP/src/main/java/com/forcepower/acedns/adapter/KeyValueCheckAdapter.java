package com.forcepower.acedns.adapter;

import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;

import java.util.ArrayList;
import java.util.Locale;

public class KeyValueCheckAdapter extends ArrayAdapter<KeyValue> implements Filterable {

    private final Context context;
    private final ArrayList<KeyValue> mOriginalKeyValueList;
    private final ArrayList<KeyValue> al_Search_data;
    private final int resourceId;
    private ArrayList<KeyValue> mFinalKeyValueList;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;
    public KeyValueCheckAdapter(Context context, int resourceId, ArrayList<KeyValue> keyValueList) {
        super(context, resourceId, keyValueList);
        this.context = context;
        this.mOriginalKeyValueList = keyValueList;
        this.mFinalKeyValueList = keyValueList;
        al_Search_data=new ArrayList<>();
        al_Search_data.addAll(mOriginalKeyValueList);
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalKeyValueList.size();
    }

    public KeyValue getItem(int position) {
        return mFinalKeyValueList.get(position);
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
//            viewHolder.mTextViewCustType = (TextView) convertView.findViewById(R.id.textViewCustType);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = mFinalKeyValueList.get(position).getValue();
        viewHolder.txtView.setText(menuItem);
//        viewHolder.mTextViewCustType.setText("");
        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtView;
//        TextView mTextViewCustType;
    }
    // Filter Class
    public void filter(String charText) {
        int i=0;
try{

    mFinalKeyValueList.clear();
    if (charText.length() == 0) {
        mFinalKeyValueList=new ArrayList<>(al_Search_data);
    } else {

        for (KeyValue wp : al_Search_data) {
            String searchItem = wp.getValue().toLowerCase(Locale.getDefault());
//                if(ShowColumnCount==4)
//                {
//                    searchItem=searchItem+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault());
//                }
//                else if(ShowColumnCount==5)
//                {
//                    searchItem=searchItem+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault())+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault());
//                }
            if (searchItem
                    .contains(charText.toLowerCase(Locale.getDefault()))) {
                mFinalKeyValueList.add(wp);
            }
            i++;
        }
    }
    notifyDataSetChanged();
}
catch(Exception e){
    Log.d(e.getMessage(),i+"");
}

    }
    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<KeyValue> keyvaluelist = mOriginalKeyValueList;

            int count = keyvaluelist.size();
            final ArrayList<KeyValue> newkeyValueList = new ArrayList<KeyValue>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = keyvaluelist.get(i).getValue();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newkeyValueList.add(keyvaluelist.get(i));
                }
            }
            results.values = newkeyValueList;
            results.count = newkeyValueList.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalKeyValueList = (ArrayList<KeyValue>) results.values;
            notifyDataSetChanged();
        }

    }
}
