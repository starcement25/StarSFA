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
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.activity.SiteVisitApprovalActivity;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.SiteLeadApproval;

import java.util.ArrayList;

public class SiteLeadApprovalAdapter extends ArrayAdapter<SiteLeadApproval> implements Filterable {

    private final Context context;
    private final ArrayList<SiteLeadApproval> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public SiteLeadApprovalAdapter(Context context, int resourceId, ArrayList<SiteLeadApproval> nameValues) {

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
            viewHolder.tvStatus = (TextView) convertView.findViewById(R.id.tvStatus);
            viewHolder.tvDate = convertView.findViewById(R.id.tvDate);
            viewHolder.tvBag = convertView.findViewById(R.id.tvBag);
            viewHolder.tvProduct = convertView.findViewById(R.id.tvProduct);
            viewHolder.tvDealerCode = convertView.findViewById(R.id.tvDealerCode);
            viewHolder.btnSub = convertView.findViewById(R.id.btn_submit);
            viewHolder.btn_submit_reject = convertView.findViewById(R.id.btn_submit_reject);
            viewHolder.tvActualDate = convertView.findViewById(R.id.tvActualDate);
            viewHolder.tvDeliveryRemarks = convertView.findViewById(R.id.tvDeliveryRemarks);
            viewHolder.tvNotDeliveryReason = convertView.findViewById(R.id.tvNotDeliveryReason);

            viewHolder.llNotDelReson = convertView.findViewById(R.id.llNotDelReson);
            viewHolder.llActualDate = convertView.findViewById(R.id.llActualDate);
            viewHolder.llRemarks = convertView.findViewById(R.id.llRemarks);

            viewHolder.tvRemarks = convertView.findViewById(R.id.tvRemarks);
            viewHolder.tvVisitType = convertView.findViewById(R.id.tvVisitType);


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        //String menuItem = nameValues.get(position).getEmp_code();
        //Date dd=new Date(menuItem);
        //SimpleDateFormat format = new SimpleDateFormat("dd-MM-yyyy");
        //String date = format.format(dd);
        viewHolder.empname.setText(nameValues.get(position).getAddress());
        viewHolder.etxt_name.setText(nameValues.get(position).getCust_name());
        viewHolder.visitdate.setText(nameValues.get(position).getCust_phone());
        viewHolder.tvRemarks.setText(nameValues.get(position).getDealer_name());
        viewHolder.tvStatus.setText(nameValues.get(position).getStatus());
        viewHolder.tvDate.setText(nameValues.get(position).getRequest_date());
        viewHolder.tvBag.setText(nameValues.get(position).getBag());
        viewHolder.tvProduct.setText(nameValues.get(position).getProduct());
        viewHolder.tvDealerCode.setText(nameValues.get(position).getDelear_code());

        viewHolder.tvActualDate.setText(nameValues.get(position).getActual_delivery_date());
        viewHolder.tvDeliveryRemarks.setText(nameValues.get(position).getRemarks());
        viewHolder.tvNotDeliveryReason.setText(nameValues.get(position).getReson());
        viewHolder.tvVisitType.setText(nameValues.get(position).getVisit_type());

        viewHolder.btnSub.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                ((SiteVisitApprovalActivity)context).sendApproval(position,"approved",nameValues.get(position).getTran_id());

                /*AlertDialog.Builder builder = new AlertDialog.Builder(context);

                builder.setTitle("Confirm Approve");
                builder.setMessage("Are you sure?");

                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {

                        if (context instanceof SiteVisitApprovalActivity) {
                            ((SiteVisitApprovalActivity)context).sendApproval(position,"approved");
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
                alert.show();*/
            }
        });
        //viewHolder.txtView.setTypeface

        viewHolder.btn_submit_reject.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (context instanceof SiteVisitApprovalActivity) {
                    ((SiteVisitApprovalActivity)context).sendApproval(position,"rejected",nameValues.get(position).getTran_id());
                }
                /*AlertDialog.Builder builder = new AlertDialog.Builder(context);

                builder.setTitle("Confirm Reject");
                builder.setMessage("Are you sure?");

                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {

                        if (context instanceof SiteVisitApprovalActivity) {
                            ((SiteVisitApprovalActivity)context).sendApproval(position,"rejected",nameValues.get(position).getTran_id());
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
                alert.show();*/
            }
        });
        //viewHolder.txtView.setTypeface

        if(nameValues.get(position).getStatus().equalsIgnoreCase("pending")){
            viewHolder.btn_submit_reject.setVisibility(View.VISIBLE);
            viewHolder.btnSub.setVisibility(View.VISIBLE);
            viewHolder.llNotDelReson.setVisibility(View.GONE);
            viewHolder.llActualDate.setVisibility(View.GONE);
            viewHolder.llRemarks.setVisibility(View.GONE);
        }else if(nameValues.get(position).getStatus().equalsIgnoreCase("rejected")){
            viewHolder.btn_submit_reject.setVisibility(View.GONE);
            viewHolder.btnSub.setVisibility(View.GONE);
            viewHolder.llNotDelReson.setVisibility(View.VISIBLE);
            viewHolder.llActualDate.setVisibility(View.GONE);
            viewHolder.llRemarks.setVisibility(View.VISIBLE);
        }else if(nameValues.get(position).getStatus().equalsIgnoreCase("approved")){
            viewHolder.btn_submit_reject.setVisibility(View.GONE);
            viewHolder.btnSub.setVisibility(View.GONE);
            viewHolder.llNotDelReson.setVisibility(View.GONE);
            viewHolder.llActualDate.setVisibility(View.VISIBLE);
            viewHolder.llRemarks.setVisibility(View.VISIBLE);
        }else{

        }

        return convertView;
    }


    public class ViewHolder {
        TextView etxt_name,empname,visitdate,tvRemarks,tvStatus,tvDate,tvBag,tvProduct,tvDealerCode,tvActualDate,tvDeliveryRemarks,tvNotDeliveryReason,tvVisitType;
        Button btnSub,btn_submit_reject;

        LinearLayout llNotDelReson,llActualDate,llRemarks;
    }
}