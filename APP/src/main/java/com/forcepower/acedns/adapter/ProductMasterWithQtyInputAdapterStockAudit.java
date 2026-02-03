package com.forcepower.acedns.adapter;

import android.app.Dialog;
import android.content.Context;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static android.view.View.GONE;
import static com.forcepower.acedns.R.id.invisibleTVProdCode;
import static com.forcepower.acedns.R.id.radioUom;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class ProductMasterWithQtyInputAdapterStockAudit extends ArrayAdapter<ProductMasterDetails> {
    private final Context mContext;
    private final int resourceId;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0,currentPosition=0;
    ProductMasterDetails productMasterDetailsCurrentItem;
    private ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapterStockAudit(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.mContext = context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++) {
            nameValuesProductListLocal.get(i).setQty("NA");
        }

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        LayoutInflater inflater = (LayoutInflater) mContext
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.etProdRate =  convertView.findViewById(R.id.etProdRate);
        viewHolder.etProdRate.setVisibility(View.GONE);
        viewHolder.txtViewProductDescmsl = (TextView) convertView.findViewById(R.id.list_details2);
        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.radioUom = (Spinner) convertView.findViewById(radioUom);
        viewHolder.weigthage = (TextView) convertView.findViewById(R.id.weigthage);

        if (Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDescmsl.setVisibility(View.VISIBLE);
        }
        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes") && Constants.userDetailsObj.getstk_audit_UOM().equalsIgnoreCase("yes")) {
//            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
//            lay.weight = 0.3f;
//            viewHolder.list_details_ll.setLayoutParams(lay);

            viewHolder.convTV.setVisibility(GONE);
            viewHolder.radioUom.setVisibility(View.VISIBLE);

            final ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom1());
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom2());

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, R.layout.custom_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            viewHolder.radioUom.setAdapter(spinnerArrayAdapter);
            if (nameValuesProductListLocal.get(position).getUomSelectedForProduct().equalsIgnoreCase(nameValuesProductListLocal.get(position).getUom2())) {
                viewHolder.radioUom.setSelection(1);
            } else {
                viewHolder.radioUom.setSelection(0);
            }
            viewHolder.radioUom.setOnItemSelectedListener(new GenericOnItemSelectedListenerUom(position, viewHolder.radioUom,viewHolder.weigthage));
        }
        else{
            nameValuesProductListLocal.get(position).setUomSelectedForProduct(nameValuesProductListLocal.get(position).getUom1());
        }
        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(invisibleTVProdCode);

        convertView.setTag(viewHolder);

        String menuItem = nameValuesProductListLocal.get(position).getDesc();
        viewHolder.txtViewProductDesc.setText(menuItem);
        if (Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDescmsl.setText(nameValuesProductListLocal.get(position).getmsl());
        }
        CharSequence text = viewHolder.invisibleTVProdCode.getText();

        if (text == null || text.equals(""))
        {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(viewHolder.weigthage,position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }
        String prodCode = nameValuesProductListLocal.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = nameValuesProductListLocal.get(position).getQty();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
            viewHolder.convTV.setText(nameValuesProductListLocal.get(position).getUom1());




        if(Constants.weightage.matches("yes")){
            viewHolder.weigthage.setVisibility(View.VISIBLE);
        }else{
            viewHolder.weigthage.setVisibility(View.GONE);
        }

        return convertView;
    }
    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view)
        {

            addItemToCard(nameValuesProductListLocal.get(pos), pos);

        }

        public void addItemToCard(ProductMasterDetails productMasterDetails, int pos) {
            String qty = productMasterDetails.getQty();

            String rate = productMasterDetails.getMrpValue();
            if (Utils.isNumeric(qty))
            {
                if(Constants.userDetailsObj.getstk_audit_mfd_date().equalsIgnoreCase("yes"))
                {
                    productMasterDetailsCurrentItem=productMasterDetails;
                    currentPosition=pos;
                    showmMfdDateDialog();
                }
                else
                {
                    addtoListAndRemoveFromUi(productMasterDetails, pos);
                }
            }
            else
            {
                if (!Utils.isNumeric(qty)) {
                    Toast.makeText(mContext, "Provide proper quantity for order.", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(mContext, "Improper sale rate. Could not add item to cart.", Toast.LENGTH_SHORT).show();
                }

            }
        }
    }
    public void showmMfdDateDialog()
    {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.stk_audit_mfd_dialog);
        instructionDialog.setCancelable(false);
        instructionDialog.getWindow().setGravity(Gravity.RIGHT);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Provide mfg date...");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        edInst.setHint("Qty-mm/yy;Qty-mm/yy");
                    edInst.setInputType(InputType.TYPE_CLASS_PHONE);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                String instruction = "";
                instruction = edInst.getText().toString();
                if(instruction.length()>0)
                {
                    productMasterDetailsCurrentItem.setmfgDate(instruction);
                    addtoListAndRemoveFromUi(productMasterDetailsCurrentItem, currentPosition);
                    Utils.HideSoftKeyBoard(edInst,mContext);
                    instructionDialog.cancel();
                }
            }
        });
        instructionDialog.show();
    }
    public void addtoListAndRemoveFromUi(ProductMasterDetails productMasterDetails, int pos) {
        Constants.selectedProductMasterList.add(productMasterDetails);
        tempProductList.remove(pos);
        notifyDataSetChanged();
    }
    private class GenericOnItemSelectedListenerUom implements Spinner.OnItemSelectedListener {
        View view;
        private int position;
        TextView tv;

        private GenericOnItemSelectedListenerUom(int pos, View view,TextView textView) {
            this.position = pos;
            this.view = (View) view.getParent();
            this.tv = textView;
        }

        @Override
        public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
        {
            ((TextView) adapterView.getChildAt(0)).setTextSize(10);
            if (i == 0)
            {
                nameValuesProductListLocal.get(position).setUomSelectedForProduct(nameValuesProductListLocal.get(position).getUom1());

                String text = nameValuesProductListLocal.get(position).getQty();;//editable.toString();
                if(Constants.weightage.toUpperCase().matches("YES")) {

                    String s = mAceDnsDatabase.getProductWeightAsOum("" + nameValuesProductListLocal.get(position).getProdCode(), "" + nameValuesProductListLocal.get(position).getUomSelectedForProduct());
                    //Toast.makeText(getContext(), s+"--"+nameValuesProductListLocal.get(pos).getProdCode(), Toast.LENGTH_SHORT).show();
                    if(text.equals("")){

                    }else{
                        if (s.isEmpty() || s.equals("NA")) {
                            nameValuesProductListLocal.get(position).setWeightage("0");
                        } else {
                            if (Utils.isNumeric(text)) {
                                s = String.valueOf(Double.parseDouble(text) * Double.parseDouble(s));
                                tv.setText(s);
                                nameValuesProductListLocal.get(position).setWeightage(s);
                            }

                        }
                    }
                }

            }
            else
            {
                nameValuesProductListLocal.get(position).setUomSelectedForProduct(nameValuesProductListLocal.get(position).getUom2());

                String text = nameValuesProductListLocal.get(position).getQty();;//editable.toString();
                if(Constants.weightage.toUpperCase().matches("YES")) {

                    String s = mAceDnsDatabase.getProductWeightAsOum("" + nameValuesProductListLocal.get(position).getProdCode(), "" + nameValuesProductListLocal.get(position).getUomSelectedForProduct());
                    //Toast.makeText(getContext(), s+"--"+nameValuesProductListLocal.get(pos).getProdCode(), Toast.LENGTH_SHORT).show();
                    if(text.equals("")){

                    }else{
                        if (s.isEmpty() || s.equals("NA")) {
                            nameValuesProductListLocal.get(position).setWeightage("0");
                        } else {
                            if (Utils.isNumeric(text)) {
                                s = String.valueOf(Double.parseDouble(text) * Double.parseDouble(s));
                                tv.setText(s);
                                nameValuesProductListLocal.get(position).setWeightage(s);
                            }

                        }
                    }
                }
            }

        }

        @Override
        public void onNothingSelected(AdapterView<?> parent) {

        }
    }
    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, txtViewProductDescmsl,convTV,weigthage;
        EditText qtyET,etProdRate;
        ImageView addtocartIV;
        Spinner radioUom;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;
        private TextView tv;

        private GenericTextWatcherQty(TextView tv, int pos) {
            this.pos = pos;
            this.tv = tv;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setQty(text);
            //Toast.makeText(getContext(), nameValuesProductListLocal.get(pos).getProdCode() + "--"+nameValuesProductListLocal.get(pos).getUomSelectedForProduct(), Toast.LENGTH_SHORT).show();
            if(Constants.weightage.toUpperCase().matches("YES")){

                //  Toast.makeText(getContext(), oum+"--"+nameValuesProductListLocal.get(pos).getProdCode(), Toast.LENGTH_SHORT).show();
                String s = mAceDnsDatabase.getProductWeightAsOum(""+nameValuesProductListLocal.get(pos).getProdCode(),""+nameValuesProductListLocal.get(pos).getUomSelectedForProduct());
                //Toast.makeText(getContext(), s+"--"+nameValuesProductListLocal.get(pos).getProdCode(), Toast.LENGTH_SHORT).show();
                if(s.isEmpty()){
                    nameValuesProductListLocal.get(pos).setWeightage("0");
                    //Toast.makeText(getContext(), "Weight Not Found", Toast.LENGTH_SHORT).show();
                }else {
                    s = String.valueOf(Double.parseDouble(text) * Double.parseDouble(s));
                    tv.setText(s);
                    nameValuesProductListLocal.get(pos).setWeightage(s);
                    //SetWeightageTextView(tv,s);

                }
            }
            //Toast.makeText(getContext(), ""+nameValuesProductListLocal.get(pos).getSelectedUOM(), Toast.LENGTH_SHORT).show();
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
                    Toast.makeText(mContext, "End of List", Toast.LENGTH_SHORT).show();
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
//            if (rateOrQtyOrStk.matches("qty")) {
//                isQtySelected = true;
//                isStkQtySelected = false;
//            } else if (rateOrQtyOrStk.matches("rate")) {
//                isQtySelected = false;
//                isStkQtySelected = false;
//            } else {
//                isStkQtySelected = true;
//            }
            int inType = edittext.getInputType();       // Backup the input type
            edittext.setInputType(InputType.TYPE_NULL); // Disable standard keyboard
            edittext.onTouchEvent(event);               // Call native handler
            edittext.setInputType(inType);              // Restore input type
            return true; // Consume touch event
        }

    }
}