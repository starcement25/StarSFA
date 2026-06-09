package com.forcepower.acedns.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
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

public class MenuAdapter extends ArrayAdapter<MenuObj> {
    private final Context context;
    private final ArrayList<MenuObj> values;
    private final int resourceId;
    boolean showName;
    private ViewHolder viewHolder;


    public MenuAdapter(Context context, int resourceId, ArrayList<MenuObj> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
    }

    public MenuAdapter(Context context, int resourceId, ArrayList<MenuObj> values, boolean showName) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        this.showName = showName;
    }

    @SuppressLint("ResourceAsColor")
    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.gridImage = (ImageView) convertView.findViewById(R.id.menu_child_img);
            if (showName) {
                viewHolder.featureName = (TextView) convertView.findViewById(R.id.txt_name);
                viewHolder.featureName.setVisibility(VISIBLE);
            }
            viewHolder.popupLayout=convertView.findViewById(R.id.popupLayout);
            viewHolder.popupLayout.setVisibility(GONE);
            viewHolder.popupCount=convertView.findViewById(R.id.popupCount);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.gridImage.setImageResource(values.get(position).getResourceId());
        if (showName) {
            viewHolder.featureName.setText(values.get(position).getFeatureName().toUpperCase());
        }
        if (values.get(position).isSelected()) {
            convertView.setBackgroundColor(color.caldroid_holo_blue_light);
        } else {
            convertView.setBackgroundColor(Color.TRANSPARENT);
        }

        // Count show
        if(values.get(position).getFeatureName().equalsIgnoreCase("funnel")){
            viewHolder.popupLayout.setVisibility(VISIBLE);
            viewHolder.popupCount.setText(values.get(position).getCount());
        }


        return convertView;
    }

    public class ViewHolder {
        ImageView gridImage;
        TextView featureName;
        LinearLayout popupLayout;
        TextView popupCount;
    }
}
