package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.invisibleTVProdCode;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

public class ProductMasterWithQtyInputAdapterStockReturn extends ArrayAdapter<ProductMasterDetails> {
    private final Context context;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    Activity activity;
    ArrayList<ProductMasterDetails> selectedProductStockReturnTemp;
    public ProductMasterWithQtyInputAdapterStockReturn(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        selectedProductStockReturnTemp = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++) {
            selectedProductStockReturnTemp.get(i).setQty("NA");
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
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        if (Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDescmsl.setVisibility(View.VISIBLE);
        }

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(invisibleTVProdCode);

        convertView.setTag(viewHolder);

        String menuItem = selectedProductStockReturnTemp.get(position).getDesc();
        viewHolder.txtViewProductDesc.setText(menuItem);
        if (Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDescmsl.setText(selectedProductStockReturnTemp.get(position).getmsl());
        }
        CharSequence text = viewHolder.invisibleTVProdCode.getText();

        if (text == null || text.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
        }
        String prodCode = selectedProductStockReturnTemp.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = selectedProductStockReturnTemp.get(position).getQty();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);

            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));

        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, txtViewProductDescmsl;
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
            selectedProductStockReturnTemp.get(pos).setQty(text);
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

    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view) {
            showDialogForReasonOfReturn(selectedProductStockReturnTemp.get(pos), pos);

        }

        public void showDialogForReasonOfReturn(final ProductMasterDetails productMasterDetails, int pos) {
            String qty = productMasterDetails.getQty();
            if (Utils.isNumeric(qty))
            {

                final Dialog grpDialog = new Dialog(activity);
                grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                grpDialog.setContentView(R.layout.sale_type_dialog);
                grpDialog.setCancelable(true);
                TextView title = (TextView) grpDialog.findViewById(R.id.title);
                title.setText("Select a Reason For Return.");
                ImageView image_cancel =  grpDialog.findViewById(R.id.image_cancel);
                image_cancel.setVisibility(View.VISIBLE);
                image_cancel.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        grpDialog.cancel();
                    }
                });
                final RadioButton radioCredit = (RadioButton) grpDialog
                        .findViewById(R.id.rd_credit);
                final RadioButton radioCOD = (RadioButton) grpDialog
                        .findViewById(R.id.rd_cod);
                final RadioButton radioPay = (RadioButton) grpDialog
                        .findViewById(R.id.rd_pay);
                radioCredit.setVisibility(View.VISIBLE);
                radioCredit.setText("Non Moving");
                if(isCurrentItemAlreadyAdded(productMasterDetails.getProdCode(),"Non Moving"))
                {
                    radioCredit.setEnabled(false);
                    radioCredit.setTextColor( context.getResources().getColor(R.color.grey));
                }

                radioCredit.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                            setValueAndCloseDialog("Non Moving", productMasterDetails, grpDialog);

                    }
                });
                if(isCurrentItemAlreadyAdded(productMasterDetails.getProdCode(),"Expired"))
                {
                    radioCOD.setEnabled(false);
                    radioCOD.setTextColor( context.getResources().getColor(R.color.grey));
                }
                radioCOD.setText("Expired");
                radioCOD.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        setValueAndCloseDialog("Expired", productMasterDetails, grpDialog);
                    }
                });
                if(isCurrentItemAlreadyAdded(productMasterDetails.getProdCode(),"Contaminated"))
                {
                    radioPay.setEnabled(false);
                    radioPay.setTextColor( context.getResources().getColor(R.color.grey));
                }
                radioPay.setText("Contaminated");
                radioPay.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        setValueAndCloseDialog("Contaminated", productMasterDetails, grpDialog);
                    }
                });
                grpDialog.show();
            }
            else
            {
                Toast.makeText(context, "Provide proper quantity for stock return.", Toast.LENGTH_SHORT).show();
            }
        }

    }

    private Boolean isCurrentItemAlreadyAdded(String prodCode, String reasonString)
    {
        Boolean isCurrentItemAdded =false;
        for(int i =0; i<Constants.selectedProductStockReturn.size();i++)
        {
            ProductMasterDetails currentItem=Constants.selectedProductStockReturn.get(i);
            if(currentItem.getProdCode().equalsIgnoreCase(prodCode) && currentItem.getstockReturnReason().equalsIgnoreCase(reasonString))
            {
                isCurrentItemAdded=true;
                break;
            }
        }
        return isCurrentItemAdded;
    }

    public void setValueAndCloseDialog(String value, ProductMasterDetails productMasterDetails, Dialog grpDialog) {
//        productMasterDetails.setstockReturnReason(value);
        ProductMasterDetails productMasterDetailsTemp=new ProductMasterDetails();
        productMasterDetailsTemp.setProdCode(productMasterDetails.getProdCode());
        productMasterDetailsTemp.setQty(productMasterDetails.getQty());
        productMasterDetailsTemp.setstockReturnReason(value);
        Constants.selectedProductStockReturn.add(productMasterDetailsTemp);
        grpDialog.cancel();
    }
}