package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.TentProductList;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;


public class TentExixtingProductListAdapter extends BaseAdapter
{

	private Activity context;
	private ArrayList<TentProductList> menu_item_list = new ArrayList<>();

	public TentExixtingProductListAdapter(final Activity context, final ArrayList<TentProductList> menu_item_list_)
	{
		this.context = context;
		this.menu_item_list = menu_item_list_;
	}

	@Override
	public int getCount()
	{
		return menu_item_list.size();
	}

	@Override
	public TentProductList getItem(int position)
	{
		return menu_item_list.get(position);
	}

	@Override
	public long getItemId(int position) {

		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent)
	{
		try
		{
			ViewHolder holder ;

			if (convertView == null)
			{
				final LayoutInflater mInflater = (LayoutInflater) context
						.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

				convertView = mInflater.inflate(R.layout.existing_product_tent_list_child_report, null);
				holder = new ViewHolder();

				holder.tv_s_timing = (TextView) convertView.findViewById(R.id.list_details);
				holder.tvLifeProduct = (TextView) convertView.findViewById(R.id.tvLifeProduct);
				holder.tvBrand = (TextView) convertView.findViewById(R.id.tvBrand);
				holder.etBrand = (EditText) convertView.findViewById(R.id.etBrand);
				holder.etLifeProduct = (EditText) convertView.findViewById(R.id.etLifeProduct);


				holder.iv_check_uncheck = (ImageView) convertView.findViewById(R.id.iv_check_uncheck);


				convertView.setTag(holder);
			}
			else
			{
				holder = (ViewHolder) convertView.getTag();
			}

			holder.tv_s_timing.setText(menu_item_list.get(position).getDate());

			/*if(menu_item_list.get(position).getDate().toString().matches("No Product")){
				holder.etBrand.setVisibility(View.GONE);
				holder.etLifeProduct.setVisibility(View.GONE);
				holder.tvLifeProduct.setVisibility(View.VISIBLE);
				holder.tvBrand.setVisibility(View.VISIBLE);

			}*/

			holder.iv_check_uncheck.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					/*if(menu_item_list.get(position).getDate().toString().matches("No Product")){
						*//*for(int i=0;i<=position;i++){
							menu_item_list.get(position).setTotal("0");
							notifyDataSetChanged();
						}*//*
						if (menu_item_list.get(position).getTotal().equalsIgnoreCase("0")) {
							holder.iv_check_uncheck.setImageResource(R.drawable.check);
							menu_item_list.get(position).setTotal("1");
							Constants.tentExistingProductList.get(position).setBrand("no");
							Constants.tentExistingProductList.get(position).setLife("no");

						} else {
							holder.iv_check_uncheck.setImageResource(R.drawable.uncheck);
							menu_item_list.get(position).setTotal("0");
							Constants.tentExistingProductList.get(position).setBrand("");
							Constants.tentExistingProductList.get(position).setLife("");
						}
					}else {*/
						if (holder.etBrand.getText().toString().isEmpty() || holder.etLifeProduct.getText().toString().isEmpty()) {
							Utils.showToast(context, "Enter Brand name Or Life Of Product");
						} else {
							if (menu_item_list.get(position).getTotal().equalsIgnoreCase("0")) {
								holder.iv_check_uncheck.setImageResource(R.drawable.check);
								menu_item_list.get(position).setTotal("1");
								Constants.tentExistingProductList.get(position).setBrand(holder.etBrand.getText().toString());
								Constants.tentExistingProductList.get(position).setLife(holder.etLifeProduct.getText().toString());

							} else {
								holder.iv_check_uncheck.setImageResource(R.drawable.uncheck);
								menu_item_list.get(position).setTotal("0");
								Constants.tentExistingProductList.get(position).setBrand("");
								Constants.tentExistingProductList.get(position).setLife("");
							}
						}
					//}
				}
			});

			holder.etLifeProduct.addTextChangedListener(new TextWatcher() {
				@Override
				public void beforeTextChanged(CharSequence s, int start, int count, int after) {

				}

				@Override
				public void onTextChanged(CharSequence s, int start, int before, int count) {
					//Utils.showToast(context, "Enter Brand name" + s);
					try {
						if (!holder.etBrand.getText().toString().isEmpty()) {
							if (holder.etLifeProduct.getText().toString().isEmpty()) {
								holder.iv_check_uncheck.setImageResource(R.drawable.uncheck);
								menu_item_list.get(position).setTotal("0");
							} else {
								holder.iv_check_uncheck.setImageResource(R.drawable.check);
								menu_item_list.get(position).setTotal("1");
								Constants.tentExistingProductList.get(position).setBrand(holder.etBrand.getText().toString());
								Constants.tentExistingProductList.get(position).setLife(holder.etLifeProduct.getText().toString());

							}
						} else {
							holder.etLifeProduct.setText("");
							Utils.showToast(context, "Enter Brand name");
						}
					}catch (Exception e){

					}
				}

				@Override
				public void afterTextChanged(Editable s) {

				}
			});

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return convertView;
	}

	public void setFilter(final ArrayList<TentProductList> menu_item_list_)
	{
		menu_item_list = new ArrayList<>();
		menu_item_list.addAll(menu_item_list_);
		notifyDataSetChanged();
	}

	public class ViewHolder
	{
		private TextView tv_s_timing,tvBrand,tvLifeProduct;
		private ImageView iv_check_uncheck;
		private EditText etBrand,etLifeProduct;

	}
}
