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
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitFoodingExpenseTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class FoodingExpenseActivity extends FragmentActivity implements OnClickListener {


    public static RadioGroup mRadioGroupAttachment = null;
    private static EditText mEditTextBase = null;
    private static EditText mEditTextExpenseType = null;
    private static EditText mEditTextAccompany = null;
    private static EditText mEditTextPayment = null;
    private static Button mButtonDate = null;
    private static Button mButtonSubmit = null;
    private static Button mButtonBack = null;
    private static ImageView mImageViewLogo = null;
    public int mWidth = 175;
    public int mHeight = 150;
    String base = "", expense = "", accompany = "", payment = "", payMode = "", date = "", attachmentName = "";
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    CaldroidFragment mCaldroidFragment;
    CaldroidListener mCaldroidListener;
    Context mContext;
    SimpleDateFormat dateFormat;
    Date currentDate;
    Handler mHandler;
    ProgressDialog mProgressDialog;
    private int TAKE_PHOTO_CODE = 0;
    private String mAttachmentName = "";
    private String mImagePath = "";
    private String mSupport = "";

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
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_fooding_expense);
        RegisterActivities.registerActivity(this);
        mContext = FoodingExpenseActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(FoodingExpenseActivity.this);
        dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        try {
            String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
        } catch (Exception e) {
            currentDate = new Date();
        }
        initView();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                    new TRANS_SubmitFoodingExpenseTask(mContext, true).execute();
                }
            }
        };

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

    }

    public void initView() {
        mImageViewLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mEditTextBase = (EditText) findViewById(R.id.editTextBaseStation);
        mEditTextExpenseType = (EditText) findViewById(R.id.editTextExpenseType);
        mEditTextAccompany = (EditText) findViewById(R.id.editTextAccompany);
        mEditTextPayment = (EditText) findViewById(R.id.editTextTotalPayment);

        mButtonDate = (Button) findViewById(R.id.bt_date);
        mButtonSubmit = (Button) findViewById(R.id.btn_submit);
        mButtonBack = (Button) findViewById(R.id.back);

        mButtonDate.setOnClickListener(FoodingExpenseActivity.this);
        mButtonSubmit.setOnClickListener(FoodingExpenseActivity.this);
        mButtonBack.setOnClickListener(FoodingExpenseActivity.this);
        mRadioGroupAttachment = (RadioGroup) findViewById(R.id.radioSelectAttachment);

        mCaldroidListener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (date.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    mCaldroidFragment.dismiss();
                    mButtonDate.setText(dateFormat.format(date));
                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                if (date.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    mCaldroidFragment.dismiss();
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };
    }

    @Override
    public void onClick(View v) {
        if (v == mButtonDate) {
            ChooseDateDialog();
        } else if (v == mButtonSubmit) {
            SubmitJob();
        } else if (v == mButtonBack) {
            finish();
        }
    }

    public void ChooseDateDialog() {
        mCaldroidFragment = new CaldroidFragment();
        mCaldroidFragment.setCaldroidListener(mCaldroidListener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        mCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void SubmitJob() {
        base = mEditTextBase.getText().toString();
        expense = mEditTextExpenseType.getText().toString();
        accompany = mEditTextAccompany.getText().toString();
        date = mButtonDate.getText().toString();
        payment = mEditTextPayment.getText().toString();

        if (base.length() > 0 && mSupport.length() > 0 && expense.length() > 0 && accompany.length() > 0 && !date.equalsIgnoreCase("dd/MM/yyyy") && payment.length() > 0) {
            saveFoodingExpenseData();
        } else {
            Utils.showToast(FoodingExpenseActivity.this, "Please fill all the mandatory  fields");
        }
    }

    public void showAttachmentDialog() {
        final Dialog infoDialog = new Dialog(FoodingExpenseActivity.this, R.style.PauseDialog);
        infoDialog.setCancelable(false);
        infoDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        infoDialog.setContentView(R.layout.paymode_dialog);
        RadioGroup radioGroup = (RadioGroup) infoDialog.findViewById(R.id.radioSel);
        radioGroup.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup arg0, int id) {
                switch (id) {
                    case R.id.radio_cash:
                        payMode = "cash";
                        infoDialog.cancel();
                        break;
                    case R.id.radio_cheque:
                        payMode = "cheque";
                        infoDialog.cancel();
                        break;
                    case R.id.radio_card:
                        payMode = "card";
                        infoDialog.cancel();
                        break;
                }
                Toast.makeText(mContext, "Scan your supporting Document", 4000).show();
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                attachmentName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".png";
                Intent imageIntent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
                String pathMedia = Utils.getAppStoragePath(mContext) + attachmentName;
                Uri uriSavedImage = Uri.fromFile(new File(pathMedia));
                imageIntent.putExtra(MediaStore.EXTRA_OUTPUT, uriSavedImage);
                startActivityForResult(imageIntent, 1);
            }
        });
        infoDialog.show();
    }

    public void saveFoodingExpenseData() {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Saving Data.Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String food_exp_id = "TF" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                if (mSupport.equalsIgnoreCase("no")) {
                    mAttachmentName = "";
                }
                mAceDnsTransactionDatabase.insertToFoodExpense(food_exp_id, base, expense, date, accompany, payment, payMode, mAttachmentName);
                mAceDnsTransactionDatabase.insertToLocationTable("TF", timeStamp);
                if (mSupport.equalsIgnoreCase("yes") && mAttachmentName.length() > 0) {
                    mAceDnsTransactionDatabase.insertToSupportingAttachTable(mAttachmentName, "FOODING");
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

}
