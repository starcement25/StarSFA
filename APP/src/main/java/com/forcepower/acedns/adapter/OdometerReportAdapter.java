package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OdometerReport;

import java.util.ArrayList;

public class OdometerReportAdapter extends ArrayAdapter<OdometerReport> implements Filterable {

    private final Context context;
    private final ArrayList<OdometerReport> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public OdometerReportAdapter(Context context, int resourceId, ArrayList<OdometerReport> nameValues) {

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
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.amount = convertView.findViewById(R.id.textViewCustType);
            viewHolder.list_intra = convertView.findViewById(R.id.list_intra);
            viewHolder.list_start = convertView.findViewById(R.id.list_start);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getDate();
        //Date dd=new Date(menuItem);
        //SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        //String date = format.format(dd);
        viewHolder.txtView.setText(menuItem);
        /*if(nameValues.get(position).getAttendenceId().toString().isEmpty()){
            viewHolder.list_intra.setText(nameValues.get(position).getStartkm());
        }else if(!nameValues.get(position).getAttendenceId().toString().isEmpty()){
            viewHolder.amount.setText(nameValues.get(position).getEndkm());
            viewHolder.list_start.setText(nameValues.get(position).getStartkm());
            //viewHolder.txtView.setTypeface
        }else{

        }*/
        viewHolder.list_intra.setText(nameValues.get(position).getIntrakm());
        viewHolder.amount.setText(nameValues.get(position).getEndkm());
        viewHolder.list_start.setText(nameValues.get(position).getStartkm());
        return convertView;
    }


    public class ViewHolder {
        TextView txtView,amount,list_intra,list_start;
    }
}