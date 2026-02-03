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
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.invisibleTVProdCode;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.plantListForRANewBid;

public class ProductMasterWithQtyInputAdapterRetailerNewBid extends ArrayAdapter<PlantProductWiseRARate> {

    private final Context context;
    private final int resourceId;
    //AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapterRetailerNewBid(Context context, int resourceId, ArrayList<PlantProductWiseRARate> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.resourceId = resourceId;
//    mAceDnsDatabase=new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++) {
            plantListForRANewBid.get(i).setQty("NA");
            plantListForRANewBid.get(i).setbidPrice("NA");
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
//    viewHolder.list_details_plant = (TextView) convertView.findViewById(R.id.list_details_plant);

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.etProdBidPrice = (EditText) convertView.findViewById(R.id.etProdBidPrice);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(invisibleTVProdCode);

        convertView.setTag(viewHolder);

        String menuItem = Constants.plantListForRANewBid.get(position).getprodName();
        viewHolder.txtViewProductDesc.setText(menuItem);
        viewHolder.txtViewProductDescmsl.setText(Constants.plantListForRANewBid.get(position).getIndicativeRateApp());
//    viewHolder.list_details_plant.setText( Constants.plantListForRANewBid.get(position).getPlantName());
        CharSequence text = viewHolder.invisibleTVProdCode.getText();

        if (text == null || text.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.etProdBidPrice.addTextChangedListener(new GenericTextWatcherBidPrice(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
        }
        String prodCode = Constants.plantListForRANewBid.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = Constants.plantListForRANewBid.get(position).getQty();
        String currentBidPrice = Constants.plantListForRANewBid.get(position).getbidPrice();
        if (!currentBidPrice.matches("NA")) {
            viewHolder.etProdBidPrice.setText(currentBidPrice);
        }
        if (!currentqty.matches("NA")) {
            viewHolder.qtyET.setText(currentqty);
        }

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, txtViewProductDescmsl;
        EditText qtyET, etProdBidPrice;
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
            Constants.plantListForRANewBid.get(pos).setQty(text);
        }
    }

    private class GenericTextWatcherBidPrice implements TextWatcher {

        private int pos;

        private GenericTextWatcherBidPrice(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            Constants.plantListForRANewBid.get(pos).setbidPrice(text);
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