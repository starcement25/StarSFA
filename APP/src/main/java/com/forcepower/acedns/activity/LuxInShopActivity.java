package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class LuxInShopActivity extends AceDnsParentActivity {

    Button btnBack,btnNotOk,buttonOk,button_site_name,buttonSubmit;
    Uri imageUri;
    String imageName;
    int attachmentType, maxImageLimit=5,numberOfImageAdded = 0,numberOfImageAddedDa = 0;
    private int TAKE_PHOTO_CODE = 0;
    Context mContext;
    ArrayList<String> AttachmentNames = new ArrayList<>();
    ArrayList<String> accessMonthList = new ArrayList<>();

    ImageView attachmentImageView2, attachmentImageView3,attachmentImageView4,attachmentImageView5;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lux_in_shop);


        btnBack = findViewById(R.id.back);
        buttonSubmit = findViewById(R.id.buttonSubmit);
        button_site_name = findViewById(R.id.button_site_name);
        mContext = LuxInShopActivity.this;

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        buttonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        button_site_name.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowEventDialog("Event Type");
            }
        });
    }


    public void addAttachment(View v)//Other_Attachment
    {
        attachmentType=1;
        if (numberOfImageAdded < maxImageLimit)
        {
            launchCameraToTakeImage(TAKE_PHOTO_CODE);
        } else {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        }
    }


    private void launchCameraToTakeImage(int CODE)
    {
        try
        {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".png";
            File file = new File(Utils.getAppStoragePath(mContext)+imageName);
            imageUri = Uri.fromFile(file);
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                startActivityForResult(takePictureIntent, CODE);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK)
        {
            try {
                Bundle extras = data.getExtras();
                Bitmap fuelPicBitmap = null;
                fuelPicBitmap = (Bitmap) extras.get("data");
                String imagePath = Utils.getAppStoragePath(mContext) + imageName;
                ImageView attachmentImageView1 = (ImageView) findViewById(R.id.attachmentImageView1);
                storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap,true,imagePath,attachmentImageView1);
                //mAceDnsTransactionDatabase.insertToSupportingAttachTable(FuelBillAttachmentName, "add_fuel_bill");

                Bitmap imageBitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), imageUri);
                imageBitmap = Utils.getResizedBitmap(imageBitmap, 75, 50);
                AttachmentNames.add( imageName);
                if (numberOfImageAdded == 0)
                {
                    attachmentImageView1 = (ImageView) findViewById(R.id.attachmentImageView1);
                    attachmentImageView1.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if(numberOfImageAdded<maxImageLimit)
                        showAddAnotherImageDialog();
                }
                else if (numberOfImageAdded == 1)
                {
                    attachmentImageView2 = (ImageView) findViewById(R.id.attachmentImageView2);
                    attachmentImageView2.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if(numberOfImageAdded<maxImageLimit)
                        showAddAnotherImageDialog();
                }
                else if (numberOfImageAdded == 2)
                {
                    attachmentImageView3 = (ImageView) findViewById(R.id.attachmentImageView3);
                    attachmentImageView3.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                }
                else if (numberOfImageAdded == 3)
                {
                    attachmentImageView4 = (ImageView) findViewById(R.id.attachmentImageView4);
                    attachmentImageView4.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                }
                else if (numberOfImageAdded == 4)
                {
                    attachmentImageView5 = (ImageView) findViewById(R.id.attachmentImageView5);
                    attachmentImageView5.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

        }

    }

    private void showAddAnotherImageDialog() {
        Constants.isSurveyImageTake = true;
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Please Note");
        AlertDG.setMessage("Do you want to take another pic?");
        AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                launchCameraToTakeImage(TAKE_PHOTO_CODE);
            }
        });
        AlertDG.setNegativeButton("No", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
            }
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    private void storeImageInLocalStorageShowOnImageViewCustomize(Bitmap customerPicBitmap,Boolean ShowImageOnUi,String imagePath,ImageView iv)
            throws FileNotFoundException {
        File outputFile = null;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out = null;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        if(ShowImageOnUi)
        {
            iv.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 200, 200));
        }
    }

    public void ShowEventDialog(String parentText) {

        accessMonthList = new ArrayList<String>();

        accessMonthList.add("type1");
        accessMonthList.add("type2");
        accessMonthList.add("type3");
        accessMonthList.add("type4");

        final Dialog routePlanAccessDialog = new Dialog(LuxInShopActivity.this,
                R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = (TextView) routePlanAccessDialog
                .findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = (ListView) routePlanAccessDialog
                .findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(
                LuxInShopActivity.this, R.layout.routeplan_access_dialog_child,
                accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = (Button) routePlanAccessDialog
                .findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanAccessDialog.cancel();
            }
        });

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {

                button_site_name.setText("" + accessMonthList.get(arg2).toString());
                routePlanAccessDialog.cancel();
            }
        });


        routePlanAccessDialog.show();
    }


}