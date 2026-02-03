package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.TargetAchievementRouteCategorywise;

import java.util.ArrayList;

public class TargetRouteWiseLandingAdapter extends ArrayAdapter<TargetAchievementRouteCategorywise> implements Filterable {

    private final Context context;
    private final ArrayList<TargetAchievementRouteCategorywise> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public TargetRouteWiseLandingAdapter(Context context, int resourceId, ArrayList<TargetAchievementRouteCategorywise> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.route_name = (TextView) convertView.findViewById(R.id.textViewroute_name);
            viewHolder.DTS_target = convertView.findViewById(R.id.textViewDTS_target);
            viewHolder.DTS_achievement = convertView.findViewById(R.id.textViewDTS_achievement);
            viewHolder.DW_target = convertView.findViewById(R.id.textViewDW_target);
            viewHolder.DW_achievement = convertView.findViewById(R.id.textViewDW_achievement);
            viewHolder.others_target = convertView.findViewById(R.id.textViewothers_target);
            viewHolder.others_achievement = convertView.findViewById(R.id.textViewothers_achievement);
            viewHolder.textViewTotalTarget = convertView.findViewById(R.id.textViewTotalTarget);
            viewHolder.textViewTotalAchv = convertView.findViewById(R.id.textViewTotalAchv);



            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        //Date dd=new Date(menuItem);
        //SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        //String date = format.format(dd);
        viewHolder.route_name.setText(nameValues.get(position).getRoute_name());
        viewHolder.DTS_target.setText(nameValues.get(position).getDTS_target());
        viewHolder.DTS_achievement.setText(nameValues.get(position).getDTS_achievement());
        viewHolder.DW_target.setText(nameValues.get(position).getDW_target());
        viewHolder.DW_achievement.setText(nameValues.get(position).getDW_achievement());
        viewHolder.others_target.setText(nameValues.get(position).getOthers_target());
        viewHolder.others_achievement.setText(nameValues.get(position).getOthers_achievement());

        try {
            //int t = Integer.parseInt(nameValues.get(position).getDTS_target())+Integer.parseInt(nameValues.get(position).getDW_target()) + Integer.parseInt(nameValues.get(position).getOthers_target());
            viewHolder.textViewTotalTarget.setText(""+nameValues.get(position).getEmp_name());
            //int ta = Integer.parseInt(nameValues.get(position).getDTS_achievement())+Integer.parseInt(nameValues.get(position).getDW_achievement()) + Integer.parseInt(nameValues.get(position).getOthers_achievement());
            viewHolder.textViewTotalAchv.setText(""+nameValues.get(position).getArea());

        }catch (Exception e){

        }

        return convertView;
    }


    public class ViewHolder {
        TextView route_name,DTS_target,DTS_achievement,DW_target,DW_achievement,others_target,others_achievement,textViewTotalTarget,textViewTotalAchv;
    }
}