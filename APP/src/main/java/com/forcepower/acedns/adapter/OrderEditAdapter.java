package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.DialogInterface;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.appcompat.app.AlertDialog;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;

import com.forcepower.acedns.activity.OrderEditActivity;


public class OrderEditAdapter extends BaseAdapter
{
	private Activity context;


	public OrderEditAdapter(Activity context)
	{
		this.context = context;
	}

	@Override
	public int getCount()
	{
//		Utils.writeDebugData("grnMasterSkuListItemGlobal.size()-"+grnMasterSkuListItemGlobal.size(),context);
		return OrderEditActivity.grnMasterSkuListItemGlobal.size();

	}

	@Override
	public commonDatabaseHelper getItem(int position)
	{

		return OrderEditActivity.grnMasterSkuListItemGlobal.get(position);
	}

	@Override
	public long getItemId(int position) {

		return 0;
	}

	@Override
	public View getView(final int position, View convertView, ViewGroup parent)
	{
		try
		{
//			Utils.writeDebugData("position-"+position,context);
			ViewHolder holder = null;
			LayoutInflater mInflater = (LayoutInflater) context
					.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

//			if (convertView == null)
//			{
				convertView = mInflater.inflate(R.layout.list_item_order_edit, null);
				holder = new ViewHolder();

				holder.tv_sl_no = (TextView) convertView.findViewById(R.id.tv_sl_no);
				holder.tv_prod_name = (TextView) convertView.findViewById(R.id.tv_prod_name);
				holder.tv_dispatched_qty = (TextView) convertView.findViewById(R.id.tv_dispatched_qty);
				holder.tv_do_qty_final = (TextView) convertView.findViewById(R.id.tv_do_qty_final);
				holder.tv_do_qty_min = (TextView) convertView.findViewById(R.id.tv_do_qty_min);
				holder.invisibleCode = (TextView) convertView.findViewById(R.id.tv_prod_uom1);
				holder.et_qty = (EditText) convertView.findViewById(R.id.et_qty);
				holder.iv_prod_add = (ImageView) convertView.findViewById(R.id.iv_prod_add);
				holder.iv_info = (ImageView) convertView.findViewById(R.id.iv_info);
				convertView.setTag(holder);
//				holder.iv_info.setVisibility(View.GONE);
//			Utils.writeDebugData("position2-",context);
			CharSequence invisibleProdCodeText = holder.invisibleCode.getText();

			holder.tv_sl_no.setText((position + 1) + ". "); // SL
			holder.tv_prod_name.setText(OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem1()); //prod_desc
//			Utils.writeDebugData("position3-",context);
			holder.tv_dispatched_qty.setText(OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem2()); //Dispatch_qty

			if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
				holder.et_qty.addTextChangedListener(new GenericTextWatcherQty(position));

			}
			String prodCode = OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem0();
//			Utils.writeDebugData("position4-",context);
			holder.invisibleCode.setText(prodCode);
			holder.iv_info.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View view) {
					if(Constants.weightage.matches("yes")) {
						details_dialog(OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem1(), OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem2(), OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem3(),"");
					}else{
						details_dialog(OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem1(), OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem2(), OrderEditActivity.grnMasterSkuListItemGlobal.get(position).getItem3(),"");
					}
				}
			});
			holder.iv_prod_add.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View view) {
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
//			Utils.writeDebugData("e.getMessage()-"+e.getMessage(),context);
//			print_Log_d("CRASH_100", e.toString());
		}

		return convertView;
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
			OrderEditActivity.grnMasterSkuListItemGlobal.get(pos).setItem7(text);
		}
	}
	public class ViewHolder
	{
		TextView tv_sl_no, tv_prod_name, tv_dispatched_qty,tv_do_qty_final, invisibleCode,tv_do_qty_min;
		ImageView iv_prod_add,iv_info;
		EditText et_qty;
	}
	private void details_dialog(String prod_desc, String qty, String rate,String WT)
	{
		try
		{
			AlertDialog.Builder builder = new AlertDialog.Builder(context);
			Double amount= Double.parseDouble(qty)*Double.parseDouble(rate);
			if(Constants.weightage.matches("yes")) {
				builder.setMessage("Prod- " + prod_desc + "\nQty- " + qty + "\nRate- " + rate + "\nAmount- " + Constants.defaultFormat.format(amount));
			}else{
				builder.setMessage("Prod- " + prod_desc + "\nQty- " + qty + "\nRate- " + rate + "\nAmount- " + Constants.defaultFormat.format(amount));
			}
			builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface dialog, int which) {

				}
			});
			AlertDialog dialog = builder.create();
			dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			dialog.setCancelable(true);
			dialog.setCanceledOnTouchOutside(true);
			dialog.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

}
