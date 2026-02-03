package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.caldroid.R.color;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MenuObj;

import java.util.ArrayList;

public class OrderMenuAdapter extends ArrayAdapter<MenuObj> {

    private final Context context;
    private final ArrayList<MenuObj> values;
    private final int resourceId;
    boolean showName;
    private ViewHolder viewHolder;


    public OrderMenuAdapter(Context context, int resourceId, ArrayList<MenuObj> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
    }

    public OrderMenuAdapter(Context context, int resourceId, ArrayList<MenuObj> values, boolean showName) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        this.showName = showName;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.gridImage = (ImageView) convertView.findViewById(R.id.menu_child_img);
            viewHolder.textcount = (TextView) convertView.findViewById(R.id.textViewCount);
            viewHolder.numberLayout = (LinearLayout) convertView.findViewById(R.id.linearLayoutNumber);

            if (showName) {
                viewHolder.featureName = (TextView) convertView.findViewById(R.id.txt_name);
            }
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.gridImage.setImageResource(values.get(position).getResourceId());
        if (showName) {
            viewHolder.featureName.setText(values.get(position).getFeatureName());
        }
        if (values.get(position).isSelected()) {
            convertView.setBackgroundColor(color.caldroid_holo_blue_light);
        } else {
            convertView.setBackgroundColor(Color.TRANSPARENT);
        }

        if (values.get(position).getCount().trim().length() > 0) {
            viewHolder.textcount.setText(values.get(position).getCount());
            viewHolder.numberLayout.setVisibility(View.VISIBLE);
        } else {
            viewHolder.numberLayout.setVisibility(View.GONE);
        }
        return convertView;
    }

    public class ViewHolder {
        ImageView gridImage;
        TextView featureName;
        TextView textcount;
        LinearLayout numberLayout;
    }
}