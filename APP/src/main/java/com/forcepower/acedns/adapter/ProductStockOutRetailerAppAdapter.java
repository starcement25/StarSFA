package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.tempProductList;

import com.forcepower.acedns.activity.OrderFormActivityAlternateDesign;

public class ProductStockOutRetailerAppAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    private ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private Boolean isRateEditable = false;


    public ProductStockOutRetailerAppAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++)
        {
            nameValuesProductListLocal.get(i).setQty("NA");
        }
    }


    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);

        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        viewHolder.txtViewProductDesc.setText(nameValuesProductListLocal.get(position).getDesc());


        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals(""))
        {
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));
        }

        String prodCode = nameValuesProductListLocal.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = nameValuesProductListLocal.get(position).getQty();

        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode;
        EditText qtyET;
        ImageView addtocartIV;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;

        private GenericTextWatcherQty(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setQty(text);
        }
    }

    private class GenericItemCLickListener implements View.OnClickListener
    {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view)
        {
            addfreeBiesForCurrentProduct(nameValuesProductListLocal.get(pos), pos);

        }

        public void addfreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails, int pos) {
            //        ProductMasterDetails productMasterDetails = ;
            String qty = productMasterDetails.getQty();
            //            Toast.makeText(context,nameValuesProductListLocalDo.get(pos).getDesc()+ "", Toast.LENGTH_SHORT).show();
            if (Utils.isNumeric(qty))
            {

                Constants.selectedProductMasterList.add(productMasterDetails);
                tempProductList.remove(pos);
                notifyDataSetChanged();
                OrderFormActivityAlternateDesign.changeCartCount();
            }
            else
            {
                Toast.makeText(context, "Provide proper quantity for stock out.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    private class GenericEditorActionListener implements TextView.OnEditorActionListener {
        EditText tv;
        private int pos;

        private GenericEditorActionListener(int pos, EditText tv) {
            this.pos = pos;
            this.tv = tv;
        }

        @Override
        public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {

            if (actionId == EditorInfo.IME_ACTION_DONE) {
                if (sizeOfList - (pos + 1) > 4) {
                    prodQtyRateListView.smoothScrollToPosition(pos + 1 + 5);
                } else if (pos + 1 == sizeOfList) {
                    Toast.makeText(context, "End of List", Toast.LENGTH_SHORT).show();
                } else {
                    prodQtyRateListView.smoothScrollToPosition(sizeOfList);
                }


                return true;
            }
            return false;
        }
    }

    private class GenericEditorOnTouchListener implements EditText.OnTouchListener {
        String rateOrQtyOrStk;

        private GenericEditorOnTouchListener(String rateOrQtyOrStk, EditText tv) {
            this.rateOrQtyOrStk = rateOrQtyOrStk;
            //        qtySrate = tv;
            //        isQtySelected
        }

        @Override
        public boolean onTouch(View v, MotionEvent event) {
            EditText edittext = (EditText) v;
            qtySrate = edittext;
            if (rateOrQtyOrStk.matches("qty")) {
                isQtySelected = true;
                isStkQtySelected = false;
            } else if (rateOrQtyOrStk.matches("rate")) {
                isQtySelected = false;
                isStkQtySelected = false;
            } else {
                isStkQtySelected = true;
            }
            int inType = edittext.getInputType();       // Backup the input type
            edittext.setInputType(InputType.TYPE_NULL); // Disable standard keyboard
            edittext.onTouchEvent(event);               // Call native handler
            edittext.setInputType(inType);              // Restore input type
            return true; // Consume touch event
        }

    }

}