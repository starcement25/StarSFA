package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.StockistVisitActivity;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.CustomerDetails;

import java.util.ArrayList;

public class NewStokistVisitAdapter extends ArrayAdapter<CustomerDetails> {
    private final ArrayList<CustomerDetails> mProductMasterDetailsList;
    private final int mResourceId;
    Context mContext;


    public NewStokistVisitAdapter(Context context, int resourceId, ArrayList<CustomerDetails> productMasterDetailsList) {
        super(context, resourceId, productMasterDetailsList);
        this.mContext = context;
        this.mResourceId = resourceId;
        this.mProductMasterDetailsList = productMasterDetailsList;
    }

    @Override
    public int getCount() {
        return mProductMasterDetailsList.size();
    }

   /* @Override
    public ProductMasterDetails getItem(int position) {
        return mProductMasterDetailsList.get(position);
    }*/

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
            mViewHolder.mEditTextQty = (EditText) convertView.findViewById(R.id.etProdQty);
            mViewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
            mViewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
            mViewHolder.mEditTextTD = (EditText) convertView.findViewById(R.id.etFoldeerQty);
            //mViewHolder.mButtonAddtoCart = (Button) convertView.findViewById(R.id.buttonAddtoCart);

            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }


            String menuItem = mProductMasterDetailsList.get(position).getCustomerName();
            mViewHolder.mTextViewName.setText("" + menuItem);

            String code = mProductMasterDetailsList.get(position).getCustomerCode();
            mViewHolder.invisibleTVProdCode.setText(code);
            mViewHolder.invisibleTVProdCode.setVisibility(View.GONE);

            mViewHolder.addtocartIV.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    String sal = mViewHolder.mEditTextQty.getText().toString();
                    String folder = mViewHolder.mEditTextTD.getText().toString();
                    if(sal.isEmpty()){
                        Toast.makeText(mContext, "Enter Sale value", Toast.LENGTH_LONG).show();
                    }else if(folder.isEmpty()){
                        Toast.makeText(mContext, "Enter Folder value", Toast.LENGTH_LONG).show();
                    }else {
                        if (mContext instanceof StockistVisitActivity) {
                            ((StockistVisitActivity) mContext).setList(sal, folder, mViewHolder.invisibleTVProdCode.getText().toString(),position,mViewHolder.mTextViewName.getText().toString());
                        }
                        mViewHolder.mEditTextTD.setText("");
                        mViewHolder.mEditTextQty.setText("");
                        //tempStokistRetailList.remove(position);
                        //notifyDataSetChanged();
                    }
                    //Toast.makeText(mContext, "Click Me " + folder, Toast.LENGTH_LONG).show();
                }
            });


        return convertView;
    }

    public class ViewHolder {
        TextView mTextViewName, mEditTextQty,invisibleTVProdCode;
        EditText mEditTextTD;
        ImageView addtocartIV;
    }
}
