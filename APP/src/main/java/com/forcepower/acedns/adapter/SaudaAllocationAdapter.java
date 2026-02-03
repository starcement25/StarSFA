package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.InputType;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SaudaAllocation;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class SaudaAllocationAdapter extends ArrayAdapter<SaudaAllocation> {

    private final Context mContext;
    private final ArrayList<SaudaAllocation> mSaudaAllocationList;
    private final int resourceId;
    private ViewHolder viewHolder;
    private boolean isBoss = false;

    public SaudaAllocationAdapter(Context context, int resourceId, ArrayList<SaudaAllocation> nameValues, boolean isBoss) {
        super(context, resourceId, nameValues);
        this.mContext = context;
        this.mSaudaAllocationList = nameValues;
        this.resourceId = resourceId;
        this.isBoss = isBoss;
    }

    public void SetData(int position, String value) {
        if (position < Constants.mSaudaAllocationList.size()) {
            //String groupname=Constants.mSaudaAllocationList.get(position).getProductFilterName();
            double maxallocationinton = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getQuantityinTon());
            double conversionfactortwo = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getConversionFactorTwo());
            double quantityinltr = 0;
            if (value.length() > 0) {
                if (isBoss == false) {
                    if (maxallocationinton >= Double.parseDouble(value)) {
                        quantityinltr = conversionfactortwo * Double.parseDouble(value);
                        Constants.mSaudaAllocationList.get(position).setQty(value);
                        Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                        Constants.mSaudaAllocationList.get(position).setValidation("OK");
                    } else {
                        Constants.mSaudaAllocationList.get(position).setValidation("NOTOK");
                        //Utils.showToast(mContext, groupname+" allocated quantity exceed");
                    }
                } else {
                    quantityinltr = conversionfactortwo * Double.parseDouble(value);
                    Constants.mSaudaAllocationList.get(position).setQty(value);
                    Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                    Constants.mSaudaAllocationList.get(position).setValidation("OK");
                }
            }
        }
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.col1 = (LinearLayout) convertView.findViewById(R.id.col1_layout);
            viewHolder.col2 = (LinearLayout) convertView.findViewById(R.id.col2_layout);

            viewHolder.txtProductGroupName = (TextView) convertView.findViewById(R.id.textViewProductGroupName);
            viewHolder.textViewMaxAllocation = (TextView) convertView.findViewById(R.id.textViewMaxQty);
            viewHolder.editTextQty = (EditText) convertView.findViewById(R.id.editTextPTD);

            viewHolder.editTextQty.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            viewHolder.editTextQty.setImeOptions(EditorInfo.IME_ACTION_DONE);


            viewHolder.editTextQty.setId(position);
            viewHolder.editTextQty.setText("0");

            //we need to update adapter once we finish with editing

            viewHolder.editTextQty.setOnKeyListener(new EditText.OnKeyListener() {
                @Override
                public boolean onKey(View v, int keyCode, KeyEvent event) {
                    final int position = v.getId();
                    final EditText Caption = (EditText) v;
                    String value = Caption.getText().toString();
                    SetData(position, value);
                    Log.i("PTD", "Position " + position + " Value " + value);
                    return false;
                }
            });

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtProductGroupName.setText(mSaudaAllocationList.get(position).getProductFilterName());
        viewHolder.textViewMaxAllocation.setText(mSaudaAllocationList.get(position).getQuantityinTon());
        return convertView;
    }

    public class ViewHolder {
        TextView txtProductGroupName, textViewMaxAllocation;
        EditText editTextQty;
        LinearLayout col1, col2;
    }
}
