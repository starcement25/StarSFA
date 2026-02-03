package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class CustomerInfoDetailsListAdapter5 extends ArrayAdapter<commonDatabaseHelper>
{
    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;

    public CustomerInfoDetailsListAdapter5(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
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

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);


        viewHolder = new ViewHolder();
        viewHolder.firstValueTV = (TextView) convertView.findViewById(R.id.firstValueTV);
        viewHolder.secondValueTV = (TextView) convertView.findViewById(R.id.secondValueTV);
        viewHolder.thirdValueTV = (TextView) convertView.findViewById(R.id.thirdValueTV);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = priceListOilGrp.get(position).getItem0();

        viewHolder.firstValueTV.setText(Html.fromHtml(desc));
        viewHolder.secondValueTV.setText(Html.fromHtml( Constants.defaultFormatWithComma.format(Double.parseDouble(priceListOilGrp.get(position).getItem1()))));
        viewHolder.thirdValueTV.setText(Html.fromHtml( priceListOilGrp.get(position).getItem2()));
        return convertView;
    }


    public class ViewHolder {
        TextView firstValueTV,secondValueTV, invisibleTVProdCode,thirdValueTV;
    }
}