package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class OrderListAdapter extends ArrayAdapter<commonDatabaseHelper>
{

    private final Context context;
    private final int resourceId;

    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;
    public OrderListAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        priceListOilGrp = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
//        if(position==0 || position% 2 == 0 )
//        {
//
//        }
//        else
//        {
//            convertView = inflater.inflate(resourceId2, parent, false);
//        }

        viewHolder = new ViewHolder();
        viewHolder.listItemlayout = (LinearLayout) convertView.findViewById(R.id.listItemlayout);
        viewHolder.firstValueTV = (TextView) convertView.findViewById(R.id.firstValueTV);
        viewHolder.secondValueTV = (TextView) convertView.findViewById(R.id.secondValueTV);
        if(position==0 || position% 2 == 0 )
        {

        }
        else
        {
            viewHolder.listItemlayout.setBackgroundColor(Color.parseColor("#ffffe0"));
            viewHolder.firstValueTV.setTextColor(Color.parseColor("#003399"));
            viewHolder.secondValueTV.setTextColor(Color.parseColor("#003399"));
        }

        viewHolder.secondValueTV.setVisibility(View.GONE);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);
        String desc = priceListOilGrp.get(position).getItem0();

        viewHolder.firstValueTV.setText(Html.fromHtml(desc));

        return convertView;
    }


    public class ViewHolder {
        TextView firstValueTV,secondValueTV, invisibleTVProdCode;
        LinearLayout listItemlayout;
    }

}