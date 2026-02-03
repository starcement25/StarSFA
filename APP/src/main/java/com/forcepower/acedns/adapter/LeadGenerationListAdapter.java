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

public class LeadGenerationListAdapter extends ArrayAdapter<CommonHelper> implements Filterable {

    private final Context context;
    private final ArrayList<CommonHelper> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public LeadGenerationListAdapter(Context context, int resourceId, ArrayList<CommonHelper> nameValues) {

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
            viewHolder.btn_details = convertView.findViewById(R.id.btn_details);
            viewHolder.tvActualDate = convertView.findViewById(R.id.tvActualDate);
            viewHolder.tvDeliveryRemarks = convertView.findViewById(R.id.tvDeliveryRemarks);
            viewHolder.tvNotDeliveryReason = convertView.findViewById(R.id.tvNotDeliveryReason);

            viewHolder.llNotDelReson = convertView.findViewById(R.id.llNotDelReson);
            viewHolder.llActualDate = convertView.findViewById(R.id.llActualDate);
            viewHolder.llRemarks = convertView.findViewById(R.id.llRemarks);

            viewHolder.tvRemarks = convertView.findViewById(R.id.tvRemarks);
            viewHolder.tvVisitType = convertView.findViewById(R.id.tvVisitType);
            viewHolder.card_game = convertView.findViewById(R.id.card_game);


            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.empname.setText(nameValues.get(position).getItem1());
        viewHolder.etxt_name.setText(nameValues.get(position).getItem4());
        viewHolder.tvRemarks.setText(nameValues.get(position).getItem2());
        viewHolder.visitdate.setText(nameValues.get(position).getItem3());
        viewHolder.tvVisitType.setText(nameValues.get(position).getItem6());
        viewHolder.tvProduct.setText(nameValues.get(position).getItem7());
        //viewHolder.empname.setText(nameValues.get(position).getItem1());


        viewHolder.card_game.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {


                Intent intent = new Intent(context, LeadGenerationApprovalDetailsActivity.class);
                intent.putExtra("id", nameValues.get(position).getItem4());
                intent.putExtra("s_id", nameValues.get(position).getItem8());
                context.startActivity(intent);

            }
        });
        //viewHolder.txtView.setTypeface

        viewHolder.btn_details.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(context, LeadGenerationApprovalDetailsActivity.class);
                intent.putExtra("id", nameValues.get(position).getItem4());
                intent.putExtra("s_id", nameValues.get(position).getItem8());
                context.startActivity(intent);

            }
        });

        return convertView;
    }


    public class ViewHolder {
        TextView etxt_name,empname,visitdate,tvRemarks,tvStatus,tvDate,tvBag,tvProduct,tvDealerCode,tvActualDate,tvDeliveryRemarks,tvNotDeliveryReason,tvVisitType;
        Button btnSub,btn_details;
        CardView card_game;

        LinearLayout llNotDelReson,llActualDate,llRemarks;
    }
}