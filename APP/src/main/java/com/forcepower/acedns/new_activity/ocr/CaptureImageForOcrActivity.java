package com.forcepower.acedns.new_activity.ocr;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForBranchAdapter;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForDealerAdapter;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForTechMeetTypeAdapter;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;

import com.google.mlkit.vision.documentscanner.GmsDocumentScannerOptions;
import com.google.mlkit.vision.documentscanner.GmsDocumentScanning;
import com.google.mlkit.vision.documentscanner.GmsDocumentScanningResult;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.activity.result.IntentSenderRequest;

public class CaptureImageForOcrActivity extends AceDnsParentActivity implements View.OnClickListener {
    private static final int MAX_IMAGES = 5;

    Context mContext;

    private Button backButton;
    private Button branchButton, dealerSubDealerButton, techMeetTypeButton, dateButton;
    private TextView branchText, dealerSubDealerText, techMeetTypeText, dateText;
    private EditText areaAddressEditText;
    private Button uploadImageButton;
    private Button saveAsDraftButton;

    // Arrays replace imageLayout1..5 / imageShow1..5 / imageDeleteButton1..5 / imageUri1..5
    private final ImageView[] imageLayout = new ImageView[MAX_IMAGES];
    private final RelativeLayout[] imageShow = new RelativeLayout[MAX_IMAGES];
    private final LinearLayout[] imageDeleteButton = new LinearLayout[MAX_IMAGES];
    private final Uri[] imageUri = new Uri[MAX_IMAGES];

    private final int[] imageLayoutIds = {
            R.id.imageLayout1, R.id.imageLayout2, R.id.imageLayout3, R.id.imageLayout4, R.id.imageLayout5
    };
    private final int[] imageShowIds = {
            R.id.imageShow1, R.id.imageShow2, R.id.imageShow3, R.id.imageShow4, R.id.imageShow5
    };
    private final int[] imageDeleteButtonIds = {
            R.id.imageDeleteButton1, R.id.imageDeleteButton2, R.id.imageDeleteButton3, R.id.imageDeleteButton4, R.id.imageDeleteButton5
    };

    public AceDnsDatabase mAceDnsDatabase;
    private ArrayList<KeyValue> mKeyValueList;
    private ArrayList<CustomerDetails> mCustomerDetailsList;
    private ArrayList<String> mTechMeetList;
    String branchCode = "", customerCode = "";
    int imageCount = 0;

    private final ActivityResultLauncher<IntentSenderRequest> scannerLauncher =
            registerForActivityResult(new ActivityResultContracts.StartIntentSenderForResult(), result -> {
                if (result.getResultCode() == RESULT_OK) {
                    GmsDocumentScanningResult scanResult =
                            GmsDocumentScanningResult.fromActivityResultIntent(result.getData());
                    if (scanResult != null && !scanResult.getPages().isEmpty()) {
                        Uri pageUri = scanResult.getPages().get(0).getImageUri(); // already cropped + perspective-corrected
                        int emptySlot = -1;
                        for (int i = 0; i < MAX_IMAGES; i++) {
                            if (imageUri[i] == null) {
                                emptySlot = i;
                                break;
                            }
                        }
                        if (emptySlot != -1) {
                            imageUri[emptySlot] = pageUri;
                            imageLayout[emptySlot].setVisibility(VISIBLE);
                            imageShow[emptySlot].setVisibility(VISIBLE);
                            imageLayout[emptySlot].setImageURI(pageUri);
                        }
                    }
                }
            });

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_capture_image_for_ocr);

        mContext = CaptureImageForOcrActivity.this;
        init();
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        String date="";
        if(month+1<10)
            date= year+"-0"+ (month + 1) +"-"+day;
        else
            date= year+"-"+ (month + 1) +"-"+day;

        dateText.setText(date);
    }

    @Override
    public void onClick(View v) {
        if (backButton == v) {
            finish();
            return;
        }

        if (branchButton == v) {
            mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7("branch_master", "branch_code", "branch_name", "", "");
            showListDataForBranchDialog(mKeyValueList);
            return;
        }

        if (dealerSubDealerButton == v) {
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListForOcr(branchCode);
            showListDataForDealerDialog(mCustomerDetailsList);
            return;
        }

        if (techMeetTypeButton == v) {
            mTechMeetList = new ArrayList<>(Arrays.asList(
                    "Counter Meet",
                    "Big Counter Meet",
                    "Mega Mason Meet",
                    "Dhalai Meet",
                    "Engineers Meet",
                    "Professional Visit",
                    "Star Tech",
                    "PC Meet",
                    "Plant Visit",
                    "Dealer/Sub-dealer Visit",
                    "Mason Meet",
                    "IHB Meet",
                    "Small Engineers Meet",
                    "Big PC Meet"
            ));
            showListDataForTechMeetTypeDialog(mTechMeetList);
            return;
        }

        if (dateButton == v) {
            nextVisitDatePicker();
            return;
        }

        if (uploadImageButton == v) {
            if (imageCount == MAX_IMAGES) {
                Toast.makeText(mContext, "You have already uploaded the maximum number of images.", Toast.LENGTH_SHORT).show();
            } else {
                imageCount++;
                checkPermissions();
            }
            return;
        }

        for (int i = 0; i < MAX_IMAGES; i++) {
            if (imageDeleteButton[i] == v) {
                removeImageAt(i);
                return;
            }
        }

        if (saveAsDraftButton == v) {
            try {
                if (branchCode.isEmpty()) {
                    Toast.makeText(mContext, "Please select Branch", Toast.LENGTH_SHORT).show();
                } else if (customerCode.isEmpty()) {
                    Toast.makeText(mContext, "Please select Dealer/Sub-dealer", Toast.LENGTH_SHORT).show();
                } else if (areaAddressEditText.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please enter Area/Address", Toast.LENGTH_SHORT).show();
                } else if (techMeetTypeText.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select Tech Meet Type", Toast.LENGTH_SHORT).show();
                } else if (dateText.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select Date", Toast.LENGTH_SHORT).show();
                } else if (allImagesEmpty()) {
                    Toast.makeText(mContext, "Please upload Image", Toast.LENGTH_SHORT).show();
                } else {
                    Intent intent = new Intent(CaptureImageForOcrActivity.this, ShowOcrDataAndSubmitActivity.class);
                    intent.putExtra("branch_code", branchCode);
                    intent.putExtra("branch_name", branchText.getText().toString().trim());
                    intent.putExtra("customer_code", customerCode);
                    intent.putExtra("customer_name", dealerSubDealerText.getText().toString().trim());
                    intent.putExtra("area_address", areaAddressEditText.getText().toString().trim());
                    intent.putExtra("tech_meet_type", techMeetTypeText.getText().toString());
                    intent.putExtra("date", dateText.getText().toString());
                    intent.putExtra("SURVEYSUBMENUDETAILS", getIntent().getStringExtra("SURVEYSUBMENUDETAILS"));
                    // pack the non-null URIs into a parcelable array
                    ArrayList<Uri> uriList = new ArrayList<>();
                    for (Uri uri : imageUri) {
                        if (uri != null) uriList.add(uri);
                    }
                    intent.putParcelableArrayListExtra("image", uriList);

                    startActivity(intent);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ onClick: " + e.getMessage());
            }
        }
    }

    /**
     * Removes the image at `index` and shifts every image after it back one slot,
     * so there's never a gap (e.g. deleting #1 moves #2->#1, #3->#2, etc.).
     */
    private void removeImageAt(int index) {
        for (int i = index; i < MAX_IMAGES - 1; i++) {
            imageUri[i] = imageUri[i + 1];
            imageLayout[i].setImageURI(imageUri[i]);
            imageShow[i].setVisibility(imageUri[i] != null ? VISIBLE : GONE);
        }
        int last = MAX_IMAGES - 1;
        imageUri[last] = null;
        imageLayout[last].setImageURI(null);
        imageShow[last].setVisibility(GONE);

        imageCount--;
    }

    private boolean allImagesEmpty() {
        for (Uri uri : imageUri) {
            if (uri != null) return false;
        }
        return true;
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForBranchDialog(ArrayList<KeyValue> dataSet) {
        try {
            final ShowDataForBranchAdapter pAdapter = new ShowDataForBranchAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select Branch");
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(VISIBLE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                Log.d("TAG", "_DOWNLOAD_ showListDataForBranchDialog: " + dataSet.get(position).getValue() + "  :  " + dataSet.get(position).getKey());
                branchCode = dataSet.get(position).getKey();
                branchText.setText(dataSet.get(position).getValue());
                branchText.setVisibility(VISIBLE);
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForDealerDialog(ArrayList<CustomerDetails> dataSet) {
        try {
            final ShowDataForDealerAdapter pAdapter = new ShowDataForDealerAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select Dealer/Sub-dealer");
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(VISIBLE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                customerCode = dataSet.get(position).getCustomerCode();
                dealerSubDealerText.setText(dataSet.get(position).getCustomerName());
                dealerSubDealerText.setVisibility(VISIBLE);
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForTechMeetTypeDialog(ArrayList<String> dataSet) {
        try {
            final ShowDataForTechMeetTypeAdapter pAdapter = new ShowDataForTechMeetTypeAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select Tech Meet Type");
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(GONE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                Log.d("TAG", "_DOWNLOAD_ showListDataForTechMeetTypeDialog: " + dataSet.get(position));
                techMeetTypeText.setText(dataSet.get(position));
                techMeetTypeText.setVisibility(VISIBLE);
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);

        branchButton = findViewById(R.id.branchButton);
        branchText = findViewById(R.id.branchText);
        branchText.setVisibility(GONE);

        dateButton = findViewById(R.id.dateButton);
        dateText = findViewById(R.id.dateText);

        dealerSubDealerButton = findViewById(R.id.dealerSubDealerButton);
        dealerSubDealerText = findViewById(R.id.dealerSubDealerText);
        dealerSubDealerText.setVisibility(GONE);

        areaAddressEditText = findViewById(R.id.areaAddressEditText);

        techMeetTypeButton = findViewById(R.id.techMeetTypeButton);
        techMeetTypeText = findViewById(R.id.techMeetTypeText);
        techMeetTypeText.setVisibility(GONE);

        uploadImageButton = findViewById(R.id.uploadImageButton);
        saveAsDraftButton = findViewById(R.id.saveAsDraftButton);

        for (int i = 0; i < MAX_IMAGES; i++) {
            imageLayout[i] = findViewById(imageLayoutIds[i]);
            imageShow[i] = findViewById(imageShowIds[i]);
            imageDeleteButton[i] = findViewById(imageDeleteButtonIds[i]);
            imageShow[i].setVisibility(GONE);
            imageDeleteButton[i].setOnClickListener(this);
        }

        backButton.setOnClickListener(this);
        branchButton.setOnClickListener(this);
        dealerSubDealerButton.setOnClickListener(this);
        techMeetTypeButton.setOnClickListener(this);
        dateButton.setOnClickListener(this);
        uploadImageButton.setOnClickListener(this);
        saveAsDraftButton.setOnClickListener(this);
    }

    private void checkPermissions() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CAMERA}, 100);
        } else {
            openCamera();
        }
    }

    private void openCamera() {
        GmsDocumentScannerOptions options = new GmsDocumentScannerOptions.Builder()
                .setGalleryImportAllowed(true)
                .setPageLimit(1)
                .setResultFormats(GmsDocumentScannerOptions.RESULT_FORMAT_JPEG)
                .setScannerMode(GmsDocumentScannerOptions.SCANNER_MODE_FULL)
                .build();

        GmsDocumentScanning.getClient(options)
                .getStartScanIntent(this)
                .addOnSuccessListener(intentSender ->
                        scannerLauncher.launch(new IntentSenderRequest.Builder(intentSender).build()))
                .addOnFailureListener(e ->
                        Toast.makeText(mContext, "Scanner unavailable: " + e.getMessage(), Toast.LENGTH_SHORT).show());
    }

    private void nextVisitDatePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                CaptureImageForOcrActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    String date;
                    if(selectedMonth+1<10)
                        date =  selectedYear+ "-0" + (selectedMonth + 1) + "-" +  selectedDay;
                    else
                        date =  selectedYear+ "-" + (selectedMonth + 1) + "-" +  selectedDay;
                    dateText.setText(date);
                    dateText.setVisibility(VISIBLE);
                },
                year, month, day
        );
        datePickerDialog.show();
    }
}