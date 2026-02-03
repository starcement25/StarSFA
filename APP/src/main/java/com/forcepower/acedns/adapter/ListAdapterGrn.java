package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import java.util.ArrayList;

/**
 * Created by Amitabha2715 on 21June'19
 */

public class ListAdapterGrn extends BaseAdapter
{
    private Activity activity;
    private ArrayList<commonDatabaseHelper> participentListItemGlobal;

    public ListAdapterGrn(Activity activity, ArrayList<commonDatabaseHelper> participentListItemGlobal)
    {
        this.activity = activity;
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
        convertView=inflater.inflate(R.layout.list_item_team_name, null);

        TextView name=(TextView)convertView.findViewById(R.id.tv_team_name);
        name.setText(participentListItemGlobal.get(position).getItem0());

        TextView tv_hidden_value_1=(TextView)convertView.findViewById(R.id.tv_hidden_value_1);
        tv_hidden_value_1.setText(participentListItemGlobal.get(position).getItem1()); //customer_code

        TextView tv_hidden_value_2=(TextView)convertView.findViewById(R.id.tv_hidden_value_2);
        tv_hidden_value_2.setText(participentListItemGlobal.get(position).getItem2()); //destination

        TextView tv_hidden_value_3=(TextView)convertView.findViewById(R.id.tv_hidden_value_3);
        tv_hidden_value_3.setText(participentListItemGlobal.get(position).getItem3()); //DO_no

        return convertView;
    }
}
