package com.forcepower.acedns.adapter;

import android.content.Context;
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
import com.forcepower.acedns.util.Utils;
import java.text.DecimalFormat;
import java.util.ArrayList;

public class DoConfirmAdapter extends ArrayAdapter<ProductMasterDetails> {

    private final Context context;
    private final ArrayList<ProductMasterDetails> nameValues;
    private final int resourceId;
    TextView txtTotal;
    String td = "";
    int editablePosition = -1;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String transType = "";
    private ViewHolder viewHolder;

    public DoConfirmAdapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues, TextView txtTotal, String td, String transType) {
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
        viewHolder.txtTDVatTitle = (TextView) convertView.findViewById(R.id.txt_vat);
        viewHolder.txt_total = (TextView) convertView.findViewById(R.id.txt_total);
        viewHolder.txtPack = (TextView) convertView.findViewById(R.id.txt_pack);
        viewHolder.txtFreight = (TextView) convertView.findViewById(R.id.txt_freight);
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
            uom = nameValues.get(position).getUom1();
            if(uom.equalsIgnoreCase("loose"))
            {
                uom="MT";
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

        double saleRate = Double.parseDouble(nameValues.get(position).getMrpValue());
        viewHolder.mrpVal.setText(defaultFormat.format(saleRate));

        Double vatAmount=0.00,totalAmount=0.00;
        double vatRateInDouble =0.00;

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes"))
        {
            String vatRate = nameValues.get(position).getVat();
            if(Utils.isNumeric(vatRate) && Double.parseDouble(vatRate)>0)
            {
                vatRateInDouble=Double.parseDouble(vatRate);
            }

            if(vatRateInDouble>0)
            {
                vatAmount=(saleRate*vatRateInDouble)/100;
            }
            viewHolder.txtTDVatTitle.setText(defaultFormat.format(vatAmount));
            viewHolder.gstPercenrtageTV.setText("GST: "+vatRateInDouble+"%");

        }
        totalAmount=(saleRate+vatAmount)*Double.parseDouble(nameValues.get(position).getQty());
        viewHolder.txt_total.setText(defaultFormat.format(totalAmount));
        return convertView;
    }

    public String getPackString(ProductMasterDetails obj) {
        String uom1Str = "", uom2Str = "", packStr = "";
        uom1Str = obj.getQty() + obj.getUom1();
        uom2Str = defaultFormat.format(Double.parseDouble(obj.getQty()) * Double.parseDouble(obj.getConversionFactor())) + obj.getUom2();
        packStr = defaultFormat.format(Double.parseDouble(obj.getQty()) * Double.parseDouble(obj.getPackSize())) + " Pack";
        return "Qty : " + uom1Str + " ~ " + uom2Str + " ~ " + packStr;
    }

    public class ViewHolder {
        TextView slNo, prodName, qty, mrpTitle, mrpVal, discount, total, txt_slsss, txtTDVatTitle, txtPack, txtFreight,txt_total,gstPercenrtageTV;
        LinearLayout discountLayout;
        LinearLayout  qtyLayout,vat_lay;
    }
}