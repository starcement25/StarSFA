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
import java.text.DecimalFormat;
import java.util.ArrayList;

import static java.lang.Double.parseDouble;
import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;


public class SaudaConfirmAdapter extends ArrayAdapter<SaudaDetails> {

    private final Context mContext;
    private final ArrayList<SaudaDetails> mSaudaDetailsList;
    private final int resourceId;
    int editablePosition = -1;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    private ViewHolder viewHolder;
    private String mSaudaType = "";


    public SaudaConfirmAdapter(Context context, int resourceId, ArrayList<SaudaDetails> saudaDetailsList, String saudatype) {
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

            viewHolder.TextViewTotal = (TextView) convertView.findViewById(R.id.textViewTotalValue);

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
        double basicRate = parseDouble(mSaudaDetailsList.get(position).getSaleRate());
        double secondaryFreight = parseDouble(mSaudaDetailsList.get(position).getFreightCharge());
        double primaryFreight = parseDouble(mSaudaDetailsList.get(position).getPrimaryFreight());
        double depotCost = parseDouble(mSaudaDetailsList.get(position).getDepotCost());
        double honeyCombCost = parseDouble(mSaudaDetailsList.get(position).getHoneyCombCost());
        double marginCost = parseDouble(mSaudaDetailsList.get(position).getMarginCost());
//		double saleRate= basicRate+secondaryFreight+primaryFreight+depotCost;
        double saleRate = 0.0;
        if (mSaudaType.equalsIgnoreCase("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
            saleRate = basicRate + secondaryFreight + primaryFreight + depotCost + honeyCombCost + marginCost + parseDouble(BrokerageCost);
        } else {
            saleRate = basicRate + primaryFreight + depotCost + honeyCombCost + marginCost + parseDouble(BrokerageCost);
        }
//		double saleRate= basicRate+primaryFreight+depotCost;


        if (mSaudaDetailsList.get(position).getTDorPremiumCheck().equalsIgnoreCase("no")) {
            viewHolder.TextViewDiscountTitle.setText("Premium");
            double premium = parseDouble(mSaudaDetailsList.get(position).getPremium());
            viewHolder.TextViewDiscount.setText(defaultFormat.format(premium));
            saleRate = saleRate + premium;
        } else {
            viewHolder.TextViewDiscountTitle.setText("Trade Discount");
            double TD = parseDouble(mSaudaDetailsList.get(position).getTD());
            viewHolder.TextViewDiscount.setText(defaultFormat.format(TD));
            saleRate = saleRate - TD;
        }
        BigDecimal aaa = new BigDecimal(saleRate);
        aaa = Utils.round(aaa, 2);
        String srate = defaultFormat.format(aaa);
        viewHolder.TextViewSaleRate.setText(srate);
        if (mSaudaType.equalsIgnoreCase("FOR")) {
            viewHolder.TextViewFreightCharge.setText(defaultFormat.format(parseDouble(mSaudaDetailsList.get(position).getFreightCharge())));
            if (Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
                viewHolder.TextViewFreightChargeTitle.setVisibility(View.GONE);
                viewHolder.TextViewFreightCharge.setVisibility(View.GONE);
            }
        } else {
            viewHolder.TextViewFreightChargeTitle.setVisibility(View.GONE);
            viewHolder.TextViewFreightCharge.setVisibility(View.GONE);
        }
        String amount = mSaudaDetailsList.get(position).getAmount();
        double amnt = Double.parseDouble(amount);

        BigDecimal aaa2 = new BigDecimal(amnt);
        aaa2 = Utils.round(aaa2, 2);
        viewHolder.TextViewTotal.setText(aaa2 + "");
        return convertView;
    }

    public class ViewHolder {
        TextView TextViewProductName, TextViewQuantity, TextViewSaleRate, TextViewDiscount, TextViewFreightCharge, TextViewTotal;
        TextView TextViewFreightChargeTitle, TextViewDiscountTitle, mTvLiquidationDiscount;
        LinearLayout ParentLayout, liquidationDiscountLayout;
    }

}