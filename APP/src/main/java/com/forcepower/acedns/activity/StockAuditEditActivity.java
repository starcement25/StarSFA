package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditEditTransactionTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ListAdapterGrn;
import com.forcepower.acedns.adapter.StockAuditEditAdapter;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;


public class StockAuditEditActivity extends AppCompatActivity {

    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    Context mContext;
    TextView tv_do_no,textViewHeader;
    String chosenOrderNo ="",chosenOrderCustomerCode="";
    public static ArrayList<commonDatabaseHelper> grnMasterSkuListItemGlobal = new ArrayList<>();
    ArrayList<commonDatabaseHelper> grnMasterListItemGlobal = new ArrayList<>();
//    ArrayList<commonDatabaseHelper> grnmasterListItemDefault = new ArrayList<>();
    ListView lv_grn;
    ProgressDialog loader;
    AceDnsTransactionDatabase dataHelperObj;
    public static ImageView mImageViewHeaderLogo = null;
    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_edit);
        RegisterActivities.registerActivity(this);

        mContext = StockAuditEditActivity.this;
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        tv_do_no =  findViewById(R.id.tv_do_no);
        textViewHeader =  findViewById(R.id.textViewHeader);
        lv_grn = findViewById(R.id.lv_loading);
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        textViewHeader.setText(getString(R.string.select_item_to_edit));
        txtVersion.setText("Ver~" + Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                StockAuditEditActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:

                                grnMasterListItemGlobal = mAceDnsDatabase.getStockAuditListForToday();

                                if(grnMasterListItemGlobal.size() == 1)
                                {
                                    setDoValueShowSkuList(0);
                                }
                                else if(grnMasterListItemGlobal.size()==0){
                                    Utils.showToast(mContext,"No item to Edit.");
                                    finish();
                                }

                                break;
                        }
                    }
                });
            }
        };


        try {
            PrepareCustomerData(1);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    public void backButtonClicked(View v){
        finish();
    }
    public void setDoValueShowSkuList(int pos) {
//        Utils.writeDebugData("inside setDoValueShowSkuList-",mContext);
        commonDatabaseHelper commonDatabaseHelper = grnMasterListItemGlobal.get(pos);
        chosenOrderNo = commonDatabaseHelper.getItem1();
//        Utils.writeDebugData("chosenOrderNot-"+chosenOrderNo,mContext);
        chosenOrderCustomerCode = commonDatabaseHelper.getItem2();
        tv_do_no.setText(commonDatabaseHelper.getItem0());
        grnMasterSkuListItemGlobal = mAceDnsDatabase.getStockAuditListForTodayDetails(chosenOrderNo);
//        Utils.writeDebugData("grnMasterSkuListItemGlobal-"+grnMasterSkuListItemGlobal.size(),mContext);
        StockAuditEditAdapter lAdapter = new StockAuditEditAdapter((Activity)mContext);
        lv_grn.setAdapter(lAdapter);
    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
//                        new commonAsyncTaskMaster(mContext, "grn");
                        break;
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (mImageViewHeaderLogo != null) {
            if (Constants.logoBmp != null) {
                mImageViewHeaderLogo.setVisibility(View.VISIBLE);
                mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
            } else {
                mImageViewHeaderLogo.setVisibility(View.GONE);
            }
        }
    }

    public void do_number_list(View view)
    {
        if(grnMasterListItemGlobal.size() > 1)
        {
            openDialog(tv_do_no);
        }
    }
    public void submitGrn(View view)
    {
        if(!HTTPUtils.isConnectionPossible(mContext)){
            Utils.showToast(mContext,getString(R.string.need_internet));
            return;
        }
        Boolean AtLeastOneProperInputGiven=false;

        for(int i=0;i<grnMasterSkuListItemGlobal.size();i++){
            commonDatabaseHelper currentItem=grnMasterSkuListItemGlobal.get(i);
            String inputGiven= currentItem.getItem7();
//            if(Utils.isNumeric(inputGiven) && Double.parseDouble(inputGiven)>0){
            if(Utils.isNumeric(inputGiven) ){
                AtLeastOneProperInputGiven=true;
            }
        }
        if(!AtLeastOneProperInputGiven){
            Utils.showToast(mContext,"At least one input must be given before submitting.");
            return;
        }
        new GPSTracker(mContext);
        SaveDataToDatabase();
//        showRemarksDialog();
    }

    public void openDialog(final TextView textView)
    {
        try
        {
            textView.setText("");
            final PopupWindow popup = new PopupWindow(this);
            View layout = getLayoutInflater().inflate(R.layout.list_item_dialog, null);
            popup.setContentView(layout);

            // Set content width and height
            popup.setHeight(WindowManager.LayoutParams.WRAP_CONTENT);
            popup.setWidth(textView.getWidth());

            // Closes the popup window when touch outside of it - when looses focus
            popup.setOutsideTouchable(true);
            popup.setFocusable(true);
            popup.setBackgroundDrawable(new BitmapDrawable());

            popup.showAsDropDown(textView, 0, 0);



            final ListAdapterGrn mAdapter = new ListAdapterGrn(this, grnMasterListItemGlobal);
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(mAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    try
                    {
                        popup.dismiss();
                        setDoValueShowSkuList(i);
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void showRemarksDialog()
    {
        final Dialog checkoutDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.remarks_material);
        checkoutDialog.setCancelable(false);
        TextView title = (TextView) checkoutDialog.findViewById(R.id.title);
        Button submit = (Button) checkoutDialog.findViewById(R.id.btn_submit);
        title.setText("Remarks");

        final ImageView back =  checkoutDialog.findViewById(R.id.back);
        back.setOnClickListener((View.OnClickListener) v -> {
            checkoutDialog.cancel();

        });
        submit.setOnClickListener((View.OnClickListener) v -> {

            final EditText remarks_box =  checkoutDialog.findViewById(R.id.remark_box);
            String remarks = remarks_box.getText().toString().trim();
            Constants.bargainNarration=remarks;
            checkoutDialog.cancel();

            if (HTTPUtils.isConnectionPossible(mContext)) {
                new GPSTracker(mContext);
                SaveDataToDatabase();
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }

        });
        checkoutDialog.show();
    }

    public void SaveDataToDatabase() {
        dataHelperObj.beginTransaction();
        Boolean  isSuccessUpdateOrderDetailsTable ;

            isSuccessUpdateOrderDetailsTable = dataHelperObj.UpdateStockDetails(chosenOrderNo);
        if (isSuccessUpdateOrderDetailsTable) {
            new TRANS_SubmitStockAuditEditTransactionTask(mContext, true,chosenOrderCustomerCode,chosenOrderNo,dataHelperObj).execute();
        } else {
            dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
            Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please contact admin!");
        }
    }
}
