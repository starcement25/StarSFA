package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MerchandisingDetails;

import java.util.ArrayList;

public class MerchandisingAdapter extends ArrayAdapter<MerchandisingDetails> {

    private final Context context;
    private final ArrayList<MerchandisingDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public MerchandisingAdapter(Context context, int resourceId, ArrayList<MerchandisingDetails> nameValues) {

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
            viewHolder.txtCode = (TextView) convertView.findViewById(R.id.txt_code);
            viewHolder.txtLocation = (TextView) convertView.findViewById(R.id.txt_location);
            viewHolder.txtProblem = (TextView) convertView.findViewById(R.id.txt_problem);
            viewHolder.txtDate = (TextView) convertView.findViewById(R.id.txt_date);
            viewHolder.txtTime = (TextView) convertView.findViewById(R.id.txt_time);
            viewHolder.txtProperty = (TextView) convertView.findViewById(R.id.txt_property);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String year = nameValues.get(position).getMerchandisingId().substring(7, 11);
        String month = nameValues.get(position).getMerchandisingId().substring(11, 13);
        String day = nameValues.get(position).getMerchandisingId().substring(13, 15);
        String date = day + "-" + month + "-" + year;
        String hour = nameValues.get(position).getMerchandisingId().substring(15, 17);
        String mins = nameValues.get(position).getMerchandisingId().substring(17, 19);
        String secs = nameValues.get(position).getMerchandisingId().substring(19, 21);
        String time = hour + ":" + mins + ":" + secs;
        String location = nameValues.get(position).getProdDesc();
        String code = nameValues.get(position).getProdCode();
        String issueDetailsTxt = "";
        String[] issueDetailsArray = nameValues.get(position).getRemarks().split(";");
        for (int ii = 0; ii < issueDetailsArray.length; ii++) {
            if (issueDetailsArray[ii].trim().length() > 0) {
                if (ii != (issueDetailsArray.length - 1)) {
                    issueDetailsTxt = issueDetailsTxt + issueDetailsArray[ii] + " : ";
                } else {
                    issueDetailsTxt = issueDetailsTxt + issueDetailsArray[ii];
                }
            }
        }


        viewHolder.txtCode.setText(code);
        viewHolder.txtProperty.setText(nameValues.get(position).getProdGroup());
        viewHolder.txtLocation.setText(location);
        viewHolder.txtProblem.setText(issueDetailsTxt);
        viewHolder.txtDate.setText(date);
        viewHolder.txtTime.setText(time);


        return convertView;
    }


    public class ViewHolder {
        TextView txtCode, txtLocation, txtProblem, txtDate, txtTime, txtProperty;
    }
}