package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;

import java.util.ArrayList;

public class RoutePlanTransAdapter extends ArrayAdapter<RoutePlanMasterDetails> {

    private final Context context;
    private final ArrayList<RoutePlanMasterDetails> nameValues;
    private final int resourceId;
    boolean showPrevious = false;
    private ViewHolder viewHolder;

    public RoutePlanTransAdapter(Context context, int resourceId, ArrayList<RoutePlanMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    public RoutePlanTransAdapter(Context context, int resourceId, ArrayList<RoutePlanMasterDetails> nameValues, boolean showPrevious) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.showPrevious = showPrevious;
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
        String menuItem = nameValues.get(position).getRouteName();
        String previousCode = nameValues.get(position).getPrevious_route_code();
        String previous = nameValues.get(position).getPrevious_route_name();
        if (menuItem != null && menuItem.length() > 0 && !menuItem.equalsIgnoreCase(" ")) {
            if (showPrevious) {
                if (previousCode.length() > 0 && !previousCode.equalsIgnoreCase(" ")) {
                    menuItem = "<font color=#003399>" + menuItem + "</font>";
                    String txt = "<font color=#000000>" + "has been changed to" + "</font>";
                    previous = "<font color=#FF0000>" + previous + "</font>";
                    viewHolder.txtView.setText(Html.fromHtml(previous + "<br/>" + txt + "<br/>" + menuItem));
                } else {
                    viewHolder.txtView.setText(menuItem);
                }
            } else {
                viewHolder.txtView.setText(menuItem);
            }
        } else {
            if (showPrevious) {
                if (previousCode.length() > 0 && !previousCode.equalsIgnoreCase(" ")) {
                    menuItem = "<font color=#003399>" + "Blank Route Name" + "</font>";
                    String txt = "<font color=#000000>" + "has been changed to" + "</font>";
                    previous = "<font color=#FF0000>" + previous + "</font>";
                    viewHolder.txtView.setText(Html.fromHtml(previous + "<br/>" + txt + "<br/>" + menuItem));
                } else {
                    viewHolder.txtView.setText("Blank Route Name");
                }
            } else {
                viewHolder.txtView.setText("Blank Route Name");
            }
        }
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}