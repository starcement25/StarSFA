package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.CustomerDetails;

import java.util.ArrayList;


/***** Adapter class extends with ArrayAdapter ******/
public class RecommendedAditionalCustomerSpinnerAdapter extends BaseAdapter
{
    private Activity activity;
    private Context ctx;
    private Boolean isDealer=false;
    private ArrayList<CustomerDetails> participentListItemGlobal;

    public RecommendedAditionalCustomerSpinnerAdapter(Context ctx, ArrayList<CustomerDetails> participentListItemGlobal)
    {
        this.ctx = ctx;
        activity =(Activity)ctx ;
        this.participentListItemGlobal = participentListItemGlobal;
    }
    public RecommendedAditionalCustomerSpinnerAdapter(Context ctx, ArrayList<CustomerDetails> participentListItemGlobal,Boolean isDealer)
    {
        this.ctx = ctx;
        this.isDealer = isDealer;
        activity =(Activity)ctx ;
        this.participentListItemGlobal = participentListItemGlobal;
    }

    @Override
    public int getCount() {

        return participentListItemGlobal.size();
    }

    @Override
    public Object getItem(int position) {

        return null;
    }

    @Override
    public long getItemId(int position) {

        return 0;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {

        LayoutInflater inflater= (LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView=inflater.inflate(R.layout.list_item_customer_route_dealer_info, null);

        TextView name=(TextView)convertView.findViewById(R.id.tv_team_name);
        TextView badge_tv=(TextView)convertView.findViewById(R.id.badge_tv);
        name.setText(participentListItemGlobal.get(position).getCustomerName());
        String custClass=participentListItemGlobal.get(position).getCustClass();
        if(!isDealer){
            if(custClass.equalsIgnoreCase("a")){
                name.setTextColor(ctx.getResources().getColor(R.color.red));
            }
            else if(custClass.equalsIgnoreCase("b")){
                name.setTextColor(ctx.getResources().getColor(R.color.colorOrangeAppGreyDark));
            }
            else if(custClass.equalsIgnoreCase("c")) {
                name.setTextColor(ctx.getResources().getColor(R.color.text_color));
            }
        }


        badge_tv.setVisibility(View.GONE);
//        badge_tv.setText(participentListItemGlobal.get(position).getCustomerName());

//        TextView tv_hidden_value_1=(TextView)convertView.findViewById(R.id.tv_hidden_value_1);
//        tv_hidden_value_1.setText(participentListItemGlobal.get(position).getItem1()); //customer_code
//
//        TextView tv_hidden_value_2=(TextView)convertView.findViewById(R.id.tv_hidden_value_2);
//        tv_hidden_value_2.setText(participentListItemGlobal.get(position).getItem2()); //destination
//
//        TextView tv_hidden_value_3=(TextView)convertView.findViewById(R.id.tv_hidden_value_3);
//        tv_hidden_value_3.setText(participentListItemGlobal.get(position).getItem3()); //DO_no

        return convertView;
    }
}
