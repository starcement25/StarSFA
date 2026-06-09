package com.forcepower.acedns.new_activity.customer_outstanding.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.CategoryDataSet;

import java.util.ArrayList;

public class CategoryAdapter extends ArrayAdapter<CategoryDataSet> {
    private final Context context;
    private final ArrayList<CategoryDataSet> values;
    private final int resourceId;

    public CategoryAdapter(Context context, int resourceId, ArrayList<CategoryDataSet> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
    }

    @NonNull
    @SuppressLint({"ResourceAsColor", "SetTextI18n"})
    @Override
    public View getView(int position, View convertView, @NonNull ViewGroup parent) {
        ViewHolder viewHolder;
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.headerLayout = convertView.findViewById(R.id.headerLayout);
            viewHolder.headerTitle = convertView.findViewById(R.id.headerTitle);
            viewHolder.amountText = convertView.findViewById(R.id.amountText);
            viewHolder.invoiceCount = convertView.findViewById(R.id.invoiceCount);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (CategoryAdapter.ViewHolder) convertView.getTag();
        }

        viewHolder.headerLayout.setBackgroundColor(Color.parseColor(values.get(position).getColorCode()));
        viewHolder.headerTitle.setText(values.get(position).getTitle());
        viewHolder.amountText.setText("₹ " + values.get(position).getAmount());
        viewHolder.invoiceCount.setText(values.get(position).getInvoiceCount() + " Invoice");


        return convertView;
    }

    public static class ViewHolder {
        LinearLayout headerLayout;
        TextView headerTitle, amountText, invoiceCount;

    }
}