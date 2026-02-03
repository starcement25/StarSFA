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
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class McxBaseOilRateGeneratedPriceInputAdapter extends ArrayAdapter<commonDatabaseHelper> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    int positionOfSelectedItem = 0;
    private ViewHolder viewHolder;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;

    public McxBaseOilRateGeneratedPriceInputAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
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
        viewHolder.list_details2 = (TextView) convertView.findViewById(R.id.list_details2);
        viewHolder.list_details3 = (TextView) convertView.findViewById(R.id.list_details3);
        viewHolder.matCostTV = (TextView) convertView.findViewById(R.id.matCostTV);
        viewHolder.proCostTV = (TextView) convertView.findViewById(R.id.proCostTV);
        viewHolder.packingTV = (TextView) convertView.findViewById(R.id.packingTV);
        viewHolder.marginCostTV = (TextView) convertView.findViewById(R.id.marginCostTV);
        viewHolder.bargainRateTV = (TextView) convertView.findViewById(R.id.bargainRateTV);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);
        viewHolder.list_details2.setVisibility(View.VISIBLE);
        viewHolder.list_details3.setVisibility(View.VISIBLE);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = priceListOilGrp.get(position).getItem1();

        viewHolder.txtViewProductDesc.setText(Html.fromHtml( desc));
//        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml(desc+"<font color='#D7B56D'>/</font>"+nameValuesProductListLocalDo.get(position).getuom4(),HtmlCompat.FROM_HTML_MODE_LEGACY));

        String prodCode = priceListOilGrp.get(position).getItem0();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        viewHolder.matCostTV.setText(priceListOilGrp.get(position).getItem2());
        viewHolder.proCostTV.setText(priceListOilGrp.get(position).getItem3());
        viewHolder.packingTV.setText(priceListOilGrp.get(position).getItem4());
        viewHolder.marginCostTV.setText(priceListOilGrp.get(position).getItem5());
        viewHolder.bargainRateTV.setText(priceListOilGrp.get(position).getItem6());
        viewHolder.list_details2.setText("MCX Rate Open- "+priceListOilGrp.get(position).getItem7());
        viewHolder.list_details3.setText("MCX Rate Close- "+priceListOilGrp.get(position).getItem8());

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode,matCostTV,proCostTV,packingTV,marginCostTV,bargainRateTV,list_details2,list_details3;
        LinearLayout list_details_ll;
    }

}