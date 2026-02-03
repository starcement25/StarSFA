package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.RedeemeDetails;

import java.util.ArrayList;

public class RedeemeAdapter extends ArrayAdapter<RedeemeDetails> {

    private final Context context;
    private final ArrayList<RedeemeDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public RedeemeAdapter(Context context, int resourceId, ArrayList<RedeemeDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtPoint = (TextView) convertView.findViewById(R.id.point);
            viewHolder.txtAward = (TextView) convertView.findViewById(R.id.award);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String awardStr = "";
        String[] awardArray = nameValues.get(position).getAward().split("/");
        for (int ii = 0; ii < awardArray.length; ii++) {
            awardStr = awardStr + "   * " + awardArray[ii] + "\n";
        }

        viewHolder.txtPoint.setText(nameValues.get(position).getPoints() + "Points");
        viewHolder.txtAward.setText(awardStr);


        return convertView;
    }


    public class ViewHolder {
        TextView txtPoint, txtAward;
    }
}