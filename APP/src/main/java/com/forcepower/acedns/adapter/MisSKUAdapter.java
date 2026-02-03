package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MisDetails;

import java.util.ArrayList;

public class MisSKUAdapter extends ArrayAdapter<MisDetails> implements Filterable {

    private final Context context;
    private final ArrayList<MisDetails> mMisDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public MisSKUAdapter(Context context, int resourceId, ArrayList<MisDetails> misDetailsList) {
        super(context, resourceId, misDetailsList);
        this.context = context;
        this.mMisDetailsList = misDetailsList;
        this.resourceId = resourceId;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.mTextViewSku = (TextView) convertView.findViewById(R.id.textViewSku);
            viewHolder.mTextViewBookedQtyCase = (TextView) convertView.findViewById(R.id.textViewBookedQtyCase);
            viewHolder.mTextViewBookedQtyTon = (TextView) convertView.findViewById(R.id.textViewBookedQtyTon);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.mTextViewSku.setText(mMisDetailsList.get(position).getSkuName());
        viewHolder.mTextViewBookedQtyCase.setText(mMisDetailsList.get(position).getBookedQtyCase());
        viewHolder.mTextViewBookedQtyTon.setText(mMisDetailsList.get(position).getBookedQtyTon());

        return convertView;
    }


    public class ViewHolder {
        TextView mTextViewSku;
        TextView mTextViewBookedQtyCase;
        TextView mTextViewBookedQtyTon;

    }
}