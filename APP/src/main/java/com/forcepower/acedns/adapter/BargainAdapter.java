package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.Html;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.R.id.radioUom;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.isTdSelected;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class BargainAdapter extends ArrayAdapter<ProductMasterDetails> {


    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    int positionOfSelectedItem = 0;
    private ViewHolder viewHolder;
    private ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private Boolean isRateEditable = true;


    public BargainAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

//        if(Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("no") && Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("no"))
//        {
//            isRateEditable = false;
//        }
//        else if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") || (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))) {
//            isRateEditable = false;
//        }
        isRateEditable = false;
        for (int i = 0; i < nameValues.size(); i++) {
            nameValuesProductListLocal.get(i).setQty("NA");

                nameValuesProductListLocal.get(i).setTD("NA");

            String prodCode = nameValues.get(i).getProdCode();
            if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, nameValuesProductListLocal.get(i).getUom1());
            } else {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, "");
            }

//            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
//                nameValuesProductListLocalDo.get(i).setMrpValue("NA");
//            }
            {

            }
        }
    }

    public String setMrpValue(String customerType, String getMultipleRate, int i, String prodCode, String selectedUom) {
        String mrpValue = "0";
        ArrayList<MRPDetails> mrpSpinnerList = mAceDnsDatabase.getMRPList(prodCode, selectedUom);
        if (mrpSpinnerList != null && mrpSpinnerList.size() > 0) {
            nameValuesProductListLocal.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
            if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                mrpValue = mrpSpinnerList.get(0).getMrpValue();

            } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {

                if (getMultipleRate.equalsIgnoreCase("yes")) {
                    if (customerType.matches("R") || customerType.matches("")) {
                        mrpValue = mrpSpinnerList.get(0).getSaleRate();
                    } else if (customerType.matches("D")) {
                        mrpValue = mrpSpinnerList.get(0).getDistributorRate();

                    } else if (customerType.matches("WS")) {
                        mrpValue = mrpSpinnerList.get(0).getWSRate();

                    } else if (customerType.matches("SS")) {
                        mrpValue = mrpSpinnerList.get(0).getSSRate();
                    } else if (customerType.matches("DEPOT")) {
                        mrpValue = mrpSpinnerList.get(0).getDepotRate();
                    }
                } else {
                    mrpValue = mrpSpinnerList.get(0).getSaleRate();
                }
            }
        }
        nameValuesProductListLocal.get(i).setMrpValue(mrpValue);
        return mrpValue;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.etProdStkQty = (EditText) convertView.findViewById(R.id.etProdStkQty);
        viewHolder.etProdTD = (EditText) convertView.findViewById(R.id.etProdTD);
        viewHolder.etProdRate = (EditText) convertView.findViewById(R.id.etProdRate);
//        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
        viewHolder.tvProdStkQty = (TextView) convertView.findViewById(R.id.tvProdStkQty);
        viewHolder.radioUom = (Spinner) convertView.findViewById(radioUom);
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);
        }

        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);

            viewHolder.radioUom.setVisibility(View.VISIBLE);

            final ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom1());
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom2());

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(context, android.R.layout.simple_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            viewHolder.radioUom.setAdapter(spinnerArrayAdapter);
            if (nameValuesProductListLocal.get(position).getUomSelectedForProduct().equalsIgnoreCase(nameValuesProductListLocal.get(position).getUom2())) {
                viewHolder.radioUom.setSelection(1);
            } else {
                viewHolder.radioUom.setSelection(0);
            }
            viewHolder.radioUom.setOnItemSelectedListener(new GenericOnItemSelectedListenerUom(position, viewHolder.radioUom));
        }
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.schemeDetailsIcon = (ImageView) convertView.findViewById(R.id.schemeDetailsIcon);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = nameValuesProductListLocal.get(position).getDesc();

        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (nameValuesProductListLocal.get(position).getFocus().equalsIgnoreCase("y") && !Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
        }
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.etProdRate.setVisibility(View.VISIBLE);

            if (!isRateEditable) {
                viewHolder.etProdRate.setEnabled(false);
            } else {
                viewHolder.etProdRate.addTextChangedListener(new GenericTextWatcherRate(position));
                viewHolder.etProdRate.setOnTouchListener(new GenericEditorOnTouchListener("rate", viewHolder.qtyET));
            }

            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }


        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
                viewHolder.etProdTD.addTextChangedListener(new GenericTextWatcherStkQty(position));
                viewHolder.etProdTD.setOnTouchListener(new GenericEditorOnTouchListener("td", viewHolder.qtyET));

            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));

        }
        String prodCode = nameValuesProductListLocal.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentStkqty = nameValuesProductListLocal.get(position).getStkQty();
        String currentTD = nameValuesProductListLocal.get(position).getTD();
        String currentqty = nameValuesProductListLocal.get(position).getQty();
        String currentMrp = nameValuesProductListLocal.get(position).getMrpValue();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
        if (!currentMrp.matches("NA"))
            viewHolder.etProdRate.setText(currentMrp);
        if (!currentTD.matches("NA"))
            viewHolder.etProdTD.setText(currentTD);

        return convertView;
    }

    public void SetMrpValueInRateEditText(View view, String mrpValue) {
//        View viewParent= (View) view.getParent();
        EditText etProdRate = view.findViewById(R.id.etProdRate);
        etProdRate.setText(mrpValue);
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode,tvProdStkQty;
        EditText qtyET, etProdRate, etProdStkQty,etProdTD;
        ImageView addtocartIV, schemeDetailsIcon;
        Spinner radioUom;
        LinearLayout list_details_ll;
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

    private class GenericTextWatcherStkQty implements TextWatcher {

        private int pos;

        private GenericTextWatcherStkQty(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setTD(text);
        }
    }

    private class GenericTextWatcherRate implements TextWatcher {
        private int pos;

        private GenericTextWatcherRate(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setMrpValue(text);
        }
    }

    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view) {
            addfreeBiesForCurrentProduct(nameValuesProductListLocal.get(pos), pos);

        }

        public void addfreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails, int pos) {
            //        ProductMasterDetails productMasterDetails = ;
            String qty = productMasterDetails.getQty();
            String stkQty = productMasterDetails.getStkQty();
            String td = productMasterDetails.getTD();

            String rate = productMasterDetails.getMrpValue();
            //            Toast.makeText(context,nameValuesProductListLocalDo.get(pos).getDesc()+ "", Toast.LENGTH_SHORT).show();
            if (Utils.isNumeric(qty) && Utils.isNumeric(rate) )
            {
                double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
                if(Utils.isNumeric(td))
                {
                    amnt=amnt-Double.parseDouble(td);
                }
                productMasterDetails.setAmount(defaultFormat.format(amnt));
                Constants.selectedProductMasterList.add(productMasterDetails);
                tempProductList.remove(pos);
                notifyDataSetChanged();
//                changeCartCount();

            }
            else {

                     if (!Utils.isNumeric(qty))
                     {
                    Toast.makeText(context, "Provide proper quantity for order.", Toast.LENGTH_SHORT).show();
                }
                else
                    {
                    Toast.makeText(context, "Improper sale rate. Could not add item to cart.", Toast.LENGTH_SHORT).show();
                }

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
            if (rateOrQtyOrStk.matches("qty"))
            {
                isQtySelected = true;
                isStkQtySelected = false;
            } else if (rateOrQtyOrStk.matches("rate"))
            {
                isQtySelected = false;
                isStkQtySelected = false;
            }
            else
            {

                isTdSelected = true;
            }
            int inType = edittext.getInputType();       // Backup the input type
            edittext.setInputType(InputType.TYPE_NULL); // Disable standard keyboard
            edittext.onTouchEvent(event);               // Call native handler
            edittext.setInputType(inType);              // Restore input type
            return true; // Consume touch event
        }

    }


    private class GenericOnItemSelectedListenerUom implements Spinner.OnItemSelectedListener {
        View view;
        private int position;

        private GenericOnItemSelectedListenerUom(int pos, View view) {
            this.position = pos;
            this.view = (View) view.getParent();
        }

        @Override
        public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
            if (i == 0) {
                nameValuesProductListLocal.get(position).setUomSelectedForProduct(nameValuesProductListLocal.get(position).getUom1());
                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes"))
                {
                    //change mrp data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, nameValuesProductListLocal.get(position).getProdCode(), nameValuesProductListLocal.get(position).getUom1());
                    SetMrpValueInRateEditText(this.view, mrpValue);

                }
            } else {
                nameValuesProductListLocal.get(position).setUomSelectedForProduct(nameValuesProductListLocal.get(position).getUom2());
                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                    //change mrp  data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, nameValuesProductListLocal.get(position).getProdCode(), nameValuesProductListLocal.get(position).getUom2());
                    SetMrpValueInRateEditText(this.view, mrpValue);
                }
            }

        }

        @Override
        public void onNothingSelected(AdapterView<?> parent) {

        }
    }

}