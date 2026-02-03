package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import androidx.core.content.FileProvider;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitMerchandisingTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.MerchandisingAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MerchandisingDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class MerchandisingActivity extends AceDnsParentActivity {

    private static ImageView mImageViewLogo = null;
    private static ImageView mImageViewMerchandising = null;

    private static Button mButtonSubmit = null;
    private static Button mButtonBack = null;
    private static Button mButtonCapture = null;
    private static Button mButtonIssue = null;
    private static Button mButtonClient = null;

    private static TextView mTextViewIssue = null;
    public File mImageFile;
    public int mWidth = 175;
    public int mHeight = 150;
    LinearLayout filterLayout, clientLayout, imageLayout, orderContLayout;
    Dialog grpDialog, brandDialog, masterDialog, hoardingTypeDialog;
    Context mContext;
    String mRemarks = "";
    String mTransType = "";
    String mImagePath = "";
    String mTransactionStatus = "";
    String mMerchandisingId = "";
    Uri mFileUri;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<String> filterList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<ProductGroupDetails> tempProductGroupList;
    ArrayList<ProductSubGrpDetails> tempProductSubGroupList;
    ArrayList<ProductBrandDetails> tempProductBrandList;
    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    int chosenCustPos;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    String lastStr = "", attachmentName = "";
    ArrayList<ProductMasterDetails> tempProductList;
    int lastProdPos = 0;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    ProgressDialog mProgressDialogFetcher;
    Handler mHandlerFetcher;
    ProgressDialog mProgressDialogSave;
    Handler mHandlerSave;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    String[] hoardingTypeArray = {"LIGHT", "FLEX", "STRUCTURE", "OBSTRUCTION"};
    String[] hoardingLIGHTArray = {"FULL OFF", "1 OFF", "2 OFF", "3 OFF", "4 OFF", "5 OFF"};
    String[] hoardingFLEXArray = {"DAMAGE", "WRINKLE"};
    String[] hoardingSTRUCTUREArray = {"BROKEN", "DAMAGE"};
    String[] hoardingOBSTRUCTIONArray = {"BRANCHED", "POLES", "WIRES", "BANNERS"};
    ArrayList<String> hoardingDialogItems;
    StringBuilder remarksStringBuilder;
    private int TAKE_PHOTO_CODE = 0;

    public static Bitmap decodeScaledBitmapFromSdCard(String filePath,
                                                      int reqWidth, int reqHeight) {
        // First decode with inJustDecodeBounds=true to check dimensions
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeFile(filePath, options);
        // Calculate inSampleSize
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);
        // Decode bitmap with inSampleSize set
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
            inSampleSize = heightRatio < widthRatio ? heightRatio : widthRatio;
        }
        return inSampleSize;
    }

    /*
     * ::::::::::::::::::::::::::::::::::: VIEW RELATED OPERATIONS
     * ::::::::::::::::::::::::::::::::::::::::::::::
     */

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_merchandising);
        RegisterActivities.registerActivity(this);

        Constants.mNoOfCapture = 0;
        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = MerchandisingActivity.this;
        //transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        Constants.selectedProductMasterList = new ArrayList<ProductMasterDetails>();
        Constants.selectedGroupList = new ArrayList<ProductGroupDetails>();
        Constants.selectedSubGroupList = new ArrayList<ProductSubGrpDetails>();
        Constants.selectedBrandList = new ArrayList<ProductBrandDetails>();

        filterButtonList = new ArrayList<Button>();


        initView();
        drawFilterLayout();


        // currentDate = formatter.format(new Date());
        // String currentDate = Constants.dateString.substring(6,
        // 8)+"-"+Constants.dateString.substring(4,
        // 6)+"-"+Constants.dateString.substring(0, 4);

        showMultiRemarksDialog();

        mHandlerFetcher = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogFetcher.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                MerchandisingActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                showGrpListDialog();
                                break;
                            case 2:
                                showSubGrpListDialog();
                                break;
                            case 3:
                                showBrandListDialog();
                                break;
                            case 4:
                                showMasterListDialog();
                                break;
                        }
                    }
                });
            }
        };

        mHandlerSave = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialogSave.cancel();
                    if (mTransType.equalsIgnoreCase("CHECK STATUS")
                            || mTransType.equalsIgnoreCase("RECTIFYING ISSUE")) {
                        new TRANS_SubmitMerchandisingTask(mContext, true, true)
                                .execute();
                    } else {
                        new TRANS_SubmitMerchandisingTask(mContext, true, false)
                                .execute();
                    }
                }
            }
        };
    }

    @Override
    public void onResume() {
        super.onResume();

        if (Constants.isFromConfirmationActivity) {
            Constants.isFromConfirmationActivity = false;
            switch (filterNo) {
                case 1:
                    filterButtonList.get(3).setEnabled(true);
                    filterButtonList.get(3).setText(filterList.get(4));
                    break;
                case 2:
                    filterButtonList.get(0).setEnabled(true);
                    filterButtonList.get(0).setText(filterList.get(1));
                    filterButtonList.get(3).setEnabled(false);
                    filterButtonList.get(3).setText(filterList.get(4));
                    break;
                case 3:
                    filterButtonList.get(0).setEnabled(true);
                    filterButtonList.get(0).setText(filterList.get(1));
                    filterButtonList.get(1).setEnabled(false);
                    filterButtonList.get(1).setText(filterList.get(2));
                    filterButtonList.get(3).setEnabled(false);
                    filterButtonList.get(3).setText(filterList.get(4));
                    break;
                case 4:
                    filterButtonList.get(0).setEnabled(true);
                    filterButtonList.get(0).setText(filterList.get(1));
                    filterButtonList.get(1).setEnabled(false);
                    filterButtonList.get(1).setText(filterList.get(2));
                    filterButtonList.get(2).setEnabled(false);
                    filterButtonList.get(2).setText(filterList.get(3));
                    filterButtonList.get(3).setEnabled(false);
                    filterButtonList.get(3).setText(filterList.get(4));
                    break;
            }
        }
    }

    /*
     * ::::::::::::::::::::::::::::::: LISTNER METHODS
     * ::::::::::::::::::::::::::::::::::::::::::
     */

    public void initView() {
        mImageViewLogo = (ImageView) findViewById(R.id.imagelogo);
        clientLayout = (LinearLayout) findViewById(R.id.client_layout);
        imageLayout = (LinearLayout) findViewById(R.id.img_layout);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        mImageViewMerchandising = (ImageView) findViewById(R.id.img_merchant);
        mButtonClient = (Button) findViewById(R.id.txt_client);
        mButtonClient.setTag(157);
        mButtonClient.setOnClickListener(MerchandisingActivity.this);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);
        orderContLayout = (LinearLayout) findViewById(R.id.odr_cont_layout);

        mButtonSubmit = (Button) findViewById(R.id.btn_order_form);
        mButtonSubmit.setTag(102);
        mButtonSubmit.setEnabled(false);
        mButtonSubmit.setOnClickListener(MerchandisingActivity.this);
        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(MerchandisingActivity.this);
        mButtonCapture = (Button) findViewById(R.id.btn_capture);
        mButtonCapture.setTag(109);
        mButtonCapture.setOnClickListener(MerchandisingActivity.this);
        mButtonIssue = (Button) findViewById(R.id.btn_remarks);
        mButtonIssue.setTag(117);
        mButtonIssue.setOnClickListener(MerchandisingActivity.this);
    }

    @SuppressWarnings("deprecation")
    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList(); // filterList =
        // [4(filter_no),dadu,baba,NA,chhele]
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                filterButton.setOnClickListener(this);
                filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    public void onClick(View clkdView) {
        if (clkdView instanceof Button) {
            int tag = (Integer) clkdView.getTag();
            switch (tag) {
                case 1:
                    switch (filterNo) {
                        case 2:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(1).setEnabled(true);
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            filterButtonList.get(1).setEnabled(true);
                            break;
                    }
                    lastSubGroupSelected = false;
                    lastBrandSelected = false;
                    lastProductSelected = false;
                    // showGrpDialog();
                    prepareOrderData(1, "");
                    break;
                case 2:
                    switch (filterNo) {
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            break;
                    }
                    lastBrandSelected = false;
                    lastProductSelected = false;
                    if (selectedGrp != null) {
                        prepareOrderData(2, selectedGrp.getGroupCode());
                    } else {
                        Toast.makeText(MerchandisingActivity.this,
                                "Please select the parent category", 2000);
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(MerchandisingActivity.this,
                                "Please select the parent category", 2000);
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                prepareOrderData(4, selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(MerchandisingActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(MerchandisingActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(MerchandisingActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                    }
            }
        }
        if (clkdView == mButtonSubmit) {
            // Order Button
            getWindow()
                    .setSoftInputMode(
                            WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            if (mTransType.equalsIgnoreCase("CHECK STATUS")) {
                mRemarks = remarksStringBuilder.toString();
                if (currentProductMasterObj != null && mImagePath.length() > 0 && mRemarks.length() > 0) {
                    saveMerchandisingData("NOT DONE", "");
                } else {
                    Toast.makeText(MerchandisingActivity.this,
                            "Please select a product,enter remarks and capture an image",
                            2000).show();
                }
            } else if (mTransType.equalsIgnoreCase("REPORTING ISSUE")) {
                if (currentProductMasterObj != null) {
                    if (remarksStringBuilder != null) {
                        showInstructionDialog();
                    } else {
                        Utils.showToast(mContext, "Select issue type.");
                    }
                } else {
                    Utils.showToast(mContext, "Select Property");
                }
            } else if (mTransType.equalsIgnoreCase("RECTIFYING ISSUE")) {

            }

        }
        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == mButtonCapture) {
            ShowImageCaptureLayer();
        }
        if (clkdView == mButtonClient) {
            showChooseCustomerDialog();
        }
        if (clkdView == mButtonIssue) {
            remarksStringBuilder = new StringBuilder();
            if (currentProductMasterObj != null) {
                if (currentProductMasterObj.getGrpCode().equalsIgnoreCase("Hoarding")) {
                    showHoardingTypeDialog(0);
                } else {
                    remarksStringBuilder = new StringBuilder();
                    showInstructionDialog();
                }
            } else {
                Utils.showToast(mContext, "Select Property");
            }
        }
    }

    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            groupAdapter = new ProductGrpAdapter(MerchandisingActivity.this,
                    R.layout.product_list_child, tempProductGroupList);
            grpDialog = new Dialog(MerchandisingActivity.this,
                    R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_with_search);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) grpDialog
                    .findViewById(R.id.autoCompleteTextView1);
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
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductGroupList();
                    }
                    lastStr = str;
                    filterProductGroupArray(str.length(), str);
                    groupAdapter.notifyDataSetChanged();


                }
            });
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            dialogList.setAdapter(groupAdapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    grpDialog.cancel();
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    selectedGrp = productGroupList.get(arg2);
                    filterButtonList.get(0).setText(selectedGrp.getGroupName());

                    switch (filterNo) {
                        case 2:
                            prepareOrderData(4, selectedGrp.getGroupCode());
                            break;
                        case 3:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                        case 4:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                    }

                }
            });
            Button cancel = (Button) grpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(MerchandisingActivity.this,
                    "There are no items left.Please submit order.", 2000)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(
                    MerchandisingActivity.this, R.layout.product_list_child,
                    tempProductSubGroupList);
            final Dialog subGrpDialog = new Dialog(MerchandisingActivity.this,
                    R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_with_search);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) subGrpDialog
                    .findViewById(R.id.autoCompleteTextView1);
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
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductSubGroupList();
                    }
                    lastStr = str;
                    filterProductSubGroupArray(str.length(), str);
                    subGroupAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) subGrpDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(subGroupAdapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    subGrpDialog.cancel();
                    selectedSubGrp = productSubGroupList.get(arg2);
                    filterButtonList.get(1).setText(
                            selectedSubGrp.getSubGrpName());
                    switch (filterNo) {
                        case 3:
                            prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            break;
                        case 4:
                            prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                            break;
                    }
                }
            });
            Button cancel = (Button) subGrpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    subGrpDialog.cancel();
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(
                    MerchandisingActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    /*
     * ::::::::::::::::::::::::::::::: REMOVING REPEATED ITEMS FROM SELECTION
     * LIST :::::::::::::::::::::::::::::::
     */

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(MerchandisingActivity.this,
                    R.layout.product_list_child, tempProductBrandList);
            brandDialog = new Dialog(MerchandisingActivity.this,
                    R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_with_search);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) brandDialog
                    .findViewById(R.id.autoCompleteTextView1);
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
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductBrandList();
                    }
                    lastStr = str;
                    filterProductBrandArray(str.length(), str);
                    brandAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) brandDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(brandAdapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    brandDialog.cancel();
                    selectedBrand = productBrandList.get(arg2);
                    filterButtonList.get(2).setText(
                            selectedBrand.getBrandName());
                    prepareOrderData(4, selectedBrand.getBrandCode());
                }
            });
            Button cancel = (Button) brandDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    brandDialog.cancel();
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(
                    MerchandisingActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            prodAdapter = new ProductMasterAdapter(MerchandisingActivity.this,
                    R.layout.product_list_child, tempProductList);
            masterDialog = new Dialog(MerchandisingActivity.this,
                    R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) masterDialog
                    .findViewById(R.id.autoCompleteTextView1);
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
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) masterDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(prodAdapter);
            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    masterDialog.cancel();
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    lastProdPos = arg2;
                    currentProductMasterObj = tempProductList.get(arg2);
                    filterButtonList.get(3).setText(
                            currentProductMasterObj.getDesc());
                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });

            masterDialog.show();
        } else {
            Toast.makeText(
                    MerchandisingActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void removeRepeatedProductItems() {
        for (int kk = 0; kk < Constants.selectedProductMasterList.size(); kk++) {
            ProductMasterDetails currentItem = Constants.selectedProductMasterList
                    .get(kk);
            for (int x = 0; x < productMasterList.size(); x++) {
                if (productMasterList.get(x).getProdCode()
                        .equalsIgnoreCase(currentItem.getProdCode())) {
                    productMasterList.remove(productMasterList.get(x));
                }
            }
        }
        int size = productMasterList.size();
        System.out.println(size);
    }

    public void removeRepeatedBrandItems() {
        for (int kk = 0; kk < Constants.selectedBrandList.size(); kk++) {
            ProductBrandDetails currentItem = Constants.selectedBrandList
                    .get(kk);
            for (int x = 0; x < productBrandList.size(); x++) {
                if (productBrandList.get(x).getBrandCode()
                        .equalsIgnoreCase(currentItem.getBrandCode())) {
                    productBrandList.remove(productBrandList.get(x));
                }
            }
        }
        int size = productBrandList.size();
        System.out.println(size);
    }

    public void removeRepeatedGroupItems() {
        for (int kk = 0; kk < Constants.selectedGroupList.size(); kk++) {
            ProductGroupDetails currentItem = Constants.selectedGroupList
                    .get(kk);
            for (int x = 0; x < productGroupList.size(); x++) {
                if (productGroupList.get(x).getGroupCode()
                        .equalsIgnoreCase(currentItem.getGroupCode())) {
                    productGroupList.remove(productGroupList.get(x));
                }
            }
        }
    }

    public void removeRepeatedSubGroupItems() {
        for (int kk = 0; kk < Constants.selectedSubGroupList.size(); kk++) {
            ProductSubGrpDetails currentItem = Constants.selectedSubGroupList
                    .get(kk);
            for (int x = 0; x < productSubGroupList.size(); x++) {
                if (productSubGroupList.get(x).getSubGrpCode()
                        .equalsIgnoreCase(currentItem.getSubGrpCode())) {
                    productSubGroupList.remove(productSubGroupList.get(x));
                }
            }
        }
    }

    public String getFormatString(String unformatString) {
        String formatString = "";
        StringBuffer res = new StringBuffer();

        String[] strArr = unformatString.split(" ");
        for (String str : strArr) {
            char[] stringArray = str.trim().toCharArray();
            for (int ii = 0; ii < stringArray.length; ii++) {
                if (ii == 0) {
                    stringArray[ii] = Character.toUpperCase(stringArray[ii]);
                } else {
                    stringArray[ii] = Character.toLowerCase(stringArray[ii]);
                }
            }
            str = new String(stringArray);
            res.append(str).append(" ");
        }
        formatString = res.toString().trim();
        return formatString;
    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                // if(tempProductList.get(ii).getDesc().substring(0,strCnt).equalsIgnoreCase(charVal)){
                /*
                 * String[] wordList =
                 * tempProductList.get(ii).getDesc().split(" "); Boolean found =
                 * checkMatch(wordList,strCnt,charVal);
                 */
                if (tempProductList.get(ii).getDesc().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductList.remove(tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductList.remove(tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductGroupArray(int strCnt, String charVal) {
        int size = tempProductGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductGroupList.get(ii).getGroupName().length() >= strCnt) {
                if (tempProductGroupList.get(ii).getGroupName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductGroupList.remove(tempProductGroupList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductGroupList.remove(tempProductGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductSubGroupArray(int strCnt, String charVal) {
        int size = tempProductSubGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductSubGroupList.get(ii).getSubGrpName().length() >= strCnt) {
                if (tempProductSubGroupList.get(ii).getSubGrpName()
                        .toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductSubGroupList.remove(tempProductSubGroupList
                            .get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductSubGroupList.remove(tempProductSubGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductBrandArray(int strCnt, String charVal) {
        int size = tempProductBrandList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductBrandList.get(ii).getBrandName().length() >= strCnt) {
                if (tempProductBrandList.get(ii).getBrandName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductBrandList.remove(tempProductBrandList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductBrandList.remove(tempProductBrandList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }

    }

    public void reInitialiseProductGroupList() {
        tempProductGroupList.removeAll(tempProductGroupList);
        int size = tempProductGroupList.size();
        int size1 = productGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productGroupList.size(); kk++) {
            tempProductGroupList.add(productGroupList.get(kk));
        }

    }

    public void reInitialiseProductSubGroupList() {
        tempProductSubGroupList.removeAll(tempProductSubGroupList);
        int size = tempProductSubGroupList.size();
        int size1 = productSubGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productSubGroupList.size(); kk++) {
            tempProductSubGroupList.add(productSubGroupList.get(kk));
        }

    }

    public void reInitialiseProductBrandList() {
        tempProductBrandList.removeAll(tempProductBrandList);
        int size = tempProductBrandList.size();
        int size1 = productBrandList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productBrandList.size(); kk++) {
            tempProductBrandList.add(productBrandList.get(kk));
        }

    }

    public boolean checkMatch(String[] wordList, int strCnt, String charVal) {
        for (String word : wordList) {
            if (word.length() >= strCnt
                    && word.substring(0, strCnt).equalsIgnoreCase(charVal))
                return true;
        }
        return false;
    }

    public void prepareOrderData(final int doWhat, final String param) {
        mProgressDialogFetcher = new ProgressDialog(mContext);
        mProgressDialogFetcher.setMessage("Fetching Data.Please wait..");
        mProgressDialogFetcher.show();
        mProgressDialogFetcher.setCancelable(false);
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productGroupList = mAceDnsDatabase.getProductGroupList(false);
                        removeRepeatedGroupItems();
                        tempProductGroupList = new ArrayList<ProductGroupDetails>();
                        reInitialiseProductGroupList();
                        break;
                    case 2:
                        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(param, false);
                        removeRepeatedSubGroupItems();
                        tempProductSubGroupList = new ArrayList<ProductSubGrpDetails>();
                        reInitialiseProductSubGroupList();
                        break;
                    case 3:
                        productBrandList = mAceDnsDatabase.getProductBrandList(param, false);
                        removeRepeatedBrandItems();
                        tempProductBrandList = new ArrayList<ProductBrandDetails>();
                        reInitialiseProductBrandList();
                        break;
                    case 4:
                        productMasterList = mAceDnsDatabase.getProductMasterList(param, filterNo, false);
                        removeRepeatedProductItems();
                        tempProductList = new ArrayList<ProductMasterDetails>();
                        reInitialiseProductList();
                        break;
                }
                Message msgObj = mHandlerFetcher.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                mHandlerFetcher.sendMessage(msgObj);
            }
        }.start();
    }
	
	

	/*@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (requestCode == 1) {
			switch (resultCode) {
			case 0:
				showAttachmentDialog();
				break;
			case -1:
				Bitmap bitmap = BitmapFactory.decodeFile(mImagePath);
				mImageViewMerchandising.setImageBitmap(bitmap);
				break;
			}
		}
	}*/

    public void ShowImageCaptureLayer() {
        if (Constants.mNoOfCapture >= 1) {
            Toast.makeText(mContext, "Image already captured", Toast.LENGTH_LONG).show();
        } else {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            attachmentName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
            mImagePath = Utils.getAppStoragePath(mContext) + attachmentName;
            mImageFile = new File(mImagePath);
            try {
                mImageFile.createNewFile();
            } catch (IOException e) {
            }

//	        mFileUri = Uri.fromFile(mImageFile);
            if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                mFileUri = FileProvider.getUriForFile(mContext,
                        BuildConfig.APPLICATION_ID + ".provider",
                        mImageFile);
            } else {
                mFileUri = Uri.fromFile(mImageFile);

            }
            Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
            startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
        }
    }

    public void showAttachmentDialog1() {
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        attachmentName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".png";

        Intent imageIntent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
        mImagePath = Utils.getAppStoragePath(mContext) + attachmentName;
//		Uri uriSavedImage = Uri.fromFile(new File(mImagePath));
        Uri uriSavedImage = FileProvider.getUriForFile(mContext,
                BuildConfig.APPLICATION_ID + ".provider",
                new File(mImagePath));

        imageIntent.putExtra(MediaStore.EXTRA_OUTPUT, uriSavedImage);
        startActivityForResult(imageIntent, 1);


    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            Constants.mNoOfCapture += 1;
            Constants.isSurveyImageTake = true;
            try {
                Bitmap bitmap = decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
                if (mTransType.equalsIgnoreCase("CHECK STATUS")) {
                    mImageViewMerchandising.setImageBitmap(bitmap);
                }
                FileOutputStream out = null;
                try {
                    out = new FileOutputStream(mImagePath);
                    bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);

                } catch (FileNotFoundException e) {
                    e.printStackTrace();
                }
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(attachmentName, "MERCHANDISING");
                if (mTransType.equalsIgnoreCase("RECTIFYING ISSUE")) {
                    saveMerchandisingData(mTransactionStatus, mMerchandisingId);
                }

            } catch (Exception ex) {
                Toast.makeText(mContext, "Image is two large", Toast.LENGTH_LONG).show();
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
            Log.i("Camera canceled", "Cancel");
        }
        if (requestCode == TAKE_PHOTO_CODE && resultCode == RESULT_OK) {

            if (Constants.mNoOfCapture >= 1) {
                Toast.makeText(mContext, "Image captured", Toast.LENGTH_LONG).show();

            } else {
                Constants.isSurveyImageTake = true;
                AlertDialog.Builder AlertDG = new AlertDialog.Builder(MerchandisingActivity.this);
                AlertDG.setTitle("Information");
                AlertDG.setMessage("Do you want to take another pics?");
                AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        mImagePath = Utils.getAppStoragePath(mContext) + imageName;
                        mImageFile = new File(mImagePath);
                        try {
                            mImageFile.createNewFile();
                        } catch (IOException e) {
                        }

//						mFileUri = Uri.fromFile(mImageFile);
                        mFileUri = FileProvider.getUriForFile(mContext,
                                BuildConfig.APPLICATION_ID + ".provider",
                                mImageFile);
                        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
                        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
                    }
                });
                AlertDG.setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {

                    }
                });
                AlertDG.setCancelable(true);
                AlertDG.create().show();
            }
        }
    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        // instructionDialog.setTitle("Remarks if any ?");
        final EditText edInst = (EditText) instructionDialog
                .findViewById(R.id.ed_input);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (edInst.getText().toString().trim().length() > 0) {
                    instructionDialog.cancel();
                    if (mTransType.equalsIgnoreCase("RECTIFYING ISSUE")) {
                        String instruction = "";
                        instruction = edInst.getText().toString();
                        instruction = instruction.toUpperCase();
                        remarksStringBuilder.append(instruction);
                    } else if (mTransType.equalsIgnoreCase("REPORTING ISSUE")) {
                        String instruction = "";
                        instruction = edInst.getText().toString();
                        instruction = instruction.toUpperCase();
                        remarksStringBuilder.append(";" + instruction);
                        mRemarks = remarksStringBuilder.toString();
                        if (currentProductMasterObj != null && mRemarks.length() > 0) {
                            saveMerchandisingData("NOT DONE", "");
                        } else {
                            Toast.makeText(
                                    MerchandisingActivity.this,
                                    "Please select a Product,Give Remarks and Capture an Image",
                                    2000).show();
                        }
                    }
                } else {
                    Utils.showToast(mContext, "Please enter the remarks");
                }


            }
        });
        instructionDialog.show();
    }

    public void showMultiRemarksDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog_selvel);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Select Operation");

        Button reportButton = (Button) multiRemarksDialog.findViewById(R.id.btn_report_issue);
        Button statusButton = (Button) multiRemarksDialog.findViewById(R.id.btn_chk_status);
        Button rectifyButton = (Button) multiRemarksDialog.findViewById(R.id.btn_rectify);

        Button btnCancel = (Button) multiRemarksDialog.findViewById(R.id.btn);
        btnCancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                multiRemarksDialog.cancel();
                finish();
            }
        });
        reportButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mTransType = "REPORTING ISSUE";
                clientLayout.setVisibility(View.GONE);
                Constants.selectedCustomer = new CustomerDetails();
                imageLayout.setVisibility(View.GONE);
                multiRemarksDialog.cancel();
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                mButtonSubmit.setEnabled(true);

                LinearLayout.LayoutParams param = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT,
                        4.0f);
                filterLayout.setLayoutParams(param);

                LinearLayout.LayoutParams param1 = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT,
                        1.0f);
                orderContLayout.setLayoutParams(param1);
            }
        });
        statusButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mTransType = "CHECK STATUS";
                multiRemarksDialog.cancel();
                mButtonIssue.setVisibility(View.GONE);
                remarksStringBuilder = new StringBuilder();
                remarksStringBuilder.append("     ");
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                mButtonSubmit.setEnabled(true);
            }
        });
        rectifyButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mTransType = "RECTIFYING ISSUE";
                Constants.selectedCustomer = new CustomerDetails();
                multiRemarksDialog.cancel();
                showUnresolvedMerchandisingListDialog();
            }
        });
        multiRemarksDialog.show();
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK
                || keyCode == KeyEvent.KEYCODE_MENU
                || keyCode == KeyEvent.KEYCODE_HOME
                || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void showHoardingTypeDialog(final int dialogId) {
        prepareHoardingDialogItems(dialogId);
        hoardingTypeDialog = new Dialog(MerchandisingActivity.this,
                R.style.PauseDialog);
        hoardingTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        hoardingTypeDialog.setContentView(R.layout.select_from_list);
        hoardingTypeDialog.setCancelable(false);
        TextView title = (TextView) hoardingTypeDialog.findViewById(R.id.title);
        title.setText("Please select an option");
        ListView dialogList = (ListView) hoardingTypeDialog
                .findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(
                MerchandisingActivity.this, R.layout.product_list_child,
                hoardingDialogItems);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                hoardingTypeDialog.cancel();
                if (dialogId == 0) {
                    remarksStringBuilder.append(hoardingDialogItems.get(arg2)
                            + ";");
                    if (arg2 == 0) {
                        showHoardingTypeDialog(1);
                    } else if (arg2 == 1) {
                        showHoardingTypeDialog(2);
                    } else if (arg2 == 2) {
                        showHoardingTypeDialog(3);
                    } else if (arg2 == 3) {
                        showHoardingTypeDialog(4);
                    }
                } else {
                    remarksStringBuilder.append(hoardingDialogItems.get(arg2));
                }
                if (mTextViewIssue == null) {
                    mTextViewIssue = new TextView(mContext);
                    mTextViewIssue.setTextSize(15);
                    LinearLayout.LayoutParams param = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1.0f);
                    mTextViewIssue.setLayoutParams(param);
                    mTextViewIssue.setTextColor(Color.parseColor("#003399"));
                    mTextViewIssue.setText("Issue Details : " + remarksStringBuilder.toString());
                    filterLayout.addView(mTextViewIssue);
                } else {
                    mTextViewIssue.setText("Issue Details : " + remarksStringBuilder.toString());
                }
            }
        });
        Button cancel = (Button) hoardingTypeDialog.findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                hoardingTypeDialog.cancel();
            }
        });
        hoardingTypeDialog.show();
    }

    public void prepareHoardingDialogItems(int dialogId) {
        hoardingDialogItems = new ArrayList<String>();
        switch (dialogId) {
            case 0:
                for (int ii = 0; ii < hoardingTypeArray.length; ii++) {
                    hoardingDialogItems.add(hoardingTypeArray[ii]);
                }
                break;
            case 1:
                for (int ii = 0; ii < hoardingLIGHTArray.length; ii++) {
                    hoardingDialogItems.add(hoardingLIGHTArray[ii]);
                }
                break;
            case 2:
                for (int ii = 0; ii < hoardingFLEXArray.length; ii++) {
                    hoardingDialogItems.add(hoardingFLEXArray[ii]);
                }
                break;
            case 3:
                for (int ii = 0; ii < hoardingSTRUCTUREArray.length; ii++) {
                    hoardingDialogItems.add(hoardingSTRUCTUREArray[ii]);
                }
                break;
            case 4:
                for (int ii = 0; ii < hoardingOBSTRUCTIONArray.length; ii++) {
                    hoardingDialogItems.add(hoardingOBSTRUCTIONArray[ii]);
                }
                break;
        }
    }

    public void saveMerchandisingData(final String transactionStatus, final String rectifyingId) {
        mProgressDialogSave = new ProgressDialog(mContext);
        mProgressDialogSave.setMessage("Saving Data.Please wait..");
        mProgressDialogSave.show();
        mProgressDialogSave.setCancelable(false);
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String merchandising_id = "MC" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                mAceDnsTransactionDatabase.insertToMerchandisingDetails(merchandising_id,
                        currentProductMasterObj.getProdCode(),
                        Constants.selectedCustomer.getCustomerCode(),
                        mTransType, mRemarks, attachmentName, transactionStatus,
                        rectifyingId);
                mAceDnsTransactionDatabase.insertToLocationTable("MC", timeStamp);
				/*if (mTransType.equalsIgnoreCase("CHECK STATUS")) {
					mAceDnsTransactionDatabase.insertToSupportingAttachTable(attachmentName, "MERCHANDISING");
				}*/
                if (mTransType.equalsIgnoreCase("RECTIFYING ISSUE")) {
                    mAceDnsTransactionDatabase.updateIssueStatus(rectifyingId);
                }
                Message msgObj = mHandlerSave.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandlerSave.sendMessage(msgObj);
            }
        }.start();
    }

    public void showChooseCustomerDialog() {
        mCustomerDetailsList = mAceDnsDatabase.getCustomerList();
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext,
                R.layout.customer_list_child, mCustomerDetailsList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a customer ");
        EditText searchText = (EditText) mDialogCustomer
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                Constants.isSelectCustomer = true;
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                mDialogCustomer.cancel();
                mButtonClient.setText(Constants.selectedCustomer.getCustomerName());
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                mButtonSubmit.setEnabled(true);
            }
        });

        Button addCustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();

    }

    public void showUnresolvedMerchandisingListDialog() {
        final ArrayList<MerchandisingDetails> unresolvedMerchandisinglist = mAceDnsTransactionDatabase.getUnresolvedMerchandisingList();
        if (unresolvedMerchandisinglist.size() > 0) {
            final Dialog merchandisingDialog = new Dialog(
                    MerchandisingActivity.this, R.style.PauseDialog);
            merchandisingDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            merchandisingDialog.setContentView(R.layout.select_from_list);
            merchandisingDialog.setTitle("Please select an option");
            merchandisingDialog.setCancelable(false);
            TextView title = (TextView) merchandisingDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) merchandisingDialog.findViewById(R.id.list);
            MerchandisingAdapter adapter1 = new MerchandisingAdapter(MerchandisingActivity.this, R.layout.merchandising_list_child, unresolvedMerchandisinglist);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    merchandisingDialog.cancel();
                    showRectifyIssueDialog(unresolvedMerchandisinglist.get(arg2));
                }
            });
            Button cancel = (Button) merchandisingDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    merchandisingDialog.cancel();
                }
            });
            merchandisingDialog.show();
        } else {
            Utils.showToast(mContext, "No issues found for Rectify.");
            finish();
        }
    }

    public void showRectifyIssueDialog(final MerchandisingDetails selectdObj) {
        final Dialog rectifyDialog = new Dialog(mContext);
        rectifyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        rectifyDialog.setContentView(R.layout.rectification_dialog);
        rectifyDialog.setCancelable(false);
        TextView title = (TextView) rectifyDialog.findViewById(R.id.title);
        title.setText("Provide the details.");
        TextView issueDetails = (TextView) rectifyDialog.findViewById(R.id.txt_issue_details);
		/*String issueDetailsTxt = "";		
		String[] issueDetailsArray = selectdObj.getRemarks().split(";");
		for (int ii = 0; ii < issueDetailsArray.length; ii++) {
			issueDetailsTxt = issueDetailsTxt + issueDetailsArray[ii] + " : ";
		}
		issueDetails.setText(issueDetailsTxt.substring(0,issueDetailsTxt.length() - 2));*/
        String issueDetailsTxt = "";
        String[] issueDetailsArray = selectdObj.getRemarks().split(";");
        for (int ii = 0; ii < issueDetailsArray.length; ii++) {
            if (issueDetailsArray[ii].trim().length() > 0) {
                if (ii != (issueDetailsArray.length - 1)) {
                    issueDetailsTxt = issueDetailsTxt + issueDetailsArray[ii] + " : ";
                } else {
                    issueDetailsTxt = issueDetailsTxt + issueDetailsArray[ii];
                }
            }
        }
        issueDetails.setText(issueDetailsTxt);


        final EditText edInst = (EditText) rectifyDialog.findViewById(R.id.ed_input);
        final RadioGroup rg = (RadioGroup) rectifyDialog.findViewById(R.id.radioGroup1);
        Button submit = (Button) rectifyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                rectifyDialog.cancel();
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                String instruction = "", transactionStatus = "";
                instruction = edInst.getText().toString();
                mRemarks = instruction;
                currentProductMasterObj = new ProductMasterDetails();
                int selectedId = rg.getCheckedRadioButtonId();
                RadioButton rb = (RadioButton) rectifyDialog
                        .findViewById(selectedId);
                if (rb.getText().toString().equalsIgnoreCase("Resolved")) {
                    transactionStatus = "DONE";
                } else {
                    transactionStatus = "NOT DONE";
                }
                mTransactionStatus = transactionStatus;
                mMerchandisingId = selectdObj.getMerchandisingId();
                ShowImageCaptureLayer();
            }
        });
        Button cancel = (Button) rectifyDialog.findViewById(R.id.btn_cancel);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                rectifyDialog.cancel();
                finish();
            }
        });
        rectifyDialog.show();
    }
}
