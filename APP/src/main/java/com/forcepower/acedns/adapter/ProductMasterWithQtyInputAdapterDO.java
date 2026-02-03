package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Arrays;

import androidx.core.text.HtmlCompat;

import static android.view.View.GONE;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.isQtySelected;
import static com.forcepower.acedns.constants.Constants.isStkQtySelected;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.remainingAllocationTV;
import static com.forcepower.acedns.constants.Constants.schemeListForCurrentProducts;
import static com.forcepower.acedns.constants.Constants.selectedItemView;
import static com.forcepower.acedns.constants.Constants.tempProductList;
import static com.forcepower.acedns.constants.Constants.totalDoQty;

import com.forcepower.acedns.activity.DOFormActivity;
import com.forcepower.acedns.activity.OrderFormActivityAlternateDesign;

public class ProductMasterWithQtyInputAdapterDO extends ArrayAdapter<ProductMasterDetails>
{

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    int positionOfSelectedItem = 0;
    private ViewHolder viewHolder;
    public static ArrayList<ProductMasterDetails> nameValuesProductListLocalDo;
    private Boolean isRateEditable = true;
    Double remainingQty=0.0;
    String currentUom;
    public ProductMasterWithQtyInputAdapterDO(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        nameValuesProductListLocalDo =new ArrayList<>();
        nameValuesProductListLocalDo = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();
        isRateEditable = false;
        currentUom=mChosenUomType;
        if(currentUom.equalsIgnoreCase("loose"))
        {
            currentUom="MT";
        }
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
        viewHolder.clstkTV = (TextView) convertView.findViewById(R.id.clstkTV);
        viewHolder.rateTV = (TextView) convertView.findViewById(R.id.rateTV);
        viewHolder.convTV = (TextView) convertView.findViewById(R.id.convTV);
        viewHolder.addtocartIV =  convertView.findViewById(R.id.addtocartIV);
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);
        }
        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes"))
        {
            viewHolder.clstkTV.setVisibility(View.VISIBLE);
        }

//        viewHolder.addtocartIV.setVisibility(View.VISIBLE);
        viewHolder.convTV.setVisibility(GONE);
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.schemeDetailsIcon = (ImageView) convertView.findViewById(R.id.schemeDetailsIcon);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = nameValuesProductListLocalDo.get(position).getDesc();

        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes"))
        {
            viewHolder.clstkTV.setText(HtmlCompat.fromHtml("<font color='#D7B56D'>"+ nameValuesProductListLocalDo.get(position).getClosingStk()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
//            viewHolder.clstkTV.setText(nameValuesProductListLocalDo.get(position).getClosingStk());
//            desc=desc+"<br> <font color='#D7B56D'>Cl Stk: "+nameValuesProductListLocalDo.get(position).getClosingStk()+"</font>";
        }
//        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml("<font color='#D7B56D'>"+nameValuesProductListLocalDo.get(position).getparentProdName()+"</font><br>"+desc,HtmlCompat.FROM_HTML_MODE_LEGACY));
        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml(desc,HtmlCompat.FROM_HTML_MODE_LEGACY));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (nameValuesProductListLocalDo.get(position).getFocus().equalsIgnoreCase("y") && !Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            viewHolder.txtViewProductDesc.setTextColor(Color.parseColor("#F58322"));
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position));
        }
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals(""))
        {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.qtyET));

        }
        String prodCode = nameValuesProductListLocalDo.get(position).getProdCode();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = nameValuesProductListLocalDo.get(position).getQty();
        String currentMrp = nameValuesProductListLocalDo.get(position).getMrpValue();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
        if (!currentMrp.matches("NA"))
            viewHolder.rateTV.setText(currentMrp);
//            viewHolder.convTV.setText(nameValuesProductListLocalDo.get(position).getUom1());


        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode, convTV,rateTV,clstkTV;
        EditText qtyET;
        ImageView addtocartIV, schemeDetailsIcon;
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
            nameValuesProductListLocalDo.get(pos).setQty(text);
            double addedQty=0.0;
            for(int i=0;i<nameValuesProductListLocalDo.size();i++)
            {
                String qty=nameValuesProductListLocalDo.get(i).getQty();
                if(Utils.isNumeric(qty))
                {
                    addedQty=addedQty+Double.parseDouble(qty);
                }
            }
            double remainingAllocation=totalDoQty-addedQty;

            remainingAllocationTV.setText("Balance Quantity : "+defaultFormat.format(remainingAllocation)+" "+currentUom);
        }
    }


    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;

        private GenericItemCLickListener(int pos) {
            this.pos = pos;
        }

        @Override
        public void onClick(View view) {
            addCurrentProductToCart(nameValuesProductListLocalDo.get(pos), pos);

        }

        public void addCurrentProductToCart(ProductMasterDetails productMasterDetails, int pos) {
            String qty = productMasterDetails.getQty();
            String rate = productMasterDetails.getMrpValue();

            if (Utils.isNumeric(qty) && Double.parseDouble(qty)>0 && Utils.isNumeric(rate))
            {

                if(isMaxQtyForDeliveryNotExceeded(Double.parseDouble(qty),Constants.selectedProductOfBargain.getQty()))
                {
                    double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
                    productMasterDetails.setAmount(defaultFormat.format(amnt));
                    Constants.selectedProductMasterList.add(productMasterDetails);

                    tempProductList.remove(pos);
                    notifyDataSetChanged();
                    OrderFormActivityAlternateDesign.changeCartCount();
                    Double QtyRemaining=Double.parseDouble(Constants.selectedProductOfBargain.getQty());
                    for(int x=0;x<Constants.selectedProductMasterList.size();x++)
                    {
                        ProductMasterDetails currentItem = Constants.selectedProductMasterList.get(x);
                        if(currentProductPresentInList(currentItem.getProdCode()))
                        {
                            if(Utils.isNumeric(currentItem.getQty()) && Double.parseDouble(currentItem.getQty())>0)
                            {
                                QtyRemaining=QtyRemaining-Double.parseDouble(currentItem.getQty());
                            }
                        }
                    }
                    remainingAllocationTV.setText("Balance Quantity : "+defaultFormat.format(QtyRemaining)+" "+ Constants.selectedProductOfBargain.getUom1());
                }
                else
                {
                    Toast.makeText(context, "Exceeded max Quantity.", Toast.LENGTH_SHORT).show();
                }
            }
            else
            {
             if (!Utils.isNumeric(qty)|| Double.parseDouble(qty)<=0)
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

    public static boolean isMaxQtyForDeliveryNotExceeded(Double currentQty,String maxavailableQty)
    {
        Double maxQtyAvailable=0.0,maxQtyAddedToCart=currentQty;
        if(Utils.isNumeric(maxavailableQty))
        {
            maxQtyAvailable=Double.parseDouble(maxavailableQty);
        }
        for(int i=0;i<nameValuesProductListLocalDo.size();i++)
        {

            Double qtyForCurrentItem=0.0;
            ProductMasterDetails currentItem = nameValuesProductListLocalDo.get(i);
            if(Utils.isNumeric(currentItem.getQty()))
            {
                if(currentProductPresentInList(currentItem.getProdCode()))
                {
                    qtyForCurrentItem=Double.parseDouble(currentItem.getQty());
                    maxQtyAddedToCart=maxQtyAddedToCart+qtyForCurrentItem;
                }

            }
        }
        if(maxQtyAddedToCart<=maxQtyAvailable)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static boolean currentProductPresentInList(String prodCode)
    {
        Boolean currentProductPresentInList=false;
        for(int i = 0; i< DOFormActivity.productMasterListBeforeRemovingRepeatedProduct.size(); i++)
        {
            String currentProdCode= DOFormActivity.productMasterListBeforeRemovingRepeatedProduct.get(i).getProdCode();
            if(currentProdCode.matches(prodCode))
            {
                currentProductPresentInList= true;
                break;

            }
        }
        return currentProductPresentInList;
    }
    private boolean currentProductPresentInList2(String prodCode)
    {
        Boolean currentProductPresentInList=false;
        for(int i=0;i<Constants.selectedProductMasterList.size();i++)
        {
            String currentProdCode=Constants.selectedProductMasterList.get(i).getProdCode();
            if(currentProdCode.matches(prodCode))
            {
                currentProductPresentInList= true;
                break;

            }
        }
        return currentProductPresentInList;
    }

    private class GenericEditorOnTouchListener implements EditText.OnTouchListener {
        String rateOrQtyOrStk;

        private GenericEditorOnTouchListener(String rateOrQtyOrStk, EditText tv) {
            this.rateOrQtyOrStk = rateOrQtyOrStk;

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
            title.setText("Schemes on " + nameValuesProductListLocalDo.get(positionOfSelectedItem).getDesc());
            btn_avail.setVisibility(GONE);
            TextView text = (TextView) schemeDialog.findViewById(R.id.text);
            text.setVisibility(GONE);
            final RadioGroup orderSchemesListOnProductRG = (RadioGroup) schemeDialog.findViewById(R.id.orderSchemesListOnProductRG);
            orderSchemesListOnProductRG.setVisibility(View.VISIBLE);
            orderSchemesListOnProductRG.setGravity(Gravity.CENTER_VERTICAL);
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(nameValuesProductListLocalDo.get(positionOfSelectedItem).getSchemeIds().split(",")));
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
                            nameValuesProductListLocalDo.get(positionOfSelectedItem).setQty(minValueForOffer);
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

}