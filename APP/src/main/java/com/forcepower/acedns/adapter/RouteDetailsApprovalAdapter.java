package com.forcepower.acedns.adapter;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Filterable;
import android.widget.TextView;


import com.forcepower.acedns.R;

import com.forcepower.acedns.activity.RoutePlanApprovalActivity;
import com.forcepower.acedns.bean.RouteDetailsAproval;

import java.util.ArrayList;

public class RouteDetailsApprovalAdapter extends ArrayAdapter<RouteDetailsAproval> implements Filterable {

    private final Context context;
    private final ArrayList<RouteDetailsAproval> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public RouteDetailsApprovalAdapter(Context context, int resourceId, ArrayList<RouteDetailsAproval> nameValues) {

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
            viewHolder.empname = (TextView) convertView.findViewById(R.id.empname);
            viewHolder.etxt_name = convertView.findViewById(R.id.routename);
            viewHolder.visitdate = convertView.findViewById(R.id.visitdate);
            viewHolder.btnSub = convertView.findViewById(R.id.btn_submit);
            viewHolder.tvRemarks = convertView.findViewById(R.id.tvRemarks);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getEmp_code();
        //Date dd=new Date(menuItem);
        //SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        //String date = format.format(dd);
        viewHolder.empname.setText(nameValues.get(position).getEmp_name()+" ("+menuItem+")");
        viewHolder.etxt_name.setText(nameValues.get(position).getRoute_name());
        viewHolder.visitdate.setText(nameValues.get(position).getVisit_date());
        viewHolder.tvRemarks.setText(nameValues.get(position).getRemarks());

        viewHolder.btnSub.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                AlertDialog.Builder builder = new AlertDialog.Builder(context);

                builder.setTitle("Confirm");
                builder.setMessage("Are you sure?");

                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {

                        if (context instanceof RoutePlanApprovalActivity) {
                            ((RoutePlanApprovalActivity)context).sendApproval(position);
                        }
                        dialog.dismiss();
                    }
                });

                builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {

                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        //Toast.makeText(context, "no"+position, Toast.LENGTH_SHORT).show();
                        // Do nothing
                        dialog.dismiss();
                    }
                });

                AlertDialog alert = builder.create();
                alert.show();
            }
        });
        //viewHolder.txtView.setTypeface

        return convertView;
    }


    public class ViewHolder {
        TextView etxt_name,empname,visitdate,tvRemarks;
        Button btnSub;
    }
}