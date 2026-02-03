package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class CatalogueVerticalSelectionAdapter extends ArrayAdapter<String> {

    private final Context context;
    private final int resourceId;
    private ArrayList<String> mFinalVerticalList;
    private ViewHolder viewHolder;


    public CatalogueVerticalSelectionAdapter(Context context, int resourceId, ArrayList<String> customerDetailsList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mFinalVerticalList = customerDetailsList;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return mFinalVerticalList.size();
    }

    public String getItem(int position) {
        return mFinalVerticalList.get(position);
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
        String verticalWithCatalogueName = mFinalVerticalList.get(position);
        viewHolder.txtView.setText(verticalWithCatalogueName.split("\\^")[0]);

        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }

}