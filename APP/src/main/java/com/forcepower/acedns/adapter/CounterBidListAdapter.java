package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.PlantProductWiseRARate;

import static com.forcepower.acedns.R.id.qtyTV;
import static com.forcepower.acedns.constants.Constants.CounterBidListListForRAOnTodayByCustomerCode;

public class CounterBidListAdapter extends ArrayAdapter<PlantProductWiseRARate> {

    private final Context mContext;
    private final int resourceId;
    private ViewHolder viewHolder;

    public CounterBidListAdapter(Context context, int resourceId) {
        super(context, resourceId, CounterBidListListForRAOnTodayByCustomerCode);
        this.mContext = context;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

//		if (convertView == null)
//		{
        LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();

        viewHolder.productTV = (TextView) convertView.findViewById(R.id.productTV);
        viewHolder.indicativeRateTV = (TextView) convertView.findViewById(R.id.indicativeRateTV);
        viewHolder.qtyTV = (TextView) convertView.findViewById(qtyTV);
        viewHolder.bidRateTV = (TextView) convertView.findViewById(R.id.bidRateTV);
        viewHolder.counterBidRateTV = (TextView) convertView.findViewById(R.id.counterBidRateTV);


        viewHolder.radioStatus = (RadioGroup) convertView.findViewById(R.id.radioStatus);
        String[] splitedStatus = "A,R".split(",");
        final RadioButton[] rb = new RadioButton[splitedStatus.length];
        viewHolder.radioStatus.setOrientation(RadioGroup.VERTICAL);//or RadioGroup.HORIZONTAL
        for (int i = 0; i < splitedStatus.length; i++) {
            rb[i] = new RadioButton(mContext);
            String currentStatus = splitedStatus[i];
            rb[i].setText(currentStatus);
            if (CounterBidListListForRAOnTodayByCustomerCode.get(position).getCounterBidStatus().equalsIgnoreCase(currentStatus)) {
                rb[i].setChecked(true);
            }
            rb[i].setTextColor(mContext.getResources().getColor(R.color.text_color));
            rb[i].setId(i);
            viewHolder.radioStatus.addView(rb[i]);
        }
        viewHolder.radioStatus.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                String appStatus = "A,R";
                String statusOnSelectedItem = appStatus.split(",")[checkedId];

                CounterBidListListForRAOnTodayByCustomerCode.get(position).setCounterBidStatus(statusOnSelectedItem);
            }
        });

        convertView.setTag(viewHolder);

        viewHolder.productTV.setText(CounterBidListListForRAOnTodayByCustomerCode.get(position).getprodName());
        viewHolder.indicativeRateTV.setText(CounterBidListListForRAOnTodayByCustomerCode.get(position).getIndicativeRateApp());
        viewHolder.qtyTV.setText(CounterBidListListForRAOnTodayByCustomerCode.get(position).getQty());
        viewHolder.bidRateTV.setText(CounterBidListListForRAOnTodayByCustomerCode.get(position).getbidPrice());
        viewHolder.counterBidRateTV.setText(CounterBidListListForRAOnTodayByCustomerCode.get(position).getCounterBidRate());

        return convertView;
    }

    public class ViewHolder {
        TextView productTV, indicativeRateTV, qtyTV, bidRateTV, counterBidRateTV;
        RadioGroup radioStatus;
    }

}