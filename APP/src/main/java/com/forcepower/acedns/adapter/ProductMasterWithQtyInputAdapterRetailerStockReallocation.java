package com.forcepower.acedns.adapter;

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
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.invisibleTVProdCode;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.qtySrate;

public class ProductMasterWithQtyInputAdapterRetailerStockReallocation extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapterRetailerStockReallocation(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        Constants.selectedProductMasterList = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++) {
            Constants.selectedProductMasterList.get(i).setQty("NA");
        }
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.txtViewProductDescmsl = (TextView) convertView.findViewById(R.id.list_details2);
        viewHolder.list_details3 = (TextView) convertView.findViewById(R.id.list_details3);
        viewHolder.txtViewProductDescmsl.setVisibility(View.VISIBLE);
        viewHolder.list_details3.setVisibility(View.VISIBLE);
        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(invisibleTVProdCode);

        convertView.setTag(viewHolder);

        String menuItem = Constants.selectedProductMasterList.get(position).getDesc();
        viewHolder.txtViewProductDesc.setText(menuItem);
        viewHolder.txtViewProductDescmsl.setText(Constants.selectedProductMasterList.get(position).getallocatedQty());
        viewHolder.list_details3.setText(Constants.selectedProductMasterList.get(position).getQtyRemaining());
        CharSequence text = viewHolder.invisibleTVProdCode.getText();

        if (text == null || text.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));
        }
        String prodCode = Constants.selectedProductMasterList.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = Constants.selectedProductMasterList.get(position).getQty();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, txtViewProductDescmsl, list_details3;
        EditText qtyET;
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
            Constants.selectedProductMasterList.get(pos).setQty(text);
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