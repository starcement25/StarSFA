package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Arrays;

import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;

public class OrderConfirmAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    TextView txtTotal;
    String td = "";
    int editablePosition = -1;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String transType = "";
    private ViewHolder viewHolder;

    public OrderConfirmAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues, TextView txtTotal, String td, String transType) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.txtTotal = txtTotal;
        this.td = td;
        this.transType = transType;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

//		if (convertView == null) {
        LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
        viewHolder.qtyLayout = (LinearLayout) convertView.findViewById(R.id.qty_lay);
        viewHolder.slNo = (TextView) convertView.findViewById(R.id.txt_sl);
        viewHolder.prodName = (TextView) convertView.findViewById(R.id.txt_name);
        viewHolder.qty = (TextView) convertView.findViewById(R.id.txt_qty);
        viewHolder.mrpTitle = (TextView) convertView.findViewById(R.id.mrp_title);
        viewHolder.mrpVal = (TextView) convertView.findViewById(R.id.txt_mrp);
        viewHolder.discountLayout = (LinearLayout) convertView.findViewById(R.id.discount_layout);
        viewHolder.discount = (TextView) convertView.findViewById(R.id.txt_discount);
        viewHolder.total = (TextView) convertView.findViewById(R.id.txt_total);
        viewHolder.txt_slsss = (TextView) convertView.findViewById(R.id.txt_slsss);
        viewHolder.txtTDVatTitle = (TextView) convertView.findViewById(R.id.txt_td_vat);
        viewHolder.txtPack = (TextView) convertView.findViewById(R.id.txt_pack);
        viewHolder.txtFreight = (TextView) convertView.findViewById(R.id.txt_freight);

        convertView.setTag(viewHolder);
//		}
//		else{
//			viewHolder = (ViewHolder) convertView.getTag();
//		}

        if (position % 2 == 0) {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            viewHolder.txtTDVatTitle.setText("VAT : ");
        } else {
            viewHolder.txtTDVatTitle.setText("TD : ");
        }
        ProductMasterDetails detailsObj = nameValues.get(position);
        if (detailsObj.getPackSize() != null && detailsObj.getPackSize().length() > 0) {
            viewHolder.txtPack.setVisibility(View.VISIBLE);
        } else {
            viewHolder.txtPack.setVisibility(View.GONE);
        }


        viewHolder.slNo.setText("" + (position + 1) + ".");


        String uom = nameValues.get(position).getUom1();


        if (nameValues.get(position).getSelectedUOM() == -1)
        {

            if (uomToBeShownForProduct.equalsIgnoreCase("uom1")) {
                uom = nameValues.get(position).getUom1();
            } else if (uomToBeShownForProduct.equalsIgnoreCase("uom2")) {
                uom = nameValues.get(position).getUom2();
            } else {
                uom = nameValues.get(position).getUOM3();
            }
            viewHolder.qty.setText(nameValues.get(position).getQty() + " " + uom);
        }
        else
        {
            if (nameValues.get(position).getSelectedUOM() == 0)
            {
                if (viewHolder.txtPack.getVisibility() == View.VISIBLE)
                {
                    viewHolder.qtyLayout.setVisibility(View.GONE);
                    viewHolder.txtPack.setText(getPackString(nameValues.get(position)));
                }
                else
                {

                }
            }

        }
        String stockAuditText = "", skuSizeText = "";
        if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
        {
            stockAuditText = ",<font color='#003399'> Stk : </font>" + nameValues.get(position).getStkQty() + " " + uom;
        }
        else if(Constants.isVanSales)
        {
            stockAuditText = ",<font color='#003399'> Return : </font>" + nameValues.get(position).getStkQty();
        }
        if (Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
            skuSizeText = "<font color='#003399'> Size : </font>" + nameValues.get(position).getsize();
        }
        viewHolder.prodName.setText(Html.fromHtml(nameValues.get(position).getDesc() + skuSizeText + stockAuditText));
        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            viewHolder.qty.setText(nameValues.get(position).getQty() + " " + nameValues.get(position).getUomSelectedForProduct());
        }

        if (nameValues.get(position).getFreight().length() > 0) {
            viewHolder.txtFreight.setText(defaultFormat.format(Double.parseDouble(nameValues.get(position).getFreight())));
        }

        String productQtyWiseTD = Constants.productDetailsObj.getProductQtyWiseTD();
        if (productQtyWiseTD.equalsIgnoreCase("yes") && nameValues.get(position).getQuantityEligibleForTD()) {
            viewHolder.txtTDVatTitle.setText("TD% :");
            viewHolder.discount.setText(nameValues.get(position).getTDPercent());
        } else if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")) {
            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
                if (nameValues.get(position).getIsTradeDiscount() == true) {
                    viewHolder.txtTDVatTitle.setText("TD :");
                    viewHolder.discount.setText(nameValues.get(position).getTradeDiscnt());
                } else {
                    viewHolder.txtTDVatTitle.setText("Premium :");
                    viewHolder.discount.setText(nameValues.get(position).getPremium());
                }
            } else {
                viewHolder.discount.setText(nameValues.get(position).getTradeDiscnt());
            }

        } else if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            viewHolder.discount.setText(defaultFormat.format(Double.parseDouble(nameValues.get(position).getVat())));
        }


        String[] vatArray = Constants.orderFormDetailsObj.getVatType().split(",");
        boolean quantityEligibleForTD = nameValues.get(position).getQuantityEligibleForTD();
        if (!quantityEligibleForTD || !productQtyWiseTD.equalsIgnoreCase("yes")) {
            viewHolder.discountLayout.setVisibility(View.INVISIBLE);
        } else if (!Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") && Arrays.asList(vatArray).contains(transType) &&
                !(Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))))
        {
            viewHolder.discountLayout.setVisibility(View.INVISIBLE);
        }
        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
            viewHolder.mrpTitle.setText("MRP : ");
            viewHolder.mrpVal.setText(defaultFormat.format(Double.parseDouble(nameValues.get(position).getMrpValue())));
        } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            viewHolder.mrpTitle.setText("Sale Rate : ");
            String mrpVal = nameValues.get(position).getMrpValue().length() > 0 ? nameValues.get(position).getMrpValue() : nameValues.get(position).getAmount();
            viewHolder.mrpVal.setText(defaultFormat.format(Double.parseDouble(mrpVal)));
        } else {
            viewHolder.mrpTitle.setVisibility(View.INVISIBLE);
            viewHolder.mrpVal.setVisibility(View.INVISIBLE);
            viewHolder.txt_slsss.setVisibility(View.INVISIBLE);
        }
        if (!Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            viewHolder.total.setVisibility(View.INVISIBLE);
        } else {
            viewHolder.total.setText(calculateTotal(nameValues.get(position), position));
        }

        if (transType.equalsIgnoreCase("PB")) {
            viewHolder.mrpTitle.setText("Purchase Rate : ");
        }

        return convertView;
    }

    public String calculateTotal(ProductMasterDetails currentObj, int position) {
        double amount = 0;
        double productamount = 0;
        if (Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {
            amount = Double.parseDouble(currentObj.getAmount());

        } else if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            double qty = Double.parseDouble(currentObj.getQty());
            String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                    .getMrpValue() : currentObj.getAmount();
            double mrp = Double.parseDouble(mrpVal);
            double discount = Double.parseDouble(currentObj.getTradeDiscnt());
            double premium = 0;
            if (currentObj.getPremium().trim().length() > 0) {
                premium = Double.parseDouble(currentObj
                        .getPremium());
            }
            if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
            {
                productamount = (qty * (mrp - discount)) + (premium * qty);
                amount = amount
                        + productamount;
            }
            else
            {
                productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                amount = amount
                        + productamount;
            }


            //amount = amount + ((mrp * qty) - (mrp * qty * discount / 100)) + (premium*qty);
            if (currentObj.getFreight().length() > 0) {
                double freightAmt = Double.parseDouble(currentObj.getFreight());
                amount = amount + freightAmt;
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount"))

            {
                double qty = Double.parseDouble(currentObj.getQty());
                String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                        .getMrpValue() : currentObj.getAmount();
                double mrp = Double.parseDouble(mrpVal);
                double vat = Double.parseDouble(currentObj.getVat());
                amount = amount + ((mrp * qty) + vat);
                if (currentObj.getFreight().length() > 0) {
                    double freightAmt = Double.parseDouble(currentObj
                            .getFreight());
                    amount = amount + freightAmt;
                }
            } else {
                double qty = Double.parseDouble(currentObj.getQty());
                String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                        .getMrpValue() : currentObj.getAmount();
                double mrp = Double.parseDouble(mrpVal);
                double vat = Double.parseDouble(currentObj.getVat());
                amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                if (currentObj.getFreight().length() > 0) {
                    double freightAmt = Double.parseDouble(currentObj
                            .getFreight());
                    amount = amount + freightAmt;
                }
            }
        }
//		amount = calculateSchemesTotalAmount(position, Double.parseDouble(currentObj.getQty()), amount);
        return (defaultFormat.format(amount));
    }

    public String getPackString(ProductMasterDetails obj) {
        String uom1Str = "", uom2Str = "", packStr = "";
        uom1Str = obj.getQty() + obj.getUom1();
        uom2Str = defaultFormat.format(Double.parseDouble(obj.getQty()) * Double.parseDouble(obj.getConversionFactor())) + obj.getUom2();
        packStr = defaultFormat.format(Double.parseDouble(obj.getQty()) * Double.parseDouble(obj.getPackSize())) + " Pack";
        return "Qty : " + uom1Str + " ~ " + uom2Str + " ~ " + packStr;
    }

    public class ViewHolder {
        TextView slNo, prodName, qty, mrpTitle, mrpVal, discount, total, txt_slsss, txtTDVatTitle, txtPack, txtFreight;
        LinearLayout discountLayout;
        LinearLayout parentLayout, qtyLayout;
    }
}