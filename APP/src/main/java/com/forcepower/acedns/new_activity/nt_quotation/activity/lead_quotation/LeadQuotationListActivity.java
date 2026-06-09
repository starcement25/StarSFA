package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_quotation;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.ComponentActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;
import com.forcepower.acedns.newDataBase.sync.DataForDownloadingLead;
import com.forcepower.acedns.new_activity.nt_quotation.activity.lead_query.LeadQueryActivity;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.QuotationListAdapter;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Objects;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class LeadQuotationListActivity extends ComponentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton, syncButton;
    private TextView totalLeadText;
    private LinearLayout soCountLayout, hosCountLayout;
    private LinearLayout poPendingButton, poRevisionButton, lostLeadButton, poPendingButtonForHOS, poReceivedButtonForHOS, poRevisionButtonForHOS, lostLeadButtonForHOS;
    private TextView pendingCountText, revisionCountText, lostCountText, pendingCountTextForHOS, receivedCountTextForHOS, revisionCountTextForHOS, lostCountTextForHOS;
    private RecyclerView leadGenerationList;

    // Lost Order Reason Popup
    private LinearLayout lostOrderReasonPopupLayout;
    private EditText editTextLostOrderReason;
    private Button lostLeadSubmitButton;
    private ImageView closeLostLeadReasonButton;

    // PO Receive Popup
    private LinearLayout poReceivePopupLayout;
    private EditText editTextPoNumber;
    private LinearLayout poDateButton;
    private TextView poDateButtonText;
    private LinearLayout poImageButton;
    private ImageView pickImageShow;
    private Button submitPoDetailsButton;
    private ImageView closePoDetailsButton;

    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    ArrayList<LeadListMasterTableDataSet> allLeadList = new ArrayList<>();
    ArrayList<LeadListMasterTableDataSet> filterLeadList = new ArrayList<>();
    QuotationListAdapter adapter;
    LeadListMasterTableDataSet obj;
    ProgressDialog mProgressDialogAgeing;

    String user_type = "";
    String savedImagePath = "";
    String lead_id = "";
    String type = "";
    Bitmap customerPicBitmap = null;
    String supportingAttachmentNameForCustomerImage = "";
    int leadStatusCode = 1;
    int imagePick = 0;
    static final int REQUEST_IMAGE_CAPTURE = 1;
    static final int REQUEST_IMAGE_PICK = 2;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_quotation_list);
        mContext = LeadQuotationListActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        allLeadList = mNewDatabaseForSiteLead.getAllLeadListMasterTableData(12);
        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
        if (v == syncButton) {
            syncLocalDatabase();
        }

        if (v == poPendingButton) {
            leadStatusCode = 1;
            _filterAllLeadListAndShowForSO();
        }
        if (v == poRevisionButton) {
            leadStatusCode = 2;
            _filterAllLeadListAndShowForSO();
        }
        if (v == lostLeadButton) {
            leadStatusCode = 3;
            _filterAllLeadListAndShowForSO();
        }

        if (v == poPendingButtonForHOS) {
            leadStatusCode = 1;
            _filterAllLeadListAndShowForHOS();
        }
        if (v == poReceivedButtonForHOS) {
            leadStatusCode = 2;
            _filterAllLeadListAndShowForHOS();
        }
        if (v == poRevisionButtonForHOS) {
            leadStatusCode = 3;
            _filterAllLeadListAndShowForHOS();
        }
        if (v == lostLeadButtonForHOS) {
            leadStatusCode = 4;
            _filterAllLeadListAndShowForHOS();
        }

        if (v == lostLeadSubmitButton) {
            if (editTextLostOrderReason.getText().toString().trim().isEmpty()) {
                Toast.makeText(mContext, "Please enter lost reason.", Toast.LENGTH_LONG).show();
            } else {
                requestForSendLostReasonToServer();
            }
        }
        if (v == closeLostLeadReasonButton) {
            lostOrderReasonPopupLayout.setVisibility(GONE);
        }

        if (v == poDateButton) {
            dateTimePicker();
        }
        if (v == poImageButton) {
            Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
            intent.setType("image/*");
            startActivityForResult(intent, REQUEST_IMAGE_PICK);
        }
        if (v == submitPoDetailsButton) {
            if (editTextPoNumber.getText().toString().trim().isEmpty()) {
                Toast.makeText(mContext, "Please enter the PO Number.", Toast.LENGTH_LONG).show();
            } else if (poDateButtonText.getText().toString().trim().equalsIgnoreCase("PO date")) {
                Toast.makeText(mContext, "Please enter the PO Date.", Toast.LENGTH_LONG).show();
            } else if (imagePick != 1) {
                Toast.makeText(mContext, "Please capture the PO image.", Toast.LENGTH_LONG).show();
            } else {
                imagePick = 0;
                requestForImageUpload();
            }
        }
        if (v == closePoDetailsButton) {
            poReceivePopupLayout.setVisibility(GONE);
        }
    }

    @SuppressLint("SimpleDateFormat")
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE_CAPTURE || requestCode == REQUEST_IMAGE_PICK) {
            if (resultCode == RESULT_OK) {
                if (data != null) {
                    if (requestCode == REQUEST_IMAGE_CAPTURE) {
                        try {
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            Bundle extras = data.getExtras();
                            assert extras != null;
                            customerPicBitmap = (Bitmap) extras.get("data");
                            supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                            String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                            savedImagePath = imagePath;
                            storeImageInLocalStorageShowOnImageView(customerPicBitmap, imagePath);
                        } catch (Exception e) {
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        try {
                            InputStream inputStream = mContext.getContentResolver().openInputStream(Objects.requireNonNull(data.getData()));
                            customerPicBitmap = BitmapFactory.decodeStream(inputStream);
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                            String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                            savedImagePath = imagePath;
                            storeImageInLocalStorageShowOnImageView(customerPicBitmap, imagePath);
                        } catch (Exception e) {
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                        }
                    }
                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);
        syncButton = findViewById(R.id.syncButton);
        syncButton.setOnClickListener(this);

        totalLeadText = findViewById(R.id.totalLeadText);
        soCountLayout = findViewById(R.id.soCountLayout);
        soCountLayout.setVisibility(GONE);
        hosCountLayout = findViewById(R.id.hosCountLayout);
        hosCountLayout.setVisibility(GONE);
        poPendingButton = findViewById(R.id.poPendingButton);
        poPendingButton.setOnClickListener(this);
        pendingCountText = findViewById(R.id.pendingCountText);
        poRevisionButton = findViewById(R.id.poRevisionButton);
        poRevisionButton.setOnClickListener(this);
        revisionCountText = findViewById(R.id.revisionCountText);
        lostLeadButton = findViewById(R.id.lostLeadButton);
        lostLeadButton.setOnClickListener(this);
        lostCountText = findViewById(R.id.lostCountText);
        poPendingButtonForHOS = findViewById(R.id.poPendingButtonForHOS);
        poPendingButtonForHOS.setOnClickListener(this);
        pendingCountTextForHOS = findViewById(R.id.pendingCountTextForHOS);
        poReceivedButtonForHOS = findViewById(R.id.poReceivedButtonForHOS);
        poReceivedButtonForHOS.setOnClickListener(this);
        receivedCountTextForHOS = findViewById(R.id.receivedCountTextForHOS);
        poRevisionButtonForHOS = findViewById(R.id.poRevisionButtonForHOS);
        poRevisionButtonForHOS.setOnClickListener(this);
        revisionCountTextForHOS = findViewById(R.id.revisionCountTextForHOS);
        lostLeadButtonForHOS = findViewById(R.id.lostLeadButtonForHOS);
        lostLeadButtonForHOS.setOnClickListener(this);
        lostCountTextForHOS = findViewById(R.id.lostCountTextForHOS);

        leadGenerationList = findViewById(R.id.leadGenerationList);
        leadGenerationList.setLayoutManager(new LinearLayoutManager(this));

        lostOrderReasonPopupLayout = findViewById(R.id.lostOrderReasonPopupLayout);
        lostOrderReasonPopupLayout.setVisibility(GONE);
        editTextLostOrderReason = findViewById(R.id.editTextLostOrderReason);
        lostLeadSubmitButton = findViewById(R.id.lostLeadSubmitButton);
        lostLeadSubmitButton.setOnClickListener(this);
        closeLostLeadReasonButton = findViewById(R.id.closeLostLeadReasonButton);
        closeLostLeadReasonButton.setOnClickListener(this);

        poReceivePopupLayout = findViewById(R.id.poReceivePopupLayout);
        poReceivePopupLayout.setVisibility(GONE);
        editTextPoNumber = findViewById(R.id.editTextPoNumber);
        poDateButton = findViewById(R.id.poDateButton);
        poDateButton.setOnClickListener(this);
        poDateButtonText = findViewById(R.id.poDateButtonText);
        poImageButton = findViewById(R.id.poImageButton);
        poImageButton.setOnClickListener(this);
        pickImageShow = findViewById(R.id.pickImageShow);
        pickImageShow.setVisibility(GONE);
        submitPoDetailsButton = findViewById(R.id.submitPoDetailsButton);
        submitPoDetailsButton.setOnClickListener(this);
        closePoDetailsButton = findViewById(R.id.closePoDetailsButton);
        closePoDetailsButton.setOnClickListener(this);
        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_EmployeeDetails_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_EmployeeDetails_AsyncTask(Context context) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.sbDevUrl + "api/employee/?emp_code=" + emp_code;
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONArray arr = new JSONArray(result);
                user_type = arr.getJSONObject(0).getString("level");
                if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt_to")) {
                    runOnUiThread(() -> hosCountLayout.setVisibility(VISIBLE));
                    _filterAllLeadListAndShowForHOS();
                } else if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt")) {
                    runOnUiThread(() -> soCountLayout.setVisibility(VISIBLE));
                    _filterAllLeadListAndShowForSO();
                } else {
                    runOnUiThread(LeadQuotationListActivity.this::finish);
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    private void dateTimePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    String date = selectedYear + "-";
                    if (selectedMonth + 1 < 10) {
                        date = date + "0" + (selectedMonth + 1) + "-";
                    } else {
                        date = date + (selectedMonth + 1) + "-";
                    }
                    if (selectedDay < 10) {
                        date = date + "0" + selectedDay;
                    } else {
                        date = date + selectedDay;
                    }
                    poDateButtonText.setText(date);
                },
                year, month, day
        );

        Calendar minCalendar = Calendar.getInstance();
        minCalendar.add(Calendar.DAY_OF_YEAR, -1000);
        Calendar maxCalendar = Calendar.getInstance();

        datePickerDialog.getDatePicker().setMinDate(minCalendar.getTimeInMillis());
        datePickerDialog.getDatePicker().setMaxDate(maxCalendar.getTimeInMillis());
        datePickerDialog.show();
    }

    private void storeImageInLocalStorageShowOnImageView(Bitmap customerPicBitmap, String imagePath) throws FileNotFoundException {
        File outputFile;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        pickImageShow.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));
        pickImageShow.setVisibility(View.VISIBLE);
        imagePick = 1;
    }

    @SuppressLint("SetTextI18n")
    private void _filterAllLeadListAndShowForSO() {
        int pendingCount = 0;
        int revisionCount = 0;
        int lostCount = 0;
        int totalCount = 0;

        filterLeadList.clear();
        for (int i = 0; i < allLeadList.size(); i++) {
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("14")) {
                pendingCount++;
                totalCount++;
                if (leadStatusCode == 1) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("17")) {
                revisionCount++;
                totalCount++;
                if (leadStatusCode == 2) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("12")) {
                lostCount++;
                totalCount++;
                if (leadStatusCode == 3) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
        }

        adapter = new QuotationListAdapter(this, filterLeadList, user_type, new QuotationListAdapter.OnActionClickListener() {
            @Override
            public void onPoDetailsFillupClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                obj = item;
                type = "po";
                poReceivePopupLayout.setVisibility(VISIBLE);
            }

            @Override
            public void onLostReasonClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                obj = item;
                type = "lost";
                lostOrderReasonPopupLayout.setVisibility(VISIBLE);
            }

            @Override
            public void onCheckPoDetailsClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
            }

            @Override
            public void onForwardToMisClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                requestForSendForwardToMISbyHOS();
            }

            @Override
            public void onSendBackToSoClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                requestForSendBackToSObyHOS();
            }
        });
        leadGenerationList.setAdapter(adapter);

        totalLeadText.setText("Total : " + totalCount);
        pendingCountText.setText(String.valueOf(pendingCount));
        revisionCountText.setText(String.valueOf(revisionCount));
        lostCountText.setText(String.valueOf(lostCount));
    }

    @SuppressLint("SetTextI18n")
    private void _filterAllLeadListAndShowForHOS() {
        int pendingCount = 0;
        int revisionCount = 0;
        int receivedCount = 0;
        int lostCount = 0;
        int totalCount = 0;

        filterLeadList.clear();
        for (int i = 0; i < allLeadList.size(); i++) {
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("14") ||
                    allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("17")) {
                pendingCount++;
                totalCount++;
                if (leadStatusCode == 1) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("15")) {
                receivedCount++;
                totalCount++;
                if (leadStatusCode == 2) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("19")) {
                revisionCount++;
                totalCount++;
                if (leadStatusCode == 3) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("12")) {
                lostCount++;
                totalCount++;
                if (leadStatusCode == 4) {
                    filterLeadList.add(allLeadList.get(i));
                }
            }
        }

        adapter = new QuotationListAdapter(this, filterLeadList, user_type, new QuotationListAdapter.OnActionClickListener() {
            @Override
            public void onPoDetailsFillupClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                obj = item;
                type = "po";
                poReceivePopupLayout.setVisibility(VISIBLE);
            }

            @Override
            public void onLostReasonClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                obj = item;
                type = "lost";
                lostOrderReasonPopupLayout.setVisibility(VISIBLE);
            }

            @Override
            public void onCheckPoDetailsClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
            }

            @Override
            public void onForwardToMisClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                requestForSendForwardToMISbyHOS();
            }

            @Override
            public void onSendBackToSoClicked(LeadListMasterTableDataSet item, int position) {
                lead_id = item.getLead_generation_id();
                requestForSendBackToSObyHOS();
            }
        });
        leadGenerationList.setAdapter(adapter);

        totalLeadText.setText("Total : " + totalCount);
        pendingCountTextForHOS.setText(String.valueOf(pendingCount));
        receivedCountTextForHOS.setText(String.valueOf(receivedCount));
        revisionCountTextForHOS.setText(String.valueOf(revisionCount));
        lostCountTextForHOS.setText(String.valueOf(lostCount));
    }

    private void syncLocalDatabase() {
        mProgressDialogAgeing = new ProgressDialog(mContext);
        mProgressDialogAgeing.setMessage("Downloading Data ...");
        mProgressDialogAgeing.show();

        DataForDownloadingLead mDataForDownloadingLead = new DataForDownloadingLead(mContext);
        mDataForDownloadingLead.addAllFormDataForLead(success -> {
            mProgressDialogAgeing.dismiss();
            allLeadList = mNewDatabaseForSiteLead.getAllLeadListMasterTableData(12);
            if (user_type.equalsIgnoreCase("nt_to"))
                _filterAllLeadListAndShowForHOS();
            else
                _filterAllLeadListAndShowForSO();
        });
    }

    private void requestForSendLostReasonToServer() {
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();
                mainObject.put("lost_order_reason", editTextLostOrderReason.getText().toString().trim());
                mainObject.put("lead_quotation_status", "12");

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + lead_id + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();

                ((LeadQuotationListActivity) mContext).runOnUiThread(() -> {
                    editTextLostOrderReason.setText("");
                    lostOrderReasonPopupLayout.setVisibility(GONE);
                    Toast.makeText(mContext, "Lost reason add successfully", Toast.LENGTH_LONG).show();
                    syncLocalDatabase();
                });

            } catch (Exception ignored) {
            }
        }).start();
    }

    private void requestForImageUpload() {
        // ✅ Reload bitmap fresh from saved file — avoids recycled bitmap issue
        if (savedImagePath.isEmpty()) {
            Toast.makeText(mContext, "Image not available, please pick again.", Toast.LENGTH_SHORT).show();
            return;
        }

        Bitmap freshBitmap = BitmapFactory.decodeFile(savedImagePath);
        if (freshBitmap == null) {
            Toast.makeText(mContext, "Image not available, please pick again.", Toast.LENGTH_SHORT).show();
            return;
        }

        new Thread(() -> {
            try {
                ByteArrayOutputStream baos = new ByteArrayOutputStream();
                freshBitmap.compress(Bitmap.CompressFormat.JPEG, 90, baos);
                byte[] imageBytes = baos.toByteArray();
                freshBitmap.recycle();

                OkHttpClient client = new OkHttpClient().newBuilder().build();

                RequestBody body = new MultipartBody.Builder()
                        .setType(MultipartBody.FORM)
                        .addFormDataPart(
                                "file",
                                supportingAttachmentNameForCustomerImage,
                                RequestBody.create(
                                        MediaType.parse("image/jpeg"),
                                        imageBytes
                                )
                        )
                        .addFormDataPart("category", "Test")
                        .addFormDataPart("sub_category", "Test")
                        .addFormDataPart("query_id", "SUB123456789")
                        .build();

                Request request = new Request.Builder()
                        .url("https://ntquotation.myvtd.site/api/media/")
                        .post(body)
                        .build();

                Response response = client.newCall(request).execute();
                String responseBody = response.body() != null ? response.body().string() : "";

                if (response.isSuccessful() && !responseBody.isEmpty()) {
                    JSONObject jsonResponse = new JSONObject(responseBody);

                    int id = jsonResponse.getInt("id");
                    String fileUrl = jsonResponse.getString("url");
                    String contentType = jsonResponse.getString("content_type");
                    String createdAt = jsonResponse.getString("created_at");

                    runOnUiThread(() -> {
                        Toast.makeText(mContext,
                                "Upload Successful!\n" +
                                        "ID: " + id + "\n" +
                                        "URL: " + fileUrl + "\n" +
                                        "Type: " + contentType + "\n" +
                                        "Created: " + createdAt,
                                Toast.LENGTH_LONG).show();
                        savedImagePath = ""; // ✅ Reset after successful upload
                        requestForSendPoDetailsToServer(fileUrl); // ✅ Chain the next call
                    });

                } else {
                    runOnUiThread(() ->
                            Toast.makeText(mContext,
                                    "Upload failed! Code: " + response.code() + "\n" + responseBody,
                                    Toast.LENGTH_SHORT).show()
                    );
                }

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(mContext, "Error: " + e.getMessage(), Toast.LENGTH_SHORT).show()
                );
                Log.e("TAG", "_DOWNLOAD_ requestForImageUpload error: " + e.getMessage());
            }
        }).start();
    }

    private void requestForSendPoDetailsToServer(String fileUrl) {
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();
                mainObject.put("lead_quotation_status", "15");
                mainObject.put("po_number", editTextPoNumber.getText().toString().trim());
                mainObject.put("po_date", poDateButtonText.getText().toString().trim());
                mainObject.put("po_image", fileUrl);

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + lead_id + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                ((LeadQuotationListActivity) mContext).runOnUiThread(() -> {
                    editTextPoNumber.setText("");
                    poDateButtonText.setText("");
                    poReceivePopupLayout.setVisibility(GONE);
                    Toast.makeText(mContext, "PO Details send to HOS successfully", Toast.LENGTH_LONG).show();
                    syncLocalDatabase();
                });
            } catch (Exception ignored) {
            }
        }).start();
    }

    private void requestForSendForwardToMISbyHOS() {
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();
                mainObject.put("lead_quotation_status", "16");

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + lead_id + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                ((LeadQuotationListActivity) mContext).runOnUiThread(() -> {
                    Toast.makeText(mContext, "PO Details send to MIS successfully", Toast.LENGTH_LONG).show();
                    syncLocalDatabase();
                });
            } catch (Exception ignored) {
            }
        }).start();
    }

    private void requestForSendBackToSObyHOS() {
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();
                mainObject.put("lead_quotation_status", "17");

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + lead_id + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                ((LeadQuotationListActivity) mContext).runOnUiThread(() -> {
                    Toast.makeText(mContext, "PO Details send back to SO for revision successfully", Toast.LENGTH_LONG).show();
                    syncLocalDatabase();
                });
            } catch (Exception ignored) {
            }
        }).start();
    }
}