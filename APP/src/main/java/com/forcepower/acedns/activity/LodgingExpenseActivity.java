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
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitLodgingExpenseTask;
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

public class LodgingExpenseActivity extends FragmentActivity implements OnClickListener {

    public static RadioGroup mRadioGroupAttachment = null;
    public int mWidth = 175;
    public int mHeight = 150;
    EditText ex_base, ex_hotel, ex_roomrent, ex_paymade, ex_paymode;
    String base = "", hotelName = "", roomRent = "", payment = "", payMode = "", chkInDate = "", chkOutDate = "", attachmentName = "";
    Button btn_chkin, btn_chkout, btn_submit, btn_back;
    Date chkinDate, chkoutDate, currentDate;
    AceDnsTransactionDatabase helperobj;
    boolean isCheckOutDate = false, isCheckInDate = false, attachGiven = false;
    CaldroidListener listener;
    Context mContext;
    ImageView imgLogo;
    SimpleDateFormat dateFormat;
    Handler mHandler;
    ProgressDialog loader;
    private CaldroidFragment dialogCaldroidFragment;
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
        setContentView(R.layout.activity_lodging_expense);
        RegisterActivities.registerActivity(this);
        mContext = LodgingExpenseActivity.this;
        helperobj = new AceDnsTransactionDatabase(LodgingExpenseActivity.this);
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
                    loader.cancel();
                    new TRANS_SubmitLodgingExpenseTask(mContext, true).execute();
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
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(LodgingExpenseActivity.this));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        ex_base = (EditText) findViewById(R.id.et_base);
        ex_hotel = (EditText) findViewById(R.id.et_hotel);
        ex_roomrent = (EditText) findViewById(R.id.et_roomrent);
        ex_paymade = (EditText) findViewById(R.id.et_payeee);
        ex_paymade.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                if (event == null) {
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        showAttachmentDialog();
                        return true;
                    }
                }
                return false;
            }
        });
        ex_roomrent = (EditText) findViewById(R.id.et_roomrent);

        btn_chkin = (Button) findViewById(R.id.bt_chkin);
        btn_chkout = (Button) findViewById(R.id.bt_chout);
        btn_submit = (Button) findViewById(R.id.btn_submit);
        btn_back = (Button) findViewById(R.id.back);
        mRadioGroupAttachment = (RadioGroup) findViewById(R.id.radioSelectAttachment);

        btn_chkin.setOnClickListener(LodgingExpenseActivity.this);
        btn_chkout.setOnClickListener(LodgingExpenseActivity.this);
        btn_submit.setOnClickListener(LodgingExpenseActivity.this);
        btn_back.setOnClickListener(LodgingExpenseActivity.this);

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (date.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    if (isCheckOutDate == true && isCheckInDate == false) {
                        chkoutDate = date;
                        if (chkinDate == null || chkoutDate.before(chkinDate)) {
                            Utils.showToast(mContext, "Please provide valid CheckIn/CheckOut Date");
                        } else {
                            dialogCaldroidFragment.dismiss();
                            btn_chkout.setText(dateFormat.format(date));
                        }
                    } else if (isCheckOutDate == false && isCheckInDate == true) {
                        chkinDate = date;
                        if (chkoutDate != null && chkinDate.after(chkoutDate)) {
                            Utils.showToast(mContext, "Please provide valid CheckIn/CheckOut Date");
                        } else {
                            dialogCaldroidFragment.dismiss();
                            btn_chkin.setText(dateFormat.format(date));
                        }
                    }
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
                    dialogCaldroidFragment.dismiss();
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };
    }

    @Override
    public void onClick(View v) {
        if (v == btn_chkin) {
            isCheckOutDate = false;
            isCheckInDate = true;
            chooseDateDialog();
        } else if (v == btn_chkout) {
            if (chkinDate != null) {
                isCheckOutDate = true;
                isCheckInDate = false;
                chooseDateDialog();
            } else {
                Utils.showToast(mContext, "Please select CheckIn Date first.");
            }
        } else if (v == btn_submit) {
            doSubmitJob();
        } else if (v == btn_back) {
            finish();
        }
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void doSubmitJob() {
        base = ex_base.getText().toString();
        hotelName = ex_hotel.getText().toString();
        roomRent = ex_roomrent.getText().toString();
        chkInDate = btn_chkin.getText().toString();
        chkOutDate = btn_chkout.getText().toString();
        payment = ex_paymade.getText().toString();

        if (base.length() > 0 && mSupport.length() > 0 && hotelName.length() > 0 && roomRent.length() > 0
                && !chkInDate.equalsIgnoreCase("dd/MM/yyyy") && !chkOutDate.equalsIgnoreCase("dd/MM/yyyy")
                && payment.length() > 0) {
            saveLodgingExpenseData();
        } else {
            Utils.showToast(LodgingExpenseActivity.this, "Please fill all the mandatory  fields");
        }
    }

    public void showAttachmentDialog() {
        final Dialog infoDialog = new Dialog(LodgingExpenseActivity.this, R.style.PauseDialog);
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
                Toast.makeText(mContext, "Scan your supporting Document", Toast.LENGTH_LONG).show();
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

    public void saveLodgingExpenseData() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {

                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String lodge_exp_id = "TL" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                if (mSupport.equalsIgnoreCase("no")) {
                    mAttachmentName = "";
                }
                helperobj.insertToLodgeExpense(lodge_exp_id, base, hotelName, roomRent, chkInDate, chkOutDate, payment, payMode, mAttachmentName);
                helperobj.insertToLocationTable("TL", timeStamp);
                if (mSupport.equalsIgnoreCase("yes") && mAttachmentName.length() > 0) {
                    helperobj.insertToSupportingAttachTable(mAttachmentName, "LODGING");
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
