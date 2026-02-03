package com.forcepower.acedns.adapter;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Filterable;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.cardview.widget.CardView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.LeadGenerationApprovalDetailsActivity;
import com.forcepower.acedns.bean.CommonHelper;

import java.util.ArrayList;

public class LeadGenerationListDetailsAdapter extends ArrayAdapter<CommonHelper> implements Filterable {

    private final Context context;
    private final ArrayList<CommonHelper> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public LeadGenerationListDetailsAdapter(Context context, int resourceId, ArrayList<CommonHelper> nameValues) {

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
            viewHolder.tv_dis = (TextView) convertView.findViewById(R.id.tv_dis);
            viewHolder.tv_val = convertView.findViewById(R.id.tv_val);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.tv_dis.setText(nameValues.get(position).getItem0());
        viewHolder.tv_val.setText(nameValues.get(position).getItem1());


        return convertView;
    }


    public class ViewHolder {
        TextView tv_dis,tv_val;

        CardView card_game;

    }
}