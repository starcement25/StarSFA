package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.math.BigDecimal;
import java.util.ArrayList;

import static java.lang.Double.parseDouble;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;


public class BargainConfirmAdapter extends ArrayAdapter<SaudaDetails> {

    private final Context mContext;
    private final ArrayList<SaudaDetails> mSaudaDetailsList;
    private final int resourceId;
    int editablePosition = -1;
    private ViewHolder viewHolder;
    private String mSaudaType = "";


    public BargainConfirmAdapter(Context context, int resourceId, ArrayList<SaudaDetails> saudaDetailsList, String saudatype) {
        super(context, resourceId, saudaDetailsList);
        this.mContext = context;
        this.mSaudaDetailsList = saudaDetailsList;
        this.resourceId = resourceId;
        this.mSaudaType = saudatype;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.ParentLayout = (LinearLayout) convertView.findViewById(R.id.saudaConfirmListParentLayout);
            viewHolder.liquidationDiscountLayout = (LinearLayout) convertView.findViewById(R.id.liquidationDiscountLayout);

            viewHolder.TextViewProductName = (TextView) convertView.findViewById(R.id.textViewProductName);
            viewHolder.TextViewQuantity = (TextView) convertView.findViewById(R.id.textViewQuantityValue);
            viewHolder.TextViewSaleRate = (TextView) convertView.findViewById(R.id.textViewSaleRateValue);

            viewHolder.mTvLiquidationDiscount = (TextView) convertView.findViewById(R.id.mTvLiquidationDiscount);

            viewHolder.TextViewDiscountTitle = (TextView) convertView.findViewById(R.id.textViewTD);
            viewHolder.TextViewDiscount = (TextView) convertView.findViewById(R.id.textViewTDValue);
            viewHolder.TextViewDiscountTitle.setVisibility(View.GONE);
            viewHolder.TextViewDiscount.setVisibility(View.GONE);
            viewHolder.TextViewFreightChargeTitle = (TextView) convertView.findViewById(R.id.textViewFreightCharge);
            viewHolder.TextViewFreightCharge = (TextView) convertView.findViewById(R.id.textViewFreightChargeValue);
            viewHolder.txt_total = (TextView) convertView.findViewById(R.id.txt_total);
            viewHolder.txtTDVatTitle = (TextView) convertView.findViewById(R.id.txt_vat);
            viewHolder.TextViewTotal = (TextView) convertView.findViewById(R.id.textViewTotalValue);
            viewHolder.gstPercenrtageTV = (TextView) convertView.findViewById(R.id.gstPercenrtageTV);
            viewHolder.vat_lay =  convertView.findViewById(R.id.vat_lay);
            if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes"))
            {
                viewHolder.vat_lay.setVisibility(View.VISIBLE);
            }
            else
            {
                viewHolder.vat_lay.setVisibility(View.GONE);
            }
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (position % 2 == 0) {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            viewHolder.ParentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }
        if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(Constants.selectedVerticalOfUser) && Utils.isNumeric(maxLiquidationDiscountForCurrentCustomer) && parseDouble(maxLiquidationDiscountForCurrentCustomer) > 0) {
            viewHolder.liquidationDiscountLayout.setVisibility(View.VISIBLE);
            viewHolder.mTvLiquidationDiscount.setText(mSaudaDetailsList.get(position).getLiquidTD());
        } else {
            viewHolder.liquidationDiscountLayout.setVisibility(View.GONE);
        }
        viewHolder.TextViewProductName.setText(mSaudaDetailsList.get(position).getProductName());
        viewHolder.TextViewQuantity.setText(mSaudaDetailsList.get(position).getQuantity());
        double saleRate = parseDouble(mSaudaDetailsList.get(position).getSaleRate());
        double secondaryFreight = parseDouble(mSaudaDetailsList.get(position).getFreightCharge());
        double additionalPremium = parseDouble(mSaudaDetailsList.get(position).getadditionalPremium());
        double additionalTD = parseDouble(mSaudaDetailsList.get(position).getadditionalTD());
        double brokerageCost = parseDouble(mSaudaDetailsList.get(position).getBrokarageCost());
        double brokerageCostSS = parseDouble(mSaudaDetailsList.get(position).getBrokarageCostSS());
        double td = parseDouble(mSaudaDetailsList.get(position).getTD());
        double qty = parseDouble(mSaudaDetailsList.get(position).getQuantity());
        saleRate = saleRate + secondaryFreight+brokerageCost+brokerageCostSS+td+additionalPremium-additionalTD ;
        Double amountBeforeVat=saleRate*qty;
        BigDecimal aaa = new BigDecimal(saleRate);
        aaa = Utils.round(aaa, 2);
        String srate = defaultFormatWithComma.format(aaa);
        viewHolder.TextViewSaleRate.setText(srate);
        if (mSaudaType.equalsIgnoreCase("FOR")) {
            viewHolder.TextViewFreightCharge.setText(defaultFormatWithComma.format(parseDouble(mSaudaDetailsList.get(position).getFreightCharge())));
            if (Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
                viewHolder.TextViewFreightChargeTitle.setVisibility(View.GONE);
                viewHolder.TextViewFreightCharge.setVisibility(View.GONE);
            }
        } else {
            viewHolder.TextViewFreightChargeTitle.setVisibility(View.GONE);
            viewHolder.TextViewFreightCharge.setVisibility(View.GONE);
        }

        Double vatAmount=0.00,totalAmount=0.00;
        double vatRateInDouble =0.00;

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes"))
        {
            String vatRate = mSaudaDetailsList.get(position).getVat();
            if(Utils.isNumeric(vatRate) && Double.parseDouble(vatRate)>0)
            {
                vatRateInDouble=Double.parseDouble(vatRate);
            }

            if(vatRateInDouble>0)
            {

                vatAmount=(amountBeforeVat*vatRateInDouble)/100;
            }

            viewHolder.txtTDVatTitle.setText(defaultFormat.format(vatAmount));
            viewHolder.gstPercenrtageTV.setText("GST: "+vatRateInDouble+"%");

        }

        totalAmount=(amountBeforeVat+vatAmount);
        viewHolder.txt_total.setText(defaultFormatWithComma.format(totalAmount));
        BigDecimal aaa2 = new BigDecimal(totalAmount);
        aaa2 = Utils.round(aaa2, 2);
        viewHolder.TextViewTotal.setText(aaa2 + "");
        return convertView;
    }

    public class ViewHolder {
        TextView TextViewProductName, TextViewQuantity, TextViewSaleRate, TextViewDiscount, TextViewFreightCharge, TextViewTotal;
        TextView TextViewFreightChargeTitle, TextViewDiscountTitle, mTvLiquidationDiscount,gstPercenrtageTV,txtTDVatTitle,txt_total;
        LinearLayout ParentLayout, liquidationDiscountLayout,vat_lay;
    }

}