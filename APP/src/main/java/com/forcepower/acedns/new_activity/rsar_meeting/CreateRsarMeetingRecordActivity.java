package com.forcepower.acedns.new_activity.rsar_meeting;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.Html;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.new_activity.rsar_meeting.adapter.ParticipateEnterAdapter;
import com.forcepower.acedns.new_activity.rsar_meeting.adapter.RSARNameAdapter;
import com.forcepower.acedns.new_activity.rsar_meeting.data_set.ParticipatedInformation;
import com.forcepower.acedns.new_activity.rsar_meeting.data_set.RSARNameItem;

import java.util.ArrayList;

public class CreateRsarMeetingRecordActivity extends AceDnsParentActivity implements View.OnClickListener {
    private Button backButton;

    private TextView textDateStamp, textTimeStamp, textLatitude, textLongitude, textNumberOfParticipate;
    private EditText editTextDateStamp, editTextTimeStamp, editTextLatitude, editTextLongitude, editTextNumberOfParticipate;

    private TextView textParticipate, textAddParticipateButton;
    private RecyclerView participatesList;

    private Button imageUploadButton;
    private LinearLayout showUploadedImageLayout;
    private RelativeLayout firstImageShow, secondImageShow;
    private ImageView firstImage, secondImage;
    private LinearLayout firstImageDeleteButton, secondImageDeleteButton;

    private Button submitButton;

    private ParticipateEnterAdapter adapter;
    ArrayList<ParticipatedInformation> arrayList = new ArrayList<>();
    ArrayList<RSARNameItem> rsarNameItemList = new ArrayList<>();
    Context mContext;
    private Uri imageUri, imageUri1, imageUri2;
    private static final int CAMERA_REQUEST = 1;
    private static final int GALLERY_REQUEST = 2;
    private int numberOfImagePick = 0;

    // ***Override Function***
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_rsar_meeting_record);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mContext = CreateRsarMeetingRecordActivity.this;
        init();
    }

    @Override
    public void onClick(View view) {
        if (view == backButton) {
            finish();
        } else if (view == textAddParticipateButton) {
            ParticipatedInformation dataSet = new ParticipatedInformation();
            dataSet.setName("");
            dataSet.setMobileNumber("");
            dataSet.setRsarCounterName("");
            arrayList.add(dataSet);
            adapter.notifyItemInserted(arrayList.size() - 1);
        } else if (view == imageUploadButton) {
            if (numberOfImagePick != 2) {
                numberOfImagePick += 1;
                checkPermissions();
            }
        } else if (view == firstImageDeleteButton) {
            imageUri1 = imageUri2;
            imageUri2 = null;
            firstImage.setImageURI(imageUri1);
            secondImage.setImageDrawable(null);
            numberOfImagePick -= 1;
        } else if (view == secondImageDeleteButton) {
            imageUri2 = null;
            secondImage.setImageDrawable(null);
            numberOfImagePick -= 1;
        } else if (view == submitButton) {
            // Submit meeting details function
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
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

    // ***Init Function***
    private void init() {
        backButton = findViewById(R.id.backButton);

        textDateStamp = findViewById(R.id.textDateStamp);
        editTextDateStamp = findViewById(R.id.editTextDateStamp);
        textTimeStamp = findViewById(R.id.textTimeStamp);
        editTextTimeStamp = findViewById(R.id.editTextTimeStamp);
        textLatitude = findViewById(R.id.textLatitude);
        editTextLatitude = findViewById(R.id.editTextLatitude);
        textLongitude = findViewById(R.id.textLongitude);
        editTextLongitude = findViewById(R.id.editTextLongitude);
        textNumberOfParticipate = findViewById(R.id.textNumberOfParticipate);
        editTextNumberOfParticipate = findViewById(R.id.editTextNumberOfParticipate);
        textParticipate = findViewById(R.id.textParticipate);
        textAddParticipateButton = findViewById(R.id.textAddParticipateButton);
        participatesList = findViewById(R.id.participatesList);
        imageUploadButton = findViewById(R.id.imageUploadButton);
        showUploadedImageLayout = findViewById(R.id.showUploadedImageLayout);
        firstImageShow = findViewById(R.id.firstImageShow);
        firstImage = findViewById(R.id.firstImage);
        firstImageDeleteButton = findViewById(R.id.firstImageDeleteButton);
        secondImageShow = findViewById(R.id.secondImageShow);
        secondImage = findViewById(R.id.secondImage);
        secondImageDeleteButton = findViewById(R.id.secondImageDeleteButton);
        submitButton = findViewById(R.id.submitButton);

        onClickFunction();
    }

    private void onClickFunction() {
        backButton.setOnClickListener(this);
        textAddParticipateButton.setOnClickListener(this);
        imageUploadButton.setOnClickListener(this);
        firstImageDeleteButton.setOnClickListener(this);
        secondImageDeleteButton.setOnClickListener(this);
        submitButton.setOnClickListener(this);

        setTextTitle();
    }

    private void setTextTitle() {
        textDateStamp.setText(Html.fromHtml("Date Stamp <font color='#FF0000'>*</font>"));
        textTimeStamp.setText(Html.fromHtml("Time Stamp <font color='#FF0000'>*</font>"));
        textLatitude.setText(Html.fromHtml("Latitude <font color='#FF0000'>*</font>"));
        textLongitude.setText(Html.fromHtml("Longitude <font color='#FF0000'>*</font>"));
        textNumberOfParticipate.setText(Html.fromHtml("Number of Participate <font color='#FF0000'>*</font>"));
        textParticipate.setText(Html.fromHtml("Participates <font color='#FF0000'>*</font>"));

        editTextDateStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextTimeStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextLatitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextLongitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextNumberOfParticipate.setHint(Html.fromHtml("Number of Participate"));

        setupEditableNonEditable();
    }

    private void setupEditableNonEditable() {
        editTextDateStamp.setEnabled(false);
        editTextTimeStamp.setEnabled(false);
        editTextLatitude.setEnabled(false);
        editTextLongitude.setEnabled(false);
        editTextNumberOfParticipate.setInputType(InputType.TYPE_CLASS_NUMBER);
        showUploadedImageLayout.setVisibility(View.GONE);
        firstImageShow.setVisibility(View.GONE);
        secondImageShow.setVisibility(View.GONE);

        setUpDefaultList();
    }

    private void setUpDefaultList() {
        arrayList = new ArrayList<>();
        ParticipatedInformation dataSet = new ParticipatedInformation();
        dataSet.setName("");
        dataSet.setMobileNumber("");
        dataSet.setRsarCounterName("");
        arrayList.add(dataSet);
        showInList();
        for (int i = 0; i < 10; i++) {
            RSARNameItem temp = new RSARNameItem();
            temp.setCode("CODE00" + i);
            temp.setName("RSAR NAME " + i);
            rsarNameItemList.add(temp);
        }
    }

    public void showInList() {
        try {
            participatesList.setLayoutManager(new LinearLayoutManager(this));
            adapter = new ParticipateEnterAdapter(this, arrayList, new ParticipateEnterAdapter.OnActionClickListener() {
                @Override
                public void onRemoveItemClicked(ParticipatedInformation item, int position) {
                    arrayList.remove(position);
                    adapter.notifyItemRemoved(position);
                    adapter.notifyItemRangeChanged(position, arrayList.size());
                }

                @Override
                public void onRSARCounterName(ParticipatedInformation item, int position) {
                    showRsarNameList(rsarNameItemList, "Select RSAR Counter Name", position);
                }
            });
            participatesList.setAdapter(adapter);
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ showInList Exception : " + e);
        }
    }

    public void showRsarNameList(ArrayList<RSARNameItem> dataSet, String titleValue, int position) {
        try {
            final RSARNameAdapter adapterCust = new RSARNameAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, listPosition, arg3) -> {
                mDialogCustomer.dismiss();
                RSARNameItem selectedItem = adapterCust.getItem(listPosition);
                if (selectedItem != null && position >= 0 && position < arrayList.size()) {
                    arrayList.get(position).setRsarCounterName(selectedItem.getName());
                    adapter.notifyItemChanged(position);
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
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