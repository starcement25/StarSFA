package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;

import androidx.annotation.NonNull;
import androidx.core.content.FileProvider;

import android.text.InputType;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.MarketFeedbackStockAuditAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitMarketFeedbackStockAudit;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Objects;

public class ActivityMarketFeedbackStockConfirmation extends AceDnsParentActivity implements OnClickListener, OnItemClickListener, OnItemLongClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListView = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonRemarks = null;
    public File mImageFile;
    public int mWidth = 175;
    public int mHeight = 150;
    public String mImagePath = "";
    public String mSImageName = "";
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Context mContext;
    MarketFeedbackStockAuditAdapter adapter;
    Handler mHandler;
    ProgressDialog loader;
    Uri mFileUri;
    private String mRemarks = "";
    private final int TAKE_PHOTO_CODE = 0;
    private String menuType = "mf_stock";

    public static Bitmap decodeScaledBitmapFromSdCard(String filePath, int reqWidth, int reqHeight) {
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeFile(filePath, options);
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);
        options.inJustDecodeBounds = false;
        return BitmapFactory.decodeFile(filePath, options);
    }

    public static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth, int reqHeight) {
        final int height = options.outHeight;
        final int width = options.outWidth;
        int inSampleSize = 1;
        if (height > reqHeight || width > reqWidth) {
            final int heightRatio = Math.round((float) height / (float) reqHeight);
            final int widthRatio = Math.round((float) width / (float) reqWidth);
            inSampleSize = Math.min(heightRatio, widthRatio);
        }
        return inSampleSize;
    }

    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_marketfeedback_stockconfirmation);
        RegisterActivities.registerActivity(this);
        if (getIntent().hasExtra("menuType")) {
            menuType = Objects.requireNonNull(getIntent().getExtras()).getString("menuType");
        }
        InitializeView();

        mContext = ActivityMarketFeedbackStockConfirmation.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        adapter = new MarketFeedbackStockAuditAdapter(ActivityMarketFeedbackStockConfirmation.this, R.layout.market_feedback_confirm_child, Constants.mMarketFeedbackStockAuditList, menuType);
        mListView.setAdapter(adapter);

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    ActivityMarketFeedbackStockConfirmation.this.runOnUiThread(() -> new TRANS_SubmitMarketFeedbackStockAudit(mContext, true, "SUBMIT").execute());
                }
            }
        };
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mButtonRemarks = findViewById(R.id.button_remarks);
        mButtonSubmit = findViewById(R.id.button_order_submit);
        mButtonAddtoCart = findViewById(R.id.button_add);
        mListView = findViewById(R.id.listView_product);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityMarketFeedbackStockConfirmation.this) + "~" + Utils.getDBVersion(ActivityMarketFeedbackStockConfirmation.this));

        mButtonRemarks.setOnClickListener(this);
        mButtonSubmit.setOnClickListener(this);
        mButtonAddtoCart.setOnClickListener(this);

        mListView.setOnItemClickListener(ActivityMarketFeedbackStockConfirmation.this);
        mListView.setOnItemLongClickListener(ActivityMarketFeedbackStockConfirmation.this);
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> parent, View view, int position, long id) {
        ShowDeleteItemDialog(position);
        return true;
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        if (menuType.equalsIgnoreCase("wsp") || menuType.equalsIgnoreCase("RSP")) {
            ShowEditPriceDialog(position);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowEditPriceDialog(final int position) {
        MarketFeedbackStockAudit currentITem = Constants.mMarketFeedbackStockAuditList.get(position);

        final Dialog edQtyDialog = new Dialog(mContext);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_order_dialog);
        TextView title = edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid input");

        final LinearLayout qnt_layout = edQtyDialog.findViewById(R.id.qnt_layout);
        final LinearLayout discountLayout = edQtyDialog.findViewById(R.id.disc_layout);
        final LinearLayout amountLayout = edQtyDialog.findViewById(R.id.amt_layout);

        qnt_layout.setVisibility(View.GONE);
        discountLayout.setVisibility(View.GONE);
        amountLayout.setVisibility(View.GONE);

        final EditText edMrp = edQtyDialog.findViewById(R.id.ed_sale);
        edMrp.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        String currentPrice = currentITem.getprice();
        edMrp.setText(currentPrice);
        edMrp.post(() -> edMrp.setSelection(edMrp.getText().length()));

        Button submit = edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String mrp;

            mrp = edMrp.getText().toString();
            if (mrp.isEmpty()) {
                mrp = "";
            }
            Constants.mMarketFeedbackStockAuditList.get(position).setprice(mrp);
            String competitor = Constants.mMarketFeedbackStockAuditList.get(position).getCompetitorName();
            for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                if (masterObj.getCopmpetitorName().equalsIgnoreCase(competitor)) {
                    Constants.selectedFeedBackList.get(count).setPtd(mrp);
                    break;
                }
            }
            adapter.notifyDataSetChanged();
            edQtyDialog.dismiss();

        });
        edQtyDialog.show();
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonAddtoCart) {
            finish();
        } else if (arg0 == mButtonSubmit) {
            if (!Constants.mMarketFeedbackStockAuditList.isEmpty()) {
                if (Constants.marketFeedbackDetailsObj.getMfSubMenuImage().equalsIgnoreCase("yes")) {
                    AlertDialog.Builder AlertDG = new AlertDialog.Builder(ActivityMarketFeedbackStockConfirmation.this);
                    AlertDG.setTitle("Information");
                    AlertDG.setMessage("Do you want to take picture?");
                    AlertDG.setPositiveButton("Yes", (dialog, which) -> ShowImageCaptureLayer());
                    AlertDG.setNegativeButton("No", (dialog, which) -> {
                        mButtonSubmit.setEnabled(false);
                        SaveMarketFeedbackStockAuditToDatabase();
                    });
                    AlertDG.setCancelable(false);
                    AlertDG.create().show();
                } else {
                    mButtonSubmit.setEnabled(false);
                    SaveMarketFeedbackStockAuditToDatabase();
                }
            } else {
                Utils.showToast(mContext, "There is no market feedback in the cart");
            }
        } else if (arg0 == mButtonRemarks) {
            ShowRemarksDialog();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowRemarksDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = instructionDialog.findViewById(R.id.ed_input);
        edInst.setText(mRemarks);
        Button submit = instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            instructionDialog.cancel();
            String instruction = "";
            instruction = edInst.getText().toString();
            mRemarks = instruction;
        });
        instructionDialog.show();
    }

    @SuppressLint("SimpleDateFormat")
    public void SaveMarketFeedbackStockAuditToDatabase() {
        new GPSTracker(mContext);
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp;
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                mAceDnsTransactionDatabase.INSERTtoStockAuditDetails(timeStamp);
                mAceDnsTransactionDatabase.INSERTtoStockAuditHeader(timeStamp, Constants.selectedCustomer.getCustomerCode(), mRemarks, mSImageName);
                mAceDnsTransactionDatabase.insertToLocationTable("MS", timeStamp);
                if (menuType.equalsIgnoreCase("wsp")) {
                    mAceDnsTransactionDatabase.INSERTtoMarketFeedbackWsp(timeStamp, "MF", Constants.selectedCustomer.getRouteCode());
                } else {
                    mAceDnsTransactionDatabase.INSERTtoMarketFeedback(timeStamp, "MF", Constants.selectedCustomer.getRouteCode());
                }

                mAceDnsTransactionDatabase.insertToLocationTable("MF", timeStamp);

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void ShowDeleteItemDialog(final int pos) {
        System.out.println("POSITION::::::::::" + pos);
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ActivityMarketFeedbackStockConfirmation.this);
        alertDialogBuilder.setMessage("Are you sure you want to delete this item ?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("OK", (dialog, id) -> {
            dialog.cancel();
            Constants.mMarketFeedbackStockAuditList.remove(pos);
            Constants.selectedFeedBackList.remove(pos);
            adapter.notifyDataSetChanged();
        });
        alertDialogBuilder.setPositiveButton("CANCEL", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint("SimpleDateFormat")
    public void ShowImageCaptureLayer() {
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
        mSImageName = imageName;
        mImagePath = Utils.getAppStoragePath(mContext) + imageName;
        mImageFile = new File(mImagePath);
        try {
            mImageFile.createNewFile();
        } catch (IOException ignored) {
        }

//	    mFileUri = Uri.fromFile(mImageFile);
        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            mFileUri = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", mImageFile);
        } else {
            mFileUri = Uri.fromFile(mImageFile);

        }
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            Constants.isSurveyImageTake = true;
            try {
                Bitmap bitmap = decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
                FileOutputStream out;
                try {
                    out = new FileOutputStream(mImagePath);
                    bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);

                } catch (FileNotFoundException ignored) {
                }
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(mSImageName, "MFS");
                mButtonSubmit.setEnabled(false);
                SaveMarketFeedbackStockAuditToDatabase();
            } catch (Exception ex) {
                Toast.makeText(mContext, "Image is two large", Toast.LENGTH_LONG).show();
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
        }
        if (requestCode == TAKE_PHOTO_CODE && resultCode == RESULT_OK) {
            Constants.isSurveyImageTake = true;
        }
    }
}
