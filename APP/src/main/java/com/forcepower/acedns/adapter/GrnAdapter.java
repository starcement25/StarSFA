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

import com.forcepower.acedns.activity.GrnActivity;


public class GrnAdapter extends BaseAdapter
{
	private Activity context;


	public GrnAdapter(Activity context)
	{
		this.context = context;
	}

	@Override
	public int getCount()
	{
		return GrnActivity.grnMasterSkuListItemGlobal.size();
	}

	@Override
	public commonDatabaseHelper getItem(int position)
	{

		return GrnActivity.grnMasterSkuListItemGlobal.get(position);
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
			ViewHolder holder = null;
			LayoutInflater mInflater = (LayoutInflater) context
					.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

//			if (convertView == null)
//			{
				convertView = mInflater.inflate(R.layout.list_item_grn, null);
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
//			}
//			else
//			{
//				holder = (ViewHolder) convertView.getTag();
//			}
			CharSequence invisibleProdCodeText = holder.invisibleCode.getText();

			holder.tv_sl_no.setText((position + 1) + ". "); // SL
			holder.tv_prod_name.setText(GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem3()); //prod_desc
//			holder.et_qty.setText(""); //et_qty
			holder.tv_dispatched_qty.setText(GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem5()); //Dispatch_qty
//			holder.tv_do_qty_final.setText(grnMasterSkuListItemGlobal.get(position).getItem2()); //DO_qty
//			holder.tv_do_qty_min.setText(grnMasterSkuListItemGlobal.get(position).getItem3()); //DO_qty_min
//			holder.tv_prod_uom1.setText(grnMasterSkuListItemGlobal.get(position).getItem4()); //uom1
			if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
				holder.et_qty.addTextChangedListener(new GenericTextWatcherQty(position));

			}
			String prodCode = GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem0();

			holder.invisibleCode.setText(prodCode);
			holder.iv_info.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View view) {
					details_dialog(GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem3(), GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem4(), GrnActivity.grnMasterSkuListItemGlobal.get(position).getItem5());
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
			GrnActivity.grnMasterSkuListItemGlobal.get(pos).setItem7(text);
		}
	}
	public class ViewHolder
	{
		TextView tv_sl_no, tv_prod_name, tv_dispatched_qty,tv_do_qty_final, invisibleCode,tv_do_qty_min;
		ImageView iv_prod_add,iv_info;
		EditText et_qty;
	}
	private void details_dialog(String prod_desc,String DO_qty, String dispQty)
	{
		try
		{
			AlertDialog.Builder builder = new AlertDialog.Builder(context);
			builder.setMessage("SKU- "+prod_desc+"\nDO Qty- "+ DO_qty +"\nDisp. Qty- "+dispQty);

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
