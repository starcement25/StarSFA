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
import androidx.appcompat.widget.Toolbar;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitGrnTransactionTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ListAdapterGrn;
import com.forcepower.acedns.adapter.GrnAdapter;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;


public class GrnActivity extends AppCompatActivity {

    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    Context mContext;
    TextView tv_do_no;
    String chosenDnsDoNo ="";
    public static ArrayList<commonDatabaseHelper> grnMasterSkuListItemGlobal = new ArrayList<>();
    ArrayList<commonDatabaseHelper> grnMasterListItemGlobal = new ArrayList<>();
    ArrayList<commonDatabaseHelper> grnmasterListItemDefault = new ArrayList<>();
    ListView lv_grn;
    ProgressDialog loader;
    AceDnsTransactionDatabase dataHelperObj;
    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_grn);
        RegisterActivities.registerActivity(this);
        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        mContext = GrnActivity.this;
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        tv_do_no =  findViewById(R.id.tv_do_no);
        lv_grn = findViewById(R.id.lv_loading);
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                GrnActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:

                                grnMasterListItemGlobal = mAceDnsDatabase.getDoListForGrn();
                                grnmasterListItemDefault =new ArrayList<>(grnMasterListItemGlobal);


                                if(grnMasterListItemGlobal.size() == 1)
                                {
                                    setDoValueShowSkuList(0);
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

    public void setDoValueShowSkuList(int pos) {
        commonDatabaseHelper commonDatabaseHelper = grnMasterListItemGlobal.get(pos);
        chosenDnsDoNo = commonDatabaseHelper.getItem0();
        tv_do_no.setText(chosenDnsDoNo);
        grnMasterSkuListItemGlobal = mAceDnsDatabase.getSkuListByDoNoForGrn(chosenDnsDoNo);
        GrnAdapter lAdapter = new GrnAdapter((Activity)mContext);
        lv_grn.setAdapter(lAdapter);
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
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
                        new commonAsyncTaskMaster(mContext, "grn");
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
        Boolean AtLeastOneProperInputGiven=false,noInputIsGraterThanAvailableQty=true;
        String msg="";
        for(int i=0;i<grnMasterSkuListItemGlobal.size();i++){
            commonDatabaseHelper currentItem=grnMasterSkuListItemGlobal.get(i);
            String inputGiven= currentItem.getItem7();
            if(Utils.isNumeric(inputGiven) && Double.parseDouble(inputGiven)>0){
                AtLeastOneProperInputGiven=true;
                if(Utils.isNumeric(currentItem.getItem5()) && Double.parseDouble(currentItem.getItem5())<Double.parseDouble(inputGiven)){
                    msg="Input can not be grater than "+currentItem.getItem5()+" for "+currentItem.getItem3()+".";
                    noInputIsGraterThanAvailableQty=false;
                    break;
                }
            }
        }
        if(!AtLeastOneProperInputGiven){
            Utils.showToast(mContext,"At least one input must be given before submitting.");
            return;
        }
        if(!noInputIsGraterThanAvailableQty){
            Utils.showToast(mContext,msg);
            return;
        }
        showRemarksDialog();

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
//                        et_bay_number.requestFocus();
                        popup.dismiss();
                        setDoValueShowSkuList(i);
//                        textView.setText(grnMasterListItemGlobal.get(i).getItem0());
//                        TextView tv_hidden_value_3 = view.findViewById(R.id.tv_hidden_value_3); //DO_no
//                        TextView tv_hidden_value_1 = view.findViewById(R.id.tv_hidden_value_1); //customer_code
//                        tv_do_no.setText(tv_hidden_value_3.getText().toString());

//                        last_selected_DO_no = tv_hidden_value_3.getText().toString().trim();

//                        onitemClick(last_selected_DO_no, tv_hidden_value_1.getText().toString());
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
            new GPSTracker(mContext);
            SaveDataToDatabase();

        });
        checkoutDialog.show();

    }

    public void SaveDataToDatabase() {
//        loader = new ProgressDialog(mContext);
//        loader.setMessage("Saving Data.Please wait..");
//        loader.show();
//        new Thread() {
//            public void run() {
                String timeStamp = "";
                timeStamp = Constants.dateString
                        + new SimpleDateFormat("HHmmss").format(Calendar
                        .getInstance().getTime());
                dataHelperObj.beginTransaction();
                Boolean isSuccessInsertToGrnTransaction, isSuccessInsertToLocationTable,isSuccessUpdateGrnMasterTable=false;
                isSuccessInsertToGrnTransaction = dataHelperObj.InsertToGrnTransaction(timeStamp);
                isSuccessInsertToLocationTable =dataHelperObj.insertToLocationTable1("GRN", timeStamp);
                if(isSuccessInsertToGrnTransaction && isSuccessInsertToLocationTable){
                    isSuccessUpdateGrnMasterTable = dataHelperObj.UpdateGrnMasterTable();
                }
                if (isSuccessInsertToGrnTransaction && isSuccessInsertToLocationTable && isSuccessUpdateGrnMasterTable) {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        new TRANS_SubmitGrnTransactionTask(mContext, true).execute();
                    } else {
                        Utils.showToast(mContext, "Forecast saved Successfully but could not be sent to server due to poor connectivity.");
                        finish();
                    }


                } else {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                    Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please contact admin!");
                }

//                Message msgObj = mHandler.obtainMessage();
//                Bundle b = new Bundle();
//                b.putString("message", "SubmitJobDone");
//                msgObj.setData(b);
//                mHandler.sendMessage(msgObj);
//            }
//        }.start();
    }
}
