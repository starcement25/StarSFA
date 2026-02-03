package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.Html;
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

import static android.view.View.GONE;
import static com.forcepower.acedns.R.id.radioUom;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.defaultFormat3;
import static com.forcepower.acedns.constants.Constants.directOrBroker;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isSecondaryFreightIncluded;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.maxallocation;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.remainingAllocation;
import static com.forcepower.acedns.constants.Constants.remainingAllocationTV;
import static com.forcepower.acedns.constants.Constants.selectedSSOfCustomer;
import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;

import com.forcepower.acedns.activity.BargainActivity;
import com.forcepower.acedns.activity.OrderFormActivityAlternateDesign;

public class BargainProductInputAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    int positionOfSelectedItem = 0;
    private ViewHolder viewHolder;
    public static ArrayList<ProductMasterDetails> nameValuesProductListLocal;
    private Boolean isRateEditable = false;

    public BargainProductInputAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocal = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();

        for (int i = 0; i < nameValues.size(); i++)
        {
            Double freightRate=0.00;
            if (isSecondaryFreightIncluded)
            {

                if (BargainActivity.selectedFreightRate != null && !BargainActivity.selectedFreightRate.matches(""))
                {
                    if(mChosenUomType.equalsIgnoreCase("loose"))//ignore track load quantity
                    {
                        freightRate=Double.parseDouble(BargainActivity.selectedFreightRate);
                    }
                    else
                    {
                        String trackLoadQuantity = "";
                        if (mDepotOrPlant.equalsIgnoreCase("depot"))
                        {
                            trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCodeBargain(nameValues.get(i).getDnsProdCode(), Constants.selectedCustomer.getLoadabilityTon(), Constants.selectedCustomer.getTransportMode(), false);
                        }
                        else
                        {

                            trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCodeBargain(nameValues.get(i).getDnsProdCode(), Constants.selectedCustomer.getLoadabilityTon(), Constants.selectedCustomer.getTransportMode(), false);
                        }
                        if (Utils.isNumeric(trackLoadQuantity) && Double.parseDouble(trackLoadQuantity)>0)
                        {
                            freightRate = Double.parseDouble(BargainActivity.selectedFreightRate) / Double.parseDouble(trackLoadQuantity);
                            freightRate=Math.ceil(freightRate);
                        }
                    }


                }
            }
            nameValuesProductListLocal.get(i).setFreight(""+freightRate);
            ProductMasterDetails productMasterDetails = nameValuesProductListLocal.get(i);
            productMasterDetails.setFreight(""+freightRate);
            if(!Utils.isNumeric( productMasterDetails.getVatRate()))
            {
                productMasterDetails.setVatRate("0");
            }
            String prodCode = nameValues.get(i).getProdCode();
            if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes"))
            {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, productMasterDetails.getUom1(), productMasterDetails.getGrpCode(),productMasterDetails);
            }
            else
            {
                setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), i, prodCode, "", productMasterDetails.getGrpCode(),productMasterDetails);
            }

            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                productMasterDetails.setMrpValue("NA");
            }
        }
    }

    public String setMrpValue(String customerType, String getMultipleRate, int i, String prodCode, String selectedUom,String grpCode,ProductMasterDetails productMasterDetails)
    {
        String mrpValue = "0";
        ArrayList<MRPDetails> mrpSpinnerList ;
        boolean ismcxOpen = mAceDnsDatabase.ismcxOpenOrCLoseForCurrentCustomerAndSku(prodCode, Constants.selectedCustomer.getCustomerCode());

        if(ismcxOpen)
        {
            mrpSpinnerList = mAceDnsDatabase.getMRPListFromMcxRateTable(prodCode, selectedUom);
        }
        else
        {
            mrpSpinnerList = mAceDnsDatabase.getMRPList(prodCode, selectedUom);
        }
        if (mrpSpinnerList != null && mrpSpinnerList.size() > 0)
        {
            nameValuesProductListLocal.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
            if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") || ismcxOpen)
            {
                mrpValue = mrpSpinnerList.get(0).getMrpValue();


                nameValuesProductListLocal.get(i).setMrpValue(mrpValue);
                nameValuesProductListLocal.get(i).setRate(mrpValue);

            } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))
            {

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
                nameValuesProductListLocal.get(i).setMrpValue(mrpValue);
                nameValuesProductListLocal.get(i).setRate(mrpValue);
            }

        }
        if (directOrBroker.equalsIgnoreCase("broker"))
        {
            String  brokerageCost = mAceDnsDatabase.getBerokarageCostByCustomerProdCatUom(grpCode, Constants.selectedCustomer.getCustomerCode(),nameValuesProductListLocal.get(i).getUom1());
            if(Utils.isNumeric(brokerageCost))
            {
                nameValuesProductListLocal.get(i).setbrokerageCost(brokerageCost);
            }
            else
            {
                nameValuesProductListLocal.get(i).setbrokerageCost("0");
            }
        }
        else
        {
            nameValuesProductListLocal.get(i).setbrokerageCost("0");
        }
        if (selectedSSOfCustomer!=null)
        {
            String  brokerageCost = mAceDnsDatabase.getBerokarageCostByCustomerProdCatUom(grpCode, Constants.selectedSSOfCustomer.getCustomerCode(),nameValuesProductListLocal.get(i).getUom1());
            if(Utils.isNumeric(brokerageCost))
            {
                nameValuesProductListLocal.get(i).setbrokerageCostSS(brokerageCost);
            }
            else
            {
                nameValuesProductListLocal.get(i).setbrokerageCostSS("0");
            }
        }
        else
        {
            nameValuesProductListLocal.get(i).setbrokerageCostSS("0");
        }
        if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app"))
        {

            String maxTD="0";
            if(Constants.productDetailsObj.getSaudaFilter().equalsIgnoreCase("1"))
            {
                maxTD = mAceDnsDatabase.GetTDOfEmployee(Constants.employeeDetailObject.getEmpCode(), productMasterDetails.getGrpCode());
            }
            else if(Constants.productDetailsObj.getSaudaFilter().equalsIgnoreCase("2"))
            {
                maxTD = mAceDnsDatabase.GetTDOfEmployee(Constants.employeeDetailObject.getEmpCode(), productMasterDetails.getSubGrpCode());
            }
            else if(Constants.productDetailsObj.getSaudaFilter().equalsIgnoreCase("3"))
            {
                maxTD = mAceDnsDatabase.GetTDOfEmployee(Constants.employeeDetailObject.getEmpCode(), productMasterDetails.getBrndCode());
            }
            else //if(Constants.productDetailsObj.getSaudaFilter().equalsIgnoreCase("4"))
            {
                maxTD = mAceDnsDatabase.GetTDOfEmployee(Constants.employeeDetailObject.getEmpCode(), productMasterDetails.getProdCode());
            }
            if(!Utils.isNumeric(maxTD))
            {
                maxTD="0";

            }
            nameValuesProductListLocal.get(i).settradeDiscntLimit(maxTD);
        }
        nameValuesProductListLocal.get(i).setTradeDiscnt("0");
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

        viewHolder.etProdDiscount = (EditText) convertView.findViewById(R.id.etProdDiscount);
        viewHolder.qtyET = (EditText) convertView.findViewById(R.id.etProdQty);
        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
        viewHolder.rateTV = (TextView) convertView.findViewById(R.id.rateTV);
        viewHolder.radioUom = (Spinner) convertView.findViewById(radioUom);
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);
        }

        ProductMasterDetails productMasterDetailsObj = nameValuesProductListLocal.get(position);
        String uom1 = productMasterDetailsObj.getUom1();
        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);

            viewHolder.convTV.setVisibility(GONE);
            viewHolder.radioUom.setVisibility(View.VISIBLE);

            final ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray.add(uom1);
            spinnerArray.add(productMasterDetailsObj.getUom2());

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(context, android.R.layout.simple_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            viewHolder.radioUom.setAdapter(spinnerArrayAdapter);
            if (productMasterDetailsObj.getUomSelectedForProduct().equalsIgnoreCase(productMasterDetailsObj.getUom2())) {
                viewHolder.radioUom.setSelection(1);
            } else {
                viewHolder.radioUom.setSelection(0);
            }
            viewHolder.radioUom.setOnItemSelectedListener(new GenericOnItemSelectedListenerUom(position, viewHolder.radioUom));
        }
        if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app"))
        {
            viewHolder.convTV.setVisibility(GONE);
            viewHolder.etProdDiscount.setVisibility(View.VISIBLE);
        }
        else
        {
            viewHolder.etProdDiscount.setVisibility(GONE);
        }
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.schemeDetailsIcon = (ImageView) convertView.findViewById(R.id.schemeDetailsIcon);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = productMasterDetailsObj.getDesc();

        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes"))
        {

        }
        else
        {
            if (uomToBeShownForProduct.equalsIgnoreCase("uom1"))
            {
                if(uom1.equalsIgnoreCase("loose"))
                {
                    uom1="MT";
                }
                viewHolder.convTV.setText(uom1);
            } else if (uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
                viewHolder.convTV.setText(productMasterDetailsObj.getUom2());
            } else {
                viewHolder.convTV.setText(productMasterDetailsObj.getUOM3());
            }
        }

        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            if (Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
                desc = productMasterDetailsObj.getsize();
            } else {
                if (desc.contains("-")) {
                    String[] splittedSkuDesc = desc.split("-");
                    desc = splittedSkuDesc[splittedSkuDesc.length - 1].trim();
                }

            }
        }
        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
//        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml(desc+"<font color='#D7B56D'>/</font>"+nameValuesProductListLocalDo.get(position).getuom4(),HtmlCompat.FROM_HTML_MODE_LEGACY));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (productMasterDetailsObj.getFocus().equalsIgnoreCase("y") && !Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
        }
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }

        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));

            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));
            if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app"))
            {
                viewHolder.etProdDiscount.addTextChangedListener(new GenericTextWatcherTD(position));
            }

        }
        String prodCode = productMasterDetailsObj.getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = productMasterDetailsObj.getQty();
        String currentMrp = productMasterDetailsObj.getRate();
        if (Utils.isNumeric(currentqty) && Double.parseDouble(currentqty)>0)
            viewHolder.qtyET.setText(currentqty);

        String currenttd = productMasterDetailsObj.getTradeDiscnt();
        if (Utils.isNumeric(currenttd) && Double.parseDouble(currenttd)>0)
            viewHolder.etProdDiscount.setText(currenttd);


        if (!currentMrp.matches("NA"))
        {
            if(!Utils.isNumeric(currentMrp))
            {
                currentMrp="0.00";
            }
            else
            {

                double mrpdbl = Double.parseDouble(currentMrp);
                double secondaryFreightDbl = Double.parseDouble(productMasterDetailsObj.getFreight());
                double brokerageCOstDbl = Double.parseDouble(productMasterDetailsObj.getbrokerageCost());
                double brokerageCostDbl = Double.parseDouble(productMasterDetailsObj.getbrokerageCostSS());
                double addntPremiumDbl = Double.parseDouble(productMasterDetailsObj.getadditionalPremium());
                double primaryFreightDbl = Double.parseDouble(productMasterDetailsObj.getprimaryFreight());
                double depotCostDbl = Double.parseDouble(productMasterDetailsObj.getdepotCost());
                double marginCostDbl = Double.parseDouble(productMasterDetailsObj.getmarginCost());
                double addtnlTdDbl = Double.parseDouble(productMasterDetailsObj.getadditionalTD());
                Double totalRate= (
                         mrpdbl
                        + secondaryFreightDbl
                        + brokerageCOstDbl
                        + brokerageCostDbl
                        + addntPremiumDbl
                        + primaryFreightDbl
                        + depotCostDbl
                        + marginCostDbl
                )- addtnlTdDbl;
//                Double vatrate=Double.parseDouble(nameValuesProductListLocalDo.get(position).getVatRate());
//                if(vatrate>0)
//                {
//                    totalRate=totalRate+((totalRate*vatrate)/100);
//                }
                currentMrp=defaultFormat.format(totalRate);
            }
//            viewHolder.rateTV.setText(currentMrp);
            String finalDisplayRat4e ="";
            if(productMasterDetailsObj.getPackSize().equalsIgnoreCase("bp"))
            {
                Double finalDisplayRate=Double.parseDouble(currentMrp);
                double RoundedUpValue = Math.ceil(finalDisplayRate);
                finalDisplayRat4e = RoundedUpValue +"";
            }
            else
            {
                Double finalDisplayRate=Double.parseDouble(currentMrp)/Double.parseDouble(productMasterDetailsObj.getuom4());
                double roundedOffOneValue = Math.round(finalDisplayRate * 10) / 10.0;
                finalDisplayRat4e = roundedOffOneValue +"";
            }

            viewHolder.rateTV.setText(finalDisplayRat4e);

        }


        return convertView;
    }

    public void SetMrpValueInRateEditText(View view, String mrpValue) {
        TextView rateTV = view.findViewById(R.id.rateTV);
        rateTV.setText(mrpValue);
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, convTV,rateTV;
        EditText qtyET,etProdDiscount;
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
            calculateRemainingAllocation();
        }
    }
    private class GenericTextWatcherTD implements TextWatcher {

        private int pos;

        private GenericTextWatcherTD(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();

            //for cp, td will be productMasterList*uom4
            if(Utils.isNumeric(text) && text.contains("-") && nameValuesProductListLocal.get(pos).getPackSize().equalsIgnoreCase("cp") && Utils.isNumeric(nameValuesProductListLocal.get(pos).getuom4()))
            {
                Double tdInput= Double.parseDouble(text);
                Double uom4= Double.parseDouble(nameValuesProductListLocal.get(pos).getuom4());
                tdInput=tdInput*uom4;
                text= String.valueOf(tdInput);
            }
            nameValuesProductListLocal.get(pos).setTradeDiscnt(text);
     }
    }
    private void calculateRemainingAllocation()
    {
        remainingAllocation=maxallocation;


        for(int i=0;i<nameValuesProductListLocal.size();i++)
        {
            ProductMasterDetails currentItem= nameValuesProductListLocal.get(i);
            String qty = currentItem.getQty();
            if(Utils.isNumeric(qty) && Double.parseDouble(qty)>0)
            {
                Double qtyCurrent=Double.parseDouble(qty);
                if(!mChosenUomType.equalsIgnoreCase("loose"))
                {
                    qtyCurrent = mAceDnsDatabase.calculatedValueC2MBargain(currentItem.getProdCode(), qtyCurrent);
                }
                remainingAllocation=remainingAllocation-qtyCurrent;
            }
            remainingAllocationTV.setText("Remaining Allocation : "+defaultFormat3.format(remainingAllocation) + " MT");
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

        public void addfreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails, int pos)
        {
            //        ProductMasterDetails productMasterDetails = ;
            String qty = productMasterDetails.getQty();

            String rate = productMasterDetails.getMrpValue();
            if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes"))
            {
                Double vatAmount=0.00,totalAmount=0.00;
                double vatRateInDouble =0.00;
                String vatRate = productMasterDetails.getVatRate();
                if(Utils.isNumeric(vatRate) && Double.parseDouble(vatRate)>0)
                {
                    vatRateInDouble=Double.parseDouble(vatRate);
                }

                if(vatRateInDouble>0)
                {
                    vatAmount=(Double.parseDouble(rate)*vatRateInDouble)/100;
                }
//                viewHolder.txtTDVatTitle.setText(defaultFormat.format(vatAmount));
//                viewHolder.gstPercenrtageTV.setText("GST: "+vatRateInDouble+"%");

            }
            //            Toast.makeText(context,nameValuesProductListLocalDo.get(pos).getDesc()+ "", Toast.LENGTH_SHORT).show();
            if (Utils.isNumeric(qty) && Utils.isNumeric(rate))
            {
                double amnt = Double.parseDouble(qty) * (Double.parseDouble(rate)+Double.parseDouble(productMasterDetails.getbrokerageCost()));
                productMasterDetails.setAmount(defaultFormat.format(amnt));
                Constants.selectedProductMasterList.add(productMasterDetails);
                OrderFormActivityAlternateDesign.changeCartCount();

            }
            else
            {
                if (!Utils.isNumeric(qty))
                {
                    Toast.makeText(context, "Provide proper quantity for order.", Toast.LENGTH_SHORT).show();
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

            if (actionId == EditorInfo.IME_ACTION_DONE)
            {
//                if (sizeOfList - (pos + 1) > 4)
//                {
//                    dialogList.smoothScrollToPosition(pos + 1 + 5);
//                }
//                else if (pos + 1 == sizeOfList)
//                {
//                    Toast.makeText(context, "End of List", Toast.LENGTH_SHORT).show();
//                } else {
//                    dialogList.smoothScrollToPosition(sizeOfList);
//                }


                return true;
            }
            return false;
        }
    }

    private class GenericEditorOnTouchListener implements EditText.OnTouchListener {
        String rateOrQtyOrStk;

        private GenericEditorOnTouchListener(String rateOrQtyOrStkOrTd, EditText tv) {
            this.rateOrQtyOrStk = rateOrQtyOrStkOrTd;
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
//            edittext.setInputType(InputType.TYPE_NULL); // Disable standard keyboard
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
            ProductMasterDetails productMasterDetails = nameValuesProductListLocal.get(position);
            if (i == 0) {
                productMasterDetails.setUomSelectedForProduct(productMasterDetails.getUom1());
                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                    //change mrp data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, productMasterDetails.getProdCode(), productMasterDetails.getUom1(),nameValuesProductListLocal.get(i).getGrpCode(),productMasterDetails);
                    SetMrpValueInRateEditText(this.view, mrpValue);

                }
            } else {
                productMasterDetails.setUomSelectedForProduct(productMasterDetails.getUom2());
                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                    //change mrp  data for arraylist
                    String mrpValue = setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, productMasterDetails.getProdCode(), productMasterDetails.getUom2(),nameValuesProductListLocal.get(i).getGrpCode(), productMasterDetails);
                    SetMrpValueInRateEditText(this.view, mrpValue);
                }
            }

        }

        @Override
        public void onNothingSelected(AdapterView<?> parent) {

        }
    }

}