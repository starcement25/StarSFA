package com.forcepower.acedns.activity_ntquotation.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.CheckedTextView;
import android.widget.Filterable;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity_ntquotation.dataset.DataSet;

import java.util.ArrayList;

public class ShowDataSetAdapter extends ArrayAdapter<DataSet> implements Filterable {
    private final Context context;
    private final ArrayList<DataSet> nameValues;
    private final int resourceId;

    public ShowDataSetAdapter(final Context context, final int resourceId, final ArrayList<DataSet> nameValue) {
        super(context, resourceId, nameValue);
        this.context = context;
        this.nameValues = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return nameValues.size();
    }

    @NonNull
    @Override
    public View getView(final int position, View convertView, @NonNull final ViewGroup parent) {
        ViewHolder viewHolder;
        if (convertView == null) {
            final LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.txtView.setText(nameValues.get(position).getValue());
        return convertView;
    }

    public static final class ViewHolder {
        private CheckedTextView txtView;
    }
}
