package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class KycActivity extends AceDnsParentActivity {

    ConnectionDetector cd;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Button btnBack, btn_select;

    private int TAKE_PHOTO_CODE = 0;
    Uri imageUri;
    String imageName;
    int attachmentType, maxImageLimit = 5, numberOfImageAdded = 0;
    ArrayList<String> AttachmentNames = new ArrayList<>();
    ImageView attachmentImageView2, attachmentImageView3, attachmentImageView4, attachmentImageView5;

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_kyc);

        mContext = KycActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        cd = new ConnectionDetector(mContext);
        btnBack = findViewById(R.id.back);
        btn_select = findViewById(R.id.btn_select);
        btnBack.setOnClickListener(v -> finish());
        btn_select.setOnClickListener(v -> chooseCustomerDialog());

        try {
            TextView txtVersion = findViewById(R.id.txt_version);
            txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        } catch (Exception ignored) {
        }
    }

    public void addAttachment(View v) {
        attachmentType = 1;
        if (numberOfImageAdded < maxImageLimit) {
            launchCameraToTakeImage(TAKE_PHOTO_CODE);
        } else {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SimpleDateFormat")
    private void launchCameraToTakeImage(int CODE) {
        try {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".png";

            File file = new File(Utils.getAppStoragePath(mContext) + imageName);
            imageUri = Uri.fromFile(file);

            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                startActivityForResult(takePictureIntent, CODE);
            }
        } catch (Exception ignored) {
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            try {
                Bundle extras = data.getExtras();
                Bitmap fuelPicBitmap;
                assert extras != null;
                fuelPicBitmap = (Bitmap) extras.get("data");
                String imagePath = Utils.getAppStoragePath(mContext) + imageName;
                ImageView attachmentImageView1 = findViewById(R.id.attachmentImageView1);
                storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap, imagePath, attachmentImageView1);

                Bitmap imageBitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), imageUri);
                imageBitmap = Utils.getResizedBitmap(imageBitmap, 75, 50);
                AttachmentNames.add(imageName);
                if (numberOfImageAdded == 0) {
                    attachmentImageView1 = findViewById(R.id.attachmentImageView1);
                    attachmentImageView1.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if (numberOfImageAdded < maxImageLimit)
                        showAddAnotherImageDialog();
                } else if (numberOfImageAdded == 1) {
                    attachmentImageView2 = findViewById(R.id.attachmentImageView2);
                    attachmentImageView2.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if (numberOfImageAdded < maxImageLimit)
                        showAddAnotherImageDialog();
                } else if (numberOfImageAdded == 2) {
                    attachmentImageView3 = findViewById(R.id.attachmentImageView3);
                    attachmentImageView3.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                } else if (numberOfImageAdded == 3) {
                    attachmentImageView4 = findViewById(R.id.attachmentImageView4);
                    attachmentImageView4.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                } else if (numberOfImageAdded == 4) {
                    attachmentImageView5 = findViewById(R.id.attachmentImageView5);
                    attachmentImageView5.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                }
            } catch (Exception ignored) {
            }
        }
    }

    private void showAddAnotherImageDialog() {
        Constants.isSurveyImageTake = true;
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Please Note");
        AlertDG.setMessage("Do you want to take another pic?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> launchCameraToTakeImage(TAKE_PHOTO_CODE));
        AlertDG.setNegativeButton("No", (dialog, which) -> {
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    private void storeImageInLocalStorageShowOnImageViewCustomize(Bitmap customerPicBitmap, String imagePath, ImageView iv) throws FileNotFoundException {
        File outputFile;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        iv.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 200, 200));
    }

    @SuppressLint("SetTextI18n")
    public void chooseCustomerDialog() {
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        CustomerDetails detailsObj = new CustomerDetails();
        detailsObj.setCustomerName("A");
        customerList.add(detailsObj);
        detailsObj = new CustomerDetails();
        detailsObj.setCustomerName("B");
        customerList.add(detailsObj);
        detailsObj = new CustomerDetails();
        detailsObj.setCustomerName("C");
        customerList.add(detailsObj);
        final CustomerAdapter adapterCust = new CustomerAdapter(KycActivity.this, R.layout.multiple_cust_child, customerList);
        final Dialog custDialog = new Dialog(KycActivity.this, R.style.PauseDialog);
        custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        custDialog.setContentView(R.layout.select_multiple_from_list);
        custDialog.setCancelable(false);
        TextView title = custDialog.findViewById(R.id.title);
        title.setText("Please select Status");
        final ListView dialogList = custDialog.findViewById(R.id.list);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        dialogList.setAdapter(adapterCust);
        RelativeLayout chkAllLayout = custDialog.findViewById(R.id.select_all_layout);
        chkAllLayout.setVisibility(View.VISIBLE);
        final CheckBox chkSelectAll = custDialog.findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(v -> {
            if (chkSelectAll.isChecked()) {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, true);
                }
            } else {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, false);
                }
            }
        });
        Button submit = custDialog.findViewById(R.id.button1);
        submit.setOnClickListener(arg0 -> {
            custDialog.cancel();
        });
        custDialog.show();
    }
}