package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.StokistDetails;

import java.util.ArrayList;

public class NewStokistVisitConfirmAdapter extends ArrayAdapter<StokistDetails> {
    private final ArrayList<StokistDetails> mProductMasterDetailsList;
    private final int mResourceId;
    Context mContext;


    public NewStokistVisitConfirmAdapter(Context context, int resourceId, ArrayList<StokistDetails> productMasterDetailsList) {
        super(context, resourceId, productMasterDetailsList);
        this.mContext = context;
        this.mResourceId = resourceId;
        this.mProductMasterDetailsList = productMasterDetailsList;
    }

    @Override
    public int getCount() {
        return mProductMasterDetailsList.size();
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView,
                        ViewGroup parent) {

        final int pos = position;
        ViewHolder mViewHolder;
        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(mResourceId, parent, false);
            mViewHolder = new ViewHolder();
            mViewHolder.mTextViewName = (TextView) convertView.findViewById(R.id.list_details);
            mViewHolder.mEditTextQty = (TextView) convertView.findViewById(R.id.HiddenValue1);
            mViewHolder.addtocartIV = (TextView) convertView.findViewById(R.id.HiddenValue3);
            //mViewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.HiddenValue3);
            mViewHolder.mEditTextTD = (TextView) convertView.findViewById(R.id.HiddenValue2);


            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }


            String menuItem = mProductMasterDetailsList.get(position).getRetail_name();
            mViewHolder.mTextViewName.setText("" + menuItem);
            mViewHolder.mEditTextQty.setText(""+mProductMasterDetailsList.get(position).getProd_name());
            mViewHolder.mEditTextTD.setText(""+mProductMasterDetailsList.get(position).getSale());
            mViewHolder.addtocartIV.setText(""+mProductMasterDetailsList.get(position).getFolder());


        return convertView;
    }

    public class ViewHolder {
        TextView mTextViewName, mEditTextQty,invisibleTVProdCode;
        TextView mEditTextTD;
        TextView addtocartIV;
    }
}
