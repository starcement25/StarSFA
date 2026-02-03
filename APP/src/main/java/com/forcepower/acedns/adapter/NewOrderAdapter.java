package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;

import java.util.ArrayList;

public class NewOrderAdapter extends ArrayAdapter<ProductMasterDetails> {
    private final ArrayList<ProductMasterDetails> mProductMasterDetailsList;
    private final int mResourceId;
    Context mContext;
    private ViewHolder mViewHolder;

    public NewOrderAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> productMasterDetailsList) {
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
    public ProductMasterDetails getItem(int position) {
        return mProductMasterDetailsList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView,
                        ViewGroup parent) {

        final int pos = position;

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(mResourceId, parent, false);
            mViewHolder = new ViewHolder();
            mViewHolder.mTextViewName = (TextView) convertView.findViewById(R.id.textViewProductName);
            mViewHolder.mEditTextQty = (EditText) convertView.findViewById(R.id.editTextQuantity);
            mViewHolder.mEditTextSaleRate = (EditText) convertView.findViewById(R.id.editTextSaleRate);
            mViewHolder.mEditTextClStk = (EditText) convertView.findViewById(R.id.editTextCLStk);
            mViewHolder.mEditTextTD = (EditText) convertView.findViewById(R.id.editTextTD);
            mViewHolder.mButtonAddtoCart = (Button) convertView.findViewById(R.id.buttonAddtoCart);

            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = mProductMasterDetailsList.get(position).getDesc();
        mViewHolder.mTextViewName.setText(menuItem);


        mViewHolder.mButtonAddtoCart.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Toast.makeText(mContext, "Click Me " + String.valueOf(pos), Toast.LENGTH_LONG).show();
            }
        });
        return convertView;
    }

    public class ViewHolder {
        TextView mTextViewName;
        EditText mEditTextQty;
        EditText mEditTextSaleRate;
        EditText mEditTextClStk;
        EditText mEditTextTD;
        Button mButtonAddtoCart;
    }
}
