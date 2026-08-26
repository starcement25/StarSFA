package com.forcepower.acedns.new_activity.dhalai_meet;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.activity.ComponentActivity;
import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class DhalaiMeetActivity extends ComponentActivity implements View.OnClickListener, RadioGroup.OnCheckedChangeListener {

    private Context mContext;
    private Button backButton, submitButton;
    private Button branchButton, categoryButton;
    private TextView branchText, categoryText;
    private TextView nameText, mobileNoText, bagsConsumedText, coverBlocksQtyText;
    private EditText nameEditText, mobileNoEditText, bagsConsumedEditText, coverBlocksQtyEditText;
    private Button pictureUploadButton;
    private TextView sweetGivenText, purchaseFromText, giftText;
    private RadioGroup sweetGivenRadioGroup, purchaseFromRadioGroup, giftRadioGroup;
    private RecyclerView giftRecyclerView;
    private LinearLayout purchaseLayout,giftLayout,imageLayout;
    private RelativeLayout firstImageShow, secondImageShow;
    private ImageView firstImage, secondImage;
    private LinearLayout firstImageDeleteButton, secondImageDeleteButton;

    private Uri imageUri, imageUri1, imageUri2;
    private static final int CAMERA_REQUEST = 1;
    private static final int GALLERY_REQUEST = 2;
    private int numberOfImagePick = 0;
    private ArrayList<KeyValue> mKeyValueList;
    public AceDnsDatabase mAceDnsDatabase;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dhalai_meet);

        mContext=DhalaiMeetActivity.this;
        mAceDnsDatabase=new AceDnsDatabase(mContext);
        init();
    }

    @Override
    public void onClick(View v) {
        if(v==backButton){
            finish();
        }
        if(v==submitButton){}

        if(v==branchButton){
            mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7("branch_master", "branch_code", "branch_name", "", "");
        }
        if(v==categoryButton){

        }
        if(v==pictureUploadButton){
            if (numberOfImagePick != 2) {
                numberOfImagePick += 1;
                checkPermissions();
            }
        }

        if (v == firstImageDeleteButton) {
            imageUri1 = imageUri2;
            imageUri2 = null;
            firstImage.setImageURI(imageUri1);
            secondImage.setImageDrawable(null);
            numberOfImagePick -= 1;
        }
        if (v == secondImageDeleteButton) {
            imageUri2 = null;
            secondImage.setImageDrawable(null);
            numberOfImagePick -= 1;
        }
    }

    @Override
    public void onCheckedChanged(RadioGroup group, int i) {
        if(i==R.id.sweetGivenYesRadioButton){
            purchaseLayout.setVisibility(VISIBLE);
        }
        if(i==R.id.sweetGivenNoRadioButton){
            purchaseLayout.setVisibility(GONE);
        }
        if(i==R.id.purchaseFromYesRadioButton){}
        if(i==R.id.purchaseFromNoRadioButton){}
        if(i==R.id.giftYesRadioButton){
            giftLayout.setVisibility(VISIBLE);
        }
        if(i==R.id.giftNoRadioButton){
            giftLayout.setVisibility(GONE);
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            imageLayout.setVisibility(VISIBLE);
            if (requestCode == CAMERA_REQUEST) {
                if (numberOfImagePick == 1) {
                    imageUri1 = imageUri;
                    firstImage.setImageURI(imageUri);
                } else {
                    imageUri2 = imageUri;
                    secondImage.setImageURI(imageUri);
                }
            } else if (requestCode == GALLERY_REQUEST) {
                Uri selectedImageUri = data.getData();
                if (numberOfImagePick == 1) {
                    imageUri1 = selectedImageUri;
                    firstImage.setImageURI(selectedImageUri);
                } else {
                    imageUri2 = selectedImageUri;
                    secondImage.setImageURI(selectedImageUri);
                }
            }
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        submitButton = findViewById(R.id.submitButton);
        branchButton = findViewById(R.id.branchButton);
        categoryButton = findViewById(R.id.categoryButton);
        branchText = findViewById(R.id.branchText);
        categoryText = findViewById(R.id.categoryText);
        nameText = findViewById(R.id.nameText);
        mobileNoText = findViewById(R.id.mobileNoText);
        bagsConsumedText = findViewById(R.id.bagsConsumedText);
        coverBlocksQtyText = findViewById(R.id.coverBlocksQtyText);
        nameEditText = findViewById(R.id.nameEditText);
        mobileNoEditText = findViewById(R.id.mobileNoEditText);
        bagsConsumedEditText = findViewById(R.id.bagsConsumedEditText);
        coverBlocksQtyEditText = findViewById(R.id.coverBlocksQtyEditText);
        pictureUploadButton = findViewById(R.id.pictureUploadButton);
        sweetGivenText = findViewById(R.id.sweetGivenText);
        purchaseFromText = findViewById(R.id.purchaseFromText);
        giftText = findViewById(R.id.giftText);
        sweetGivenRadioGroup = findViewById(R.id.sweetGivenRadioGroup);
        purchaseFromRadioGroup = findViewById(R.id.purchaseFromRadioGroup);
        giftRadioGroup = findViewById(R.id.giftRadioGroup);
        giftRecyclerView = findViewById(R.id.giftRecyclerView);

        purchaseLayout=findViewById(R.id.purchaseLayout);
        purchaseLayout.setVisibility(GONE);
        giftLayout=findViewById(R.id.giftLayout);
        giftLayout.setVisibility(GONE);
        imageLayout=findViewById(R.id.imageLayout);
        imageLayout.setVisibility(GONE);

        firstImageShow = findViewById(R.id.firstImageShow);
        firstImage = findViewById(R.id.firstImage);
        firstImageDeleteButton = findViewById(R.id.firstImageDeleteButton);
        secondImageShow = findViewById(R.id.secondImageShow);
        secondImage = findViewById(R.id.secondImage);
        secondImageDeleteButton = findViewById(R.id.secondImageDeleteButton);
        firstImageShow.setVisibility(View.GONE);
        secondImageShow.setVisibility(View.GONE);

        branchButton.setOnClickListener(this);
        categoryButton.setOnClickListener(this);
        firstImageDeleteButton.setOnClickListener(this);
        secondImageDeleteButton.setOnClickListener(this);
        backButton.setOnClickListener(this);
        submitButton.setOnClickListener(this);
        pictureUploadButton.setOnClickListener(this);
    }
    private void checkPermissions() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CAMERA, Manifest.permission.READ_EXTERNAL_STORAGE}, 100);
        } else {
            showImagePickerDialog();
        }
    }
    private void showImagePickerDialog() {
        String[] options = {"Camera", "Gallery"};
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Select Image Source");
        builder.setItems(options, (dialog, which) -> {
            if (which == 0) {
                openCamera();
            } else {
                openGallery();
            }
        });
        builder.show();
    }
    private void openCamera() {
        ContentValues values = new ContentValues();
        values.put(MediaStore.Images.Media.TITLE, "New Picture");
        values.put(MediaStore.Images.Media.DESCRIPTION, "From the Camera");
        imageUri = getContentResolver().insert(MediaStore.Images.Media.EXTERNAL_CONTENT_URI, values);

        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
        startActivityForResult(cameraIntent, CAMERA_REQUEST);
    }
    @SuppressLint("IntentReset")
    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
        intent.setType("image/*");
        startActivityForResult(intent, GALLERY_REQUEST);
    }
}