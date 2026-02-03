package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.Html;
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
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.activity.OrderFormActivityAlternateDesign;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Arrays;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;
import static com.forcepower.acedns.R.id.radioUom;
import static com.forcepower.acedns.R.id.textView;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.retailercareOrderOrAudit;
import static com.forcepower.acedns.constants.Constants.schemeListForCurrentProducts;
import static com.forcepower.acedns.constants.Constants.selectedItemView;
import static com.forcepower.acedns.constants.Constants.tempProductList;
import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;
import static com.forcepower.acedns.util.Utils.doesQuantityMatchesQtySlab;
import static com.forcepower.acedns.util.Utils.getAmnt;


public class ProductMasterWithQtyInputAdapterAlternateDesign extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    int sizeOfList = 0,positionFinal;
    int positionOfSelectedItem = 0;
    String pCode="0",oum="pc";
    private ViewHolder viewHolder;
    private ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private Boolean isRateEditable = true;
    ProductMasterDetails productMasterDetailsFinal;
    View wView;


    public ProductMasterWithQtyInputAdapterAlternateDesign(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(context);
        sizeOfList = nameValues.size();

        if(Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("no") && Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("no"))
        {
            isRateEditable = false;
        }
        else if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") || (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))) {
            isRateEditable = false;
        }
        for (int i = 0; i < nameValues.size(); i++)
        {

            if (orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && retailercareOrderOrAudit.equalsIgnoreCase("audit"))
            {
                nameValuesProductListLocal.get(i).setQty("0");
            }
            else
            {
                nameValuesProductListLocal.get(i).setQty("NA");
            }
            if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") || Constants.isVanSales)
            {
                nameValuesProductListLocal.get(i).setStkQty("NA");
            }
            else
            {
                nameValuesProductListLocal.get(i).setStkQty("0");
            }

            String prodCode = nameValues.get(i).getProdCode();
            if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, nameValuesProductListLocal.get(i).getUom1(),"uom1",nameValuesProductListLocal.get(i).getConversionFactor());
            } else {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, "","","");
            }

            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                nameValuesProductListLocal.get(i).setMrpValue("NA");
            }
            {

            }
        }
    }

    public String setMrpValue(String customerType, String getMultipleRate, int i, String prodCode, String selectedUom,String UomType,String convFactor) {
        String mrpValue = "0";
        try{
            ArrayList<MRPDetails> mrpSpinnerList = mAceDnsDatabase.getMRPList(prodCode, selectedUom);
            if (mrpSpinnerList != null && mrpSpinnerList.size() > 0)
            {
                nameValuesProductListLocal.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
                if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes"))
                {
                    mrpValue = mrpSpinnerList.get(0).getMrpValue();

                }
                else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))
                {

                    if (getMultipleRate.equalsIgnoreCase("yes"))
                    {
                        if (customerType.equalsIgnoreCase("R") || customerType.matches(""))
                        {
                            mrpValue = mrpSpinnerList.get(0).getSaleRate();
                        } else if (customerType.equalsIgnoreCase("D")) {
                            mrpValue = mrpSpinnerList.get(0).getDistributorRate();

                        } else if (customerType.equalsIgnoreCase("WS")) {
                            mrpValue = mrpSpinnerList.get(0).getWSRate();

                        } else if (customerType.equalsIgnoreCase("SS")) {
                            mrpValue = mrpSpinnerList.get(0).getSSRate();
                        } else if (customerType.equalsIgnoreCase("DEPOT")) {
                            mrpValue = mrpSpinnerList.get(0).getDepotRate();
                        }
                    }
                    else
                    {
                        mrpValue = mrpSpinnerList.get(0).getSaleRate();
                    }
//                if(selectedUom.length()>1)
//                {
//                    if(!selectedUom.equalsIgnoreCase(mrpSpinnerList.get(0).getUom()))
//                    {
//                        Double mrpVal=Double.parseDouble(mrpValue);
//                        Double convInDbl= Double.parseDouble(convFactor);;
//                        if(UomType.equalsIgnoreCase("uom1"))
//                        {
//                            mrpVal=mrpVal/convInDbl;
//
//                        }
//                        else if(UomType.equalsIgnoreCase("uom2"))
//                        {
//                            mrpVal=mrpVal*convInDbl;
//                        }
//                        mrpValue=mrpVal+"";
//                    }
//                }
                }
            }
            nameValuesProductListLocal.get(i).setMrpValue(mrpValue);
        }
        catch (Exception e){
            e.printStackTrace();
        }

        return mrpValue;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        wView = convertView;
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);

        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.weigthage = (TextView) convertView.findViewById(R.id.weigthage);
        viewHolder.tv_stock = (TextView) convertView.findViewById(R.id.tv_stock);

        viewHolder.etProdStkQty = (EditText) convertView.findViewById(R.id.etProdStkQty);

        if(Constants.weightage.matches("yes")){
            viewHolder.weigthage.setVisibility(VISIBLE);
        }else{
            viewHolder.weigthage.setVisibility(GONE);
        }

        if(Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
        {
            if(retailercareOrderOrAudit.equalsIgnoreCase("order"))
            {
                viewHolder.etProdStkQty.setEnabled(false);
            }
            else if(retailercareOrderOrAudit.equalsIgnoreCase("audit"))
            {
                viewHolder.qtyET.setEnabled(false);
            }
        }
        viewHolder.etProdRate = (EditText) convertView.findViewById(R.id.etProdRate);
        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
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

            viewHolder.convTV.setVisibility(GONE);
            viewHolder.radioUom.setVisibility(View.VISIBLE);

            final ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom1());
            spinnerArray.add(nameValuesProductListLocal.get(position).getUom2());

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(context, R.layout.custom_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            viewHolder.radioUom.setAdapter(spinnerArrayAdapter);
            if (nameValuesProductListLocal.get(position).getUomSelectedForProduct().equalsIgnoreCase(nameValuesProductListLocal.get(position).getUom2())) {
                viewHolder.radioUom.setSelection(1);
            } else {
                viewHolder.radioUom.setSelection(0);
            }
            viewHolder.radioUom.setOnItemSelectedListener(new GenericOnItemSelectedListenerUom(position, viewHolder.radioUom,viewHolder.weigthage));
        }
        else if(Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("no_uom")){
            viewHolder.convTV.setVisibility(GONE);
        }
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.schemeDetailsIcon = (ImageView) convertView.findViewById(R.id.schemeDetailsIcon);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") )
        {
            viewHolder.etProdStkQty.setVisibility(View.VISIBLE);
        }
        else if(Constants.isVanSales)
        {
            viewHolder.tvProdStkQty.setVisibility(View.VISIBLE);
        }
        convertView.setTag(viewHolder);

        String desc = nameValuesProductListLocal.get(position).getDesc();

        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {

        } else {
            if (uomToBeShownForProduct.equalsIgnoreCase("uom1")) {
                oum="uom1";
                viewHolder.convTV.setText(nameValuesProductListLocal.get(position).getUom1());
            } else if (uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
                oum="uom2";
                viewHolder.convTV.setText(nameValuesProductListLocal.get(position).getUom2());
            } else {
                oum="uom2";
                viewHolder.convTV.setText(nameValuesProductListLocal.get(position).getUOM3());
            }
        }

        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            if (Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
                desc = nameValuesProductListLocal.get(position).getsize();
            } else {
                if (desc.contains("-")) {
                    String[] splittedSkuDesc = desc.split("-");
                    desc = splittedSkuDesc[splittedSkuDesc.length - 1].trim();
                }

            }


        }
        if(Constants.isVanSales)
        {
            String closingStk = nameValuesProductListLocal.get(position).getClosingStk();
            if(Utils.isNumeric(closingStk))
            {
                double closingStkdbl= Double.parseDouble(closingStk);
                int clstk=(int)closingStkdbl;
                closingStk= String.valueOf(clstk);
            }
            viewHolder.tvProdStkQty.setText(closingStk);
        }
        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (nameValuesProductListLocal.get(position).getFocus().equalsIgnoreCase("y") && !Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
        }
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.etProdRate.setVisibility(View.VISIBLE);
            if (nameValuesProductListLocal.get(position).getSchemePresent()) {
                viewHolder.schemeDetailsIcon.setVisibility(View.VISIBLE);
                viewHolder.schemeDetailsIcon.setOnClickListener(new GenericItemCLickListenerForSchemes(position));
            }

            if (!isRateEditable) {
                viewHolder.etProdRate.setEnabled(false);
            } else {
                viewHolder.etProdRate.addTextChangedListener(new GenericTextWatcherRate(position));
                viewHolder.etProdRate.setOnTouchListener(new GenericEditorOnTouchListener("rate", viewHolder.qtyET, viewHolder.weigthage));
            }

            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }

//viewHolder.weigthage.setText("14");
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(viewHolder.weigthage,viewHolder.tv_stock,position));
            if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") || Constants.isVanSales)
            {
                viewHolder.etProdStkQty.addTextChangedListener(new GenericTextWatcherStkQty(viewHolder.weigthage,position));
                viewHolder.etProdStkQty.setOnTouchListener(new GenericEditorOnTouchListener("stkqty", viewHolder.qtyET,viewHolder.weigthage));
            }

            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET,viewHolder.weigthage));

        }
        String prodCode = nameValuesProductListLocal.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentStkqty = nameValuesProductListLocal.get(position).getStkQty();
        String currentqty = nameValuesProductListLocal.get(position).getQty();
        String currentMrp = nameValuesProductListLocal.get(position).getMrpValue();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
        if (!currentMrp.matches("NA"))
            viewHolder.etProdRate.setText(currentMrp);
        if (!currentStkqty.matches("NA") && (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")  || Constants.isVanSales))
            viewHolder.etProdStkQty.setText(currentStkqty);

        if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")){
            viewHolder.tv_stock.setVisibility(VISIBLE);
            viewHolder.tv_stock.setText(""+nameValuesProductListLocal.get(position).getStock());
            //viewHolder.convTV.setVisibility(GONE);
        }else{
            //viewHolder.tv_stock.setVisibility(GONE);
        }


        return convertView;
    }

    public void SetMrpValueInRateEditText(View view, String mrpValue) {
//        View viewParent= (View) view.getParent();
        EditText etProdRate = view.findViewById(R.id.etProdRate);
        etProdRate.setText(mrpValue);
    }

    public class ViewHolder {
        //public static JustifiedTextView weigthage;
        TextView txtViewProductDesc, invisibleTVProdCode, convTV,tvProdStkQty,weigthage,tv_stock;
        EditText qtyET, etProdRate, etProdStkQty;
        ImageView addtocartIV, schemeDetailsIcon;
        Spinner radioUom;
        LinearLayout list_details_ll;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;
        private TextView tv,tv_stock;

        private GenericTextWatcherQty(TextView tv,TextView tv_stock,int pos) {
            this.pos = pos;
            this.tv = tv;
            this.tv_stock = tv_stock;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
            //if(text.isEmpty()){
                tv_stock.setText(nameValuesProductListLocal.get(pos).getStock());
            //}
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setQty(text);
            //Toast.makeText(getContext(), oum+"--"+nameValuesProductListLocal.get(pos).getUomSelectedForProduct(), Toast.LENGTH_SHORT).show();
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

            if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {
                String st = String.valueOf(Integer.parseInt(nameValuesProductListLocal.get(pos).getStock()) - Integer.parseInt(text));
                tv_stock.setText(st);
            }



        }
    }

    public void SetWeightageTextView(TextView tv, String mrpValue) {
//        View viewParent= (View) view.getParent();
        //TextView etProdRate = view.findViewById(R.id.weigthage);
        //private TextView  tv;
        tv.setText(mrpValue);
        //Toast.makeText(getContext(), "Click-" + mrpValue, Toast.LENGTH_SHORT).show();
    }

/*    private class Weightage extends androidx.appcompat.widget.AppCompatTextView {

        public Weightage(Context context,TextView tv) {

            context.viewholder.weigthage = tv;
            super(context);

        }
    }*/

    private class GenericTextWatcherStkQty implements TextWatcher {

        private int pos;
        private TextView tv;

        private GenericTextWatcherStkQty(TextView tv,int pos) {
            this.pos = pos;
            this.tv = tv;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            nameValuesProductListLocal.get(pos).setStkQty(text);
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
            ProductMasterDetails productMasterDetails = nameValuesProductListLocal.get(pos);

            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdInputDropDown().equalsIgnoreCase("input"))
            {
                positionFinal=pos;
                productMasterDetailsFinal=productMasterDetails;
                String qty = productMasterDetails.getQty();
                String rate = productMasterDetails.getMrpValue();
                if (Utils.isNumeric(qty) && Utils.isNumeric(rate))
                {
                    if(Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
                    {
                        if (retailercareOrderOrAudit.equalsIgnoreCase("audit"))
                        {
                            productMasterDetailsFinal.setTradeDiscnt("0.0");
                            productMasterDetailsFinal.setIsTradeDiscount(true);
                            if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                                showRemarks(productMasterDetailsFinal, positionFinal);
                            }else {
                                addCurrentProductToCart(productMasterDetailsFinal, positionFinal);
                            }

                        }
                        else
                        {
                            showSkuWiseTdDialog();
                        }
                    }
                    else
                    {

                        showSkuWiseTdDialog();
                    }

                }
                else
                {
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
            else
            {
                //Toast.makeText(context, "Provide test--.", Toast.LENGTH_SHORT).show();
                if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                    showRemarks(productMasterDetails, pos);
                }else {
                    addCurrentProductToCart(productMasterDetails, pos);
                }
            }


        }
        public void showSkuWiseTdDialog()
        {
            final Dialog instructionDialog = new Dialog(context);
            instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            instructionDialog.setContentView(R.layout.stk_audit_mfd_dialog);
            instructionDialog.setCancelable(false);
            instructionDialog.getWindow().setGravity(Gravity.RIGHT);
            TextView title = (TextView) instructionDialog.findViewById(R.id.title);
            title.setText("Provide TD...");
            final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
//            edInst.setInputType(InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edInst.setHint("  Type here");
            Button submit = (Button) instructionDialog.findViewById(R.id.btn);
            submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    String tdInput = "";
                    tdInput = edInst.getText().toString();
                    if(tdInput.length()>0 && Utils.isNumeric(tdInput))
                    {
                        double amountInDouble = Double.parseDouble(productMasterDetailsFinal.getQty())*Double.parseDouble(productMasterDetailsFinal.getMrpValue());
                        double tdInputInDouble = Double.parseDouble(tdInput);
                        productMasterDetailsFinal.setTradeDiscnt(defaultFormat.format(tdInputInDouble));
                        productMasterDetailsFinal.setIsTradeDiscount(true);

                        if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
//                        if (Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("amount"))
                        {
                            if(tdInputInDouble>amountInDouble)
                            {
                                Utils.showToast(context,"TD can not be grater than amount.");
                                return;
                            }


                        }
                        else
                        {
                           if(tdInputInDouble>100)
                           {
                               Utils.showToast(context,"TD percentage can not be grater than 100.");
                               return;

                           }
                        }

                        if(Constants.orderFormDetailsObj.getProduct_wise_remarks().toLowerCase().matches("yes")){
                            showRemarks(productMasterDetailsFinal, positionFinal);
                        }else {
                            addCurrentProductToCart(productMasterDetailsFinal, positionFinal);
                        }
                        Utils.HideSoftKeyBoard(edInst,context);
                        instructionDialog.cancel();
                    }
                }
            });
            instructionDialog.show();
        }
        public void addCurrentProductToCart(ProductMasterDetails productMasterDetails, int pos) {
            //        ProductMasterDetails productMasterDetails = ;
            String qty = productMasterDetails.getQty();

            String stkQty = productMasterDetails.getStkQty();

            if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {

                if (!Utils.isNumeric(qty)) {
                    Toast.makeText(context, "Provide proper quantity for order-.", Toast.LENGTH_SHORT).show();
                } else {
                    String st = String.valueOf(Integer.parseInt(nameValuesProductListLocal.get(pos).getStock()) - Integer.parseInt(nameValuesProductListLocal.get(pos).getQty()));
                    mAceDnsTransactionDatabase.UpdateOrderStockCustomerAddToCart(""+nameValuesProductListLocal.get(pos).getProdCode(), ""+st);
                    //Utils.showToast(context,st + " " + nameValuesProductListLocal.get(pos).getProdCode());
                }
            }

            if(Constants.isVanSales)
            {
                if(!Utils.isNumeric(stkQty))
                {
                    stkQty="0";
                    if(!Utils.isNumeric(qty) || Double.parseDouble(qty)<=0)
                    {
                        return;
                    }

                }
                else
                {
                    if(!Utils.isNumeric(qty) || Double.parseDouble(qty)<=0)
                    {
                        productMasterDetails.setQty("0");
                        qty="0";
                    }
                }
            }
            String rate = productMasterDetails.getMrpValue();
            //            Toast.makeText(context,nameValuesProductListLocalDo.get(pos).getDesc()+ "", Toast.LENGTH_SHORT).show();
            if (Utils.isNumeric(qty) && Utils.isNumeric(rate))
            {
                if(Constants.isVanSales && Double.parseDouble(qty)>Double.parseDouble(productMasterDetails.getClosingStk()))
                {
                    Toast.makeText(context, "Insufficient Stock.", Toast.LENGTH_SHORT).show();
                }
                else
                {
                    double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
                    if (productMasterDetails.getSchemePresent())
                    {
                        //                String prodCode=nameValuesProductListLocalDo.get(pos).getProdCode();
                        final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
                        for (int i = 0; i < schemesOnCurrentProduct.size(); i++)
                        {
                            ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
                            for (int x = 0; x < FreeProductsOnCurrentItem.size(); x++) {
                                SchemeFreebiesDetails item = FreeProductsOnCurrentItem.get(x);
                                if (!item.getqty().matches("0"))//offer on qty
                                {
                                    if (doesQuantityMatchesQtySlab(qty, item.getqty())) {
                                        if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                        {
                                            if(Constants.weightage.matches("no")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, context);
                                            }else if (Constants.weightage.matches("yes")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context);
                                            }
                                        } else//offer on total amount
                                        {
                                            amnt = getAmnt(amnt, item);
                                        }

                                    } else {
                                        if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                        {
                                            if(Constants.weightage.matches("no")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, context);
                                            }else if (Constants.weightage.matches("yes")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context);
                                            }

                                        }
                                    }
                                } else {
                                    if (amnt >= Double.parseDouble(item.getamount())) {
                                        if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                        {
                                            if(Constants.weightage.matches("no")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, context);
                                            }else if (Constants.weightage.matches("yes")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context);
                                            }

                                        } else {
                                            amnt = getAmnt(amnt, item);
                                        }

                                    } else {
                                        if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                        {
                                            if(Constants.weightage.matches("no")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, context);
                                            }else if (Constants.weightage.matches("yes")) {
                                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context);
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))
                            && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdInputDropDown().equalsIgnoreCase("input"))
                    {
                        String TradeDiscount=productMasterDetails.getTradeDiscnt();
                        if(Utils.isNumeric(TradeDiscount))
                        {
                            Double tdInDouble= Double.parseDouble(TradeDiscount);
                            if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
                            {

                                amnt=amnt - tdInDouble;
                            }
                            else
                            {
                                amnt=amnt - ((tdInDouble*amnt)/100);
                            }
                        }
                    }
                    productMasterDetails.setAmount(defaultFormat.format(amnt));
                    //productMasterDetails.setWeightage("10");

                    tempProductList.remove(pos);
                    notifyDataSetChanged();
                    OrderFormActivityAlternateDesign.changeCartCount();

                    if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes"))
                    {
                        productMasterDetails.setHeight(""+Constants.sylHight);
                        productMasterDetails.setWeidth(""+Constants.sylWeidth);
                    }

                    Constants.selectedProductMasterList.add(productMasterDetails);

                    if (context instanceof OrderFormActivityAlternateDesign) {
                        ((OrderFormActivityAlternateDesign)context).changeSylText();
                    }
                }

            } else {

//                if (!Utils.isNumeric(stkQty)) {
//                    Toast.makeText(context, "Please Provide numeric input for stock audit even if its zero.", Toast.LENGTH_SHORT).show();
//                }

                 if (!Utils.isNumeric(qty)) {
                    Toast.makeText(context, "Provide proper quantity for order-.", Toast.LENGTH_SHORT).show();
                } else {
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
        TextView textView;

        private GenericEditorOnTouchListener(String rateOrQtyOrStk, EditText tv,TextView textView) {
            this.rateOrQtyOrStk = rateOrQtyOrStk;
            //        qtySrate = tv;
            //        isQtySelected
            this.textView = textView;
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

          //  textView.setText("1234");
            return true; // Consume touch event
        }

    }

    private class GenericItemCLickListenerForSchemes implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListenerForSchemes(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view) {
            selectedItemView = (View) view.getParent().getParent();
            ShowSchemeDetails(pos);
        }

        public void ShowSchemeDetails(int pos) {
            positionOfSelectedItem = pos;
            //        SchemeFreebiesDetails itemOffer=new SchemeFreebiesDetails();
            final Dialog schemeDialog = new Dialog(context, R.style.PauseDialog);
            schemeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            schemeDialog.setContentView(R.layout.dialog_layout_header_body);
            schemeDialog.setCancelable(true);
            Button btn_avail = (Button) schemeDialog.findViewById(R.id.btn_avail);
            TextView title = (TextView) schemeDialog.findViewById(R.id.title);
            title.setText("Schemes on " + nameValuesProductListLocal.get(positionOfSelectedItem).getDesc());
            btn_avail.setVisibility(GONE);
            TextView text = (TextView) schemeDialog.findViewById(R.id.text);
            text.setVisibility(GONE);
            final RadioGroup orderSchemesListOnProductRG = (RadioGroup) schemeDialog.findViewById(R.id.orderSchemesListOnProductRG);
            orderSchemesListOnProductRG.setVisibility(View.VISIBLE);
            orderSchemesListOnProductRG.setGravity(Gravity.CENTER_VERTICAL);
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(nameValuesProductListLocal.get(positionOfSelectedItem).getSchemeIds().split(",")));
            for (int i = 0; i < schemesOnCurrentProduct.size(); i++) {
                String textOne = "", textTwo = "", finalSchemeText = "";
                ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
                for (int x = 0; x < FreeProductsOnCurrentItem.size(); x++) {
                    SchemeFreebiesDetails item = FreeProductsOnCurrentItem.get(x);

                    if (textOne.matches("")) {
                        if (!item.getqty().matches("0")) {
                            textOne = " Buy " + item.getqty() + " " + item.getProductUomDisplayValue() + " get ";
                        } else {
                            textOne = "Buy " + item.getamount() + " get ";
                        }
                    }

                    if (!item.getfreebiesProdCode().trim().matches("") || !item.getfreebiesProdDesc().trim().matches("")) {
                        //                        textTwo=item.getFreebieQty()+" quantity of "+item.getfreebiesProdDesc()+" free.";
                        if (textTwo.matches("")) {
                            textTwo = item.getFreebieQty() + " " + item.getfreebieUomDisplayValue() + " of " + item.getfreebiesProdDesc() + " free.";
                        } else {
                            textTwo = textTwo + " Also " + item.getFreebieQty() + " " + item.getfreebieUomDisplayValue() + " of " + item.getfreebiesProdDesc() + " free.";
                        }

                    } else if (!item.getvaluePercent().matches("0")) {
                        //                            textTwo=item.getvaluePercent()+" % off.";
                        if (textTwo.matches("")) {
                            textTwo = item.getvaluePercent() + " % off.";
                        } else {
                            textTwo = textTwo + " Also " + item.getvaluePercent() + " % off.";
                        }
                    } else if (!item.getvalueAmount().matches("0")) {
                        //                            textTwo=item.getvalueAmount()+" amount off.";
                        if (textTwo.matches("")) {
                            textTwo = item.getvalueAmount() + " amount off.";
                        } else {
                            textTwo = textTwo + " Also " + item.getvalueAmount() + " amount off.";
                        }
                    } else {
                        //                            textTwo= item.getvalueAmount()+" quantity extra.";
                        if (textTwo.matches("")) {
                            textTwo = item.getvalueAmount() + " quantity extra.";
                        } else {
                            textTwo = textTwo + " Also " + item.getvalueAmount() + " quantity extra.";
                        }
                    }
                    //                    textTwo=textTwo+"\n";
                    //                    finalSchemeText=finalSchemeText+textOne+textTwo+" Scheme valid till "+Utils.changeDateFormat("yyyy-MM-dd","MM-dd-yyyy",item.getendDate())+".";
                    //                    finalSchemeText=finalSchemeText+textOne+textTwo+".".replace(">="," ").replace("<=","").replace("&&"," to ");
                }
                finalSchemeText = textOne + textTwo.replace(">=", " ").replace("<=", "").replace("&&", " to ");
                finalSchemeText = finalSchemeText.replace("&&", " to ");
                finalSchemeText = finalSchemeText.replace(">=", " ");
                finalSchemeText = finalSchemeText.replace("<=", " ");
                finalSchemeText = finalSchemeText.trim().replace("  ", " ");

                RadioButton rdbtn = new RadioButton(context);
                rdbtn.setId(i + 1);
                rdbtn.setTextColor(context.getResources().getColor(R.color.text_color));
                rdbtn.setText(finalSchemeText);
                rdbtn.setGravity(Gravity.CENTER_VERTICAL);
                orderSchemesListOnProductRG.addView(rdbtn);
            }
            //            text.setText(finalSchemeText);
            Button submit = (Button) schemeDialog.findViewById(R.id.btn_ok);
            //        submit.setVisibility(GONE);
            submit.setText("Apply Offer");

            submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    TextView qty = (TextView) selectedItemView.findViewById(R.id.etProdQty);
                    TextView rate = (TextView) selectedItemView.findViewById(R.id.etProdRate);
                    int selectedRadioButtonID = orderSchemesListOnProductRG.getCheckedRadioButtonId();

                    // If nothing is selected from Radio Group, then it return -1
                    if (selectedRadioButtonID != -1) {
                        int positionOfRadioButton = selectedRadioButtonID - 1;
                        ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(positionOfRadioButton));

                        SchemeFreebiesDetails schemeFreebiesDetailsCurrentItem = FreeProductsOnCurrentItem.get(0);
                        String minValueForOffer = schemeFreebiesDetailsCurrentItem.getqty().replaceAll("[^\\d.]", "");
                        if (Utils.isNumeric(minValueForOffer)) {
                            minValueForOffer = Utils.convertQtyFromOfferUomToInputUom(Double.parseDouble(minValueForOffer), schemeFreebiesDetailsCurrentItem, context) + "";
                            qty.setText(minValueForOffer);
                            nameValuesProductListLocal.get(positionOfSelectedItem).setQty(minValueForOffer);
                        }
                        schemeDialog.cancel();
                    } else {
                        Toast.makeText(context, "Please select an offer first.", Toast.LENGTH_SHORT).show();
                    }
                }
            });

            ImageView cancelDialog = (ImageView) schemeDialog.findViewById(R.id.image_cancel);
            cancelDialog.setVisibility(View.VISIBLE);
            cancelDialog.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    schemeDialog.cancel();
                }
            });
            schemeDialog.show();

        }
    }

    private class GenericOnItemSelectedListenerUom implements Spinner.OnItemSelectedListener {
        View view;
        private int position;
        TextView tv;

        private GenericOnItemSelectedListenerUom(int pos, View view, TextView textView) {
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
//                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes"))
//                {
                    //change mrp data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, nameValuesProductListLocal.get(position).getProdCode(), nameValuesProductListLocal.get(position).getUom1(),"uom1",nameValuesProductListLocal.get(position).getConversionFactor());
                    SetMrpValueInRateEditText(this.view, mrpValue);
//                }
                oum = "uom1";



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
//                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes"))
//                {
                    //change mrp  data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, nameValuesProductListLocal.get(position).getProdCode(), nameValuesProductListLocal.get(position).getUom2(),"uom2",nameValuesProductListLocal.get(position).getConversionFactor());
                    SetMrpValueInRateEditText(this.view, mrpValue);
//                }
                oum = "uom2";


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


    public void showRemarks(ProductMasterDetails productMasterDetails, int pos)
    {
        final Dialog discountDialog = new Dialog(context);
        discountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        discountDialog.setContentView(R.layout.user_instruction_dialog1);
        discountDialog.setCancelable(false);
        TextView title = (TextView) discountDialog.findViewById(R.id.title);
        title.setText("Enter Remarks");
        final EditText edInst = (EditText) discountDialog.findViewById(R.id.ed_input);
        Button submit = (Button) discountDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new View.OnClickListener()
        {
            @Override
            public void onClick(View v) {
                String freightAmount = "";

                    freightAmount = edInst.getText().toString();
                    addCurrentProductToCartWithRemarks(productMasterDetails, pos, freightAmount);
                    //Constants.orderRemarks = freightAmount;
                    discountDialog.cancel();


            }
        });
        discountDialog.show();
    }


    public void addCurrentProductToCartWithRemarks(ProductMasterDetails productMasterDetails, int pos,String remarks) {

        String qty = productMasterDetails.getQty();

        String stkQty = productMasterDetails.getStkQty();

        if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {

            if (!Utils.isNumeric(qty)) {
                Toast.makeText(context, "Provide proper quantity for order-.", Toast.LENGTH_SHORT).show();
            } else {
                String st = String.valueOf(Integer.parseInt(nameValuesProductListLocal.get(pos).getStock()) - Integer.parseInt(nameValuesProductListLocal.get(pos).getQty()));
                mAceDnsTransactionDatabase.UpdateOrderStockCustomerAddToCart(""+nameValuesProductListLocal.get(pos).getProdCode(), ""+st);
                //Utils.showToast(context,st + " " + nameValuesProductListLocal.get(pos).getProdCode());
            }
        }

        if(Constants.isVanSales)
        {
            if(!Utils.isNumeric(stkQty))
            {
                stkQty="0";
                if(!Utils.isNumeric(qty) || Double.parseDouble(qty)<=0)
                {
                    return;
                }

            }
            else
            {
                if(!Utils.isNumeric(qty) || Double.parseDouble(qty)<=0)
                {
                    productMasterDetails.setQty("0");
                    qty="0";
                }
            }
        }
        String rate = productMasterDetails.getMrpValue();
        //            Toast.makeText(context,nameValuesProductListLocalDo.get(pos).getDesc()+ "", Toast.LENGTH_SHORT).show();
        if (Utils.isNumeric(qty) && Utils.isNumeric(rate))
        {
            if(Constants.isVanSales && Double.parseDouble(qty)>Double.parseDouble(productMasterDetails.getClosingStk()))
            {
                Toast.makeText(context, "Insufficient Stock.", Toast.LENGTH_SHORT).show();
            }
            else
            {
                double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
                if (productMasterDetails.getSchemePresent())
                {
                    //                String prodCode=nameValuesProductListLocalDo.get(pos).getProdCode();
                    final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
                    for (int i = 0; i < schemesOnCurrentProduct.size(); i++)
                    {
                        ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
                        for (int x = 0; x < FreeProductsOnCurrentItem.size(); x++) {
                            SchemeFreebiesDetails item = FreeProductsOnCurrentItem.get(x);
                            if (!item.getqty().matches("0"))//offer on qty
                            {
                                if (doesQuantityMatchesQtySlab(qty, item.getqty())) {
                                    if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                    {
                                        if(Constants.weightage.matches("no")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt, context,remarks);
                                        }else if (Constants.weightage.matches("yes")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context,remarks);
                                        }
                                    } else//offer on total amount
                                    {
                                        amnt = getAmnt(amnt, item);
                                    }

                                } else {
                                    if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                    {
                                        if(Constants.weightage.matches("no")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt, context,remarks);
                                        }else if (Constants.weightage.matches("yes")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context,remarks);
                                        }

                                    }
                                }
                            } else {
                                if (amnt >= Double.parseDouble(item.getamount())) {
                                    if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                    {
                                        if(Constants.weightage.matches("no")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt, context,remarks);
                                        }else if (Constants.weightage.matches("yes")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context,remarks);
                                        }

                                    } else {
                                        amnt = getAmnt(amnt, item);
                                    }

                                } else {
                                    if (!item.getfreebiesProdCode().trim().matches(""))//free product
                                    {
                                        if(Constants.weightage.matches("no")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt, context,remarks);
                                        }else if (Constants.weightage.matches("yes")) {
                                            Utils.addQtyProductToListWithRemarks(item, Double.parseDouble(qty), amnt,""+productMasterDetails.getWeightage(), context,remarks);
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
                if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))
                        && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdInputDropDown().equalsIgnoreCase("input"))
                {
                    String TradeDiscount=productMasterDetails.getTradeDiscnt();
                    if(Utils.isNumeric(TradeDiscount))
                    {
                        Double tdInDouble= Double.parseDouble(TradeDiscount);
                        if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
                        {

                            amnt=amnt - tdInDouble;
                        }
                        else
                        {
                            amnt=amnt - ((tdInDouble*amnt)/100);
                        }
                    }
                }
                productMasterDetails.setAmount(defaultFormat.format(amnt));
                productMasterDetails.setOrder_wise_remarks(""+remarks);
                if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes"))
                {
                    productMasterDetails.setHeight(""+Constants.sylHight);
                    productMasterDetails.setWeidth(""+Constants.sylWeidth);
                }

                Constants.selectedProductMasterList.add(productMasterDetails);
                tempProductList.remove(pos);
                notifyDataSetChanged();
                OrderFormActivityAlternateDesign.changeCartCount();
            }

        } else {

//                if (!Utils.isNumeric(stkQty)) {
//                    Toast.makeText(context, "Please Provide numeric input for stock audit even if its zero.", Toast.LENGTH_SHORT).show();
//                }

            if (!Utils.isNumeric(qty)) {
                Toast.makeText(context, "Provide proper quantity for order-.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(context, "Improper sale rate. Could not add item to cart.", Toast.LENGTH_SHORT).show();
            }

        }
    }



}