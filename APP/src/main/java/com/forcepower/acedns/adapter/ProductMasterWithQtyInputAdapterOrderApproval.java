package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.database.AceDnsDatabase;
import java.util.ArrayList;
import static android.view.View.GONE;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

import com.forcepower.acedns.activity.OrderApprovalActivity;

public class ProductMasterWithQtyInputAdapterOrderApproval extends ArrayAdapter<commonDatabaseHelper> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;

    public ProductMasterWithQtyInputAdapterOrderApproval(Context context, int resourceId) {
        super(context, resourceId, OrderApprovalActivity.ChosenOrderList);
        this.context = context;
        activity = (Activity) context;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = OrderApprovalActivity.ChosenOrderList.size();


    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.qtyET =  convertView.findViewById(R.id.etProdQty);
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.addtocartIV = (ImageView) convertView.findViewById(R.id.addtocartIV);
        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        if(!OrderApprovalActivity.status.equalsIgnoreCase("modify"))
        {
            viewHolder.qtyET.setFocusable(false);
            viewHolder.addtocartIV.setVisibility(GONE);
        }
        else
        {
            viewHolder.qtyET.setFocusable(true);
            viewHolder.addtocartIV.setVisibility(View.VISIBLE);
        }


        convertView.setTag(viewHolder);

        String desc = OrderApprovalActivity.ChosenOrderList.get(position).getItem9();
        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();

        if (invisibleProdCodeText == null || invisibleProdCodeText.equals(""))
        {
            viewHolder.addtocartIV.setOnClickListener(new GenericItemCLickListener(position,viewHolder.txtViewProductDesc));
        }
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals(""))
        {
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            viewHolder.qtyET.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.qtyET));
        }
        String prodCode = OrderApprovalActivity.ChosenOrderList.get(position).getItem7();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = OrderApprovalActivity.ChosenOrderList.get(position).getchangedqty();
        if (!currentqty.matches("NA"))
            viewHolder.qtyET.setText(currentqty);
        return convertView;
    }

    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode;
        EditText qtyET;
        ImageView addtocartIV;
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
            OrderApprovalActivity.ChosenOrderList.get(pos).setchangedqty(text);
        }
    }


    private class GenericItemCLickListener implements View.OnClickListener {
        private int pos;
        private TextView tv;

        private GenericItemCLickListener(int pos,TextView tv) {
            this.pos = pos;
            this.tv = tv;
        }

        @Override
        public void onClick(View view) 
        {
            if(OrderApprovalActivity.status.equalsIgnoreCase("modify"))
            {
                showMasterListDialog(pos,tv);
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
    
    public void showMasterListDialog(int pos,TextView tv) 
    {
        Dialog masterDialog;
        ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getProductMasterListForOrderApproval();
        if (productMasterList.size() > 0)
        {
            ArrayList<ProductMasterDetails> tempProductList=new ArrayList<>(productMasterList);
            ProductMasterAdapter  prodAdapter = new ProductMasterAdapter(context, R.layout.product_list_child_alternate, tempProductList);

            masterDialog = new Dialog(context, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            ImageView imageView1 =  masterDialog.findViewById(R.id.imageView1);
            imageView1.setClickable(true);
            imageView1.setImageResource(R.drawable.back_bg);
            imageView1.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view)
                {
                    masterDialog.cancel();
                }
            });
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher()
            {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s)
                {
                    String str = s.toString();
                    tempProductList.removeAll(tempProductList);

                    int size = productMasterList.size();
                    for (int ii = 0; ii < size; ii++)
                    {
                        if (productMasterList.get(ii).getDesc().length()>1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase()))
                        {
                            tempProductList.add(productMasterList.get(ii));
                        }

                        prodAdapter.notifyDataSetChanged();
                    }
                }
            });
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
//                    if (lastStr.length() > str.length()) {
//                        reInitialiseProductList();
//                    }
//                    lastStr = str;
//                    filterProductArray(str.length(), str);
//                    prodAdapter.notifyDataSetChanged();
                }
            });
            prodQtyRateListView = (ListView) masterDialog.findViewById(R.id.list);


                prodQtyRateListView.setAdapter(prodAdapter);

            prodQtyRateListView.setOnItemClickListener(new AdapterView.OnItemClickListener()
            {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3)
                {

                    String prodCode = tempProductList.get(arg2).getDnsProdCode();
                    String prodDEsc = tempProductList.get(arg2).getDesc();
                    tv.setText(prodDEsc);
                    OrderApprovalActivity.ChosenOrderList.get(pos).setchangedDnsProdCode(prodCode);
                    OrderApprovalActivity.ChosenOrderList.get(pos).setItem9(prodDEsc);
                    masterDialog.cancel();

                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setVisibility(GONE);
//            btnCancel.setOnClickListener(new OnClickListener() {
//                @Override
//                public void onClick(View v) {
//                    masterDialog.cancel();
//                }
//            });
            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            btn_addToCart.setVisibility(GONE);

            masterDialog.show();
        }
        else
        {
            Toast.makeText(context, "No product found.", Toast.LENGTH_LONG).show();
        }
    }

}