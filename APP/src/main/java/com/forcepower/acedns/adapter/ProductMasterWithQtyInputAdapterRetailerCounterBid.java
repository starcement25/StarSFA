package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
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

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.invisibleTVProdCode;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

public class ProductMasterWithQtyInputAdapterRetailerCounterBid extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    //AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapterRetailerCounterBid(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        Constants.selectedProductMasterList = nameValues;
        this.resourceId = resourceId;
//    mAceDnsDatabase=new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++) {
            Constants.selectedProductMasterList.get(i).setQty("NA");
        }

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.txtViewProductDescmsl = (TextView) convertView.findViewById(R.id.list_details2);
//    if(Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes"))
//    {
        viewHolder.txtViewProductDescmsl.setVisibility(View.VISIBLE);
//    }
        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(invisibleTVProdCode);

        convertView.setTag(viewHolder);

        String menuItem = Constants.selectedProductMasterList.get(position).getDesc();
        viewHolder.txtViewProductDesc.setText(menuItem);
//    if(Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes"))
//    {
        viewHolder.txtViewProductDescmsl.setText(Constants.selectedProductMasterList.get(position).getindicativePrice());
//    }
        CharSequence text = viewHolder.invisibleTVProdCode.getText();

        if (text == null || text.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
        }
        String prodCode = Constants.selectedProductMasterList.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = Constants.selectedProductMasterList.get(position).getQty();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, txtViewProductDescmsl;
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
}