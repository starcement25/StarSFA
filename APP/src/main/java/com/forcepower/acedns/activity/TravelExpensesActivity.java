package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import androidx.fragment.app.FragmentActivity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitTravelExpenseTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.TravelExpCategory;
import com.forcepower.acedns.bean.TravelExpSubCategory;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class TravelExpensesActivity extends FragmentActivity implements OnClickListener {

    public static EditText mEditTextDate = null;
    public static EditText mEditTextStartDestination = null;
    public static EditText mEditTextEndDestination = null;
    public static EditText mEditTextDistance = null;
    public static EditText mEditTextFare = null;

    public static Button mButtonSubmit = null;
    public static Button mButtonTransportMode = null;
    public static Button mButtonBack = null;

    public static ImageView mImageViewLogo = null;
    public static ImageView mImageViewBlink = null;

    public static LinearLayout mLinearLayoutDistance = null;
    ;

    public static RadioGroup mRadioGroupAttachment = null;
    public int mWidth = 175;
    public int mHeight = 150;
    Context mContext;
    ArrayList<String> transportModeList;
    ArrayList<String> transportSubCategoryList;
    ArrayList<TravelExpCategory> mTravelExpCategoryList;
    ArrayList<TravelExpSubCategory> mTravelExpSubCategoryList;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Handler mHandler;
    ProgressDialog mProgressDialog;
    private int itemSelected = -1;
    private int TAKE_PHOTO_CODE = 0;
    private CaldroidFragment mCaldroidFragment;
    private CaldroidListener mCaldroidListener;
    private Date mCurrentDate;
    private SimpleDateFormat mSimpleDateFormat, mSimpleDateFormatBackend;
    private String mDateValue = "";
    private String mStartDestination = "";
    private String mEndDestination = "";
    private String mFare = "0";
    private String mSupport = "";
    private String mDistance = "0";
    private String mCategoryID = "";
    private String mSubCategoryID = "";
    private String mAttachmentName = "";
    private String mImagePath = "";
    private String mDate = "";

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
            inSampleSize = heightRatio < widthRatio ? heightRatio : widthRatio;
        }
        return inSampleSize;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_travel_exp);
        RegisterActivities.registerActivity(this);
        mSimpleDateFormat = new SimpleDateFormat("dd/MM/yyyy");
        mSimpleDateFormatBackend = new SimpleDateFormat("yyyy-MM-dd");
        try {
            String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            mCurrentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
        } catch (Exception e) {
            mCurrentDate = new Date();
        }

        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(this);
        mContext = TravelExpensesActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        InitializeView();

        mRadioGroupAttachment.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                if (radioSelection.getText().toString().equalsIgnoreCase("yes")) {
                    mSupport = "yes";
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    mAttachmentName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    mImagePath = Utils.getAppStoragePath(mContext) + mAttachmentName;
                    Intent imageIntent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
                    Uri uriSavedImage = Uri.fromFile(new File(mImagePath));
                    imageIntent.putExtra(MediaStore.EXTRA_OUTPUT, uriSavedImage);
                    startActivityForResult(imageIntent, TAKE_PHOTO_CODE);
                } else {
                    mSupport = "no";
                }
            }
        });

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                    new TRANS_SubmitTravelExpenseTask(mContext, true).execute();
                }
            }
        };

        mCaldroidListener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (date.after(mCurrentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    mCaldroidFragment.dismiss();
                    mEditTextDate.setText(mSimpleDateFormat.format(date));
                    mDate = mSimpleDateFormatBackend.format(date);
                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                if (date.after(mCurrentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    mCaldroidFragment.dismiss();
                    mEditTextDate.setText(mSimpleDateFormat.format(date));
                    mDate = mSimpleDateFormatBackend.format(date);
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };
    }

    public void InitializeView() {
        Animation myFadeInAnimation = AnimationUtils.loadAnimation(TravelExpensesActivity.this, R.anim.tween);
        mImageViewLogo = (ImageView) findViewById(R.id.imagelogo);
        mImageViewBlink = (ImageView) findViewById(R.id.image_blink);
        mImageViewBlink.startAnimation(myFadeInAnimation);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mLinearLayoutDistance = (LinearLayout) findViewById(R.id.distance_layout);
        mEditTextDate = (EditText) findViewById(R.id.ed_date);
        mEditTextDate.setFocusableInTouchMode(false);
        mEditTextDate.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowDateDialog();
            }
        });
        mEditTextStartDestination = (EditText) findViewById(R.id.ed_from);
        mEditTextEndDestination = (EditText) findViewById(R.id.ed_to);
        mEditTextDistance = (EditText) findViewById(R.id.ed_distance);
        mEditTextFare = (EditText) findViewById(R.id.ed_fare);


        mRadioGroupAttachment = (RadioGroup) findViewById(R.id.radioSelectAttachment);
        mButtonTransportMode = (Button) findViewById(R.id.btn_transport);
        mButtonSubmit = (Button) findViewById(R.id.btn_submit);
        mButtonBack = (Button) findViewById(R.id.back);
        mButtonTransportMode.setOnClickListener(this);
        mButtonSubmit.setOnClickListener(this);
        mButtonBack.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        if (v == mButtonTransportMode) {
            ShowTravelCategoryDialog();
        } else if (v == mButtonSubmit) {
            saveTourExpData();
        } else if (v == mButtonBack) {
            finish();
        }
    }

    public void saveTourExpData() {
        mDateValue = mDate;
        mStartDestination = mEditTextStartDestination.getText().toString();
        mEndDestination = mEditTextEndDestination.getText().toString();
        mFare = mEditTextFare.getText().toString();
        if (mLinearLayoutDistance.getVisibility() == View.VISIBLE) {
            mDistance = mEditTextDistance.getText().toString();
        } else {
            mDistance = "0";
        }
        if (mDateValue.length() > 0 && mStartDestination.length() > 0 && mEndDestination.length() > 0 && mFare.length() > 0 && mSupport.length() > 0 && mCategoryID.length() > 0) {
            if (Double.parseDouble(mFare) > 0) {
                if (mLinearLayoutDistance.getVisibility() == View.VISIBLE) {
                    if (mDistance.length() > 0 && Double.parseDouble(mDistance) > 0) {
                        mButtonSubmit.setEnabled(false);
                        saveTravelExpenseData();
                    } else {
                        Toast.makeText(TravelExpensesActivity.this, "Distance must be greater than 0.", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    mButtonSubmit.setEnabled(false);
                    saveTravelExpenseData();
                }
            } else {
                Toast.makeText(TravelExpensesActivity.this, "Fare must be greater than 0.", Toast.LENGTH_SHORT).show();
            }
        } else {
            if (mDateValue.length() <= 0) {
                Toast.makeText(TravelExpensesActivity.this, "Please provide the date", Toast.LENGTH_SHORT).show();
            }
            if (mStartDestination.length() <= 0) {
                Toast.makeText(TravelExpensesActivity.this, "Please provide the starting destination", Toast.LENGTH_SHORT).show();
            }
            if (mEndDestination.length() <= 0) {
                Toast.makeText(TravelExpensesActivity.this, "Please provide the end destination", Toast.LENGTH_SHORT).show();
            }
            if (mSupport.length() <= 0) {
                Toast.makeText(TravelExpensesActivity.this, "Please select Yes/No.\n If you have any supporting attachment", Toast.LENGTH_SHORT).show();
            }
            if (mCategoryID.length() <= 0) {
                Toast.makeText(TravelExpensesActivity.this, "Please select travel mode", Toast.LENGTH_SHORT).show();
            }
        }
    }

    /*********************************    DIALOG    *********************************/

    public void ShowTravelCategoryDialog() {
        mTravelExpCategoryList = new ArrayList<TravelExpCategory>();
        mTravelExpCategoryList = mAceDnsDatabase.getTravelCatList();
        if (mTravelExpCategoryList != null) {
            final Dialog dialog = new Dialog(TravelExpensesActivity.this);
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setContentView(R.layout.select_from_list);
            transportModeList = new ArrayList<String>();
            for (int ii = 0; ii < mTravelExpCategoryList.size(); ii++) {
                transportModeList.add(mTravelExpCategoryList.get(ii).getCategoryName());
            }
            TextView title = (TextView) dialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) dialog.findViewById(R.id.list);
            SimpleStringAdapter adapter1 = new SimpleStringAdapter(TravelExpensesActivity.this, R.layout.simple_list_child, transportModeList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    dialog.cancel();
                    itemSelected = arg2;
                    if (transportModeList.get(itemSelected).equalsIgnoreCase("Own Vehicle")) {
                        mLinearLayoutDistance.setVisibility(View.VISIBLE);
                    } else {
                        mLinearLayoutDistance.setVisibility(View.GONE);
                    }
                    TravelExpCategory selectedCat = mTravelExpCategoryList.get(arg2);
                    mCategoryID = selectedCat.getCategoryId();
                    mTravelExpSubCategoryList = mAceDnsDatabase.getTravelSubCatList(mCategoryID);
                    if (mTravelExpSubCategoryList.size() > 0) {
                        ShowTravelSubCategoryDialog(selectedCat);
                    } else {
                        mButtonTransportMode.setText(selectedCat.getCategoryName());
                    }
                }
            });
            Button cancel = (Button) dialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    dialog.cancel();
                }
            });
            dialog.show();
        } else {
            Utils.showToast(mContext, "No travel category found. Please Synchronize Data");
        }

    }

    public int ShowTravelSubCategoryDialog(final TravelExpCategory parentOption) {
        transportSubCategoryList = new ArrayList<String>();
        for (int a = 0; a < mTravelExpSubCategoryList.size(); a++) {
            if (mTravelExpSubCategoryList.get(a).getCategoryId().equalsIgnoreCase(parentOption.getCategoryId())) {
                transportSubCategoryList.add(mTravelExpSubCategoryList.get(a).getSubCatName());
            }
        }
        final Dialog dialog = new Dialog(TravelExpensesActivity.this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.select_from_list);
        TextView title = (TextView) dialog.findViewById(R.id.title);
        title.setText("Please select an option");
        ListView dialogList = (ListView) dialog.findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(TravelExpensesActivity.this, R.layout.simple_list_child, transportSubCategoryList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                dialog.cancel();
                TravelExpSubCategory selectedSubCategory = mTravelExpSubCategoryList.get(arg2);
                mSubCategoryID = selectedSubCategory.getSubCatId();
                String optionSelected = transportSubCategoryList.get(arg2);
                mButtonTransportMode.setText(parentOption.getCategoryName() + " - " + optionSelected);
            }
        });
        Button cancel = (Button) dialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                dialog.cancel();
            }
        });
        dialog.show();
        return itemSelected;
    }

    public void ShowDateDialog() {
        mCaldroidFragment = new CaldroidFragment();
        mCaldroidFragment.setCaldroidListener(mCaldroidListener);
        mCaldroidFragment.show(getSupportFragmentManager(), "CALDROID_DIALOG_FRAGMENT");
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (resultCode == RESULT_OK) {
            Bitmap bitmap = decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
            FileOutputStream out = null;
            try {
                out = new FileOutputStream(mImagePath);
                bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);

            } catch (FileNotFoundException e) {
                e.printStackTrace();
            }
        } else {
            mSupport = "no";
        }
    }

    public void saveTravelExpenseData() {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Saving Data.Please wait..");
        mProgressDialog.show();
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String tour_exp_id = "TT" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                if (mSupport.equalsIgnoreCase("no")) {
                    mAttachmentName = "";
                }
                mAceDnsTransactionDatabase.insertToTourExpense(tour_exp_id, mDateValue, mStartDestination, mEndDestination, mFare, mSupport, mDistance, mCategoryID, mSubCategoryID, mAttachmentName);
                mAceDnsTransactionDatabase.insertToLocationTable("TT", timeStamp);
                if (mSupport.equalsIgnoreCase("yes") && mAttachmentName.length() > 0) {
                    mAceDnsTransactionDatabase.insertToSupportingAttachTable(mAttachmentName, "TRAVEL");
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }


}


