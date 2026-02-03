package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class PreviousDateRateSelectionAdapter extends ArrayAdapter<commonDatabaseHelper> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    int positionOfSelectedItem = 0;
    private ViewHolder viewHolder;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;
    private Boolean isRateEditable = false;
    public PreviousDateRateSelectionAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
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
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);
        viewHolder.rateTv =  convertView.findViewById(R.id.rateTv);
        viewHolder.dateTv =  convertView.findViewById(R.id.dateTv);
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);
        }

        viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = priceListOilGrp.get(position).getItem0();

        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
        viewHolder.rateTv.setText(Html.fromHtml( priceListOilGrp.get(position).getItem1()));
        viewHolder.dateTv.setText(Html.fromHtml( priceListOilGrp.get(position).getItem2()));
//        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml(desc+"<font color='#D7B56D'>/</font>"+nameValuesProductListLocalDo.get(position).getuom4(),HtmlCompat.FROM_HTML_MODE_LEGACY));



        return convertView;
    }


    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode,rateTv,dateTv;
        LinearLayout list_details_ll;
    }

}